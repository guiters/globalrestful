<?php

/**
 * @param $x
 * @return bool
 */
function numbertype($x)
{
    if ($x % 2 == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * @param $string
 * @return bool
 */
function is_json($string)
{
    if (!is_array($string)) {
        json_decode($string);
        if (is_numeric($string)) {
            $res = false;
        } else {
            $res = (json_last_error() == JSON_ERROR_NONE);
        }

    } else {
        foreach ($string as $key => $value) {
            $res = is_json($value);
        }
    }
    return $res;
}

/**
 * @param $array
 * @param bool $convert
 * @param null $dad
 * @return array|bool
 */
function multidimensionalisjson($array, $convert = false, $dad = null)
{
    $res = [];
    $result = '';
    if ($convert) {
        $keys = key($array);
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $res = multidimensionalisjson($value, $convert, $key);
        } else {
            $result = is_json($value);
            if ($result === true) {
                if (!$convert) {
                    $res = $result;
                } else {
                    if ($dad) {
                        $tres = [$dad => [$key => json_decode($value, true)]];
                    } else {
                        $tres = [$key => json_decode($value, true)];
                    }
                    if ($tres) {
                        $res = array_merge($tres, $res);
                    }
                }
            } else {
                if ($dad) {
                    $tres = [$dad => [$key => utf8_encode($value)]];
                } else {
                    $tres = [$key => utf8_encode($value)];
                }
                if ($tres) {
                    if (is_array($res)) {
                        $res = array_merge($tres, $res);
                    } else {
                        $res = $tres;
                    }
                }
            }
        }
    }
    if ($res) {
        return $res;
    } else {
        return false;
    }
}


/**
 * @param array $arr
 * @return bool
 */
function isAssoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Dimonio feito para reconhecer palavras parecidas
 * dentro de um array multidimencional ou nao
 * @param $needle
 * @param $haystack
 * @param bool $strict
 * @return bool
 */
function in_array_r_like($needle, $haystack, $strict = false)
{
    $res = false;
    foreach ($haystack as $key => $value) {
        if ($value && !is_array($value)) {
            $atual = strpos($needle, $value);
            $atual2 = strpos($value, $needle);
            if ($atual !== false) {
                $res = $value;
            } else if ($atual2 !== false) {
                $res = $needle;
            }
        } else if (is_array($value)) {
            $res = in_array_r_like($needle, $value, $strict);
        }
    }
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

/**
 * @param $var1
 * @param $var2
 * @param $res1
 * @param null $res2
 * @return bool|null
 */
function checkvar($var1, $var2, $res1, $res2 = null)
{
    if (is_array($var1)) {
        foreach ($var1 as $var) {
            $res = checkvar($var, $var2, $res1, $res2);
            if ($res == $res2 || $res == false) {
                return $res;
                break;
            }
        }
    }
    if ($var1 == $var2) {
        return $res1;
    } else {
        if ($res2) {
            return $res2;
        } else {
            return false;
        }
    }
}

/**
 * @param $array1
 * @param $array2
 * @param $subject
 * @return mixed
 */
function checkarray($array1, $array2, $subject)
{
    if ($subject) {
        $i = 0;
        foreach ($array1 as $value) {
            if ($value == $subject) {
                return $array2[$i];
            }
            $i++;
        }
    }
}

/**
 * @param $ru
 * @param $rus
 * @param $index
 * @return float|int
 */
function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000)) - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

/**
 * @param $size
 * @return string
 */
function convert($size)
{
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function isRegex($str0)
{
    $regex = "/^\/[\s\S]+\/$/";
    return preg_match($regex, $str0);
}

/**
 * @param $array
 */
function debug($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

/**
 * @param $valor
 * @return bool
 */
function check_par($valor)
{
    if ($valor % 2 == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param $array
 * @return mixed
 */
function array_to_index($array)
{
    $i = 0;
    foreach ($array as $key => $value) {
        $result[$i] = $key;
        $result[$i + 1] = $value;
        $i++;
    }
    return $result;
}

/**
 * @param $needle
 * @param $haystack
 * @param bool $strict
 * @return bool
 */
function in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }
    return false;
}


/**
 * @param $needle
 * @param $haystack
 * @param bool $strict
 * @return bool|int|string
 */
function in_array_r_like_key($needle, $haystack, $strict = false)
{
    $res = false;
    if(is_array($haystack)) {
        foreach ($haystack as $item => $key) {
            $atual = strpos($item, $needle);
            if ($atual !== false) {
                $res = $item;
            }
            if (is_array($key)) {
                $res = in_array_r_like($needle, $key, $strict);
            }
        }
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }else{
        return false;
    }
}

/**
 * @param $arr
 * @param $keys
 * @param $value
 * @return mixed
 */
function insert_using_keys($arr, $keys, $value)
{
    // we're modifying a copy of $arr, but here
    // we obtain a reference to it. we move the
    // reference in order to set the values.
    $a = &$arr;

    while (count($keys) > 0) {
        // get next first key
        $k = array_shift($keys);

        // if $a isn't an array already, make it one
        if (!is_array($a)) {
            $a = array();
        }

        // move the reference deeper
        $a = &$a[$k];
    }
    $a = $value;

    // return a copy of $arr with the value set
    return $arr;
}

/**
 * @param $needle
 * @param $haystack
 * @param null $dad
 * @return array|mixed
 */
function get_array_like_key($needle, $haystack, $dad = null)
{
    $res = [];
    foreach ($haystack as $key => $value) {

        $atual = strpos($key, $needle);
        if ($atual !== false) {
            if ($dad) {
                if (is_array($dad)) {
                    $res = insert_using_keys($res, $dad, $value);
                } else {
                    $res[$dad][$key] = $value;
                }
            } else {
                $res[$key] = $value;
            }
        }
        if (is_array($value)) {
            if (isset($dad)) {
                $adad[] = $dad;
                $adad[] = $key;
                $key = $adad;
            }
            $res = get_array_like_key($needle, $value, $key);
        }
    }
    return $res;
}

/**
 * @param $array1
 * @param $array2
 * @param string $type
 * @return bool|int|string
 */
function in_arrays($array1, $array2, $type = 'value')
{
    foreach ($array2 as $key => $val) {
        if ($type == 'value') {
            if (in_array($val, $array1)) {
                $found = $key;
            }
        } else {
            if (array_key_exists($val, $array1)) {
                $found = $val;
            }
        }
    }
    if (isset($found)) {
        return $found;
    } else {
        return false;
    }
}


/**
 * @param $array
 * @param $schema
 * @return array
 */
function processschema($array, $schema)
{
    $var = [];
    $act = [];
    foreach ($array as $item) {
        foreach ($schema as $key => $value) {
            if (!is_array($value)) {
                if (is_numeric($key)) {
                    $act[$value] = $item[$value];
                }
            }
        }
        $var[] = $act;
    }
    return $var;
}

/**
 * @param $array
 * @param $schema
 * @return array
 */
function returnschema($array, $schema)
{
    $result = [];
    foreach ($schema as $item) {
        $result[$item] = $array[$item];
    }
    return $result;
}


/**
 * @param array $array
 * @return array
 */
function getKeysMultidimensional(array $array)
{
    $keys = array();
    foreach ($array as $key => $value) {
        $keys[] = $key;
        if (is_array($value)) {
            $keys = array_merge($keys, getKeysMultidimensional($value));
        }
    }

    return $keys;
}


/**
 * @param $array
 * @param $nkey
 * @param $nvalue
 * @return array
 */
function extractdata($array, $nkey, $nvalue)
{
    $result = [];
    if (isset($array[$nkey])) {
        $result[$array[$nkey]] = $array[$nvalue];
    }
    return $result;
}

/**
 * @param $array
 * @param $nkey
 * @param $nvalue
 * @return array
 */
function extractdata_keys($array, $nkey, $nvalue)
{
    $result = [];
    foreach ($array as $key => $value) {
        $result = array_merge(extractdata($value, $nkey, $nvalue), $result);
    }
    return $result;
}

/**
 * @param $page
 * @param null $flag
 * @return string
 */
function getsessionname($page, $flag = null)
{
    if (!$flag) {
        $flagtag = date('dmY-Hi');
    } else {
        if ($flag == 'schemalabels') {
            $flagtag = date('dmY-H');
        }
    }
    $name = md5($_SERVER['REMOTE_ADDR'] . $page['complete'] . $flagtag . json_encode($_POST) . json_encode($_GET)) . '.session';
    return $name;
}

/**
 * @param $path
 * @param $page
 * @param null $flag
 * @return bool|string
 */
function getsession($path, $page, $flag = null)
{
    $name = getsessionname($page, $flag);
    if (is_file($path . $name)) {
        return file_get_contents($path . $name);
    } else {
        return false;
    }
}

/**
 * @param $path
 * @param $page
 * @param $session
 * @param null $flag
 * @return bool|resource
 */
function writesession($path, $page, $session, $flag = null)
{
    $name = getsessionname($page, $flag);
    $myfile = fopen($path . $name, "w") or die("Unable to open file!");
    fwrite($myfile, $session);
    fclose($myfile);
    return $myfile;
}

/**
 * @param $date
 * @param $format
 * @return mixed
 */
function formatdate($date, $format)
{
    $defaut['y'] = 4;
    $defaut['m'] = 2;
    $defaut['d'] = 2;
    $date = str_replace('/', '-', $date);
    /*Separo tudo com o espaço*/
    $dnh = explode(' ', $date);
    /*passo tudo por um foreach para separar data de hora*/
    foreach ($dnh as $value) {
        if (strpos($value, '-') !== false) {
            $d = $value;
        } else {
            $h = $value;
        }
    }

    /*separo a data*/
    $d = explode('-', $d);
    foreach ($d as $key => $value) {
        if ($key == 1) {
            $this['m'] = $value;
        }
        if ($key == 0) {
            if (strlen($value) == 4) {
                $this['y'] = $value;
            } else {
                $this['d'] = $value;
            }
        }
        if ($key == 2) {
            if (strlen($value) == 4) {
                $this['y'] = $value;
            } else {
                $this['d'] = $value;
            }
        }
    }
    if (isset($h)) {
        $h = explode(':', $h);
    }
    $result = $format;
    if (isset($this['d']) && isset($this['m']) && isset($this['y'])) {
        $result = str_replace('d', $this['d'], $result);
        $result = str_replace('m', $this['m'], $result);
        $result = str_replace('y', $this['y'], $result);
    }
    if (isset($h[0]) && isset($h[1]) && isset($h[2])) {
        $result = str_replace('h', $h[0], $result);
        $result = str_replace('i', $h[1], $result);
        $result = str_replace('s', $h[2], $result);
    }
    $result = explode(' ', $result);
    if (isset($result[1])) {
        $res['hour'] = $result[1];
    }
    $res['date'] = $result[0];

    return $res;
}


/**
 * @param $date
 * @param $pattern
 * @param $qtn
 * @return false|string
 */
function calcDates($date, $pattern, $qtn)
{
    $date = strtotime($date);
    $date = strtotime($qtn, $date);
    return date($pattern, $date);
}

/**
 * @param $array
 * @param array $labels
 * @return bool|null
 */
function mountDate($array, $labels = [])
{
    $help = HELPERS;
    $res = null;
    foreach ($array as $key => $value) {
        /*Caso seja uma data fixa*/
        if (strpos($value, 'date|') !== false) {
            $explode = explode('|', $value);
            $hoje = date('Y-m-d');
            if ($explode[2]{0} == '-') {
                $datep1 = calcDates($hoje, 'Y-m-d', $explode[2] . ' ' . $explode[1]);
                $datep2 = calcDates($hoje, 'Y-m-d', '+1Day');
            } else {
                $datep1 = $hoje;
                $datep2 = calcDates($hoje, 'Y-m-d', $explode[2] . ' ' . $explode[1]);
                $datep2 = calcDates($datep2, 'Y-m-d', '+1Day');
            }
            $res[$key] = " >= '$datep1' AND $key <= '$datep2'";
        }
        /*Caso exista uma data na url*/
        if (strpos($key, 'data_') !== false || strpos($key, 'date_') !== false) {
            $value = formatdate($value, 'y-m-d')['date'];
            $action = str_replace('date_', '', str_replace('data_', '', $key));
            if (in_array($action, $help['start'])) {
                if ($value) {
                    $date1 = $value;
                    $key1 = $key;
                }
            }
            if (in_array($action, $help['end'])) {
                if ($value) {
                    $date2 = $value;
                    $key2 = $key;
                }
            }
        }
    }
    if (isset($date1) && isset($date2)) {

        $date2 = calcDates($date2, 'Y-m-d', '+1Day');
        if (isset($labels[$key1]) && isset($labels[$key2])) {
            $res[$labels[$key1]] = " >= '$date1' AND $labels[$key2] <= '$date2'";
        }
    } else if (isset($date1)) {
        $res[$key1] = "LIKE '%$date1%'";
    } else if (isset($date2)) {
        $res[$key2] = "LIKE '%$date2%'";
    }

    if (isset($res)) {
        return $res;
    } else {
        return false;
    }
}

/**
 * @param $strDateFrom
 * @param $strDateTo
 * @return int
 */
function getDaysFromRange($strDateFrom, $strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange = array();
    //var_dump(str_split($strDateFrom));
    $y = substr($strDateFrom, 6, 4);
    $m = substr($strDateFrom, 3, 2);
    $d = substr($strDateFrom, 0, 2);
    $iDateFrom = mktime(1, 0, 0, $m, $d, $y);

    //var_dump($strDateTo);
    $y = substr($strDateTo, 6, 4);
    $m = substr($strDateTo, 3, 2);
    $d = substr($strDateTo, 0, 2);
    $iDateTo = mktime(1, 0, 0, $m, $d, $y);

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return count($aryRange);
}

/**
 * @param $string
 * @return null|string|string[]
 */
function utf8fix($string)
{
    $str = preg_replace("/\\\\u([0-9a-fA-F]{4})/", "&#x\\1;", $string);
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return $str;
}


/**
 * @param $keys
 * @param $array
 * @return bool
 */
function exporttojsonarray($keys, $array)
{
    if (isset($keys)) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $vkey => $vvalue) {
                    if (in_array($vkey, $keys)) {
                        $array[$key][$vkey] = utf8fix(json_encode($vvalue));
                    }
                }
            } else {
                if (in_array($key, $keys)) {
                    $array[$key] = json_encode($value);
                }
            }
        }
        if ($array) {
            return $array;
        } else {
            return false;
        }
    }
}

/**
 * @param $array
 * @param $key
 * @return array
 */
function unique_multidim_array($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (isset($val[$key])) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
        }
        $i++;
    }
    return $temp_array;
}

/**
 * @param $array
 * @return mixed
 */
function in_array_number($array)
{
    foreach ($array as $key => $item) {
        if (!is_array($item)) {
            if (is_numeric($item)) {
                return $item;
            }
        } else {
            return in_array_number($item);
        }
    }
}

/**
 * @param $val
 * @return int|string
 */
function get_numeric($val)
{
    if (is_numeric($val)) {
        return $val + 0;
    }
    return 0;
}

/**
 * @param $array
 * @return mixed
 */
function convert_value_to_number($array)
{
    foreach ($array as $key => $value) {
        if (is_numeric($value)) {
            $value = floatval($value);
        } elseif ($value == '') {
            $value = null;
        }
        $result[$key] = $value;
    }

    return $result;
}

/**
 * @param $text
 * @return bool|resource
 */
function toconsole($text)
{
    if (is_array($text)) {
        $text = json_encode($text, JSON_PRETTY_PRINT);
    }
    $myfile = fopen(CONSOLE . '/console.log', "a+") or die("Unable to open file!");
    fwrite($myfile, $text.PHP_EOL);
    fclose($myfile);
    return $myfile;
}