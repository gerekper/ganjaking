<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\Error;
class SecurityError extends Error
{
}
\class_alias('MailPoetVendor\\Twig\\Sandbox\\SecurityError', 'MailPoetVendor\\Twig_Sandbox_SecurityError');
