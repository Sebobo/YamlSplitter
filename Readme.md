# Neos CMS node type YAML file splitter

Allows splitting YAML files into multiple based on node type names.

## Usage

### List commands

    ./yaml-splitter.phar list

### Split a file

    ./yaml-splitter.phar split [options] [--] <path> [<output-path>]
    
This will copy each node type found in the input file into a new file.
Each new file be have a name like `NodeTypes.MyNodeType.yaml`.

Additionally, each filename will get a prefix after `NodeTypes` like `Document`, `Content` or `Mixin`
based on the name of the actual node type.

Also, when the option `package-key` is provided, node types matching the package key will
have the standard naming and others will get names like `NodeTypes.Override.SomeOtherNodeType.yaml`.
    
#### Options

* `dry-run` allows seeing what would happen without writing any file
* `package-key` allows to define the main package key of your node types, so node types with different package keys would be written to `NodeTypes.Override.xyz` files.
* `indentation` number of spaces for indentation in the resulting YAML files
    
#### Example

    ./yaml-splitter.phar split --dry-run --package-key MyVendor path/to/MyVendor.NodeTypes.yaml path/to/package

## Contributing

The tool is based on the Symfony console component.

### Building the phar

First [install box](https://github.com/humbug/box/blob/master/doc/installation.md#installation).

Then run

    composer run compile 
