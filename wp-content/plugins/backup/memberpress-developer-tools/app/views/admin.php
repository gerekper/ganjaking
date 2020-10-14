<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php /*
<div class="wrap">
*/ ?>
  <h1><?php _e('MemberPress Developer Tools', 'memberpress-developer-tools'); ?></h1>
  <?php require(MEPR_VIEWS_PATH.'/admin/errors.php'); ?>

  <div class="mpdt_spacer"></div>
  <table class="mepr-settings-table">
    <tr class="mepr-mobile-nav">
      <td colspan="2">
        <a href="" class="mepr-toggle-nav"><i class="mp-icon-menu"> </i></a>
      </td>
    </tr>
    <tr>
      <td class="mepr-settings-table-nav">
        <ul class="mepr-sidebar-nav">
          <li><a data-id="mepr-webhooks"><?php _e('Webhooks', 'memberpress-developer-tools'); ?></a></li>
          <li><a data-id="mepr-events"><?php _e('Events', 'memberpress-developer-tools'); ?></a></li>
          <li><a data-id="mepr-api"><?php _e('REST API', 'memberpress-developer-tools'); ?></a></li>
          <li><a data-id="mepr-resources"><?php _e('Other Resources', 'memberpress-developer-tools'); ?></a></li>
        </ul>
      </td>
      <td class="mepr-settings-table-pages">
        <div class="mepr-page" id="mepr-webhooks">
          <?php require(MPDT_VIEWS_PATH.'/webhooks.php'); ?>
          <div class="mpdt_spacer"></div>
        </div>
        <div class="mepr-page" id="mepr-events">
          <?php require(MPDT_VIEWS_PATH.'/events.php'); ?>
          <div class="mpdt_spacer"></div>
        </div>
        <div class="mepr-page" id="mepr-api">
          <?php require(MPDT_VIEWS_PATH.'/api.php'); ?>
          <div class="mpdt_spacer"></div>
        </div>
        <div class="mepr-page" id="mepr-resources">
          <?php require(MPDT_VIEWS_PATH.'/resources.php'); ?>
          <div class="mpdt_spacer"></div>
        </div>
      </td>
    </tr>
  </table>

<?php /*
</div>
*/ ?>
