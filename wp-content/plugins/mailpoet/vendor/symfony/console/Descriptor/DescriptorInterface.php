<?php
namespace Symfony\Component\Console\Descriptor;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Output\OutputInterface;
interface DescriptorInterface
{
 public function describe(OutputInterface $output, $object, array $options = []);
}
