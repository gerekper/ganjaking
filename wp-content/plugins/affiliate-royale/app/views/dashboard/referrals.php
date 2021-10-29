<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if( !empty($referrer) ): ?>
  <h4><?php _e('Affiliate Referrer:', 'affiliate-royale', 'easy-affiliate'); ?></h4>
  <span id="wafp-aff-name-<?php echo $referrer->get_id(); ?>" class="wafp-aff-name"><?php echo $referrer->get_full_name(); ?></span>
  <div id="wafp-aff-info-<?php echo $referrer->get_id(); ?>" class="wafp-aff-info wafp-hidden" style="display: block;">
    <div class="wafp-aff-info-row wafp-aff-info-row-id">
      <span class="wafp-aff-info-label wafp-aff-info-label-id"><?php _e('Id','affiliate-royale', 'easy-affiliate'); ?></span>
      <span class="wafp-aff-info-value wafp-aff-info-value-id"><?php echo $referrer->get_id(); ?></span>
    </div>
    <div class="wafp-aff-info-row wafp-aff-info-row-username">
      <span class="wafp-aff-info-label wafp-aff-info-label-username"><?php _e('Username','affiliate-royale', 'easy-affiliate'); ?></span>
      <span class="wafp-aff-info-value wafp-aff-info-value-username"><?php echo $referrer->get_field('user_login'); ?></span>
    </div>
    <div class="wafp-aff-info-row wafp-aff-info-row-email">
      <span class="wafp-aff-info-label wafp-aff-info-label-email"><?php _e('Email','affiliate-royale', 'easy-affiliate'); ?></span>
      <span class="wafp-aff-info-value wafp-aff-info-value-email"><?php echo $referrer->get_field('user_email'); ?></span>
    </div>
  </div>
  <div class="wafp-spacer">&nbsp;</div>
<?php endif; ?>

<h4><?php _e('Affiliate Referrals:', 'affiliate-royale', 'easy-affiliate'); ?></h4>
<?php if( empty($affiliates) ): ?>
  <p><?php _e('Sorry, you haven\'t had any referrals yet.','affiliate-royale', 'easy-affiliate'); ?></p>
<?php else: ?>
  <?php WafpDashboardHelper::display_referrals($affiliates, $affiliate_id); ?>
  <p><a href="<?php echo admin_url( 'admin-ajax.php?action=wafp-referrals-csv', 'relative' ); ?>"><?php _e('Download as CSV','affiliate-royale', 'easy-affiliate'); ?></a></p>
<?php endif; ?>
