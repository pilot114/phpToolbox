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

  public function printResourseUsage()
  {
        $total_mem = explode("\n", (string)trim( shell_exec('free') ) );
        $mem = explode(" ", $total_mem[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_percent = $mem[2]/$mem[1]*100;

        $resources = [
            'total_memory' => $mem[2],
            'total_use_memory_%' => $memory_percent,
            'php_memory_limit' => ini_get('memory_limit'),
            'php_script_alloc' => memory_get_usage(true),
            'php_script_use' => memory_get_usage(),
        ];
        F::printer('Used resources:', $resources);
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
