<?php

use Ramsey\Uuid\Uuid;

/**
 * Class WpHook
 */
class WpHook implements JsonSerializable {

  private string $uuid;
  private string $name;
  private string $type;
  private string $triggeredFunction;

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
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType(string $type): void
  {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getTriggeredFunction(): string
  {
    return $this->triggeredFunction;
  }

  /**
   * @param string $triggeredFunction
   */
  public function setTriggeredFunction(string $triggeredFunction): void
  {
    $this->triggeredFunction = $triggeredFunction;
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }
}
