<?php

class HighlightConfig {

  const MARKER_DEPENDENCIES_ALL = '->*';
  const MARKER_DEPENDENCIES_DIRECT = '->D';
  const MARKER_DEPENDENCIES_INDIRECT = '->I';
  const MARKER_DEPENDENTS_ALL = '*->';
  const MARKER_DEPENDENTS_DIRECT = 'D->';
  const MARKER_DEPENDENTS_INDIRECT = 'I->';

  private $_highlights = [];

  public function __construct($object) {
    $this->_configObject = $object;

    foreach ($this->_configObject as $rule) {
      $this->_highlights[$this->_getHighlightName($rule)] = $rule;
    }
  }

  public function getHighlights() {
    return array_keys($this->_highlights);
  }

  public function getHighlightsRequireDirectDependencies() {
    return $this->_collectHighlightsByMarker(self::MARKER_DEPENDENCIES_DIRECT);
  }

  public function getHighlightsRequireIndirectDependencies() {
    return $this->_collectHighlightsByMarker(self::MARKER_DEPENDENCIES_INDIRECT);
  }

  public function getHighlightsRequireAllDependencies() {
    return $this->_collectHighlightsByMarker(self::MARKER_DEPENDENCIES_ALL);
  }

  public function getHighlightsRequireDirectDependents() {
    return $this->_collectHighlightsByMarker(self::MARKER_DEPENDENTS_DIRECT);
  }

  public function getHighlightsRequireIndirectDependents() {
    return $this->_collectHighlightsByMarker(self::MARKER_DEPENDENTS_INDIRECT);
  }

  public function getHighlightsRequireAllDependents() {
    return $this->_collectHighlightsByMarker(self::MARKER_DEPENDENTS_ALL);
  }

  private function _collectHighlightsByMarker ($marker) {

    $results = [];

    foreach ($this->_highlights as $name => $rule) {
      $requiredMarkerPosition = 0;
      if ($marker == self::MARKER_DEPENDENCIES_ALL
        || $marker == self::MARKER_DEPENDENCIES_DIRECT
        || $marker == self::MARKER_DEPENDENCIES_INDIRECT) {
          $requiredMarkerPosition = strlen($rule) - strlen($marker);
      }

      if (strpos($rule, $marker) === $requiredMarkerPosition) {
         $results[] = $name;
      }
    }

    return $results;
  }

  private function _getHighlightName($rule) {

    $markers = [
      self::MARKER_DEPENDENCIES_ALL,
      self::MARKER_DEPENDENCIES_DIRECT,
      self::MARKER_DEPENDENCIES_INDIRECT,
      self::MARKER_DEPENDENTS_ALL,
      self::MARKER_DEPENDENTS_DIRECT,
      self::MARKER_DEPENDENTS_INDIRECT
    ];
    foreach ($markers as $marker) {
      $rule = str_replace($marker, '', $rule);
    }

    return $rule;
  }

}
