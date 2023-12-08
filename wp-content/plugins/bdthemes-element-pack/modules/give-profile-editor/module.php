<?php

namespace ElementPack\Modules\GiveProfileEditor;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'give-profile-editor';
    }

    public function get_widgets() {

        $widgets = ['Give_Profile_Editor'];

        return $widgets;
    }
}
