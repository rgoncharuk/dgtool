<?php
class PackageDependenciesGraphElement extends DependenciesGraphElement{

  private $_name;

  public function getName() {
    return $this->_name;
  }

  public function setName($name) {
    return $this->_name = $name;
  }

}
