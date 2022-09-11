<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with Checkout Field Editor for WooCommerce.
 * Version tested: 1.4.2.
 *
 * @since 1.3
 */
class PLLWC_WCFD {
	/**
	 * Checkout Field Editor option names.
	 *
	 * @var array
	 */
	protected static $options = array(
		'wc_fields_additional',
		'wc_fields_billing',
		'wc_fields_shipping',
	);

	/**
	 * Constructor.
	 * Setups filters.
	 *
	 * @since 1.3
	 */
	public function __construct() {
		if ( PLL() instanceof PLL_Frontend ) {
			foreach ( self::$options as $option ) {
				add_filter( 'option_' . $option, array( $this, 'translate_fields' ) );
			}
		}

		if ( PLL() instanceof PLL_Settings ) {
			$this->register_strings();
			add_filter( 'pll_sanitize_string_translation', array( $this, 'sanitize_strings' ), 10, 3 );
		}
	}

	/**
	 * Translates the fields label and placeholder.
	 *
	 * @since 1.3
	 *
	 * @param array $fields List of fields.
	 * @return array
	 */
	public function translate_fields( $fields ) {
		if ( is_array( $fields ) ) {
			foreach ( $fields as $name => $field ) {
				if ( ! empty( $field['label'] ) ) {
					$fields[ $name ]['label'] = pll__( $field['label'] );
				}

				if ( ! empty( $field['placeholder'] ) ) {
					$fields[ $name ]['placeholder'] = pll__( $field['placeholder'] );
				}
			}
		}

		return $fields;
	}

	/**
	 * Registers the strings.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	protected function register_strings() {
		foreach ( self::$options as $option ) {
			$fields = get_option( $option );

			if ( is_array( $fields ) ) {
				foreach ( $fields as $name => $field ) {
					if ( ! empty( $field['label'] ) ) {
						pll_register_string( sprintf( __( 'Label', 'polylang-wc' ), $name ), $field['label'], 'Checkout Field Editor' );
					}

					if ( ! empty( $field['placeholder'] ) ) {
						pll_register_string( sprintf( __( 'Placeholder', 'polylang-wc' ), $name ), $field['placeholder'], 'Checkout Field Editor' );
					}
				}
			}
		}
	}

	/**
	 * Translated strings must be sanitized the same way Checkout Field Editor for WooCommerce does before they are saved
	 *
	 * @since 1.3
	 *
	 * @param string $translation The string translation.
	 * @param string $name        The name as defined in pll_register_string.
	 * @param string $context     The context as defined in pll_register_string.
	 * @return string Sanitized translation.
	 */
	public function sanitize_strings( $translation, $name, $context ) {
		if ( 'Checkout Field Editor' === $context ) {
			if ( __( 'Label', 'polylang-wc' ) === $name ) {
				$translation = wp_kses_post( trim( $translation ) );
			}

			if ( __( 'Placeholder', 'polylang-wc' ) === $name ) {
				$translation = wc_clean( $translation );
			}
		}

		return $translation;
	}
}
