<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
$count = esc_html($count);
$title = esc_html($title);
$delim = esc_html($delim);
?>
<div class="mpdt_ops_row" id="mpdt_ops_row-<?php echo esc_html($count); ?>">
  <div><?php _e('Webhook URL', 'memberpress-developer-tools'); ?></div>

  <input type="text" name="<?php echo MPDT_WEBHOOKS_KEY; ?>[<?php echo $count; ?>][url]" class="regular-text" value="<?php echo $webhook['url']; ?>" />
  <span>
    <a href="#" class="mpdt_remove_row" title="<?php _e('Remove Webhook URL', 'memberpress-developer-tools'); ?>" data-value="<?php echo $count; ?>"><i class="mp-icon mp-icon-cancel-circled mp-16"></i></a>
  </span>

  <span>
    <a href="" class="mpdt_toggle_advanced" data-id="<?php echo $count; ?>" title="<?php echo $title; ?>"><?php _e('Advanced', 'memberpress-developer-tools'); ?></a>
  </span>

  <div>&nbsp;</div>

  <div id="mpdt_advanced_<?php echo $count; ?>" class="mepr-sub-box-white mpdt_advanced_box">
    <div class="mepr-arrow mepr-white mepr-up mepr-sub-box-arrow"> </div>

    <h3><?php _e('Select Events to send to this Webhook', 'memberpress-developer-tools'); ?></h3>

    <?php $i = 0; ?>
    <?php foreach($events as $slug => $e): ?>

      <?php if(0===($i % 2)): ?>
      <div class="grid grid-pad">
      <?php endif; ?>

      <?php $col = (0===$i) ? 'col-1-1' : 'col-1-2'; ?>

        <div id="mpdt_row_<?php echo $count; ?>_<?php echo $slug; ?>" class="<?php echo $col; ?> mpdt_<?php echo $slug; ?>_checkbox mpdt_row_<?php echo $count; ?>_checkbox">
          <div class="content">
            <label>
              <input type="checkbox" data-id='<?php echo $count; ?>' name="<?php echo MPDT_WEBHOOKS_KEY; ?>[<?php echo $count; ?>][events][<?php echo $slug; ?>]" <?php checked(isset($webhook['events'][$slug])); ?> />
              <?php echo $e->label; ?>
            </label>
            <div class="mpdt_description"><?php echo $e->desc; ?></div>
          </div>
        </div>

      <?php if(0===$i) { $i++; } ?>

      <?php if(1===($i % 2)): ?>
      </div>
      <?php endif; ?>

      <?php $i++; ?>
    <?php endforeach; ?>

    <?php /* Correct for odd number of events */ ?>
    <?php if(1===($i % 2)): ?>
        <div class="col-1-2"><div class="content">&nbsp;</div></div>
      </div>
    <?php endif; ?>
  </div>


</div>

