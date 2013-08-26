#!/usr/bin/env php
<?php

Phar::mapPhar();
require 'phar://myapp.phar/vendor/autoload.php';

Boomerang\Boomerang::main($argv);

__HALT_COMPILER();