<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendors_Admin
 * @package    Yithemes
 * @since      Version 1.9.17
 * @author     Your Inspiration Themes
 *
 */
if ( !class_exists( 'YITH_Vendor_Shipping_Admin' ) ) {

    class YITH_Vendor_Shipping_Admin {


        /**
         * Construct
         */
        public function __construct() {

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            add_filter( 'yith_wpv_save_checkboxes', array( $this, 'save_empty_checkboxes' ), 10, 2 );

            add_action( 'wp_ajax_yith_wpv_shipping_add_new_option', array( $this, 'yith_wpv_shipping_add_new_option' ) );

            add_action( 'wp_ajax_yith_wpv_shipping_add_new_shipping_method', array( $this, 'yith_wpv_shipping_add_new_shipping_method' ) );

            add_filter( 'yith_wcmv_register_commissions_status', array( $this, 'register_shipping_fee_status' ), 15, 3 );

	        /**
	         * @TODO: Add support for delviery Date Plugin
	         *
	         * Removed the Delivery Date fields
	         */
	        if( function_exists( 'YITH_Delivery_Date_Shipping_Manager' ) && YITH_Delivery_Date_Shipping_Manager() instanceof YITH_Delivery_Date_Shipping_Manager ){
		        $vendor = yith_get_vendor( 'current', 'user' );
		        if( $vendor->is_valid() && $vendor->has_limited_access() ){
			        remove_action( 'admin_init', array( YITH_Delivery_Date_Shipping_Manager(), 'set_shipping_method' ), 99 );
		        }
	        }

            //Register shipping commissions
	        add_action( 'yith_wcmv_suborder_created', 'YITH_Vendor_Shipping::register_commissions', 15, 1 );
        }

        public function register_shipping_fee_status( $commission_ids, $order_id, $status ){
            $args = array(
                'order_id'  => $order_id,
                'type'      => 'shipping',
                'status'    => 'all'
            );

            $shipping_fee_ids = YITH_Commissions()->get_commissions( $args );

            return ! empty( $shipping_fee_ids ) ? array_merge( $commission_ids, $shipping_fee_ids ) : $commission_ids;
        }

        public function enqueue_scripts() {
            global $pagenow;
            $is_shipping_tab = 'admin.php' == $pagenow && ! empty( $_GET['page'] ) && YITH_Vendors()->admin->vendor_panel_page == $_GET['page'] && ! empty( $_GET['tab'] ) && 'vendor-shipping' == $_GET['tab'];

            $selectWoo = YITH_Vendors()->is_wc_3_2_or_greather ? 'selectWoo' : 'select2';

            $deps = array(
                'jquery' ,
                'jquery-blockui',
                'jquery-ui-sortable',
                'jquery-tiptip',
                'wc-backbone-modal',
                $selectWoo
            );

            $handle = 'yith-wpv-admin-shipping';

            $script_params = array(
                'ajax_url'    => admin_url( 'admin-ajax.php' ),
                'wc_ajax_url' => WC_AJAX::get_endpoint( "%%endpoint%%" ),
            );

            wp_register_script( $handle, YITH_WPV_ASSETS_URL . 'js/' . yit_load_js_file( 'shipping.js' ), $deps, YITH_WPV_VERSION, true );
            wp_localize_script( $handle, 'yith_wpv_shipping_general', $script_params );

            if( apply_filters( 'yith_wcmv_is_shipping_tab', $is_shipping_tab ) ){
                wp_enqueue_script( $handle );
                wp_enqueue_style( 'jquery-ui-style' );
	            wp_enqueue_style( 'woocommerce_admin_styles' );

                if( wp_script_is( 'wc-enhanced-select' ) ){
                    wp_deregister_script( 'wc-enhanced-select' );
                }
            }
        }

        /**
         * @param $zone
         *
         * @return array|mixed
         *
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function get_shipping_methods_per_vendor_by_zone( $zone ) {

            $shipping_methods = array();

            // Rest of the World
            if( is_object( $zone ) && $zone instanceof WC_Shipping_Zone) {

                $shipping_methods = $zone->get_shipping_methods();

            }
            //Custom Shipping Zones
            else if( is_array( $zone ) ) {

                $shipping_methods = $zone['shipping_methods'];

            }

            return $shipping_methods;

        }

        /**
         * @param $vendor
         */
        public function print_shipping_table( $vendor , $wc_country_obj ) {

            yith_wcpv_get_template( 'vendor-admin-shipping-table', array( 'vendor' => $vendor , 'wc_country' => $wc_country_obj ), 'admin/vendor-panel' );
        }

        /**
         * @param $index
         * @param $zone
         */
        public function print_line_option( $index, $zone , $continents = null , $allowed_countries = null , $shipping_methods = null ) {
            $args = array(
                'index'             => $index ,
                'zone'              => $zone ,
                'continents'        => $continents ,
                'allowed_countries' => $allowed_countries ,
                'shipping_methods'  => $shipping_methods
            );

            yith_wcpv_get_template( 'vendor-admin-shipping-table-row', $args, 'admin/vendor-panel' );

        }

        public function print_line_shipping_methods( $area_key, $zone_shipping_methods , $wc_shipping_methods ) {
            ?>
            <li class="wc-shipping-zone-methods-add-row">
                <a href="#" class="yith-wpv-shipping-methods-add add_shipping_method tips" data-tip="<?php esc_attr_e( 'Add shipping method', 'yith-woocommerce-product-vendors' ); ?>" data-disabled-tip="<?php esc_attr_e( 'Save changes to continue adding shipping methods to this zone', 'yith-woocommerce-product-vendors' ); ?>" data-key="<?php echo $area_key; ?>"><?php _e( 'Add shipping method', 'yith-woocommerce-product-vendors' ); ?>
                </a>
            </li>
            <?php

            if ( ! empty( $zone_shipping_methods ) ) {
                foreach ( $zone_shipping_methods as $index => $method ) {

                    if( isset( $method['type_id'] ) && isset( $wc_shipping_methods[ $method['type_id'] ] ) )  {

                        $wc_method = $wc_shipping_methods[ $method['type_id'] ] ;

                        $this->print_single_method_in_cell( $wc_method, $area_key, $index , $method );
                    }

                }
            } else {
                echo '<li class="wc-shipping-zone-method wc-shipping-zone-method-noshipping">' . __( 'No shipping methods offered to this zone.', 'yith-woocommerce-product-vendors' ) . '</li>';
            }
        }

        /**
         *  @since  1.9.17
         *  @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function yith_wpv_shipping_add_new_option() {
			$allowed_countries = WC()->countries->get_allowed_countries();
			$continents        = WC()->countries->get_continents();

	        try{
		        $index = random_int( PHP_INT_MIN, PHP_INT_MAX );
	        }
	        catch( Exception $e ){
		        echo $e->getMessage();
	        }

            $this->print_line_option( $index, null, $continents, $allowed_countries );

            wp_die();
        }

        /**
         *  @since  1.9.17
         *  @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function yith_wpv_shipping_add_new_shipping_method() {

            if( isset( $_REQUEST[ 'data' ] ) ) {

                $method_id = $_REQUEST[ 'data' ]['add_method_id'];
                $shipping_methods  = WC()->shipping->load_shipping_methods();

                if ( isset( $shipping_methods[$method_id] ) ) {

                    $method = $shipping_methods[$method_id];

                    $area_key = $_REQUEST['data']['yith_wpd_area_key'];

                    try{
	                    $index = random_int( PHP_INT_MIN, PHP_INT_MAX );
                    }
                    catch( Exception $e ){
                        echo $e->getMessage();
                    }

                    $this->print_single_method_in_cell( $method, $area_key, $index , $method_id );

                }

            }

            wp_die();
        }

        /**
         * @param $method
         * @param $area_key
         * @param $index
         * @param $method_id
         *
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        private function print_single_method_in_cell( $wc_method , $area_key , $index , $method) {

	        $default_method_values = array(
		        'method_title'      => $wc_method->method_title,
		        'method_tax_status' => '',
		        'method_cost'       => '',
		        'method_requires'   => '',
		        'min_amount'        => '',
		        'type_id'           => $wc_method->id
	        );

	        //Get Shipping Class Fields
	        $instance_form_fields         = $wc_method instanceof WC_Shipping_Method ? $wc_method->get_instance_form_fields() : array();
	        $instance_form_fields_to_skip = apply_filters( 'yith_wcmv_shipping_method_isntance_form_fields_to_skip', array(
			        'title',
			        'tax_status',
			        'cost'
		        )
	        );

	        $extra_fields = array_keys( $instance_form_fields );
	        $extra_fields = array_diff( $extra_fields, $instance_form_fields_to_skip );

	        foreach( $extra_fields as $field ){
	            $default_method_values[ $field ] = '';
            }

	        $method = wp_parse_args( $method, $default_method_values );

	        $title      = $method['method_title'];
	        $method_id  = $method['type_id'];
	        $class_name = 'yes' === $wc_method->enabled ? 'method_enabled' : 'method_disabled';

            echo '<li class="wc-shipping-zone-method yith-wpv-new-shipping-method-temp"><span class="wc-shipping-zone-method-remove" data-index="'.esc_attr( $index ).'"></span><a href="#" class="' . esc_attr( $class_name ) . '" data-parent-key="'. esc_attr( $area_key ) . '" data-shipping-key="'. esc_attr( $index ) . '" data-shipping-type="'. esc_attr( $method_id ) . '" data-title="' . esc_html( $wc_method->method_title ) . '">' . esc_html( $title ) . '</a></li>';

            echo '<input type="hidden" class="wc-shipping-zone-method-hidden-data" name="yith_vendor_data[zone_data][' . esc_attr( $area_key ) . '][zone_shipping_methods][' . esc_attr( $index ) . '][type_id]" value="' . esc_attr( $method_id ) . '" />';

            echo '<div class="yith-wpv-shipping-method-form-container yith-wpv-shipping-method-form-container_'.esc_attr( $index ).'" >';

            foreach ( $method as $key => $value ){
                printf( '<input type="hidden" id="%s_%s" class="wc-shipping-zone-method-hidden-data" name="yith_vendor_data[zone_data][%s][zone_shipping_methods][%s][%s]" value="%s"/>', $key, $index, esc_attr( $area_key ), esc_attr( $index ), $key, esc_attr( $value ) );
            }

            $this->print_form_settings( $method_id , $index );

            do_action( 'ypdv_shipping_methods_list_item' , $method_id , $index );

            echo '</div>';


        }

        /**
         * @param $method_id
         * @param $index
         */
        private function print_form_settings( $method_id , $index ) {

            switch ($method_id) {

                case 'flat_rate':

                    $flat_rate =  new WC_Shipping_Flat_Rate( $index );

                    echo $flat_rate->get_admin_options_html();

                    break;

                case 'local_pickup':

                    $local_pickup =  new WC_Shipping_Local_Pickup( $index );

                    echo $local_pickup->get_admin_options_html();

                    break;

                case 'free_shipping':

                    $free_shipping =  new WC_Shipping_Free_Shipping( $index );

                    echo $free_shipping->get_admin_options_html();

                    break;

            }
        }

        /**
         * @param $checkboxes
         * @param $has_limited_access
         * @return array
         */
        public function save_empty_checkboxes( $checkboxes, $has_limited_access ) {
            global $pagenow;

            $query_string = array();
            parse_str( $_REQUEST['_wp_http_referer'], $query_string );

            if( $has_limited_access && ! empty( $query_string['tab'] ) && 'vendor-shipping' == $query_string['tab'] ){
                /* Vendor checkbox options */
                $checkboxes[] = 'enable_shipping';
            }

            return $checkboxes;
        }
    }
}
