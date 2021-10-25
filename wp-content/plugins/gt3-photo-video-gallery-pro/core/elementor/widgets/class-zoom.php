<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use GT3\PhotoVideoGalleryPro\Block\Zoom as Gallery;

class Zoom extends Basic {

	public function get_name(){
		return 'gt3pg-zoom';
	}

	public function get_title(){
		return esc_html__('Zoom Gallery', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-grid';
	}

	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();

		echo $gallery->render_block($settings);

	}

	protected function _controls(){
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__('Images', 'gt3pg_pro'),
			)
		);

		$this->imagesControls(array(
			'withCustomVideoLink' => true,
		));

		$this->end_controls_section();

		$this->start_controls_section(
			'settings_section',
			array(
				'label' => esc_html__('Settings', 'gt3_elementor_zoom_gallery'),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'loader',
			array(
				'label'   => esc_html__('Show Images', 'gt3_elementor_zoom_gallery'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'asIs'      => esc_html__('Image Ready', 'gt3_elementor_zoom_gallery'),
					'fromFirst' => esc_html__('From First To Last', 'gt3_elementor_zoom_gallery'),
					'random'    => esc_html__('Random', 'gt3_elementor_zoom_gallery'),
				),
				'default' => 'fromFirst',
			)
		);

		$this->add_control(
			'smartResize',
			array(
				'label' => esc_html__('Align to Edges', 'gt3_elementor_zoom_gallery'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'grid_gap',
			array(
				'label'     => esc_html__('Grid Gap', 'gt3_elementor_zoom_gallery'),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'0'    => '0',
					'1px'  => '1px',
					'2px'  => '2px',
					'3px'  => '3px',
					'4px'  => '4px',
					'5px'  => '5px',
					'10px' => '10px',
					'15px' => '15px',
					'20px' => '20px',
					'25px' => '25px',
					'30px' => '30px',
					'35px' => '35px',
				),
				'default'   => '0',
				'selectors' => array(
					'{{WRAPPER}} .isotope_wrapper'     => 'margin-right:-{{VALUE}}; margin-bottom:-{{VALUE}};',
					'{{WRAPPER}} .mirror_wrapper'      => 'margin-right:-{{VALUE}};',
					'{{WRAPPER}} .isotope_item .img'   => 'padding-right: {{VALUE}}; padding-bottom:{{VALUE}};',
					'{{WRAPPER}} .mirror_wrapper .img' => 'padding-right: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox',
			array(
				'label' => esc_html__('Lightbox', 'elementor_zoom_gallery'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'start_extended',
			array(
				'label' => esc_html__('Start with Zoomed Image', 'gt3_elementor_zoom_gallery'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'wrap_height',
			array(
				'label'      => esc_html__('Zoomed Image Height', 'gt3_elementor_zoom_gallery'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 100,
					'unit' => 'vh',
				),
				'range'      => array(
					'px' => array(
						'min'  => 200,
						'max'  => 1920,
						'step' => 10,
					),
					'vh' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					)
				),
				'size_units' => array( 'px', 'vh' ),
			)
		);

		$this->add_responsive_control(
			'thumb_height',
			array(
				'label'      => esc_html__('Thumbnail Height', 'gt3_elementor_zoom_gallery'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 20,
					'unit' => 'vh',
				),
				'range'      => array(
					'px' => array(
						'min'  => 200,
						'max'  => 1920,
						'step' => 10,
					),
					'vh' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					)
				),
				'size_units' => array( 'px', 'vh' ),
//				'selectors'  => array(
//					'{{WRAPPER}} .isotope_item .img' => 'height: {{SIZE}}{{UNIT}};',
//				),
			)
		);

		$this->add_responsive_control(
			'fade_duration',
			array(
				'label'          => esc_html__('Fade Duration', 'gt3_elementor_zoom_gallery'),
				'description'    => esc_html__('Fade Duration Time', 'gt3_elementor_zoom_gallery'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 300,
					'unit' => 'ms',
				),
				'tablet_default' => array(
					'size' => 500,
					'unit' => 'ms',
				),
				'mobile_default' => array(
					'size' => 500,
					'unit' => 'ms',
				),
				'range'          => array(
					'ms' => array(
						'min'  => 100,
						'max'  => 2000,
						'step' => 40,
					)
				),
				'size_units'     => array( 'ms' ),
				'selectors'      => array(
					'{{WRAPPER}} .zoom_gallery_wrapper .isotope_item .img' =>
						'transition-duration: {{SIZE}}{{UNIT}};
						 -moz-transition-duration: {{SIZE}}{{UNIT}};
						 -webkit-transition-duration: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fade_delay',
			array(
				'label'       => esc_html__('Fade Timeout', 'gt3_elementor_zoom_gallery'),
				'description' => esc_html__('Delay Between Loading Items', 'gt3_elementor_zoom_gallery'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 100,
					'unit' => 'ms',
				),
				'range'       => array(
					'ms' => array(
						'min'  => 100,
						'max'  => 2000,
						'step' => 20,
					)
				),
				'size_units'  => array( 'ms' ),
			)
		);

//		$this->add_control(
//			'random_shows',
//			array(
//				'label' => esc_html__('Random Loading', 'gt3_elementor_zoom_gallery'),
//				'type'  => Controls_Manager::SWITCHER,
//			)
//		);

		$this->add_control(
			'position_divider',
			array(
				'label'   => esc_html__('Position Divider', 'gt3_elementor_zoom_gallery'),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__(' of ', 'gt3_elementor_zoom_gallery'),
			)
		);

		$this->add_control(
			'expand_close_button',
			array(
				'label'   => esc_html__('Back to Thumbs', 'gt3_elementor_zoom_gallery'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'close_button_size',
			array(
				'label'      => esc_html__('Back to Thumbs Size', 'gt3_elementor_zoom_gallery'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 40,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 10,
						'max'  => 80,
						'step' => 2,
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .info_panel svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'size_units' => array( 'px' ),
				'condition'  => array(
					'expand_close_button!' => '',
				),
			)
		);

		$this->add_control(
			'close_button_padding',
			array(
				'label'      => esc_html__('Back to Thumbs Padding', 'gt3_elementor_zoom_gallery'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 40,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 80,
						'step' => 2,
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .thumbs' => 'padding: 0 {{SIZE}}px;',
				),
				'size_units' => array( 'px' ),
				'condition'  => array(
					'expand_close_button!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'expand_close_position',
			array(
				'label'     => esc_html__('Back to Thumbs Position', 'gt3_elementor_zoom_gallery'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'  => array(
						'title' => esc_html__('Left', 'gt3_elementor_zoom_gallery'),
						'icon'  => 'fa fa-align-left',
					),
					'right' => array(
						'title' => esc_html__('Right', 'gt3_elementor_zoom_gallery'),
						'icon'  => 'fa fa-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'right',
				'condition' => array(
					'expand_close_button!' => '',
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__('Choose Image Size', 'gt3_elementor_zoom_gallery'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'medium'       => esc_html__('Medium (300px)', 'gt3_elementor_zoom_gallery'),
					'medium_large' => esc_html__('Medium Large (768px)', 'gt3_elementor_zoom_gallery'),
					'large'        => esc_html__('Large (1024px)', 'gt3_elementor_zoom_gallery'),
					'full'         => esc_html__('Full', 'gt3_elementor_zoom_gallery'),
				),
				'default' => 'medium',
			)
		);

		$this->add_control(
			'image_size_opened',
			array(
				'label'   => esc_html__('Choose Opened Image Size', 'gt3_elementor_zoom_gallery'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'medium'       => esc_html__('Medium (300px)', 'gt3_elementor_zoom_gallery'),
					'medium_large' => esc_html__('Medium Large (768px)', 'gt3_elementor_zoom_gallery'),
					'large'        => esc_html__('Large (1024px)', 'gt3_elementor_zoom_gallery'),
					'full'         => esc_html__('Full', 'gt3_elementor_zoom_gallery'),
				),
				'default' => 'large',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__('Style', 'gt3_elementor_zoom_gallery'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'mirror_color',
			array(
				'label'       => esc_html__('Background for Zoomed Image', 'gt3_elementor_zoom_gallery'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .mirror_wrapper' => 'background-color: {{VALUE}};',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'info_panel_color',
			array(
				'label'     => esc_html__('Panel Background', 'gt3_elementor_zoom_gallery'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .info_panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'position_color',
			array(
				'label'     => esc_html__('Position Color', 'gt3_elementor_zoom_gallery'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .zoom_gallery_wrapper .info_panel .position' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_color',
			array(
				'label'     => esc_html__('Close Button Color', 'gt3_elementor_zoom_gallery'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .zoom_gallery_wrapper .info_panel svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_color_hover',
			array(
				'label'       => esc_html__('Close Button Color on Hover', 'gt3_elementor_zoom_gallery'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .zoom_gallery_wrapper .info_panel .thumbs:hover svg' => 'fill: {{VALUE}};',
				),
				'label_block' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'info_panel_position',
				'selector' => '{{WRAPPER}} .zoom_gallery_wrapper .info_panel .position',
				'label'    => esc_html__('Position Typography', 'gt3_elementor_zoom_gallery'),
			)
		);

		$this->end_controls_section();
	}
}

