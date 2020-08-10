<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Coupons_Mode_2 __A__Scroll<?php if (EnergyPlus_Helpers::get('orderby')) { echo " mb-0"; }?>">
  <div class="row energyplus-gp __A__GP">
    <ul>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('action')?>" href="<?php echo EnergyPlus_Helpers::admin_page('products', array( ));  ?>"><?php esc_html_e('Products', 'energyplus'); ?></a></li>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('action', 'categories')?>" href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'action' => 'categories' ));  ?>"><?php esc_html_e('Categories', 'energyplus'); ?></a></li>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('action', 'attributes')?>" href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'action' => 'attributes' ));  ?>"><?php esc_html_e('Attributes', 'energyplus'); ?></a></li>

      <?php do_action('energyplus_submenu', 'products'); ?>

      <?php if ('' === EnergyPlus_Helpers::get('action')): ?>
        <li class="__A__Li_Search">
          <?php if('-1' !== EnergyPlus_Helpers::get('category') && '-2' !== EnergyPlus_Helpers::get('category')) { ?>
          <div class="btn-group">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="dashicons dashicons-sort"></span></a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="<?php echo EnergyPlus_Helpers::thead_sort('title')?>"><?php esc_html_e('Name', 'energyplus'); ?></a>
              <a class="dropdown-item" href="<?php echo EnergyPlus_Helpers::thead_sort('meta__price')?>"><?php esc_html_e('Price', 'energyplus'); ?></a>
              <a class="dropdown-item" href="<?php echo EnergyPlus_Helpers::thead_sort('meta__sku')?>"><?php esc_html_e('SKU', 'energyplus'); ?></a>
              <a class="dropdown-item" href="<?php echo EnergyPlus_Helpers::thead_sort('date')?>"><?php esc_html_e('Date', 'energyplus'); ?></a>
            </div>
          </div>
        <?php } ?>
          <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'energyplus'); ?></a>
        </li>
      <?php endif; ?>
    </ul>
  </div>

</div>
<?php if (EnergyPlus_Helpers::get('orderby')) { ?>
<div class="energyplus-title--menu __A__Coupons_Mode_2 __A__Scroll __A__OrderBy">
  <div class="row energyplus-gp __A__GP">
    <ul>
      <li>ORDER BY</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;  &mdash;</li>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('orderby', 'title')?>" href="<?php echo EnergyPlus_Helpers::thead_sort('title')?>"><?php esc_html_e('Name', 'energyplus'); ?></a></li>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('orderby', 'meta__price')?>" href="<?php echo EnergyPlus_Helpers::thead_sort('meta__price')?>"><?php esc_html_e('Price', 'energyplus'); ?></a></li>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('orderby', 'meta__sku')?>" href="<?php echo EnergyPlus_Helpers::thead_sort('meta__sku')?>"><?php esc_html_e('SKU', 'energyplus'); ?></a></li>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('orderby', 'date')?>" href="<?php echo EnergyPlus_Helpers::thead_sort('date')?>"><?php esc_html_e('Date', 'energyplus'); ?></a></li>


    </ul>
  </div>
</div>
<?php } ?>
