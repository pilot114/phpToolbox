<?php

namespace PhpToolbox;

use PhpToolbox\Utils\InputChecker;
use PhpToolbox\Utils\Formatter as F;

class Unit
{
  private $storage;
  private $hookup;
  private $user;
  private $unitId;

  function __construct($storage, $hookup, $unitId = '', $user = '', $defaultMode = 'env')
  {
    $this->storage = $storage;
    $this->hookup = $hookup;
    $this->unitId = $unitId;
    $this->user = $user;

    $this->storage->init($defaultMode, $this->unitId, $user);
  }

  private function generateId()
  {
    return md5(__CLASS__);
  }

  protected final function check($data, $returnObj = false)
  {
      $checker = new InputChecker($data, $this->hookup);
      if ($returnObj) {
          return json_decode(json_encode($checker->getData()), false);
      } else {
          return $checker->getData();
      }
  }

  public function save($data, $mode = null)
  {
    if ($mode) {
      $this->setMode($mode);
    }
    foreach ($data as $key => $val) {
      $this->storage->save($key, serialize($val));
    }
  }
  public function load($keys, $mode = null)
  {
    if ($mode) {
      $this->setMode($mode);
    }
    $result = [];
    foreach ($keys as $key) {
      $result[$key] = unserialize($this->storage->load($key));
    }
    return $result;
  }

  //----------------------------client methods-------------------------------//

  protected function uiOutput($data)
  {
  }

  protected function output($data)
  {
  }

  protected function uiEmbed($data)
  {
  }
}
