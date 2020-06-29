<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Date_Picker_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_datetime';

    public function enqueue()
    {
        wp_enqueue_script(
            'mailoptin-datetime-transition',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/datetime-control/transition.js',
            array('jquery'),
            false,
            true
        );

        wp_enqueue_script(
            'mailoptin-datetime-collapse',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/datetime-control/collapse.js',
            array('jquery'),
            false,
            true
        );

        wp_enqueue_script(
            'mailoptin-datetime-moment',
            MAILOPTIN_ASSETS_URL . 'js/src/moment.js',
            array('jquery'),
            false,
            true
        );

        wp_enqueue_script(
            'mailoptin-datetime-datetimepicker',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/datetime-control/bootstrap-datetimepicker.min.js',
            array('jquery'),
            false,
            true
        );

        wp_enqueue_script(
            'mailoptin-datetime-datetimepicker-init',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/datetime-init.js',
            array('jquery', 'mailoptin-datetime-moment', 'mailoptin-datetime-datetimepicker', 'customize-base'),
            false,
            true
        );

        wp_enqueue_style(
            'mailoptin-bs',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/datetime-control/bootstrap.css'
        );

        wp_enqueue_style(
            'mailoptin-bs-datetimepicker',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/datetime-control/bootstrap-datetimepicker.min.css',
            array('mailoptin-bs')
        );
    }

    public function render_content()
    {
        ?>
        <label for="_<?php echo $this->id ?>">
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
        </label>

        <div class="mo-datetime-container">
            <input id="_<?php echo $this->id ?>" type="text" value="<?php echo $this->value() ?>" class="mo-date-picker" <?php $this->link(); ?>/>
            <span class="dashicons dashicons-calendar-alt mo-datetime-container-icon"></span>
        </div>

        <?php if (!empty($this->description)) : ?>
        <span class="description customize-control-description"><?php echo $this->description; ?></span>
    <?php endif; ?>
        <?php
    }
}