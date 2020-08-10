<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Coupons', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . admin_url( 'post-new.php?post_type=shop_coupon&energyplus_hide' ). '" class="btn btn-sm btn-danger trig"> ' . esc_html__(' &nbsp;+ &nbsp; New coupon &nbsp;', 'energyplus').'</a>')); ?>
<?php echo EnergyPlus_View::run('coupons/nav', array( 'counts' => $counts )) ?>

<div id="energyplus-coupons-1"  class="__A__Frame_Inline_Top">
  <div class="__A__Searching<?php if ('' === EnergyPlus_Helpers::get('s', '')) echo" closed"; ?>">
    <div class="__A__Searching_In">
      <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in coupons..', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>" autofocus></span>
    </div>
  </div>
  <div class="__A__List_M1 __A__Container __A__Frame_Inline">
    <iframe src="<?php echo esc_url_raw($iframe_url); ?> " id="energyplus-frame" frameborder=0></iframe>
  </div>
</div>
