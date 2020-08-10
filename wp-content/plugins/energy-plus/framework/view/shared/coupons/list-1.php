<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!$ajax) {  ?>

  <?php echo EnergyPlus_View::run('header-energyplus'); ?>
  <?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Coupons', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . admin_url( 'post-new.php?post_type=shop_coupon&energyplus_hide' ). '" class="btn btn-sm btn-danger trig"> ' . esc_html__(' &nbsp;+ &nbsp; New coupon &nbsp;', 'energyplus').'</a>')); ?>
  <?php echo EnergyPlus_View::run('coupons/nav', array( 'counts' => $counts )) ?>

  <div id="energyplus-coupons-1"  class="__A__GP">
    <div class="__A__Searching<?php if ('' === EnergyPlus_Helpers::get('s', '')) echo" closed"; ?>">
      <div class="__A__Searching_In">
        <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in coupons...', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>"></span>
      </div>
    </div>
  <?php } ?>
  <div class="__A__List_M1 __A__Container">
    <?php if (0 === count( $coupons )) {  ?>
      <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
        <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
      </div>
    <?php } else {  ?>
      <?php foreach ( $coupons AS $coupon ) { ?>
        <div class="btnA __A__Item collapsed"  id="item_<?php echo esc_attr( $coupon['id'] )?>" data-toggle="collapse" data-target="#item_d_<?php echo esc_attr( $coupon['id'])?>" aria-expanded="false" aria-controls="item_d_<?php echo esc_attr( $coupon['id'])?>">
          <div class="liste  row d-flex align-items-center">
            <div class="col-4 col-sm-2 d-flex align-items-center text-left __A__Coupon_Code">
              <span class="__A__Code badge badge-pill badge-black"><?php echo esc_attr($coupon['code'])  ?></span>
              <button class="__A__Mobile_Actions __A__M1-A"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
            </div>
            <div class="__A__Details2R"></div>
            <div class="__A__Coupon_Info col-8 col-sm-10 col-md-3">
              <h6>
                <?php switch ($coupon['type']) {

                  case "fixed_cart": ?>
                  <?php echo sprintf(__('%s %s', 'energyplus'), get_woocommerce_currency_symbol(), esc_html($coupon['amount'])); ?>
                  <?php break; ?>

                  <?php case "percent": ?>
                  <?php echo sprintf('%s', esc_html($coupon['amount']).'%'); ?>
                  <?php break; ?>

                  <?php default: ?>
                  <?php echo sprintf('%s %s', get_woocommerce_currency_symbol(), esc_html($coupon['amount'])); ?>
                  <?php break; ?>
                <?php } ?>
              </h6>
              <?php if (intval($coupon['product_ids'][0])>0) {  ?>
                <br /><?php esc_html_e('For specific products', 'energyplus'); ?><br>
              <?php } else {  ?>
              <?php } ?>
              <?php
              if ($coupon['expiry_date']) {
                echo esc_html(date_i18n( 'd M', strtotime($coupon['post_date'] )) . ' - ' .date_i18n( 'd M', $coupon['expiry_date']  ));
              } else {
                echo "";
              } ?>
            </div>
            <div class="__A__Col_Coupon_Stats col-7 __A__Col_3" data-colname='' id="energyplus-coupons-stats">
              <?php if (0<$coupon['stats']['usage_count']) {  ?>
                <div class="container">
                  <div class="row d-flex align-items-center">
                    <div class="col"><div class="__A__Big"><?php echo esc_attr( $coupon['stats']['usage_count'])?></div> <?php esc_html_e('TIMES USED', 'energyplus'); ?></div>
                    <div class="col"><div class="__A__Big"><?php echo wc_price($coupon['stats']['total_discount'])?></div> <?php esc_html_e('TOTAL DISCOUNT', 'energyplus'); ?></div>
                    <div class="col"><div class="__A__Big"><?php echo wc_price($coupon['stats']['total_sales'])?></div> <?php esc_html_e('TOTAL SPENT', 'energyplus'); ?></div>
                  </div>
                </div>
              <?php } ?>
              <?php  if ($coupon['expiry_date'] && $coupon['expiry_date']<time()) {  ?>
                <div class="__A__Coupon_Expired">
                  <span class="dashicons dashicons-info"></span> &nbsp; <?php esc_html_e('This coupon has expired', 'energyplus'); ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="collapse col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo esc_attr( $coupon['id'])?>">
            <div class="__A__Item_Details">
              <?php if ('publish' === $coupon['status'] OR 'private' === $coupon['status']) {  ?>
                <span class="__A__StopPropagation">
                  <label class="switch">
                    <input type="checkbox" value="1" data-nonce="<?php echo wp_create_nonce( 'energyplus-coupons--onoff-' . $coupon['code'] )?>" data-id="<?php echo esc_attr(  $coupon['code'])?>" class="success __A__ActivePassive __A__StopPropagation" <?php if ('publish' === $coupon['status']) echo ' checked'; ?> />
                    <span class="__A__slider round"></span>
                  </label></span>
                  <a href="<?php echo EnergyPlus_Helpers::secure_url('coupons', $coupon['id'], array('action' => 'stats', 'id' => $coupon['id'])); ?>" class="__A__StopPropagation"><?php esc_html_e('Usage Stats', 'energyplus'); ?></a>
                  <a href="<?php echo admin_url('post.php?post=' . $coupon['id']. '&action=edit&energyplus_hide' ) ?>" class="__A__StopPropagation __A__HideMe trig"  data-hash="<?php echo esc_attr( $coupon['id'])?>"><?php esc_html_e('Edit', 'energyplus'); ?></a>
                  <a href="<?php echo EnergyPlus_Helpers::secure_url('coupons', $coupon['id'], array('action' => 'delete', 'id' => $coupon['id'])); ?>" class="__A__HideMe __A__StopPropagation text-danger"><?php esc_html_e('Delete', 'energyplus'); ?></a>
                <?php } else {  ?>
                  <a href="<?php echo EnergyPlus_Helpers::secure_url('coupons', $coupon['id'], array('action' => 'delete', 'untrash' => 'true', 'id' => $coupon['id'])); ?>" class="__A__HideMe  __A__StopPropagation"><?php esc_html_e('Restore Coupon', 'energyplus'); ?></a>
                  <a href="<?php echo EnergyPlus_Helpers::secure_url('coupons', $coupon['id'], array('action' => 'delete', 'forever'=> 'true', 'id' => $coupon['id'])); ?>" class=" __A__HideMe __A__StopPropagation"><?php esc_html_e('Delete Forever', 'energyplus'); ?></a>
                <?php } ?>

              </div>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
      <?php if (!$ajax) {  ?>
          <?php echo EnergyPlus_View::run('core/pagination', array( 'count' => $count, 'per_page'=> absint(EnergyPlus::option('reactors-tweaks-pg-customers', 10)), 'page' => intval(EnergyPlus_Helpers::get('pg', 0)))); ?>
      </div>
    </div>
    <?php } ?>
    </div>
  </div>
