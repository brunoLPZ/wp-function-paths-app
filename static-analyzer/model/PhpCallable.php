<?php

use Ramsey\Uuid\Uuid;

/**
 * Class PhpCallable
 */
class PhpCallable implements JsonSerializable
{
  private string $uuid;
  private string $name;
  private int $params;
  private array $varParams = [];
  private int $startLine;
  private int $endLine;
  private array $insecureCalls = [];
  private array $possibleCalls = [];
  private bool $isControlledByUser;
  private array $sanitizers = [];

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
   * @return array|int
   */
  public function getParams()
  {
    return $this->params;
  }

  /**
   * @param array|int $params
   */
  public function setParams($params): void
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
  public function getInsecureCalls(): array
  {
    return $this->insecureCalls;
  }

  /**
   * @param array $insecureCalls
   */
  public function setInsecureCalls(array $insecureCalls): void
  {
    $this->insecureCalls = $insecureCalls;
  }

  /**
   * @return array
   */
  public function getPossibleCalls(): array
  {
    return $this->possibleCalls;
  }

  /**
   * @param array $possibleCalls
   */
  public function setPossibleCalls(array $possibleCalls): void
  {
    $this->possibleCalls = $possibleCalls;
  }

  /**
   * @return bool
   */
  public function isControlledByUser(): bool
  {
    return $this->isControlledByUser;
  }

  /**
   * @param bool $isControlledByUser
   */
  public function setIsControlledByUser(bool $isControlledByUser): void
  {
    $this->isControlledByUser = $isControlledByUser;
  }

  /**
   * @return array
   */
  public function getSanitizers(): array
  {
    return $this->sanitizers;
  }

  /**
   * @param array $sanitizers
   */
  public function setSanitizers(array $sanitizers): void
  {
    $this->sanitizers = $sanitizers;
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }

}
