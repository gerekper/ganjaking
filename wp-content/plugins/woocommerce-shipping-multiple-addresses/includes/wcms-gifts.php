<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class WC_MS_Gifts {

    private $wcms;

    public function __construct( WC_Ship_Multiple $wcms ) {

        $this->wcms = $wcms;

        add_action( 'wc_ms_shipping_package_block', array( __CLASS__, 'render_gift_form'), 10, 2 );

        add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'store_order_gift_data'), 20, 2 );

        add_action( 'woocommerce_before_checkout_shipping_form', array( __CLASS__, 'shipping_address_gift_form' ) );
        add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'store_shipping_address_gift_data' ) );
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array( __CLASS__, 'render_order_shipping_gift_data' ) );

        // Modify the packages, shipping methods and addresses in the session
        add_filter( 'wc_ms_checkout_session_packages', array( __CLASS__, 'apply_gift_data_to_packages' ), 30 );

        add_action( 'wc_ms_order_package_block_before_address', array( __CLASS__, 'render_gift_data'), 10, 3 );
    }

    /**
     * Returns TRUE if the Gift Packages setting is enabled
     * @return bool
     */
    public static function is_enabled() {
        global $wcms;

        if ( !isset( $wcms->gateway_settings['gift_packages'] ) || $wcms->gateway_settings['gift_packages'] != 'yes' ) {
            return false;
        }

        return true;
    }

    /**
     * Show the gift checkbox on the shipping packages blocks
     */
    public static function render_gift_form( $loop, $package ) {
        if ( !self::is_enabled() ) {
            return;
        }

        ?>
        <div class="gift-form">
            <p>
                <label>
                    <input type="checkbox" class="chk-gift" name="shipping_gift[<?php echo $loop; ?>]" value="yes" data-index="<?php echo $loop; ?>" />
                    <?php _e( 'This is a gift', 'wc_shipping_multiple_address' ); ?>
                </label>
            </p>
        </div>

    <?php
    }

    /**
     * Modify the 'wcms_packages' session data to attach gift data from POST
     * and at the same time, populate the WC_Gift_Checkout::gifts array
     */
    public static function apply_gift_data_to_packages( $packages ) {

        if (! isset($_POST['shipping_gift']) || empty($_POST['shipping_gift']) )
            return $packages;


        foreach ( $_POST['shipping_gift'] as $idx => $value ) {

            if ( $value != 'yes' ) {
                continue;
            }

            if ( !isset( $packages[ $idx ] ) ) {
                continue;
            }

            $packages[ $idx ]['gift'] = true;

        }

        return $packages;

    }

    public static function store_order_gift_data( $order_id ) {

        if ( empty($_POST['shipping_gift'] ) ) {
            return;
        }

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

        $packages = $order->get_meta( '_wcms_packages' );

        foreach ( $_POST['shipping_gift'] as $idx => $value ) {

            if ( $value != 'yes' )
                continue;

            if ( ! array_key_exists( $idx, $packages ) )
                continue;

			$order->update_meta_data( '_gift_'. $idx, true );
        }

		$order->save();
    }

    /**
     * Render the 'This is a Gift' option in the shipping address form
     * @param WC_Checkout $checkout
     */
    public static function shipping_address_gift_form( $checkout ) {
        if ( ! self::is_enabled() ) {
            return;
        }
        ?>
        <div class="gift-form">
            <p>
                <label>
                    <input type="checkbox" class="chk-gift" name="checkout_shipping_gift" value="yes" />
                    <?php _e( 'This is a gift', 'wc_shipping_multiple_address' ); ?>
                </label>
            </p>
        </div>
        <?php
    }

    public static function store_shipping_address_gift_data( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return; 
		}

        if ( ! empty( $_POST['checkout_shipping_gift'] ) ) {
            $order->update_meta_data( '_gift', true );
			$order->save();
        }
    }

    public static function render_order_shipping_gift_data( $order ) {
		if ( ! is_callable( array( $order, 'get_meta' ) ) ) {
			return;
		}

        $is_gift = $order->get_meta( '_gift' );

        if ( $is_gift ) {
            echo '<p><span class="dashicons dashicons-megaphone"></span> <strong>This is a gift</strong></p>';
        }
    }

    public static function render_gift_data( $order, $package, $package_index ) {
		if ( is_callable( array( $order, 'get_meta' ) ) ) {
			return;
		}

        $packages      = $order->get_meta( '_wcms_packages' );
        $order_is_gift = ( true == $order->get_meta( '_gift_' . $package_index ) ) ? true : false;

        if ( $order_is_gift && count( $packages ) == 1 ) {
            // inject the gift data into the only package
            // because multishipping doesn't process gift
            // data when there's only one package
            $package['gift'] = true;
        }

        if ( isset( $package['gift'] ) && true == $package['gift'] ) {
            ?>
            <div class="gift-package">
                <h5><div class="dashicons dashicons-yes"></div><?php _e('This is a Gift', 'wc_shipping_multiple_address'); ?></h5>
            </div>
            <?php

        }

        return;
    }

}
