<?php
namespace Composer\DependencyResolver;
if (!defined('ABSPATH')) exit;
use Composer\Repository\RepositorySet;
class RuleSet implements \IteratorAggregate, \Countable
{
 // highest priority => lowest number
 const TYPE_PACKAGE = 0;
 const TYPE_REQUEST = 1;
 const TYPE_LEARNED = 4;
 public $ruleById = array();
 protected static $types = array(
 self::TYPE_PACKAGE => 'PACKAGE',
 self::TYPE_REQUEST => 'REQUEST',
 self::TYPE_LEARNED => 'LEARNED',
 );
 protected $rules;
 protected $nextRuleId = 0;
 protected $rulesByHash = array();
 public function __construct()
 {
 foreach ($this->getTypes() as $type) {
 $this->rules[$type] = array();
 }
 }
 public function add(Rule $rule, $type)
 {
 if (!isset(self::$types[$type])) {
 throw new \OutOfBoundsException('Unknown rule type: ' . $type);
 }
 $hash = $rule->getHash();
 // Do not add if rule already exists
 if (isset($this->rulesByHash[$hash])) {
 $potentialDuplicates = $this->rulesByHash[$hash];
 if (\is_array($potentialDuplicates)) {
 foreach ($potentialDuplicates as $potentialDuplicate) {
 if ($rule->equals($potentialDuplicate)) {
 return;
 }
 }
 } else {
 if ($rule->equals($potentialDuplicates)) {
 return;
 }
 }
 }
 if (!isset($this->rules[$type])) {
 $this->rules[$type] = array();
 }
 $this->rules[$type][] = $rule;
 $this->ruleById[$this->nextRuleId] = $rule;
 $rule->setType($type);
 $this->nextRuleId++;
 if (!isset($this->rulesByHash[$hash])) {
 $this->rulesByHash[$hash] = $rule;
 } elseif (\is_array($this->rulesByHash[$hash])) {
 $this->rulesByHash[$hash][] = $rule;
 } else {
 $originalRule = $this->rulesByHash[$hash];
 $this->rulesByHash[$hash] = array($originalRule, $rule);
 }
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 return $this->nextRuleId;
 }
 public function ruleById($id)
 {
 return $this->ruleById[$id];
 }
 public function getRules()
 {
 return $this->rules;
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 return new RuleSetIterator($this->getRules());
 }
 public function getIteratorFor($types)
 {
 if (!\is_array($types)) {
 $types = array($types);
 }
 $allRules = $this->getRules();
 $rules = array();
 foreach ($types as $type) {
 $rules[$type] = $allRules[$type];
 }
 return new RuleSetIterator($rules);
 }
 public function getIteratorWithout($types)
 {
 if (!\is_array($types)) {
 $types = array($types);
 }
 $rules = $this->getRules();
 foreach ($types as $type) {
 unset($rules[$type]);
 }
 return new RuleSetIterator($rules);
 }
 public function getTypes()
 {
 $types = self::$types;
 return array_keys($types);
 }
 public function getPrettyString(RepositorySet $repositorySet = null, Request $request = null, Pool $pool = null, $isVerbose = false)
 {
 $string = "\n";
 foreach ($this->rules as $type => $rules) {
 $string .= str_pad(self::$types[$type], 8, ' ') . ": ";
 foreach ($rules as $rule) {
 $string .= ($repositorySet && $request && $pool ? $rule->getPrettyString($repositorySet, $request, $pool, $isVerbose) : $rule)."\n";
 }
 $string .= "\n\n";
 }
 return $string;
 }
 public function __toString()
 {
 return $this->getPrettyString();
 }
}
