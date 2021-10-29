<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="<?php echo $wafp_options->edge_updates_str; ?>-wrap">
  <input type="checkbox" id="<?php echo $wafp_options->edge_updates_str; ?>" data-nonce="<?php echo wp_create_nonce('wp-edge-updates'); ?>" <?php checked($wafp_options->edge_updates); ?>/>&nbsp;<?php _e('Include Affiliate Royale edge (development) releases in automatic updates (not recommended for production websites)', 'affiliate-royale', 'easy-affiliate'); ?> <img src="<?php echo WAFP_IMAGES_URL . '/square-loader.gif'; ?>" alt="<?php _e('Loading...', 'affiliate-royale', 'easy-affiliate'); ?>" class="wafp_loader" />
</div>
