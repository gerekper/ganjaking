<?php

namespace ElementPack\Modules\GiveRegister;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'give-register';
    }

    public function get_widgets() {

        $widgets = ['Give_Register'];

        return $widgets;
    }
}
