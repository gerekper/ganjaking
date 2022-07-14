<?php
namespace Composer\DependencyResolver\Operation;
if (!defined('ABSPATH')) exit;
use Composer\Package\AliasPackage;
class MarkAliasUninstalledOperation extends SolverOperation implements OperationInterface
{
 const TYPE = 'markAliasUninstalled';
 protected $package;
 public function __construct(AliasPackage $package)
 {
 $this->package = $package;
 }
 public function getPackage()
 {
 return $this->package;
 }
 public function show($lock)
 {
 return 'Marking <info>'.$this->package->getPrettyName().'</info> (<comment>'.$this->package->getFullPrettyVersion().'</comment>) as uninstalled, alias of <info>'.$this->package->getAliasOf()->getPrettyName().'</info> (<comment>'.$this->package->getAliasOf()->getFullPrettyVersion().'</comment>)';
 }
}
