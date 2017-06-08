# LEVIY Coding Standard

## Installation

Update `composer.json`:

    {
        "repositories": [
            { "type": "vcs", "url": "git@bitbucket.org:dcoorn/leviy-coding-standard.git" }
        ],
        
        "require-dev": {
            "leviy/coding-standard": "^1.0"
        }
    }

Install dependencies:

    composer update leviy/coding-standard

Create a `phpcs.xml` file containing the following bootstrap code sniffer configuration:  

    <?xml version="1.0"?>
    <ruleset name="LEVIY">
        <description>LEVIY</description>
            
        <arg name="extensions" value="php" />
        
        <file>./src</file>
        
        <exclude-pattern>*Test.php</exclude-pattern>
        
        <rule ref="vendor/leviy/coding-standard/src/LEVIY/ruleset.xml"/>
    </ruleset>

An annotated `ruleset.xml` configuration file can be found [here](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml) 
where you can see the complete range of features and configuration options that can be used to modify the configuration to your project needs.

## Usage

Run the code sniffer and show both errors and warnings:

    bin/phpcs --standard=phpcs.xml
    
Run the code sniffer and show only errors:

    bin/phpcs --standard=phpcs.xml --error-severity=0 --warning-severity=1

Run the code sniffer and show only warnings:

    bin/phpcs --standard=phpcs.xml --error-severity=0 --warning-severity=1

Run the code sniffer against a specific file or directory

    bin/phpcs --standard=phpcs.xml src/path/to/file.php
        
    bin/phpcs --standard=phpcs.xml src/path/to/file_1.php src/path/to/file_2.php
        
    bin/phpcs --standard=phpcs.xml src/path/to/directory

## PHPStorm code sniffer validation

1. Open "Preferences" and navigate to "Editor > Inspections".

2. Enable "PHP > PHP Code Sniffer validation".

3. Select a "Custom" coding standard.

4. Select the path to the projects code sniffer configuration.

![](resources/preferences-dialog.png)

**Enjoy the immediate feedback of code sniffer validation issues!**

![](resources/code-sniffer-validation-hint.png)
