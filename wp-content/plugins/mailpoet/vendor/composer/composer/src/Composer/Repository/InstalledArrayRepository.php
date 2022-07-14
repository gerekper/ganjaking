<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
class InstalledArrayRepository extends WritableArrayRepository implements InstalledRepositoryInterface
{
 public function getRepoName()
 {
 return 'installed '.parent::getRepoName();
 }
 public function isFresh()
 {
 // this is not a completely correct implementation but there is no way to
 // distinguish an empty repo and a newly created one given this is all in-memory
 return $this->count() === 0;
 }
}
