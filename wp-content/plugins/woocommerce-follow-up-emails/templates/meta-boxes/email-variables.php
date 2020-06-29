<?php
if ( !$email->type ):
?>
<div id="fue-email-variables-notice">
	<p class="meta-box-notice"><?php esc_html_e('Please set the email type first', 'follow_up_emails'); ?></p>
</div>
<?php else: ?>
<ul id="fue-email-variables-list">
	<p><span style="color:#7ad03a;" class="dashicons dashicons-warning"></span> Please <a href="http://docs.woothemes.com/document/automated-follow-up-emails-docs/email-variables-and-merge-tags/" target="_blank">review the documentation</a> for an exhaustive list of variables.</p>
	<?php do_action('fue_email_variables_list', $email); ?>
	<li class="var hideable var_web_version_url"><strong>{webversion_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL to the web version of the email.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_web_version_link"><strong>{webversion_link}</strong> <img class="help_tip" title="<?php esc_attr_e('Renders a <em>View in browser</em> link that points to the web version of the email.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_customer_username"><strong>{customer_username}</strong> <img class="help_tip" title="<?php esc_attr_e('The username of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_customer_first_name"><strong>{customer_first_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The first name of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_customer_name"><strong>{customer_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The full name of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_customer_email"><strong>{customer_email}</strong> <img class="help_tip" title="<?php esc_attr_e('The email address of the customer who purchased from your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_store_url"><strong>{store_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL/Address of your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_store_url_secure"><strong>{store_url_secure}</strong> <img class="help_tip" title="<?php esc_attr_e('The secure URL/Address of your store (HTTPS).', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_store_url_path"><strong>{store_url=path}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL/Address of your store with path added at the end. Ex. {store_url=/categories}', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_store_name"><strong>{store_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The name of your store.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_cart"><strong>{cart_contents}</strong> <img class="help_tip" title="<?php esc_attr_e('The cart items displayed in a table.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_cart"><strong>{cart_total}</strong> <img class="help_tip" title="<?php esc_attr_e('The total amount of all items in the cart', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_cart"><strong>{cart_url}</strong> <img class="help_tip" title="<?php esc_attr_e('The URL of the cart page.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_coupon var_interval_coupon"><strong>{coupon_code}</strong> <img class="help_tip" title="<?php esc_attr_e('Coupon code generated/used', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_coupon var_interval_coupon"><strong>{coupon_code_used}</strong> <img class="help_tip" title="<?php esc_attr_e('Coupon code used', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_coupon var_interval_coupon"><strong>{coupon_amount}</strong> <img class="help_tip" title="<?php esc_attr_e('Coupon code discount amount', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_unsubscribe_url"><strong>{unsubscribe_url}</strong> <img class="help_tip" title="<?php esc_attr_e('URL where users will be able to opt-out of the email list.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
	<li class="var hideable var_post_id"><strong>{post_id=xx}</strong> <img class="help_tip" title="<?php esc_attr_e('Include the excerpt of the specified Post ID.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
</ul>
<?php endif; ?>
