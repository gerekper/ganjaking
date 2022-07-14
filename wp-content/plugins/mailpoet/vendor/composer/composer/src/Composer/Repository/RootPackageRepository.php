<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
use Composer\Package\RootPackageInterface;
class RootPackageRepository extends ArrayRepository
{
 public function __construct(RootPackageInterface $package)
 {
 parent::__construct(array($package));
 }
 public function getRepoName()
 {
 return 'root package repo';
 }
}
