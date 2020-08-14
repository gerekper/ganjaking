<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$all_carrier = YITH_Delivery_Date_Carrier()->get_all_formatted_carriers();
$desc_carrier = __( 'Select a carrier for this processing method', 'yith-woocommerce-delivery-date' );
$extra_desc = '';
$new_post = admin_url('post-new.php');
$params = array('post_type' => 'yith_carrier');
$new_carrier_url = esc_url( add_query_arg( $params, $new_post ) );
$extra_desc = sprintf(' <a href="%s">%s</a>', $new_carrier_url, __('or create one', 'yith-woocommerce-delivery-date' ) );

$meta_boxes_options = array(
	'label'    => __( 'Processing Method settings', 'yith-woocommerce-delivery-date' ),
	'pages'    => 'yith_proc_method', //or array( 'post-type1', 'post-type2')
	'context'  => 'normal', //('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		'processing_method_settings' => array(
			'label'  => __( 'Settings', 'yith-woocommerce-delivery-date' ),
			'fields' => array(
				'ywcdd_minworkday'      => array(
					'label' => __( 'Required Workdays', 'yith-woocommerce-delivery-date' ),
					'desc'  => __( 'Set the minimum number of days required to process an order ( Set 0 if you can ship the package within the day )', 'yith-woocommerce-delivery-date' ),
					'type'  => 'number',
					'std'   => 3,
					'min'   => 0
				),
				'ywcdd_list_day'        => array(
					'label' => __( 'Workday', 'yith-woocommerce-delivery-date' ),
					'desc'  => __( 'Select the days on which you process the orders', 'yith-woocommerce-delivery-date' ),
					'type'  => 'check_list_day'
				),
				'ywcdd_carrier'         => array(
					'label'       => __( 'Select Carrier', 'allows selecting the created carriers', 'yith-woocommerce-delivery-date' ),
					'type'        => 'select-buttons',
					'options'    => $all_carrier,
					'desc'        => $desc_carrier.$extra_desc,
					'placeholder' => __( 'Click to add a carrier', 'yith-woocommerce-delivery-date' ),
					'add_all_button_label' => __( 'Add All Carriers', 'yith-woocommerce-delivery-date' )
				),
				'ywcdd_shipping_method' => array(
					  'label' => __('Select Shipping Method', 'yith-woocommerce-delivery-date'),
					  'type' => 'select-shipping-method'
				)
			)
		),
	)
);

return apply_filters( 'ywcdd_processing_method_metaboxes', $meta_boxes_options );