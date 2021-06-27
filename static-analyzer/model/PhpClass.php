<?php

use Ramsey\Uuid\Uuid;

/**
 * Class PhpClass
 */
class PhpClass implements JsonSerializable
{

  private string $uuid;
  private string $name;
  private array $parentClasses = [];
  private int $startLine;
  private int $endLine;
  private bool $isInterface;
  private array $methods = [];

  public function __construct()
  {
    $this->uuid = Uuid::uuid4()->toString();
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name): void
  {
    $this->name = $name;
  }

  /**
   * @return array
   */
  public function getParentClasses(): array
  {
    return $this->parentClasses;
  }

  /**
   * @param array $parentClasses
   */
  public function setParentClasses(array $parentClasses): void
  {
    $this->parentClasses = $parentClasses;
  }

  /**
   * @return int
   */
  public function getStartLine(): int
  {
    return $this->startLine;
  }

  /**
   * @param int $startLine
   */
  public function setStartLine(int $startLine): void
  {
    $this->startLine = $startLine;
  }

  /**
   * @return int
   */
  public function getEndLine(): int
  {
    return $this->endLine;
  }

  /**
   * @param int $endLine
   */
  public function setEndLine(int $endLine): void
  {
    $this->endLine = $endLine;
  }

  /**
   * @return array
   */
  public function getMethods(): array
  {
    return $this->methods;
  }

  /**
   * @param array $methods
   */
  public function setMethods(array $methods): void
  {
    $this->methods = $methods;
  }

  /**
   * Add a new method to the class
   * @param PhpMethod $method
   */
  public function addMethod(PhpMethod $method) {
    array_push($this->methods, $method);
  }

  /**
   * @return bool
   */
  public function isInterface(): bool
  {
    return $this->isInterface;
  }

  /**
   * @param bool $isInterface
   */
  public function setIsInterface(bool $isInterface): void
  {
    $this->isInterface = $isInterface;
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }


}
