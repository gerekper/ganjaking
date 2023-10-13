<?php

use WCML\Compatibility\WcCheckoutAddons\OptionIterator;

/**
 * Compatibility class for  wc_checkout_addons plugin.
 *
 * @author konrad
 */
class WCML_Checkout_Addons implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'option_wc_checkout_add_ons', [ $this, 'option_wc_checkout_add_ons' ] );
	}

	/**
	 * @param array|mixed $option_value
	 *
	 * @return array|mixed
	 */
	public function option_wc_checkout_add_ons( $option_value ) {
		return OptionIterator::apply( [ $this, 'handle_option_part' ], $option_value );
	}

	public function handle_option_part( $index, $conf ) {
		$conf = $this->register_or_translate( 'label', $conf, $index );
		$conf = $this->register_or_translate( 'description', $conf, $index );
		return $conf;
	}

	private function register_or_translate( $element, $conf, $index ) {
		if ( isset( $conf[ $element ] ) ) {
			$string = $conf[ $element ];
			$key    = $index . '_' . $element . '_' . md5( $string );
			if ( $this->is_default_language() ) {
				do_action( 'wpml_register_single_string', 'wc_checkout_addons', $key, $string );
			} else {
				$conf[ $element ] = apply_filters( 'wpml_translate_single_string', $string, 'wc_checkout_addons', $key );
			}
		}
		return $conf;
	}

	private function is_default_language() {
		return apply_filters( 'wpml_current_language', null ) === apply_filters( 'wpml_default_language', null );
	}
}
