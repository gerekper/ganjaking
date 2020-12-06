<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-page-title">
  <?php _e('Affiliate Pages', 'affiliate-royale', 'easy-affiliate'); ?>
</div>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->dashboard_page_id_str; ?>"><?php _e('Dashboard Page*', 'affiliate-royale', 'easy-affiliate') ?></label>
        <?php WafpAppHelper::info_tooltip(
          'esaf-options-pages-dashboard',
          __('Affiliate Dashboard Page', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
          __('This is the WordPress page that Easy Affiliate will use as the Affiliate\'s Dashboard.', 'affiliate-royale', 'easy-affiliate')
        );
        ?>
      </th>
      <td>
        <?php
          WafpOptionsHelper::wp_pages_dropdown(
            $wafp_options->dashboard_page_id_str,
            $wafp_options->dashboard_page_id,
            __('Affiliate Dashboard', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->signup_page_id_str; ?>"><?php _e('Signup Page*', 'affiliate-royale', 'easy-affiliate') ?></label>
        <?php WafpAppHelper::info_tooltip(
          'esaf-options-pages-signup',
          __('Affiliate Signup Page', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
          __('This is the WordPress page that Easy Affiliate will use as the affiliate signup page.', 'affiliate-royale', 'easy-affiliate')
        );
        ?>
      </th>
      <td>
        <?php
          WafpOptionsHelper::wp_pages_dropdown(
            $wafp_options->signup_page_id_str,
            $wafp_options->signup_page_id,
            __('Affiliate Signup', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->login_page_id_str; ?>"><?php _e('Login Page*', 'affiliate-royale', 'easy-affiliate') ?></label>
        <?php WafpAppHelper::info_tooltip(
          'esaf-options-pages-login',
          __('Affiliate Login Page', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
          __('This is the WordPress page that Easy Affiliate will use as the affiliate login page.', 'affiliate-royale', 'easy-affiliate')
        );
        ?>
      </th>
      <td>
        <?php
          WafpOptionsHelper::wp_pages_dropdown(
            $wafp_options->login_page_id_str,
            $wafp_options->login_page_id,
            __('Affiliate Login', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </td>
    </tr>
  </tbody>
</table>

