<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$desc_tip = sprintf( '%s <ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s </li></ul>',
	__( 'You can use these placeholders', 'yith-woocommerce-sequential-order-number' ),
	__( '[D] Day without leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[DD] Day with leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[M] Month without leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[MM] Month with leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[YY] two-digit year', 'yith-woocommerce-sequential-order-number' ),
	__( '[YYYY] Full year', 'yith-woocommerce-sequential-order-number' ),
	__( '[h] 24-hour format of an hour without leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[hh] 24-hour format of an hour with leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[m] Minutes with leading zeros', 'yith-woocommerce-sequential-order-number' ),
	__( '[s] Seconds, with leading zeros', 'yith-woocommerce-sequential-order-number' )
);

$desc_quote = sprintf( '<span class="description">%s<br/>%s <a href="%s" target="_blank">%s</a> %s </span>',
	__( 'If you enable this option, you can use a different numeration for your quotes.', 'yith-woocommerce-sequential-order-number' ),
	_x( 'This option is available if', 'This option is available if YITH WooCommerce Request a Quote Premium version 1.5.6 or later is activated', 'yith-woocommerce-sequential-order-number' ),
	'://yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/',
	__( 'YITH WooCommerce Request a Quote Premium', 'yith-woocommerce-sequential-order-number' ),
	__( '(version 1.5.7 or later) is activated', 'yith-woocommerce-sequential-order-number' ) );
$settings   = array(

	'general' => array(
		'section_module_settings' => array(
			'name' => __( 'Plugin Modules', 'yith-woocommerce-sequential-order-number' ),
			'type' => 'title'
		),
		'base_module'              => array(
			'title'            => __( 'Base Module', 'yith-woocommerce-sequential-order-number' ),
			'type'             => 'yith-field',
			'yith-type'        => 'toggle-element-fixed',
			'yith-display-row' => false,
			'id'               => 'ywson_base_module_settings',
			'default'          => array( 'enabled' => 'yes' ),
			'elements'         => array(
				array(
					'title'             => __( 'Numeration starting from', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'number',
					'desc'              => __( 'Set the starting number for order numeration', 'yith-woocommerce-sequential-order-number' ),
					'id'                => 'order_number',
					'default'           => 1,
					'custom_attributes' => "min=1 step=1 required",
				),
				array(
					'title'             => __( 'Order prefix', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'text',
					'desc'              => sprintf( '%s <br> %s', __( 'Set a text to be used as prefix for order numbers.', 'yith-woocommerce-sequential-order-number' ), $desc_tip ),
					'id'                => 'order_prefix',
					'custom_attributes' => 'placeholder="Ex: YWSON-"',
					'default'           => '',
				),
				array(
					'title'             => __( 'Order suffix', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'text',
					'desc'              => sprintf( '%s <br> %s', __( 'Set a text to be used as suffix for order numbers.', 'yith-woocommerce-sequential-order-number' ), $desc_tip ),
					'id'                => 'order_suffix',
					'custom_attributes' => 'placeholder="Ex: YWSON-"',
					'default'           => '',
				)
			)

		),
		'free_module'              => array(
			'title'            => __( 'Free Order Module', 'yith-woocommerce-sequential-order-number' ),
			'type'             => 'yith-field',
			'yith-type'        => 'toggle-element-fixed',
			'yith-display-row' => false,
			'id'               => 'ywson_free_module_settings',
			'elements'         => array(
				array(
					'title'             => __( 'Numeration starting from:', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'number',
					'desc'              => __( 'Set the starting number for free order numeration', 'yith-woocommerce-sequential-order-number' ),
					'id'                => 'order_number',
					'default'           => 1,
					'custom_attributes' => "min=1 step=1 required",
				),
				array(
					'title'             => __( 'Free order prefix', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'text',
					'id'                => 'order_prefix',
					'desc'              => sprintf( '%s <br> %s', __( 'Set a text to be used as prefix for free order numbers.', 'yith-woocommerce-sequential-order-number' ), $desc_tip ),
					'custom_attributes' => 'placeholder="Ex: YWSON-"',
					'default'           => '',
				),
				array(
					'title'             => __( 'Free order suffix', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'text',
					'id'                => 'order_suffix',
					'desc'              => sprintf( '%s <br> %s', __( 'Set a text to be used as suffix for free order numbers.', 'yith-woocommerce-sequential-order-number' ), $desc_tip ),
					'custom_attributes' => 'placeholder="Ex: YWSON-"',
				),
				array(
					'title' => __( 'Set your free order type', 'yith-woocommerce-sequential-order-number' ),
					'type'  => 'select',
					'class' => 'wc-enhanced-select',
					'id'    => 'order_type',
					'desc'  => sprintf( '%s<br/><b>%s<br/>%s<b/>',
						__( 'With this option, you can choose in which way orders have to be recognized as free.', 'yith-woocommerce-sequential-order-number' ),
						__( 'Order total: your order is free if the total is 0 (coupon and shipping included).', 'yith-woocommerce-sequential-order-number' ),
						__( 'Order products: your order is free only if all products it contains are free (coupon and shipping excluded).', 'yith-woocommerce-sequential-order-number' ) ),

					'options' => array(
						'order_tot'   => __( 'Order Total', 'yith-woocommerce-sequential-order-number' ),
						'product_ord' => __( 'Products in Order', 'yith-woocommerce-sequential-order-number' )
					),
					'default' => 'order_tot',
				)
			)
		),
		'quote_module'             => array(
			'title'            => __( 'Quote Module', 'yith-woocommerce-sequential-order-number' ),
			'subtitle' => __('Available only with Yith WooCommerce Request a quote Premium', 'yith-woocommerce-sequential-order-number' ),
			'type'             => 'yith-field',
			'class'            => ! defined( 'YITH_YWRAQ_PREMIUM' ) ? 'yith-disabled' : '',
			'yith-type'        => 'toggle-element-fixed',
			'yith-display-row' => false,
			'id'               => 'ywson_quote_module_settings',
			'elements'         => array(
				array(
					'title'             => __( 'Numeration starting from:', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'number',
					'desc'              => __( 'Set the starting number for quote numeration', 'yith-woocommerce-sequential-order-number' ),
					'id'                => 'order_number',
					'default'           => 1,
					'custom_attributes' => "min=1 step=1 required",
				),
				array(
					'title'             => __( 'Quote prefix', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'text',
					'id'                => 'order_prefix',
					'desc'              => sprintf( '%s <br> %s', __( 'Set a text to be used as prefix for quote numbers.', 'yith-woocommerce-sequential-order-number' ), $desc_tip ),
					'custom_attributes' => 'placeholder ="Ex: YWSON_QUOTE-"',
					'default'           => '',
				),

				array(
					'title'             => __( 'Quote suffix', 'yith-woocommerce-sequential-order-number' ),
					'type'              => 'text',
					'id'                => 'order_suffix',
					'desc'              => sprintf( '%s <br> %s', __( 'Set a text to be used as suffix for quote numbers.', 'yith-woocommerce-sequential-order-number' ), $desc_tip ),
					'custom_attributes' => 'placeholder="Ex: -YWSON_QUOTE"',
					'default'           => '',
				)
			)
		),
		'end_module_settings'     => array(
			'type' => 'sectionend',
		),
	)

);

return apply_filters( 'ywson_general_options', $settings );
