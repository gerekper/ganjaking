<?php

namespace MailPoet\Util\pQuery;

if (!defined('ABSPATH')) exit;


class Html5Parser extends \pQuery\HtmlParser {
  /** @var string|\pQuery\DomNode */
  public $root = 'MailPoet\Util\pQuery\DomNode';
}
