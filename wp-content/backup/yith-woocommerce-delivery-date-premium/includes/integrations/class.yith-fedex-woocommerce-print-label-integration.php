<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_WC_FedEx_PrintLabel' ) ) {

	class YITH_Delivery_Date_WC_FedEx_PrintLabel {

		public function __construct() {
			add_filter( 'ywcdd_get_shipping_method_option', array( $this, 'get_fedex_option_name' ), 10, 2 );
			add_filter( 'woocommerce_settings_api_form_fields_wf_fedex_woocommerce_shipping', array(
				$this,
				'add_custom_fields'
			), 5, 1 );
			add_filter( 'ywcdd_disable_delivery_date_for_shipping_method', array(
				$this,
				'hide_duplicate_rows'
			), 10, 3 );

		}

		public function get_fedex_option_name( $shipping_option, $option_name ) {


			if ( empty( $shipping_option ) ) {
				$delimiter = ':';

				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					$delimiter = '_';
				}

				$option = explode( $delimiter, $option_name );

				if ( ! empty( $option[0] ) && strpos( $option[0], 'wf_fedex_woocommerce_shipping' ) !== false  ) {
					$shipping_option = get_option( 'woocommerce_wf_fedex_woocommerce_shipping_settings' );
				}


			}

			return $shipping_option;
		}


		public function hide_duplicate_rows( $hide, $key, $shipping_id ) {

			if ( 'wf_fedex_woocommerce_shipping' == $shipping_id ) {
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
				'class'   => 'ywcdd_processing_method wc-enhanced-select fedex_general_tab',
				'options' => $options,

			);

			$form_fields['set_method_as_mandatory'] = array(

				'title'       => __( 'Set as required', 'yith-woocommerce-delivery-date' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'class'       => 'ywcdd_set_mandatory fedex_general_tab',
				'description' => __( 'If enabled, customers must select a date for the delivery', 'yith-woocommerce-delivery-date' ),

			);

			return $form_fields;
		}


	}
}

new YITH_Delivery_Date_WC_FedEx_PrintLabel();