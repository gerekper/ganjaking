<?php

namespace ElementPack\Modules\GiveTotals;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'give-totals';
    }

    public function get_widgets() {

        $widgets = ['Give_Totals'];

        return $widgets;
    }
}
