<?php
use Illuminate\Support\Arr;

function verTime()
{
    return \Carbon\Carbon::now()->timestamp;
}

function get_all_const_in_class($class, $except = [])
{
    $reflectionClass = new \ReflectionClass($class);
    return Arr::except($reflectionClass->getConstants(), $except);
}


?>
