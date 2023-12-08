<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Equal_Height extends Elementor\Widget_Base {
	public function __construct() {
		$theplus_options=get_option('theplus_options');
		$plus_extras=theplus_get_option('general','extras_elements');		
		
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && in_array('plus_equal_height',$plus_extras))){
			
			add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'tp_equalheight_controls' ], 10, 2 );
			add_action( 'elementor/element/column/_section_responsive/after_section_end', [ $this, 'tp_equalheight_controls' ], 10, 2 );
			add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', [ $this, 'tp_equalheight_controls' ], 10, 2 );
			
			$experiments_manager = Plugin::$instance->experiments;		
			if($experiments_manager->is_feature_active( 'container' )){
				add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'tp_equalheight_controls' ], 10, 2  );
			}

			//add_action( 'elementor/frontend/section/before_render', [ $this, 'plus_before_render'], 10, 1 );			
			//add_action( 'elementor/frontend/widget/before_render', [ $this, 'plus_before_render' ], 10, 1 );
			add_action( 'elementor/frontend/before_render', [ $this, 'plus_before_render'], 10, 1 );
		}		
	}
	
	public function get_name() {
		return 'plus-equal-height';
	}
	
	public function tp_equalheight_controls($element) {		
		$element->start_controls_section(
			'plus_equal_height_section',
			[
				'label' => esc_html__( 'Plus Extras : Equal Height', 'theplus' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);
		$element->add_control(
			'seh_switch',
			[
				'label'        => esc_html__( 'Equal Height', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' 		=> 'no',
			]
		);		
		$element->add_control(
			'seh_mode',
			[
				'label'     => esc_html__( 'Mode Based on', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'bodl' => 'Div Level',
					'bouc' => 'Unique Class',
				],
				'default'   => 'bodl',
				'condition' => [
					'seh_switch' => 'yes',
				],
			]
		);		
		$element->add_control(
			'seh_opt',
			[
				'label'     => esc_html__( 'Select Nested Level', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [					
					'widgets'    => 'Widgets',
					'1' => 'Nested Level 1',
					'2' => 'Nested Level 2',
					'3' => 'Nested Level 3',
					'4' => 'Nested Level 4',
					'5' => 'Nested Level 5',					
					'6' => 'Nested Level 6',					
					'7' => 'Nested Level 7',					
					'8' => 'Nested Level 8',					
					'9' => 'Nested Level 9',					
					'10' => 'Nested Level 10',
				],
				'default'   => 'widgets',
				'condition' => [
					'seh_switch' => 'yes',
					'seh_mode' => 'bodl',
				],
			]
		);
		$element->add_control(
			'seh_eql_opt',
			[
				'label'     => esc_html__( 'Select Sub Nested Level', 'theplus' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'1' => 'Level 1',
					'2' => 'Level 2',
					'3' => 'Level 3',
					'4' => 'Level 4',
					'5' => 'Level 5',
					'6' => 'Level 6',
					'7' => 'Level 7',
					'8' => 'Level 8',
					'9' => 'Level 9',
					'10' => 'Level 10',
				],
				'default'   => '1',
				'condition' => [
					'seh_switch' => 'yes',
					'seh_mode' => 'bodl',
				],
			]
		);


		$element->add_control(
			'seh_opt_custom',
			[
				'label'       => esc_html__( 'Enter Unique Class', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '.class-name', 'theplus' ),
				'condition'   => [
					'seh_switch' => 'yes',
					'seh_mode' => 'bouc',
				],
			]
		);
		$element->end_controls_section();
	}
	
	public function plus_before_render($element) {		
		$settings = $element->get_settings();
		//$settings = $element->get_settings_for_display();
		
		if( !empty($settings['seh_switch']) && $settings['seh_switch'] == 'yes' ){			
			$opt='';
			if(!empty($settings['seh_mode']) && $settings['seh_mode']=='bodl'){			
				if($settings['seh_opt'] == 'widgets'){
					$opt = '.elementor-widget-container';
				}			
				if($settings['seh_opt'] == 'widgets_l1' && $settings['seh_eql_opt'] == '1'){
					$opt = '.elementor-widget-container > div:nth-of-type(1)';
				}
				
				$nested_opt=$eql_opt='';
				$nested_opt= array('2', '3', '4', '5',"6","7","8","9","10");
				//$eql_opt= array('2', '3', '4', '5',"6","7","8","9","10");
				if((in_array($settings['seh_opt'], $nested_opt)) && !empty($settings['seh_eql_opt'])){				
					$seh_opt_add='';
					for( $i=2;$i<=$settings['seh_opt'];$i++) {
						$seh_opt_add .= ' > div ';
					}
					$opt = '.elementor-widget-container '.$seh_opt_add.' > div:nth-of-type('.$settings['seh_eql_opt'].')';
					
				}
			}
			
			if((!empty($settings['seh_mode']) && $settings['seh_mode']=='bouc') && !empty($settings['seh_opt_custom']) ){					
					$opt = esc_attr($settings['seh_opt_custom']) ;	
			}
			
			if ($opt){							
				$element->add_render_attribute( '_wrapper', 
				array(
					'class' => 'theplus-equal-height',
					'data-tp-equal-height-loadded' => $opt,
				));
			}
		}
		
	}
}