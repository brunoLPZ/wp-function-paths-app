<?php

$a = $_GET['a'];

functionIndirectTaintFileInclusion($a);

function functionIndirectTaintFileInclusion($a) {
  require_once $a;
}

add_action('wp_ajax_nopriv_file_inc', 'functionIndirectTaintFileInclusion');
