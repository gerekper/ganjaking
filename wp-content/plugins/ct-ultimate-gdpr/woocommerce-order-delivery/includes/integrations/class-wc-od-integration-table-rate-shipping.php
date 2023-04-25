<?php
/**
 * Integration: Table Rate Shipping.
 *
 * @package WC_OD\Integrations
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Integration_Table_Rate_Shipping.
 */
class WC_OD_Integration_Table_Rate_Shipping implements WC_OD_Integration {

	/**
	 * Init.
	 *
	 * @since 2.2.0
	 */
	public static function init() {
		add_filter( 'wc_od_table_rate_shipping_method_has_rates', '__return_true' );
		add_filter( 'wc_od_table_rate_shipping_method_rate_ids', array( __CLASS__, 'filter_table_rate_ids' ), 10, 2 );
		add_filter( 'wc_od_table_rate_shipping_method_rate_label', array( __CLASS__, 'filter_table_rate_label' ), 10, 3 );
		add_filter( 'wc_od_order_shipping_method_value', array( __CLASS__, 'order_shipping_method_value' ), 10, 2 );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php';
	}

	/**
	 * Filters the rate IDs of a table rate shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param array              $rate_ids        An array with the rate IDs.
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @return array
	 */
	public static function filter_table_rate_ids( $rate_ids, $shipping_method ) {
		return self::get_rate_ids( $shipping_method );
	}

	/**
	 * Filters the label of the shipping method rate.
	 *
	 * @since 2.2.0
	 *
	 * @param string             $label           The option label.
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @param string             $rate_id         Rate id.
	 * @return string
	 */
	public static function filter_table_rate_label( $label, $shipping_method, $rate_id ) {
		if ( ! $rate_id ) {
			return $label;
		}

		$rate = self::get_rate_by_id( $shipping_method, $rate_id );

		if ( $rate ) {
			/* translators: %d: Rate ID of the table rate shipping */
			$label = ( ! empty( $rate['rate_label'] ) ? $rate['rate_label'] : sprintf( _x( 'Rate %d', 'table rate shipping label', 'woocommerce-order-delivery' ), $rate_id ) );
		}

		return $label;
	}

	/**
	 * Filters the value of the order shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param string                 $value         The shipping method value.
	 * @param WC_Order_Item_Shipping $shipping_item Order item shipping object.
	 * @return string
	 */
	public static function order_shipping_method_value( $value, $shipping_item ) {
		if ( 'table_rate' !== $shipping_item->get_method_id() ) {
			return $value;
		}

		$shipping_method = WC_Shipping_Zones::get_shipping_method( $shipping_item->get_instance_id() );

		/*
		 * Look for the 'rate_id' of the 'table_rate' shipping method.
		 * This info is not stored in the 'WC_Order_Item_Shipping' object metadata.
		 */
		$rate = self::get_rate_by_field( $shipping_method, 'rate_label', $shipping_item->get_method_title() );

		if ( ! empty( $rate ) && ! empty( $rate['rate_id'] ) ) {
			$value .= ":{$rate['rate_id']}";
		}

		return $value;
	}

	/**
	 * Gets the rates for the specified table rate shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @return array|bool An array with the rates. False on failure.
	 */
	public static function get_rates( $shipping_method ) {
		if ( ! $shipping_method instanceof WC_Shipping_Table_Rate ) {
			return false;
		}

		return $shipping_method->get_normalized_shipping_rates();
	}

	/**
	 * Gets the rate IDs of a table rate shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @return array
	 */
	public static function get_rate_ids( $shipping_method ) {
		$values = array();
		$rates  = self::get_rates( $shipping_method );

		if ( is_array( $rates ) ) {
			$values = wp_list_pluck( $rates, 'rate_id' );
		}

		return $values;
	}

	/**
	 * Gets the rate of a table rate shipping method by ID.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @param string             $rate_id         Rate id.
	 * @return array|bool An array with the rate data. False on failure.
	 */
	public static function get_rate_by_id( $shipping_method, $rate_id ) {
		return self::get_rate_by_field( $shipping_method, 'rate_id', $rate_id );
	}

	/**
	 * Gets the rate of a table rate shipping method by field.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @param string             $field           The field key.
	 * @param mixed              $value           The field value.
	 * @return array|bool An array with the rate data. False on failure.
	 */
	public static function get_rate_by_field( $shipping_method, $field, $value ) {
		$rates = self::get_rates( $shipping_method );

		if ( ! empty( $rates ) ) {
			foreach ( $rates as $rate ) {
				if ( isset( $rate[ $field ] ) && $value === $rate[ $field ] ) {
					return $rate;
				}
			}
		}

		return false;
	}
}
