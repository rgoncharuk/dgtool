<?php
class GraphElement{

  private $_graph;
  private $_key;

  public function addGraph(&$graph) {
    $this->_graph = $graph;
  }

  public function getGraph() {
    return $this->_graph;
  }

  public function addKey($key) {
    $this->_key = $key;
  }

  public function getKey() {
    return $this->_key;
  }

}
