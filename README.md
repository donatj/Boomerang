# Boomerang_!_

[![Latest Stable Version](https://poser.pugx.org/boomerang/boomerang/version)](https://packagist.org/packages/boomerang/boomerang)
[![Total Downloads](https://poser.pugx.org/boomerang/boomerang/downloads)](https://packagist.org/packages/boomerang/boomerang)
[![License](https://poser.pugx.org/boomerang/boomerang/license)](https://packagist.org/packages/boomerang/boomerang)
[![ci.yml](https://github.com/donatj/Boomerang/actions/workflows/ci.yml/badge.svg?)](https://github.com/donatj/Boomerang/actions/workflows/ci.yml)


Boomerang_!_ is a simple Frisby.js inspired API E2E endpoint testing framework, providing the tools you need to validate REST responses.

Boomerang_!_ consumes your API and validates your defined set of expectations, alerting you to any problems that arise.

Documentation and more information is available at https://boomerang.work/
			

## Requirements

- **psr/http-message**: ^1.0 || ^2.0
- **psr/http-factory**: ^1.0
- **php-http/discovery**: ^1.18
- **psr/http-message-implementation**: *
- **psr/http-factory-implementation**: *
- **corpus/http**: ^1.0
- **donatj/cli-toolkit**: ^0.3.1
- **donatj/flags**: ^1.5
- **php**: >=7.4.0
- **ext-curl**: *
- **ext-SPL**: *
- **ext-json**: *

## Installing

Install the latest version with:

```bash
composer require --dev 'boomerang/boomerang' '[Any PSR-7 provider]' '[Any PSR-17 provider]'
```

If your project already includes a PSR-7 and PSR-17 implementation there is no need to require a new one.

### Guzzle PSR-7

An easy solution is [Guzzle PSR-7](https://github.com/guzzle/psr7) as it provides both PSR-7 and PSR-17. It can be used as follows.

Install the latest version with:

```bash
composer require --dev 'boomerang/boomerang' 'guzzlehttp/psr7'
```