<?php
// Porto countdown

add_action( 'vc_after_init', 'porto_load_countdown_shortcode' );

function porto_load_countdown_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	$left               = is_rtl() ? 'right' : 'left';
	vc_map(
		array(
			'name'        => __( 'Porto Countdown', 'porto-functionality' ),
			'base'        => 'porto_countdown',
			'class'       => 'porto_countdown',
			'icon'        => 'far fa-clock',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Countdown Timer.', 'porto-functionality' ),
			'params'      => array_merge(
				array(
					array(
						'type'       => 'dropdown',
						'class'      => '',
						'heading'    => __( 'Countdown Timer Style', 'porto-functionality' ),
						'param_name' => 'count_style',
						'value'      => array(
							__( 'Inline', 'porto-functionality' ) => 'porto-cd-s1',
							__( 'Block', 'porto-functionality' ) => 'porto-cd-s2',
						),
					),
				),
				Porto_Wpb_Dynamic_Tags::get_instance()->dynamic_wpb_tags( 'field', '', 'Format of Fallback should be 2022/1/1.' ),
				array(
					array(
						'type'        => 'datetimepicker',
						'class'       => '',
						'heading'     => __( 'Target Time For Countdown', 'porto-functionality' ),
						'param_name'  => 'datetime',
						'value'       => '',
						'description' => __( 'Date and time format (yyyy/mm/dd hh:mm:ss).', 'porto-functionality' ),
						'dependency'  => array(
							'element'  => 'enable_field_dynamic',
							'is_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'class'      => '',
						'heading'    => __( 'Countdown Timer Depends on', 'porto-functionality' ),
						'param_name' => 'porto_tz',
						'value'      => array(
							__( 'WordPress Defined Timezone', 'porto-functionality' ) => 'porto-wptz',
							__( "User's System Timezone", 'porto-functionality' ) => 'porto-usrtz',
						),
					),
					array(
						'type'       => 'porto_multiselect',
						'heading'    => __( 'Select Time Units To Display In Countdown Timer', 'porto-functionality' ),
						'param_name' => 'countdown_opts',
						'std'        => '',
						'value'      => array(
							__( 'Years', 'porto-functionality' )  => 'syear',
							__( 'Months', 'porto-functionality' ) => 'smonth',
							__( 'Weeks', 'porto-functionality' )  => 'sweek',
							__( 'Days', 'porto-functionality' )   => 'sday',
							__( 'Hours', 'porto-functionality' )  => 'shr',
							__( 'Minutes', 'porto-functionality' ) => 'smin',
							__( 'Seconds', 'porto-functionality' ) => 'ssec',
						),
					),
					$custom_class,
					array(
						'type'       => 'porto_param_heading',
						'text'       => __( 'Texts', 'porto-functionality' ),
						'param_name' => 'countdown_texts',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Year', 'porto-functionality' ),
						'param_name' => 'string_years',
						'value'      => 'Year',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Month', 'porto-functionality' ),
						'param_name' => 'string_months',
						'value'      => 'Month',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Week', 'porto-functionality' ),
						'param_name' => 'string_weeks',
						'value'      => 'Week',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Day', 'porto-functionality' ),
						'param_name' => 'string_days',
						'value'      => 'Day',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Hour', 'porto-functionality' ),
						'param_name' => 'string_hours',
						'value'      => 'Hour',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Minute', 'porto-functionality' ),
						'param_name' => 'string_minutes',
						'value'      => 'Minute',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Second', 'porto-functionality' ),
						'param_name' => 'string_seconds',
						'value'      => 'Second',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_param_heading',
						'text'       => __( 'Texts Plural', 'porto-functionality' ),
						'param_name' => 'countdown_texts_plural',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Years (Plural)', 'porto-functionality' ),
						'param_name' => 'string_years2',
						'value'      => 'Years',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Months (Plural)', 'porto-functionality' ),
						'param_name' => 'string_months2',
						'value'      => 'Months',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),

					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Weeks (Plural)', 'porto-functionality' ),
						'param_name' => 'string_weeks2',
						'value'      => 'Weeks',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),

					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Days (Plural)', 'porto-functionality' ),
						'param_name' => 'string_days2',
						'value'      => 'Days',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Hours (Plural)', 'porto-functionality' ),
						'param_name' => 'string_hours2',
						'value'      => 'Hours',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),

					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Minutes (Plural)', 'porto-functionality' ),
						'param_name' => 'string_minutes2',
						'value'      => 'Minutes',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),

					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Seconds (Plural)', 'porto-functionality' ),
						'param_name' => 'string_seconds2',
						'value'      => 'Seconds',
						'group'      => __( 'Strings Translation', 'porto-functionality' ),
					),
					array(
						'type'             => 'porto_param_heading',
						'text'             => __( 'Timer Digit Settings', 'porto-functionality' ),
						'param_name'       => 'countdown_typograpy',
						'group'            => __( 'Typography', 'porto-functionality' ),
						'edit_field_class' => 'no-top-margin vc_column vc_col-sm-12',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Font Weight', 'porto-functionality' ),
						'param_name' => 'tick_style',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Timer Digit Text Color', 'porto-functionality' ),
						'param_name' => 'tick_col',
						'value'      => '',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Timer Digit Text Size', 'porto-functionality' ),
						'param_name' => 'tick_size',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Timer Digit Text Line height', 'porto-functionality' ),
						'param_name' => 'tick_line_height',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'             => 'porto_param_heading',
						'text'             => __( 'Timer Unit Settings', 'porto-functionality' ),
						'param_name'       => 'countdown_typograpy',
						'group'            => __( 'Typography', 'porto-functionality' ),
						'edit_field_class' => 'no-top-margin vc_column vc_col-sm-12',
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Timer Unit Text Color', 'porto-functionality' ),
						'param_name' => 'tick_sep_col',
						'value'      => '',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Timer Unit Text Size', 'porto-functionality' ),
						'param_name' => 'tick_sep_size',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Timer Unit Font Weight', 'porto-functionality' ),
						'param_name' => 'tick_sep_style',
						'group'      => __( 'Typography', 'porto-functionality' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Text Alignment', 'porto-functionality' ),
						'param_name' => 'countdown_align',
						'group'      => __( 'Counter Box', 'porto-functionality' ),
						'value'      => array(
							__( 'Center', 'porto-functionality' ) => 'center',
							__( 'Left', 'porto-functionality' ) => 'left',
							__( 'Right', 'porto-functionality' ) => 'right',
						),
						'selectors'  => array(
							'{{WRAPPER}}.porto_countdown' => 'text-align: {{VALUE}};',
						),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Align Middle ?', 'porto-functionality' ),
						'description' => __( 'Turn on to make them align middle.', 'porto-functionality' ),
						'param_name'  => 'is_middle',
						'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
						'selectors'   => array(
							'{{WRAPPER}} .porto_countdown-amount, {{WRAPPER}} .porto_countdown-period' => 'vertical-align: middle;',
						),
						'group'       => __( 'Counter Box', 'porto-functionality' ),
						'dependency'  => array(
							'element' => 'count_style',
							'value'   => 'porto-cd-s1',
						),
					),
					array(
						'type'       => 'porto_dimension',
						'heading'    => __( 'Each Countdown Section Padding', 'porto-functionality' ),
						'param_name' => 'section_padding',
						'responsive' => true,
						'selectors'  => array(
							'{{WRAPPER}}.porto_countdown .porto_countdown-section' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
						),
						'group'      => __( 'Counter Box', 'porto-functionality' ),
					),
					array(
						'type'       => 'porto_dimension',
						'heading'    => __( 'Each Countdown Section Margin', 'porto-functionality' ),
						'param_name' => 'section_margin',
						'selectors'  => array(
							'{{WRAPPER}}.porto_countdown .porto_countdown-section' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
						),
						'group'      => __( 'Counter Box', 'porto-functionality' ),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'Spacing Between units and label', 'porto-functionality' ),
						'param_name'  => 'spacing',
						'units'       => array( 'px', 'em' ),
						'group'       => __( 'Counter Box', 'porto-functionality' ),
						'qa_selector' => '.porto_countdown-section:first-child .porto_countdown-period',
						'selectors'   => array(
							'{{WRAPPER}}.porto-cd-s2 .porto_countdown-section .porto_countdown-period' => 'margin-top: {{VALUE}}{{UNIT}};',
							'{{WRAPPER}}.porto-cd-s1 .porto_countdown-section .porto_countdown-period' => 'margin-' . $left . ': {{VALUE}}{{UNIT}};',
						),
					),
					array(
						'type'        => 'porto_number',
						'heading'     => __( 'FlexBox Width', 'porto-functionality' ),
						'description' => __( 'Controls the width of each timer.', 'porto-functionality' ),
						'param_name'  => 'item_width',
						'units'       => array( '%', 'px' ),
						'responsive'  => true,
						'group'       => __( 'Counter Box', 'porto-functionality' ),
						'selectors'   => array(
							'{{WRAPPER}} .porto_countdown-section' => 'width: {{VALUE}}{{UNIT}};',
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'FlexBox Alignment', 'porto-functionality' ),
						'description' => esc_html__( 'Controls the alignment of counter boxes. It is used only when \'Flexbox Width\' option is set.', 'porto-functionality' ),
						'param_name'  => 'flexbox_align',
						'group'       => __( 'Counter Box', 'porto-functionality' ),
						'value'       => array(
							'' => '',
							__( 'Left', 'porto-functionality' ) => 'flex-start',
							__( 'Center', 'porto-functionality' ) => 'center',
							__( 'Right', 'porto-functionality' ) => 'flex-end',
						),
						'selectors'   => array(
							'{{WRAPPER}} .porto_countdown-row' => 'display: flex; flex-wrap: wrap;justify-content: {{VALUE}};',
						),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Timer Box Background Color', 'porto-functionality' ),
						'param_name' => 'box_bg',
						'value'      => '',
						'group'      => __( 'Counter Box', 'porto-functionality' ),
						'selectors'  => array(
							'{{WRAPPER}} .porto_countdown-section' => 'background-color: {{VALUE}};',
						),
					),
					array(
						'type'             => 'css_editor',
						'heading'          => __( 'Css', 'porto-functionality' ),
						'param_name'       => 'css_countdown',
						'group'            => __( 'Design ', 'porto-functionality' ),
						'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
					),
				)
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_countdown extends WPBakeryShortCode {
		}
	}
}
