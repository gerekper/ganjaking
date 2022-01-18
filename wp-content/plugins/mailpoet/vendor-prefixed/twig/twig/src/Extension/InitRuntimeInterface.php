<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
interface InitRuntimeInterface
{
 public function initRuntime(Environment $environment);
}
\class_alias('MailPoetVendor\\Twig\\Extension\\InitRuntimeInterface', 'MailPoetVendor\\Twig_Extension_InitRuntimeInterface');
