<?php 
/*
Widget Name: Site Logo
Description: Site logo.
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

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Site_Logo extends Widget_Base {
		
	public function get_name() {
		return 'tp-site-logo';
	}

    public function get_title() {
        return esc_html__('Site Logo', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-fire theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-header');
    }

   
    protected function register_controls() {
		
		$this->start_controls_section(
			'normal_logo_sections',
			[
				'label' => esc_html__( 'Site Logo', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'logo_animate',
			[
				'label' => esc_html__( 'Logo Normal/Double', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal'  => esc_html__( 'Normal', 'theplus' ),					
					'double' => esc_html__( 'Double', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'logo_type',
			[
				'label' => esc_html__( 'Logo Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image'  => esc_html__( 'Image', 'theplus' ),					
					'svg' => esc_html__( 'SVG', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'image_logo',
			[
				'label' => esc_html__( 'Image Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'default' => [
					'url' => '',
				],
				'condition' => [
					'logo_animate' => ['normal','double'],
					'logo_type' => 'image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_logo_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'logo_animate' => ['normal','double'],
					'logo_type' => 'image',
				],
			]
		);
		$this->add_control(
			'svg_logo',
			[
				'label' => esc_html__( 'Only Svg Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__('Select Only .svg File from media library.','theplus'),
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'logo_animate' => ['normal','double'],
					'logo_type' => 'svg',
				],
			]
		);
		
		$this->add_responsive_control(
            'logo_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Logo Max Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .plus-site-logo .site-normal-logo img.image-logo-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'logo_animate' => ['normal','double'],					
				],
            ]
        );
		$this->add_control(
			'hover_image_logo',
			[
				'label' => esc_html__( 'Hover Logo Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'default' => [
					'url' => '',
				],
				'condition' => [
					'logo_animate' => 'double',
					'logo_type' => 'image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'hover_image_logo_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'logo_animate' => 'double',
					'logo_type' => 'image',
				],
			]
		);
		$this->add_control(
			'hover_svg_logo',
			[
				'label' => esc_html__( 'Hover Svg Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__('Select Only .svg File from media library.','theplus'),
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'logo_animate' => 'double',
					'logo_type' => 'svg',
				],
			]
		);
		$this->add_responsive_control(
            'hover_logo_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Hover Logo Max Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .plus-site-logo .site-normal-logo.hover-logo img.image-logo-wrap' => 'max-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'logo_animate' => 'double',
				],
            ]
        );
		
		$this->end_controls_section();
		
		
		$this->start_controls_section(
            'section_extra_options',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'logo_url_type',
			[
				'label' => esc_html__( 'Logo Url Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'home_url',
				'options' => [
					'home_url'  => esc_html__( 'Home URL', 'theplus' ),
					'custom_url' => esc_html__( 'Custom Link', 'theplus' ),
				],
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'logo_url',
			[
				'label' => esc_html__( 'Logo Url', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'show_external' => true,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
				],				
				'condition' => [
					'logo_url_type' => 'custom_url',
				],
			]
		);
		
		$this->add_responsive_control(
			'logo_alignment',
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
				'selectors' => [
					'{{WRAPPER}} .plus-site-logo' => 'text-align:{{VALUE}};',
				],
				'separator' => 'before',
				'default' => 'text-center',
				'toggle' => true,
				'label_block' => false,
			]
		);
		/*sticky logo strt*/
		$this->add_control(
			'sticky_logo',
			[
				'label' => esc_html__( 'Sticky Logo', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'logo_animate' => 'normal',
				],
			]
		);
		$this->add_control(
			'sticky_image_logo',
			[
				'label' => esc_html__( 'Image Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'default' => [
					'url' => '',
				],
				'condition' => [
					'logo_animate' => 'normal',
					'logo_type' => 'image',
					'sticky_logo' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'sticky_image_logo_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'logo_animate' => 'normal',
					'logo_type' => 'image',
					'sticky_logo' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_svg_logo',
			[
				'label' => esc_html__( 'Only Svg Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__('Select Only .svg File from media library.','theplus'),
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'logo_animate' => 'normal',
					'logo_type' => 'svg',
					'sticky_logo' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
            'sticky_logo_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Logo Max Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .plus-site-logo .site-normal-logo img.image-logo-wrap.sticky-image' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'logo_animate' => 'normal',
					'sticky_logo' => 'yes',
				],
            ]
        );
		/*sticky logo end*/
		$this->end_controls_section();
		
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$logo_alignment='';
		
				
		//Logo Url 
		$logo_url=$target =$nofollow ='';
		if($settings["logo_url_type"]=='home_url'){
			$logo_url=get_home_url();
		}else if($settings["logo_url_type"]=='custom_url'){
			$target = $settings['logo_url']['is_external'] ? ' target="_blank"' : '';
			$nofollow = $settings['logo_url']['nofollow'] ? ' rel="nofollow"' : '';
			$logo_url=$settings['logo_url']['url'];
		}
		
		$stcky_cls='';
		if(!empty($settings['sticky_logo']) && $settings['sticky_logo']=='yes'){
			$stcky_cls = 'tp-sticky-logo-cls';
		}
		//Normal Logo
		$logo_type=$settings["logo_type"];
		$normal_logo=$normal_logo_hover=$normal_hover='';
		if($logo_type=='image'){
			
			//Image logo	
			if(!empty($settings["image_logo"]["url"])){				
				$image_logo=$settings['image_logo']['id'];
				$img = wp_get_attachment_image_src($image_logo,$settings['image_logo_thumbnail_size']);
				$logo_image = !empty($img) ? $img[0] : '';
				$normal_logo = '<a href="'.esc_url($logo_url).'" ' . $target . $nofollow . ' title="'.esc_attr(get_bloginfo()).'" class="site-normal-logo image-logo" >';
					$normal_logo .= '<img src="'.esc_url($logo_image).'" class="image-logo-wrap normal-image '.esc_attr($stcky_cls).'" alt="'.esc_attr(get_bloginfo()).'">';
					
					if(!empty($settings['sticky_logo'] && $settings['sticky_logo']=='yes')){
						if(!empty($settings["sticky_image_logo"]["url"])){	
							$sticky_image_logo=$settings['sticky_image_logo']['id'];
							$sticky_img = wp_get_attachment_image_src($sticky_image_logo,$settings['sticky_image_logo_thumbnail_size']);
							$sticky_logo_image = $sticky_img[0];	
							$normal_logo .= '<img src="'.esc_url($sticky_logo_image).'" class="image-logo-wrap sticky-image" alt="'.esc_attr(get_bloginfo()).'">';
						}
					}
					
				$normal_logo .= '</a>';
			}

			if($settings["logo_animate"]=='double'){
				if(!empty($settings["hover_image_logo"]["url"])){					
					$hover_image_logo=$settings['hover_image_logo']['id'];
					$img = wp_get_attachment_image_src($hover_image_logo,$settings['hover_image_logo_thumbnail_size']);
					$hover_logo_image = $img[0];
					
					$normal_logo_hover ='<a href="'.esc_url($logo_url).'" ' . $target . $nofollow . ' title="'.esc_attr(get_bloginfo()).'" class="site-normal-logo image-logo hover-logo" >';
						$normal_logo_hover .='<img src="'.esc_url($hover_logo_image).'" class="image-logo-wrap" alt="'.esc_attr(get_bloginfo()).'">';
					$normal_logo_hover .='</a>';
					$normal_hover =' logo-hover-normal';
				}
			}
			
		}else if($logo_type=='svg'){
			
			//Svg logo
			if(!empty($settings["svg_logo"]["url"])){
				$logo_svg = $settings["svg_logo"]["url"];				
				$normal_logo ='<a href="'.esc_url($logo_url).'" ' . $target . $nofollow . ' title="'.esc_attr(get_bloginfo()).'" class="site-normal-logo svg-logo">';
					$normal_logo .='<img class="image-logo-wrap normal-image  '.esc_attr($stcky_cls).'" src="'.esc_url($logo_svg).'" alt="'.esc_attr(get_bloginfo()).'" />';
						
						if(!empty($settings['sticky_logo'] && $settings['sticky_logo']=='yes')){
							if(!empty($settings["sticky_svg_logo"]["url"])){	
								$sticky_svg_logo = $settings["sticky_svg_logo"]["url"];	
								$normal_logo .='<img class="image-logo-wrap sticky-image" src="'.esc_url($sticky_svg_logo).'" alt="'.esc_attr(get_bloginfo()).'" />';
							}
						}
				$normal_logo .='</a>';
			}
			
			
			if($settings["logo_animate"]=='double'){
				if(!empty($settings["hover_svg_logo"]["url"])){
					$hover_logo_svg = $settings["hover_svg_logo"]["url"];
					
					$normal_logo_hover ='<a href="'.esc_url($logo_url).'" ' . $target . $nofollow . ' title="'.esc_attr(get_bloginfo()).'" class="site-normal-logo svg-logo hover-logo">';
						$normal_logo_hover .='<img class="image-logo-wrap" src="'.esc_url($hover_logo_svg).'" alt="'.esc_attr(get_bloginfo()).'" />';
					$normal_logo_hover .='</a>';
					$normal_hover =' logo-hover-normal';
				}
			}
			
		}
		
		
		$site_logo ='<div class="plus-site-logo '.esc_attr($logo_alignment).' ">';
			$site_logo .= '<div class="site-logo-wrap '.esc_attr($normal_hover).'">';
				$site_logo .= $normal_logo;
				$site_logo .= $normal_logo_hover;
			$site_logo .= '</div>';
		$site_logo .='</div>';
		
		echo $site_logo;		
	}
	
	
    protected function content_template() {
	
    }
}