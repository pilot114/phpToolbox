<?php

namespace PhpToolbox\Storages;

// see http://php.net/manual/en/class.mongocollection.php

class Mongo extends Storage
{
    protected function setBucket($number) {
        $dbname = 'wshell_' . $number;
        $this->sh = $this->sh->$dbname;
    }
    protected function envPrefix() {
        $userId = $this->user ? $this->user->get('id'): session_id();
        $collection = $this->unitId . ':' . $userId;
        $this->sh = $this->sh->$collection;
    }
    protected function pubPrefix() {
        $collection = $this->unitId;
        $this->sh = $this->sh->$collection;
    }
    protected function perPrefix() {
        $collection = $this->user->id;
        $this->sh = $this->sh->$collection;
    }
    protected function anoPrefix() {
        $collection = uniqid(mt_rand());
        $this->sh = $this->sh->$collection;
    }
    protected function build() {
    }

    public function __call($method, $args)
    {
        $deniedMethods = [];
        if (in_array($method, $deniedMethods)) {
            die('Not support method');
        }
        return call_user_func_array([$this->sh, $method], $args);
    }

    public function save($key, $val)
    {
      // $result = $this->sh->insertOne();
      if ($result->getInsertedCount() == 1) {
        return true;
      }
      return false;
    }

    public function load($key)
    {
      return $this->get('mongo')->wshell->units->findOne([ 'key' => $key ]);
    }
}
