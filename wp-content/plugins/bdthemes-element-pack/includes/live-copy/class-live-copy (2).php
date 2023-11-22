<?php

namespace ElementPack\Includes\LiveCopy;

use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Controls_Stack;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ElementPack_Live_Copy {

    function __construct() {
        add_action('wp_ajax_ep_elementor_import_live_copy_assets_files', [$this, 'ajax_import_data']);
        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'live_copy_enqueue' ] );
    }

    public function live_copy_enqueue(){
        wp_enqueue_script( 'bdt-live-copy-storage', BDTEP_URL . 'includes/live-copy/assets/xdLocalStorage.min.js', [], BDTEP_VER, true );
        wp_enqueue_script( 'bdt-live-copy-scripts', BDTEP_URL . 'includes/live-copy/assets/ep-live-copy.min.js', [ 'jquery', 'elementor-editor', 'bdt-live-copy-storage'], BDTEP_VER, true);

        wp_localize_script(
            'bdt-live-copy-scripts',
            'bdt_ep_live_copy',
            [
                'magic_key' => 'magic_copy_data',
                'ajax_url'  => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'magic_copy_data' ),
            ]
        );
    }

    public function ajax_import_data() {
        $nonce = isset($_REQUEST['security']) ? sanitize_text_field($_REQUEST['security']) : '';
        $data  = isset($_REQUEST['data']) ? wp_unslash($_REQUEST['data']) : '';

        if ( !wp_verify_nonce($nonce, 'magic_copy_data') || empty($data) ) {
            wp_send_json_error(__('Sorry, invalid nonce or empty content!', 'bdthemes-element-pack'));
        }

        $data = [json_decode($data, true)];

        $data = $this->ready_for_import($data);
        $data = $this->import_content($data);

        wp_send_json_success($data);
    }

    protected function process_import_content(Controls_Stack $element) {
        $element_data = $element->get_data();
        $method       = 'on_import';

        if ( method_exists($element, $method) ) {
            $element_data = $element->{$method}($element_data);
        }

        foreach ( $element->get_controls() as $control ) {
            $control_class = Plugin::instance()->controls_manager->get_control($control['type']);

            if ( !$control_class ) {
                return $element_data;
            }

            if ( method_exists($control_class, $method) ) {
                $element_data['settings'][$control['name']] = $control_class->{$method}($element->get_settings($control['name']), $control);
            }
        }

        return $element_data;
    }

    protected function ready_for_import($content) {
        return Plugin::instance()->db->iterate_data($content, function ($element) {
            $element['id'] = Utils::generate_random_string();
            return $element;
        });
    }

    protected function import_content($content) {
        return Plugin::instance()->db->iterate_data(
            $content,
            function ($element_data) {
                $element = Plugin::instance()->elements_manager->create_element_instance($element_data);

                if ( !$element ) {
                    return null;
                }

                return $this->process_import_content($element);
            }
        );
    }

}

new ElementPack_Live_Copy();