<?php
namespace MailPoetVendor\Twig\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Source;
final class ArrayLoader implements LoaderInterface, ExistsLoaderInterface, SourceContextLoaderInterface
{
 private $templates = [];
 public function __construct(array $templates = [])
 {
 $this->templates = $templates;
 }
 public function setTemplate($name, $template)
 {
 $this->templates[$name] = $template;
 }
 public function getSourceContext($name)
 {
 $name = (string) $name;
 if (!isset($this->templates[$name])) {
 throw new LoaderError(\sprintf('Template "%s" is not defined.', $name));
 }
 return new Source($this->templates[$name], $name);
 }
 public function exists($name)
 {
 return isset($this->templates[$name]);
 }
 public function getCacheKey($name)
 {
 if (!isset($this->templates[$name])) {
 throw new LoaderError(\sprintf('Template "%s" is not defined.', $name));
 }
 return $name . ':' . $this->templates[$name];
 }
 public function isFresh($name, $time)
 {
 if (!isset($this->templates[$name])) {
 throw new LoaderError(\sprintf('Template "%s" is not defined.', $name));
 }
 return \true;
 }
}
\class_alias('MailPoetVendor\\Twig\\Loader\\ArrayLoader', 'MailPoetVendor\\Twig_Loader_Array');
