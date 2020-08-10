<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php
if ( class_exists('\Automattic\WooCommerce\Admin\FeaturePlugin') ) { // if Woocomerce Admin active
 echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_attr__('Dashboard', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'default')) . '" class="__A__Dashboard_Buttons">' . esc_html__('Overview', 'energyplus'). '</a> <a href="' . EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'wc-admin')) . '" class="__A__Dashboard_Buttons __A__Selected">' . esc_html__('Charts', 'energyplus'). ' <i class="fas fa-bookmark fae-woo"></i></a>'));
} else {
  echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_attr__('Dashboard', 'energyplus'), 'description' => '', 'buttons'=>''));
}
?>

<div id="energyplus-wp-notices" class="__A__WP_Notices_Container __A__GP"><?php apply_filters('admin_notices', array()); ?></div>

<div id="energyplus-dashboard-wc-admin">
  <iframe src="<?php echo admin_url('admin.php?page=wc-admin') ?> " id="energyplus-frame" frameborder=0></iframe>
</div>


<script>
  jQuery(document).ready(function() {
    "use strict";

    if (jQuery('#energyplus-wp-notices').text().length>0) {
      jQuery('.energyplus-title > div > h3').append('<a href="javascript:;" class="__A__WP_Notice_Show"><span class="__A__WP_Notice">You have notice(s), click to show</span></a>');
    }

    jQuery('.__A__WP_Notice_Show').on( "click", function() {
      jQuery('.__A__WP_Notices_Container').toggle();
    });
  })
</script>
