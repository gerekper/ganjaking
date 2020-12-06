<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <h2><?php _e('Account Page Navigation Tabs', 'mant'); ?></h2>
  <br/>
  <div class="mant-tabs-wrapper">
    <form method="post" action="">
      <div class="mant-tabs">
        <?php echo MantAppHelper::render_admin_page_tabs(); ?>
      </div>
      <img src="<?php echo MANTURL . 'images/plus.png'; ?>" class="mant-new-tab" />
      <img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" class="mant-new-tab-spinner mant-hidden" />
      <br/><br/>
      <input type="submit" name="mant_admin_page_save" class="button button-primary" value="<?php _e('Save Tabs', 'mant'); ?>" />
    </form>
  </div>
</div>
