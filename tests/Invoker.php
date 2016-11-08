<?php

namespace Lavary\Menu\Tests;

trait Invoker
{
    protected function invoke(&$object, $method, array $args = [])
    {
        $ref    = new \ReflectionClass(get_class($object));
        $method = $ref->getMethod($methodName);
        
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
