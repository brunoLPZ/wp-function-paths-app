<?php

use PhpParser\{Node, NodeFinder, NodeTraverser, NodeVisitorAbstract};

require_once __DIR__ . '/../checker/NodeChecker.php';
require_once __DIR__ . '/../builder/CallBuilder.php';
require_once __DIR__ . '/../../util/GlobalObjectInstances.php';

class TaintVisitor extends NodeVisitorAbstract
{

  private $currentClass;
  private array $taintedVars;
  private array $taintedVarsByParam;
  private array $calls;
  private array $insecureCalls;
  private array $sanitizers;
  private bool $isControlledByUser;
  private $condition;

  private NodeFinder $nodeFinder;

  public function __construct($param = null, $currentClass = null)
  {
    $this->currentClass = $currentClass;
    $this->taintedVars = [];
    $this->taintedVarsByParam = $param == null ? [] : [$param->getName()];
    $this->condition = $param == null ? null : $param->getPosition();
    $this->insecureCalls = [];
    $this->calls = [];
    $this->sanitizers = [];
    $this->isControlledByUser = false;
    $this->nodeFinder = new NodeFinder();
  }

  /**
   * @return array
   */
  public function getCalls(): array
  {
    return $this->calls;
  }

  /**
   * @return array
   */
  public function getInsecureCalls(): array
  {
    return $this->insecureCalls;
  }

  /**
   * @return bool
   */
  public function isControlledByUser(): bool
  {
    return $this->isControlledByUser;
  }

  /**
   * @return array
   */
  public function getSanitizers(): array
  {
    return $this->sanitizers;
  }

  public function enterNode(Node $node)
  {
    // DONT TRAVERSE if new context begins (class, function, method or trait definition)
    if (NodeChecker::isDifferentContextNode($node)) {
      return NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }
    // Deal with ASSIGN expressions
    if (NodeChecker::isAssignExpr($node)) {
      // Extract OBJECT INSTANTIATION assigned to variables
      $this->extractNewExprs($node);
      // If variable IS TAINTED add it to the list
      if ($this->isTaintedVar($node)) {
        $this->addTaintedVar($node);
      } // If variable IS TAINTED BY PARAM add it to additional list
      else if ($this->isTaintedVarByParam($node)) {
        $this->addTaintedVarByParam($node);
      }
      // If variable becomes UNTAINTED remove it from the list
      else {
        $this->removeTaintedVar($node);
      }
    } // Deal with SANITIZERS
    else if (NodeChecker::isSanitizer($node) &&
      !in_array(SANITIZERS[end($node->name->parts)], $this->sanitizers)) {
      array_push($this->sanitizers, SANITIZERS[end($node->name->parts)]);
    } // Deal with INSECURE CALLS
    else if (NodeChecker::isInsecureCall($node)) {
      array_push($this->insecureCalls,
        CallBuilder::buildInsecureCall($node, $this->taintedVars, $this->taintedVarsByParam));
    } // Deal with FUNCTION CALLS defined by user
    else if (NodeChecker::isFunctionCall($node)) {
      array_push($this->calls,
        CallBuilder::buildFromFunctionNode($node, $this->taintedVars, $this->taintedVarsByParam, $this->condition));
    } // Deal with METHOD CALLS
    else if (NodeChecker::isMethodCall($node)) {
      $className = null;
      // Resolve classname for calls like: $VAR->METHOD()
      if (NodeChecker::isMethodCallWithVarContext($node)) {
        $className = GlobalObjectInstances::getObjectByVar($node->var->name);
      } // Resolve classname for calls like: $THIS->METHOD()
      else if (NodeChecker::isMethodCallWithThisContext($node)) {
        $className = $this->currentClass;
      } // Resolve classname for calls like: $THIS->PROPERTY->METHOD()
      else if (NodeChecker::isMethodCallWithThisAndVarContext($node)) {
        $className = GlobalObjectInstances::getObjectByVar($node->var->name->name);
      }
      array_push($this->calls,
        CallBuilder::buildFromMethodNode($className, $node, $this->taintedVars, $this->taintedVarsByParam, $this->condition));
    } // Deal with STATIC METHOD CALLS
    else if (NodeChecker::isStaticCall($node)) {
      $className = null;
      // Resolve classname for calls like: CLASS::METHOD()
      if (NodeChecker::isStaticCallFromClass($node)) {
        $className = end($node->class->parts);
        if ($className === 'self' || $className === 'static') {
          $className = $this->currentClass;
        }
      } // Resolve classname for calls like: $VAR::METHOD()
      else if (NodeChecker::isStaticCallWithVarContext($node)) {
        $className = GlobalObjectInstances::getObjectByVar($node->class->name);
      }
      array_push($this->calls,
        CallBuilder::buildFromMethodNode($className, $node, $this->taintedVars, $this->taintedVarsByParam, $this->condition));
    }  // Deal with INCLUDE EXPRESSIONS
    else if (NodeChecker::isIncludeExpr($node)) {
      array_push($this->insecureCalls,
        CallBuilder::buildInsecureCallFromExpr('INCLUDE_EXPR', $node, $this->taintedVars, $this->taintedVarsByParam));
    } // Deal with PRINT EXPRESSIONS
    else if (NodeChecker::isPrintExpr($node)) {
      array_push($this->insecureCalls,
        CallBuilder::buildInsecureCallFromExpr('PRINT_EXPR', $node, $this->taintedVars, $this->taintedVarsByParam));
    } // Deal with EVAL EXPRESSIONS
    else if (NodeChecker::isEvalExpr($node)) {
      array_push($this->insecureCalls,
        CallBuilder::buildInsecureCallFromExpr('EVAL_EXPR', $node, $this->taintedVars, $this->taintedVarsByParam));
    } // Determine if USER HAS DIRECT CONTROL
    else if (NodeChecker::isControlledByUserVar($node)) {
      $this->isControlledByUser = true;
    }
  }


  private function isTaintedVar(Node $node): bool
  {
    return count($this->extractRightAssignmentTaintedVars($node)) > 0;
  }

  private function isTaintedVarByParam(Node $node): bool
  {
    return count($this->extractRightAssignmentTaintedVarsByParam($node)) > 0;
  }

  private function extractRightAssignmentTaintedVars(Node $node): array
  {
    return $this->nodeFinder->find($node->expr, function ($node) {
      return $node instanceof Node\Expr\Variable &&
        (in_array($node->name, TAINTED_VARS) || in_array($node->name, $this->taintedVars));
    });
  }

  private function extractRightAssignmentTaintedVarsByParam(Node $node): array
  {
    return $this->nodeFinder->find($node->expr, function ($node) {
      return $node instanceof Node\Expr\Variable && in_array($node->name, $this->taintedVarsByParam);
    });
  }

  private function addTaintedVar(Node $node)
  {
    $assignedVar = $this->extractLeftAssigmentVar($node);
    if ($assignedVar != null && !in_array($assignedVar->name, $this->taintedVars)) {
      array_push($this->taintedVars, $assignedVar->name);
    }
  }

  private function addTaintedVarByParam(Node $node)
  {
    $assignedVar = $this->extractLeftAssigmentVar($node);
    if ($assignedVar != null && !in_array($assignedVar->name, $this->taintedVarsByParam)) {
      array_push($this->taintedVarsByParam, $assignedVar->name);
    }
  }

  private function removeTaintedVar(Node $node)
  {
    $assignedVar = $this->extractLeftAssigmentVar($node);
    if ($assignedVar != null) {
      if (($key = array_search($assignedVar->name, $this->taintedVars)) !== false) {
        unset($this->taintedVars[$key]);
      } else if (($key = array_search($assignedVar->name, $this->taintedVarsByParam)) !== false) {
        unset($this->taintedVarsByParam[$key]);
      }
    }
  }

  private function extractNewExprs(Node $node)
  {
    $newExpr = $this->nodeFinder->findFirstInstanceOf($node->expr, Node\Expr\New_::class);
    $assignedVar = $this->extractLeftAssigmentVar($node);
    if ($newExpr != null && $assignedVar != null && isset($newExpr->class->parts)) {
      GlobalObjectInstances::pushObject($assignedVar->name, end($newExpr->class->parts));
    }
  }

  private function extractLeftAssigmentVar(Node $node)
  {
    return $this->nodeFinder->findFirst($node->var, function ($node) {
      return $node instanceof Node\Expr\Variable && $node->name !== 'this' || $node instanceof Node\Identifier;
    });
  }

}
