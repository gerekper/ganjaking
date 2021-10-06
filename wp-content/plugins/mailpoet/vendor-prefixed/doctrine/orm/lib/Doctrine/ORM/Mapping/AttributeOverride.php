<?php
 namespace MailPoetVendor\Doctrine\ORM\Mapping; if (!defined('ABSPATH')) exit; use Attribute; final class AttributeOverride implements Annotation { public $name; public $column; } 