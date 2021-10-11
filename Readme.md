# Neos CMS node type YAML file splitter

Allows splitting YAML files into multiple based on node type names.

## Installation

### Recommended

Download the Phar version of the [latest release](https://github.com/Sebobo/YamlSplitter/releases). 
You can find it after the individual change log in the `Assets` section.

Afterwards you should make the file executable by running 

```console
chmod a+x yaml-splitter.phar
```

Now you can follow the usage examples below.

### Via composer 

Add this tool as dependency to your project via

```console
composer require --dev shel/yaml-splitter 
```

Afterwards you can run the commands in your project by prefixing the usage examples like this:

```console
bin/yaml-splitter.php split ...
```

## Usage

### List commands

    ./yaml-splitter.phar list

### Show options for split command

    ./yaml-splitter.phar help split

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
* `use-folders` splits the nodetypes into a folder structure for Neos 7.2+
* `package-key` allows to define the main package key of your node types, so node types with different package keys would be written to `NodeTypes.Override.xyz` files.
* `indentation` number of spaces for indentation in the resulting YAML files
    
#### Example

```console
./yaml-splitter.phar split --dry-run --package-key MyVendor path/to/MyVendor.NodeTypes.yaml path/to/package
```


### Reorganize the nodetypes in a Configuration folder into Neos 7.2+ nodetype subfolders

With Neos 7.2 it's possible to organize nodetypes into separate folders than `Configuration`.
It also allows you to use subfolders.

With the following command you can move all `NodeTypes.*.yaml` from a `Configuration` folder into
another folder. They will automatically be put into subfolders based on their naming scheme.

So for example you have the following files in your `Configuration` folder of your site package:

```console     
Configuration
├── NodeTypes.Content.Image.yaml
├── NodeTypes.Content.Text.yaml
├── NodeTypes.Document.Abstract.Page.yaml
├── NodeTypes.Document.Home.yaml
├── NodeTypes.Document.Page.yaml
├── NodeTypes.Override.Content.Popup.yaml
├── NodeTypes.Override.Mixin.Document.yaml
└── NodeTypes.Override.Mixin.MarginMixin.yaml

```
                                                                                    
Now you run the `reorganize` command:

```console
./yaml-splitter.phar reorganize path/to/sitepackage/Configuration path/to/sitepackage/NodeTypes
```
                                                                                     
After you execute the command you will have the following structure:

```console
NodeTypes
├── Content
│   ├── Image.yaml
│   └── Text.yaml
├── Document
│  ├── Abstract
│  │   └── Page.yaml
│  ├── Home.yaml
│  └── Page.yaml
└── Override
    ├── Content
    │   └── Popup.yaml
    └── Mixin
        ├── Document.yaml
        └── MarginMixin.yaml
```

**Note:** If you still have multiple nodetypes inside one file, it's recommended to first run the `split` command and then `reorganize`.

## Contributing

The tool is based on the Symfony console component.

### Building the phar

First [install box](https://github.com/humbug/box/blob/master/doc/installation.md#installation).

Then run

    composer run compile 
