<?php

function debugPrint($data = NULL) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function replace_space($name) {
    $name = trim($name);
    $str = str_replace(' ', '-', $name);
    $str = str_replace("'", '', $str);
    $str = strtolower($str);

    return $str;
}

function clean($string) {
    $string = str_replace(' ', '-', trim($string)); // Replaces all spaces with hyphens.
    $string = str_replace('&', 'and', trim($string)); // Replaces all &nbsp with string and

    return preg_replace('/[^A-Za-z0-9\-&]/', '', strtolower($string)); // Removes special chars.
}

function cleanBarcodeName($string){
    $string = str_replace([' ', ':', '\\', '/', '*'], '-', trim($string)); // Replaces with hyphen
    return strtolower($string);
}

function covtime($youtube_time) {
    preg_match_all('/(\d+)/', $youtube_time, $parts);

    // Put in zeros if we have less than 3 numbers.
    if (count($parts[0]) == 1) {
        array_unshift($parts[0], "0", "0");
    } elseif (count($parts[0]) == 2) {
        array_unshift($parts[0], "0");
    }

    $sec_init = $parts[0][2];
    $seconds = $sec_init % 60;
    $seconds_overflow = floor($sec_init / 60);

    $min_init = $parts[0][1] + $seconds_overflow;
    $minutes = ($min_init) % 60;
    $minutes_overflow = floor(($min_init) / 60);

    $hours = $parts[0][0] + $minutes_overflow;

    if ($hours != 0)
        return $hours . ':' . $minutes . ':' . $seconds;
    else
        return $minutes . ':' . $seconds;
}

function cleanPrice($a) {
    $b = str_replace(',', '', $a);
    if (is_numeric($b)) {
        $a = $b;
    }
    return $a;
}
function isValidDate($datetimeString=null)
{
    if($datetimeString==null)
    {
        return 0;
    }
    else{
        $timestring = strtotime($datetimeString);
        $month = date('m',$timestring);
        $day = date('d',$timestring);
        $year = date('Y',$timestring);
        
        return checkdate($month, $day, $year);
    }
}

function stringSanitize($s) {
    $result = preg_replace("/[^a-zA-Z0-9]+-/", "", html_entity_decode($s, ENT_QUOTES));
    return $result;
}
function calculate_discount($price, $finalprice) {
    $price = (float)$price;
    $finalprice = (float)$finalprice;

    $discount = $price - $finalprice;
    $value = ($discount / $price) * 100;
    return round($value);
}
function replaceQuote($string)
{
    $str = str_replace('"', '', $string);
    $str = str_replace("'", '', $string);
    return $str;
}
function array_has_duplicates($array) {
    return count($array) !== count(array_unique($array));
}

function convertToEAN13($digits){
    $digits = substr($digits, 0, 12);
    $arr = str_split($digits);

    //( 10 - [ (3 * Odd + Even) modulo 10 ] ) modulo 10
    $odd = $arr[11] + $arr[9] + $arr[7] + $arr[5] + $arr[3] + $arr[1];
    $even = $arr[10] + $arr[8] + $arr[6] + $arr[4] + $arr[2] + $arr[0];
    $checksum = (3 * $odd + $even);
    $checksum = $checksum % 10;
    $checksum = 10 - $checksum;
    $checksum = $checksum % 10;
    $digits = $digits.$checksum;

    return $digits;
}
