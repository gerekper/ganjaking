<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>

<?php echo EnergyPlus_View::run('header-in'); ?>

<div class="energyplus-title inbrowser">
  <h3><?php echo esc_html(sprintf('%s %s', esc_html($customer['first_name']), esc_html($customer['last_name'])));?></h3>
</div>

<div id="energyplus-customers--detail">
  <div class="container">
    <div class="row">
      <label><?php esc_html_e('E-Mail', 'energyplus'); ?> </label>
      <?php echo esc_html( $customer['email']) ?>
    </div>

    <div class="row">
      <label><?php esc_html_e('Last Order', 'energyplus'); ?> </label>
      <?php if (!empty($customer['last_order_id'])) { ?>
        <a class="trig" href="<?php echo admin_url( 'post.php?post=' . intval($customer['last_order_id']). '&action=edit&energyplus_hide' );?>">#<?php echo esc_html($customer['last_order_id']) ?> - <?php echo date("d/m/Y", strtotime( esc_attr($customer['last_order_date']) ) ) ?></a>
      <?php } else { ?>
        -
      <?php } ?>
    </div>

    <div class="row">
      <label><?php esc_html_e('Order Counts', 'energyplus'); ?> </label>
      <?php echo esc_html($customer['orders_count']) ?>
    </div>

    <div class="row">
      <label><?php esc_html_e('Total Spent', 'energyplus'); ?> </label>
      <?php echo wc_price(esc_html($customer['total_spent']))  ?>
    </div>

    <div class="row">
      <label><?php esc_html_e('Join Date', 'energyplus'); ?> </label>
      <?php echo EnergyPlus_Helpers::strtotime(esc_attr($customer['created_at']), "d F Y" ) ?>
    </div>
    <div class="row">
      <label><?php esc_html_e('Last Seen', 'energyplus'); ?> </label>
      <?php if (isset($meta['wc_last_active'][0])) {
        echo date("d F Y",  esc_attr($meta['wc_last_active'][0])  );
      } else {
        echo "-";
      } ?>
    </div>
  </div>
</div>
<div id="energyplus-customers--address">
  <h5><?php esc_html_e('Billing Address', 'energyplus'); ?>
    <a href="javascript:;" class="__A__Edit_Text"><?php esc_html_e('Edit', 'energyplus'); ?></a>
    <a href="javascript:;" class="__A__Edit_Save d-none" data-id="<?php echo esc_attr($customer['id']) ?>"><?php esc_html_e('Save', 'energyplus'); ?></a>
  </h5>
  <form action="" method="POST" id="energyplus-details--form">
    <div class="row">
      <div class="col-6">
        <div class="__A__I">
          <strong><?php esc_html_e('First Name', 'energyplus'); ?></strong>
          <span data-name="billing_first_name" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_first_name'][0],'') ?></span><br />
        </div>
        <div class="__A__I">
          <strong><?php esc_html_e('Last name', 'energyplus'); ?></strong>
          <span data-name="billing_last_name" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_last_name'][0],'') ?></span><br />
        </div>
        <div class="__A__I">
          <strong><?php esc_html_e('Company', 'energyplus'); ?></strong>
          <span data-name="billing_company" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_company'][0],'') ?></span><br />
        </div>

        <div class="__A__I">
          <strong><?php esc_html_e('E-Mail', 'energyplus'); ?></strong>
          <span data-name="billing_email" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_email'][0],'') ?></span><br />
        </div>
        <div class="__A__I">
          <strong><?php esc_html_e('Phone', 'energyplus'); ?></strong>
          <span data-name="billing_phone" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_phone'][0],'') ?></span><br />

        </div>

      </div>
      <div class="col-6">
        <div class="__A__I">
          <strong><?php esc_html_e('Country', 'energyplus'); ?></strong>
          <span data-name="billing_country" class="__A__Editable_C">
            <span class="__A__H"><?php echo EnergyPlus_Helpers::clean($meta['billing_country'][0],'') ?></span>
            <span class="__A__S __A__Display_None"> <?php woocommerce_form_field('billing_country', array(
              'type'        => 'country',
              'class'       => array( '' ),
              'label'       => '',
              'placeholder' => esc_html__('Select a Country', 'energyplus')
            ),EnergyPlus_Helpers::clean($meta['billing_country'][0],'')

          );
          ?></span>
        </span>
      </div>
      <div class="__A__I">
        <strong><?php esc_html_e('State', 'energyplus'); ?></strong>
        <span data-name="billing_state" class="__A__Editable_C">
          <span class="__A__H"><?php echo EnergyPlus_Helpers::clean($meta['billing_state'][0] ,'')?></span>
          <span class="__A__S __A__Display_None"> <?php woocommerce_form_field('billing_state', array(
            'type'        => 'state',
            'country'     => $meta['billing_country'][0],
            'class'       => array( '' ),
            'label'       => '',
            'placeholder' => esc_attr__('Select a state', 'energyplus')
          ),EnergyPlus_Helpers::clean($meta['billing_state'][0],'')

        );
        ?></span>
      </span>
    </div>
    <div class="__A__I">
      <strong><?php esc_html_e('Address', 'energyplus'); ?> 1</strong>
      <span data-name="billing_address_1" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_address_1'][0],'') ?></span><br />
    </div>
    <div class="__A__I">
      <strong><?php esc_html_e('Address', 'energyplus'); ?> 2</strong>
      <span data-name="billing_address_2" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_address_2'][0],'') ?></span><br />
    </div>
    <div class="__A__I">
      <strong><?php esc_html_e('City', 'energyplus'); ?></strong>
      <span data-name="billing_city" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_city'][0],'') ?></span><br />
    </div>
    <div class="__A__I">
      <strong><?php esc_html_e('Post Code', 'energyplus'); ?></strong>
      <span data-name="billing_postcode" class="__A__Editable"><?php echo EnergyPlus_Helpers::clean($meta['billing_postcode'][0],'') ?></span><br />
    </div>
  </div>
</form>
<br />
</div>
</div>
<div id="energyplus-customers--orders">
  <h5><?php esc_html_e('Orders', 'energyplus'); ?></h5>
  <?php echo wp_kses_post($orders); ?>
</div>
</div>
