<?php

use Ramsey\Uuid\Uuid;

/**
 * Class Parameter
 */
class Parameter implements JsonSerializable
{

  private string $uuid;
  private string $name;
  private int $position;

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
   * @return int
   */
  public function getPosition(): int
  {
    return $this->position;
  }

  /**
   * @param int $position
   */
  public function setPosition(int $position): void
  {
    $this->position = $position;
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }


}
