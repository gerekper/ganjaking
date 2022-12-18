<?php
// Porto Schedule Timeline Container
add_action( 'vc_after_init', 'porto_load_schedule_timeline_container_shortcode' );

function porto_load_schedule_timeline_container_shortcode() {
	$custom_class = porto_vc_custom_class();
	vc_map(
		array(
			'name'            => 'Porto ' . esc_html__( 'Steps', 'porto-functionality' ),
			'base'            => 'porto_schedule_timeline_container',
			'category'        => esc_html__( 'Porto', 'porto-functionality' ),
			'description'     => esc_html__( 'Show schedules by beautiful timeline, histories or steps', 'porto-functionality' ),
			'icon'            => 'far fa-calendar',
			'as_parent'       => array( 'only' => 'porto_schedule_timeline_item' ),
			'content_element' => true,
			'controls'        => 'full',
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => array(
						esc_html__( 'Schedule', 'porto-functionality' ) => 'schedule',
						esc_html__( 'History', 'porto-functionality' )  => 'history',
						esc_html__( 'Step', 'porto-functionality' )     => 'step',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'std'         => esc_html__( 'Title', 'porto-functionality' ),
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Subtitle', 'porto-functionality' ),
					'param_name' => 'subtitle',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Circle Type', 'porto-functionality' ),
					'description' => esc_html__( 'This changes the background color of the wrapper which contains title & sub title.', 'porto-functionality' ),
					'param_name'  => 'circle_type',
					'value'       => array(
						esc_html__( 'Filled', 'porto-functionality' ) => 'filled',
						esc_html__( 'Simple', 'porto-functionality' ) => 'simple',
					),
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => esc_html__( 'Is Horizontal?', 'porto-functionality' ),
					'description' => esc_html__( 'Default layout is vertical.', 'porto-functionality' ),
					'param_name'  => 'is_horizontal',
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
				),
				$custom_class,

				array(
					'type'       => 'porto_typography',
					'heading'    => esc_html__( 'Title Typography', 'porto-functionality' ),
					'param_name' => 'title_typography',
					'selectors'  => array(
						'{{WRAPPER}} .step-title',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Steps Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Title Color', 'porto-functionality' ),
					'param_name' => 'title_color',
					'selectors'  => array(
						'{{WRAPPER}} .step-title' => 'color: {{VALUE}} !important;',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Steps Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => esc_html__( 'Sub Title Typography', 'porto-functionality' ),
					'param_name' => 'subtitle_typography',
					'selectors'  => array(
						'{{WRAPPER}} .step-subtitle',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Steps Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Sub Title Color', 'porto-functionality' ),
					'param_name' => 'subtitle_color',
					'selectors'  => array(
						'{{WRAPPER}} .step-subtitle' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Steps Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Line Color', 'porto-functionality' ),
					'param_name' => 'line_clr',
					'selectors'  => array(
						'{{WRAPPER}} .timeline-balloon:before, {{WRAPPER}} .process-step-circle:before, {{WRAPPER}} .process-step-circle:after, {{WRAPPER}}.timeline:after' => 'background-color: {{VALUE}}; opacity: 1;',
						'{{WRAPPER}} .process-horizontal .process-step:before' => 'background-color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Steps Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'Line Width (px)', 'porto-functionality' ),
					'param_name' => 'line_width',
					'min'        => 0,
					'max'        => 10,
					'selectors'  => array(
						'{{WRAPPER}}' => '--porto-step-line-width: {{VALUE}}px;',
						'{{WRAPPER}}.timeline:after' => 'width: {{VALUE}}px;',
					),
					'group'      => esc_html__( 'Steps Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => esc_html__( 'Image / Icon Size', 'porto-functionality' ),
					'param_name' => 'item_img_sz',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .balloon-content .balloon-photo' => 'width: {{VALUE}}{{UNIT}};height: {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'Icon Font Size (px)', 'porto-functionality' ),
					'param_name' => 'item_icon_fs',
					'min'        => 0,
					'max'        => 64,
					'selectors'  => array(
						'{{WRAPPER}} .balloon-content .balloon-photo' => 'font-size: {{VALUE}}px;',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Icon Text Color', 'porto-functionality' ),
					'param_name' => 'item_icon_clr',
					'selectors'  => array(
						'{{WRAPPER}} .balloon-content .balloon-photo' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'Circle Size (px)', 'porto-functionality' ),
					'param_name' => 'item_circle_wd',
					'min'        => 0,
					'max'        => 200,
					'selectors'  => array(
						'{{WRAPPER}} .process-step .process-step-circle' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
						'{{WRAPPER}} .process-step-circle:before, {{WRAPPER}} .process-step-circle:after' => 'left: calc( {{VALUE}}px / 2 - var(--porto-step-line-width, 2px) / 2 - var(--porto-step-circle-bw, 2px) );',
						'{{WRAPPER}} .process-step-circle:before' => 'height: calc( {{VALUE}}px - var(--porto-step-circle-bw, 2px) );',
						'{{WRAPPER}} .process-step-circle:after' => 'top: calc( {{VALUE}}px - var(--porto-step-circle-bw, 2px) );',
						'{{WRAPPER}} .process-horizontal .process-step:before' => 'top: calc( {{VALUE}}px / 2 - var(--porto-step-line-width, 2px) / 2 );',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => esc_html__( 'Circle Margin', 'porto-functionality' ),
					'param_name' => 'item_circle_mg',
					'selectors'  => array(
						'{{WRAPPER}} .process-step-circle' => 'margin-top:{{TOP}};margin-right:{{RIGHT}};margin-bottom:{{BOTTOM}};margin-left:{{LEFT}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'Circle Font Size (px)', 'porto-functionality' ),
					'param_name' => 'item_circle_fs',
					'min'        => 0,
					'max'        => 50,
					'selectors'  => array(
						'{{WRAPPER}} .process-step-circle' => 'font-size: {{VALUE}}px;',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Circle Text Color', 'porto-functionality' ),
					'param_name' => 'item_circle_clr',
					'selectors'  => array(
						'{{WRAPPER}} .process-step-circle' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'Circle Border Width (px)', 'porto-functionality' ),
					'param_name' => 'item_circle_bw',
					'min'        => 0,
					'max'        => 10,
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .process-step-circle' => '--porto-step-circle-bw: {{VALUE}}px;',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Circle Border Color', 'porto-functionality' ),
					'param_name' => 'item_circle_bc',
					'selectors'  => array(
						'{{WRAPPER}} .process-step-circle' => 'border-color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'step' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => esc_html__( 'Title Typography', 'porto-functionality' ),
					'param_name' => 'item_title_tg',
					'selectors'  => array(
						'{{WRAPPER}} .step-item-title',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Title Color', 'porto-functionality' ),
					'param_name' => 'item_title_clr',
					'selectors'  => array(
						'{{WRAPPER}} .step-item-title' => 'color: {{VALUE}} !important;',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'param_name' => 'item_subtitle_tg',
					'heading'    => esc_html__( 'Time Text Typography', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .step-item-subtitle',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule', 'history' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Time Text Color', 'porto-functionality' ),
					'param_name' => 'item_subtitle_clr',
					'selectors'  => array(
						'{{WRAPPER}} .step-item-subtitle' => 'color: {{VALUE}} !important;',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule', 'history' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'param_name' => 'item_desc_tg',
					'heading'    => esc_html__( 'Description Typography', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .process-step-desc, {{WRAPPER}} .process-step-desc p, {{WRAPPER}} .timeline-item-content',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Description Color', 'porto-functionality' ),
					'param_name' => 'item_desc_clr',
					'selectors'  => array(
						'{{WRAPPER}} .process-step-desc, {{WRAPPER}} .process-step-desc p, {{WRAPPER}} .timeline-item-content' => 'color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => esc_html__( 'Spacing between Icon & Text', 'porto-functionality' ),
					'param_name' => 'item_sp_icon_text',
					'units'      => array( 'px', 'rem' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule', 'history' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .balloon-content .balloon-photo' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{VALUE}}{{UNIT}};',
						'{{WRAPPER}} .timeline-item-title' => 'margin-top: {{VALUE}}{{UNIT}};',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => esc_html__( 'Spacing between Title & Sub Title', 'porto-functionality' ),
					'param_name' => 'item_sp_title',
					'units'      => array( 'px', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .step-item-title, {{WRAPPER}} .timeline-item-title' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Background Color', 'porto-functionality' ),
					'param_name' => 'item_bgc',
					'selectors'  => array(
						'{{WRAPPER}} .timeline-balloon .balloon-content' => 'background-color: {{VALUE}} !important;',
						'{{WRAPPER}}.timeline .timeline-box' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule', 'history' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
				array(
					'heading'    => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => 'porto_dimension',
					'param_name' => 'item_pd',
					'selectors'  => array(
						'{{WRAPPER}} .timeline-balloon .balloon-content, {{WRAPPER}}.timeline .timeline-box' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule', 'history' ),
					),
					'group'      => esc_html__( 'Item Style', 'porto-functionality' ),
				),
			),
		)
	);
	if ( ! class_exists( 'WPBakeryShortCode_Porto_Schedule_Timeline_Container' ) ) {
		class WPBakeryShortCode_Porto_Schedule_Timeline_Container extends WPBakeryShortCodesContainer {
		}
	}
}
