<?php
/**
 * Extra Product Options Cart Functionality
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Cart Functionality
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Cart {

	/**
	 * Edit option in cart helper
	 *
	 * @var boolean
	 */
	public $new_add_to_cart_key = false;

	/**
	 * The current product quantity
	 *
	 * @var mixed
	 */
	private $saved_product_quantity = false;

	/**
	 * Helper flag to determin if we should
	 * add theedit link on product title
	 *
	 * @var boolean
	 */
	private $added_woocommerce_checkout_cart_item_quantity = false;

	/**
	 * The element id array
	 *
	 * @var array
	 */
	public $element_id_array = [];

	/**
	 * The global prices array
	 *
	 * @var array
	 */
	public $global_prices = [];

	/**
	 * The global sections array
	 *
	 * @var array
	 */
	public $global_sections = [];

	/**
	 * The current options array
	 *
	 * @var array
	 */
	public $global_price_array = [];

	/**
	 * The current normal options array
	 *
	 * @var array
	 */
	public $local_price_array = [];

	/**
	 * The current from prefix
	 *
	 * @var string
	 */
	public $form_prefix = '';

	/**
	 * If the element ids have been populated
	 *
	 * @var boolean
	 */
	public $populate_arrays_set = false;

	/**
	 * The current cart item meta
	 *
	 * @var array
	 */
	public $cart_item_meta = [];

	/**
	 * The last cart key that was added to the cart
	 *
	 * @var string
	 */
	public $last_added_cart_key = '';

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Cart|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
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
	 * @since 1.0
	 */
	public function __construct() {

		// Alter the cart id upon adding the product to the cart.
		add_filter( 'woocommerce_cart_id', [ $this, 'woocommerce_cart_id' ], 10, 5 );
		// Modifies the cart item.
		add_action( 'woocommerce_add_to_cart', [ $this, 'woocommerce_add_to_cart' ], 12, 6 );
		add_filter( 'woocommerce_before_calculate_totals', [ $this, 'woocommerce_before_calculate_totals' ], 9999, 1 );
		// Load cart data on every page load.
		add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'woocommerce_get_cart_item_from_session' ], 9999, 3 );
		// Gets cart item to display in the frontend.
		add_filter( 'woocommerce_get_item_data', [ $this, 'woocommerce_get_item_data' ], 50, 2 );
		// Add item data to the cart.
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'woocommerce_add_cart_item_data' ], 50, 3 );
		// Validate upon adding to cart.
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'woocommerce_add_to_cart_validation' ], 50, 6 );
		// Alter the product thumbnail in cart.
		add_filter( 'woocommerce_cart_item_thumbnail', [ $this, 'woocommerce_cart_item_thumbnail' ], 50, 3 );
		// Ensures correct price is shown on minicart.
		add_action( 'woocommerce_before_mini_cart_contents', [ $this, 'woocommerce_before_mini_cart' ] );
		// Cart edit key.
		add_action( 'woocommerce_cart_loaded_from_session', [ $this, 'woocommerce_cart_loaded_from_session' ], 0 );
		// Calculate totals on remove from cart/update.
		add_action( 'woocommerce_update_cart_action_cart_updated', [ $this, 'woocommerce_update_cart_action_cart_updated' ], 9999, 1 );
		// Support for fee price types.
		add_action( 'woocommerce_cart_calculate_fees', [ $this, 'woocommerce_cart_calculate_fees' ] );

		// Empty cart button.
		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_clear_cart_button ) {
			add_action( 'woocommerce_cart_actions', [ $this, 'add_empty_cart_button' ] );
			// check for empty-cart get param to clear the cart.
			add_action( 'init', [ $this, 'clear_cart' ] );
		}

		// Override templates.
		if ( apply_filters( 'tm_get_template', true ) ) {
			add_filter( 'wc_get_template', [ $this, 'tm_wc_get_template' ], 10, 2 );
		}

		// Custom actions running for advanced template system.
		add_filter( 'woocommerce_cart_item_subtotal', [ $this, 'woocommerce_cart_item_subtotal' ], 10, 3 );
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'woocommerce_cart_item_quantity' ], 10, 3 );
		add_filter( 'woocommerce_cart_item_price', [ $this, 'woocommerce_cart_item_price' ], 10, 3 );
		add_filter( 'woocommerce_cart_item_class', [ $this, 'woocommerce_cart_item_class' ], 10, 3 );
		add_filter( 'wc_tm_epo_ac_product_price', [ $this, 'wc_tm_epo_ac_product_price' ], 10, 5 );
		add_filter( 'wc_tm_epo_ac_subtotal_price', [ $this, 'wc_tm_epo_ac_product_price' ], 10, 5 );

		// Edit cart item.
		// Adds edit link on product title in cart.
		add_filter( 'woocommerce_cart_item_name', [ $this, 'woocommerce_cart_item_name' ], 50, 3 );
		// Alters add to cart text when editing a product.
		add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'woocommerce_before_add_to_cart_button' ] );
		// Alters the cart item key when editing a product.
		add_action( 'woocommerce_add_to_cart', [ $this, 'edit_woocommerce_add_to_cart' ], 10, 6 );
		// Redirect to cart when updating information for a cart item.
		add_filter( 'woocommerce_add_to_cart_redirect', [ $this, 'woocommerce_add_to_cart_redirect' ], 9999, 1 );
		// Remove product from cart when editing a product.
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'remove_previous_product_from_cart' ], 99999, 6 );
		// Alter add to cart message.
		add_filter( 'wc_add_to_cart_message_html', [ $this, 'wc_add_to_cart_message_html' ], 10, 2 );
		// Change quantity value when editing a cart item.
		add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'tm_woocommerce_before_add_to_cart_form' ], 1 );
		add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'tm_woocommerce_after_add_to_cart_form' ], 9999 );

		// Disables persistent cart.
		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_turn_off_persi_cart ) {
			add_filter( 'get_user_metadata', [ $this, 'turn_off_persi_cart' ], 10, 3 );
			add_filter( 'update_user_metadata', [ $this, 'turn_off_persi_cart' ], 10, 3 );
			add_filter( 'add_user_metadata', [ $this, 'turn_off_persi_cart' ], 10, 3 );
		}

		// Add option specific styles to the cart page.
		add_action( 'woocommerce_after_cart', [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ], 99999 );

	}

	/**
	 * Returns correct formated price for the cart table
	 *
	 * @param float  $price The element price.
	 * @param array  $cart_item The cart item.
	 * @param mixed  $symbol The plus or minus symbol.
	 * @param mixed  $quantity The element quantity.
	 * @param string $price_type The element price type.
	 * @since 1.0
	 */
	public function get_price_for_cart( $price = 0, $cart_item = [], $symbol = false, $quantity = 0, $price_type = '' ) {

		global $woocommerce;
		$product          = $cart_item['data'];
		$cart             = $woocommerce->cart;
		$taxable          = $product->is_taxable();
		$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );
		$tax_string       = '';

		if ( false === $price ) {
			$price = $product->get_price();
		}
		if ( is_array( $price_type ) ) {
			$price_type = array_values( $price_type );
			$price_type = $price_type[0];
		}
		$price = apply_filters( 'wc_epo_price_on_cart', $price, $cart_item );

		// Taxable.
		if ( $taxable ) {

			if ( 'excl' === $tax_display_cart ) {

				if ( $cart->tax_total > 0 && wc_prices_include_tax() ) {
					$tax_string = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';
				}
				if ( (float) 0 !== floatval( $price ) ) {
					$price = themecomplete_get_price_excluding_tax(
						$product,
						[
							'qty'   => 10000,
							'price' => $price,
						]
					) / 10000;
				}
			} else {

				if ( $cart->tax_total > 0 && ! wc_prices_include_tax() ) {
					$tax_string = ' <small>' . apply_filters( 'inc_tax_or_vat', WC()->countries->inc_tax_or_vat() ) . '</small>';
				}
				if ( (float) 0 !== floatval( $price ) ) {
					$price = themecomplete_get_price_including_tax(
						$product,
						[
							'qty'   => 10000,
							'price' => $price,
						]
					) / 10000;
				}
			}
		}

		if ( false === $symbol ) {
			if ( '' === THEMECOMPLETE_EPO()->tm_epo_global_price_sign && 'advanced' !== THEMECOMPLETE_EPO()->tm_epo_cart_field_display ) {
				$symbol = apply_filters( 'wc_epo_get_price_for_cart_plus_sign', "<span class='tc-plus-sign'>+</span>" );
			}
			if ( floatval( $price ) < 0 ) {
				$symbol = apply_filters( 'wc_epo_get_price_for_cart_minus_sign', "<span class='tc-minus-sign'>-</span>" );
			}
		}

		if ( ! empty( $quantity ) ) {
			$price = floatval( $price ) * floatval( $quantity );
		}

		if ( (float) 0 === floatval( $price ) ) {
			$symbol = apply_filters( 'wc_epo_get_price_for_cart_price_empty', '', $price, $tax_string, $cart_item, $symbol, $quantity, $price_type );
		} else {
			$price  = apply_filters( 'wc_epo_get_price_for_cart_price', ' <span class="tc-price-amount-in-cart">' . ( themecomplete_price( abs( $price ) ) ) . '</span>', $price = 0, $cart_item, $symbol, $quantity, $price_type );
			$symbol = apply_filters( 'wc_epo_get_price_for_cart_symbol', " $symbol" . $price . $tax_string, $symbol, $price, $tax_string, $cart_item, $symbol, $quantity, $price_type );

			if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_strip_html_from_emails ) {
				$symbol = wp_strip_all_tags( $symbol );
			}
		}

		return apply_filters( 'wc_epo_get_price_for_cart', $symbol, $price, $cart_item, $symbol, $quantity, $price_type );

	}

	/**
	 * Helper function to remove the form prefix
	 *
	 * @param array  $tmpost_data The posted data.
	 * @param string $form_prefix The form prefix.
	 * @return array
	 */
	public function woocommerce_cart_id_map( $tmpost_data, $form_prefix ) {
		if ( '' !== $form_prefix ) {
			$tmpost_data = str_replace( $form_prefix, '', $tmpost_data );
		}
		return $tmpost_data;
	}

	/**
	 * Alter the cart id upon adding the product to the cart
	 *
	 * @param string  $cart_id A unique ID for the cart item being added.
	 * @param integer $product_id The id of the product the key is being generated for.
	 * @param integer $variation_id The variation id of the product the key is being generated for.
	 * @param array   $variation The variation data for the cart item.
	 * @param array   $cart_item_data The Cart item meta data.
	 * @since 1.0
	 */
	public function woocommerce_cart_id( $cart_id, $product_id, $variation_id = 0, $variation = [], $cart_item_data = [] ) {

		if ( is_array( $cart_item_data ) && ! empty( $cart_item_data ) && isset( $cart_item_data['tmpost_data'] ) ) {
			if ( is_array( $cart_item_data['tmpost_data'] ) && ! empty( $cart_item_data['tmpost_data'] ) ) {
				foreach ( $cart_item_data['tmpost_data'] as $key => $value ) {
					if ( strpos( $key, 'tmcp_' ) !== 0 ) {
						unset( $cart_item_data['tmpost_data'][ $key ] );
					}
				}
			}

			if ( isset( $cart_item_data['tmdata'] ) && isset( $cart_item_data['tmdata']['tmcp_post_fields'] ) ) {
				foreach ( $cart_item_data['tmdata']['tmcp_post_fields'] as $key => $value ) {
					if ( ! isset( $cart_item_data['tmpost_data'][ $key ] ) ) {
						$cart_item_data['tmpost_data'][ $key ] = $value;
					}
				}
			}

			$form_prefix                   = $cart_item_data['tmdata']['form_prefix'];
			$cart_item_data['tmpost_data'] = THEMECOMPLETE_EPO_HELPER()->array_map_key( [ $this, 'woocommerce_cart_id_map' ], $cart_item_data['tmpost_data'], [ $form_prefix ] );

			unset( $cart_item_data['tmdata'] );
			unset( $cart_item_data['tmcartepo'] );
			unset( $cart_item_data['tmcartfee'] );

			$id_parts = [ $product_id ];

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
		}

		// Required in order to not have errors with stock when editing products.
		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['quantity'] = 0;
		}

		return $cart_id;

	}

	/**
	 * Repopulate the cart meta
	 *
	 * @param array      $cart_item_meta Cart item meta.
	 * @param integer    $product_id Product id.
	 * @param array|null $post_data Post data.
	 * @param boolean    $novarprices If we should not calculate variable prices.
	 * @param string     $form_prefix Form prefix.
	 * @param string     $epo_type What type of date to repopulate.
	 *
	 * @return array
	 */
	public function repopulatecart( $cart_item_meta, $product_id, $post_data = null, $novarprices = false, $form_prefix = '', $epo_type = 'tmcartepo' ) {
		if ( is_array( $post_data ) && isset( $post_data['tc_form_prefix'] ) ) {
			$form_prefix = $post_data['tc_form_prefix'];
		}
		if ( ! $this->populate_arrays( $product_id, $post_data, $cart_item_meta ) ) {
			return $cart_item_meta;
		}
		global $woocommerce_wpml;

		if ( isset( $cart_item_meta['composite_item'] ) ) {
			global $woocommerce;
			$cart_contents = $woocommerce->cart->get_cart();

			$bundled_item_id = $cart_item_meta['composite_item'];
			if ( isset( $post_data['bto_variation_id'][ $bundled_item_id ] ) ) {
				$variation_id = $post_data['bto_variation_id'][ $bundled_item_id ];
			} elseif ( isset( $post_data['wccp_variation_id'][ $bundled_item_id ] ) ) {
				$variation_id = $post_data['wccp_variation_id'][ $bundled_item_id ];
			}
			$cpf_product_price = 0;
			if ( isset( $post_data['cpf_bto_price'] ) ) {
				if ( is_array( $post_data['cpf_bto_price'] ) ) {
					if ( isset( $post_data['cpf_bto_price'][ $bundled_item_id ] ) ) {
						$cpf_product_price = $post_data['cpf_bto_price'][ $bundled_item_id ];
					}
				} else {
					$cpf_product_price = $post_data['cpf_bto_price'];
				}
			}
		} else {
			if ( isset( $cart_item_meta['associated_uniqid'] ) ) {
				$associated_formprefix = $cart_item_meta['associated_formprefix'];

				if ( isset( $post_data[ 'cpf_product_price' . $associated_formprefix ] ) ) {
					$cpf_product_price = $post_data[ 'cpf_product_price' . $associated_formprefix ];
				}
			} else {
				$cpf_product_price = $post_data['cpf_product_price'];
			}
		}

		if ( isset( $cart_item_meta['associated_priced_individually'] ) && ! $cart_item_meta['associated_priced_individually'] ) {
			$cpf_product_price = 0;
		}

		$global_prices = $this->global_prices;

		$element_object = [];
		$pl             = [ 'before', 'after' ];
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

		if ( ! isset( $cart_item_meta['associated_products_price'] ) || ! empty( $cart_item_meta['tc_recalculate'] ) ) {

			$associated_products_price = 0;

			if ( isset( $cart_item_meta['associated_products'] ) ) {
				foreach ( $cart_item_meta['associated_products'] as $associated_cart_key ) {
					if ( isset( WC()->cart->cart_contents[ $associated_cart_key ] ) ) {
						$associated_products_price = $associated_products_price + floatval( WC()->cart->cart_contents[ $associated_cart_key ]['data']->get_price() );
					}
				}
			}

			$cart_item_meta['associated_products_price'] = $associated_products_price;
		}

		if ( $cart_item_meta['associated_products_price'] > 0 ) {
			$cart_item_meta['tc_recalculate'] = true;
		}

		if ( isset( $cart_item_meta[ $epo_type ] ) ) {
			$current_currency = themecomplete_get_woocommerce_currency();

			$tc_added_in_currency = isset( $cart_item_meta['tmdata']['tc_added_in_currency'] ) ? $cart_item_meta['tmdata']['tc_added_in_currency'] : false;
			$tc_default_currency  = isset( $cart_item_meta['tmdata']['tc_default_currency'] ) ? $cart_item_meta['tmdata']['tc_default_currency'] : false;
			$percentcurrenttotal  = [];

			foreach ( $cart_item_meta[ $epo_type ] as $key => $value ) {
				if ( ! isset( $element_object[ $value['section'] ] ) ) {
					continue;
				}
				if ( 'builder' === $value['mode'] ) {

					$new_key                = false;
					$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $product_id, true );
					if ( ! empty( $value['multiple'] ) && ! empty( $value['key'] ) ) {
						$pos = strrpos( $value['key'], '_' );
						if ( false !== $pos && isset( $wpml_translation_by_id[ 'options_' . $value['section'] ] ) && is_array( $wpml_translation_by_id[ 'options_' . $value['section'] ] ) ) {
							$av = array_values( $wpml_translation_by_id[ 'options_' . $value['section'] ] );
							$ak = array_keys( $wpml_translation_by_id[ 'options_' . $value['section'] ] );
							if ( isset( $av[ substr( $value['key'], $pos + 1 ) ] ) ) {
								$new_key = $ak[ substr( $value['key'], $pos + 1 ) ];
							}
						}
					}

					$thiskey = ( false !== $new_key ) ? $new_key : $cart_item_meta['tmdata'][ $epo_type . '_data' ][ $key ]['key'];

					$price_per_currencies = isset( $element_object[ $value['section'] ]['price_per_currencies'] ) ? $element_object[ $value['section'] ]['price_per_currencies'] : [];
					$price_per_currency   = [];
					$_price_type          = THEMECOMPLETE_EPO()->get_saved_element_price_type( $value );

					if ( ! $novarprices && ( 'percentcurrenttotal' === $_price_type || 'fixedcurrenttotal' === $_price_type ) ) {
						$percentcurrenttotal[] = $key;
					} elseif ( $novarprices ) {
						$_price = THEMECOMPLETE_EPO()->calculate_price(
							$post_data,
							$element_object[ $value['section'] ],
							$thiskey,
							$cart_item_meta['tmdata'][ $epo_type . '_data' ][ $key ]['attribute'],
							$value['quantity'],
							isset( $value['key_id'] ) ? $value['key_id'] : 0,
							isset( $value['keyvalue_id'] ) ? $value['keyvalue_id'] : 0,
							$cart_item_meta['tmdata']['per_product_pricing'],
							apply_filters( 'wc_epo_convert_to_currency', $cpf_product_price, $tc_added_in_currency, $current_currency, true ),
							$cart_item_meta['tmdata']['variation_id'],
							0,
							themecomplete_get_woocommerce_currency(),
							themecomplete_get_woocommerce_currency(),
							$price_per_currencies,
							false,
							$cart_item_meta['tmdata']
						);

						$cart_item_meta[ $epo_type ][ $key ]['price'] = $_price;
						if ( 'percent' === $_price_type ) {
							$cart_item_meta['tm_epo_options_static_prices'] = floatval( $cart_item_meta['tm_epo_options_static_prices'] ) + floatval( $_price );
						}
					}
				}
			}
			$post_data['tm_epo_options_static_prices'] = floatval( isset( $post_data['tm_epo_options_static_prices'] ) ? $post_data['tm_epo_options_static_prices'] : 0 ) + floatval( $cart_item_meta['associated_products_price'] );

			foreach ( $percentcurrenttotal as $key ) {
				$value = $cart_item_meta[ $epo_type ][ $key ];

				if ( ! isset( $element_object[ $value['section'] ] ) ) {
					continue;
				}

				if ( 'builder' === $value['mode'] ) {

					$new_key                = false;
					$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $product_id, true );
					if ( ! empty( $value['multiple'] ) && ! empty( $value['key'] ) ) {
						$pos = strrpos( $value['key'], '_' );
						if ( false !== $pos && isset( $wpml_translation_by_id[ 'options_' . $value['section'] ] ) && is_array( $wpml_translation_by_id[ 'options_' . $value['section'] ] ) ) {
							$av = array_values( $wpml_translation_by_id[ 'options_' . $value['section'] ] );
							$ak = array_keys( $wpml_translation_by_id[ 'options_' . $value['section'] ] );
							if ( isset( $av[ substr( $value['key'], $pos + 1 ) ] ) ) {
								$new_key = $ak[ substr( $value['key'], $pos + 1 ) ];
							}
						}
					}

					$thiskey = ( false !== $new_key ) ? $new_key : $cart_item_meta['tmdata'][ $epo_type . '_data' ][ $key ]['key'];

					$price_per_currencies = isset( $element_object[ $value['section'] ]['price_per_currencies'] ) ? $element_object[ $value['section'] ]['price_per_currencies'] : [];
					$_price_type          = THEMECOMPLETE_EPO()->get_saved_element_price_type( $value );

					$_price = THEMECOMPLETE_EPO()->calculate_price(
						$post_data,
						$element_object[ $value['section'] ],
						$thiskey,
						$cart_item_meta['tmdata'][ $epo_type . '_data' ][ $key ]['attribute'],
						$value['quantity'],
						isset( $value['key_id'] ) ? $value['key_id'] : 0,
						isset( $value['keyvalue_id'] ) ? $value['keyvalue_id'] : 0,
						$cart_item_meta['tmdata']['per_product_pricing'],
						apply_filters( 'wc_epo_convert_to_currency', $cpf_product_price, $tc_added_in_currency, $current_currency, true ),
						$cart_item_meta['tmdata']['variation_id'],
						0,
						themecomplete_get_woocommerce_currency(),
						themecomplete_get_woocommerce_currency(),
						$price_per_currencies,
						false,
						$cart_item_meta['tmdata']
					);

					$cart_item_meta[ $epo_type ][ $key ]['price'] = $_price;

				}
			}
		}

		return $cart_item_meta;
	}

	/**
	 * Helper function to remove string part
	 *
	 * @param string $input The string to convert.
	 * @since 4.9.8
	 */
	private function remove_underscore_part( $input = '' ) {
		return substr( $input, 0, strrpos( $input, '_' ) );
	}


	/**
	 * Alters the cart item key when editing a product
	 *
	 * @param string       $cart_item_key The cart item key.
	 * @param integer      $product_id Contains the id of the product to add to the cart.
	 * @param int|string   $quantity Contains the quantity of the item to add.
	 * @param integer      $variation_id ID of the variation being added to the cart.
	 * @param array|string $variation Attribute values.
	 * @param array|string $cart_item_data Extra cart item data to pass into the item.
	 */
	public function woocommerce_add_to_cart( $cart_item_key = '', $product_id = 0, $quantity = '', $variation_id = 0, $variation = '', $cart_item_data = '' ) {
		$this->last_added_cart_key                  = $cart_item_key;
		WC()->cart->cart_contents[ $cart_item_key ] = $this->add_cart_item( WC()->cart->cart_contents[ $cart_item_key ], $cart_item_key );

	}

	/**
	 * Modifies the cart item
	 *
	 * @param array   $cart_item The cart item.
	 * @param string  $cart_item_key The cart item key.
	 * @param boolean $from_session If we are in the woocommerce_get_cart_item_from_session hook.
	 * @since 1.0
	 * @throws Exception If we detect negactive or zero priced products.
	 */
	public function add_cart_item( $cart_item = [], $cart_item_key = '', $from_session = false ) {

		if ( apply_filters( 'wc_epo_no_add_cart_item', false, $cart_item, $cart_item_key, $from_session ) ) {
			return $cart_item;
		}

		if ( ! isset( $cart_item['data'] ) || ! $cart_item['data'] ) {
			return $cart_item;
		}

		if ( isset( $cart_item['associated_uniqid'] ) ) {
			THEMECOMPLETE_EPO()->associated_element_uniqid = $cart_item['associated_uniqid'];
		}

		if ( isset( $cart_item['associated_key'] ) ) {
			THEMECOMPLETE_EPO()->associated_product_counter = $cart_item['associated_key'];
		}

		if ( isset( $cart_item['associated_formprefix'] ) ) {
			THEMECOMPLETE_EPO()->associated_product_formprefix = $cart_item['associated_formprefix'];
		}

		$cart_item = apply_filters( 'wc_epo_adjust_cart_item_before', $cart_item );

		/*
		* The following logic ensures that the correct price is being calculated
		* when currency or product price is being changed from various
		* 3rd part plugins.
		*/
		$cart_item['tm_epo_product_original_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $cart_item['data']->get_price( 'edit' ), $cart_item );

		$cart_item['tm_epo_options_prices']             = 0;
		$cart_item['tm_epo_product_price_with_options'] = $cart_item['tm_epo_product_original_price'];

		$this->cart_item_meta = $cart_item;

		$product_epos         = [];
		$product_epos_choices = [];

		if ( ! empty( $cart_item['tmcartepo'] ) ) {

			$tmcp_prices           = 0;
			$tmcp_static_prices    = 0;
			$tmcp_variable_prices  = 0; // percentcurrenttotal.
			$tmcp_variable_prices2 = 0; // percent.
			$tmcp_variable_prices3 = 0; // fixedcurrenttotal.

			$cart_item['tm_epo_options_static_prices'] = 0;

			$to_currency = themecomplete_get_woocommerce_currency();

			$product_id = $cart_item['product_id'];
			if ( ! isset( $cart_item['tmdata'] ) ) {
				$cart_item['tmdata']['form_prefix'] = '';
			}
			$product_epos         = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $cart_item['tmdata']['form_prefix'], true, true );
			$product_epos_choices = $product_epos['product_epos_choices'];
			if ( is_array( $product_epos_choices ) ) {
				foreach ( $product_epos_choices as $key => $product_epos_choice ) {
					if ( is_array( $product_epos_choice ) ) {
						$product_epos_choices[ $key ] = array_map( [ $this, 'remove_underscore_part' ], $product_epos_choice );
					}
				}
			}

			// disable check for WPML.
			$tcremoved = apply_filters( 'wc_epo_remove_cart_item', THEMECOMPLETE_EPO_WPML()->is_active() ? false : true, $cart_item, $cart_item_key, $from_session );

			if ( is_array( $cart_item['tmcartepo'] ) ) {
				if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== 'composite' ) {
					$post_data = wp_unslash( $cart_item['tmpost_data'] );
					if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
						$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
					}
					// todo:check for a better alternative.
					if ( ! isset( $post_data['cpf_product_price'] ) ) {
						$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
					}
					$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
					$post_data['quantity']          = $cart_item['quantity'];

					$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data, true );
					if ( false === $cart_item ) {
						if ( isset( $cart_item['associated_uniqid'] ) ) {
							THEMECOMPLETE_EPO()->associated_element_uniqid = false;
						}
						if ( isset( $cart_item['associated_key'] ) ) {
							THEMECOMPLETE_EPO()->associated_product_counter = false;
						}
						if ( isset( $cart_item['associated_formprefix'] ) ) {
							THEMECOMPLETE_EPO()->associated_product_formprefix = false;
						}
						return [];
					}
					$cart_item = apply_filters( 'tm_cart_contents', $cart_item, [] );
				} elseif ( themecomplete_get_product_type( $cart_item['data'] ) !== 'composite' ) {
					$post_data = [];
					if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
						$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
					}
					// todo:check for a better alternative.
					if ( ! isset( $post_data['cpf_product_price'] ) ) {
						$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
					}
					$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
					$post_data['quantity']          = $cart_item['quantity'];

					$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data, true );
					if ( false === $cart_item ) {
						if ( isset( $cart_item['associated_uniqid'] ) ) {
							THEMECOMPLETE_EPO()->associated_element_uniqid = false;
						}
						if ( isset( $cart_item['associated_key'] ) ) {
							THEMECOMPLETE_EPO()->associated_product_counter = false;
						}
						if ( isset( $cart_item['associated_formprefix'] ) ) {
							THEMECOMPLETE_EPO()->associated_product_formprefix = false;
						}
						return [];
					}
					$cart_item = apply_filters( 'tm_cart_contents', $cart_item, [] );
				}

				$keys_to_delete = [];
				foreach ( $cart_item['tmcartepo'] as $cart_item_key => $tmcp ) {

					if ( ! THEMECOMPLETE_EPO_WPML()->is_active() && isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

						$key = $this->remove_underscore_part( $tmcp['key'] );
						if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ], true ) ) {
							$keys_to_delete[] = $cart_item_key;
							continue;
						}
					}

					if ( ! isset( $product_epos['epos_uniqids'] ) || ! is_array( $product_epos['epos_uniqids'] ) || ! in_array( $tmcp['section'], $product_epos['epos_uniqids'], true ) ) {
						$keys_to_delete[] = $cart_item_key;
						continue;
					}

					$tcremoved = false;

					if ( apply_filters( 'wc_epo_add_cart_item_loop', false, $tmcp ) ) {
						continue;
					}

					if ( isset( $tmcp['price_formula'] ) ) {
						$cart_item['tc_recalculate'] = true;
					}

					$_price_type   = THEMECOMPLETE_EPO()->get_saved_element_price_type( $tmcp );
					$tmcp['price'] = (float) wc_format_decimal( $tmcp['price'], false, true );
					if ( 'fixedcurrenttotal' === $_price_type ) {
						$tmcp_variable_prices3 += $tmcp['price'];
					} elseif ( 'percentcurrenttotal' === $_price_type ) {
						$tmcp_variable_prices += $tmcp['price'];
					} elseif ( 'percent' === $_price_type ) {
						$tmcp_variable_prices2 += $tmcp['price'];
					} else {
						$tmcp_static_prices += apply_filters( 'woocommerce_tm_epo_price_add_on_cart', $tmcp['price'], $_price_type );
					}
				}

				foreach ( $keys_to_delete as $key_to_delete ) {
					$item_name  = $cart_item['tmcartepo'][ $key_to_delete ]['name'];
					$item_value = $cart_item['tmcartepo'][ $key_to_delete ]['value'];
					if ( is_array( $item_name ) ) {
						$item_name = reset( array_values( $item_name ) );
					}
					if ( is_array( $item_value ) ) {
						$item_value = reset( array_values( $item_value ) );
					}
					$message = $item_name . THEMECOMPLETE_EPO()->tm_epo_separator_cart_text . $item_value;
					wc_add_notice(
						sprintf(
							/* translators: %s: Addon name and value that was removed. */
							esc_html__( 'The item %s was removed from the cart because it had changed since it was added.', 'woocommerce-tm-extra-product-options' ),
							$message,
							'error'
						)
					);
					unset( $cart_item['tmcartepo'][ $key_to_delete ] );
				}
			}

			$tmcp_static_prices = apply_filters( 'associated_tmcp_static_prices', $tmcp_static_prices, $cart_item );

			if ( $tcremoved ) {
				$cart_item['tcremoved'] = true;
				if ( isset( $cart_item['associated_uniqid'] ) ) {
					THEMECOMPLETE_EPO()->associated_element_uniqid = false;
				}
				if ( isset( $cart_item['associated_key'] ) ) {
					THEMECOMPLETE_EPO()->associated_product_counter = false;
				}
				if ( isset( $cart_item['associated_formprefix'] ) ) {
					THEMECOMPLETE_EPO()->associated_product_formprefix = false;
				}
				return apply_filters( 'wc_epo_adjust_cart_item', $cart_item );
			}

			$cart_item['tm_epo_options_static_prices']       = (float) $cart_item['tm_epo_options_static_prices'] + (float) $tmcp_static_prices;
			$cart_item['tm_epo_options_static_prices_first'] = $cart_item['tm_epo_options_static_prices'];

			if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== 'composite' ) {
				$post_data = wp_unslash( $cart_item['tmpost_data'] );
				if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
					$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
				}
				// todo:check for a better alternative.
				if ( ! isset( $post_data['cpf_product_price'] ) ) {
					$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
				}
				$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
				$post_data['quantity']          = $cart_item['quantity'];

				$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data );
				if ( false === $cart_item ) {
					if ( isset( $cart_item['associated_uniqid'] ) ) {
						THEMECOMPLETE_EPO()->associated_element_uniqid = false;
					}
					if ( isset( $cart_item['associated_key'] ) ) {
						THEMECOMPLETE_EPO()->associated_product_counter = false;
					}
					if ( isset( $cart_item['associated_formprefix'] ) ) {
						THEMECOMPLETE_EPO()->associated_product_formprefix = false;
					}
					return [];
				}
				$cart_item = apply_filters( 'tm_cart_contents', $cart_item, [] );
			}

			if ( is_array( $cart_item['tmcartepo'] ) ) {
				$tmcp_variable_prices  = 0;
				$tmcp_variable_prices2 = 0;
				$tmcp_variable_prices3 = 0;
				foreach ( $cart_item['tmcartepo'] as $tmcp ) {
					if ( ! THEMECOMPLETE_EPO_WPML()->is_active() && isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

						$key = $this->remove_underscore_part( $tmcp['key'] );
						if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ], true ) ) {
							continue;
						}
					}
					if ( ! in_array( $tmcp['section'], $product_epos['epos_uniqids'], true ) || apply_filters( 'wc_epo_add_cart_item_loop', false, $tmcp ) ) {
						continue;
					}
					$_price_type = THEMECOMPLETE_EPO()->get_saved_element_price_type( $tmcp );

					$tmcp['price'] = (float) wc_format_decimal( $tmcp['price'], false, true );

					if ( 'fixedcurrenttotal' === $_price_type ) {
						$tmcp_variable_prices3 += $tmcp['price'];
					}
					if ( 'percentcurrenttotal' === $_price_type ) {
						$tmcp_variable_prices += $tmcp['price'];
					}
					if ( 'percent' === $_price_type ) {
						$tmcp_variable_prices2 += $tmcp['price'];
					}
				}
			}

			$tmcp_prices = apply_filters( 'wc_epo_cart_options_prices_before', $tmcp_static_prices + $tmcp_variable_prices + $tmcp_variable_prices2 + $tmcp_variable_prices3, $cart_item );

			$cart_item['tm_epo_options_prices'] = $tmcp_prices;

			$price1 = (float) wc_format_decimal( apply_filters( 'wc_epo_option_price_correction', $tmcp_prices, $cart_item ) );

			$price2 = (float) wc_format_decimal(
				apply_filters( 'wc_epo_product_price_correction', wc_format_decimal( $cart_item['tm_epo_product_original_price'] ), $cart_item )
			) + (float) $price1;

			$price1 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price1', $price1, $cart_item ) );

			$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price2', $price2, $cart_item ) );

			$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price3', $price2, $price1, $cart_item ) );

			do_action( 'wc_epo_currency_actions', $price1, $price2, $cart_item );

			if ( apply_filters( 'wc_epo_adjust_price', true, $cart_item ) ) {
				if ( ! empty( $cart_item['epo_price_override'] ) && $tmcp_prices > 0 ) {
					$cart_item['data']->set_price( $price1 );
					$cart_item['tm_epo_set_product_price_with_options'] = $price1;
					$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price1 );
				} else {
					if ( ! empty( $price1 ) ) {
						$cart_item['data']->set_price( $price2 );
						$cart_item['tm_epo_set_product_price_with_options'] = $price2;
					}
					$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price2 );
				}
			}
			$cart_item['tm_epo_product_price_with_options'] = $cart_item['data']->get_price();

		} else {
			if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== 'composite' ) {
				$post_data = wp_unslash( $cart_item['tmpost_data'] );
				if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
					$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
				}
				// todo:check for a better alternative.
				if ( ! isset( $post_data['cpf_product_price'] ) ) {
					$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
				}
				$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
				$post_data['quantity']          = $cart_item['quantity'];

				$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data );
			}
		}

		$associated_products_price = 0;
		if ( isset( $cart_item['associated_products_price'] ) ) {
			$associated_products_price = floatval( $cart_item['associated_products_price'] );
		}

		if ( floatval( apply_filters( 'tm_epo_cart_options_prices', floatval( $cart_item['tm_epo_product_price_with_options'] ) + $associated_products_price, $cart_item ) ) < 0 ) {
			if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_no_negative_priced_products ) {
				$message = ! empty( THEMECOMPLETE_EPO()->tm_epo_no_negative_priced_products_text ) ? THEMECOMPLETE_EPO()->tm_epo_no_negative_priced_products_text : esc_html__( 'You cannot add negative priced products to the cart.', 'woocommerce-tm-extra-product-options' );
				if ( $from_session ) {
					$cart_item['delete_negative']         = true;
					$cart_item['delete_negative_message'] = $message;
				} else {
					throw new Exception( $message );
				}
			}
		}

		if ( (float) 0 === floatval( apply_filters( 'tm_epo_no_zero_priced_products', floatval( $cart_item['tm_epo_product_price_with_options'] ) + $associated_products_price, $cart_item ) ) ) {
			if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_no_zero_priced_products ) {
				$message = ! empty( THEMECOMPLETE_EPO()->tm_epo_no_zero_priced_products_text ) ? THEMECOMPLETE_EPO()->tm_epo_no_zero_priced_products_text : esc_html__( 'You cannot add zero priced products to the cart.', 'woocommerce-tm-extra-product-options' );
				if ( $from_session ) {
					$cart_item['delete_zero']         = true;
					$cart_item['delete_zero_message'] = $message;
				} else {
					throw new Exception( $message );
				}
			}
		}

		// variation slug-to-name-for order again.
		if ( isset( $cart_item['variation'] ) && is_array( $cart_item['variation'] ) ) {
			$_variation_name_fix = [];
			$_temp               = [];
			foreach ( $cart_item['variation'] as $meta_name => $meta_value ) {
				if ( strpos( $meta_name, 'attribute_' ) !== 0 ) {
					$_variation_name_fix[ 'attribute_' . $meta_name ] = $meta_value;
					$_temp[ $meta_name ]                              = $meta_value;
				}
			}
			$cart_item['variation'] = array_diff_key( $cart_item['variation'], $_temp );
			$cart_item['variation'] = array_merge( $cart_item['variation'], $_variation_name_fix );
		}

		if ( isset( $cart_item['associated_uniqid'] ) ) {
			THEMECOMPLETE_EPO()->associated_element_uniqid = false;
		}
		if ( isset( $cart_item['associated_key'] ) ) {
			THEMECOMPLETE_EPO()->associated_product_counter = false;
		}
		if ( isset( $cart_item['associated_formprefix'] ) ) {
			THEMECOMPLETE_EPO()->associated_product_formprefix = false;
		}

		return apply_filters( 'wc_epo_adjust_cart_item', $cart_item );

	}

	/**
	 * Modifies the cart item
	 *
	 * @param array $cart_object The cart object.
	 * @since 1.0
	 */
	public function woocommerce_before_calculate_totals( $cart_object ) {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		if ( method_exists( $cart_object, 'get_cart' ) ) {
			$cart_contents = $cart_object->get_cart();
		} else {
			$cart_contents = $cart_object->cart_contents;
		}

		foreach ( $cart_contents as $cart_key => $cart_item ) {

			if ( apply_filters( 'wc_epo_no_add_cart_item', false ) ) {
				continue;
			}

			$product_epos         = [];
			$product_epos_choices = [];

			if ( ! empty( $cart_item['tmcartepo'] ) && ! empty( $cart_item['tc_recalculate'] ) ) {

				unset( $cart_item['tc_recalculate'] );

				$tmcp_prices           = 0;
				$tmcp_static_prices    = 0;
				$tmcp_variable_prices  = 0; // percentcurrenttotal.
				$tmcp_variable_prices2 = 0; // percent.
				$tmcp_variable_prices3 = 0; // fixedcurrenttotal.

				$to_currency = themecomplete_get_woocommerce_currency();

				$product_id           = $cart_item['product_id'];
				$product_epos         = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $cart_item['tmdata']['form_prefix'], true, true );
				$product_epos_choices = $product_epos['product_epos_choices'];
				if ( is_array( $product_epos_choices ) ) {
					foreach ( $product_epos_choices as $key => $product_epos_choice ) {
						$product_epos_choices[ $key ] = array_map( [ $this, 'remove_underscore_part' ], $product_epos_choice );
					}
				}

				$cart_item['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices_first'];

				if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== 'composite' ) {
					$post_data = wp_unslash( $cart_item['tmpost_data'] );
					if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
						$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
					}
					// todo:check for a better alternative.
					if ( ! isset( $post_data['cpf_product_price'] ) ) {
						$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
					}
					$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
					$post_data['quantity']          = $cart_item['quantity'];

					$cart_item = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data );
					if ( false === $cart_item ) {
						continue;
					}
					$cart_item = apply_filters( 'tm_cart_contents', $cart_item, [] );
				}

				if ( is_array( $cart_item['tmcartepo'] ) ) {
					$tmcp_variable_prices  = 0;
					$tmcp_variable_prices2 = 0;
					$tmcp_variable_prices3 = 0;
					foreach ( $cart_item['tmcartepo'] as $tmcp ) {
						if ( ! THEMECOMPLETE_EPO_WPML()->is_active() && isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {

							$key = $this->remove_underscore_part( $tmcp['key'] );
							if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ], true ) ) {
								continue;
							}
						}
						if ( ! in_array( $tmcp['section'], $product_epos['epos_uniqids'], true ) || apply_filters( 'wc_epo_add_cart_item_loop', false, $tmcp ) ) {
							continue;
						}
						$_price_type = THEMECOMPLETE_EPO()->get_saved_element_price_type( $tmcp );

						$tmcp['price'] = (float) wc_format_decimal( $tmcp['price'], false, true );
						if ( 'fixedcurrenttotal' === $_price_type ) {
							$tmcp_variable_prices3 += $tmcp['price'];
						} elseif ( 'percentcurrenttotal' === $_price_type ) {
							$tmcp_variable_prices += $tmcp['price'];
						} elseif ( 'percent' === $_price_type ) {
							$tmcp_variable_prices2 += $tmcp['price'];
						} else {
							$tmcp_static_prices += apply_filters( 'woocommerce_tm_epo_price_add_on_cart', $tmcp['price'], $_price_type );
						}
					}
				}

				$tmcp_prices = apply_filters( 'wc_epo_cart_options_prices_before', $tmcp_static_prices + $tmcp_variable_prices + $tmcp_variable_prices2 + $tmcp_variable_prices3, $cart_item );

				$cart_item['tm_epo_options_prices'] = $tmcp_prices;

				$price1 = (float) wc_format_decimal( apply_filters( 'wc_epo_option_price_correction', $tmcp_prices, $cart_item ) );

				$price2 = (float) wc_format_decimal(
					apply_filters(
						'wc_epo_product_price_correction',
						wc_format_decimal( $cart_item['tm_epo_product_original_price'] ),
						$cart_item
					)
				) + (float) $price1;

				$price1 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price1', $price1, $cart_item ) );

				$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price2', $price2, $cart_item ) );

				$price2 = wc_format_decimal( apply_filters( 'wc_epo_add_cart_item_calculated_price3', $price2, $price1, $cart_item ) );

				do_action( 'wc_epo_currency_actions', $price1, $price2, $cart_item );

				if ( apply_filters( 'wc_epo_adjust_price', true, $cart_item ) ) {
					if ( ! empty( $cart_item['epo_price_override'] ) && $tmcp_prices > 0 ) {
						if ( apply_filters( 'wc_epo_adjust_price_before_calculate_totals', true, $cart_item ) ) {
							$cart_item['data']->set_price( $price1 );
						}
						$cart_item['tm_epo_product_price_with_options'] = $price1;
						$cart_item                                      = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price1 );
					} else {
						if ( ! empty( $price1 ) ) {
							$cart_item['tm_epo_product_price_with_options'] = $price2;
							if ( apply_filters( 'wc_epo_adjust_price_before_calculate_totals', true, $cart_item ) ) {
								$cart_item['data']->set_price( $price2 );
							}
						}
						$cart_item = apply_filters( 'wc_epo_cart_set_price', $cart_item, $price2 );
					}
				}
			}

			$cart_item = apply_filters( 'wc_epo_adjust_cart_item', $cart_item );

			$cart_contents[ $cart_key ] = $cart_item;

		}

		if ( method_exists( $cart_object, 'set_cart_contents' ) ) {
			$cart_object->set_cart_contents( $cart_contents );
		} else {
			$cart_object->cart_contents = $cart_contents;
		}

	}

	/**
	 * Gets the cart from session.
	 *
	 * @param array  $cart_item The cart item.
	 * @param array  $values Cart item values.
	 * @param string $cart_item_key The cart item key.
	 * @since 1.0
	 */
	public function woocommerce_get_cart_item_from_session( $cart_item = [], $values = [], $cart_item_key = '' ) {

		if ( ! empty( $values['tmcartepo'] ) ) {
			$cart_item['tmcartepo'] = $values['tmcartepo'];
			$cart_item              = $this->add_cart_item( $cart_item, $cart_item_key, true );
			if ( ! empty( $cart_item['delete_zero'] ) ) {
				wc_add_notice( $cart_item['delete_zero_message'], 'error' );
				return [ 'data' => false ];
			}
			if ( ! empty( $cart_item['delete_negative'] ) ) {
				wc_add_notice( $cart_item['delete_negative_message'], 'error' );
				return [ 'data' => false ];
			}
			if ( empty( $cart_item['addons'] ) && ! empty( $cart_item['tm_epo_options_prices'] ) ) {
				$cart_item['addons'] = [
					'epo'   => true,
					'price' => 0,
				];
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

		if ( ! empty( $values['associated_products'] ) ) {
			$cart_item['associated_products'] = $values['associated_products'];
		}

		$cart_item = apply_filters( 'tm_cart_contents', $cart_item, $values );

		return apply_filters( 'wc_epo_get_cart_item_from_session', $cart_item, $values );

	}

	/**
	 * Gets cart item to display in the frontend
	 *
	 * @param array $item_data The item data.
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	public function woocommerce_get_item_data( $item_data = [], $cart_item = [] ) {

		if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart && 'advanced' !== THEMECOMPLETE_EPO()->tm_epo_cart_field_display && ! empty( $cart_item['tmcartepo'] ) ) {

			$item_data = $this->get_item_data_array( $item_data, $cart_item );

		}

		return $item_data;

	}

	/**
	 * Helper function for filtered_get_item_data
	 *
	 * @param array $tmcp Saved element data.
	 * @since 1.0
	 */
	private function filtered_get_item_data_get_array_data( $tmcp = [] ) {
		$sep = isset( $tmcp['prices'] ) ? THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text : '';
		if ( isset( $tmcp['display'] ) ) {
			$tmcp['display'] = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $tmcp['display'], $sep );
		}
		if ( isset( $tmcp['value'] ) ) {
			$tmcp['value'] = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $tmcp['value'], $sep );
		}
		return [
			'label'               => $tmcp['section_label'],
			'type'                => isset( $tmcp['element'] ) && isset( $tmcp['element']['type'] ) ? $tmcp['element']['type'] : '',
			'other_data'          => [
				[
					'name'                    => $tmcp['name'],
					'value'                   => $tmcp['value'],
					'price_type'              => isset( $tmcp['element'] ) ? ( isset( $tmcp['key'] ) ? $tmcp['element']['rules_type'][ $tmcp['key'] ][0] : $tmcp['element']['rules_type'][0] ) : '',
					'unit_price'              => $tmcp['price'],
					'unit_price_per_currency' => ( isset( $tmcp['price_per_currency'] ) ) ? $tmcp['price_per_currency'] : [],
					'display'                 => isset( $tmcp['display'] ) ? $tmcp['display'] : '',
					'images'                  => isset( $tmcp['images'] ) ? $tmcp['images'] : '',
					'imagesc'                 => isset( $tmcp['imagesc'] ) ? $tmcp['imagesc'] : '',
					'color'                   => isset( $tmcp['color'] ) ? $tmcp['color'] : '',
					'quantity'                => isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1,
				],
			],
			'price'               => $tmcp['price'],
			'currencies'          => isset( $tmcp['currencies'] ) ? $tmcp['currencies'] : [],
			'price_per_currency'  => isset( $tmcp['price_per_currency'] ) ? $tmcp['price_per_currency'] : [],
			'quantity'            => isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1,
			'percentcurrenttotal' => isset( $tmcp['percentcurrenttotal'] ) ? $tmcp['percentcurrenttotal'] : 0,
			'fixedcurrenttotal'   => isset( $tmcp['fixedcurrenttotal'] ) ? $tmcp['fixedcurrenttotal'] : 0,
			'items'               => 1,
			'multiple_values'     => isset( $tmcp['multiple_values'] ) ? $tmcp['multiple_values'] : '',
			'hidelabelincart'     => isset( $tmcp['hidelabelincart'] ) ? $tmcp['hidelabelincart'] : '',
			'hidevalueincart'     => isset( $tmcp['hidevalueincart'] ) ? $tmcp['hidevalueincart'] : '',
			'uniqid'              => $tmcp['section'],
		];

	}

	/**
	 * Filters our cart items
	 *
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	private function filtered_get_item_data( $cart_item = [] ) {

		$to_currency    = themecomplete_get_woocommerce_currency();
		$filtered_array = [];
		$product_id     = $cart_item['product_id'];

		if ( isset( $cart_item['tmcartepo'] ) && is_array( $cart_item['tmcartepo'] ) ) {
			$cart_item    = apply_filters( 'tm_cart_contents', $cart_item, [] );
			$product_epos = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $cart_item['tmdata']['form_prefix'], true, true );

			// This check is required to avoid php errors
			// for cart plugins that use a different domain for the checkout.
			if ( is_array( $product_epos ) && isset( $product_epos['product_epos_choices'] ) ) {
				$product_epos_choices = $product_epos['product_epos_choices'];
				if ( is_array( $product_epos_choices ) ) {
					foreach ( $product_epos_choices as $key => $product_epos_choice ) {
						if ( is_array( $product_epos_choice ) ) {
							$product_epos_choices[ $key ] = array_map( [ $this, 'remove_underscore_part' ], $product_epos_choice );
						}
					}
				}

				foreach ( $cart_item['tmcartepo'] as $tmcp ) {

					if ( ! THEMECOMPLETE_EPO_WPML()->is_active() && isset( $tmcp['key'] ) && isset( $tmcp['element'] ) && isset( $tmcp['element']['rules_type'] ) ) {
						$key = $this->remove_underscore_part( $tmcp['key'] );

						if ( isset( $product_epos_choices[ $tmcp['section'] ] ) && ! in_array( $key, $product_epos_choices[ $tmcp['section'] ], true ) ) {
							continue;
						}
					}

					if ( $tmcp && in_array( $tmcp['section'], $product_epos['epos_uniqids'], true ) ) {

						$tmcp['price']        = (float) wc_format_decimal( $tmcp['price'], false, true );
						$pp                   = false;
						$tc_added_in_currency = false;
						if ( isset( $cart_item['tmpost_data'] ) && isset( $cart_item['tmpost_data']['cpf_product_price'] ) && isset( $cart_item['tmdata']['tc_added_in_currency'] ) ) {
							$pp                   = $cart_item['tmpost_data']['cpf_product_price'];
							$tc_added_in_currency = $cart_item['tmdata']['tc_added_in_currency'];
						}

						if ( empty( $cart_item['associated_discount_exclude_addons'] ) && isset( $cart_item['associated_discount'] ) && isset( $cart_item['associated_discount_type'] ) ) {
							$tmcp['price'] = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $tmcp['price'], $cart_item['associated_discount'], $cart_item['associated_discount_type'] );
						}

						$id = $tmcp['section'];
						if ( isset( $tmcp['repeater'] ) ) {
							$id .= '-' . $tmcp['repeater'] . '-' . $tmcp['key_id'] . '-' . $tmcp['keyvalue_id'];
						}

						if ( ! isset( $filtered_array[ $id ] ) ) {
							$filtered_array[ $id ] = $this->filtered_get_item_data_get_array_data( $tmcp );
						} else {
							if ( 'advanced' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display || 'link' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display ) {
								$filtered_array[ $id . '_' . THEMECOMPLETE_EPO_HELPER()->tm_uniqid() ] = $this->filtered_get_item_data_get_array_data( $tmcp );
							} else {
								$filtered_array[ $id ]['items'] += 1;
								$filtered_array[ $id ]['price'] += $tmcp['price'];

								if ( isset( $tmcp['price_per_currency'] ) ) {
									$filtered_array[ $id ]['price_per_currency'] = THEMECOMPLETE_EPO_HELPER()->add_array_values( $filtered_array[ $id ]['price_per_currency'], $tmcp['price_per_currency'] );
								}

								$filtered_array[ $id ]['quantity']    += isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1;
								$filtered_array[ $id ]['other_data'][] = [
									'name'       => $tmcp['name'],
									'value'      => $tmcp['value'],
									'price_type' => isset( $tmcp['element'] ) ? ( isset( $tmcp['key'] ) ? $tmcp['element']['rules_type'][ $tmcp['key'] ][0] : $tmcp['element']['rules_type'][0] ) : '',
									'unit_price' => $tmcp['price'],
									'unit_price_per_currency' => ( isset( $tmcp['price_per_currency'] ) ) ? $tmcp['price_per_currency'] : [],
									'display'    => isset( $tmcp['display'] ) ? $tmcp['display'] : '',
									'images'     => isset( $tmcp['images'] ) ? $tmcp['images'] : '',
									'imagesc'    => isset( $tmcp['imagesc'] ) ? $tmcp['imagesc'] : '',
									'color'      => isset( $tmcp['color'] ) ? $tmcp['color'] : '',
									'quantity'   => isset( $tmcp['quantity'] ) ? $tmcp['quantity'] : 1,
								];

								$filtered_array[ $id ]['uniqid'] = $tmcp['section'];
							}
						}
					}
				}
			}
		}

		return $filtered_array;

	}

	/**
	 * Return formatted cart items
	 *
	 * @param array $item_data The item data.
	 * @param array $cart_item The cart item.
	 */
	public function get_item_data_array( $item_data = [], $cart_item = [] ) {

		$filtered_array = $this->filtered_get_item_data( $cart_item );
		$price          = 0;
		$link_data      = [];
		$quantity       = $cart_item['quantity'];
		if ( is_array( $filtered_array ) ) {
			foreach ( $filtered_array as $section ) {
				$value                   = [];
				$value_only              = [];
				$value_original          = [];
				$value_unique            = [];
				$quantity_string_shown   = false;
				$format_price_shown      = false;
				$do_unique_values        = false;
				$prev_unit_price         = false;
				$prev_unit_quantity      = false;
				$dont_show_mass_quantity = false;
				$format_price            = '';
				if ( isset( $section['other_data'] ) && is_array( $section['other_data'] ) ) {
					foreach ( $section['other_data'] as $key => $data ) {
						if ( empty( $data['quantity'] ) ) {
							continue;
						}
						$display_value            = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
						$display_value_only       = $display_value;
						$display_other_value_only = '';

						if ( 'checkbox' === $section['type'] && 'normal' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display ) {
							if ( 'noprice' === $section['hidevalueincart'] || 'hidden' === $section['hidevalueincart'] ) {
								$format_price = '';
							} else {
								if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart ) {
									$original_price = $data['unit_price'] / $data['quantity'];
									$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], isset( $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] ) ? $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] : '' );
									$after_price    = $new_price / $data['quantity'];
									$format_price   = $this->get_price_for_cart( $after_price, $cart_item, false, 0, $data['price_type'] );

									if ( (string) $original_price !== (string) $after_price ) {
										$original_price = $this->get_price_for_cart( $original_price, $cart_item, false, 0, $data['price_type'] );
										$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
									}
									$format_price_shown = true;
								} else {
									$format_price = '';
								}
							}
							$quantity_string = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
							if ( 'price' === $section['hidevalueincart'] ) {
								$display_value = '';
							}
							$display_value_only = $display_value;
							if ( '' !== $format_price ) {
								$display_value = $display_value . ' <span class="tc-price-in-cart">' . $format_price . '</span>';
							}
							if ( '' !== $quantity_string ) {
								$display_value = $display_value . ' <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
							}
							$quantity_string_shown = true;

						}

						if ( ( ! empty( $data['images'] ) || ( isset( $data['imagesc'] ) && ! empty( $data['imagesc'] ) ) ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_image_replacement ) {
							if ( ! $format_price_shown && 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart ) {
								$original_price = $data['unit_price'] / $data['quantity'];
								$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], isset( $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] ) ? $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] : '' );
								$after_price    = $new_price / $data['quantity'];
								$format_price   = $this->get_price_for_cart( $after_price, $cart_item, false, 0, $data['price_type'] );

								if ( (string) $original_price !== (string) $after_price ) {
									$original_price = $this->get_price_for_cart( $original_price, $cart_item, false, 0, $data['price_type'] );
									$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
								}
								$format_price_shown = true;
							} else {
								$format_price = '';
							}
							$quantity_string          = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
							$image_src                = ( isset( $data['imagesc'] ) && ! empty( $data['imagesc'] ) ) ? $data['imagesc'] : $data['images'];
							$display_other_value_only = '<span class="cpf-img-on-cart"><img alt="' . esc_attr( wp_strip_all_tags( $section['label'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="' . apply_filters( 'tm_image_url', $image_src ) . '" />';
							$display_value            = $display_other_value_only . $display_value;
							$display_value_only       = $display_value . '</span>';
							$display_other_value_only = $display_other_value_only . '</span>';
							$display_value            = $display_value . '<span class="tc-price-in-cart">' . $format_price . '</span></span>';
							if ( ! $quantity_string_shown ) {
								$display_value = $display_value . ' <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
							}
							$quantity_string_shown = true;
						} elseif ( ! empty( $data['color'] ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_image_replacement ) {
							if ( ! $format_price_shown && 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart ) {
								$original_price = $data['unit_price'] / $data['quantity'];
								$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], isset( $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] ) ? $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] : '' );
								$after_price    = $new_price / $data['quantity'];
								$format_price   = $this->get_price_for_cart( $after_price, $cart_item, false, 0, $data['price_type'] );

								if ( (string) $original_price !== (string) $after_price ) {
									$original_price = $this->get_price_for_cart( $original_price, $cart_item, false, 0, $data['price_type'] );
									$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
								}
								$format_price_shown = true;
							} else {
								$format_price = '';
							}
							$quantity_string          = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
							$display_other_value_only = '<span class="cpf-colors-on-cart"><span class="cpf-color-on-cart backgroundcolor' . esc_attr( sanitize_hex_color_no_hash( $data['color'] ) ) . '"></span> ';
							$display_value            = $display_other_value_only . $display_value;
							$display_value_only       = $display_value . '</span>';
							$display_other_value_only = $display_other_value_only . '</span>';
							$display_value            = $display_value . '<span class="tc-price-in-cart">' . $format_price . '</span></span>';
							if ( ! $quantity_string_shown ) {
								$display_value = $display_value . ' <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
							}
							$quantity_string_shown = true;
							THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( '.backgroundcolor' . esc_attr( sanitize_hex_color_no_hash( $data['color'] ) ) . '{background-color:' . esc_attr( sanitize_hex_color( $data['color'] ) ) . ';}' );
						} else {

							if ( false === $prev_unit_quantity ) {
								$prev_unit_quantity = $data['quantity'];
							}
							if ( false === $prev_unit_price ) {
								$prev_unit_price = $data['unit_price'];
							} elseif ( $prev_unit_price !== $data['unit_price'] || $prev_unit_quantity !== $data['quantity'] || $data['quantity'] > 1 ) {

								if ( THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart === 'yes' ) {
									$dont_show_mass_quantity = true;
								}
							}
							$prev_unit_price    = $data['unit_price'];
							$prev_unit_quantity = $data['quantity'];

						}

						if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_show_hide_uploaded_file_url_cart && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_upload_image_replacement && ( 'upload' === $section['type'] || 'multiple_file_upload' === $section['type'] ) ) {
							$check = wp_check_filetype( $data['value'] );
							if ( ! empty( $check['ext'] ) ) {
								$image_exts = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png' ];
								if ( in_array( $check['ext'], $image_exts, true ) ) {
									$display_other_value_only = '<span class="cpf-img-on-cart">';

									$files = explode( '|', $data['value'] );
									foreach ( $files as $file ) {
										$display_other_value_only .= '<img alt="' . esc_attr( wp_strip_all_tags( $section['label'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="' . apply_filters( 'tm_image_url', $file ) . '">';
									}

									$display_other_value_only .= '</span>';
									$display_value             = $display_other_value_only;
									$display_value_only        = $display_value;
								}
							}
						}
						$value[]      = $display_value;
						$value_only[] = $display_value_only;

						// Unique values.
						if ( 'price' === $section['hidevalueincart'] ) {
							$display_value = '';
						} else {
							$display_value = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
						}
						$original_price = $data['unit_price'] / $data['quantity'];
						$new_price      = apply_filters( 'wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], isset( $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] ) ? $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] : [] );
						$after_price    = $new_price / $data['quantity'];
						$format_price   = $this->get_price_for_cart( $after_price, $cart_item, false, 0, $data['price_type'] );

						if ( (string) $original_price !== (string) $after_price ) {
							$original_price = $this->get_price_for_cart( $original_price, $cart_item, false, 0, $data['price_type'] );
							$format_price   = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
						}
						$quantity_string = ( $data['quantity'] > 1 ) ? ' &times; ' . $data['quantity'] : '';
						if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart || 'noprice' === $section['hidevalueincart'] || 'hidden' === $section['hidevalueincart'] ) {
							$format_price = '';
						}
						if ( ! empty( $section['multiple_values'] ) ) {
							$display_value_array = explode( $section['multiple_values'], $display_value );
							$display_value       = '';
							foreach ( $display_value_array as $d => $dv ) {
								$display_value .= '<span class="cpf-data-on-cart">' . $dv . '</span>';
							}
							$display_value .= $display_other_value_only . ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span>';
						} else {
							$display_value = $display_other_value_only . '<span class="cpf-data-on-cart">' . $display_value . ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span></span>';
						}
						$value_unique[] = $display_value;
					}

					$value_original = $value;

					if ( ! empty( $section['multiple_values'] ) ) {
						$do_unique_values = true;
					}

					if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_always_unique_values && 'checkbox' === $section['type'] ) {
						$do_unique_values = true;
					}

					if ( $do_unique_values ) {
						$quantity_string_shown = true;
						$format_price_shown    = true;
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
					$value_original = '';
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
					$value = '';
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
					$value_only = '';
				}

				if ( empty( $section['quantity'] ) ) {
					$section['quantity'] = 1;
				}

				// WooCommerce Dynamic Pricing & Discounts.
				$original_price   = $section['price'] / $section['quantity'];
				$original_price_q = $original_price * $quantity * $section['quantity'];

				$section['price'] = apply_filters( 'wc_epo_discounted_price', $section['price'], $cart_item['data'], isset( $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] ) ? $cart_item[ THEMECOMPLETE_EPO()->cart_edit_key_var ] : '' );
				$after_price      = $section['price'] / $section['quantity'];

				$price                 = $price + (float) $section['price'];
				$section['price_type'] = '';
				if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart ) {
					$format_price        = $this->get_price_for_cart( $after_price, $cart_item, false, 0, $section['price_type'] );
					$format_price_total  = $this->get_price_for_cart( $section['price'], $cart_item, false, $quantity, $section['price_type'] );
					$format_price_total2 = $this->get_price_for_cart( $section['price'], $cart_item, false, 0, $section['price_type'] );

					if ( (string) $original_price !== (string) $after_price ) {
						$original_price       = $this->get_price_for_cart( $original_price, $cart_item, false, 0, $section['price_type'] );
						$original_price_total = $this->get_price_for_cart( $original_price_q, $cart_item, false, 0, $section['price_type'] );
						$format_price         = '<span class="tc-epo-cart-price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
					}
				} else {
					$format_price        = '';
					$format_price_total  = '';
					$format_price_total2 = '';
				}
				$single_price    = $this->get_price_for_cart( (float) $section['price'] / $section['quantity'], $cart_item, false, 0, $section['price_type'] );
				$quantity_string = ( $section['quantity'] > 1 ) ? ' &times; ' . $section['quantity'] : '';

				if ( $quantity_string_shown || $dont_show_mass_quantity ) {
					$quantity_string = '';
				}

				$is_checkbox = false;
				if ( 'checkbox' === $section['type'] ) {
					$quantity_string = '';
					$is_checkbox     = true;
				}

				if ( 'link' !== THEMECOMPLETE_EPO()->tm_epo_cart_field_display ) {
					if ( empty( $section['hidelabelincart'] ) || 'noprice' === $section['hidevalueincart'] || empty( $section['hidevalueincart'] ) ) {
						$value_to_show =
						(
							empty( $section['hidevalueincart'] ) ||
							'noprice' === $section['hidevalueincart'] ||
							'price' === $section['hidevalueincart']
						)
							? apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->entity_decode( $value ), $section )
							: '';

						$item_data[] = [
							'name'            => empty( $section['hidelabelincart'] ) ? $section['label'] : '',
							'value'           => ( empty( $section['hidevalueincart'] ) || 'noprice' === $section['hidevalueincart'] || 'price' === $section['hidevalueincart'] )
								?
								( 'checkbox' !== $section['type'] && 'price' === $section['hidevalueincart'] ? '' : $value_to_show ) .
								(
									'noprice' !== $section['hidevalueincart'] ?
									( ( ! $format_price_shown && $format_price && isset( $quantity_string ) )
										? ' <span class="tc-price-in-cart">' . $format_price . '</span> <span class="tc-quantity-in-cart">' . $quantity_string . '</span>'
										: ( ( $format_price && $is_checkbox )
											? ( ( $do_unique_values )
												? ( ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_cart_average_price ) ? '<span class="tc-average-price">' . $format_price . '</span>' : '' )
												: ( ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_cart_average_price ) ? '<span class="tc-av-price">' . $format_price . '</span>' : '' )
											)
											: ( ( $quantity_string ) ? '<span class="tc-quantity-in-cart">' . $quantity_string . '</span>' : '' )
										)
									)
									: ''
								)
								: '',
							'tm_label'        => $section['label'],
							'tm_value'        => apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->entity_decode( $value ), $section ),
							'tc_simple_value' => apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->entity_decode( $value_original ), $section ),
							'tm_price'        => $format_price,
							'tm_total_price'  => $format_price_total,
							'tm_quantity'     => $section['quantity'],
							'tm_image'        => $section['other_data'][0]['images'],
						];
					}
				}
				if ( empty( $section['hidelabelincart'] ) || empty( $section['hidevalueincart'] ) ) {
					$link_data[] = [
						'name'            => empty( $section['hidelabelincart'] ) ? $section['label'] : '',
						'value'           => ( empty( $section['hidevalueincart'] ) || 'noprice' === $section['hidevalueincart'] ) ? $value_only : '',
						'price'           => $format_price,
						'tm_price'        => $single_price,
						'tm_total_price'  => $format_price_total,
						'tm_quantity'     => $section['quantity'],
						'tm_total_price2' => $format_price_total2,
						'section'         => $section,
					];
				}
			}
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display === 'link' ) {
			if ( empty( $price ) || THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart === 'yes' ) {
				$price = '';
			} else {
				$price = $this->get_price_for_cart( $price, $cart_item, false, 0, $section['price_type'] );
			}
			$uni   = uniqid( '' );
			$data  = '<div class="tm-extra-product-options">';
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
						. '<div class="tc-cell tc-col-4 cpf-value">' . apply_filters( 'wc_epo_label_in_cart', THEMECOMPLETE_EPO_HELPER()->entity_decode( $link['value'] ), $link['section'] ) . '</div>'
						. '<div class="tc-cell tc-col-2 cpf-price">' . $link['tm_price'] . '</div>'
						. '<div class="tc-cell tc-col-1 cpf-quantity">' . ( ( '' === $link['tm_price'] ) ? '' : $link['tm_quantity'] ) . '</div>'
						. '<div class="tc-cell tc-col-1 cpf-total-price">' . $link['tm_total_price2'] . '</div>'
						. '</div>';

			}
			$data .= '</div>';

			/*
			 * using esc_url on $uni gives a wrong result for our JS code
			 * so we use esc_attr since it is basically a hash and not an actual URL
			 */
			$item_data[] = [
				'name'      => '<a href="#tm-cart-link-data-' . esc_attr( $uni ) . '" class="tm-cart-link">' . ( ( ! empty( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_additional_options_text : esc_html__( 'Additional options', 'woocommerce-tm-extra-product-options' ) ) . '</a>',
				'value'     => $price . '<div id="tm-cart-link-data-' . esc_attr( $uni ) . '" class="tm-cart-link-data tm-hidden">' . $data . '</div>',
				'popuplink' => true,
			];
		}

		return $item_data;

	}

	/**
	 * Populate element id array
	 * and global price array
	 *
	 * @param integer      $product_id The product id.
	 * @param array        $post_data The posted data.
	 * @param array        $cart_item_meta The Cart item meta data.
	 * @param string|false $form_prefix The form prefix.
	 *
	 * @since 5.0
	 */
	public function populate_arrays( $product_id = 0, $post_data = [], $cart_item_meta = [], $form_prefix = false ) {

		if ( false === $form_prefix ) {
			$form_prefix = '';

			if ( isset( $cart_item_meta['composite_item'] ) ) {
				$form_prefix = '_' . $cart_item_meta['composite_item'];
			} elseif ( isset( $cart_item_meta['associated_uniqid'] ) && isset( $cart_item_meta['associated_formprefix'] ) ) {
				$form_prefix = str_replace( [ '.', ' ', '[' ], '', $cart_item_meta['associated_formprefix'] );
				$form_prefix = '_' . $form_prefix;
			} else {
				if ( ! empty( $post_data['tc_form_prefix'] ) ) {
					$form_prefix = $post_data['tc_form_prefix'];
					$form_prefix = str_replace( '_', '', $form_prefix );
					$form_prefix = '_' . $form_prefix;
				}
			}

			$this->form_prefix = $form_prefix;
		} else {
			if ( '' !== $form_prefix ) {
				$form_prefix = str_replace( '_', '', $form_prefix );
				$form_prefix = '_' . $form_prefix;
			}
		}

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $form_prefix, true, true );

		if ( empty( $cpf_price_array ) ) {
			return false;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];

		if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
			return false;
		}

		$element_id_array = [];

		$global_prices   = [
			'before' => [],
			'after'  => [],
		];
		$global_sections = [];
		foreach ( $global_price_array as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				if ( isset( $field['sections'] ) ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[ $section['sections_placement'] ][ $priority ][ $pid ]['sections'][ $section_id ] = $section;
							$global_sections[ $section['sections_uniqid'] ] = $section;
							if ( isset( $section['elements'] ) ) {
								foreach ( $section['elements'] as $element_key => $element ) {
									if ( isset( $element['uniqid'] ) && isset( $element['name_inc'] ) ) {
										$element_id_array[ $element['uniqid'] . $form_prefix ] = [
											'name_inc'    => $element['name_inc'],
											'priority'    => $priority,
											'pid'         => $pid,
											'section_id'  => $section_id,
											'element_key' => $element_key,
										];
									}
								}
							}
						}
					}
				}
			}
		}

		if ( false === $cart_item_meta ) {
			return [
				'element_id_array'   => $element_id_array,
				'global_price_array' => $global_price_array,
				'local_price_array'  => $local_price_array,
				'global_prices'      => $global_prices,
				'global_sections'    => $global_sections,
			];
		}

		$this->element_id_array    = $element_id_array;
		$this->global_price_array  = $global_price_array;
		$this->local_price_array   = $local_price_array;
		$this->global_prices       = $global_prices;
		$this->global_sections     = $global_sections;
		$this->populate_arrays_set = true;

		return true;

	}

	/**
	 * Add item data to the cart
	 *
	 * @param array   $cart_item_meta Cart item meta data.
	 * @param integer $product_id The product id.
	 * @param integer $variation_id The variation id.
	 *
	 * @return mixed
	 */
	public function woocommerce_add_cart_item_data( $cart_item_meta, $product_id, $variation_id ) {
		$this->populate_arrays_set = false;

		return $this->add_cart_item_data_helper( $cart_item_meta, $product_id, $_POST ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Adds data to the cart
	 *
	 * @param array      $cart_item_meta Cart item meta data.
	 * @param integer    $product_id The product id.
	 * @param array|null $post_data The posted data.
	 *
	 * @return mixed
	 */
	public function tm_add_cart_item_data( $cart_item_meta, $product_id, $post_data = null ) {
		$this->populate_arrays_set = false;

		return $this->add_cart_item_data_helper( $cart_item_meta, $product_id, $post_data );
	}


	/**
	 * Helper for adding data to the cart
	 *
	 * @param array      $cart_item_meta Cart item meta data.
	 * @param integer    $product_id The product id.
	 * @param array|null $post_data The posted data.
	 *
	 * @return mixed
	 */
	public function add_cart_item_data_helper( $cart_item_meta, $product_id, $post_data = null ) {

		if ( ! is_array( $cart_item_meta ) ) {
			$cart_item_meta = apply_filters( 'wc_epo_add_cart_item_data_no_array', [], $cart_item_meta );
		}

		if ( is_null( $post_data ) && isset( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$post_data = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		if ( empty( $post_data ) && isset( $_REQUEST['tcajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_data = wp_unslash( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		// Normalize posted strings.
		$post_data = THEMECOMPLETE_EPO_HELPER()->normalize_data( $post_data, true, false );

		// Workaround to get unique items in cart for bto.
		if ( empty( $cart_item_meta['tmcartepo_bto'] ) ) {
			$terms        = get_the_terms( $product_id, 'product_type' );
			$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
			if ( ( 'bto' === $product_type || 'composite' === $product_type ) &&
				( isset( $post_data['add-product-to-cart'] ) && is_array( $post_data['add-product-to-cart'] ) ) ||
				( isset( $post_data['wccp_component_selection'] ) && is_array( $post_data['wccp_component_selection'] ) ) ||
				( isset( $_GET['wccp_component_selection'] ) && is_array( $_GET['wccp_component_selection'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {
				$copy = [];
				$enum = [];
				if ( isset( $post_data['add-product-to-cart'] ) ) {
					$enum = $post_data['add-product-to-cart'];
				} elseif ( isset( $post_data['wccp_component_selection'] ) && is_array( $post_data['wccp_component_selection'] ) ) {
					$enum = $post_data['wccp_component_selection'];
				} elseif ( isset( $_GET['wccp_component_selection'] ) && is_array( $_GET['wccp_component_selection'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$enum = sanitize_text_field( wp_unslash( $_GET['wccp_component_selection'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}
				foreach ( $enum as $bundled_item_id => $bundled_product_id ) {
					$copy = array_merge( $copy, THEMECOMPLETE_EPO_HELPER()->array_filter_key( $post_data, $bundled_item_id, 'end' ) );
				}
				$copy                            = THEMECOMPLETE_EPO_HELPER()->array_filter_key( $copy );
				$cart_item_meta['tmcartepo_bto'] = $copy;
			}
		}

		$variation_id        = false;
		$cpf_product_price   = false;
		$per_product_pricing = true;

		if ( isset( $cart_item_meta['composite_item'] ) ) {
			global $woocommerce;
			$cart_contents = $woocommerce->cart->get_cart();

			if ( isset( $cart_item_meta['composite_parent'] ) && ! empty( $cart_item_meta['composite_parent'] ) ) {
				$parent_cart_key = $cart_item_meta['composite_parent'];

				if ( $cart_contents[ $parent_cart_key ]['data'] && is_callable( [ $cart_contents[ $parent_cart_key ]['data'], 'contains' ] ) ) {
					$per_product_pricing = $cart_contents[ $parent_cart_key ]['data']->contains( 'priced_individually' );
				} else {
					$per_product_pricing = $cart_contents[ $parent_cart_key ]['data']->per_product_pricing;
				}

				if ( 'no' === $per_product_pricing ) {
					$per_product_pricing = false;
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
			if ( isset( $cart_item_meta['associated_uniqid'] ) ) {
				if ( isset( $cart_item_meta['associated_variation_id'] ) ) {
					$variation_id = $cart_item_meta['associated_variation_id'];
				}
				$associated_formprefix = $cart_item_meta['associated_formprefix'];

				if ( isset( $cart_item_meta['associated_element_name'] ) && isset( $post_data[ $cart_item_meta['associated_element_name'] . '_counter' ] ) ) {
					$associated_formprefix = $cart_item_meta['associated_formprefix'] . $post_data[ $cart_item_meta['associated_element_name'] . '_counter' ];
				}

				if ( isset( $post_data[ 'cpf_product_price' . $associated_formprefix ] ) ) {
					$cpf_product_price = $post_data[ 'cpf_product_price' . $associated_formprefix ];
				}
			} else {
				if ( isset( $post_data['variation_id'] ) ) {
					$variation_id = $post_data['variation_id'];
				}
				if ( isset( $post_data['cpf_product_price'] ) ) {
					$cpf_product_price = $post_data['cpf_product_price'];
				}
			}
		}
		if ( isset( $cart_item_meta['associated_priced_individually'] ) ) {
			if ( ! $cart_item_meta['associated_priced_individually'] ) {
				$per_product_pricing = false;
				$cpf_product_price   = 0;
			}
		}

		if ( ! $this->populate_arrays( $product_id, $post_data, $cart_item_meta ) ) {
			return $cart_item_meta;
		}

		// If the following key doesn't exist the edit cart link is not being displayed.
		if ( in_array( $product_type, apply_filters( 'wc_epo_can_be_edited_product_type', [ 'simple', 'variable' ] ), true ) ) {
			$cart_item_meta['tmhasepo'] = 1;
		}

		$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id, 'product' ) );
		$tm_meta_cpf         = themecomplete_get_post_meta( $original_product_id, 'tm_meta_cpf', true );
		if ( ! is_array( $tm_meta_cpf ) ) {
			$tm_meta_cpf = [];
		}
		foreach ( THEMECOMPLETE_EPO()->meta_fields as $key => $value ) {
			$tm_meta_cpf[ $key ] = isset( $tm_meta_cpf[ $key ] ) ? $tm_meta_cpf[ $key ] : $value;
		}

		$price_override = ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price )
			? 0
			: ( ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price )
				? 1
				: ( ! empty( $tm_meta_cpf['price_override'] ) ? 1 : 0 ) );

		if ( ! empty( $price_override ) ) {
			$cart_item_meta['epo_price_override'] = 1;
		}

		$files = [];
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
			$cart_item_meta['tmcartepo'] = [];
		}

		if ( empty( $cart_item_meta['tmcartfee'] ) ) {
			$cart_item_meta['tmcartfee'] = [];
		}
		if ( empty( $cart_item_meta['tmpost_data'] ) ) {
			$cart_item_meta['tmpost_data'] = $post_data;
		}

		$cart_item_meta = apply_filters( 'wc_epo_add_cart_item_data_helper', $cart_item_meta );

		if ( empty( $cart_item_meta['tmdata'] ) ) {
			$cart_item_meta['tmdata'] = [
				'tmcp_post_fields'     => $tmcp_post_fields,
				'product_id'           => $product_id,
				'per_product_pricing'  => $per_product_pricing,
				'cpf_product_price'    => $cpf_product_price,
				'variation_id'         => $variation_id,
				'form_prefix'          => $this->form_prefix,
				'tc_added_in_currency' => themecomplete_get_woocommerce_currency(),
				'tc_default_currency'  => apply_filters( 'tc_get_default_currency', get_option( 'woocommerce_currency' ) ),
			];
		}

		$loop       = 0;
		$field_loop = 0;
		$connectors = [];

		$_return        = $this->add_cart_item_data_loop( $this->global_prices, 'before', $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $connectors, $this->form_prefix, $post_data );
		$loop           = $_return['loop'];
		$field_loop     = $_return['field_loop'];
		$connectors     = $_return['connectors'];
		$cart_item_meta = $_return['cart_item_meta'];

		// NORMAL FIELDS (to be deprecated).
		$_return        = $this->add_cart_item_data_loop_local( $this->local_price_array, $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $this->form_prefix, $post_data );
		$loop           = $_return['loop'];
		$field_loop     = $_return['field_loop'];
		$cart_item_meta = $_return['cart_item_meta'];

		$_return        = $this->add_cart_item_data_loop( $this->global_prices, 'after', $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $connectors, $this->form_prefix, $post_data );
		$loop           = $_return['loop'];
		$field_loop     = $_return['field_loop'];
		$connectors     = $_return['connectors'];
		$cart_item_meta = $_return['cart_item_meta'];

		return apply_filters( 'wc_epo_add_cart_item_data', $cart_item_meta );

	}

	/**
	 * Add item data to the cart
	 * NORMAL FIELDS (to be deprecated)
	 *
	 * @param array   $local_price_array The normal options array.
	 * @param array   $cart_item_meta Cart item meta data.
	 * @param array   $tmcp_post_fields Array of posted fields.
	 * @param integer $product_id The product id.
	 * @param boolean $per_product_pricing If the product has pricing, true or false.
	 * @param float   $cpf_product_price The product price.
	 * @param integer $variation_id The variation id.
	 * @param integer $field_loop The field loop index.
	 * @param integer $loop The loop index.
	 * @param string  $form_prefix The form prefix.
	 * @param array   $post_data The posted data.
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

							case 'checkbox':
							case 'radio':
							case 'select':
								$_price = THEMECOMPLETE_EPO()->calculate_price( $_REQUEST, $tmcp, $key, $attribute, 1, 0, 0, $per_product_pricing, $cpf_product_price, $variation_id ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

								$cart_item_meta['tmcartepo'][]                = [
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
								];
								$cart_item_meta['tmdata']['tmcartepo_data'][] = [
									'key'       => $key,
									'attribute' => $attribute,
								];
								break;

						}
					}
					if ( in_array( $tmcp['type'], THEMECOMPLETE_EPO()->element_post_types, true ) ) {
						$field_loop ++;
					}
					$loop ++;

				}
			}
		}

		return [
			'loop'           => $loop,
			'field_loop'     => $field_loop,
			'cart_item_meta' => $cart_item_meta,
		];

	}

	/**
	 * Add item data to the cart
	 * BUILDER FIELDS
	 *
	 * @param array   $global_prices The global option array.
	 * @param string  $where The global option array placement (before or after).
	 * @param array   $cart_item_meta Cart item meta data.
	 * @param array   $tmcp_post_fields Array of posted fields.
	 * @param integer $product_id The product id.
	 * @param boolean $per_product_pricing If the product has pricing, true or false.
	 * @param float   $cpf_product_price The product price.
	 * @param integer $variation_id The variation id.
	 * @param integer $field_loop The field loop index.
	 * @param integer $loop The loop index.
	 * @param array   $connectors The connectors array.
	 * @param string  $form_prefix The form prefix.
	 * @param array   $post_data The posted data.
	 *
	 * @return array
	 */
	public function add_cart_item_data_loop( $global_prices, $where, $cart_item_meta, $tmcp_post_fields, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $connectors, $form_prefix, $post_data ) {

		foreach ( $global_prices[ $where ] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id => $section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {

							$init_class = 'THEMECOMPLETE_EPO_FIELDS_' . $element['type'];
							if ( ! class_exists( $init_class ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_addon ) ) {
								$init_class = 'THEMECOMPLETE_EPO_FIELDS';
							}
							if ( class_exists( $init_class ) ) {
								$field_obj = new $init_class( $product_id, $element, $per_product_pricing, $cpf_product_price, $variation_id, $post_data );

								$c_field_loop = $field_loop;
								if ( isset( $element['connector'] ) && isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
									$c_field_loop = $connectors[ 'c-' . sanitize_key( $element['connector'] ) ];
								}

								// Cart fees.
								$current_tmcp_post_fields = THEMECOMPLETE_EPO_HELPER()->array_intersect_key_wildcard( $tmcp_post_fields, array_flip( THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $c_field_loop, $form_prefix, THEMECOMPLETE_EPO()->cart_fee_name, $element ) ) );
								foreach ( $current_tmcp_post_fields as $attribute => $key ) {
									if ( ! empty( $field_obj->holder_cart_fees ) ) {
										$original_key = $key;
										// We convert all $key to an array
										// in order to support repeaters.
										if ( 'multiple_file_upload' === $element['type'] ) {
											$key = 'temp';
										}
										if ( ! is_array( $key ) ) {
											$key = [ $key ];
										}
										foreach ( $key as $key_id => $key_value ) {
											if ( ! is_array( $key_value ) ) {
												$key_value = [ $key_value ];
											}
											if ( 'multiple_file_upload' === $element['type'] ) {
												$key_value = [ $original_key ];
											}
											foreach ( $key_value as $keyvalue_id => $keyvalue_value ) {
												if ( isset( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
													if ( is_array( $tmcp_post_fields[ $attribute . '_quantity' ] ) && isset( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ] ) ) {
														if ( is_array( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ] ) ) {
															if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ][ $keyvalue_id ] ) ) {
																continue;
															}
														} else {
															if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ] ) ) {
																continue;
															}
														}
													} else {
														if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
															continue;
														}
													}
												}

												if ( count( $key ) > 1 || $key_id > 0 ) {
													$field_obj->repeater = $key_id;
												}
												if ( count( $key_value ) > 1 ) {
													if ( false !== $field_obj->repeater ) {
														$field_obj->repeater = $field_obj->repeater . ' ' . $keyvalue_id;
													} else {
														$field_obj->repeater = $keyvalue_id;
													}
												}

												$meta = $field_obj->add_cart_item_data_cart_fees( $attribute, $keyvalue_value, $key_id, $keyvalue_id );

												if ( is_array( $meta ) ) {
													if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
														foreach ( $meta as $k => $value ) {
															if ( isset( $value['mode'] ) && 'products' !== $value['mode'] ) {
																$cart_item_meta['tmcartfee'][]                = $value;
																$cart_item_meta['tmdata']['tmcartfee_data'][] = [
																	'key'       => $keyvalue_value,
																	'attribute' => $attribute,
																];
															} else {
																$value['element_name']          = $attribute;
																$cart_item_meta['tmproducts'][] = $value;
															}
														}
													} else {
														if ( isset( $meta['mode'] ) && 'products' !== $meta['mode'] ) {
															$cart_item_meta['tmcartfee'][]                = $meta;
															$cart_item_meta['tmdata']['tmcartfee_data'][] = [
																'key' => $keyvalue_value,
																'attribute' => $attribute,
															];
														} else {
															$meta['element_name']           = $attribute;
															$cart_item_meta['tmproducts'][] = $meta;
														}
													}
												}
											}
										}
									}
								}

								// Normal fields.
								$current_tmcp_post_fields = THEMECOMPLETE_EPO_HELPER()->array_intersect_key_wildcard( $tmcp_post_fields, array_flip( THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $c_field_loop, $form_prefix, '', $element ) ) );
								foreach ( $current_tmcp_post_fields as $attribute => $key ) {
									$original_key = $key;
									if ( ! empty( $field_obj->holder ) ) {
										// We convert all $key to an array
										// in order to support repeaters.
										if ( 'multiple_file_upload' === $element['type'] ) {
											$key = 'temp';
										}
										if ( ! is_array( $key ) ) {
											$key = [ $key ];
										}
										foreach ( $key as $key_id => $key_value ) {
											if ( ! is_array( $key_value ) ) {
												$key_value = [ $key_value ];
											}
											if ( 'multiple_file_upload' === $element['type'] ) {
												$key_value = [ $original_key ];
											}
											if ( 'singlemultiple' === $field_obj->holder ) {
												$key_value = [ $key_value ];
											}
											foreach ( $key_value as $keyvalue_id => $keyvalue_value ) {
												if ( isset( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
													if ( is_array( $tmcp_post_fields[ $attribute . '_quantity' ] ) && isset( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ] ) ) {
														if ( is_array( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ] ) ) {
															if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ][ $keyvalue_id ] ) ) {
																continue;
															}
														} else {
															if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ][ $key_id ] ) ) {
																continue;
															}
														}
													} else {
														if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
															continue;
														}
													}
												}

												if ( count( $key ) > 1 || $key_id > 0 ) {
													$field_obj->repeater = $key_id;
												}
												if ( count( $key_value ) > 1 ) {
													if ( false !== $field_obj->repeater ) {
														$field_obj->repeater = $field_obj->repeater . ' ' . $keyvalue_id;
													} else {
														$field_obj->repeater = $keyvalue_id;
													}
												}

												$meta = $field_obj->add_cart_item_data( $attribute, $keyvalue_value, $key_id, $keyvalue_id );

												if ( is_array( $meta ) ) {
													if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
														foreach ( $meta as $k => $value ) {
															if ( isset( $value['mode'] ) && 'products' !== $value['mode'] ) {
																$cart_item_meta['tmcartepo'][]                = $value;
																$cart_item_meta['tmdata']['tmcartepo_data'][] = [
																	'key'       => $keyvalue_value,
																	'attribute' => $attribute,
																];
															} else {
																$value['element_name']          = $attribute;
																$cart_item_meta['tmproducts'][] = $value;
															}
														}
													} else {
														if ( isset( $meta['mode'] ) && 'products' !== $meta['mode'] ) {
															$cart_item_meta['tmcartepo'][]                = $meta;
															$cart_item_meta['tmdata']['tmcartepo_data'][] = [
																'key' => $keyvalue_value,
																'attribute' => $attribute,
															];
														} else {
															$meta['element_name']           = $attribute;
															$cart_item_meta['tmproducts'][] = $meta;
														}
													}
												}
											}
										}
									}
								}

								$cart_item_meta = apply_filters( 'wc_epo_add_cart_item_data_loop', $cart_item_meta, $field_obj, $tmcp_post_fields, $element, $c_field_loop, $form_prefix, $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $post_data );
								unset( $field_obj ); // clear memory.
							}

							if ( in_array( $element['type'], THEMECOMPLETE_EPO()->element_post_types, true ) ) {
								if ( isset( $element['connector'] ) && '' !== $element['connector'] ) {
									if ( ! isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
										$field_loop ++;
									}
									$connectors[ 'c-' . sanitize_key( $element['connector'] ) ] = $c_field_loop;
								} else {
									$field_loop ++;
								}
							}
							$loop ++;

						}
					}
				}
			}
		}

		return [
			'loop'           => $loop,
			'field_loop'     => $field_loop,
			'cart_item_meta' => $cart_item_meta,
			'connectors'     => $connectors,
		];

	}

	/**
	 * Validates the cart data
	 *
	 * @param boolean $passed The current passed status.
	 * @param integer $product_id The product id.
	 * @param integer $qty The product quantity.
	 * @param integer $variation_id The variation id.
	 * @param array   $variations The variation attributes array.
	 * @param array   $cart_item_data Cart item meta data.
	 * @since 1.0
	 */
	public function woocommerce_add_to_cart_validation( $passed, $product_id, $qty = 0, $variation_id = 0, $variations = [], $cart_item_data = [] ) {

		// disables add_to_cart_button class on shop page.
		if ( wp_doing_ajax() && 'yes' === THEMECOMPLETE_EPO()->tm_epo_force_select_options && ! isset( $_REQUEST['tcaddtocart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$has_epo = THEMECOMPLETE_EPO_API()->has_options( $product_id );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				return false;
			}
		}

		$is_validate = true;

		// Get product type.
		$terms        = get_the_terms( $product_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		if ( 'bto' === $product_type || 'composite' === $product_type ) {

			$bto_data  = themecomplete_maybe_unserialize( themecomplete_get_post_meta( $product_id, '_bto_data', true ) );
			$valid_ids = [];
			if ( is_array( $bto_data ) ) {
				$valid_ids = array_keys( $bto_data );
			}
			foreach ( $valid_ids as $bundled_item_id ) {

				if ( isset( $_REQUEST['add-product-to-cart'][ $bundled_item_id ] ) && '' !== $_REQUEST['add-product-to-cart'][ $bundled_item_id ] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$bundled_product_id = absint( wp_unslash( $_REQUEST['add-product-to-cart'][ $bundled_item_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} elseif ( isset( $cart_item_data['composite_data'][ $bundled_item_id ]['product_id'] ) && isset( $_GET['order_again'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$bundled_product_id = $cart_item_data['composite_data'][ $bundled_item_id ]['product_id'];
				} elseif ( isset( $_REQUEST['wccp_component_selection'] ) && isset( $_REQUEST['wccp_component_selection'][ $bundled_item_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$bundled_product_id = absint( wp_unslash( $_REQUEST['wccp_component_selection'][ $bundled_item_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				if ( isset( $bundled_product_id ) && ! empty( $bundled_product_id ) ) {

					$_passed = true;

					if ( isset( $_REQUEST['item_quantity'][ $bundled_item_id ] ) && is_numeric( $_REQUEST['item_quantity'][ $bundled_item_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$item_quantity = absint( wp_unslash( $_REQUEST['item_quantity'][ $bundled_item_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					} elseif ( isset( $cart_item_data['composite_data'][ $bundled_item_id ]['quantity'] ) && isset( $_GET['order_again'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$item_quantity = $cart_item_data['composite_data'][ $bundled_item_id ]['quantity'];
					} elseif ( isset( $_REQUEST['wccp_component_quantity'][ $bundled_item_id ] ) && is_numeric( $_REQUEST['wccp_component_quantity'][ $bundled_item_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$item_quantity = absint( wp_unslash( $_REQUEST['wccp_component_quantity'][ $bundled_item_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					}
					if ( ! empty( $item_quantity ) ) {
						$item_quantity = absint( $item_quantity );
						$_passed       = $this->validate_product_id( $bundled_product_id, $bundled_item_id );
					}

					if ( ! $_passed ) {
						$is_validate = false;
					}
				}
			}
		}

		$tc_form_prefix = '';
		if ( isset( $_REQUEST['tc_form_prefix'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tc_form_prefix = sanitize_text_field( wp_unslash( $_REQUEST['tc_form_prefix'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( ! $this->validate_product_id( $product_id, $tc_form_prefix ) ) {
			$passed = false;
		}

		// Try to validate uploads before they happen.
		foreach ( $_FILES as $k => $file ) {
			if ( ! empty( $file['name'] ) && 'undefined' !== $file['name'] ) {
				$file_name = $file['name'];
				if ( ! empty( $file['error'] ) ) {
					$file_error = $file['error'];

					// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
					$upload_error_strings = [
						false,
						esc_html__( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'The uploaded file was only partially uploaded.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'No file was uploaded.', 'woocommerce-tm-extra-product-options' ),
						'',
						esc_html__( 'Missing a temporary folder.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'Failed to write file to disk.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'File upload stopped by extension.', 'woocommerce-tm-extra-product-options' ),
					];

					if ( is_array( $file_error ) ) {
						foreach ( $file_error as $key => $value ) {
							if ( is_array( $value ) ) {
								foreach ( $value as $value_id => $value_value ) {
									if ( ! empty( $value_value ) && ! empty( $file_name[ $key ][ $value_id ] ) ) {
										$passed = false;
										if ( isset( $upload_error_strings[ $value_value ] ) ) {
											wc_add_notice( $upload_error_strings[ $value ], 'error' );
										}
									}
								}
							} else {
								if ( ! empty( $value ) && ! empty( $file_name[ $key ] ) ) {
									$passed = false;
									if ( isset( $upload_error_strings[ $value ] ) ) {
										wc_add_notice( $upload_error_strings[ $value ], 'error' );
									}
								}
							}
						}
					} else {
						$passed = false;
						if ( isset( $upload_error_strings[ $file_error ] ) ) {
							wc_add_notice( $upload_error_strings[ $file_error ], 'error' );
						}
					}
				}
				add_filter( 'upload_mimes', [ THEMECOMPLETE_EPO(), 'upload_mimes_trick' ] );
				if ( is_array( $file_name ) ) {
					foreach ( $file_name as $key => $value ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $value_id => $value_value ) {
								if ( ! empty( $value_value ) ) {
									$check_filetype = wp_check_filetype( $value_value );
									$check_filetype = $check_filetype['ext'];
									if ( ! $check_filetype && ! empty( $file_name[ $key ][ $value_id ] ) ) {
										$passed = false;
										wc_add_notice( esc_html__( 'Sorry, this file type is not permitted for security reasons.', 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $value_value, PATHINFO_EXTENSION ) . ')', 'error' );
									}
								}
							}
						} else {
							if ( ! empty( $value ) ) {
								$check_filetype = wp_check_filetype( $value );
								$check_filetype = $check_filetype['ext'];
								if ( ! $check_filetype && ! empty( $file['name'] ) ) {
									$passed = false;
									wc_add_notice( esc_html__( 'Sorry, this file type is not permitted for security reasons.', 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $value, PATHINFO_EXTENSION ) . ')', 'error' );
								}
							}
						}
					}
				} else {
					$check_filetype = wp_check_filetype( $file['name'] );
					$check_filetype = $check_filetype['ext'];

					if ( ! $check_filetype && ! empty( $file['name'] ) ) {
						$passed = false;
						wc_add_notice( esc_html__( 'Sorry, this file type is not permitted for security reasons.', 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $file['name'], PATHINFO_EXTENSION ) . ')', 'error' );
					}
				}
				remove_filter( 'upload_mimes', [ THEMECOMPLETE_EPO(), 'upload_mimes_trick' ] );

			}
		}

		if ( ! $is_validate ) {
			$passed = false;
		}

		return apply_filters( 'tm_add_to_cart_validation', $passed );

	}

	/**
	 * Validates builder options
	 *
	 * @param array   $global_sections The global sections array.
	 * @param array   $global_prices The global option array.
	 * @param string  $where The global option array placement (before or after).
	 * @param array   $tmcp_post_fields Array of posted fields.
	 * @param boolean $passed The current passed status.
	 * @param integer $loop The loop index.
	 * @param string  $form_prefix The form prefix.
	 *
	 * @return array
	 */
	public function validate_product_id_loop( $global_sections, $global_prices, $where, $tmcp_post_fields, $passed, $loop, $form_prefix ) {
		// Initialize the connectors.
		$connectors = [];
		foreach ( $global_prices[ $where ] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id => $section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {

							if ( in_array( $element['type'], THEMECOMPLETE_EPO()->element_post_types, true ) ) {
								if ( empty( $element['connector'] ) || ! in_array( $element['connector'], $connectors, true ) ) {
									if ( ! empty( $element['connector'] ) ) {
										$connectors[] = $element['connector'];
									}
									// $loop is incremented only if connector is empty or is not in the connectors array.
									$loop ++;
								}
							}

							if ( isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] )
								&& 'display' !== THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_post
								&& THEMECOMPLETE_EPO()->is_visible( $element, $section, $global_sections, $form_prefix )
							) {

								$_passed  = true;
								$_message = false;

								$init_class = 'THEMECOMPLETE_EPO_FIELDS_' . $element['type'];
								if ( ! class_exists( $init_class ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_addon ) ) {
									$init_class = 'THEMECOMPLETE_EPO_FIELDS';
								}
								if ( class_exists( $init_class ) ) {
									$field_obj = new $init_class();
									$_passed   = $field_obj->validate_field( $tmcp_post_fields, $element, $loop, $form_prefix );
									$_message  = isset( $_passed['message'] ) ? $_passed['message'] : false;
									$_passed   = isset( $_passed['passed'] ) ? $_passed['passed'] : false;
									unset( $field_obj ); // clear memory.
								}

								if ( ! $_passed ) {

									$passed = false;
									if ( false !== $_message && is_array( $_message ) ) {
										foreach ( $_message as $key => $value ) {
											if ( 'required' === $value ) {
												/* translators: %s Field name */
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

		return [
			'loop'   => $loop,
			'passed' => $passed,
		];

	}

	/**
	 * Validates a product
	 *
	 * @param integer $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 *
	 * @return boolean
	 */
	public function validate_product_id( $product_id, $form_prefix = '' ) {

		$passed = true;

		if ( $form_prefix ) {
			$form_prefix = '_' . $form_prefix;
		}

		$populate_arrays = $this->populate_arrays( $product_id, false, false, $form_prefix );
		if ( ! $populate_arrays ) {
			return $passed;
		}

		$global_prices      = $populate_arrays['global_prices'];
		$global_sections    = $populate_arrays['global_sections'];
		$global_price_array = $populate_arrays['global_price_array'];
		$local_price_array  = $populate_arrays['local_price_array'];

		if ( ( ! empty( $global_price_array ) && is_array( $global_price_array ) && count( $global_price_array ) > 0 ) || ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) ) {
			$tmcp_post_fields = THEMECOMPLETE_EPO_HELPER()->array_filter_key( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( is_array( $tmcp_post_fields ) && ! empty( $tmcp_post_fields ) && count( $tmcp_post_fields ) > 0 ) {
				$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
			}

			$loop = -1;

			$_return = $this->validate_product_id_loop( $global_sections, $global_prices, 'before', $tmcp_post_fields, $passed, $loop, $form_prefix );
			$loop    = $_return['loop'];
			$passed  = $_return['passed'];

			if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

				foreach ( $local_price_array as $tmcp ) {

					if ( in_array( $tmcp['type'], THEMECOMPLETE_EPO()->element_post_types, true ) ) {
						$loop ++;
					}
					if ( empty( $tmcp['type'] ) || empty( $tmcp['required'] ) ) {
						continue;
					}

					if ( $tmcp['required'] ) {

						$tmcp_attributes = THEMECOMPLETE_EPO()->get_post_names( $tmcp['attributes'], $tmcp['type'], $loop, $form_prefix );
						$_passed         = true;

						switch ( $tmcp['type'] ) {

							case 'checkbox':
								$_check = array_intersect( $tmcp_attributes, array_keys( $tmcp_post_fields ) );
								if ( empty( $_check ) || 0 === count( $_check ) ) {
									$_passed = false;
								}
								break;

							case 'radio':
								foreach ( $tmcp_attributes as $attribute ) {
									if ( ! isset( $tmcp_post_fields[ $attribute ] ) ) {
										$_passed = false;
									}
								}
								break;

							case 'select':
								foreach ( $tmcp_attributes as $attribute ) {
									if ( ! isset( $tmcp_post_fields[ $attribute ] ) || '' === $tmcp_post_fields[ $attribute ] ) {
										$_passed = false;
									}
								}
								break;

						}

						if ( ! $_passed ) {
							$passed = false;
							/* translators: %s Field name */
							wc_add_notice( sprintf( esc_html__( '"%s" is a required field.', 'woocommerce-tm-extra-product-options' ), $tmcp['label'] ), 'error' );
						}
					}
				}
			}

			$_return = $this->validate_product_id_loop( $global_sections, $global_prices, 'after', $tmcp_post_fields, $passed, $loop, $form_prefix );
			$loop    = $_return['loop'];
			$passed  = $_return['passed'];

		}

		return $passed;

	}

	/**
	 * Alter the product thumbnail in cart
	 *
	 * @param string $image The image html.
	 * @param array  $cart_item The item data.
	 * @param string $cart_item_key The cart item key.
	 * @since 1.0
	 */
	public function woocommerce_cart_item_thumbnail( $image = '', $cart_item = [], $cart_item_key = '' ) {

		$_image = [];
		$_alt   = [];
		if ( isset( $cart_item['tmcartepo'] ) && is_array( $cart_item['tmcartepo'] ) ) {
			foreach ( $cart_item['tmcartepo'] as $key => $value ) {
				if ( ! empty( $value['changes_product_image'] ) ) {
					if ( 'images' === $value['changes_product_image'] ) {
						if ( isset( $value['use_images'] ) && 'images' === $value['use_images'] && isset( $value['images'] ) ) {
							$_image[] = $value['images'];
							$_alt[]   = $value['value'];
						}
					} elseif ( 'custom' === $value['changes_product_image'] ) {
						if ( isset( $value['imagesp'] ) ) {
							$_image[] = $value['imagesp'];
							$_alt[]   = $value['value'];
						}
					}
				}
			}
		}
		if ( count( $_image ) === 0 ) {
			if ( isset( $cart_item['tmcartfee'] ) && is_array( $cart_item['tmcartfee'] ) ) {
				foreach ( $cart_item['tmcartfee'] as $key => $value ) {
					if ( ! empty( $value['changes_product_image'] ) ) {
						if ( 'images' === $value['changes_product_image'] ) {
							if ( isset( $value['use_images'] ) && 'images' === $value['use_images'] && isset( $value['images'] ) ) {
								$_image[] = $value['images'];
								$_alt[]   = $value['value'];
							}
						} elseif ( 'custom' === $value['changes_product_image'] ) {
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
			$current      = 0;
			$_image_count = count( $_image );
			for ( $i = 0; $i <= $_image_count; $i ++ ) {
				if ( ! empty( $_image[ $i ] ) ) {
					$current = $i;
				}
			}
			if ( ! empty( $_image[ $current ] ) ) {
				$size       = 'shop_thumbnail';
				$dimensions = wc_get_image_size( $size );
				$image      = apply_filters(
					'tm_woocommerce_img',
					'<img src="' . apply_filters( 'tm_woocommerce_img_src', $_image[ $current ] )
					. '" alt="'
					. esc_attr( wp_strip_all_tags( $_alt[ $current ] ) )
					. '" width="' . esc_attr( $dimensions['width'] )
					. '" class="tc-thumbnail woocommerce-placeholder wp-post-image" height="'
					. esc_attr( $dimensions['height'] )
					. '" />',
					$size,
					$dimensions
				);
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

				do_action( 'wc_epo_cart_loaded_from_session_before_cart_item', $cart_item );

				// for displaying eg the colors for color replacements.
				$other_data = $this->get_item_data_array( [], $cart_item );

				if ( isset( $cart_item['tcremoved'] ) && ! empty( $cart_item['tcremoved'] ) ) {
					$product = $cart_item['data'];
					unset( WC()->cart->cart_contents[ $cart_item_key ] );
					/* translators: %1 Product name %2 here link */
					wc_add_notice( sprintf( esc_html__( '%1$s has been removed from your cart because it has since been modified. You can add it back to your cart %2$s.', 'woocommerce-tm-extra-product-options' ), $product->get_name(), '<a href="' . $product->get_permalink() . '">' . esc_html__( 'here', 'woocommerce-tm-extra-product-options' ) . '</a>' ), 'notice' );
				} else {
					WC()->cart->cart_contents[ $cart_item_key ][ THEMECOMPLETE_EPO()->cart_edit_key_var ] = $cart_item_key;
				}

				do_action( 'wc_epo_cart_loaded_from_session_after_cart_item', $cart_item );

			}
		}

	}

	/**
	 * Calculate totals on remove from cart/update
	 *
	 * @param boolean $cart_updated The current cart_updated status.
	 * @since 1.0
	 */
	public function woocommerce_update_cart_action_cart_updated( $cart_updated = false ) {

		if ( apply_filters( 'wc_epo_update_cart_action_cart_updated', false, $cart_updated ) ) {
			return $cart_updated;
		}

		$cart_contents = WC()->cart->cart_contents;
		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['tm_epo_options_prices'] ) ) {
					$cart_updated = true;
				}
				if ( ! empty( $cart_item['tmpost_data'] ) && themecomplete_get_product_type( $cart_item['data'] ) !== 'composite' ) {
					$post_data = wp_unslash( $cart_item['tmpost_data'] );
					if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
						$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
					}
					// todo:check for a better alternative.
					if ( ! isset( $post_data['cpf_product_price'] ) && isset( $cart_item['tm_epo_product_original_price'] ) ) {
						$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
					} else {
						$post_data['cpf_product_price'] = 0;
					}
					$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
					$post_data['quantity']          = $cart_item['quantity'];
					$new_cart_item                  = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data );
					if ( false === $new_cart_item ) {
						continue;
					}

					$cart_item = apply_filters( 'tm_cart_contents', $new_cart_item, [] );

					WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
				}
			}
		}

		return $cart_updated;

	}

	/**
	 * Support for fee price types
	 *
	 * @param object $cart_object The cart object.
	 * @since 1.0
	 */
	public function woocommerce_cart_calculate_fees( $cart_object = [] ) {

		if ( is_array( $cart_object->cart_contents ) ) {

			$to_currency = themecomplete_get_woocommerce_currency();

			foreach ( $cart_object->cart_contents as $key => $cart_item ) {
				$tax_class      = themecomplete_get_tax_class( $cart_item['data'] );
				$get_tax_status = is_callable( [ $cart_item['data'], 'get_tax_status' ] ) ? $cart_item['data']->get_tax_status() : $cart_item['data']->tax_status;
				if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) && 'taxable' === $get_tax_status ) {
					$tax = true;
				} else {
					$tax = false;
				}

				$did_repopulatecart = false;
				$tmcartfee          = isset( $cart_item['tmcartfee'] ) ? $cart_item['tmcartfee'] : [];
				foreach ( $tmcartfee as $cartfee ) {
					$_price_type = THEMECOMPLETE_EPO()->get_saved_element_price_type( $cartfee );
					if ( 'math' === $_price_type && ! $did_repopulatecart ) {
						if ( ! empty( $cart_item['tmpost_data'] ) ) {
							$post_data = wp_unslash( $cart_item['tmpost_data'] );
							if ( isset( $cart_item['tm_epo_options_static_prices'] ) ) {
								$post_data['tm_epo_options_static_prices'] = $cart_item['tm_epo_options_static_prices'];
							}
							// todo:check for a better alternative.
							if ( ! isset( $post_data['cpf_product_price'] ) ) {
								$post_data['cpf_product_price'] = $cart_item['tm_epo_product_original_price'];
							}
							$post_data['cpf_product_price'] = apply_filters( 'wc_epo_add_cart_item_original_price', $post_data['cpf_product_price'], $cart_item );
							$post_data['quantity']          = $cart_item['quantity'];
							$_cart_item                     = $this->repopulatecart( $cart_item, $cart_item['product_id'], $post_data, false, '', 'tmcartfee' );
							$did_repopulatecart             = true;
							if ( false !== $_cart_item ) {
								$cart_item = apply_filters( 'tm_cart_contents', $_cart_item, [] );
							}
							break;
						}
					}
				}

				$tmcartfee = isset( $cart_item['tmcartfee'] ) ? $cart_item['tmcartfee'] : false;

				if ( $tmcartfee && is_array( $tmcartfee ) ) {
					foreach ( $tmcartfee as $cartfee ) {
						$_price_type      = THEMECOMPLETE_EPO()->get_saved_element_price_type( $cartfee );
						$new_price        = $cartfee['price'];
						$new_price        = apply_filters( 'wc_epo_get_current_currency_price', apply_filters( 'wc_epo_price_on_cart', $new_price, $cart_item ), $_price_type );
						$hidelabelincart  = isset( $cartfee['hidelabelincart'] ) ? $cartfee['hidelabelincart'] : '';
						$hidevalueincart  = isset( $cartfee['hidevalueincart'] ) ? $cartfee['hidevalueincart'] : '';
						$hidelabelinorder = isset( $cartfee['hidelabelinorder'] ) ? $cartfee['hidelabelinorder'] : '';
						$hidevalueinorder = isset( $cartfee['hidevalueinorder'] ) ? $cartfee['hidevalueinorder'] : '';

						$new_name = '';

						if ( ! $hidelabelincart && ! $hidelabelinorder ) {
							$new_name = $cartfee['name'];
							if ( empty( $new_name ) ) {
								$new_name = esc_html__( 'Extra fee', 'woocommerce-tm-extra-product-options' );
							}
						}

						if ( ! $hidevalueincart && ! $hidevalueinorder ) {
							$fee_value = '';
							if ( isset( $cartfee['display'] ) ) {
								$fee_value = $cartfee['display'];
							} else {
								$fee_value = $cartfee['value'];
							}

							if ( '' !== $fee_value && '' !== $new_name && ! $hidevalueincart && ! $hidevalueinorder ) {
								$new_name .= apply_filters( 'wc_epo_fee_quantity_separator', ' - ' ) . $fee_value;
							}
						}

						// Fee names cannot be empty.
						if ( '' === $new_name ) {
							$new_name = esc_html__( 'Extra fee', 'woocommerce-tm-extra-product-options' );
						}

						if ( floatval( $cartfee['quantity'] ) > 1 ) {
							$new_name .= apply_filters( 'wc_epo_fee_quantity_times', ' &times; ' ) . $cartfee['quantity'];
						}
						$canbadded = true;

						$fees = [];
						if ( is_object( $cart_object ) && is_callable( [ $cart_object, 'get_fees' ] ) ) {
							$fees = $cart_object->get_fees();
						} else {
							$fees = $cart_object->fees;
						}
						if ( is_array( $fees ) ) {
							foreach ( $fees as $fee ) {
								if ( (string) sanitize_title( $new_name ) === (string) $fee->id ) {
									if ( apply_filters( 'wc_epo_add_same_fee', true, $new_price, $fee->amount ) ) {
										$fee->amount = $fee->amount + (float) $new_price;
									}
									$canbadded = false;
									break;
								}
							}
						}
						if ( $canbadded ) {

							$current_tax       = $tax;
							$current_tax_class = $tax_class;
							if ( isset( $cartfee['include_tax_for_fee_price_type'] ) && '' !== $cartfee['include_tax_for_fee_price_type'] ) {
								if ( 'yes' === $cartfee['include_tax_for_fee_price_type'] ) {
									$current_tax = true;
								} elseif ( 'no' === $cartfee['include_tax_for_fee_price_type'] ) {
									$current_tax = false;
								}
							}
							if ( isset( $cartfee['tax_class_for_fee_price_type'] ) && '' !== $cartfee['tax_class_for_fee_price_type'] ) {
								$current_tax_class = $cartfee['tax_class_for_fee_price_type'];
								if ( '@' === $cartfee['tax_class_for_fee_price_type'] ) {
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
	 * @param float   $price The element price.
	 * @param integer $product_id The product id.
	 * @param array   $element The element array.
	 * @param string  $attribute The posted element name.
	 * @since 1.0
	 */
	public function calculate_fee_price( $price = 0, $product_id = 0, $element = [], $attribute = '' ) {

		global $woocommerce;
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return $price;
		}

		$taxable   = $product->is_taxable();
		$tax_class = themecomplete_get_tax_class( $product );

		if ( $element ) {
			if ( isset( $element['include_tax_for_fee_price_type'] ) ) {
				if ( 'no' === $element['include_tax_for_fee_price_type'] ) {
					$taxable = false;
				}
				if ( 'yes' === $element['include_tax_for_fee_price_type'] ) {
					$taxable = true;
				}
			}
			if ( isset( $element['tax_class_for_fee_price_type'] ) ) {
				$tax_class = $element['tax_class_for_fee_price_type'];
			}
		}

		// Taxable.
		if ( $taxable ) {

			if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' ) {
				$tax_rates = WC_Tax::get_base_tax_rates( $tax_class );
				$taxes     = WC_Tax::calc_tax( $price, $tax_rates, true );
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
		echo '<button type="submit" class="tm-clear-cart-button button" name="tm_empty_cart" value="' . esc_attr( $text ) . '">' . esc_attr( $text ) . '</button>';
	}

	/**
	 * Empties the cart
	 *
	 * @since 1.0
	 */
	public function tm_empty_cart() {

		if ( ! isset( WC()->cart ) || '' === WC()->cart ) {
			WC()->cart = new WC_Cart();
		}
		WC()->cart->empty_cart( true );

	}

	/**
	 * Empties the cart from the clear cart button
	 *
	 * @since 1.0
	 */
	public function clear_cart() {

		if ( isset( $_REQUEST['tm_empty_cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->tm_empty_cart();
		}

	}

	/**
	 * Override templates for Cart advanced template system
	 *
	 * @param string $located The file to locate.
	 * @param string $template_name The template name.
	 * @since 1.0
	 */
	public function tm_wc_get_template( $located = '', $template_name = '' ) {

		$templates = [ 'cart/cart-item-data.php' ];

		if ( in_array( $template_name, $templates, true ) ) {
			$_located = wc_locate_template( $template_name, THEMECOMPLETE_EPO_DISPLAY()->get_template_path(), THEMECOMPLETE_EPO_DISPLAY()->get_default_path() );
			if ( file_exists( $_located ) ) {
				$located = $_located;
			}
		}

		return $located;

	}

	/**
	 * Advanced template system - Alter item subtoal
	 *
	 * @param string $subtotal The subtotal string.
	 * @param object $cart_item The cart item object.
	 * @param string $cart_item_key The cart item key.
	 * @since 1.0
	 */
	public function woocommerce_cart_item_subtotal( $subtotal = '', $cart_item = '', $cart_item_key = '' ) {

		// is_cart() is used to filter out the review order screen.
		if ( 'advanced' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( is_cart() ) {

				$original_subtotal = $subtotal;

				$subtotal = '';

				if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart ) {
					if ( isset( $cart_item['tm_epo_product_after_adjustment'] ) && isset( THEMECOMPLETE_EPO()->tm_epo_dpd_enable ) && 'no' === THEMECOMPLETE_EPO()->tm_epo_dpd_enable ) {
						$price = $cart_item['tm_epo_product_after_adjustment'];
					} else {
						$price = isset( $cart_item['tm_epo_product_original_price'] ) ? $cart_item['tm_epo_product_original_price'] : $_product->get_price();
						$price = apply_filters( 'wc_epo_discounted_price', $price, wc_get_product( $cart_item['product_id'] ), $cart_item_key, true );
					}
					$price     = floatval( $price ) * floatval( $cart_item['quantity'] );
					$subtotal .= apply_filters( 'wc_tm_epo_ac_subtotal_price', $this->get_price_for_cart( $price, $cart_item, '' ), $cart_item_key, $cart_item, $_product, $product_id );
				} else {
					$subtotal .= apply_filters( 'wc_tm_epo_ac_subtotal_price', $subtotal, $cart_item_key, $cart_item, $_product, $product_id );
				}

				$subtotal .= $this->cart_add_option_rows( $original_subtotal, $cart_item_key, $cart_item, $_product, $product_id );

			} elseif ( defined( 'WOOCOMMERCE_CHECKOUT' ) || THEMECOMPLETE_EPO()->wc_vars['is_checkout'] ) {

				if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart ) {
					if ( isset( $cart_item['tm_epo_product_after_adjustment'] ) && 'no' === THEMECOMPLETE_EPO()->tm_epo_dpd_enable ) {
						$price = $cart_item['tm_epo_product_after_adjustment'];
					} else {
						$price = isset( $cart_item['tm_epo_product_original_price'] ) ? $cart_item['tm_epo_product_original_price'] : $_product->get_price();
						$price = apply_filters( 'wc_epo_discounted_price', $price, wc_get_product( $cart_item['product_id'] ), $cart_item_key );
					}
					$price = floatval( $price ) * floatval( $cart_item['quantity'] );

					$subtotal = apply_filters( 'wc_tm_epo_ac_subtotal_prices', $this->get_price_for_cart( $price, $cart_item, '' ), $cart_item, $cart_item_key );

					$subtotal .= $this->checkout_add_option_rows( $cart_item_key, $cart_item, $_product, $product_id );

				}
			}
		}

		return $subtotal;

	}

	/**
	 * Advanced template system - Alter product quantity
	 *
	 * @param string $product_quantity The product quantity.
	 * @param string $cart_item_key The cart item key.
	 * @param object $cart_item The cart item object.
	 * @since 1.0
	 */
	public function woocommerce_cart_item_quantity( $product_quantity = '', $cart_item_key = '', $cart_item = '' ) {

		$this->saved_product_quantity = $product_quantity;

		$no_epo = apply_filters( 'wc_epo_no_epo_in_cart', empty( $cart_item['tmcartepo'] ), $cart_item );

		if ( 'advanced' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display && ! $no_epo ) {

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
	 * @param string $price The product price.
	 * @param object $cart_item The cart item object.
	 * @param string $cart_item_key The cart item key.
	 * @since 1.0
	 */
	public function woocommerce_cart_item_price( $price = '', $cart_item = '', $cart_item_key = '' ) {

		// is_cart() is used to filter out the mini cart hook.
		if ( is_cart() && 'advanced' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display ) {

			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart ) {
				$variation_id = $cart_item['variation_id'];
				if ( empty( $variation_id ) ) {
					$variation_id = $product_id;
				}
				$original_product = wc_get_product( $variation_id );

				$price = isset( $cart_item['tm_epo_product_original_price'] ) ? $cart_item['tm_epo_product_original_price'] : $cart_item['data']->get_price();
				$price = apply_filters( 'wc_tm_epo_ac_product_price', $this->get_price_for_cart( $price, $cart_item, '' ), $cart_item_key, $cart_item, $original_product, $product_id );
			} else {
				$price = apply_filters( 'wc_tm_epo_ac_product_price', $price, $cart_item_key, $cart_item, $_product, $product_id );
			}
		}

		return $price;

	}

	/**
	 * Advanced template system - Add custom class name
	 *
	 * @param string $class The cart item class.
	 * @param object $cart_item The cart item object.
	 * @param string $cart_item_key The cart item key.
	 * @since 1.0
	 */
	public function woocommerce_cart_item_class( $class = '', $cart_item = '', $cart_item_key = '' ) {

		$no_epo = apply_filters( 'wc_epo_no_epo_in_cart', empty( $cart_item['tmcartepo'] ), $cart_item );

		// is_cart() is used to filter out the review order screen.
		if ( is_cart() && 'advanced' === THEMECOMPLETE_EPO()->tm_epo_cart_field_display && ! $no_epo ) {
			$class .= ' tm-epo-cart-row-product';
		} else {
			$class .= ' tm-epo-cart-row-product-noepo';
		}

		return $class;

	}

	/**
	 * Custom actions running for advanced template system
	 *
	 * @param string  $cart_item_key The cart item key.
	 * @param object  $cart_item The cart item object.
	 * @param object  $_product The product object.
	 * @param integer $product_id The product id.
	 * @since 1.0
	 */
	public function checkout_add_option_rows( $cart_item_key = '', $cart_item = '', $_product = '', $product_id = 0 ) {

		$out = [];

		$other_data = [];
		if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart ) {
			$other_data = $this->get_item_data_array( [], $cart_item );
		}
		$odd = 1;
		foreach ( $other_data as $key => $value ) {
			$zebra_class = 'odd ';
			if ( ! $odd ) {
				$zebra_class = 'even ';
				$odd         = 2;
			}
			$out[] = '</td></tr>';
			$out[] = '<tr class="tm-epo-checkout-row '
					. $zebra_class
					. esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) )
					. '">';

			$epo_name  = '';
			$epo_value = '';
			if ( ! empty( $value['name'] ) ) {
				$epo_name = $value['tc_simple_value'];
			}
			if ( ! empty( $value['value'] ) ) {
				$epo_value = $value['tc_simple_value'];
			}

			$qty = ' <strong class="tm-epo-checkout-quantity">' . apply_filters( 'wc_tm_epo_ac_qty', sprintf( '&times; %s', $value['tm_quantity'] * $cart_item['quantity'] ), $cart_item_key, $cart_item, $value, $_product, $product_id ) . '</strong>';

			$name = '';
			if ( ! empty( $value['value'] ) && empty( $value['name'] ) ) {
				$name = '<div class="tc-epo-checkout-option-value tc-epo-checkout-no-label">' . $value['tc_simple_value'] . $qty . '</div>';
			} elseif ( empty( $value['value'] ) && ! empty( $value['name'] ) ) {
				$name = '<div class="tm-epo-checkout-option-label tc-epo-checkout-no-value">' . $value['tm_label'] . $qty . '</div>';
			} elseif ( ! empty( $value['value'] ) && ! empty( $value['name'] ) ) {
				$name = '<div class="tm-epo-checkout-option-label">' . $value['tm_label'] . $qty . '</div><div class="tm-epo-checkout-option-value">' . $value['tc_simple_value'] . '</div>';
			}

			$out[] = '<td class="tm-epo-checkout-name">' . $name . '</td>';
			$out[] = '<td class="tm-epo-checkout-subtotal">' . $value['tm_total_price'];

			$odd --;
		}

		return implode( '', $out );

	}

	/**
	 * Custom actions running for advanced template system
	 *
	 * @param string  $subtotal The row subtotal.
	 * @param string  $cart_item_key The cart item key.
	 * @param object  $cart_item The cart item object.
	 * @param object  $_product The product object.
	 * @param integer $product_id The product id.
	 * @since 1.0
	 */
	public function cart_add_option_rows( $subtotal = '', $cart_item_key = '', $cart_item = '', $_product = '', $product_id = 0 ) {

		$out        = [];
		$other_data = [];
		if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_in_cart ) {
			$other_data = $this->get_item_data_array( [], $cart_item );
		}
		$odd = 1;
		foreach ( $other_data as $key => $value ) {
			$zebra_class = 'odd ';
			if ( ! $odd ) {
				$zebra_class = 'even ';
				$odd         = 2;
			}

			$out[]     = '</td></tr>';
			$out[]     = '<tr class="tm-epo-cart-row ' . $zebra_class . esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ) . '">';
			$out[]     = '<td class="product-remove">&nbsp;</td>';
			$thumbnail = '&nbsp;';

			$out[]     = '<td class="product-thumbnail">' . $thumbnail . '</td>';
			$epo_name  = '';
			$epo_value = '';
			if ( ! empty( $value['name'] ) ) {
				$epo_name = $value['tc_simple_value'];
			}
			if ( ! empty( $value['value'] ) ) {
				$epo_value = $value['tc_simple_value'];
			}

			$name = '';
			if ( ! empty( $value['value'] ) && empty( $value['name'] ) ) {
				$name = '<div class="tm-epo-cart-option-value tc-epo-cart-no-label">' . $value['tc_simple_value'] . '</div>';
			} elseif ( empty( $value['value'] ) && ! empty( $value['name'] ) ) {
				$name = '<div class="tm-epo-cart-option-label tc-epo-cart-no-value">' . $value['tm_label'] . '</div><div class="tc-epo-cart-option-value">' . $value['tc_simple_value'] . '</div>';
			} elseif ( ! empty( $value['value'] ) && ! empty( $value['name'] ) ) {
				$name = '<div class="tm-epo-cart-option-label">' . $value['tm_label'] . '</div><div class="tc-epo-cart-option-value">' . $value['tc_simple_value'] . '</div>';
			}
			$item_quantity = apply_filters( 'wc_tm_epo_ac_qty', $value['tm_quantity'] * $cart_item['quantity'], $cart_item_key, $cart_item, $value, $_product, $product_id );
			$item_subtotal = $value['tm_total_price'];

			$name .= '<div class="tm-epo-cart-option-mobile">';
			$name .= '<div class="mobile-product-price">' . $value['tm_price'];
			if ( $item_quantity > 1 ) {
				$name .= '<span class="mobile-product-quantity"><small> &times; ' . $item_quantity . '</small></span>';
			}
			$name .= '</div>';
			if ( $item_quantity > 1 ) {
				$name .= '<div class="mobile-product-subtotal">' . $item_subtotal . '</div>';
			}
			$name .= '</div>';
			$out[] = '<td class="product-name">' . $name . '</td>';
			$out[] = '<td class="product-price">' . $value['tm_price'] . '</td>';
			$out[] = '<td class="product-quantity">' . $item_quantity . '</td>';
			$out[] = '<td class="product-subtotal">' . $item_subtotal;

			$odd --;
		}
		if ( is_array( $other_data ) && count( $other_data ) > 0 ) {
			$out[] = '<tr class="tm-epo-cart-row tc-epo-cart-row-total ' . esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ) . '">';
			$out[] = '<td class="product-remove">&nbsp;</td>';
			$out[] = '<td class="product-thumbnail">&nbsp;</td>';
			$out[] = '<td class="product-name">&nbsp;</td>';
			$out[] = '<td class="product-price">&nbsp;</td>';

			$out[] = '<td class="product-quantity">' . ( null !== $this->saved_product_quantity ? $this->saved_product_quantity : '' ) . '</td>';
			$out[] = '<td class="product-subtotal">' . $subtotal;
		}

		return implode( '', $out );

	}

	/**
	 * Adds edit link on product title in cart
	 *
	 * @param string $title The title html.
	 * @param array  $cart_item The item data.
	 * @param string $cart_item_key The cart item key.
	 * @since 1.0
	 */
	public function woocommerce_cart_item_name( $title = '', $cart_item = [], $cart_item_key = '' ) {

		if ( ! THEMECOMPLETE_EPO()->wc_vars['is_cart'] && ( defined( 'WOOCOMMERCE_CHECKOUT' ) || THEMECOMPLETE_EPO()->wc_vars['is_checkout'] ) && false === $this->added_woocommerce_checkout_cart_item_quantity ) {
			add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'woocommerce_cart_item_name' ], 10, 3 );
			$this->added_woocommerce_checkout_cart_item_quantity = 1;
			return $title;
		}

		$this->added_woocommerce_checkout_cart_item_quantity = false;

		if ( apply_filters( 'wc_epo_no_edit_options', false, $title, $cart_item, $cart_item_key ) ) {
			return $title;
		}
		if ( ! isset( $cart_item['data'] ) || ! isset( $cart_item['tmhasepo'] ) || isset( $cart_item['associated_key'] ) ) {
			return $title;
		}
		if ( apply_filters( 'wc_epo_override_edit_options', true, $title, $cart_item, $cart_item_key ) ) {
			if ( ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) && ! ( THEMECOMPLETE_EPO()->wc_vars['is_cart'] || THEMECOMPLETE_EPO()->wc_vars['is_checkout'] ) ) || isset( $cart_item['composite_item'] ) || isset( $cart_item['composite_data'] ) ) {
				return $title;
			}
			// Chained products cannot be edited.
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ) {
				return $title;
			}
			// Cannot function with TLS eDocBuidler.
			if ( isset( $cart_item['eDocBuilderID'] ) ) {
				return $title;
			}
		}
		$product = $cart_item['data'];

		$link = apply_filters( 'wc_epo_edit_options_get_permalink', $product->get_permalink( $cart_item ), $product, $title, $cart_item, $cart_item_key );
		$link = add_query_arg(
			[
				THEMECOMPLETE_EPO()->cart_edit_key_var => $cart_item_key,
				'cart_item_key'                        => $cart_item_key,
			],
			$link
		);
		// wp_nonce_url escapes the url.
		$link   = wp_nonce_url( $link, 'tm-edit' );
		$title .= '<a href="' . esc_url( $link ) . '" class="tm-cart-edit-options">' . ( ( ! empty( THEMECOMPLETE_EPO()->tm_epo_edit_options_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_edit_options_text ) : esc_html__( 'Edit options', 'woocommerce-tm-extra-product-options' ) ) . '</a>';

		return apply_filters( 'wc_epo_edit_options_link', $title, $cart_item, $cart_item_key );

	}

	/**
	 * Alters add to cart text when editing a product
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_add_to_cart_button() {

		if ( THEMECOMPLETE_EPO()->is_edit_mode() ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'woocommerce_product_single_add_to_cart_text' ], 9999 );
			echo '<input type="hidden" name="' . esc_attr( THEMECOMPLETE_EPO()->cart_edit_key_var_alt ) . '" value="' . esc_attr( THEMECOMPLETE_EPO()->cart_edit_key ) . '">';
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
	 * @param string  $cart_item_key The cart item key.
	 * @param integer $product_id The product id.
	 * @param integer $quantity The product quantity.
	 * @param integer $variation_id The variation id.
	 * @param array   $variation Attribute values.
	 * @param array   $cart_item_data The cart item meta data.
	 * @return string|void
	 */
	public function edit_woocommerce_add_to_cart( $cart_item_key = '', $product_id = 0, $quantity = 0, $variation_id = 0, $variation = [], $cart_item_data = [] ) {

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
	 * @param string $url The url to redirect to.
	 * @return string|void
	 */
	public function woocommerce_add_to_cart_redirect( $url = '' ) {

		if ( empty( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
	 * @param boolean $passed The currenct passed status.
	 * @param integer $product_id The product id.
	 * @param integer $qty The product quantity.
	 * @param integer $variation_id The variation id..
	 * @param array   $variations Attribute values.
	 * @param array   $cart_item_data The cart item meta data.
	 * @return string|void
	 */
	public function remove_previous_product_from_cart( $passed, $product_id, $qty, $variation_id = 0, $variations = [], $cart_item_data = [] ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			if ( isset( $this->new_add_to_cart_key ) ) {
				if ( $this->new_add_to_cart_key === $cart_item_key && isset( $_REQUEST['quantity'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					WC()->cart->set_quantity( $this->new_add_to_cart_key, sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}
			}
		}

		return $passed;

	}

	/**
	 * Alter add to cart message
	 *
	 * @param string    $message The message to return.
	 * @param int|array $products Product ID list or single product ID.
	 * @return string|void
	 */
	public function wc_add_to_cart_message_html( $message = '', $products = [] ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key && isset( $this->new_add_to_cart_key ) ) {
			$titles = [];
			$count  = 0;
			foreach ( $products as $product_id => $qty ) {
				/* translators: %s: product name */
				$titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . sprintf( esc_html_x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), wp_strip_all_tags( get_the_title( $product_id ) ) );
				$count   += $qty;
			}
			$titles = array_filter( $titles );
			/* translators: %s: product name */
			$added_text = sprintf( esc_html( _n( '%s has been updated.', '%s have been updated.', $count, 'woocommerce-tm-extra-product-options' ) ), wc_format_list_of_items( $titles ) );

			$message = sprintf(
				'<a href="%s" class="button wc-forward">%s</a> %s',
				esc_url( wc_get_page_permalink( 'cart' ) ),
				esc_html__( 'View cart', 'woocommerce' ),
				esc_html( $added_text )
			);
		}

		return $message;

	}

	/**
	 * Change quantity value when editing a cart item
	 *
	 * @return string|void
	 */
	public function tm_woocommerce_before_add_to_cart_form() {
		add_filter( 'woocommerce_quantity_input_args', [ $this, 'tm_woocommerce_quantity_input_args' ], 9999, 1 );
	}

	/**
	 * Remove filter for change quantity value when editing a cart item
	 *
	 * @return string|void
	 */
	public function tm_woocommerce_after_add_to_cart_form() {
		remove_filter( 'woocommerce_quantity_input_args', [ $this, 'tm_woocommerce_quantity_input_args' ], 9999 );
	}

	/**
	 * Change quantity value when editing a cart item
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public function tm_woocommerce_quantity_input_args( $args = '' ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( isset( $cart_item['quantity'] ) ) {
				$args['input_value'] = $cart_item['quantity'];
			}
		}

		return $args;

	}

	/**
	 * Advanced template product price fix for override price
	 *
	 * @param float   $price The product price.
	 * @param string  $cart_item_key Cart item key.
	 * @param object  $cart_item The cart item object.
	 * @param object  $_product The product object.
	 * @param integer $product_id The product id.
	 * @return string|void
	 */
	public function wc_tm_epo_ac_product_price( $price, $cart_item_key, $cart_item, $_product, $product_id ) {
		$flag = false;
		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price ) {
			$flag = true;
		} elseif ( '' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price ) {
			$tm_meta_cpf = themecomplete_get_post_meta( $product_id, 'tm_meta_cpf', true );
			if ( ! is_array( $tm_meta_cpf ) ) {
				$tm_meta_cpf = [];
			}

			if ( ! empty( $tm_meta_cpf['price_override'] ) ) {
				$flag = true;
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
	 * @param mixed   $value The value to return.
	 * @param integer $id ID of the object metadata is for.
	 * @param string  $key Metadata key.
	 *
	 * @return bool
	 */
	public function turn_off_persi_cart( $value, $id, $key ) {
		if ( '_woocommerce_persistent_cart' === $key ) {
			return false;
		}

		return $value;
	}

}
