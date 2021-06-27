<?php

$targetDir = './';

$targetFile = $targetDir.basename($_FILES['file']['name']);
$tmpName = $_FILES['file']['tmp_name'];
$size = $_FILES['file']['size'];

functionIndirectTaintFileManipulation($targetFile, $tmpName, $size);

function functionIndirectTaintFileManipulation($targetFile, $tmpName, $size) {
  if (file_exists($targetFile)) {
    die('File exists');
  }

  if ($size > 500000) {
    die('Invalid file size');
  }

  if (!move_uploaded_file($tmpName, $targetFile)) {
    die('Error uploading');
  }
}

add_action('wp_ajax_nopriv_file_man', 'functionIndirectTaintFileManipulation');
