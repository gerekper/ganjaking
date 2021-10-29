<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php if(defined('AFFILIATE_ROYALE_LICENSE_KEY') and isset($error)): ?>
  <div class="error" style="padding: 10px;"><?php printf(__('Error with AFFILIATE_ROYALE_LICENSE_KEY: %s', 'affiliate-royale', 'easy-affiliate'), $error); ?></div>
<?php else: ?>
  <div class="error" style="padding: 10px;"><?php printf(__('<b>Affiliate Royale hasn\'t been activated yet.</b> Go to the Affiliate Royale %1$sactivation page%2$s to activate it.', 'affiliate-royale', 'easy-affiliate'), '<a href="'.admin_url('admin.php?page=affiliate-royale-activate').'">','</a>'); ?></div>
<?php endif; ?>
