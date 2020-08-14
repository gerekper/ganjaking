<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

class YITH_YWQA_Show_Questions_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'yith-ywqa-show-questions';
    }

    public function get_title() {
        return esc_html__( 'YITH Questions and Answers', 'yith-woocommerce-questions-and-answers' );
    }

    public function get_icon() {
        return 'far fa-question-circle';
    }

    public function get_categories() {
        return [ 'yith' ];
    }

    public function get_keywords() {
        return [ 'woocommerce', 'product', 'questions', 'and', 'answers' ];
    }
    protected function _register_controls() {

    }

    protected function render() {


        echo '<div class="yith-ywqa-show-questions-elementor-widget">';

        echo is_callable('apply_shortcodes') ? apply_shortcodes('[ywqa_questions]') : do_shortcode( '[ywqa_questions]' );

        echo '</div>';
    }

}