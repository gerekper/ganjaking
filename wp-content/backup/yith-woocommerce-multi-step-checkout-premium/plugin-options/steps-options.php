<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$steps = array(
	'login'    => array(
		'label' => esc_html_x( 'Login', 'Frontend: Step title', 'yith-woocommerce-multi-step-checkout' ),
		'icon'  => 'icon'
	),
	'billing'  => array(
		'label' => esc_html_x( 'Billing', 'Frontend: Step title', 'yith-woocommerce-multi-step-checkout' ),
		'icon'  => 'icon'
	),
	'shipping' => array(
		'label' => esc_html_x( 'Shipping', 'Frontend: Step title', 'yith-woocommerce-multi-step-checkout' ),
		'icon'  => 'icon'
	),
	'order'    => array(
		'label' => esc_html_x( 'Order info', 'Frontend: Step title', 'yith-woocommerce-multi-step-checkout' ),
		'icon'  => 'icon'
	),
	'payment'  => array(
		'label' => esc_html_x( 'Payment info', 'Frontend: Step title', 'yith-woocommerce-multi-step-checkout' ),
		'icon'  => 'icon'
	)
);

$icon = esc_html_x( 'Icon', 'Admin: part of label string, i.e. Login Icon, Billing Icon', 'yith-woocommerce-multi-step-checkout' );

$step_styles_images = array(
	'text'   => array(
		'horizontal' => YITH_WCMS_ASSETS_URL . 'images/text-style.jpg',
		'vertical'   => YITH_WCMS_ASSETS_URL . 'images/text-style-vertical.jpg',
	),
	'style1' => array(
		'horizontal' => YITH_WCMS_ASSETS_URL . 'images/style1.jpg',
		'vertical'   => YITH_WCMS_ASSETS_URL . 'images/style1-vertical.jpg',
	),
	'style2' => array(
		'horizontal' => YITH_WCMS_ASSETS_URL . 'images/style2.jpg',
		'vertical'   => YITH_WCMS_ASSETS_URL . 'images/style2-vertical.jpg',
	),
	'style3' => array(
		'horizontal' => YITH_WCMS_ASSETS_URL . 'images/style3.jpg',
		'vertical'   => YITH_WCMS_ASSETS_URL . 'images/style3-vertical.jpg',
	),
	'style4' => array(
		'horizontal' => YITH_WCMS_ASSETS_URL . 'images/style4.jpg',
		'vertical'   => YITH_WCMS_ASSETS_URL . 'images/style4-vertical.jpg',
	),
);

$yith_wcms_timeline_display = get_option( 'yith_wcms_timeline_display', 'horizontal' );

$options = array(

	'steps' => array(

		'timeline_template_options_start' => array(
			'type' => 'sectionstart',
		),

		'timeline_template_options_title' => array(
			'title' => esc_html_x( 'Steps Style', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
			'type'  => 'title',
		),

		'timeline_style_options_type' => array(
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'title'     => esc_html_x( 'Step position', 'Option: Title', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Set if you want to display the steps horizontally or vertically', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_timeline_display',
			'options'   => array(
				'horizontal' => esc_html_x( 'Horizontal', 'Option: timeline display', 'yith-woocommerce-multi-step-checkout' ),
				'vertical'   => esc_html_x( 'Vertical', 'Option: timeline display', 'yith-woocommerce-multi-step-checkout' ),
			),
			'default'   => 'horizontal',
		),

		'timeline_template_options_style_horizontal' => array(
			'type'      => 'yith-field',
			'yith-type' => 'select-images',
			'title'     => esc_html_x( 'Step style', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Choose the step design style to apply', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_timeline_template',
			'default'   => 'style4',
			'options'   => array(
				'text'   => array(
					'label' => esc_html_x( 'Text', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
					'image' => $step_styles_images['text'][ $yith_wcms_timeline_display ],
					'data'  => $step_styles_images['text']
				),
				'style1' => array(
					'label' => esc_html_x( 'Style 1', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
					'image' => $step_styles_images['style1'][ $yith_wcms_timeline_display ],
					'data'  => $step_styles_images['style1']
				),
				'style2' => array(
					'label' => esc_html_x( 'Style 2', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
					'image' => $step_styles_images['style2'][ $yith_wcms_timeline_display ],
					'data'  => $step_styles_images['style2']
				),
				'style3' => array(
					'label' => esc_html_x( 'Style 3', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
					'image' => $step_styles_images['style3'][ $yith_wcms_timeline_display ],
					'data'  => $step_styles_images['style3']
				),
				'style4' => array(
					'label' => esc_html_x( 'Style 4', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
					'image' => $step_styles_images['style4'][ $yith_wcms_timeline_display ],
					'data'  => $step_styles_images['style4']
				),
			),
		),

		'timeline_template_options_style_on_mobile' => array(
			'id'        => 'yith_wcms_timeline_template_on_mobile',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'title'     => esc_html_x( 'Step style on mobile', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Choose the step design style to apply on mobile devices', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
			'options'   => array(
				'text'   => esc_html_x( 'Text', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
				'style1' => esc_html_x( 'Style 1', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
				'style2' => esc_html_x( 'Style 2', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
				'style3' => esc_html_x( 'Style 3', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
				'style4' => esc_html_x( 'Style 4', 'Option: Timeline Style', 'yith-woocommerce-multi-step-checkout' ),
			),
			'default'   => 'style4',
		),

		'timeline_text_option_separator_onoff' => array(
			'title'     => esc_html_x( 'Show a graphic separator between steps', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_wcms_text_step_separator_onoff',
			'desc'      => esc_html_x( 'Enable to show a graphic separator between steps', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => 'yes',
		),


		'timeline_text_option_separator' => array(
			'title'     => esc_html_x( 'Separate step with', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'yith_wcms_text_step_separator',
			'desc'      => esc_html_x( "Enter the separator element for steps. For example, you can use / or -.", 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '/',
			'deps'      => array(
				'id'    => 'yith_wcms_text_step_separator_onoff',
				'value' => 'yes',
			),
		),

		'timeline_option_show_number' => array(
			'title'     => esc_html_x( 'Show the step number', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_wcms_show_step_number',
			'desc'      => esc_html_x( 'Enable to show the step number before the step label', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => 'yes',
		),

		/* === TEXT STYLE === */

		'timeline_step_background_color_text' => array(
			'name'         => esc_html__( 'Step text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set text color for past, current, future and hover step', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_text_step_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#43A08C'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#000000'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#9B9B9B'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#000000',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'text',
				'type'  => 'hide'
			),
		),

		'timeline_step_style1_text_alignment' => array(
			'id'        => 'yith_wcms_timeline_style1_step_text_alignment',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'title'     => esc_html_x( 'Step text alignment', 'Panel: Option title', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Set the text alignment', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'options'   => array(
				'left'   => esc_html_x( 'Left', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
				'center' => esc_html_x( 'Center', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
				'right'  => esc_html_x( 'Right', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
			),
			'deps'      => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style1',
				'type'  => 'hide'
			),
			'default'   => 'left'
		),

		'timeline_step_background_color_style1' => array(
			'name'         => esc_html__( 'Step background color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the background color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style1_step_background_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#40bfa4'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#777777'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#c1c1c1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#777777',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style1',
				'type'  => 'hide'
			),
		),

		'timeline_step_text_color_style1' => array(
			'name'         => esc_html__( 'Step text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the text color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style1_step_text_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#ffffff',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style1',
				'type'  => 'hide'
			),
		),

		'timeline_step_square_color_style1' => array(
			'name'         => esc_html__( 'Square color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the square color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style1_square_background_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#43a08c'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#5c5c5c'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#aaaaaa'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#5c5c5c',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style1',
				'type'  => 'hide'
			),
		),

		'timeline_step_square_text_color_style1' => array(
			'name'         => esc_html__( 'Square text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the square text color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style1_square_text_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#ffffff',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style1',
				'type'  => 'hide'
			),
		),

		'timeline_step_style2_text_alignment' => array(
			'id'        => 'yith_wcms_timeline_style2_step_text_alignment',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'title'     => esc_html_x( 'Step text alignment', 'Panel: Option title', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Set the text alignment', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'options'   => array(
				'left'   => esc_html_x( 'Left', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
				'center' => esc_html_x( 'Center', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
				'right'  => esc_html_x( 'Right', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
			),
			'deps'      => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
			'default'   => 'left'
		),

		'timeline_step_background_color_style2' => array(
			'name'         => esc_html__( 'Step background color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the background color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style2_step_background_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#ffffff',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
		),

		'timeline_step_text_color_style2' => array(
			'name'         => esc_html__( 'Step text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the text color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style2_step_text_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#535353'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#c1c1c1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#535353',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
		),

		'timeline_step_border_color_style2' => array(
			'name'         => esc_html__( 'Step border color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the border color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style2_step_border_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#535353'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#c1c1c1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#535353',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
		),

		'timeline_step_circle_color_style2' => array(
			'name'         => esc_html__( 'Circle color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the circle color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style2_circle_background_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#535353'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#c1c1c1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#535353',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
		),

		'timeline_step_circle_border_color_style2' => array(
			'name'         => esc_html__( 'Circle border color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the circle border color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style2_circle_border_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#535353'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#c1c1c1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#535353',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
		),

		'timeline_step_square_text_color_style2' => array(
			'name'         => esc_html__( 'Circle text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the square text color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style2_circle_text_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#ffffff',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style2',
				'type'  => 'hide'
			),
		),

		'timeline_step_text_alignment' => array(
			'id'        => 'yith_wcms_timeline_style3_step_text_alignment',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'title'     => esc_html_x( 'Step text alignment', 'Panel: Option title', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Set the text alignment', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
			'options'   => array(
				'left'   => esc_html_x( 'Left', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
				'center' => esc_html_x( 'Center', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
				'right'  => esc_html_x( 'Right', 'Admin option: Left alignment', 'yith-woocommerce-multi-step-checkout' ),
			),
			'deps'      => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style3',
				'type'  => 'hide'
			),
			'default'   => 'left'
		),

		'timeline_step_background_color_style3' => array(
			'name'         => esc_html__( 'Step background color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the background color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style3_step_background_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#4b4b4b'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#4b4b4b',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style3',
				'type'  => 'hide'
			),
		),

		'timeline_step_text_color_style3' => array(
			'name'         => esc_html__( 'Step text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the text color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style3_step_text_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#ffffff'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#c1c1c1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#ffffff',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style3',
				'type'  => 'hide'
			),
		),

		'timeline_step_border_color_style3' => array(
			'name'         => esc_html__( 'Step border color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the border color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style3_step_border_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#4b4b4b'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#C1C1C1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#4b4b4b',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style3',
				'type'  => 'hide'
			),
		),

		'timeline_step_text_color_style4' => array(
			'name'         => esc_html__( 'Step text color', 'yith-woocommerce-multi-step-checkout' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => esc_html__( 'Set the text color for the past, current, future and hover steps', 'yith-woocommerce-multi-step-checkout' ),
			'id'           => 'yith_wcms_timeline_style4_step_text_color',
			'colorpickers' => array(
				array(
					'name'    => esc_html__( 'Prev color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'prev',
					'default' => '#3ABFA3'
				),
				array(
					'name'    => esc_html__( 'Current color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'current',
					'default' => '#4b4b4b'
				),
				array(
					'name'    => esc_html__( 'Future color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'future',
					'default' => '#C1C1C1'
				),
				array(
					'name'    => esc_html__( 'Hover color', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'hover',
					'default' => '#4b4b4b',
				),
			),
			'deps'         => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style4',
				'type'  => 'hide'
			),
		),

		'timeline_step_border_color_style4' => array(
			'name'      => esc_html__( 'Step separator color', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => esc_html__( 'Set the color for step separator', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_timeline_style4_step_border_color',
			'default'   => '#707070',
			'deps'      => array(
				'id'    => 'yith_wcms_timeline_template',
				'value' => 'style4',
				'type'  => 'hide'
			),
		),

		'timeline_transition_options_type' => array(
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'title'     => esc_html_x( 'FadeIn and FadeOut Transition speed', 'Option: Title. Please, do not translate FadeIn/FadeOut', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Set the speed of fade animation during transition from one step to the next', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_timeline_fade_duration',
			'option'    => array( 'min' => 0, 'max' => 1000 ),
			'default'   => '200',
			'step'      => 50,
		),

		'timeline_ajax validation' => array(
			'title'     => esc_html_x( 'Activate Ajax Validation', 'Admin option: Enable plugin', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html_x( "If enabled users can't proceed to the next step if they haven't first filled in mandatory fields", 'Admin option description: Enable live validation', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_enable_ajax_validator',
			'default'   => 'no'
		),

		'timeline_disabled_cookie' => array(
			'title'     => esc_html_x( 'Use cookies in checkout', 'Admin option: Enable plugin', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html_x( "Enable to save the checkout fields when users leave the checkout. In this way they will not have to start the checkout process from the very beginning and fill in all the details again.", 'Admin option description: Enable live validation', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_use_cookie',
			'default'   => 'yes'
		),

		'timeline_template_options_end' => array(
			'type' => 'sectionend',
		),

		'steps_options_start' => array(
			'type' => 'sectionstart',
		),

		'steps_options_title' => array(
			'title' => esc_html_x( 'Steps Customization', 'Panel: section title', 'yith-woocommerce-multi-step-checkout' ),
			'type'  => 'title',
		),

		'login_step'        => array(
			'title'               => esc_html__( 'Login', 'yith-woocommerce-multi-step-checkout' ),
			'type'                => 'yith-field',
			'yith-type'           => 'toggle-element-fixed',
			'yith-display-row'    => false,
			'id'                  => 'yith_wcmv_login_settings',
			'onoff_field'         => false,
			'save_single_options' => true,
			'elements'            => array(
				'settings_options_login_label'                    => array(
					'title'   => esc_html__( 'Login label', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enter a label for the login step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_login',
					'default' => esc_html__( 'Login', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'text',
				),
				'settings_options_login_tab_guest_checkout'       => array(
					'title'   => esc_html__( 'Allow guest checkout', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enable this to allow a customer to place an order without an account', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'woocommerce_enable_guest_checkout',
					'default' => 'yes',
					'type'    => 'onoff',
				),
				'settings_options_login_tab_enable_login'         => array(
					'title'   => esc_html__( 'Allow customer login', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enable this to allow the customer to login during the checkout process', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'woocommerce_enable_checkout_login_reminder',
					'default' => 'no',
					'type'    => 'onoff',
				),
				'settings_options_returning_customer_information' => array(
					'title'   => esc_html_x( 'Message for returning customers (not available if you enable the same style as in "My Account", see below).', 'Admin option: text', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'textarea',
					'id'      => 'yith_wcms_form_checkout_login_message',
					'default' => esc_html_x( 'If you already have an account on this site, please enter your credentials below. If you don\'t have an account yet, please go to the billing step.', '[Frontend] Message for returning customer on checkout page', 'yith-woocommerce-multi-step-checkout' ),
					'rows'    => 5,
				),
				'settings_options_login_tab_style'                => array(
					'title'   => esc_html_x( 'Use the "My Account" login/register box', 'Admin: Option title', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'onoff',
					'id'      => 'yith_wcms_timeline_use_my_account_in_login_step',
					'desc'    => esc_html_x( 'Enable to show the "My Account login/register" form instead of the default \'returning customer\' box', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'default' => 'no',
				),
				'settings_options_login_tab_registration'         => array(
					'title'   => esc_html__( 'Enable customer registration with "My Account" Style', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enable the customer registration on the login step.', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'woocommerce_enable_myaccount_registration',
					'default' => 'no',
					'type'    => 'onoff',
				),
				'settings_option_login_tab_icon'                  => array(
					'title'   => esc_html__( 'Login icon', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Choose to hide or set a default or a custom icon, to identify this step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_use_icon_login',
					'default' => 'default-icon',
					'type'    => 'radio',
					'options' => array(
						'no-icon'      => esc_html_x( 'No icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'default-icon' => esc_html_x( 'Use default icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'custom-icon'  => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					)
				),
				'settings_option_login_tab_default_icon'          => array(
					'type'    => 'select',
					'title'   => esc_html_x( 'Default icon', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html_x( 'Choose the default icon for login step', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_default_icon_login',
					'default' => 'login',
					'options' => YITH_Multistep_Checkout()->admin->get_default_icons_list(),
				),
				'settings_option_login_tab_custom_icon'           => array(
					'title'   => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'upload',
					'id'      => 'yith_wcms_timeline_options_icon_login',
					'default' => '',
					'desc'    => esc_html__( 'Upload your custom icon for login step', 'yith-woocommerce-multi-step-checkout' ),
				)
			)
		),
		'billing_step'      => array(
			'title'               => esc_html__( 'Billing', 'yith-woocommerce-multi-step-checkout' ),
			'type'                => 'yith-field',
			'yith-type'           => 'toggle-element-fixed',
			'yith-display-row'    => false,
			'id'                  => 'yith_wcmv_billing_settings',
			'onoff_field'         => false,
			'save_single_options' => true,
			'elements'            => array(
				'settings_options_billing_merge_shipping'         => array(
					'title'   => esc_html__( 'Merge billing and shipping in a single step', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'If enabled, you will have a single step with billing and shipping info', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_merge_billing_and_shipping_step',
					'default' => 'no',
					'type'    => 'onoff',
				),
				'settings_options_billing_label'           => array(
					'title'   => esc_html__( 'Billing label', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enter a label for the billing step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_billing',
					'default' => esc_html__( 'Billing', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'text',
				),
				'settings_option_billing_tab_icon'         => array(
					'title'   => esc_html__( 'Billing icon', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Choose to hide or set a default or a custom icon, to identify this step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_use_icon_billing',
					'default' => 'default-icon',
					'type'    => 'radio',
					'options' => array(
						'no-icon'      => esc_html_x( 'No icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'default-icon' => esc_html_x( 'Use default icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'custom-icon'  => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					)
				),
				'settings_option_billing_tab_default_icon' => array(
					'type'    => 'select',
					'title'   => esc_html_x( 'Default icon', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html_x( 'Choose the default icon for billing step', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_default_icon_billing',
					'default' => 'billing',
					'options' => YITH_Multistep_Checkout()->admin->get_default_icons_list(),
				),
				'settings_option_billing_tab_custom_icon'  => array(
					'title'   => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'upload',
					'id'      => 'yith_wcms_timeline_options_icon_billing',
					'default' => '',
					'desc'    => esc_html__( 'Upload your custom icon for billing step', 'yith-woocommerce-multi-step-checkout' ),
				)
			)
		),
		'shipping_step'     => array(
			'title'               => esc_html__( 'Shipping', 'yith-woocommerce-multi-step-checkout' ),
			'type'                => 'yith-field',
			'yith-type'           => 'toggle-element-fixed',
			'yith-display-row'    => false,
			'id'                  => 'yith_wcmv_shipping_settings',
			'onoff_field'         => false,
			'save_single_options' => true,
			'elements'            => array(
				'settings_options_shipping_tab_hide'        => array(
					'title'   => esc_html_x( 'Hide shipping step', 'Admin: Option title', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'onoff',
					'id'      => 'yith_wcms_timeline_remove_shipping_step',
					'desc'    => esc_html_x( 'Choose to show or hide the shipping step on the checkout page. (For example: you can hide this for digital products)', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'default' => 'no',
				),
				'settings_options_shipping_label'           => array(
					'title'   => esc_html__( 'Shipping label', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enter a label for the shipping step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_shipping',
					'default' => esc_html__( 'Shipping', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'text',
				),
				'settings_option_shipping_tab_icon'         => array(
					'title'   => esc_html__( 'Shipping icon', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Choose to hide or set a default or a custom icon, to identify this step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_use_icon_shipping',
					'default' => 'default-icon',
					'type'    => 'radio',
					'options' => array(
						'no-icon'      => esc_html_x( 'No icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'default-icon' => esc_html_x( 'Use default icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'custom-icon'  => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					)
				),
				'settings_option_shipping_tab_default_icon' => array(
					'type'    => 'select',
					'title'   => esc_html_x( 'Default icon', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html_x( 'Choose the default icon for shipping step', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_default_icon_shipping',
					'default' => 'shipping',
					'options' => YITH_Multistep_Checkout()->admin->get_default_icons_list(),
				),
				'settings_option_shipping_tab_custom_icon'  => array(
					'title'   => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'upload',
					'id'      => 'yith_wcms_timeline_options_icon_shipping',
					'default' => '',
					'desc'    => esc_html__( 'Upload your custom icon for shipping step', 'yith-woocommerce-multi-step-checkout' ),
				)
			)
		),
		'order_info_step'   => array(
			'title'               => esc_html__( 'Order Info', 'yith-woocommerce-multi-step-checkout' ),
			'type'                => 'yith-field',
			'yith-type'           => 'toggle-element-fixed',
			'yith-display-row'    => false,
			'id'                  => 'yith_wcmv_order_info_settings',
			'onoff_field'         => false,
			'save_single_options' => true,
			'elements'            => array(
				'settings_options_payment_merge_order_info'         => array(
					'title'   => esc_html__( 'Merge order info and payment in a single step', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'If enabled, you will have a single step with the order info and payment detail', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_merge_order_and_payment_step',
					'default' => 'no',
					'type'    => 'onoff',
				),
				'settings_options_order_label'           => array(
					'title'   => esc_html__( 'Order Info label', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enter a label for the order info step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_order',
					'default' => esc_html__( 'Order Info', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'text',
				),
				'settings_option_order_tab_icon'         => array(
					'title'   => esc_html__( 'Order info icon', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Choose to hide or set a default or a custom icon, to identify this step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_use_icon_order',
					'default' => 'default-icon',
					'type'    => 'radio',
					'options' => array(
						'no-icon'      => esc_html_x( 'No icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'default-icon' => esc_html_x( 'Use default icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'custom-icon'  => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					)
				),
				'settings_option_order_tab_default_icon' => array(
					'type'    => 'select',
					'title'   => esc_html_x( 'Default icon', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html_x( 'Choose the default icon for order info step', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_default_icon_order',
					'default' => 'order',
					'options' => YITH_Multistep_Checkout()->admin->get_default_icons_list(),
				),
				'settings_option_order_tab_custom_icon'  => array(
					'title'   => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'upload',
					'id'      => 'yith_wcms_timeline_options_icon_order',
					'default' => '',
					'desc'    => esc_html__( 'Upload your custom icon for order info step', 'yith-woocommerce-multi-step-checkout' ),
				)
			)
		),
		'payment_step'      => array(
			'title'               => esc_html__( 'Payment', 'yith-woocommerce-multi-step-checkout' ),
			'type'                => 'yith-field',
			'yith-type'           => 'toggle-element-fixed',
			'yith-display-row'    => false,
			'id'                  => 'yith_wcmv_payment_settings',
			'onoff_field'         => false,
			'save_single_options' => true,
			'elements'            => array(
				'settings_options_last_step_check'         => array(
					'title'   => esc_html_x( 'Show order total amount in Payment tab', 'Admin option: Enable featrues', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html_x( "Choose if want to show the 'order total amount' in this step", '[Admin]Option example', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'onoff',
					'id'      => 'yith_wcms_show_amount_on_payments',
					'default' => 'no'
				),
				'settings_options_last_step_check_text'    => array(
					'title'   => esc_html_x( 'Order total label', 'Admin option: Enable featrues', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'text',
					'desc'    => esc_html_x( 'e.g.: Order total amount: 13,00$ (includes 0,60$ VAT)', '[Admin]Option example', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_show_amount_on_payments_text',
					'default' => esc_html__( 'Order total amount', 'yith-woocommerce-multi-step-checkout' ),
					'deps'    => array(
						'id'    => 'yith_wcms_show_amount_on_payments',
						'value' => 'yes',
						'type'  => 'disable'
					),
				),
				'settings_options_payment_label'           => array(
					'title'   => esc_html__( 'Payment label', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Enter a label for the payment info step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_payment',
					'default' => esc_html__( 'Payment', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'text',
				),
				'settings_option_payment_tab_icon'         => array(
					'title'   => esc_html__( 'Payment icon', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html__( 'Choose to hide or set a default or a custom icon, to identify this step', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_use_icon_payment',
					'default' => 'default-icon',
					'type'    => 'radio',
					'options' => array(
						'no-icon'      => esc_html_x( 'No icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'default-icon' => esc_html_x( 'Use default icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
						'custom-icon'  => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					)
				),
				'settings_option_payment_tab_default_icon' => array(
					'type'    => 'select',
					'title'   => esc_html_x( 'Default icon', 'Option: title', 'yith-woocommerce-multi-step-checkout' ),
					'desc'    => esc_html_x( 'Choose the default icon for payment info step', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
					'id'      => 'yith_wcms_timeline_options_default_icon_payment',
					'default' => 'payment',
					'options' => YITH_Multistep_Checkout()->admin->get_default_icons_list(),
				),
				'settings_option_payment_tab_custom_icon'  => array(
					'title'   => esc_html_x( 'Upload custom icon', 'Admin: option description', 'yith-woocommerce-multi-step-checkout' ),
					'type'    => 'upload',
					'id'      => 'yith_wcms_timeline_options_icon_payment',
					'default' => '',
					'desc'    => esc_html__( 'Upload your custom icon for payment info step', 'yith-woocommerce-multi-step-checkout' ),
				)
			)
		),
		'steps_options_end' => array(
			'type' => 'sectionend',
		),
	)
);

return $options;
