<?php

namespace ElementPack\Base;

use Elementor\Widget_Base;
use ElementPack\Element_Pack_Loader;

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

abstract class Module_Base extends Widget_Base {

    public function get_style_depends() {

        if ( method_exists($this, '_get_style_depends') ) {
            if ( $this->ep_is_edit_mode() ) {
                return ['ep-all-styles'];
            }
            return $this->_get_style_depends();
        }
        return array();
    }

    protected function ep_is_edit_mode() {

        if ( Element_Pack_Loader::elementor()->preview->is_preview_mode() || Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
            return true;
        }

        return false;
    }
}

