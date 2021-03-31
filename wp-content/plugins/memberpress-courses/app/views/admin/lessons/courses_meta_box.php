<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <input type="hidden" name="mpcs-lesson-nonce" value="<?php echo \wp_create_nonce('mpcs-lesson-nonce' . \wp_salt()); ?>" />
  <div class="grid">
    <div class="col-1-3 grid-pad">
      <div class="content">
        <label for="option-1"><?php _e('Option 1', 'memberpress-courses'); ?></label>
        <?php memberpress\courses\helpers\App::info_tooltip('option-1',
                __('Option 1', 'memberpress-courses'),
                __('This is a tooltip. Use it wisely', 'memberpress-courses'));
        ?>
      </div>
    </div>
    <div class="col-2-3 grid-pad">
      <div class="content">
        <input type="text" class="regular-text" name="option-1" value="" />
      </div>
    </div>
    <div class="col-1-3 grid-pad">
      <div class="content">
        <label for="option-2"><?php _e('Option 2', 'memberpress-courses'); ?></label>
      </div>
    </div>
    <div class="col-2-3 grid-pad">
      <div class="content">
        <input type="checkbox" name="option-2" />
      </div>
    </div>
  </div>
</div>
