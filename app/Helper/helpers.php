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

function convertUrlCategory($code, $type) :string
{
    $typeUrl = $type === C_TYPE ? 'ctype' : 'btype';
    return route('web.show') . '?' . $typeUrl . '=' . $code;
}
function pushParamUrl($key, $value)
{
    $dataPush = [];
    $except = [];

    if ($key === 'model_kana_prefix') {
        $except[] = 'model_name_prefix';
    } elseif ($key === 'model_name_prefix') {
        $except[] = 'model_kana_prefix';
    }

    $currentValue = request()->get($key);
    $arrCurrentValue = explode('-', $currentValue);

    if (($index = array_search($value, $arrCurrentValue)) !== false) {
        unset($arrCurrentValue[$index]);
        if(count($arrCurrentValue) === 0) $except[] = $key;
        $newValue = implode($arrCurrentValue);
    } else {
        $newValue = $currentValue ? $currentValue . '-' . $value : $value;
    }

    if (count($arrCurrentValue) === 0) {
        $except[] = $key;
    } else {
        $dataPush[$key] = $newValue;
    }

    return url()->current().'?'.http_build_query(array_merge(Arr::except(request()->all(), $except),$dataPush));
}

?>
