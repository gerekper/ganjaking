<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWGC_Elementor_Form_Widget' ) ) {


    class YWGC_Elementor_Form_Widget extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywgc-form-widget';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Gift Card Product Form', 'yith-woocommerce-gift-cards' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'fas fa-address-card';
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
                    'label' => esc_html__( 'Content', 'yith-woocommerce-gift-cards' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'section-title',
                [
                    'label' => esc_html__( 'Form Title', 'yith-woocommerce-gift-cards' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'text',
                    'placeholder' => esc_html__( 'Your section title', 'yith-woocommerce-gift-cards' ),
                ]
            );

            $this->end_controls_section();

        }

        /**
         * Render widget output on the frontend.
         */
        protected function render() {

            $settings = $this->get_settings_for_display();

            $html = wp_oembed_get( $settings['section-title'] );

            echo '<div class="ywgc-form-widget-elementor-widget">';

            echo ( $html ) ? $html : $settings['section-title'];

            echo do_shortcode('[yith_ywgc_display_gift_card_form]');

            echo '</div>';

        }

    }











}
