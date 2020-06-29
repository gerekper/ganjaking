<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>


<?php MeprView::render("/checkout/MeprStripeGateway/payment_gateway_fields", get_defined_vars()); ?>
<div class="mp-form-row">
  <div class="mp-form-label">
    <label><?php _e('Name on the card:*', 'memberpress'); ?></label>
    <span class="cc-error"><?php _ex('Name on the card is required.', 'ui', 'memberpress'); ?></span>
  </div>
  <input type="text" name="card-name" class="mepr-form-input stripe-card-name" required />
</div>

<div class="mp-form-row">
  <div class="mp-form-label">
    <label><?php _e('Credit Card:*', 'memberpress'); ?></label>
    <span role="alert" class="mepr-stripe-card-errors"></span>
  </div>
  <div class="mepr-stripe-card-element" data-stripe-public-key="<?php echo esc_attr($payment_method->settings->public_key); ?>" data-payment-method-id="<?php echo esc_attr($payment_method->settings->id); ?>">
    <!-- a Stripe Element will be inserted here. -->
  </div>
</div>

<?php MeprHooks::do_action('mepr-stripe-payment-form', $txn); ?>
<noscript><p class="mepr_nojs"><?php _e('Javascript is disabled in your browser. You will not be able to complete your purchase until you either enable JavaScript in your browser, or switch to a browser that supports it.', 'memberpress'); ?></p></noscript>
