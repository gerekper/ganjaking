<?php
/**
 * Extra Product Options Data Store class
 *
 * @package Extra Product Options/Classes
 * @version 6.4.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Data Store class
 *
 * @package Extra Product Options/Classes
 * @version 6.4.1
 */
final class THEMECOMPLETE_EPO_Data_Store_Base {

	/**
	 * Custom data
	 * Settings data store
	 *
	 * @var array<mixed>
	 */
	public $data_store = [];

	/**
	 * Holds all of the plugin settings
	 *
	 * @var array<mixed>
	 */
	public $plugin_settings = [];

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Data_Store_Base|null
	 * @since 6.4.1
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 6.4.1
	 * @return THEMECOMPLETE_EPO_Data_Store_Base
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 6.4.1
	 */
	public function __construct() {
		$this->plugin_settings = THEMECOMPLETE_EPO_SETTINGS()->plugin_settings();
	}

	/**
	 * Initialize Data Store.
	 *
	 * @since 6.4.1
	 */
	public function init() {
		foreach ( apply_filters( 'wc_epo_get_settings', [] ) as $key => $value ) {
			$this->set_setting( $key, $value );
		}

		foreach ( $this->plugin_settings as $key => $value ) {
			if ( isset( $this->data_store[ $key ] ) ) {
				continue;
			}
			$this->set_setting( $key, $value );
		}

		THEMECOMPLETE_EPO()->upload_dir = $this->data_store['tm_epo_upload_folder'];
		THEMECOMPLETE_EPO()->upload_dir = str_replace( '/', '', THEMECOMPLETE_EPO()->upload_dir );
		THEMECOMPLETE_EPO()->upload_dir = sanitize_file_name( THEMECOMPLETE_EPO()->upload_dir );
		THEMECOMPLETE_EPO()->upload_dir = '/' . THEMECOMPLETE_EPO()->upload_dir . '/';

		if ( 'custom' === $this->data_store['tm_epo_options_placement'] ) {
			$this->data_store['tm_epo_options_placement'] = $this->data_store['tm_epo_options_placement_custom_hook'];
		}

		if ( 'custom' === $this->data_store['tm_epo_totals_box_placement'] ) {
			$this->data_store['tm_epo_totals_box_placement'] = $this->data_store['tm_epo_totals_box_placement_custom_hook'];
		}

		if ( THEMECOMPLETE_EPO()->is_quick_view() ) {
			$this->data_store['tm_epo_options_placement_hook_priority']    = 50;
			$this->data_store['tm_epo_totals_box_placement_hook_priority'] = 50;
			$this->data_store['tm_epo_options_placement']                  = 'woocommerce_before_add_to_cart_button';
			$this->data_store['tm_epo_totals_box_placement']               = 'woocommerce_before_add_to_cart_button';
		}

		// Backwards compatibility.
		$this->data_store['tm_epo_change_original_price'] = THEMECOMPLETE_EPO_SETTINGS()->get_compatibility_value( 'tm_epo_change_original_price', $this->data_store['tm_epo_change_original_price'] );
		$this->data_store['tm_epo_final_total_box']       = THEMECOMPLETE_EPO_SETTINGS()->get_compatibility_value( 'tm_epo_final_total_box', $this->data_store['tm_epo_final_total_box'] );

		if ( 'display' === $this->data_store['tm_epo_force_select_options'] ) {
			$this->data_store['tm_epo_force_select_options'] = 'yes';
		}
		if ( 'normal' === $this->data_store['tm_epo_force_select_options'] ) {
			$this->data_store['tm_epo_force_select_options'] = 'no';
		}

		if ( 'show' === $this->data_store['tm_epo_clear_cart_button'] ) {
			$this->data_store['tm_epo_clear_cart_button'] = 'yes';
		}
		if ( 'normal' === $this->data_store['tm_epo_clear_cart_button'] ) {
			$this->data_store['tm_epo_clear_cart_button'] = 'no';
		}

		if ( 'hide' === $this->data_store['tm_epo_hide_options_in_cart'] ) {
			$this->data_store['tm_epo_hide_options_in_cart'] = 'yes';
		}
		if ( 'normal' === $this->data_store['tm_epo_hide_options_in_cart'] ) {
			$this->data_store['tm_epo_hide_options_in_cart'] = 'no';
		}

		if ( 'hide' === $this->data_store['tm_epo_hide_options_prices_in_cart'] ) {
			$this->data_store['tm_epo_hide_options_prices_in_cart'] = 'yes';
		}
		if ( 'normal' === $this->data_store['tm_epo_hide_options_prices_in_cart'] ) {
			$this->data_store['tm_epo_hide_options_prices_in_cart'] = 'no';
		}

		if ( 'on' === $this->data_store['tm_epo_css_styles'] ) {
			$this->data_store['tm_epo_css_styles'] = 'yes';
		}
		if ( '' === $this->data_store['tm_epo_css_styles'] ) {
			$this->data_store['tm_epo_css_styles '] = 'no';
		}

		if ( '' !== $this->data_store['tm_epo_global_image_max_width'] || '' !== $this->data_store['tm_epo_global_image_max_height'] ) {
			$image_css = '.woocommerce #content table.cart img.epo-option-image, .woocommerce table.cart img.epo-option-image, .woocommerce-page #content table.cart img.epo-option-image, .woocommerce-page table.cart img.epo-option-image, .woocommerce-mini-cart .cpf-img-on-cart .epo-option-image, .woocommerce-checkout-review-order .cpf-img-on-cart .epo-option-image, .woocommerce-order-details .cpf-img-on-cart .epo-option-image, .epo-option-image, .cpf-img-on-order > * {';
			if ( '' !== $this->data_store['tm_epo_global_image_max_width'] ) {
				$image_css .= 'max-width: calc(' . $this->data_store['tm_epo_global_image_max_width'] . ' - var(--tcgap))  !important;';
			}
			if ( '' !== $this->data_store['tm_epo_global_image_max_height'] ) {
				$image_css .= 'max-height: ' . $this->data_store['tm_epo_global_image_max_height'] . ' !important;';
			}
			$image_css .= '}';
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $image_css );
		}

		$swatch_css = '';

		if ( '' !== $this->data_store['tm_epo_swatch_border_color'] ) {
			$swatch_css .= '--swatch-border-color:';
			$swatch_css .= $this->data_store['tm_epo_swatch_border_color'] . ';';
		}

		if ( '' !== $this->data_store['tm_epo_text_swatch_border_color'] ) {
			$swatch_css .= '--text-swatch-border-color:';
			$swatch_css .= $this->data_store['tm_epo_text_swatch_border_color'] . ';';
		}

		if ( '' !== $this->data_store['tm_epo_swatch_active_border_color'] ) {
			$swatch_css .= '--swatch-active-border-color:';
			$swatch_css .= $this->data_store['tm_epo_swatch_active_border_color'] . ';';
		}

		if ( '' !== $this->data_store['tm_epo_swatch_border_width'] ) {
			$swatch_css .= '--swatch-border-width:';
			$swatch_css .= $this->data_store['tm_epo_swatch_border_width'] . ';';
		}

		if ( '' !== $this->data_store['tm_epo_swatch_active_border_width'] ) {
			$swatch_css .= '--swatch-active-border-width:';
			$swatch_css .= $this->data_store['tm_epo_swatch_active_border_width'] . ';';
		}

		if ( '' !== $this->data_store['tm_epo_text_swatch_border_width'] ) {
			$swatch_css .= '--text-swatch-border-width:';
			$swatch_css .= $this->data_store['tm_epo_text_swatch_border_width'] . ';';
		}

		if ( '' !== $this->data_store['tm_epo_text_swatch_active_border_width'] ) {
			$swatch_css .= '--text-w:';
			$swatch_css .= $this->data_store['tm_epo_text_swatch_active_border_width'] . ';';
		}

		if ( '' !== $swatch_css ) {
			$swatch_css = 'body {' . $swatch_css . '}';
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $swatch_css );
		}

		// Ensure that option are visible for ajax mini cart.
		if ( THEMECOMPLETE_EPO()->wc_vars['is_ajax'] ) {
			$this->data_store['tm_epo_cart_field_display'] = 'normal';
		}

		if ( isset( $_REQUEST['tc-fullwidth'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->data_store['tm_epo_select_fullwidth'] = sanitize_text_field( wp_unslash( $_REQUEST['tc-fullwidth'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	/**
	 * Stores a setting key/value pair
	 *
	 * @param string $key The setting name.
	 * @param mixed  $value The setting value.
	 * @since 6.4.1
	 * @return void
	 */
	private function set_setting( $key, $value ) {
		if ( is_array( $value ) && 3 === count( $value ) ) {
			$method    = $value[2];
			$classname = $value[1];
			if ( is_object( $classname ) && call_user_func( [ $classname, $method ] ) ) {
				$this->data_store[ $key ] = get_option( $key );
				if ( false === $this->data_store[ $key ] ) {
					$this->data_store[ $key ] = $value[0];
				}
			} else {
				$this->data_store[ $key ] = get_option( $key );
				if ( false === $this->data_store[ $key ] ) {
					$this->data_store[ $key ] = $value;
				}
			}
		} else {
			$this->data_store[ $key ] = get_option( $key );
			if ( false === $this->data_store[ $key ] ) {
				$this->data_store[ $key ] = $value;
			}
		}
		$this->data_store[ $key ] = wp_unslash( $this->data_store[ $key ] );
	}

	/**
	 * Get the value of a setting
	 *
	 * @param string $key The setting name.
	 * @param mixed  $default_value The default value is the setting is not found.
	 * @since 6.4.1
	 * @return mixed
	 */
	public function get( $key = '', $default_value = '' ) {
		$value = $default_value;
		if ( '' !== $key && isset( $this->data_store[ $key ] ) ) {
			$value = $this->data_store[ $key ];
		}

		return $value;
	}

	/**
	 * Set the value of a setting
	 *
	 * @param string $key The setting name.
	 * @param mixed  $value The setting value.
	 * @since 6.4.1
	 * @return void
	 */
	public function set( $key = '', $value = '' ) {
		if ( '' !== $key ) {
			$this->data_store[ $key ] = $value;
		}
	}
}
