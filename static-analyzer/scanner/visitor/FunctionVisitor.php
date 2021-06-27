<?php

use PhpParser\{Node, NodeFinder, NodeTraverser, NodeVisitorAbstract};

require_once __DIR__ . '/../checker/NodeChecker.php';
require_once __DIR__ . '/../builder/FunctionMethodBuilder.php';

class FunctionVisitor extends NodeVisitorAbstract
{

  private array $functions;

  private NodeFinder $nodeFinder;

  public function __construct()
  {
    $this->functions = [];
    $this->nodeFinder = new NodeFinder();
  }

  /**
   * @return array
   */
  public function getFunctions(): array
  {
    return $this->functions;
  }

  public function enterNode(Node $node)
  {
    if (NodeChecker::isFunctionDefinition($node)) {
      $function = FunctionMethodBuilder::buildFromFunctionNode($node);
      $traverser = new NodeTraverser();
      if ($node->stmts != null) {
        if (count($function->getVarParams()) > 0) {
          $possibleCalls = [];
          $insecureCalls = [];
          $sanitizers = [];
          foreach ($function->getVarParams() as $param) {
            $taintVisitor = new TaintVisitor($param);
            $traverser->addVisitor($taintVisitor);
            $traverser->traverse($node->stmts);
            $possibleCalls = array_merge($possibleCalls, $taintVisitor->getCalls());
            $insecureCalls = array_merge($insecureCalls, $taintVisitor->getInsecureCalls());
            $sanitizers = $taintVisitor->getSanitizers();
          }
          $function->setPossibleCalls($possibleCalls);
          $function->setInsecureCalls($insecureCalls);
          $function->setSanitizers($sanitizers);
          $function->setIsControlledByUser($taintVisitor->isControlledByUser());
        } else {
          $taintVisitor = new TaintVisitor();
          $traverser->addVisitor($taintVisitor);
          $traverser->traverse($node->stmts);
          $function->setPossibleCalls($taintVisitor->getCalls());
          $function->setInsecureCalls($taintVisitor->getInsecureCalls());
          $function->setSanitizers($taintVisitor->getSanitizers());
          $function->setIsControlledByUser($taintVisitor->isControlledByUser());
        }
      }
      array_push($this->functions, $function);
    }
  }

  private function extractParamNames(PhpFunction $function) {
    return array_map(function ($param) {
      return $param->getName();
    }, $function->getVarParams());
  }

}
