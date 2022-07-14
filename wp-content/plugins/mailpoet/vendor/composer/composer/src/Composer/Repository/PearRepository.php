<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
class PearRepository extends ArrayRepository
{
 public function __construct()
 {
 throw new \InvalidArgumentException('The PEAR repository has been removed from Composer 2.x');
 }
}
