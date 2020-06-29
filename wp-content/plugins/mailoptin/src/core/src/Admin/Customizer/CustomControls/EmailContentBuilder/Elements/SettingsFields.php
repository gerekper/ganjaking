<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


class SettingsFields
{
    public static function tinymce($name)
    {
        echo '<div class="mo-email-content-field-tinymce-wrap">';
        printf('<textarea data-field-type="tinymce" id="%1$s" name="%1$s" style="height: 280px" class="mo-email-content-element-field mo-email-content-field-tinymce">{{{mo_ece_get_field_value("%1$s", data)}}}</textarea>', $name);
        echo '</div>';
    }

    public static function text($name, $setting)
    {
        $placeholder = isset($setting['placeholder']) ? $setting['placeholder'] : '';
        printf('<input placeholder="%2$s" data-field-type="text" class="mo-email-content-element-field" type="text" name="%1$s" id="%1$s" value="{{mo_ece_get_field_value("%1$s", data)}}">', $name, $placeholder);
    }

    public static function checkbox($name, $setting)
    {
        $checked = sprintf('<# if(mo_ece_get_field_value("%1$s", data) === true) { #> checked <# } #>', $name);
        printf('<input data-field-type="checkbox" class="mo-email-content-element-field" type="checkbox" name="%1$s" id="%1$s" value="yes" %2$s>', $name, $checked);
        printf('<label for="%2$s" style="vertical-align: initial;font-weight: 500;">%1$s</label>', $setting['checkbox_label'], $name);
    }

    public static function select_image($name)
    {
        printf('<div class="mo-select-image-field"><input data-field-type="select_image" class="mo-email-content-element-field" type="text" name="%1$s" id="%1$s" value="{{mo_ece_get_field_value("%1$s", data)}}"></div>', $name);
        printf('<div class="mo-select-image-btn"><a href="#" class="button action">%s</a></div>', esc_html__('Choose Image', 'mailoptin'));
    }

    public static function font_family($name, $setting)
    {
        $setting['choices'] = [
            ''                                        => esc_html__('Select...', 'mailoptin'),
            esc_html__('Standard Fonts', 'mailoptin') => [
                'Arial'           => esc_html__('Arial', 'mailoptin'),
                'Comic Sans MS'   => esc_html__('Comic Sans MS', 'mailoptin'),
                'Courier New'     => esc_html__('Courier New', 'mailoptin'),
                'Georgia'         => esc_html__('Georgia', 'mailoptin'),
                'Helvetica'       => esc_html__('Helvetica', 'mailoptin'),
                'Lucida'          => esc_html__('Lucida', 'mailoptin'),
                'Tahoma'          => esc_html__('Tahoma', 'mailoptin'),
                'Times New Roman' => esc_html__('Times New Roman', 'mailoptin'),
                'Trebuchet MS'    => esc_html__('Trebuchet MS', 'mailoptin'),
                'Verdana'         => esc_html__('Verdana', 'mailoptin')
            ],
            esc_html__('Custom Fonts', 'mailoptin')   => [
                'Arvo'              => esc_html__('Arvo', 'mailoptin'),
                'Lato'              => esc_html__('Lato', 'mailoptin'),
                'Lora'              => esc_html__('Lora', 'mailoptin'),
                'Merriweather'      => esc_html__('Merriweather', 'mailoptin'),
                'Merriweather Sans' => esc_html__('Merriweather Sans', 'mailoptin'),
                'Noticia Text'      => esc_html__('Noticia Text', 'mailoptin'),
                'Open Sans'         => esc_html__('Open Sans', 'mailoptin'),
                'Playfair Display'  => esc_html__('Playfair Display', 'mailoptin'),
                'Roboto'            => esc_html__('Roboto', 'mailoptin'),
                'Source Sans Pro'   => esc_html__('Source Sans Pro', 'mailoptin'),
                'Oswald'            => esc_html__('Oswald', 'mailoptin'),
                'Raleway'           => esc_html__('Raleway', 'mailoptin'),
                'Permanent Marker'  => esc_html__('Permanent Marker', 'mailoptin'),
                'Pacifico'          => esc_html__('Pacifico', 'mailoptin'),
            ]
        ];

        self::select($name, $setting);
    }

    protected static function _selected($name, $value, $is_multiple = false)
    {
        $status = sprintf('<# if(mo_ece_get_field_value("%1$s", data) == "%2$s") { #> selected <# } #>', $name, $value);

        if ($is_multiple) {
            $status = sprintf('<# if(_.contains(mo_ece_get_field_value("%1$s", data), "%2$s")) { #> selected <# } #>', $name, $value);
        }

        return $status;
    }

    public static function select($name, $setting = [])
    {
        $setting['select2_options'] = isset($setting['select2_options']) ? esc_attr(wp_json_encode($setting['select2_options'])) : '{}';

        $choices = $setting['choices'];

        $is_multiple     = isset($setting['multiple']) && $setting['multiple'] === true;
        $multiple        = $is_multiple ? ' multiple' : '';
        $multiple_class  = $is_multiple ? ' mo-multiple-select' : '';
        $select2_options = $is_multiple ? sprintf(' data-select2-options="%s"', $setting['select2_options']) : '';

        printf('<select data-field-type="select" class="mo-email-content-element-field%3$s" id="%1$s" name="%1$s"%4$s%2$s>', $name, $multiple, $multiple_class, $select2_options);

        foreach ($choices as $key => $value) {
            if (is_array($value)) {
                echo "<optgroup label='$key'>";
                foreach ($value as $key2 => $value2) {
                    printf('<option value="%1$s" %3$s>%2$s</option>', $key2, $value2, self::_selected($name, $key2, $is_multiple));
                }
                echo "</optgroup>";
            } else {
                printf('<option value="%1$s" %3$s>%2$s</option>', $key, $value, self::_selected($name, $key, $is_multiple));
            }
        }

        echo '</select>';
    }

    public static function range($name, $setting, $element_type)
    {
        $min  = isset($setting['min']) ? $setting['min'] : 0;
        $max  = isset($setting['max']) ? $setting['max'] : 100;
        $step = isset($setting['step']) ? $setting['step'] : 1;

        $default = sprintf('{{mo_email_content_builder_elements_defaults["%s"]["%s"]}}', $element_type, $name);
        echo '<div class="customize-control-mo-range">';
        echo '<div class="control-wrap">';
        printf(
            '<input type="range" min="%3$s" max="%4$s" step="%5$s" value="{{mo_ece_get_field_value("%1$s", data)}}" data-reset_value="%2$s">',
            $name, $default, $min, $max, $step
        );

        printf(
            '<input data-field-type="range" name="%1$s" type="number" min="%3$s" max="%4$s" step="%5$s" class="mo-email-content-element-field mo-range-input" value="{{mo_ece_get_field_value("%1$s", data)}}">',
            $name, $default, $min, $max, $step
        );
        echo '<span class="mo-reset-slider"><span class="dashicons dashicons-image-rotate"></span></span>';
        echo '</div>';
        echo '</div>';
    }

    public static function color_picker($name, $setting, $element_type)
    {
        $default     = sprintf('{{mo_email_content_builder_elements_defaults["%s"]["%s"]}}', $element_type, $name);
        $saved_value = sprintf('{{mo_ece_get_field_value("%1$s", data)}}', $name);

        printf(
            '<input data-field-type="color_picker"  name="%1$s" class="mo-email-content-element-field mo-color-picker-hex" type="text" maxlength="7" value="%2$s" placeholder="%3$s" data-default-color="%3$s"/>',
            $name, $saved_value, $default
        );
    }

    public static function dimension($name)
    {
        $item_link_desc = esc_html__('Link Values Together', 'mailoptin');
        ?>
        <# var values = mo_ece_get_field_value("<?= $name ?>", data); #>
        <div class="customize-control-mo-border">
            <div class="mo-border-outer-wrapper">
                <div class="input-wrapper mo-border-wrapper">

                    <ul data-field-type="dimension" class="mo-email-content-element-field mo-border-wrapper desktop active" name="<?= $name ?>">
                        <li class="mo-border-input-item-link">
                            <span class="dashicons dashicons-admin-links mo-border-connected wp-ui-highlight" title="<?= $item_link_desc ?>"></span>
                            <span class="dashicons dashicons-editor-unlink mo-border-disconnected" title="<?= $item_link_desc ?>"></span>
                        </li>
                        <li class="mo-border-input-item">
                            <input type="number" class="mo-border-input motop" value="{{values.top}}">
                            <span class="mo-border-title"><?= esc_html__('Top', 'mailoptin') ?></span>
                        </li>
                        <li class="mo-border-input-item">
                            <input type="number" class="mo-border-input moright" value="{{values.right}}">
                            <span class="mo-border-title"><?= esc_html__('Right', 'mailoptin') ?></span>
                        </li>
                        <li class="mo-border-input-item">
                            <input type="number" class="mo-border-input mobottom" value="{{values.bottom}}">
                            <span class="mo-border-title"><?= esc_html__('Bottom', 'mailoptin') ?></span>
                        </li>
                        <li class="mo-border-input-item">
                            <input type="number" class="mo-border-input moleft" value="{{values.left}}">
                            <span class="mo-border-title"><?= esc_html__('Left', 'mailoptin') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}