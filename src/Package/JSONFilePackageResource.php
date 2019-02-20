<?php
class JSONFilePackageResource {

  private $_jsonObject = null;
  private $_filePath = null;

  public function __construct($file) {

    if (!$file) {
      throw new \Exception('Package file path is not correct: "' . $file . '"');
    }

     $this->_filePath = $file;
     $this->_jsonObject = $this->_readPackageFile($file);

  }

  public function getName() {
    if (property_exists($this->_jsonObject, 'name')){
      return $this->_jsonObject->name;
    }

    return false;
  }

  public function getRequirePackageNames() {
    if (property_exists($this->_jsonObject, 'require')){
      return array_keys(get_object_vars($this->_jsonObject->require));
    }

    return array();
  }


  private function _readPackageFile($filePath) {

      $fileContent =  file_get_contents($filePath);

      if ($fileContent === false) {
        throw new \Exception('Package file read problem: ' . $filePath);
      }

       try {
           $object = json_decode($fileContent);
           return $object;
       } catch (\Exception $e) {
          throw new \Exception('Package encode problem: ' . $filePath);
       }
  }

}
