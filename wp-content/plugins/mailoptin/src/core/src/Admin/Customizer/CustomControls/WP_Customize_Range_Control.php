<?php
/**
 * Copyright (C) 2016  Agbonghama Collins <me@w3guy.com>
 */

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Range_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_range';

    public function enqueue()
    {
        wp_enqueue_script(
            'mailoptin-range-control',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/range-control.js',
            array('jquery'),
            false,
            true
        );
    }

    public function render_content()
    {
        ?>
        <label>
            <?php if ( ! empty( $this->label )) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <div class="cs-range-value"><?php echo esc_attr($this->value()); ?></div>
            <input data-input-type="range" type="range" <?php $this->input_attrs(); ?> value="<?php echo esc_attr($this->value()); ?>" <?php $this->link(); ?> />
            <?php if ( ! empty( $this->description )) : ?>
                <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>
        </label>
        <?php
    }
}