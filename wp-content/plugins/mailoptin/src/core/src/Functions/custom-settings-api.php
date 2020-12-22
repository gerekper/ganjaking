<?php
/**
 * WordPress Custom Settings API Library.
 *
 * Copyright (C) 2017  Agbonghama Collins
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WordPress Custom Settings API
 * @version 1.0
 * @author Agbonghama Collins
 * @link to be decided
 * @license http://www.gnu.org/licenses GNU General Public License
 */

namespace W3Guy;

ob_start();

class Custom_Settings_Page_Api
{
    /** @var mixed|void database saved data. */
    private $db_options = array();

    /** @var string option name for database saving. */
    private $option_name = '';

    /** @var array config of settings page tabs */
    private $tabs_config = array();

    /** @var array config of main settings builpage */
    private $main_content_config = array();

    /** @var array config of settings page sidebar */
    private $sidebar_config = array();

    /** @var string header title of the page */
    private $page_header = '';

    private $exclude_top_tav_nav = false;

    protected function __construct($main_content_config = array(), $option_name = '', $page_header = '')
    {
        $this->db_options          = get_option($option_name, array());
        $this->option_name         = $option_name;
        $this->main_content_config = $main_content_config;
        $this->page_header         = $page_header;
    }

    public function option_name($val)
    {
        $this->option_name = $val;
    }

    public function tab($val)
    {
        $this->tabs_config = $val;
    }

    public function main_content($val)
    {
        $this->main_content_config = $val;
    }

    public function sidebar($val)
    {
        $this->sidebar_config = $val;
    }

    public function page_header($val)
    {
        $this->page_header = $val;
    }

    /**
     * Construct the settings page tab.
     *
     * array(
     *  array('url' => '', 'label' => ''),
     *  array('url' => '', 'label' => ''),
     *  );
     *
     */
    public function settings_page_tab()
    {
        if ($this->exclude_top_tav_nav) return;

        $args = $this->tabs_config;
        if (empty($args)) return;

        echo '<h2 class="nav-tab-wrapper">';
        if ( ! empty($args)) {
            foreach ($args as $arg) {
                $url              = esc_url_raw(@$arg['url']);
                $label            = esc_html(@$arg['label']);
                $class            = esc_attr(@$arg['class']);
                $style            = esc_attr(@$arg['style']);
                $active           = null;
                $current_page_url = $this->current_page_url();
                if (strpos($current_page_url, $url) !== false) {
                    $active = apply_filters('wp_cspa_active_tab_class', ' nav-tab-active', $url, $current_page_url);
                }
                echo "<a href=\"$url\" class=\"$class nav-tab{$active}\" style='$style'>$label</a>";
            }
        }

        echo '</h2>';
    }


    /**
     * Construct the settings page sidebar.
     *
     * array(
     *      array(
     *          'section_title' => 'Documentation',
     *          'content'       => '',
     *`     );
     * );
     *
     */
    public function setting_page_sidebar()
    {
        $custom = apply_filters('wp_cspa_setting_page_sidebar', '', $this->option_name);

        if ( ! empty($custom))
            return $custom;
        ?>
        <div id="postbox-container-1" class="postbox-container">
            <div class="meta-box-sortables" style="text-align: center; margin: auto">
                <?php if ( ! empty($this->sidebar_config)): ?>
                    <?php foreach ($this->sidebar_config as $arg) : ?>
                        <div class="postbox">
                        <div class="postbox-header">
                            <h3 class="hndle is-non-sortable"><span><?php echo $arg['section_title']; ?></span></h3>
                        </div>

                            <div class="inside">
                                <?php do_action('wp_cspa_sidebar_before_box_content', $this->option_name); ?>
                                <?php echo $arg['content']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Helper function to recursively sanitize POSTed data.
     *
     * @param $data
     *
     * @return string|array
     */
    public static function sanitize_data($data)
    {
        if (is_string($data)) {
            return esc_html($data);
        }

        $sanitized_data = array();
        foreach ($data as $key => $value) {
            // skip sanitation. useful for fields that expects html
            if (apply_filters('wp_cspa_sanitize_skip', false, $key, $value)) {
                $sanitized_data[$key] = $data[$key];
                continue;
            }

            if (is_array($data[$key])) {
                $sanitized_data[$key] = self::sanitize_data($data[$key]);
            } else {
                $sanitized_data[$key] = esc_html(stripslashes($data[$key]));
            }
        }

        return $sanitized_data;
    }

    /**
     * Persist the form data to database.
     *
     * @return \WP_Error|Void
     */
    public function persist_plugin_settings()
    {
        add_action('admin_notices', array($this, 'do_settings_errors'));

        if ( ! \MailOptin\Core\current_user_has_privilege()) {
            return;
        }

        if (empty($_POST['save_' . $this->option_name])) {
            return;
        }

        check_admin_referer('wp-csa-nonce', 'wp_csa_nonce');

        /**
         * Use add_settings_error() to create/generate an errors add_settings_error('wp_csa_notice', '', 'an error');
         * in your validation function/class method hooked to this action.
         */
        do_action('wp_cspa_validate_data', $_POST[$this->option_name]);

        $settings_error = get_settings_errors('wp_csa_notice');
        if ( ! empty($settings_error)) {
            return;
        }

        $sanitize_callable = apply_filters('wp_cspa_sanitize_callback', 'self::sanitize_data');

        $sanitized_data = apply_filters(
            'wp_cspa_santized_data',
            call_user_func($sanitize_callable, $_POST[$this->option_name]),
            $this->option_name
        );

        // skip unchanged (with asterisk ** in its data) api key/token values.
        foreach ($sanitized_data as $key => $value) {
            if (is_string($value) && strpos($value, '**') !== false) {
                unset($sanitized_data[$key]);
            }
        }

        do_action('wp_cspa_persist_settings', $sanitized_data, $this->option_name, $this);

        if ( ! apply_filters('wp_cspa_disable_default_persistence', false)) {

            update_option($this->option_name, array_replace($this->db_options, $sanitized_data));

            do_action('wp_cspa_after_persist_settings', $sanitized_data, $this->option_name);

            wp_safe_redirect(esc_url_raw(add_query_arg('settings-updated', 'true')));
            exit;
        }
    }

    /**
     * Do settings page error
     */
    public function do_settings_errors()
    {
        $success_notice = apply_filters('', 'Settings saved.');
        if (isset($_GET['settings-updated']) && ($_GET['settings-updated'] == 'true')) : ?>
            <?php add_settings_error('wp_csa_notice', 'wp_csa_settings_updated', $success_notice, 'updated'); ?>
        <?php endif; ?>
        <?php
    }

    public function metabox_toggle_script()
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.wp_csa_view .handlediv').click(function () {
                    $(this).parent().toggleClass("closed").addClass('postbox');
                });
            });
        </script>
        <?php
    }

    public function settings_page_heading()
    {
        do_action('wp_cspa_before_header');
        echo '<h2 class="wp-csa-heading">';
        echo $this->page_header;
        do_action('wp_cspa_before_closing_header');
        echo '</h2>';
    }

    public function nonce_field()
    {
        printf('<input id="wp_csa_nonce" type="hidden" name="wp_csa_nonce" value="%s">', wp_create_nonce('wp-csa-nonce'));
    }


    /**
     * Get current page URL.
     *
     * @return string
     */
    public function current_page_url()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"])) {
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        return apply_filters('wp_cspa_main_current_page_url', $pageURL);
    }


    /**
     * Main settings page markup.
     *
     * @param bool $return_output
     */
    public function _settings_page_main_content_area($return_output = false)
    {
        $args_arrays = $this->main_content_config;

        // variable declaration
        $html = '';

        if ( ! empty($args_arrays)) {
            foreach ($args_arrays as $args_array) {
                $html .= $this->metax_box_instance($args_array);
            }
        }

        if ($return_output) {
            return $html;
        }

        echo $html;
    }

    /**
     * @param $args
     *
     * @return string
     */
    public function metax_box_instance($args)
    {
        $db_options = $this->db_options;

        $html = '';

        if ( ! empty($args['section_title'])) {
            $html .= $this->_header($args['section_title'], $args);
        }

        $disable_submit_button = isset($args['disable_submit_button']) ? true : false;

        // remove section title from array to make the argument keys be arrays so it can work with foreach loop
        if (isset($args['section_title'])) {
            unset($args['section_title']);
        }

        if (isset($args['disable_submit_button'])) {
            unset($args['disable_submit_button']);
        }

        foreach ($args as $key => $value) {

            $field_type = '_' . $args[$key]['type'];
            $html       .= $this->{$field_type}($db_options, $key, $args[$key]);
        }

        if ($disable_submit_button) {
            $html .= $this->_footer_without_button();
        } else {
            $html .= $this->_footer();
        }

        return $html;
    }

    /**
     * Create repeatable fields.
     *
     * @param string $db_options
     * @param string $key
     * @param array $args
     *
     * @return string
     */
    public function _repeatable($db_options, $key, $args)
    {
        $key         = sanitize_key($key);
        $label       = esc_attr($args['label']);
        $description = @$args['description'];
        $option_name = $this->option_name;
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";

        $fields = (array)$args['fields'];

        // if we have any repeatable fields saved to DB, use its array structure from DB to build out the repeatable fields
        // otherwise use the default array config for the field as defined in MailOptin\SendyConnect\ConnectSettingsPage class.
        if ((array)isset($db_options[$key]) && ! empty($db_options[$key])) {
            $repeatable_fields = $db_options[$key];
        } else {
            // we are adding an array with key 1 and empty value because repeatable field must begin with an index of 1.
            $repeatable_fields = array(1 => '');
        }

        ob_start(); ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <?php do_action('wp_cspa_before_text_field', $db_options, $option_name, $key, $args); ?>
            <td>
                <table>
                    <?php foreach ($repeatable_fields as $index => $repeatable_field) :
                        ?>
                        <tr class="<?php echo $key; ?>_fields_row mo_repeatable_fields_row">
                            <?php foreach ($fields as $field)  : ?>
                                <?php $field_name = $field['name']; ?>
                                <?php $field_value = is_array($repeatable_field) ? @$repeatable_field[$field_name] : ''; ?>
                                <?php $field_type = $field['type'] == 'text' ? '_text_field' : ''; ?>
                                <?php $field_placeholder = isset($field['placeholder']) ? $field['placeholder'] : ''; ?>
                                <?php $name_attr = $option_name . '[' . $key . ']' . '[' . $index . ']' . '[' . $field_name . ']'; ?>
                                <td><?php $this->$field_type("{$key}_{$field_name}", $name_attr, $field_value, 'all-options', $field_placeholder, $index); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <a href="#" class="mo_remove_repeatable"
                                   style="background: url(<?php echo admin_url('images/xit.gif'); ?>) no-repeat;">×</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td class="mo_add_repeatable_button" colspan="4"
                            style="float: none; clear:both; background: #fff;">
                            <a data-repeatable-field="<?php echo $key; ?>" class="button-secondary mo_add_repeatable"
                               style="margin: 0 0 5px;">
                                <?php _e('Add New Email List', 'mailoptin'); ?>
                            </a>
                        </td>
                    </tr>
                </table>
                <?php do_action('wp_cspa_after_text_field', $db_options, $option_name, $key, $args); ?>
                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $value
     * @param string $class
     * @param string $placeholder
     * @param string $data_key
     */
    public function _text_field($id, $name, $value, $class = 'regular-text', $placeholder = '', $data_key = '')
    {
        $id       = ! empty($id) ? $id : '';
        $data_key = ! empty($data_key) ? "data-index='$data_key'" : null;
        $value    = ! empty($value) ? $value : null;
        ?>
        <input type="text" placeholder="<?php echo $placeholder; ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="<?php echo $class; ?>" value="<?php echo $value; ?>" <?php echo $data_key; ?>/>
        <?php
    }

    /**
     * Useful if u wanna use any text/html in the settings page.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function _arbitrary($db_options, $key, $args)
    {
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $data        = @$args['data'];
        $description = @$args['description'];

        return "<tr id=\"$tr_id\"><td colspan=\"5\" style='margin:0;padding:0;'>" . $data . $description . '</td></tr>';
    }

    /**
     * Renders the text field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _text($db_options, $key, $args)
    {
        $key         = sanitize_key($key);
        $label       = esc_attr($args['label']);
        $defvalue    = sanitize_text_field(@$args['value']);
        $description = @$args['description'];
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $option_name = $this->option_name;
        $name_attr   = $option_name . '[' . $key . ']';
        $value       = ! empty($db_options[$key]) ? $db_options[$key] : $defvalue;

        if (isset($args['obfuscate_val']) && in_array($args['obfuscate_val'], [true, 'true'])) {
            $value = $this->obfuscate_string($value);
        }

        ob_start(); ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_text_field', $db_options, $option_name, $key, $args); ?>
                <?php $this->_text_field($key, $name_attr, $value); ?>
                <?php do_action('wp_cspa_after_text_field', $db_options, $option_name, $key, $args); ?>
                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    public function obfuscate_string($string)
    {
        $length            = strlen($string);
        $obfuscated_length = ceil($length / 2);
        $string            = str_repeat('*', $obfuscated_length) . substr($string, $obfuscated_length);

        return $string;
    }

    /**
     * Renders the custom field block
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _custom_field_block($db_options, $key, $args)
    {
        $key         = sanitize_key($key);
        $label       = esc_attr(@$args['label']);
        $description = @$args['description'];
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $option_name = $this->option_name;

        $data = @$args['data'];

        ob_start(); ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_custom_field_block', $db_options, $option_name, $key, $args); ?>
                <?php echo $data; ?>
                <?php do_action('wp_cspa_after_custom_field_block', $db_options, $option_name, $key, $args); ?>
                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the number field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _number($db_options, $key, $args)
    {
        $key         = esc_attr($key);
        $label       = esc_attr($args['label']);
        $defvalue    = sanitize_text_field(@$args['value']);
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $description = @$args['description'];
        $option_name = $this->option_name;
        $value       = ! empty($db_options[$key]) ? $db_options[$key] : $defvalue;
        ob_start(); ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_number_field', $db_options, $option_name, $key, $args); ?>
                <input type="number" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>"
                       class="regular-text" value="<?php echo $value; ?>"/>
                <?php do_action('wp_cspa_after_number_field', $db_options, $option_name, $key, $args); ?>

                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the password field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _password($db_options, $key, $args)
    {
        $key         = esc_attr($key);
        $label       = esc_attr($args['label']);
        $defvalue    = sanitize_text_field(@$args['value']);
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $disabled    = isset($args['disabled']) && $args['disabled'] === true ? 'disabled="disabled"' : '';
        $description = @$args['description'];
        $option_name = $this->option_name;
        $value       = ! empty($db_options[$key]) ? $db_options[$key] : $defvalue;
        ob_start(); ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_password_field', $db_options, $option_name, $key, $args); ?>
                <input type="password" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>"
                       class="regular-text" value="<?php echo $value; ?>" <?php echo $disabled; ?>/>
                <?php do_action('wp_cspa_after_password_field', $db_options, $option_name, $key, $args); ?>

                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the number text field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _hidden($db_options, $key, $args)
    {
        $key         = esc_attr($key);
        $label       = esc_attr($args['label']);
        $description = @$args['description'];
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $option_name = $this->option_name;
        /**
         * @todo add default value support to other field types.
         */
        $value = ! empty($db_options[$key]) ? $db_options[$key] : $args['value'];
        ob_start(); ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_hidden_field', $db_options, $option_name, $key, $args); ?>
                <input type="hidden" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>"
                       class="regular-text" value="<?php echo $value; ?>"/>
                <?php do_action('wp_cspa_after_hidden_field', $db_options, $option_name, $key, $args); ?>
                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the textarea field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _textarea($db_options, $key, $args)
    {
        $key         = esc_attr($key);
        $label       = esc_attr($args['label']);
        $description = @$args['description'];
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $rows        = ! empty($args['rows']) ? $args['rows'] : 5;
        $cols        = ! empty($args['column']) ? $args['column'] : '';
        $option_name = $this->option_name;
        $value       = ! empty($db_options[$key]) ? stripslashes($db_options[$key]) : @$args['value'];
        ob_start();
        ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_textarea_field', $db_options, $option_name, $key, $args); ?>
                <textarea rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"
                          name="<?php echo $option_name, '[', $key, ']'; ?>"
                          id="<?php echo $key; ?>"><?php echo $value; ?></textarea>
                <?php do_action('wp_cspa_after_textarea_field', $db_options, $option_name, $key, $args); ?>

                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the textarea field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _wp_editor($db_options, $key, $args)
    {
        $key         = esc_attr($key);
        $label       = esc_attr($args['label']);
        $description = @$args['description'];
        $tr_id       = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $option_name = $this->option_name;
        $value       = ! empty($db_options[$key]) ? stripslashes($db_options[$key]) : @$args['value'];
        $settings    = ! empty($args['settings']) ? $args['settings'] : [];

        $settings = array_replace(['textarea_name' => $option_name . '[' . $key . ']'], $settings);
        ob_start();
        ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_textarea_field', $db_options, $option_name, $key, $args); ?>
                <?php wp_editor($value, $key, $settings); ?>
                <?php do_action('wp_cspa_after_textarea_field', $db_options, $option_name, $key, $args); ?>

                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }


    /**
     * Renders the select dropdown
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _select($db_options, $key, $args)
    {
        $key                  = esc_attr($key);
        $label                = esc_attr($args['label']);
        $description          = @$args['description'];
        $tr_id                = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $disabled             = isset($args['disabled']) && $args['disabled'] === true ? 'disabled="disabled"' : '';
        $options              = $args['options'];
        $default_select_value = @$args['value'];
        $option_name          = $this->option_name;
        ob_start() ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>

                <?php do_action('wp_cspa_before_select_dropdown', $db_options, $option_name, $key, $args); ?>
                <select id="<?php echo $key; ?>"
                        name="<?php echo $option_name, '[', $key, ']'; ?>" <?php echo $disabled; ?>>
                    <?php foreach ($options as $option_key => $option_value) : ?>
                        <option value="<?php echo $option_key; ?>" <?php ! empty($db_options[$key]) ? selected($db_options[$key], $option_key) : selected($option_key, $default_select_value); ?>><?php echo esc_attr($option_value); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php do_action('wp_cspa_after_select_dropdown', $db_options, $option_name, $key, $args); ?>

                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the checkbox field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _checkbox($db_options, $key, $args)
    {
        $key            = esc_attr($key);
        $label          = esc_attr($args['label']);
        $description    = @$args['description'];
        $tr_id          = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $checkbox_label = ! empty($args['checkbox_label']) ? sanitize_text_field($args['checkbox_label']) : __('Activate', 'wp_cspa');
        $value          = ! empty($args['value']) ? esc_attr($args['value']) : 'true';
        $option_name    = $this->option_name;
        ob_start();
        ?>
        <tr id="<?php echo $tr_id; ?>">
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php do_action('wp_cspa_before_checkbox_field', $db_options, $option_name, $key, $args); ?>
                <strong><label for="<?php echo $key; ?>"><?php echo $checkbox_label; ?></label></strong>
                <input type="hidden" name="<?php echo $option_name, '[', $key, ']'; ?>" value="false">
                <input type="checkbox" id="<?php echo $key; ?>" name="<?php echo $option_name, '[', $key, ']'; ?>" value="<?php echo $value; ?>" <?php ! empty($db_options[$key]) ? checked($db_options[$key], $value) : ''; ?> />
                <?php do_action('wp_cspa_after_checkbox_field', $db_options, $option_name, $key, $args); ?>

                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Section header
     *
     * @param string $section_title
     *
     * @return string
     */
public function _header($section_title, $args = array())
{
    ob_start();
    ?>
    <div class="postbox">
        <div class="postbox-header">
            <h3 class="hndle is-non-sortable">
                <span><?php echo $section_title; ?></span><?php do_action('wp_cspa_settings_after_title', $this->option_name, $args); ?>
            </h3>
        </div>
        <div class="inside">
            <?php do_action('wp_cspa_header_before_box_content', $section_title, $this->option_name); ?>
            <table class="form-table">
                <?php
                return ob_get_clean();
                }


                /**
                 * Section header without the frills (title and toggle button).
                 *
                 * @return string
                 */
                public function _header_without_frills()
                {
                ob_start();
                ?>
                <div class="postbox">
                    <div class="inside">
                        <table class="form-table">
    <?php
    return ob_get_clean();
}

    /**
     * Section footer.
     *
     * @return string
     */
    public function _footer()
    {
        return '</table>
		<p><input class="button-primary" type="submit" name="save_' . $this->option_name . '" value="' . __('Save Changes', 'mailoptin') . '"></p>
	</div>
</div>';
    }

    /**
     * Section footer without "save changes" button.
     *
     * @return string
     */
    public function _footer_without_button()
    {
        return '</table>
	</div>
</div>';
    }

    /**
     * For building settings page with vertical sidebar tab menus.
     */
    public function build_sidebar_tab_style()
    {
        $settings_args = $this->main_content_config;
        $option_name   = $this->option_name;

        do_action('mailoptin_before_settings_page', $option_name);
        $nav_tabs         = '';
        $tab_content_area = '';

        if ( ! empty($settings_args)) {
            foreach ($settings_args as $key => $settings_arg) {
                $tab_title     = @$settings_arg['tab_title'];
                $section_title = @$settings_arg['section_title'];
                unset($settings_arg['tab_title']);
                unset($settings_arg['section_title']);
                $nav_tabs .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab"><span class="dashicons dashicons-admin-generic"></span> %2$s</a>', $key, $tab_title);

                if (isset($settings_arg[0]['section_title'])) {
                    $tab_content_area .= sprintf('<div id="%s" class="mailoptin-group-wrapper">', $key);
                    foreach ($settings_arg as $single_arg) {
                        $tab_content_area .= $this->metax_box_instance($single_arg);
                    }
                    $tab_content_area .= '</div>';
                } else {
                    $settings_arg['section_title'] = $section_title;
                    $tab_content_area              .= sprintf('<div id="%s" class="mailoptin-group-wrapper">', $key);
                    $tab_content_area              .= $this->metax_box_instance($settings_arg);
                    $tab_content_area              .= '</div>';
                }
            }

            $this->persist_plugin_settings();

            echo '<div class="wrap moview">';
            $this->settings_page_heading();
            do_action('mailoptin_after_settings_page_heading', $option_name);
            $this->do_settings_errors();
            settings_errors('wp_csa_notice');
            $this->settings_page_tab();
            echo '<div class="mailoptin-settings-wrap ' . $option_name . '" data-option-name="' . $option_name . '">';
            if ( ! empty($nav_tabs)) {
                echo '<h2 class="nav-tab-wrapper">' . $nav_tabs . '</h2>';
            }
            echo '<div class="metabox-holder mailoptin-tab-settings">';
            echo '<form method="post">';
            $this->nonce_field();
            echo $tab_content_area;
            echo apply_filters('wp_cspa_main_content_area', ob_get_clean(), $this->option_name);
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            do_action('mailoptin_after_settings_page', $option_name);
        }
    }

    /**
     * Build the settings page.
     *
     * @param bool $exclude_sidebar set to true to remove sidebar markup (.column-2)
     */
    public function build($exclude_sidebar = false, $exclude_top_tav_nav = false)
    {
        $this->persist_plugin_settings();

        $this->exclude_top_tav_nav = $exclude_top_tav_nav;

        $columns2_class = ! $exclude_sidebar ? ' columns-2' : null;
        ?>
        <div class="wrap">
            <?php $this->settings_page_heading(); ?>
            <?php $this->do_settings_errors(); ?>
            <?php settings_errors('wp_csa_notice'); ?>
            <?php $this->settings_page_tab(); ?>
            <?php do_action('wp_cspa_after_settings_tab', $this->option_name); ?>
            <div id="poststuff" class="wp_csa_view">
                <?php do_action('wp_cspa_before_metabox_holder_column'); ?>
                <div id="post-body" class="metabox-holder<?php echo $columns2_class; ?>">
                    <div id="post-body-content">
                        <?php do_action('wp_cspa_before_post_body_content', $this->option_name, $this->db_options); ?>
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post" <?php do_action('wp_cspa_form_tag', $this->option_name); ?>>
                                <?php $this->nonce_field(); ?>
                                <?php ob_start(); ?>
                                <?php $this->_settings_page_main_content_area(); ?>
                                <?php echo apply_filters('wp_cspa_main_content_area', ob_get_clean(), $this->option_name); ?>
                            </form>
                        </div>
                    </div>
                    <?php $this->setting_page_sidebar(); ?>
                </div>
            </div>
        </div>

        <?php $this->metabox_toggle_script(); ?>
        <?php
    }

    /**
     * Custom_Settings_Page_Api
     *
     * @param array $main_content_config
     * @param string $option_name
     * @param string $page_header
     *
     * @return Custom_Settings_Page_Api
     */
    public static function instance($main_content_config = array(), $option_name = '', $page_header = '')
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self($main_content_config, $option_name, $page_header);
        }

        return $instance;
    }
}