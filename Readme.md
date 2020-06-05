# Neos CMS node type YAML file splitter

Allows splitting YAML files into multiple based on node type names.

## Usage

### List commands

    ./yaml-splitter.phar list

### Split a file

    ./yaml-splitter.phar split [options] [--] <path> [<output-path>]
    
#### Options

* `dry-run` allows seeing what would happen without writing any file
* `package-key` allows to define the main package key of your nodetypes, so nodetypes with different package keys would be written to `NodeTypes.Override.xyz` files.
* `indentation` number of spaces for indendation in the resulting YAML files
    
#### Example

    ./yaml-splitter.phar split --dry-run --package-key MyVendor path/to/MyVendor.NodeTypes.yaml path/to/package

## Contributing

### Building the phar

First [install box](https://github.com/humbug/box/blob/master/doc/installation.md#installation).

Then run

    composer run compile 
