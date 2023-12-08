<?php

namespace ElementPack\Modules\GiveDonorWall;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'crypto-currency';
    }

    public function get_widgets() {

        $widgets = ['Give_Donor_Wall'];

        return $widgets;
    }
}
