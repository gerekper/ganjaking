<?php
namespace Symfony\Component\Filesystem\Exception;
if (!defined('ABSPATH')) exit;
interface IOExceptionInterface extends ExceptionInterface
{
 public function getPath();
}
