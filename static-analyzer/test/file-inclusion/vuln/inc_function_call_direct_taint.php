<?php

functionDirectTaintFileInclusion($_GET['a']);

function functionDirectTaintFileInclusion($a) {
  require $a;
}
