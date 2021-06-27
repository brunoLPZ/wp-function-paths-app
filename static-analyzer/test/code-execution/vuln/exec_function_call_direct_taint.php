<?php

functionDirectTaintCodeExecution($_GET['a']);

function functionDirectTaintCodeExecution($a) {
  eval($a);
}
