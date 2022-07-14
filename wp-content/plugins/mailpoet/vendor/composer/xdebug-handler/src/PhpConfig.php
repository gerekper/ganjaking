<?php
namespace Composer\XdebugHandler;
if (!defined('ABSPATH')) exit;
class PhpConfig
{
 public function useOriginal()
 {
 $this->getDataAndReset();
 return array();
 }
 public function useStandard()
 {
 $data = $this->getDataAndReset();
 if ($data !== null) {
 return array('-n', '-c', $data['tmpIni']);
 }
 return array();
 }
 public function usePersistent()
 {
 $data = $this->getDataAndReset();
 if ($data !== null) {
 $this->updateEnv('PHPRC', $data['tmpIni']);
 $this->updateEnv('PHP_INI_SCAN_DIR', '');
 }
 return array();
 }
 private function getDataAndReset()
 {
 $data = XdebugHandler::getRestartSettings();
 if ($data !== null) {
 $this->updateEnv('PHPRC', $data['phprc']);
 $this->updateEnv('PHP_INI_SCAN_DIR', $data['scanDir']);
 }
 return $data;
 }
 private function updateEnv($name, $value)
 {
 Process::setEnv($name, false !== $value ? $value : null);
 }
}
