<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of Aweber into Affiliate Royale
*/
class WafpMailChimpController {
  public static function load_hooks() {
    add_action('wafp-set-is-affiliate',   'WafpMailChimpController::maybe_add_remove_affiliate', 10, 2);
    add_action('esaf_marketing_options',  'WafpMailChimpController::display_option_fields');
    add_action('wafp_process_options',    'WafpMailChimpController::store_option_fields');
    add_action('wafp-user-signup-fields', 'WafpMailChimpController::display_signup_field'); //Lol, we're not even using this - oh well
    // add_action('wafp_signup_thankyou_message', 'WafpMailChimpController::thank_you_message'); //WTF is this?
  }

  public static function display_option_fields() {
    $mailchimp_api_key = get_option('wafpmailchimp_api_key', '');
    $mailchimp_list_id = get_option('wafpmailchimp_list_id', '');
    $mailchimp_double_optin = get_option('wafpmailchimp_double_optin', true);
    $mailchimp_text = get_option('wafpmailchimp_text', '');

    ?>
    <div class="esaf-page-title"><?php _e('Mailchimp Signup Integration', 'affiliate-royale', 'easy-affiliate'); ?></div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="wafpmailchimp_api_key"><?php _e('Mailchimp API Key', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-mailchimp-api-key",
                __('Mailchimp API Key', 'easy-affiliate', 'affiliate-royale'),
                __('You can find your API key under your Account settings at Mailchimp.com.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
           <input type="text" name="wafpmailchimp_api_key" id="wafpmailchimp_api_key" value="<?php echo $mailchimp_api_key; ?>" class="wafp-text-input form-field regular-text" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpmailchimp_list_id"><?php _e('Mailchimp List ID', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-mailchimp-api-key",
                __('Mailchimp List ID', 'easy-affiliate', 'affiliate-royale'),
                __('You can find your List ID under your list\'s settings.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
           <input type="text" name="wafpmailchimp_list_id" id="wafpmailchimp_list_id" value="<?php echo $mailchimp_list_id; ?>" class="wafp-text-input form-field regular-text" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpmailchimp_double_optin"><?php _e('Enable Double Opt-in', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-double-optin",
                __('Enable Double Opt-in', 'easy-affiliate', 'affiliate-royale'),
                __('Members will have to click a confirmation link in an email before being added to your list.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
           <input type="checkbox" name="wafpmailchimp_double_optin" id="wafpmailchimp_double_optin" <?php checked($mailchimp_double_optin); ?> class="form-field" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpmailchimp_text"><?php _e('Signup Checkbox Label', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-mailchimp-text",
                __('Signup Checkbox Label', 'easy-affiliate', 'affiliate-royale'),
                __('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="wafpmailchimp_text" id="wafpmailchimp_text" value="<?php echo $mailchimp_text; ?>" class="wafp-text-input form-field large-text" />
          </td>
        </tr>
      </tbody>
    </table>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <?php
  }

  public static function store_option_fields() {
    update_option('wafpmailchimp_api_key', stripslashes($_POST['wafpmailchimp_api_key']));
    update_option('wafpmailchimp_list_id', stripslashes($_POST['wafpmailchimp_list_id']));
    update_option('wafpmailchimp_double_optin', (isset($_POST['wafpmailchimp_double_optin'])));
    update_option('wafpmailchimp_text', stripslashes($_POST['wafpmailchimp_text']));
  }

  public static function display_signup_field() {
    $listname = get_option('wafpmailchimp_list_id');

    if(!empty($listname)) {
      if(isset($_POST['wafpmailchimp_opt_in_set']))
        $checked = isset($_POST['wafpmailchimp_opt_in'])?' checked="checked"':'';
      else
        $checked = ' checked="checked"';

      $message = get_option('wafpmailchimp_text');

      if(!$message or empty($message))
        $message = __('Sign Up for our newsletter', 'affiliate-royale', 'easy-affiliate');

      ?>
      <tr>
        <td valign="top" colspan="2">
          <div class="wafp-mailchimp-signup-field">
            <input type="hidden" name="wafpmailchimp_opt_in_set" value="Y" />
            <div id="wafp-mailchimp-checkbox">
              <input type="checkbox" name="wafpmailchimp_opt_in" id="wafpmailchimp_opt_in" class="wafp-form-checkbox" <?php echo $checked; ?> style="width:auto;" /> <span class="wafp-mailchimp-message"> <?php echo $message; ?></span>
            </div>
            <div id="wafp-mailchimp-privacy"><small><a href="http://mailchimp.com/legal/privacy/" class="wafp-mailchimp-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'affiliate-royale', 'easy-affiliate'); ?></a></small></div>
          </div>
        </td>
      </tr>
      <?php
     }
  }

  public static function maybe_add_remove_affiliate($wafp_user, $is_affiliate) {
    if($is_affiliate) {
      self::maybe_add_member_to_list($wafp_user->get_first_name(), $wafp_user->get_last_name(), $wafp_user->get_field('user_email'));
    }
  }

  public static function maybe_add_member_to_list($first_name, $last_name, $email) {
    $mailchimp_list_id = get_option('wafpmailchimp_list_id', '');
    $mailchimp_double_optin = (int)get_option('wafpmailchimp_double_optin', true);
    $status = $mailchimp_double_optin ? 'pending' : 'subscribed';
    $hashed = self::contact_hash($email);

    $args = array(
      'email_address' => $email,
      'status'        => $status,
      'status_if_new' => $status,
      'merge_fields'  => array(
         'FNAME' => $first_name,
         'LNAME' => $last_name
      )
    );

    self::call("lists/{$mailchimp_list_id}/members/{$hashed}", $args, 'PUT');
  }

  public static function get_datacenter($apikey) {
    $dc = explode('-', $apikey);
    return isset($dc[1]) ? $dc[1] : '';
  }

  public static function call($endpoint, $args = array(), $method = 'GET') {
    $apikey = get_option('wafpmailchimp_api_key', '');
    $dc = self::get_datacenter($apikey);
    $url = "https://{$dc}.api.mailchimp.com/3.0/{$endpoint}";

    $wp_args = array(
      'headers'     =>  array(
                          "Content-Type"  => "application/json",
                          "Authorization" => "Basic " . base64_encode(uniqid().":".$apikey),
                        ),
      'timeout'     => 60,
      'sslverify'   => false,
      'method'      => strtoupper($method),
      'httpversion' => '1.1',
      'body'        => array()
    );

    if(strtoupper($method) == 'GET' || strtoupper($method) == 'DELETE') {
      $url .= '?' . http_build_query($args);
    }
    else {
      $wp_args['body'] = json_encode($args);
    }

    $res = wp_remote_request($url, $wp_args); //Blidly sending these for now
  }

  public static function contact_hash($email) {
    return md5(strtolower($email));
  }
}
