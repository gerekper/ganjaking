<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
class CompletePackage extends Package implements CompletePackageInterface
{
 protected $repositories = array();
 protected $license = array();
 protected $keywords = array();
 protected $authors = array();
 protected $description = null;
 protected $homepage = null;
 protected $scripts = array();
 protected $support = array();
 protected $funding = array();
 protected $abandoned = false;
 protected $archiveName = null;
 protected $archiveExcludes = array();
 public function setScripts(array $scripts)
 {
 $this->scripts = $scripts;
 }
 public function getScripts()
 {
 return $this->scripts;
 }
 public function setRepositories(array $repositories)
 {
 $this->repositories = $repositories;
 }
 public function getRepositories()
 {
 return $this->repositories;
 }
 public function setLicense(array $license)
 {
 $this->license = $license;
 }
 public function getLicense()
 {
 return $this->license;
 }
 public function setKeywords(array $keywords)
 {
 $this->keywords = $keywords;
 }
 public function getKeywords()
 {
 return $this->keywords;
 }
 public function setAuthors(array $authors)
 {
 $this->authors = $authors;
 }
 public function getAuthors()
 {
 return $this->authors;
 }
 public function setDescription($description)
 {
 $this->description = $description;
 }
 public function getDescription()
 {
 return $this->description;
 }
 public function setHomepage($homepage)
 {
 $this->homepage = $homepage;
 }
 public function getHomepage()
 {
 return $this->homepage;
 }
 public function setSupport(array $support)
 {
 $this->support = $support;
 }
 public function getSupport()
 {
 return $this->support;
 }
 public function setFunding(array $funding)
 {
 $this->funding = $funding;
 }
 public function getFunding()
 {
 return $this->funding;
 }
 public function isAbandoned()
 {
 return (bool) $this->abandoned;
 }
 public function setAbandoned($abandoned)
 {
 $this->abandoned = $abandoned;
 }
 public function getReplacementPackage()
 {
 return \is_string($this->abandoned) ? $this->abandoned : null;
 }
 public function setArchiveName($name)
 {
 $this->archiveName = $name;
 }
 public function getArchiveName()
 {
 return $this->archiveName;
 }
 public function setArchiveExcludes(array $excludes)
 {
 $this->archiveExcludes = $excludes;
 }
 public function getArchiveExcludes()
 {
 return $this->archiveExcludes;
 }
}
