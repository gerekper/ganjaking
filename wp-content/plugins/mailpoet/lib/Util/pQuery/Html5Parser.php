<?php

namespace MailPoet\Util\pQuery;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\pQuery\Html5Parser as pQueryHtml5Parser;

class Html5Parser extends pQueryHtml5Parser {
  /** @var string|DomNode */
  public $root = DomNode::class;
}
