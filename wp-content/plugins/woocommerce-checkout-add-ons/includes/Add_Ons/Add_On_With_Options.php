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

defined( 'ABSPATH' ) or exit;

/**
 * Abstract Class for Add-Ons with Options
 *
 * @since 2.0.0
 */
abstract class Add_On_With_Options extends Add_On {


	/** @var array extra data to add to the add-on data */
	protected $extra_data = array(
		'options' => array()
	);

	/** @var boolean have we run the add-on options filter already? */
	protected $has_run_add_on_options_filter = false;


	/**
	 * No-Op: Add-Ons with options store defaults in the options.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_default_value( $context = 'view' ) {

		return '';
	}


	/**
	 * No-Op: Add-Ons with options store defaults in the options.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_default_value( $value ) {}


	/**
	 * Checks if the add-on has any options.
	 *
	 * Checks if get_options() returns an array with at least
	 * one item. This ensures that other plugins can tap in and
	 * add options even if there are no manually configured options.
	 *
	 * @since 1.0
	 *
	 * @return bool true if the add-on has any options
	 */
	public function has_options() {

		return count( $this->get_options( 'edit' ) ) > 0;
	}


	/**
	 * Returns whether this add-on supports multiple defaults (e.g. multi-checkbox).
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function has_multiple_defaults() {

		return false;
	}


	/**
	 * Gets the options for this add-on.
	 *
	 * The `edit` context returns raw, unfiltered values.
	 * The `view` context returns values that have been allowed to be filtered.
	 * The `render` context allows child classes to do some action when rendering
	 * and then return the `view` context data.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit`, `view`, or `render`
	 * @return array
	 */
	public function get_options( $context = 'view' ) {

		// compat with older versions where the param was (bool) $filter and defaulted to `true`
		$context = false === $context ? 'edit' : $context;

		// allow child classes to use `render`, but set to `view` here
		$context = 'render' === $context ? 'view' : $context;

		// set to `edit` if filters have already run
		$context = 'view' === $context && ! $this->has_run_add_on_options_filter ? 'view' : 'edit';
		$options = $this->get_prop( 'options', $context );

		if ( 'view' === $context ) {
			// only run this filter once to avoid duplicate intensive operations
			$this->has_run_add_on_options_filter = true;
		}

		foreach ( $options as $key => $option ) {

			if ( $option['default'] ) {
				$options[ $key ]['selected'] = true;
			} else {
				$options[ $key ]['selected'] = false;
			}

			$options[ $key ]['cost_type'] = isset( $options[ $key ]['cost_type'] ) ? $options[ $key ]['cost_type'] : 'fixed';

			if ( 'view' === $context ) {

				$options[ $key ]['label'] = stripslashes( $options[ $key ]['label'] );

				/**
				 * Filters the individual option cost.
				 *
				 * @since 1.6.0
				 *
				 * @param float $cost the option cost
				 * @param array $option the option data
				 * @param Add_On $add_on the full add-on object
				 */
				$options[ $key ]['adjustment'] = apply_filters( 'wc_checkout_add_ons_add_on_option_cost', $options[ $key ]['adjustment'], $options[ $key ], $this );

				/**
				 * Filters the individual option cost type.
				 *
				 * @since 1.6.0
				 *
				 * @param float $cost the option cost type
				 * @param array $option the option data
				 * @param Add_On $add_on the full add-on object
				 */
				$options[ $key ]['adjustment_type'] = apply_filters( 'wc_checkout_add_ons_add_on_option_cost_type', $options[ $key ]['adjustment_type'], $options[ $key ], $this );
			}
		}

		return $options;
	}


	/**
	 * Sets the `options` property.
	 *
	 * @since 2.0.0
	 *
	 * @param array $options the options to set in format `option_value` => `option_name`
	 */
	public function set_options( array $options ) {

		$this->set_prop( 'options', $options );
	}


	/**
	 * Sanitizes extra data to get it ready to be used in this add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_data the raw input data to sanitize
	 * @return array the sanitized extra data
	 */
	protected function sanitize_extra_data( $raw_data = array() ) {

		// trash any data we don't have keys for
		$extra_data_keys   = $this->get_extra_data_keys();

		if ( ! $this::has_multiple_defaults() ) {
			$extra_data_keys[] = 'default_option';
		}

		$extra_data = array_intersect_key( $raw_data, array_flip( $extra_data_keys ) );

		if ( isset( $extra_data['options'] ) ) {

			$options        = is_array( $extra_data['options'] ) ? $extra_data['options'] : array();
			$default_option = ! $this::has_multiple_defaults() && isset( $extra_data['default_option'] ) ? (int) $extra_data['default_option'] : null;
			$new_options    = array();

			unset( $options['template'] );

			foreach ( $options as $index => $option ) {

				$new_option                    = array();
				$new_option['label']           = isset( $option['label'] ) ? wp_kses_post( $option['label'] ) : '';
				$new_option['adjustment']      = isset( $option['adjustment'] ) ? (float) $option['adjustment'] : 0.0;
				$new_option['adjustment_type'] = isset( $option['adjustment_type'] ) && 'percent' === $option['adjustment_type'] ? 'percent' : 'fixed';

				if ( $this::has_multiple_defaults() ) {

					$new_option['default'] = ( isset( $option['multi_default'] ) && '1' === $option['multi_default'] );

				} else {

					$new_option['default'] = (int) $index === $default_option;
				}

				$new_options[]                 = $new_option;
			}

			$extra_data['options'] = $new_options;
		}

		return $extra_data;
	}


}
