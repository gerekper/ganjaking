<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\PersisterException;
use LogicException;
class CantUseInOperatorOnCompositeKeys extends PersisterException
{
 public static function create() : self
 {
 return new self("Can't use IN operator on entities that have composite keys.");
 }
}
