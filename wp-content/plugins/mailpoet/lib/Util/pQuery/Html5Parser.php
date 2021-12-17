<?php

namespace MailPoet\Util\pQuery;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\pQuery\HtmlParser;

class Html5Parser extends HtmlParser {
  /** @var string|DomNode */
  public $root = DomNode::class;
}
