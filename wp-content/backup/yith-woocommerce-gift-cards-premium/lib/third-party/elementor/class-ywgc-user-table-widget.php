<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWGC_Elementor_User_Table_Widget' ) ) {


    class YWGC_Elementor_User_Table_Widget extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywgc-user-table-widget';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Gift Card User Table', 'yith-woocommerce-gift-cards' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'fas fa-table';
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

            echo '<div class="ywgc-user-table-widget-elementor-widget">';

            echo ( $html ) ? $html : $settings['section-description'];

            echo do_shortcode('[yith_gift_cards_user_table]');

            echo '</div>';

        }

    }











}
