<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
interface CompletePackageInterface extends PackageInterface
{
 public function getScripts();
 public function setScripts(array $scripts);
 public function getRepositories();
 public function setRepositories(array $repositories);
 public function getLicense();
 public function setLicense(array $license);
 public function getKeywords();
 public function setKeywords(array $keywords);
 public function getDescription();
 public function setDescription($description);
 public function getHomepage();
 public function setHomepage($homepage);
 public function getAuthors();
 public function setAuthors(array $authors);
 public function getSupport();
 public function setSupport(array $support);
 public function getFunding();
 public function setFunding(array $funding);
 public function isAbandoned();
 public function getReplacementPackage();
 public function setAbandoned($abandoned);
 public function getArchiveName();
 public function setArchiveName($name);
 public function getArchiveExcludes();
 public function setArchiveExcludes(array $excludes);
}
