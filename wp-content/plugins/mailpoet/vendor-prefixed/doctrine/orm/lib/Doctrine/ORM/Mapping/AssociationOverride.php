<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
final class AssociationOverride implements Annotation
{
 public $name;
 public $joinColumns;
 public $joinTable;
 public $inversedBy;
 public $fetch;
}
