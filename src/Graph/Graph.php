<?php
class Graph {

  private $_elements = [];

  public function add($key,$element) {

    //@todo unclear exception and possile replace to interface
     if (!is_a($element, 'GraphElement')) {
       throw new \Exception("Element should implement GraphElement ", 1);
     }

    $element->addGraph($this);
    $element->addKey($key);
    $this->_elements[$key] = $element;
  }

  public function get($key) {
    if (isset($this->_elements[$key])) {
      return $this->_elements[$key];
    }

    return false;
  }

  public function isExist($key) {
    return isset($this->_elements[$key]);
  }

  public function delete($key) {
    unset ($this->_elements[$key]);
  }

  //@todo replace with iterator interface
  public function getAll() {
    return $this->_elements;
  }
}
