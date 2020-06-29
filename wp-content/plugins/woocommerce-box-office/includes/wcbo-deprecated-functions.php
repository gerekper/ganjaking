<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check if this is a ticket page on the frontend.
 *
 * @deprecated
 *
 * @param  string  $page Page type
 * @return boolean       True if ticket page
 */
function wc_box_office_is_ticket_page( $page = 'edit' ) {
	_deprecated_function( 'wc_box_office_is_ticket_page', '1.1.0', 'wcbo_is_my_ticket_page' );

	return (
		! is_admin()
		&& isset( $_GET['ticket'] )
		&& $page === $_GET['ticket']
		&& isset( $_GET['token'] )
	);
}

/**
 * Get ticket URL for customer.
 *
 * @deprecated
 *
 * @param int $ticket_id Ticket ID
 * @param string $action Action
 *
 * @return string Ticket URL
 */
function wc_box_office_get_ticket_url( $ticket_id, $action = 'edit' ) {
	_deprecated_function( 'wc_box_office_get_ticket_url', '1.1.0', 'wcbo_get_my_ticket_url' );

	$token = get_post_meta( $ticket_id, '_token', true );
	return apply_filters( 'wc_box_office_get_ticket_url', home_url( '/?ticket=' . $action . '&token=' . $token ) );
}


