<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of GetResponse into Affiliate Royale
*/
class WafpGetResponseController {
  public static function load_hooks() {
    add_action('wafp-set-is-affiliate',   'WafpGetResponseController::maybe_add_remove_affiliate', 10, 2);
    add_action('esaf_marketing_options',  'WafpGetResponseController::display_option_fields');
    add_action('wafp_process_options',    'WafpGetResponseController::store_option_fields');
    add_action('wafp-user-signup-fields', 'WafpGetResponseController::display_signup_field'); //Lol, we're not even using this - oh well
    // add_action('wafp_signup_thankyou_message', 'WafpGetResponseController::thank_you_message');
  }

  public static function display_option_fields() {
    $getresponse_api_key = get_option('wafpgetresponse_api_key', '');
    $getresponse_list_id = get_option('wafpgetresponse_list_id', '');
    $getresponse_text = get_option('wafpgetresponse_text', '');

    ?>
    <div class="esaf-page-title"><?php _e('GetResponse Signup Integration', 'affiliate-royale', 'easy-affiliate'); ?></div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="wafpgetresponse_api_key"><?php _e('GetResponse API Key', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-getresponse-api-key",
                __('GetResponse API Key', 'easy-affiliate', 'affiliate-royale'),
                __('You can find your API key under your Account settings at GetResponse.com.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="wafpgetresponse_api_key" id="wafpgetresponse_api_key" value="<?php echo $getresponse_api_key; ?>" class="wafp-text-input form-field regular-text" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpgetresponse_list_id"><?php _e('GetResponse Campaign Token', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-getresponse-list-id",
                __('GetResponse Campaign Token', 'easy-affiliate', 'affiliate-royale'),
                __('You can find your Campaign Token under your Campaign settings at GetResponse.com.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
           <input type="text" name="wafpgetresponse_list_id" id="wafpgetresponse_list_id" value="<?php echo $getresponse_list_id; ?>" class="wafp-text-input form-field regular-text" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpgetresponse_text"><?php _e('Signup Checkbox Label', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-getresponse-text",
                __('Signup Checkbox Label', 'easy-affiliate', 'affiliate-royale'),
                __('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="wafpgetresponse_text" id="wafpgetresponse_text" value="<?php echo $getresponse_text; ?>" class="wafp-text-input form-field large-text" />
          </td>
        </tr>
      </tbody>
    </table>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <?php
  }

  public static function store_option_fields() {
    update_option('wafpgetresponse_api_key', stripslashes($_POST['wafpgetresponse_api_key']));
    update_option('wafpgetresponse_list_id', stripslashes($_POST['wafpgetresponse_list_id']));
    update_option('wafpgetresponse_text', stripslashes($_POST['wafpgetresponse_text']));
  }

  public static function display_signup_field() {
    $listname = get_option('wafpgetresponse_list_id');

    if(!empty($listname)) {
      if(isset($_POST['wafpgetresponse_opt_in_set']))
        $checked = isset($_POST['wafpgetresponse_opt_in'])?' checked="checked"':'';
      else
        $checked = ' checked="checked"';

      $message = get_option('wafpgetresponse_text');

      if(!$message or empty($message))
        $message = __('Sign Up for our newsletter', 'affiliate-royale', 'easy-affiliate');

      ?>
      <tr>
        <td valign="top" colspan="2">
          <div class="wafp-getresponse-signup-field">
            <input type="hidden" name="wafpgetresponse_opt_in_set" value="Y" />
            <div id="wafp-getresponse-checkbox">
              <input type="checkbox" name="wafpgetresponse_opt_in" id="wafpgetresponse_opt_in" class="wafp-form-checkbox" <?php echo $checked; ?> style="width:auto;" /> <span class="wafp-getresponse-message"> <?php echo $message; ?></span>
            </div>
            <div id="wafp-getresponse-privacy"><small><a href="http://www.getresponse.com/legal/privacy.html" class="wafp-getresponse-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'affiliate-royale', 'easy-affiliate'); ?></a></small></div>
          </div>
        </td>
      </tr>
      <?php
     }
  }

  public static function maybe_add_remove_affiliate($wafp_user, $is_affiliate) {
    if($is_affiliate)
      self::maybe_add_member_to_list($wafp_user->get_first_name(), $wafp_user->get_last_name(), $wafp_user->get_field('user_email'));
    // else //Will add this later when we feel like piggy backing off of MemberPress
      // self::maybe_remove_member_from_list($wafp_user->get_field('user_email'));
  }

  public static function maybe_add_member_to_list($first_name, $last_name, $email) {
    $getresponse_list_id = get_option('wafpgetresponse_list_id', '');
    $getresponse_api_key = get_option('wafpgetresponse_api_key', '');
    $apiEndpoint = "http://api2.getresponse.com";

    if(!empty($getresponse_list_id) && !empty($getresponse_api_key)) {
      $params = array();
      $params[] = $getresponse_api_key;
      $params[] = array('campaign'  => $getresponse_list_id,
                        'name'      => "{$first_name} {$last_name}",
                        'email'     => $email,
                        'action'    => 'standard');

      $body = json_encode(array('method' => 'add_contact', 'params' => $params, 'id' => $getresponse_list_id));

      $resp = wp_remote_post($apiEndpoint, array('method' => 'POST', 'body' => $body));
    }
  }

  public static function maybe_remove_member_from_list($email) {
    return;
    //Will support this later when we feel like piggy backing off of MemberPress
  }
}
