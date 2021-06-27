<?php

require_once __DIR__ . '/../../model/PossibleCall.php';
require_once __DIR__ . '/../../model/InsecureCall.php';
require_once __DIR__ . '/../../model/Parameter.php';

use PhpParser\{Node, NodeFinder};

class CallBuilder
{

  public static function buildFromFunctionNode(Node $node, array $taintedVars,
                                               array $taintedVarsByParam, $condition): PossibleCall
  {
    $call = new PossibleCall();
    $call->setName(end($node->name->parts));
    $call->setLine($node->getStartLine());
    $call->setParams(count($node->args));
    $call->setVarParams(CallBuilder::getVariableParams($node));
    $directTaintedParams = self::getDirectTaintedParams($call, $taintedVars);
    $conditionalTaintedParams = self::getConditionalTaintedParams($call, $taintedVarsByParam);
    $call->setIsTainted(count($directTaintedParams) > 0 || count($conditionalTaintedParams) > 0);
    if (count($directTaintedParams) === 0 && count($conditionalTaintedParams) > 0) {
      $call->setCondition($condition);
    }
    $call->setPositions(self::getTaintedPositions($directTaintedParams, $conditionalTaintedParams));
    return $call;
  }

  public static function buildFromMethodNode($className, Node $node, array $taintedVars,
                                             array $taintedVarsByParam, $condition): PossibleCall
  {
    $call = new PossibleCall();
    $call->setClassName($className);
    $call->setName($node->name->name);
    $call->setLine($node->getStartLine());
    $call->setParams(count($node->args));
    $call->setVarParams(CallBuilder::getVariableParams($node));
    $directTaintedParams = self::getDirectTaintedParams($call, $taintedVars);
    $conditionalTaintedParams = self::getConditionalTaintedParams($call, $taintedVarsByParam);
    $call->setIsTainted(count($directTaintedParams) > 0 || count($conditionalTaintedParams) > 0);
    if (count($directTaintedParams) === 0 && count($conditionalTaintedParams) > 0) {
      $call->setCondition($condition);
    }
    $call->setPositions(self::getTaintedPositions($directTaintedParams, $conditionalTaintedParams));
    return $call;
  }

  public static function buildInsecureCall(Node $node, array $taintedVars,
                                           array $taintedVarsByParam): InsecureCall
  {
    $call = new InsecureCall();
    $call->setName(end($node->name->parts));
    $call->setLine($node->getStartLine());
    $call->setParams(count($node->args));
    $call->setVarParams(CallBuilder::getVariableParams($node));
    $directTaintedParams = self::getDirectTaintedParams($call, $taintedVars);
    $conditionalTaintedParams = self::getConditionalTaintedParams($call, $taintedVarsByParam);
    $call->setIsTainted(count($directTaintedParams) > 0 || count($conditionalTaintedParams) > 0);
    $call->setVulnerability(INSECURE_FUNCTIONS[end($node->name->parts)]);
    return $call;
  }

  public static function buildInsecureCallFromExpr(string $exprType, Node $node, array $taintedVars,
                                                   array $taintedVarsByParam): InsecureCall
  {
    $stmts = $node instanceof Node\Stmt\Echo_ ? $node->exprs : $node->expr;
    $variables = self::getExprVariables($stmts);
    $call = new InsecureCall();
    $call->setName($exprType);
    $call->setLine($node->getStartLine());
    $call->setParams(count($variables));
    $call->setVarParams($variables);
    $directTaintedParams = self::getDirectTaintedParams($call, $taintedVars);
    $conditionalTaintedParams = self::getConditionalTaintedParams($call, $taintedVarsByParam);
    $call->setIsTainted(count($directTaintedParams) > 0 || count($conditionalTaintedParams) > 0);
    $call->setVulnerability(INSECURE_FUNCTIONS[$exprType]);
    return $call;
  }

  private static function getTaintedPositions(array $directTaintedParams, array $conditionalTaintedParams): array {
    $positions = [];
    if (count($directTaintedParams) === 0 && count($conditionalTaintedParams) > 0) {
      foreach ($conditionalTaintedParams as $p) {
        if (!in_array($p->getPosition(), $positions)) {
          array_push($positions, $p->getPosition());
        }
      }
    } else {
      foreach ($directTaintedParams as $p) {
        if (!in_array($p->getPosition(), $positions)) {
          array_push($positions, $p->getPosition());
        }
      }
    }
    return $positions;
  }

  private static function getExprVariables($stmts): array
  {
    $nodeFinder = new NodeFinder();
    $variables = $nodeFinder->findInstanceOf($stmts, Node\Expr\Variable::class);
    return array_map(function ($varNode) {
      $param = new Parameter();
      $param->setName($varNode->name);
      return $param;
    }, $variables);
  }

  private static function getVariableParams(Node $node): array
  {
    $nodeFinder = new NodeFinder();
    $varNodesByPosition = [];
    foreach ($node->args as $arg) {
      array_push($varNodesByPosition, $nodeFinder->find($arg, function ($node) {
        return $node instanceof Node\Expr\Variable;
      }));
    }
    $params = [];

    $position = 0;

    foreach ($varNodesByPosition as $varNodes) {
      foreach ($varNodes as $varNode) {
        $parameter = new Parameter();
        $parameter->setPosition($position);
        $parameter->setName($varNode->name);
        array_push($params, $parameter);
      }
      $position++;
    }
    return $params;
  }

  private static function getDirectTaintedParams($call, $taintedVars): array
  {
    $taintedParams = [];
    foreach ($call->getVarParams() as $param) {
      if (in_array($param->getName(), $taintedVars) || in_array($param->getName(), TAINTED_VARS)) {
        array_push($taintedParams, $param);
      }
    }
    return $taintedParams;
  }

  private static function getConditionalTaintedParams($call, $taintedVars): array
  {
    $taintedParams = [];
    foreach ($call->getVarParams() as $param) {
      if (in_array($param->getName(), $taintedVars)) {
        array_push($taintedParams, $param);
      }
    }
    return $taintedParams;
  }

}
