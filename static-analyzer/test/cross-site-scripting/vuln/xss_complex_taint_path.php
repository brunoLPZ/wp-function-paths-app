<?php

$a = $_GET['a'];

functionComplexTaintXSS($a, 'other');

function functionComplexTaintXSS($data, $aux) {
  $testClass = new ComplexTaintXSS();
  $testClass->methodComplexTaintXSS($data, $aux);
}

class ComplexTaintXSS {

  public function methodComplexTaintXSS($first, $second) {
    $non_used = $second;
    print $first;
  }

}
