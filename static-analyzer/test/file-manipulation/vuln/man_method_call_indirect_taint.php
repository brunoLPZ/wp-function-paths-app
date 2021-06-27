<?php

$targetDir = './';
$test = new MethodIndirectTaintFileManipulation();

$targetFile = $targetDir.basename($_FILES['file']['name']);
$tmpName = $_FILES['file']['tmp_name'];
$size = $_FILES['file']['size'];

$test->methodIndirectTaintFileManipulation($targetFile, $tmpName, $size);

class MethodIndirectTaintFileManipulation {
  function methodIndirectTaintFileManipulation($targetFile, $tmpName, $size) {
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
}