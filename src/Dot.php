<?php

class Dot {

  private $_ln = "\n";

  private $_graphDefaultConfig = [
    'rankdir' => 'LR',
    'ratio' => 'fill'
//    'size' => '25,10'
  ];

  private $_nodeDefaultConfig = [
    'shape' => 'circle',
    'style' => 'filled',
    'color' => 'lightgray',
    'fontname' => 'Helvetica-Oblique',
    'fontsize' => '50',
    //'width' => '1',
    //'height' => '0.5',
    //'margin' => '0.5'
  ];

  private $_nodes = [];
  private $_groups = [];
  private $_connections = [];


  public function addNode($key, $settings = []) {
    $this->_nodes[$key] = $settings;
  }

  private function _nodeToText($key) {
    if (!isset($this->_nodes[$key])) {
      throw new \Exception("Node is not declared " . $key);
    }

    $settings = $this->_nodes[$key];
    $settingsText = '';

    if (!empty($settings)) {
      $settingsText = '[' . $this->_arrayToText($settings) . ']';
    }

    return '"' . $key . '" ' . $settingsText . ';' . $this->_ln;
  }

  public function addGroup($key, $nodes, $settings = []) {
    $this->_groups[$key] = [
      'nodes' => $nodes,
      'settings' => $settings
    ];
  }

  private function _groupToText($key) {
    if (!isset($this->_groups[$key])) {
      throw new \Exception("Group is not declared " . $key);
    }

    $ln = $this->_ln;
    $nodes = $this->_groups[$key]['nodes'];
    $settings = $this->_groups[$key]['settings'];

    $settingsText = '';
    if (!empty($settings)) {
      $settingsText = 'graph [' . $this->_arrayToText($settings) . ']' . $ln;
    }

    $nodesText = '';
    if (!empty($nodes)) {
      $nodesText = '"' . implode('";' . $ln . '"', $nodes) . '";' . $ln;
    }

    return 'subgraph cluster_' . md5($key) . ' {' . $ln
      . $settingsText
      . $nodesText
      . '}' . $ln;
  }


  public function addConnection($nodeA, $nodeB, $settings = []) {
    $this->_connections[$nodeA . '-' . $nodeB] = [
      'node_a' => $nodeA,
      'node_b' => $nodeB,
      'settings' => $settings
    ];
  }

  private function _connectionToText($nodeA, $nodeB) {
    $key = $nodeA . '-' . $nodeB;

    if (!isset($this->_connections[$key])) {
      throw new \Exception("Connection is not declared " . $key);
    }

    $ln = $this->_ln;
    $settings = $this->_connections[$key]['settings'];

    $settingsText = '';
    if (!empty($settings)) {
      $settingsText = '[' . $this->_arrayToText($settings) . ']';
    }

    return '"' . $nodeA . '"->"' . $nodeB . '" ' . $settingsText . ';' . $ln;
  }


  private function _toText(){
    $nodesText = '';
    foreach (array_keys($this->_nodes) as $key) {
      $nodesText .= $this->_nodeToText($key);
    }

    $groupsText = '';
    foreach (array_keys($this->_groups) as $key) {
      $groupsText .= $this->_groupToText($key);
    }

    $connectionsText = '';
    foreach ($this->_connections as $connectionItem) {
      $connectionsText .= $this->_connectionToText(
        $connectionItem['node_a'],
        $connectionItem['node_b']
      );
    }


    $ln = $this->_ln;

    return "digraph Test { $ln"
      . 'graph [' . $this->_arrayToText($this->_graphDefaultConfig) . ']' . $ln
      . 'node [' . $this->_arrayToText($this->_nodeDefaultConfig) . ']' . $ln
      . $nodesText
      . $groupsText
      . $connectionsText
      . '}';
  }

  private function _arrayToText($values, $lineEndSymbol = ' ')
  {
    if (empty($values)) {
      return '';
    }

    $text = '';
    foreach ($values as $key => $value) {
      $text .= $key . '="' . $value . '";' . $lineEndSymbol;
    }
    return $text;
  }

  public function generate($outfile){
    $myfile = fopen("$outfile", "w") or die("Unable to open file!");
    fwrite($myfile, $this->_toText());
    fclose($myfile);

    $outpoot = shell_exec ("dot -Tpdf -O $outfile");
  }

}
