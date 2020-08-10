<?php

/**
 * Renders signup form
 *
 * @return void
 */
if (!function_exists('woochimp_form')) {
    function woochimp_form()
    {
        $opt = get_option('woochimp_options', $results);

        // Check if integration is enabled
        if (!$opt || !is_array($opt) || empty($opt) || !isset($opt['woochimp_enabled_shortcode']) || !$opt['woochimp_enabled_shortcode']) {
            return;
        }

        // Check if mailing list is selected
        if (!isset($opt['woochimp_list_shortcode']) || empty($opt['woochimp_list_shortcode'])) {
            return;
        }

        $html = woochimp_prepare_form($opt, 'shortcode');

        echo $html;
    }
}

/**
 * Returns generated form to be displayed
 *
 * @param array $form
 * @param array $opt
 * @param string $context
 * @param mixed $widget_args
 * @return string
 */
if (!function_exists('woochimp_prepare_form')) {
    function woochimp_prepare_form($opt, $context, $widget_args = null)
    {

        // Define form styles
        $form_styles = array(
            '2' => 'woochimp_skin_general',
        );

        $form = '';

        // Set ajax url
        $form .= '<script>var ajaxurl = \'' . admin_url('admin-ajax.php') . '\';</script>';

        // Display custom css
        if ($opt['woochimp_shortcode_css'] != '') {
            $form .= '<style>' . $opt['woochimp_shortcode_css'] . '</style>';
        }

        // Override styles if needed
        if ($opt['woochimp_shortcode_skin'] != '1') {
            $form .= '<div class="woochimp-reset ' . ($opt['woochimp_shortcode_skin'] > 1 ? $form_styles[$opt['woochimp_shortcode_skin']] : '') . ' woochimp_sc">';
        }
        else {
            $form .= '<div class="woochimp_sc">';
        }

        // Make sure we now this is a shortcode
        $form .= '<div class="woochimp_shortcode_content">';

        // Begin form
        $form .= '<form id="woochimp_registration_form_shortcode">' .
                '<table><thead><tr><th>' . $opt['woochimp_label_subscribe_shortcode'] . '</th></tr></thead>' .
                '<tbody><tr>';

        // Email address
        $form .= '<td>';

        if (!$opt['woochimp_shortcode_show_labels_inline']) {
            $form .= '<label for="woochimp_shortcode_subscription_email">' . $opt['woochimp_label_email'] . '</label>';
        }

        $form .= '<input type="text" name="woochimp_shortcode_subscription[email]" id="woochimp_shortcode_subscription_email" class="woochimp_shortcode_field" placeholder="' . ($opt['woochimp_shortcode_show_labels_inline'] ? $opt['woochimp_label_email'] : '') . '" title="' . $opt['woochimp_label_email'] . '" /></td>';

        // Custom fields for mail merge
        if (!empty($opt['woochimp_shortcode_fields'])) {
            foreach ($opt['woochimp_shortcode_fields'] as $field) {
                $form .= '</tr><tr><td>';

                if (!$opt['woochimp_shortcode_show_labels_inline']) {
                    $form .= '<label for="woochimp_shortcode_subscription_custom_'. $field['tag'] .'">' . $field['name'] . '</label>';
                }

                $form .= '<input type="text" name="woochimp_shortcode_subscription[custom]['. $field['tag'] .']" id="woochimp_shortcode_subscription_custom_'. $field['tag'] .'" class="woochimp_shortcode_field" placeholder="' . ($opt['woochimp_shortcode_show_labels_inline'] ? $field['name'] : '') . '" title="' . $field['name'] . '" /></td>';
            }
        }

        $form .= '</tr>';

        // Optional checkbox
        if ($opt['woochimp_subscription_shortcode_privacy_checkbox']) {
            $form .= '<tr><td><input type="checkbox" id="woochimp_shortcode_field_consent_checkbox" name="woochimp_shortcode_field_consent_checkbox" value="1"><label id="woochimp_shortcode_field_consent_checkbox_text">' . $opt['woochimp_subscription_shortcode_privacy_checkbox_text'] . '</label></td></tr>';
        }

        // Submit button
        $form .= '<tr><td><button type="button" id="woochimp_shortcode_subscription_submit" value="'. $opt['woochimp_label_button'] .'">'. $opt['woochimp_label_button'] .'</button></td>';

        // End form
        $form .= '</tr></tbody></table></form></div></div><div style="reset: both"></div>';

        // Hook to load assets
        do_action('woochimp_load_frontend_assets');

        return $form;
    }
}

?>