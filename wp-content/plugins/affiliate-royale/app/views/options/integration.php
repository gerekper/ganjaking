<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-page-title"><?php _e('Payment Integration', 'affiliate-royale', 'easy-affiliate'); ?></div>
<?php
$integrations = array(
  'memberpress' => array(
    'label' => __('MemberPress', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'woocommerce' => array(
    'label' => __('WooCommerce', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'easy_digital_downloads' => array(
    'label' => __('Easy Digital Downloads', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'super_stripe' => array(
    'label' => __('Buy Now for Stripe', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => true,
  ),
  'jigoshop' => array(
    'label' => __('Jigoshop', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'marketpress' => array(
    'label' => __('MarketPress', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'shopp' => array(
    'label' => __('Shopp', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'cart66' => array(
    'label' => __('Cart66', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'ecommerce' => array(
    'label' => __('WP E-Commerce', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
  'authorize' => array(
    'label' => __('Authorize.net ARB', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => true,
  ),
  'paypal' => array(
    'label' => __('PayPal', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => true,
  ),
  'wishlist' => array(
    'label' => __('Wishlist + PayPal', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => true,
  ),
  'diglabs_stripe_payments' => array(
    'label' => __('DigLabs Stripe Payments', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => true,
  ),
  'general' => array(
    'label' => __('Other', 'easy-affiliate', 'affiliate-royale'),
    'deprecated' => false,
  ),
);

$show_deprecated = get_option('wafp_show_deprecated_integrations');
?>

<?php foreach($integrations as $slug => $integration): ?>
  <?php
    if( ( isset($_POST[$wafp_options->integration_str]) &&
          in_array($slug, array_keys($_POST[$wafp_options->integration_str]))
        ) ||
        ( !isset($_POST[$wafp_options->integration_str]) &&
          in_array($slug, $wafp_options->integration)
        ) ) {
      $integration['checked'] = true;
    }
    else {
      $integration['checked'] = false;
    }

    if($integration['deprecated'] && !$show_deprecated && !$integration['checked']) {
      continue; // Skip this integration if it's deprecated and not being used
    }

    $curr_name = "{$wafp_options->integration_str}[{$slug}]";
    $curr_id = "wafp-options-{$slug}";
    $curr_label = $integration['label'];

    $curr_info_file = WAFP_VIEWS_PATH . "/options/{$slug}_integration.php";
    $curr_info_box = "esaf-integration-{$slug}-box";
    $curr_show_info_box = file_exists($curr_info_file);

    $curr_config_file = WAFP_VIEWS_PATH . "/options/{$slug}_config.php";
    $curr_config_box = "esaf-config-{$slug}-box";
    $curr_show_config_box = file_exists(WAFP_VIEWS_PATH . "/options/{$slug}_config.php");
  ?>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo $curr_id; ?>"><?php echo $curr_label; ?></label>
        </th>
        <td>
          <input type="checkbox" name="<?php echo $curr_name; ?>" id="<?php echo $curr_id; ?>" <?php checked($integration['checked']); ?> />
          <?php if($curr_show_info_box): ?>
            <a href="" class="esaf-toggle-link" data-box="<?php echo $curr_info_box; ?>"><?php _e('Instructions', 'easy-affiliate', 'affiliate-royale'); ?></a>
          <?php endif; ?>
          <?php if($curr_show_config_box): ?>
            <a href="" class="esaf-toggle-link" data-box="<?php echo $curr_config_box; ?>"><?php _e('Config', 'easy-affiliate', 'affiliate-royale'); ?></a>
          <?php endif; ?>
        </td>
      </tr>
    </tbody>
  </table>

  <?php if($curr_show_info_box): ?>
    <div class="esaf-sub-box <?php echo $curr_info_box; ?>">
      <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
      <?php require($curr_info_file); ?>
    </div>
  <?php endif; ?>

  <?php if($curr_show_config_box): ?>
    <div class="esaf-sub-box <?php echo $curr_config_box; ?>">
      <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
      <?php require($curr_config_file); ?>
    </div>
  <?php endif; ?>

<?php endforeach; ?>

</div>

