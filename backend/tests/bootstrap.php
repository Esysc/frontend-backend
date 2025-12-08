<?php

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Composer dependencies are not installed');
}

require_once __DIR__.'/../vendor/autoload.php';
