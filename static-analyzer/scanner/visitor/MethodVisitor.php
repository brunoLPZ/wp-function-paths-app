<?php

use PhpParser\{Node, NodeFinder, NodeTraverser, NodeVisitorAbstract};

require_once __DIR__ . '/../checker/NodeChecker.php';
require_once __DIR__ . '/../builder/FunctionMethodBuilder.php';

class MethodVisitor extends NodeVisitorAbstract
{

  private array $classes;
  private array $methods;

  private NodeFinder $nodeFinder;

  public function __construct(&$classes)
  {
    $this->classes =& $classes;
    $this->methods = [];
    $this->nodeFinder = new NodeFinder();
  }

  public function enterNode(Node $node)
  {
    if (NodeChecker::isMethodDefinition($node)) {
      $method = FunctionMethodBuilder::buildFromMethodNode($node);
      $this->assignMethodToClass($method);
      $traverser = new NodeTraverser();
      if ($node->stmts != null) {
        if (count($method->getVarParams()) > 0) {
          $possibleCalls = [];
          $insecureCalls = [];
          $sanitizers = [];
          foreach ($method->getVarParams() as $param) {
            $taintVisitor = new TaintVisitor($param, $method->getClassName());
            $traverser->addVisitor($taintVisitor);
            $traverser->traverse($node->stmts);
            $possibleCalls = array_merge($possibleCalls, $taintVisitor->getCalls());
            $insecureCalls = array_merge($insecureCalls, $taintVisitor->getInsecureCalls());
            $sanitizers = $taintVisitor->getSanitizers();
          }
          $method->setPossibleCalls($possibleCalls);
          $method->setInsecureCalls($insecureCalls);
          $method->setSanitizers($sanitizers);
          $method->setIsControlledByUser($taintVisitor->isControlledByUser());
        } else {
          $taintVisitor = new TaintVisitor(null, $method->getClassName());
          $traverser->addVisitor($taintVisitor);
          $traverser->traverse($node->stmts);
          $method->setPossibleCalls($taintVisitor->getCalls());
          $method->setInsecureCalls($taintVisitor->getInsecureCalls());
          $method->setSanitizers($taintVisitor->getSanitizers());
          $method->setIsControlledByUser($taintVisitor->isControlledByUser());
        }
      }
    }
  }

  private function assignMethodToClass(PhpMethod $phpMethod): void
  {
    // Filter all possible classes (method must be inside class start and end lines)
    $possibleClasses = array_filter($this->classes, function ($class) use ($phpMethod) {
      return $class->getStartLine() <= $phpMethod->getStartLine() && $class->getEndLine() >= $phpMethod->getEndLine();
    });

    // Sort by start line (greatest first)
    usort($possibleClasses, function ($a, $b) {
      return $b->getStartLine() - $a->getStartLine();
    });

    // If there's any class then the class where method is defined must be the first one (closest line)
    if (count($possibleClasses) > 0) {
      $className = $possibleClasses[0]->getName();
      array_walk($this->classes, function (&$class) use ($className, &$phpMethod) {
        if ($class->getName() === $className) {
          $phpMethod->setClassName($className);
          $class->addMethod($phpMethod);
        }
      });
    }

  }

}
