<?php

$targetDir = './';

if (file_exists($targetDir.basename($_FILES['file']['name']))) {
  die('File exists');
}

if ($_FILES['file']['size'] > 500000) {
  die('Invalid file size');
}

if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetDir.basename($_FILES['file']['name']))) {
  die('Error uploading');
}