<?php

$test = new MethodDirectTaintFileInclusion();
$test->methodDirectTaintFileInclusion($_GET['a']);

class MethodDirectTaintFileInclusion
{

  public function methodDirectTaintFileInclusion($a)
  {
    set_include_path($a);
  }

}