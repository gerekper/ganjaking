<?php

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Psr\Cache\InvalidArgumentException;

class PSRCacheInvalidArgumentException extends \Exception implements InvalidArgumentException {

}
