<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<br/>
<h3><?php _e('HelpScout', 'memberpress'); ?></h3>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="mepr_helpscout_custom_app_enabled"><?php _e('Enable HelpScout App', 'memberpress'); ?></label>
        <?php MeprAppHelper::info_tooltip(
          'mepr-helpscout-custom-app-enabled-tooltip',
          __('Enable HelpScout Custom App', 'memberpress'),
          __('Check this option to enable MemberPress to send data to a HelpScout custom app.', 'memberpress')
        ); ?>
      </th>
      <td>
        <input type="checkbox" id="mepr_helpscout_custom_app_enabled" class="mepr-toggle-checkbox" data-box="mepr_helpscout_custom_app_box" name="mepr_helpscout_custom_app_enabled" <?php checked($helpscout_enabled); ?> />
      </td>
    </tr>
  </tbody>
</table>
<div id="mepr_helpscout_custom_app_box" class="mepr-sub-box-white mepr_helpscout_custom_app_box">
  <div class="mepr-arrow mepr-white mepr-up mepr-sub-box-arrow"> </div>
  <h3 class="mepr-page-heading"><?php _e('HelpScout Custom App'); ?></h3>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label><?php _e('HelpScout Callback URL'); ?></label>
          <?php MeprAppHelper::info_tooltip(
            'mepr-helpscout-custom-app-url-tooltip',
            __('HelpScout Custom App Callback Url', 'memberpress'),
            __('Simply copy this URL and enter it into your HelpScout custom app under "Callback Url."', 'memberpress')
          ); ?>
        </th>
        <td>
          <input id="mepr_helpscout_url" class="regular-text" value="<?php echo admin_url('admin-ajax.php?action=mepr_helpscout_custom_app'); ?>" disabled="true" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="mepr_helpscout_custom_app_secret"><?php _e('HelpScout Secret Key'); ?></label>
          <?php MeprAppHelper::info_tooltip(
            'mepr-helpscout-custom-app-secret-tooltip',
            __('HelpScout Secret Key', 'memberpress'),
            __('HelpScout uses a shared secret to authenticate it\'s custom apps. Enter a string of characters as a key here and make sure it\'s the same key entered under "Secret Key" in your HelpScout custom app.', 'memberpress')
          ); ?>
        </th>
        <td>
          <input id="mepr_helpscout_custom_app_secret" name="mepr_helpscout_custom_app_secret" class="regular-text" value="<?php echo $helpscout_secret; ?>">
        </td>
      </tr>
    </tbody>
  </table>
</div>

