<?php
namespace Composer\Plugin;
if (!defined('ABSPATH')) exit;
use Composer\EventDispatcher\Event;
use Composer\Package\PackageInterface;
class PostFileDownloadEvent extends Event
{
 private $fileName;
 private $checksum;
 private $url;
 private $context;
 private $type;
 public function __construct($name, $fileName, $checksum, $url, $type, $context = null)
 {
 if ($context === null && $type instanceof PackageInterface) {
 $context = $type;
 $type = 'package';
 trigger_error('PostFileDownloadEvent::__construct should receive a $type=package and the package object in $context since Composer 2.1.', E_USER_DEPRECATED);
 }
 parent::__construct($name);
 $this->fileName = $fileName;
 $this->checksum = $checksum;
 $this->url = $url;
 $this->context = $context;
 $this->type = $type;
 }
 public function getFileName()
 {
 return $this->fileName;
 }
 public function getChecksum()
 {
 return $this->checksum;
 }
 public function getUrl()
 {
 return $this->url;
 }
 public function getContext()
 {
 return $this->context;
 }
 public function getPackage()
 {
 trigger_error('PostFileDownloadEvent::getPackage is deprecated since Composer 2.1, use getContext instead.', E_USER_DEPRECATED);
 $context = $this->getContext();
 return $context instanceof PackageInterface ? $context : null;
 }
 public function getType()
 {
 return $this->type;
 }
}
