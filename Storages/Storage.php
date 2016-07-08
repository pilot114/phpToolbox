<?php

namespace PhpToolbox\Storages;

/*
 * add context and array-like access
 *
 * Mode -> namespace/storage:
 * env -> UnitId:UserId/1
 * pub -> UnitId/2
 * per -> UserId/3
 * ano -> generate/4
 *
 * for visitors env & per => 5
 *
 * All storages work flow:
 *
 * $stor = new Storage($storHandler);  <- inject storage
 * $stor->init($mode, $unitId, $user); <- create context
 * .. and custom use $stor!
 */

abstract class Storage
{
    const ENV = 1;
    const PUB = 2;
    const PER = 3;
    const ANO = 4;
    const TEMP = 5;

    public $mode;
    public $unitId;
    public $user;

    protected $prefix;
    protected $sh;

    function __construct($storageHandler)
    {
        $this->sh = $storageHandler;
    }

    abstract protected function setBucket($number);
    abstract protected function envPrefix();
    abstract protected function pubPrefix();
    abstract protected function perPrefix();
    abstract protected function anoPrefix();
    abstract protected function build();

    public function init($mode, $unitId, $user)
    {
        $this->mode   = $mode;
        $this->unitId = $unitId;
        $this->user = $user;

        $role = ($this->user) ? 'User' : 'Visitor';

        switch($mode . $role) {
            case 'envVisitor':
                $this->setBucket(self::TEMP);
                $this->envPrefix();
                break;
            case 'envUser':
                $this->setBucket(self::ENV);
                $this->envPrefix();
                break;
            case 'pubVisitor':
            case 'pubUser':
                $this->setBucket(self::PUB);
                $this->pubPrefix();
                break;
            case 'perVisitor':
                $this->setBucket(self::TEMP);
                $this->perPrefix();
                break;
            case 'perUser':
                $this->setBucket(self::PER);
                $this->perPrefix();
                break;
            default:
                $this->setBucket(self::ANO);
                $this->anoPrefix();
        }
        $this->build();
    }
}
