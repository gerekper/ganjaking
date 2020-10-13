<?php

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


class SecondLevelDomainNames {
  public function get($host) {
    if (preg_match('/[^.]*\.[^.]{2,3}(?:\.[^.]{2,3})?$/', $host, $matches)) {
      return $matches[0];
    }
    return $host;
  }
}
