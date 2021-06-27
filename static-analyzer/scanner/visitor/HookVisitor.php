<?php

use PhpParser\{Node, NodeFinder, NodeTraverser, NodeVisitorAbstract};

require_once __DIR__ . '/../checker/NodeChecker.php';
require_once __DIR__ . '/../builder/HookBuilder.php';

class HookVisitor extends NodeVisitorAbstract
{

  private array $hooks;

  private NodeFinder $nodeFinder;

  public function __construct()
  {
    $this->hooks = [];
    $this->nodeFinder = new NodeFinder();
  }

  /**
   * @return array
   */
  public function getHooks(): array
  {
    return $this->hooks;
  }

  public function enterNode(Node $node)
  {
    if (NodeChecker::isHook($node)) {
      $scalarNodes = $this->nodeFinder->findInstanceOf($node->args, Node\Scalar\String_::class);
      if (count($scalarNodes) > 1) {
        array_push($this->hooks,
          HookBuilder::buildFromNode($node, $scalarNodes[0]->value, $scalarNodes[1]->value));
      }
    }
  }

}
