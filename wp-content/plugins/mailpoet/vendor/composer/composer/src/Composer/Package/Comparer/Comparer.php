<?php
namespace Composer\Package\Comparer;
if (!defined('ABSPATH')) exit;
class Comparer
{
 private $source;
 private $update;
 private $changed;
 public function setSource($source)
 {
 $this->source = $source;
 }
 public function setUpdate($update)
 {
 $this->update = $update;
 }
 public function getChanged($toString = false, $explicated = false)
 {
 $changed = $this->changed;
 if (!count($changed)) {
 return false;
 }
 if ($explicated) {
 foreach ($changed as $sectionKey => $itemSection) {
 foreach ($itemSection as $itemKey => $item) {
 $changed[$sectionKey][$itemKey] = $item.' ('.$sectionKey.')';
 }
 }
 }
 if ($toString) {
 $strings = array();
 foreach ($changed as $sectionKey => $itemSection) {
 foreach ($itemSection as $itemKey => $item) {
 $strings[] = $item."\r\n";
 }
 }
 $changed = implode("\r\n", $strings);
 }
 return $changed;
 }
 public function doCompare()
 {
 $source = array();
 $destination = array();
 $this->changed = array();
 $currentDirectory = getcwd();
 chdir($this->source);
 $source = $this->doTree('.', $source);
 if (!is_array($source)) {
 return;
 }
 chdir($currentDirectory);
 chdir($this->update);
 $destination = $this->doTree('.', $destination);
 if (!is_array($destination)) {
 exit;
 }
 chdir($currentDirectory);
 foreach ($source as $dir => $value) {
 foreach ($value as $file => $hash) {
 if (isset($destination[$dir][$file])) {
 if ($hash !== $destination[$dir][$file]) {
 $this->changed['changed'][] = $dir.'/'.$file;
 }
 } else {
 $this->changed['removed'][] = $dir.'/'.$file;
 }
 }
 }
 foreach ($destination as $dir => $value) {
 foreach ($value as $file => $hash) {
 if (!isset($source[$dir][$file])) {
 $this->changed['added'][] = $dir.'/'.$file;
 }
 }
 }
 }
 private function doTree($dir, &$array)
 {
 if ($dh = opendir($dir)) {
 while ($file = readdir($dh)) {
 if ($file !== '.' && $file !== '..') {
 if (is_link($dir.'/'.$file)) {
 $array[$dir][$file] = readlink($dir.'/'.$file);
 } elseif (is_dir($dir.'/'.$file)) {
 if (!count($array)) {
 $array[0] = 'Temp';
 }
 if (!$this->doTree($dir.'/'.$file, $array)) {
 return false;
 }
 } elseif (is_file($dir.'/'.$file) && filesize($dir.'/'.$file)) {
 $array[$dir][$file] = md5_file($dir.'/'.$file);
 }
 }
 }
 if (count($array) > 1 && isset($array['0'])) {
 unset($array['0']);
 }
 return $array;
 }
 return false;
 }
}
