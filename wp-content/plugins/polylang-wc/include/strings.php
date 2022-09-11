<?php
/**
 * @package Polylang-WC
 */

/**
 * Manage the strings translations.
 *
 * @since 0.1
 */
class PLLWC_Strings {

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		// Translate strings in emails.
		add_action( 'pllwc_email_language', array( $this, 'translate_emails' ) );

		if ( PLL() instanceof PLL_Frontend ) {
			// Translate strings on frontend.
			add_action( 'init', array( $this, 'translate_strings' ) );
		} else {
			if ( PLL() instanceof PLL_Settings ) {
				// Register strings.
				add_action( 'init', array( $this, 'register_strings' ), 99 ); // Priority 99 in case gateways are registered in the same hook. See WooCommerce Invoice Gateway.
				add_filter( 'pll_sanitize_string_translation', array( $this, 'sanitize_strings' ), 10, 3 );
			}

			add_filter( 'woocommerce_attribute_label', array( $this, 'attribute_label' ), 10, 3 );
		}

		// Register strings.
		if ( PLL() instanceof PLL_Settings ) {
			add_filter( 'pll_sanitize_string_translation', array( $this, 'sanitize_strings' ), 10, 3 );
		}

		// Translate strings in emails.
		add_action( 'pllwc_email_language', array( $this, 'translate_emails' ) );
	}

	/**
	 * Returns the options to translate.
	 * Not called before the action 'init' to avoid loading WooCommerce translations sooner than WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	protected static function get_options() {
		return array(
			'email_footer_text'                           => array( 'name' => __( 'Footer text', 'polylang-wc' ), 'multiline' => true ),
			'demo_store_notice'                           => array( 'name' => __( 'Store notice text', 'polylang-wc' ), 'multiline' => true ),
			'price_display_suffix'                        => array( 'name' => 'price_display_suffix' ),
			'currency_pos'                                => array( 'name' => __( 'Currency position', 'polylang-wc' ) ),
			'price_thousand_sep'                          => array( 'name' => __( 'Thousand separator', 'polylang-wc' ) ),
			'price_decimal_sep'                           => array( 'name' => __( 'Decimal separator', 'polylang-wc' ) ),
			'registration_privacy_policy_text'            => array( 'name' => __( 'Registration privacy policy', 'polylang-wc' ), 'multiline' => true ),
			'checkout_privacy_policy_text'                => array( 'name' => __( 'Checkout privacy policy', 'polylang-wc' ), 'multiline' => true ),
			'checkout_terms_and_conditions_checkbox_text' => array( 'name' => __( 'Terms and conditions', 'polylang-wc' ) ),
			'email_from_name'                             => array( 'name' => 'email_from_name' ),
			'email_from_address'                          => array( 'name' => 'email_from_address' ),
		);
	}

	/**
	 * Tests whether an email property should be translated.
	 *
	 * @since 0.1
	 *
	 * @param string $prop Property name.
	 * @return bool
	 */
	protected function is_translated_email_property( $prop ) {
		return 0 === strpos( $prop, 'subject' ) || 0 === strpos( $prop, 'heading' ) || 0 === strpos( $prop, 'additional_content' );
	}

	/**
	 * Tests whether a gateway property should be translated.
	 * Verifies that the property has been saved in database.
	 *
	 * @since 0.1
	 * @since 0.9 Add $gateway parameter
	 *
	 * @param string             $prop    Property name.
	 * @param WC_Payment_Gateway $gateway WC_Payment_Gateway object.
	 * @return bool
	 */
	protected function is_translated_gateway_property( $prop, $gateway = null ) {
		if ( empty( $gateway ) ) {
			return in_array( $prop, array( 'title', 'description', 'instructions' ) );
		} else {
			$settings = get_option( 'woocommerce_' . $gateway->id . '_settings' );
			return in_array( $prop, array( 'title', 'description', 'instructions' ) ) && ! empty( $settings[ $prop ] );
		}
	}

	/**
	 * Tests whether a shipping property should be translated.
	 *
	 * @since 0.1
	 *
	 * @param string $prop Property name.
	 * @return bool
	 */
	protected function is_translated_shipping_property( $prop ) {
		return 'title' === $prop;
	}

	/**
	 * Tests whether an email property input field should be multiline.
	 *
	 * @since 1.5.5
	 *
	 * @param string $prop Property name.
	 * @return bool
	 */
	protected function is_email_property_multiline( $prop ) {
		return 'additional_content' === $prop;
	}

	/**
	 * Tests whether a gateway property input field should be multiline.
	 *
	 * @since 0.1
	 *
	 * @param string $prop Property name.
	 * @return bool
	 */
	protected function is_gateway_property_multiline( $prop ) {
		return 'title' !== $prop;
	}

	/**
	 * Register sub strings.
	 *
	 * @since 0.1
	 *
	 * @param WC_Settings_API[] $objects          Array of objects having properties to translate.
	 * @param callable          $is_translated_cb Function testing if a property should be translated.
	 * @param callable          $is_multiline_cb  Function testing if the input field should be multiline (default to false).
	 * @return void
	 */
	protected function register_sub_options( $objects, $is_translated_cb, $is_multiline_cb = '__return_false' ) {
		foreach ( $objects as $obj ) {
			if ( ! isset( $obj->enabled ) || 'no' !== $obj->enabled ) {
				foreach ( array_keys( $obj->form_fields ) as $prop ) {
					if ( call_user_func( $is_translated_cb, $prop, $obj ) ) {
						if ( ! empty( $obj->settings[ $prop ] ) ) {
							pll_register_string( $prop . '_' . $obj->id, $obj->settings[ $prop ], 'WooCommerce', call_user_func( $is_multiline_cb, $prop ) );
						} elseif ( ! empty( $obj->$prop ) ) {
							pll_register_string( $prop . '_' . $obj->id, $obj->$prop, 'WooCommerce', call_user_func( $is_multiline_cb, $prop ) );
						}
					}
				}
			}
		}
	}

	/**
	 * Registers strings
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function register_strings() {
		global $wpdb;

		// Emails.
		$this->register_sub_options(
			WC_Emails::instance()->get_emails(),
			array( $this, 'is_translated_email_property' ),
			array( $this, 'is_email_property_multiline' )
		);

		// Gateways.
		$this->register_sub_options(
			WC_Payment_Gateways::instance()->payment_gateways(),
			array( $this, 'is_translated_gateway_property' ),
			array( $this, 'is_gateway_property_multiline' )
		);

		// BACS Account details.
		$woocommerce_bacs_accounts = get_option( 'woocommerce_bacs_accounts', array() );
		if ( is_array( $woocommerce_bacs_accounts ) ) {
			foreach ( $woocommerce_bacs_accounts as $account ) {
				pll_register_string( __( 'Account name', 'polylang-wc' ), $account['account_name'], 'WooCommerce' );
				pll_register_string( __( 'Bank name', 'polylang-wc' ), $account['bank_name'], 'WooCommerce' );
			}
		}

		// Shipping methods (backward compatibility with WC < 2.6 - maybe kept in WC 2.6+ for sites not using shipping zones?).
		$this->register_sub_options(
			WC_Shipping::instance()->get_shipping_methods(),
			array( $this, 'is_translated_shipping_property' )
		);

		// Shipping methods in shipping zones.
		$zone = new WC_Shipping_Zone( 0 ); // Rest of the the world.
		foreach ( $zone->get_shipping_methods() as $method ) {
			pll_register_string( 'title_0_' . $method->id, $method->title, 'WooCommerce' );
		}

		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			foreach ( $zone['shipping_methods'] as $method ) {
				pll_register_string( 'title_' . $zone['zone_id'] . '_' . $method->id, $method->title, 'WooCommerce' );
			}
		}

		// Strings as single option.
		foreach ( self::get_options() as $string => $arr ) {
			if ( $option = get_option( 'woocommerce_' . $string ) ) {
				pll_register_string( $arr['name'], $option, 'WooCommerce', ! empty( $arr['multiline'] ) );
			}
		}

		// Attributes labels.
		foreach ( wc_get_attribute_taxonomies() as $attr ) {
			pll_register_string( __( 'Attribute', 'polylang-wc' ), $attr->attribute_label, 'WooCommerce' );
		}

		// Tax rate labels.
		$labels = $wpdb->get_col( "SELECT tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates" );
		foreach ( $labels as $label ) {
			pll_register_string( __( 'Tax name', 'polylang-wc' ), $label, 'WooCommerce' );
		}
	}

	/**
	 * Sanitizes translated strings.
	 * This is done the same way WooCommerce does before they are saved.
	 *
	 * @since 0.1
	 *
	 * @param string $translation The string translation.
	 * @param string $name        The name as defined in pll_register_string.
	 * @param string $context     The context as defined in pll_register_string.
	 * @return string sanitized translation
	 */
	public function sanitize_strings( $translation, $name, $context ) {
		if ( 'WooCommerce' === $context ) {
			// Options.
			$is_text_field = in_array( $name, wp_list_pluck( self::get_options(), 'name' ) ) ||
				$this->is_translated_email_property( $name ) ||
				$this->is_translated_gateway_property( $name ) ||
				$this->is_translated_shipping_property( $name );

			if ( $is_text_field ) {
				$translation = wp_kses_post( trim( $translation ) );
			}

			// Account details.
			if ( __( 'Account name', 'polylang-wc' ) === $name || __( 'Bank name', 'polylang-wc' ) === $name ) {
				$translation = wc_clean( $translation );
			}

			if ( __( 'Currency position', 'polylang-wc' ) === $name && ! in_array( $translation, array( 'left', 'right', 'left_space', 'right_space' ) ) ) {
				$translation = get_option( 'woocommerce_currency_pos', 'left' );
			}

			// Attributes labels.
			if ( __( 'Attribute', 'polylang-wc' ) === $name ) {
				$translation = wc_clean( $translation );
			}

			// email address.
			if ( 'email_from_address' === $name ) {
				$translation = sanitize_email( $translation );
			}
		}
		return $translation;
	}

	/**
	 * Setups actions and filters to translate strings.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function translate_strings() {
		// Gateway instructions in emails.
		add_action( 'woocommerce_email_before_order_table', array( $this, 'translate_instructions' ), 5 ); // Before WooCommerce.

		// Gateway instructions in thankyou page.
		add_action( 'woocommerce_before_thankyou', array( $this, 'translate_instructions' ) ); // Since WooCommerce 3.7.

		// Gateways.
		add_filter( 'woocommerce_gateway_title', 'pll__' );
		add_filter( 'woocommerce_gateway_description', 'pll__' );


		add_filter( 'woocommerce_bacs_accounts', array( $this, 'translate_bacs_accounts' ) );

		// Shipping methods.
		add_filter( 'woocommerce_package_rates', array( $this, 'translate_shipping' ) );

		if ( isset( $_COOKIE[ PLL_COOKIE ] ) && pll_current_language() !== $_COOKIE[ PLL_COOKIE ] ) {
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'reset_shipping_language' ) );
		}

		// Shipping methods since WooCommerce 2.6.
		add_filter( 'woocommerce_shipping_rate_label', 'pll__' );

		// Options.
		foreach ( array_keys( self::get_options() ) as $string ) {
			add_filter( 'option_woocommerce_' . $string, 'pll__' );
		}

		// Attributes.
		add_filter( 'woocommerce_attribute_taxonomies', array( $this, 'attribute_taxonomies' ) );
		add_filter( 'woocommerce_attribute_label', 'pll__' );

		// Tax rate labels.
		add_filter( 'woocommerce_rate_label', 'pll__' );
		add_filter( 'woocommerce_find_rates', array( $this, 'find_rates' ) );
	}

	/**
	 * Translates emails subject, heading and footer.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function translate_emails() {
		add_filter( 'woocommerce_email_get_option', array( $this, 'translate_email_option' ), 10, 4 );

		// These filters are added by Polylang but not on admin.
		foreach ( array( 'option_blogname', 'option_blogdescription', 'option_date_format', 'option_time_format' ) as $filter ) {
			add_filter( $filter, 'pll__', 1 );
		}

		// Other strings.
		$this->translate_strings();

		// In case mails are sent in bulk, we need to reset some settings such as the subject and heading for each email sent.
		foreach ( WC_Emails::instance()->get_emails() as $email ) {
			$email->init_settings();
		}
	}

	/**
	 * Translates emails options such as the subject and heading.
	 * Hooked to the filter 'woocommerce_email_get_option'.
	 *
	 * @since 0.8
	 *
	 * @param string   $value  String to translate.
	 * @param WC_Email $email  Instance of WC_Email, not used.
	 * @param string   $_value Same as $value, not used.
	 * @param string   $key    Option name.
	 * @return string
	 */
	public function translate_email_option( $value, $email, $_value, $key ) {
		if ( $this->is_translated_email_property( $key ) ) {
			$value = pll__( $value );
		}
		return $value;
	}

	/**
	 * Translates the gateway instructions in thankyou page or email.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function translate_instructions() {
		$gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
		foreach ( $gateways as $key => $gateway ) {
			if ( isset( $gateway->instructions ) ) {
				$gateways[ $key ]->instructions = pll__( $gateway->instructions );
			}
		}
	}

	/**
	 * Translate the account names and bank names for the BACS gateway.
	 *
	 * @since 1.2
	 *
	 * @param array $accounts Array of account details.
	 * @return array
	 */
	public function translate_bacs_accounts( $accounts ) {
		foreach ( $accounts as $k => $account ) {
			$accounts[ $k ]['account_name'] = pll__( $account['account_name'] );
			$accounts[ $k ]['bank_name'] = pll__( $account['bank_name'] );
		}
		return $accounts;
	}

	/**
	 * Translate the shipping methods titles.
	 *
	 * @since 0.1
	 *
	 * @param WC_Shipping_Rate[] $rates Array of WC_Shipping_Rate objects.
	 * @return array
	 */
	public function translate_shipping( $rates ) {
		foreach ( $rates as $key => $rate ) {
			$rates[ $key ]->set_label( pll__( $rate->get_label() ) );
		}
		return $rates;
	}

	/**
	 * Reset the shipping in session in case a user switches the language.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function reset_shipping_language() {
		unset( WC()->session->shipping_for_package ); // Since WooCommerce 2.5.
	}

	/**
	 * Translates the attributes labels.
	 *
	 * @since 0.1
	 *
	 * @param stdClass[] $attribute_taxonomies Attribute taxonomies.
	 * @return stdClass[]
	 */
	public function attribute_taxonomies( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $attr ) {
			$attr->attribute_label = pll__( $attr->attribute_label );
		}
		return $attribute_taxonomies;
	}

	/**
	 * Translates the tax rates labels.
	 *
	 * @since 1.2
	 *
	 * @param array $rates An array of tax rates.
	 * @return array
	 */
	public function find_rates( $rates ) {
		foreach ( $rates as $k => $rate ) {
			$rates[ $k ]['label'] = pll__( $rate['label'] );
		}
		return $rates;
	}

	/**
	 * Translates an attribute label on admin.
	 * Needed for variations titles since WC 3.0.
	 *
	 * @since 0.7
	 *
	 * @param string            $label   Attribute label.
	 * @param string            $name    Taxonomy name, not used.
	 * @param WC_Product|string $product Product data or empty string.
	 * @return string
	 */
	public function attribute_label( $label, $name, $product ) {
		// Don't translate the attribute label when exporting a product, as it would create new attributes if the file is imported back.
		if ( $product instanceof WC_Product && ! doing_action( 'wp_ajax_woocommerce_do_ajax_product_export' ) ) {
			/** @var PLLWC_Product_Language_CPT */
			$data_store = PLLWC_Data_Store::load( 'product_language' );

			$lang     = $data_store->get_language( $product->get_id() );
			$language = PLL()->model->get_language( $lang );

			if ( $language ) {
				$mo = new PLL_MO();
				$mo->import_from_db( $language );
				return $mo->translate( $label );
			}
		}
		return $label;
	}
}
