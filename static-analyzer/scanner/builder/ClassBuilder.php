<?php

use PhpParser\{Node, NodeFinder};

require_once __DIR__ . '/../../model/PhpClass.php';

class ClassBuilder {


  public static function buildFromNode(Node $node): PhpClass
  {
    $class = new PhpClass();

    if (isset($node->extends) && isset($node->extends->parts)) {
      $class->setParentClasses($node->extends->parts);
    }

    $class->setIsInterface($node instanceof Node\Stmt\Interface_);
    $class->setName($node->name->name);
    $class->setStartLine($node->getStartLine());
    $class->setEndLine($node->getEndLine());

    return $class;
  }
}
