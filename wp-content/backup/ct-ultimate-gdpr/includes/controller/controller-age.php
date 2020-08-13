<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CT_Ultimate_GDPR_Controller_Age
 *
 */
class CT_Ultimate_GDPR_Controller_Age extends CT_Ultimate_GDPR_Controller_Abstract
{

    /**
     *
     */
    const ID = 'ct-ultimate-gdpr-age';

    /**
     *
     */
    const LEVEL_BELOW_ENTER = 1;
    /**
     *
     */
    const LEVEL_BETWEEN_ENTER_SELL = 2;
    /**
     *
     */
    const LEVEL_ABOVE_SELL = 3;

    /** @var @bool true if user is from ca */
    private $is_user_from_ca = false;

    /**
     * @var int
     */
    private $user_id;
    /**
     * @var array
     */
    private $user_meta;

    /**
     * @var
     */
    private $attachment_id;

    /**
     * Runs on init
     */
    public function init()
    {
        $this->grab_user_data();
        $this->check_if_user_is_from_ca();
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts_action'), 1);
        add_action('wp_ajax_ct_ultimate_gdpr_age_set_date', array($this, 'request_set_date'));
        add_action('wp_ajax_nopriv_ct_ultimate_gdpr_age_set_date', array($this, 'request_set_date'));
        add_action('wp_ajax_ct_ultimate_gdpr_age_set_date', array($this, 'send_ajax_response'));
        add_action('wp_ajax_nopriv_ct_ultimate_gdpr_age_set_date', array($this, 'send_ajax_response'));
        add_filter('ct_ultimate_gdpr_redirect', array($this, 'filter_redirect'), 10, 4);

        if (is_admin()) {
            $this->enqueue_age_background_image_upload_handler();
        }

    }

    /**
     * @param int $user_id
     */
    public function grab_user_data($user_id = 0)
    {
        $this->user_id   = $user_id ? $user_id : get_current_user_id();
        $this->user_meta = get_user_meta($this->user_id, $this->get_id(), true);

    }

    /**
     * Disable redirect for users aged <13 and >=16
     *
     * @param $return
     * @return bool
     */
    public function filter_redirect($return, $url, $stack, $priority)
    {
        if (ct_ultimate_gdpr_do_age_restriction_apply()) {

            // don't redirect if birth date not yet entered
            if (!ct_ultimate_gdpr_get_user_date_of_birth()) {
                return false;
            }

            // don't redirect for selected age ranges
            if ($priority < CT_Ultimate_GDPR_Model_Redirect::PRIORITY_HIGH && (ct_ultimate_gdpr_should_age_unblock_features() || ct_ultimate_gdpr_should_age_block_features())) {
                return false;
            }
        }

        return $return;
    }

    /**
     * send a response if the request an ajax action
     * @param $notice
     */
    public function send_ajax_response()
    {
        wp_send_json(array('notices' => CT_Ultimate_GDPR_Model_Front_View::instance()->get('notices')));
    }

    /**
     * @param bool $preview
     */
    private function check_if_user_is_from_ca($preview = false)
    {
        $is_user_from_ca = false;

        if ($this->get_option('age_check_if_user_is_from_ca', false) === false) {

            // always assume CA
            $is_user_from_ca = true;
        }

        if ($this->is_consent_valid()) {

            $is_user_from_ca = true;

        }

        if (!$is_user_from_ca || $preview) {

            $user_ip = ct_ultimate_gdpr_get_user_ip();
            try {

                $db          = new \IP2Location\Database(ct_ultimate_gdpr_path('vendor/GeoIP/IP2LOCATION-LITE-DB3.BIN'), \IP2Location\Database::FILE_IO);
                $region_name = $db->lookup($user_ip, \IP2Location\Database::REGION_NAME);

            } catch (Exception $e) {
                $region_name = '';
            }

            if ($region_name === 'California') {
                $is_user_from_ca = true;
            }

            if ($preview) {
                echo "<h1 class='container'>Region name: $region_name</h1>";
                echo "<h1 class='container'>Is california: ".($is_user_from_ca ? 'true' : 'false')."</h1>";
            }

        }

        $is_user_from_ca       = apply_filters('ct_ugdpr_check_if_user_is_from_ca', $is_user_from_ca);
        $this->is_user_from_ca = $is_user_from_ca;
    }

    /**
     *
     */
    public function render_menu_page()
    {
        return parent::render_menu_page();
    }

    /**
     * @param bool $force
     */
    public function wp_enqueue_scripts_action($force = false)
    {

        if ($force || $this->should_display_on_page(get_queried_object_id())) {

            /* cookie popup features can be in footer */
            wp_enqueue_script(
                'ct-ultimate-gdpr-age-popup',
                ct_ultimate_gdpr_url('assets/js/age-popup.js'),
                array('jquery'),
                ct_ultimate_gdpr_get_plugin_version(),
                true
            );
            wp_enqueue_script('ct-ultimate-gdpr-base64'
                , ct_ultimate_gdpr_url('assets/js/jquery.base64.min.js'),
                array('jquery'),
                ct_ultimate_gdpr_get_plugin_version(),
                true
            );

            $my_account_page = get_permalink($this->get_option('age_verification_page', '', 'page'));
            if ($my_account_page && false === stripos($my_account_page, '//')) {
                $my_account_page = set_url_scheme("//$my_account_page");
            }

            wp_localize_script('ct-ultimate-gdpr-age-popup', 'ct_ultimate_gdpr_age',
                array(
                    'ajaxurl'               => admin_url('admin-ajax.php'),
                    'my_account_page_url'   => $my_account_page,
                    'consent'               => $this->is_consent_valid(),
                    'consent_expire_time'   => $this->get_expire_time(),
                    'consent_time'          => time(),
                    'consent_default_level' => 1,
                    'consent_accept_level'  => 1,
                    'enabled'               => $this->get_option('age_enabled'),
                    'age_limit_to_sell'     => $this->get_option('age_limit_to_sell', 16),
                    'age_limit_to_enter'    => $this->get_option('age_limit_to_enter', 13),
                    'scheduled_redirect'    => CT_Ultimate_GDPR_Model_Redirect::get_scheduled_redirection_url(),
                )
            );

            wp_enqueue_style('ct-ultimate-gdpr-age-popup', ct_ultimate_gdpr_url('/assets/css/age-popup.min.css'));

            // cookie custom styles
            $cookie_style = strip_tags($this->get_option('age_style', ''));
            if ($cookie_style) {
                wp_add_inline_style('ct-ultimate-gdpr-age-popup', $cookie_style);
            }

        }

        wp_enqueue_style('dashicons');

    }

    /**
     * Fires on user settings saved
     *
     * @param int $custom_expire_time
     */
    public function request_set_date($custom_expire_time = 0)
    {
        $date       = ct_ultimate_gdpr_get_value('ct-ultimate-gdpr-age-date', $this->get_request_array());
        $guard_date = ct_ultimate_gdpr_get_value('ct-ultimate-gdpr-age-guard-date', $this->get_request_array());
        $guard_name = ct_ultimate_gdpr_get_value('ct-ultimate-gdpr-age-guard-name', $this->get_request_array());
//        $skip_cookies = ct_ultimate_gdpr_get_value('skip_cookies', $this->get_request_array());
        $expire_time = $custom_expire_time ? $custom_expire_time : $this->get_expire_time();
        $time        = time();

        // strip long timezone - php unable to parse it
        $date = preg_replace('#\(.*\)#', '', $date);

        $age = ct_ultimate_gdpr_date_to_age($date);
        if ($age && $age < $this->get_option('age_limit_to_enter')) {
            $consent_level = self::LEVEL_BELOW_ENTER;
        } elseif ($age && $age >= $this->get_option('age_limit_to_sell')) {
            $consent_level = self::LEVEL_ABOVE_SELL;
        } else {
            $consent_level = self::LEVEL_BETWEEN_ENTER_SELL;
        }
        $value = array(
            'consent_expire_time' => $expire_time,
            'consent_level'       => $consent_level,
            'consent_time'        => $time,
            'date'                => $date,
        );

        if ($guard_date && $guard_name) {
            $value = array_merge($value,
                array(
                    'guard_date' => $guard_date,
                    'guard_name' => $guard_name,
                )
            );
        }

        // save settings in a user meta
        if ($this->user_id) {
            update_user_meta($this->user_id, $this->get_id(), $value);
        }

//        if ($skip_cookies) {
//            wp_die('ok');
//        }

        // save settings in a cookie
        ct_ultimate_gdpr_set_encoded_cookie($this->get_id(), ct_ultimate_gdpr_json_encode($value), $expire_time, '/');
        //for wp-rocket caching
        ct_ultimate_gdpr_set_encoded_cookie($this->get_id().'-level', ct_ultimate_gdpr_json_encode($consent_level), $expire_time, '/');

        CT_Ultimate_GDPR_Model_Front_View::instance()->add('notices', esc_html__("OK!", 'ct-ultimate-gdpr'));

    }


    /**
     * @return float|int
     */
    private function get_expire_time()
    {

        if ($this->options['age_expire']) {
            return time() + (int)$this->options['age_expire'];
        }

        return time() + YEAR_IN_SECONDS;

    }

    /**
     * Render age popup
     */
    public function render()
    {

        if ($this->is_consent_valid()) {
            return;
        }

        if (!$this->should_display_on_page(get_queried_object_id())) {
            return;
        }

        if ($this->is_user_bot()) {
            return false;
        }

        $template = 'age-popup';

        $options = array_merge($this->get_default_options(), $this->options);
        ct_ultimate_gdpr_render_template(ct_ultimate_gdpr_locate_template($template, false), true, $options);

    }

    /**
     * Check  bot/user agent
     *
     * @return bool
     */
    public function is_user_bot()
    {

        $age_bot_crawler   = $this->get_option('age_popup_consent_crawler', '');
        $bot_crawler_array = array_filter(array_map('trim', explode(',', $age_bot_crawler)));

        foreach ($bot_crawler_array as $bot) {
            if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), $bot)) {
                return true;
            }
        }

        return false;

    }

    /**
     * Get current privacy group level (either from POST data or usermeta or cookie)
     *
     * @return int
     */
    public function get_group_level()
    {

        if ($this->is_giving_consent()) {

            $consent_level = (int)ct_ultimate_gdpr_get_value('level', $this->get_request_array(), 0);

            if ($consent_level) {
                return $consent_level;
            }

        }

        $cookie_consent_level = $this->get_cookie('consent_level', $this->get_option('cookie_cookies_group_default', 1));

        return $cookie_consent_level;
    }

    /**
     * @return bool
     */
    private function is_giving_consent()
    {
        return wp_doing_ajax() && 'ct_ultimate_gdpr_age_consent_give' == ct_ultimate_gdpr_get_value('action', $this->get_request_array(), false);
    }

    /**
     * @return bool
     */
    public function is_consent_valid()
    {
        $user_valid = false;
        if ($this->user_id) {

            $user_valid = (
                is_array($this->user_meta) &&
                !empty($this->user_meta['consent_expire_time']) &&
                $this->user_meta['consent_expire_time'] > time()
            );

        }

        $cookie_date  = $this->get_cookie('consent_expire_time', 0);
        $cookie_valid = (
            $cookie_date &&
            $cookie_date > time()
        );

        return $cookie_valid || $user_valid;
    }

    /**
     * @return array
     */
    public function get_user_meta_data_array()
    {
        return $this->user_meta;
    }

    /**
     * @param string $variable_name
     * @param string $variable_default_value
     *
     * @return array|mixed|object|string
     */
    public function get_cookie($variable_name = '', $variable_default_value = '')
    {

        $value  = ct_ultimate_gdpr_get_encoded_cookie($this->get_id());
        $cookie = $value ? json_decode(stripslashes($value), true) : array();

        if ($variable_name) {
            return is_array($cookie) && isset($cookie[$variable_name]) ? $cookie[$variable_name] : $variable_default_value;
        }

        return $cookie;

    }

    /**
     * @param $page_id
     *
     * @return bool
     */
    private function should_display_on_page($page_id)
    {

        if(empty($this->get_option('age_display_all')) && empty($this->get_option('age_enabled'))){
          return false;
        }

        if ($this->get_option('age_display_all')) {
            return true;
        }

        if (in_array($page_id, $this->get_option('age_pages', array(), 'page'))) {
            return true;
        }

        if (is_front_page() && in_array('front', $this->get_option('age_pages', array()))) {
            return true;
        }

        if (is_home() && in_array('posts', $this->get_option('age_pages', array()))) {
            return true;
        }

        return false;

    }

    /**
     *
     */
    public function front_action()
    {
        if ($this->is_user_from_ca == true) {
            add_action('wp_footer', array($this, 'render'));
        }

        $this->maybe_schedule_redirect();

    }

    /**
     *
     */
    private function maybe_schedule_redirect()
    {
        $page = $this->get_option('age_verification_page');
        if ($page && ct_ultimate_gdpr_should_age_block_features()) {
            $url = get_permalink($page);
            new CT_Ultimate_GDPR_Model_Redirect(
                $url,
                CT_Ultimate_GDPR_Model_Redirect::PRIORITY_HIGH
            );
        }

    }

    /**
     *
     */
    public function admin_action()
    {
    }

    /**
     * @return string
     */
    public function get_id()
    {
        return self::ID;
    }

    /**
     * @return mixed|void
     */
    protected function admin_page_action()
    {
    }

    /**
     *
     */
    public function add_menu_page()
    {
        add_submenu_page(
            CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
            esc_html__('Age Verification', 'ct-ultimate-gdpr'),
            esc_html__('Age Verification', 'ct-ultimate-gdpr'),
            'manage_options',
            $this->get_id(),
            array($this, 'render_menu_page')
        );
    }

    /**
     * @return string
     */
    public function get_view_template()
    {
        return 'admin/admin-age';
    }

    /**
     * @return mixed
     */
    public function add_option_fields()
    {

        /* Age section - Age popup tab */

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-1_section-1', // ID
            esc_html__('Popup content', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-1_section-2', // ID
            esc_html__('Options', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-1_section-3', // ID
            esc_html__('Options', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        /* Age section - preferences tab */

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-2_section-1', // ID
            esc_html__('Epiration time', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-2_section-4', // ID
            esc_html__('Position of the age popup', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-2_section-2', // ID
            esc_html__('Buttons styles', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-2_section-5', // ID
            esc_html__('Popup box', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        add_settings_section(
            'ct-ultimate-gdpr-age_tab-2_section-6', // ID
            esc_html__('Custom style CSS', 'ct-ultimate-gdpr'), // Title
            null, // callback
            'ct-ultimate-gdpr-age' // Page
        );

        /* Age section fields */

        //TAB 1 -SECTION 1
        {
            add_settings_field(
                'age_popup_title', // ID
                esc_html__("Popup title", 'ct-ultimate-gdpr'), // Title
                array($this, 'render_field_age_popup_title'), // Callback
                'ct-ultimate-gdpr-age', // Page
                'ct-ultimate-gdpr-age_tab-1_section-1' // Section
            );

            add_settings_field(
                'age_popup_content', // ID
                esc_html__('Age popup content', 'ct-ultimate-gdpr'), // Title
                array($this, 'render_field_age_popup_content'), // Callback
                'ct-ultimate-gdpr-age', // Page
                'ct-ultimate-gdpr-age_tab-1_section-1' // Section
            );

            add_settings_field(
                'age_popup_label_accept', // ID
                esc_html__("Popup 'Submit' button label", 'ct-ultimate-gdpr'), // Title
                array($this, 'render_field_age_popup_label_accept'), // Callback
                'ct-ultimate-gdpr-age', // Page
                'ct-ultimate-gdpr-age_tab-1_section-1' // Section
            );

            //TAB 1 -SECTION 2
            add_settings_field(
                'age_popup_consent_crawler',
                esc_html__('Block user agents (eg. bots) containing the following texts (comma separated)', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_popup_consent_crawler'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-2'
            );

            add_settings_field(
                'age_limit_to_enter',
                esc_html__('Lower age limit to enter the website', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_limit_to_enter'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-2'
            );

            add_settings_field(
                'age_limit_to_sell',
                esc_html__('Lower age limit to provide personal data', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_limit_to_sell'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-2'
            );

            add_settings_field(
                'age_placeholder',
                esc_html__('Default age select placeholder', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_placeholder'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-2'
            );

            add_settings_field(
                'age_assume_default',
                esc_html__('Enter age value to assume if age not yet entered', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_assume_default'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-2'
            );

            //TAB 1 -SECTION 3
            add_settings_field(
                'age_enabled',
                esc_html__('Enable Age Verification', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_enabled'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-3'
            );

            add_settings_field(
                'age_display_all',
                esc_html__('Display the popup on all pages', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_display_all'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-3'
            );

            add_settings_field(
                'check_if_user_is_from_ca',
                esc_html__('Check if user is from California', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_check_if_user_is_from_ca'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-3'
            );

            add_settings_field(
                'age_pages',
                esc_html__('Select page where to display the popup', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_pages'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-3'
            );

            add_settings_field(
                'age_verification_page',
                esc_html__("Select 'my account' page (one to redirect to when user is underage, eg. show link on registration form)", 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_verification_page'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-1_section-3'
            );

            //TAB 2 - SECTION 1 - Age CHECK

            add_settings_field(
                'age_expire',
                esc_html__('Set age verification expire time [s]', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_expire'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-1'
            );

            //TAB 2 - SECTION 2 - BUTTON STYLES

            //button shape
            add_settings_field(
                'age_button_shape',
                esc_html__('Button shape', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_button_shape'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-2'
            );

            //button background color
            add_settings_field(
                'age_button_bg_color',
                esc_html__('Button background color', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_button_bg_color'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-2'
            );

            //button text color
            add_settings_field(
                'age_button_text_color',
                esc_html__('Button text color', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_button_text_color'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-2'
            );

            //TAB 2 - SECTION 4 - POSITION

            add_settings_field(
                'age_position',
                esc_html__('Position  (bottom, top and full page layout)', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_position'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-4'
            );

            add_settings_field(
                'age_position_distance',
                esc_html__('Distance from border [px]', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_position_distance'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-4'
            );

            //TAB 2 - SECTION 5 - BOX STYLES

            add_settings_field(
                'age_box_style',
                esc_html__('Box style', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_box_style'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-5'
            );

            add_settings_field(
                'age_box_shape',
                esc_html__('Box shape', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_box_shape'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-5'
            );

            add_settings_field(
                'age_background_color',
                esc_html__('Background color', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_background_color'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-5'
            );

            add_settings_field(
                'age_background_image',
                esc_html__('Background image', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_background_image'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-5'
            );

            // nasty hack for saving background image - beware
            add_settings_field(
                'age_read_tabs',
                esc_html__( 'Active Tab', 'ct-ultimate-gdpr' ),
                array( $this, 'render_field_age_read_tabs' ),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-5',
                array(
                    'class' => 'ct-ultimate-gdpr-hide'
                )
            );

            add_settings_field(
                'age_text_color',
                esc_html__('Text color', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_text_color'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-5'
            );

            //TAB 2 - SECTION 7 - PROTECTION SHORTCODE

            add_settings_field(
                'age_protection_shortcode_label',
                esc_html__('Protection shortcode label', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_protection_shortcode_label'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-7'
            );

            //TAB 2 - SECTION 6 - CUSTOM STYLE CSS

            add_settings_field(
                'age_style',
                esc_html__('Custom style CSS', 'ct-ultimate-gdpr'),
                array($this, 'render_field_age_style'),
                'ct-ultimate-gdpr-age',
                'ct-ultimate-gdpr-age_tab-2_section-6'
            );

        }
    }

    /**
     *
     */
    public function render_field_age_popup_consent_crawler()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    /**
     *
     */
    public function render_field_age_expire()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, YEAR_IN_SECONDS)
        );

    }

    /**
     *
     */
    public function render_field_age_read_tabs() {
        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

        $field_name = $admin->get_field_name( __FUNCTION__ );
        $value      = $admin->get_option_value( $field_name );

        printf(
            "<input class='ct-ultimate-gdpr-InputForTab' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            esc_html( $value )
        );
    }

    /**
     *
     */
    public function render_field_age_text_color()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, '#ffffff')
        );

    }

    /**
     *
     */
    public function render_field_age_background_color()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, '#ff7e27')
        );

    }

    /**
     *
     */
    public function render_field_age_background_image()
    {
        $admin          = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name     = $admin->get_field_name(__FUNCTION__);
        $feat_image_url = wp_get_attachment_url($admin->get_option_value_escaped($field_name, ''));
        $file           = basename($feat_image_url);
        $format_string  = "
            <input class='ct-ultimate-gdpr-field ct-cookie-background-image' type ='text' value='%s' readonly>
            <input class='ct-ultimate-gdpr-field ct-cookie-background-image' id='%s' name='%s' value='%s' style='display: none;'>
            <input class='ct-ultimate-gdpr-field ct-cookie-background-image' type='file' id='%s' name='%s' accept='image/*'/>
            <br/>
            <input class='button button-primary ct-cookie-background-image' name='ct-ultimate-gdpr-age-update-background-image' value='%s' type='submit'>
            <input class='button button-primary ct-cookie-background-image' name='ct-ultimate-gdpr-age-remove-background-image' value='%s' type='submit'>
            ";
        printf(
            $format_string,
            $file,
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, ''),
            'age_background_image_file',
            'age_background_image_file',
            esc_html__('Update', 'ct-ultimate-gdpr'),
            esc_html__('Remove', 'ct-ultimate-gdpr')
        );
    }

    /**
     *
     */
    public function render_field_age_box_style()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        $default     = ct_ultimate_gdpr_get_value($field_name, $this->get_default_options());
        $field_value = $admin->get_option_value_escaped($field_name, '');
        $field_value = $field_value ? $field_value : $default;

        $positions = array(
            'red_velvet'          => esc_html__('Red Velvet', 'ct-ultimate-gdpr'),
            'thin_mint'           => esc_html__('Thin Mint', 'ct-ultimate-gdpr'),
            'mint_chocolate'      => esc_html__('Mint Chocolate', 'ct-ultimate-gdpr'),
            'classic_createit'    => esc_html__('Classic createIT', 'ct-ultimate-gdpr'),
            'blueberry_orange'    => esc_html__('Blueberry with Orange', 'ct-ultimate-gdpr'),
            'blue_velvet'         => esc_html__('Blue Velvet', 'ct-ultimate-gdpr'),
            'chocolate_matcha'    => esc_html__('Chocolate Matcha', 'ct-ultimate-gdpr'),
            'classic_dark'        => esc_html__('Classic Dark', 'ct-ultimate-gdpr'),
            'classic_light_style' => esc_html__('Classic Light', 'ct-ultimate-gdpr'),
            'oreo'                => esc_html__('Oreo', 'ct-ultimate-gdpr'),
            'blue_shortbread'     => esc_html__('Blue Shortbread', 'ct-ultimate-gdpr'),
            'light_mint'          => esc_html__('Light Mint', 'ct-ultimate-gdpr'),
            'blue_cupcake'        => esc_html__('Blue Cupcake', 'ct-ultimate-gdpr'),
            'matcha'              => esc_html__('Matcha', 'ct-ultimate-gdpr'),
            'mint'                => esc_html__('Mint', 'ct-ultimate-gdpr'),
            'none'                => esc_html__('None', 'ct-ultimate-gdpr'),
        );

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        foreach ($positions as $value => $label) :

            $selected = ($field_value == $value) ? "selected" : '';
            echo "<option value='$value' $selected>$label</option>";

        endforeach;

        echo '</select>';

    }

    /**
     *
     */
    public function render_field_age_box_shape()
    {

        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $field_value = $admin->get_option_value($field_name);
        $positions   = array(
            'squared' => esc_html__('Squared', 'ct-ultimate-gdpr'),
            'rounded' => esc_html__('Rounded', 'ct-ultimate-gdpr'),
        );

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        foreach ($positions as $value => $label) :

            $selected = ($field_value == $value) ? "selected" : '';
            echo "<option value='$value' $selected>$label</option>";

        endforeach;

        echo '</select>';

    }


    /**
     *
     */
    public function render_field_age_button_settings()
    {

        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $field_value = $admin->get_option_value($field_name);
        $positions   = array(
            'text_only_' => esc_html__('Text Only', 'ct-ultimate-gdpr'),
            'text_icon_' => esc_html__('Icon and Text', 'ct-ultimate-gdpr'),
        );

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        foreach ($positions as $value => $label) :

            $selected = ($field_value == $value) ? "selected" : '';
            echo "<option value='$value' $selected>$label</option>";

        endforeach;

        echo '</select>';

    }

    /**
     *
     */
    public function render_field_age_button_shape()
    {

        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $field_value = $admin->get_option_value($field_name);
        $positions   = array(
            'squared' => esc_html__('Squared', 'ct-ultimate-gdpr'),
            'rounded' => esc_html__('Rounded', 'ct-ultimate-gdpr'),
        );

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        foreach ($positions as $value => $label) :

            $selected = ($field_value == $value) ? "selected" : '';
            echo "<option value='$value' $selected>$label</option>";

        endforeach;

        echo '</select>';

    }

    /**
     *
     */
    public function render_field_age_button_border_color()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, '#ffffff')
        );

    }

    /**
     *
     */
    public function render_field_age_button_text_color()
    {

        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $default     = ct_ultimate_gdpr_get_value($field_name, $this->get_default_options());
        $field_value = $admin->get_option_value_escaped($field_name, '');
        $field_value = $field_value ? $field_value : $default;

        printf(
            "<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $field_value
        );

    }

    /**
     *
     */
    public function render_field_age_button_size()
    {


        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $default     = ct_ultimate_gdpr_get_value($field_name, $this->get_default_options());
        $field_value = $admin->get_option_value_escaped($field_name, '');
        $field_value = $field_value ? $field_value : $default;

        $positions = array(
            'normal' => esc_html__('Normal', 'ct-ultimate-gdpr'),
            'large'  => esc_html__('Large', 'ct-ultimate-gdpr'),
        );

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        foreach ($positions as $value => $label) :

            $selected = ($field_value == $value) ? "selected" : '';
            echo "<option value='$value' $selected>$label</option>";

        endforeach;

        echo '</select>';

    }

    /**
     *
     */
    public function render_field_age_button_bg_color()
    {

        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $default     = ct_ultimate_gdpr_get_value($field_name, $this->get_default_options());
        $field_value = $admin->get_option_value_escaped($field_name, '');
        $field_value = $field_value ? $field_value : $default;

        printf(
            "<input class='ct-color-field ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $field_value
        );

    }

    /**
     *
     */
    public function render_field_age_popup_content()
    {

        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

        $field_name = $admin->get_field_name(__FUNCTION__);

        wp_editor(
            $admin->get_option_value($field_name),
            $this->get_id().'_'.$field_name,
            array(
                'textarea_rows' => 10,
                'textarea_name' => $admin->get_field_name_prefixed($field_name),
            )
        );

    }

    /**
     *
     */
    public function render_field_age_popup_label_accept()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    /**
     *
     */
    public function render_field_age_popup_title()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    /**
     *
     */
    public function render_field_age_limit_to_enter()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    /**
     *
     */
    public function render_field_age_limit_to_sell()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    public function render_field_age_placeholder()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' class='ct-datepicker' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    public function render_field_age_assume_default()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input type='text' class='' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name)
        );

    }

    /**
     *
     */
    public function render_field_age_style()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='10' cols='100'>%s</textarea>",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, '')
        );
    }

    /**
     *
     */
    public function render_field_age_display_all()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

    }

    /**
     *
     */
    public function render_field_age_enabled()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

    }

    /**
     *
     */
    public function render_field_age_check_if_user_is_from_ca()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

    }

    /**
     *
     */
    public function render_field_age_position()
    {

        $admin       = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name  = $admin->get_field_name(__FUNCTION__);
        $field_value = $admin->get_option_value($field_name);
        $positions   = array(
            'bottom_left_'       => esc_html__('Bottom left', 'ct-ultimate-gdpr'),
            'bottom_right_'      => esc_html__('Bottom right', 'ct-ultimate-gdpr'),
            'bottom_panel_'      => esc_html__('Bottom panel', 'ct-ultimate-gdpr'),
            'top_left_'          => esc_html__('Top left', 'ct-ultimate-gdpr'),
            'top_right_'         => esc_html__('Top right', 'ct-ultimate-gdpr'),
            'top_panel_'         => esc_html__('Top panel', 'ct-ultimate-gdpr'),
            'full_layout_panel_' => esc_html__('Full page layout', 'ct-ultimate-gdpr'),
        );

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        foreach ($positions as $value => $label) :

            $selected = ($field_value == $value) ? "selected" : '';
            echo "<option value='$value' $selected>$label</option>";

        endforeach;

        echo '</select>';

    }

    /**
     *
     */
    public function render_field_age_position_distance()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name, '20')
        );

    }

    /**
     *
     */
    public function render_field_age_pages()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        $values     = $admin->get_option_value($field_name);
        $post_types = ct_ultimate_gpdr_get_default_post_types();
        $posts      = ct_ultimate_gdpr_wpml_get_original_posts(array(
            'posts_per_page' => -1,
            'post_type'      => $post_types,
            'orderby'        => 'post_title',
        ));

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s" size="15" multiple>',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)."[]"
        );

        // default options
        echo "<option value=''></option>";

        $selected = is_array($values) && in_array('front', $values) ? "selected" : '';
        echo "<option value='front' $selected>".esc_html__('Front page', 'ct-ultimate-gdpr')."</option>";

        $selected = is_array($values) && in_array('posts', $values) ? "selected" : '';
        echo "<option value='posts' $selected>".esc_html__('Posts page', 'ct-ultimate-gdpr');

        /** @var WP_Post $post */
        foreach ($posts as $post) :

            $post_title = $post->post_title ? $post->post_title : $post->post_name;
            $post_id    = $post->ID;
            $selected   = is_array($values) && in_array($post_id, $values) ? "selected" : '';
            echo "<option value='$post->ID' $selected>$post_title</option>";

        endforeach;

        echo '</select>';

    }

    public function render_field_age_verification_page()
    {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);
        $value      = $admin->get_option_value($field_name);
        $post_types = ct_ultimate_gpdr_get_default_post_types();
        $posts      = ct_ultimate_gdpr_wpml_get_original_posts(array(
            'posts_per_page' => -1,
            'post_type'      => $post_types,
            'orderby'        => 'post_title',
        ));

        printf(
            '<select class="ct-ultimate-gdpr-field" id="%s" name="%s" size="15">',
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name)
        );

        // default options
        echo "<option value=''></option>";

        $selected = $value && 'front' == $value ? "selected" : '';
        echo "<option value='front' $selected>".esc_html__('Front page', 'ct-ultimate-gdpr')."</option>";

        $selected = $value && 'posts' == $value ? "selected" : '';
        echo "<option value='posts' $selected>".esc_html__('Posts page', 'ct-ultimate-gdpr');

        /** @var WP_Post $post */
        foreach ($posts as $post) :

            $post_title = $post->post_title ? $post->post_title : $post->post_name;
            $post_id    = $post->ID;
            $selected   = $value && $post_id == $value ? "selected" : '';
            echo "<option value='$post->ID' $selected>$post_title</option>";

        endforeach;

        echo '</select>';

    }

    /**
     * @return array
     */
    public function get_default_options()
    {

        return apply_filters("ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
            'age_enabled'                                   => false,
            'age_box_style'                                 => "classic_dark",
            'age_display_all'                               => true,
            'age_style'                                     => '',
            'age_expire'                                    => 31536000,
            'age_whitelist'                                 => 'PHPSESSID, wordpress, wp-settings-, __cfduid, ct-ultimate-gdpr-age',
            'age_content'                                   => '',
            'age_popup_label_accept'                        => esc_html__('Submit', 'ct-ultimate-gdpr'),
            'age_position'                                  => 'bottom_panel_',
            'age_position_distance'                         => 20,
            'age_box_shape'                                 => 'squared',
            'age_background_image'                          => '',
            'age_background_color'                          => '#262626',
            'age_text_color'                                => '#ffffff',
            'age_button_settings'                           => 'text_only_',
            'age_button_shape'                              => 'squared',
            'age_button_border_color'                       => '#6a8ee7',
            'age_button_text_color'                         => '#ffffff',
            'age_button_bg_color'                           => '#6a8ee7',
            'age_button_size'                               => 'normal',
            'age_gear_icon_position'                        => 'bottom_left_',
            'age_gear_icon_color'                           => '#ffffff',
            'age_trigger_modal_bg_shape'                    => 'round',
            'age_trigger_modal_bg'                          => '#000000',
            'age_trigger_modal_text'                        => esc_html__('Trigger', 'ct-ultimate-gdpr'),
            'age_trigger_modal_icon'                        => 'fa fa-cog',
            'age_settings_trigger'                          => 'icon_only_',
            'age_cookies_group_default'                     => 1,
            'age_group_popup_header_content'                => ct_ultimate_gdpr_render_template(ct_ultimate_gdpr_locate_template('cookie-group-popup-header-content', false)),
            'age_group_popup_label_will'                    => esc_html__('This website will:', 'ct-ultimate-gdpr'),
            'age_group_popup_label_wont'                    => esc_html__("This website won't:", 'ct-ultimate-gdpr'),
            'age_group_popup_label_save'                    => esc_html__("Save & Close", 'ct-ultimate-gdpr'),
            'age_group_popup_label_block_all'               => esc_html__('Block all', 'ct-ultimate-gdpr'),
            'age_group_popup_label_essentials'              => esc_html__('Essentials', 'ct-ultimate-gdpr'),
            'age_group_popup_label_functionality'           => esc_html__('Functionality', 'ct-ultimate-gdpr'),
            'age_group_popup_label_analytics'               => esc_html__('Analytics', 'ct-ultimate-gdpr'),
            'age_group_popup_label_advertising'             => esc_html__('Advertising', 'ct-ultimate-gdpr'),
            'age_group_popup_features_available_group_2'    => esc_html__("Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected;",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_nonavailable_group_2' => esc_html__("Remember your login details; Functionality: Remember social media settings; Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_available_group_3'    => esc_html__("Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settings; Functionality: Remember selected region and country;",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_nonavailable_group_3' => esc_html__("Remember your login details; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_available_group_4'    => esc_html__("Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settingsl Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions;",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_nonavailable_group_4' => esc_html__("Remember your login details; Advertising: Use information for tailored advertising with third parties; Advertising: Allow you to connect to social sites; Advertising: Identify device you are using; Advertising: Gather personally identifiable information such as name and location",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_available_group_5'    => esc_html__("Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settingsl Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Use information for tailored advertising with third parties; Advertising: Allow you to connect to social sitesl Advertising: Identify device you are using; Advertising: Gather personally identifiable information such as name and location",
                'ct-ultimate-gdpr'),
            'age_group_popup_features_nonavailable_group_5' => esc_html__("Remember your login details", 'ct-ultimate-gdpr'),
            'age_modal_header_color'                        => "#595959",
            'age_modal_text_color'                          => "#797979",
            'age_modal_skin'                                => "default",
            'age_protection_shortcode_label'                => esc_html__("This content requires cookies", 'ct-ultimate-gdpr'),
            'age_my_account_disclaimer'                     => esc_html__("Removal of your data will not limit in any way the system functionalities", 'ct-ultimate-gdpr'),
            'age_placeholder'                               => '',
            'age_limit_to_sell'                             => '16',
            'age_limit_to_enter'                            => '13',
            'age_assume_default'                            => '13',
            'age_verification_page'                         => '',
            'age_popup_title'                               => esc_html__("Age verification", 'ct-ultimate-gdpr'),
            'age_popup_content'                             => esc_html__("Enter your date of birth", 'ct-ultimate-gdpr'),
        ));

    }

    /**
     * @return array
     */
    public function get_all_options()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function is_user_from_ca()
    {
        return $this->is_user_from_ca;
    }

    public function enqueue_age_background_image_upload_handler()
    {
        if ($this->is_request_update_background_image()) {
            add_action('ct_ultimate_gdpr_after_controllers_registered', array($this, 'update_background_image'));
        } elseif ($this->is_request_remove_background_image()) {
            add_action('ct_ultimate_gdpr_after_controllers_registered', array($this, 'remove_background_image'));
        }
    }

    /**
     * @return bool
     */
    private function is_request_update_background_image()
    {
        if (ct_ultimate_gdpr_get_value('age_background_image_file', $_FILES) && $_FILES["age_background_image_file"]["size"] > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function is_request_remove_background_image()
    {
        if (ct_ultimate_gdpr_get_value('ct-ultimate-gdpr-age-remove-background-image', $_POST)) {
            return true;
        }

        return false;
    }

    /**
     *
     */
    public function remove_background_image()
    {
        $update_action_name = 'pre_update_option_'.self::ID;
        add_action($update_action_name, array($this, 'update_option_remove_age_background_image'));
    }

    /**
     *
     */
    public function update_background_image()
    {
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH."wp-admin".'/includes/image.php');
            require_once(ABSPATH."wp-admin".'/includes/file.php');
            require_once(ABSPATH."wp-admin".'/includes/media.php');
        }

        $attachment_id = media_handle_upload('age_background_image_file', 0);
        if (!is_wp_error($attachment_id)) {
            $this->attachment_id = $attachment_id;
            $update_action_name  = 'pre_update_option_'.self::ID;
            add_action($update_action_name, array($this, 'update_option_add_age_background_image'));
        }
    }

    /**
     * @param array $values
     * @return array
     */
    public function update_option_remove_age_background_image($values)
    {
        $options = $this->options;
        wp_delete_attachment($options['age_background_image']);
        $values['age_background_image'] = '';
        return $values;
    }

    /**
     * @param array $values
     * @return array
     */
    public function update_option_add_age_background_image($values)
    {
        $values['age_background_image'] = $this->attachment_id;
        return $values;
    }

}
