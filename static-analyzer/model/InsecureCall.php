<?php

use Ramsey\Uuid\Uuid;

class InsecureCall implements JsonSerializable {

  private string $uuid;
  private string $name;
  private string $vulnerability;
  private int $params;
  private array $varParams = [];
  private int $line;
  private bool $isTainted;

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
   * @return string
   */
  public function getVulnerability(): string
  {
    return $this->vulnerability;
  }

  /**
   * @param string $vulnerability
   */
  public function setVulnerability(string $vulnerability): void
  {
    $this->vulnerability = $vulnerability;
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
   * @return array
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
   * @param bool $isTainted
   */
  public function setIsTainted(bool $isTainted): void
  {
    $this->isTainted = $isTainted;
  }

  /**
   * @return bool
   */
  public function isTainted(): bool
  {
    return $this->isTainted;
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }


}
