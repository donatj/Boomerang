#!/usr/bin/env php
<?php

Phar::mapPhar();
require 'phar://myapp.phar/vendor/autoload.php';

define('BOOMERANG_IS_PHAR', true);

Boomerang\Boomerang::main($argv);

__HALT_COMPILER();