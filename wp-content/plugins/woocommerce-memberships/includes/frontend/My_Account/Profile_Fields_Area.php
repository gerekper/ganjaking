<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Frontend\My_Account;

use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The handler for the Profile Fields Area.
 *
 * @since 1.19.0
 */
class Profile_Fields_Area {


	/** @var string $endpoint the Profile Fields area endpoint */
	private $endpoint;

	/** @var bool $using_permalinks whether permalinks are being used */
	private $using_permalinks;


	/**
	 * Constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		$this->endpoint         = wc_memberships_get_profile_fields_area_endpoint();
		$this->using_permalinks = (bool) get_option( 'permalink_structure' );

		add_filter( 'woocommerce_account_menu_items', [ $this, 'add_account_profile_fields_area_menu_item' ] );
		add_action( "woocommerce_account_{$this->endpoint}_endpoint", [ $this, 'output_profile_fields_area' ] );

		add_action( 'template_redirect', [ $this, 'update_profile_fields' ] );

		add_action( 'wp_footer', [ $this, 'output_incomplete_profile_message_html' ] );
	}


	/**
	 * Adds a My Account menu item for the Profile Fields Area.
	 *
	 * @internal
	 *
	 * @since 1.19.0-dev.
	 *
	 * @param array $items associative array of custom endpoints and endpoint labels
	 * @return array
	 */
	public function add_account_profile_fields_area_menu_item( $items ) {

		// we grab again the endpoint option even if not using permalinks, to check if it's emptied by the admin
		$profile_fields_area_endpoint = get_option( 'woocommerce_myaccount_profile_fields_area_endpoint', 'my-profile' );

		// add new endpoint if there is at least one profile field visible in the Profile Fields Area
		if ( ! empty( $profile_fields_area_endpoint ) && $this->get_profile_fields_for_user() ) {

			$endpoint       = wc_memberships_get_profile_fields_area_endpoint();
			$endpoint_title = esc_html( $this->get_profile_fields_area_endpoint_title() );

			if ( array_key_exists( 'orders', $items ) ) {
				$items = Framework\SV_WC_Helper::array_insert_after( $items, 'orders', array( $endpoint => $endpoint_title ) );
			} else {
				$items[ $endpoint ] = $endpoint_title;
			}
		}

		return $items;
	}


	/**
	 * Gets a list of Profile Fields that should be visible in the Profile Fields Area for the current user.
	 *
	 * Returns an empty list if there is not user logged in.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Field[]
	 */
	private function get_profile_fields_for_user() {

		$profile_fields = [];

		if ( $user_id = get_current_user_id() ) {

			$profile_field_definitions = Profile_Fields::get_profile_field_definitions( [
				'visibility'          => Profile_Fields::VISIBILITY_PROFILE_FIELDS_AREA,
				'editable_by'         => Profile_Field_Definition::EDITABLE_BY_CUSTOMER,
				'membership_plan_ids' => array_map(
					static function( $membership ) {
						return $membership->get_plan_id();
					},
					wc_memberships_get_user_memberships( $user_id )
				),
			] );

			$posted_data = (array) Framework\SV_WC_Helper::get_posted_value( 'member_profile_fields', [] );

			foreach ( $profile_field_definitions as $definition ) {

				$profile_field = Profile_Fields::get_profile_field( $user_id, $definition->get_slug() ) ?: new Profile_Field();

				$profile_field->set_user_id( $user_id );
				$profile_field->set_slug( $definition->get_slug() );

				if ( isset( $posted_data[ $profile_field->get_slug() ] ) ) {
					$profile_field->set_value( $posted_data[ $profile_field->get_slug() ] );
				}

				$profile_fields[] = $profile_field;
			}
		}

		return $profile_fields;
	}


	/**
	 * Gets the title for the Profile Fields Area endpoint.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	private function get_profile_fields_area_endpoint_title() {

		$endpoint_title = __( 'My Profile', 'woocommerce-memberships' );

		/**
		 * Filters the title for the Profile Fields Area endpoint.
		 *
		 * @since 1.19.0
		 *
		 * @param string $endpoint_title the endpoint title
		 */
		return (string) apply_filters( 'wc_memberships_my_account_memberships_title', $endpoint_title );
	}


	/**
	 * Checks if we are on the profile fields area endpoint.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_profile_fields_area() {
		global $wp_query;

		if ( $wp_query ) {
			if ( $this->using_permalinks ) {
				$is_endpoint_url = array_key_exists( $this->endpoint, $wp_query->query_vars ) || ! empty( $wp_query->query_vars[ $this->endpoint ] );
			} else {
				$is_endpoint_url = isset( $_GET[ $this->endpoint ] );
			}
		}

		return ! empty( $is_endpoint_url );
	}


	/**
	 * Renders the profile fields area content.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function output_profile_fields_area() {

		if ( ! $this->is_profile_fields_area() ) {
			return;
		}

		$profile_fields = $this->get_profile_fields_for_user();

		if ( ! $profile_fields ) {
			return;
		}

		wc_get_template( 'myaccount/my-profile-fields.php', [
			'profile_fields' => $profile_fields,
			'security'       => wp_create_nonce( 'update_profile_fields' ),
		] );
	}


	/**
	 * Saves the profile field values submitted from the My Profile page.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function update_profile_fields() {

		if ( ! Framework\SV_WC_Helper::get_posted_value( 'update_profile_fields' ) ) {
			return;
		}

		if ( ! wp_verify_nonce( Framework\SV_WC_Helper::get_posted_value( 'security' ), 'update_profile_fields' ) ) {
			Framework\SV_WC_Helper::wc_add_notice( __( 'Cannot update profile fields. Please try again.', 'woocommerce-memberships' ), 'error' );
			return;
		}

		$errors = [];

		foreach ( $this->get_profile_fields_for_user() as $profile_field ) {
			try {
				$profile_field->save();
			} catch ( Framework\SV_WC_Plugin_Exception $e ) {
				$errors[] = $e->getMessage();
			}
		}

		if ( $errors ) {
			Framework\SV_WC_Helper::wc_add_notice( __( 'Some profile fields had invalid or missing values and were not saved.', 'woocommerce-memberships' ), 'error' );
		} else {
			Framework\SV_WC_Helper::wc_add_notice( __( 'The profile fields have been saved.', 'woocommerce-memberships' ), 'success' );
		}

		foreach ( $errors as $error ) {
			Framework\SV_WC_Helper::wc_add_notice( $error, 'error' );
		}
	}


	/**
	 * Renders the user incomplete profile notice html.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function output_incomplete_profile_message_html() {

		if ( ! $this->should_show_incomplete_profile_message() ) {
			return;
		}

		$this->enqueue_frontend_banner_js();

		echo $this->get_incomplete_profile_message_html();
	}


	/**
	 * Determines whether we should show a message to encourage an user to fill incomplete profile fields.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	private function should_show_incomplete_profile_message() {

		$show_message = false;

		if ( 'no' !== get_user_meta( get_current_user_id(), '_wc_memberships_show_user_incomplete_profile_notice', true ) ) {

			foreach ( $this->get_profile_fields_for_user() as $profile_field ) {

				if ( $profile_field->validate()->get_error_message( 'required_value' ) ) {
					$show_message = true;
					break;
				}
			}
		}

		/**
		 * Filter whether we should show a message to encourage a user to fill incomplete profile fields.
		 *
		 * @since 1.19.0
		 *
		 * @param bool $display_message true if the user has required fields that need to be filled
		 */
		return (bool) apply_filters( 'wc_memberships_display_incomplete_profile_message', $show_message );
	}


	/**
	 * Enqueues the JS snippet used to dismiss frontend banners.
	 *
	 * @since 1.19.0
	 */
	private function enqueue_frontend_banner_js() {

		wc_enqueue_js( "
			jQuery( document ).ready( function( $ ) {

				$( 'div.wc-memberships.wc-memberships-frontend-banner a.dismiss-link' ).on( 'click', function ( e ) {

					e.preventDefault();

					var message_id = $( this ).closest( '.wc-memberships-frontend-banner' ).data( 'message-id' );

					if ( message_id ) {

						$.post( '" . esc_js( admin_url( 'admin-ajax.php' ) ) . "', {
							action: 'wc_memberships_dismiss_frontend_banner',
							message_id: message_id,
						} ).done( function() {
							location.reload();
						} );
					}
				} );
			} );
		" );
	}


	/**
	 * Gets the HTML content for the incomplete profile notice.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	private function get_incomplete_profile_message_html() {

		$text = sprintf(
			/* translators: Placeholders: %1$s - <strong>, %2$s - </strong>, %3$s - opening <a> tag, %4$s - closing </a> tag */
			esc_html__( '%1$sHey there%2$s, your profile is incomplete! %3$sPlease click here to complete it%4$s.', 'woocommerce-memberships' ),
			'<strong>',
			'</strong>',
			sprintf( '<a href="%1$s">', esc_url( wc_get_account_endpoint_url( $this->endpoint ) ) ),
			'</a>'
		);

		return '<div class="woocommerce wc-memberships wc-memberships-frontend-banner user-incomplete-profile-notice" data-message-id="user-incomplete-profile">' . wp_kses_post( $text ) . ' <a href="#" class="dismiss-link">' . __( 'Dismiss', 'woocommerce-memberships' ) . '</a></div>';
	}


}
