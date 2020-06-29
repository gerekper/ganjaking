<?php

/**
 * Update FUE Data to 7.1
 *
 * Merge storewide and product emails together
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$product_emails = fue_get_emails( 'product', array(FUE_Email::STATUS_ARCHIVED, FUE_Email::STATUS_ACTIVE, FUE_Email::STATUS_INACTIVE) );

foreach ( $product_emails as $email ) {
	$args = array(
		'id'    => $email->id,
		'type'  => 'storewide'
	);
	fue_update_email( $args );
}
