<?php
/**
 * Elementor Integration
 * @version 4.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVO_Elementor_Wig extends \Elementor\Widget_Base {


   public function get_id() {  return 'eventon';  }

   public function get_name() {	return "EventON";	}

   public function get_title() { return __( 'EventON', 'eventon' ); }

   public function get_categories() {	return [ 'eventon-category' ];	}
  
   public function get_icon() {  return 'eicon-coding evoIcon'; }

   protected function register_controls() {

      $this->start_controls_section(
         'section',
         [
            'label' => __( 'EventON Calendar', 'eventon' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_control(
         'evo_shortcode',
         [
            'label' => __( 'Copy/Paste EventON Calendar Shortcode', 'eventon' ),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
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

// event title @since 4.5
class EVO_Elementor_Wig_title extends \Elementor\Widget_Base {


    public function get_id() {  return 'eventon_title';  }
    public function get_name() {  return "EventON Event Title"; }
    public function get_title() { return __( 'EventON Event Title', 'eventon' ); }
    public function get_categories() {  return [ 'eventon-category' ];   }  
    public function get_icon() {  return 'eicon-coding evoIcon'; }

    protected function register_controls() {

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__( 'EventON Event Title', 'eventon' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $options_array = array();
        $wp_arg = array(
            'posts_per_page'=>-1,
            'post_type' => 'ajde_events',
            'post_status'=>'publish',
            'has_password'    => FALSE,
            'order'           =>'ASC',
            'orderby'         => 'menu_order',       
        );
        $events = new WP_Query($wp_arg);
        
        if( $events->have_posts()){
            foreach($events->posts as $pid=>$pd){
                $options_array[$pd->ID] = $pd->post_title;
            }
        }

        $this->add_control(
             'event_post_select',
             [
                'type' => \Elementor\Controls_Manager::SELECT,
                'label' => esc_html__('Select Event','eventon'),
                'default'=>'',
                'options'=>$options_array,
             ]
        );  
            
        $this->end_controls_section();
    }


    protected function render( $instance = [] ) {
        $settings = $this->get_settings_for_display();        
        
        if(!empty( $settings['event_post_select'] )){

            $event_id = esc_attr( $settings['event_post_select'] );

            echo "<h3 class='evo_event_title'>" . get_the_title( $event_id ) .'</h3>';
        }
        
    }

    protected function content_template() {}
    public function render_plain_content( $instance = [] ) {}

}