<?php
/**
 * Update functions.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wccos_update_1_1_11_sendmail_to_recipients' ) ) {
	/**
	 * Update 1.1.11 - sendmail to recipient.
	 */
	function yith_wccos_update_1_1_11_sendmail_to_recipients() {
		// phpcs:disable WordPress.DB.SlowDBQuery
		$args               = array(
			'meta_query' => array(
				array(
					'key'     => 'status_type',
					'value'   => array( 'custom', '' ),
					'compare' => 'IN',
				),
				array(
					'key'     => 'sendmail',
					'value'   => '0',
					'compare' => '!=',
				),
			),
		);
		$statuses_to_update = yith_wccos_get_statuses( $args );

		foreach ( $statuses_to_update as $id ) {
			$sendmail = get_post_meta( $id, 'sendmail', true );

			$map = array(
				'1' => array( 'admin' ),
				'2' => array( 'customer' ),
				'3' => array( 'admin', 'customer' ),
				'4' => array( 'custom-email' ),
				'5' => array( 'admin', 'customer', 'custom-email' ),
			);

			if ( $sendmail && array_key_exists( $sendmail, $map ) ) {
				update_post_meta( $id, 'recipients', $map[ $sendmail ] );
				delete_post_meta( $id, 'sendmail' );
			}
		}

		// phpcs:enable
	}
}
