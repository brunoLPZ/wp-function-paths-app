<?php

functionDirectTaintXSS($_GET['a']);

function functionDirectTaintXSS($a) {
  print_r($a);
}
