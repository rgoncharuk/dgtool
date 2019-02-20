# Overview

This tool may be used for composer package dependency visualisation. 
It uses [GraphViz](https://www.graphviz.org/) for graph generation. 

You may create a dependency graph for all packages located in a specific folder.
Config file will allow you research denendencies, group packages with appropriate way, see circular dependencies, highlight specific subset of packages, etc.

Curently the tool graps all package.json files in mentioned dir and build dependencies based on section "require". All packages mentioned in "require" will be added to graph dispite they are not defined as packages in mentioned dir.

# Environment initialization

Install graphviz
```bash
sudo apt-get install graphviz
```

Perform all appropriate steps to run php in console.

Clone this repo to a specific place on your local mashine. 

# Usage

Run the tool with mentioned parameters. 
As a result you will have a dot file with instructions for dot plus pdf file with graph generated based on instructions.

Play with test artifacts 

```bash
php console.php --projectdir ./test/project --config ./test/testconfig.json --outfile ./test.dot
```

# Config

Config helps organise information on graph with a way suitable for observation. Use ./test/testconfig.json as an example of config structure

## Scope

If your project contains packages you do not want to add to graph use black\white list to define required scope. White list have priority. 

!!! Today the tool skips all not magento packages. It should be changed as soon as regexp rules will be added to scope.

## Groups

Use groups to see packages as logically related unions.
Group mode defines grouping behaviour

#### None

Such group will not have affect to graph generation. Use it for packages organisation within config file and possible resuse with other modes.

#### Hide

All packages mentioned in such group will not be present on graph. Use this group to hide details not important for a specific moment.

Note that here we are talking just about visualisation effect. All hidden packages will be mentioned in dependency counts and dependency relations. 

#### Bound

All packages mentioned in such group will be grouped within visible borders. Gategory group name will be present within the same borers. Use this mode for logical packages grouping. 

#### Merge

All packages mentioned in such group will be merged to a single graph node and named with group name. All dependencies related with merged packages will be replaced to the same denendencies related with the merged node. 

All dependency counts will be affected by that merge. Merged node will be reflected as one package.

Use this mode to scale view from packages dependencies to domains\functional areas dependencies.

## Visual Efects

#### Nodes

Each node will represented as a circle. (Style customiation will be added later)

Package name will be splitted to parts for best feet within the node.

!!! Today magento package name prefixes are scipped to save space. Should be changed in the future with a more smart way of label modification.

Eaco node contaon information regarding dependency counts in the following format

Indirect dependents -> Direct dependents -> NODE -> Direct Dependencies -> Indirect Dependencies

#### Dependencies

Each dependency will be reflected with -> line.
Direct and Indirect Circular dependencies wil be reflected with red -> lines

## Highlights

Use that effect to highlight required packages and their dependencies within saved context.

You may define each package separately or add related nodes automatically based on prefixes and sufixes. 

The following format is supported:

DependentsPrefix->PackageName->DependenciesSuffix

#### Dependent Prefix
