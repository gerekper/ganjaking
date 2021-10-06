<?php
 namespace MailPoetVendor\Doctrine\ORM\Cache; if (!defined('ABSPATH')) exit; interface TimestampRegion extends Region { public function update(CacheKey $key); } 