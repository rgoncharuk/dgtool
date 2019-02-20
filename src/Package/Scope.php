<?php
class Scope {

  private $_blackList = [];
  private $_whiteList = [];


  public function isInScope($element){

    // @todo replace hardcoded magento rule. Add regexp rules + transfer via config and maybe config interface
    if (substr($element, 0, 14) !== 'magento/module') {
      return false;
    }

    if (count($this->_getWhiteList()) > 0) {
      return in_array($element, $this->_getWhiteList()) && !in_array($element, $this->_getBlackList());
    } else {
      return !in_array($element, $this->_getBlackList());
    }

    return true;
  }

  public function setBlackList($list) {
    $this->_blackList = $list;
  }

  public function setWhiteList($list) {
    $this->_whiteList = $list;
  }

  private function _getBlackList() {
    return $this->_blackList;
  }

  private function _getWhiteList() {
    return $this->_whiteList;
  }

}
