<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
  <?php WafpAppHelper::plugin_title(__('Options','affiliate-royale', 'easy-affiliate'),
    '<a href="https://affiliateroyale.com/user-manual/" class="add-new-h2" target="_blank">'.__('User Manual', 'affiliate-royale', 'easy-affiliate').'</a>'); ?>

  <div>&nbsp;</div>

  <form name="wafp_options_form" id="wafp_options_form" method="post" action="">
    <input type="hidden" name="action" value="process-form">
    <table class="esaf-settings-table">
      <tr class="esaf-mobile-nav">
        <td colspan="2">
          <a href="" class="esaf-toggle-nav"><i class="ea-icon-menu"> </i></a>
        </td>
      </tr>
      <tr>
        <td class="esaf-settings-table-nav">
          <ul class="esaf-sidebar-nav">
            <li><a data-id="pages"><?php _e('Pages', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="commission"><?php _e('Commission', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="dashboard"><?php _e('Dashboard', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="affiliates"><?php _e('Affiliates', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="integration"><?php _e('Integrations', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="emails"><?php _e('Emails', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="i18n"><?php _e('I18n', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <li><a data-id="marketing"><?php _e('Marketing', 'affiliate-royale', 'easy-affiliate'); ?></a></li>
            <?php do_action('esaf_admin_options_nav'); ?>
          </ul>
        </td>
        <td class="esaf-settings-table-pages">
          <div class="esaf-page" id="pages">
            <?php require(WAFP_VIEWS_PATH . '/options/pages.php'); ?>
            <?php do_action('esaf_pages_options'); ?>
          </div>
          <div class="esaf-page" id="commission">
            <?php require(WAFP_VIEWS_PATH . '/options/commission.php'); ?>
            <?php do_action('esaf_commission_options'); ?>
          </div>
          <div class="esaf-page" id="dashboard">
            <?php require(WAFP_VIEWS_PATH . '/options/dashboard.php'); ?>
            <?php do_action('esaf_dashboard_options'); ?>
          </div>
          <div class="esaf-page" id="affiliates">
            <?php require(WAFP_VIEWS_PATH . '/options/affiliates.php'); ?>
            <?php do_action('esaf_affiliates_options'); ?>
          </div>
          <div class="esaf-page" id="integration">
            <?php require(WAFP_VIEWS_PATH . '/options/integration.php'); ?>
            <?php do_action('esaf_integration_options'); ?>
          </div>
          <div class="esaf-page" id="emails">
            <?php require(WAFP_VIEWS_PATH . '/options/emails.php'); ?>
            <?php do_action('esaf_emails_options'); ?>
          </div>
          <div class="esaf-page" id="i18n">
            <?php require(WAFP_VIEWS_PATH . '/options/i18n.php'); ?>
            <?php do_action('esaf_i18n_options'); ?>
          </div>
          <div class="esaf-page" id="marketing">
            <?php do_action('esaf_marketing_options'); ?>
          </div>
          <?php do_action('wafp_display_options'); /* Deprecated */ ?>
          <?php do_action('esaf_display_options'); ?>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Options', 'affiliate-royale', 'easy-affiliate') ?>" />
    </p>

  </form>
</div>

