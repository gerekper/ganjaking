<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWAR_Elementor_Reviews_List_Widget' ) ) {


    class YWAR_Elementor_Reviews_List_Widget extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywar-reviews-list-widget';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Advanced Reviews List', 'yith-woocommerce-advanced-reviews' );
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
                    'label' => esc_html__( 'Content', 'yith-woocommerce-advanced-reviews' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );
            

            $this->add_control(
                'reviews-to-display-attr',
                [
                    'label' => esc_html__( 'Reviews to display', 'yith-woocommerce-advanced-reviews' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'input_type' => 'number',
                ]
            );

            $this->add_control(
                'pagination-attr',
                [
                    'label' => esc_html__( 'Allow pagination', 'yith-woocommerce-advanced-reviews' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'input_type' => 'number',
                ]
            );

            $this->add_control(
                'product-id-attr',
                [
                    'label' => esc_html__( 'Product ID', 'yith-woocommerce-advanced-reviews' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'input_type' => 'number',
                ]
            );




            $this->end_controls_section();

        }

        /**
         * Render widget output on the frontend.
         */
        protected function render() {

            $settings = $this->get_settings_for_display();

            $atts = "";

            if ( $settings['reviews-to-display-attr'] > 0 ){
                $atts .= 'reviews_number= ' . $settings['reviews-to-display-attr'];
            }

            if ( $settings['product-id-attr'] > 0 ){
                $atts .= ' product_id=' . $settings['product-id-attr'];
            }

            if ( $settings['pagination-attr'] == 'yes' ){
                $atts .= ' pagination=' . $settings['pagination-attr'];
            }

            
            echo '<div class="ywar-reviews-list-widget">';

            echo do_shortcode('[yith_ywar_show_reviews ' . $atts . ' ]');

            echo '</div>';

        }

    }











}
