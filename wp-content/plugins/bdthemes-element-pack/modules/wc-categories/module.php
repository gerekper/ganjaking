<?php

namespace ElementPack\Modules\WcCategories;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'wc-categories';
    }

    public function get_widgets() {

        $widgets = ['WC_Categories'];

        return $widgets;
    }
}
