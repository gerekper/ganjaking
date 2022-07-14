<?php
namespace Composer\XdebugHandler;
if (!defined('ABSPATH')) exit;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
class Status
{
 const ENV_RESTART = 'XDEBUG_HANDLER_RESTART';
 const CHECK = 'Check';
 const ERROR = 'Error';
 const INFO = 'Info';
 const NORESTART = 'NoRestart';
 const RESTART = 'Restart';
 const RESTARTING = 'Restarting';
 const RESTARTED = 'Restarted';
 private $debug;
 private $envAllowXdebug;
 private $loaded;
 private $logger;
 private $modeOff;
 private $time;
 public function __construct($envAllowXdebug, $debug)
 {
 $start = getenv(self::ENV_RESTART);
 Process::setEnv(self::ENV_RESTART);
 $this->time = is_numeric($start) ? round((microtime(true) - $start) * 1000) : 0;
 $this->envAllowXdebug = $envAllowXdebug;
 $this->debug = $debug && defined('STDERR');
 $this->modeOff = false;
 }
 public function setLogger(LoggerInterface $logger)
 {
 $this->logger = $logger;
 }
 public function report($op, $data)
 {
 if ($this->logger !== null || $this->debug) {
 $callable = array($this, 'report'.$op);
 if (!is_callable($callable)) {
 throw new \InvalidArgumentException('Unknown op handler: '.$op);
 }
 $params = $data !== null ? $data : array();
 call_user_func_array($callable, array($params));
 }
 }
 private function output($text, $level = null)
 {
 if ($this->logger !== null) {
 $this->logger->log($level !== null ? $level: LogLevel::DEBUG, $text);
 }
 if ($this->debug) {
 fwrite(STDERR, sprintf('xdebug-handler[%d] %s', getmypid(), $text.PHP_EOL));
 }
 }
 private function reportCheck($loaded)
 {
 list($version, $mode) = explode('|', $loaded);
 if ($version !== '') {
 $this->loaded = '('.$version.')'.($mode !== '' ? ' mode='.$mode : '');
 }
 $this->modeOff = $mode === 'off';
 $this->output('Checking '.$this->envAllowXdebug);
 }
 private function reportError($error)
 {
 $this->output(sprintf('No restart (%s)', $error), LogLevel::WARNING);
 }
 private function reportInfo($info)
 {
 $this->output($info);
 }
 private function reportNoRestart()
 {
 $this->output($this->getLoadedMessage());
 if ($this->loaded !== null) {
 $text = sprintf('No restart (%s)', $this->getEnvAllow());
 if (!((bool) getenv($this->envAllowXdebug))) {
 $text .= ' Allowed by '.($this->modeOff ? 'mode' : 'application');
 }
 $this->output($text);
 }
 }
 private function reportRestart()
 {
 $this->output($this->getLoadedMessage());
 Process::setEnv(self::ENV_RESTART, (string) microtime(true));
 }
 private function reportRestarted()
 {
 $loaded = $this->getLoadedMessage();
 $text = sprintf('Restarted (%d ms). %s', $this->time, $loaded);
 $level = $this->loaded !== null ? LogLevel::WARNING : null;
 $this->output($text, $level);
 }
 private function reportRestarting($command)
 {
 $text = sprintf('Process restarting (%s)', $this->getEnvAllow());
 $this->output($text);
 $text = 'Running '.$command;
 $this->output($text);
 }
 private function getEnvAllow()
 {
 return $this->envAllowXdebug.'='.getenv($this->envAllowXdebug);
 }
 private function getLoadedMessage()
 {
 $loaded = $this->loaded !== null ? sprintf('loaded %s', $this->loaded) : 'not loaded';
 return 'The Xdebug extension is '.$loaded;
 }
}
