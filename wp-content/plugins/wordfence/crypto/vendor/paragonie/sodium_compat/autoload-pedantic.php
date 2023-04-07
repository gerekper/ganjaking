<?php
// phpcs:ignoreFile -- compatibility library for PHP 5-7.1

require_once dirname(__FILE__) . '/autoload.php';
define('DO_PEDANTIC_TEST', true);

ParagonIE_Sodium_Compat::$fastMult = true;
