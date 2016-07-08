<?php

namespace PhpToolbox\Storages;

class Fs extends Storage
{
    protected function setBucket($number) {
        $this->prefix = '/home/wshell/';
    }
    protected function envPrefix() {
        $this->prefix .= 'users/' . $this->user->id . '/' . $this->unitId;
    }
    protected function pubPrefix() {
        $this->prefix .= 'units/' . $this->unitId;
    }
    protected function perPrefix() {
        $this->prefix .= 'users/' . $this->user->id;
    }
    protected function anoPrefix() {
        $this->prefix .= uniqid(mt_rand());
    }

    protected function build() {
        $this->prefix .= '/';
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case 'copy':
            case 'rename':
            case 'symlink':
            case 'mirror':
                $args[0] = $this->prefix . $args[0];
                $args[1] = $this->prefix . $args[1];
                break;
            case 'dumpFile':
                $args[0] = $this->prefix . $args[0];
                break;
            case 'mkdir':
            case 'exists':
            case 'remove':
            case 'touch':
                $args[0] = $this->addPrefixRecursive($args[0]);
                break;
            default:
                die('Not support method');
        }

        return call_user_func_array([$this->sh, $method], $args);
    }

    private function addPrefixRecursive($arg)
    {
        if (!$arg instanceof \Traversable) {
            $arg = new \ArrayObject(is_array($arg) ? $arg : [$arg]);
        }
        array_walk_recursive ( $arg, function(&$val) {
            $val = $this->prefix . $val;
        });
        return $arg;
    }
}
