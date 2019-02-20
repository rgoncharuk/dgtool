# Overview

This tool may be used for composer package dependency visualization. 
It uses [GraphViz](https://www.graphviz.org/) for graph generation. 

You may create a dependency graph for all packages located in a specific folder.
Config file will allow you research dependencies, group packages with an appropriate way, see circular dependencies, highlight a specific subset of packages, etc.

Currently, the tool grabs all package.json files in mentioned dir and build dependencies based on section "require". All packages mentioned in "require" will be added to the graph despite they are not defined as packages in mentioned dir.

# Environment initialization

Install graphviz
```bash
sudo apt-get install graphviz
```

Perform all appropriate steps to run php in console.

Clone this repo to a specific place on your local machine. 

# Usage

Run the tool with the mentioned parameters. 
As a result, you will have a dot file with instructions for [Dot](https://www.graphviz.org/) plus pdf file with graph generated based on instructions.

Play with test artifacts 

```bash
php console.php --projectdir ./test/project --config ./test/testconfig.json --outfile ./test.dot
```

# Config

Config helps organize information on a graph with a way suitable for observation. Use ./test/testconfig.json as an example of config structure

## Scope

If your project contains packages you do not want to add to the graph use the black\white list to define the required scope. The white list has a priority. 

!!! Today the tool skips all not Magento packages. It should be changed as soon as regexp rules will be added to the scope.

## Groups

Use groups to see packages as logically related unions.
Group mode defines grouping behavior

#### None

Such a group will not have an effect on graph generation. Use it for packages organization within the config file and possible reuse with other modes.

#### Hide

All packages mentioned in such a group will not be present on the graph. Use this group to hide details not important for a specific moment.

Note that here we are talking just about visualization effect. All hidden packages will be mentioned in dependency counts and dependency relations. 

#### Bound

All packages mentioned in such a group will be grouped within visible borders. The category group name will be present within the same borders. Use this mode for logical packages grouping. 

#### Merge

All packages mentioned in such a group will be merged to a single graph node and named with the group name. All dependencies related with merged packages will be replaced to the same dependencies related with the merged node. 

All dependency counts will be affected by that merge. The merged node will be reflected as one package.

Use this mode to scale view from packages dependencies to domains\functional areas dependencies.

## Visual Effects

#### Nodes

Each node will be represented as a circle. (Style customization will be added later)

The package name will be split to parts for best feet within the node.

!!! Today Magento package name prefixes are skipped to save space. Should be changed in the future with a more smart way of label modification.

Each node contains information regarding dependency counts in the following format

Indirect dependents \-\> Direct dependents \-\> NODE \-\> Direct Dependencies \-\> Indirect Dependencies

#### Dependencies

Each dependency will be reflected with -> line.
Direct and Indirect Circular dependencies will be reflected with red -> lines

## Highlights

Use that effect to highlight required packages and their dependencies within the saved context.

You may define each package separately or add related nodes automatically based on prefixes and suffixes. 

The following format is supported:

[%DependentsPrefix%]%PackageName%[%DependenciesSuffix%]

| Dependent Prefixes  | Action |
| :--- | :--- |
| I-\>  | add indirect dependents  |
| D-\>  | add direct dependents  |
| \*\-\>  | add all dependents  |

| Dependency Suffixes  | Action |
| :--- | :--- |
| \-\>I  | add indirect dependencies  |
| \-\>D  | add direct dependencies  |
| \-\>\*  | add all dependencies  |
