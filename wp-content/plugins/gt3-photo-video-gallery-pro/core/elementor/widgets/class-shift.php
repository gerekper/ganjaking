<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use GT3\PhotoVideoGalleryPro\Block\Shift as Gallery;

class Shift extends Basic {

	public function get_title(){
		return esc_html__('Shift', 'gt3pg_pro');
	}

	public function get_icon(){
		return 'gt3-elementor-editor-icon gt3-icon-shift';
	}

	public function get_name(){
		return 'gt3pg-shift';
	}


	public function __construct(array $data = [], $args = null){
		parent::__construct($data, $args);
		$this->actions();
	}

	private function actions(){
		add_action('elementor/widgets/widgets_registered', function($widgets_manager){
			/* @var \Elementor\Widgets_Manager $widgets_manager */
			$widgets_manager->register_widget_type($this);
		});
	}

	//////////////////////////

	protected function _register_controls(){
		$this->start_controls_section(
			'images',
			array(
				'label' => esc_html__('Images', 'gt3pg_pro'),
			)
		);

		$this->imagesControls();

		$this->end_controls_section();

		$this->start_controls_section(
			'settings',
			array(
				'label' => esc_html__('Basic', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'controls',
			array(
				'label'       => esc_html__('Show Control Buttons', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF control buttons', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'infinityScroll',
			array(
				'label'       => esc_html__('Infinite Scroll', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF infinite  scrolling. Autoplay works only when infinite scroll is ON', 'gt3pg_pro'),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'       => esc_html__('Autoplay', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF slider autoplay', 'gt3pg_pro'),
				'default'     => 'yes',
				'condition'   => array(
					'infinityScroll!' => '',
				),
			)
		);

		$this->add_control(
			'interval',
			array(
				'label'       => esc_html__('Slide Duration', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__('Set the timing of single slides in seconds', 'gt3pg_pro'),
				'default'     => 6,
				'condition'   => array(
					'infinityScroll!' => '',
					'autoplay!'       => '',
				),
			)
		);

		$this->add_control(
			'transitionTime',
			array(
				'label'       => esc_html__('Transition Time', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__('Set transition animation time', 'gt3pg_pro'),
				'default'     => 600,
			)
		);

		$this->add_control(
			'showTitle',
			array(
				'label'   => esc_html__('Show Title', 'gt3pg_pro'),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'always'   => esc_html__('Always Show', 'gt3pg_pro'),
					'hide'     => esc_html__('Always Hide', 'gt3pg_pro'),
					'on_hover' => esc_html__('Show on Hover', 'gt3pg_pro'),
					'expanded' => esc_html__('Show when slide is expanded', 'gt3pg_pro'),
				),
				'default' => 'on_hover',
			)
		);

		$this->add_control(
			'expandable',
			array(
				'label'       => esc_html__('Expandable slides', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF expandable slides', 'gt3pg_pro'),
			)
		);

		/*$this->add_control(
			'hoverEffect',
			array(
				'label'       => esc_html__('Hover Effect', 'gt3pg_pro'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Turn ON or OFF hover effect', 'gt3pg_pro'),
			)
		);*/

		$this->add_control(
			'moduleHeight',
			array(
				'label'       => esc_html__('Module Height', 'gt3pg_pro'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '100%',
				'description' => esc_html__('Set module height in px (pixels). Enter \'100%\' for full height mode', 'gt3pg_pro'),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__('Title', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'title_space',
			array(
				'label'      => esc_html__('Title Width', 'gt3pg_pro'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1600,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .shift_title' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__('Title Color', 'gt3pg_pro'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .shift_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .controls a' => 'color: {{VALUE}};'
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .shift_title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'description_style_section',
			array(
				'label' => esc_html__('Description', 'gt3pg_pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'description_space',
			array(
				'label'      => esc_html__('Description Width', 'gt3pg_pro'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1600,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .shift_descr' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => esc_html__('Description Color', 'gt3pg_pro'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .shift_descr' => 'color: {{VALUE}};'
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .shift_descr',
			)
		);

		$this->end_controls_section();


	}

	// php
	protected function render(){
		$this->WRAP = esc_html('#uid-'.$this->get_id().' ');
		$settings   = $this->_get_settings();

		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();

		echo $gallery->render_block($settings);
	}

	// js
	protected function _content_template(){

	}

}

