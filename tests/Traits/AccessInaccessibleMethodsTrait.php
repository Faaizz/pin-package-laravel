<?php

namespace Faaizz\PinGenerator\Tests\Traits;

use Exception;
use ReflectionClass;
use stdClass;

trait AccessInaccessibleMethodsTrait
{
    public function invokeInaccessibleMethod(&$obj, string $method, array $params)
    {
        try {
            $refClass = new ReflectionClass(get_class($obj));
            $refMethod = $refClass->getMethod($method);
        } catch (Exception $e) {
            return null;
        }

        $refMethod->setAccessible(true);

        return $refMethod->invokeArgs($obj, $params);
    }
}
