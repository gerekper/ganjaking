<?php
/**
 * Extra Product Options Cart Functionality
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Cart {

	// Edit option in cart helper 
	public $new_add_to_cart_key = FALSE;

	private $saved_product_quantity = FALSE;
	private $added_woocommerce_checkout_cart_item_quantity = FALSE;

	public $element_id_array = array();
	public $global_prices = array();
	public $global_sections = array();
	public $global_price_array = array();
	public $local_price_array = array();
	public $form_prefix = "";
	public $populate_arrays_set = FALSE;
	public $cart_item_meta = array();

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Alter the cart id upon adding the product to the cart
		add_filter( 'woocommerce_cart_id', array( $this, 'woocommerce_cart_id' ), 10, 5 );
		// Modifies the cart item
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 9999, 1 );
		add_filter( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_before_calculate_totals' ), 9999, 1 );
		// Load cart data on every page load
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), 9999, 3 );
		// Gets cart item to display in the frontend
		add_filter( 'woocommerce_get_item_data', array( $this, 'woocommerce_get_item_data' ), 50, 2 );
		// Add item data to the cart
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ), 50, 3 );
		// Validate upon adding to cart
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woocommerce_add_to_cart_validation' ), 50, 6 );
		// Alter the product thumbnail in cart
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'woocommerce_cart_item_thumbnail' ), 50, 3 );
		// Ensures correct price is shown on minicart
		add_action( 'woocommerce_before_mini_cart', array( $this, 'woocommerce_before_mini_cart' ) );
		// Cart edit key
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'woocommerce_cart_loaded_from_session' ), 0 );
		// Calculate totals on remove from cart/update
		add_action( 'woocommerce_update_cart_action_cart_updated', array( $this, 'woocommerce_update_cart_action_cart_updated' ), 9999, 1 );
		// Support for fee price types 
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'woocommerce_cart_calculate_fees' ) );

		// Empty cart button 
		if ( THEMECOMPLETE_EPO()->tm_epo_clear_cart_button == "show" ) {
			add_action( 'woocommerce_cart_actions', array( $this, 'add_empty_cart_button' ) );
			// check for empty-cart get param to clear the cart
			add_action( 'init', array( $this, 'clear_cart' ) );
		}

		// Override templates
		if ( apply_filters( 'tm_get_template', TRUE ) ) {
			add_filter( 'wc_get_template', array( $this, 'tm_wc_get_template' ), 10, 5 );
		}

		// Custom actions running for advanced template system
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'woocommerce_cart_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woocommerce_cart_item_quantity' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'woocommerce_cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_class', array( $this, 'woocommerce_cart_item_class' ), 10, 3 );
		add_filter( 'wc_tm_epo_ac_product_price', array( $this, 'wc_tm_epo_ac_product_price' ), 10, 5 );
		add_filter( 'wc_tm_epo_ac_subtotal_price', array( $this, 'wc_tm_epo_ac_product_price' ), 10, 5 );

		// Edit cart item
		// Adds edit link on product title in cart
		add_filter( 'woocommerce_cart_item_name', array( $this, 'woocommerce_cart_item_name' ), 50, 3 );
		// Alters add to cart text when editing a product
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woocommerce_before_add_to_cart_button' ) );
		// Alters the cart item key when editing a product
		add_action( 'woocommerce_add_to_cart', array( $this, 'woocommerce_add_to_cart' ), 10, 6 );
		// Redirect to cart when updating information for a cart item
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'woocommerce_add_to_cart_redirect' ), 9999, 1 );
		// Remove product from cart when editing a product
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'remove_previous_product_from_cart' ), 99999, 6 );
		// Alter add to cart message
		add_filter( 'wc_add_to_cart_message_html', array( $this, 'wc_add_to_cart_message_html' ), 10, 2 );
		// Change quantity value when editing a cart item 
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'tm_woocommerce_before_add_to_cart_form' ), 1 );
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'tm_woocommerce_after_add_to_cart_form' ), 9999 );

		// Disables persistent cart 
		if ( THEMECOMPLETE_EPO()->tm_epo_turn_off_persi_cart == "yes" ) {
			add_filter( 'get_user_metadata', array( $this, 'turn_off_persi_cart' ), 10, 3 );
			add_filter( 'update_user_metadata', array( $this, 'turn_off_persi_cart' ), 10, 3 );
			add_filter( 'add_user_metadata', array( $this, 'turn_off_persi_cart' ), 10, 3 );
		}

		// Add option specific styles to the cart page
		add_action( 'woocommerce_after_cart', array( THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ), 99999 );

	}

	/**
	 * Returns correct formated price for the cart table
	 *
	 * @since 1.0
	 */
	public function get_price_for_cart( $price = 0, $cart_item = array(), $symbol = FALSE, $currencies = NULL, $quantity_divide = 0, $quantity = 0, $price_type = "" ) {

		global $woocommerce;
		$product          = $cart_item['data'];
		$cart             = $woocommerce->cart;
		$taxable          = $product->is_taxable();
		$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );
		$tax_string       = "";

		if ( $price === FALSE ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
				if ( is_object( $product ) && property_exists( $product, "price" ) ) {
					$price = $cart_item['data']->price;
				} else {
					$price = $product->price;
				}
			} else {
				$price = $product->get_price();
			}
		}
		if ( is_array( $price_type ) ) {
			$price_type = array_values( $price_type );
			$price_type = $price_type[0];
		}
		$price = apply_filters( 'woocommerce_tm_epo_price_on_cart', $price, $cart_item );

		// Taxable
		if ( $taxable ) {

			if ( $tax_display_cart == 'excl' ) {

				if ( $cart->tax_total > 0 && wc_prices_include_tax() ) {
					$tax_string = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';
				}
				if ( floatval( $price ) != 0 ) {
					$price = themecomplete_get_price_excluding_tax( $product, array( 'qty' => 10000, 'price' => $price ) ) / 10000;
				}

			} else {

				if ( $cart->tax_total > 0 && ! wc_prices_include_tax() ) {
					$tax_string = ' <small>' . apply_filters( 'inc_tax_or_vat', WC()->countries->inc_tax_or_vat() ) . '</small>';
				}
				if ( floatval( $price ) != 0 ) {
					$price = themecomplete_get_price_including_tax( $product, array( 'qty' => 10000, 'price' => $price ) ) / 10000;
				}

			}

		}

		if ( $symbol === FALSE ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_global_price_sign == '' && THEMECOMPLETE_EPO()->tm_epo_cart_field_display != "advanced" ) {
				$symbol = apply_filters( 'wc_epo_get_price_for_cart_plus_sign', "<span class='tc-plus-sign'>+</span>" );
			}
			if ( floatval( $price ) < 0 ) {
				$symbol = apply_filters( 'wc_epo_get_price_for_cart_minus_sign', "<span class='tc-minus-sign'>-</span>" );
			}
		}

		if ( ! empty( $quantity ) ) {
			$price = floatval( $price ) * floatval( $quantity );
		}

		if ( floatval( $price ) == 0 ) {
			$symbol = apply_filters( 'wc_epo_get_price_for_cart_price_empty', '', $price, $tax_string, $cart_item, $symbol, $currencies, $quantity_divide, $quantity, $price_type );
		} else {
			$price  = apply_filters( 'wc_epo_get_price_for_cart_price', ' <span class="tc-price-amount-in-cart">' . ( themecomplete_price( abs( $price ) ) ) . '</span>', $price = 0, $cart_item, $symbol, $currencies, $quantity_divide, $quantity, $price_type );
			$symbol = apply_filters( 'wc_epo_get_price_for_cart_symbol', " $symbol" . $price . $tax_string, $symbol, $price, $tax_string, $cart_item, $symbol, $currencies, $quantity_divide, $quantity, $price_type );

			if ( THEMECOMPLETE_EPO()->tm_epo_strip_html_from_emails == "yes" ) {
				$symbol = wp_strip_all_tags( $symbol );
			}
		}

		return apply_filters( 'wc_epo_get_price_for_cart', $symbol, $price, $cart_item, $symbol, $currencies, $quantity_divide, $quantity, $price_type );

	}

	/**
	 * Alter the cart id upon adding the product to the cart
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_id( $cart_id, $product_id, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
		
		if ( isset( $cart_item_data['tmpost_data'] ) && isset( $cart_item_data['tmpost_data']['quantity'] ) ) {
			unset( $cart_item_data['tmpost_data']['quantity'] );
		}
		if ( isset( $cart_item_data['tmpost_data'] ) && isset( $cart_item_data['tmpost_data'][ THEMECOMPLETE_EPO()->cart_edit_key_var_alt ] ) ) {
			unset( $cart_item_data['tmpost_data'][ THEMECOMPLETE_EPO()->cart_edit_key_var_alt ] );
		}
		if ( isset( $cart_item_data['tmdata'] ) && isset( $cart_item_data['tmdata']['tc_added_in_currency'] ) ) {
			unset( $cart_item_data['tmdata']['tc_added_in_currency'] );
		}

		$id_parts = array( $product_id );

		if ( $variation_id && 0 !== $variation_id ) {
			$id_parts[] = $variation_id;
		}

		if ( is_array( $variation ) && ! empty( $variation ) ) {
			$variation_key = '';
			foreach ( $variation as $key => $value ) {
				$variation_key .= trim( $key ) . trim( $value );
			}
			$id_parts[] = $variation_key;
		}

		if ( is_array( $cart_item_data ) && ! empty( $cart_item_data ) ) {
			$cart_item_data_key = '';
			foreach ( $cart_item_data as $key => $value ) {
				if ( is_array( $value ) || is_object( $value ) ) {
					$value = http_build_query( $value );
				}
				$cart_item_data_key .= trim( $key ) . trim( $value );

			}
			$id_parts[] = $cart_item_data_key;
		}

		$cart_id = md5( implode( '_', $id_parts ) );

		return $cart_id;

	}

	/**
	 * @param      $cart_item_meta
	 * @param      $product_id
	 * @param null $post_data
	 *
	 * @return mixed
	 */
	public function repopulatecart( $cart_item_meta, $product_id, $post_data = NULL ) {

		if ( ! $this->populate_arrays( $product_id, $post_data, $cart_item_meta ) ) {
			return $cart_item_meta;
		}

		$cpf_product_price = $post_data['cpf_product_price'];

		$global_prices = $this->global_prices;

		$element_object = array();
		$pl             = array( "before", "after" );
		foreach ( $pl as $where ) {
			foreach ( $global_prices[ $where ] as $priorities ) {
				foreach ( $priorities as $field ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['elements'] ) ) {
							foreach ( $section['elements'] as $element ) {
								$element_object[ $element['uniqid'] ] = $element;
							}
						}
					}
				}
			}
		}

		if ( isset( $cart_item_meta['tmcartepo'] ) ) {
			$current_currency = themecomplete_get_woocommerce_currency();

			$tc_added_in_currency = isset( $cart_item_meta['tmdata']['tc_added_in_currency'] ) ? $cart_item_meta['tmdata']['tc_added_in_currency'] : FALSE;

			$percentcurrenttotal = array();

			foreach ( $cart_item_meta['tmcartepo'] as $key => $value ) {
				if ( ! isset( $element_object[ $value['section'] ] ) ) {
					continue;
				}
				if ( $value["mode"] == "builder" ) {

					$new_key                = FALSE;
					$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $product_id, TRUE );
					if ( ! empty( $value['multiple'] ) && ! empty( $value['key'] ) ) {
						$pos = strrpos( $value['key'], '_' );
						if ( $pos !== FALSE && isset( $wpml_translation_by_id[ "options_" . $value['section'] ] ) && is_array( $wpml_translation_by_id[ "options_" . $value['section'] ] ) ) {
							$av = array_values( $wpml_translation_by_id[ "options_" . $value['section'] ] );
							$ak = array_keys( $wpml_translation_by_id[ "options_" . $value['section'] ] );
							if ( isset( $av[ substr( $value['key'], $pos + 1 ) ] ) ) {
								$new_key = $ak[ substr( $value['key'], $pos + 1 ) ];
							}
						}
					}

					$price_per_currencies = isset( $element_object[ $value['section'] ]['price_per_currencies'] ) ? $element_object[ $value['section'] ]['price_per_currencies'] : array();
					$price_per_currency   = array();
					$_price_type          = THEMECOMPLETE_EPO()->get_saved_element_price_type( $value );

					if ( $_price_type == "percentcurrenttotal" ) {
						$percentcurrenttotal[] = $key;
					} else {

						foreach ( $price_per_currencies as $currency => $price_rule ) {
							$copy_element                         = $element_object[ $value['section'] ];
							$copy_element['price_rules_original'] = $copy_element['price_rules'];
							$copy_element['price_rules']          = $price_rule;
							$currency_price                       = THEMECOMPLETE_EPO()->calculate_price( $post_data,
								$copy_element,
								( $new_key !== FALSE ) ? $new_key : $cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['key'],
								$cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['attribute'],
								$cart_item_meta["tmdata"]["per_product_pricing"],
								$cpf_product_price,
								$cart_item_meta["tmdata"]["variation_id"],
								'',
								$currency,
								$tc_added_in_currency,
								$price_per_currencies );

							$price_per_currency[ $currency ] = $currency_price;
						}

						$_price = THEMECOMPLETE_EPO()->calculate_price( $post_data,
							$element_object[ $value['section'] ],
							( $new_key !== FALSE ) ? $new_key : $cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['key'],
							$cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['attribute'],
							$cart_item_meta["tmdata"]["per_product_pricing"],
							$cpf_product_price,
							$cart_item_meta["tmdata"]["variation_id"] );

						$cart_item_meta['tmcartepo'][ $key ]['price']              = $_price;
						$cart_item_meta['tmcartepo'][ $key ]['price_per_currency'] = $price_per_currency;

						if ( $_price_type == "percent" && $tc_added_in_currency && isset($price_per_currency[ $tc_added_in_currency ]) ) {
							$_price                                    = $price_per_currency[ $tc_added_in_currency ];
							$_price                                    = apply_filters( 'wc_epo_convert_to_currency', $_price, $tc_added_in_currency, $current_currency );
							$post_data['tm_epo_options_static_prices'] = floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $_price );
						}

					}

				}
			}

			foreach ( $percentcurrenttotal as $key ) {
				$value = $cart_item_meta['tmcartepo'][ $key ];

				if ( ! isset( $element_object[ $value['section'] ] ) ) {
					continue;
				}

				if ( $value["mode"] == "builder" ) {

					$new_key                = FALSE;
					$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $product_id, TRUE );
					if ( ! empty( $value['multiple'] ) && ! empty( $value['key'] ) ) {
						$pos = strrpos( $value['key'], '_' );
						if ( $pos !== FALSE && isset( $wpml_translation_by_id[ "options_" . $value['section'] ] ) && is_array( $wpml_translation_by_id[ "options_" . $value['section'] ] ) ) {
							$av = array_values( $wpml_translation_by_id[ "options_" . $value['section'] ] );
							$ak = array_keys( $wpml_translation_by_id[ "options_" . $value['section'] ] );
							if ( isset( $av[ substr( $value['key'], $pos + 1 ) ] ) ) {
								$new_key = $ak[ substr( $value['key'], $pos + 1 ) ];
							}
						}
					}

					$price_per_currencies = isset( $element_object[ $value['section'] ]['price_per_currencies'] ) ? $element_object[ $value['section'] ]['price_per_currencies'] : array();
					$price_per_currency   = array();
					$_price_type          = THEMECOMPLETE_EPO()->get_saved_element_price_type( $value );

					foreach ( $price_per_currencies as $currency => $price_rule ) {

						$copy_element                         = $element_object[ $value['section'] ];
						$copy_element['price_rules_original'] = $copy_element['price_rules'];
						$copy_element['price_rules']          = $price_rule;
						$currency_price                       = THEMECOMPLETE_EPO()->calculate_price( $post_data,
							$copy_element,
							( $new_key !== FALSE ) ? $new_key : $cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['key'],
							$cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['attribute'],
							$cart_item_meta["tmdata"]["per_product_pricing"],
							apply_filters( 'wc_epo_convert_to_currency', $cpf_product_price, $tc_added_in_currency, $currency ),
							$cart_item_meta["tmdata"]["variation_id"],
							'',
							$currency,
							$current_currency,
							$price_per_currencies );

						$price_per_currency[ $currency ] = $currency_price;

					}

					$_price = THEMECOMPLETE_EPO()->calculate_price( $post_data,
						$element_object[ $value['section'] ],
						( $new_key !== FALSE ) ? $new_key : $cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['key'],
						$cart_item_meta['tmdata']['tmcartepo_data'][ $key ]['attribute'],
						$cart_item_meta["tmdata"]["per_product_pricing"],
						$cpf_product_price,
						$cart_item_meta["tmdata"]["variation_id"] );

					$cart_item_meta['tmcartepo'][ $key ]['price']              = $_price;
					$cart_item_meta['tmcartepo'][ $key ]['price_per_currency'] = $price_per_currency;

				}

			}

		}

		return $cart_item_meta;
	}

	/**
	 * Helper function to remove string part
	 *
	 * @since 4.9.8
	 */
	private function remove_underscore_part( $input = "" ) {
		return substr( $input, 0, strrpos( $input, '_' ) );
	}

	/**
	 * Modifies the cart item
	 *
	 * @since 1.0
	 */
	public function add_cart_item( $cart_item = array() ) {

		if ( apply_filters( 'wc_epo_no_add_cart_item', FALSE ) ) {
			return $cart_item;
		}

		/*
		* The following logic ensures that the correct price is being calculated
		* when currency or product price is being changed from various
		* 3rd part plugins.
		*/
		$cart_item['tm_epo_product_original_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $cart_item['data']->get_price(), $cart_item );

		$cart_item['tm_epo_options_prices']             = 0;
		$cart_item['tm_epo_product_price_with_options'] = $cart_item['tm_epo_product_original_price'];

		$this->cart_item_meta = $cart_item;

		$product_epos         = array();
		$product_epos_choices = array();

		if ( ! empty( $cart_item['tmcartepo'] ) ) {

			$tmcp_prices           = 0;
			$tmcp_static_prices    = 0;
			$tmcp_variable_prices  = 0; // percentcurrenttotal
			$tmcp_variable_prices2 = 0; // percent
			$tmcp_variable_prices3 = 0; // fixedcurrenttotal

			$to_currency = themecomplete_get_woocommerce_currency();

			$product_id           = $cart_item['product_id'];
			$product_epos         = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $cart_item["tmdata"]["form_prefix"], TRUE, TRUE );
			$product_epos_choices = $product_epos['product_epos_choices'];
			if ( is_array( $product_epos_choices ) ) {
				foreach ( $product_epos_choices as $key => $product_epos_choice ) {
					$product_epos_choices[ $key ] = array_map( array( $this, 'remove_underscore_part' ), $product_epos_choice );
				}
			}

			// disable check for WPML
			$tcremoved = THEMECOMPLETE_EPO_WPML()->is_active() ? FALSE : TRUE;

			if ( is_array( $cart_item['tmcartepo'] ) ) {
				foreach ( $cart_item['tmcartepo'] as $tmcp ) {

					if ( ! THEMECOMPLETE_EPO_WPML()->is_active() && isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

						$key = $this->remove_underscore_part( $tmcp['key'] );
						if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ] ) ) {
							continue;
						}

					}

					if ( ! isset( $product_epos['epos_uniqids'] ) || ! is_array( $product_epos['epos_uniqids'] ) || ! in_array( $tmcp['section'], $product_epos['epos_uniqids'] ) ) {
						continue;
					}

					$tcremoved = FALSE;

					if ( apply_filters( 'wc_epo_add_cart_item_loop', FALSE, $tmcp ) ) {
						continue;
					}

					if (isset($tmcp['price_formula'])){
						$cart_item['tc_recalculate'] = true;
					}

					$_price_type = THEMECOMPLETE_EPO()->get_saved_element_price_type( $tmcp );

					if ( isset( $tmcp['price_per_currency'] ) && isset( $tmcp['price_per_currency'][ $to_currency ] ) && $tmcp['price_per_currency'][ $to_currency ] != '' ) {
						$tmcp['price'] = apply_filters( 'woocommerce_tm_epo_price_per_currency_diff', (float) wc_format_decimal( $tmcp['price_per_currency'][ $to_currency ], FALSE, TRUE ), $to_currency );
						$tmcp_prices   += $tmcp['price'];
						if ( $_price_type == "fixedcurrenttotal" ) {
							$tmcp_variable_prices3 += $tmcp['price'];
						} elseif ( $_price_type == "percentcurrenttotal" ) {
							$tmcp_variable_prices += $tmcp['price'];
						} elseif ( $_price_type == "percent" ) {
							$tmcp_variable_prices2 += $tmcp['price'];
						} else {
							$tmcp_static_prices += $tmcp['price'];
						}
					} else {
						$tmcp['price'] = (float) wc_format_decimal( $tmcp['price'], FALSE, TRUE );
						$tmcp_prices   += apply_filters( 'woocommerce_tm_epo_price_add_on_cart', $tmcp['price'], $_price_type );
						if ( $_price_type == "fixedcurrenttotal" ) {
							$tmcp_variable_prices3 += $tmcp['price'];
						} elseif ( $_price_type == "percentcurrenttotal" ) {
							$tmcp_variable_prices += $tmcp['price'];
						} elseif ( $_price_type == "percent" ) {
							$tmcp_variable_prices2 += $tmcp['price'];
						} else {
							$tmcp_static_prices += apply_filters( 'woocommerce_tm_epo_price_add_on_cart', $tmcp['price'], $_price_type );
						}
					}
				}
			}

			if ( $tcremoved ) {
				$cart_item['tcremoved'] = TRUE;

				return apply_filters( "wc_epo_adjust_cart_item", $cart_item );
			}

			$cart_item['tm_epo_options_static_prices'] = $tmcp_static_prices;
			$cart_item['tm_epo_options_static_prices_first'] = $tmcp_static_prices;

			if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== "composite" ) {
				$post_data = $cart_item['tmpost_data'];
				if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
					$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
				}
				// todo:check for a better alternative
				if ( ! isset( $post_data['cpf_product_price'] ) ) {
					$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
				}
				$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );

				$post_data['quantity'] = $cart_item['quantity'];
				$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data );
				if ( $cart_item === FALSE ) {
					return array();
				}
				$cart_item = apply_filters( 'tm_cart_contents', $cart_item, array() );
			}

			if ( is_array( $cart_item['tmcartepo'] ) ) {
				$tmcp_variable_prices  = 0;
				$tmcp_variable_prices2 = 0;
				$tmcp_variable_prices3 = 0;
				foreach ( $cart_item['tmcartepo'] as $tmcp ) {
					if ( isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

						$key = $this->remove_underscore_part( $tmcp['key'] );
						if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ] ) ) {
							continue;
						}

					}
					if ( ! in_array( $tmcp['section'], $product_epos['epos_uniqids'] ) || apply_filters( 'wc_epo_add_cart_item_loop', FALSE, $tmcp ) ) {
						continue;
					}
					$_price_type = THEMECOMPLETE_EPO()->get_saved_element_price_type( $tmcp );

					if ( isset( $tmcp['price_per_currency'] ) && isset( $tmcp['price_per_currency'][ $to_currency ] ) && $tmcp['price_per_currency'][ $to_currency ] != '' ) {
						$tmcp['price'] = apply_filters( 'woocommerce_tm_epo_price_per_currency_diff', (float) wc_format_decimal( $tmcp['price_per_currency'][ $to_currency ], FALSE, TRUE ), $to_currency );
					} else {
						$tmcp['price'] = (float) wc_format_decimal( $tmcp['price'], FALSE, TRUE );
					}

					if ( $_price_type == "fixedcurrenttotal" ) {
						$tmcp_variable_prices3 += $tmcp['price'];
					}
					if ( $_price_type == "percentcurrenttotal" ) {
						$tmcp_variable_prices += $tmcp['price'];
					}
					if ( $_price_type == "percent" ) {
						$tmcp_variable_prices2 += $tmcp['price'];
					}

				}

			}

			$tmcp_prices = apply_filters( 'wc_epo_cart_options_prices', $tmcp_static_prices + $tmcp_variable_prices + $tmcp_variable_prices2 + $tmcp_variable_prices3, $cart_item );

			$cart_item['tm_epo_options_prices'] = $tmcp_prices;

			$price1 = (float) wc_format_decimal( apply_filters( 'wc_epo_option_price_correction', $tmcp_prices, $cart_item ) );
			$price2 = (float) wc_format_decimal(
					apply_filters( 'wc_epo_product_price_correction',
						wc_format_decimal( $cart_item['tm_epo_product_original_price'] ),
						$cart_item ) )
			          + (float) $price1;

			$price1 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price1', $price1, $cart_item ) );

			$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price2', $price2, $cart_item ) );

			$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price3', $price2, $price1, $cart_item ) );

			do_action( 'wc_epo_currency_actions', $price1, $price2, $cart_item );

			if ( apply_filters( 'wc_epo_adjust_price', TRUE, $cart_item ) ) {
				if ( ! empty( $cart_item['epo_price_override'] ) && $tmcp_prices > 0 ) {
					$cart_item['data']->set_price( $price1 );
					$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price1 );
				} else {
					if ( ! empty( $price1 ) ) {
						$cart_item['data']->set_price( $price2 );
					}
					$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price2 );
				}
			}
			$cart_item['tm_epo_product_price_with_options'] = $cart_item['data']->get_price();

		}

		if ( floatval( apply_filters( 'tm_epo_cart_options_prices', $cart_item['tm_epo_product_price_with_options'], $cart_item ) ) < 0 ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_no_negative_priced_products == "yes" ) {
				$message = ! empty( THEMECOMPLETE_EPO()->tm_epo_no_negative_priced_products_text ) ? THEMECOMPLETE_EPO()->tm_epo_no_negative_priced_products_text : esc_html__( 'You cannot add negative priced products to the cart.', 'woocommerce-tm-extra-product-options' );
				throw new Exception( $message );
			}
		}

		if ( floatval( apply_filters( 'tm_epo_no_zero_priced_products', $cart_item['tm_epo_product_price_with_options'], $cart_item ) ) == 0 ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_no_zero_priced_products == "yes" ) {
				$message = ! empty( THEMECOMPLETE_EPO()->tm_epo_no_zero_priced_products_text ) ? THEMECOMPLETE_EPO()->tm_epo_no_zero_priced_products_text : esc_html__( 'You cannot add zero priced products to the cart.', 'woocommerce-tm-extra-product-options' );
				throw new Exception( $message );
			}
		}

		// variation slug-to-name-for order again
		if ( isset( $cart_item["variation"] ) && is_array( $cart_item["variation"] ) ) {
			$_variation_name_fix = array();
			$_temp               = array();
			foreach ( $cart_item["variation"] as $meta_name => $meta_value ) {
				if ( strpos( $meta_name, "attribute_" ) !== 0 ) {
					$_variation_name_fix[ "attribute_" . $meta_name ] = $meta_value;
					$_temp[ $meta_name ]                              = $meta_value;
				}
			}
			$cart_item["variation"] = array_diff_key( $cart_item["variation"], $_temp );
			$cart_item["variation"] = array_merge( $cart_item["variation"], $_variation_name_fix );
		}

		return apply_filters( "wc_epo_adjust_cart_item", $cart_item );

	}

	/**
	 * Modifies the cart item
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_calculate_totals( $cart_object = array() ) {

		if (is_admin() && !defined('DOING_AJAX')){
            return;
		}
        if (method_exists($cart_object, 'get_cart')) {
            $cart_contents = $cart_object->get_cart();
        } else {
            $cart_contents = $cart_object->cart_contents;
        }

		foreach ($cart_contents as $cart_key => $cart_item) {

			if ( apply_filters( 'wc_epo_no_add_cart_item', FALSE ) ) {
				continue;
			}

			$product_epos         = array();
			$product_epos_choices = array();

			if ( ! empty( $cart_item['tmcartepo'] ) && ! empty( $cart_item['tc_recalculate'] ) ) {

				$tmcp_prices           = 0;
				$tmcp_static_prices    = 0;
				$tmcp_variable_prices  = 0; // percentcurrenttotal
				$tmcp_variable_prices2 = 0; // percent
				$tmcp_variable_prices3 = 0; // fixedcurrenttotal

				$to_currency = themecomplete_get_woocommerce_currency();

				$product_id           = $cart_item['product_id'];
				$product_epos         = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $cart_item["tmdata"]["form_prefix"], TRUE, TRUE );
				$product_epos_choices = $product_epos['product_epos_choices'];
				if ( is_array( $product_epos_choices ) ) {
					foreach ( $product_epos_choices as $key => $product_epos_choice ) {
						$product_epos_choices[ $key ] = array_map( array( $this, 'remove_underscore_part' ), $product_epos_choice );
					}
				}

				$cart_item['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices_first'];

				if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== "composite" ) {
					$post_data = $cart_item['tmpost_data'];
					if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
						$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
					}
					// todo:check for a better alternative
					if ( ! isset( $post_data['cpf_product_price'] ) ) {
						$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
					}
					$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );

					$post_data['quantity'] = $cart_item['quantity'];

					$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data );
					if ( $cart_item === FALSE ) {
						continue;
					}
					$cart_item = apply_filters( 'tm_cart_contents', $cart_item, array() );
				}

				if ( is_array( $cart_item['tmcartepo'] ) ) {
					$tmcp_variable_prices  = 0;
					$tmcp_variable_prices2 = 0;
					$tmcp_variable_prices3 = 0;
					foreach ( $cart_item['tmcartepo'] as $tmcp ) {
						if ( isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

							$key = $this->remove_underscore_part( $tmcp['key'] );
							if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ] ) ) {
								continue;
							}

						}
						if ( ! in_array( $tmcp['section'], $product_epos['epos_uniqids'] ) || apply_filters( 'wc_epo_add_cart_item_loop', FALSE, $tmcp ) ) {
							continue;
						}
						$_price_type = THEMECOMPLETE_EPO()->get_saved_element_price_type( $tmcp );

						if ( isset( $tmcp['price_per_currency'] ) && isset( $tmcp['price_per_currency'][ $to_currency ] ) && $tmcp['price_per_currency'][ $to_currency ] != '' ) {
							$tmcp['price'] = apply_filters( 'woocommerce_tm_epo_price_per_currency_diff', (float) wc_format_decimal( $tmcp['price_per_currency'][ $to_currency ], FALSE, TRUE ), $to_currency );
						} else {
							$tmcp['price'] = (float) wc_format_decimal( $tmcp['price'], FALSE, TRUE );
						}

						if ( $_price_type == "fixedcurrenttotal" ) {
							$tmcp_variable_prices3 += $tmcp['price'];
						}
						elseif ( $_price_type == "percentcurrenttotal" ) {
							$tmcp_variable_prices += $tmcp['price'];
						}
						elseif ( $_price_type == "percent" ) {
							$tmcp_variable_prices2 += $tmcp['price'];
						}
						else {
							$tmcp_static_prices += $tmcp['price'];
						}

					}

				}

				$tmcp_prices = apply_filters( 'wc_epo_cart_options_prices', $tmcp_static_prices + $tmcp_variable_prices + $tmcp_variable_prices2 + $tmcp_variable_prices3, $cart_item );

				$cart_item['tm_epo_options_prices'] = $tmcp_prices;

				$price1 = (float) wc_format_decimal( apply_filters( 'wc_epo_option_price_correction', $tmcp_prices, $cart_item ) );
				$price2 = (float) wc_format_decimal(
						apply_filters( 'wc_epo_product_price_correction',
							wc_format_decimal( $cart_item['tm_epo_product_original_price'] ),
							$cart_item ) )
				          + (float) $price1;

				$price1 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price1', $price1, $cart_item ) );

				$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price2', $price2, $cart_item ) );

				$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price3', $price2, $price1, $cart_item ) );

				do_action( 'wc_epo_currency_actions', $price1, $price2, $cart_item );

				if ( apply_filters( 'wc_epo_adjust_price', TRUE, $cart_item ) ) {
					if ( ! empty( $cart_item['epo_price_override'] ) && $tmcp_prices > 0 ) {
						$cart_item['tm_epo_product_price_with_options'] = $price1;
						$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price1 );
					} else {
						if ( ! empty( $price1 ) ) {
							$cart_item['tm_epo_product_price_with_options'] = $price2;
						}
						$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price2 );
					}
				}

			}

			$cart_item = apply_filters( "wc_epo_adjust_cart_item", $cart_item );

			$cart_contents[ $cart_key ] = $cart_item;

		}

		if (method_exists($cart_object, 'set_cart_contents')) {
			$cart_object->set_cart_contents($cart_contents);
		} else {
			$cart_object->cart_contents = $cart_contents;
		}

	}

	/**
	 * Gets the cart from session.
	 *
	 * @since 1.0
	 */
	public function woocommerce_get_cart_item_from_session( $cart_item = array(), $values = array(), $cart_item_key = "" ) {

		if ( ! empty( $values['tmcartepo'] ) ) {
			$cart_item['tmcartepo'] = $values['tmcartepo'];
			$cart_item              = $this->add_cart_item( $cart_item );
			if ( empty( $cart_item['addons'] ) && ! empty( $cart_item['tm_epo_options_prices'] ) ) {
				$cart_item['addons'] = array( "epo" => TRUE, 'price' => 0 );
			}
		}
		if ( ! empty( $values['tmcartepo_bto'] ) ) {
			$cart_item['tmcartepo_bto'] = $values['tmcartepo_bto'];
		}

		if ( ! empty( $values['tmcartfee'] ) ) {
			$cart_item['tmcartfee'] = $values['tmcartfee'];
		}

		if ( ! empty( $values['tmpost_data'] ) ) {
			$cart_item['tmpost_data'] = $values['tmpost_data'];
		}

		if ( ! empty( $values['tmproducts'] ) ) {
			$cart_item['tmproducts'] = $values['tmproducts'];
		}

		$cart_item = apply_filters( 'tm_cart_contents', $cart_item, $values );

		return apply_filters( 'wc_epo_get_cart_item_from_session', $cart_item, $values );

	}

	/**
	 * Gets cart item to display in the frontend
	 *
	 * @since 1.0
	 */
	public function woocommerce_get_item_data( $other_data = array(), $cart_item = array() ) {

		if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart == "normal" && THEMECOMPLETE_EPO()->tm_epo_cart_field_display != "advanced" && ! empty( $cart_item['tmcartepo'] ) ) {

			$other_data = $this->get_item_data_array( $other_data, $cart_item );

		}

		return $other_data;

	}

	/**
	 * Helper function for filtered_get_item_data
	 *
	 * @since 1.0
	 */
	private function filtered_get_item_data_get_array_data( $tmcp = array() ) {

		return array(
			'label'               => $tmcp['section_label'],
			'type'                => isset( $tmcp['element'] ) && isset( $tmcp['element']['type'] ) ? $tmcp['element']['type'] : '',
			'other_data'          => array(
				array(
					'name'                    => $tmcp['name'],
					'value'                   => $tmcp['value'],
					'price_type'              => isset( $tmcp['element'] ) ? ( isset( $tmcp['key'] ) ? $tmcp['element']['rules_type'][ $tmcp['key'] ][0] : $tmcp['element']['rules_type'][0] ) : '',
					'unit_price'              => $tmcp['price'],
					'unit_price_per_currency' => ( isset( $tmcp['price_per_currency'] ) ) ? $tmcp['price_per_currency'] : array(),
					'display'                 => isset( $tmcp['display'] ) ? $tmcp['display'] : '',
					'images'                  => isset( $tmcp['images'] ) ? $tmcp['images'] : '',
					'color'                   => isset( $tmcp['color'] ) ? $tmcp['color'] : '',
					'quantity'                => isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1,
				)
			),
			'price'               => $tmcp['price'],
			'currencies'          => isset( $tmcp['currencies'] ) ? $tmcp['currencies'] : array(),
			'price_per_currency'  => isset( $tmcp['price_per_currency'] ) ? $tmcp['price_per_currency'] : array(),
			'quantity'            => isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1,
			'percentcurrenttotal' => isset( $tmcp['percentcurrenttotal'] ) ? $tmcp['percentcurrenttotal'] : 0,
			'fixedcurrenttotal'   => isset( $tmcp['fixedcurrenttotal'] ) ? $tmcp['fixedcurrenttotal'] : 0,
			'items'               => 1,
			'multiple_values'     => isset( $tmcp['multiple_values'] ) ? $tmcp['multiple_values'] : '',
			'hidelabelincart'     => isset( $tmcp['hidelabelincart'] ) ? $tmcp['hidelabelincart'] : '',
			'hidevalueincart'     => isset( $tmcp['hidevalueincart'] ) ? $tmcp['hidevalueincart'] : '',
		);

	}

	/**
	 * Filters our cart items
	 *
	 * @since 1.0
	 */
	private function filtered_get_item_data( $cart_item = array() ) {

		$to_currency          = themecomplete_get_woocommerce_currency();
		$filtered_array       = array();
		$product_id           = $cart_item['product_id'];

		if ( isset( $cart_item['tmcartepo'] ) && is_array( $cart_item['tmcartepo'] ) ) {

			$product_epos         = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $cart_item["tmdata"]["form_prefix"], TRUE, TRUE );
			$product_epos_choices = $product_epos['product_epos_choices'];
			foreach ( $product_epos_choices as $key => $product_epos_choice ) {
				$product_epos_choices[ $key ] = array_map( array( $this, 'remove_underscore_part' ), $product_epos_choice );
			}

			foreach ( $cart_item['tmcartepo'] as $tmcp ) {

				if ( isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

					$key = $this->remove_underscore_part( $tmcp['key'] );
					if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ] ) ) {
						continue;
					}

				}

				if ( $tmcp && in_array( $tmcp['section'], $product_epos['epos_uniqids'] ) ) {

					if ( isset( $tmcp['price_per_currency'] ) && isset( $tmcp['price_per_currency'][ $to_currency ] ) && $tmcp['price_per_currency'][ $to_currency ] !== '' ) {
						$tmcp['price'] = (float) wc_format_decimal( $tmcp['price_per_currency'][ $to_currency ], FALSE, TRUE );
					} else {
						$tmcp['price']        = (float) wc_format_decimal( $tmcp['price'], FALSE, TRUE );
						$pp                   = FALSE;
						$tc_added_in_currency = FALSE;
						if ( isset( $cart_item['tmpost_data'] ) && isset( $cart_item['tmpost_data']['cpf_product_price'] ) && isset( $cart_item['tmdata']['tc_added_in_currency'] ) ) {
							$pp                   = $cart_item['tmpost_data']['cpf_product_price'];
							$tc_added_in_currency = $cart_item['tmdata']['tc_added_in_currency'];
						}
						$tmcp['price'] = apply_filters( 'wc_epo_get_current_currency_price', $tmcp['price'], isset( $tmcp['element'] ) ? $tmcp['element']['rules_type'][ isset( $tmcp['key'] ) ? $tmcp['key'] : 0 ][0] : '', TRUE, NULL, FALSE, $pp, $tc_added_in_currency );
					}

					if (isset($cart_item['associated_discount']) && isset($cart_item['associated_discount_type'])){
						$tmcp['price'] = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $tmcp['price'], $cart_item['associated_discount'], $cart_item['associated_discount_type']);
					}

					if ( ! isset( $filtered_array[ $tmcp['section'] ] ) ) {
						$filtered_array[ $tmcp['section'] ] = $this->filtered_get_item_data_get_array_data( $tmcp );
					} else {
						if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "advanced" || THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "link" ) {
							$filtered_array[ $tmcp['section'] . "_" . THEMECOMPLETE_EPO_HELPER()->tm_uniqid() ] = $this->filtered_get_item_data_get_array_data( $tmcp );
						} else {
							$filtered_array[ $tmcp['section'] ]['items'] += 1;
							$filtered_array[ $tmcp['section'] ]['price'] += $tmcp['price'];

							if ( isset( $tmcp['price_per_currency'] ) ) {
								$filtered_array[ $tmcp['section'] ]['price_per_currency'] = THEMECOMPLETE_EPO_HELPER()->add_array_values( $filtered_array[ $tmcp['section'] ]['price_per_currency'], $tmcp['price_per_currency'] );
							}

							$filtered_array[ $tmcp['section'] ]['quantity']     += isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1;
							$filtered_array[ $tmcp['section'] ]['other_data'][] = array(
								'name'                    => $tmcp['name'],
								'value'                   => $tmcp['value'],
								'price_type'              => isset( $tmcp['element'] ) ? ( isset( $tmcp['key'] ) ? $tmcp['element']['rules_type'][ $tmcp['key'] ][0] : $tmcp['element']['rules_type'][0] ) : '',
								'unit_price'              => $tmcp['price'],
								'unit_price_per_currency' => ( isset( $tmcp['price_per_currency'] ) ) ? $tmcp['price_per_currency'] : array(),
								'display'                 => isset( $tmcp['display'] ) ? $tmcp['display'] : '',
								'images'                  => isset( $tmcp['images'] ) ? $tmcp['images'] : '',
								'color'                   => isset( $tmcp['color'] ) ? $tmcp['color'] : '',
								'quantity'                => isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1,
							);
						}
					}
				}
			}
		}

		return $filtered_array;

	}

	/** Return formatted cart items **/
	public function get_item_data_array( $other_data = array(), $cart_item = array() ) {

		$filtered_array = $this->filtered_get_item_data( $cart_item );
		$price          = 0;
		$link_data      = array();
		$quantity       = $cart_item['quantity'];
		if ( is_array( $filtered_array ) ) {
			foreach ( $filtered_array as $section ) {
				$value                   = array();
				$value_only              = array();
				$value_original          = array();
				$value_unique            = array();
				$quantity_string_shown   = FALSE;
				$format_price_shown      = FALSE;
				$do_unique_values        = FALSE;
				$prev_unit_price         = FALSE;
				$prev_unit_quantity      = FALSE;
				$dont_show_mass_quantity = FALSE;
				$format_price            = "";
				if ( isset( $section['other_data'] ) && is_array( $section['other_data'] ) ) {
					foreach ( $section['other_data'] as $key => $data ) {
						if ( empty( $data['quantity'] ) ) {
							continue;
						}
						$display_value      = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
						$display_value_only = $display_value;

						if ( $section['type'] === 'checkbox' && THEMECOMPLETE_EPO()->tm_epo_cart_field_display == 'normal' ) {
							if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart == "normal" ) {
								$original_price = $data['unit_price'] / $data['quantity'];
								$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] );
								$after_price    = $new_price / $data['quantity'];
								$format_price   = $this->get_price_for_cart( $after_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );

								if ( $original_price != $after_price ) {
									$original_price = $this->get_price_for_cart( $original_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );
									$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
								}
								$format_price_shown = TRUE;
							} else {
								$format_price = '';
							}
							$quantity_string       = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
							$display_value_only    = $display_value;
							$display_value         = $display_value . ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
							$quantity_string_shown = TRUE;

						}

						if ( ! empty( $data['images'] ) && THEMECOMPLETE_EPO()->tm_epo_show_image_replacement == "yes" ) {
							if ( ! $format_price_shown && THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart == "normal" ) {
								$original_price = $data['unit_price'] / $data['quantity'];
								$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] );
								$after_price    = $new_price / $data['quantity'];
								$format_price   = $this->get_price_for_cart( $after_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );

								if ( $original_price != $after_price ) {
									$original_price = $this->get_price_for_cart( $original_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );
									$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
								}
								$format_price_shown = TRUE;
							} else {
								$format_price = '';
							}
							$quantity_string       = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
							$display_value         = '<span class="cpf-img-on-cart"><img alt="' . esc_attr( strip_tags( $section['label'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="' .
							                         apply_filters( "tm_image_url", $data['images'] ) . '" />' . $display_value;
							$display_value_only    = $display_value;
							$display_value         = $display_value . '<span class="tc-price-in-cart">' . $format_price . '</span></span>  <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
							$quantity_string_shown = TRUE;
						} elseif ( ! empty( $data['color'] ) && THEMECOMPLETE_EPO()->tm_epo_show_image_replacement == "yes" ) {
							if ( ! $format_price_shown && THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart == "normal" ) {
								$original_price = $data['unit_price'] / $data['quantity'];
								$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] );
								$after_price    = $new_price / $data['quantity'];
								$format_price   = $this->get_price_for_cart( $after_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );

								if ( $original_price != $after_price ) {
									$original_price = $this->get_price_for_cart( $original_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );
									$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
								}
								$format_price_shown = TRUE;
							} else {
								$format_price = '';
							}
							$quantity_string       = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
							$display_value         = '<span class="cpf-colors-on-cart"><span class="cpf-color-on-cart backgroundcolor' . esc_attr( sanitize_hex_color_no_hash( $data['color'] ) ) . '"></span> ' . $display_value;
							$display_value_only    = $display_value;
							$display_value         = $display_value . '<span class="tc-price-in-cart">' . $format_price . '</span></span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
							$quantity_string_shown = TRUE;
							THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( '.backgroundcolor' . esc_attr( sanitize_hex_color_no_hash( $data['color'] ) ) . '{background-color:' . esc_attr( sanitize_hex_color( $data['color'] ) ) . ';}' );
						} else {

							if ( $prev_unit_quantity === FALSE ) {
								$prev_unit_quantity = $data['quantity'];
							}
							if ( $prev_unit_price === FALSE ) {
								$prev_unit_price = $data['unit_price'];
							} elseif ( $prev_unit_price !== $data['unit_price'] || $prev_unit_quantity != $data['quantity'] || $data['quantity'] > 1 ) {

								if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart !== "normal" ) {
									$dont_show_mass_quantity = TRUE;
								}

							}
							$prev_unit_price    = $data['unit_price'];
							$prev_unit_quantity = $data['quantity'];

						}
						if ( THEMECOMPLETE_EPO()->tm_epo_show_hide_uploaded_file_url_cart == "no" && THEMECOMPLETE_EPO()->tm_epo_show_upload_image_replacement == "yes" && $section['type'] == "upload" ) {
							$check = wp_check_filetype( $data['value'] );
							if ( ! empty( $check['ext'] ) ) {
								$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
								if ( in_array( $check['ext'], $image_exts ) ) {
									$display_value      = '<span class="cpf-img-on-cart"><img alt="' . esc_attr( strip_tags( $section['label'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="' .
									                      apply_filters( "tm_image_url", $data['value'] ) . '" /><span>';
									$display_value_only = $display_value;
								}
							}
						}
						$value[]      = $display_value;
						$value_only[] = $display_value_only;

						// Unique values
						$display_value  = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
						$original_price = $data['unit_price'] / $data['quantity'];
						$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] );
						$after_price    = $new_price / $data['quantity'];
						$format_price   = $this->get_price_for_cart( $after_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );

						if ( $original_price != $after_price ) {
							$original_price = $this->get_price_for_cart( $original_price, $cart_item, FALSE, $data['unit_price_per_currency'], $data['quantity'], 0, $data['price_type'] );
							$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
						}
						$quantity_string = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
						if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart != "normal" || $section['hidevalueincart'] === 'noprice' || $section['hidevalueincart'] === 'hidden' ) {
							$format_price = '';
						}
						if ( ! empty( $section['multiple_values'] ) ) {
							$display_value_array = explode( $section['multiple_values'], $display_value );
							$display_value       = "";
							foreach ( $display_value_array as $d => $dv ) {
								$display_value .= '<span class="cpf-data-on-cart">' . $dv . '</span>';
							}
							$display_value .= ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
						} else {
							$display_value = '<span class="cpf-data-on-cart">' . $display_value . ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span></span>';
						}
						$value_unique[] = $display_value;
					}

					$value_original = $value;

					if ( ! empty( $section['multiple_values'] ) ) {
						$do_unique_values = TRUE;
					}

					if ( THEMECOMPLETE_EPO()->tm_epo_always_unique_values === 'yes' && $section['type'] === 'checkbox' ) {
						$do_unique_values = TRUE;
					}

					if ( $do_unique_values ) {
						$quantity_string_shown = TRUE;
						$format_price_shown    = TRUE;
						$value                 = $value_unique;
					}

				}

				if ( ! empty( $value_original ) && count( $value_original ) > 0 ) {
					if ( $quantity_string_shown ) {
						if ( is_array( $value_original[0] ) ) {
							$temp = '';
							foreach ( $value_original as $k => $v ) {
								$temp .= implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $v );
							}
							$value_original = $temp;
						} else {
							$value_original = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value_original );
						}
					} else {
						if ( is_array( $value_original[0] ) ) {
							$temp = '';
							foreach ( $value_original as $k => $v ) {
								$temp .= implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $v );
							}
							$value_original = $temp;
						} else {
							if ( ! empty( $section['multiple_values'] ) ) {
								$value_original = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value_original );
							} else {
								$value_original = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value_original );
							}
						}

					}
				} else {
					$value_original = "";
				}

				if ( ! empty( $value ) && count( $value ) > 0 ) {
					if ( $quantity_string_shown ) {
						if ( is_array( $value[0] ) ) {
							$temp = '';
							foreach ( $value as $k => $v ) {
								$temp .= implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $v );
							}
							$value = $temp;
						} else {
							$value = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value );
						}
					} else {
						if ( is_array( $value[0] ) ) {
							$temp = '';
							foreach ( $value as $k => $v ) {
								$temp .= implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $v );
							}
							$value = $temp;
						} else {
							if ( ! empty( $section['multiple_values'] ) ) {
								$value = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value );
							} else {
								$value = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value );
							}
						}

					}
				} else {
					$value = "";
				}

				if ( ! empty( $value_only ) && count( $value_only ) > 0 ) {
					if ( $quantity_string_shown ) {
						if ( is_array( $value_only[0] ) ) {
							$temp = '';
							foreach ( $value_only as $k => $v ) {
								$temp .= implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $v );
							}
							$value_only = $temp;
						} else {
							$value_only = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value_only );
						}
					} else {
						if ( is_array( $value_only[0] ) ) {
							$temp = '';
							foreach ( $value_only as $k => $v ) {
								$temp .= implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $v );
							}
							$value_only = $temp;
						} else {
							if ( ! empty( $section['multiple_values'] ) ) {
								$value_only = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value_only );
							} else {
								$value_only = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value_only );
							}
						}

					}
				} else {
					$value_only = "";
				}

				if ( empty( $section['quantity'] ) ) {
					$section['quantity'] = 1;
				}

				// WooCommerce Dynamic Pricing & Discounts
				$original_price   = $section['price'] / $section['quantity'];
				$original_price_q = $original_price * $quantity * $section['quantity'];

				$section['price'] = apply_filters( 'wc_epo_discounted_price', $section['price'], $cart_item['data'], $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] );
				$after_price      = $section['price'] / $section['quantity'];

				$price                 = $price + (float) $section['price'];
				$section['price_type'] = "";
				if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart == "normal" ) {
					$format_price        = $this->get_price_for_cart( $after_price, $cart_item, FALSE, $section['price_per_currency'], $section['quantity'], 0, $section['price_type'] );
					$format_price_total  = $this->get_price_for_cart( $section['price'], $cart_item, FALSE, $section['price_per_currency'], 0, $quantity, $section['price_type'] );
					$format_price_total2 = $this->get_price_for_cart( $section['price'], $cart_item, FALSE, $section['price_per_currency'], 0, 0, $section['price_type'] );

					if ( $original_price != $after_price ) {
						$original_price       = $this->get_price_for_cart( $original_price, $cart_item, FALSE, $section['price_per_currency'], 0, 0, $section['price_type'] );
						$original_price_total = $this->get_price_for_cart( $original_price_q, $cart_item, FALSE, $section['price_per_currency'], 0, 0, $section['price_type'] );
						$format_price         = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
					}
				} else {
					$format_price        = '';
					$format_price_total  = '';
					$format_price_total2 = '';
				}
				$single_price    = $this->get_price_for_cart( (float) $section['price'] / $section['quantity'], $cart_item, FALSE, $section['price_per_currency'], 0, 0, $section['price_type'] );
				$quantity_string = ( $section['quantity'] > 1 ) ? ' &times; ' . $section['quantity'] : '';

				if ( $quantity_string_shown || $dont_show_mass_quantity ) {
					$quantity_string = "";
				}

				$is_checkbox = FALSE;
				if ( $section['type'] === 'checkbox' ) {
					$quantity_string = "";
					$is_checkbox     = TRUE;
				}

				if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display != "link" ) {
					if ( empty( $section['hidelabelincart'] ) || $section['hidevalueincart'] === 'noprice' || empty( $section['hidevalueincart'] ) ) {
						$value_to_show = ( empty( $section['hidevalueincart'] ) || $section['hidevalueincart'] === 'noprice' || $section['hidevalueincart'] === 'price' ) ? apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->html_entity_decode( $value ) ) : '';

						$other_data[] = array(
							'name'            => empty( $section['hidelabelincart'] ) ? $section['label'] : '',
							'value'           => ( empty( $section['hidevalueincart'] ) || $section['hidevalueincart'] === 'noprice' || $section['hidevalueincart'] === 'price' )
								?
								($section['hidevalueincart'] === 'price'?'':$value_to_show) .
								(
								$section['hidevalueincart'] !== 'noprice' ?

									( ! $format_price_shown && $format_price && isset( $quantity_string ) )
										? ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span>'
										:
										(
										( $format_price && $is_checkbox )
											? ( $do_unique_values )
											? (
											( THEMECOMPLETE_EPO()->tm_epo_hide_cart_average_price == 'no' )
												? '<span class="tc-average-price">' . $format_price . '</span>'
												: ''
											)
											: (
											( THEMECOMPLETE_EPO()->tm_epo_hide_cart_average_price == 'no' )
												? '<span class="tc-av-price">' . $format_price . '</span>'
												: ''
											)
											: ( ( $quantity_string ) ? '<span class="tc-quantity-in-cart">' . $quantity_string . '</span>' : '' )
										)

									: ''
								)
								: '',
							'tm_label'        => $section['label'],
							'tm_value'        => apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->html_entity_decode( $value ) ),
							'tc_simple_value' => apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->html_entity_decode( $value_original ) ),
							'tm_price'        => $format_price,
							'tm_total_price'  => $format_price_total,
							'tm_quantity'     => $section['quantity'],
							'tm_image'        => $section['other_data'][0]['images'],
						);
					}
				}
				if ( empty( $section['hidelabelincart'] ) || empty( $section['hidevalueincart'] ) ) {
					$link_data[] = array(
						'name'            => empty( $section['hidelabelincart'] ) ? $section['label'] : '',
						'value'           => ( empty( $section['hidevalueincart'] ) || $section['hidevalueincart'] === 'noprice' ) ? $value_only : '',
						'price'           => $format_price,
						'tm_price'        => $single_price,
						'tm_total_price'  => $format_price_total,
						'tm_quantity'     => $section['quantity'],
						'tm_total_price2' => $format_price_total2,
					);
				}
			}
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "link" ) {
			if ( empty( $price ) || THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart != "normal" ) {
				$price = '';
			} else {
				$price = $this->get_price_for_cart( $price, $cart_item, FALSE, NULL, 0, 0, $section['price_type'] );
			}
			$uni  = uniqid( '' );
			$data = '<div class="tm-extra-product-options">';
			$data .= '<div class="tc-row tm-cart-row">'
			         . '<div class="tc-cell tc-col-4 cpf-name">&nbsp;</div>'
			         . '<div class="tc-cell tc-col-4 cpf-value">&nbsp;</div>'
			         . '<div class="tc-cell tc-col-2 cpf-price">' . esc_html__( 'Price', 'woocommerce' ) . '</div>'
			         . '<div class="tc-cell tc-col-1 cpf-quantity">' . esc_html__( 'Quantity', 'woocommerce' ) . '</div>'
			         . '<div class="tc-cell tc-col-1 cpf-total-price">' . esc_html__( 'Total', 'woocommerce' ) . '</div>'
			         . '</div>';
			foreach ( $link_data as $link ) {
				$data .= '<div class="tc-row tm-cart-row">'
				         . '<div class="tc-cell tc-col-4 cpf-name">' . $link['name'] . '</div>'
				         . '<div class="tc-cell tc-col-4 cpf-value">' . apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->html_entity_decode( $link['value'] ) ) . '</div>'
				         . '<div class="tc-cell tc-col-2 cpf-price">' . $link['tm_price'] . '</div>'
				         . '<div class="tc-cell tc-col-1 cpf-quantity">' . ( ( $link['tm_price'] == '' ) ? '' : $link['tm_quantity'] ) . '</div>'
				         . '<div class="tc-cell tc-col-1 cpf-total-price">' . $link['tm_total_price2'] . '</div>'
				         . '</div>';

			}
			$data         .= '</div>';
			$other_data[] = array(
				// using esc_url on $uni gives a wrong result for our JS code 
				// so we use esc_attr since it is basically a hash and not an actual URL
				'name'  => '<a href="#tm-cart-link-data-' . esc_attr( $uni ) . '" class="tm-cart-link">' . ( ( ! empty( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_additional_options_text : esc_html__( 'Additional options', 'woocommerce-tm-extra-product-options' ) ) . '</a>',
				'value' => $price . '<div id="tm-cart-link-data-' . esc_attr( $uni ) . '" class="tm-cart-link-data tm-hidden">' . $data . '</div>',
				'popuplink' => true,
			);
		}

		return $other_data;

	}

	/**
	 * Populate element id array
	 * and global price array
	 *
	 * @since 5.0
	 *
	 */
	public function populate_arrays( $product_id = 0, $post_data = array(), $cart_item_meta = array(), $form_prefix = FALSE ) {

		if ( $post_data !== FALSE && $cart_item_meta !== FALSE && $this->populate_arrays_set ) {
			return TRUE;
		}

		if ( $form_prefix === FALSE ) {
			$form_prefix = "";

			if ( isset( $cart_item_meta['composite_item'] ) ) {
				$form_prefix = "_" . $cart_item_meta['composite_item'];
			} elseif ( isset( $cart_item_meta['associated_uniqid'] ) ) {
				$form_prefix = str_replace( array( ".", " ", "[" ), "", $cart_item_meta['associated_uniqid'] );
				$form_prefix = "_" . $form_prefix;
			} else {
				if ( ! empty( $post_data['tc_form_prefix'] ) ) {
					$form_prefix = $post_data['tc_form_prefix'];
					$form_prefix = str_replace( "_", "", $form_prefix );
					$form_prefix = "_" . $form_prefix;
				}
			}

			$this->form_prefix = $form_prefix;
		}

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $form_prefix, TRUE, TRUE );

		if ( empty( $cpf_price_array ) ) {
			return FALSE;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];

		if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
			return FALSE;
		}

		$element_id_array = array();

		$global_prices   = array( 'before' => array(), 'after' => array() );
		$global_sections = array();
		foreach ( $global_price_array as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				if ( isset( $field['sections'] ) ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[ $section['sections_placement'] ][ $priority ][ $pid ]['sections'][ $section_id ] = $section;
							$global_sections[ $section['sections_uniqid'] ]                                                  = $section;
							if ( isset( $section['elements'] ) ) {
								foreach ( $section['elements'] as $element_key => $element ) {
									if ( isset( $element["uniqid"] ) && isset( $element['name_inc'] ) ) {
										$element_id_array[ $element["uniqid"] ] = array(
											'name_inc'    => $element['name_inc'],
											'priority'    => $priority,
											'pid'         => $pid,
											'section_id'  => $section_id,
											'element_key' => $element_key,
										);
									}
								}
							}

						}
					}
				}
			}
		}

		if ( $cart_item_meta === FALSE ) {
			return array(
				'element_id_array'   => $element_id_array,
				'global_price_array' => $global_price_array,
				'local_price_array'  => $local_price_array,
				'global_prices'      => $global_prices,
				'global_sections'    => $global_sections,
			);
		}

		$this->element_id_array    = $element_id_array;
		$this->global_price_array  = $global_price_array;
		$this->local_price_array   = $local_price_array;
		$this->global_prices       = $global_prices;
		$this->global_sections     = $global_sections;
		$this->populate_arrays_set = TRUE;

		return TRUE;

	}

	/**
	 * Add item data to the cart
	 *
	 * @param $cart_item_meta
	 * @param $product_id
	 *
	 * @return mixed
	 */
	public function woocommerce_add_cart_item_data( $cart_item_meta, $product_id ) {
		$this->populate_arrays_set = FALSE;

		return $this->add_cart_item_data_helper( $cart_item_meta, $product_id, $_POST );
	}

	/**
	 * Adds data to the cart
	 *
	 * @param      $cart_item_meta
	 * @param      $product_id
	 * @param null $post_data
	 *
	 * @return mixed
	 */
	public function tm_add_cart_item_data( $cart_item_meta, $product_id, $post_data = NULL ) {
		$this->populate_arrays_set = FALSE;

		return $this->add_cart_item_data_helper( $cart_item_meta, $product_id, $post_data );
	}


	/**
	 * Helper for adding data to the cart
	 *
	 * @param      $cart_item_meta
	 * @param      $product_id
	 * @param null $post_data
	 *
	 * @return mixed
	 */
	public function add_cart_item_data_helper( $cart_item_meta, $product_id, $post_data = NULL ) {

		if ( ! is_array( $cart_item_meta ) ) {
			$cart_item_meta = apply_filters( 'wc_epo_add_cart_item_data_no_array', array(), $cart_item_meta );
		}

		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}
		if ( empty( $post_data ) && isset( $_REQUEST['tcajax'] ) ) {
			$post_data = $_REQUEST;
		}

		// Normalize posted strings
		if ( class_exists( 'Normalizer' ) ) {
			foreach ( $post_data as $post_data_key => $post_data_value ) {
				if ( is_array( $post_data_key ) ) {
					$post_data_key = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $post_data_key, "" );
				}
				if ( is_array( $post_data_value ) ) {
					$post_data_value = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $post_data_value, "" );
				}
				$post_data[ Normalizer::normalize( $post_data_key ) ] = Normalizer::normalize( $post_data_value );
			}
		}

		// Workaround to get unique items in cart for bto 
		if ( empty( $cart_item_meta['tmcartepo_bto'] ) ) {
			$terms        = get_the_terms( $product_id, 'product_type' );
			$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
			if ( ( $product_type == 'bto' || $product_type == 'composite' ) &&
			     ( isset( $post_data['add-product-to-cart'] ) && is_array( $post_data['add-product-to-cart'] ) ) ||
			     ( isset( $post_data['wccp_component_selection'] ) && is_array( $post_data['wccp_component_selection'] ) ) ||
			     ( isset( $_GET['wccp_component_selection'] ) && is_array( $_GET['wccp_component_selection'] ) )
			) {
				$copy = array();
				$enum = array();
				if ( isset( $post_data['add-product-to-cart'] ) ) {
					$enum = $post_data['add-product-to-cart'];
				} elseif ( isset( $post_data['wccp_component_selection'] ) && is_array( $post_data['wccp_component_selection'] ) ) {
					$enum = $post_data['wccp_component_selection'];
				} elseif ( isset( $_GET['wccp_component_selection'] ) && is_array( $_GET['wccp_component_selection'] ) ) {
					$enum = $_GET['wccp_component_selection'];
				}
				foreach ( $enum as $bundled_item_id => $bundled_product_id ) {
					$copy = array_merge( $copy, THEMECOMPLETE_EPO_HELPER()->array_filter_key( $post_data, $bundled_item_id, "end" ) );
				}
				$copy                            = THEMECOMPLETE_EPO_HELPER()->array_filter_key( $copy );
				$cart_item_meta['tmcartepo_bto'] = $copy;
			}
		}

		$variation_id        = FALSE;
		$cpf_product_price   = FALSE;
		$per_product_pricing = TRUE;

		if ( isset( $cart_item_meta['composite_item'] ) ) {
			global $woocommerce;
			$cart_contents = $woocommerce->cart->get_cart();

			if ( isset( $cart_item_meta['composite_parent'] ) && ! empty( $cart_item_meta['composite_parent'] ) ) {
				$parent_cart_key = $cart_item_meta['composite_parent'];

				if ( $cart_contents[ $parent_cart_key ]['data'] && is_callable( array( $cart_contents[ $parent_cart_key ]['data'], "contains" ) ) ) {
					$per_product_pricing = $cart_contents[ $parent_cart_key ]['data']->contains( "priced_individually" );
				} else {
					$per_product_pricing = $cart_contents[ $parent_cart_key ]['data']->per_product_pricing;
				}

				if ( $per_product_pricing === 'no' ) {
					$per_product_pricing = FALSE;
				}
			}

			$bundled_item_id = $cart_item_meta['composite_item'];
			if ( isset( $post_data['bto_variation_id'][ $bundled_item_id ] ) ) {
				$variation_id = $post_data['bto_variation_id'][ $bundled_item_id ];
			} elseif ( isset( $post_data['wccp_variation_id'][ $bundled_item_id ] ) ) {
				$variation_id = $post_data['wccp_variation_id'][ $bundled_item_id ];
			}
			if ( isset( $post_data['cpf_bto_price'][ $bundled_item_id ] ) ) {
				$cpf_product_price = $post_data['cpf_bto_price'][ $bundled_item_id ];
			}
		} else {
			if ( isset( $post_data['variation_id'] ) ) {
				$variation_id = $post_data['variation_id'];
			}
			if ( isset( $post_data['cpf_product_price'] ) ) {
				$cpf_product_price = $post_data['cpf_product_price'];
			}
		}
		if (isset($cart_item_meta['associated_priced_individually'])){
			if ( ! $cart_item_meta['associated_priced_individually'] ) {
				$per_product_pricing = FALSE;
			}
		}

		if ( ! $this->populate_arrays( $product_id, $post_data, $cart_item_meta ) ) {
			return $cart_item_meta;
		}

		// If the following key doesn't exist the edit cart link is not being displayed.		
		if ( in_array( $product_type, apply_filters( 'wc_epo_can_be_edited_product_type', array( "simple", "variable" ) ) ) ) {
			$cart_item_meta['tmhasepo'] = 1;
		}

		$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id, 'product' ) );
		$tm_meta_cpf         = themecomplete_get_post_meta( $original_product_id, 'tm_meta_cpf', TRUE );
		if ( ! is_array( $tm_meta_cpf ) ) {
			$tm_meta_cpf = array();
		}
		foreach ( THEMECOMPLETE_EPO()->meta_fields as $key => $value ) {
			$tm_meta_cpf[ $key ] = isset( $tm_meta_cpf[ $key ] ) ? $tm_meta_cpf[ $key ] : $value;
		}

		$price_override = ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == 'no' )
			? 0
			: ( ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == 'yes' )
				? 1
				: ( ! empty( $tm_meta_cpf['price_override'] ) ? 1 : 0 ) );

		if ( ! empty( $price_override ) ) {
			$cart_item_meta['epo_price_override'] = 1;
		}

		$files = array();
		foreach ( $_FILES as $k => $file ) {
			if ( ! empty( $file['name'] ) ) {
				$files[ $k ] = $file['name'];
			}
		}

		$tmcp_post_fields = array_merge( THEMECOMPLETE_EPO_HELPER()->array_filter_key( $post_data ), THEMECOMPLETE_EPO_HELPER()->array_filter_key( $files ) );
		if ( is_array( $tmcp_post_fields ) ) {
			$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
		}

		if ( empty( $cart_item_meta['tmcartepo'] ) ) {
			$cart_item_meta['tmcartepo'] = array();
		}

		if ( empty( $cart_item_meta['tmcartfee'] ) ) {
			$cart_item_meta['tmcartfee'] = array();
		}
		if ( empty( $cart_item_meta['tmpost_data'] ) ) {
			$cart_item_meta['tmpost_data'] = $post_data;
		}

		$cart_item_meta = apply_filters( 'wc_epo_add_cart_item_data_helper', $cart_item_meta );

		if ( empty( $cart_item_meta['tmdata'] ) ) {
			$cart_item_meta['tmdata'] = array(
				'tmcp_post_fields'     => $tmcp_post_fields,
				'product_id'           => $product_id,
				'per_product_pricing'  => $per_product_pricing,
				'cpf_product_price'    => $cpf_product_price,
				'variation_id'         => $variation_id,
				'form_prefix'          => $this->form_prefix,
				'tc_added_in_currency' => themecomplete_get_woocommerce_currency(),
			);
		}

		$loop       = 0;
		$field_loop = 0;

		$_return = $this->add_cart_item_data_loop( $this->global_prices, 'before', $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $this->form_prefix, $post_data );
		extract( $_return, EXTR_OVERWRITE );

		// NORMAL FIELDS (to be deprecated) 
		$_return = $this->add_cart_item_data_loop_local( $this->local_price_array, $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $this->form_prefix, $post_data );
		extract( $_return, EXTR_OVERWRITE );

		$_return = $this->add_cart_item_data_loop( $this->global_prices, 'after', $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $this->form_prefix, $post_data );
		extract( $_return, EXTR_OVERWRITE );

		return apply_filters( 'wc_epo_add_cart_item_data', $cart_item_meta );

	}

	/**
	 * Add item data to the cart
	 * NORMAL FIELDS (to be deprecated)
	 *
	 * @param $local_price_array
	 * @param $cart_item_meta
	 * @param $tmcp_post_fields
	 * @param $product_id
	 * @param $per_product_pricing
	 * @param $cpf_product_price
	 * @param $variation_id
	 * @param $field_loop
	 * @param $loop
	 * @param $form_prefix
	 * @param $post_data
	 *
	 * @return array
	 */
	public function add_cart_item_data_loop_local( $local_price_array, $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix, $post_data ) {

		if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

			if ( is_array( $tmcp_post_fields ) ) {

				$getproduct = wc_get_product( $product_id );

				foreach ( $local_price_array as $tmcp ) {
					if ( empty( $tmcp['type'] ) ) {
						continue;
					}

					$current_tmcp_post_fields = array_intersect_key( $tmcp_post_fields, array_flip( THEMECOMPLETE_EPO()->get_post_names( $tmcp['attributes'], $tmcp['type'], $field_loop, $form_prefix ) ) );

					foreach ( $current_tmcp_post_fields as $attribute => $key ) {

						switch ( $tmcp['type'] ) {

							case "checkbox" :
							case "radio" :
							case "select" :
								$_price = THEMECOMPLETE_EPO()->calculate_price( $_POST, $tmcp, $key, $attribute, $per_product_pricing, $cpf_product_price, $variation_id );

								$cart_item_meta['tmcartepo'][]                = array(
									'mode'                => 'local',
									'key'                 => $key,
									'is_taxonomy'         => $tmcp['is_taxonomy'],
									'name'                => $tmcp['name'],
									'value'               => wc_attribute_label( $tmcp['attributes_wpml'][ $key ], $getproduct ),
									'price'               => $_price,
									'section'             => $tmcp['name'],
									'section_label'       => wc_attribute_label( urldecode( $tmcp['label'] ), $getproduct ),
									'percentcurrenttotal' => isset( $post_data[ $attribute . '_hidden' ] ) ? 1 : 0,
									'fixedcurrenttotal'   => 0,
									'quantity'            => 1,
								);
								$cart_item_meta['tmdata']['tmcartepo_data'][] = array( 'key' => $key, 'attribute' => $attribute );
								break;

						}
					}
					if ( in_array( $tmcp['type'], THEMECOMPLETE_EPO()->element_post_types ) ) {
						$field_loop ++;
					}
					$loop ++;

				}
			}
		}

		return array( 'loop' => $loop, 'field_loop' => $field_loop, 'cart_item_meta' => $cart_item_meta );

	}

	/**
	 * Add item data to the cart
	 * BUILDER FIELDS
	 *
	 * @param $global_prices
	 * @param $where
	 * @param $cart_item_meta
	 * @param $tmcp_post_fields
	 * @param $product_id
	 * @param $per_product_pricing
	 * @param $cpf_product_price
	 * @param $variation_id
	 * @param $field_loop
	 * @param $loop
	 * @param $form_prefix
	 * @param $post_data
	 *
	 * @return array
	 */
	public function add_cart_item_data_loop( $global_prices, $where, $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix, $post_data ) {

		foreach ( $global_prices[ $where ] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id => $section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {

							$init_class = "THEMECOMPLETE_EPO_FIELDS_" . $element['type'];
							if ( ! class_exists( $init_class ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["_is_addon"] ) ) {
								$init_class = "THEMECOMPLETE_EPO_FIELDS";
							}
							if ( class_exists( $init_class ) ) {
								$field_obj = new $init_class( $product_id, $element, $per_product_pricing, $cpf_product_price, $variation_id, $post_data );

								// Cart fees 
								$current_tmcp_post_fields = array_intersect_key( $tmcp_post_fields, array_flip( THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $field_loop, $form_prefix, THEMECOMPLETE_EPO()->cart_fee_name ) ) );
								foreach ( $current_tmcp_post_fields as $attribute => $key ) {
									if ( ! empty( $field_obj->holder_cart_fees ) ) {
										if ( isset( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
											if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
												continue;
											}
										}
										$meta = $field_obj->add_cart_item_data_cart_fees( $attribute, $key );
										if ( is_array( $meta ) ) {
											if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
												foreach ( $meta as $k => $value ) {
													if ( isset( $value["mode"] ) && $value["mode"] !== "products" ) {
														$cart_item_meta['tmcartfee'][]                = $value;
														$cart_item_meta['tmdata']['tmcartfee_data'][] = array( 'key' => $key, 'attribute' => $attribute );
													} else {
														$value['element_name'] = $attribute;
														$cart_item_meta['tmproducts'][] = $value;
													}
												}
											} else {
												if ( isset( $meta["mode"] ) && $meta["mode"] !== "products" ) {
													$cart_item_meta['tmcartfee'][]                = $meta;
													$cart_item_meta['tmdata']['tmcartfee_data'][] = array( 'key' => $key, 'attribute' => $attribute );
												} else {
													$meta['element_name'] = $attribute;
													$cart_item_meta['tmproducts'][] = $meta;
												}
											}
										}
									}
								}

								// Normal fields 
								$current_tmcp_post_fields = array_intersect_key( $tmcp_post_fields, array_flip( THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $field_loop, $form_prefix, "" ) ) );
								foreach ( $current_tmcp_post_fields as $attribute => $key ) {
									if ( ! empty( $field_obj->holder ) ) {
										if ( isset( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
											if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
												continue;
											}
										}
										$meta = $field_obj->add_cart_item_data( $attribute, $key );

										if ( is_array( $meta ) ) {
											if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
												foreach ( $meta as $k => $value ) {
													if ( isset( $value["mode"] ) && $value["mode"] !== "products" ) {
														$cart_item_meta['tmcartepo'][]                = $value;
														$cart_item_meta['tmdata']['tmcartepo_data'][] = array( 'key' => $key, 'attribute' => $attribute );
													} else {
														$value['element_name'] = $attribute;
														$cart_item_meta['tmproducts'][] = $value;
													}
												}
											} else {
												if ( isset( $meta["mode"] ) && $meta["mode"] !== "products" ) {
													$cart_item_meta['tmcartepo'][]                = $meta;
													$cart_item_meta['tmdata']['tmcartepo_data'][] = array( 'key' => $key, 'attribute' => $attribute );
												} else {
													$meta['element_name'] = $attribute;
													$cart_item_meta['tmproducts'][] = $meta;
												}
											}
										}
									}
								}

								$cart_item_meta = apply_filters( 'wc_epo_add_cart_item_data_loop', $cart_item_meta, $field_obj, $tmcp_post_fields, $element, $field_loop, $form_prefix, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $post_data );

								unset( $field_obj ); // clear memory
							}

							if ( in_array( $element['type'], THEMECOMPLETE_EPO()->element_post_types ) ) {
								$field_loop ++;
							}
							$loop ++;

						}
					}
				}
			}
		}

		return array( 'loop' => $loop, 'field_loop' => $field_loop, 'cart_item_meta' => $cart_item_meta );

	}

	/**
	 * Validates the cart data
	 *
	 * @since 1.0
	 */
	public function woocommerce_add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		// disables add_to_cart_button class on shop page
		if ( is_ajax() && THEMECOMPLETE_EPO()->tm_epo_force_select_options == "display" && ! isset( $_REQUEST['tcaddtocart'] ) ) {

			$has_epo = THEMECOMPLETE_EPO_API()->has_options( $product_id );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				return FALSE;
			}

		}

		$is_validate = TRUE;

		// Get product type
		$terms        = get_the_terms( $product_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		if ( $product_type == 'bto' || $product_type == 'composite' ) {

			$bto_data  = maybe_unserialize( get_post_meta( $product_id, '_bto_data', TRUE ) );
			$valid_ids = array();
			if ( is_array( $bto_data ) ) {
				$valid_ids = array_keys( $bto_data );
			}
			foreach ( $valid_ids as $bundled_item_id ) {

				if ( isset( $_REQUEST['add-product-to-cart'][ $bundled_item_id ] ) && $_REQUEST['add-product-to-cart'][ $bundled_item_id ] !== '' ) {
					$bundled_product_id = $_REQUEST['add-product-to-cart'][ $bundled_item_id ];
				} elseif ( isset( $cart_item_data['composite_data'][ $bundled_item_id ]['product_id'] ) && isset( $_GET['order_again'] ) ) {
					$bundled_product_id = $cart_item_data['composite_data'][ $bundled_item_id ]['product_id'];
				} elseif ( isset( $_REQUEST['add-product-to-cart'][ $bundled_item_id ] ) && $_REQUEST['add-product-to-cart'][ $bundled_item_id ] !== '' ) {
					$bundled_product_id = $_REQUEST['wccp_component_selection'][ $bundled_item_id ];
				} elseif ( isset( $_REQUEST['wccp_component_selection'] ) && isset( $_REQUEST['wccp_component_selection'][ $bundled_item_id ] ) ) {
					$bundled_product_id = $_REQUEST['wccp_component_selection'][ $bundled_item_id ];
				}

				if ( isset( $bundled_product_id ) && ! empty( $bundled_product_id ) ) {

					$_passed = TRUE;

					if ( isset( $_REQUEST['item_quantity'][ $bundled_item_id ] ) && is_numeric( $_REQUEST['item_quantity'][ $bundled_item_id ] ) ) {
						$item_quantity = absint( $_REQUEST['item_quantity'][ $bundled_item_id ] );
					} elseif ( isset( $cart_item_data['composite_data'][ $bundled_item_id ]['quantity'] ) && isset( $_GET['order_again'] ) ) {
						$item_quantity = $cart_item_data['composite_data'][ $bundled_item_id ]['quantity'];
					} elseif ( isset( $_REQUEST['wccp_component_quantity'][ $bundled_item_id ] ) && is_numeric( $_REQUEST['wccp_component_quantity'][ $bundled_item_id ] ) ) {
						$item_quantity = absint( $_REQUEST['wccp_component_quantity'][ $bundled_item_id ] );
					}
					if ( ! empty( $item_quantity ) ) {
						$item_quantity = absint( $item_quantity );

						$_passed = $this->validate_product_id( $bundled_product_id, $item_quantity, $bundled_item_id );
					}

					if ( ! $_passed ) {
						$is_validate = FALSE;
					}

				}
			}
		}

		$tc_form_prefix = "";
		if ( isset( $_REQUEST['tc_form_prefix'] ) ) {
			$tc_form_prefix = $_REQUEST['tc_form_prefix'];
		}
		if ( ! $this->validate_product_id( $product_id, $qty, $tc_form_prefix ) ) {
			$passed = FALSE;
		}

		// Try to validate uploads before they happen
		$files = array();
		foreach ( $_FILES as $k => $file ) {
			if ( ! empty( $file['name'] ) && $file['name'] !== "undefined" ) {
				$file_name = $file['name'];
				if ( ! empty( $file['error'] ) ) {
					$file_error = $file['error'];

					// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
					$upload_error_strings = array( FALSE,
					                               esc_html__( "The uploaded file exceeds the upload_max_filesize directive in php.ini.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "The uploaded file was only partially uploaded.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "No file was uploaded.", 'woocommerce-tm-extra-product-options' ),
					                               '',
					                               esc_html__( "Missing a temporary folder.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "Failed to write file to disk.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "File upload stopped by extension.", 'woocommerce-tm-extra-product-options' )
					);

					if ( is_array( $file_error ) ) {
						foreach ( $file_error as $key => $value ) {
							if ( ! empty( $value ) && ! empty( $file_name[ $key ] ) ) {
								$passed = FALSE;
								if ( isset( $upload_error_strings[ $value ] ) ) {
									wc_add_notice( $upload_error_strings[ $value ], 'error' );
								}
							}
						}
					} else {
						$passed = FALSE;
						if ( isset( $upload_error_strings[ $file_error ] ) ) {
							wc_add_notice( $upload_error_strings[ $file_error ], 'error' );
						}
					}

				}
				add_filter( 'upload_mimes', array( THEMECOMPLETE_EPO(), 'upload_mimes_trick' ) );
				if ( is_array( $file_name ) ) {
					foreach ( $file_name as $key => $value ) {
						if ( ! empty( $value ) ) {
							$check_filetype = wp_check_filetype( $value );
							$check_filetype = $check_filetype['ext'];
							if ( ! $check_filetype && ! empty( $file['name'] ) ) {
								$passed = FALSE;
								wc_add_notice( esc_html__( "Sorry, this file type is not permitted for security reasons.", 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $value, PATHINFO_EXTENSION ) . ')', 'error' );
							}
						}
					}
				} else {
					$check_filetype = wp_check_filetype( $file['name'] );
					$check_filetype = $check_filetype['ext'];

					if ( ! $check_filetype && ! empty( $file['name'] ) ) {
						$passed = FALSE;
						wc_add_notice( esc_html__( "Sorry, this file type is not permitted for security reasons.", 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $file['name'], PATHINFO_EXTENSION ) . ')', 'error' );
					}
				}
				remove_filter( 'upload_mimes', array( THEMECOMPLETE_EPO(), 'upload_mimes_trick' ) );

			}

		}

		if ( ! $is_validate ) {
			$passed = FALSE;
		}

		return apply_filters( 'tm_add_to_cart_validation', $passed );

	}

	/**
	 * Validates builder options
	 *
	 * @param $global_sections
	 * @param $global_prices
	 * @param $where
	 * @param $tmcp_post_fields
	 * @param $passed
	 * @param $loop
	 * @param $form_prefix
	 *
	 * @return array
	 */
	public function validate_product_id_loop( $global_sections, $global_prices, $where, $tmcp_post_fields, $passed, $loop, $form_prefix ) {

		foreach ( $global_prices[ $where ] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id => $section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {

							if ( in_array( $element['type'], THEMECOMPLETE_EPO()->element_post_types ) ) {
								$loop ++;
							}

							if ( isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] )
							     && isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] )
							     && THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] != "display"
							     && THEMECOMPLETE_EPO()->is_visible( $element, $section, $global_sections, $form_prefix )
							) {

								$_passed  = TRUE;
								$_message = FALSE;

								$init_class = "THEMECOMPLETE_EPO_FIELDS_" . $element['type'];
								if ( ! class_exists( $init_class ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["_is_addon"] ) ) {
									$init_class = "THEMECOMPLETE_EPO_FIELDS";
								}
								if ( class_exists( $init_class ) ) {
									$field_obj = new $init_class();
									$_passed   = $field_obj->validate_field( $tmcp_post_fields, $element, $loop, $form_prefix );
									$_message  = isset( $_passed["message"] ) ? $_passed["message"] : FALSE;
									$_passed   = isset( $_passed["passed"] ) ? $_passed["passed"] : FALSE;
									unset( $field_obj ); // clear memory
								}

								if ( ! $_passed ) {

									$passed = FALSE;
									if ( $_message !== FALSE && is_array( $_message ) ) {
										foreach ( $_message as $key => $value ) {
											if ( $value == 'required' ) {
												wc_add_notice( sprintf( esc_html__( '"%s" is a required field.', 'woocommerce-tm-extra-product-options' ), $element['label'] ), 'error' );
											} else {
												wc_add_notice( $value, 'error' );
											}
										}
									}

								}
							}

						}
					}
				}
			}
		}

		return array( 'loop' => $loop, 'passed' => $passed );

	}

	/**
	 * Validates a product
	 *
	 * @param        $product_id
	 * @param        $qty
	 * @param string $form_prefix
	 *
	 * @return bool
	 */
	public function validate_product_id( $product_id, $qty, $form_prefix = "" ) {

		$passed = TRUE;

		if ( $form_prefix ) {
			$form_prefix = "_" . $form_prefix;
		}

		$populate_arrays = $this->populate_arrays( $product_id, FALSE, FALSE, $form_prefix );
		if ( ! $populate_arrays ) {
			return $passed;
		}

		$global_prices      = $populate_arrays['global_prices'];
		$global_sections    = $populate_arrays['global_sections'];
		$global_price_array = $populate_arrays['global_price_array'];
		$local_price_array  = $populate_arrays['local_price_array'];

		if ( ( ! empty( $global_price_array ) && is_array( $global_price_array ) && count( $global_price_array ) > 0 ) || ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) ) {
			$tmcp_post_fields = THEMECOMPLETE_EPO_HELPER()->array_filter_key( $_REQUEST );
			if ( is_array( $tmcp_post_fields ) && ! empty( $tmcp_post_fields ) && count( $tmcp_post_fields ) > 0 ) {
				$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
			}

			$loop = - 1;

			$_return = $this->validate_product_id_loop( $global_sections, $global_prices, 'before', $tmcp_post_fields, $passed, $loop, $form_prefix );
			extract( $_return, EXTR_OVERWRITE );

			if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

				foreach ( $local_price_array as $tmcp ) {

					if ( in_array( $tmcp['type'], THEMECOMPLETE_EPO()->element_post_types ) ) {
						$loop ++;
					}
					if ( empty( $tmcp['type'] ) || empty( $tmcp['required'] ) ) {
						continue;
					}

					if ( $tmcp['required'] ) {

						$tmcp_attributes = THEMECOMPLETE_EPO()->get_post_names( $tmcp['attributes'], $tmcp['type'], $loop, $form_prefix );
						$_passed         = TRUE;

						switch ( $tmcp['type'] ) {

							case "checkbox" :
								$_check = array_intersect( $tmcp_attributes, array_keys( $tmcp_post_fields ) );
								if ( empty( $_check ) || count( $_check ) == 0 ) {
									$_passed = FALSE;
								}
								break;

							case "radio" :
								foreach ( $tmcp_attributes as $attribute ) {
									if ( ! isset( $tmcp_post_fields[ $attribute ] ) ) {
										$_passed = FALSE;
									}
								}
								break;

							case "select" :
								foreach ( $tmcp_attributes as $attribute ) {
									if ( ! isset( $tmcp_post_fields[ $attribute ] ) || $tmcp_post_fields[ $attribute ] == "" ) {
										$_passed = FALSE;
									}
								}
								break;

						}

						if ( ! $_passed ) {
							$passed = FALSE;
							wc_add_notice( sprintf( esc_html__( '"%s" is a required field.', 'woocommerce-tm-extra-product-options' ), $tmcp['label'] ), 'error' );

						}
					}
				}

			}

			$_return = $this->validate_product_id_loop( $global_sections, $global_prices, 'after', $tmcp_post_fields, $passed, $loop, $form_prefix );
			extract( $_return, EXTR_OVERWRITE );

		}

		return $passed;

	}

	/**
	 * Alter the product thumbnail in cart
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_item_thumbnail( $image = "", $cart_item = array(), $cart_item_key = "" ) {

		$_image = array();
		$_alt   = array();
		if ( isset( $cart_item['tmcartepo'] ) && is_array( $cart_item['tmcartepo'] ) ) {
			foreach ( $cart_item['tmcartepo'] as $key => $value ) {
				if ( ! empty( $value['changes_product_image'] ) ) {
					if ( $value['changes_product_image'] == 'images' ) {
						if ( isset( $value['use_images'] ) && $value['use_images'] == 'images' && isset( $value['images'] ) ) {
							$_image[] = $value['images'];
							$_alt[]   = $value['value'];
						}
					} elseif ( $value['changes_product_image'] == 'custom' ) {
						if ( isset( $value['imagesp'] ) ) {
							$_image[] = $value['imagesp'];
							$_alt[]   = $value['value'];
						}
					}
				}
			}
		}
		if ( count( $_image ) == 0 ) {
			if ( isset( $cart_item['tmcartfee'] ) && is_array( $cart_item['tmcartfee'] ) ) {
				foreach ( $cart_item['tmcartfee'] as $key => $value ) {
					if ( ! empty( $value['changes_product_image'] ) ) {
						if ( $value['changes_product_image'] == 'images' ) {
							if ( isset( $value['use_images'] ) && $value['use_images'] == 'images' && isset( $value['images'] ) ) {
								$_image[] = $value['images'];
								$_alt[]   = $value['value'];
							}
						} elseif ( $value['changes_product_image'] == 'custom' ) {
							if ( isset( $value['imagesp'] ) ) {
								$_image[] = $value['imagesp'];
								$_alt[]   = $value['value'];
							}
						}
					}
				}
			}
		}
		if ( count( $_image ) > 0 ) {
			$current = 0;
			for ( $i = 0; $i <= count( $_image ); $i ++ ) {
				if ( ! empty( $_image[ $i ] ) ) {
					$current = $i;
				}
			}
			if ( ! empty( $_image[ $current ] ) ) {
				$size       = 'shop_thumbnail';
				$dimensions = wc_get_image_size( $size );
				$image      = apply_filters( 'tm_woocommerce_img',
					'<img src="' . apply_filters( 'tm_woocommerce_img_src', $_image[ $current ] )
					. '" alt="'
					. esc_attr( strip_tags( $_alt[ $current ] ) )
					. '" width="' . esc_attr( $dimensions['width'] )
					. '" class="tc-thumbnail woocommerce-placeholder wp-post-image" height="'
					. esc_attr( $dimensions['height'] )
					. '" />', $size, $dimensions );
			}
		}

		return $image;

	}

	/**
	 * Ensures correct price is shown on minicart
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_mini_cart() {

		WC()->cart->calculate_totals();

	}


	/**
	 * Cart edit key
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_loaded_from_session() {

		$cart_contents = WC()->cart->cart_contents;

		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['tcremoved'] ) && ! empty( $cart_item['tcremoved'] ) ) {
					$product = $cart_item['data'];
					unset( WC()->cart->cart_contents[ $cart_item_key ] );
					wc_add_notice( sprintf( esc_html__( '%1$s has been removed from your cart because it has since been modified. You can add it back to your cart %2$s.', 'woocommerce-tm-extra-product-options' ), $product->get_name(), '<a href="' . $product->get_permalink() . '">' . esc_html__( 'here', 'woocommerce-tm-extra-product-options' ) . '</a>' ), 'notice' );
				} else {
					WC()->cart->cart_contents[ $cart_item_key ][ THEMECOMPLETE_EPO()->cart_edit_key_var ] = $cart_item_key;
				}
			}
		}

	}

	/**
	 * Calculate totals on remove from cart/update
	 *
	 * @since 1.0
	 */
	public function woocommerce_update_cart_action_cart_updated( $cart_updated = FALSE ) {

		$cart_contents = WC()->cart->cart_contents;
		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['tm_epo_options_prices'] ) ) {
					$cart_updated = TRUE;
				}
			}
		}

		return $cart_updated;

	}

	/**
	 * Support for fee price types
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_calculate_fees( $cart_object = array() ) {

		if ( is_array( $cart_object->cart_contents ) ) {

			$to_currency = themecomplete_get_woocommerce_currency();

			foreach ( $cart_object->cart_contents as $key => $value ) {
				$tax_class      = themecomplete_get_tax_class( $value["data"] );
				$get_tax_status = is_callable( array( $value["data"], 'get_tax_status' ) ) ? $value["data"]->get_tax_status() : $value["data"]->tax_status;
				if ( get_option( 'woocommerce_calc_taxes' ) == "yes" && $get_tax_status == "taxable" ) {
					$tax = TRUE;
				} else {
					$tax = FALSE;
				}

				$tmcartfee = isset( $value['tmcartfee'] ) ? $value['tmcartfee'] : FALSE;
				if ( $tmcartfee && is_array( $tmcartfee ) ) {
					foreach ( $tmcartfee as $cartfee ) {
						$new_price = $cartfee["price"];

						$is_currency = FALSE;
						if ( isset( $cartfee['price_per_currency'] ) && isset( $cartfee['price_per_currency'][ $to_currency ] ) && $cartfee['price_per_currency'][ $to_currency ] != '' ) {
							$new_price   = (float) wc_format_decimal( $cartfee['price_per_currency'][ $to_currency ], FALSE, TRUE );
							$is_currency = TRUE;
						} else {
							$new_price = apply_filters( 'wc_epo_get_current_currency_price', apply_filters( 'woocommerce_tm_epo_price_on_cart', $new_price, $value ) );
						}

						if ( $is_currency && wc_prices_include_tax() ) {
							$this_element = FALSE;
							$builder      = THEMECOMPLETE_EPO()->get_product_tm_epos( themecomplete_get_id( $value["data"] ), $value["tmdata"]["form_prefix"], TRUE, TRUE );
							foreach ( $builder['global'] as $priority => $priorities ) {
								foreach ( $priorities as $pid => $field ) {
									if ( isset( $field['sections'] ) ) {
										foreach ( $field['sections'] as $section_id => $section ) {
											if ( isset( $section['elements'] ) ) {
												foreach ( $section['elements'] as $element ) {
													if ( $element['uniqid'] == $cartfee['section'] ) {
														$this_element = $element;
														break 4;
													}
												}
											}
										}
									}
								}
							}
							$new_price = $this->cacl_fee_price( $new_price, themecomplete_get_id( $value["data"] ), $this_element );
						}

						$hidelabelincart  = isset( $cartfee['hidelabelincart'] ) ? $cartfee['hidelabelincart'] : '';
						$hidevalueincart  = isset( $cartfee['hidevalueincart'] ) ? $cartfee['hidevalueincart'] : '';
						$hidelabelinorder = isset( $cartfee['hidelabelinorder'] ) ? $cartfee['hidelabelinorder'] : '';
						$hidevalueinorder = isset( $cartfee['hidevalueinorder'] ) ? $cartfee['hidevalueinorder'] : '';

						$new_name = "";

						if ( ! $hidelabelincart && ! $hidelabelinorder ) {
							$new_name = $cartfee["name"];
							if ( empty( $new_name ) ) {
								$new_name = esc_html__( "Extra fee", 'woocommerce-tm-extra-product-options' );
							}
						}

						if ( $new_name && ! $hidevalueincart && ! $hidevalueinorder ) {
							$new_name .= apply_filters( 'wc_epo_fee_quantity_separator', " - " );
						}

						if ( ! $hidevalueincart && ! $hidevalueinorder ) {
							if ( isset( $cartfee["display"] ) ) {
								$new_name .= $cartfee["display"];
							} else {
								$new_name .= $cartfee["value"];
							}
						}

						// Fee names cannot be empty
						if ( empty( $new_name ) ) {
							$new_name = esc_html__( "Extra fee", 'woocommerce-tm-extra-product-options' );
						}

						if ( floatval( $cartfee["quantity"] ) > 1 ) {
							$new_name .= apply_filters( 'wc_epo_fee_quantity_times', " &times; " ) . $cartfee["quantity"];
						}
						$canbadded = TRUE;

						$fees = array();
						if ( is_object( $cart_object ) && is_callable( array( $cart_object, "get_fees" ) ) ) {
							$fees = $cart_object->get_fees();
						} else {
							$fees = $cart_object->fees;
						}
						if ( is_array( $fees ) ) {
							foreach ( $fees as $fee ) {
								if ( $fee->id == sanitize_title( $new_name ) ) {
									if ( apply_filters( 'wc_epo_add_same_fee', TRUE, $new_price, $fee->amount ) ) {
										$fee->amount = $fee->amount + (float) $new_price;
									}
									$canbadded = FALSE;
									break;
								}
							}
						}
						if ( $canbadded ) {

							$current_tax       = $tax;
							$current_tax_class = $tax_class;
							if ( isset( $cartfee["include_tax_for_fee_price_type"] ) && $cartfee["include_tax_for_fee_price_type"] !== '' ) {
								if ( $cartfee["include_tax_for_fee_price_type"] == "yes" ) {
									$current_tax = TRUE;
								} elseif ( $cartfee["include_tax_for_fee_price_type"] == "no" ) {
									$current_tax = FALSE;
								}
							}
							if ( isset( $cartfee["tax_class_for_fee_price_type"] ) && $cartfee["tax_class_for_fee_price_type"] !== '' ) {
								$current_tax_class = $cartfee["tax_class_for_fee_price_type"];
								if ( $cartfee["tax_class_for_fee_price_type"] === '@' ) {
									$current_tax_class = '';
								}
							}
							$cart_object->add_fee( $new_name, $new_price, $current_tax, $current_tax_class );
						}
					}
				}
			}
		}

	}

	/**
	 * Calculates the fee price
	 *
	 * @since 1.0
	 */
	public function cacl_fee_price( $price = "", $product_id = "", $element = FALSE, $attribute = "" ) {

		global $woocommerce;
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return $price;
		}

		$taxable   = $product->is_taxable();
		$tax_class = themecomplete_get_tax_class( $product );

		if ( $element ) {
			if ( isset( $element['include_tax_for_fee_price_type'] ) ) {
				if ( $element['include_tax_for_fee_price_type'] == "no" ) {
					$taxable = FALSE;
				}
				if ( $element['include_tax_for_fee_price_type'] == "yes" ) {
					$taxable = TRUE;
				}
			}
			if ( isset( $element['tax_class_for_fee_price_type'] ) ) {
				$tax_class = $element['tax_class_for_fee_price_type'];
			}
		}

		// Taxable
		if ( $taxable ) {

			if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' ) {
				$tax_rates = WC_Tax::get_base_tax_rates( $tax_class );
				$taxes     = WC_Tax::calc_tax( $price, $tax_rates, TRUE );
				$price     = WC_Tax::round( $price - array_sum( $taxes ) );
			}

			return $price;

		}

		return $price;

	}

	/**
	 * Adds the Empty cart button
	 *
	 * @since 1.0
	 */
	public function add_empty_cart_button() {
		$text = ( ! empty( THEMECOMPLETE_EPO()->tm_epo_empty_cart_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_empty_cart_text : esc_html__( 'Empty cart', 'woocommerce-tm-extra-product-options' );
		echo '<input type="submit" class="tm-clear-cart-button button" name="tm_empty_cart" value="' . esc_attr( $text ) . '" />';

	}

	/**
	 * Empties the cart
	 *
	 * @since 1.0
	 */
	public function tm_empty_cart() {

		if ( ! isset( WC()->cart ) || WC()->cart == '' ) {
			WC()->cart = new WC_Cart();
		}
		WC()->cart->empty_cart( TRUE );

	}

	/**
	 * Empties the cart from the clear cart button
	 *
	 * @since 1.0
	 */
	public function clear_cart() {

		if ( isset( $_POST['tm_empty_cart'] ) ) {
			$this->tm_empty_cart();
		}

	}

	/**
	 * Override templates for Cart advanced template system
	 *
	 * @since 1.0
	 */
	public function tm_wc_get_template( $located = "", $template_name = "", $args = "", $template_path = "", $default_path = "" ) {

		$templates = array( 'cart/cart-item-data.php' );

		if ( in_array( $template_name, $templates ) ) {
			$_located = wc_locate_template( $template_name, THEMECOMPLETE_EPO_DISPLAY()->get_namespace(), THEMECOMPLETE_EPO_TEMPLATE_PATH );
			if ( file_exists( $_located ) ) {
				$located = $_located;
			}
		}

		return $located;

	}

	/**
	 * Advanced template system - Alter item subtoal
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_item_subtotal( $subtotal = "", $cart_item = "", $cart_item_key = "" ) {

		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		// is_cart() is used to filter out the review order screen
		if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "advanced" ) {

			if ( is_cart() ) {

				$original_subtotal = $subtotal;

				$subtotal = "";

				if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart == "normal" ) {
					if ( isset( $cart_item['tm_epo_product_after_adjustment'] ) && isset( THEMECOMPLETE_EPO()->tm_epo_dpd_enable ) && THEMECOMPLETE_EPO()->tm_epo_dpd_enable == "no" ) {
						$price = $cart_item['tm_epo_product_after_adjustment'];
					} else {
						$price = isset($cart_item['tm_epo_product_original_price'])?$cart_item['tm_epo_product_original_price']:$_product->get_price();
						$price = apply_filters( 'wc_epo_discounted_price', $price, wc_get_product( $cart_item['product_id'] ), $cart_item_key, TRUE );
					}
					$price    = floatval($price) * floatval($cart_item['quantity']);
					$subtotal .= apply_filters( 'wc_tm_epo_ac_subtotal_price', $this->get_price_for_cart( $price, $cart_item, "" ), $cart_item_key, $cart_item, $_product, $product_id );
				} else {
					$subtotal .= apply_filters( 'wc_tm_epo_ac_subtotal_price', $subtotal, $cart_item_key, $cart_item, $_product, $product_id );
				}

				$subtotal .= $this->cart_add_option_rows( $original_subtotal, $cart_item_key, $cart_item, $_product, $product_id );

			} else if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || THEMECOMPLETE_EPO()->wc_vars["is_checkout"] ) {

				if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart == "normal" ) {
					if ( isset( $cart_item['tm_epo_product_after_adjustment'] ) && THEMECOMPLETE_EPO()->tm_epo_dpd_enable == "no" ) {
						$price = $cart_item['tm_epo_product_after_adjustment'];
					} else {
						$price = isset($cart_item['tm_epo_product_original_price'])?$cart_item['tm_epo_product_original_price']:$_product->get_price();
						$price = apply_filters( 'wc_epo_discounted_price', $price, wc_get_product( $cart_item['product_id'] ), $cart_item_key );
					}
					$price = floatval($price) * floatval($cart_item['quantity']);

					$subtotal = apply_filters( 'wc_tm_epo_ac_subtotal_prices', $this->get_price_for_cart( $price, $cart_item, "" ), $cart_item, $cart_item_key );

					$subtotal .= $this->checkout_add_option_rows( $cart_item_key, $cart_item, $_product, $product_id );

				}

			}

		}

		return $subtotal;

	}

	/**
	 * Advanced template system - Alter product quantity
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_item_quantity( $product_quantity = "", $cart_item_key = "", $cart_item = "" ) {

		$this->saved_product_quantity = $product_quantity;

		$no_epo = apply_filters( 'wc_epo_no_epo_in_cart', empty( $cart_item["tmcartepo"] ), $cart_item );

		if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "advanced" && ! $no_epo ) {

			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product->is_sold_individually() ) {
				$product_quantity = apply_filters( 'wc_tm_epo_ac_product_qty', '1', $cart_item_key, $cart_item, $_product, $product_id );
			} else {
				$product_quantity = apply_filters( 'wc_tm_epo_ac_product_qty', $cart_item['quantity'], $cart_item_key, $cart_item, $_product, $product_id );
			}

		}

		return $product_quantity;

	}

	/**
	 * Advanced template system - Alter product price
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_item_price( $price = "", $cart_item = "", $cart_item_key = "" ) {

		// is_cart() is used to filter out the mini cart hook
		if ( is_cart() && THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "advanced" ) {

			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart == "normal" ) {
				$variation_id = $cart_item['variation_id'];
				if ( empty( $variation_id ) ) {
					$variation_id = $product_id;
				}
				$original_product = wc_get_product( $variation_id );

				$price = isset($cart_item['tm_epo_product_original_price'])?$cart_item['tm_epo_product_original_price']:$cart_item['data']->get_price();
				$price = apply_filters( 'wc_tm_epo_ac_product_price', $this->get_price_for_cart( $price, $cart_item, "" ), $cart_item_key, $cart_item, $original_product, $product_id );
			} else {
				$price = apply_filters( 'wc_tm_epo_ac_product_price', $price, $cart_item_key, $cart_item, $_product, $product_id );
			}

		}

		return $price;

	}

	/**
	 * Advanced template system - Add custom class name
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_item_class( $class = "", $cart_item = "", $cart_item_key = "" ) {

		$no_epo = apply_filters( 'wc_epo_no_epo_in_cart', empty( $cart_item["tmcartepo"] ), $cart_item );

		// is_cart() is used to filter out the review order screen
		if ( is_cart() && THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "advanced" && ! $no_epo ) {
			$class .= " tm-epo-cart-row-product";
		} else {
			$class .= " tm-epo-cart-row-product-noepo";
		}

		return $class;

	}

	/**
	 * Custom actions running for advanced template system
	 *
	 * @since 1.0
	 */
	public function checkout_add_option_rows( $cart_item_key = "", $cart_item = "", $_product = "", $product_id = "" ) {

		$out = array();;
		$other_data = array();
		if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart == "normal" ) {
			$other_data = $this->get_item_data_array( array(), $cart_item );
		}
		$odd = 1;
		foreach ( $other_data as $key => $value ) {
			$zebra_class = "odd ";
			if ( ! $odd ) {
				$zebra_class = "even ";
				$odd         = 2;
			}
			$out[] = '</td></tr>';
			$out[] = '<tr class="tm-epo-checkout-row '
			         . $zebra_class
			         . esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) )
			         . '">';

			if ( empty( $value['tm_label'] ) ) {

				$name = '<div class="mc-epo-checkout-option-value tc-epo-checkout-no-label">'
				        . $value['tc_simple_value']
				        . ' <strong class="tm-epo-checkout-quantity">'
				        . sprintf( '&times; %s', $value['tm_quantity'] * $cart_item['quantity'] )
				        . '</strong>'
				        . '</div>';

			} else {

				$name = '<div class="tm-epo-checkout-option-label">'
				        . $value['tm_label']
				        . ' <strong class="tm-epo-checkout-quantity">'
				        . apply_filters( 'wc_tm_epo_ac_qty', sprintf( '&times; %s', $value['tm_quantity'] * $cart_item['quantity'] ), $cart_item_key, $cart_item, $value, $_product, $product_id )
				        . '</strong>'
				        . '</div>'
				        . '<div class="tm-epo-checkout-option-value">' . $value['tc_simple_value'] . '</div>';
				        
			}
			$out[] = '<td class="tm-epo-checkout-name">' . $name . '</td>';
			$out[] = '<td class="tm-epo-checkout-subtotal">' . $value['tm_total_price'];

			$odd --;
		}

		return implode( "", $out );

	}

	/**
	 * Custom actions running for advanced template system
	 *
	 * @since 1.0
	 */
	public function cart_add_option_rows( $subtotal = "", $cart_item_key = "", $cart_item = "", $_product = "", $product_id = "" ) {

		$out        = array();
		$other_data = array();
		if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart == "normal" ) {
			$other_data = $this->get_item_data_array( array(), $cart_item );
		}
		$odd = 1;
		foreach ( $other_data as $key => $value ) {
			$zebra_class = "odd ";
			if ( ! $odd ) {
				$zebra_class = "even ";
				$odd         = 2;
			}

			$out[]     = '</td></tr>';
			$out[]     = '<tr class="tm-epo-cart-row ' . $zebra_class . esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ) . '">';
			$out[]     = '<td class="product-remove">&nbsp;</td>';
			$thumbnail = '&nbsp;';

			$out[] = '<td class="product-thumbnail">' . $thumbnail . '</td>';
			$name  = '<div class="tm-epo-cart-option-value tc-epo-cart-no-label">' . $value['tc_simple_value'] . '</div>';
			if ( ! empty( $value['tm_label'] ) ) {
				$name = '<div class="tm-epo-cart-option-label">' . $value['tm_label'] . '</div>' . '<div class="tc-epo-cart-option-value">' . $value['tc_simple_value'] . '</div>';
			}
			$out[] = '<td class="product-name">' . $name . '</td>';
			$out[] = '<td class="product-price">' . $value['tm_price'] . '</td>';
			$out[] = '<td class="product-quantity">' . apply_filters( 'wc_tm_epo_ac_qty', $value['tm_quantity'] * $cart_item['quantity'], $cart_item_key, $cart_item, $value, $_product, $product_id ) . '</td>';
			$out[] = '<td class="product-subtotal">' . $value['tm_total_price'];

			$odd --;
		}
		if ( is_array( $other_data ) && count( $other_data ) > 0 ) {
			$out[] = '<tr class="tm-epo-cart-row tc-epo-cart-row-total ' . esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ) . '">';
			$out[] = '<td class="product-remove">&nbsp;</td>';
			$out[] = '<td class="product-thumbnail">&nbsp;</td>';
			$out[] = '<td class="product-name">&nbsp;</td>';
			$out[] = '<td class="product-price">&nbsp;</td>';

			$out[] = '<td class="product-quantity">' . ( $this->saved_product_quantity !== NULL ? $this->saved_product_quantity : '' ) . '</td>';
			$out[] = '<td class="product-subtotal">' . $subtotal;
		}

		return implode( "", $out );

	}

	/**
	 * Adds edit link on product title in cart
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_item_name( $title = "", $cart_item = array(), $cart_item_key = "" ) {

		if ( ! THEMECOMPLETE_EPO()->wc_vars["is_cart"] && ( defined( 'WOOCOMMERCE_CHECKOUT' ) || THEMECOMPLETE_EPO()->wc_vars["is_checkout"] ) && $this->added_woocommerce_checkout_cart_item_quantity === FALSE ) {
			add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'woocommerce_cart_item_name' ), 10, 3 );
			$this->added_woocommerce_checkout_cart_item_quantity = 1;

			return $title;
		}

		if ( apply_filters( 'wc_epo_no_edit_options', FALSE, $title, $cart_item, $cart_item_key ) ) {
			return $title;
		}
		if ( ! isset( $cart_item['data'] ) || ! isset( $cart_item['tmhasepo'] ) || isset( $cart_item['associated_key'] ) ) {
			return $title;
		}
		if ( apply_filters( 'wc_epo_override_edit_options', TRUE, $title, $cart_item, $cart_item_key ) ) {
			if ( ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) && ! ( THEMECOMPLETE_EPO()->wc_vars["is_cart"] || THEMECOMPLETE_EPO()->wc_vars["is_checkout"] ) ) || isset( $cart_item['composite_item'] ) || isset( $cart_item['composite_data'] ) ) {
				return $title;
			}
			// Chained products cannot be edited
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ) {
				return $title;
			}
			// Cannot function with TLS eDocBuidler
			if ( isset( $cart_item['eDocBuilderID'] ) ) {
				return $title;
			}
		}
		$product = $cart_item['data'];

		$link = apply_filters( 'wc_epo_edit_options_get_permalink', $product->get_permalink( $cart_item ), $product, $title, $cart_item, $cart_item_key );
		$link = add_query_arg(
			array(
				THEMECOMPLETE_EPO()->cart_edit_key_var => $cart_item_key,
				'cart_item_key'                        => $cart_item_key,
			)
			, $link );
		//wp_nonce_url escapes the url
		$link                                                = wp_nonce_url( $link, 'tm-edit' );
		$title                                               .= '<a href="' . esc_url( $link ) . '" class="tm-cart-edit-options">' . ( ( ! empty( THEMECOMPLETE_EPO()->tm_epo_edit_options_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_edit_options_text ) : esc_html__( 'Edit options', 'woocommerce-tm-extra-product-options' ) ) . '</a>';
		$this->added_woocommerce_checkout_cart_item_quantity = FALSE;

		return apply_filters( 'wc_epo_edit_options_link', $title, $cart_item, $cart_item_key );

	}

	/**
	 * Alters add to cart text when editing a product
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_add_to_cart_button() {

		if ( THEMECOMPLETE_EPO()->is_edit_mode() ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'woocommerce_product_single_add_to_cart_text' ), 9999 );
			echo '<input type="hidden" name="' . esc_attr( THEMECOMPLETE_EPO()->cart_edit_key_var_alt ) . '" value="' . esc_attr( THEMECOMPLETE_EPO()->cart_edit_key ) . '" />';
		}

	}

	/**
	 * Alter add to cart button text when in edit mode
	 *
	 * @return string|void
	 */
	public function woocommerce_product_single_add_to_cart_text() {
		return ( ! empty( THEMECOMPLETE_EPO()->tm_epo_update_cart_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_update_cart_text : esc_attr__( 'Update cart', 'woocommerce' );
	}

	/**
	 * Alters the cart item key when editing a product
	 *
	 * @return string|void
	 */
	public function woocommerce_add_to_cart( $cart_item_key = "", $product_id = "", $quantity = "", $variation_id = "", $variation = "", $cart_item_data = "" ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {

			$this->new_add_to_cart_key = $cart_item_key;

		} else {

			if ( is_array( $cart_item_data ) && isset( $cart_item_data['tmhasepo'] ) ) {

				$cart_contents = WC()->cart->cart_contents;

				if (
					is_array( $cart_contents ) &&
					isset( $cart_contents[ $cart_item_key ] ) &&
					! empty( $cart_contents[ $cart_item_key ] ) &&
					! isset( $cart_contents[ $cart_item_key ][ THEMECOMPLETE_EPO()->cart_edit_key_var ] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ][ THEMECOMPLETE_EPO()->cart_edit_key_var ] = $cart_item_key;
				}

			}
		}

	}

	/**
	 * Redirect to cart when updating information for a cart item
	 *
	 * @return string|void
	 */
	public function woocommerce_add_to_cart_redirect( $url = "" ) {

		if ( empty( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
			return $url;
		}
		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			if ( ! THEMECOMPLETE_EPO_HELPER()->is_ajax_request() ) {
				$url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
			}
		}

		return $url;

	}

	/**
	 * Remove product from cart when editing a product
	 *
	 * @return string|void
	 */
	public function remove_previous_product_from_cart( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			if ( isset( $this->new_add_to_cart_key ) ) {
				if ( $this->new_add_to_cart_key == $cart_item_key && isset( $_POST['quantity'] ) ) {
					WC()->cart->set_quantity( $this->new_add_to_cart_key, $_POST['quantity'], TRUE );
				}
			}
		}

		return $passed;

	}

	/**
	 * Alter add to cart message
	 *
	 * @return string|void
	 */
	public function wc_add_to_cart_message_html( $message = "", $products ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key && isset( $this->new_add_to_cart_key ) ) {
			$titles = array();
			$count  = 0;
			foreach ( $products as $product_id => $qty ) {
				/* translators: %s: product name */
				$titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . sprintf( esc_html_x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), wp_strip_all_tags( get_the_title( $product_id ) ) );
				$count    += $qty;
			}
			$titles = array_filter( $titles );
			/* translators: %s: product name */
			$added_text = sprintf( esc_html( _n( '%s has been updated.', '%s have been updated.', $count, 'woocommerce-tm-extra-product-options' ) ), wc_format_list_of_items( $titles ) );

			$message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s',
				esc_url( wc_get_page_permalink( 'cart' ) ),
				esc_html__( 'View cart', 'woocommerce' ),
				esc_html( $added_text ) );
		}

		return $message;

	}

	/**
	 * Change quantity value when editing a cart item
	 *
	 * @return string|void
	 */
	public function tm_woocommerce_before_add_to_cart_form() {
		add_filter( 'woocommerce_quantity_input_args', array( $this, 'tm_woocommerce_quantity_input_args' ), 9999, 2 );
	}

	/**
	 * Remove filter for change quantity value when editing a cart item
	 *
	 * @return string|void
	 */
	public function tm_woocommerce_after_add_to_cart_form() {
		remove_filter( 'woocommerce_quantity_input_args', array( $this, 'tm_woocommerce_quantity_input_args' ), 9999 );
	}

	/**
	 * Change quantity value when editing a cart item
	 *
	 * @return string|void
	 */
	public function tm_woocommerce_quantity_input_args( $args = "", $product = "" ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( isset( $cart_item["quantity"] ) ) {
				$args["input_value"] = $cart_item["quantity"];
			}
		}

		return $args;

	}

	/**
	 * Advanced template product price fix for override price
	 *
	 * @return string|void
	 */
	public function wc_tm_epo_ac_product_price( $price, $cart_item_key, $cart_item, $_product, $product_id ) {
		$flag = FALSE;
		if ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == "yes" ) {
			$flag = TRUE;
		} elseif ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == "" ) {
			$tm_meta_cpf = themecomplete_get_post_meta( $product_id, 'tm_meta_cpf', TRUE );
			if ( ! is_array( $tm_meta_cpf ) ) {
				$tm_meta_cpf = array();
			}

			if ( ! empty( $tm_meta_cpf['price_override'] ) ) {
				$flag = TRUE;
			}
		}

		if ( isset( $cart_item['tm_epo_options_prices'] ) && floatval( $cart_item['tm_epo_options_prices'] ) > 0 ) {
			$display_price = $price;

			if ( $flag ) {
				$display_price = '';
			}

			return apply_filters( 'wc_epo_ac_override_price', $display_price, $price, $cart_item_key, $cart_item, $_product, $product_id );
		}

		return $price;

	}

	/**
	 * Disables persistent cart
	 *
	 * @param $value
	 * @param $id
	 * @param $key
	 *
	 * @return bool
	 */
	public function turn_off_persi_cart( $value, $id, $key ) {
		if ( $key == '_woocommerce_persistent_cart' ) {
			return FALSE;
		}

		return $value;
	}

}
