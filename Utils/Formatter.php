<?php

namespace PhpToolbox\Utils;

class Formatter
{
    static function printer($message, $data, $escape = false)
    {
        if($escape){
            if(is_array($data)) {
                $data = self::escapeRecursive($data);
            } else {
                $data = htmlentities($data);
            }
        }

        echo '<b>' . $message . '</b><pre>';
        print_r($data);
        echo '</pre>';
    }

    static function escapeRecursive($data)
    {
        $escaped = [];
        foreach($data as $key => $value) {
            $escKey = htmlentities($key);
            $escVal = $value;
            if(is_array($escVal)){
                $escaped[$escKey] = self::escapeRecursive($escVal);
            } else {
                $escaped[$escKey] = htmlentities($escVal);
            }
        }
        return $escaped;
    }

    static function set($setName, $elems)
    {
        echo "<h2>$setName</h2>";
        foreach($elems as $elem) {
            echo '<span class="label label-success">' . $elem . '</span> ';
        }
    }

    /*
     * Default Primary Success Info Warning Danger
     */
    static function alert($type, $message)
    {
        echo "<div class='alert alert-$type'>$message</div>";
    }


    static function structToTab($array, $pre = '')
    {
        if (is_scalar($array))
            return $array;
        foreach($array as $key => $val) {
            if (is_array($val)) {
                $output[] = self::toFormNames($val, $pre . $key . '_');
            } else
                $output[$pre . $key] = $val;
        }
        array_walk_recursive($output, function ($value, $key) use (&$result) {
            $result .= "$value.<br>";
        });
        return $result;
    }

    // convert array to list unical names:value
    static function toFormNames($array, $pre = '')
    {
        if (is_scalar($array))
            return $array;
        foreach($array as $key => $val) {
            if (is_array($val)) {
                $output[] = self::toFormNames($val, $pre . $key . '_');
            } else
                $output[$pre . $key] = $val;
        }
        array_walk_recursive($output, function ($value, $key) use (&$result) {
            $result[$key] = $value;
        });
        return $result;
    }


    /*
      list elements as
      key : value
      value in custom encoding
     */

    static function toList($data, $format = FALSE)
    {
        foreach($data as $key => $elem) {
            if (is_array($elem))
                self::toList($elem, $format);
            else {
                if ($format)
                    $elem = self::format($elem, $format);
                echo "$key : <code>$elem</code><br>";
            }
        }
    }

    static function format($elem, $format)
    {
        switch($format) {
            case 'base64':
                return base64_encode($elem);
            default:
                echo 'unknown format type';
        }
    }

    static function toYaml($array, $space = '')
    {
        foreach($array as $key => $val) {
            if (is_array($val)) {
                echo "$space$key:\n";
                self::toYaml($val, $space . '  ');
            } else
                echo "$space$key:$val\n";
        }
    }

}
