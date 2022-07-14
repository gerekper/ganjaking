<?php
namespace JsonSchema\Uri\Retrievers;
if (!defined('ABSPATH')) exit;
interface UriRetrieverInterface
{
 public function retrieve($uri);
 public function getContentType();
}
