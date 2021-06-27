<?php

use PhpParser\{Node, NodeFinder};
require_once __DIR__ . '/../../model/WpHook.php';

class HookBuilder {

  public static function buildFromNode(Node $node, $name, $triggeredFunction): WpHook {
    $wpHook = new WpHook;
    $wpHook->setType(end($node->name->parts));
    $wpHook->setName($name);
    $wpHook->setTriggeredFunction($triggeredFunction);
    return $wpHook;
  }



}
