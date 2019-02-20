### Overview

This tool may be used for composer package dependency visualisation. 
It uses [GraphViz](https://www.graphviz.org/) for graph generation. 

You may create a dependency graph for all packages located in a specific folder.
Config file will allow you research denendencies, group packages with appropriate way, see circular dependencies, highlight specific subset of packages, etc.

Curently the tool graps all package.json files in mentioned dir and build dependencies based on section "require". All packages mentioned in "require" will be added to graph dispite they are not defined as packages in mentioned dir.

### Environment initialization

Install graphviz
```bash
sudo apt-get install graphviz
```

Perform all appropriate steps to run php in console.

Clone this repo to a specific place on your local mashine. 

### Usage

Run the tool with mentioned parameters. 
As a result you will have a dot file with instructions for dot plus pdf file with graph generated based on instructions.

Play with test artifacts 

```bash
php console.php --projectdir ./test/project --config ./test/testconfig.json --outfile ./test.dot
```

### Config

Config helps organise information on graph with a way suitable for observation.



