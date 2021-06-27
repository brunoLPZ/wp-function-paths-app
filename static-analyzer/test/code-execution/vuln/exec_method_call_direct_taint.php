<?php

$test = new MethodCallDirectTaintCodeExecution();
$test->methodCallDirectTaintCodeExecution($_GET['a'], $_GET['b']);

class MethodCallDirectTaintCodeExecution
{

  public function methodCallDirectTaintCodeExecution($a, $b)
  {
    preg_filter($a, $b, 't');
  }

}