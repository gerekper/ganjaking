<?php
namespace JsonSchema;
if (!defined('ABSPATH')) exit;
interface UriRetrieverInterface
{
 public function retrieve($uri, $baseUri = null);
}
