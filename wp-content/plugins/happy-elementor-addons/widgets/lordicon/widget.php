<?php
	/**
	 * LordIcon widget class
	 *
	 * @package Happy_Addons
	 */
	namespace Happy_Addons\Elementor\Widget;

	use Elementor\Controls_Manager;
	use Elementor\Group_Control_Border;
	use Elementor\Group_Control_Box_Shadow;

	defined( 'ABSPATH' ) || die();

class LordIcon extends Base {

	/**
	 * Get widget title.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'LordIcon', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/icon-box/';
	}

	/**
	 * Get widget icon.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-icon-box';
	}

	public function get_keywords() {
		return ['icon', 'lordicon', 'info', 'box', 'icon'];
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'_section_icon',
			[
				'label' => __( 'Content', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_method',
			[
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Icon Method', 'happy-elementor-addons' ),
				'description' => sprintf( '<a target="_blank" href="%1$s">Learn how to use the Lordicon widget</a>', esc_url( 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/lord-icon' ) ),
				'options'     => [
					'cdn'  => esc_html__( 'Paste Lordicon URL', 'happy-elementor-addons' ),
					'file' => esc_html__( 'Upload Lordicon file', 'happy-elementor-addons' ),
				],
				'default'     => 'cdn',
				'label_block' => true,
			]
		);
		$this->add_control(
			'icon_cdn',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Paste CDN', 'happy-elementor-addons' ),
				'label_block' => true,
				'description' => sprintf(
					'Paste icon code from <a target="_blank" href="%1$s">lordicon.com</a> <br /><br /> <a target="_blank" href="%2$s">Learn how to get Lordicon CDN</a><br><br>
                Example: https://cdn.lordicon.com/lupuorrc.json', esc_url( 'https://lordicon.com/' ), esc_url( 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/lord-icon' )
				),
				'default'     => 'https://cdn.lordicon.com/lupuorrc.json',
				'condition'   => [
					'icon_method' => 'cdn',
				],
			]
		);
		$this->add_control(
			'icon_json',
			[
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'JSON File', 'happy-elementor-addons' ),
				'media_type'  => 'application/json',
				'description' => sprintf( 'Download Json file from <a href="%1$s" target="_blank">lordicon.com</a>', esc_url( 'https://lordicon.com/' ) ),
				'default'     => [
					'url' => HAPPY_ADDONS_ASSETS . 'vendor/lord-icon/placeholder.json',
				],
				'condition'   => [
					'icon_method' => 'file',
				],
			]
		);
		$this->add_control(
			'animation_trigger',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Animation Trigger', 'happy-elementor-addons' ),
				'options' => [
					'loop'          => esc_html__( 'Loop (infinite)', 'happy-elementor-addons' ),
					'click'         => esc_html__( 'Click', 'happy-elementor-addons' ),
					'hover'         => esc_html__( 'Hover', 'happy-elementor-addons' ),
					'loop-on-hover' => esc_html__( 'Loop on Hover', 'happy-elementor-addons' ),
					'morph'         => esc_html__( 'Morph', 'happy-elementor-addons' ),
					'morph-two-way' => esc_html__( 'Morph two way', 'happy-elementor-addons' ),
				],
				'default' => 'loop',
			]
		);
		$this->add_control(
			'target',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Target', 'happy-elementor-addons' ),
				'options' => [
					'widget'  => __( 'On Widget', 'happy-elementor-addons' ),
					// 'icon' => __('On Icon', 'happy-elementor-addons' ),
					'column'  => __( 'On Column', 'happy-elementor-addons' ),
					'section' => __( 'On Section', 'happy-elementor-addons' ),
					'custom'  => __( 'Custom', 'happy-elementor-addons' ),
				],
				'default' => 'widget',
			]
		);
		$this->add_control(
			'custom_target',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Custom Target', 'happy-elementor-addons' ),
				'placeholder' => __( '.example', 'happy-elementor-addons' ),
				'default'     => __( '.example', 'happy-elementor-addons' ),
				'condition'   => [
					'target' => 'custom',
				],
			]
		);

		$this->add_control(
			'pulse_effect',
			[
				'label'        => esc_html__( 'Pulse Effect', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'This will override your box shadow', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);
		$this->add_control(
			'pulse_color',
			[
				'label'     => __( 'Pulse Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#B6B6B6',
				'condition' => [
					'pulse_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .pulse_effect' => '--pulse-color:{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => __( 'Alignment', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ha-lordicon-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__icon_style_controls();
	}

	protected function __icon_style_controls() {
		$this->start_controls_section(
			'_section_style_icon',
			[
				'label' => __( 'Icon', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'   => __( 'Size', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SLIDER,
				// 'size_units' => [ 'px' ],
				'range'   => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'default' => [
					'size' => 150,
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label'   => __( 'Primary Color', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#121331',
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label'   => __( 'Secondary Color', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#08a88a',
			]
		);
		$this->add_control(
			'tertiary_color',
			[
				'label'   => __( 'Tertiary Color', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#0816A8',
			]
		);

		$this->add_control(
			'quaternary_color',
			[
				'label'   => __( 'Quaternary Color', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#2CA808',
			]
		);

		$this->add_control(
			'icon_stroke',
			[
				'label'   => __( 'Stroke', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'min' => 1,
					'max' => 500,
				],
				'default' => [
					'size' => '20',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-lordicon-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .ha-lordicon-wrapper',
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-lordicon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'exclude'  => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-lordicon-wrapper',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();

		//for manage loard icon global colors only
		$primary_color = $settings['primary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['primary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['primary_color']);
			$get_id = end($color_id);
			$primary_color = $this->get_golobal_color($get_id);
		} 
		
		$secondary_color = $settings['secondary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['secondary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['secondary_color']);
			$get_id = end($color_id);
			$secondary_color = $this->get_golobal_color($get_id);
		} 
		
		$tertiary_color = $settings['tertiary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['tertiary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['tertiary_color']);
			$get_id = end($color_id);
			$tertiary_color = $this->get_golobal_color($get_id);
		} 
		
		$quaternary_color = $settings['quaternary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['quaternary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['quaternary_color']);
			$get_id = end($color_id);
			$quaternary_color = $this->get_golobal_color($get_id);
		} 

		$json_url    = '';
		$method      = $settings['icon_method'];
		$target      = $settings['target'];
		$icon_size   = $settings['icon_size'];
		$icon_stroke = $settings['icon_stroke'];

		if ( 'file' == $method ) {
			$json_url = $settings['icon_json']['url'];
		} else {
			$json_url = $settings['icon_cdn'];
		}
		$target_class = '';

		if ( 'custom' == $target ) {
			$target_class = $settings['custom_target'];
		} elseif ( 'column' == $target ) {
			$target_class = '.elementor-column';
		} elseif ( 'section' == $target ) {
			$target_class = '.elementor-section';
		} else {
			$target_class = '.ha-lordicon-wrapper';
		}
		$pulse_effect = ( 'yes' == $settings['pulse_effect'] ) ? ' pulse_effect' : '';

		?>
			<div class="ha-lordicon-wrapper<?php echo esc_attr( $pulse_effect ); ?>">
			<lord-icon
				src="<?php echo esc_url( $json_url ); ?>"
				trigger="<?php echo esc_attr( $settings['animation_trigger'] ); ?>"
				stroke="<?php echo esc_attr( $icon_stroke['size'] ); ?>"
				target="<?php echo esc_attr( $target_class ); ?>"
				colors="primary:<?php echo esc_attr( $primary_color ); ?>,secondary:<?php echo esc_attr( $secondary_color ); ?>,tertiary:<?php echo esc_attr( $tertiary_color ); ?>,quaternary:<?php echo esc_attr( $quaternary_color ); ?>"
				style="width:<?php echo esc_attr( $icon_size['size'] ); ?>px;height:<?php echo esc_attr( $icon_size['size'] ); ?>px">
			</lord-icon>
			</div>
		<?php

	}

	private function get_golobal_color($id) {
		$global_color = '';

		if( ! $id ) {
			return $global_color;
		}
		
		$el_page_settings 	= [];

		$ekit_id = get_option('elementor_active_kit', true);

		if ( $ekit_id ) {
			$el_page_settings = get_post_meta($ekit_id, '_elementor_page_settings', true);

			if( !empty( $el_page_settings ) && isset($el_page_settings['system_colors']) ) {
				foreach( $el_page_settings['system_colors'] as $key => $val ) {
					if( $val['_id'] == $id ) {
						$global_color = $val['color'];
					}
				}
			}

		}

		return $global_color;
	}

}
