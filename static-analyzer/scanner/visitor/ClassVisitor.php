<?php

use PhpParser\{Node, NodeFinder, NodeTraverser, NodeVisitorAbstract};

require_once __DIR__ . '/../checker/NodeChecker.php';
require_once __DIR__ . '/../builder/ClassBuilder.php';

class ClassVisitor extends NodeVisitorAbstract
{

  private array $classes;

  private NodeFinder $nodeFinder;

  public function __construct()
  {
    $this->classes = [];
    $this->nodeFinder = new NodeFinder();
  }

  /**
   * @return array
   */
  public function getClasses(): array
  {
    return $this->classes;
  }

  public function enterNode(Node $node)
  {
    if (NodeChecker::isClassDefinition($node)) {
      array_push($this->classes, ClassBuilder::buildFromNode($node));
    }
  }

}
