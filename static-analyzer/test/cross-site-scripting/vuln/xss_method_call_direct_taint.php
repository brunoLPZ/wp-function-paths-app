<?php

$test = new MethodDirectTaintXSS();
$test->methodDirectTaintXSS($_GET['a']);

class MethodDirectTaintXSS
{

  public function methodDirectTaintXSS($a)
  {
    die($a);
  }

}