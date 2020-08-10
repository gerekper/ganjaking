<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!$ajax) {  ?>

  <?php echo EnergyPlus_View::run('header-energyplus'); ?>
  <?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Customers', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . admin_url( 'user-new.php?energyplus_hide' ). '" class="btn btn-sm btn-danger trig"> ' . esc_html__(' &nbsp;+ &nbsp; New customer &nbsp;', 'energyplus').'</a>')); ?>
  <?php echo EnergyPlus_View::run('customers/nav', array('counts'=>$counts, 'roles'=>$roles)) ?>

  <div id="energyplus-customers-1"  class="">

    <div class="__A__Searching<?php if ('' === EnergyPlus_Helpers::get('s', '')) echo" closed"; ?>">
      <div class="__A__Searching_In">
        <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in customers...', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>"></span>
      </div>
    </div>

    <div class="__A__List_M1 __A__Container __A__GP">
    <?php } ?>
    <?php if (0 === count($customers)) { ?>
      <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
        <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
      </div>
    <?php } else { ?>
      <div class="__A__Customers_Container">
      <?php foreach ( $customers AS $customer ) { ?>
      <div class="btnA __A__Item collapsed" id="item_<?php echo  esc_attr($customer['id'])?>" data-toggle="collapse" data-target="#item_d_<?php echo  esc_attr($customer['id'])?>" aria-expanded="false" aria-controls="item_d_<?php echo esc_attr($customer['id'])?>">
        <div class="liste  row d-flex align-items-center">

          <div class="col-7 col-sm-3 __A__Col_Name">
            <p class="energyplus-orders--name">
              <?php echo esc_html(sprintf('%s %s', $customer['first_name'], $customer['last_name']))  ?>
            </p>
            <p class="energyplus-orders--address">
              <?php echo esc_html(isset(WC()->countries->states[$customer['billing_address']['country']][$customer['billing_address']['state']])? WC()->countries->states[$customer['billing_address']['country']][$customer['billing_address']['state']] : $customer['billing_address']['state']) ?>
            </p>
          </div>
          <div class="col col-3 __A__Col_Email __A__Col_3 __A__StopPropagation align-middle" data-colname="<?php esc_attr_e('E-mail', 'energyplus'); ?>"><a href="mailto:<?php echo esc_attr($customer['email']) ?>"><?php echo esc_html($customer['email']) ?></a>         </div>
          <div class="col col-2 __A__Col_Phone __A__Col_3 __A__StopPropagation  align-middle" data-colname="<?php esc_attr_e('Phone', 'energyplus'); ?>"><a href="tel:<?php echo esc_attr($customer['billing_address']['phone']) ?>"><?php echo esc_html($customer['billing_address']['phone']) ?></a> &nbsp;</div>
          <div class="col col-2 __A__Col_OrderCount __A__Col_3 align-middle text-right" data-colname="<?php esc_attr_e('Orders', 'energyplus'); ?>" data-order-count="<?php echo esc_attr($customer['orders_count']) ?>"><?php echo esc_html($customer['orders_count']) ?> <?php esc_html_e('ORDERS', 'energyplus'); ?></div>
          <div class="col col-sm-2 __A__Col_TotalSpent __A__Col_3X text-right"  data-colname="<?php esc_attr_e('Spent', 'energyplus'); ?>">
            <span class="energyplus-orders--item-price"><?php echo wc_price($customer['total_spent']); ?></span>
            <button class="__A__Mobile_Actions __A__M1-A"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
          </div>
        </div>
        <div class="collapse col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo esc_attr($customer['id'])?>">
          <div class="__A__Item_Details">
            <div class="row">
              <div class="col-sm-9  __A__Customer_Details text-left">
                <div class="lds-ellipsis lds-ellipsis-black"><div></div><div></div><div></div></div>
              </div>
              <div class="col-sm-1"></div>
              <div class="col-sm-2 __A__Customer_Details_Actions">
                <?php if (0 < intval($customer['id'])) { ?> <a href="<?php echo EnergyPlus_Helpers::secure_url('customers', esc_attr($customer['id']), array('action' => 'view', 'id' => esc_attr($customer['id']))); ?>" class="__A__StopPropagation trig" data-hash="<?php echo esc_attr($customer['id']) ?>"><?php esc_html_e('View customer', 'energyplus'); ?></a><?php } ?>
                <a href="<?php echo admin_url( "user-edit.php?user_id=" . esc_attr($customer['id'])); ?>" class="__A__HideMe __A__StopPropagation trig"><?php esc_html_e('Edit customer', 'energyplus'); ?></a>

                <a href="mailto:<?php echo sanitize_email($customer['email']) ?>" class="__A__StopPropagation __A__HideMe trig"><?php esc_html_e('Send e-mail', 'energyplus'); ?></a>
                <a href="<?php echo wp_nonce_url( "users.php?action=delete&user=" . esc_attr($customer['id']), 'bulk-users' ); ?>" class="__A__HideMe __A__StopPropagation text-danger trig"><?php esc_html_e('Delete', 'energyplus'); ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  <?php } ?>
    <?php if (!$ajax) {  ?>
        <?php echo EnergyPlus_View::run('core/pagination', array( 'count' => $count, 'per_page'=> absint(EnergyPlus::option('reactors-tweaks-pg-customers', 10)), 'page' => intval(EnergyPlus_Helpers::get('pg', 0)))); ?>
    </div>
  </div>
<?php } ?>
