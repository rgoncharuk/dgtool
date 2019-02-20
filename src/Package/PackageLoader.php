<?php
class PackageLoader{

  private $_scope;
  private $_projectPath;

  public function __construct($scope, $projectPath) {
     $this->_scope = $scope;
     $this->_projectPath = $projectPath;
  }

  private function _getScope() {
    return $this->_scope;
  }

  private function _getPackageResources () {
    $outpoot = shell_exec ("find $this->_projectPath -name 'composer.json'");
    if ($outpoot == '') return [];
    $filesList = explode("\n", trim($outpoot));

    $packageResources = [];
    foreach ($filesList as $patch) {
        $packageResources[] = new JSONFilePackageResource($patch);
    }

    return $packageResources;

  }

  public function loadPackages() {

    $packages = [];

    foreach ($this->_getPackageResources() as $packageResource) {
      if (!$packageResource->getName()) {
        continue;
      }

      if (!$this->_getScope()->isInScope($packageResource->getName())) {
        continue;
      }

      $packageDependencies = [];
      foreach ($packageResource->getRequirePackageNames() as $requirePackageName) {
        if ($this->_getScope()->isInScope($requirePackageName)) {
          $packageDependencies[] = $requirePackageName;
        }
      }

      $packages[$packageResource->getName()] = new Package(
        $packageResource->getName(),
        $packageDependencies
      );
    }

    // load dependencies as packages
    $notLoadedDependencies = [];
    foreach ($packages as $package) {
      foreach ($package->getRequires() as $requiredPackageName) {
        if (!isset($packages[$requiredPackageName])) {
            $notLoadedDependencies[] = $requiredPackageName;
        }
      }
    }
    foreach (array_unique($notLoadedDependencies) as $packageName) {
      $packages[$packageName] = new Package($packageName, []);
    }

    return array_values($packages);

  }

}
