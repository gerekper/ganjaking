<?php 
/*
Widget Name: TP Text Block
Description: Content of text text block.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Adv_Text_Block extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
		
	public function get_name() {
		return 'tp-adv-text-block';
	}

    public function get_title() {
        return esc_html__('TP Text Block', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-file-text theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "advanced-text";

		return esc_url($DocUrl);
	}

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Advanced Text Block', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'content_description',
			[
				'label' => wp_kses_post( "Description <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "advanced-text-block-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$this->add_responsive_control(
			'content_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'prefix_class' => 'text-%s',
			]
		);
		$this->add_control(
			'display_count',
			[
				'label' => wp_kses_post( "Description Limit <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "limit-wordcount-text-widget-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'display_count_by', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Limit on', 'theplus'),
                'default' => 'char',
                'options' => [
                    'char' => esc_html__('Character', 'theplus'),
                    'word' => esc_html__('Word', 'theplus'),                    
                ],
				'condition'   => [
					'display_count'    => 'yes',
				],
            ]
        );
		$this->add_control(
			'display_count_input',
			[
				'label' => esc_html__( 'Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
				'condition'   => [
					'display_count'    => 'yes',
				],
			]
		);
		$this->add_control(
			'display_3_dots',
			[
				'label' => esc_html__( 'Display Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition'   => [
					'display_count'    => 'yes',
				],
			]
		);	
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__('Typography', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
            'content_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_adv_text_block .text-content-block p,{{WRAPPER}} .pt_plus_adv_text_block .text-content-block' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .pt_plus_adv_text_block .text-content-block,{{WRAPPER}} .pt_plus_adv_text_block .text-content-block p',
            ]
        );

		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		 /*Adv tab*/

		/*--OnScroll View Animation ---*/
		include THEPLUS_PATH.'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
	
	protected function limit_words($string, $word_limit){
		$words = explode(" ",$string);
		return implode(" ",array_splice($words,0,$word_limit));
	}

	protected function render() {
        $settings = $this->get_settings_for_display();

		/*--OnScroll View Animation ---*/
		include THEPLUS_PATH.'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
		$PlusExtra_Class="plus-adv-text-widget";
		include THEPLUS_PATH.'modules/widgets/theplus-widgets-extra.php';

		if((!empty($settings['display_count']) && $settings['display_count']=='yes') && !empty($settings['display_count_input'])){			
			if(!empty($settings['display_count_by'])){				
				if($settings['display_count_by']=='char'){
					$content_description = substr($settings['content_description'],0,$settings['display_count_input']);										
				}else if($settings['display_count_by']=='word'){
					$content_description = $this->limit_words($settings['content_description'],$settings['display_count_input']);					
				}
			}	
				if($settings['display_count_by']=='char'){
					if(strlen($settings["content_description"]) > $settings['display_count_input']){
						if(!empty($settings['display_3_dots']) && $settings['display_3_dots']=='yes'){
							$content_description .='...';
						}
					}
				}else if($settings['display_count_by']=='word'){
					if(str_word_count($settings["content_description"]) > $settings['display_count_input']){
						if(!empty($settings['display_3_dots']) && $settings['display_3_dots']=='yes'){
							$content_description .='...';
						}
					}
				}						
		}else{
			$content_description = $settings['content_description'];
		}
			
			$text_block ='<div class="pt-plus-text-block-wrapper" >';
				$text_block .='<div class="text_block_parallax">';
					$text_block .='<div class="pt_plus_adv_text_block '.$animated_class.'" '.$animation_attr.'>';
						$text_block .= '<div class="text-content-block">';
							$text_block .= wp_kses_post($content_description);
						$text_block .= '</div>';
					$text_block .='</div>';
				$text_block .='</div>';
			$text_block .='</div>';
			
		echo $before_content.$text_block.$after_content;
	}
	
    protected function content_template() {
	
    }
}