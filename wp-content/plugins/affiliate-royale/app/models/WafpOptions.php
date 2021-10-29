<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpOptions
{
  public static function fetch() {
    $wafp_options = get_option('wafp_options');

    if($wafp_options) {
      if(is_string($wafp_options))
        $wafp_options = unserialize($wafp_options);

      if(is_object($wafp_options) and is_a($wafp_options,'WafpOptions')) {
        $wafp_options->set_default_options();
        $wafp_options->store(); // store will convert this back into an array
      }
      else if(is_array($wafp_options))
        $wafp_options = new WafpOptions($wafp_options);
      else
        $wafp_options = new WafpOptions();
    }
    else
      $wafp_options = new WafpOptions();

    return $wafp_options;
  }

  // License Key
  public $mothership_license;
  public $mothership_license_str;
  public $edge_updates;
  public $edge_updates_str;

  // Page Setup Variables
  public $dashboard_page_id;
  public $dashboard_page_id_str;
  public $signup_page_id;
  public $signup_page_id_str;
  public $login_page_id;
  public $login_page_id_str;

  //Affiliate Settings
  public $make_new_users_affiliates;
  public $make_new_users_affiliates_str;
  public $show_address_fields;
  public $show_address_fields_str;
  public $show_tax_id_fields;
  public $show_tax_id_fields_str;
  public $force_account_info_str;
  public $pretty_affiliate_links;
  public $pretty_affiliate_links_str;

  // Commission Settings
  public $commission_type;
  public $commission_type_str;
  public $commission;
  public $commission_str;

  public $recurring;
  public $recurring_str;

  public $minimum;
  public $minimum_str;

  // Integration Settings
  public $integration;
  public $integration_str;

  // Paypal IPN Options
  public $paypal_sandbox;
  public $paypal_sandbox_str;
  public $paypal_emails;
  public $paypal_emails_str;
  public $paypal_src;
  public $paypal_src_str;
  public $paypal_dst;
  public $paypal_dst_str;

  // Authorize Options
  public $arb_post_process;
  public $arb_post_process_str;
  public $arb_debug;
  public $arb_debug_str;

  // Payment Settings
  public $payment_type;
  public $payment_type_str;

  // Dashboard CSS Settings
  public $dash_css_width;
  public $dash_css_width_str;

  public $dash_show_genealogy;
  public $dash_show_genealogy_str;

  public $dash_nav;
  public $dash_nav_str;

  // Cookie Settings
  public $expire_after_days;
  public $expire_after_days_str;

  // International Settings
  public $currency_code;
  public $currency_code_str;
  public $currency_symbol;
  public $currency_symbol_str;
  public $number_format;
  public $number_format_str;

  // Notification Settings
  public $welcome_email;
  public $welcome_email_str;
  public $welcome_email_subject;
  public $welcome_email_subject_str;
  public $welcome_email_body;
  public $welcome_email_body_str;
  public $admin_email;
  public $admin_email_str;
  public $admin_email_subject;
  public $admin_email_subject_str;
  public $admin_email_body;
  public $admin_email_body_str;
  public $affiliate_email;
  public $affiliate_email_str;
  public $affiliate_email_subject;
  public $affiliate_email_subject_str;
  public $affiliate_email_body;
  public $affiliate_email_body_str;
  public $affiliate_agreement_enabled;
  public $affiliate_agreement_enabled_str;
  public $affiliate_agreement_text;
  public $affiliate_agreement_text_str;

  public $custom_message;
  public $custom_message_str;

  // Is the setup sufficiently completed for affiliate program to function?
  public $setup_complete;

  public function __construct($options_array=array())
  {
    // Set values from array
    foreach($options_array as $key => $value)
      $this->{$key} = $value;

    $this->set_default_options();
  }

  public function set_default_options()
  {
    global $wafp_blogname;

    if(!isset($this->mothership_license))
      $this->mothership_license = '';

    if(!isset($this->edge_updates))
      $this->edge_updates = false;

    if(!isset($this->dashboard_page_id))
      $this->dashboard_page_id = 0;

    if(!isset($this->signup_page_id) or empty($this->signup_page_id))
      $this->signup_page_id = 0;

    if(!isset($this->login_page_id) or empty($this->login_page_id))
      $this->login_page_id = 0;

    if(!isset($this->welcome_email))
      $this->welcome_email = 1;

    if(!isset($this->welcome_email_subject))
      $this->welcome_email_subject = __("Welcome to the Affiliate Program on {\$site_name}!",'affiliate-royale', 'easy-affiliate');

    if(!isset($this->welcome_email_body))
      $this->welcome_email_body = <<<WELCOME_EMAIL
{\$affiliate_first_name},

Welcome to the Affiliate Program on {\$site_name}!

Username: {\$affiliate_login}
Password: {\$affiliate_password}

You can login here: {\$login_url}

Enjoy!

The {\$site_name} Team
WELCOME_EMAIL;

    if(!isset($this->admin_email))
      $this->admin_email = 1;

    if(!isset($this->admin_email_subject))
      $this->admin_email_subject = __('** Affiliate Sale', 'affiliate-royale', 'easy-affiliate');

    if(!isset($this->admin_email_body))
      $this->admin_email_body = <<<ADMIN_EMAIL
Dear admin,

New sale has been made with an affiliate link.
You may find sale details below:

----
Affilate: {\$affiliate_id} / {\$affiliate_login} / {\$affiliate_email}
          {\$affiliate_first_name} {\$affiliate_last_name}

Transaction Type: {\$transaction_type}
Customer Name: {\$customer_name}
Customer Email: {\$customer_email}
Product: {\$item_name}
Transaction #: {\$trans_num}
Remote IP Address: {\$remote_ip_addr}
Total:       {\$payment_amount}
Commission paid: {\$commission_amount}
----
ADMIN_EMAIL;

    if(!isset($this->affiliate_email))
      $this->affiliate_email = 1;

    if(!isset($this->affiliate_email_subject))
      $this->affiliate_email_subject = __('** Affiliate Commission', 'affiliate-royale', 'easy-affiliate');

    if(!isset($this->affiliate_email_body))
      $this->affiliate_email_body = <<<AFFILIATE_EMAIL
Dear {\$affiliate_first_name},

New sale has been made with your affiliate link and
commission credited to your balance. You can see the
sale details below:

----
Transaction Type: {\$transaction_type}
Customer Name: {\$customer_name}
Product: {\$item_name}
Transaction #: {\$trans_num}
Total: {\$payment_amount}
Your commission: {\$commission_amount}
----
AFFILIATE_EMAIL;

    if(!isset($this->affiliate_agreement_enabled))
      $this->affiliate_agreement_enabled = 0;

    if(!isset($this->affiliate_agreement_text))
      $this->affiliate_agreement_text = '';

    // Affiliate Settings
    if(!isset($this->make_new_users_affiliates))
      $this->make_new_users_affiliates = 0;

    $this->make_new_users_affiliates_str     = 'wafp-make-new-users-affiliates';

    if(!isset($this->show_address_fields))
      $this->show_address_fields = 0;

    $this->show_address_fields_str     = 'wafp-show-address-fields';

    if(!isset($this->show_tax_id_fields))
      $this->show_tax_id_fields = 0;

     $this->show_tax_id_fields_str    = 'wafp-show-tax-id-fields';

    if(!isset($this->force_account_info))
      $this->force_account_info = 0;

     $this->force_account_info_str    = 'wafp-force-account-form';

    if(!isset($this->pretty_affiliate_links))
      $this->pretty_affiliate_links = 0;

    $this->pretty_affiliate_links_str = 'wafp-pretty-affiliate-links';

    if(!isset($this->commission_type))
      $this->commission_type = 'percentage';

    if(!isset($this->commission))
      $this->commission = array(0);
    else if(is_numeric($this->commission))
      $this->commission = array($this->commission);

    if(!isset($this->recurring))
      $this->recurring = true;

    if(!isset($this->minimum))
      $this->minimum = '0.00';

    $this->mothership_license_str= 'wafp-mothership-license';
    $this->edge_updates_str      = 'wafp-edge-updates';

    $this->dashboard_page_id_str = 'wafp-dashboard-page-id';
    $this->signup_page_id_str    = 'wafp-signup-page-id';
    $this->login_page_id_str     = 'wafp-login-page-id';

    $this->commission_type_str   = 'wafp-commission-type';
    $this->commission_str        = 'wafp-commission';
    $this->recurring_str         = 'wafp_recurring';
    $this->minimum_str           = 'wafp_minimum';

    // Payment Settings
    if(!isset($this->payment_type))
      $this->payment_type = 'paypal';

    $this->payment_type_str = 'wafp-payment-type';

    //Dash CSS Settings
    if(!isset($this->dash_css_width))
      $this->dash_css_width = 500;
    $this->dash_css_width_str = 'wafp-dash-css-width';

    if(!isset($this->dash_show_genealogy))
      $this->dash_show_genealogy = false;
    $this->dash_show_genealogy_str = 'wafp-dash-show-genealogy';

    if(!isset($this->dash_nav))
      $this->dash_nav = array();
    $this->dash_nav_str = 'wafp-dash-nav';

    // Cookie Settings
    if(!isset($this->expire_after_days))
      $this->expire_after_days = 60;

    $this->expire_after_days_str = 'wafp-expire-after-days';

    // Notification Settings
    $this->welcome_email_str            = 'wafp-welcome-email';
    $this->welcome_email_subject_str    = 'wafp-welcome-email-subject';
    $this->welcome_email_body_str       = 'wafp-welcome-email-body';
    $this->admin_email_str              = 'wafp-admin-email';
    $this->admin_email_subject_str      = 'wafp-admin-email-subject';
    $this->admin_email_body_str         = 'wafp-admin-email-body';
    $this->affiliate_email_str          = 'wafp-affiliate-email';
    $this->affiliate_email_subject_str  = 'wafp-affiliate-email-subject';
    $this->affiliate_email_body_str     = 'wafp-affiliate-email-body';
    $this->affiliate_agreement_enabled_str  = 'wafp-affiliate-agreement-enabled';
    $this->affiliate_agreement_text_str = 'wafp-affiliate-agreement-text';

    if(!isset($this->custom_message))
      $this->custom_message = sprintf(__('Welcome to %s\'s Affiliate Program.', 'affiliate-royale', 'easy-affiliate'), $wafp_blogname);
    $this->custom_message_str = 'wafp-custom-message';

    if(!isset($this->setup_complete))
      $this->setup_complete = ($this->dashboard_page_id)?0:1;

    $this->currency_code_str   = 'wafp_currency_code';
    $this->currency_symbol_str = 'wafp_currency_symbol';
    $this->number_format_str   = 'wafp_number_format';

    if( !isset($this->currency_code))
      $this->currency_code = 'USD';
    if( !isset($this->currency_symbol))
      $this->currency_symbol = '$';
    if( !isset($this->number_format))
      $this->number_format = '#,###.##';

    if(!isset($this->integration))
      $this->integration = array();
    else
      $this->integration = is_array($this->integration)?$this->integration:array($this->integration);

    $this->integration_str = 'wafp-integration-type';

    $this->paypal_src_str = 'wafp-paypal-ipn-source';
    if(!isset($this->paypal_src))
      $this->paypal_src = '';

    $this->paypal_dst_str = 'wafp-paypal-ipn-destination';
    if(!isset($this->paypal_dst))
      $this->paypal_dst = '';

    $this->paypal_sandbox_str = 'wafp-paypal-sandbox';
    if(!isset($this->paypal_sandbox))
      $this->paypal_sandbox = false;

    $this->paypal_emails_str = 'wafp-paypal-emails';
    if(!isset($this->paypal_emails))
      $this->paypal_emails = '';

    $this->arb_post_process_str = 'arb_post_process';
    if (!isset($this->arb_post_process))
      $this->arb_post_process = false;

    $this->arb_debug_str = 'arb_debug';
    if (!isset($this->arb_debug))
      $this->arb_debug = false;

    if(!isset($this->default_link_id))
      $this->default_link_id = 0;

    if(!isset($this->custom_default_redirect))
      $this->custom_default_redirect = false;

    if(!isset($this->custom_default_redirect_url))
      $this->custom_default_redirect_url = '';
  }

  public function validate($params,$errors)
  {
    /* We now auto create a page if one isn't selected
    if($params[ $this->dashboard_page_id_str ] == 0)
      $errors[] = __("The Affiliate Dashboard Page Must Not Be Blank.", 'affiliate-royale');

    if($params[ $this->signup_page_id_str ] == 0)
      $errors[] = __("The Affiliate Signup Page Must Not Be Blank.", 'affiliate-royale');

    if($params[ $this->login_page_id_str ] == 0)
      $errors[] = __("The Affiliate Login Page Must Not Be Blank.", 'affiliate-royale');
    */

    if( empty($params[ $this->integration_str ]) )
      $errors[] = __("Your Payment Integration Must Not Be Blank.", 'affiliate-royale', 'easy-affiliate');

    if( empty($params[$this->commission_str]) )
      $errors[] = __("The Commission Amount Must Not Be Blank.", 'affiliate-royale', 'easy-affiliate');

    foreach($params[$this->commission_str] as $index => $commish)
    {
      $level = $index + 1;
      if( !is_numeric($commish) )
        $errors[] = sprintf(__("The commission amount in level %d must be a number.", 'affiliate-royale', 'easy-affiliate'), $level);
      else if( ( (int)$commish > 100 or (int)$commish < 0 ) && $params[$this->commission_type_str] == 'percentage' )
        $errors[] = sprintf(__("The commission amount in level %d is a percentage so it must be a number from 0 to 100.", 'affiliate-royale', 'easy-affiliate'), $level);
    }

/*
    if( !isset($params[$this->dash_css_width_str]) or empty($params[$this->dash_css_width_str]) )
      $errors[] = __("Your Dashboard Width Must be Set. A sensible default is 500px.", 'affiliate-royale');
    else if( !is_numeric($params[$this->dash_css_width_str]) )
      $errors[] = __("Your Dashboard Width Must be A Number.", 'affiliate-royale');
*/

    if ( !empty( $params[$this->paypal_emails_str] ) )
    {
      $paypal_emails = explode(',', $params[$this->paypal_emails_str]);
      $email_pattern = "/^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4})$/";
      foreach($paypal_emails as $paypal_email)
      {
        if(!preg_match($email_pattern, trim($paypal_email))) {
          $errors[] = __( "One or more of your paypal email addresses is not a valid email.", 'affiliate-royale' , 'easy-affiliate');
          break;
        }
      }
    }

    // Validate urls in PayPal IPN urls
    if ( !empty( $params[$this->paypal_src_str] ))
    {
      $ip_pattern = "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
      $ips = explode( ',', $params[$this->paypal_src_str] );
      foreach ( $ips as $ip )
      {
        if ( !preg_match( $ip_pattern, trim($ip) ))
        {
          $errors[] = __( "One or more of the PayPal source IPN source is not a valid ip address.", 'affiliate-royale' , 'easy-affiliate');
          break;
        }
      }
    }
    if ( !empty( $params[$this->paypal_dst_str] ))
    {
      $url_pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
      $urls = explode( "\n", $params[$this->paypal_dst_str] );
      foreach ( $urls as $url )
      {
        if ( !preg_match( $url_pattern, trim($url) ))
        {
          $errors[] = __( "One or more of the PayPal destination IPN URLs is not a valid URL.", 'affiliate-royale' , 'easy-affiliate');
          break;
        }
      }
    }

    return $errors;
  }

  public function update(&$params)
  {
    //mothership_license and edge_updates are set in the update controller, don't do it here

    // Page Settings
    if( !is_numeric($params[$this->dashboard_page_id_str]) and
        preg_match("#^__auto_page:(.*?)$#",$params[$this->dashboard_page_id_str],$matches) )
      $this->dashboard_page_id = $params[$this->dashboard_page_id_str] = $this->auto_add_page($matches[1]);
    else
      $this->dashboard_page_id = (int)$params[$this->dashboard_page_id_str];

    if( !is_numeric($params[$this->signup_page_id_str]) and
        preg_match("#^__auto_page:(.*?)$#",$params[$this->signup_page_id_str],$matches) )
      $this->signup_page_id = $params[$this->signup_page_id_str] = $this->auto_add_page($matches[1]);
    else
      $this->signup_page_id = (int)$params[$this->signup_page_id_str];

    if( !is_numeric($params[$this->login_page_id_str]) and
        preg_match("#^__auto_page:(.*?)$#",$params[$this->login_page_id_str],$matches) )
      $this->login_page_id = $params[$this->login_page_id_str] = $this->auto_add_page($matches[1]);
    else
      $this->login_page_id = (int)$params[$this->login_page_id_str];

    // Notification Settings
    $this->welcome_email                = isset($params[$this->welcome_email_str]);
    $this->welcome_email_subject        = stripslashes($params[$this->welcome_email_subject_str]);
    $this->welcome_email_body           = stripslashes($params[$this->welcome_email_body_str]);
    $this->admin_email                  = isset($params[$this->admin_email_str]);
    $this->admin_email_subject          = stripslashes($params[$this->admin_email_subject_str]);
    $this->admin_email_body             = stripslashes($params[$this->admin_email_body_str]);
    $this->affiliate_email              = isset($params[$this->affiliate_email_str]);
    $this->affiliate_email_subject      = stripslashes($params[$this->affiliate_email_subject_str]);
    $this->affiliate_email_body         = stripslashes($params[$this->affiliate_email_body_str]);
    $this->affiliate_agreement_enabled  = isset($params[$this->affiliate_agreement_enabled_str]);
    $this->affiliate_agreement_text     = stripslashes($params[$this->affiliate_agreement_text_str]);

    $this->make_new_users_affiliates    = isset($params[$this->make_new_users_affiliates_str]);
    $this->show_address_fields          = isset($params[$this->show_address_fields_str]);
    $this->show_tax_id_fields           = isset($params[$this->show_tax_id_fields_str]);
    $this->force_account_info           = isset($params[$this->force_account_info_str]);
    $this->pretty_affiliate_links       = isset($params[$this->pretty_affiliate_links_str]);

    $this->commission_type              = $params[$this->commission_type_str];
    $this->commission                   = $params[$this->commission_str];
    $this->recurring                    = isset($params[$this->recurring_str]);

    if(isset($params[$this->minimum_str.'-checkbox'])) {
      $this->minimum = stripslashes($params[$this->minimum_str]);
    }
    else {
      $this->minimum = 0.00; // Reset if checkbox is un-checked
    }

    $this->payment_type                 = stripslashes($params[$this->payment_type_str]);
    $this->expire_after_days            = stripslashes($params[$this->expire_after_days_str]);

    //$this->dash_css_width               = stripslashes($params[$this->dash_css_width_str]);
    $this->dash_show_genealogy          = isset($params[$this->dash_show_genealogy_str]);
    $this->dash_nav                     = isset($params[$this->dash_nav_str]) ? $params[$this->dash_nav_str] : array();

    $this->integration                  = is_null($params[$this->integration_str]) ? array() : array_keys($params[$this->integration_str]);

    if(empty($this->minimum)) { $this->minimum = '0.00'; }
    if(!isset($this->recurring)) { $this->recurring = 1; }

    $this->custom_message               = stripslashes($params[$this->custom_message_str]);

    $this->currency_code                = stripslashes($params[$this->currency_code_str]);
    $this->currency_symbol              = stripslashes($params[$this->currency_symbol_str]);
    $this->number_format                = stripslashes($params[$this->number_format_str]);

    $this->paypal_src                   = isset($params[$this->paypal_src_str]) ? $params[$this->paypal_src_str] : '';
    $this->paypal_dst                   = isset($params[$this->paypal_dst_str]) ? $params[$this->paypal_dst_str] : '';
    $this->paypal_emails                = isset($params[$this->paypal_emails_str]) ? $params[$this->paypal_emails_str] : '';
    $this->paypal_sandbox               = isset($params[$this->paypal_sandbox_str]);

    $this->arb_post_process             = isset($params[$this->arb_post_process_str]);
    $this->arb_debug                    = isset($params[$this->arb_debug_str]);

    if($this->dashboard_page_id)
      $this->setup_complete = true;
  }

  public function store() {
    $storage_array = (array)$this;
    update_option( 'wafp_options', $storage_array );
  }

  public function affiliate_page_url( $args )
  {
    $url = WafpUtils::dashboard_url();
    $delimiter = WafpAppController::get_param_delimiter_char( $url );
    return $url . $delimiter . $args;
  }

  public function transaction_tracking_url( $amount='', $order_id='', $prod_id='', $aff_id='', $subscr_id='', $use_params=false, $idev_compatible=false )
  {
    $delimiter = WafpAppController::get_param_delimiter_char( WAFP_SCRIPT_URL );

    if($use_params)
    {
      $amount    = urlencode(empty($amount)?'':$_REQUEST[$amount]);
      $order_id  = urlencode(empty($prod_id)?'':$_REQUEST[$order_id]);
      $prod_id   = urlencode(empty($prod_id)?'':$_REQUEST[$prod_id]);
      $aff_id    = urlencode(empty($aff_id)?'':$_REQUEST[$aff_id]);
      $subscr_id = urlencode(empty($aff_id)?'':$_REQUEST[$subscr_id]);
    }
    else
    {
      $amount    = urlencode(empty($amount)?'':$amount);
      $order_id  = urlencode(empty($prod_id)?'':$order_id);
      $prod_id   = urlencode(empty($prod_id)?'':$prod_id);
      $aff_id    = urlencode(empty($aff_id)?'':$aff_id);
      $subscr_id = urlencode(empty($aff_id)?'':$aff_id);
    }

    if($idev_compatible)
      $args = "controller=transactions&action=track&prod_id=Cart66&idev_saleamt={$amount}&idev_ordernum={$order_id}";
    else
      $args = "controller=transactions&action=track&amount={$amount}&order_id={$order_id}&prod_id={$prod_id}&aff_id={$aff_id}&subscr_id={$subscr_id}";

    return WAFP_SCRIPT_URL . $delimiter . $args;
  }

  public function transaction_tracking_code( $html_entities=true, $amount='', $order_id='', $prod_id='', $aff_id='', $subscr_id='', $use_params=true, $idev_compatible=false )
  {
    $lt = $html_entities?"&lt;":"<";
    $gt = $html_entities?"&gt;":">";
    return "{$lt}img src=\"" . $this->transaction_tracking_url($amount, $order_id, $prod_id, $aff_id, $subscr_id, $use_params, $idev_compatible) . "\" width=\"1px\" height=\"1px\" style=\"display: none;\" /{$gt}";
  }

  public function auto_add_page($page_name)
  {
    return wp_insert_post(array('post_title' => $page_name, 'post_type' => 'page', 'post_status' => 'publish', 'comment_status' => 'closed'));
  }
}
