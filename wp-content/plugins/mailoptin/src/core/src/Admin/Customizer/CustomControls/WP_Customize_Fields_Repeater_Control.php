<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\OptinForms\AbstractOptinForm;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use WP_Customize_Control;
use function MailOptin\Core\moVar;

class WP_Customize_Fields_Repeater_Control extends WP_Customize_Control
{
    public $type = 'mailoptin-fields';

    public $default_values;

    public $optin_campaign_id;

    public $customizerClassInstance;

    /** @var null|AbstractOptinForm */
    public $optin_class_instance;

    public $saved_values = [];

    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);

        $saved_values = $this->value();

        if ( ! empty($saved_values) && is_string($saved_values)) {
            $result = json_decode($saved_values, true);
            if (is_array($result)) {
                $this->saved_values = $result;
            }
        }
    }

    /**
     * Enqueue control related scripts/styles.
     *
     * @access public
     */
    public function enqueue()
    {
        // color field
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');

        add_action('customize_controls_print_footer_scripts', [$this, 'field_template']);

        wp_enqueue_script('mailoptin-customizer-fields', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/fields-control/control.js', array('jquery', 'customize-base', 'wp-color-picker'), false, true);
        wp_enqueue_style('mailoptin-customizer-fields', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/fields-control/style.css', null);

        // toggle control assets
        wp_enqueue_script('mo-customizer-toggle-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/toggle-control/customizer-toggle-control.js', array('jquery'), false, true);
        wp_enqueue_style('mo-pure-css-toggle-buttons', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/toggle-control/pure-css-togle-buttons.css', array(), false);


        wp_enqueue_script('jquery-ui-sortable');
    }

    public function font_select($settings_link, $field_value)
    {
        $system_font_optgroup_label = __('System Fonts', 'mailoptin');
        $google_font_optgroup_label = __('Google Fonts', 'mailoptin');

        $fonts = [$system_font_optgroup_label => ControlsHelpers::get_system_font_stack()] + [$google_font_optgroup_label => WP_Customize_Google_Font_Control::get_fonts(300)];

        if ( ! empty($fonts)) {
            ?>
            <select data-customize-setting-link="<?php echo $settings_link; ?>">
                <?php

                printf('<option value="inherit" %s>%s</option>', selected($field_value, 'inherit', false), __('Inherit from Theme', 'mailoptin'));

                foreach ($fonts as $key => $font) {
                    if (is_array($font)) {
                        printf('<optgroup label="%s">', $key);
                        foreach ($font as $font2) {
                            $option_value = $font2;
                            if ($key == $google_font_optgroup_label) {
                                $option_value = str_replace(' ', '+', $font2);
                            }
                            printf('<option value="%s" %s>%s</option>', $option_value, selected($field_value, $option_value, false), $font2);
                        }

                        echo '</optgroup>';

                    } else {
                        printf('<option value="%s" %s>%s</option>', $font, selected($field_value, $font, false), $font);
                    }
                }
                ?>
            </select>
            <?php
        }
    }

    public function name_field()
    {
        if (apply_filters('mo_optin_form_disable_name_field', false)) return;

        $placeholder_setting     = sprintf('mo_optin_campaign[%s][name_field_placeholder]', $this->optin_campaign_id);
        $placeholder_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'name_field_placeholder');

        $color_setting     = sprintf('mo_optin_campaign[%s][name_field_color]', $this->optin_campaign_id);
        $color_default     = (new AbstractCustomizer($this->optin_campaign_id))->customizer_defaults['name_field_color'];
        $color_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'name_field_color');

        $background_setting     = sprintf('mo_optin_campaign[%s][name_field_background]', $this->optin_campaign_id);
        $background_default     = (new AbstractCustomizer($this->optin_campaign_id))->customizer_defaults['name_field_background'];
        $background_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'name_field_background');

        $font_setting     = sprintf('mo_optin_campaign[%s][name_field_font]', $this->optin_campaign_id);
        $font_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'name_field_font');

        $required_setting     = sprintf('mo_optin_campaign[%s][name_field_required]', $this->optin_campaign_id);
        $required_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'name_field_required');

        $hide_field_setting = sprintf('mo_optin_campaign[%s][hide_name_field]', $this->optin_campaign_id);
        $hide_field_value   = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'hide_name_field');
        ?>
        <div class="mo-fields-widget mo-fields-part-widget">
            <div class="mo-fields-widget-top mo-fields-part-widget-top ui-sortable-handle">
                <div class="mo-fields-part-widget-title-action">
                    <button type="button" class="mo-fields-widget-action">
                        <span class="toggle-indicator"></span>
                    </button>
                </div>
                <div class="mo-fields-widget-title">
                    <h3><?php _e('Name', 'mailoptin'); ?></h3>
                </div>
            </div>
            <div class="mo-fields-widget-content">
                <div class="mo-fields-widget-form">
                    <div class="mo-fields-block">
                        <div class="mo-fields-toggle-field" style="display:flex;flex-direction: row;justify-content: flex-start;">
                            <span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php _e('Hide Name Field', 'mailoptin'); ?></span>
                            <input data-customize-setting-link="<?php echo $hide_field_setting; ?>" id="<?php echo $hide_field_setting; ?>" type="checkbox" class="tgl tgl-light" <?php checked($hide_field_value); ?>>
                            <label for="<?php echo $hide_field_setting; ?>" class="tgl-btn"></label>
                        </div>
                    </div>
                    <div class="mo-fields-block">
                        <label for="<?php echo $placeholder_setting; ?>" class="customize-control-title"><?php _e('Title', 'mailoptin'); ?></label>
                        <input id="<?php echo $placeholder_setting; ?>" type="text" value="<?php echo $placeholder_field_value; ?>" data-customize-setting-link="<?php echo $placeholder_setting; ?>">
                    </div>
                    <div class="mo-fields-block">
                        <label for="<?php echo $color_setting; ?>" class="customize-control-title"><?php _e('Color', 'mailoptin'); ?></label>
                        <input id="<?php echo $color_setting; ?>" class="mo-color-picker-hex" type="text" maxlength="7" value="<?php echo $color_field_value; ?>" placeholder="<?php echo $color_field_value; ?>" data-default-color="<?php echo $color_default; ?>" data-customize-setting-link="<?php echo $color_setting; ?>">
                    </div>
                    <?php if ( ! apply_filters('mo_optin_hide_name_field_background_control', false)) : ?>
                        <div class="mo-fields-block">
                            <label for="<?php echo $background_setting; ?>" class="customize-control-title"><?php _e('Background', 'mailoptin'); ?></label>
                            <input id="<?php echo $background_setting; ?>" class="mo-color-picker-hex" type="text" maxlength="7" value="<?php echo $background_field_value; ?>" placeholder="<?php echo $background_field_value; ?>" data-default-color="<?php echo $background_default; ?>" data-customize-setting-link="<?php echo $background_setting; ?>">
                        </div>
                    <?php endif; ?>
                    <div class="mo-fields-block">
                        <label for="<?php echo $font_setting; ?>" class="customize-control-title"><?php _e('Font', 'mailoptin'); ?></label>
                        <?php $this->font_select($font_setting, $font_field_value); ?>
                    </div>
                    <div class="mo-fields-block">
                        <div class="mo-fields-toggle-field" style="display:flex;flex-direction: row;justify-content: flex-start;">
                            <span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php _e('Make Field Required', 'mailoptin'); ?></span>
                            <input data-customize-setting-link="<?php echo $required_setting; ?>" id="<?php echo $required_setting; ?>" type="checkbox" class="tgl tgl-light" <?php checked($required_field_value); ?>>
                            <label for="<?php echo $required_setting; ?>" class="tgl-btn"></label>
                        </div>
                        <span class="description customize-control-description"><?php _e('Activate to make name field required', 'mailoptin') ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function email_field()
    {
        $placeholder_setting     = sprintf('mo_optin_campaign[%s][email_field_placeholder]', $this->optin_campaign_id);
        $placeholder_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'email_field_placeholder');

        $color_setting     = sprintf('mo_optin_campaign[%s][email_field_color]', $this->optin_campaign_id);
        $color_default     = (new AbstractCustomizer($this->optin_campaign_id))->customizer_defaults['email_field_color'];
        $color_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'email_field_color');

        $background_setting     = sprintf('mo_optin_campaign[%s][email_field_background]', $this->optin_campaign_id);
        $background_default     = (new AbstractCustomizer($this->optin_campaign_id))->customizer_defaults['email_field_background'];
        $background_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'email_field_background');

        $font_setting     = sprintf('mo_optin_campaign[%s][email_field_font]', $this->optin_campaign_id);
        $font_field_value = OptinCampaignsRepository::get_merged_customizer_value($this->optin_campaign_id, 'email_field_font');
        ?>
        <div class="mo-fields-widget mo-fields-part-widget">
            <div class="mo-fields-widget-top mo-fields-part-widget-top ui-sortable-handle">
                <div class="mo-fields-part-widget-title-action">
                    <button type="button" class="mo-fields-widget-action">
                        <span class="toggle-indicator"></span>
                    </button>
                </div>
                <div class="mo-fields-widget-title">
                    <h3><?php _e('Email', 'mailoptin'); ?></h3>
                </div>
            </div>
            <div class="mo-fields-widget-content">
                <div class="mo-fields-widget-form">
                    <div class="mo-fields-block">
                        <label for="<?php echo $placeholder_setting; ?>" class="customize-control-title"><?php _e('Title', 'mailoptin'); ?></label>
                        <input id="<?php echo $placeholder_setting; ?>" type="text" value="<?php echo $placeholder_field_value; ?>" data-customize-setting-link="<?php echo $placeholder_setting; ?>">
                    </div>
                    <div class="mo-fields-block">
                        <label for="<?php echo $color_setting; ?>" class="customize-control-title"><?php _e('Color', 'mailoptin'); ?></label>
                        <input id="<?php echo $color_setting; ?>" class="mo-color-picker-hex" type="text" maxlength="7" value="<?php echo $color_field_value; ?>" placeholder="<?php echo $color_field_value; ?>" data-default-color="<?php echo $color_default; ?>" data-customize-setting-link="<?php echo $color_setting; ?>">
                    </div>
                    <?php if ( ! apply_filters('mo_optin_hide_email_field_background_control', false)) : ?>
                        <div class="mo-fields-block">
                            <label for="<?php echo $background_setting; ?>" class="customize-control-title"><?php _e('Background', 'mailoptin'); ?></label>
                            <input id="<?php echo $background_setting; ?>" class="mo-color-picker-hex" type="text" maxlength="7" value="<?php echo $background_field_value; ?>" placeholder="<?php echo $background_field_value; ?>" data-default-color="<?php echo $background_default; ?>" data-customize-setting-link="<?php echo $background_setting; ?>">
                        </div>
                    <?php endif; ?>
                    <div class="mo-fields-block">
                        <label for="<?php echo $font_setting; ?>" class="customize-control-title"><?php _e('Font', 'mailoptin'); ?></label>
                        <?php $this->font_select($font_setting, $font_field_value); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function field_template()
    {
        ?>
        <script type="text/html" id="tmpl-mo-fields-js-template">
            <?php $this->template(); ?>
        </script>
        <?php
    }

    public function repeater_text_field($index, $name, $class = '', $label = '', $description = '', $placeholder = '', $type = 'text')
    {
        $type = empty($type) ? 'text' : $type;

        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-fields-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $random_id = wp_generate_password(5, false) . '_' . $index;
        echo "<div class=\"$name mo-fields-block{$class}\">";
        if ( ! empty($label)) : ?>
            <label for="<?php echo $random_id; ?>" class="customize-control-title"><?php echo esc_html($label); ?></label>
        <?php endif; ?>
        <?php if ( ! empty($description)) : ?>
        <span class="description customize-control-description"><?php echo $description; ?></span>
    <?php endif; ?>
        <input
                id="<?php echo $random_id; ?>"
                type="<?php echo esc_attr($type); ?>"
                name="<?php echo $name; ?>"
                placeholder="<?php echo $placeholder; ?>"
                value="<?php echo esc_attr($saved_value); ?>"
        />
        <?php
        echo '</div>';
    }

    public function repeater_textarea_field($index, $name, $class = '', $label = '', $description = '', $placeholder = '', $type = 'text')
    {
        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-fields-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $random_id = wp_generate_password(5, false) . '_' . $index;
        echo "<div class=\"$name mo-fields-block{$class}\">";
        if ( ! empty($label)) : ?>
            <label for="<?php echo $random_id; ?>" class="customize-control-title"><?php echo esc_html($label); ?></label>
        <?php endif; ?>
        <?php if ( ! empty($description)) : ?>
        <span class="description customize-control-description"><?php echo $description; ?></span>
    <?php endif; ?>
        <textarea rows="3" id="<?php echo $random_id; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>"><?php echo esc_attr($saved_value); ?></textarea>
        <?php
        echo '</div>';
    }

    public function repeater_select_field($index, $name, $choices, $class = '', $label = '', $description = '')
    {
        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-fields-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if (empty($choices)) return;

        $random_id = wp_generate_password(5, false) . '_' . $index;

        if ( ! empty($class)) {
            $class = " $class";
        }

        echo "<div class=\"$name mo-fields-block{$class}\">";
        if ( ! empty($label)) : ?>
            <label for="<?php echo $random_id ?>" class="customize-control-title"><?php echo esc_html($label); ?></label>
        <?php endif; ?>
        <select id="<?php echo $random_id ?>" class="mo-optin-fields-field" name="<?php echo $name ?>">
            <?php
            foreach ($choices as $value => $label) {
                echo '<option value="' . esc_attr($value) . '"' . selected($saved_value, $value, false) . '>' . $label . '</option>';
            }
            ?>
        </select>
        <?php if ( ! empty($description)) : ?>
        <span class="description customize-control-description"><?php echo $description; ?></span>
    <?php endif;
        echo '</div>';
    }


    public function repeater_chosen_select_field($index, $name, $choices, $class = '', $label = '', $description = '')
    {
        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        echo "<div class=\"$name mo-fields-block{$class}\">";
        ?>
        <label>
            <?php if ( ! empty($label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($label); ?></span>
            <?php endif; ?>
            <select class="mo-optin-field-field mailoptin-field-chosen" name="<?php echo $name ?>" multiple>
                <?php
                if (is_array($choices)) {
                    foreach ($choices as $key => $value) {
                        if (is_array($value)) {
                            echo "<optgroup label='$key'>";
                            foreach ($value as $key2 => $value2) {
                                echo '<option value="' . esc_attr($key2) . '"' . $this->_selected($key2, $saved_value) . '>' . $value2 . '</option>';
                            }
                            echo "</optgroup>";
                        } else {
                            echo '<option value="' . esc_attr($key) . '"' . $this->_selected($key, $saved_value) . '>' . $value . '</option>';
                        }
                    }
                }
                ?>
            </select>
        </label>

        <?php if ( ! empty($description)) : ?>
        <span class="description customize-control-description"><?php echo $description; ?></span>
    <?php endif;
        echo '</div>';
    }

    protected function _selected($key, $saved_values)
    {
        return in_array($key, (array)$saved_values) ? 'selected=selected' : null;
    }

    public function repeater_color_field($index, $name, $class = '', $label = '', $description = '')
    {
        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        $defaultValue     = '#RRGGBB';
        $defaultValueAttr = '';

        if ( ! empty($class)) {
            $class = " $class";
        }

        if ($default && is_string($default)) {
            if ('#' !== substr($default, 0, 1)) {
                $defaultValue = '#' . $default;
            } else {
                $defaultValue = $default;
            }
            $defaultValueAttr = " data-default-color=\"$defaultValue\""; // Quotes added automatically.
        }

        echo "<div class=\"$name mo-fields-block{$class}\">";
        if ($label) {
            echo '<span class="customize-control-title">' . $label . '</span>';
        }

        echo '<div class="customize-control-content">';
        echo '<label><span class="screen-reader-text">' . $label . '</span>';

        echo '<input name="' . $name . '" class="mo-color-picker-hex" type="text" maxlength="7" value="' . $saved_value . '" placeholder="' . $defaultValue . '"' . $defaultValueAttr . '/>';
        echo '</label>';
        echo '</div>';
        if ($description) {
            echo '<span class="description customize-control-description">' . $description . '</span>';
        }
        echo '</div>';
    }

    public function repeater_font_field($index, $name, $class = '', $label = '', $description = '', $count = 300)
    {
        $count = empty($count) ? 300 : $count;

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $system_font_optgroup_label = __('System Fonts', 'mailoptin');
        $google_font_optgroup_label = __('Google Fonts', 'mailoptin');

        $fonts = [$system_font_optgroup_label => ControlsHelpers::get_system_font_stack()] + [$google_font_optgroup_label => WP_Customize_Google_Font_Control::get_fonts($count)];

        echo "<div class=\"$name mo-fields-block{$class}\">";
        if ( ! empty($fonts)) {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($label); ?></span>
                <select name="<?= $name; ?>">
                    <?php
                    printf('<option value="inherit" %s>%s</option>', selected($this->value(), 'inherit', false), __('Inherit from Theme', 'mailoptin'));

                    foreach ($fonts as $key => $font) {
                        if (is_array($font)) {
                            printf('<optgroup label="%s">', $key);
                            foreach ($font as $font2) {
                                $option_value = $font2;
                                if ($key == $google_font_optgroup_label) {
                                    $option_value = str_replace(' ', '+', $font2);
                                }
                                printf('<option value="%s" %s>%s</option>', $option_value, selected($saved_value, $option_value, false), $font2);
                            }

                            echo '</optgroup>';

                        }
                    }
                    ?>
                </select>
                <?php if ( ! empty($description)) : ?>
                    <span class="description customize-control-description"><?php echo $description; ?></span>
                <?php endif; ?>
            </label>
            <?php
        }
        echo '</div>';
    }

    public function repeater_toggle_field($index, $name, $class = '', $label = '', $description = '')
    {
        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-fields-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $random_id = wp_generate_password(5, false) . '_' . $index;
        ?>
        <div class="<?= $name; ?> mo-fields-block<?= $class; ?>">
            <div class="mo-fields-toggle-field" style="display:flex;flex-direction: row;justify-content: flex-start;">
                <span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php echo $label; ?></span>
                <input name="<?= $name; ?>" id="<?php echo $random_id ?>" type="checkbox" class="tgl tgl-light" value="<?php echo esc_attr($saved_value); ?>" <?php checked($saved_value); ?> />
                <label for="<?php echo $random_id ?>" class="tgl-btn"></label>
            </div>
            <?php if ( ! empty($description)) : ?>
                <span class="description customize-control-description"><?php echo $description; ?></span>
            <?php endif; ?>

        </div>
        <?php
    }

    public function parse_control($index, $control_args)
    {
        if ( ! is_array($control_args) || empty($control_args)) return;

        foreach ($control_args as $key => $control_arg) {
            switch ($control_arg['field']) {
                case 'text':
                    $this->repeater_text_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description'],
                        @$control_arg['placeholder'],
                        @$control_arg['type']
                    );
                    break;
                case 'select':
                    $this->repeater_select_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['choices'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'chosen_select':
                    $this->repeater_chosen_select_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['choices'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'color':
                    $this->repeater_color_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'font':
                    $this->repeater_font_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description'],
                        @$control_arg['count']
                    );
                    break;
                case 'toggle':
                    $this->repeater_toggle_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'custom_content':
                    $content = $control_arg['content'];
                    if ( ! empty($control_arg['name'])) {
                        $name = esc_attr($control_arg['name']);
                        echo "<div class=\"$name mo-fields-block\">";
                        echo $content;
                        echo '</div>';
                    } else {
                        echo $content;
                    }
                    break;
            }
        }
    }

    /**
     * $index is high numeric value by default so new field added wont have populated data from saved data.
     *
     * @param string $index
     */
    public function template($index = 9999999999999)
    {
        $field_types = [
            'text'              => __('Text', 'mailoptin'),
            'password'          => __('Password', 'mailoptin'),
            'textarea'          => __('Textarea', 'mailoptin'),
            'checkbox'          => __('Checkbox', 'mailoptin'),
            'select'            => __('Select', 'mailoptin'),
            'radio'             => __('Radio', 'mailoptin'),
            'date'              => __('Date', 'mailoptin'),
            'hidden'            => __('Hidden', 'mailoptin'),
            'list_subscription' => __('List Selection', 'mailoptin'),
            'recaptcha_v2'      => __('reCAPTCHA v2', 'mailoptin'),
            'recaptcha_v3'      => __('reCAPTCHA v3', 'mailoptin'),
            'country'           => __('Country', 'mailoptin'),
        ];

        $widget_title = sprintf(__('Field %s', 'mailoptin'), '#' . ($index + 1));
        if (isset($this->saved_values[$index]['placeholder'])) {
            $widget_title = $this->saved_values[$index]['placeholder'];
        }

        $list_country_name_select_type = [
            'alpha-2' => esc_html__('Alpha-2 Code (e.g US)', 'mailoptin'),
            'alpha-3' => esc_html__('Alpha-3 Code (e.g USA)', 'mailoptin'),
        ];

        $integrations = ConnectionsRepository::get_connections();

        $list_subscription_display_type = [
            'checkbox' => esc_html__('Checkboxes (Multiple Select)', 'mailoptin'),
            'radio'    => esc_html__('Radio Buttons (Single Select)', 'mailoptin'),
            'select'   => esc_html__('Dropdown (Single Select)', 'mailoptin'),
        ];

        $list_subscription_alignment = [
            'left'   => esc_html__('Left', 'mailoptin'),
            'center' => esc_html__('Center', 'mailoptin'),
            'right'  => esc_html__('Right', 'mailoptin'),
        ];

        $list_subscription_saved_integration = moVar(isset($this->saved_values[$index]) ? $this->saved_values[$index] : [], 'list_subscription_integration', '', true);
        $list_subscription_lists             = [];
        if ( ! empty($list_subscription_saved_integration)) {
            $list_subscription_lists = ConnectionsRepository::connection_email_list($list_subscription_saved_integration);
        }

        // added .mo-custom-field below to differentiate custom field from name and email fields above.
        ?>
        <div class="mo-fields-widget mo-fields-part-widget mo-custom-field" data-field-index="<?= $index; ?>">
            <div class="mo-fields-widget-top mo-fields-part-widget-top ui-sortable-handle">
                <div class="mo-fields-part-widget-title-action">
                    <button type="button" class="mo-fields-widget-action">
                        <span class="toggle-indicator"></span>
                    </button>
                </div>
                <div class="mo-fields-widget-title">
                    <h3><?= $widget_title; ?></h3>
                </div>
            </div>
            <div class="mo-fields-widget-content">
                <div class="mo-fields-widget-form">
                    <?php $this->parse_control($index, apply_filters('mo_optin_fields_controls_before', [], $this->optin_campaign_id, $index, $this->saved_values)); ?>
                    <?php $this->repeater_text_field($index, 'placeholder', '', __('Title', 'mailoptin')); ?>
                    <?php $this->repeater_select_field($index, 'field_type', $field_types, '', __('Type', 'mailoptin')); ?>
                    <?php $this->repeater_textarea_field($index, 'field_options', '', __('Options', 'mailoptin'), __('Enter a comma-separated list of options', 'mailoptin')); ?>
                    <?php $this->repeater_text_field($index, 'hidden_value', '', __('Value', 'mailoptin'), __('Enter the value for this hidden field', 'mailoptin')); ?>

                    <?php $this->repeater_select_field($index, 'country_field_options', $list_country_name_select_type, '', __('Show Country In', 'mailoptin')); ?>
                    <?php $this->repeater_select_field($index, 'list_subscription_integration', $integrations, '', __('Select Integration', 'mailoptin'), '<span class="spinner mo-list-subscription-spinner"></span>'); ?>
                    <?php $this->repeater_chosen_select_field($index, 'list_subscription_lists', $list_subscription_lists, '', __('Options', 'mailoptin')); ?>
                    <?php $this->repeater_select_field($index, 'list_subscription_display_type', $list_subscription_display_type, '', __('Field Type', 'mailoptin')); ?>
                    <?php $this->repeater_select_field($index, 'list_subscription_alignment', $list_subscription_alignment, '', __('Aligment', 'mailoptin')); ?>

                    <?php $this->repeater_color_field($index, 'color', '', __('Color', 'mailoptin')); ?>
                    <?php $this->repeater_color_field($index, 'background', '', __('Background', 'mailoptin')); ?>
                    <?php $this->repeater_font_field($index, 'font', '', __('Font', 'mailoptin')); ?>
                    <?php $this->repeater_toggle_field($index, 'field_required', '', __('Make Field Required', 'mailoptin')); ?>

                    <?php $this->repeater_select_field($index, 'recaptcha_v2_size', ['normal' => __('Normal', 'mailoptin'), 'compact' => __('Compact', 'mailoptin')], '', __('Size', 'mailoptin')); ?>
                    <?php $this->repeater_select_field($index, 'recaptcha_v2_style', ['light' => __('Light', 'mailoptin'), 'dark' => __('Dark', 'mailoptin')], '', __('Style', 'mailoptin')); ?>
                    <?php $this->parse_control($index, apply_filters('mo_optin_fields_controls_after', [], $this->optin_campaign_id, $index, $this->saved_values)); ?>
                </div>
                <div class="mo-fields-widget-actions">
                    <a href="#" class="mo-fields-delete"><?php _e('Delete', 'mailoptin'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_content()
    {
        $collapse_text = __('Collapse all', 'mailoptin');
        $expand_text   = __('Expand all', 'mailoptin');
        printf(
            '<div class="mo-fields-expand-collapse-wrap"><a href="#" class="mo-fields-expand-collapse-all mo-expand" data-collapse-text="%1$s" data-expand-text="%2$s">%2$s</a></div>',
            $collapse_text, $expand_text
        );

        $this->name_field();
        $this->email_field();

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $content = sprintf(
                '<div class="mo-pro"><a href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=optin_custom_fields1" target="_blank">%s</a></div>',
                __('Premium Version Available', 'mailoptin')
            );

            $content .= sprintf(
                __('Upgrade to %sMailOptin Premium%s to add reCAPTCHA to prevent spam bots, option to allow users select list to join and custom fields to capture additional information.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=optin_custom_fields2">',
                '</a>'
            );

            echo '<div style="background: #fff;padding:5px">' . $content . '</div>';

            return;
        }

        $optin_class_instance = $this->optin_class_instance;

        if ( ! in_array($optin_class_instance::OPTIN_CUSTOM_FIELD_SUPPORT, $optin_class_instance->features_support())) return;

        echo '<div class="mo-custom-fields-container mo-fields-widgets mo-custom-field">';
        if (is_array($this->saved_values) && count($this->saved_values) > 0) {
            foreach ($this->saved_values as $index => $field) {
                $this->template($index);
            }
        }
        echo '</div>';
        ?>
        <div class="mo-fields__add_new">
            <button type="button" class="button mo-add-new-field">
                <?php _e('Add Custom Field', 'mailoptin') ?>
            </button>
        </div>
        <input class="mo-fields-save-field" id="<?= '_customize-input-' . $this->id; ?>" type="hidden" <?php $this->link(); ?>/>
        <?php
    }
}