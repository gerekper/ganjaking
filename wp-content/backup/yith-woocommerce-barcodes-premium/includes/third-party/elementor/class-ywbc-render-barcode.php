<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YWBC_Elementor_Render_Barcode' ) ) {


    class YWBC_Elementor_Render_Barcode extends \Elementor\Widget_Base  {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'ywgc-render-barcode';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return esc_html__( 'YITH Barcodes Render Barcode by ID', 'yith-woocommerce-barcodes' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'eicon-barcode';
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
		        'id-attr',
		        [
			        'label' => esc_html__( 'Post ID', 'yith-woocommerce-barcodes' ),
			        'type' => \Elementor\Controls_Manager::NUMBER,
			        'input_type' => 'number',
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

	        $atts = "";

	        if ( $settings['id-attr'] > 0 ){
		        $atts .= 'id= ' . $settings['id-attr'];
	        }

            $html = wp_oembed_get( $settings['section-description'] );

            echo '<div class="ywgc-render-barcode-elementor-widget">';

            echo ( $html ) ? $html : $settings['section-description'];

            echo do_shortcode('[yith_render_barcode ' . $atts . ']');

            echo '</div>';

        }

    }











}
