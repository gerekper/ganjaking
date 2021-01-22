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

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules\Display_Rule;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules\Display_Rule_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Abstract Add-On Base Class
 *
 * @since 2.0.0
 */
abstract class Add_On extends \WC_Data {


	/** @var string apply percentage adjustment on cart subtotal */
	const PERCENTAGE_ADJUSTMENT_SUBTOTAL = 'woocommerce_checkout_order_subtotal';
	/** @var string apply percentage adjustment on cart total */
	const PERCENTAGE_ADJUSTMENT_TOTAL = 'woocommerce_checkout_order_total';


	/** @var string ID for this add-on */
	protected $id = '';

	/** @var string the type of object -- used in action and filter names */
	protected $object_type = 'checkout_add_on';

	/** @var string the add-on type -- override in concrete classes */
	protected $add_on_type = '';

	/** @var string the add-on type name (add-on typed formatted for `view`) */
	protected $add_on_type_name = '';

	/** @var string the classname to use to instantiate this add-on */
	protected $add_on_classname = '';

	/** @var array the data for this add-on, with defaults */
	protected $data = array(
		'enabled'         => false,
		'name'            => '',
		'label'           => '',
		'description'     => '',
		'default_value'   => '',
		'adjustment'      => 0.0,
		'adjustment_type' => 'fixed',
		'is_taxable'      => false,
		'tax_class'       => '',
		'attributes'      => [],
		'rules'           => [],
	);


	/**
	 * Sets up the add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|array|string Add_On object, data array, or ID
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function __construct( $data = '' ) {

		parent::__construct( $data );

		$this->add_on_classname = get_class( $this );

		// Add_On is passed in
		if ( $data instanceof self ) {

			$this->set_id( $data->get_id() );

		// ID is passed in
		} elseif ( is_string( $data ) ) {

			$this->set_id( sanitize_text_field( $data ) );

		// data array is passed in
		} elseif ( is_array( $data ) ) {

			$this->set_props( $data );
			$this->set_object_read( true );

		// otherwise, assume it's a new object
		} else {

			$this->set_object_read( true );
		}

		$this->data_store = new Data_Store_Options();

		if ( $this->get_id() !== '' && ! $this->get_object_read() ) {
			$this->data_store->read( $this );
		}
	}


	/**
	 * Sets the ID for this add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param int $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}


	/**
	 * Returns all data for this object.
	 *
	 * @since  2.0.0
	 *
	 * @return array
	 */
	public function get_data() {

		return array_merge( parent::get_data(), array(
			'type'      => $this->add_on_type,
			'classname' => $this->add_on_classname,
		) );
	}


	/**
	 * Gets whether the add-on is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return bool
	 */
	public function get_enabled( $context = 'view' ) {

		return (bool) $this->get_prop( 'enabled', $context );
	}


	/**
	 * Sets whether this add-on is enabled or not.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $enabled
	 */
	public function set_enabled( $enabled ) {

		$this->set_prop( 'enabled', (bool) $enabled );
	}


	/**
	 * Enables this add-on -- convenience method.
	 *
	 * @since 2.0.0
	 */
	public function enable() {

		$this->set_enabled( true );
	}


	/**
	 * Disables this add-on -- convenience method.
	 *
	 * @since 2.0.0
	 */
	public function disable() {

		$this->set_enabled( false );
	}


	/**
	 * Gets the add-on type.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_type() {

		return $this->add_on_type;
	}


	/**
	 * Gets the add-on type name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_type_name() {

		return $this->add_on_type_name;
	}


	/**
	 * Gets the `name` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_name( $context = 'view' ) {

		$name = $this->get_prop( 'name', $context );

		if ( '' === $name && 'view' === $context ) {

			$name = __( '(no name)', 'woocommerce-checkout-add-ons' );
		}

		return $name;
	}


	/**
	 * Sets the `name` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_name( $value ) {

		$this->set_prop( 'name', $value );
	}


	/**
	 * Gets the `label` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_label( $context = 'view' ) {

		return $this->get_prop( 'label', $context );
	}


	/**
	 * Sets the `label` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_label( $value ) {

		$this->set_prop( 'label', $value );
	}


	/**
	 * Gets the `description` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_description( $context = 'view' ) {

		return $this->get_prop( 'description', $context );
	}


	/**
	 * Sets the `description` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_description( $value ) {

		$this->set_prop( 'description', $value );
	}


	/**
	 * Gets the `default_value` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_default_value( $context = 'view' ) {

		return $this->get_prop( 'default_value', $context );
	}


	/**
	 * Sets the `default_value` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_default_value( $value ) {

		$this->set_prop( 'default_value', $value );
	}


	/**
	 * Returns the add-on cost.
	 *
	 * Convenience / Compatibility method.
	 *
	 * @since 1.0
	 * @deprecated since 2.1.2
	 *
	 * @return mixed the add-on cost
	 */
	public function get_cost() {

		return $this->get_adjustment();
	}


	/**
	 * Returns the add-on cost (including tax).
	 *
	 * @since 1.0
	 *
	 * @param string|null $cost Optional. cost to calculate, leave blank to just use get_cost()
	 * @return mixed the add-on cost including any taxes
	 */
	public function get_cost_including_tax( $cost = null ) {

		$_tax  = new \WC_Tax();

		if ( null === $cost ) {
			$cost = $this->get_cost();
		}

		if ( $this->is_taxable() ) {

			// Get tax rates
			$tax_rates    = $_tax->get_rates( $this->get_tax_class() );
			$add_on_taxes = $_tax->calc_tax( $cost, $tax_rates, false );

			// add tax totals to the cost
			if ( ! empty( $add_on_taxes ) ) {
				$cost += array_sum( $add_on_taxes );
			}
		}

		return $cost;
	}


	/**
	 * Returns the cost in html format.
	 *
	 * Returns the formatted cost for the add-on, either with or
	 * without taxes, based on the `tax_display_cart` option.
	 *
	 * @since 2.0.0
	 *
	 * @param string|float $cost Optional. Cost to use (default: $this->get_cost())
	 * @param string $cost_type Optional. Whether the cost is flat or a percentage.
	 * @return string
	 */
	public function get_cost_html( $cost = null, $cost_type = null ) {

		if ( null === $cost ) {
			$cost = $this->get_cost();
		}

		if ( null === $cost_type ) {
			$cost_type = $this->get_cost_type();
		}

		// Be sure the cost is a number.
		if ( ! is_numeric( $cost ) ) {
			$cost = '';
		}

		// Calculate the percentage if necessary.
		if ( 'percent' === $cost_type ) {
			$cost = $this->calculate_percentage_adjustment( $cost );
		}

		$cost_html = '';

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '4.4' ) ) {
			$display_cost = 'incl' === WC()->cart->get_tax_price_display_mode() ? $this->get_cost_including_tax( $cost ) : $cost;
		} else {
			$display_cost = 'incl' === WC()->cart->tax_display_cart ? $this->get_cost_including_tax( $cost ) : $cost;
		}

		if ( $cost > 0 ) {

			$cost_html = wc_price( $display_cost );

			/**
			 * Filters the positive add-on cost html.
			 *
			 * @since 1.0
			 *
			 * @param string $cost_html The positive add-on cost html.
			 * @param Add_On $add_on This add-on class instance.
			 */
			$cost_html = apply_filters( 'wc_checkout_add_on_cost_html', $cost_html, $this );

		} elseif ( $cost === '' ) {

			/**
			 * Filters the empty add-on cost html.
			 *
			 * @since 1.0
			 *
			 * @param string $cost_html The empty add-on cost html.
			 * @param Add_On $add_on This add-on class instance.
			 */
			$cost_html = apply_filters( 'wc_checkout_add_on_empty_cost_html', '', $this );

		} elseif ( $cost === 0 ) {

			$cost_html = __( 'Free!', 'woocommerce-checkout-add-ons' );

			/**
			 * Filters the free add-on cost html.
			 *
			 * @since 1.0
			 *
			 * @param string $cost_html The free add-on cost html.
			 * @param Add_On $add_on This add-on class instance.
			 */
			$cost_html = apply_filters( 'woocommerce_free_cost_html', $cost_html, $this );

		} else if ( $cost < 0 ) {

			$cost_html = wc_price( $display_cost );

			/**
			 * Filters the negative add-on cost html.
			 *
			 * @since 1.6.1
			 *
			 * @param string $cost_html The negative add-on cost html.
			 * @param Add_On $add_on This add-on class instance.
			 */
			$cost_html = apply_filters( 'wc_checkout_add_on_negative_cost_html', $cost_html, $this );
		}

		/**
		 * Filters the add-on cost html.
		 *
		 * @since 1.0
		 *
		 * @param string $cost_html The add-on cost html.
		 * @param Add_On $add_on This add-on class instance.
		 */
		return apply_filters( 'wc_checkout_add_on_get_cost_html', $cost_html, $this );
	}


	/**
	 * Gets the `adjustment` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return float
	 */
	public function get_adjustment( $context = 'view' ) {

		return $this->get_prop( 'adjustment', $context );
	}


	/**
	 * Sets the `adjustment` property.
	 *
	 * @since 2.0.0
	 *
	 * @param float $value
	 */
	public function set_adjustment( $value ) {

		$this->set_prop( 'adjustment', $value );
	}


	/**
	 * Returns the add-on cost type.
	 *
	 * Convenience / Compatibility method.
	 *
	 * @since 1.6.0
	 *
	 * @return string $cost_type The add-on cost type
	 */
	public function get_cost_type() {

		return $this->get_adjustment_type();
	}


	/**
	 * Gets the `adjustment_type` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_adjustment_type( $context = 'view' ) {

		return $this->get_prop( 'adjustment_type', $context );
	}


	/**
	 * Sets the `adjustment_type` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_adjustment_type( $value ) {

		$this->set_prop( 'adjustment_type', $value );
	}


	/**
	 * Gets whether the add-on is required or not.
	 *
	 * Convenience/compatibility method.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_required() {

		return $this->has_attribute( 'required' );
	}


	/**
	 * Gets whether the add-on is taxable or not.
	 *
	 * Convenience/compatibility method.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_taxable() {

		return $this->get_is_taxable();
	}


	/**
	 * Gets the `is_taxable` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return bool
	 */
	public function get_is_taxable( $context = 'view' ) {

		// If taxes are disabled return false in any case
		if ( 'yes' !== get_option( 'woocommerce_calc_taxes' ) ) {
			return false;
		}

		return (bool) $this->get_prop( 'is_taxable', $context );
	}


	/**
	 * Sets the `is_taxable` property.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $value
	 */
	public function set_is_taxable( $value ) {

		$this->set_prop( 'is_taxable', (bool) $value );
	}


	/**
	 * Gets the `tax_class` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_tax_class( $context = 'view' ) {

		return $this->get_prop( 'tax_class', $context );
	}


	/**
	 * Sets the `tax_class` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_tax_class( $value ) {

		$this->set_prop( 'tax_class', $value );
	}


	/**
	 * Gets the value for a specific attribute.
	 *
	 * @since 2.0.0
	 *
	 * @param string $attribute the attribute key
	 * @param string $context the request context - `edit` or `view`
	 * @return mixed|null the attribute value, or null if not found
	 */
	public function has_attribute( $attribute, $context = 'view' ) {

		return in_array( $attribute, $this->get_attributes( $context ), true );
	}


	/**
	 * Returns whether this add-on supports a specific attribute.
	 *
	 * @since 2.0.0
	 *
	 * @param string $attribute the attribute key
	 * @return bool
	 */
	public function supports_attribute( $attribute ) {

		return in_array( $attribute, self::get_supported_attributes(), true );
	}


	/**
	 * Gets the `attributes` property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return array
	 */
	public function get_attributes( $context = 'view' ) {

		return array_intersect( $this->get_prop( 'attributes', $context ), self::get_supported_attributes() );
	}


	/**
	 * Sets the `attributes` property.
	 *
	 * @since 2.0.0
	 *
	 * @param array $value
	 */
	public function set_attributes( $value ) {

		$value = is_array( $value ) ? $value : array( $value );

		$this->set_prop( 'attributes', array_intersect( $value, self::get_supported_attributes() ) );
	}


	/**
	 * Gets the `rules` property.
	 *
	 * @since 2.1.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return array
	 */
	public function get_rules( $context = 'view' ) {

		return $this->get_prop( 'rules', $context );
	}


	/**
	 * Sets the `rules` property.
	 *
	 * @since 2.1.0
	 *
	 * @param array $value
	 */
	public function set_rules( $value ) {

		$this->set_prop( 'rules', $value );
	}


	/**
	 * Sanitizes an array of data to get it ready to be used in this add-on.
	 *
	 * Any data keys that are either not set in the input or are unset during
	 * sanitation will be filled with their default value when set on the object.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_data array of data to sanitize.
	 * @return array sanitized data
	 */
	public function sanitize_data( $raw_data = array() ) {

		// trash any data we don't have keys for
		$data = array_intersect_key( $raw_data, array_flip( $this->get_data_keys() ) );

		foreach ( $data as $key => $value ) {
			switch ( $key ) {

				case 'name':
				case 'label':
				case 'default_value':
					$data[ $key ] = sanitize_text_field( stripslashes( $value ) );
				break;

				case 'description':
					$data[ $key ] = wp_kses_post( stripslashes( $value ) );
				break;

				case 'adjustment':
					$data[ $key ] = (float) $value;
				break;

				case 'adjustment_type':

					$value = strtolower( trim( $value ) );

					if ( 'fixed' === $value || 'percent' === $value ) {
						$data[ $key ] = $value;
					} else {
						unset( $data[ $key ] );
					}

				break;

				case 'enabled':
				case 'is_required':
				case 'is_taxable':
					$data[ $key ] = is_string( $value ) ? 'yes' === strtolower( trim( $value ) ) : (bool) $value;
				break;

				case 'tax_class':

					$value = sanitize_title( $value );

					if ( in_array( $value, array_merge( \WC_Tax::get_tax_class_slugs(), array( 'standard' ) ), true ) ) {
						$data[ $key ] = $value;
					} else {
						unset( $data[ $key ] );
					}

				break;

				case 'attributes':

					$value   = is_array( $value ) ? $value : array( $value );
					$options = self::get_attribute_options();

					foreach ( $value as $attribute ) {

						if ( ! isset( $options[ $attribute ] ) ) {
							unset( $value[ $attribute ] );
						}
					}

					$data[ $key ] = $value;

				break;

				case 'rules':
					$data[ $key ] = $value;
				break;
			}
		}

		$extra_data = $this->sanitize_extra_data( $raw_data );

		return array_merge( $data, $extra_data );
	}


	/**
	 * Sanitizes extra data to get it ready to be used in this add-on.
	 *
	 * Override this method in concrete classes that utilize extra data.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_data the raw input data to sanitize
	 * @return array the sanitized extra data
	 */
	protected function sanitize_extra_data( $raw_data = array() ) {

		return array();
	}


	/**
	 * Gets values for the `attributes` field.
	 *
	 * @since 2.0.0
	 *
	 * @return array attribute_slug => attribute_name
	 */
	public static function get_attribute_options() {

		$add_on_attributes = array(
			'required'   => __( 'Required', 'woocommerce-checkout-add-ons' ),
			'listable'   => __( 'Display in View Orders screen', 'woocommerce-checkout-add-ons' ),
			'sortable'   => __( 'Allow Sorting on View Orders screen', 'woocommerce-checkout-add-ons' ),
			'filterable' => __( 'Allow Filtering on View Orders screen', 'woocommerce-checkout-add-ons' ),
		);

		if ( wc_checkout_add_ons()->is_subscriptions_active() ) {
			$add_on_attributes['subscriptions_renewable'] = __( 'Renew with Subscriptions', 'woocommerce-checkout-add-ons' );
		}

		/**
		 * Filters the valid add-on attributes.
		 *
		 * @since 1.0.0
		 *
		 * @param array $add_on_attributes The valid add-on attributes.
		 */
		return apply_filters( 'wc_checkout_add_ons_add_on_attributes', $add_on_attributes );
	}


	/**
	 * Gets an array of supported attribute keys.
	 *
	 * Allows concrete classes to mark some attributes as unsupported.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public static function get_supported_attributes() {

		return array_keys( self::get_attribute_options() );
	}


	/**
	 * Truncates the label for display.
	 *
	 * @since 1.0
	 *
	 * @param  string $label add-on label
	 * @return string truncated label
	 */
	public function truncate_label( $label ) {

		/**
		 * Filter the label length
		 *
		 * @since 1.0
		 *
		 * @param int $label_length The length of the truncated label.
		 */
		$label_length = apply_filters( 'wc_checkout_add_ons_add_on_label_length', 140 );

		/**
		 * Filter the label trim marker
		 *
		 * @since 1.0
		 *
		 * @param string $label_more The string that is added to the end of the label.
		 */
		$label_trimmarker = apply_filters( 'wc_checkout_add_ons_add_on_label_trimmarker', ' [&hellip;]');

		if ( $label_length < strlen( $label ) ) {
			$label = substr( $label, 0, $label_length ) . $label_trimmarker;
		}

		return $label;
	}


	/**
	 * Normalizes the add-on value.
	 *
	 * Provided the value(s), looks up the proper label(s) and returns them
	 * as a comma-separated string or an array.
	 *
	 * @since 1.0
	 *
	 * @param string|array $value sanitized key or array of keys
	 * @param bool $implode whether to glue labels together with commas
	 * @return mixed string|array label or array of labels matching the value
	 */
	public function normalize_value( $value, $implode ) {

		return is_array( $value ) && $implode ? implode( ', ', $value ) : $value;
	}


	/**
	 * Checks if the add-on has any options.
	 *
	 * @since 1.0
	 *
	 * @return bool true if the add-on has any options
	 */
	public function has_options() {

		return false;
	}


	/**
	 * Gets the key for this add-on.
	 *
	 * @since 2.0.0
	 *
	 * @return string add-on key
	 */
	public function get_key() {

		return $this->get_id();
	}


	/**
	 * Returns true if this add-on should be displayed in the Order admin list.
	 *
	 * @since 1.0
	 *
	 * @return bool true if the add-on should be displayed in the orders list
	 */
	public function is_listable() {

		return $this->has_attribute( 'listable' );
	}


	/**
	 * Returns true if this listable add-on is also sortable.
	 *
	 * @since 1.0
	 *
	 * @return bool true if the add-on should be sortable in the orders list
	 */
	public function is_sortable() {

		return $this->has_attribute( 'sortable' );
	}


	/**
	 * Returns true if this listable add-on is also filterable in the Orders admin.
	 *
	 * @since 1.0
	 *
	 * @return bool true if the add-on is both listable and filterable
	 */
	public function is_filterable() {

		return $this->has_attribute( 'filterable' );
	}


	/**
	 * Determines if this is a renewable add-on via Subscriptions.
	 *
	 * @since 1.7.1
	 *
	 * @return bool
	 */
	public function is_renewable() {

		return $this->has_attribute( 'subscriptions_renewable' );
	}


	/**
	 * Gets the defined ruleset.
	 *
	 * @since 2.1.0
	 *
	 * @return Display_Rule[]
	 */
	public function get_ruleset() {

		$values  = $this->get_rules();
		$ruleset = [];

		foreach ( array_keys( Display_Rule_Factory::get_display_rule_classnames() ) as $rule_type ) {

			$rule_data             = ! empty( $values[ $rule_type ] ) ? $values[ $rule_type ] : [];
			$rule_data['add_on']   = $this;
			$ruleset[ $rule_type ] = Display_Rule_Factory::create_display_rule( $rule_type, $rule_data );
		}

		return $ruleset;
	}


	/**
	 * Checks if the add-on should be displayed, based on the display rules.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function should_display() {

		$should_display = true;
		$rules          = $this->get_ruleset();

		foreach ( $rules as $rule ) {

			if ( ! $rule->evaluate() ) {
				$should_display = false;
				break;
			}
		}

		/**
		 * Filter to allow merchants to control when an add-on is displayed on the checkout page without being bound by
		 * the limitations of the plugin display rules.
		 *
		 * @since 2.3.1-dev.1
		 *
		 * @param bool $should_display true if the add-on will be displayed in the checkout page
		 * @param Add_On $add_on Add-On instance
		 */
		return (bool) apply_filters( 'wc_checkout_add_ons_should_display', $should_display, $this );
	}


	/**
	 * Calculates percentage adjustment.
	 *
	 * @since 2.3.0
	 *
	 * @param float $percentage adjustment percentage
	 * @return float
	 */
	public function calculate_percentage_adjustment( $percentage ) {

		$apply_percentage_on = get_option( 'woocommerce_checkout_add_ons_percentage_adjustment_from', Add_On::PERCENTAGE_ADJUSTMENT_SUBTOTAL );

		if ( Add_On::PERCENTAGE_ADJUSTMENT_SUBTOTAL === $apply_percentage_on ) {
			$cost = ( $percentage / 100 ) * WC()->cart->get_subtotal();
		} else {
			$cost = ( $percentage / 100 ) * WC()->cart->cart_contents_total;
		}

		return $cost;
	}


}
