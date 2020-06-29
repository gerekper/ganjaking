<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\Repositories\ConnectionsRepository;
use WP_Customize_Control;

class WP_Customize_Integration_Repeater_Control extends WP_Customize_Control
{
    public $type = 'mailoptin-integration';

    public $default_values;

    public $optin_campaign_id;

    public $customizerClassInstance;

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
        add_action('customize_controls_print_footer_scripts', [$this, 'integration_template']);

        wp_enqueue_script('mailoptin-customizer-integrations', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/integration-control/control.js', array('jquery', 'customize-base'), false, true);
        wp_enqueue_style('mailoptin-customizer-integrations', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/integration-control/style.css', null);

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            wp_enqueue_script('mailoptin-customizer-map-custom-field', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/integration-control/map-custom-field.js', array('jquery'), false, true);
        }

        // toggle control assets
        wp_enqueue_script('mo-customizer-toggle-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/toggle-control/customizer-toggle-control.js', array('jquery'), false, true);
        wp_enqueue_style('mo-pure-css-toggle-buttons', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/toggle-control/pure-css-togle-buttons.css', array(), false);

        $css = '
			.disabled-control-title {
				color: #a0a5aa;
			}
			input[type=checkbox].tgl-light:checked + .tgl-btn {
				background: #0085ba;
			}
			input[type=checkbox].tgl-light + .tgl-btn {
			  background: #a0a5aa;
			}
			input[type=checkbox].tgl-light + .tgl-btn:after {
			  background: #f7f7f7;
			}

			input[type=checkbox].tgl-ios:checked + .tgl-btn {
			  background: #0085ba;
			}

			input[type=checkbox].tgl-flat:checked + .tgl-btn {
			  border: 4px solid #0085ba;
			}
			input[type=checkbox].tgl-flat:checked + .tgl-btn:after {
			  background: #0085ba;
			}

		';
        wp_add_inline_style('mo-pure-css-toggle-buttons', $css);

        // color field
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');

        do_action('mo_optin_integration_control_enqueue');
    }

    public function integration_template()
    {
        ?>
        <script type="text/html" id="tmpl-mo-integration-js-template">
            <?php $this->template(); ?>
        </script>
        <?php

        $email_providers = ConnectionsRepository::get_connections();

        $connections_with_custom_field_support = [];

        foreach ($email_providers as $className => $label) {
            if ( ! empty($className)) {
                $optin_class_instance = OptinFormFactory::make($this->optin_campaign_id);

                /** @var ConnectionInterface $connection_class */
                $connection_class = "MailOptin\\$className\\Connect";
                if (in_array(AbstractConnect::OPTIN_CUSTOM_FIELD_SUPPORT, $connection_class::features_support()) &&
                    in_array($optin_class_instance::OPTIN_CUSTOM_FIELD_SUPPORT, $optin_class_instance->features_support())
                ) {
                    $connections_with_custom_field_support[] = $className;
                }
            }
        }

        $connections_with_advance_settings_support = apply_filters('mo_connections_with_advance_settings_support', []);

        ?>
        <script type="text/javascript">
            var mo_connections_with_custom_field_support = <?php echo json_encode($connections_with_custom_field_support); ?>;
            var mo_connections_with_advance_settings_support = <?php echo json_encode($connections_with_advance_settings_support); ?>;
        </script>
        <?php
    }

    public function text_field($index, $name, $class = '', $label = '', $description = '', $placeholder = '', $type = 'text')
    {
        $type = empty($type) ? 'text' : $type;

        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-integration-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $random_id = wp_generate_password(5, false) . '_' . $index;
        echo "<div class=\"$name mo-integration-block{$class}\">";
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

    public function textarea_field($index, $name, $class = '', $label = '', $description = '', $placeholder = '')
    {
        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-integration-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $random_id = wp_generate_password(5, false) . '_' . $index;
        echo "<div class=\"$name mo-integration-block{$class}\">";
        if ( ! empty($label)) : ?>
            <label for="<?php echo $random_id; ?>" class="customize-control-title"><?php echo esc_html($label); ?></label>
        <?php endif; ?>
        <?php if ( ! empty($description)) : ?>
        <span class="description customize-control-description"><?php echo $description; ?></span>
    <?php endif; ?>
        <textarea id="<?php echo $random_id; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>"><?php echo esc_textarea($saved_value); ?></textarea>
        <?php
        echo '</div>';
    }

    public function select_field($index, $name, $choices, $class = '', $label = '', $description = '')
    {
        if (empty($choices)) return;

        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-integration-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        $random_id = wp_generate_password(5, false) . '_' . $index;

        if ( ! empty($class)) {
            $class = " $class";
        }

        echo "<div class=\"$name mo-integration-block{$class}\">";
        if ( ! empty($label)) : ?>
            <label for="<?php echo $random_id ?>" class="customize-control-title"><?php echo esc_html($label); ?></label>
        <?php endif; ?>
        <select id="<?php echo $random_id ?>" class="mo-optin-integration-field" name="<?php echo $name ?>">
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

    public function chosen_select_field($index, $name, $choices, $class = '', $label = '', $description = '')
    {
        if (empty($choices)) return;

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        echo "<div class=\"$name mo-integration-block{$class}\">";
        ?>
        <label>
            <?php if ( ! empty($label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($label); ?></span>
            <?php endif; ?>
            <select class="mo-optin-integration-field mailoptin-integration-chosen" name="<?php echo $name ?>" multiple>
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

    public function mc_group_select($index, $name, $choices, $class = '')
    {
        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        echo "<div class=\"$name mo-integration-block{$class}\">";

        if (empty($choices)) {
            echo '<div style="background:#000000;color:#fff;padding:10px;font-size:14px;">' . __('No MailChimp group found. Try selecting another email list.', 'mailoptin') . '</div>';

            return;
        }

        foreach ($choices as $choice) : ?>
            <div>
                <span class="customize-control-title"><?= $choice['title']; ?></span>
                <?php foreach ($choice['interests'] as $interests) : ?>
                    <div>
                        <label>
                            <input type="checkbox" class="mo_mc_interest" name="<?= $name; ?>[]" value="<?= $interests['id']; ?>" <?php if (is_array($saved_value) && in_array($interests['id'], array_keys($saved_value))) {
                                echo 'checked="checked"';
                            } ?>
                            >
                            <span class="mo_mc_interest_label"><?= $interests['name']; ?></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach;
        echo '</div>';
    }

    public function color_field($index, $name, $class = '', $label = '', $description = '')
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

        echo "<div class=\"$name mo-integration-block{$class}\">";
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

    public function font_fields($index, $name, $class = '', $label = '', $description = '', $count = 200)
    {
        $count = empty($count) ? 200 : $count;

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $fonts = WP_Customize_Google_Font_Control::get_fonts($count);
        echo "<div class=\"$name mo-integration-block{$class}\">";
        if ( ! empty($fonts)) {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($label); ?></span>
                <select name="<?= $name; ?>">
                    <?php
                    printf('<option value="inherit" %s>%s</option>', selected($this->value(), 'inherit', false), __('Inherit from Theme', 'mailoptin'));
                    foreach ($fonts as $v) {
                        $option_value = str_replace(' ', '+', $v);
                        printf('<option value="%s" %s>%s</option>', $option_value, selected($saved_value, $option_value, false), $v);
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

    public function toggle_field($index, $name, $class = '', $label = '', $description = '')
    {
        if ( ! isset($index) || ! array_key_exists($index, $this->saved_values)) {
            $index = '{mo-integration-index}';
        }

        $default     = isset($this->default_values[$name]) ? $this->default_values[$name] : '';
        $saved_value = isset($this->saved_values[$index][$name]) ? $this->saved_values[$index][$name] : $default;

        if ( ! empty($class)) {
            $class = " $class";
        }

        $random_id = wp_generate_password(5, false) . '_' . $index;
        ?>
        <div class="<?= $name; ?> mo-integration-block<?= $class; ?>">
            <div class="mo-integration-toggle-field" style="display:flex;flex-direction: row;justify-content: flex-start;">
                <span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php echo $label; ?></span>
                <input name="<?= $name; ?>" id="<?php echo $random_id ?>" type="checkbox" class="tgl tgl-light" value="<?php echo esc_attr($saved_value); ?>" <?php checked($saved_value); ?> />
                <label for="<?php echo $random_id ?>" class="tgl-btn"></label>
            </div>
            <?php if ( ! empty($description)) : ?>
                <span class="description customize-control-description"><?php echo $description; ?></span>
            <?php endif ?>
        </div>
        <?php
    }

    public function parse_control($index, $control_args)
    {
        if ( ! is_array($control_args) || empty($control_args)) return;

        foreach ($control_args as $key => $control_arg) {
            switch ($control_arg['field']) {
                case 'text':
                    $this->text_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description'],
                        @$control_arg['placeholder'],
                        @$control_arg['type']
                    );
                    break;
                case 'textarea':
                    $this->textarea_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description'],
                        @$control_arg['placeholder']
                    );
                    break;
                case 'select':
                    $this->select_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['choices'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'chosen_select':
                    $this->chosen_select_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['choices'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'mc_group_select':
                    $this->mc_group_select(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['choices'],
                        @$control_arg['class']
                    );
                    break;
                case 'color':
                    $this->color_field(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description']
                    );
                    break;
                case 'font':
                    $this->font_fields(
                        $index,
                        @$control_arg['name'],
                        @$control_arg['class'],
                        @$control_arg['label'],
                        @$control_arg['description'],
                        @$control_arg['count']
                    );
                    break;
                case 'toggle':
                    $this->toggle_field(
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
                        echo "<div class=\"$name mo-integration-block\">";
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
     * $index is high numeric value by default so new integration added wont have populated data from saved data.
     *
     * @param string $index
     */
    public function template($index = 9999999999999)
    {
        $email_providers = ConnectionsRepository::get_connections();

        $widget_title          = __('New Integration', 'mailoptin');
        $connection_email_list = ['' => __('Select...', 'mailoptin')];
        if (isset($this->saved_values[$index]['connection_service'])) {
            $saved_email_provider = $this->saved_values[$index]['connection_service'];
            if ( ! empty($email_providers[$saved_email_provider])) {
                $widget_title = $email_providers[$saved_email_provider];
            }
            // prepend 'Select...' to the array of email list.
            // because select control will be hidden if no choice is found.
            $connection_email_list = $connection_email_list + ConnectionsRepository::connection_email_list($saved_email_provider);
        }
        ?>
        <div class="mo-integration-widget mo-integration-part-widget" data-integration-index="<?= $index; ?>">
            <div class="mo-integration-widget-top mo-integration-part-widget-top ui-sortable-handle">
                <div class="mo-integration-part-widget-title-action">
                    <button type="button" class="mo-integration-widget-action">
                        <span class="toggle-indicator"></span>
                    </button>
                </div>
                <div class="mo-integration-widget-title">
                    <h3><?= $widget_title; ?></h3>
                </div>
            </div>
            <div class="mo-integration-widget-content">
                <div class="mo-integration-widget-form">
                    <?php $this->parse_control($index, apply_filters('mo_optin_integrations_controls_before', [], $this->optin_campaign_id, $index, $this->saved_values)); ?>
                    <?php $this->select_field($index, 'connection_service', $email_providers, '', __('Select Integration', 'mailoptin')); ?>
                    <?php $this->select_field($index, 'connection_email_list', $connection_email_list, '', __('Select Email List', 'mailoptin')); ?>
                    <?php $this->parse_control($index, apply_filters('mo_optin_integrations_controls_after', [], $this->optin_campaign_id, $index, $this->saved_values)); ?>
                    <?php $this->map_custom_field_btn(); ?>
                    <?php $this->advance_settings($index); ?>
                </div>
                <div class="mo-integration-widget-actions">
                    <a href="#" class="mo-integration-delete"><?php _e('Delete', 'mailoptin'); ?></a>
                    <a href="#" class="mo-integration-advanced-settings"><?php _e('Advanced', 'mailoptin'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    public function advance_settings($index)
    {
        echo '<div class="mo-integration-widget-advanced-settings-wrap" style="display: none">';
        $this->parse_control($index, apply_filters('mo_optin_integrations_advance_controls', [], $this->optin_campaign_id, $index, $this->saved_values));
        echo '</div>';
    }

    public function map_custom_field_btn()
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        ?>
        <div class="mo-optin-map-custom-field">
            <a href="#" class="page-title-action map-link"><?php _e('Map Custom Fields', 'mailoptin') ?></a>
        </div>
        <div class="mo-optin-map-custom-field-settings" style="display: none">
            <div class="mo-optin-map-custom-field-settings-content" style="display: none"></div>
        </div>
        <?php
    }

    public function render_content()
    {
        $collapse_text = __('Collapse all', 'mailoptin');
        $expand_text   = __('Expand all', 'mailoptin');
        printf(
            '<div class="mo-integration-expand-collapse-wrap"><a href="#" class="mo-expand-collapse-all mo-expand" data-collapse-text="%1$s" data-expand-text="%2$s">%2$s</a></div>',
            $collapse_text, $expand_text
        );

        if (is_array($this->saved_values) && count($this->saved_values) > 0) {
            foreach ($this->saved_values as $index => $integration) {
                // in place to ensure empty integration isn't displayed.
                if ( ! empty($integration['connection_service'])) {
                    $this->template($index);
                }
            }
        } else {
            $this->template();
        }

        /* we normally would have added 'value="<?php $this->value();?>"
        Apparently, it is automatically inserted by customizer*/
        ?>
        <div class="mo-integration__add_new">
            <button type="button" class="button mo-add-new-integration">
                <?php _e('Add Another Integration', 'mailoptin') ?>
            </button>
        </div>
        <input class="mo-integrations-save-field" id="<?= '_customize-input-' . $this->id; ?>" type="hidden" <?php $this->link(); ?>/>
        <?php
    }
}