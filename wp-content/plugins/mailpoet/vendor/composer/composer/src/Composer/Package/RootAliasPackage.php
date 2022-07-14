<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
class RootAliasPackage extends CompleteAliasPackage implements RootPackageInterface
{
 protected $aliasOf;
 public function __construct(RootPackage $aliasOf, $version, $prettyVersion)
 {
 parent::__construct($aliasOf, $version, $prettyVersion);
 }
 public function getAliasOf()
 {
 return $this->aliasOf;
 }
 public function getAliases()
 {
 return $this->aliasOf->getAliases();
 }
 public function getMinimumStability()
 {
 return $this->aliasOf->getMinimumStability();
 }
 public function getStabilityFlags()
 {
 return $this->aliasOf->getStabilityFlags();
 }
 public function getReferences()
 {
 return $this->aliasOf->getReferences();
 }
 public function getPreferStable()
 {
 return $this->aliasOf->getPreferStable();
 }
 public function getConfig()
 {
 return $this->aliasOf->getConfig();
 }
 public function setRequires(array $require)
 {
 $this->requires = $this->replaceSelfVersionDependencies($require, Link::TYPE_REQUIRE);
 $this->aliasOf->setRequires($require);
 }
 public function setDevRequires(array $devRequire)
 {
 $this->devRequires = $this->replaceSelfVersionDependencies($devRequire, Link::TYPE_DEV_REQUIRE);
 $this->aliasOf->setDevRequires($devRequire);
 }
 public function setConflicts(array $conflicts)
 {
 $this->conflicts = $this->replaceSelfVersionDependencies($conflicts, Link::TYPE_CONFLICT);
 $this->aliasOf->setConflicts($conflicts);
 }
 public function setProvides(array $provides)
 {
 $this->provides = $this->replaceSelfVersionDependencies($provides, Link::TYPE_PROVIDE);
 $this->aliasOf->setProvides($provides);
 }
 public function setReplaces(array $replaces)
 {
 $this->replaces = $this->replaceSelfVersionDependencies($replaces, Link::TYPE_REPLACE);
 $this->aliasOf->setReplaces($replaces);
 }
 public function setAutoload(array $autoload)
 {
 $this->aliasOf->setAutoload($autoload);
 }
 public function setDevAutoload(array $devAutoload)
 {
 $this->aliasOf->setDevAutoload($devAutoload);
 }
 public function setStabilityFlags(array $stabilityFlags)
 {
 $this->aliasOf->setStabilityFlags($stabilityFlags);
 }
 public function setMinimumStability($minimumStability)
 {
 $this->aliasOf->setMinimumStability($minimumStability);
 }
 public function setPreferStable($preferStable)
 {
 $this->aliasOf->setPreferStable($preferStable);
 }
 public function setConfig(array $config)
 {
 $this->aliasOf->setConfig($config);
 }
 public function setReferences(array $references)
 {
 $this->aliasOf->setReferences($references);
 }
 public function setAliases(array $aliases)
 {
 $this->aliasOf->setAliases($aliases);
 }
 public function setSuggests(array $suggests)
 {
 $this->aliasOf->setSuggests($suggests);
 }
 public function setExtra(array $extra)
 {
 $this->aliasOf->setExtra($extra);
 }
 public function __clone()
 {
 parent::__clone();
 $this->aliasOf = clone $this->aliasOf;
 }
}
