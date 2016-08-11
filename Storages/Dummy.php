<?php

namespace PhpToolbox\Storages;

class Dummy extends Storage
{
    protected function setBucket($number) {
      return false;
    }
    protected function envPrefix() {
      return false;
    }
    protected function pubPrefix() {
      return false;
    }
    protected function perPrefix() {
      return false;
    }
    protected function anoPrefix() {
      return false;
    }
    protected function build() {
      return false;
    }

    public function save($key, $val)
    {
      return false;
    }
    public function load($key)
    {
      return false;
    }
}
