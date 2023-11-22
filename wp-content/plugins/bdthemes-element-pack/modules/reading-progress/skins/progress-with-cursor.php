<?php

namespace ElementPack\Modules\ReadingProgress\Skins;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Progress_With_Cursor extends Elementor_Skin_Base {

    public function get_id() {
        return 'bdt-progress-with-cursor';
    }

    public function get_title() {
        return __('Progress With Cursor', 'bdthemes-element-pack');
    }
 
    public function render() {
         
        ?>
        <div class="bdt-progress-with-cursor">
            <div class='bdt-cursor'></div>
            <div class='bdt-cursor2'>					
                <div class="bdt-progress-wrap">
                    <svg class="bdt-progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
                    </svg>
                </div>
            </div>
            <div class='bdt-cursor3'></div>
        </div>

        <?php
    }

}
