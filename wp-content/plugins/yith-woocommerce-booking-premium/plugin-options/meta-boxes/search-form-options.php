<?php
/**
 * Search Form Options meta-box.
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit();

$colors = yith_wcbk_get_colors();

$options = array(
	'style' => array(
		'label'  => __( 'Style', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'layout'                      => array(
				'label'   => __( 'Layout', 'yith-booking-for-woocommerce' ),
				'desc'    => __( 'Choose between a vertical or horizontal layout for the form.', 'yith-booking-for-woocommerce' ),
				'type'    => 'radio',
				'options' => array(
					'vertical'   => __( 'Vertical', 'yith-booking-for-woocommerce' ),
					'horizontal' => __( 'Horizontal', 'yith-booking-for-woocommerce' ),
				),
				'std'     => 'vertical',
			),
			'show-results'                => array(
				'label'   => __( 'Show results in', 'yith-booking-for-woocommerce' ),
				'desc'    => __( 'Select where you want to show results.', 'yith-booking-for-woocommerce' ),
				'type'    => 'radio',
				'options' => array(
					'popup' => __( 'Popup', 'yith-booking-for-woocommerce' ),
					'shop'  => __( 'Shop Page', 'yith-booking-for-woocommerce' ),
				),
				'std'     => 'popup',
			),
			'colors'                      => array(
				'label'        => __( 'Colors', 'yith-booking-for-woocommerce' ),
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => __( 'Background', 'yith-booking-for-woocommerce' ),
						'default' => 'transparent',
					),
					array(
						'id'      => 'text',
						'name'    => __( 'Text', 'yith-booking-for-woocommerce' ),
						'default' => '#333333',
					),
				),
			),
			'search-button-colors'        => array(
				'label'        => __( 'Search button colors', 'yith-booking-for-woocommerce' ),
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => __( 'Background', 'yith-booking-for-woocommerce' ),
						'default' => $colors['primary'],
					),
					array(
						'id'      => 'text',
						'name'    => __( 'Text', 'yith-booking-for-woocommerce' ),
						'default' => $colors['primary-contrast'],
					),
					array(
						'id'      => 'background-hover',
						'name'    => __( 'Background hover', 'yith-booking-for-woocommerce' ),
						'default' => $colors['primary-light'],
					),
					array(
						'id'      => 'text-hover',
						'name'    => __( 'Text hover', 'yith-booking-for-woocommerce' ),
						'default' => $colors['primary-contrast'],
					),
				),
			),
			'search_button_border_radius' => array(
				'label'      => __( 'Search button border radius', 'yith-booking-for-woocommerce' ),
				'type'       => 'dimensions',
				'dimensions' => array(
					'top-left'     => __( 'Top-Left', 'yith-booking-for-woocommerce' ),
					'top-right'    => __( 'Top-Right', 'yith-booking-for-woocommerce' ),
					'bottom-right' => __( 'Bottom-Right', 'yith-booking-for-woocommerce' ),
					'bottom-left'  => __( 'Bottom-Left', 'yith-booking-for-woocommerce' ),
				),
				'units'      => array(
					'px' => 'px',
				),
				'std'        => array(
					'dimensions' => array(
						'top-left'     => 5,
						'top-right'    => 5,
						'bottom-right' => 5,
						'bottom-left'  => 5,
					),
					'unit'       => 'px',
					'linked'     => 'yes',
				),
			),
		),
	),
);

return apply_filters( 'yith_wcbk_search_form_options_metabox_fields', $options );
