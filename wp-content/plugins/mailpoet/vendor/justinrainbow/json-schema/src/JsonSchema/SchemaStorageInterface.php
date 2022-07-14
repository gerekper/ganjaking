<?php
namespace JsonSchema;
if (!defined('ABSPATH')) exit;
interface SchemaStorageInterface
{
 public function addSchema($id, $schema = null);
 public function getSchema($id);
 public function resolveRef($ref);
 public function resolveRefSchema($refSchema);
}
