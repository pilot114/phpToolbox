<?php

namespace PhpToolbox\Utils;

class Debug
{
    private $timeStart;

    function __construct()
    {
        $this->timeStart = microtime(true);
    }

    public function result()
    {
        $total_mem = explode("\n", (string)trim( shell_exec('free') ) );
        $mem = explode(" ", $total_mem[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_percent = round($mem[2]/$mem[1]*100, 4);

        $timeEnd = microtime(true);

        $resources = [
            'total_memory_kb' => intval($mem[2]/1024),
            'total_use_memory_%' => $memory_percent,
            'php_memory_limit' => ini_get('memory_limit'),
            'php_script_alloc_kb' => intval(memory_get_usage(true)/1024),
            'php_script_use_kb' => intval(memory_get_usage()/1024),
            'time' => round($timeEnd - $this->timeStart, 4)
        ];
        return $resources;
    }
}
