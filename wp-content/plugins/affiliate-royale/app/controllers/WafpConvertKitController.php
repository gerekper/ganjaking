<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of ConvertKit into Affiliate Royale
*/
class WafpConvertKitController {
  public static function load_hooks() {
    add_action('wafp-set-is-affiliate',            'WafpConvertKitController::maybe_add_remove_affiliate', 10, 2);
    add_action('esaf_marketing_options',           'WafpConvertKitController::display_option_fields');
    add_action('wafp_process_options',             'WafpConvertKitController::store_option_fields');
    add_action('wafp-user-signup-fields',          'WafpConvertKitController::display_signup_field'); //Lol, we're not even using this - oh well
    add_action('wp_ajax_wafp_convertkit_get_tags', 'WafpConvertKitController::ajax_get_tags');
    // add_action('wafp_signup_thankyou_message', 'WafpConvertKitController::thank_you_message'); //WTF is this?
  }

  public static function display_option_fields() {
    $convertkit_api_key = get_option('wafpconvertkit_api_key', '');
    $convertkit_tag_id = get_option('wafpconvertkit_tag_id', '');
    $convertkit_text = get_option('wafpconvertkit_text', '');

    ?>
    <div class="esaf-page-title"><?php _e('ConvertKit Signup Integration', 'affiliate-royale', 'easy-affiliate'); ?></div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="wafpconvertkit_api_key"><?php _e('ConvertKit API Secret', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-convertkit-api-key",
                __('ConvertKit API Secret', 'easy-affiliate', 'affiliate-royale'),
                __('You can find your API secret under your Account settings at ConvertKit.com.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="wafpconvertkit_api_key" id="wafpconvertkit_api_key" value="<?php echo $convertkit_api_key; ?>" class="wafp-text-input form-field regular-text" />
            <span id="ck_message"></span>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpconvertkit_tag_id"><?php _e('ConvertKit Tag', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-convertkit-tag-id",
                __('ConvertKit Tag', 'easy-affiliate', 'affiliate-royale'),
                __('You can find your Tag under your settings at ConvertKit.com.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <select name="wafpconvertkit_tag_id" id="wafpconvertkit_tag_id" data-tagid="<?php echo $convertkit_tag_id; ?>" class="wafp-text-input form-field"></select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wafpconvertkit_text"><?php _e('Signup Checkbox Label', 'affiliate-royale', 'easy-affiliate'); ?></label>
            <?php
              WafpAppHelper::info_tooltip(
                "esaf-options-marketing-convertkit-text",
                __('Signup Checkbox Label', 'easy-affiliate', 'affiliate-royale'),
                __('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'affiliate-royale', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="wafpconvertkit_text" id="wafpconvertkit_text" value="<?php echo $convertkit_text; ?>" class="wafp-text-input form-field large-text" />
          </td>
        </tr>
      </tbody>
    </table>
    <div>&nbsp;</div>
    <div>&nbsp;</div>

    <script type="text/javascript">
      var wafp_load_convertkit_tags_dropdown = function(id, api_secret) {
        (function($) {
          if(api_secret == '') { return; }

          var tag_id = $(id).data('tagid');

          var args = {
            action: 'wafp_convertkit_get_tags',
            api_secret: api_secret
          };

          $.post(ajaxurl, args, function(res) {
            if(res && res.length > 0) {
              $('span#ck_message').html('<span style="color:green;">All Groovy!</span>');
              var options = '';
              var selected = '';

              $.each(res, function(index, tag) {
                selected = ((tag_id == tag.id) ? ' selected' : '');
                options += '<option value="' + tag.id + '"' + selected + '>' + tag.name + '</option>';
              });

              $(id).html(options);
            } else {
              $('span#ck_message').html('<span style="color:red;">Sorry, wrong API Secret</span>');
              $(id).html('');
            }
          }, 'json');
        })(jQuery);
      };

      jQuery('#wafpconvertkit_api_key').blur(function() {
        wafp_load_convertkit_tags_dropdown('#wafpconvertkit_tag_id', jQuery(this).val());
      });

      jQuery(document).ready(function($) {
        wafp_load_convertkit_tags_dropdown('#wafpconvertkit_tag_id', $('#wafpconvertkit_api_key').val());
      });
    </script>
    <?php
  }

  public static function store_option_fields() {
    update_option('wafpconvertkit_api_key', stripslashes($_POST['wafpconvertkit_api_key']));
    update_option('wafpconvertkit_tag_id', (isset($_POST['wafpconvertkit_tag_id']))?stripslashes($_POST['wafpconvertkit_tag_id']):'');
    update_option('wafpconvertkit_text', stripslashes($_POST['wafpconvertkit_text']));
  }

  public static function display_signup_field() {
    $listname = get_option('wafpconvertkit_tag_id');

    if(!empty($listname)) {
      if(isset($_POST['wafpconvertkit_opt_in_set']))
        $checked = isset($_POST['wafpconvertkit_opt_in'])?' checked="checked"':'';
      else
        $checked = ' checked="checked"';

      $message = get_option('wafpconvertkit_text');

      if(!$message or empty($message))
        $message = __('Sign Up for our newsletter', 'affiliate-royale', 'easy-affiliate');

      ?>
      <tr>
        <td valign="top" colspan="2">
          <div class="wafp-convertkit-signup-field">
            <input type="hidden" name="wafpconvertkit_opt_in_set" value="Y" />
            <div id="wafp-convertkit-checkbox">
              <input type="checkbox" name="wafpconvertkit_opt_in" id="wafpconvertkit_opt_in" class="wafp-form-checkbox" <?php echo $checked; ?> style="width:auto;" /> <span class="wafp-convertkit-message"> <?php echo $message; ?></span>
            </div>
            <div id="wafp-convertkit-privacy"><small><a href="http://convertkit.com/privacy/" class="wafp-convertkit-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'affiliate-royale', 'easy-affiliate'); ?></a></small></div>
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
    $convertkit_api_key = get_option('wafpconvertkit_api_key', '');
    $convertkit_tag_id = get_option('wafpconvertkit_tag_id', '');

    $args = array(
      'email'     => $email,
      'name'      => $first_name . ' ' . $last_name
    );

    $res = (array)json_decode(self::call($args, "tags/{$convertkit_tag_id}/subscribe", $convertkit_api_key, 'POST'));

    return (!empty($res));
  }

  public static function maybe_remove_member_from_list($email) {
    return;
    //Will support this later when we feel like piggy backing off of MemberPress
    //As of right now though, this isn't supported by ConvertKit's API anyways
  }

  public static function ajax_get_tags() {
    // Validate inputs
    if(!isset($_POST['api_secret'])) {
      die(''); //Silence
    }

    $api_secret = stripslashes($_POST['api_secret']);

    $res = (array)json_decode(self::call(array(), 'tags', $api_secret));

    if(isset($res['tags'])) {
      die(json_encode($res['tags']));
    }

    die(''); //Silence
  }

  public static function call($args, $endpoint, $api_secret, $method = 'GET') {
    $url                = "https://api.convertkit.com/v3/{$endpoint}?api_secret={$api_secret}";
    $wp_args            = array('body' => $args);
    $wp_args['method']  = $method;
    $wp_args['timeout'] = 30;

    $res = wp_remote_request($url, $wp_args);

    if(!is_wp_error($res) && ($res['response']['code'] == 200 || $res['response']['code'] == 201)) {
      return $res['body'];
    }
    else {
      return false;
    }
  }
} //End class
