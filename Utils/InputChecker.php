<?php

/*
 * Auto check data sending from user to unit.
 *
 * check "flow":
 *  1. convert data to hookup-like (hData). validate HTML names
 *  2. cut data non used in mode
 *  3. checking each param
 *
 *  @TODO tests
 */

namespace PhpToolbox\Utils;

use PhpToolbox\Exception\CheckingError;
use PhpToolbox\Exception\MissError;
use PhpToolbox\Exception\DimensionError;
use PhpToolbox\Exception\ModeError;

class InputChecker
{
    private $clearData;

    function __construct($data, $hookup)
    {
        foreach($this->recursiveKeys($data) as $name => $value) {
            if (preg_match('#[^a-zA-Z0-9:]+#', $name)) {
                throw new \Exception('Некорректное имя параметра');
            } else {
                $hData = $data;
            }
        }

        if (isset($hookup['mode'])) {

            if (!isset($hData['mode'])) {
                throw new ModeError('Нужно указать режим');
            }
            $sm = $hData['mode'];
            if (!isset($hookup['mode'][$sm])) {
                throw new ModeError('Некорректный режим');
            }

            // selected mode
            $this->clearData['mode'] = $sm;
            $paramsMode              = $hookup['mode'][$sm];
            foreach($hookup['input'] as $key => $value) {
                if (!in_array($key, $paramsMode)) {
                    unset($hookup['input'][$key]);
                }
            }
        }

        // check input data
        foreach($hookup['input'] as $paramName => $param) {

            if (!isset($hData[$paramName])) {
                if (isset($param['norm'])) {
                    $hData[$paramName] = $param['norm'];
                } else {
                    throw new MissError("Нет значения по умолчанию для '$key'");
                }
            }

            // okay, structure
            if (isset($param['map'])) {

                // array of struct
                if (isset($param['dimension'])) {
                    foreach($hData[$paramName] as $key => $item) {
                        $hData[$paramName][$key] = $this->checkStruct($item, $param);
                    }
                } else {
                    $hData[$paramName] = $this->checkStruct($hData[$paramName], $param);
                }
                $this->clearData[$paramName] = $hData[$paramName];

                // non-structure
            } else {
                $result = $this->paramCheck($hData[$paramName], $param, $paramName);
                $this->clearData[$paramName] = $result;
            }
        }
    }

    // check structure
    private function checkStruct($struct, $param)
    {
        array_walk_recursive($struct, function (&$v, $k, $map) {
            $structNodeHookup = $this->findRecursive($map, $k);
            $result           = Null;
            $v                = $this->paramCheck($v, $structNodeHookup, $k);
        }, $param['map']);
        return $struct;
    }

    // check non-structure. dP = data, hP = meta info from hookup
    private function paramCheck($dP, $hP, $key)
    {
        // check bool param
        if(is_bool($hP)){
            if(is_null($dP)){
                $result = $hP;
            } else {
                $result = (bool)$dP;
            }
            return $result;
        }
        // check null param
        if(is_null($hP)){
            return $dP;
        }

        if (isset($hP['dimension'])) {
            $this->testDim($dP, $hP['dimension'], $key);
            $result = [];
            foreach($dP as $key => $val) {
                if (is_array($val)) {
                    $result[$key] = [];
                    $this->paramCheck($result[$key], $val, $hP, $key);
                } else {
                    $result[$key] = $this->checkNode($val, $hP, $key);
                }
            }
            return $result;
        } else {
            return $this->checkNode($dP, $hP, $key);
        }
    }

    private function testDim($dP, $dim, $key)
    {
        switch($dim) {
            case 1:
                if (!is_array($dP)) {
                    throw new DimensionError("Ошибка размерности в параметре '$key'");
                }
                break;
            case 2:
                $first = current($dP);
                if (!is_array($first)) {
                    throw new DimensionError("Ошибка размерности в параметре '$key'");
                }
                break;
            case 3:
                $first  = current($dP);
                $second = current($first);
                if (!is_array($second)) {
                    throw new DimensionError("Ошибка размерности в параметре '$key'");
                }
                break;
            default:
                throw new DimensionError("Ошибка размерности в параметре '$key'");
        }
    }

    private function checkNode($value, $hP, $key)
    {
        if (preg_match($hP['elem'], $value)) {
            return $value;
        } else {
            throw new CheckingError("Недопустимое значение для '$key'");
        }
    }

    private function findRecursive(&$array, $needle)
    {
        foreach($array as $key => &$value) {
            if ($key == $needle) {
                return $array[$key];
            }
            if (is_array($value)) {
                $this->findRecursive($value, $needle);
            }
        }
    }

    private function recursiveKeys($input)
    {
        $output = array_keys($input);
        foreach($input as $sub){
            if(is_array($sub)){
                $output = array_merge($output, $this->recursiveKeys($sub));
            }
        }
        return $output;
    }

    // get checking result
    public function getData()
    {
        return $this->clearData;
    }
}
