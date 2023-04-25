<?php
/**
 * Class to handle the shipping methods select field.
 *
 * @package WC_OD
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shipping methods selector class.
 */
class WC_OD_Shipping_Methods_Selector {

	/**
	 * Gets the shipping methods options for a select field.
	 *
	 * @since 2.2.0
	 *
	 * @return array
	 */
	public static function get_options() {
		$options = array();

		$zones = WC_OD_Shipping_Zones::get_zones();

		// Add the default shipping zone.
		$zones[0] = WC_OD_Shipping_Zones::get_zone( 0 );

		foreach ( $zones as $zone ) {
			$methods = $zone->get_shipping_methods( true );

			// Skip zones without shipping methods.
			if ( empty( $methods ) ) {
				continue;
			}

			// Add the shipping zone.
			$zone_value             = self::get_option_value_for_zone( $zone );
			$options[ $zone_value ] = self::get_option_label_for_zone( $zone );

			// Add the shipping methods of the current zone.
			foreach ( $methods as $method ) {
				$method_value             = self::get_option_value_for_method( $method );
				$options[ $method_value ] = self::indent_label( self::get_option_label_for_method( $method ), 3 );

				// Add the shipping method rates if they exist.
				$rate_ids = wc_od_get_shipping_method_rate_ids( $method );

				foreach ( $rate_ids as $rate_id ) {
					$rate_value             = self::get_option_value_for_rate( $method, $rate_id );
					$options[ $rate_value ] = self::indent_label( self::get_option_label_for_method( $method, $rate_id ), 6 );
				}
			}
		}

		return $options;
	}

	/**
	 * Gets the option value for the specified shipping zone.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Zone $shipping_zone Shipping zone object.
	 * @return string
	 */
	public static function get_option_value_for_zone( $shipping_zone ) {
		$value = "zone:{$shipping_zone->get_id()}";

		/**
		 * Filters the option value of the shipping zone.
		 *
		 * @since 2.2.0
		 *
		 * @param string           $value         The option value.
		 * @param WC_Shipping_Zone $shipping_zone Shipping zone object.
		 */
		return apply_filters( 'wc_od_shipping_zone_option_value', $value, $shipping_zone );
	}

	/**
	 * Gets the option value for the specified shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @return string
	 */
	public static function get_option_value_for_method( $shipping_method ) {
		$value = ( $shipping_method->id . ':' . $shipping_method->get_instance_id() );

		/**
		 * Filters the option value of the shipping method.
		 *
		 * @since 2.2.0
		 *
		 * @param string             $value           The option value.
		 * @param WC_Shipping_Method $shipping_method Shipping method object.
		 */
		return apply_filters( 'wc_od_shipping_method_option_value', $value, $shipping_method );
	}

	/**
	 * Gets the option value for the specified shipping method rate.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @param string             $rate_id         Rate id.
	 * @return string
	 */
	public static function get_option_value_for_rate( $shipping_method, $rate_id ) {
		$value = self::get_option_value_for_method( $shipping_method ) . ':' . $rate_id;

		/**
		 * Filters the option value of the shipping method rate.
		 *
		 * @since 2.2.0
		 *
		 * @param string             $value           The option value.
		 * @param WC_Shipping_Method $shipping_method Shipping method object.
		 * @param string             $rate_id         Rate id.
		 */
		return apply_filters( 'wc_od_shipping_method_rate_option_value', $value, $shipping_method, $rate_id );
	}

	/**
	 * Gets the label for the specified option value.
	 *
	 * @since 2.2.0
	 *
	 * @param string $value The option value.
	 * @return string
	 */
	public static function get_option_label( $value ) {
		$parts = explode( ':', $value );

		if ( 2 > count( $parts ) ) {
			return '';
		}

		// Get cached result.
		$cache_key = 'option_label_' . join( '_', $parts );
		$label     = wp_cache_get( $cache_key, 'wc_od_shipping_methods' );

		if ( false !== $label ) {
			return $label;
		}

		if ( 'zone' === $parts[0] ) {
			$label = self::get_option_label_for_zone( $parts[1] );
		} else {
			$rate_id = ( isset( $parts[2] ) ? $parts[2] : 0 );
			$label   = self::get_option_label_for_method( $parts[1], $rate_id );
		}

		// Cache the result.
		wp_cache_set( $cache_key, $label, 'wc_od_shipping_methods' );

		return $label;
	}

	/**
	 * Gets the option label for the specified shipping zone.
	 *
	 * @since 2.2.0
	 *
	 * @param mixed $the_zone Shipping Zone object or ID.
	 * @return string
	 */
	public static function get_option_label_for_zone( $the_zone ) {
		$zone = WC_OD_Shipping_Zones::get_zone( $the_zone );

		if ( ! $zone ) {
			return '';
		}

		return self::get_zone_label( $zone ) . ': ' . __( 'All shipping methods', 'woocommerce-order-delivery' );
	}

	/**
	 * Gets the option label for the specified shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param mixed  $the_method Shipping method object or ID.
	 * @param string $rate_id    Optional. Rate id. Default 0.
	 * @return string
	 */
	public static function get_option_label_for_method( $the_method, $rate_id = 0 ) {
		$method = wc_od_get_shipping_method( $the_method );

		if ( ! $method ) {
			return '';
		}

		$zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $method->get_instance_id() );

		if ( ! $zone ) {
			return '';
		}

		$label = ( self::get_zone_label( $zone ) . ' — ' . self::get_method_label( $method ) );

		if ( wc_od_shipping_method_has_rates( $method ) ) {
			if ( $rate_id ) {
				$label .= ' - ' . self::get_rate_label( $method, $rate_id );
			} else {
				$label .= ': ' . __( 'All rates', 'woocommerce-order-delivery' );
			}
		}

		return $label;
	}

	/**
	 * Gets the label for the specified shipping zone.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Zone $shipping_zone Shipping zone object.
	 * @return string
	 */
	public static function get_zone_label( $shipping_zone ) {
		return ( $shipping_zone->get_id() ? $shipping_zone->get_zone_name() : _x( 'Other locations', 'label for the default shipping zone', 'woocommerce-order-delivery' ) );
	}

	/**
	 * Gets the label for the specified shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @return string
	 */
	public static function get_method_label( $shipping_method ) {
		$title = $shipping_method->get_title();

		if ( ! $title ) {
			$title = $shipping_method->get_method_title();
		}

		return $title;
	}

	/**
	 * Gets the label for the specified shipping method rate.
	 *
	 * @since 2.2.0
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 * @param string             $rate_id         Rate id.
	 * @return string
	 */
	public static function get_rate_label( $shipping_method, $rate_id ) {
		/**
		 * Filters the label of the shipping method rate.
		 *
		 * The dynamic portion of the hook name refers to the shipping method ID.
		 *
		 * @since 2.2.0
		 *
		 * @param string             $label           The option label.
		 * @param WC_Shipping_Method $shipping_method Shipping method object.
		 * @param string             $rate_id         Rate id.
		 */
		return apply_filters( "wc_od_{$shipping_method->id}_shipping_method_rate_label", '', $shipping_method, $rate_id );
	}

	/**
	 * Adds white spaces at the beginning of the label.
	 *
	 * @since 2.2.0
	 *
	 * @param string $label  The label to indent.
	 * @param int    $length The number of characters to indent with.
	 * @return string
	 */
	protected static function indent_label( $label, $length ) {
		$indent = str_pad( '', $length * 6, '&nbsp;' );

		return $indent . $label;
	}
}
