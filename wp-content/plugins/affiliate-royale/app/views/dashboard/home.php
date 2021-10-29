<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h3><?php _e('My Affiliate Dashboard', 'affiliate-royale', 'easy-affiliate'); ?></h3>
<?php do_action('wafp-dashboard-home-top',$affiliate_id,$overall_stats); ?>
<div class="wafp-dashboard-custom-message">
<?php echo $wafp_options->custom_message; ?>
</div>
<br/>
<?php WafpAppHelper::display_affiliate_commissions($affiliate_id); ?>
<br/>
<h3><?php _e('Quick Stats', 'affiliate-royale', 'easy-affiliate'); ?></h3>
<?php if( apply_filters('wafp-show-quick-stats', true ) ): ?>
<div class="wafp-dashboard-quick-stats">
 <span><?php printf(__('Clicks Referred: %d', 'affiliate-royale', 'easy-affiliate'), esc_html($overall_stats['clicks'])); ?></span><br/>
 <span><?php printf(__('Sales Referred: %s', 'affiliate-royale', 'easy-affiliate'), esc_html($overall_stats['transactions'])); ?></span><br/>
 <span><?php printf(__('Revenues from Referred Sales: %s', 'affiliate-royale', 'easy-affiliate'), esc_html(WafpAppHelper::format_currency($overall_stats['total']))); ?></span><br/>
 <span><?php printf(__('Commissions from Referred Sales: %s', 'affiliate-royale', 'easy-affiliate'), esc_html(WafpAppHelper::format_currency($overall_stats['commission']))); ?></span>
</div>
<?php endif; ?>
<?php do_action('wafp-dashboard-home-bottom',$affiliate_id,$overall_stats); ?>

