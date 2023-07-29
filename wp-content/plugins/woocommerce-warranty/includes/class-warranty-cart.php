<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Warranty_Cart {

	public function __construct() {
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'show_product_warranty' ) );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );

		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_cart_validation' ), 10, 2 );

		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_action( 'woocommerce_add_to_cart', array( $this, 'add_warranty_index' ), 10 );

		// Change buttons/cart urls.
		add_filter( 'add_to_cart_text', array( $this, 'add_to_cart_text' ), 15 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 15, 2 );
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 2 );
		add_filter( 'woocommerce_product_supports', array( $this, 'ajax_add_to_cart_supports' ), 10, 3 );
		add_filter( 'woocommerce_is_purchasable', array( $this, 'prevent_purchase_at_grouped_level' ), 10, 2 );

	}

	/**
	 * Show a product's warranty information
	 */
	function show_product_warranty() {
		global $post, $product, $woocommerce;

		if ( $product->is_type( 'external' ) ) {
			return;
		}

		$product_id     = $product->get_id();
		$warranty       = warranty_get_product_warranty( $product_id );
		$warranty_label = $warranty['label'];

		if ( $warranty['type'] == 'included_warranty' ) {
			if ( $warranty['length'] == 'limited' ) {
				$value    = $warranty['value'];
				$duration = warranty_duration_i18n( $warranty['duration'], $value );

				echo '<p class="warranty_info"><b>' . esc_html( $warranty_label ) . ':</b> ' . esc_html( $value ) . ' ' . esc_html( $duration ) . '</p>';
			} else {
				echo '<p class="warranty_info"><b>' . esc_html( $warranty_label ) . ':</b> ' . esc_html__( 'Lifetime', 'wc_warranty' ) . '</p>';
			}
		} elseif ( $warranty['type'] == 'addon_warranty' ) {
			$addons = $warranty['addons'];

			if ( is_array( $addons ) && ! empty( $addons ) ) {
				echo '<p class="warranty_info"><b>' . esc_html( $warranty_label ) . '</b> <select name="warranty">';

				if ( isset( $warranty['no_warranty_option'] ) && $warranty['no_warranty_option'] == 'yes' ) {
					echo '<option value="-1">' . esc_html__( 'No warranty', 'wc_warranty' ) . '</option>';
				}

				foreach ( $addons as $x => $addon ) {
					$amount   = $addon['amount'];
					$value    = $addon['value'];
					$duration = warranty_duration_i18n( $addon['duration'], $value );

					if ( 0 == $value && 0 == $amount ) {
						// no warranty option
						echo '<option value="-1">' . esc_html__( 'No warranty', 'wc_warranty' ) . '</option>';
					} else {
						if ( 0 == $amount ) {
							$amount = __( 'Free', 'wc_warranty' );
						} else {
							$amount = wp_strip_all_tags( wc_price( floatval( $amount ) ) );
						}
						echo '<option value="' . esc_attr( $x ) . '">' . esc_html( $value ) . ' ' . esc_html( $duration ) . ' &mdash; ' . esc_html( $amount ) . '</option>';
					}
				}

				echo '</select></p>';
			}
		} else {
			echo '<p class="warranty_info"></p>';
		}

	}

	/**
	 * Adds a warranty_index to a cart item. Used in tracking the selected warranty options
	 *
	 * @see WC_Warranty_Frontend::add_cart_item()
	 * @param array $item_data
	 * @param int   $product_id
	 * @return array $item_data
	 */
	function add_cart_item_data( $item_data, $product_id ) {
		global $woocommerce;

    // Passed down from WC_Cart::add_to_cart(), so nonce verification can be skipped.
    //phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['warranty'] ) && $_POST['warranty'] !== '' ) {
			$item_data['warranty_index'] = sanitize_text_field( $_POST['warranty'] );
		}
    //phpcs:enable WordPress.Security.NonceVerification.Missing
    
		return $item_data;
	}

	/**
	 * Add custom data to a cart item based on the selected warranty type
	 *
	 * @see WC_Warranty_Frontend::add_cart_item_data()
	 * @param array $item_data
	 * @return array $item_data
	 */
	function add_cart_item( $item_data ) {
		global $woocommerce;

		$_product       = $item_data['data'];
		$warranty_index = false;

		if ( isset( $item_data['warranty_index'] ) ) {
			$warranty_index = $item_data['warranty_index'];
		}

		$product_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();
		$warranty   = warranty_get_product_warranty( $product_id );

		if ( $warranty ) {
			if ( $warranty['type'] == 'addon_warranty' && $warranty_index !== false ) {
				$addons                      = $warranty['addons'];
				$item_data['warranty_index'] = $warranty_index;
				$add_cost                    = 0;

				if ( isset( $addons[ $warranty_index ] ) && ! empty( $addons[ $warranty_index ] ) ) {
					$addon = $addons[ $warranty_index ];
					if ( $addon['amount'] > 0 ) {
						$add_cost += $addon['amount'];

						$_product->set_price( $_product->get_price() + $add_cost );
					}
				}
			}
		}

		return $item_data;
	}

	/**
	 * Make sure an add-to-cart request is valid
	 *
	 * @param bool $valid
	 * @param int  $product_id
	 * @return bool $valid
	 */
	function add_cart_validation( $valid = '', $product_id = '' ) {
		global $woocommerce;

		$warranty       = warranty_get_product_warranty( $product_id );
		$warranty_label = $warranty['label'];

		if ( $warranty['type'] == 'addon_warranty' && ! isset( $_REQUEST['warranty'] ) ) {
			$error = sprintf( __( 'Please select your %s first.', 'wc_warranty' ), $warranty_label );
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( $error, 'error' );
			} else {
				$woocommerce->add_error( $error );
			}

			return false;
		}

		return $valid;
	}


	/**
	 * Check required add-ons.
	 *
	 * @param int $product_id Product ID.
	 * @return bool
	 */
	public function check_required_warranty( $product_id ) {
		$warranty = warranty_get_product_warranty( $product_id );

		if ( $warranty['type'] == 'addon_warranty' && ! isset( $_REQUEST['warranty'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add to cart text.
	 *
	 * @since 1.0.0
	 * @version 2.9.0
	 * @param string $text Add to cart text.
	 * @param object $product
	 * @return string
	 */
	public function add_to_cart_text( $text, $product = null ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return $text;
		}

		if ( ! is_single( $product->get_id() ) && $this->check_required_warranty( $product->get_id() ) ) {
				$text = apply_filters( 'addons_add_to_cart_text', __( 'Select options', 'woocommerce-warranty' ) );
		}

		return $text;
	}

	/**
	 * Removes ajax-add-to-cart functionality in WC 2.5 when a product has required add-ons.
	 *
	 * @param  bool       $supports If support a feature.
	 * @param  string     $feature  Feature to support.
	 * @param  WC_Product $product  Product data.
	 * @return bool
	 */
	public function ajax_add_to_cart_supports( $supports, $feature, $product ) {
		if ( 'ajax_add_to_cart' === $feature && $this->check_required_warranty( $product->get_id() ) ) {
			$supports = false;
		}

		return $supports;
	}

	/**
	 * Include product add-ons to add to cart URL.
	 *
	 * @since 1.0.0
	 * @version 2.9.0
	 * @param string $url Add to cart URL.
	 * @param object $product WC_Product.
	 * @return string
	 */
	public function add_to_cart_url( $url, $product = null ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return $url;
		}

		$product_id = $product->get_id();
		$data       = warranty_request_get_data();

		if ( ! is_single( $product_id ) && in_array( $product->get_type(), apply_filters( 'woocommerce_product_addons_add_to_cart_product_types', array( 'subscription', 'simple' ) ), true ) && ( ! isset( $data['wc-api'] ) || 'WC_Quick_View' !== $data['wc-api'] ) && $this->check_required_warranty( $product_id ) ) {
			$url = apply_filters( 'addons_add_to_cart_url', get_permalink( $product_id ) );
		}

		return $url;
	}

	/**
	 * Don't let products with required addons be added to cart when viewing grouped products.
	 *
	 * @param  bool       $purchasable If product is purchasable.
	 * @param  WC_Product $product     Product data.
	 * @return bool
	 */
	public function prevent_purchase_at_grouped_level( $purchasable, $product ) {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$product_id = $product->parent->id;
		} else {
			$product_id = $product->get_parent_id();
		}

		if ( $product && ! $product->is_type( 'variation' ) && $product_id && is_single( $product_id ) && $this->check_required_warranty( $product->get_id() ) ) {
			$purchasable = false;
		}

		return $purchasable;
	}

	/**
	 * Returns warranty data about a cart item
	 *
	 * @param array $other_data
	 * @param array $cart_item
	 * @return array $other_data
	 */
	function get_item_data( $other_data, $cart_item ) {
		$_product   = $cart_item['data'];
		$product_id = $_product->get_id();

		$warranty       = warranty_get_product_warranty( $product_id );
		$warranty_label = $warranty['label'];

		if ( $warranty ) {
			if ( 'addon_warranty' === $warranty['type'] && isset( $cart_item['warranty_index'] ) ) {
				$addons         = $warranty['addons'];
				$warranty_index = $cart_item['warranty_index'];

				if ( isset( $addons[ $warranty_index ] ) && ! empty( $addons[ $warranty_index ] ) ) {
					$addon = $addons[ $warranty_index ];
					$name  = $warranty_label;
					$value = $GLOBALS['wc_warranty']->get_warranty_string( $addon['value'], $addon['duration'] );

					if ( $addon['amount'] > 0 ) {
						$value .= ' (' . wc_price( $addon['amount'] ) . ')';
					}

					$other_data[] = array(
						'name'    => $name,
						'value'   => $value,
						'display' => '',
					);
				}
			} elseif ( 'included_warranty' === $warranty['type'] ) {
				if ( 'lifetime' === $warranty['length'] ) {
					$other_data[] = array(
						'name'    => $warranty_label,
						'value'   => __( 'Lifetime', 'wc_warranty' ),
						'display' => '',
					);
				} elseif ( 'limited' === $warranty['length'] ) {
					$string       = $GLOBALS['wc_warranty']->get_warranty_string( $warranty['value'], $warranty['duration'] );
					$other_data[] = array(
						'name'    => $warranty_label,
						'value'   => $string,
						'display' => '',
					);
				}
			}
		}

		return $other_data;
	}

	/**
	 * Get warranty index and add it to the cart item
	 *
	 * @param array $cart_item Cart item.
	 * @param array $values Warranty data.
	 * @return array $cart_item
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {

		if ( isset( $values['warranty_index'] ) ) {
			$cart_item['warranty_index'] = $values['warranty_index'];
			$cart_item                   = $this->add_cart_item( $cart_item );
		}

		return $cart_item;
	}

	/**
	 * Add warranty index to the cart items from POST
	 *
	 * @param string $cart_key Cart key.
	 * @return void
	 */
	public function add_warranty_index( $cart_key ) {
		global $woocommerce;

		$data = warranty_request_post_data();

		if ( ! empty( $data['warranty'] ) ) {
			$woocommerce->cart->cart_contents[ $cart_key ]['warranty_index'] = $data['warranty'];
		}
	}

}

$GLOBALS['warranty_cart'] = new Warranty_Cart();
