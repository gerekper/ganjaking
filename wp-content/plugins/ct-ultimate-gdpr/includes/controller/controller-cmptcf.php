<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Cmptcf
 */
class CT_Ultimate_GDPR_Controller_Cmptcf extends CT_Ultimate_GDPR_Controller_Abstract
{

    /**
     *
     */
    const ID = 'ct-ultimate-gdpr-cmptcf';


    /**
     * Get unique controller id (page name, option id)
     */
    public function get_id()
    {
        return self::ID;
    }

    /**
     * Init after construct
     */
    public function init()
    {
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'), 1);

        add_action('wp_head', array($this, 'add_custom_klaro_script'), 1);

        add_action('rest_api_init', array($this, 'klaro_config_init'));

    }

    private function tcf_enabled(){
        $option_value = get_option('ct-ultimate-gdpr-cmptcf');
        if (isset($option_value['cmptcf_enable_tcf']) && $option_value['cmptcf_enable_tcf'] === 'on') {
            return true;
        }

        return false;
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

        /* Section */

        add_settings_section(
            $this->get_id(), // ID
            esc_html__('TCF Compliance', 'ct-ultimate-gdpr'), // Title
            array($this, 'section_description_callback'),
            $this->get_id() // Page
        );

        add_settings_field(
            'cmptcf_enable_tcf', // ID
            esc_html__('Enable TCF 2.2 Integration', 'ct-ultimate-gdpr'), // Title
            array($this, 'render_field_cmptcf_enable_tcf'), // Callback
            $this->get_id(), // Page
            $this->get_id() // Section
        );

    }

    public function section_description_callback()
    {
        echo esc_html__('Activate the TCF 2.2 compliant Ad Choices modal. This allows users to select their advertising preferences and view a comprehensive list of all advertising partners. Enabling this ensures better transparency and compliance with the latest data protection guidelines.', 'ct-ultimate-gdpr');
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


    public function frontend_enqueue_scripts($hook_suffix)
    {
        /**
         * @TODO - optimiza loading places
         */
        if ($this->tcf_enabled()) {
            wp_enqueue_script('ct-ultimate-gdpr-tcfcmp', ct_ultimate_gdpr_url('assets/tcf/dist/bundle.js'),
                array('jquery'),
                ct_ultimate_gdpr_get_plugin_version().'_6',
                false
            );
        }
    }

    public function add_custom_klaro_script()
    {
        if ($this->tcf_enabled()) {
            $base_url = home_url();
            echo '<script src="' . esc_url($base_url . '/wp-json/custom/v1/generate-klaro-config') . '?v=3"></script>';
        }
    }

    public function klaro_config_init()
    {
        register_rest_route('custom/v1', '/generate-klaro-config', array(
            'methods' => 'GET',
            'callback' => array($this, 'generate_klaro_config'),
            'permission_callback' => function () {
                return true; // You can add authentication here if needed
            },
        ));
    }

    public function generate_klaro_config(WP_REST_Request $request)
    {
        // Load the static klaroConfig from the local JSON file
        $staticConfig = json_decode(file_get_contents(ct_ultimate_gdpr_url('assets/tcf/config.json')), true);

        // Fetch dynamic data (for example from a database, or another file)
        $data = json_decode(file_get_contents(ct_ultimate_gdpr_url('assets/tcf/vendor-list.json')), true);


        // $data['vendors'] = array_slice($data['vendors'],2,39);

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
                    $description .= '<p>Privacy policy: <a href="' . $privacyPolicy . '" target="_blank">' . $privacyPolicy . '</a></p>';
                }
                if ($legIntClaim) {
                    $description .= '<p> Legitimate Interest claim: <a href="' . $legIntClaim . '" target="_blank">' . $legIntClaim . '</a></p>';
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

        // Overwrite the services node in the staticConfig
        $staticConfig['services'] = $services;


        $jsConfig = 'var klaroConfig = ' . json_encode($staticConfig) . ';';

        // Set content type to JavaScript
        header('Content-Type: text/javascript');

        // Return the JS string
        echo $jsConfig;
        exit;


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


}
