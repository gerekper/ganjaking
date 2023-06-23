<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_MS_Cart {
    private $wcms;

    public function __construct( WC_Ship_Multiple $wcms ) {
        $this->wcms = $wcms;

        // duplicate cart POST
        add_action( 'template_redirect', array($this, 'duplicate_cart_post') );

        add_action( 'woocommerce_cart_totals_after_shipping', array(&$this, 'remove_shipping_calculator') );

        /* WCMS Cart */
        add_action( 'woocommerce_cart_actions', array( $this, 'show_duplicate_cart_button' ) );

        // cleanup
        add_action( 'woocommerce_cart_emptied', array( $this->wcms, 'clear_session' ) );
        add_action( 'woocommerce_cart_updated', array( $this, 'cart_updated' ) );
    }

    public function duplicate_cart_post() {

        if ( isset( $_GET['duplicate-form'] ) && isset( $_GET['_wcmsnonce'] ) && wp_verify_nonce( $_GET['_wcmsnonce'], 'wcms-duplicate-cart' ) ) {
            $fields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );

            $user_addresses = $this->wcms->address_book->get_user_addresses( wp_get_current_user() );
			$address_ids 	= array_keys( $user_addresses );

            $data   = ( wcms_session_isset( 'cart_item_addresses' ) ) ? wcms_session_get( 'cart_item_addresses' ) : array();
            $rel    = ( wcms_session_isset( 'wcms_item_addresses' ) ) ? wcms_session_get( 'wcms_item_addresses' ) : array();

			$added      = $this->duplicate_cart();
			$address_id = array_shift( $address_ids );
			$address    = $user_addresses[ $address_id ];

			foreach ( $added as $item ) {
				$qtys       = $item['qty'];
				$product_id = $item['id'];
				$sig        = $item['key'] . '_' . $product_id . '_';

				$i = 1;
				for ( $y = 0; $y < $qtys; $y++ ) {
					$rel[ $address_id ][]  = $item['key'];

					while ( isset( $data[ 'shipping_first_name_' . $sig . $i ] ) ) {
						$i++;
					}

					$_sig = $sig . $i;
					if ( $fields ) { 
						foreach ( $fields as $key => $field ) {
							$data[ $key . '_' . $_sig ] = $address[ $key ];
						}
					}
				}

				$cart_address_ids_session = wcms_session_get( 'cart_address_ids' );

				if ( ! wcms_session_isset( 'cart_address_ids' ) || ! in_array( $sig, $cart_address_ids_session ) ) {
					$cart_address_sigs_session          = wcms_session_get( 'cart_address_sigs' );
					$cart_address_sigs_session[ $_sig ] = $address_id;
					wcms_session_set( 'cart_address_sigs', $cart_address_sigs_session );
				}
			}

            wcms_session_set( 'cart_item_addresses', $data );
            wcms_session_set( 'address_relationships', $rel );
            wcms_session_set( 'wcms_item_addresses', $rel );

            wp_redirect( get_permalink( wc_get_page_id('multiple_addresses') ) );
            exit;
        }
    }

	/**
	 * Removes the shipping calculator on the cart page when we have multiple shipping addresses.
	 */
	public function remove_shipping_calculator() {

		if ( isset( WC()->session ) && isset( WC()->session->cart_item_addresses ) ) {
			$script = '
				jQuery( function( $ ) {
					$( ".woocommerce-shipping-calculator" ).remove();
					$( document.body ).on( "updated_cart_totals", function() {
						$( ".woocommerce-shipping-calculator" ).remove();
					} );
				} );
			';
			wc_enqueue_js( $script );
		}
	}

    public function show_duplicate_cart_button() {
        $ms_settings = get_option( 'woocommerce_multiple_shipping_settings', array() );

        if ( isset($ms_settings['cart_duplication']) && $ms_settings['cart_duplication'] != 'no' ) {
            $dupe_url = add_query_arg( array(
				'duplicate-form' => '1',
				'_wcmsnonce'     => wp_create_nonce( 'wcms-duplicate-cart' ),
			), get_permalink( wc_get_page_id( 'multiple_addresses' ) ) );

            echo '<a class="button expand" href="' . esc_url( $dupe_url ) . '" >' . __( 'Duplicate Cart', 'wc_shipping_multiple_address' ) . '</a>';
        }
    }

    public function cart_updated() {

        $cart = WC()->cart->get_cart();

        if ( empty($cart) || !$this->cart_is_eligible_for_multi_shipping() ) {
            wcms_session_delete( 'cart_item_addresses' );
            wcms_session_delete( 'cart_address_sigs' );
            wcms_session_delete( 'address_relationships' );
            wcms_session_delete( 'shipping_methods' );
            wcms_session_delete( 'wcms_original_cart' );
        }
    }

    public function duplicate_cart( $multiplier = 1 ) {

        $this->load_cart_files();

        $cart           = WC()->cart;
        $current_cart   = $cart->get_cart();
        $orig_cart      = array();

        if ( wcms_session_isset('wcms_original_cart') ) {
            $orig_cart = wcms_session_get( 'wcms_original_cart' );
        }

        if ( !empty($orig_cart) ) {
            $contents = wcms_session_get( 'wcms_original_cart' );
        } else {
            $contents = $cart->get_cart();
            wcms_session_set( 'wcms_original_cart', $contents );
        }

        $added = array();
        foreach ( $contents as $cart_key => $content ) {
            $add_qty        = $content['quantity'] * $multiplier;
            $current_qty    = (isset($current_cart[$cart_key])) ? $current_cart[$cart_key]['quantity'] : 0;

            $cart->set_quantity( $cart_key, $current_qty + $add_qty );

            $added[] = array(
                'id'        => $content['product_id'],
                'qty'       => $add_qty,
                'key'       => $cart_key,
                'content'   => $content
            );
        }

        return $added;
    }

    public function load_cart_files() {

        if ( file_exists(WC()->plugin_path() .'/classes/class-wc-cart.php') ) {
            require_once WC()->plugin_path() .'/classes/abstracts/abstract-wc-session.php';
            require_once WC()->plugin_path() .'/classes/class-wc-session-handler.php';
            require_once WC()->plugin_path() .'/classes/class-wc-cart.php';
            require_once WC()->plugin_path() .'/classes/class-wc-checkout.php';
            require_once WC()->plugin_path() .'/classes/class-wc-customer.php';
        } else {
            require_once WC()->plugin_path() .'/includes/abstracts/abstract-wc-session.php';
            require_once WC()->plugin_path() .'/includes/class-wc-session-handler.php';
            require_once WC()->plugin_path() .'/includes/class-wc-cart.php';
            require_once WC()->plugin_path() .'/includes/class-wc-checkout.php';
            require_once WC()->plugin_path() .'/includes/class-wc-customer.php';
        }

        if (! WC()->session )
            WC()->session = new WC_Session_Handler();

        if (! WC()->customer )
            WC()->customer = new WC_Customer();
    }

    /**
     * Check if the contents of the current cart are valid for multiple shipping
     *
     * To pass, there must be 1 or more items in the cart that passes the @see WC_Cart::needs_shipping() test.
     * If there is only 1 item in the cart, it must have a quantity of 2 or more. And child items
     * from Bundles and Composite Products are excluded from the count.
     *
     * This method will automatically return false if the only available shipping method is Local Pickup
     *
     * @return bool
     */
    public function cart_is_eligible_for_multi_shipping() {
        $sess_item_address  = wcms_session_get( 'cart_item_addresses' );
        $has_item_address   = (!wcms_session_isset( 'cart_item_addresses' ) || empty( $sess_item_address )) ? false : true;
        $item_allowed       = false;
        $contents           = wcms_get_real_cart_items();

	    if ( empty( $contents ) ) {
	    	// no real, shippable products. Return false immediately
		    return apply_filters( 'wc_ms_cart_is_eligible', false );
	    } elseif ( count( $contents ) > 1) {
            $item_allowed = true;
        } else {
            $content = current( $contents );
            if ( $content && $content['quantity'] > 1) {
                $item_allowed = true;
            }
        }

        // do not allow to set multiple addresses if only local pickup is available
        $available_methods = $this->wcms->get_available_shipping_methods();

        if ( count($available_methods) == 1 && ( isset($available_methods['local_pickup']) || isset($available_methods['local_pickup_plus']) ) ) {
            $item_allowed = false;
        } elseif (isset($_POST['shipping_method']) && ( $_POST['shipping_method'] == 'local_pickup' || $_POST['shipping_method'] == 'local_pickup_plus' ) ) {
            $item_allowed = false;
        }

        // do not allow if any of the cart items is in the excludes list
        $settings           = get_option( 'woocommerce_multiple_shipping_settings', array() );
        $excl_products      = (isset($settings['excluded_products'])) ? $settings['excluded_products'] : array();
        $excl_categories    = (isset($settings['excluded_categories'])) ? $settings['excluded_categories'] : array();

        if ( $excl_products || $excl_categories ) {

            foreach ( $contents as $cart_item ) {
                if ( in_array($cart_item['product_id'], $excl_products) ) {
                    $item_allowed = false;
                    break;
                }

                // item categories
                $cat_ids = wp_get_object_terms( $cart_item['product_id'], 'product_cat', array('fields' => 'ids') );

                foreach ( $cat_ids as $cat_id ) {
                    if ( in_array( $cat_id, $excl_categories ) ) {
                        $item_allowed = false;
                        break 2;
                    }
                }

            }
        }

        return apply_filters( 'wc_ms_cart_is_eligible', $item_allowed );
    }

	/**
	 * Check if multiple addresses have been set
	 *
	 * @return bool
	 */
	public function cart_has_multi_shipping() {
		$sess_item_address = wcms_session_get( 'cart_item_addresses' );
		return apply_filters( 'wc_ms_cart_has_multi_shipping', empty( $sess_item_address ) ? false : true );
	}
}
