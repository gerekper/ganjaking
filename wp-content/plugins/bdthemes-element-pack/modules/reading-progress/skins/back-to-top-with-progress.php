<?php

namespace ElementPack\Modules\ReadingProgress\Skins;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Back_To_Top_With_Progress extends Elementor_Skin_Base
{

    public function get_id()
    {
        return 'bdt-back-to-top-with-progress';
    }

    public function get_title()
    {
        return __('Back To Top With Progress', 'bdthemes-element-pack');
    }
 
    public function render()
    {
        $positionProgress =$this->parent->get_settings('progress_position');
        ?>
       
        <div class="bdt-progress-with-top">
            <div class="bdt-progress-wrap <?php echo $positionProgress;?>">
                <svg class="bdt-progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
                </svg>
            </div>
        </div>
        
        <?php
    }

}
