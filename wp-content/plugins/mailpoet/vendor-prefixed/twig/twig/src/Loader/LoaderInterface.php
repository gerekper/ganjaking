<?php
namespace MailPoetVendor\Twig\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Source;
interface LoaderInterface
{
 public function getSourceContext($name);
 public function getCacheKey($name);
 public function isFresh($name, $time);
 public function exists($name);
}
\class_alias('MailPoetVendor\\Twig\\Loader\\LoaderInterface', 'MailPoetVendor\\Twig_LoaderInterface');
