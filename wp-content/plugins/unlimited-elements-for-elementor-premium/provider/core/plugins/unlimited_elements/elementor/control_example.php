<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;

class ElementorWidgetTest extends Widget_Base {


    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return array('general-elements');
    }
    
    
    /**
	* register controls
     */
    protected function register_controls() {
    	
         $this->start_controls_section(
                'section_content', array(
                'label' => __('Content', 'hello-world'),
                    )
          );
          
          $this->add_control(
                'title', array(
                'label' => __('Title', 'hello-world'),
                'type' => Controls_Manager::TEXT,
                )
           );
           
           $this->end_controls_section();
           
    }
    

        /*
        Controls_Manager::TEXT
        Controls_Manager::NUMBER
        Controls_Manager::ANIMATION
        Controls_Manager::BOX_SHADOW
        Controls_Manager::BUTTON
        Controls_Manager::CHOOSE
		Controls_Manager::SWITCHER        
        Controls_Manager::CODE
        Controls_Manager::DATE_TIME
        Controls_Manager::DIMENSIONS
        Controls_Manager::DIVIDER
        Controls_Manager::FONT
        Controls_Manager::GALLERY
        Controls_Manager::HEADING
        Controls_Manager::HIDDEN
        Controls_Manager::HOVER_ANIMATION
        Controls_Manager::ICON
        Controls_Manager::IMAGE_DIMENSIONS
        Controls_Manager::MEDIA
        Controls_Manager::TEXT_SHADOW
        Controls_Manager::TEXTAREA
        Controls_Manager::URL
        Controls_Manager::TABS
        Controls_Manager::RAW_HTML
        */
    
    
}
