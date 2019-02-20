<?php
class GroupConfig {
  private $_configObject;
  private $_defaultMode = 'none';

  public function __construct($object) {
    $this->_configObject = $object;
  }

  private function _getMode() {
    if (!property_exists($this->_configObject, 'mode')) {
      return $this->_defaultMode;
    }
    return $this->_configObject->mode;
  }

  public function isHideMode() {
    return $this->_getMode() == 'hide';
  }

  public function isMergeMode() {
    return $this->_getMode() == 'merge';
  }

  public function isBoundMode() {
    return $this->_getMode() == 'bound';
  }

  public function getPackages() {
    if (!property_exists($this->_configObject, 'packages')) {
      return [];
    }
    return $this->_configObject->packages;
  }

  public function getName() {
    if (!property_exists($this->_configObject, 'name')) {
      return false;
    }
    return $this->_configObject->name;
  }
}
