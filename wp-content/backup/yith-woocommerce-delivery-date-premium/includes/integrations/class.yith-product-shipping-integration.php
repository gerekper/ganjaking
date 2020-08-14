<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_YITH_Product_Shipping' ) ) {

	class YITH_Delivery_Date_YITH_Product_Shipping {

		public function __construct() {

			add_filter( 'woocommerce_settings_api_form_fields_yith_wc_product_shipping_method', array(
				$this,
				'add_custom_fields'
			), 5, 1 );
			add_filter( 'ywcdd_disable_delivery_date_for_shipping_method', array(
				$this,
				'hide_duplicate_rows'
			), 10, 3 );

		}



		public function hide_duplicate_rows( $hide, $key, $shipping_id ) {


			if ( 'yith_wc_product_shipping_method' == $shipping_id ) {
				$hide = true;
			}

			return $hide;
		}

		/* add custom form fields in shipping method
		*
		* @param array $defaults
		* @param array $form_fields
		*
		* @return array
		* @since  1.0.0
		*
		* @author YITHEMES
		*/
		public function add_custom_fields( $form_fields ) {

			$all_processing_method = get_posts( array(
				'post_type'   => 'yith_proc_method',
				'post_status' => 'publish',
				'numberposts' => - 1
			) );

			$options = array();

			$options[''] = __( 'Select a processing method', 'yith-woocommerce-delivery-date' );

			foreach ( $all_processing_method as $key => $method ) {
				$options[ $method->ID ] = get_the_title( $method->ID );
			}


			$form_fields['select_process_method'] = array(

				'title'   => __( 'Processing Method', 'yith-woocommerce-delivery-date' ),
				'type'    => 'select',
				'default' => '',
				'class'   => 'ywcdd_processing_method wc-enhanced-select ',
				'options' => $options,

			);

			$form_fields['set_method_as_mandatory'] = array(

				'title'       => __( 'Set as required', 'yith-woocommerce-delivery-date' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'class'       => 'ywcdd_set_mandatory ',
				'description' => __( 'If enabled, customers must select a date for the delivery', 'yith-woocommerce-delivery-date' ),

			);

			return $form_fields;
		}


	}
}

new YITH_Delivery_Date_YITH_Product_Shipping();