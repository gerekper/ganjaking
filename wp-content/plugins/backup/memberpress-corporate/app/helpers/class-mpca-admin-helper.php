<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/*
 * Instantiate the helper in your controller and call its functions from the
 * view template.
 *
 * Example:
 *
 *   // controller
 *   $helper = new MPCA_Admin_Helper();
 *
 *   // view template
 *   <?php echo $helper->subscription_header_html($p, $s) ?>
 */
class MPCA_Admin_Helper {
  public function subscription_header_html($sub) {
    $html = '';
    $product = $sub->product();
    $sub_id = __('Unknown', 'memberpress-corporate');

    if($sub instanceof MeprTransaction) {
      $sub_id = $sub->trans_num;
    }
    else if($sub instanceof MeprSubscription) {
      $sub_id = $sub->subscr_id;
    }

    $status = $this->subscription_status($sub);
    $html .= "{$product->post_title} (ID: {$sub_id}, Status: {$status})";

    return $html;
  }

  public function member_type_html($options, $selected_value) {
    ob_start();
    ?>
    <select id="mpca-member-type" name="mpca_member_type">

    <?php
    foreach($options as $text => $value):
      ?>
      <option value="<?php echo $value; ?>" <?php selected($selected_value, $value); ?>><?php echo $text; ?></option>
      <?php
    endforeach;
    ?>

    </select>
    <?php

    return ob_get_clean();
  }

  /* Private functions */

  private function subscription_status($sub) {
    return (($sub->is_active()) ? __('Active', 'memberpress-corporate') : __('Inactive', 'memberpress-corporate'));
  }
}
