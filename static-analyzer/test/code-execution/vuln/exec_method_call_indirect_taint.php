<?php

$a = $_GET['a'];
$b = $_GET['b'];

$test = new MethodCallIndirectTaintCodeExecution();
$test->methodCallIndirectTaintCodeExecution($a, $b);

class MethodCallIndirectTaintCodeExecution
{

  public function methodCallIndirectTaintCodeExecution($a, $b)
  {
    preg_replace($a, $b, 't');
  }

}