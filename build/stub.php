#!/usr/bin/env php
<?php

Phar::mapPhar();
require 'phar://myapp.phar/vendor/autoload.php';

Boomerang\Boomerang::main($argv);

define('BOOMERANG_IS_PHAR', true);

__HALT_COMPILER();