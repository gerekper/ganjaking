<?php
 namespace MailPoetVendor\Doctrine\ORM\Cache; if (!defined('ABSPATH')) exit; interface MultiGetRegion { public function getMultiple(CollectionCacheEntry $collection); } 