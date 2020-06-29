<?php
if( !defined('ABSPATH')){
	exit;
}
if( !class_exists('YITH_Donation_Subscription_Module')){

	class  YITH_Donation_Subscription_Module{

		protected static  $instance;

		public function __construct() {

			add_action( 'ywcds_after_widget_amount_field', array($this, 'show_subscription_info' ) );
			add_action( 'ywcds_after_donation_amount', array($this, 'show_subscription_info' ) );
		}

		/**
		 * @return YITH_Donation_Subscription_Module
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function show_subscription_info(){
			/**
			 * @var YITH_WC_Donations_Premium $YITH_Donations
			 */
			global $YITH_Donations;
			if( !is_null( $YITH_Donations)) {
				$donation_id =$YITH_Donations->get_donation_id();
				$product = wc_get_product( $donation_id );

				if(  function_exists('YITH_WC_Subscription') && YITH_WC_Subscription()->is_subscription( $donation_id ) ){

					$signup_fee               =  $product->get_meta( '_ywsbs_fee' );
					$trial_period             =  $product->get_meta(  '_ywsbs_trial_per' );
					$trial_time_option        =  $product->get_meta(  '_ywsbs_trial_time_option' );
					$price_is_per             =  $product->get_meta(  '_ywsbs_price_is_per' );
					$price_time_option        =  $product->get_meta(  '_ywsbs_price_time_option' );
					$max_length               =  $product->get_meta(  '_ywsbs_max_length' );
					$currency = get_woocommerce_currency();
					$price_time_option_string = ywsbs_get_price_per_string( $price_is_per, $price_time_option );
					$price_html    = '<span class="price_time_opt"> / ' . $price_time_option_string . '</span>';

					if( ( $max_length && get_option( 'ywsbs_show_length_period' ) == 'yes' ) || ( $signup_fee && get_option( 'ywsbs_show_fee' ) == 'yes' ) || ( $trial_period && get_option( 'ywsbs_show_trial_period' ) == 'yes' ) ) {
						if ( $max_length && get_option( 'ywsbs_show_length_period' ) == 'yes' ) {
							$price_html .= __( ' for ', 'yith-woocommerce-subscription' ) . ywsbs_get_price_per_string( $max_length, $price_time_option, true );
						}

						$and   = false;
						$price_html .= ( $signup_fee || $trial_period ) ? __( '<span class="ywsbs-price-detail"> + ', 'yith-woocommerce-subscription' ) : '';

						if ( $signup_fee && get_option( 'ywsbs_show_fee' ) == 'yes' ) {
							$price_html .= $and ? __( ' and ', 'yith-woocommerce-subscription' ) : '';
							$price_html .= apply_filters( 'ywsbs_signup_fee_label', __( ' a sign-up fee of ', 'yith-woocommerce-subscription' ) ) . wc_price( $signup_fee, array( 'currency' => $currency ) );
							$and   = true;
						}

						$t = ywsbs_get_price_per_string( $trial_period, $trial_time_option, true );

						if ( $trial_period && get_option( 'ywsbs_show_trial_period' ) == 'yes' ) {
							$price_html .= $and ? __( ' and ', 'yith-woocommerce-subscription' ) : '';
							$price_html .= __( ' a free trial of ', 'yith-woocommerce-subscription' ) . $t;
						}

						$price_html .= ( $signup_fee || $trial_period ) ? '</span>' : '';
					}

					echo $price_html;

				}
			}
		}
	}
}

YITH_Donation_Subscription_Module::get_instance();