<?php

/**
 * Coupon Notifications
 */

if (isset($_GET['coupon_created'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
<div id="message" class="updated"><p><?php esc_html_e('Coupon created', 'follow_up_emails'); ?></p></div>
<?php
endif;

if (isset($_GET['coupon_deleted'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Coupon deleted!', 'follow_up_emails'); ?></p></div>
<?php
endif;

if (isset($_GET['coupon_updated'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Coupon updated', 'follow_up_emails'); ?></p></div>
<?php
endif;