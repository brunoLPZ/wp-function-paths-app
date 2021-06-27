<?php

$a = $_GET['a'];

functionIndirectTaintXSS($a);

function functionIndirectTaintXSS($a) {
  exit($a);
}

add_action('wp_ajax_nopriv_xss', 'functionIndirectTaintXSS');
