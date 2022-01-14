<?php
/**
 * Render Login Page
 */



/**
 * Login redirect.
 *
 * @return void|boolean
 */
function seedprod_pro_redirect_login_page() {
	$post = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( empty( $post ) ) {
		$query = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( empty( $query ) || strpos( http_build_query( $query ), 'redirect_to' ) !== false || strpos( http_build_query( $query ), 'loggedout' ) !== false ) {
			// Top Level Settings
			$ts                = get_option( 'seedprod_settings' );
			$seedprod_settings = json_decode( $ts );

			// Page Info
			$page_id = 0;

			//Get 404 Page Id
			if ( ! empty( $seedprod_settings->enable_login_mode ) ) {
				$page_id = get_option( 'seedprod_login_page_id' );
			} else {
				return false;
			}

			// Get Page
			global $wpdb;
			$tablename = $wpdb->prefix . 'posts';
			$sql       = "SELECT * FROM $tablename WHERE id= %d";
			$safe_sql  = $wpdb->prepare( $sql, absint( $page_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$page      = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			$settings = json_decode( $page->post_content_filtered );

			if ( empty( $page ) ) {
				return false;
			}

			if ( ! empty( $settings->redirect_login_page ) && ! empty( $seedprod_settings->enable_login_mode ) ) {
				wp_safe_redirect( '/?page_id=' . $page_id );
				exit;
			}
		}
	}
}


