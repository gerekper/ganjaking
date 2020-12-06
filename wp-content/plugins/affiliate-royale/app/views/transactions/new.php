<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wrap">
  <?php
  WafpAppHelper::plugin_title(__('Add New Affiliate Transaction','affiliate-royale', 'easy-affiliate'));
  require(WAFP_VIEWS_PATH . "/shared/errors.php");
  ?>

  <div class="form-wrap">
    <p class="description"><?php _e('Creating a new transaction here will calculate and record commissions based on the affiliate selected. Also, any commission notification emails that are enabled will be sent out.','affiliate-royale', 'easy-affiliate'); ?></p>
    <form action="" method="post">
      <input type="hidden" name="action" value="create" />
      <table class="form-table">
        <tbody>
          <?php require(WAFP_VIEWS_PATH . "/transactions/_form.php"); ?>
        </tbody>
      </table>
      <p class="submit">
        <input type="submit" id="submit" class="button button-primary" value="<?php _e('Create', 'affiliate-royale', 'easy-affiliate'); ?>" />
      </p>
    </form>
  </div>
</div>

