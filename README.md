# Boomerang*!*

[![Build Status](https://travis-ci.org/donatj/Boomerang.png?branch=master)](https://travis-ci.org/donatj/Boomerang)

Boomerang*!* is a simple Frisby.js inspired API endpoint testing framework, providing the tools you need to validate REST responses.

Boomerang*!* consumes your API and validates your defined set of expectations.

More info coming soon

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
$ ./composer/bin/boomerang
```

### Building a Phar

You will need Composer to fetch the requirements

```bash
$ php create-phar.php
```
