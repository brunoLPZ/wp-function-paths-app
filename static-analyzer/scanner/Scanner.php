<?php

use PhpParser\{ParserFactory, NodeTraverser};

require_once __DIR__ . '/visitor/ClassVisitor.php';
require_once __DIR__ . '/visitor/FunctionVisitor.php';
require_once __DIR__ . '/visitor/HookVisitor.php';
require_once __DIR__ . '/visitor/MethodVisitor.php';
require_once __DIR__ . '/visitor/TaintVisitor.php';
require_once __DIR__ . '/../model/PhpFile.php';

class Scanner {

  private $parser;
  private PhpFile $file;

  public function __construct($path)
  {
    $this->file = new PhpFile();
    $this->file->setPath($path);
    $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
  }

  public function scan() {
    try {
      $stmts = $this->parseFile();
      $this->fileTaintAnalysis($stmts);
      $this->extractHooks($stmts);
      $this->extractFunctions($stmts);
      $this->extractClasses($stmts);
      $this->extractMethods($stmts);
      $this->printResult();
    } catch (PhpParser\Error $e) {
      $this->printResult();
    }
  }

  private function fileTaintAnalysis(array $stmts) {
    $traverser = new NodeTraverser();
    $taintVisitor = new TaintVisitor();
    $traverser->addVisitor($taintVisitor);
    $traverser->traverse($stmts);
    $this->file->setPossibleCalls($taintVisitor->getCalls());
    $this->file->setInsecureCalls($taintVisitor->getInsecureCalls());
    $this->file->setIsControlledByUser($taintVisitor->isControlledByUser());
    $this->file->setSanitizers($taintVisitor->getSanitizers());
  }

  private function extractHooks(array $stmts) {
    $traverser = new NodeTraverser();
    $hookVisitor = new HookVisitor();
    $traverser->addVisitor($hookVisitor);
    $traverser->traverse($stmts);
    $this->file->setHooks($hookVisitor->getHooks());
  }

  private function extractFunctions(array $stmts) {
    $traverser = new NodeTraverser();
    $functionVisitor = new FunctionVisitor();
    $traverser->addVisitor($functionVisitor);
    $traverser->traverse($stmts);
    $this->file->setFunctions($functionVisitor->getFunctions());
  }

  private function extractClasses(array $stmts) {
    $traverser = new NodeTraverser();
    $classVisitor = new ClassVisitor();
    $traverser->addVisitor($classVisitor);
    $traverser->traverse($stmts);
    $this->file->setClasses($classVisitor->getClasses());
  }

  private function extractMethods(array $stmts) {
    $traverser = new NodeTraverser();
    $classes = $this->file->getClasses();
    $methodVisitor = new MethodVisitor($classes);
    $traverser->addVisitor($methodVisitor);
    $traverser->traverse($stmts);
  }

  private function parseFile(): array
  {
    $code = file_get_contents($this->file->getPath());
    return $this->parser->parse($code);
  }

  private function printResult() {
    echo json_encode($this->file, JSON_PRETTY_PRINT);
  }

}
