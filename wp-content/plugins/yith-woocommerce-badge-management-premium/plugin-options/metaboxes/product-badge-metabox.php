<?php
/**
 * Badge Metabox in Product
 *
 * @package  YITH\BadgeManagementPremium\PluginOptions
 * @author   YITH <plugins@yithemes.com>
 * @since    2.0
 */

return apply_filters( 'yith_wcbm_product_badge_metabox', array(
	'label'    => __( 'Product Badge', 'yith-woocommerce-badges-management' ),
	'pages'    => 'product',
	'class'    => yith_set_wrapper_class(),
	'context'  => 'side',
	'priority' => 'high',
	'tabs'     => array(
		'product-badge-options' => array(
			'label'  => '',
			'fields' => array(
				'badges'              => array(
					'id'              => '_yith_wcbm_badge_ids',
					'label'           => '',
					'extra_row_class' => 'yith-wcbm-badge-select-row',
					'type'            => 'ajax-posts',
					'multiple'        => true,
					'data'            => array(
						'placeholder'          => __( 'Search Badges', 'yith-woocommerce-badges-management' ),
						'post_type'            => YITH_WCBM_Post_Types::$badge,
						'minimum_input_length' => 1,
					),
				),
				'badge-schedule'      => array(
					'id'    => '_yith_wcbm_badge_schedule',
					'label' => __( 'Schedule badge', 'yith-woocommerce-badges-management' ),
					'type'  => 'onoff',
					'std'   => 'no',
				),
				'badge-schedule-from' => array(
					'id'                => '_yith_wcbm_badge_from_date',
					'label'             => '',
					'type'              => 'datepicker',
					'custom_attributes' => array(
						'placeholder' => __( 'From... YYYY-MM-DD', 'yith-woocommerce-badges-management' ),
					),
					'data'              => array(
						'date-format' => 'yy-mm-dd',
					),
					'deps'              => array(
						'id'    => '_yith_wcbm_badge_schedule',
						'value' => 'yes',
						'type'  => 'hide',
					),
				),
				'badge-schedule-to'   => array(
					'id'                => '_yith_wcbm_badge_to_date',
					'label'             => '',
					'type'              => 'datepicker',
					'custom_attributes' => array(
						'placeholder' => __( 'To... YYYY-MM-DD', 'yith-woocommerce-badges-management' ),
					),
					'data'              => array(
						'date-format' => 'yy-mm-dd',
						'min-date'    => 0,
					),
					'deps'              => array(
						'id'    => '_yith_wcbm_badge_schedule',
						'value' => 'yes',
						'type'  => 'hide',
					),
				),
			),
		),
	),
));
