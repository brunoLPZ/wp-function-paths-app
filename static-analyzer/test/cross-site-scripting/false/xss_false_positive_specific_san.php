<?php

$a = $_GET['a'];
$test = new FalsePositiveSpecificSan();
$test->falsePositiveSpecific($a);

class FalsePositiveSpecificSan
{

  public function falsePositiveSpecific($a)
  {
    $fix = htmlentities($a);
    printf('%s', $fix);
  }

}
