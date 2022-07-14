<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
class LockArrayRepository extends ArrayRepository
{
 public function getRepoName()
 {
 return 'lock repo';
 }
}
