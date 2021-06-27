<?php

$targetDir = './';
$test = new MethodDirectTaintFileManipulation();
$test->methodDirectTaintFileManipulation($targetDir.basename($_FILES['file']['name']), $_FILES['file']['tmp_name'], $_FILES['file']['size']);

class MethodDirectTaintFileManipulation {
  function methodDirectTaintFileManipulation($targetFile, $tmpName, $size) {
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