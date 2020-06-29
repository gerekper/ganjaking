<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Autocomplete_Input_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_autocomplete';

    public $field_id = 'mo-autocomplete';

    public $options = [];

    public $input_type = 'text';

    public $sub_description;

    public function enqueue()
    {
        wp_deregister_script('jquery-ui-core');
        wp_dequeue_script('jquery-ui-core');
        wp_enqueue_script(
            'mailoptin-jqueryui',
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
            array('jquery'),
            false,
            true
        );
        wp_enqueue_script(
            'mailoptin-autocomplete-init',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/autocomplete-init.js',
            array('jquery', 'customize-base', 'mailoptin-jqueryui'),
            false,
            true
        );
    }

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
            <input id="<?php echo $this->field_id; ?>" data-block-type="autocomplete" data-autocomplete-options="<?php echo esc_attr(wp_json_encode($this->options)); ?>" type="<?php echo esc_attr($this->input_type); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr($this->value()); ?>" <?php $this->link(); ?> />
            <?php if (!empty($this->sub_description)) : ?>
                <span class="description customize-control-description"><?php echo $this->sub_description; ?></span>
            <?php endif; ?>
        </label>
        <?php
    }
}