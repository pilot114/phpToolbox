<?php

namespace PhpToolbox\Storages;

class Redis extends Storage
{
    protected function setBucket($number)
    {
        $this->sh->select($number);
    }

    protected function envPrefix()
    {
        $this->prefix = $this->unitId . ':' . $this->user->id;
    }

    protected function pubPrefix()
    {
        $this->prefix = $this->unitId;
    }

    protected function perPrefix()
    {
        $this->prefix = $this->user->id;
    }

    protected function anoPrefix()
    {
        $this->prefix = uniqid(mt_rand());
    }

    function build()
    {
        $this->sh->setOption(\Redis::OPT_PREFIX, $this->prefix . ':');
    }

    /*
     *      Sandbox
     */
    public function __call($method, $args)
    {
        $deniedMethods = [
            'connect', 'pconnect', 'close', 'ping', 'echo', 'sendEcho',
            'save', 'bgSave', 'lastSave', 'flushDB', 'flushAll',
            'dbSize', 'auth', 'info', 'resetStat', 'select',
            'move', 'bgrewriteaof', 'slaveof', 'pipeline',
            'multi', 'exec', 'discard', 'watch', 'unwatch',
            'publish', 'subscribe', 'psubscribe', 'pubsub',
            'unsubscribe', 'punsubscribe',
            'eval', 'evalsha', 'script', 'evaluate', 'evaluateSha',
            'migrate', 'getLastError', 'clearLastError',
            '_prefix', '_serialize', '_unserialize',
            'client', 'getOption', 'setOption',
            'config', 'slowlog', 'getHost', 'getPort', 'getDBNum',
            'getTimeout', 'getReadTimeout', 'getPersistentID',
            'getAuth', 'isConnected', 'wait',
            'open', 'popen', 'keys',
        ];
        if (in_array($method, $deniedMethods)) {
            die('Not support method');
        }
        return call_user_func_array([$this->sh, $method], $args);
    }
}
