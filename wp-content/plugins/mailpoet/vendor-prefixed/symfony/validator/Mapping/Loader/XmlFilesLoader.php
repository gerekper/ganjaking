<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
class XmlFilesLoader extends FilesLoader
{
 public function getFileLoaderInstance($file)
 {
 return new XmlFileLoader($file);
 }
}
