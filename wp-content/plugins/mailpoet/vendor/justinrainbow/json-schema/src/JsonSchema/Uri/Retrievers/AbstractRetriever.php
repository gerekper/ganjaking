<?php
namespace JsonSchema\Uri\Retrievers;
if (!defined('ABSPATH')) exit;
abstract class AbstractRetriever implements UriRetrieverInterface
{
 protected $contentType;
 public function getContentType()
 {
 return $this->contentType;
 }
}
