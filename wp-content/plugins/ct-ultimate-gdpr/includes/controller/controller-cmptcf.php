<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Cmptcf
 */
class CT_Ultimate_GDPR_Controller_Cmptcf extends CT_Ultimate_GDPR_Controller_Abstract
{

    const ID = 'ct-ultimate-gdpr-cmptcf';

    private $cmptcf_options;

    private $tcf_version = '32';

    private $vl_key = 'ct_ultimate_gdpr-tcfvendorlist';

    private $default_options;

    public function get_id()
    {
        return self::ID;
    }

    /**
     * Init after construct
     */
    public function init()
    {
        $this->cmptcf_options = get_option('ct-ultimate-gdpr-cmptcf',  $this->default_options);
        $this->cmptcf_options = wp_parse_args($this->cmptcf_options, $this->default_options);

        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'), 1);

        add_action('wp_head', array($this, 'add_custom_klaro_script'), 1);

        add_action('rest_api_init', array($this, 'klaro_config_init'));

        add_action('rest_api_init', array($this, 'vendor_list_init'));

        add_action('admin_init', array($this, 'update_vendor_list'));

        add_action('ctgdpr_update_vendor_list', array($this,'fetch_vendor_list'));

        add_action('wp_ajax_ctgdpr_update_vendor_list', array($this,'ajax_ctgdpr_update_vendor_list'));

        add_action( 'wp_ajax_ct_ultimate_gdpr_consent_give_tcf', array( $this, 'give_consent_tcf' ) );
        add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_consent_give_tcf', array( $this, 'give_consent_tcf' ) );


        if($this->tcf_enabled()){
            add_filter( 'ct_ultimate_gdpr_controller_should_render', array( $this, 'gdpr_popup_should_render' ) );
        }

    }

    private function tcf_enabled() {
        return isset($this->cmptcf_options['cmptcf_enable_tcf']) && $this->cmptcf_options['cmptcf_enable_tcf'] === 'on';
    }

    private function is_defer_enabled() {
        return isset($this->cmptcf_options['cmptcf_defer_script_loading']) && $this->cmptcf_options['cmptcf_defer_script_loading'] === 'on';
    }

    private function is_debug_mode() {
        return isset($this->cmptcf_options['cmptcf_debug_mode']) && $this->cmptcf_options['cmptcf_debug_mode'] === 'on';
    }
    private function get_privacy_url() {
        return isset($this->cmptcf_options['cmptcf_privacy_url']) ? $this->cmptcf_options['cmptcf_privacy_url'] : '';
    }
    private function is_consent_logging_disabled() {
        return isset($this->cmptcf_options['cmptcf_disable_tc_string_logging']) && $this->cmptcf_options['cmptcf_disable_tc_string_logging'] === 'on';
    }
    private function is_consent_mode() {
        return isset($this->cmptcf_options['cmptcf_consent_mode']) && $this->cmptcf_options['cmptcf_consent_mode'] === 'on';
    }
    private function get_filtered_vendors() {
        if (isset($this->cmptcf_options['cmptcf_vendors_ids']) && $this->cmptcf_options['cmptcf_vendors_ids'] != '') {
            // Explode the string into an array
            $vendorIdsArray = explode(',', $this->cmptcf_options['cmptcf_vendors_ids']);

            // Trim spaces from each element in the array
            $trimmedVendorIds = array_map('trim', $vendorIdsArray);

            return $trimmedVendorIds;
        } else {
            return array();
        }
    }
    private function get_tab2_description() {
        return isset($this->cmptcf_options['cmptcf_tab2_description']) ? $this->cmptcf_options['cmptcf_tab2_description'] : '';
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
        return 'admin/admin-cmptcf';
    }

    /**
     * Add menu page (if not added in admin controller)
     */
    public function add_menu_page()
    {
        add_submenu_page(
            CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
            esc_html__('TCF Compliance', 'ct-ultimate-gdpr'),
            esc_html__('TCF Compliance', 'ct-ultimate-gdpr'),
            'manage_options',
            $this->get_id(),
            array($this, 'render_menu_page')
        );
    }

    /**
     * @return mixed
     */
    public function add_option_fields()
    {

        $section_1_id =  $this->get_id().'_col_1';
        $section_2_id =  $this->get_id().'_col_2';

        /* Section */

        add_settings_section(
            $section_1_id , // ID
            esc_html__('Ad Choices', 'ct-ultimate-gdpr'), // Title
            array($this, 'section1_description_callback'),
            $this->get_id() // Page
        );

        add_settings_section(
            $section_2_id, // ID
            esc_html__('Cookie Preferences', 'ct-ultimate-gdpr'), // Title
            array($this, 'section2_description_callback'),
            $this->get_id() // Page
        );


        add_settings_field(
            'cmptcf_enable_tcf', // ID
            esc_html__('Enable TCF 2.2 Integration', 'ct-ultimate-gdpr'), // Title
            array($this, 'render_field_cmptcf_enable_tcf'), // Callback
            $this->get_id(), // Page
            $section_1_id // Section
        );

        add_settings_field(
            'cmptcf_defer_script_loading',
            esc_html__('Enable Defer Loading', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_defer_script_loading'),
            $this->get_id(),
            $section_1_id
        );


        add_settings_field(
            'cmptcf_privacy_url',
            esc_html__('Privacy policy url', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_privacy_url'),
            $this->get_id(),
            $section_1_id
        );

        // Add settings field for manual update
        add_settings_field(
            'cmptcf_vl_manual_update',
            esc_html__('Update Vendor List', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_vl_manual_update'),
            $this->get_id(),
            $section_1_id
        );

        add_settings_field(
            'cmptcf_disable_tc_string_logging', // Unique identifier for the field to disable logging
            esc_html__('Disable user consent logging (TCString)', 'ct-ultimate-gdpr'), // Title
            array($this, 'render_field_cmptcf_disable_tc_string_logging'), // Callback to render the field
            $this->get_id(), // The page on which to display this field
            $section_1_id
        );





        add_settings_field(
            'cmptcf_vendors_ids', // Unique identifier for the field to disable logging
            esc_html__('Vendor ID Filter', 'ct-ultimate-gdpr'), // Title
            array($this, 'render_field_cmptcf_vendors_ids'), // Callback to render the field
            $this->get_id(), // The page on which to display this field
            $section_1_id // The section to which this field belongs
        );


        add_settings_field(
            'cmptcf_debug_mode',
            esc_html__('Enable debug mode', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_debug_mode'),
            $this->get_id(),
            $section_1_id
        );


        add_settings_field(
            'cmptcf_consent_mode', // Unique identifier for the field to disable logging
            esc_html__('Enable Google Consent Mode', 'ct-ultimate-gdpr'), // Title
            array($this, 'render_field_cmptcf_consent_mode'), // Callback to render the field
            $this->get_id(), // The page on which to display this field
            $section_2_id // The section to which this field belongs
        );

        add_settings_field(
            'cmptcf_tab2_description',
            esc_html__('Introduction text', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_tab2_description'),
            $this->get_id(),
            $section_2_id
        );


        add_settings_field(
            'cmptcf_necessary_cookies',
            esc_html__('Necessary Cookies option visible', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_necessary_cookies'),
            $this->get_id(),
            $section_2_id
        );
        add_settings_field(
            'cmptcf_functional_cookies',
            esc_html__('Functional Cookies option visible', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_functional_cookies'),
            $this->get_id(),
            $section_2_id
        );
        add_settings_field(
            'cmptcf_analytics_cookies',
            esc_html__('Analytics Cookies option visible', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_analytics_cookies'),
            $this->get_id(),
            $section_2_id
        );
        add_settings_field(
            'cmptcf_advertising_cookies',
            esc_html__('Advertising Cookies option visible', 'ct-ultimate-gdpr'),
            array($this, 'render_field_cmptcf_advertising_cookies'),
            $this->get_id(),
            $section_2_id
        );
    }


    public function render_field_cmptcf_necessary_cookies() {
        $this->render_cookie_option_field('necessary_cookies', 'cmptcf_necessary_cookies');
    }
    public function render_field_cmptcf_advertising_cookies() {
        $this->render_cookie_option_field('advertising_cookies', 'cmptcf_advertising_cookies');
    }
    public function render_field_cmptcf_functional_cookies() {
        $this->render_cookie_option_field('functional_cookies', 'cmptcf_functional_cookies');
    }
    public function render_field_cmptcf_analytics_cookies() {
        $this->render_cookie_option_field('analytics_cookies', 'cmptcf_analytics_cookies');
    }

    public function render_field_cmptcf_disable_tc_string_logging(){
        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

        // Description
        echo '<p class="description">';
        esc_html_e('User consent is automatically logged for compliance and auditing purposes. If you do not wish to save these records, make option Enabled to disable logging.', 'ct-ultimate-gdpr');
        echo '</p>';
    }


    private function get_vl_last_update() {
        $text = '<span id="vl_updated_time">';
        // Retrieve the last update time from the database.
        $last_update = get_option($this->vl_key.'_last_updated');

        // Retrieve the vendor list data from the database.
        $vendor_list = get_option($this->vl_key);

        // Initialize a variable for the "recently_updated" date.
        $recently_updated = '';

        // Check if the vendor list is an array and contains the "recently_updated" key.
        if (is_array($vendor_list) && isset($vendor_list['lastUpdated'])) {
            $recently_updated = $vendor_list['lastUpdated'];
        }

        // Display the last update time.
        $text .= 'Last fetched: ' . esc_html($last_update) . '<br>';

        // Display the "recently_updated" date.
        $text .= 'Vendor List version: ' . esc_html($recently_updated);

        $text .= '</span>';

        return $text;
    }


    public function render_field_cmptcf_vl_manual_update(){
        // The nonce field for security
        wp_nonce_field('ct-ultimate-gdpr_vl_manual_update', 'ct-ultimate-gdpr_vl_manual_update');
        echo '<button id="ct-ultimate-gdpr_update_button" class="button button-primary">'.esc_html__('Update Now', 'ct-ultimate-gdpr').'</button>';
         echo "
         <script>
         jQuery(document).ready(function($) {
            $('#ct-ultimate-gdpr_update_button').on('click', function(e) {
                e.preventDefault();
                 var updateButton = $(this);
                 
                var data = {
                    'action': 'ctgdpr_update_vendor_list', // This should match the action hook for the AJAX call
                    '_nonce': $('#ct-ultimate-gdpr_vl_manual_update').val() // The nonce field passed for security
                };
                
                updateButton.prop('disabled', true).text('". esc_js(__('Updating...', 'ct-ultimate-gdpr')) . "');

        
                $.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        alert('Vendor list updated');
                        $('#vl_updated_time').fadeOut(function() {
                            $(this).html(response.data.update_times_html).fadeIn();
                        });
                        
                        updateButton.text('" . esc_js(__('Update Now', 'ct-ultimate-gdpr')) ."');
                        updateButton.prop('disabled', false);
                    } else {
                        updateButton.text('". esc_js(__('Update Failed, Try Again', 'ct-ultimate-gdpr')) ."');
                    
                    }
                }).fail(function() {
                    updateButton.text('". esc_js(__('Update Failed, Try Again', 'ct-ultimate-gdpr')) ."');
                });
            });
        });
         </script>                  
";

        echo '<p class="description">';
        echo 'The vendor list is <strong>automatically updated weekly</strong>. Use the button above to manually update if immediate changes are required.';
        echo '<br><br>';
        echo $this->get_vl_last_update();
        echo '</p>';


    }

    public function ajax_ctgdpr_update_vendor_list(){
        check_ajax_referer('ct-ultimate-gdpr_vl_manual_update', '_nonce');

        $success = $this->fetch_vendor_list();

        if ($success) {
            $update_times_html = $this->get_vl_last_update();

            wp_send_json_success(array(
                'update_times_html' => $update_times_html
            ));
        } else {
            wp_send_json_error('Failed to update vendor list.');
        }
    }

    public function section1_description_callback()
    {
        echo '<h6>'. esc_html__('Activate the TCF 2.2 compliant Ad Choices modal. This allows users to select their advertising preferences and view a comprehensive list of all advertising partners (vendors).', 'ct-ultimate-gdpr') . '</h6>';
    }
    public function section2_description_callback()
    {
        echo '<h6>'. esc_html__('Manage your cookie settings in the Cookie Preferences tab, designed for Google Consent Mode integration.', 'ct-ultimate-gdpr'). '</h6>';
    }

    public function render_field_cmptcf_enable_tcf()
    {
        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

    }

    public function render_field_cmptcf_defer_script_loading(){
        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );
    }

    public function render_field_cmptcf_debug_mode(){
        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );
        // Description
        echo '<p class="description">';
        esc_html_e('Enabling this will display the TC string in browser’s developer tools console for debugging purposes.', 'ct-ultimate-gdpr');
        echo '</p>';
    }

    public function render_field_cmptcf_privacy_url() {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, '')
        );
        // Description
        echo '<p class="description">';
        esc_html_e('ex: https://www.example.com/privacy/', 'ct-ultimate-gdpr');
        echo '</p>';
    }


    public function render_field_cmptcf_consent_mode(){
        $admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name(__FUNCTION__);

        printf(
            "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name(__FUNCTION__),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

        // Description
        echo '<p class="description">';
        esc_html_e("This setting activates Google's consent mode functionalities, allowing you to manage how Google services behave based on the consent choices made by your users.", "ct-ultimate-gdpr");
        echo '</p>';
    }



    public function render_field_cmptcf_vendors_ids() {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<input type='text' id='%s' name='%s' value='%s' />",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, '')
        );

        // Description
        echo '<p class="description">';
        esc_html_e("Enter a list of vendor IDs, separated by commas, to selectively display only these vendors in the TCF modal.", "ct-ultimate-gdpr");
        echo '</p>';

    }
    public function render_field_cmptcf_tab2_description() {

        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $admin->get_field_name( __FUNCTION__ );
        printf(
            "<textarea id='%s_desc' name='%s_desc'>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, '')
        );

        // Description
        echo '<p class="description">';
        esc_html_e("Custom text that will show up in the Tab", "ct-ultimate-gdpr");
        echo '</p>';

    }


    private function render_cookie_option_field($cookie_type, $function_suffix) {
        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
        $field_name = $function_suffix;

        // Checkbox
        printf(
            "<input type='checkbox' id='%s' name='%s' %s />",
            $admin->get_field_name($function_suffix),
            $admin->get_field_name_prefixed($field_name),
            $admin->get_option_value_escaped($field_name) ? 'checked' : ''
        );

        // TextArea for description
        printf(
            "<textarea id='%s_desc' name='%s_desc'>%s</textarea>",
            $admin->get_field_name($function_suffix),
            $admin->get_field_name_prefixed($field_name . '_desc'),
            $admin->get_option_value_escaped($field_name . '_desc', '')
        );

        echo '<p class="description">';
        esc_html_e('Custom Description for ' . str_replace('_', ' ', ucfirst($cookie_type)) . '.', 'ct-ultimate-gdpr');
        echo '</p>';
    }


    /**
     * TCF functionality
     */

    public function frontend_enqueue_scripts($hook_suffix)
    {
        /**
         * @TODO - optimiza loading places
         */
        if ($this->tcf_enabled()) {
            wp_enqueue_script('ct-ultimate-gdpr-tcfcmp', ct_ultimate_gdpr_url('assets/tcf/dist/bundle.js'),
                array(),
                ct_ultimate_gdpr_get_plugin_version().'_' . $this->tcf_version,
                false
            );

            if($this->is_defer_enabled()){
                add_filter('script_loader_tag', function($tag, $handle) {
                    if ('ct-ultimate-gdpr-tcfcmp' !== $handle) {
                        return $tag;
                    }
                    return str_replace(' src', ' defer="defer" src', $tag);
                }, 10, 2);
            }
            $base_url = home_url();

            // Localize the script with new data
            $translation_array = array(
                'GVL_BASE_URL' => esc_url($base_url . '/wp-json/custom/v1'),
                'GVL_LATEST_FILENAME' => 'get-vendor-list?v='. $this->tcf_version,
                'DEBUG_MODE' => $this->is_debug_mode(),
                'CONSENT_MODE' => $this->is_consent_mode(),
                'HIDE_TAB_2' => $this->should_hide_tab_2()
            );


            wp_localize_script('ct-ultimate-gdpr-tcfcmp', 'ct_ultimate_gdpr_tcf', $translation_array);
        }
    }

    public function add_custom_klaro_script()
    {
        if ($this->tcf_enabled()) {
            $base_url = home_url();
            $nonce = wp_create_nonce('ct_ultimate_gdpr_tcf_nonce');

            echo '<script src="' . esc_url($base_url . '/wp-json/custom/v1/generate-tcf-config') . '?v='. $this->tcf_version .'"></script>';
            if(! $this->is_consent_logging_disabled()) {
                echo "
<script>
document.addEventListener('ctTCModelUpdated', function(e) {
    sendTCStringToServer(e.detail.encodedTCString);
});
function sendTCStringToServer(encodedTCString){
    jQuery.ajax({
        url: ct_ultimate_gdpr_cookie.ajaxurl,
        type: 'POST',
        data: {
            action: 'ct_ultimate_gdpr_consent_give_tcf',
            data: encodedTCString,
            nonce: '{$nonce}'
        },
    });
}
</script>
";
            }
        }
    }

    public function klaro_config_init()
    {
        register_rest_route('custom/v1', '/generate-tcf-config', array(
            'methods' => 'GET',
            'callback' => array($this, 'generate_klaro_config'),
            'permission_callback' => function () {
                return true; // You can add authentication here if needed
            },
        ));
    }

    public function vendor_list_init()
    {
        register_rest_route('custom/v1', '/get-vendor-list', array(
            'methods' => 'GET',
            'callback' => array($this, 'vendor_list_config'),
            'permission_callback' => function () {
                return true; // You can add authentication here if needed
            },
        ));
    }



    private function filter_vendors($allVendors) {

        // Get the list of vendor IDs to keep
        $vendorIdsToKeep = $this->get_filtered_vendors();

        if(empty($vendorIdsToKeep)){
            return $allVendors;
        }

        // Filter the array to keep only vendors with IDs in $vendorIdsToKeep
        $filteredVendors = array_filter($allVendors, function($vendor) use ($vendorIdsToKeep) {
            return in_array($vendor['id'], $vendorIdsToKeep);
        });

        return $filteredVendors;
    }

    public function vendor_list_config(WP_REST_Request $request) {
        $data = $this->get_vendor_list();

        if (empty($data)) {
            // Return an error if the data is not available
            return new WP_Error('no_data', 'No vendor data found', array('status' => 404));
        }

        // Return the data as a REST response
        return new WP_REST_Response($data, 200);
    }

    public function generate_klaro_config(WP_REST_Request $request)
    {

        // Load the static klaroConfig from the local JSON file
        $staticConfig = json_decode(file_get_contents(ct_ultimate_gdpr_url('assets/tcf/config.json')), true);

        // Fetch dynamic data (for example from a database, or another file)
        $data = $this->get_vendor_list();

        if(! $data){
            return;
        }

        $data['vendors'] = $this->filter_vendors($data['vendors']);


        // tab2 description

        $tab2_desc = $this->get_tab2_description();
        if($tab2_desc){
            $staticConfig["translations"]["en"]["tab2"]["description"] =  $tab2_desc;
        }


        // prepare data for partners description
        // Convert the 'purposes', 'specialPurposes', and 'features' arrays into associative arrays
        $purposesMapping = [];
        foreach ($data['purposes'] as $purpose) {
            $purposesMapping[$purpose['id']] = $purpose['name'];
        }

        $specialPurposesMapping = [];
        foreach ($data['specialPurposes'] as $specialPurpose) {
            $specialPurposesMapping[$specialPurpose['id']] = $specialPurpose['name'];
        }

        $featuresMapping = [];
        foreach ($data['features'] as $feature) {
            $featuresMapping[$feature['id']] = $feature['name'];
        }

        $dataDeclarationMapping = [];
        foreach ($data['dataCategories'] as $dataCategory) {
            $dataDeclarationMapping[$dataCategory['id']] = [
                'name' => $dataCategory['name'],
                'description' => $dataCategory['description']
            ];
        }

        $vendorsPurposeCount = [];

        foreach ($data['vendors'] as $vendor) {
            foreach ($vendor['purposes'] as $purposeId) {
                if (!isset($vendorsPurposeCount[$purposeId])) {
                    $vendorsPurposeCount[$purposeId] = 0;
                }
                $vendorsPurposeCount[$purposeId]++;
            }
        }
        $vendorCount = count($data['vendors']);

        // Modify the description by replacing [partners_count] with the actual count
        $staticConfig["translations"]["en"]["consentModal"]["description"] = str_replace('[partners_count]', "($vendorCount)", $staticConfig["translations"]["en"]["consentModal"]["description"]);

        // Generate the dynamic services node
        $services = array_merge(
            array_map(function ($p) use ($vendorsPurposeCount) {
                $illustrationsList = '';
                if (isset($p['illustrations']) && is_array($p['illustrations'])) {
                    $illustrationsList .= '<div class="toggle-section">';
                    $illustrationsList .= '<p class="toggle-title">Examples ▼</p>';
                    $illustrationsList .= '<ul class="toggle-content" style="display:none;">';
                    foreach ($p['illustrations'] as $illustration) {
                        $illustrationsList .= '<li>' . $illustration . '</li>';
                    }
                    $illustrationsList .= '</ul></div>';
                }
                $vendorCount = isset($vendorsPurposeCount[$p['id']]) ? ' (' . $vendorsPurposeCount[$p['id']] . ' partners)' : '';

                return array(
                    'name' => 'pu_' . $p['id'],
                    'title' => $p['name'] . $vendorCount,
                    'description' => $p['description'] . $illustrationsList,
                    'purposes' => array('purposes'),
                    'required' => false
                );
            }, $data['purposes']),
            array_map(function ($p) {
                return array(
                    'name' => 'spfu_' . $p['id'],
                    'title' => $p['name'],
                    'description' => $p['description'],
                    'purposes' => array('special_features'),
                    'required' => false
                );
            }, $data['specialFeatures']),
            array_map(function ($p) use ($featuresMapping, $specialPurposesMapping, $purposesMapping, $dataDeclarationMapping) {

                $pName = $p['name'];
                $privacyPolicy = $p['urls'][0]['privacy'];
                $legIntClaim = isset($p['urls'][0]['legIntClaim']) ? $p['urls'][0]['legIntClaim'] : null;

                // Map purposes to their descriptions
                $purposesConsent = array_map(function ($purposeId) use ($purposesMapping) {
                    return $purposesMapping[$purposeId];
                }, $p['purposes']);

                $purposesLegitimate = array_map(function ($purposeId) use ($purposesMapping) {
                    return $purposesMapping[$purposeId];
                }, $p['legIntPurposes']);

                $specialPurposes = array_map(function ($specialPurposeId) use ($specialPurposesMapping) {
                    return $specialPurposesMapping[$specialPurposeId];
                }, $p['specialPurposes']);

                $features = array_map(function ($featureId) use ($featuresMapping) {
                    return $featuresMapping[$featureId];
                }, $p['features']);


                if (isset($p['dataDeclaration']) && is_array($p['dataDeclaration'])) {
                    $dataDeclarations = array_map(function ($dataDeclarationId) use ($dataDeclarationMapping) {
                        return $dataDeclarationMapping[$dataDeclarationId];
                    }, $p['dataDeclaration']);
                }

                $seconds = isset($p['cookieMaxAgeSeconds']) ? $p['cookieMaxAgeSeconds'] : null;
                $consentExpiry = null;

                if ($seconds !== null) {
                    $minutes = floor($seconds / 60);
                    $hours = floor($minutes / 60);
                    $days = floor($hours / 24);

                    if ($days >= 1) {
                        $consentExpiry = "$days days";
                    } else {
                        $consentExpiry = "$minutes minutes";
                    }
                }

                $description = "";

                if ($privacyPolicy) {
                    $description .= '<p><a href="' . $privacyPolicy . '" target="_blank">Privacy policy</a></p>';
                }
                if ($legIntClaim) {
                    $description .= '<p><a href="' . $legIntClaim . '" target="_blank">Legitimate Interest claim</a></p>';
                }


                // Purposes (Consent)
                if (!empty($purposesConsent)) {
                    $description .= '<div class="toggle-section">';
                    $description .= '<p class="toggle-title">Purposes (Consent) ▼</p>';
                    $description .= '<ul class="toggle-content" style="display:none;">';
                    foreach ($purposesConsent as $purpose) {
                        $description .= '<li>' . htmlspecialchars($purpose) . '</li>';
                    }
                    $description .= '</ul></div>';
                }

                // Purposes (Legitimate Interest)
                if (!empty($purposesLegitimate)) {
                    $description .= '<div class="toggle-section">';
                    $description .= '<p class="toggle-title">Purposes (Legitimate Interest) ▼</p>';
                    $description .= '<ul class="toggle-content" style="display:none;">';
                    foreach ($purposesLegitimate as $purpose) {
                        $description .= '<li>' . htmlspecialchars($purpose) . '</li>';
                    }
                    $description .= '</ul></div>';
                }

                // Special Purposes
                if (!empty($specialPurposes)) {
                    $description .= '<div class="toggle-section">';
                    $description .= '<p class="toggle-title">Special Purposes ▼</p>';
                    $description .= '<ul class="toggle-content" style="display:none;">';
                    foreach ($specialPurposes as $purpose) {
                        $description .= '<li>' . htmlspecialchars($purpose) . '</li>';
                    }
                    $description .= '</ul></div>';
                }

                // Features
                if (!empty($features)) {
                    $description .= '<div class="toggle-section">';
                    $description .= '<p class="toggle-title">Features ▼</p>';
                    $description .= '<ul class="toggle-content" style="display:none;">';
                    foreach ($features as $feature) {
                        $description .= '<li>' . htmlspecialchars($feature) . '</li>';
                    }
                    $description .= '</ul></div>';
                }

                // Data Declaration
                if (!empty($dataDeclarations)) {
                    $description .= '<div class="toggle-section">';
                    $description .= '<p class="toggle-title">Data Declaration ▼</p>';
                    $description .= '<ul class="toggle-content" style="display:none;">';
                    foreach ($dataDeclarations as $dataDeclaration) {
                        $description .= '<li>' . htmlspecialchars($dataDeclaration['name']) . '</li>';
                    }
                    $description .= '</ul></div>';
                }

                // Consent details
                $yourConsentDetails = '';

                if ($consentExpiry) {
                    $yourConsentDetails .= '<p>Maximum cookie lifetime: ' . htmlspecialchars($consentExpiry) . '</p>';
                }

                if ($p['cookieRefresh']) {
                    $yourConsentDetails .= '<p>Cookie expiry may be refreshed during the lifetime.</p>';
                }

                if (isset($p['usesCookies']) && $p['usesCookies']) {
                    $yourConsentDetails .= '<p>Tracking method: Cookies' .
                        (isset($p['usesNonCookieAccess']) && $p['usesNonCookieAccess'] ? ' and others' : '') .
                        '</p>';
                } elseif (isset($p['usesNonCookieAccess']) && $p['usesNonCookieAccess']) {
                    $yourConsentDetails .= '<p>Tracking method: Others</p>';
                }

                // apply to main description string
                if ($yourConsentDetails) {
                    $description .= '<div class="toggle-section">';
                    $description .= '<p class="toggle-title">Device storage ▼</p>';
                    $description .= '<div class="toggle-content" style="display:none;">';
                    $description .= $yourConsentDetails;
                    $description .= '</div></div>';
                }


                $dataRetentionSection = "";

                if (isset($p['dataRetention'])) {
                    $retentionDetails = "";

                    if (isset($p['dataRetention']['stdRetention'])) {
                        $retentionDetails .= "<strong>Standard retention: </strong>";
                        $retentionDetails .= $this->formatRetention($p['dataRetention']['stdRetention']) . "<br>";
                    }

                    if (isset($p['dataRetention']['purposes']) && !empty($p['dataRetention']['purposes'])) {
                        $retentionDetails .= "<strong>Purposes:</strong><br>";
                        foreach ($p['dataRetention']['purposes'] as $purposeId => $days) {
                            $purposeName = $purposesMapping[$purposeId];
                            $retentionDetails .= "$purposeName: " . $this->formatRetention($days) . "<br>";
                        }
                    }

                    if (isset($p['dataRetention']['specialPurposes']) && !empty($p['dataRetention']['specialPurposes'])) {
                        $retentionDetails .= "<strong>Special purposes:</strong><br>";
                        foreach ($p['dataRetention']['specialPurposes'] as $specialPurposeId => $days) {
                            $specialPurposeName = $specialPurposesMapping[$specialPurposeId];
                            $retentionDetails .= "$specialPurposeName: " . $this->formatRetention($days) . "<br>";
                        }
                    }

                    if ($retentionDetails) {
                        $dataRetentionSection .= '<div class="toggle-section">';
                        $dataRetentionSection .= '<p class="toggle-title">Data retention ▼</p>';
                        $dataRetentionSection .= '<div class="toggle-content" style="display:none;">';
                        $dataRetentionSection .= $retentionDetails;
                        $dataRetentionSection .= '</div></div>';
                    }
                }

                // Append to the description
                $description .= $dataRetentionSection;


                $vendor_item = array(
                    'name' => 've_' . $p['id'],
                    'title' => $p['name'],
                    'description' => $description,
                    'purposes' => array('partners'),
                    'required' => false
                );

                if(!empty($purposesLegitimate)){
                    $vendor_item['purposesLegitimate'] = true;
                }

                return $vendor_item;

            }, $data['vendors'])
        );

        $purposesLegitimateVendors = array_filter($services, function($service) {
            return strpos($service['name'], 've_') === 0 && isset($service['purposesLegitimate']) && $service['purposesLegitimate'] === true;
        });

        $purposesLegitimateVendorsModified = array_map(function($service) {
            $service['name'] = str_replace('ve_', 'veli_', $service['name']);
            $service['purposes'] = ['li_partners'];
            $service['default'] = false;
            return $service;
        }, $purposesLegitimateVendors);

        $services = array_merge($services, $purposesLegitimateVendorsModified);

        /**
         * Add at the end
         *
         */
        $service1 = array_map(function ($p) {
            return array(
                'name' => 'fe_' . $p['id'],
                'title' => $p['name'],
                'description' => $p['description'],
                'purposes' => array('features'),
                'required' => true
            );
        }, $data['features']);

        $service2 = array_map(function ($p) {
            return array(
                'name' => 'sppu_' . $p['id'],
                'title' => $p['name'],
                'description' => $p['description'],
                'purposes' => array('special_purposes'),
                'required' => true
            );
        }, $data['specialPurposes']);

        $services = array_merge($services, $service1);
        $services = array_merge($services, $service2);

        $cookieCategories = $this->load_cookie_category_data();

        $cookieCategoriesList = array_values($cookieCategories);

        $services = array_merge($services, $cookieCategoriesList);

        // Overwrite the services node in the staticConfig
        $staticConfig['services'] = $services;

        // overwrite main privacy url
        $mainPrivacyUrl = $this->get_privacy_url();
        if($mainPrivacyUrl){
            $staticConfig['translations']['zz']['privacyPolicyUrl'] = $this->get_privacy_url();
        }

        $jsConfig = 'var klaroConfig = ' . json_encode($staticConfig) . ';';

        // Set content type to JavaScript
        header('Content-Type: text/javascript');

        // Return the JS string
        echo $jsConfig;
        exit;


    }

    private function load_cookie_category_data() {

        $cookieCategories = [
            'necessary' => [
                'name' => 'cookies_1',
                'title' => esc_html__( 'Necessary Cookies', 'ct-ultimate-gdpr' ),
                'description' => 'These are crucial for the basic operations of our website. They enable core functionalities such as security, network management, and accessibility. As they are essential for the website to work correctly, they cannot be turned off.',
                'purposes' => ['cookies'],
                'required' => true
            ],
            'functional' => [
                'name' => 'cookies_2',
                'title' => esc_html__( 'Functional Cookies', 'ct-ultimate-gdpr' ),
                'description' => 'These cookies enable additional features on our website for a more personalized experience. They remember your preferences and settings, like language or location, making your experience more convenient and tailored.',
                'purposes' => ['cookies'],
                'required' => false
            ],
            'analytics' => [
                'name' => 'cookies_3',
                'title' => esc_html__( 'Analytics Cookies', 'ct-ultimate-gdpr' ),
                'description' => "These cookies help us understand how visitors interact with our website. They collect information about your use of the site, which pages you visit, and how you navigate the site. This data is used to improve the website's functionality and user experience.",
                'purposes' => ['cookies'],
                'required' => false
            ],
            'advertising' => [
                'name' => 'cookies_4',
                'title' => esc_html__( 'Advertising Cookies', 'ct-ultimate-gdpr' ),
                'description' => 'These cookies are used to display relevant advertisements to you. They track your online activity to tailor advertising to your interests. By not allowing these cookies, the ads you see may be less relevant to you.',
                'purposes' => ['cookies'],
                'required' => false
            ]
        ];

        $options = $this->cmptcf_options;
        if ($options) {
            $options = maybe_unserialize($options);

            // Check each category and update or remove as necessary
            foreach ($cookieCategories as $key => &$category) {
                $optionEnabledKey = 'cmptcf_' . $key . '_cookies';
                $optionDescKey = 'cmptcf_' . $key . '_cookies_desc';

                // If 'enabled' state is not 'on', remove the element from the array
                if (!isset($options[$optionEnabledKey]) || $options[$optionEnabledKey] !== 'on') {
                    unset($cookieCategories[$key]);
                } else {
                    // Update description if available, otherwise keep original
                    if (!empty($options[$optionDescKey])) {
                        $category['description'] = $options[$optionDescKey];
                    }
                }
            }
            unset($category); // Unset reference to last element
        }

        return $cookieCategories;
    }

    private function should_hide_tab_2() {
        $cookieCategories = $this->load_cookie_category_data(); // Assuming this is your existing function

        if(empty($cookieCategories)){
            return true;
        }

        return false;
    }



    private function get_vendor_list() {
        // Check if the vendor list is stored in the database
        $vendor_list = get_option($this->vl_key);

        // If not, use the fallback static file
        if (!$vendor_list) {
            $file_path = ct_ultimate_gdpr_path('assets/tcf/vendor-list.json');
            if (file_exists($file_path)) {
                $vendor_list = json_decode(file_get_contents($file_path), true);
            }
        }

        return $vendor_list;
    }

    public function update_vendor_list(){

        if(! $this->tcf_enabled()){
            return;
        }

        if (!wp_next_scheduled('ctgdpr_update_vendor_list')) {
            wp_schedule_event(time(), 'weekly', 'ctgdpr_update_vendor_list');
        }
    }

    public function fetch_vendor_list() {
        $response = wp_remote_get('https://vendor-list.consensu.org/v3/vendor-list.json');

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return false;
        } elseif (is_array($response) && $response['response']['code'] == 200) {
            $body = $response['body'];
            $data = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                // Optionally, do additional checks to ensure $data contains what you expect.
                update_option($this->vl_key, $data,'no');
                update_option($this->vl_key.'_last_updated', current_time('mysql'),'no');
                return true;
            } else {
                // Handle the case where json_decode fails or does not return an array.
                return false;
            }
        } else {
            // Handle other kinds of HTTP errors.
            return false;
        }
    }

    public function give_consent_tcf(){

        check_ajax_referer('ct_ultimate_gdpr_tcf_nonce', 'nonce');

        $time = time();
        $data = ct_ultimate_gdpr_get_value('data', $this->get_request_array(), '' );

        if($data){
            $this->logger->consent( array(
                'type'       => 'tcf',
                'time'       => $time,
                'user_id'    => $this->user->get_current_user_id(),
                'user_ip'    => ct_ultimate_gdpr_get_permitted_user_ip(),
                'user_agent' => ct_ultimate_gdpr_get_permitted_user_agent(),
                'data'       => $data,
            ) );
        }
    }


    private function formatRetention($days) {

        if ($days === 0) {
            return 0;
        }

        $years = floor($days / 365);
        $remainingDays = $days % 365;

        $formatted = "";
        if ($years > 0) {
            $formatted .= "$years year" . ($years > 1 ? "s" : "");
        }

        if ($remainingDays > 0) {
            if ($formatted) {
                $formatted .= " - ";
            }
            $formatted .= "$remainingDays day" . ($remainingDays > 1 ? "s" : "");
        }

        return $formatted;
    }

    public function gdpr_popup_should_render(){
        return false;
    }

}
