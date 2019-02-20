<?php

class Config {

  private $_configObject;

  public function __construct($file) {
    $this->_configObject = $this->_loadConfig($file);
  }

  public function getGroups() {
    $result = [];
    foreach ($this->_configObject->groups as $group) {
      $result[] = new GroupConfig($group);
    }
    return $result;
  }

  public function getScope() {
    return new ScopeConfig($this->_configObject->scope);
  }

  public function getHighlight() {
    return new HighlightConfig($this->_configObject->highlight);
  }

  private function _loadConfig($filePath) {
      if (!file_exists ($filePath)) {
        throw new \Exception('Config file can`t be read: ' . $filePath);
      }

      $fileContent =  file_get_contents($filePath);

      if ($fileContent === false) {
        throw new \Exception('Config file read problem: ' . $filePath);
      }

       try {
           $object = json_decode($fileContent);
           return $object;
       } catch (\Exception $e) {
          throw new \Exception('Config encode problem: ' . $filePath);
       }
  }

}
