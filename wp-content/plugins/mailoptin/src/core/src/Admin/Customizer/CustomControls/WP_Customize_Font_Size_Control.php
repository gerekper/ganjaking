<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Font_Size_Control extends WP_Customize_Control
{
    public $type = 'mo-responsive-font-size';

    public function enqueue()
    {
        wp_enqueue_style(
            'mailoptin-font-size-control',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/font-size-control/control.css',
            array(),
            false
        );

        wp_enqueue_script(
            'mailoptin-font-size-control',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/font-size-control/control.js',
            array('jquery'),
            false,
            true
        );
    }

    public function render_content()
    {
        $devices = array('desktop', 'tablet', 'mobile'); ?>

        <span class="customize-control-title"><?php echo esc_attr($this->label); ?></span>

        <ul class="mo-responsive-options">
            <li class="desktop">
                <button type="button" class="preview-desktop active" data-device="desktop">
                    <i class="dashicons dashicons-desktop"></i>
                </button>
            </li>
            <li class="tablet">
                <button type="button" class="preview-tablet" data-device="tablet">
                    <i class="dashicons dashicons-tablet"></i>
                </button>
            </li>
            <li class="mobile">
                <button type="button" class="preview-mobile" data-device="mobile">
                    <i class="dashicons dashicons-smartphone"></i>
                </button>
            </li>
        </ul>

        <?php foreach ($devices as $device) { ?>

        <div class="mo-control-<?php echo esc_attr($device); ?>">

            <?php $link = $this->get_link() ?>

            <?php $link = str_replace('mobile', $device, $link); ?>

            <?php $link = str_replace('"', '', $link); ?>

            <label>

                <input style="width: 90%" type="number" <?php echo esc_html($link); ?> value="<?php echo esc_textarea($this->value()); ?>">
                <strong>px</strong>

            </label>

        </div>

        <?php }
    }

}
