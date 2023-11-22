<?php

namespace ElementPack\Modules\WcAddToCart;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'wc-add-to-cart';
    }

    public function get_widgets() {

        $widgets = ['WC_Add_To_Cart'];

        return $widgets;
    }
}
