<?php

$a = $_GET['a'];
$test = new MethodIndirectTaintXSS();
$test->methodIndirectTaintXSS($a);

class MethodIndirectTaintXSS
{

  public function methodIndirectTaintXSS($a)
  {
    printf('%s', $a);
  }

}