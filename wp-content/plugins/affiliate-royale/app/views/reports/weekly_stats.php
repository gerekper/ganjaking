<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<img style="padding: 2px 5px 5px 2px; vertical-align: top;" src="<?php echo WAFP_IMAGES_URL; ?>/affiliate_royale_logo_small@2x.png" width="212px" height="48px" />
<div>&nbsp;</div>
<div class="description"><?php _e('Your 7 day Affiliate activity:', 'affiliate-royale', 'easy-affiliate'); ?></div>
<div>&nbsp;</div>
<?php
  $chart_height = "200px";
  $chart_id = "wafp-weekly-stats";
?>
<?php require(WAFP_VIEWS_PATH . '/shared/flot_chart.php'); ?>
<p><a href="<?php echo admin_url('admin.php?page=affiliate-royale'); ?>" class="button"><?php _e('View More Affiliate Royale Reports', 'affiliate-royale', 'easy-affiliate'); ?></a></p>
