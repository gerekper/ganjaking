<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of Aweber into Affiliate Royale
*/
  class WafpAweberController
  {
    public static function load_hooks()
    {
      add_action('esaf_marketing_options',  'WafpAweberController::display_option_fields');
      add_action('wafp_process_options',    'WafpAweberController::store_option_fields');
      add_action('wafp-user-signup-fields', 'WafpAweberController::display_signup_field');
      add_action('wafp_signup_thankyou_message', 'WafpAweberController::thank_you_message');
      add_action('wp_enqueue_scripts', 'WafpAweberController::load_scripts');
    }

    public static function display_option_fields()
    {
      if(isset($_POST['wafpaweber_listname']) and !empty($_POST['wafpaweber_listname']))
        $aweber_listname = $_POST['wafpaweber_listname'];
      else
        $aweber_listname = get_option('wafpaweber_listname');

      if(isset($_POST['wafpaweber_text']) and !empty($_POST['wafpaweber_text']))
        $aweber_text = stripslashes($_POST['wafpaweber_text']);
      else
        $aweber_text = get_option('wafpaweber_text');

      ?>
      <div class="esaf-page-title"><?php _e('AWeber Signup Integration', 'affiliate-royale', 'easy-affiliate'); ?></div>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="wafpaweber_listname"><?php _e('AWeber List Name', 'easy-affiliate', 'affiliate-royale'); ?></label>
              <?php
                WafpAppHelper::info_tooltip(
                  "esaf-options-marketing-aweber-list",
                  __('AWeber List Name', 'easy-affiliate', 'affiliate-royale'),
                  __('Enter the AWeber mailing list name that you want users signed up for when they sign up for your affiliate program.', 'affiliate-royale', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <input type="text" name="wafpaweber_listname" id="wafpaweber_listname" value="<?php echo $aweber_listname; ?>" class="wafp-text-input form-field regular-text" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <label for="wafpaweber_text"><?php _e('Signup Checkbox Label', 'easy-affiliate', 'affiliate-royale'); ?></label>
              <?php
                WafpAppHelper::info_tooltip(
                  "esaf-options-marketing-aweber-text",
                  __('Signup Checkbox Label', 'easy-affiliate', 'affiliate-royale'),
                  __('This is the text that will display on the signup page next to your mailing list opt-out checkbox.', 'affiliate-royale', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <input type="text" name="wafpaweber_text" id="wafpaweber_text" value="<?php echo $aweber_text; ?>" class="form-field large-text" />
            </td>
          </tr>
        </tbody>
      </table>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <?php
    }

    public static function validate_option_fields($errors)
    {
      // Nothing to validate yet -- if ever
    }

    public static function update_option_fields()
    {
      // Nothing to do yet -- if ever
    }

    public static function store_option_fields()
    {
      update_option('wafpaweber_listname', $_POST['wafpaweber_listname']);
      update_option('wafpaweber_text', stripslashes($_POST['wafpaweber_text']));
    }

    public static function display_signup_field()
    {
      global $wafp_user, $wafp_blogname;

      $listname = get_option('wafpaweber_listname');
      if (!empty($listname))
      {
        if(isset($_POST['wafpaweber_opt_in_set']))
          $checked = isset($_POST['wafpaweber_opt_in'])?' checked="checked"':'';
        else
          $checked = ' checked="checked"';

        $message = get_option('wafpaweber_text');

        if(!$message or empty($message))
          $message = sprintf(__('Sign Up for the %s Newsletter', 'affiliate-royale', 'easy-affiliate'), $wafp_blogname);

        ?>
        <tr>
          <td valign="top" colspan="2">
            <input type="hidden" name="wafpaweber_opt_in_set" value="Y" />
            <input type="checkbox" name="wafpaweber_opt_in" data-listname="<?php echo $listname; ?>" style="width:auto;" id="wafpaweber_opt_in"<?php echo $checked; ?>/><?php echo $message; ?><br/><small><a href="http://www.aweber.com/permission.htm" target="_blank"><?php _e('We Respect Your Privacy', 'affiliate-royale', 'easy-affiliate'); ?></a></small><br/>
          </td>
        </tr>
        <?php
       }
    }

    public static function validate_signup_field($errors)
    {
      // Nothing to validate -- if ever
    }

    public static function load_scripts() {
      global $post, $wafp_options;

      if(!$post instanceof WP_Post) { return; }

      $listname = get_option('wafpaweber_listname');

      if(!empty($listname) and $post->ID == $wafp_options->signup_page_id)
        wp_enqueue_script( 'ar-aweber', WAFP_JS_URL . '/aweber.js', array('jquery') );
    }

    public static function thank_you_message()
    {
      if(isset($_POST['wafpaweber_opt_in']))
      {
      ?>
        <h3><?php _e("You're Almost Done - Activate Your Newsletter Subscription!", 'affiliate-royale', 'easy-affiliate'); ?></h3>
        <p><?php _e("You've just been sent an email that contains a <strong>confirm link</strong>.", 'affiliate-royale', 'easy-affiliate'); ?></p>
        <p><?php _e("In order to activate your subscription, check your email and click on the link in that email.
           You will not receive your subscription until you <strong>click that link to activate it</strong>.", 'affiliate-royale', 'easy-affiliate'); ?></p>
        <p><?php _e("If you don't see that email in your inbox shortly, fill out the form again to have another copy of it sent to you.", 'affiliate-royale', 'easy-affiliate'); ?></p>
      <?php
      }
    }
  } //END CLASS
