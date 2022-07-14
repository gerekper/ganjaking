<?php
namespace Composer\Console;
if (!defined('ABSPATH')) exit;
use Composer\IO\IOInterface;
use Composer\Util\Platform;
final class GithubActionError
{
 protected $io;
 public function __construct(IOInterface $io)
 {
 $this->io = $io;
 }
 public function emit($message, $file = null, $line = null)
 {
 if (Platform::getEnv('GITHUB_ACTIONS') && !Platform::getEnv('COMPOSER_TESTS_ARE_RUNNING')) {
 $message = $this->escapeData($message);
 if ($file && $line) {
 $file = $this->escapeProperty($file);
 $this->io->write("::error file=". $file .",line=". $line ."::". $message);
 } elseif ($file) {
 $file = $this->escapeProperty($file);
 $this->io->write("::error file=". $file ."::". $message);
 } else {
 $this->io->write("::error ::". $message);
 }
 }
 }
 private function escapeData($data) {
 // see https://github.com/actions/toolkit/blob/4f7fb6513a355689f69f0849edeb369a4dc81729/packages/core/src/command.ts#L80-L85
 $data = str_replace("%", '%25', $data);
 $data = str_replace("\r", '%0D', $data);
 $data = str_replace("\n", '%0A', $data);
 return $data;
 }
 private function escapeProperty($property) {
 // see https://github.com/actions/toolkit/blob/4f7fb6513a355689f69f0849edeb369a4dc81729/packages/core/src/command.ts#L87-L94
 $property = str_replace("%", '%25', $property);
 $property = str_replace("\r", '%0D', $property);
 $property = str_replace("\n", '%0A', $property);
 $property = str_replace(":", '%3A', $property);
 $property = str_replace(",", '%2C', $property);
 return $property;
 }
}
