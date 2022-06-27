<?php
/**
 * WC_CSP_Admin_Ajax class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product CSP Restrictions Admin Class.
 *
 * Loads admin tabs and adds related hooks / filters.
 *
 * @version  1.5.0
 */
class WC_CSP_Admin_Ajax {

	/*
	 * Setup admin class.
	 */
	public static function init() {

		// Ajax save config.
		add_action( 'wp_ajax_woocommerce_add_checkout_restriction', array( __CLASS__, 'add_checkout_restriction' ) );
		// Ajax toggle restriction.
		add_action( 'wp_ajax_woocommerce_toggle_restriction', array( __CLASS__, 'toggle_restriction' ) );
	}

	/**
	 * Handles toggling restrictions via Ajax.
	 *
	 * @since  1.4.0
	 *
	 * @return void
	 */
	public static function toggle_restriction() {

		check_ajax_referer( 'wc_restrictions_toggle_restriction', 'security' );

		if ( ! isset( $_POST[ 'restriction_id' ] ) || ! isset( $_POST[ 'value' ] ) || ! isset( $_POST[ 'index' ] ) || ! isset( $_POST[ 'post_id' ] ) || ! isset( $_POST[ 'hash' ] ) ) {

			wp_send_json( array(
				'hash'   => '',
				'errors' => array( __( 'Action failed. Please refresh your browser and try again.', 'woocommerce-conditional-shipping-and-payments' ) )
			) );
		}

		// Get POST data.
		$restriction_id = strval( stripslashes( $_POST[ 'restriction_id' ] ) ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$value          = intval( $_POST[ 'value' ] );
		$index          = intval( $_POST[ 'index' ] );
		$post_id        = intval( $_POST[ 'post_id' ] );
		$data_hash      = wc_clean( $_POST[ 'hash' ] );

		// Init containers.
		$errors   = array();
		$rules    = array();

		// Save flag.
		$should_update = true;

		if ( $is_global = empty( $post_id ) ) {

			// Global toggle.
			$restriction_data = WC_CSP()->restrictions->maybe_update_restriction_data( get_option( 'wccsp_restrictions_global_settings', false ), 'global' );

			// Limit to specific id.
			if ( isset( $restriction_data[ $restriction_id ] ) ) {
				$rules = $restriction_data[ $restriction_id ];
			}

		} else {

			// Product toggle.
			$product = wc_get_product( $post_id );

			// Get flat data.
			$rules = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) && $product ? $product->get_meta( '_wccsp_restrictions', true ) : get_post_meta( $post_id, '_wccsp_restrictions', true );
			$rules = WC_CSP()->restrictions->maybe_update_restriction_data( $rules, 'product' );

		}

		// Check dirty flag.
		$current_hash = md5( json_encode( $rules ) );

		if ( $current_hash !== $data_hash ) {
			$errors[] = __( 'Restriction data has been changed. Please refresh your browser and try again.', 'woocommerce-conditional-shipping-and-payments' );
		}

		// If no errors, proceed to change and save.
		if ( empty( $errors ) ) {

			// Toggle the active attribute if the index exists.
			if ( isset( $rules[ $index ] ) ) {

				// Backwards compatibilty.
				if ( ! isset( $rules[ $index ][ 'enabled' ] ) ) {
					// Revert the value because we initialize the key.
					$rules[ $index ][ 'enabled' ] = ( 'yes' === $value ) ? 'no' : 'yes';
				}

				$rules[ $index ][ 'enabled' ] = ( 'yes' === $rules[ $index ][ 'enabled' ] ) ? 'no' : 'yes';

			} else {
				// Bad request.
				$should_update = false;
			}

			if ( $should_update ) {

				// Save back to DB.
				if ( $is_global ) {

					// Replace data.
					$restriction_data[ $restriction_id ] = $rules;
					update_option( 'wccsp_restrictions_global_settings', $restriction_data );

				} else {

					$restriction_data = $rules;

					if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) && $product ) {

						$product->update_meta_data( '_wccsp_restrictions', $restriction_data );
						$product->save();

					} else {
						update_post_meta( $post_id, '_wccsp_restrictions', $restriction_data );
					}
				}

				// Clear cached shipping rates.
				WC_CSP_Core_Compatibility::clear_cached_shipping_rates();
			}
		}

		wp_send_json( array(
			'hash'   => ! empty( $rules ) ? md5( json_encode( $rules ) ) : '',
			'errors' => $errors
		) );
	}

	/**
	 * Handles adding restrictions via Ajax.
	 *
	 * @return void
	 */
	public static function add_checkout_restriction() {

		check_ajax_referer( 'wc_restrictions_add_restriction', 'security' );

		if ( ! isset( $_POST[ 'restriction_id' ] ) || ! isset( $_POST[ 'index' ] ) || ! isset( $_POST[ 'post_id' ] ) || ! isset( $_POST[ 'applied_count' ] ) || ! isset( $_POST[ 'count' ] ) ) {

			wp_send_json( array(
				'markup'   => '',
				'errors' => array( __( 'Action failed. Please refresh your browser and try again.', 'woocommerce-conditional-shipping-and-payments' ) )
			) );
		}

		$restriction_id = strval( stripslashes( $_POST[ 'restriction_id' ] ) ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$applied_count  = intval( $_POST[ 'applied_count' ] );
		$count          = intval( $_POST[ 'count' ] );
		$index          = intval( $_POST[ 'index' ] );
		$post_id        = intval( $_POST[ 'post_id' ] );

		$errors = array();

		ob_start();

		// Add if no rules exist, or if the restriction supports multiple definitions.
		if ( $applied_count === 0 || ( $applied_count > 0 && WC_CSP()->restrictions->get_restriction( $restriction_id )->supports_multiple() ) ) {

			if ( empty( $post_id ) ) {
				WC_CSP()->restrictions->get_restriction( $restriction_id )->get_admin_global_metaboxes_content( $index, array( 'index' => $count ), true );
			} else {
				WC_CSP()->restrictions->get_restriction( $restriction_id )->get_admin_product_metaboxes_content( $index, array( 'index' => $count ), true );
			}

		} else {
			$errors[] = __( 'This restriction is already defined and cannot be added again. Only restrictions that support multiple rule definitions can be added more than once.', 'woocommerce-conditional-shipping-and-payments' );
		}

		$output = ob_get_clean();

		wp_send_json( array(
			'markup' => $output,
			'errors' => $errors
		) );
	}
}

WC_CSP_Admin_Ajax::init();
