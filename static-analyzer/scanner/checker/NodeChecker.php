<?php

use PhpParser\{Node};

class NodeChecker
{

  public static function isDifferentContextNode(Node $node): bool
  {
    return $node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Trait_ ||
      $node instanceof Node\Stmt\Function_ || $node instanceof Node\Stmt\ClassMethod;
  }

  public static function isAssignExpr(Node $node): bool
  {
    return $node instanceof Node\Expr\Assign;
  }

  public static function isFunctionCall(Node $node): bool
  {
    return $node instanceof Node\Expr\FuncCall && isset($node->name->parts) &&
      !in_array(end($node->name->parts), PHP_INTERNAL_FUNCTIONS);
  }

  public static function isHook(Node $node): bool
  {
    return $node instanceof Node\Expr\FuncCall && isset($node->name->parts) &&
      in_array(end($node->name->parts), WP_HOOKS);
  }

  public static function isMethodCall(Node $node): bool
  {
    return $node instanceof Node\Expr\MethodCall && isset($node->name->name);
  }

  public static function isMethodCallWithVarContext(Node $node): bool
  {
    return isset($node->var->name) && is_string($node->var->name) && $node->var->name !== 'this';
  }

  public static function isMethodCallWithThisContext(Node $node): bool
  {
    return $node->var instanceof Node\Expr\Variable && $node->var->name === 'this';
  }

  public static function isMethodCallWithThisAndVarContext(Node $node): bool
  {
    return $node->var instanceof Node\Expr\PropertyFetch && $node->var->var->name === 'this';
  }

  public static function isStaticCall(Node $node): bool
  {
    return $node instanceof Node\Expr\StaticCall;
  }

  public static function isStaticCallFromClass(Node $node): bool
  {
    return isset($node->class->parts);
  }

  public static function isStaticCallWithVarContext(Node $node): bool
  {
    return $node->class instanceof Node\Expr\Variable;
  }

  public static function isInsecureCall(Node $node): bool
  {
    return $node instanceof Node\Expr\FuncCall && isset($node->name->parts) &&
      isset(INSECURE_FUNCTIONS[end($node->name->parts)]);
  }

  public static function isFunctionDefinition(Node $node): bool
  {
    return $node instanceof Node\Stmt\Function_;
  }

  public static function isClassDefinition(Node $node): bool
  {
    return ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Trait_ ||
      $node instanceof Node\Stmt\Interface_) && isset($node->name->name);
  }

  public static function isMethodDefinition(Node $node): bool
  {
    return $node instanceof Node\Stmt\ClassMethod;
  }

  public static function isIncludeExpr(Node $node): bool
  {
    return $node instanceof Node\Expr\Include_;
  }

  public static function isPrintExpr(Node $node): bool
  {
    return $node instanceof Node\Expr\Print_ || $node instanceof Node\Stmt\Echo_ ||
      $node instanceof Node\Expr\Exit_;
  }

  public static function isEvalExpr(Node $node): bool
  {
    return $node instanceof Node\Expr\Eval_;
  }

  public static function isControlledByUserVar(Node $node): bool
  {
    return $node instanceof Node\Expr\Variable && in_array($node->name, TAINTED_VARS);
  }

  public static function isSanitizer(Node $node): bool
  {
    return $node instanceof Node\Expr\FuncCall && isset($node->name->parts) &&
      isset(SANITIZERS[end($node->name->parts)]);
  }

}
