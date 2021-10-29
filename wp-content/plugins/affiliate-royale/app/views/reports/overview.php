<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
<?php WafpAppHelper::plugin_title($title); ?>
<?php require(WAFP_VIEWS_PATH . "/reports/nav.php"); ?>
<?php

$action = isset($_GET['action'])?$_GET['action']:'';

if($action=='admin_affiliate_top')
  WafpReportsController::admin_affiliate_top();
else
  WafpReportsController::admin_affiliate_stats();

?>
</div>
