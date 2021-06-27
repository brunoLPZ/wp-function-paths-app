<?php

$a = $_GET['a'];
$test = new MethodIndirectTaintFileInclusion();
$test->methodIndirectTaintFileInclusion($a);

class MethodIndirectTaintFileInclusion
{

  public function methodIndirectTaintFileInclusion($a)
  {
    virtual($a);
  }

}