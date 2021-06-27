<?php

$a = $_GET['a'];

functionComplexTaintFileInclusion($a, 'other');

function functionComplexTaintFileInclusion($data, $aux) {
  $testClass = new ComplexTaintFileInclusion();
  $testClass->methodComplexTaintFileInclusion($data, $aux);
}

class ComplexTaintFileInclusion {

  public function methodComplexTaintFileInclusion($first, $second) {
    $non_used = $second;
    include $first;
  }

}
