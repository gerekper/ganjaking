<?php
if ( ! class_exists( 'YITH_Delivery_Date_WC_Advanced_Shipping' ) ) {

	class YITH_Delivery_Date_WC_Advanced_Shipping {

		public function __construct() {
			add_filter( 'ywcdd_get_shipping_method_option', array( $this, 'get_table_rate_option_name' ), 10, 2 );
			add_filter( 'woocommerce_settings_api_form_fields_advanced_shipping', array(
				$this,
				'add_custom_fields'
			), 99, 1 );

		}

		public function get_table_rate_option_name( $shipping_option, $option_name ) {


			if ( empty( $shipping_option ) ) {
				$shipping_option = get_option( 'woocommerce_advanced_shipping_settings', array() );


			}

			return $shipping_option;
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
				'class'   => 'ywcdd_processing_method wc-enhanced-select',
				'options' => $options,
			);

			$form_fields['set_method_as_mandatory'] = array(
				'title'       => __( 'Set as required', 'yith-woocommerce-delivery-date' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'class'       => 'ywcdd_set_mandatory',
				'description' => __( 'If enabled, customers must select a date for the delivery', 'yith-woocommerce-delivery-date' )
			);

			return $form_fields;
		}


	}
}

new YITH_Delivery_Date_WC_Advanced_Shipping();