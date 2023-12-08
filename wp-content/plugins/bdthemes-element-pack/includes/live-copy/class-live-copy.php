<?php

namespace ElementPack\Includes\LiveCopy;

use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Controls_Stack;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ElementPack_Live_Copy {

    public function __construct() {
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'live_copy_enqueue']);
    }
    public function live_copy_enqueue() {
        wp_enqueue_script('bdt-live-copy-scripts', BDTEP_URL . 'includes/live-copy/assets/ep-live-copy.min.js', ['jquery', 'elementor-editor'], BDTEP_VER, true);
    }
}

new ElementPack_Live_Copy();
