<?php
namespace Composer\DependencyResolver\Operation;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
class InstallOperation extends SolverOperation implements OperationInterface
{
 const TYPE = 'install';
 protected $package;
 public function __construct(PackageInterface $package)
 {
 $this->package = $package;
 }
 public function getPackage()
 {
 return $this->package;
 }
 public function show($lock)
 {
 return self::format($this->package, $lock);
 }
 public static function format(PackageInterface $package, $lock = false)
 {
 return ($lock ? 'Locking ' : 'Installing ').'<info>'.$package->getPrettyName().'</info> (<comment>'.$package->getFullPrettyVersion().'</comment>)';
 }
}
