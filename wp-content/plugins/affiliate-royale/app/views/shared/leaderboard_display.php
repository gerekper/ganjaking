<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if(!empty($data)): ?>
  <div class="wafp_leaderboard_wrapper">
    <div class="wafp_leaderboard_affiliate wafp_leaderboard_header">
      <?php _e('Affiliate', 'affiliate-royale', 'easy-affiliate'); ?>
    </div>

    <div class="wafp_leaderboard_stats wafp_leaderboard_header">
      <?php _e('Sales Referred', 'affiliate-royale', 'easy-affiliate'); ?>
    </div>

    <div style="clear:both;"></div>

    <?php foreach($data as $datum): ?>
      <?php
        $alt = (isset($alt) & empty($alt))?' wafp_leaderboard_alt':'';
      ?>
      <div class="wafp_leaderboard_affiliate <?php echo $alt; ?>">
        <?php $user = get_userdata($datum->affiliate_id); ?>
        <?php echo $user->display_name; ?>
      </div>

      <div class="wafp_leaderboard_stats <?php echo $alt; ?>">
        <?php echo $datum->total; ?>
      </div>

      <div style="clear:both;"></div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="wafp_leaderboard_empty"><?php _e('No data available', 'affiliate-royale', 'easy-affiliate'); ?></p>
<?php endif; ?>
