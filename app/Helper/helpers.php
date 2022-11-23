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

function get_value_between_in_array($arr, $startValue, $endValue): bool
{
    foreach ($arr as $item) {
        if($item >= $startValue && $item <= $endValue){
            return true;
        }
    }
    return false;
}

?>
