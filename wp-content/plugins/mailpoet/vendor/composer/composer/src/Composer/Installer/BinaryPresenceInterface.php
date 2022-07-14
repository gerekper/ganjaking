<?php
namespace Composer\Installer;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
interface BinaryPresenceInterface
{
 public function ensureBinariesPresence(PackageInterface $package);
}
