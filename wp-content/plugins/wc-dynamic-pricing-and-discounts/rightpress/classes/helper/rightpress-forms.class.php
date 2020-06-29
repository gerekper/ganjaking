<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Forms Helper
 *
 * @class RightPress_Forms
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Forms
{

    /**
     * Render text field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function text($params)
    {
        self::input('text', $params, array('value', 'maxlength', 'placeholder', 'readonly'));
    }

    /**
     * Render hidden field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function hidden($params)
    {
        self::input('hidden', $params, array('value'));
    }

    /**
     * Render text area field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function textarea($params)
    {
        // Get attributes
        $attributes = self::attributes($params, array('value', 'maxlength', 'placeholder', 'rows'), 'textarea');

        // Get value
        $value = !empty($params['value']) ? $params['value'] : '';

        // Generate field html
        $field_html = '<textarea ' . $attributes . '>' . $value . '</textarea>';

        // Render field
        self::output($params, $field_html, 'textarea');
    }

    /**
     * Render password field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function password($params)
    {
        $params['autocomplete'] = 'off';
        self::input('password', $params, array('value', 'maxlength', 'placeholder'));
    }

    /**
     * Render email field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function email($params)
    {
        self::input('email', $params, array('value', 'maxlength', 'placeholder'));
    }

    /**
     * Render decimal field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function decimal($params)
    {
        self::number($params, true);
    }

    /**
     * Render number field
     *
     * @access public
     * @param array $params
     * @param bool $is_decimal
     * @return void
     */
    public static function number($params, $is_decimal = false)
    {
        // Accept decimals
        if ($is_decimal && !isset($params['step'])) {
            $params['step'] = 'any';
        }

        // Print field
        self::input('number', $params, array('value', 'maxlength', 'placeholder', 'step', 'min'));
    }

    /**
     * Render date field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function date($params)
    {
        // Disable autocomplete
        $params['autocomplete'] = 'off';

        // Display as regular text field, will initialize Datetimepicker based on object's class
        self::input('text', $params, array('value', 'placeholder'), true);
    }

    /**
     * Render select field
     *
     * @access public
     * @param array $params
     * @param bool $is_multiple
     * @param bool $is_grouped
     * @param bool $prepend_group_key
     * @return void
     */
    public static function select($params, $is_multiple = false, $is_grouped = false, $prepend_group_key = false)
    {
        // Get attributes
        $attributes = self::attributes($params, array(), 'select');

        // Get options
        $options = self::options($params, $is_grouped, $prepend_group_key);

        // Check if it's multiselect
        $multiple_html = $is_multiple ? 'multiple' : '';

        // Generate field html
        $field_html = '<select ' . $multiple_html . ' ' . $attributes . '>' . $options . '</select>';

        // Render field
        $field_type = $is_multiple ? 'multiselect' : ($is_grouped ? 'grouped_select' : 'select');
        self::output($params, $field_html, $field_type);
    }

    /**
     * Render grouped select field
     *
     * @access public
     * @param array $params
     * @param bool $prepend_group_key
     * @return void
     */
    public static function grouped_select($params, $prepend_group_key = false)
    {
        self::select($params, false, true, $prepend_group_key);
    }

    /**
     * Render multiselect field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function multiselect($params)
    {
        self::select($params, true);
    }

    /**
     * Render checkbox field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function checkbox($params)
    {
        self::checkbox_or_radio('checkbox', $params);
    }

    /**
     * Render radio field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function radio($params)
    {
        self::checkbox_or_radio('radio', $params);
    }

    /**
     * Render checkbox or radio field
     *
     * @access public
     * @param string $type
     * @param array $params
     * @return void
     */
    public static function checkbox_or_radio($type, $params)
    {
        $field_html = '';

        // Single field?
        if (empty($params['options'])) {
            $attributes = self::attributes($params, array('value', 'checked'), $type);
            $field_html .= '<input type="' . $type . '" ' . $attributes . '>';
        }

        // Set of fields - iterate over options and generate field for each option
        else {

            // Open list
            $field_html .= '<ul>';

            // Iterate over field options and display as individual items
            foreach ($params['options'] as $key => $label) {

                // Customize params
                $custom_params = $params;
                $custom_params['id'] = $custom_params['id'] . '_' . $key;

                // Get attributes
                $attributes = self::attributes($custom_params, array(), $type);

                // Check if this item needs to be checked
                if (isset($params['value'])) {
                    $values = (array) $params['value'];
                    $checked = in_array($key, $values) ? 'checked="checked"' : '';
                }
                else {
                    $checked = (isset($params['checked']) && in_array($key, $params['checked']) ? 'checked="checked"' : '');
                }

                // Generate HTML
                $field_html .= '<li><input type="' . $type . '" value="' . $key . '" ' . $checked . ' ' . $attributes . '>' . (!empty($label) ? ' ' . $label : '') . '</li>';
            }

            // Close list
            $field_html .= '</ul>';
        }

        // Render field
        self::output($params, $field_html, $type);
    }

    /**
     * Render file field
     *
     * @access public
     * @param array $params
     * @return void
     */
    public static function file($params)
    {
        self::input('file', $params, array('accept'));
    }

    /**
     * Render generic input field
     *
     * @access public
     * @param string $type
     * @param array $params
     * @param array $custom_attributes
     * @param bool $is_date
     * @return void
     */
    private static function input($type, $params, $custom_attributes = array(), $is_date = false)
    {
        // Get attributes
        $attributes = self::attributes($params, $custom_attributes, $type);

        // Generate field html
        $field_html = '<input type="' . $type . '" ' . $attributes . '>';

        // Render field
        self::output($params, $field_html, $type, $is_date);
    }

    /**
     * Render attributes
     *
     * @access public
     * @param array $params
     * @param array $custom
     * @param string $type
     * @return void
     */
    private static function attributes($params, $custom = array(), $type = 'text')
    {
        $html = '';

        // Get full list of attributes
        $attributes = array_merge(array('type', 'name', 'id', 'class', 'autocomplete', 'style', 'title', 'placeholder'), $custom);

        // Additional attributes for admin ui
        if (is_admin()) {
            $attributes[] = 'required';
            $attributes[] = 'disabled';
        }

        // Extract attributes and append to html string
        foreach ($attributes as $attribute) {
            if (isset($params[$attribute]) && !RightPress_Help::is_empty($params[$attribute])) {
                $html .= $attribute . '="' . $params[$attribute] . '" ';
            }
        }

        // Extract any data attributes
        foreach ($params as $param_key => $param) {
            if (RightPress_Help::string_begins_with_substring($param_key, 'data-') && !in_array($param_key, $attributes, true)) {
                $html .= $param_key . '="' . $param . '" ';
            }
        }

        // Return attributes string
        return $html;
    }

    /**
     * Get options for select field
     *
     * @access public
     * @param array $params
     * @param bool $is_grouped
     * @param bool $prepend_group_key
     * @return string
     */
    private static function options($params, $is_grouped = false, $prepend_group_key = false)
    {
        $html = '';
        $selected = array();

        // Get selected option(s)
        if (isset($params['value'])) {
            $selected = (array) $params['value'];
        }
        else if (!empty($params['selected'])) {
            $selected = (array) $params['selected'];
        }

        // Extract options and append to html string
        if (!empty($params['options']) && is_array($params['options'])) {

            // Fix array depth if options are not grouped
            if (!$is_grouped) {
                $params['options'] = array(
                    'not_grouped' => array(
                        'options' => $params['options'],
                    ),
                );
            }

            // Iterate over option groups
            foreach ($params['options'] as $group_key => $group) {

                // Option group start
                if ($is_grouped) {
                    $html .= '<optgroup label="' . $group['label'] . '">';
                }

                // Iterate over options
                foreach ($group['options'] as $option_key => $option) {

                    // Get option key
                    $option_key = (($is_grouped && $prepend_group_key) ? $group_key . '__' . $option_key : $option_key);

                    // Get option data
                    $option_data = '';

                    if (!empty($params['option_data'][$option_key])) {
                        foreach ($params['option_data'][$option_key] as $data_key => $data) {
                            $option_data .= 'data-' . $data_key . '="' . htmlspecialchars($data) . '" ';
                        }
                    }

                    // Check if option is selected
                    $selected_html = in_array($option_key, $selected) ? 'selected="selected"' : '';

                    // Format option html
                    $html .= '<option value="' . $option_key . '" ' . $option_data . ' ' . $selected_html . '>' . $option . '</option>';
                }

                // Option group end
                if ($is_grouped) {
                    $html .= '</optgroup>';
                }
            }
        }

        return $html;
    }

    /**
     * Render field label
     *
     * @access public
     * @param array $params
     * @return string
     */
    private static function label($params)
    {
        echo self::label_html($params);
    }

    /**
     * Get field label html
     *
     * @access public
     * @param array $params
     * @return string
     */
    private static function label_html($params)
    {
        // Check if label needs to be displayed
        if (!empty($params['id']) && isset($params['label']) && !RightPress_Help::is_empty($params['label'])) {

            // Return label html
            return '<label for="' . $params['id'] . '">' . $params['label'] . '</label>';
        }

        return '';
    }

    /**
     * Render field description
     *
     * @access public
     * @param array $params
     * @return string
     */
    private static function description($params)
    {
        echo self::description_html($params);
    }

    /**
     * Get field description html
     *
     * @access public
     * @param array $params
     * @return string
     */
    private static function description_html($params)
    {
        if (!empty($params['description'])) {
            return '<small>' . $params['description'] . '</small>';
        }

        return '';
    }

    /**
     * Print custom content before field
     *
     * @access public
     * @param array $params
     * @return string
     */
    private static function before($params)
    {

        if (!empty($params['before'])) {
            echo $params['before'];
        }
    }

    /**
     * Print custom content after field
     *
     * @access public
     * @param array $params
     * @return string
     */
    private static function after($params)
    {

        if (!empty($params['after'])) {
            echo $params['after'];
        }
    }

    /**
     * Output field based on context
     *
     * @access public
     * @param array $params
     * @param string $field_html
     * @param string $type
     * @param bool $is_date
     * @return void
     */
    private static function output($params, $field_html, $type, $is_date = false)
    {
        // Open container
        self::output_begin($type, $is_date);

        // Print label
        self::label($params);

        // Print custom content before field
        self::before($params);

        // Print field
        echo $field_html;

        // Print custom content after field
        self::after($params);

        // Print description after field
        self::description($params);

        // Close container
        self::output_end($type);
    }

    /**
     * Output container begin
     *
     * @access public
     * @param string $type
     * @param bool $is_date
     * @return void
     */
    private static function output_begin($type, $is_date = false)
    {

    }

    /**
     * Output container end
     *
     * @access public
     * @param string $type
     * @return void
     */
    private static function output_end($type)
    {

    }

    /**
     * Check if field type has options
     *
     * @access public
     * @param string $type
     * @return bool
     */
    public static function has_options($type)
    {
        return in_array($type, array('select', 'multiselect', 'checkbox', 'radio'));
    }

}
