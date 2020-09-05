<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\WPBuddy_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Rating.
 *
 * Class that checks and invites the user to rate the plugin on CodeCanyon.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.9.0
 */
final class Admin_Rating_Controller {


	/**
	 * Admin_Settings_Controller constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {

		add_action( 'admin_notices', [ $this, 'rating_notice' ] );

		Admin_Scripts_Controller::instance()->enqueue_rating_scripts();

		/**
		 * Admin Rating Init Action.
		 *
		 * Allows to hook into the Admin Rating Controller after everything has started up.
		 *
		 * @hook  wpbuddy/rich_snippets/backend/rating/init
		 *
		 * @param {Admin_Rating_Controller} $admin_rating_controller
		 *
		 * @since 2.9.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/backend/rating/init', array( $this ) );
	}


	/**
	 * Shows the admin notice to inform a user that he/she should rate the plugin.
	 *
	 * @since 2.9.0
	 */
	public function rating_notice() {
		printf(
			'<div class="wpb-rs-rating-notice notice notice-warning is-dismissible notice-alt"><p>%s <a href="#" data-next="50" class="button">%s</a> <a href="#" data-next="15" class="button">%s</a></p></div>',
			__( 'Do you want to get free LIFETIME updates for SNIP, the Rich Snippets & Structured Data Plugin?', 'rich-snippets-schema' ),
			__( 'No', 'rich-snippets-schema' ),
			__( 'Yes, of course! 👍', 'rich-snippets-schema' )
		);
	}


	/**
	 * Checks if a user has rated on CodeCanyon.
	 *
	 * @since 2.9.0
	 */
	public static function check_user_rating() {
		$response = WPBuddy_Model::request(
			'/wpbuddy/rich_snippets_manager/v1/rating',
			[],
			false,
			true
		);

		if ( is_wp_error( $response ) ) {

			$data = $response->get_error_data();

			if ( ! isset( $data['body'] ) ) {
				return;
			}

			$data = @json_decode( $data['body'] );

			if ( is_null( $data ) ) {
				return;
			}

			if ( ! isset( $data->data ) ) {
				return;
			}

			$data = $data->data;

			if ( ! isset( $data->reason ) ) {
				return;
			}

			if ( 'not_yet_rated' === $data->reason ) {
				update_option( 'wpb_rs/rated', false, true );
			}

			return;
		}

		if ( ! isset( $response->rating ) ) {
			return;
		}

		if ( $response->rating > 0 ) {
			update_option( 'wpb_rs/rated', true, true );
			update_option( 'wpb_rs/rating', intval( $response->rating ), false );
		}

	}

}
