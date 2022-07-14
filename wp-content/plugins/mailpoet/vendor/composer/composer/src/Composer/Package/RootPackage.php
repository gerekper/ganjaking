<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
class RootPackage extends CompletePackage implements RootPackageInterface
{
 const DEFAULT_PRETTY_VERSION = '1.0.0+no-version-set';
 protected $minimumStability = 'stable';
 protected $preferStable = false;
 protected $stabilityFlags = array();
 protected $config = array();
 protected $references = array();
 protected $aliases = array();
 public function setMinimumStability($minimumStability)
 {
 $this->minimumStability = $minimumStability;
 }
 public function getMinimumStability()
 {
 return $this->minimumStability;
 }
 public function setStabilityFlags(array $stabilityFlags)
 {
 $this->stabilityFlags = $stabilityFlags;
 }
 public function getStabilityFlags()
 {
 return $this->stabilityFlags;
 }
 public function setPreferStable($preferStable)
 {
 $this->preferStable = $preferStable;
 }
 public function getPreferStable()
 {
 return $this->preferStable;
 }
 public function setConfig(array $config)
 {
 $this->config = $config;
 }
 public function getConfig()
 {
 return $this->config;
 }
 public function setReferences(array $references)
 {
 $this->references = $references;
 }
 public function getReferences()
 {
 return $this->references;
 }
 public function setAliases(array $aliases)
 {
 $this->aliases = $aliases;
 }
 public function getAliases()
 {
 return $this->aliases;
 }
}
