<?php
/**
 * Input fields with bottom sub description below field.
 */

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Custom_Input_Control extends WP_Customize_Control
{
    public $input_type = 'text';

    public $sub_description;

    public function render_content()
    {
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif;
            if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>
            <input type="<?php echo esc_attr($this->input_type); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr($this->value()); ?>" <?php $this->link(); ?> />
            <?php if (!empty($this->sub_description)) : ?>
                <span class="description customize-control-description"><?php echo $this->sub_description; ?></span>
            <?php endif; ?>
        </label>
        <?php
    }
}