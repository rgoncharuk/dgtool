<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once('src/Config/Config.php');
include_once('src/Config/ScopeConfig.php');
include_once('src/Config/GroupConfig.php');
include_once('src/Config/HighlightConfig.php');

include_once('src/Package/Scope.php');
include_once('src/Package/JSONFilePackageResource.php');
include_once('src/Package/PackageLoader.php');
include_once('src/Package/Package.php');

include_once('src/Graph/Graph.php');
include_once('src/Graph/GraphElement.php');
include_once('src/Graph/DependenciesGraph.php');
include_once('src/Graph/DependenciesGraphElement.php');
include_once('src/Graph/PackageDependenciesGraphElement.php');

include_once('src/Dot.php');



$PROJECT_PATH = false;
$CONFIG_PATH = false;
$OUTFILE_PATH = false;

$longopts  = array(
    'config:',
    'projectdir:',
    'outfile:'
);

$options = getopt('', $longopts);
if (isset($options['projectdir'])) {
  $PROJECT_PATH = $options['projectdir'];
} else {
  echo '--projectdir is required' . "\n";
  die();
}
if (isset($options['config'])) {
  $CONFIG_PATH = $options['config'];
} else {
  echo '--config is required' . "\n";
  die();
}
if (isset($options['outfile'])) {
  $OUTFILE_PATH = $options['outfile'];
} else {
  echo '--outfile is required' . "\n";
  die();
}

$config = new Config($CONFIG_PATH);

/**
* LOAD PACKAGE ELEMENTS TO GRAPH
*/

$packageScope = new Scope();
$scopeConfig = $config->getScope();
$packageScope->setBlackList($scopeConfig->getBlackList());
$packageScope->setWhiteList($scopeConfig->getWhiteList());
$packageLoader = new PackageLoader($packageScope, $PROJECT_PATH);

$union = new DependenciesGraph();

foreach ($packageLoader->loadPackages() as $package) {
  $packageElement = new PackageDependenciesGraphElement();
  $packageElement->setName($package->getName());
  $packageElement->setDirectDependencies($package->getRequires());
  $union->add($package->getName(), $packageElement);
}

/**
* MERGE GRAPH ELEMENTS BASED ON CONFIG GROUPS (MERGE MODE)
*/

// REPLACE DEPENDENCIES WITH GROUP ALIASES
$aliases = [];
foreach ($config->getGroups() as $groupConfig) {
  if (!$groupConfig->isMergeMode()) {
    continue;
  }
  foreach ($groupConfig->getPackages() as $groupPackage) {
    $aliases[$groupPackage] = $groupConfig->getName();
  }
}
foreach ($union->getAll() as $package) {
  $updatedDirectDependencies = [];

  foreach ($package->getDirectDependencies() as $dependencyName) {
    if (isset($aliases[$dependencyName])) {
      $updatedDirectDependencies[] = $aliases[$dependencyName];
    } else {
      $updatedDirectDependencies[] = $dependencyName;
    }
  }

  $package->setDirectDependencies(array_unique($updatedDirectDependencies));
}

// MERGE ELEMENTS TO GROUPS
foreach ($config->getGroups() as $groupConfig) {

  if (!$groupConfig->isMergeMode()) {
    continue;
  }

  $isGroupMembersReplaced = false;
  $collectedDependencies = [];

  foreach ($groupConfig->getPackages() as $groupPackage) {
    $unionPackageElement = $union->get($groupPackage);
    if (!$unionPackageElement) {
      continue;
    }

    $collectedDependencies = array_merge(
      $collectedDependencies,
      array_diff( $unionPackageElement->getDirectDependencies(), [$groupConfig->getName()])
    );

    $union->delete($groupPackage);
    $isGroupMembersReplaced = true;
  }

  if ($isGroupMembersReplaced) {
    $newGroupPackage = new PackageDependenciesGraphElement();
    $newGroupPackage->setName($groupConfig->getName());
    $newGroupPackage->setDirectDependencies(array_unique($collectedDependencies));
    $union->add($groupConfig->getName(), $newGroupPackage);
  }
}


/**
* COLLECT HIGLIGHTS
*/

$highlightList = [];

// collect declared higlights
$highlightConfig = $config->getHighlight();
foreach ($highlightConfig->getHighlights() as $highlightPackage) {
  $highlightList[] = $highlightPackage;
}

// collect highligths related by rule
$methodNameEnds = [
  'AllDependents',
  'IndirectDependents',
  'DirectDependents',
  'DirectDependencies',
  'IndirectDependencies',
  'AllDependencies',
];

foreach ($methodNameEnds as $methodNameEnd) {
  $configMethodName = 'getHighlightsRequire' . $methodNameEnd;
  $graphElementMethodName = 'get' . $methodNameEnd;

  foreach ($highlightConfig->$configMethodName() as $highlightPackage) {
    if (!$union->isExist($highlightPackage)) {
      continue;
    }

    $highlightList = array_merge(
      $highlightList,
      $union->get($highlightPackage)->$graphElementMethodName()
    );
  }

}

$highlightList = array_unique($highlightList);


/**
* VIEW
*/


$circularDependencyColor = 'red';
$shadedNodeColor = 'white';
$shadedNodeTextColor = 'gainsboro';
$shadedConnectionColor = 'gainsboro';



$dot = new Dot();

// COLLECT HIDDEN ELEMENTS
$hiddenElements = [];
foreach ($config->getGroups() as $groupConfig) {
  if ($groupConfig->isHideMode()) {
    $hiddenElements = array_merge($hiddenElements, $groupConfig->getPackages());
  }
}

// COLLECT SHADED ELEMENTS
$shadedElements = [];
foreach ($union->getAll() as $package) {
  if (count($highlightList) > 0 && !in_array($package->getName(), $highlightList)) {
    $shadedElements[] = $package->getName();
  }
}


// ADD NODES
foreach ($union->getAll() as $package) {

  //hidden
  if (in_array($package->getName(), $hiddenElements)) {
    continue;
  }

  $settings = [];

  //label
  $settings['label'] = $package->getName();
  $settings['label'] = str_replace("magento/module-", '', $settings['label']);
  $settings['label'] = str_replace("-", "\n", $settings['label']);
  $numbersLabel = [
    count($package->getIndirectDependents()),
    count($package->getDirectDependents()),
    '#',
    count($package->getDirectDependencies()),
    count($package->getIndirectDependencies())
  ];
  $settings['label'] .= "\n" . implode('->', $numbersLabel);

  // shaded
  if (in_array($package->getName(), $shadedElements)) {
    $settings['color'] = 'grey';//$shadedNodeColor;
    $settings['fontcolor'] = $shadedNodeTextColor;
    $settings['style'] = 'dashed';
  }

  $dot->addNode($package->getName(), $settings);
}

// ADD CONNECTIONS
foreach ($union->getAll() as $package) {
  foreach ($package->getDirectDependencies() as $dependencyName) {
    //hidden
    if (in_array($package->getName(), $hiddenElements)
      || in_array($dependencyName, $hiddenElements)) {
      continue;
    }

    $settings = [];

    // circular
    if ($package->isDependencyCircular($dependencyName)) {
      $settings['color'] = $circularDependencyColor;
    }

    // shaded
    if (in_array($package->getName(), $shadedElements)
      || in_array($dependencyName, $shadedElements)) {
      $settings['color'] = $shadedConnectionColor;
      $settings['style'] = 'dashed';
    }

    $settings['arrowType'] = 'vee';

    $dot->addConnection($package->getName(), $dependencyName , $settings);
  }
}

// ADD BORDERED GROUPS
foreach ($config->getGroups() as $groupConfig) {
  if (!$groupConfig->isBoundMode()) {
    continue;
  }

  $groupPackages = [];
  foreach ($groupConfig->getPackages() as $groupPackage) {
    if ($union->isExist($groupPackage)) {
      $groupPackages[] = $groupPackage;
    }
  }
  if (empty($groupPackages)) {
    continue;
  }


  $dot->addGroup(
    $groupConfig->getName(),
    $groupPackages,
    [
      'label' => $groupConfig->getName(),
      'fontsize' => '50',
      'color' => '#2F4F4F',
      'fontname' => 'Helvetica-Oblique',
      'fontcolor' => '#2F4F4F',
      'style' => 'bold'
    ]
  );
}


$dot->generate($OUTFILE_PATH);

?>
