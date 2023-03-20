<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Wizard
 */
class CT_Ultimate_GDPR_Controller_Wizard extends CT_Ultimate_GDPR_Controller_Abstract
{

    /**
     *
     */
    const ID = 'ct-ultimate-gdpr-wizard';

    const NONCE_KEY = 'ct_ultimate_gdpr_wizard';

    const WHITELISTED_KEYS = array(
        'ct-ultimate-gdpr-admin',
        'ct-ultimate-gdpr-age',
        'ct-ultimate-gdpr-cookie',
        'ct-ultimate-gdpr-forgotten',
        'ct-ultimate-gdpr-dataaccess',
        'ct-ultimate-gdpr-breach',
        'ct-ultimate-gdpr-rectification',
        'ct-ultimate-gdpr-terms',
        'ct-ultimate-gdpr-policy',
        'ct-ultimate-gdpr-services'
    );


    protected $views = array(
        'not-found' => 'admin/wizard/not-found',
        'welcome' => 'admin/wizard/welcome',
        'welcomeb' => 'admin/wizard/welcomeb',
        'step1a' => 'admin/wizard/includes/step1a',
        'step1b' => 'admin/wizard/includes/step1b',
        'step1c' => 'admin/wizard/includes/step1c',
        'step2' => 'admin/wizard/includes/step2',
        'step2b' => 'admin/wizard/includes/step2b',
        'step3' => 'admin/wizard/includes/step3',
        'step4' => 'admin/wizard/includes/step4',
        'step4b' => 'admin/wizard/includes/step4b',
        'step5' => 'admin/wizard/includes/step5',
        'step6' => 'admin/wizard/includes/step6',
        'step7' => 'admin/wizard/includes/step7',
        'step8' => 'admin/wizard/includes/step8',
        'step8b' => 'admin/wizard/includes/step8b',
        'step8c' => 'admin/wizard/includes/step8c',
    );
    private $default_values = array();
    private $current_page = '';
    private $preview_url = 'ctgdprwizard';
    private $preview_param = 'shortcodepreview';


    /**
     * Get unique controller id (page name, option id)
     */
    public function get_id()
    {
        return self::ID;
    }

    public function get_nonce_key()
    {
        return self::NONCE_KEY;
    }

    public function get_whitelisted_keys()
    {
        return self::WHITELISTED_KEYS;
    }


    /**
     * Init after construct
     */
    public function init()
    {

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_action('admin_post_ct_ultimate_gdpr_wizard_save', array($this, 'wizard_save'));

        add_action( 'wp_ajax_ct_ultimate_gdpr_wizard_ajax1', array($this, 'wizard_ajax1') );

        add_action( 'template_include', array($this, 'ct_preview_iframe') );

        add_action( 'admin_init', function() {
            if ( current_user_can( 'manage_options' ) && ( ! wp_doing_ajax() ) ) {
                // $_REQUEST['step'] == step8b
                $step = $_REQUEST['step'] ?? '';
                if(in_array($step, array('step8b', 'step2b')) ){
                    $cookie_key = 'ct-ultimate-gdpr-cookie';
                    if (isset($_COOKIE[$cookie_key])) {
                        unset($_COOKIE[$cookie_key]);
                        // empty value and expiration one hour before
                        $res = setcookie($cookie_key, '', time() - 3600, '/');
                    }
                    
                }
            }
        } );

    }


    function ct_preview_iframe($template){

        if(isset($_GET[$this->preview_param])){
            // /ctgdprwizard?shortcodepreview=1
            global $wp;
            $current_url = home_url(add_query_arg($_GET,$wp->request));

            if (strpos($current_url, $this->preview_url) !== false) {
                global $wp_query;
                status_header( 200 );
                $wp_query->is_page = true;
                $wp_query->is_404=false;

                $page_template = ct_ultimate_gdpr_locate_template('admin/wizard/preview', false);
                return $page_template;
            }
        }

        return $template;
    }


    public function wizard_ajax1() {
        // step 1c ajax save

        $postID = intval( $_POST['postid'] );
        $serviceID = intval( $_POST['serviceid'] );

        update_post_meta( $postID, 'type_of_cookie', $serviceID );

        wp_send_json_success( );
    }

    public function admin_enqueue_scripts($hook_suffix)
    {
        if (strpos($hook_suffix, $this->get_id()) === false) {
            return;
        }
    
        wp_enqueue_style('ct-ultimate-gdpr-wizard-bs-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css', ct_ultimate_gdpr_get_plugin_version());
        wp_enqueue_style('ct-ultimate-gdpr-wizard-bs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', ct_ultimate_gdpr_get_plugin_version());

        wp_enqueue_style('ct-ultimate-gdpr-wizard-nice-select-css', ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/css/nice-select.css'), ct_ultimate_gdpr_get_plugin_version());
        wp_enqueue_style('ct-ultimate-gdpr-wizard', ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/css/admin-wizard.css'), ct_ultimate_gdpr_get_plugin_version());

        wp_enqueue_script('ct-ultimate-gdpr-wizard-nice-select-js', ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/js/jquery.nice-select.min.js'),
            array('jquery'),
            ct_ultimate_gdpr_get_plugin_version(),
            true
        );

        wp_enqueue_script('ct-ultimate-gdpr-wizard-js', ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/js/admin-wizard.js'),
            array('jquery'),
            ct_ultimate_gdpr_get_plugin_version(),
            true
        );

        wp_enqueue_script('ct-ultimate-gdpr-wizard-bs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
            array('jquery'),
            ct_ultimate_gdpr_get_plugin_version(),
            true
        );

    }

    public function add_option_fields()
    {

    }


    public function wizard_save()
    {

        $nonce = sanitize_text_field($_POST[$this->get_nonce_key()]);
        $action = sanitize_text_field($_POST['action']);

        if (!isset($nonce) || !wp_verify_nonce($nonce, $action)) {
            print 'Sorry, your nonce did not verify.';
            exit;
        }
        if (!current_user_can('manage_options')) {
            print 'You can\'t manage options';
            exit;
        }
        /**
         * whitelist keys that can be updated
         */
        $whitelisted_keys = $this->get_whitelisted_keys();

        $fields_to_update = [];

        foreach ($whitelisted_keys as $key) {
            if (array_key_exists($key, $_POST)) {
                $fields_to_update[$key] = $_POST[$key];
            }
        }

        /**
         * Loop through form fields keys and update data in DB (wp_options)
         */

        $this->db_update_options($fields_to_update);

        $redirect_to = $_POST['redirectToUrl'];

        if($redirect_to) {
            wp_safe_redirect( $redirect_to );
            exit;
        }
    }


    private function db_update_options($group)
    {
        foreach ($group as $key => $fields) {
            $db_opts = get_option($key);
            $updated = array_merge($db_opts, $fields);
            update_option($key, $updated);
        }
    }

    private function get_defaults()
    {
        $defaults = array();
        foreach ($this->get_whitelisted_keys() as $key => $val) {
            $defaults[$val] = get_option($val);
        }
        return $defaults;
    }

    /**
     * Do actions on frontend
     */
    public function front_action()
    {
    }

    /**
     * Do actions in admin (general)
     */
    public function admin_action()
    {
    }

    /**
     * Do actions on current admin page
     */
    protected function admin_page_action()
    {

    }


    /**
     * Get view template string
     * @return string
     */
    public function get_view_template()
    {
        return 'admin/wizard/index';
    }

    function load_view()
    {

        $this->default_values = $this->get_defaults();
        $this->current_page = ct_ultimate_gdpr_wizard_current_step();

        $current_views = isset($this->views[$this->current_page]) ? $this->views[$this->current_page] : $this->views['not-found'];

        $step_data_func_name =$this->current_page  . '_data';

        $args = [];
        if (method_exists($this, $step_data_func_name)) {
            $args = $this->$step_data_func_name();
        }
        /**
         * Default Wizard Template
         */

        echo '<div class="ct-ultimate-gdpr-wizard '. $this->current_page .'">';

        ct_ultimate_gdpr_render_template(ct_ultimate_gdpr_locate_template('admin/wizard/includes/nav', true));

        echo '<div class="container container1">';
            echo '<div class="inner">';

                $this->includeWithVariables(ct_ultimate_gdpr_locate_template($current_views, false), $args);

            echo '</div>';
        echo '</div>';

        ct_ultimate_gdpr_render_template(ct_ultimate_gdpr_locate_template('admin/wizard/includes/foot', true));

        echo '</div> <!-- / ct-ultimate-gdpr-wizard -->';
    }

    function includeWithVariables($filePath, $variables = array(), $print = true)
    {
        $output = NULL;
        if (file_exists($filePath)) {
            // Extract the variables to a local namespace
            extract($variables);

            // Start output buffering
            ob_start();

            // Include the template file
            include $filePath;

            // End buffering and return its contents
            $output = ob_get_clean();
        }
        if ($print) {
            print $output;
        }
        return $output;

    }

    private function step1a_data()
    {

        $args = [];
        $args['admin_envato_key'] = $this->render_input('ct-ultimate-gdpr-admin', 'admin_envato_key', true);

        return $args;

    }

    private function step1c_data()
    {

        $values = ct_ultimate_gdpr_wizard_get_levels();

        $services_posts = [];

        foreach ( $values as $value ) :
            $services_args = array(
                'post_type'        => 'ct_ugdpr_service',
                'numberposts'      => - 1,
                'meta_query' => array(
                    array(
                        'key' => 'type_of_cookie',
                        'value' => $value,
                    )
                ),
                'suppress_filters' => false,
            );

            $services_posts[$value] = get_posts($services_args);

            foreach ( $services_posts[$value] as $post ) {
                $post->is_active = get_post_meta($post->ID, 'is_active', true);
                $post->cookie_type = get_post_meta($post->ID, 'type_of_cookie', true);
            }

        endforeach;

        $args = [];
        $args['services'] = $services_posts;

        // add options
        $values = array(
            'manual'                     => __( 'Never', 'ct-ultimate-gdpr' ),
            'ct-ultimate-gdpr-weekly'    => __( 'Weekly', 'ct-ultimate-gdpr' ),
            'ct-ultimate-gdpr-monthly'   => __( 'Monthly', 'ct-ultimate-gdpr' ),
            'ct-ultimate-gdpr-quarterly' => __( 'Quarterly', 'ct-ultimate-gdpr' )
        );
        $args['cookie_scan_period'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_scan_period', $values);

        // add options
        $levels = ct_ultimate_gdpr_wizard_get_levels();
        $values = [];
        foreach($levels as $level){
            $values[$level] = CT_Ultimate_GDPR_Model_Group::get_label( $level );
        }
        $args['cookie_default_level_assigned_for_inserted_cookies'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_default_level_assigned_for_inserted_cookies', $values);


        return $args;

    }

    private function step2_data(){
        $args = [];

        $values = array(
            ''   => esc_html__( 'Select', 'ct-ultimate-gdpr' ),
            'cs' => 'Čeština',
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'hr' => 'Hrvatski',
            'hu' => 'Magyar',
            'no' => 'Norwegian',
            'it' => 'Italiano',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ro' => 'Română',
            'ru' => 'Русский',
            'sk' => 'Slovenčina',
            'dk' => 'Danish',
            'bg' => 'Bulgarian',
            'sv' => 'Swedish'
        );
        $args['cookie_content_language'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_content_language', $values);

        $args['cookie_content'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_content');
        $args['cookie_group_popup_header_content'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_header_content');
        $args['cookie_popup_label_accept'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_popup_label_accept');
        $args['cookie_popup_label_read_more'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_popup_label_read_more');
        $args['cookie_popup_label_settings'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_popup_label_settings');

        // labels
        $args['cookie_group_popup_label_will'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_will');
        $args['cookie_group_popup_label_wont'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_wont');
        $args['cookie_group_popup_label_block_all'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_block_all');
        $args['cookie_group_popup_label_essentials'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_essentials');
        $args['cookie_group_popup_label_functionality'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_functionality');
        $args['cookie_group_popup_label_analytics'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_analytics');
        $args['cookie_group_popup_label_advertising'] = $this->render_input('ct-ultimate-gdpr-cookie', 'cookie_group_popup_label_advertising');

        // list of features
        $args['cookie_group_popup_features_available_group_2'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_available_group_2');
        $args['cookie_group_popup_features_nonavailable_group_2'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_nonavailable_group_2');
        $args['cookie_group_popup_features_available_group_3'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_available_group_3');
        $args['cookie_group_popup_features_nonavailable_group_3'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_nonavailable_group_3');
        $args['cookie_group_popup_features_available_group_4'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_available_group_4');
        $args['cookie_group_popup_features_nonavailable_group_4'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_nonavailable_group_4');
        $args['cookie_group_popup_features_available_group_5'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_available_group_5');
        $args['cookie_group_popup_features_nonavailable_group_5'] = $this->render_textarea('ct-ultimate-gdpr-cookie', 'cookie_group_popup_features_nonavailable_group_5');

        // Cookie bar layout
        $values1 = array(
            'classic'       => esc_html__( 'Classic', 'ct-ultimate-gdpr' ),
            'classic_blue'  => esc_html__( 'Classic Dark', 'ct-ultimate-gdpr' ),
            'classic_light' => esc_html__( 'Classic Light', 'ct-ultimate-gdpr' ),
            'modern'        => esc_html__( 'Modern', 'ct-ultimate-gdpr' ),
            'apas_blue'     => esc_html__( 'Apas Blue', 'ct-ultimate-gdpr' ),
            'apas_black'    => esc_html__( 'Apas Black', 'ct-ultimate-gdpr' ),
            'apas_white'    => esc_html__( 'Apas White', 'ct-ultimate-gdpr' ),
            'kahk_blue'     => esc_html__( 'Kahk Blue', 'ct-ultimate-gdpr' ),
            'kahk_black'    => esc_html__( 'Kahk Black', 'ct-ultimate-gdpr' ),
            'kahk_white'    => esc_html__( 'Kahk White', 'ct-ultimate-gdpr' ),
            'oreo_blue'     => esc_html__( 'Oreo Blue', 'ct-ultimate-gdpr' ),
            'oreo_black'    => esc_html__( 'Oreo Black', 'ct-ultimate-gdpr' ),
            'oreo_white'    => esc_html__( 'Oreo White', 'ct-ultimate-gdpr' ),
            'wafer_blue'    => esc_html__( 'Wafer Blue', 'ct-ultimate-gdpr' ),
            'wafer_black'   => esc_html__( 'Wafer Black', 'ct-ultimate-gdpr' ),
            'wafer_white'   => esc_html__( 'Wafer White', 'ct-ultimate-gdpr' ),
            'jumble_blue'   => esc_html__( 'Jumble Blue', 'ct-ultimate-gdpr' ),
            'jumble_black'  => esc_html__( 'Jumble Black', 'ct-ultimate-gdpr' ),
            'jumble_white'  => esc_html__( 'Jumble White', 'ct-ultimate-gdpr' ),
            'khapse_blue'   => esc_html__( 'Khapse Blue', 'ct-ultimate-gdpr' ),
            'khapse_black'  => esc_html__( 'Khapse Black', 'ct-ultimate-gdpr' ),
            'khapse_white'  => esc_html__( 'Khapse White', 'ct-ultimate-gdpr' ),
            'tareco_blue'   => esc_html__( 'Tareco Blue', 'ct-ultimate-gdpr' ),
            'tareco_black'  => esc_html__( 'Tareco Black', 'ct-ultimate-gdpr' ),
            'tareco_white'  => esc_html__( 'Tareco White', 'ct-ultimate-gdpr' ),
            'kichel_blue'   => esc_html__( 'Kichel Blue', 'ct-ultimate-gdpr' ),
            'kichel_black'  => esc_html__( 'Kichel Black', 'ct-ultimate-gdpr' ),
            'kichel_white'  => esc_html__( 'Kichel White', 'ct-ultimate-gdpr' ),
            'macaron_blue'  => esc_html__( 'Macaron Blue', 'ct-ultimate-gdpr' ),
            'macaron_black' => esc_html__( 'Macaron Black', 'ct-ultimate-gdpr' ),
            'macaron_white' => esc_html__( 'Macaron White', 'ct-ultimate-gdpr' ),
            'wibele_blue'   => esc_html__( 'Wibele Blue', 'ct-ultimate-gdpr' ),
            'wibele_black'  => esc_html__( 'Wibele Black', 'ct-ultimate-gdpr' ),
            'wibele_white'  => esc_html__( 'Wibele White', 'ct-ultimate-gdpr' ),
        );

        $args['cookie_box_style'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_box_style', $values1);

        $values2   = array(
            'rounded' => esc_html__( 'Rounded', 'ct-ultimate-gdpr' ),
            'squared' => esc_html__( 'Squared', 'ct-ultimate-gdpr' ),
        );

        $args['cookie_box_shape'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_box_shape', $values2);

        $values3   = array(
            'text_only_' => esc_html__( 'Text Only', 'ct-ultimate-gdpr' ),
            'text_icon_' => esc_html__( 'Icon and Text', 'ct-ultimate-gdpr' ),
        );

        $args['cookie_button_settings'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_button_settings', $values3);

        $values4   = array(
            'rounded' => esc_html__( 'Rounded', 'ct-ultimate-gdpr' ),
            'squared' => esc_html__( 'Squared', 'ct-ultimate-gdpr' ),
        );

        $args['cookie_button_shape'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_button_shape', $values4);

        $values5 = array(
            'normal' => esc_html__( 'Normal', 'ct-ultimate-gdpr' ),
            'large'  => esc_html__( 'Large', 'ct-ultimate-gdpr' ),
        );

        $args['cookie_button_size'] = $this->render_select('ct-ultimate-gdpr-cookie', 'cookie_button_size', $values5);


        return $args;
    }

    private function step2b_data(){
        $args = [];
        $args['iframe_url'] = $this->get_iframe_url(0);
        return $args;
    }
    private function step3_data(){
        $args = [];
        $args['forgotten_automated_forget'] = $this->render_checkbox('ct-ultimate-gdpr-forgotten', 'forgotten_automated_forget');
        $args['forgotten_automated_user_email'] = $this->render_checkbox('ct-ultimate-gdpr-forgotten', 'forgotten_automated_user_email');
        $args['forgotten_notify_mail'] = $this->render_input('ct-ultimate-gdpr-forgotten', 'forgotten_notify_mail');
        $args['forgotten_notify_email_subject'] = $this->render_input('ct-ultimate-gdpr-forgotten', 'forgotten_notify_email_subject');

        $args['dataaccess_automated_dataaccess'] = $this->render_checkbox('ct-ultimate-gdpr-dataaccess', 'dataaccess_automated_dataaccess');
        $args['dataaccess_notify_mail'] = $this->render_input('ct-ultimate-gdpr-dataaccess', 'dataaccess_notify_mail');
        $args['dataaccess_mail_title'] = $this->render_input('ct-ultimate-gdpr-dataaccess', 'dataaccess_mail_title');

        $args['breach_mail_title'] = $this->render_input('ct-ultimate-gdpr-breach', 'breach_mail_title');

        $args['rectification_notify_mail'] = $this->render_input('ct-ultimate-gdpr-rectification', 'rectification_notify_mail');
        $args['rectification_mail_title'] = $this->render_input('ct-ultimate-gdpr-rectification', 'rectification_mail_title');

        return $args;
    }

    private function step4_data(){
        $args = [];
        $args['age_enabled'] = $this->render_checkbox('ct-ultimate-gdpr-age', 'age_enabled');

        $positions   = array(
            'bottom_left_'       => esc_html__('Bottom left', 'ct-ultimate-gdpr'),
            'bottom_right_'      => esc_html__('Bottom right', 'ct-ultimate-gdpr'),
            'bottom_panel_'      => esc_html__('Bottom panel', 'ct-ultimate-gdpr'),
            'top_left_'          => esc_html__('Top left', 'ct-ultimate-gdpr'),
            'top_right_'         => esc_html__('Top right', 'ct-ultimate-gdpr'),
            'top_panel_'         => esc_html__('Top panel', 'ct-ultimate-gdpr'),
            'full_layout_panel_' => esc_html__('Full page layout', 'ct-ultimate-gdpr'),
        );
        $args['age_position'] = $this->render_select('ct-ultimate-gdpr-age', 'age_position', $positions);

        $args['age_limit_to_enter'] = $this->render_input('ct-ultimate-gdpr-age', 'age_limit_to_enter');
        $args['age_limit_to_sell'] = $this->render_input('ct-ultimate-gdpr-age', 'age_limit_to_sell');
        $args['age_popup_title'] = $this->render_input('ct-ultimate-gdpr-age', 'age_popup_title');
        $args['age_popup_content'] = $this->render_textarea('ct-ultimate-gdpr-age', 'age_popup_content');
        $args['age_popup_label_accept'] = $this->render_input('ct-ultimate-gdpr-age', 'age_popup_label_accept');

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

        $args['age_box_style'] = $this->render_select('ct-ultimate-gdpr-age', 'age_box_style', $positions);

        $positions   = array(
            'squared' => esc_html__('Squared', 'ct-ultimate-gdpr'),
            'rounded' => esc_html__('Rounded', 'ct-ultimate-gdpr'),
        );

        $args['age_box_shape'] = $this->render_select('ct-ultimate-gdpr-age', 'age_box_shape', $positions);

        $positions   = array(
            'squared' => esc_html__('Squared', 'ct-ultimate-gdpr'),
            'rounded' => esc_html__('Rounded', 'ct-ultimate-gdpr'),
        );
        $args['age_button_shape'] = $this->render_select('ct-ultimate-gdpr-age', 'age_button_shape', $positions);

        return $args;

    }

    private function step5_data(){
        $args = [];

        $args['terms_require_administrator'] = $this->render_checkbox('ct-ultimate-gdpr-terms', 'terms_require_administrator');
        $args['terms_require_users'] = $this->render_checkbox('ct-ultimate-gdpr-terms', 'terms_require_users');
        $args['terms_require_guests'] = $this->render_checkbox('ct-ultimate-gdpr-terms', 'terms_require_guests');

        // START terms_target_page
        $post_types = ct_ultimate_gpdr_get_default_post_types();
        $posts      = ct_ultimate_gdpr_wpml_get_original_posts( array(
            'posts_per_page' => - 1,
            'post_type'      => $post_types,
        ) );
        $options1 = [];
        foreach ( $posts as $post ) :
            $post_title = $post->post_title ? $post->post_title : $post->post_name;
            $post_id    = $post->ID;
            $options1[$post_id] = $post_title;
        endforeach;
        $args['terms_target_page'] = $this->render_select('ct-ultimate-gdpr-terms', 'terms_target_page', $options1);
        // END terms_target_page

        $args['terms_target_custom'] = $this->render_input('ct-ultimate-gdpr-terms', 'terms_target_custom');

        /**
         * ct-ultimate-gdpr-policy
         */

        $args['policy_require_administrator'] = $this->render_checkbox('ct-ultimate-gdpr-policy', 'policy_require_administrator');
        $args['policy_require_users'] = $this->render_checkbox('ct-ultimate-gdpr-policy', 'policy_require_users');
        $args['policy_require_guests'] = $this->render_checkbox('ct-ultimate-gdpr-policy', 'policy_require_guests');

        // START policy_target_page
        $options3temp = [];
        $options3temp[0] = esc_html__( "Don't redirect", 'ct-ultimate-gdpr' );
        $options3temp['wp'] = esc_html__( 'WordPress Privacy page', 'ct-ultimate-gdpr' );

        $options3 = $options3temp + $options1;
        $args['policy_target_page'] = $this->render_select('ct-ultimate-gdpr-policy', 'policy_target_page', $options3);
        // END policy_target_page

        $args['policy_target_custom'] = $this->render_input('ct-ultimate-gdpr-policy', 'policy_target_custom');

        return $args;
    }

    private function step7_data(){
        $args = [];

        $args['choose_plugin'] = array(
            esc_html__("Select"),
            'Addthis',
            'ARForms',
            'bbPress',
            'BuddyPress',
            'Caldera Forms',
            'Contact Form CFDB7',
            'WPForms Lite',
            'Contact Form 7',
            'Easy Forms for Mailchimp',
            'Waitlist for WooCommerce - Back In Stock Notifier by CreateIT',
            'eForm - WordPress Form Builder',
            'Events Manager',
            'Flamingo',
            'Formcraft',
            'Formidable Forms',
            'Gravity Forms',
            'Klaviyo',
            'Mailchimp',
            'Mailerlite',
            'Mailster',
            'Metorik Helper',
            'Newsletter',
            'Ninja-Forms',
            'Quform',
            'Ultimate Member',
            'WooCommerce',
            'Wordfence',
            'WP Comments',
            'wpForo',
            'WP Job Manager',
            'WordPress Posts',
            'WP User Data',
            'YITH Woocommerce Wishlist',
            'Youtube',
            'Akismet Anti-Spam'
        );

        $args['services_addthis_block_cookies'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_addthis_block_cookies');
        $args['services_arforms_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_arforms_consent_field');
        $args['services_bbpress_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_bbpress_consent_field');
        $args['services_buddypress_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_buddypress_consent_field');
        $args['services_caldera_forms_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_caldera_forms_consent_field');
        $args['services_cf7db_hide_from_forgetme_form'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_cf7db_hide_from_forgetme_form');
        $args['services_wpforms_lite_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wpforms_lite_consent_field');
        $args['services_contact_form_7_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_contact_form_7_consent_field');
        $args['services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field');
        $args['services_ct_waitlist_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_ct_waitlist_consent_field');
        $args['services_eform_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_eform_consent_field');
        $args['services_events_manager_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_events_manager_consent_field');
        $args['services_flamingo_hide_from_forgetme_form'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_flamingo_hide_from_forgetme_form');
        $args['services_formcraft_form_premium_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_formcraft_form_premium_consent_field');
        $args['services_formcraft_form_builder_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_formcraft_form_builder_consent_field');
        $args['services_formidable_forms_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_formidable_forms_consent_field');
        $args['services_gravity_forms_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_gravity_forms_consent_field');
        $args['services_klaviyo_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_klaviyo_consent_field');
        $args['services_mailchimp_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_mailchimp_consent_field');
        $args['services_mailerlite_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_mailerlite_consent_field');
        $args['services_mailster_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_mailster_consent_field');
        $args['services_metorik_helper_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_metorik_helper_consent_field');
        $args['services_newsletter_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_newsletter_consent_field');
        $args['services_ninja_forms_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_ninja_forms_consent_field');
        $args['services_quform_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_quform_consent_field');
        $args['services_ultimate_member_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_ultimate_member_consent_field');
        $args['services_woocommerce_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_woocommerce_consent_field');
        $args['services_woocommerce_edit_account_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_woocommerce_edit_account_consent_field');
        $args['services_woocommerce_checkout_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_woocommerce_checkout_consent_field');
        $args['services_wordfence_block_cookies'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wordfence_block_cookies');
        $args['services_wp_comments_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_comments_consent_field');
        $args['services_wp_foro_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_foro_consent_field');
        $args['services_wp_job_manager_hide_from_forgetme_form'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_job_manager_hide_from_forgetme_form');
        $args['services_wp_posts_hide_from_forgetme_form'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_posts_hide_from_forgetme_form');
        $args['services_wp_comments_network_signup_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_comments_network_signup_consent_field');
        $args['services_wp_comments_register_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_comments_register_consent_field');
        $args['services_wp_comments_lost_password_consent_field'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_wp_comments_lost_password_consent_field');
        $args['services_yith_woocommerce_wishlist_hide_from_forgetme_form'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_yith_woocommerce_wishlist_hide_from_forgetme_form');
        $args['services_youtube_remove_iframe'] = $this->render_checkbox('ct-ultimate-gdpr-services', 'services_youtube_remove_iframe');

        return $args;
    }

    private function step8_data(){
        $args = [];
        $args['shortcodes'] = ct_ultimate_gdpr_wizard_shortcodes_list();
        return $args;
    }


    private function step8b_data(){
        $shortcode_index = isset($_GET['ctshortcode']) ? $_GET['ctshortcode'] : 0;

        $args = [];
        $args['iframe_url'] = $this->get_iframe_url($shortcode_index);

        return $args;
    }

    private function get_iframe_url($shortcode_index){
        return '/' . $this->preview_url . '?' . $this->preview_param . '=' . $shortcode_index . '&ctpass=1' . '&time=' . time();
    }

    private function render_input($group, $key, $required = false)
    {
        $inputValue = isset($this->default_values[$group][$key]) ? stripslashes($this->default_values[$group][$key]) : '';
        $requiredAttr = ($required) ? "required" : '';

        return '<input type="text" id="' . $key . '" name="' . $group . '[' . $key . ']" class="form-control" value="' . $inputValue . '" '. $requiredAttr .'>';
    }

    private function render_textarea($group, $key)
    {
        $defaultValue = isset($this->default_values[$group][$key]) ? stripslashes($this->default_values[$group][$key]) : '';

        return '<textarea class="form-control" rows="6" autocomplete="off" id="' . $key . '" name="' . $group . '[' . $key . ']">' . $defaultValue . '</textarea>';
    }

    private function render_select($group, $key, $options){
        $selectedVal = isset($this->default_values[$group][$key]) ? $this->default_values[$group][$key] : '';

        $html = '';
        $html .= '<select class="form-control" id="' . $key . '" name="' . $group . '[' . $key . ']">';
        $html .= ($selectedVal == '') ? '<option value=""></option>' : '';
        foreach ($options as $key => $opt){
            $selectedOpt = '';
            if($selectedVal == $key){
                $selectedOpt = 'selected="selected"';
            }
            $html .= '<option value="'. $key .'" '. $selectedOpt .'>'. __($opt, 'ct-ultimate-gdpr') .'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    private function render_checkbox($group, $key){
        $checkedVal = isset($this->default_values[$group][$key]) ? $this->default_values[$group][$key] : '';

        $checkedAttr = "";
        if($checkedVal != ''){
            $checkedAttr = "checked";
        }
        $html = '';

        $html .= '
        <input type="hidden" name="' . $group . '[' . $key . ']" value="">
        <input class="form-check-input" type="checkbox" value="on" id="' . $key . '" name="' . $group . '[' . $key . ']" '. $checkedAttr .'>';

        return $html;
    }


    /**
     * Add menu page (if not added in admin controller)
     */
    public function add_menu_page()
    {

        add_submenu_page(
            null,
            esc_html__('Wizard', 'ct-ultimate-gdpr'),
            esc_html__('Wizard', 'ct-ultimate-gdpr'),
            'manage_options',
            $this->get_id(),
            array(&$this, 'load_view')
        );

        add_submenu_page(
            'ct-ultimate-gdpr',
            esc_html__('Wizard', 'ct-ultimate-gdpr'),
            esc_html__('Wizard', 'ct-ultimate-gdpr'),
            'manage_options',
            $this->get_id() . '&step=welcome',
            array(&$this, 'load_view')
        );

    }


}

// helpers

function ct_ultimate_gdpr_wizard_is_step($step)
{
    $current_page = ct_ultimate_gdpr_wizard_current_step();

    if (strpos($current_page, $step) !== false) {
        return true;
    }

    return false;
}

function ct_ultimate_gdpr_wizard_current_step()
{
    return isset($_GET['step']) ? $_GET['step'] : 0;
}

function ct_ultimate_gdpr_wizard_step_url($step)
{
    return admin_url('admin.php?page=ct-ultimate-gdpr-wizard&step=' . $step);
}

function ct_ultimate_gdpr_wizard_get_levels(){
    return array(
        CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL,
        CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY,
        CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE,
        CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS,
        CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING,
    );
}
function ct_ultimate_gdpr_wizard_prev_step(){
    $current_page = ct_ultimate_gdpr_wizard_current_step();
    $current_number = (int) filter_var($current_page, FILTER_SANITIZE_NUMBER_INT);
    $prev_number = $current_number-1;
    $prev_page = 'step'. $prev_number;
    if($prev_number === 1){
        $prev_page = 'step1a';
    }
    return ct_ultimate_gdpr_wizard_step_url($prev_page);
}
function ct_ultimate_gdpr_wizard_submit($submit_text, $hide_class = "sr-only"){ ?>
    <div class="form__submit <?php echo $hide_class ?>">
        <p class="submit">
            <input type="submit" name="submit5" id="submit5" class="button" value="<?php echo $submit_text; ?>">
        </p>
    </div>
<?php }

function ct_ultimate_gdpr_wizard_preview_url($path){ ?>
    <a href="<?php echo ct_ultimate_gdpr_wizard_step_url($path); ?>" class="ml-3"><?php echo esc_html__( 'Preview', 'ct-ultimate-gdpr' ); ?></a>
<?php
}
function ct_ultimate_gdpr_wizard_shortcodes_list(){
    return array(
        0 => '',
        1 => '[ultimate_gdpr_myaccount]',
        2 => '[ultimate_gdpr_policy_accept]',
        3 => '[ultimate_gdpr_terms_accept]',
        4 => '[ultimate_gdpr_cookie_popup]Link [/ultimate_gdpr_cookie_popup]',
        5 => '[render_cookies_list]',
        6 => '[ultimate_gdpr_protection level=4] content [/ultimate_gdpr_protection]',
        7 => '[ultimate_gdpr_center myaccount_page=15 contact_page=18 icon_color=#e03131]',
    );

}
