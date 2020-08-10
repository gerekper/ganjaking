<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<?php $buttons = '<a href="' . admin_url( 'post-new.php?post_type=shop_order&energyplus_hide' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; '. esc_attr__('New order', 'energyplus').' &nbsp;</a>';
echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Orders', 'energyplus'), 'description' => '', 'buttons'=>$buttons)); ?>

<?php echo EnergyPlus_View::run('orders/nav', array('list' => $list )) ?>

<div id="energyplus-orders-1" class="__A__Frame_Inline_Top">
  <div class="__A__Searching<?php if ('' === EnergyPlus_Helpers::get('s', '')) echo" closed"; ?>">
    <div class="__A__Searching_In">
      <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in orders..', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>" autofocus></span>
    </div>
  </div>
  <div class="__A__List_M1 __A__Container __A__Frame_Inline">
    <iframe src="<?php echo esc_url_raw($iframe_url); ?> " id="energyplus-frame" frameborder=0></iframe>
  </div>
</div>
