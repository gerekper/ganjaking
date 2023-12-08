<?php 
/*
Widget Name: Heading Animattion 
Description: Text Animation of style.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Heading_Animation extends Widget_Base {
		
	public function get_name() {
		return 'tp-heading-animation';
	}

    public function get_title() {
        return esc_html__('Heading Animation', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-i-cursor theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Text Animation', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'anim_styles',[
				'label' => esc_html__( 'Animation Style','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1','theplus' ),
					'style-2' => esc_html__( 'Style 2','theplus' ),
					'style-3' => esc_html__( 'Style 3','theplus' ),
					'style-4' => esc_html__( 'Style 4','theplus' ),
					'style-5' => esc_html__( 'Style 5','theplus' ),
					'style-6' => esc_html__( 'Style 6','theplus' ),
				],
			]
		);
		$this->add_control(
            'prefix',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Prefix Text', 'theplus'),
                'label_block' => true,
                'description' => esc_html__('Enter Text, Which will be visible before the Animated Text.', 'theplus'),
                'separator' => 'before',
                'default' => esc_html__('This is ', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
            ]
        );
		$this->add_control(
			'ani_title',
			[
				'label' => esc_html__( 'Animated Text', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'You need to add Multiple line by ctrl + Enter Or Shift + Enter for animated text.', 'theplus' ),
				'rows' => 5,
				'default' => esc_html__( 'Heading', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$this->add_control(
            'ani_title_tag', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Animated Text Tag', 'theplus'),
                'default' => 'h1',
                'options' => theplus_get_tags_options(),
            ]
        );
		$this->add_control(
            'postfix',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Postfix Text', 'theplus'),
                'label_block' => true,
                'description' => esc_html__('Enter Text, Which will be visible After the Animated Text.', 'theplus'),
                'separator' => 'before',
                'default' => esc_html__('Animation', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
            ]
        );
		$this->add_responsive_control(
			'heading_text_align',
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
				],
				'default' => 'center',
				 'selectors' => [
                    '{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline,{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline span' => 'text-align: {{VALUE}};',
                ],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_prefix_postfix_styling',
            [
                'label' => esc_html__('Prefix and Postfix', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
            'heading_anim_color',
            [
                'label' => esc_html__('Font Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#313131',
                'selectors' => [
                    '{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline,{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline span' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'prefix_postfix_typography',
				'selector' => '{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline,{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline span',
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_heading_animation_styling',
            [
                'label' => esc_html__('Animated Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
            'ani_color',
            [
                'label' => esc_html__('Font Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#313131',
                'selectors' => [
                    '{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline b' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ani_typography',
				'selector' => '{{WRAPPER}} .pt-plus-heading-animation .pt-plus-cd-headline b',
			]
		);
		$this->add_control(
            'ani_bg_color',
            [
                'label' => esc_html__('Animation Background Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#d3d3d3',
				'condition' => [
					'anim_styles!' => ['style-6'],
				],
                'selectors' => [
                    '{{WRAPPER}} .pt-plus-heading-animation:not(.head-anim-style-6) .pt-plus-cd-headline b' => 'background: {{VALUE}};',
                ],
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

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';			
	}
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$anim_styles=$settings["anim_styles"];
		$prefix = !empty($settings["prefix"]) ? $settings["prefix"] : '';
		$postfix = !empty($settings["postfix"]) ? $settings["postfix"] : '';
		$ani_title = !empty($settings["ani_title"]) ? $settings["ani_title"] : '';
		$ani_title_tag = !empty($settings["ani_title_tag"]) ? $settings["ani_title_tag"] : 'h1';
		
			/*--On Scroll View Animation ---*/
				include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

			$heading_animation_back = 'style="';
			if(!empty($settings["ani_bg_color"])) {
				$heading_animation_back .='background: '.esc_attr($settings["ani_bg_color"]).';';
			}
			$heading_animation_back .= '"';
				
			// Order of replacement
			$order   = array("\r\n", "\n", "\r", "<br/>", "<br>");
			$replace = '|';
				
			// Processes \r\n's first so they aren't converted twice.
			$str = str_replace($order, $replace, $ani_title);
			
			$lines = explode("|", $str);
			
			$count_lines = count($lines);
				
			$background_css='';
			if(!empty($settings["ani_color"])) {
				$background_css .= 'background-color: '.esc_attr($settings["ani_color"]).';';
			}
			
		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
		/*--Plus Extra ---*/
		
		$uid = uniqid('heading-animation');
		
		$heading_animation ='<div class="pt-plus-heading-animation heading-animation head-anim-'.esc_attr($anim_styles).' '.esc_attr($animated_class).' '.esc_attr($uid).'"  '.$animation_attr.'>';

		if ($anim_styles == 'style-1') {	
			$heading_animation .='<'.theplus_validate_html_tag($ani_title_tag).' class="pt-plus-cd-headline letters type" >';
			if(!empty($prefix)){
				$heading_animation .='<span>'.wp_kses_post($prefix).' </span>';	
			}
			$heading_animation .='<span class="cd-words-wrapper waiting" '. wp_kses( $heading_animation_back, array('style' => array()) ).'>';
			$i=0;
			foreach($lines as $line)
			{
				if($i==0){
					$heading_animation .= '<b  class="is-visible"> '.strip_tags($line).'</b>';
				}else{
					$heading_animation .= '<b> '.strip_tags($line).'</b>';
				}
				$i++;
			}

			$strings = '['; 
				foreach($lines as $key => $line)  
				{ 
					$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
					if($key != ($count_lines-1))
					$strings .= ','; 
				} 
			$strings .= ']';		
			$heading_animation .= '</span>';
				if(!empty($postfix)){
					$heading_animation .='<span> '.wp_kses_post($postfix).' </span>';	
				}
			$heading_animation .= '</'.theplus_validate_html_tag($ani_title_tag).'>';
		}
		if ($anim_styles == 'style-2') {
			$heading_animation .='<'.theplus_validate_html_tag($ani_title_tag).' class="pt-plus-cd-headline rotate-1" >';
			if(!empty($prefix)){
				$heading_animation .='<span >'.wp_kses_post($prefix).' </span>';	
			}	
			$heading_animation .='<span class="cd-words-wrapper">';
			$i=0;
			foreach($lines as $line)
			{
				if($i==0){
					$heading_animation .= '<b  class="is-visible"> '.strip_tags($line).'</b>';
				}else{
					$heading_animation .= '<b> '.strip_tags($line).'</b>';
				}
				$i++;
			} 
			$strings = '['; 
			foreach($lines as $key => $line)  
			{ 
				$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
				if($key != ($count_lines-1))
				$strings .= ','; 
			} 
			$strings .= ']';
			$heading_animation .='</span>';	
			if(!empty($postfix)){
				$heading_animation .='<span > '.wp_kses_post($postfix).' </span>';	
			}
			$heading_animation .='</'.theplus_validate_html_tag($ani_title_tag).'>';	
		}
		if ($anim_styles == 'style-3') {
			$heading_animation .='<'.theplus_validate_html_tag($ani_title_tag).' class="pt-plus-cd-headline zoom" >';
			if(!empty($prefix)){
				$heading_animation .='<span >'.wp_kses_post($prefix).' </span>';	
			}	
			$heading_animation .='<span class="cd-words-wrapper">';
			$i=0;
			foreach($lines as $line)
			{
				if($i==0){
					$heading_animation .= ' <b  class="is-visible ">'.strip_tags($line).'</b>';
					}else{
					$heading_animation .= ' <b>'.strip_tags($line).'</b>';
				}
				$i++;
			}
			
			$strings = '['; 
			foreach($lines as $key => $line)  
			{ 
				$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
				if($key != ($count_lines-1))
				$strings .= ','; 
			} 
			$strings .= ']';
			$heading_animation .='</span>';
			if(!empty($postfix)){
				$heading_animation .='<span > '.wp_kses_post($postfix).' </span>';	
			}		
			$heading_animation .='</'.theplus_validate_html_tag($ani_title_tag).'>';	
		}
		if ($anim_styles == 'style-4') {
			$heading_animation .='<'.theplus_validate_html_tag($ani_title_tag).' class="pt-plus-cd-headline loading-bar " >';
			if(!empty($prefix)){
				$heading_animation .='<span >'.wp_kses_post($prefix).' </span>';	
			}
			$heading_animation .='<span class="cd-words-wrapper">';
			$i=0;
			foreach($lines as $line)
			{
				if($i==0){
					$heading_animation .= ' <b class="is-visible ">'.strip_tags($line).'</b>';
				}else{
					$heading_animation .= ' <b>'.strip_tags($line).'</b>';
				}
				$i++;
			}
			
			$strings = '['; 
			foreach($lines as $key => $line)  
			{ 
				$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
				if($key != ($count_lines-1))
				$strings .= ','; 
			} 
			$strings .= ']';				
			$heading_animation .='</span>';	
			if(!empty($postfix)){
				$heading_animation .='<span > '.wp_kses_post($postfix).'</span>';	
			}		
			$heading_animation .='</'.theplus_validate_html_tag($ani_title_tag).'>';	
		}		
		if ($anim_styles == 'style-5') {
			$heading_animation .='<'.theplus_validate_html_tag($ani_title_tag).' class="pt-plus-cd-headline push" >';
			if(!empty($prefix)){
				$heading_animation .='<span >'.wp_kses_post($prefix).' </span>';	
			}
			$heading_animation .='<span class="cd-words-wrapper">';
			$i=0;
			foreach($lines as $line)
			{
				if($i==0){
					$heading_animation .= '<b  class="is-visible "> '.strip_tags($line).'</b>';	
				}else{
					$heading_animation .= '<b> '.strip_tags($line).'</b>';
				}
				$i++;
			}
			
			$strings = '['; 
			foreach($lines as $key => $line)  
			{ 
				$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
				if($key != ($count_lines-1))
				$strings .= ','; 
			} 
			$strings .= ']';
			$heading_animation .='</span>';	
			if(!empty($postfix)){
				$heading_animation .='<span > '.wp_kses_post($postfix).' </span>';	
			}		
			$heading_animation .='</'.theplus_validate_html_tag($ani_title_tag).'>';
		}
		if ($anim_styles == 'style-6') {
			$heading_animation .='<'.theplus_validate_html_tag($ani_title_tag).' class="pt-plus-cd-headline letters scale" >';
			if(!empty($prefix)){
				$heading_animation .='<span >'.wp_kses_post($prefix).' </span>';	
			}
			$heading_animation .='<span class="cd-words-wrapper style-6"   >';
			$i=0;
			foreach($lines as $line)
			{
				if($i==0){
					$heading_animation .= '<b  class="is-visible ">'.strip_tags($line).'</b>';
					}else{
					$heading_animation .= '<b>'.strip_tags($line).'</b>';
				}
				$i++;
			}
			
			$strings = '['; 
			foreach($lines as $key => $line)  
			{ 
				$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
				if($key != ($count_lines-1))
				$strings .= ','; 
			} 
			$strings .= ']';
				$heading_animation .='</span>';	
			if(!empty($postfix)){
				$heading_animation .='<span > '.wp_kses_post($postfix).' </span>';	
			}
			$heading_animation .='</'.theplus_validate_html_tag($ani_title_tag).'>';	
		}
		$heading_animation .='</div>';
		
		$css_rule = '';
		$css_rule .= '<style>';
			$css_rule .= '.'.esc_attr($uid).' .pt-plus-cd-headline.loading-bar .cd-words-wrapper::after{'.esc_attr($background_css).'}';
		$css_rule .= '</style>';

		echo $css_rule.$before_content.$heading_animation.$after_content;
	}
    protected function content_template() {
	
    }
}