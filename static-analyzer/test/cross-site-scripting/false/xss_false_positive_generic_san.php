<?php

$a = $_GET['a'];
$test = new FalsePositiveGenericSan();
$test->falsePositiveGeneric($a);

class FalsePositiveGenericSan
{

  public function falsePositiveGeneric($a)
  {
    $fix = intval($a);
    printf('%s', $fix);
  }

}
