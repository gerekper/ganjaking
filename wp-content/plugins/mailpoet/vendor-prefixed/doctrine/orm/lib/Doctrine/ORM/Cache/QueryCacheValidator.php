<?php
 namespace MailPoetVendor\Doctrine\ORM\Cache; if (!defined('ABSPATH')) exit; interface QueryCacheValidator { public function isValid(QueryCacheKey $key, QueryCacheEntry $entry); } 