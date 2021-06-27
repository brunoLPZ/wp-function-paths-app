<?php

class GlobalObjectInstances
{

  public static array $OBJECTS_BY_VAR = [];

  public static function pushObject(string $variable, string $object)
  {
    self::$OBJECTS_BY_VAR[$variable] = $object;
  }

  public static function getObjectByVar(string $variable)
  {
    if (array_key_exists($variable, self::$OBJECTS_BY_VAR)) {
      return self::$OBJECTS_BY_VAR[$variable];
    }
    return null;
  }

}
