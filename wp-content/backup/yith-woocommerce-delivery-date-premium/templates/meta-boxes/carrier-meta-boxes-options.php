<?php
if( !defined('ABSPATH')){
    exit;
}

$meta_boxes_options = array(
    'label' => __( 'Carrier settings', 'yith-woocommerce-delivery-date' ),
    'pages' => 'yith_carrier', //or array( 'post-type1', 'post-type2')
    'context' => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'tabs' => array(
        'carrier_settings' => array(
            'label' => __( 'Settings', 'yith-woocommerce-delivery-date' ),
            'fields' => array(
                'ywcdd_dayrange' => array(
                    'label' => __( 'Estimated Delivery Day', 'yith-woocommerce-delivery-date' ),
                    'desc' => __('Set the number of days essential to your carrier to deliver the order (Set the value to 0 if the delivery occurs within the same day)','yith-woocommerce-delviery-date'),
                    'type' => 'number-select',
                    'std' => array( array( 'shipping_zone' => 'all', 'day' => 3 ) )
                 ),
                'ywcdd_workday' => array(
                    'label' => _x('Workday', 'workdays', 'yith-woocommerce-delivery-date' ),
                    'desc' => __('Choose the carriers workdays', 'yith-woocommerce-delivery-date' ),
                    'type' => 'select-buttons',
                    'options' => yith_get_worksday(),
                    'data' => array( 'placeholder' => __('Select a day','yith-woocommerce-delivery-date') )
                ),
                'ywcdd_max_selec_orders' => array(
                    'label' => _x('Maximum number of days to select','days that can be selected starting from', 'yith-woocommerce-delivery-date'),
                     'desc' => __('The maximum number of available days to show starting from the first valid delivery date.', 'yith-woocommerce-delivery-date'),
                    'type' => 'number',
                    'std'   =>  30,
                    'min'   =>  0,
                )
            )
        ),
    )
);

return $meta_boxes_options;