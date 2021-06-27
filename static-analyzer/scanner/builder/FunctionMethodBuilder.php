<?php

use PhpParser\Node;

require_once __DIR__ . '/../../model/PhpFunction.php';
require_once __DIR__ . '/../../model/PhpMethod.php';
require_once __DIR__ . '/../../model/Parameter.php';

class FunctionMethodBuilder {

  public static function buildFromFunctionNode(Node $node): PhpFunction {
    $function = new PhpFunction();

    return self::buildCallableItem($node, $function);
  }

  public static function buildFromMethodNode(Node $node): PhpMethod {
    $method = new PhpMethod();

    return self::buildCallableItem($node, $method);
  }

  private static function buildCallableItem(Node $node, PhpCallable $item): PhpCallable {
    $item->setName($node->name->name);
    $item->setStartLine($node->getStartLine());
    $item->setEndLine($node->getEndLine());
    $item->setParams(count($node->getParams()));
    $position = 0;
    $params = [];
    foreach ($node->getParams() as $paramNode) {
      $param = new Parameter();
      $param->setPosition($position);
      $param->setName($paramNode->var->name);
      $position++;
      array_push($params, $param);
    }
    $item->setVarParams($params);

    return $item;
  }

}
