<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_Vendors_Gateways' ) ){
    /**
     * YITH Gateway
     *
     * Define methods and properties for class that manages admin payments
     *
     * @class      YITH_Vendors_Gateways
     * @package    Yithemes
     * @since      Version 2.0.0
     * @author     Your Inspiration Themes
     */
    class YITH_Vendors_Gateways{

        /**
         * List of available gateways
         *
         * @var array Array of available gateways
         *
         * @since 1.0
         */
        public static $available_gateways = array();

	    /**
	     * List of all gateway instance
	     *
	     * @var array
	     *
	     * @since 1.0
	     */
	    public static $gateways = array();

        /**
         * Array of instances of the class, one for each available gateway
         *
         * @var mixed Array of instances of the class
         *
         * @since 1.0
         */
        public static $instance = null;

        /**
         * Constructor Method
         *
         * @return \YITH_Vendors_Gateway
         * @since 1.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.it>
         */
        public function __construct(){
	        self::$available_gateways = apply_filters( 'yith_wcmv_available_gateways', array(
			        'paypal-masspay',
			        'stripe-connect',
                    'paypal-payouts',
                    'account-funds'
		        )
	        );

	        foreach( self::$available_gateways as $gateway ){
	            self::$gateways[ $gateway ] = YITH_Vendors_Gateway( $gateway );
            }
        }

        /**
         * Returns list of gateways that results enabled
         *
         * @static
         * @return array Array of enabled gateway, as slug => Class_Name
         * @since 1.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.it>
         */
        static public function get_available_gateways( $return = 'ids' ){
            $enabled_gateways = array();

            if( ! empty( self::$available_gateways ) ){
                foreach( self::$available_gateways as $gateway_slug ){
                    $enabled_gateways[ $gateway_slug ] = 'ids' == $return ? self::get_gateway_class_from_slug( $gateway_slug ) : YITH_Vendors_Gateway( $gateway_slug );
                }
            }

            return apply_filters( 'yith_wcmv_get_available_gateways', $enabled_gateways, $return );
        }

	    /**
	     * Returns class name of a gateway, calculating it from slug
	     *
	     * @param $slug string Gateway slug
	     *
	     * @static
	     * @return string Name of the gateway class
	     * @since 1.0
	     * @author Antonio La Rocca <antonio.larocca@yithemes.it>
	     */
	    static public function get_gateway_class_from_slug( $slug ){
		    return 'YITH_Vendors_Gateway_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', strtolower( $slug ) ) ) );
	    }

        /**
         * Returns instance of the class,
         *
         * @static
         * @return \YITH_Vendors_Gateway Unique instance of the class for the passed gateway slug
         * @since 1.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.it>
         */
	    public static function get_instance() {
		    if ( is_null( self::$instance ) ) {
			    self::$instance = new self;
		    }

		    return self::$instance;
	    }

	    /**
	     * Show html content for yith_wcmv_gateways_list option type
	     *
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @return  void
	     */
	    public static function show_gateways_list(){
		    $gateways = apply_filters( 'yith_wcmv_show_enabled_gateways_table', self::get_available_gateways() );
		    if( ! empty( $gateways ) ) :
			    ?>
			    <tr valign="top">
				    <th scope="row" class="titledesc">
                        <?php _ex( 'Installed gateways', '[Admin]: Option title', 'yith-woocommerce-product-vendors' ) ?>
                    </th>
				    <td class="forminp">
					    <table class="yith_wcmv_gateways wc_gateways widefat" cellspacing="0">
						    <thead>
						    <tr>
							    <?php
							    $columns = apply_filters( 'yith_wcmv_payment_gateways_setting_columns', array(
								    'name'     => __( 'Gateway', 'yith-woocommerce-product-vendors' ),
								    'id'       => __( 'Gateway ID', 'yith-woocommerce-product-vendors' ),
								    'status'   => __( 'Enabled', 'yith-woocommerce-product-vendors' ),
							    ) );

							    foreach ( $columns as $key => $column ) {
								    echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
							    }
							    ?>
						    </tr>
						    </thead>
						    <tbody>
						    <?php
						    foreach ( $gateways as $slug => $class ) {
							    $gateway = YITH_Vendors_Gateway( $slug );
							    
							    if( empty( $gateway ) ){
							        $gateway = apply_filters( "yith_wcmv_external_gateway_{$slug}", $gateway, $slug );
							        
							        if( empty( $gateway ) ){
								        /**
								         * Prevent fatal error "Call to a member function on boolean or null"
								         */
							            continue;
                                    }
                                }

							    echo '<tr>';

							    foreach ( $columns as $key => $column ) {

								    switch ( $key ) {

									    case 'name' :
									        $displayed_gateway_name = apply_filters( "yith_wcmv_{$gateway->get_id()}_gateway_display_name", $gateway->get_method_title() );
										    $method_title = ! empty( $displayed_gateway_name ) ? $displayed_gateway_name : _x( '(no title)',  '[Admin]: means "no name". Powered by WooCommerce. Please, refer to WooCommerce .po file for more details about that.', 'yith-woocommerce-product-vendors' );
										    $admin_url = admin_url( 'admin.php?page=yith_wpv_panel&tab=gateways&section=' . strtolower( $gateway->get_id() ) );
										    $class = 'name ' . $gateway->get_id();

										    if( method_exists( $gateway, 'get_is_coming_soon' ) && $gateway->get_is_coming_soon() ){
										        $class .= " is_coming_soon";
                                            }

										    echo '<td class="' . $class . '">
                                                        <a href="' . apply_filters( "yith_wcmv_{$gateway->get_id()}_options_admin_url", $admin_url ) . '">' . esc_html( $method_title ) . '</a>
                                                    </td>';
										    break;

									    case 'id' :
									        $gateway_id = $gateway->get_id();
									        $displayed_gateway_id = apply_filters( "yith_wcmv_displayed_{$gateway_id}_id", $gateway_id );
										    echo '<td class="id">' . $displayed_gateway_id . '</td>';
										    break;

									    case 'status' :
										    echo '<td class="status">';
										    $span_class = $span_data_tip = $span_description = '';

										    if( apply_filters( "yith_wcmv_is_{$gateway->get_id()}_gateway_enabled", $gateway->is_enabled() ) ){
											    $span_class = 'status-enabled';
											    $span_data_tip = esc_attr__( 'Enabled', 'yith-woocommerce-product-vendors' );
											    $span_description = esc_html__( 'Enabled', 'yith-woocommerce-product-vendors' );
                                            }

                                            else {
	                                            $span_class = 'status-disabled';
	                                            $span_data_tip = esc_attr__( 'Disabled', 'yith-woocommerce-product-vendors' );
	                                            $span_description = esc_html__( 'Disabled', 'yith-woocommerce-product-vendors' );
                                            }

                                            printf( "<span class='yith tips %s' data-tip='%s'>%s</span>", $span_class, $span_data_tip, $span_description );
										    echo '</td>';
										    break;

									    default :
										    do_action( 'yith_wcmv_payment_gateways_setting_column_' . $key, $gateway );
										    break;
								    }
							    }

							    echo '</tr>';
						    }
						    ?>
						    </tbody>
					    </table>
				    </td>
			    </tr>
			    <?php
		    endif;
	    }
    }
}

/**
 * Get the single instance of YITH_Vendors_Gateway_Panel class
 *
 *
 * @return \YITH_Vendors_Gateway Single instance of the class
 * @since  1.0
 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
 */
function YITH_Vendors_Gateways(){
    return YITH_Vendors_Gateways::get_instance();
}