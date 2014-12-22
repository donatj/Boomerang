# Boomerang!
[![Latest Stable Version](https://poser.pugx.org/boomerang/boomerang/v/stable.png)](https://packagist.org/packages/boomerang/boomerang) [![Total Downloads](https://poser.pugx.org/boomerang/boomerang/downloads.png)](https://packagist.org/packages/boomerang/boomerang) [![Latest Unstable Version](https://poser.pugx.org/boomerang/boomerang/v/unstable.png)](https://packagist.org/packages/boomerang/boomerang) [![License](https://poser.pugx.org/boomerang/boomerang/license.png)](https://packagist.org/packages/boomerang/boomerang) [![Build Status](https://travis-ci.org/donatj/Boomerang.png?branch=master)](https://travis-ci.org/donatj/Boomerang)
[![Dependency Status](https://www.versioneye.com/php/boomerang:boomerang/dev-master/badge.png)](https://www.versioneye.com/php/boomerang:boomerang/dev-master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/donatj/Boomerang/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/donatj/Boomerang/?branch=master)

Boomerang! is a simple Frisby.js inspired API endpoint testing framework, providing the tools you need to validate REST responses.

Boomerang! consumes your API and validates your defined set of expectations, alerting you to any problems that arise.

Boomerang! is still in development and more info is coming soon!

## Requirements

- PHP 5.3.0+ with CLI and SPL
- *nix or cygwin on windows.

## Installation

Using composer, boomerang can be installed globally via: 

```bash
$ composer global require 'boomerang/boomerang=dev-master'
```

If you are using composer for the project you wish to test, you can simply add

```json
{
  "require-dev": {
      "boomerang/boomerang": "dev-master"
  }
}
```

## Development

```bash
$ git clone https://github.com/donatj/Boomerang.git Boomerang
$ cd Boomerang
$ composer.phar install
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
