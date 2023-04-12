<?php
/**
 * Extra Product Options Order Functionality
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Order Functionality
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Order {

	/**
	 * If we are in the WooCommerce admin order page
	 *
	 * @var boolean
	 */
	private $is_in_woocommerce_admin_order_page = false;

	/**
	 * If WooCommerce is about to send an email
	 *
	 * @var boolean
	 */
	private $is_about_to_sent_email = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Order|null
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

		// Gets the stored cart data for the order again functionality.
		add_filter( 'woocommerce_order_again_cart_item_data', [ $this, 'woocommerce_order_again_cart_item_data' ], 50, 3 );

		// Alter the product thumbnail in order.
		add_filter( 'woocommerce_order_item_thumbnail', [ $this, 'woocommerce_order_item_thumbnail' ], 50, 2 );

		// Alter the product thumbnail in order.
		add_filter( 'woocommerce_admin_order_item_thumbnail', [ $this, 'woocommerce_admin_order_item_thumbnail' ], 50, 3 );

		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_legacy_meta_data ) {
			// Adds options to the array of items/products of an order.
			add_filter( 'woocommerce_order_get_items', [ $this, 'woocommerce_order_get_items' ], 10, 2 );
			add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'woocommerce_order_item_get_formatted_meta_data' ], 10, 2 );

		} else {
			// Adds options to the array of items/products of an order.
			add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'woocommerce_order_item_get_formatted_meta_data' ], 10, 2 );
			add_action( 'woocommerce_order_item_meta_start', [ $this, 'woocommerce_order_item_meta_start' ] );
		}

		// Add meta to order.
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'woocommerce_checkout_create_order_line_item' ], 50, 3 );

		// WC 2.7x only Flag admin order page.
		add_filter( 'woocommerce_admin_order_item_types', [ $this, 'woocommerce_admin_order_item_types' ], 10, 2 );
		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'woocommerce_admin_order_data_after_order_details' ], 2 );

		// Helper to include options in the order items - used for payment gateways.
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'woocommerce_checkout_order_processed' ] );

		// Hides uploaded file path in order.
		add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'woocommerce_order_item_display_meta_value' ], 10, 1 );

		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_disable_options_on_order_status ) {
			$email_actions = apply_filters(
				'woocommerce_email_actions',
				[
					'woocommerce_order_status_pending_to_processing',
					'woocommerce_order_status_pending_to_completed',
					'woocommerce_order_status_processing_to_cancelled',
					'woocommerce_order_status_pending_to_failed',
					'woocommerce_order_status_pending_to_on-hold',
					'woocommerce_order_status_failed_to_processing',
					'woocommerce_order_status_failed_to_completed',
					'woocommerce_order_status_failed_to_on-hold',
					'woocommerce_order_status_on-hold_to_processing',
					'woocommerce_order_status_on-hold_to_cancelled',
					'woocommerce_order_status_on-hold_to_failed',
					'woocommerce_order_status_completed',
					'woocommerce_order_fully_refunded',
					'woocommerce_order_partially_refunded',
				]
			);

			foreach ( $email_actions as $action ) {
				add_action( $action, [ $this, 'change_is_about_to_sent_email' ] );
			}
		}

		// Attach upload files to emails.
		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_attach_uploaded_to_emails ) {
			add_filter( 'woocommerce_email_attachments', [ $this, 'woocommerce_email_attachments' ], 10, 3 );
		}

		add_action( 'woocommerce_checkout_order_review', [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ], 99999 );
		add_action( 'woocommerce_order_details_after_order_table', [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ], 99999 );

	}

	/**
	 * Gets the stored cart data for the order again functionality
	 *
	 * @param array  $cart_item_meta The cart item meta.
	 * @param array  $item The order item.
	 * @param object $order The order object.
	 * @since 1.0
	 */
	public function woocommerce_order_again_cart_item_data( $cart_item_meta, $item, $order ) {

		global $woocommerce;

		// Disable validation.
		remove_filter( 'woocommerce_add_to_cart_validation', [ THEMECOMPLETE_EPO_CART(), 'woocommerce_add_to_cart_validation' ], 50 );

		if ( apply_filters( 'wc_epo_no_order_again_cart_item_data', false ) ) {
			return $cart_item_meta;
		}

		$_backup_cart = isset( $item['item_meta']['tmcartepo_data'] ) ? $item['item_meta']['tmcartepo_data'] : false;
		if ( ! $_backup_cart ) {
			$_backup_cart = isset( $item['item_meta']['_tmcartepo_data'] ) ? $item['item_meta']['_tmcartepo_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			if ( is_string( $_backup_cart[0] ) ) {
				$_backup_cart = themecomplete_maybe_unserialize( $_backup_cart[0] );
			}
			$cart_item_meta['tmcartepo'] = $_backup_cart;
		}

		$_backup_cart = isset( $item['item_meta']['tmcartfee_data'] ) ? $item['item_meta']['tmcartfee_data'] : false;
		if ( ! $_backup_cart ) {
			$_backup_cart = isset( $item['item_meta']['_tmcartfee_data'] ) ? $item['item_meta']['_tmcartfee_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			if ( is_string( $_backup_cart[0] ) ) {
				$_backup_cart = themecomplete_maybe_unserialize( $_backup_cart[0] );
			}
			$cart_item_meta['tmcartfee'] = $_backup_cart[0];
		}

		$_backup_cart = isset( $item['item_meta']['tmdata'] ) ? $item['item_meta']['tmdata'] : false;
		if ( ! $_backup_cart ) {
			$_backup_cart = isset( $item['item_meta']['_tmdata'] ) ? $item['item_meta']['_tmdata'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			if ( is_string( $_backup_cart[0] ) ) {
				$_backup_cart = themecomplete_maybe_unserialize( $_backup_cart[0] );
			}
			$cart_item_meta['tmdata'] = $_backup_cart[0];
		}

		$_backup_cart = isset( $item['item_meta']['tmpost_data'] ) ? $item['item_meta']['tmpost_data'] : false;
		if ( ! $_backup_cart ) {
			$_backup_cart = isset( $item['item_meta']['_tmpost_data'] ) ? $item['item_meta']['_tmpost_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			if ( is_string( $_backup_cart[0] ) ) {
				$_backup_cart = themecomplete_maybe_unserialize( $_backup_cart[0] );
			}
			$cart_item_meta['tmpost_data'] = $_backup_cart[0];
		}

		if ( ! isset( $cart_item_meta['tmpost_data'] ) && isset( $cart_item_meta['tmdata']['tmcp_post_fields'] ) ) {
			$cart_item_meta['tmpost_data'] = $cart_item_meta['tmdata']['tmcp_post_fields'];
		}

		$cart_item_meta = apply_filters( 'wc_epo_woocommerce_order_again_cart_item_data', $cart_item_meta, $item );

		if ( apply_filters( 'wc_epo_woocommerce_order_again_cart_item_data_has_epo', isset( $cart_item_meta['tmcartepo'] ) || isset( $cart_item_meta['tmcartfee'] ), $cart_item_meta ) ) {

			$product_id = isset( $item['product_id'] ) ? $item['product_id'] : $item->get_product_id();

			$terms        = get_the_terms( $product_id, 'product_type' );
			$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

			if ( in_array( $product_type, apply_filters( 'wc_epo_can_be_edited_product_type', [ 'simple', 'variable' ] ), true ) ) {
				$cart_item_meta['tmhasepo'] = 1;
			}

			$price_override = ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price )
				? 0
				: ( ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price )
					? 1
					: ( ! empty( THEMECOMPLETE_EPO()->tm_meta_cpf['price_override'] ) ? 1 : 0 ) );

			if ( ! empty( $price_override ) ) {
				$cart_item_meta['epo_price_override'] = 1;
			}
		}

		return $cart_item_meta;

	}

	/**
	 * Return image thumbnail
	 *
	 * @param string $image The image url.
	 * @param array  $item_meta The order item meta data.
	 * @since 6.0
	 */
	private function get_order_item_thumbnail( $image = '', $item_meta = [] ) {

		$_image = [];
		$_alt   = [];

		$has_epo     = is_array( $item_meta ) && isset( $item_meta['_tmcartepo_data'] ) && isset( $item_meta['_tmcartepo_data'][0] );
		$has_epo_fee = isset( $item_meta ) && isset( $item_meta['_tmcartfee_data'] ) && isset( $item_meta['_tmcartfee_data'][0] );

		if ( $has_epo ) {
			$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartepo_data'][0] );
			if ( ! is_array( $epos ) ) {
				return $image;
			}

			if ( $epos ) {
				foreach ( $epos as $key => $value ) {
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

		if ( 0 === count( $_image ) ) {
			if ( $has_epo_fee ) {
				$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartfee_data'][0] );
				if ( ! is_array( $epos ) ) {
					return $image;
				}

				if ( $epos ) {
					foreach ( $epos as $key => $value ) {
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
		}

		if ( count( $_image ) > 0 ) {
			$current = 0;
			for ( $i = count( $_image ); $i > 0; $i -- ) {
				if ( ! empty( $_image[ $i ] ) ) {
					$current = $i;
				}
			}
			$size       = 'shop_thumbnail';
			$dimensions = wc_get_image_size( $size );
			$image      = apply_filters(
				'tm_woocommerce_img',
				'<img src="' . apply_filters( 'tm_woocommerce_img_src', $_image[ $current ] )
				. '" alt="'
				. esc_attr( wp_strip_all_tags( $_alt[ $current ] ) )
				. '" width="' . esc_attr( $dimensions['width'] )
				. '" class="woocommerce-placeholder wp-post-image" height="'
				. esc_attr( $dimensions['height'] )
				. '" />',
				$size,
				$dimensions
			);
		}

		return $image;

	}

	/**
	 * Alter the product thumbnail in order email
	 *
	 * @param string $image The image url.
	 * @param object $item The order item object.
	 * @since 6.0
	 */
	public function woocommerce_order_item_thumbnail( $image = '', $item = '' ) {

		$order     = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
		$item_id   = $item->get_id();
		$item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );

		$image = $this->get_order_item_thumbnail( $image, $item_meta );

		return $image;

	}

	/**
	 * Alter the product thumbnail in order
	 *
	 * @param string  $image The image url.
	 * @param integer $item_id The item id.
	 * @param object  $item The order item object.
	 * @since 1.0
	 */
	public function woocommerce_admin_order_item_thumbnail( $image = '', $item_id = 0, $item = '' ) {

		$order     = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
		$item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );

		$image = $this->get_order_item_thumbnail( $image, $item_meta );

		return $image;

	}

	/**
	 * Adds meta data to the order - WC >= 2.7 (crud)
	 *
	 * @param object $item The order item object.
	 * @param string $cart_item_key The cart item key.
	 * @param array  $values Array of values.
	 * @since 1.0
	 */
	public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values ) {

		do_action( 'wc_epo_order_item_meta_before', $item, $cart_item_key, $values );
		if ( ! empty( $values['tmcartepo'] ) ) {
			$item->add_meta_data( '_tmcartepo_data', $values['tmcartepo'] );
			$item->add_meta_data( '_tm_epo_product_original_price', [ $values['tm_epo_product_original_price'] ] );
			$item->add_meta_data( '_tm_epo_options_prices', [ $values['tm_epo_options_prices'] ] );
			$item->add_meta_data( '_tm_epo', [ 1 ] );
		}
		if ( ! empty( $values['tmcartfee'] ) ) {
			$item->add_meta_data( '_tmcartfee_data', [ $values['tmcartfee'] ] );
		}
		if ( ! empty( $values['tmdata'] ) ) {
			$item->add_meta_data( '_tmdata', [ $values['tmdata'] ] );
		}
		if ( ! empty( $values['tmpost_data'] ) ) {
			$item->add_meta_data( '_tmpost_data', [ $values['tmpost_data'] ] );
		}
		do_action( 'wc_epo_order_item_meta', $item, $cart_item_key, $values );

	}

	/**
	 * Check if an attribute is included in the attributes area of a variation name
	 *
	 * @since 1.0
	 */
	public function woocommerce_is_attribute_in_product_name() {
		return false;
	}

	/**
	 * Disable custom woocommerce_is_attribute_in_product_name filter
	 *
	 * @since 1.0
	 */
	public function woocommerce_order_items_table() {

		remove_filter( 'woocommerce_is_attribute_in_product_name', [ $this, 'woocommerce_is_attribute_in_product_name' ], 10 );

	}

	/**
	 * Adds options to the array of items/products of an order
	 *
	 * @since 5.0.12.11
	 */
	public function woocommerce_order_item_meta_start() {
		add_filter( 'wc_epo_admin_in_shop_order', '__return_false' );
	}

	/**
	 * Adds options to the array of items/products of an order
	 *
	 * @param array  $formatted_meta The formatted meta data.
	 * @param object $item The order item object.
	 * @since 4.9.12
	 */
	public function woocommerce_order_item_get_formatted_meta_data( $formatted_meta = [], $item = false ) {

		if ( apply_filters( 'wc_epo_no_order_get_items', false ) ||
			( 'yes' === THEMECOMPLETE_EPO()->tm_epo_disable_sending_options_in_order && defined( 'WOOCOMMERCE_CHECKOUT' ) && ! $this->is_about_to_sent_email && ! defined( 'TM_CHECKOUT_ORDER_PROCESSED' ) ) ||
			( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_prevent_options_from_emails ) ||
			( isset( $_POST['action'] ) && 'woocommerce_calc_line_taxes' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_add_order_item' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_add_coupon_discount' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_save_order_items' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_add_order_fee' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_remove_order_item' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_remove_order_coupon' === $_POST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			return $formatted_meta;
		}

		global $woocommerce;

		$item_id = $item->get_id();
		$order   = $item->get_order();
		if ( ! $order || THEMECOMPLETE_EPO_ADMIN()->in_shop_order() ) {
			return $formatted_meta;
		}

		$order_currency = is_callable( [ $order, 'get_currency' ] ) ? $order->get_currency() : $order->get_order_currency();
		$currency_arg   = [ 'currency' => $order_currency ];
		$mt_prefix      = $order_currency;

		$return_items = [];

		$item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );

		$has_epo = is_array( $item_meta ) && isset( $item_meta['_tmcartepo_data'] ) && isset( $item_meta['_tmcartepo_data'][0] );

		if ( $has_epo ) {
			$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartepo_data'][0] );
			if ( ! is_array( $epos ) ) {
				return $formatted_meta;
			}
			$current_product_id  = $item['product_id'];
			$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $current_product_id, 'product' ) );
			if ( THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() && absint( $original_product_id ) !== absint( $current_product_id ) ) {
				$current_product_id = $original_product_id;
			}
			$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );
			$_unique_elements_added = [];
			$_items_to_add          = [];
			foreach ( $epos as $key => $epo ) {
				if ( $epo && is_array( $epo ) && isset( $epo['section'] ) ) {
					if ( ! isset( $epo['quantity'] ) ) {
						$epo['quantity'] = 1;
					}
					if ( $epo['quantity'] < 1 ) {
						$epo['quantity'] = 1;
					}
					if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
						$epo['name'] = $wpml_translation_by_id[ $epo['section'] ];
					}
					if ( ! empty( $epo['multiple'] ) && ! empty( $epo['key'] ) ) {
						$pos = strrpos( $epo['key'], '_' );
						if ( false !== $pos ) {
							if ( isset( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) && is_array( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) ) {
								$av = array_values( $wpml_translation_by_id[ 'options_' . $epo['section'] ] );
								if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {
									$epo['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];
								}
							}
						}
					}
					$original_value = $epo['value'];

					$override     = isset( $epo['element'] ) && isset( $epo['element']['type'] ) && 'upload' === $epo['element']['type'];
					$epo['value'] = $this->display_meta_value( $epo['value'], $override, 'order' );
					$epo['value'] = apply_filters( 'wc_epo_enable_shortocde', $epo['value'], $epo['value'], false );

					$new_currency = false;
					if ( isset( $epo['price_per_currency'] ) ) {
						$_current_currency_prices = $epo['price_per_currency'];
						if ( '' !== $mt_prefix
							&& '' !== $_current_currency_prices
							&& is_array( $_current_currency_prices )
							&& isset( $_current_currency_prices[ $mt_prefix ] )
							&& '' !== $_current_currency_prices[ $mt_prefix ]
						) {

							$new_currency = true;
							$epo['price'] = $_current_currency_prices[ $mt_prefix ];

						}
					}
					if ( ! $new_currency ) {
						$epo['price'] = apply_filters( 'wc_epo_get_current_currency_price', $epo['price'], '', null, $order_currency );
					}

					if ( ! empty( $epo['multiple_values'] ) ) {
						$display_value_array = explode( $epo['multiple_values'], $epo['value'] );
						$display_value       = '';
						foreach ( $display_value_array as $d => $dv ) {
							$display_value .= '<span class="cpf-data-on-cart">' . $dv . '</span>';
						}
						$epo['value'] = $display_value;
					}

					$epovalue = '';
					if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart && ! empty( $epo['price'] ) ) {

						$product = wc_get_product( $current_product_id );

						$price      = (float) $epo['price'] / (float) $epo['quantity'];
						$tax_string = '';

						// This check is need in case the product is deleted.
						if ( $product ) {

							$cart             = $woocommerce->cart;
							$taxable          = $product->is_taxable();
							$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

							// Taxable.
							if ( $taxable ) {

								if ( 'excl' === $tax_display_cart ) {

									if ( themecomplete_order_get_att( $order, 'cart_tax' ) > 0 && wc_prices_include_tax() ) {
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

									if ( themecomplete_order_get_att( $order, 'cart_tax' ) > 0 && ! wc_prices_include_tax() ) {
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
						}

						$epovalue .= ' ' . ( ( apply_filters( 'epo_can_show_order_price', true, $item_meta ) ) ? ( wc_price( $price, $currency_arg ) . $tax_string ) : '' );
					}
					if ( $epo['quantity'] > 1 ) {
						$epovalue .= ' &times; ' . $epo['quantity'];
					}

					$epovalue = apply_filters( 'wc_epo_value_in_order', $epovalue, $epo['price'], $epo, $item, $item_id, $order );

					if ( '' !== $epovalue && ! is_array( $epo['value'] ) && ( ( ! empty( $epo['hidevalueinorder'] ) && 'price' === $epo['hidevalueinorder'] ) || empty( $epo['hidevalueinorder'] ) ) ) {
						$epo['value'] .= ' <small>' . $epovalue . '</small>';
					}

					if ( is_array( $epo['value'] ) ) {
						$epo['value'] = array_map( [ THEMECOMPLETE_EPO_HELPER(), 'html_entity_decode' ], $epo['value'] );
					} else {
						$epo['value'] = THEMECOMPLETE_EPO_HELPER()->entity_decode( $epo['value'] );
					}

					if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_strip_html_from_emails ) {
						$epo['value'] = wp_strip_all_tags( $epo['value'] );
					} else {
						if ( ! empty( $epo['images'] ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_image_replacement ) {
							$display_value = '<span class="cpf-img-on-cart"><img alt="'
											. esc_attr( wp_strip_all_tags( $epo['name'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'
											. apply_filters( 'tm_image_url', $epo['images'] )
											. '" /></span>';
							$epo['value']  = $display_value . $epo['value'];
						} elseif ( ! empty( $epo['color'] ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_image_replacement ) {
							$display_value = '<span class="cpf-colors-on-cart"><span class="cpf-color-on-cart backgroundcolor'
											. esc_attr( sanitize_hex_color_no_hash( $epo['color'] ) ) . '"></span> '
											. '</span>';
							$epo['value']  = $display_value . $epo['value'];
							THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( '.backgroundcolor' . esc_attr( sanitize_hex_color_no_hash( $epo['color'] ) ) . '{background-color:' . esc_attr( sanitize_hex_color( $epo['color'] ) ) . ';}' );
						}

						if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_show_hide_uploaded_file_url_order && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_upload_image_replacement && isset( $epo['element'] ) && isset( $epo['element']['type'] ) && 'upload' === $epo['element']['type'] ) {
							add_filter( 'upload_mimes', [ THEMECOMPLETE_EPO(), 'upload_mimes_trick' ] );
							$check = wp_check_filetype( $original_value );
							remove_filter( 'upload_mimes', [ THEMECOMPLETE_EPO(), 'upload_mimes_trick' ] );
							if ( ! empty( $check['ext'] ) ) {
								$image_exts = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png' ];
								if ( in_array( $check['ext'], $image_exts, true ) ) {
									$display_value  = '<span class="cpf-img-on-order">';
									$display_value .= '<img alt="' . esc_attr( wp_strip_all_tags( $epo['name'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="' . apply_filters( 'tm_image_url', $original_value ) . '" />';
									$avalue         = $epo['value'];
									$display_value .= '<span class="cpf-data-on-order"><a download href="' . esc_url( $original_value ) . '">' . $avalue . '</a></span>';
									$display_value .= '</span>';
									$epo['value']   = $display_value;
								}
							}
						}
					}

					if ( isset( $epo['element'] ) && 'textarea' === $epo['element']['type'] ) {
						$epo_value = trim( $epo['value'] );

						$epo_value = str_replace( [ "\r\n", "\r" ], "\n", $epo_value );

						$epo_value = preg_replace( "/\n\n+/", "\n\n", $epo_value );

						$epo_value = array_map( 'wc_clean', explode( "\n", $epo_value ) );

						$epo_value = implode( "\r\n", $epo_value );

						$epo['value'] = $epo_value;
					}
					if ( empty( $epo['hidelabelinorder'] ) || 'noprice' === $epo['hidevalueinorder'] || empty( $epo['hidevalueinorder'] ) ) {
						$_label = empty( $epo['hidelabelinorder'] ) ? $epo['name'] : '';

						$_value = $epo['value'];

						if ( isset( $epo['hidevalueinorder'] ) ) {
							switch ( $epo['hidevalueinorder'] ) {
								case 'price':
									$_value = ( ( apply_filters( 'epo_can_show_order_price', true, $item_meta ) ) ? ( wc_price( (float) $epo['price'] / (float) $epo['quantity'], $currency_arg ) ) : '' );

									if ( $epo['quantity'] > 1 ) {
										$_value .= ' &times; ' . $epo['quantity'];
									}
									break;
								case 'hidden':
									$_value = '';
									break;
								case 'noprice':
									$_value = $epo['value'];
									break;
								default:
									$_value = $epo['value'];
									break;
							}
						}
						if ( isset( $_unique_elements_added[ $epo['section'] ] ) && isset( $_items_to_add[ $epo['section'] ] ) ) {
							$_ta                              = $_items_to_add[ $epo['section'] ];
							$_ta[ $_label ][]                 = $_value;
							$_items_to_add[ $epo['section'] ] = $_ta;
						} else {
							$_ta                              = [];
							$_ta[ $_label ]                   = [ $_value ];
							$_items_to_add[ $epo['section'] ] = $_ta;
						}
						$_unique_elements_added[ $epo['section'] ] = $epo['section'];
					}
				}
			}

			$current_meta_key = 99999;
			$added            = false;

			foreach ( $_items_to_add as $uniquid => $element ) {
				foreach ( $element as $key => $value ) {

					if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_unique_meta_values && is_array( $value ) ) {
						$value = implode( ', ', $value );
					}
					if ( '' === $value ) {
						$value = ' ';
					}
					$added = true;

					if ( is_array( $value ) ) {
						if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_always_unique_values ) {
							foreach ( $value as $currentvalue ) {
								$current_meta_key ++;
								if ( ! isset( $formatted_meta[ $current_meta_key ] ) ) {
									$formatted_meta[ $current_meta_key ] = (object) [
										'key'           => $current_meta_key,
										'value'         => $currentvalue,
										'display_key'   => $key,
										'display_value' => make_clickable( $currentvalue ),
									];
								}
							}
						} else {
							$current_meta_key ++;
							if ( ! isset( $formatted_meta[ $current_meta_key ] ) ) {
								$value                               = implode( THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text, $value );
								$formatted_meta[ $current_meta_key ] = (object) [
									'key'           => $key,
									'value'         => $value,
									'display_key'   => $key,
									'display_value' => make_clickable( $value ),
								];
							}
						}
					} else {
						$current_meta_key ++;
						if ( ! isset( $formatted_meta[ $current_meta_key ] ) ) {
							$formatted_meta[ $current_meta_key ] = (object) [
								'key'           => $key,
								'value'         => $value,
								'display_key'   => $key,
								'display_value' => make_clickable( $value ),
							];
						}
					}
				}
			}
		}

		return $formatted_meta;
	}

	/**
	 * Adds options to the array of items/products of an order
	 *
	 * @param array  $items The order items array.
	 * @param object $order The order object.
	 * @since 1.0
	 */
	public function woocommerce_order_get_items( $items = [], $order = false ) {

		if ( apply_filters( 'wc_epo_no_order_get_items', false ) ||
			! is_array( $items ) ||
			( 'yes' === THEMECOMPLETE_EPO()->tm_epo_disable_sending_options_in_order && defined( 'WOOCOMMERCE_CHECKOUT' ) && ! $this->is_about_to_sent_email && ! defined( 'TM_CHECKOUT_ORDER_PROCESSED' ) ) ||
			( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_prevent_options_from_emails ) ||
			( isset( $_POST['action'] ) && 'woocommerce_calc_line_taxes' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_add_order_item' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_add_coupon_discount' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_save_order_items' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_add_order_fee' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_remove_order_item' === $_POST['action'] ) || // phpcs:ignore WordPress.Security.NonceVerification
			( isset( $_POST['action'] ) && 'woocommerce_remove_order_coupon' === $_POST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			return $items;
		}

		add_filter( 'woocommerce_is_attribute_in_product_name', [ $this, 'woocommerce_is_attribute_in_product_name' ], 10 );

		add_action( 'woocommerce_order_details_after_order_table_items', [ $this, 'woocommerce_order_items_table' ], 10 );

		$order_currency = is_callable( [ $order, 'get_currency' ] ) ? $order->get_currency() : $order->get_order_currency();
		$currency_arg   = [ 'currency' => $order_currency ];
		$mt_prefix      = $order_currency;

		$return_items = [];

		global $woocommerce;

		foreach ( $items as $item_id => $item ) {

			$type = '';
			if ( is_array( $item ) ) {
				$type = $item['type'];
			} elseif ( is_object( $item ) && is_callable( [ $item, 'get_type' ] ) ) {
				$type = $item->get_type();
			}

			if ( ! in_array( $type, [ 'line_item', 'fee' ], true ) ) {
				continue;
			}

			if ( isset( $item->tc_added_meta ) && ! empty( $item->tc_added_meta ) ) {
				$return_items[ $item_id ] = $item;
				continue;
			}

			$item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );

			$has_epo = is_array( $item_meta ) && isset( $item_meta['_tmcartepo_data'] ) && isset( $item_meta['_tmcartepo_data'][0] );

			if ( $has_epo ) {
				$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartepo_data'][0] );
				if ( ! is_array( $epos ) ) {
					return $items;
				}
				$current_product_id  = $item['product_id'];
				$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $current_product_id, 'product' ) );
				if ( THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() && absint( $original_product_id ) !== absint( $current_product_id ) ) {
					$current_product_id = $original_product_id;
				}
				$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );
				$_unique_elements_added = [];
				$_items_to_add          = [];
				foreach ( $epos as $key => $epo ) {
					if ( $epo && is_array( $epo ) && isset( $epo['section'] ) ) {
						if ( ! isset( $epo['quantity'] ) ) {
							$epo['quantity'] = 1;
						}
						if ( $epo['quantity'] < 1 ) {
							$epo['quantity'] = 1;
						}
						if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
							$epo['name'] = $wpml_translation_by_id[ $epo['section'] ];
						}
						if ( ! empty( $epo['multiple'] ) && ! empty( $epo['key'] ) ) {
							$pos = strrpos( $epo['key'], '_' );
							if ( false !== $pos ) {
								if ( isset( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) && is_array( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) ) {
									$av = array_values( $wpml_translation_by_id[ 'options_' . $epo['section'] ] );
									if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {
										$epo['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];
									}
								}
							}
						}
						$original_value = apply_filters( 'wc_epo_enable_shortocde', $epo['value'], $epo['value'], false );

						$override     = isset( $epo['element'] ) && isset( $epo['element']['type'] ) && 'upload' === $epo['element']['type'];
						$epo['value'] = $this->display_meta_value( $epo['value'], $override, 'order' );
						$epo['value'] = apply_filters( 'wc_epo_enable_shortocde', $epo['value'], $epo['value'], false );

						$new_currency = false;
						if ( isset( $epo['price_per_currency'] ) ) {
							$_current_currency_prices = $epo['price_per_currency'];
							if ( '' !== $mt_prefix
								&& '' !== $_current_currency_prices
								&& is_array( $_current_currency_prices )
								&& isset( $_current_currency_prices[ $mt_prefix ] )
								&& '' !== $_current_currency_prices[ $mt_prefix ]
							) {

								$new_currency = true;
								$epo['price'] = $_current_currency_prices[ $mt_prefix ];

							}
						}
						if ( ! $new_currency ) {
							$epo['price'] = apply_filters( 'wc_epo_get_current_currency_price', $epo['price'], '', null, $order_currency );
						}

						if ( ! empty( $epo['multiple_values'] ) ) {
							$display_value_array = explode( $epo['multiple_values'], $epo['value'] );
							$display_value       = '';
							foreach ( $display_value_array as $d => $dv ) {
								$display_value .= '<span class="cpf-data-on-cart">' . $dv . '</span>';
							}
							$epo['value'] = $display_value;
						}

						$epovalue = '';
						if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_hide_options_prices_in_cart && ! empty( $epo['price'] ) ) {

							$product = wc_get_product( $current_product_id );

							$price      = (float) $epo['price'] / (float) $epo['quantity'];
							$tax_string = '';

							// This check is need in case the product is deleted.
							if ( $product ) {

								$cart             = $woocommerce->cart;
								$taxable          = $product->is_taxable();
								$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

								// Taxable.
								if ( $taxable ) {

									if ( 'excl' === $tax_display_cart ) {

										if ( themecomplete_order_get_att( $order, 'cart_tax' ) > 0 && wc_prices_include_tax() ) {
											$tax_string = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';
										}
										if ( (float) 0 !== floatval( $price ) ) {
											$price = themecomplete_get_price_excluding_tax(
												$product,
												[
													'qty' => 10000,
													'price' => $price,
												]
											) / 10000;
										}
									} else {

										if ( themecomplete_order_get_att( $order, 'cart_tax' ) > 0 && ! wc_prices_include_tax() ) {
											$tax_string = ' <small>' . apply_filters( 'inc_tax_or_vat', WC()->countries->inc_tax_or_vat() ) . '</small>';
										}
										if ( (float) 0 !== floatval( $price ) ) {
											$price = themecomplete_get_price_including_tax(
												$product,
												[
													'qty' => 10000,
													'price' => $price,
												]
											) / 10000;
										}
									}
								}
							}

							$epovalue .= ' ' . ( ( apply_filters( 'epo_can_show_order_price', true, $item_meta ) ) ? ( wc_price( $price, $currency_arg ) . $tax_string ) : '' );
						}
						if ( $epo['quantity'] > 1 ) {
							$epovalue .= ' &times; ' . $epo['quantity'];
						}

						$epovalue = apply_filters( 'wc_epo_value_in_order', $epovalue, $epo['price'], $epo, $item, $item_id, $order );

						if ( '' !== $epovalue && ! is_array( $epo['value'] ) && ( ( ! empty( $epo['hidevalueinorder'] ) && 'price' === $epo['hidevalueinorder'] ) || empty( $epo['hidevalueinorder'] ) ) ) {
							$epo['value'] .= ' <small>' . $epovalue . '</small>';
						}

						if ( is_array( $epo['value'] ) ) {
							$epo['value'] = array_map( [ THEMECOMPLETE_EPO_HELPER(), 'html_entity_decode' ], $epo['value'] );
						} else {
							$epo['value'] = THEMECOMPLETE_EPO_HELPER()->entity_decode( $epo['value'] );
						}

						if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_strip_html_from_emails ) {
							$epo['value'] = wp_strip_all_tags( $epo['value'] );
						} else {
							if ( ! empty( $epo['images'] ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_image_replacement ) {
								$display_value = '<span class="cpf-img-on-cart"><img alt="'
												. esc_attr( wp_strip_all_tags( $epo['name'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'
												. apply_filters( 'tm_image_url', $epo['images'] )
												. '" /></span>';
								$epo['value']  = $display_value . $epo['value'];
							} elseif ( ! empty( $epo['color'] ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_image_replacement ) {
								$display_value = '<span class="cpf-colors-on-cart"><span class="cpf-color-on-cart backgroundcolor'
												. esc_attr( sanitize_hex_color_no_hash( $epo['color'] ) ) . '"></span> '
												. '</span>';
								$epo['value']  = $display_value . $epo['value'];
								THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( '.backgroundcolor' . esc_attr( sanitize_hex_color_no_hash( $epo['color'] ) ) . '{background-color:' . esc_attr( sanitize_hex_color( $epo['color'] ) ) . ';}' );
							}

							if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_show_hide_uploaded_file_url_cart && 'yes' === THEMECOMPLETE_EPO()->tm_epo_show_upload_image_replacement && isset( $epo['element'] ) && isset( $epo['element']['type'] ) && 'upload' === $epo['element']['type'] ) {
								$check = wp_check_filetype( $epo['value'] );
								if ( ! empty( $check['ext'] ) ) {
									$image_exts = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png' ];
									if ( in_array( $check['ext'], $image_exts, true ) ) {
										$display_value = '<span class="cpf-img-on-cart"><img alt="' . esc_attr( wp_strip_all_tags( $epo['name'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="' . apply_filters( 'tm_image_url', $original_value ) . '" /><span class="cpf-data-on-cart"><a download href="' . esc_url( $original_value ) . '">' . $epo['value'] . '</a></span></span>';
										$epo['value']  = $display_value;
									}
								}
							}
						}

						if ( isset( $epo['element'] ) && 'textarea' === $epo['element']['type'] ) {
							$epo_value = trim( $epo['value'] );

							$epo_value = str_replace( [ "\r\n", "\r" ], "\n", $epo_value );

							$epo_value = preg_replace( "/\n\n+/", "\n\n", $epo_value );

							$epo_value = array_map( 'wc_clean', explode( "\n", $epo_value ) );

							$epo_value = implode( "\r\n", $epo_value );

							$epo['value'] = $epo_value;
						}
						if ( empty( $epo['hidelabelinorder'] ) || 'noprice' === $epo['hidevalueinorder'] || empty( $epo['hidevalueinorder'] ) ) {
							$_label = empty( $epo['hidelabelinorder'] ) ? $epo['name'] : '';

							$_value = $epo['value'];

							if ( isset( $epo['hidevalueinorder'] ) ) {
								switch ( $epo['hidevalueinorder'] ) {
									case 'price':
										$_value = ( ( apply_filters( 'epo_can_show_order_price', true, $item_meta ) ) ? ( wc_price( (float) $epo['price'] / (float) $epo['quantity'], $currency_arg ) ) : '' );

										if ( $epo['quantity'] > 1 ) {
											$_value .= ' &times; ' . $epo['quantity'];
										}
										break;
									case 'hidden':
										$_value = '';
										break;
									case 'noprice':
										$_value = $epo['value'];
										break;
									default:
										$_value = $epo['value'];
										break;
								}
							}
							if ( isset( $_unique_elements_added[ $epo['section'] ] ) && isset( $_items_to_add[ $epo['section'] ] ) ) {
								$_ta                              = $_items_to_add[ $epo['section'] ];
								$_ta[ $_label ][]                 = $_value;
								$_items_to_add[ $epo['section'] ] = $_ta;
							} else {
								$_ta                              = [];
								$_ta[ $_label ]                   = [ $_value ];
								$_items_to_add[ $epo['section'] ] = $_ta;
							}
							$_unique_elements_added[ $epo['section'] ] = $epo['section'];
						}
					}
				}

				$current_meta_key = 0;
				$added            = false;
				$current_meta     = [];
				if ( false === $this->is_in_woocommerce_admin_order_page && is_object( $item ) ) {
					$current_meta_key = 99999;

					$current_product = wc_get_product( $current_product_id );

					if ( themecomplete_get_product_type( $current_product ) !== 'variable' ) {

						foreach ( $item->get_meta_data() as $item_meta ) {
							if ( isset( $item_meta->key, $item_meta->value, $item_meta->id ) ) {
								$current_meta[] = [
									'key'   => $item_meta->key,
									'value' => $item_meta->value,
									'id'    => $item_meta->id,
								];
							}
						}

						$cloned_item           = clone $item;
						$cloned_item_meta_data = $cloned_item->get_meta_data();
						foreach ( $cloned_item_meta_data as $cloned_item_meta ) {
							$cloned_item->delete_meta_data( $cloned_item_meta->key );
						}
						$cloned_item->set_meta_data( $current_meta );
						$item = $cloned_item;

					}
				}

				foreach ( $_items_to_add as $uniquid => $element ) {
					foreach ( $element as $key => $value ) {

						if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_unique_meta_values && is_array( $value ) ) {
							$value = implode( ', ', $value );
						}
						if ( '' === $value ) {
							$value = ' ';
						}
						if ( is_array( $items[ $item_id ] ) ) {
							if ( is_array( $value ) ) {
								foreach ( $value as $currentvalue ) {
									$item['item_meta'][ $key ][]                                  = $currentvalue;
									$item['item_meta_array'][ count( $item['item_meta_array'] ) ] = (object) [
										'key'   => $key,
										'value' => $currentvalue,
									];
								}
							} else {
								$item['item_meta'][ $key ][]                                  = $value;
								$item['item_meta_array'][ count( $item['item_meta_array'] ) ] = (object) [
									'key'   => $key,
									'value' => $value,
								];
							}
						} elseif ( $current_meta_key > 0 && is_object( $items[ $item_id ] ) ) {

							$added = true;

							if ( is_array( $value ) ) {
								foreach ( $value as $currentvalue ) {
									$current_meta_key ++;
									if ( ! isset( $current_meta[ $current_meta_key ] ) ) {
										$current_meta[] = (object) [
											'id'          => $current_meta_key,
											'key'         => $key,
											'display_key' => $key,
											'value'       => $currentvalue,
										];
									}
								}
							} else {
								$current_meta_key ++;
								if ( ! isset( $current_meta[ $current_meta_key ] ) ) {
									$current_meta[] = (object) [
										'id'    => $current_meta_key,
										'key'   => $key,
										'value' => $value,
									];
								}
							}
						}
					}
				}

				if ( $current_meta_key > 0 && $added ) {
					$item->set_meta_data( $current_meta );
					$item->tc_added_meta = true;
				}
			}
			$return_items[ $item_id ] = $item;
		}

		if ( empty( $return_items ) ) {
			$return_items = $items;
		}

		return $return_items;

	}

	/**
	 * Flag WooCommerce admin order page
	 *
	 * @param array $type The type of line item.
	 * @since 1.0
	 */
	public function woocommerce_admin_order_item_types( $type ) {

		$this->is_in_woocommerce_admin_order_page = true;

		return $type;

	}

	/**
	 * Flag WooCommerce admin order page
	 *
	 * @since 1.0
	 */
	public function woocommerce_admin_order_data_after_order_details() {

		$this->is_in_woocommerce_admin_order_page = true;

	}

	/**
	 * Flag WooCommerce orde checkout
	 *
	 * @since 1.0
	 */
	public function woocommerce_checkout_order_processed() {

		define( 'TM_CHECKOUT_ORDER_PROCESSED', 1 );

	}

	/**
	 * For hiding uploaded file path
	 *
	 * @param string $value The meta value to display.
	 * @since 1.0
	 */
	public function woocommerce_order_item_display_meta_value( $value = '' ) {
		return $this->display_meta_value( $value, 0, 'order' );
	}

	/**
	 * Display meta value
	 * Mainly used for hiding uploaded file path
	 *
	 * @param string  $value The meta value to display.
	 * @param integer $override Override default behavior.
	 * @param string  $check The placement where the meta value is displayed.
	 * @since 1.0
	 */
	public function display_meta_value( $value = '', $override = 0, $check = 'cart' ) {

		$original_value = $value;

		$canbeallowed = true;
		if ( 'cart' === $check ) {
			$canbeallowed = 'no' === THEMECOMPLETE_EPO()->tm_epo_show_hide_uploaded_file_url_cart;
		}
		if ( 'order' === $check ) {
			$canbeallowed = 'no' === THEMECOMPLETE_EPO()->tm_epo_show_hide_uploaded_file_url_order;
		}
		if ( 'always' === $check ) {
			$canbeallowed = true;
		}

		if ( is_array( $value ) ) {
			$new_value = [];
			foreach ( $value as $k => $v ) {
				if ( is_array( $v ) ) {
					foreach ( $v as $k2 => $v2 ) {
						if ( is_array( $v2 ) ) {
							// There maybe cases where $v2 is an array.
							foreach ( $v2 as $k3 => $v3 ) {
								$original_value = $v3;
								$found          = ( strpos( $v3, THEMECOMPLETE_EPO()->upload_dir ) !== false );
								if ( ( $found && empty( $override ) ) || ! empty( $override ) ) {
									if ( 'no' !== THEMECOMPLETE_EPO()->tm_epo_hide_upload_file_path && filter_var( filter_var( $v3, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH ), FILTER_VALIDATE_URL ) ) {
										$v3 = mb_basename( $v3 );
									}
								}
								if ( ! empty( $override ) && $canbeallowed ) {
									$v3 = '<a href="' . esc_url( $original_value ) . '">' . $v3 . '</a>';
								}
								$v[ $k2 ][ $k3 ] = $v3;
							}
						} else {
							$original_value = $v2;
							$found          = ( strpos( $v2, THEMECOMPLETE_EPO()->upload_dir ) !== false );
							if ( ( $found && empty( $override ) ) || ! empty( $override ) ) {
								if ( 'no' !== THEMECOMPLETE_EPO()->tm_epo_hide_upload_file_path && filter_var( filter_var( $v2, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH ), FILTER_VALIDATE_URL ) ) {
									$v2 = mb_basename( $v2 );
								}
							}
							if ( ! empty( $override ) && $canbeallowed ) {
								$v2 = '<a href="' . esc_url( $original_value ) . '">' . $v2 . '</a>';
							}
							$v[ $k2 ] = $v2;
						}
					}
				} else {

					$original_value = $v;
					$found          = ( strpos( $v, THEMECOMPLETE_EPO()->upload_dir ) !== false );
					if ( ( $found && empty( $override ) ) || ! empty( $override ) ) {
						if ( 'no' !== THEMECOMPLETE_EPO()->tm_epo_hide_upload_file_path && filter_var( filter_var( $v, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH ), FILTER_VALIDATE_URL ) ) {
							$v = mb_basename( $v );
						}
					}
					if ( ! empty( $override ) && $canbeallowed ) {
						$v = '<a href="' . esc_url( $original_value ) . '">' . $v . '</a>';
					}
				}
				$new_value[ $k ] = $v;
			}
			$value = $new_value;
		} else {
			$found = ( strpos( $value, THEMECOMPLETE_EPO()->upload_dir ) !== false );
			if ( ( $found && empty( $override ) ) || ! empty( $override ) ) {
				if ( 'no' !== THEMECOMPLETE_EPO()->tm_epo_hide_upload_file_path && filter_var( filter_var( $value, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH ), FILTER_VALIDATE_URL ) ) {
					$value = mb_basename( $value );
				}
			}
			if ( ! empty( $override ) && $canbeallowed ) {
				$value = '<a href="' . esc_url( $original_value ) . '">' . $value . '</a>';
			}
		}
		if ( is_array( $value ) ) {
			$value = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $value, ',' );
		}

		return $value;

	}

	/**
	 * Helper to determine when the email is about to be sent
	 *
	 * @since 1.0
	 */
	public function change_is_about_to_sent_email() {
		$this->is_about_to_sent_email = true;

	}

	/**
	 * Attach upload files to emails
	 *
	 * @param array  $attachments The email attachments.
	 * @param string $emailmethodid The Email method ID.
	 * @param object $order The order object.
	 *
	 * @return array
	 */
	public function woocommerce_email_attachments( $attachments, $emailmethodid, $order ) {
		if ( $order && is_callable( [ $order, 'get_items' ] ) ) {

			$items = $order->get_items();
			if ( ! is_array( $items ) ) {
				return $attachments;
			}

			$upload_dir = get_option( 'tm_epo_upload_folder' );
			$upload_dir = str_replace( '/', '', $upload_dir );
			$upload_dir = sanitize_file_name( $upload_dir );
			$upload_dir = '/' . $upload_dir . '/';
			$main_path  = $upload_dir;
			$todir      = '';
			$subdir     = $main_path . $todir;
			$param      = wp_upload_dir();
			if ( empty( $param['subdir'] ) ) {
				$base_url        = $param['url'] . $main_path;
				$param['path']   = $param['path'] . $subdir;
				$param['url']    = $param['url'] . $subdir;
				$param['subdir'] = $subdir;
			} else {
				$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
				$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
				$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
				$base_url        = str_replace( $param['subdir'], $main_path, $param['url'] );

			}
			$base_url = THEMECOMPLETE_EPO_HELPER()->to_ssl( $base_url );
			foreach ( $items as $item_id => $item ) {

				$item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );

				$has_epo = is_array( $item_meta ) && isset( $item_meta['_tmcartepo_data'] ) && isset( $item_meta['_tmcartepo_data'][0] );

				if ( $has_epo ) {
					$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartepo_data'][0] );
					if ( is_array( $epos ) ) {
						foreach ( $epos as $key => $epo ) {
							if ( $epo && is_array( $epo ) && isset( $epo['section'] ) ) {

								if ( isset( $epo['element'] ) && isset( $epo['element']['type'] ) && in_array( $epo['element']['type'], apply_filters( 'wc_epo_upload_file_type_array', [ 'upload', 'multiple_file_upload' ] ), true ) ) {
									if ( false !== strpos( $epo['value'], '|' ) ) {
										$links = explode( '|', $epo['value'] );
										foreach ( $links as $link ) {
											$attachments[] = $param['path'] . str_replace( $base_url, '', $link );
										}
									} else {
										$attachments[] = $param['path'] . str_replace( $base_url, '', $epo['value'] );
									}
								}
							}
						}
					}
				}

				$has_fee = is_array( $item_meta ) && isset( $item_meta['_tmcartfee_data'] ) && isset( $item_meta['_tmcartfee_data'][0] );

				if ( $has_fee ) {
					$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartfee_data'][0] );
					if ( is_array( $epos ) && isset( $epos[0] ) && is_array( $epos[0] ) ) {
						$epos = $epos[0];
						foreach ( $epos as $key => $epo ) {
							if ( $epo && is_array( $epo ) && isset( $epo['section'] ) ) {

								if ( isset( $epo['element'] ) && isset( $epo['element']['type'] ) && in_array( $epo['element']['type'], apply_filters( 'wc_epo_upload_file_type_array', [ 'upload', 'multiple_file_upload' ] ), true ) ) {
									if ( false !== strpos( $epo['value'], '|' ) ) {
										$links = explode( '|', $epo['value'] );
										foreach ( $links as $link ) {
											$attachments[] = $param['path'] . str_replace( $base_url, '', $link );
										}
									} else {
										$attachments[] = $param['path'] . str_replace( $base_url, '', $epo['value'] );
									}
								}
							}
						}
					}
				}
			}
		}

		return $attachments;
	}

}
