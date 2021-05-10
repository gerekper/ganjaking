<?php
/**
 * HTML required for the waitlist panel toggle tabs
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>
<ul class="wcwl_tabs">
	<li class="current" data-tab="waitlist"><?php _e( 'Waitlist', 'woocommerce-waitlist' ); ?></li>
	<li data-tab="archive"><?php _e( 'Archive', 'woocommerce-waitlist' ); ?></li>
	<li data-tab="options"><?php _e( 'Options', 'woocommerce-waitlist' ); ?></li>
</ul>
