<?php

class PossibleCall implements JsonSerializable {

  private $className;
  private string $name;
  private int $line;
  private int $params;
  private array $varParams;
  private bool $isTainted;
  private array $positions = [];
  private $condition;

  public function getClassName()
  {
    return $this->className;
  }

   public function setClassName($className): void
  {
    $this->className = $className;
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
   * @return int
   */
  public function getLine(): int
  {
    return $this->line;
  }

  /**
   * @param int $line
   */
  public function setLine(int $line): void
  {
    $this->line = $line;
  }

  /**
   * @return int
   */
  public function getParams(): int
  {
    return $this->params;
  }

  /**
   * @param int $params
   */
  public function setParams(int $params): void
  {
    $this->params = $params;
  }

  /**
   * @return string
   */
  public function getVarParams(): array
  {
    return $this->varParams;
  }

  /**
   * @param array $varParams
   */
  public function setVarParams(array $varParams): void
  {
    $this->varParams = $varParams;
  }

  /**
   * @return bool
   */
  public function isTainted(): bool
  {
    return $this->isTainted;
  }

  /**
   * @param bool $isTainted
   */
  public function setIsTainted(bool $isTainted): void
  {
    $this->isTainted = $isTainted;
  }

  /**
   * @param array $positions
   */
  public function setPositions(array $positions): void
  {
    $this->positions = $positions;
  }

  /**
   * @return mixed
   */
  public function getCondition()
  {
    return $this->condition;
  }

  /**
   * @param mixed $condition
   */
  public function setCondition($condition): void
  {
    $this->condition = $condition;
  }

  /**
   * @return array
   */
  public function getPositions(): array
  {
    return $this->positions;
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }

}
