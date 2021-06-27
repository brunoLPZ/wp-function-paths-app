<?php

require_once __DIR__ . '/PhpCallable.php';

/**
 * Class PhpMethod
 */
class PhpMethod extends PhpCallable implements JsonSerializable {

  private string $className;

  /**
   * @return string
   */
  public function getClassName(): string
  {
    return $this->className;
  }

  /**
   * @param string $className
   */
  public function setClassName(string $className): void
  {
    $this->className = $className;
  }

  public function jsonSerialize()
  {
    return array_merge(parent::jsonSerialize(), get_object_vars($this));
  }

}
