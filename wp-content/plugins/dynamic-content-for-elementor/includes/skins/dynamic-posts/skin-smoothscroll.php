<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_Smoothscroll extends \DynamicContentForElementor\Includes\Skins\Skin_Base
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_smoothscroll_controls']);
    }
    public $depended_scripts = ['dce-dynamicPosts-smoothscroll'];
    public $depended_styles = ['dce-dynamicPosts-smoothscroll'];
    public function get_id()
    {
        return 'smoothscroll';
    }
    public function get_title()
    {
        return __('Smoothscroll', 'dynamic-content-for-elementor');
    }
    public function register_additional_smoothscroll_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_smoothscroll', ['label' => __('Smoothscroll', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        // Width
        $this->add_responsive_control('smoothscroll_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vh'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 800, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-smoothscroll-container .dce-smoothscroll-wrapper .dce-smoothscroll-item' => 'width: {{SIZE}}{{UNIT}};']]);
        // Alternanza sinistra / destra
        $this->add_responsive_control('smoothscroll_alternate', ['label' => __('Alternate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vw'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 400, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-smoothscroll-container .dce-smoothscroll-wrapper .dce-smoothscroll-item:nth-child(even)' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-smoothscroll-container .dce-smoothscroll-wrapper .dce-smoothscroll-item:nth-child(odd)' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        // Spazio Righe
        $this->add_responsive_control('smoothscroll_rowspace', ['label' => __('Row space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vh'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 400, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-smoothscroll-container .dce-smoothscroll-wrapper .dce-smoothscroll-item:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        // Padding sopra /sotto
        $this->add_responsive_control('smoothscroll_padding', ['label' => __('Padding of content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vh'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 400, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-smoothscroll-container .dce-smoothscroll-wrapper' => 'padding: {{SIZE}}{{UNIT}} 0 {{SIZE}}{{UNIT}};']]);
        //////////////////////////////////////////////////////////////////
        // ------------------- BLOCK
        /*$this->add_control(
        			'smoothscroll_heading_block', [
        				'label' => __('Block', 'dynamic-content-for-elementor'),
        				'type' => Controls_Manager::HEADING,
        				'separator' => 'before',
        			]
        		);
        		$this->add_control(
        			'smoothscroll_block_perspective', [
        				'label' => __('Perspective', 'dynamic-content-for-elementor'),
        				'type' => Controls_Manager::SWITCHER,
        				'default' => 'yes',
        
        			]
        		);*/
        // ------------------- IMAGE
        $this->add_control('smoothscroll_heading_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('smoothscroll_image_perspective', ['label' => __('Perspective', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('smoothscroll_image_scale', ['label' => __('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('smoothscroll_image_translatey', ['label' => __('Translate Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        // ------------------- CONTENT
        $this->add_control('smoothscroll_heading_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('smoothscroll_content_translatey', ['label' => __('Translate Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        $this->start_controls_section('section_style_smoothscroll', ['label' => __('Smoothscroll', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->end_controls_section();
    }
    public function render()
    {
        parent::render();
    }
    /*protected function render_post_items() {
    
    
    		$item = [];
    
    		//
    		$this->render_repeater_item_start('item_image');
    		$this->render_featured_image($item);
    		$this->render_repeater_item_end();
    
    		$this->render_repeater_item_start('item_title');
    		$this->render_title($item);
    		$this->render_repeater_item_end();
    		//
    
    	}
    	protected function render_featured_image($settings) {
    
    		$use_bgimage = $this->get_instance_value('use_bgimage');
    		//
    		$use_overlay = $this->get_instance_value('use_overlay');
    		$use_overlay_hover = $this->get_parent()->get_settings('use_overlay_hover');
    
    		//
    		$use_link = 'yes'; //$settings['use_link'];;
    
    		// ---------------------------------------
    		$setting_key = $this->get_instance_value('thumbnail_size_size');
    
    		$image_attr = [
    			//'class' => 'dce-img', // così non va bene!
    		];
    		$image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail_size');
    		$thumbnail_html = wp_get_attachment_image( get_post_thumbnail_id(), $setting_key, false, $image_attr );
    
    		if ( empty( $thumbnail_html ) ) {
    			return;
    		}
    
    		$bgimage = '';
    		if($use_bgimage){
    			$bgimage = ' dce-post-bgimage';
    		}
    		$overlayimage = '';
    		if($use_overlay){
    			$overlayimage = ' dce-post-overlayimage';
    		}
    		$overlayhover = '';
    		if($use_overlay_hover){
    			$overlayhover = ' dce-post-overlayhover';
    		}
    		$html_tag = 'div';
    		$attribute_link = '';
    		if($use_link){
    			$html_tag = 'a';
    			$attribute_link = ' href="'.$this->current_permalink.'"';
    		}
    		echo '<'.$html_tag.' class="dce-post-image'.$bgimage.$overlayimage.$overlayhover.'"'.$attribute_link.'>';
    
    			if($use_bgimage){
    				echo '<figure class="dce-img dce-bgimage" style="background: url('.$image_url[0].') no-repeat center; background-size: cover; display: block;"></figure>';
    			}else{
    				echo '<figure class="dce-img">'.$thumbnail_html.'</figure>';
    			}
    
    		echo '</'.$html_tag.'>';
    
    	}*/
    // Classes ----------
    public function get_container_class()
    {
        return 'dce-smoothscroll-container dce-skin-' . $this->get_id();
    }
    public function get_wrapper_class()
    {
        return 'dce-smoothscroll-wrapper dce-wrapper-' . $this->get_id();
    }
    public function get_item_class()
    {
        return 'dce-smoothscroll-item dce-item-' . $this->get_id();
    }
}
