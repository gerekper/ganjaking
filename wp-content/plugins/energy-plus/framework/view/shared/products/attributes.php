<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Products', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . admin_url( 'edit.php?post_type=product&page=product_attributes' ). '" class="btn btn-sm btn-danger trig trig-refresh"> + &nbsp; ' . esc_html__('New attribute', 'energyplus'). ' &nbsp;</a>')); ?>
<?php echo EnergyPlus_View::run('products/nav' ) ?>

<div id="energyplus-attribute" class="__A__GP">
  <?php if (0 === count( $attributes['product_attributes']  )) {  ?>
    <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
      <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
    </div>
  <?php } else {  ?>
    <div class="__A__List_M2 __A__Container energyplus-list-m2 ">
      <div class="__A__List_M2_Header d-none d-sm-block">
        <div class="row __A__Item">
          <div class="col-2 col-sm-2 __A__Col_2 "><?php esc_html_e('Name', 'energyplus'); ?></div>
          <div class="col-2 col-sm-2 __A__Col_3"><?php esc_html_e('Terms', 'energyplus'); ?></div>
          <div class="col-2 col-sm-auto __A__Col_6"></div>
        </div>
      </div>
      <?php foreach ( $attributes['product_attributes'] AS $attribute ) {  ?>
        <div class="row align-middle align-items-center __A__Item" id='item_<?php echo esc_attr($attribute['id'])  ?>'>
          <div class="col-4 col-sm-2 __A__Col_2 align-middle text-uppercase">
            <?php echo esc_attr($attribute['name'])  ?>
          </div>
          <div class="col-8 col-sm-6 __A__Col_Terms align-middle"><?php $terms = get_terms(wc_attribute_taxonomy_name_by_id( $attribute['id'] ), array( 'hide_empty' => false, 'orderby'=>'id', 'order'=>'ASC' ));
          if (0 < count($terms)) {
            foreach ($terms AS $term) {  ?>
            <span class="badge badge-pill badge-black text-uppercase"><?php echo esc_attr($term->name)?></span>
          <?php }  }  ?></div>
          <div class="col __A__Col_Actions __A__Col_5x  __A__Col_3 align-middle __A__Actions2">
            <ul class="float-right">
              <li><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=' . $attribute['slug']. '&post_type=product' );?>" class="__A__Button1 __A__MainButton trig"><?php esc_html_e('Configure', 'energyplus'); ?></a></li>
              <li><a href="<?php echo admin_url( 'edit.php?post_type=product&page=product_attributes&edit=' . $attribute['id'] );?>" class="__A__Button1  trig"><?php esc_html_e('Edit', 'energyplus'); ?></a></li>
              <li><a href="<?php echo EnergyPlus_Helpers::secure_url('products', $attribute['id'], array('action' => 'view', 'id' => $attribute['id'])); ?>" data-do='delete-attribute' data-nonce='<?php echo wp_create_nonce( 'energyplus-products--attr-delete-' . $attribute['id'] )?>' data-id='<?php echo esc_attr($attribute['id'])?>' class="__A__Button1  __A__AjaxButton" data-confirm="<?php esc_attr_e("Are you sure to delete?", "energyplus")?>"><?php esc_html_e('Delete', 'energyplus'); ?></a></li>
            </ul>
          </div>
          <button class="__A__Mobile_Actions"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
        </div>
      <?php }  ?>
    </div>
  <?php }  ?>
</div>
