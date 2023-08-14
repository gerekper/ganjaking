<?php

namespace SearchWP\Dependencies;

use SearchWP\Dependencies\voku\helper\Bootup;
use SearchWP\Dependencies\voku\helper\UTF8;
Bootup::initAll();
// Enables UTF-8 for PHP
UTF8::checkForSupport();
// Check UTF-8 support for PHP
