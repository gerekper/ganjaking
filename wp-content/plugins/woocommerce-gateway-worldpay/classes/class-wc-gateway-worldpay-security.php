<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Add security column to WooCommerce Admin, uses AVS result from Worldpay
 */
class WC_Gateway_Worldpay_Security {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Add security check column to orders page in admin
        add_action( 'admin_init', array( $this, 'manage_edit_shop_order_columns' ), 10, 2 );

        // Add security check details
        add_action( 'manage_shop_order_posts_custom_column' , array( $this, 'security_check_admin_init') , 2 );

        // Enqueue Admin Scripts and CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

	}

	/**
	 * Load admin JS / CSS
	 * @param  [type] $hook [description]
	 * @return void
	 */
	function admin_scripts( $hook ) {
		wp_enqueue_style( 'worldpay-admin-fa', "//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" , array(), WORLDPAYPLUGINVERSION );
		wp_enqueue_style( 'worldpay-admin-wp', $this->get_plugin_url() . '/assets/admin-css.css' , array(), WORLDPAYPLUGINVERSION );
	}

	/**
     * Add Security Check column to orders page in admin
     */
    function manage_edit_shop_order_columns( $columns ) {
    	add_filter( 'manage_edit-shop_order_columns',  array( $this, 'security_check_column_admin_init' ) );
    }

    function security_check_column_admin_init( $columns ) {
        global $woocommerce;
                
        $columns["worldpay_security"] = __( 'Checks', 'woocommerce_worlday' );
                
        return $columns;

    }

    /**
     * Add Worldpay security responses to order rows
     */
    function security_check_admin_init( $column ) {
        global $post, $woocommerce, $the_order;

        if ( $column === 'worldpay_security' && get_post_meta( $post->ID, '_payment_method', TRUE ) === 'worldpay' ) {

        	$CV2Result      = NULL;
            $PostCodeResult = NULL;
            $AddressResult  = NULL;
            $CountryResult  = NULL;

            if( !empty( get_post_meta( $post->ID, '_worldpay_response', TRUE ) ) ) {

                $result         = get_post_meta( $post->ID, '_worldpay_response', TRUE );
                $avs 			= isset( $result['AVS'] ) && strlen( $result['AVS'] ) === 4 ? $result['AVS'] : NULL;

                if( !is_null( $avs ) ) {

                	$CV2Result      = substr( $avs, 0, 1 );
		            $PostCodeResult = substr( $avs, 1, 1 );
		            $AddressResult  = substr( $avs, 2, 1 );
		            $CountryResult  = substr( $avs, 3, 1 );

                }
            }

            if( !is_null( $CV2Result ) ) {

                switch ( $CV2Result ) {
                    case '0':
                        $cv2class 		= 'worldpay-check';
                        $cv2response 	= __( 'Not supported', 'woocommerce_worlday' );
                        break;
                    case '1':
                        $cv2class 		= 'worldpay-check';
                        $cv2response 	= __( 'Not checked', 'woocommerce_worlday' );
                        break;
                    case '2':
                        $cv2class 		= 'worldpay-ok';
                        $cv2response 	= __( 'Matched', 'woocommerce_worlday' );
                        break;
                    case '4' :
                        $cv2class 		= 'worldpay-fail';
                        $cv2response 	= __( 'Not matched', 'woocommerce_worlday' );
                        break;

                }

                printf( '<span class="%s tips" data-tip="%s">%s</span>', $cv2class, __( 'Card Verification Value check ', 'woocommerce_worlday' ) . $cv2response, 'C' );

            } 

            if( !is_null( $PostCodeResult ) ) {

                switch ( $PostCodeResult ) {
                    case '0':
                        $postcodeclass 		= 'worldpay-check';
                        $postcoderesponse 	= __( 'Not supported', 'woocommerce_worlday' );
                        break;
                    case '1':
                        $postcodeclass 		= 'worldpay-check';
                        $postcoderesponse 	= __( 'Not checked', 'woocommerce_worlday' );
                        break;
                    case '2':
                        $postcodeclass 		= 'worldpay-ok';
                        $postcoderesponse 	= __( 'Matched', 'woocommerce_worlday' );
                        break;
                    case '4' :
                        $postcodeclass 		= 'worldpay-fail';
                        $postcoderesponse 	= __( 'Not matched', 'woocommerce_worlday' );
                        break;

                }

                printf( '<span class="%s tips" data-tip="%s">%s</span>', $postcodeclass, __( 'Postcode check ', 'woocommerce_worlday' ) . $postcoderesponse, 'P' );

            }

            if( !is_null( $AddressResult ) ) {

                switch ( $AddressResult ) {
                    case '0':
                        $addressclass 		= 'worldpay-check';
                        $addressresponse 	= __( 'Not supported', 'woocommerce_worlday' );
                        break;
                    case '1':
                        $addressclass 		= 'worldpay-check';
                        $addressresponse 	= __( 'Not checked', 'woocommerce_worlday' );
                        break;
                    case '2':
                        $addressclass 		= 'worldpay-ok';
                        $addressresponse 	= __( 'Matched', 'woocommerce_worlday' );
                        break;
                    case '4' :
                        $addressclass 		= 'worldpay-fail';
                        $addressresponse 	= __( 'Not matched', 'woocommerce_worlday' );
                        break;

                }

                printf( '<span class="%s tips" data-tip="%s">%s</span>', $addressclass, __( 'Address check ', 'woocommerce_worlday' ) . $addressresponse, 'A' );

            }

            if( !is_null( $CountryResult ) ) {

                switch ( $CountryResult ) {
                    case '0':
                        $countyclass 	= 'worldpay-check';
                        $countyresponse = __( 'Not supported', 'woocommerce_worlday' );
                        break;
                    case '1':
                        $countyclass 	= 'worldpay-check';
                        $countyresponse = __( 'Not checked', 'woocommerce_worlday' );
                        break;
                    case '2':
                        $countyclass 	= 'worldpay-ok';
                        $countyresponse = __( 'Matched', 'woocommerce_worlday' );
                        break;
                    case '4' :
                        $countyclass 	= 'worldpay-fail';
                        $countyresponse = __( 'Not matched', 'woocommerce_worlday' );
                        break;

                }

                printf( '<span class="%s tips" data-tip="%s">%s</span>', $countyclass, __( 'Country comparison check ', 'woocommerce_worlday' ) . $countyresponse, 'W' );

            }   

        }

    }

    /**
	 * Returns the plugin's url without a trailing slash
	 *
	 * [get_plugin_url description]
	 * @return [type]
	 */
	function get_plugin_url() {
		return str_replace('/classes','',untrailingslashit( plugins_url( '/', __FILE__ ) ) );
	}

} // WC_Gateway_Worldpay_Security
