<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWGC_Elementor_Redeem_Widget' ) ) {


    class YWGC_Elementor_Redeem_Widget extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywgc-redeem-widget';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Gift Card Redeem Form', 'yith-woocommerce-gift-cards' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'fas fa-cash-register';
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
                'section-description',
                [
                    'label' => esc_html__( 'Form description', 'yith-woocommerce-gift-cards' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'text',
                    'placeholder' => esc_html__( 'Write a description here', 'yith-woocommerce-gift-cards' ),
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

            echo '<div class="ywgc-redeem-widget-elementor-widget">';

            echo ( $html ) ? $html : $settings['section-description'];

            echo do_shortcode('[yith_redeem_gift_card_form ]');

            echo '</div>';

        }

    }











}
