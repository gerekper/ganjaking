<?php

namespace MailPoet\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Idiorm\ORM;

interface Filter {
  public function toSql(ORM $orm);

  public function toArray();
}
