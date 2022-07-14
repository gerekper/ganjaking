<?php
namespace JsonSchema;
if (!defined('ABSPATH')) exit;
interface UriResolverInterface
{
 public function resolve($uri, $baseUri = null);
}
