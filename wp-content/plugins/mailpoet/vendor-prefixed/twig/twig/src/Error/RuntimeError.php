<?php
namespace MailPoetVendor\Twig\Error;
if (!defined('ABSPATH')) exit;
class RuntimeError extends Error
{
}
\class_alias('MailPoetVendor\\Twig\\Error\\RuntimeError', 'MailPoetVendor\\Twig_Error_Runtime');
