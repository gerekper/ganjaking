<?php

namespace ElementPack\Modules\WcCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'wc-carousel';
    }

    public function get_widgets() {

        $widgets = ['WC_Carousel'];

        return $widgets;
    }
}
