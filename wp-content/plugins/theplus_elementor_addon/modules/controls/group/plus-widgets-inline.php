<?php	
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Column_Responsive extends Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init();
	}

	public function get_name() {
		return 'plus-column-responsive';
	}

	public function register_controls( $element, $section_id ) {
		if($element->get_name() == 'column' ) {
		
			$element->start_controls_section(
				'plus_column_responsive_section',
				[
					'label' => esc_html__( 'Plus Extras', 'theplus' ),
					'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);
			$element->add_control(
				'plus_column_sticky',
				[
					'label'        => esc_html__( 'Sticky Column', 'theplus' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'theplus' ),
					'label_off'    => esc_html__( 'No', 'theplus' ),
					'return_value' => 'true',
					'default'      => 'false',
				]
			);

			$element->add_control(
				'plus_sticky_top_spacing',
				[
					'label'   => esc_html__( 'Top Spacing', 'theplus' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 40,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
					'condition' => [
						'plus_column_sticky' => 'true',
					],
				]
			);

			$element->add_control(
				'plus_sticky_bottom_spacing',
				[
					'label'   => esc_html__( 'Bottom Spacing', 'theplus' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 40,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
					'condition' => [
						'plus_column_sticky' => 'true',
					],
				]
			);
			$element->add_responsive_control(
				'plus_sticky_padding',
				[
					'label' => esc_html__( 'Padding', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .inner-wrapper-sticky' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'plus_column_sticky' => 'true',
					],
				]
			);
			$element->add_control(
				'plus_sticky_enable_desktop',
				[
					'label' => esc_html__( 'Sticky Desktop', 'theplus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Enable', 'theplus' ),
					'label_off' => esc_html__( 'Disable', 'theplus' ),					
					'default' => 'yes',
					'condition' => [
						'plus_column_sticky' => 'true',
					],
				]
			);
			$element->add_control(
				'plus_sticky_enable_tablet',
				[
					'label' => esc_html__( 'Sticky Tablet', 'theplus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Enable', 'theplus' ),
					'label_off' => esc_html__( 'Disable', 'theplus' ),					
					'default' => 'yes',
					'condition' => [
						'plus_column_sticky' => 'true',
					],
				]
			);
			$element->add_control(
				'plus_sticky_enable_mobile',
				[
					'label' => esc_html__( 'Sticky Mobile', 'theplus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Enable', 'theplus' ),
					'label_off' => esc_html__( 'Disable', 'theplus' ),					
					'default' => 'no',
					'condition' => [
						'plus_column_sticky' => 'true',
					],
				]
			);
			$element->add_responsive_control(
				'plus_column_width',
				[
					'label' => esc_html__( 'Column Width', 'theplus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'description' => 'E.g. 300px, 40%, calc(100%-400px)',					
					'selectors' => [
						'{{WRAPPER}}' => 'width: {{VALUE}} !important;',
					],
					'separator' => 'before',
				]
			);
			$element->add_control(
				'plus_column_hide_desktop',
				[
					'label' => esc_html__( 'Hide On Desktop', 'theplus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'prefix_class' => 'elementor-',
					'label_on' => esc_html__( 'Hide', 'theplus' ),
					'label_off' => esc_html__( 'Show', 'theplus' ),
					'return_value' => 'hidden-desktop',
					'separator' => 'before',
				]
			);

			$element->add_control(
				'plus_column_hide_tablet',
				[
					'label' => esc_html__( 'Hide On Tablet', 'theplus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'prefix_class' => 'elementor-',
					'label_on' => esc_html__( 'Hide', 'theplus' ),
					'label_off' => esc_html__( 'Show', 'theplus' ),
					'return_value' => 'hidden-tablet',
				]
			);

			$element->add_control(
				'plus_column_hide_mobile',
				[
					'label' => esc_html__( 'Hide On Mobile', 'theplus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'prefix_class' => 'elementor-',
					'label_on' => esc_html__( 'Hide', 'theplus' ),
					'label_off' => esc_html__( 'Show', 'theplus' ),
					'return_value' => 'hidden-phone',
				]
			);
			$element->add_responsive_control(
				'plus_column_order',
				[
					'label' => esc_html__( 'Column Order', 'theplus' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 20,
					'step' => 1,
					'default' => '',
					'separator' => 'before',
					'description' => 'E.g. 0,1,2,3,etc.',
					'selectors' => [
						'{{WRAPPER}}' => 'order: {{VALUE}}',
					],
				]
			);
			
			
			$element->add_control(
				'plus_responsive_column_heading',
				[
					'label' => esc_html__( 'Responsive Options for Breakpoints', 'theplus' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);
			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'plus_media_max_width',
				[
					'label' => esc_html__( '@Media Max-Width Value', 'theplus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'no',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);
			$repeater->add_control(
				'media_max_width',
				[
					'label' => esc_html__( 'Select Max-Width Value(px)', 'theplus' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 3000,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 0,
					],
					'condition' => [
						'plus_media_max_width' => 'yes',
					],
				]
			);
			$repeater->add_control(
				'plus_media_min_width',
				[
					'label' => esc_html__( '@Media Min-Width Value', 'theplus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'no',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);
			$repeater->add_control(
				'media_min_width',
				[
					'label' => esc_html__( 'Select Min-Width Value(px)', 'theplus' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 3000,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 0,
					],
					'condition' => [
						'plus_media_min_width' => 'yes',
					],
				]
			);
			
			$repeater->add_control(
				'plus_column_width',
				[
					'label' => esc_html__( 'Column Width', 'theplus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'description' => 'E.g. 300px, 40%, calc(100%-400px)',
					'separator' => 'before',
				]
			);
			$repeater->add_responsive_control(
				'plus_column_margin',
				[
					'label' => esc_html__( 'Margin', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .your-class' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$repeater->add_responsive_control(
				'plus_column_padding',
				[
					'label' => esc_html__( 'Padding', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .your-class' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$repeater->add_control(
				'plus_column_hide',
				[
					'label' => esc_html__( 'Column Visibility', 'theplus' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Hide',
					'label_off' => 'Show',
					'return_value' => 'yes',
					'separator' => 'before',
				]
			);
			$repeater->add_control(
				'plus_column_order',
				[
					'label' => esc_html__( 'Column Order', 'theplus' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 20,
					'step' => 1,
					'default' => '',
					'separator' => 'before',
					'description' => 'E.g. 0,1,2,3,etc.',
				]
			);
			$element->add_control(
				'plus_column_responsive_list',
				[
					'type'    => \Elementor\Controls_Manager::REPEATER,
					'fields'  => $repeater->get_controls(),
					'title_field' => 'Min: {{{plus_media_min_width}}} - Max: {{{plus_media_min_width}}}',
				]
			);
			$element->add_control(
				'plus_custom_css',
				[
					'label' => esc_html__( 'Custom CSS', 'theplus' ),
					'type' => \Elementor\Controls_Manager::CODE,
					'language' => 'css',
					'render_type' => 'ui',					
					'separator' => 'none',
					'rows' => 20,
				]
			);
			$element->end_controls_section();
		}
	}
	public function section_register_controls( $element, $section_id ) {
		if($element->get_name() == 'section' || $element->get_name() == 'container') {
		
			$element->start_controls_section(
				'plus_section_responsive_section',
				[
					'label' => esc_html__( 'Plus Extras', 'theplus' ),
					'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);
			
			$element->add_control(
				'plus_custom_css',
				[
					'label' => esc_html__( 'Custom CSS', 'theplus' ),
					'type' => \Elementor\Controls_Manager::CODE,
					'language' => 'css',
					'render_type' => 'ui',
					'separator' => 'none',
					'rows' => 20,
				]
			);
			$element->end_controls_section();
		}
	}
	
	public function before_render_element($element) {
		$settings = $element->get_settings();
		$column_wrapper_id= $element->get_id();
		$post_id = get_the_ID();		
		
		$data     = $element->get_data();
		$type     = isset( $data['elType'] ) ? $data['elType'] : 'column';
		//$settings = $data['settings'];

		if ( 'column' !== $type ) {
			return;
		}

		if ( isset( $settings['plus_column_sticky'] ) ) {
			$array_enable=array();
			if(isset($settings['plus_sticky_enable_desktop']) && $settings['plus_sticky_enable_desktop']=='yes'){
				$array_enable[]= 'desktop';
			}
			if(isset($settings['plus_sticky_enable_tablet']) && $settings['plus_sticky_enable_tablet']=='yes'){
				$array_enable[]= 'tablet';
			}
			if(isset($settings['plus_sticky_enable_mobile']) && $settings['plus_sticky_enable_mobile']=='yes'){
				$array_enable[]= 'mobile';
			}
			
			$column_settings = array(
				'id'            => $data['id'],
				'sticky'        => filter_var( $settings['plus_column_sticky'], FILTER_VALIDATE_BOOLEAN ),
				'topSpacing'    => isset( $settings['plus_sticky_top_spacing'] ) ? $settings['plus_sticky_top_spacing'] : 40,
				'bottomSpacing' => isset( $settings['plus_sticky_bottom_spacing'] ) ? $settings['plus_sticky_bottom_spacing'] : 40,
				'stickyOn'      => !empty( $array_enable ) ? $array_enable : array( 'desktop', 'tablet' ),
			);

			if ( filter_var( $settings['plus_column_sticky'], FILTER_VALIDATE_BOOLEAN ) ) {

				$element->add_render_attribute( '_wrapper', array(
					'class' => 'plus-sticky-column-sticky',
					'data-plus-sticky-column-settings' => json_encode( $column_settings ),
				) );
			}

			$this->columns_data[ $data['id'] ] = $column_settings;
		}
		
		if ( array_key_exists( 'plus_column_responsive_list',$settings ) ) {
			$list = $settings['plus_column_responsive_list'];	
			if( !empty($list[0]['media_max_width']['size']) || !empty($list[0]['media_min_width']['size']) ) {

				$media_query = '@media ';
				
				$index = 0;
				$style=$max_width=$min_width=$betwn_and='';
				foreach ($list as $item) {
					$index++;
					$max_width=$min_width=$betwn_and='';
					if(!empty($item['media_max_width']['size']) && !empty($item["plus_media_max_width"]) && $item["plus_media_max_width"]=='yes') {
						$max_width = '(max-width: '.$item['media_max_width']['size'].$item['media_max_width']['unit'].') ';
					}
					if(!empty($item['media_min_width']['size']) && !empty($item["plus_media_min_width"]) && $item["plus_media_min_width"]=='yes') {
						$min_width = ' (min-width: '.$item['media_min_width']['size'].$item['media_min_width']['unit'].') ';
					}
					if(!empty($item['media_max_width']['size']) && !empty($item['media_min_width']['size']) && $item["plus_media_max_width"]=='yes' && $item["plus_media_min_width"]=='yes'){
						$betwn_and =' and ';
					}
					$style .= $media_query . $max_width . $betwn_and . $min_width .'{';
						$style .= '.elementor-'.$post_id.' .elementor-element.elementor-column.elementor-element-'.$column_wrapper_id.'{';
						if(!empty($item['plus_column_width'])){
							$style .= 'width : '. $item['plus_column_width'].' !important;';
						}
						if(!empty($item['plus_column_hide'])){
							$style .= 'display : none;';
						}
						if(!empty($item['plus_column_order'])){
							$style .= 'order : '.$item['plus_column_order'].';';
						}
						
						$style .= '}';
						$style .= '.elementor-'.$post_id.' .elementor-element.elementor-column.elementor-element-'.$column_wrapper_id.' > .elementor-column{';
						if(!empty($item['plus_column_margin'])){
							$style .= 'margin : '.$item['plus_column_margin']["top"].$item['plus_column_margin']["unit"].' '.$item['plus_column_margin']["right"].$item['plus_column_margin']["unit"].' '.$item['plus_column_margin']["bottom"].$item['plus_column_margin']["unit"].' '.$item['plus_column_margin']["left"].$item['plus_column_margin']["unit"].' !important;';
						}
						if(!empty($item['plus_column_padding'])){
							$style .= 'padding : '.$item['plus_column_padding']["top"].$item['plus_column_padding']["unit"].' '.$item['plus_column_padding']["right"].$item['plus_column_padding']["unit"].' '.$item['plus_column_padding']["bottom"].$item['plus_column_padding']["unit"].' '.$item['plus_column_padding']["left"].$item['plus_column_padding']["unit"].' !important;';
						}
						$style .= '}';
					
					$style .= '}';
					
				}
				if(!empty($style)){
					echo '<style>'.$style.'</style>';
				}
			}
		}
	}
	
	/**
	 * @param $post_css Post
	 * @param $element  Element_Base
	 */
	public function add_post_css( $post_css, $element ) {		
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$element_settings = $element->get_settings();

		if ( empty( $element_settings['plus_custom_css'] ) ) {
			return;
		}

		$css = trim( $element_settings['plus_custom_css'] );

		if ( empty( $css ) ) {
			return;
		}
		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );
		$css = $css;		
		$post_css->get_stylesheet()->add_raw_css( $css );
	}
	protected function init() {
		
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );		
		add_action( 'elementor/frontend/element/before_render', array( $this, 'before_render_element' ) );
		
		add_action( 'elementor/element/parse_css', [ $this, 'add_post_css' ], 10, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'section_register_controls' ], 10, 2 );

		$experiments_manager = Plugin::$instance->experiments;		
		if($experiments_manager->is_feature_active( 'container' )){
			add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'section_register_controls' ], 10, 2  );
		}
	}

}