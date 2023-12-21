<?php
/**
 * Chart widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Elementor\Widget\Shipping_Bar;

defined( 'ABSPATH' ) || die();


class Shippingbar_Uitls {

    public function __construct(){
        //
    }

    /**
     * define get message
     * @param 
     *
     * return string message
     */
    public static function ha_get_message($shipping_zone='', $order_min_amount='', $achieve_amount='', $announcement_message='', $success_message='') {
        $message = 'Plese set your store\'s free shipping zone first!!';
        
        if( !empty( $shipping_zone ) ) {
            if( ($achieve_amount > 0 ) && ($order_min_amount > 0) && ($achieve_amount >= $order_min_amount) && !empty($success_message) ) {
                $message = $success_message;
            }else if( ($achieve_amount < $order_min_amount) && !empty( $announcement_message ) ) {
                $message = $announcement_message;
            }
        }

        return $message;
        
    }
    
    /**
     * define get cart subtotal
     * @param 
     *
     * return float subtotal amount
     */
    public static function ha_get_cart_subtotal() {
        $subTotalAmount = 0;

        if( class_exists( 'WooCommerce' ) ) {
            if( isset( WC()->cart ) && !empty( WC()->cart ) ) {
                $subTotalAmount = WC()->cart->get_displayed_subtotal();
            }
        }

        return $subTotalAmount;
        
    }

    /**
     * define cal progress
     * 
     * @param int $targetAmount
     * @param int $achieveAmount
     * @param string $type
     * 
     * return float Progress Percent
     */
    public static function ha_cal_progress($targetAmount, $achieveAmount, $type = 'percent') {
		$progress_persent = 0;
		
		if( $achieveAmount >= $targetAmount ){
			$progress_persent = 100;

		} else {

			if( $targetAmount == 0 ){
				$progress_persent = 0;
			} else {
				$progress_persent = round( ( $achieveAmount * 100 ) / $targetAmount, 2 );
			}
		}

		return $progress_persent;
	}

    /**
     * define ha_get_default_shipping_zone
     * @param 
     *
     * return $zones[]
     *
     */
    public static function ha_get_default_shipping_zone() {
        $values = [];
        if( class_exists( 'WooCommerce' ) ){
            if( class_exists( 'WC_Shipping_Zones' ) ) {
                $shipping_zones = WC()->shipping->get_shipping_methods();
                foreach($shipping_zones as $shipping_zone) {
                    if( Shippingbar_Uitls::ha_check_available_zone($shipping_zone->id) ) {
                        $values[$shipping_zone->id]  = $shipping_zone->method_title;
                    }
                }
            }
            
        }

        return $values;  

    }

    /**
     * define check avaliable shipping zone
     * @param 
     *
     * return boolean
     *
     */
    public static function ha_check_available_zone( $shipping_zone_id = '' ) {
        global $wpdb;
        $wfspb_query = $wpdb->prepare( 
                                        "SELECT * 
                                            FROM {$wpdb->prefix}woocommerce_shipping_zone_methods 
                                            WHERE method_id = %s AND is_enabled = %d
                                            ORDER BY method_order ASC", 
                                            $shipping_zone_id, 1 
                                            
                                        );
        $zone_data   = $wpdb->get_results( $wfspb_query, OBJECT );

        if ( empty( $zone_data ) ) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * get minimum order amount
     * 
     * @since 1.0.0
	 * @access private
	 *
	 * @return float minimum order amount.
     */
    public static function ha_get_minimum_order_amount($shipping_zone) {
        $order_min_amount = 0;
        if( !class_exists( 'WooCommerce' ) ){
            return $order_min_amount;
        }
        $available_freeshipping = Shippingbar_Uitls::ha_check_available_freeshipping($shipping_zone);

        if ( $available_freeshipping ) {

            foreach( $available_freeshipping as $shipping_class ) {
                $first_zone       = $shipping_class;
                $instance_id      = $first_zone->instance_id;
                $method_id        = $first_zone->method_id;
                $arr_method       = array( $method_id, $instance_id );
                $implode_method   = implode( "_", $arr_method );
                $free_option      = 'woocommerce_' . $implode_method . '_settings';
                $free_shipping_s  = get_option( $free_option );

                if(isset($free_shipping_s['min_amount']) && $free_shipping_s['min_amount'] > 0 && isset($free_shipping_s['requires']) && ($free_shipping_s['requires'] == "min_amount" || $free_shipping_s['requires'] == "either"  || $free_shipping_s['requires'] == "both" )){
                    $order_min_amount = $free_shipping_s['min_amount'];
                }elseif(!isset($free_shipping_s['requires']) || $free_shipping_s['requires'] == ""){
                    $order_min_amount = 0;
                }

            }

        } 

        return $order_min_amount;

    }

    public static function ha_check_available_freeshipping( $selected_shipping_zone = 'free_shipping' ) {
        
        global $wpdb;
        $fsbQuery = $wpdb->prepare( 
                                "SELECT * 
                                    FROM {$wpdb->prefix}woocommerce_shipping_zone_methods 
                                    WHERE method_id = %s AND is_enabled = %d
                                    ORDER BY method_order ASC", 
                                    $selected_shipping_zone, 
                                    1,  
                            );
        $zoneData   = $wpdb->get_results( $fsbQuery, OBJECT );

        if ( empty( $zoneData ) ) {
            return false;
        } else {
            return $zoneData;
        }
    }

    /**
     * calculate progress percent
     * 
     * @since 1.0.0
	 * @access private
	 *
	 * @return float progress percent.
     */
    protected static function ha_calculate_progress_percent() {
        $progress_persent = 0;
        if( !class_exists( 'WooCommerce' ) ){
            return $progress_persent;
        }

        $progress_persent = 0;
        $targetAmount   = Shippingbar_Uitls::ha_get_minimum_order_amount();
        $achieveAmount  = isset( WC()->cart ) ? WC()->cart->get_displayed_subtotal() : 0;

        if ( ! empty( WC()->cart ) && WC()->cart->display_prices_including_tax() ) {
            $achieveAmount = $achieveAmount - WC()->cart->get_discount_tax();
        }
        if ( 'no' === $is_discount ) {
            $achieveAmount = $achieveAmount - WC()->cart->get_discount_total();
        }

        $achieveAmount = round( $achieveAmount, wc_get_price_decimals() );
        
        if( ( $achieveAmount >= $targetAmount ) && $targetAmount > 0 ){
            $progress_persent = 100;
        }else{
            if( $targetAmount == 0 ){
                $progress_persent = 0;
            }else{
                $progress_persent = round( ( $achieveAmount * 100 ) / $targetAmount, 2 );
            }
        }
    }

}

new Shippingbar_Uitls;