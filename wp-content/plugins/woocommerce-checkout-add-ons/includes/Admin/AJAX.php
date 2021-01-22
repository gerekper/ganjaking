<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin;

use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules\Display_Rule_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes\Add_On_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Data_Store_Options;

defined( 'ABSPATH' ) or exit;

/**
 * Admin AJAX class
 *
 * @since 2.0.0
 */
class AJAX {


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// reorder add-ons from admin listing
		add_action( 'wp_ajax_wc_checkout_add_ons_sort_add_ons', array( $this, 'sort_add_ons' ) );

		// enable/disable add-ons from admin listing
		add_action( 'wp_ajax_wc_checkout_add_ons_enable_disable_add_on', array( $this, 'enable_disable_add_ons' ) );

		// custom ajax handler for AJAX search
		add_action( 'wp_ajax_wc_checkout_add_ons_json_search_field', array( $this, 'add_json_search_field' ) );

		// save checkout add-ons value via ajax
		add_action( 'wp_ajax_wc_checkout_add_ons_save_order_items', array( $this, 'save_order_item_values_ajax' ) );

		// render conditional fields in other add-on display rule edit
		add_action( 'wp_ajax_wc_checkout_add_ons_other_add_on_fields', [ $this, 'render_other_add_on_display_rule_fields' ] );
	}


	/**
	 * Sorts add-ons from the admin list view.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function sort_add_ons() {

		check_admin_referer( 'wc-checkout-add-ons-list-sort', 'security' );

		if ( current_user_can( 'manage_woocommerce' ) ) {

			$order = isset( $_POST['order'] ) ? $_POST['order'] : null;

			if ( $order && is_array( $order ) && ! empty( $order ) ) {

				$order      = array_map( 'sanitize_text_field', $order );
				$data_store = new Data_Store_Options();

				$result = $data_store->reorder( $order );

				if ( $result ) {
					wp_send_json_success();
					exit;
				}
			}
		}

		wp_send_json_error( null, 500 );
		exit;
	}


	/**
	 * Enables or disables add-ons from the admin list view.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function enable_disable_add_ons() {

		check_admin_referer( 'wc-checkout-add-ons-list-enable-disable', 'security' );

		if ( current_user_can( 'manage_woocommerce' ) ) {

			$enabled = isset( $_POST['enabled'] ) ? wc_string_to_bool( $_POST['enabled'] ) : null;

			if ( null !== $enabled ) {

				$add_on_id = isset( $_POST['add_on'] ) ? sanitize_text_field( $_POST['add_on'] ) : '';
				$add_on    = '' !== $add_on_id ? Add_On_Factory::get_add_on( $add_on_id ) : null;

				$add_on->set_enabled( $enabled );

				if ( '' !== $add_on->save() ) {
					wp_send_json_success();
					exit;
				}
			}
		}

		wp_send_json_error( null, 500 );
		exit;
	}


	/**
	 * Handles search requests for enhanced multi-select fields.
	 *
	 * Searches for checkout add-ons and returns the results.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_json_search_field() {
		global $wpdb;

		check_ajax_referer( 'search-field', 'security' );

		// the search term
		$term = isset( $_GET['term'] ) ? urldecode( stripslashes( strip_tags( $_GET['term'] ) ) ) : '';

		// the field to search
		$id = isset( $_GET['request_data']['add_on_id'] ) ? urldecode( stripslashes( strip_tags( $_GET['request_data']['add_on_id'] ) ) ) : '';

		// required parameters
		if ( empty( $term ) || empty( $id ) ) {
			die;
		}

		$found_values = array();
		$query        = $wpdb->prepare( "
			SELECT woim_value.meta_value
			FROM {$wpdb->prefix}woocommerce_order_itemmeta woim_id
			JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim_value ON woim_id.order_item_id = woim_value.order_item_id
			WHERE 1=1
				AND woim_id.meta_key = '_wc_checkout_add_on_id'
				AND woim_id.meta_value = %d
				AND woim_value.meta_key = '_wc_checkout_add_on_value'
				AND woim_value.meta_value LIKE %s
		", $id, '%' . $term . '%' );

		$results = $wpdb->get_results( $query );

		if ( $results ) {
			foreach ( $results as $result ) {
				$found_values[ $result->meta_value ] = $result->meta_value;
			}
		}

		echo json_encode( $found_values );

		exit;
	}


	/**
	 * Saves checkout add-on values.
	 *
	 * @internal
	 *
	 * @since 1.2.0
	 */
	public static function save_order_item_values_ajax() {

		check_ajax_referer( 'save-checkout-add-ons', 'security' );

		if ( isset( $_POST['order_id'], $_POST['items'] ) ) {

			$order_id = absint( $_POST['order_id'] );

			// Parse the jQuery serialized items
			$items = array();
			parse_str( $_POST['items'], $items );

			// Save order items
			wc_checkout_add_ons()->save_order_item_values( $order_id, $items );
		}

		exit;
	}


	/**
	 * Renders conditional fields in Other Add-on display rule edit
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function render_other_add_on_display_rule_fields() {

		check_admin_referer( 'wc-checkout-add-ons-render-other-add-on-fields', 'security' );

		if ( current_user_can( 'manage_woocommerce' ) ) {

			$current_add_on_id = wc_clean( $_POST['current_add_on_id'] );
			$other_add_on_id   = wc_clean( $_POST['other_add_on_id'] );
			$operator          = ! empty( $_POST['operator'] ) ? wc_clean( $_POST['operator'] ) : null;

			$rule_data = [];
			$add_on    = Add_On_Factory::get_add_on( $current_add_on_id );

			if ( $add_on ) {

				$rules_data = $add_on->get_rules();
				$rule_data  = isset( $rules_data['other_add_on'] ) ? $rules_data['other_add_on'] : [];
			}

			$rule_data = array_merge( $rule_data, [
				'add_on'   => $add_on,
				'property' => $other_add_on_id,
				'operator' => $operator,
			] );

			$rule = Display_Rule_Factory::create_display_rule( 'other_add_on', $rule_data );

			$add_on_data = new Add_On_Data( $add_on );

			ob_start();

			$add_on_data->render_rule_logic( $rule );

			wp_send_json_success( ob_get_clean() );
			exit;
		}

		wp_send_json_error( null, 500 );
		exit;
	}


}
