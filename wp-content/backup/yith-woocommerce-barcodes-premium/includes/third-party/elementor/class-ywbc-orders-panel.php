<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWBC_Elementor_Orders_Panel' ) ) {


    class YWBC_Elementor_Orders_Panel extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywgc-orders-panel';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Barcodes Orders Panel', 'yith-woocommerce-barcodes' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'eicon-product-info';
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
                    'label' => esc_html__( 'Content', 'yith-woocommerce-barcodes' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'section-description',
                [
                    'label' => esc_html__( 'Form description', 'yith-woocommerce-barcodes' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'text',
                    'placeholder' => esc_html__( 'Write a description here', 'yith-woocommerce-barcodes' ),
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

            echo '<div class="ywgc-orders-panel-elementor-widget">';

            echo ( $html ) ? $html : $settings['section-description'];

            echo do_shortcode('[yith_order_barcode]');

            echo '</div>';

        }

    }











}
