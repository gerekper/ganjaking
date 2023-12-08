<?php

namespace ElementPack\Modules\GiveForm;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'give-form';
    }

    public function get_widgets() {

        $widgets = ['Give_Form'];

        return $widgets;
    }
}
