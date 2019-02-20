<?php

class ScopeConfig {
  private $_configObject;

  public function __construct($object) {
    $this->_configObject = $object;
  }

  public function getBlackList() {
    if (!property_exists($this->_configObject, 'black_list')) {
      return [];
    }
    return $this->_configObject->black_list;
  }

  public function getWhiteList() {
    if (!property_exists($this->_configObject, 'white_list')) {
      return [];
    }
    return $this->_configObject->white_list;
  }
}
