<?php
/**
 * Worldpay payment gateway implementation for Gutenberg Blocks
 */

namespace Automattic\WooCommerce\Blocks\Payments\Integrations;

class Wc_Worldpay_Blocks extends AbstractPaymentMethodType {
    
    private $localized = 0;
	protected $name    = 'worldpay';

	public function initialize() {
		$this->settings = get_option( 'woocommerce_worldpay_settings' );
        $this->icon = apply_filters( 'wc_worldpay_icon', '' );
	}

    // Register this payment method
    public static function register() {
        add_action( 'woocommerce_blocks_payment_method_type_registration', 
                    function ( $registry ) {
                        $registry->register( new static() );
        });
    }

	public function is_active() {
		return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
	}

	public function get_payment_method_script_handles() {

        $path           = WORLDPAYPLUGINURL . 'classes/blocks/js/wc-payment-method-worldpay.js';
        $handle         = 'wc-payment-method-worldpay';
        $dependencies   = array( 'wp-hooks' );

        wp_register_script( $handle, $path, $dependencies, WORLDPAYPLUGINVERSION, TRUE );
       
        if (!$this->localized) {

            $strings = array( 
                    'Pay via Worldpay'    => __('Pay via Worldpay', 'woocommerce_worlday'),
                    'Worldpay'            => __('Worldpay', 'woocommerce_worlday') 
                );

            wp_localize_script('wc-payment-method-worldpay', 'WorldpayLocale', $strings);
            $this->localized = 1;

        }

		return array( 'wc-payment-method-worldpay' );
	}

	public function get_payment_method_data() {

        $args = array(
            'title'           => $this->get_title(),
            'description'     => $this->get_description(),
            'iconsrc'         => $this->get_icons(),
            'supports'        => $this->get_supports(),
            'poweredbywp'     => $this->get_poweredby(),
            'testmode'        => $this->get_testmode(),
            'testcard'        => $this->get_testcard(),
        );

        return $args;

	}

    private function get_title() {
        return isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Pay with Worldpay', 'woocommerce_worlday' );
    }

    private function get_description() {

        $description = isset( $this->settings['description'] ) ? $this->settings['description'] : __( 'Pay with Worldpay', 'woocommerce_worlday' );

        return $description;
    }

    private function get_icons() {

        $icons_src  = array();

        if ( $this->icon ) {

            $icons_src[ strtolower($this->settings['title']) ] = array(
                'src' => esc_url( $this->icon ),
                'alt' => $this->settings['title']
            );

        } elseif ( ! empty( $this->settings['cardtypes'] ) ) {
            foreach ( $this->settings['cardtypes'] as $card_type ) {

                $icons_src[esc_attr( strtolower( str_replace( ' ','-',$card_type ) ) )] = array(
                    'src' => esc_url( WORLDPAYPLUGINURL . 'images/card-' . strtolower( str_replace(' ','-',$card_type) ) . '.png' ),
                    'alt' => esc_attr( ucwords( $card_type ) )
                );

            }
        }

        /**
         * Add Payments V.me logo
         */
        if ( $this->settings['vmelogo'] == 'yes' ) {
            // $icons_src['vmelogo'] = esc_url( WORLDPAYPLUGINURL . 'images/vme.png' );
        }

        /**
         * Add Payments Powered By WorldPay logo
         */
        if ( $this->settings['wplogo'] == 'yes' ) {
            // $icons_src['Payments Powered By WorldPay'] = esc_url( WORLDPAYPLUGINURL . 'images/poweredByWorldPay.png' );   
        }

        return $icons_src;
    }

    private function get_supports() {

        $dynamiccallback = isset( $this->settings['dynamiccallback'] ) && $this->settings['dynamiccallback'] == 'yes' ? true : false;
        $remoteid        = isset( $this->settings['remoteid'] ) ? $this->settings['remoteid'] : false;

        if( $dynamiccallback || !$remoteid ) {

            return array(
                'products',
                'refunds'
            );

        } else {

            return array(
                'products',
                'subscriptions',
                'gateway_scheduled_payments',
                'subscription_cancellation',
                'refunds',
                'subscription_amount_changes'
            );  

        }

    }

    private function get_poweredby() {
        return esc_url( WORLDPAYPLUGINURL . 'images/poweredByWorldPay.png' );
    }

    private function get_testmode() {

        $return = NULL;
        if ( $this->settings['status'] == 'testing' ) {
            $return = __( 'TEST MODE ENABLED.', 'woocommerce_worlday' );
            $return = trim( $return );
        }

        return $return;
    }

    private function get_testcard() {

        $return = NULL;
        if ( $this->settings['status'] == 'testing' ) {
            $return = __( 'In test mode, you can use Visa card number 4111111111111111 with any CVC and a valid expiration date.', 'woocommerce_worlday' );
            $return = trim( $return );
        }

        return $return;
    }

}