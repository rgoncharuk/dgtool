<?php
class DependenciesGraphElement extends GraphElement{
  private $_dependencies = [];

  public function setDirectDependencies($list) {
    $this->_dependencies = $list;
  }

  public function getDirectDependencies() {
    return $this->_dependencies;
  }

  public function getIndirectDependencies() {
    return array_diff($this->getAllDependencies(), $this->getDirectDependencies());
  }

  public function getDirectDependents() {

    $directDependents = [];

    foreach ($this->getGraph()->getAll() as $package) {
      foreach ($package->getDirectDependencies() as $dependencyName) {
        if ($dependencyName == $this->getName()) {
          $directDependents[] = $package->getName();
        }
      }
    }
    return $directDependents;
  }

  public function getIndirectDependents() {
    return array_diff($this->getAllDependents(), $this->getDirectDependents());
  }

  public function getAllDependencies(&$result = []){

    foreach ($this->getDirectDependencies() as $dependencyName) {
      if (in_array($dependencyName, $result)) {
        continue;
      }
      $result[] = $dependencyName;


      $this->getGraph()->get($dependencyName)->getAllDependencies($result);
    }

    return $result;
  }



  public function getAllDependents(&$result = []){

    foreach ($this->getDirectDependents() as $dependentName) {
      if (in_array($dependentName, $result)) {
        continue;
      }
      $result[] = $dependentName;


      $this->getGraph()->get($dependentName)->getAllDependents($result);
    }

    return $result;
  }

  public function isDependencyCircular($dependencyName) {
    return in_array (
      $this->getName(),
      $this->getGraph()->get($dependencyName)->getAllDependencies()
    );
  }


}
