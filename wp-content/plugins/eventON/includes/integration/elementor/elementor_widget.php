<?php
/**
 * Elementor Integration
 * @version 4.1.2
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVO_Elementor_Wig extends Widget_Base {


   public function get_id() {
      return 'eventon';
   }

   public function get_name() {
		return "EventON";
	}

   public function get_title() {
      return __( 'EventON', 'eventon' );
   }

   public function get_categories() {
		return [ 'eventon-category' ];
	}
  
   public function get_icon() {
      return 'eicon-coding evoIcon';
   }


   protected function register_controls() {

      $this->start_controls_section(
         'section',
         [
            'label' => __( 'EventON Calendar', 'eventon' ),
            'tab' => Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_control(
         'evo_shortcode',
         [
            'label' => __( 'Copy/Paste EventON Calendar Shortcode', 'eventon' ),
            'type' => Controls_Manager::TEXTAREA,
            'default'=>'[add_eventon]'
         ]
      );
      $this->add_control(
         'notes',
         [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<p class="evcal_btn trigger_shortcode_generator">'.__('Generate Shortcode','eventon').'</p>',
            'content_classes' => 'evo_test',
         ]
      );    
      $this->end_controls_section();
   }



   protected function render( $instance = [] ) {
   		$settings = $this->get_settings_for_display();

      
      	if(empty( $settings['evo_shortcode'] )){
            echo "<p class='evoelm_no_sc'>EventON Calendar</p>";
      	}else{
      		$C =$this->parse_text_editor( $settings['evo_shortcode'] );
      		echo $C;
      	} 
   }

   protected function content_template() {}

   public function render_plain_content( $instance = [] ) {}

}

Plugin::instance()->widgets_manager->register_widget_type( new EVO_Elementor_Wig );
