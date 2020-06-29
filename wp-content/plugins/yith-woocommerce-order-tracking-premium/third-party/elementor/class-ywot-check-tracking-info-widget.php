<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWOT_Elementor_Check_Tracking_Info_Widget' ) ) {


    class YWOT_Elementor_Check_Tracking_Info_Widget extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywar-check-tracking-info-widget';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Check Order Tracking Info', 'yith-woocommerce-order-tracking' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'fa fa-code';
        }

        /**
         * Get widget categories.
         */
        public function get_categories() {
            return [ 'yith' ];
        }

        /**
         * Register widget controls.
         */
        protected function _register_controls() {

            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__( 'Content', 'yith-woocommerce-order-tracking' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );


            $this->add_control(
                'section-description',
                [
                    'label' => esc_html__( 'Form description', 'yith-woocommerce-order-tracking' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'text',
                    'placeholder' => esc_html__( 'Write a description here', 'yith-woocommerce-order-tracking' ),
                ]
            );




            $this->end_controls_section();

        }

        /**
         * Render widget output on the frontend.
         */
        protected function render() {

            $settings = $this->get_settings_for_display();

            $html = wp_oembed_get( $settings['section-description'] );

            
            echo '<div class="ywar-check-tracking-info-widget">';

            echo ( $html ) ? $html : $settings['section-description'];

            echo do_shortcode('[yith_check_tracking_info_form]');

            echo '</div>';

        }

    }











}
