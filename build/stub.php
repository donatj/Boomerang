#!/usr/bin/env php
<?php

Phar::mapPhar();
require 'phar://myapp.phar/vendor/autoload.php';

Phrisby\Runner\UserInterface::main($argv);

__HALT_COMPILER();