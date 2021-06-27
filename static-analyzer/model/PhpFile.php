<?php

use Ramsey\Uuid\Uuid;

/**
 * Class PhpFile
 */
class PhpFile implements JsonSerializable
{

  private string $uuid;
  private string $path;
  private array $functions = [];
  private array $classes = [];
  private array $insecureCalls = [];
  private array $possibleCalls = [];
  private array $hooks = [];
  private bool $isControlledByUser;
  private array $sanitizers = [];

  public function __construct()
  {
    $this->uuid = Uuid::uuid4()->toString();
  }

  /**
   * @return string
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * @param string $path
   */
  public function setPath(string $path)
  {
    $this->path = $path;
  }

  /**
   * @return array
   */
  public function getFunctions(): array
  {
    return $this->functions;
  }

  /**
   * @param array $functions
   */
  public function setFunctions(array $functions)
  {
    $this->functions = $functions;
  }

  /**
   * @return array
   */
  public function getClasses(): array
  {
    return $this->classes;
  }

  /**
   * @param array $classes
   */
  public function setClasses(array $classes)
  {
    $this->classes = $classes;
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
  public function setInsecureCalls(array $insecureCalls)
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
  public function setPossibleCalls(array $possibleCalls)
  {
    $this->possibleCalls = $possibleCalls;
  }

  /**
   * @return array
   */
  public function getHooks(): array
  {
    return $this->hooks;
  }

  /**
   * @param array $hooks
   */
  public function setHooks(array $hooks)
  {
    $this->hooks = $hooks;
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
