<?php
class DependenciesGraph extends Graph {

  public function add($key,$element) {

    //@todo unclear exception and possile replace to interface
     if (!is_a($element, 'DependenciesGraphElement')) {
       throw new \Exception("Element should implement DependenciesGraphElement ", 1);
     }

    parent::add($key, $element);

  }

}
