<?php
class Package {
  private $_name;
  private $_requires;

  public function __construct($name, $requires) {
    $this->_name = $name;
    $this->_requires = $requires;
  }

  public function getName() {
    return $this->_name;
  }

  public function getRequires() {
    return $this->_requires;
  }

}
