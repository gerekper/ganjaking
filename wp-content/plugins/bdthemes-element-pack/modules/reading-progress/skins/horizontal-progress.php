<?php

namespace ElementPack\Modules\ReadingProgress\Skins;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Horizontal_Progress extends Elementor_Skin_Base
{

    public function get_id()
    {
        return 'bdt-horizontal-progress';
    }

    public function get_title()
    {
        return __('Horizontal Progress', 'bdthemes-element-pack');
    }

    public function render()
    {
        $settings = $this->parent->get_settings();
        $position = $settings['horizontal_reading_progress_position'];
 
        ?>
        <div class="bdt-horizontal-progress <?php echo $position; ?>" id="bdt-progress">
        </div>

        <?php
}

}
