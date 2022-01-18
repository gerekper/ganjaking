<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManager;
class IdentityGenerator extends AbstractIdGenerator
{
 private $sequenceName;
 public function __construct($sequenceName = null)
 {
 $this->sequenceName = $sequenceName;
 }
 public function generate(EntityManager $em, $entity)
 {
 return (int) $em->getConnection()->lastInsertId($this->sequenceName);
 }
 public function isPostInsertGenerator()
 {
 return \true;
 }
}
