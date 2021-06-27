<?php

$a = $_GET['a'];

functionComplexTaintPathCodeExecution($a, 'other');

function functionComplexTaintPathCodeExecution($data, $aux) {
  $testClass = new ComplexTaintPathCodeExecution();
  $testClass->methodComplexTaintPathCodeExecution($data, $aux);
}

class ComplexTaintPathCodeExecution {

  public function methodComplexTaintPathCodeExecution($first, $second) {
    $non_used = $second;
    assert($first);
  }

}
