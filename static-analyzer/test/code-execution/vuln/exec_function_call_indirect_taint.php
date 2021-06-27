<?php

$a = $_GET['a'];
$b = $_GET['b'];

functionIndirectTaintCodeExecution($a, $b);

function functionIndirectTaintCodeExecution($a, $b) {
  mb_ereg_replace($a, $b, 't');
}

add_action('wp_ajax_nopriv_code_exec', 'functionIndirectTaintCodeExecution');
