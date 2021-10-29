<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h3><?php printf(__('Thanks for Registering for %s!', 'affiliate-royale', 'easy-affiliate'), $wafp_blogname); ?></h3>
<p><?php _e('You should shortly receive a confirmation email with your login information.', 'affiliate-royale', 'easy-affiliate'); ?></p>
<p><a href="<?php echo WafpUtils::login_url(); ?>"><?php _e('Login to your Affiliate Dashboard', 'affiliate-royale', 'easy-affiliate'); ?></a></p>
<?php do_action('wafp_signup_thankyou_message'); ?>