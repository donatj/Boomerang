# Boomerang!
[![Latest Stable Version](https://poser.pugx.org/boomerang/boomerang/v/stable.svg)](https://packagist.org/packages/boomerang/boomerang)
[![Total Downloads](https://poser.pugx.org/boomerang/boomerang/downloads.svg)](https://packagist.org/packages/boomerang/boomerang) 
[![Latest Unstable Version](https://poser.pugx.org/boomerang/boomerang/v/unstable.svg)](https://packagist.org/packages/boomerang/boomerang)
[![License](https://poser.pugx.org/boomerang/boomerang/license.svg)](https://packagist.org/packages/boomerang/boomerang)
![CI](https://github.com/donatj/Boomerang/workflows/CI/badge.svg)

Boomerang! is a simple Frisby.js inspired API E2E endpoint testing framework, providing the tools you need to validate REST responses.

Boomerang! consumes your API and validates your defined set of expectations, alerting you to any problems that arise.

Boomerang! is still in active development and more info is coming soon!

Documentation and more information is availible at http://boomerang.work/

## Requirements

- PHP 7.4+ with the CLI, cURL, SPL, and JSON extensions
- A Unix-like environment (or Cygwin on Windows)

Continuous integration tests PHP 7.4 through PHP 8.5.

## Installation

Using composer, `boomerang` can be installed globally via: 

```bash
$ composer global require 'boomerang/boomerang'
```

Or add it to the project you wish to test as a [vendor binary](https://getcomposer.org/doc/articles/vendor-binaries.md):

```bash
$ composer require --dev boomerang/boomerang
```

## Development

```bash
$ git clone https://github.com/donatj/Boomerang.git Boomerang
$ cd Boomerang
$ composer install
```

### Basic Execution

The easiest way to test is simply using the composer executable.

From the root of the cloned project, execute 
```bash
$ ./vendor/bin/boomerang
```

### Building a Phar

You will need Composer to fetch the requirements

```bash
$ php create-phar.php
```
