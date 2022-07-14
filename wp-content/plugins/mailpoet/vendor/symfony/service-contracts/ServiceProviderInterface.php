<?php
namespace Symfony\Contracts\Service;
if (!defined('ABSPATH')) exit;
use Psr\Container\ContainerInterface;
interface ServiceProviderInterface extends ContainerInterface
{
 public function getProvidedServices(): array;
}
