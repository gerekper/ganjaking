<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** This is a special controller that handles all of the PayPal specific
  * public static functions for the Affiliate Program.
  */
class WafpPayPalController {
  public static $sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
  public static $live_url = 'https://www.paypal.com/cgi-bin/webscr';

  public static function load_hooks() {
    add_action('wafp_process_route', 'WafpPayPalController::listener');

    // If Wishlist Member is installed, hook into it
    add_action('wlmem_paypal_display_custom_var', 'WafpPayPalController::wl_custom_instructions');
    add_action('wlmem_paypal_ipn_response', 'WafpPayPalController::wl_process_ipn');
  }

  public static function wl_custom_instructions() {
    ?>
      <li><?php _e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'affiliate-royale', 'easy-affiliate'); ?></li>
      <li><?php _e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area:', 'affiliate-royale', 'easy-affiliate'); ?><br/>
      <pre><strong>custom=[wafp_custom_args]</strong></pre>
      </li>
      <li><?php _e('Click "Create Button"', 'affiliate-royale', 'easy-affiliate'); ?></li>
      <li><?php _e('Click "Remove code protection"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <?php
  }

  public static function wl_process_ipn() {
    self::_email_status("Got Wishlist Member IPN:\n" . WafpUtils::array_to_string($_POST, true) . "\n");

    self::_process_message();
  }

  public static function listener() {
    if( isset($_REQUEST['plugin']) and $_REQUEST['plugin'] == 'wafp' and
        isset($_REQUEST['controller']) and $_REQUEST['controller'] == 'paypal' and
        isset($_REQUEST['action']) and $_REQUEST['action'] == 'ipn' ) {
      $_POST = stripslashes_deep($_POST);

      if((self::_valid_ip() or self::_validate_message()) and self::_valid_email()) {
        self::_process_message();
        self::_pass_along_message();
        do_action('wafp_pass_ipn_message');
      }

      exit;
    }
  }

  public static function _process_message() {
    global $wafp_options;

    $subscr = false;

    // We don't necessarily rely on the custom variable being set for subscr_payments --
    // if it isn't set then we'll attempt to pull the affiliate id & remote ip address from
    // the first subscr_payment for this specific subscription...
    if(!isset($_POST['custom']) and isset($_POST['subscr_id']) and !empty($_POST['subscr_id'])) {
      $subscr = WafpSubscription::get_one_by_subscr_id($_POST['subscr_id']);

      if(!$subscr or !$subscr->affiliate_id) {
        return false; // we can't find the affiliate then there's no point to the rest of this function
      }

      $custom_array = array( 'aff_id' => $subscr->affiliate_id,
                             'ip_addr' => $subscr->ip_addr );
    }
    else {
      $custom_array = wp_parse_args($_POST['custom']);
    }

    if(isset($_POST['payment_status']) and isset($custom_array['aff_id'])) {
      if($_POST['payment_status'] == 'Refunded') {
        $og_transaction = WafpTransaction::get_one_by_trans_num( $_POST['parent_txn_id'] );
        WafpTransaction::update_refund( $og_transaction->id, abs($_POST['mc_gross']) );
      }
      else if($_POST['payment_status'] == 'Completed') {
        $existing_transaction = WafpTransaction::get_one_by_trans_num($_POST['txn_id']);

        // If we've already recorded this transaction then don't bother
        if($existing_transaction) { return; }

        $affiliate_id = $custom_array['aff_id'];
        $affiliate = new WafpUser($affiliate_id);
        self::_email_status("Affiliate Info:\nID:{$affiliate_id}" . WafpUtils::array_to_string($affiliate->userdata) . "\n");

        if($affiliate->is_affiliate()) { // is this a valid affiliate?
          $affiliate_login      = $affiliate->get_field('user_login');
          $affiliate_email      = $affiliate->get_field('user_email');
          $affiliate_first_name = $affiliate->get_first_name();
          $affiliate_last_name  = $affiliate->get_last_name();
          $item_name            = $_POST['item_name'];
          $trans_num            = $_POST['txn_id'];
          $trans_type           = $_POST['txn_type'];
          $payment_status       = $_POST['payment_status'];
          $commission_percent   = WafpUtils::format_float($affiliate->get_commission_percentages_total(true));
          $commission_type      = $affiliate->get_commission_type();
          $remote_ip_addr       = isset($custom_array['ip_addr'])?$custom_array['ip_addr']:'';
          $response             = WafpUtils::object_to_string($_POST);
          $payment_amount       = WafpUtils::format_float((float)$_POST['mc_gross']-((float)$_POST['shipping']+(float)$_POST['tax']));
          $commission_amount    = WafpUtils::format_float($affiliate->calculate_commissions_total(($_POST['mc_gross'] - ((float)$_POST['shipping'] + (float)$_POST['tax'])), true, false, $item_name));
          $customer_name        = $_POST['first_name'] . ' ' . $_POST['last_name'];
          $customer_email       = $_POST['payer_email'];
          $subscription_id      = isset($_POST['subscr_id'])?$_POST['subscr_id']:NULL;
          $transaction_type     = (isset($_POST['subscr_id']) and !empty($_POST['subscr_id']))?__("Subscription Payment", 'affiliate-royale', 'easy-affiliate'):__("Standard Payment", 'affiliate-royale', 'easy-affiliate');

          // Create a subscription if it's set
          if(!empty($affiliate_id) and $affiliate_id and !empty($subscription_id) and $subscription_id) {
            if(!( $wafp_subscr = WafpSubscription::subscription_exists($subscription_id))) {
              $wafp_subscr_id = WafpSubscription::create( $subscription_id, 'paypal', $affiliate_id, $item_name, $remote_ip_addr );
            }
            else {
              $wafp_subscr_id = $wafp_subscr->subscription->ID;
            }
          }

          if(!is_null($subscription_id) and !empty($subscription_id) and $subscription_id) {
            $subscription_already_paid = WafpTransaction::get_one_by_subscription_id($subscription_id);
            $pay_affiliate = $affiliate->pay_commission($subscription_already_paid);
          }
          else
            $pay_affiliate = true;

          $wafp_subscr_paynum = ($wafp_subscr_id?(WafpTransaction::get_num_trans_by_subscr_id($wafp_subscr_id) + 1):0);

          if($pay_affiliate) {
            WafpTransaction::create( $item_name, $payment_amount, $commission_amount,
                                     $trans_num, 'commission', $payment_status,
                                     $response, $affiliate_id, $customer_name,
                                     $customer_email, $remote_ip_addr, $commission_percent,
                                     $wafp_subscr_id, $wafp_subscr_paynum, $commission_type );

            // Handled in WafpTransaction::create now
            //$params = compact( 'affiliate_id', 'affiliate_login', 'affiliate_email',
            //                   'affiliate_first_name', 'affiliate_last_name', 'item_name',
            //                   'trans_num', 'trans_type', 'payment_status',
            //                   'commission_percent', 'remote_ip_addr', 'response',
            //                   'payment_amount', 'commission_amount', 'customer_name',
            //                   'customer_email', 'subscription_id', 'transaction_type',
            //                   'wafp_subscr_id', 'wafp_subscr_paynum' );
            //
            //WafpUtils::send_admin_sale_notification($params);
            //WafpUtils::send_affiliate_sale_notification($params);
          }
          else {
            WafpTransaction::create( $item_name, $payment_amount, '0.00',
                                     $trans_num, 'no_commission', $payment_status,
                                     $response, '', $customer_name,
                                     $customer_email, $remote_ip_addr, '',
                                     '' );
          }
        }
      }
    }
  }

  /**
   * Pass along the IPN message if there are more destinations
   */
  public static function _pass_along_message() {
    global $wafp_options;
    if(empty($wafp_options->paypal_dst)) { return; }

    if(!function_exists('wp_remote_post')) { require_once('http.php'); }

    $params = array(
      'body'    => $_POST,
      'sslverify' => false,
      'timeout'   => 30,
    );

    $urls = array_map('trim',explode("\n", $wafp_options->paypal_dst));
    foreach ($urls as $url) {
      wp_remote_post($url, $params);
    }
  }

  /**
   * Validate the message by checking with PayPal to make sure they really
   * sent it
   */
  public static function _validate_message() {
    global $wafp_options;

    // Set the command that is used to validate the message
    $_POST['cmd'] = "_notify-validate";

    // We need to send the message back to PayPal just as we received it
    $params = array(
      'method'      => 'POST',
      'body'        => $_POST,
      'headers'     => array('connection' => 'close'),
      'httpversion' => 1.1,
      'sslverify'   => true,
      'user-agent'  => 'AffiliateRoyale/'.WAFP_VERSION,
      'timeout'     => 30
    );

    if(!function_exists('wp_remote_post')) { require_once('http.php'); }

    $url = $wafp_options->paypal_sandbox?self::$sandbox_url:self::$live_url;

    $resp = wp_remote_post($url, $params);

    self::_email_status("PayPal IPN Server\n" . WafpUtils::array_to_string($_SERVER, true) . "\n");
    self::_email_status("PayPal IPN Parameters\n" . WafpUtils::array_to_string($params, true) . "\n");
    self::_email_status("PayPal IPN Response\n" . WafpUtils::array_to_string($resp, true) . "\n");

    // Put the $_POST data back to how it was so we can pass it to the action
    unset($_POST['cmd']);

    // If the response was valid, check to see if the request was valid
    if( !is_wp_error($resp) and
        $resp['response']['code'] >= 200 and
        $resp['response']['code'] < 300 and
        (strcmp( $resp['body'], "VERIFIED") == 0) ) {
      return true;
    }

    self::_email_status("PayPal IPN Processing\n" . WafpUtils::array_to_string($_POST, true) . "\n");

    return false;
  }

  /**
   * Validate REMOTE_ADDR
   */
  public static function _valid_ip() {
    global $wafp_options;

    if(empty($wafp_options->paypal_src)) { return false; }

    $ips = array_map('trim', explode(',', $wafp_options->paypal_src));
    $ip_valid = in_array($_SERVER['REMOTE_ADDR'], $ips);

    self::_email_status("IPs\n" . WafpUtils::object_to_string($ips, true) . "\nREMOTE IP\n" . $_SERVER['REMOTE_ADDR'] . "\nREMOTE IP IN ARRAY\n" . ($ip_valid?"YES":"NO"));

    return $ip_valid;
  }

  public static function _valid_email() {
    global $wafp_options;

    if(empty($wafp_options->paypal_emails)) { return true; }

    $emails = array_map('trim', explode( ',', $wafp_options->paypal_emails ));
    $email_valid = (in_array( $_REQUEST['receiver_email'], $emails ) or in_array( $_REQUEST['business'], $emails ));
    self::_email_status("Emails\n" . WafpUtils::object_to_string($emails, true) . "\n");

    return $email_valid;
  }

  public static function _email_status($message) {
    global $wafp_blogname;

    $debug = get_option('wafp-paypal-debug');

    if($debug) {
      // Send notification email to admin user (to and from the admin user)
      $recipient = get_option('admin_email'); //senders name
      $header    = "From: {$recipient}"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject       = sprintf( __("[%s] PayPal Debug Email", 'affiliate-royale', 'easy-affiliate'), $wafp_blogname);

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }
  }
}
