# selective/cd-checker

[Circular dependency](https://en.wikipedia.org/wiki/Circular_dependency) checker for PHP.

[![Latest Version on Packagist](https://img.shields.io/github/release/selective-php/cd-checker.svg?style=flat-square)](https://packagist.org/packages/selective/cd-checker)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/selective-php/cd-checker/master.svg?style=flat-square)](https://travis-ci.org/selective-php/cd-checker)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/selective-php/cd-checker.svg?style=flat-square)](https://scrutinizer-ci.com/g/selective-php/cd-checker/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/selective-php/cd-checker.svg?style=flat-square)](https://scrutinizer-ci.com/g/selective-php/cd-checker/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/selective/cd-checker.svg?style=flat-square)](https://packagist.org/packages/selective/cd-checker/stats)


## Features

Detection of circular dependencies between:

* Class A and class B
* Class A and class A (self-reference)
* Scan directories recursive
* Exclude pattern
* Ability to check a single file

## Requirements

* PHP 7.2+

## Installation

```
composer require selective/cd-checker
```

## Usage

Linux

```bash
$ vendor/bin/cd-checker {params}
```

Windows

```bash
> vendor\bin\cd-checker {params}
```

## Parameters

Shortcut | Name | Description
------------ | ------------- | -----------
-h | --help | Display help message.
-d | --directory=DIRECTORY | Directory to scan. [default: "./"]
-f | --file=filename | Single file to scan.
-x | --exclude=EXCLUDE | Files and directories to exclude. You can use exclude patterns ex. tests/*
-q | --quiet | Do not output any message.
-V | --version | Display this application version.
none | --ansi | Force ANSI output.
none | --no-ansi | Disable ANSI output.
-n | --no-interaction | Do not ask any interactive question.
-v -vv -vvv | --verbose | Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.

## License

* MIT
