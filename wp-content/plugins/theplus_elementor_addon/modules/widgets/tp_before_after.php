<?php 
/*
Widget Name: Before After Image 
Description: Horizontal,Vertical and Opacity before/after Image
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Before_After extends Widget_Base {
		
	public function get_name() {
		return 'tp-before-after';
	}

    public function get_title() {
        return esc_html__('Before After', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-star-half-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'type', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Style', 'theplus'),
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => esc_html__('Horizontal', 'theplus'),
                    'vertical' => esc_html__('Vertical', 'theplus'),
                    'cursor' => esc_html__('Opacity', 'theplus'),
                ],
            ]
        );
		$this->add_control(
            'before_image', [
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__('Image for Before', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
			]
        );
		$this->add_control(
            'before_label',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Label for Before', 'theplus'),
                'label_block' => true,
                'separator' => 'after',
                'default' => '',
				'dynamic' => [
					'active'   => true,
				],
            ]
        );
		$this->add_control(
            'after_image', [
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__('Image for After', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
			]
        );
		$this->add_control(
            'after_label',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Label for After', 'theplus'),
                'label_block' => true,
                'separator' => 'after',
                'default' => '',
				'dynamic' => [
					'active'   => true,
				],
            ]
        );
		
	$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'option_settings',
            [
                'label' => esc_html__('Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'alignment',
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
				'default' => 'left',
				'toggle' => false,
			]
		);
		$this->add_control(
			'full_switch',
			[
				'label'   => esc_html__( 'Full Width', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'click_hover_move',
			[
				'label'   => esc_html__( 'Mouse Hover option', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->add_control(
			'separate_switch',
			[
				'label'   => esc_html__( 'Separator Line', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
				],
			]
		);
		$this->add_control(
            'separator_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Separator Style', 'theplus'),
                'default' => 'middle',
                'options' => [
                    'middle' => esc_html__('Middle', 'theplus'),
                    'bottom' => esc_html__('Bottom', 'theplus'),
                ],
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
					'separate_switch' => 'yes',
				],
            ]
        );
		
		$this->add_control(
            'separate_width', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Separator Width', 'theplus'),
				'size_units' => ['px'],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
					'separate_switch' => 'yes',
				],
			]
        );
		$this->add_control(
            'separate_color',
            [
                'label' => esc_html__('Separator Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
					'separate_switch' => 'yes',
				],
            ]
        );
		$this->add_control(
            'separate_bg_color',
            [
                'label' => esc_html__('Separator Bottom Bg Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(200,200,200,0.7)',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_before_after .before-after-bottom-separate' => 'background:{{VALUE}};',
                ],
				'separator' => 'after',
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
					'separate_switch' => 'yes',
					'separator_style' => 'bottom',
				],
            ]
        );
		$this->add_control(
            'image_separator', [
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__('Separator Icon', 'theplus'),
				'separator' => 'before',
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
					'separate_switch' => 'yes',
				],
			]
        );
		$this->add_control(
            'separate_position', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Separator Position', 'theplus'),
				'size_units' => '%',
				'default' => [
					'size' => 50,
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'type' => [ 'horizontal','vertical' ],
					'separate_switch' => 'yes',
				],
			]
        );
		
		$this->end_controls_section();
		/*label option start*/
		$this->start_controls_section(
            'label_option_settings',
            [
                'label' => esc_html__('Label Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'label_option_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em','%'],				
				'selectors' => [
					'{{WRAPPER}} .before-after-inner .before-after-image .before_after_label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_option_typography',
				'selector' => '{{WRAPPER}} .before-after-inner .before-after-image .before_after_label',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'label_option_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .before-after-inner .before-after-image .before_after_label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'label_option_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .before-after-inner .before-after-image .before_after_label',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'label_option_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .before-after-inner .before-after-image .before_after_label',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'label_option_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .before-after-inner .before-after-image .before_after_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'label_option_shadow',
				'selector' => '{{WRAPPER}} .before-after-inner .before-after-image .before_after_label',				
			]
		);
		$this->end_controls_section();
		/*label option end*/
		
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
		
		$type = $settings['type'];
		$click_hover_move = (($settings['click_hover_move']=='yes') ? 'on' : 'off' ); 
		$separate_width = (!empty($settings['separate_width']['size'])) ? $settings['separate_width']['size'] : 3;
		$separate_width_unit = (!empty($settings['separate_width']['unit'])) ? $settings['separate_width']['unit'] : 'px';
		$separate_position = !empty($settings['separate_position']['size']) ? $settings['separate_position']['size'] : '';
		$separator_style = $settings['separator_style'];
		$separate_switch = (($settings['separate_switch']=='no') ? 'false' : 'true' );
		$separate_color = $settings['separate_color'];
		$alignment= (!empty($settings["alignment"])) ? 'text-'.$settings["alignment"] : '';
		$uid=uniqid("bf_af");
			$attr_data=$image_sep=$middle_separator=$bottom_separator=$before_image_tag=$after_image_tag=$sep_style=$before_label_text=$after_label_text='';
			
			$attr_data .=' data-id="'.esc_attr($uid).'" ';
			$attr_data .=' data-type="'.esc_attr($type).'" ';
			$attr_data .=' data-click_hover_move="'.esc_attr($click_hover_move).'" ';
			$attr_data .=' data-separate_width="'.esc_attr($separate_width).'" ';
			$attr_data .=' data-separate_position="'.esc_attr($separate_position).'" ';
			$attr_data .=' data-separator_style="'.esc_attr($separator_style).'" ';
			$attr_data .=' data-show="1" ';
			$attr_data .=' data-responsive="yes" ';
			$attr_data .= (!empty($settings["full_switch"]) && $settings["full_switch"]=='yes') ? ' data-full_width="yes" ' : ' data-full_width="no" ';
			$attr_data .=' data-width="0" ';
			$attr_data .=' data-max-width="0" ';
			$attr_data .=' data-separate_switch="'.esc_attr($separate_switch).'" ';
			
			if(!empty($settings['image_separator']['url'])){
				$attr_data .=' data-separate_image="2" ';
			}else{
				$attr_data .=' data-separate_image="1" ';
			}
			
			if(!empty($settings['before_image']['url'])){
				
				if(!empty($settings['before_image']) && !empty($settings['thumbnail_size'])){
					$before_image=$settings['before_image']['id'];
					$before_imgSrc = wp_get_attachment_image( $before_image, $settings['thumbnail_size'],false, ['class' => 'image-before-wrap' ] );
				}else{
					$before_imgSrc = $settings['before_image']['url'];
				}
				
				$before_image_tag=$before_imgSrc;
			}
			if(!empty($settings['after_image']['url'])){
				if(!empty($settings['after_image']) && !empty($settings['thumbnail_size'])){
					$after_image=$settings['after_image']['id'];					
					$after_imgSrc = wp_get_attachment_image( $after_image, $settings['thumbnail_size'],false, ['class' => 'image-after-wrap' ] );
				}else{
					$after_imgSrc = $settings['after_image']['url'];
				}
				
				
				$after_image_tag=$after_imgSrc;
			}
			if(!empty($separate_switch) && $separate_switch=='true'){
				$sep_style=' style="background: '.esc_attr($separate_color).';"';
				if(!empty($settings['image_separator']['url'])){
					$image_id=$settings["image_separator"]["id"];
					$imgSrc= tp_get_image_rander( $image_id,'full', [ 'class' => 'service-img' ] );
					
					$image_sep= '<div class="before-after-sep-icon">'.$imgSrc.'</div>';
				}
				if(!empty($type) && ($type=='horizontal' || $type=='vertical')){
				if($separator_style=='middle'){
					$middle_separator='<div class="before-after-sep" '.$sep_style.'></div>';
				}else{
					$middle_separator='<div class="before-after-sep" '.$sep_style.'></div>';
					$bottom_separator='<div class="before-after-bottom-separate"></div>';
				}
				}
			}
			
			if(!empty($settings['before_label'])){
				$before_label_text='<div class="before_after_label before_label_text">'.esc_html($settings["before_label"]).'</div>';
			}
			if(!empty($settings["after_label"])){
				$after_label_text='<div class="before_after_label after_label_text">'.esc_html($settings["after_label"]).'</div>';
			}
				
			/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

			/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-before-after-widget";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
				
				$bf_af ='<div class="pt_plus_before_after '.esc_attr($uid).' '.esc_attr($alignment).' '.$animated_class.'" '.$attr_data.'  '.$animation_attr.'>';
					$bf_af .='<div class="before-after-inner">
						<div class="before-after-image image-before">
							'.$before_image_tag.'
							'.$before_label_text.'
						</div>
						<div class="before-after-image image-after">
							'.$after_image_tag.'
							'.$after_label_text.'
						</div>
						'.$middle_separator.'
						'.$image_sep.'
					</div>
					'.$bottom_separator;
				$bf_af .='</div>';
			
			echo $before_content.$bf_af.$after_content;
	}
    protected function content_template() {
	
    }
}