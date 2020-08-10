<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php if (EnergyPlus_Helpers::get('in') ) {
  echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => ''));
} ?>

<?php if (
  FALSE !== stripos($page, 'wc-settings') ||
  FALSE !== stripos($page, 'wc-reports') ||
  FALSE !== stripos($page, 'wc-status') ||
  FALSE !== stripos($page, 'wc-addons') ||
  FALSE !== stripos($page, 'post_type=product') ||
  FALSE !== stripos($page, 'page=product_attributes') ||
  FALSE !== stripos($page, 'post_type=shop_order') ||
  FALSE !== stripos($page, 'post_type=shop_coupon')
  ) { // compatibility ?>
<div id="inbrowser--loading" class="inbrowser--loading h100 d-flex align-items-center align-middle h95">
  <div class="lds-ellipsis lds-ellipsis-black"><div></div><div></div><div></div></div>
</div>
<?php } ?>

<iframe src="<?php echo EnergyPlus_Helpers::clean ( $page, 'about::blank' ); ?>" id="energyplus-frame" class="energyplus-frame<?php if (EnergyPlus_Helpers::get('in') ) { echo " energyplus-frame-in"; } if (EnergyPlus_Helpers::get('go') ) { echo " energyplus-frame-go"; }  ?>" frameborder=0></iframe>
  </div>
