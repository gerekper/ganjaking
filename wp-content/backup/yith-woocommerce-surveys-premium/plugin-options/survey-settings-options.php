<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = array(
	'survey-settings' => array(

		'section_survey_settings' => array(
			'name' => __( 'Survey Option', 'yith-woocommerce-surveys' ),
			'type' => 'title',
			'id'   => 'ywcsur_section_general'
		),

		'survey_thanks_message' => array(
			'name'    => __( 'Thank-you message', 'yith-woocommerce-surveys' ),
			'type'    => 'yith-field',
			'yith-type'    => 'text',
			'default' => __( 'Thanks for voting', 'yith-woocommerce-surveys' ),
			'id'      => 'ywcsur_thanks_message',
			'desc'    => __( 'Works only in Survey, visible in product or via shortcode or widget', 'yith-woocommerce-surveys' ),
			'css'     => 'width:60%;'
		),

		'survey_hide_after_answer' => array(
			'name'      => __( 'Hide survey only after one answer is given. ', 'yith-woocommerce-surveys' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'desc'      => __( 'If enabled, users will no longer be able to see surveys they have answered. Works only in Survey, visible in product or via shortcode or widget', 'yith-woocommerce-surveys' ),
			'id'        => 'ywcsur_hide_after_answer'
		),

		'survey_orderby'              => array(
			'name'      => __( 'Order By', 'yith-woocommerce-surveys' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'date'  => __( 'DATE', 'yith-woocommerce-surveys' ),
				'title' => __( 'NAME', 'yith-woocommerce-surveys' )
			),
			'defaut'    => 'date',
			'id'        => 'ywcsur_orderby'
		),
		'survey_order'                => array(
			'name'      => __( 'Order', 'yith-woocommerce-surveys' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'asc'  => __( 'ASC', 'yith-woocommerce-surveys' ),
				'desc' => __( 'DESC', 'yith-woocommerce-surveys' )
			),
			'default'   => 'asc',
			'id'        => 'ywcsur_order'
		),
		'section_survey_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcsur_section_general_end'
		)

	)
);


return apply_filters( 'yith_wc_survey_settings_options', $options );