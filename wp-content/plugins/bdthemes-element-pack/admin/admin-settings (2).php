<?php

use Elementor\Modules\Usage\Module;
use Elementor\Tracker;
use ElementPack\Admin\ModuleService;
use ElementPack\Base\Element_Pack_Base;
use ElementPack\Notices;
use ElementPack\Utils;

/**
 * Element Pack Admin Settings Class
 */

class ElementPack_Admin_Settings {

    public static $modules_list;
    public static $modules_names;

    public static $modules_list_only_widgets;
    public static $modules_names_only_widgets;

    public static $modules_list_only_3rdparty;
    public static $modules_names_only_3rdparty;

    const PAGE_ID = 'element_pack_options';

    private $settings_api;

    public $responseObj;
    public $licenseMessage;
    public $showMessage = false;
    private $is_activated = false;

    public function __construct() {

        $this->settings_api = new ElementPack_Settings_API;

        $license_key = self::get_license_key();
        $license_email = self::get_license_email();

        Element_Pack_Base::add_on_delete(
            function () {
                update_option('element_pack_license_email', '');
                update_option('element_pack_license_key', '');
                update_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'), '');
            }
        );

        if (!defined('BDTEP_HIDE')) {
            add_action('admin_init', [$this, 'admin_init']);
            add_action('admin_menu', [$this, 'admin_menu'], 201);
        }


        //add_action('admin_notices', [$this, 'free_v_require_notice'], 10, 3);

        if (function_exists('bdt_license_validation') && true == bdt_license_validation()) {
            // if (!Tracker::is_allow_track() && (isset($_GET['page']) && 'element_pack_options' != $_GET['page'])) {
            //     add_action('admin_notices', [$this, 'allow_tracker_activate_notice'], 10, 3);
            // }

            if (!Tracker::is_allow_track() && (isset($_GET['page']) && 'element_pack_options' == $_GET['page'])) {
                add_action('admin_notices', [$this, 'allow_tracker_dashboard_notice'], 10, 3);
            }
        }

        /**
         * Mini-Cart issue fixed
         * Check if MiniCart activate in EP and Elementor
         * If both is activated then Show Notice
         */

        $ep_3rdPartyOption = get_option('element_pack_third_party_widget');

        $el_use_mini_cart = get_option('elementor_use_mini_cart_template');

        if ($el_use_mini_cart !== false && $ep_3rdPartyOption !== false) {
            if ($ep_3rdPartyOption) {
                if ('yes' == $el_use_mini_cart && isset($ep_3rdPartyOption['wc-mini-cart']) && 'off' !== trim($ep_3rdPartyOption['wc-mini-cart'])) {
                    add_action('admin_notices', [$this, 'el_use_mini_cart'], 10, 3);
                }
            }
        }

        if (Element_Pack_Base::check_wp_plugin($license_key, $license_email, $error, $responseObj, BDTEP__FILE__)) {

            if (!defined('BDTEP_LO')) {
                add_action('admin_post_element_pack_deactivate_license', [$this, 'action_deactivate_license']);

                $this->is_activated = true;
            }
        } else {

            if (!defined('BDTEP_LO')) {
                if (!empty($licenseKey) && !empty($this->licenseMessage)) {
                    $this->showMessage = true;
                }

                //echo $error;
                if ($error) {
                    $this->licenseMessage = $error;
                    add_action('admin_notices', [$this, 'license_activate_error_notice'], 10, 3);
                }

                add_action('admin_notices', [$this, 'license_activate_notice']);

                update_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'), "");
                add_action('admin_post_element_pack_activate_license', [$this, 'action_activate_license']);
            }
        }
    }

    /**
     * Get used widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */
    public static function get_used_widgets() {

        $used_widgets = array();

        if (!Tracker::is_allow_track()) {
            return $used_widgets;
        }

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module = Module::instance();
            $elements = $module->get_formatted_usage('raw');
            $ep_widgets = self::get_ep_widgets_names();

            if (is_array($elements) || is_object($elements)) {
                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $ep_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }
        return $used_widgets;
    }

    /**
     * Get used separate widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_used_only_widgets() {

        $used_widgets = array();

        if (!Tracker::is_allow_track()) {
            return $used_widgets;
        }

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module = Module::instance();
            $elements = $module->get_formatted_usage('raw');
            $ep_widgets = self::get_ep_only_widgets();

            if (is_array($elements) || is_object($elements)) {

                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $ep_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }

        return $used_widgets;
    }

    /**
     * Get used only separate 3rdParty widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_used_only_3rdparty() {

        $used_widgets = array();

        if (!Tracker::is_allow_track()) {
            return $used_widgets;
        }

        if (class_exists('Elementor\Modules\Usage\Module')) {

            $module = Module::instance();
            $elements = $module->get_formatted_usage('raw');
            $ep_widgets = self::get_ep_only_3rdparty_names();

            if (is_array($elements) || is_object($elements)) {

                foreach ($elements as $post_type => $data) {
                    foreach ($data['elements'] as $element => $count) {
                        if (in_array($element, $ep_widgets, true)) {
                            if (isset($used_widgets[$element])) {
                                $used_widgets[$element] += $count;
                            } else {
                                $used_widgets[$element] = $count;
                            }
                        }
                    }
                }
            }
        }

        return $used_widgets;
    }

    /**
     * Get unused widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_widgets() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $ep_widgets = self::get_ep_widgets_names();

        $used_widgets = self::get_used_widgets();

        $unused_widgets = array_diff($ep_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get unused separate widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_only_widgets() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $ep_widgets = self::get_ep_only_widgets();

        $used_widgets = self::get_used_only_widgets();

        $unused_widgets = array_diff($ep_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get unused separate 3rdparty widgets.
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_unused_only_3rdparty() {

        if (!current_user_can('install_plugins')) {
            die();
        }

        $ep_widgets = self::get_ep_only_3rdparty_names();

        $used_widgets = self::get_used_only_3rdparty();

        $unused_widgets = array_diff($ep_widgets, array_keys($used_widgets));

        return $unused_widgets;
    }

    /**
     * Get widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_ep_widgets_names() {
        $names = self::$modules_names;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'bdt-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list
            );
        }

        return $names;
    }

    /**
     * Get separate widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_ep_only_widgets() {
        $names = self::$modules_names_only_widgets;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'bdt-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list_only_widgets
            );
        }

        return $names;
    }

    /**
     * Get separate 3rdParty widgets name
     *
     * @access public
     * @return array
     * @since 6.0.0
     *
     */

    public static function get_ep_only_3rdparty_names() {
        $names = self::$modules_names_only_3rdparty;

        if (null === $names) {
            $names = array_map(
                function ($item) {
                    return isset($item['name']) ? 'bdt-' . str_replace('_', '-', $item['name']) : 'none';
                },
                self::$modules_list_only_3rdparty
            );
        }

        return $names;
    }

    /**
     * Get URL with page id
     *
     * @access public
     *
     */

    public static function get_url() {
        return admin_url('admin.php?page=' . self::PAGE_ID);
    }

    /**
     * Init settings API
     *
     * @access public
     *
     */

    public function admin_init() {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->element_pack_admin_settings());

        //initialize settings
        $this->settings_api->admin_init();
    }

    /**
     * Add Plugin Menus
     *
     * @access public
     *
     */

    public function admin_menu() {
        add_menu_page(
            BDTEP_TITLE . ' ' . esc_html__('Dashboard', 'bdthemes-element-pack'),
            BDTEP_TITLE,
            'manage_options',
            self::PAGE_ID,
            [$this, 'plugin_page'],
            $this->element_pack_icon(),
            58
        );

        add_submenu_page(
            self::PAGE_ID,
            BDTEP_TITLE,
            esc_html__('Core Widgets', 'bdthemes-element-pack'),
            'manage_options',
            self::PAGE_ID . '#element_pack_active_modules',
            [$this, 'display_page']
        );

        add_submenu_page(
            self::PAGE_ID,
            BDTEP_TITLE,
            esc_html__('3rd Party Widgets', 'bdthemes-element-pack'),
            'manage_options',
            self::PAGE_ID . '#element_pack_third_party_widget',
            [$this, 'display_page']
        );

        add_submenu_page(
            self::PAGE_ID,
            BDTEP_TITLE,
            esc_html__('Extensions', 'bdthemes-element-pack'),
            'manage_options',
            self::PAGE_ID . '#element_pack_elementor_extend',
            [$this, 'display_page']
        );

        add_submenu_page(
            self::PAGE_ID,
            BDTEP_TITLE,
            esc_html__('API Settings', 'bdthemes-element-pack'),
            'manage_options',
            self::PAGE_ID . '#element_pack_api_settings',
            [$this, 'display_page']
        );

        if (!defined('BDTEP_LO')) {

            add_submenu_page(
                self::PAGE_ID,
                BDTEP_TITLE,
                esc_html__('Other Settings', 'bdthemes-element-pack'),
                'manage_options',
                self::PAGE_ID . '#element_pack_other_settings',
                [$this, 'display_page']
            );

            add_submenu_page(
                self::PAGE_ID,
                BDTEP_TITLE,
                esc_html__('License', 'bdthemes-element-pack'),
                'manage_options',
                self::PAGE_ID . '#element_pack_license_settings',
                [$this, 'display_page']
            );
        }
    }

    /**
     * Get SVG Icons of Element Pack
     *
     * @access public
     * @return string
     */

    public function element_pack_icon() {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjIsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSIyMzAuN3B4IiBoZWlnaHQ9IjI1NC44MXB4IiB2aWV3Qm94PSIwIDAgMjMwLjcgMjU0LjgxIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyMzAuNyAyNTQuODE7Ig0KCSB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik02MS4wOSwyMjkuMThIMjguOTVjLTMuMTcsMC01Ljc1LTIuNTctNS43NS01Ljc1bDAtMTkyLjA3YzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDMyLjE0DQoJYzMuMTcsMCw1Ljc1LDIuNTcsNS43NSw1Ljc1djE5Mi4wN0M2Ni44MywyMjYuNjEsNjQuMjYsMjI5LjE4LDYxLjA5LDIyOS4xOHoiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yMDcuNSwzMS4zN3YzMi4xNGMwLDMuMTctMi41Nyw1Ljc1LTUuNzUsNS43NUg5MC4wNGMtMy4xNywwLTUuNzUtMi41Ny01Ljc1LTUuNzVWMzEuMzcNCgljMC0zLjE3LDIuNTctNS43NSw1Ljc1LTUuNzVoMTExLjcyQzIwNC45MywyNS42MiwyMDcuNSwyOC4yLDIwNy41LDMxLjM3eiIvPg0KPHBhdGggY2xhc3M9InN0MCIgZD0iTTIwNy41LDExMS4zM3YzMi4xNGMwLDMuMTctMi41Nyw1Ljc1LTUuNzUsNS43NUg5MC4wNGMtMy4xNywwLTUuNzUtMi41Ny01Ljc1LTUuNzV2LTMyLjE0DQoJYzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDExMS43MkMyMDQuOTMsMTA1LjU5LDIwNy41LDEwOC4xNiwyMDcuNSwxMTEuMzN6Ii8+DQo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMjA3LjUsMTkxLjN2MzIuMTRjMCwzLjE3LTIuNTcsNS43NS01Ljc1LDUuNzVIOTAuMDRjLTMuMTcsMC01Ljc1LTIuNTctNS43NS01Ljc1VjE5MS4zDQoJYzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDExMS43MkMyMDQuOTMsMTg1LjU1LDIwNy41LDE4OC4xMywyMDcuNSwxOTEuM3oiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xNjkuNjIsMjUuNjJoMzIuMTRjMy4xNywwLDUuNzUsMi41Nyw1Ljc1LDUuNzV2MTEyLjFjMCwzLjE3LTIuNTcsNS43NS01Ljc1LDUuNzVoLTMyLjE0DQoJYy0zLjE3LDAtNS43NS0yLjU3LTUuNzUtNS43NVYzMS4zN0MxNjMuODcsMjguMiwxNjYuNDQsMjUuNjIsMTY5LjYyLDI1LjYyeiIvPg0KPC9zdmc+DQo=';
    }

    /**
     * Get SVG Icons of Element Pack
     *
     * @access public
     * @return array
     */

    public function get_settings_sections() {
        $sections = [
            [
                'id' => 'element_pack_active_modules',
                'title' => esc_html__('Core Widgets', 'bdthemes-element-pack'),
            ],
            [
                'id' => 'element_pack_third_party_widget',
                'title' => esc_html__('3rd Party Widgets', 'bdthemes-element-pack'),
            ],
            [
                'id' => 'element_pack_elementor_extend',
                'title' => esc_html__('Extensions', 'bdthemes-element-pack'),
            ],
            [
                'id' => 'element_pack_api_settings',
                'title' => esc_html__('API Settings', 'bdthemes-element-pack'),
            ],
            [
                'id' => 'element_pack_other_settings',
                'title' => esc_html__('Other Settings', 'bdthemes-element-pack'),
            ],
        ];

        return $sections;
    }

    /**
     * Merge Admin Settings
     *
     * @access protected
     * @return array
     */

    protected function element_pack_admin_settings() {

        return ModuleService::get_widget_settings(function ($settings) {
            $settings_fields = $settings['settings_fields'];

            self::$modules_list = array_merge($settings_fields['element_pack_active_modules'], $settings_fields['element_pack_third_party_widget']);
            self::$modules_list_only_widgets = $settings_fields['element_pack_active_modules'];
            self::$modules_list_only_3rdparty = $settings_fields['element_pack_third_party_widget'];

            return $settings_fields;
        });
    }

    /**
     * Get Welcome Panel
     *
     * @access public
     * @return void
     */

    public function element_pack_welcome() {
        $track_nw_msg = '';
        if (!Tracker::is_allow_track()) {
            $track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'bdthemes-element-pack');
            $track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
        }
?>

        <div class="ep-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="ep-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?> <?php echo $track_nw_msg; ?>>

                        <?php
                        $used_widgets = count(self::get_used_widgets());
                        $un_used_widgets = count(self::get_unused_widgets());
                        ?>


                        <div class="ep-count-canvas-wrap bdt-flex bdt-flex-between">
                            <div class="ep-count-wrap">
                                <h1 class="ep-feature-title">All Widgets</h1>
                                <div class="ep-widget-count">Used: <b><?php echo $used_widgets; ?></b></div>
                                <div class="ep-widget-count">Unused: <b><?php echo $un_used_widgets; ?></b></div>
                                <div class="ep-widget-count">Total:
                                    <b><?php echo $used_widgets + $un_used_widgets; ?></b>
                                </div>
                            </div>

                            <div class="ep-canvas-wrap">
                                <canvas id="bdt-db-total-status" style="height: 120px; width: 120px;" data-label="Total Widgets Status - (<?php echo $used_widgets + $un_used_widgets; ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>" data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="ep-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <?php
                        $used_only_widgets = count(self::get_used_only_widgets());
                        $unused_only_widgets = count(self::get_unused_only_widgets());
                        ?>


                        <div class="ep-count-canvas-wrap bdt-flex bdt-flex-between">
                            <div class="ep-count-wrap">
                                <h1 class="ep-feature-title">Core</h1>
                                <div class="ep-widget-count">Used: <b><?php echo $used_only_widgets; ?></b></div>
                                <div class="ep-widget-count">Unused: <b><?php echo $unused_only_widgets; ?></b></div>
                                <div class="ep-widget-count">Total:
                                    <b><?php echo $used_only_widgets + $unused_only_widgets; ?></b>
                                </div>
                            </div>

                            <div class="ep-canvas-wrap">
                                <canvas id="bdt-db-only-widget-status" style="height: 120px; width: 120px;" data-label="Core Widgets Status - (<?php echo $used_only_widgets + $unused_only_widgets; ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_only_widgets) . ',' . esc_attr($unused_only_widgets); ?>" data-bg="#EF476F, #ffcdd9" data-bg-hover="#0673e1, #e71522"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="ep-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <?php
                        $used_only_3rdparty = count(self::get_used_only_3rdparty());
                        $unused_only_3rdparty = count(self::get_unused_only_3rdparty());
                        ?>


                        <div class="ep-count-canvas-wrap bdt-flex bdt-flex-between">
                            <div class="ep-count-wrap">
                                <h1 class="ep-feature-title">3rd Party</h1>
                                <div class="ep-widget-count">Used: <b><?php echo $used_only_3rdparty; ?></b></div>
                                <div class="ep-widget-count">Unused: <b><?php echo $unused_only_3rdparty; ?></b></div>
                                <div class="ep-widget-count">Total:
                                    <b><?php echo $used_only_3rdparty + $unused_only_3rdparty; ?></b>
                                </div>
                            </div>

                            <div class="ep-canvas-wrap">
                                <canvas id="bdt-db-only-3rdparty-status" style="height: 120px; width: 120px;" data-label="3rd Party Widgets Status - (<?php echo $used_only_3rdparty + $unused_only_3rdparty; ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_only_3rdparty) . ',' . esc_attr($unused_only_3rdparty); ?>" data-bg="#06D6A0, #B6FFEC" data-bg-hover="#0673e1, #e71522"></canvas>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bdt-width-1-2@m bdt-width-1-4@l">
                    <div class="ep-widget-status bdt-card bdt-card-body" <?php echo $track_nw_msg; ?>>

                        <div class="ep-count-canvas-wrap bdt-flex bdt-flex-between">
                            <div class="ep-count-wrap">
                                <h1 class="ep-feature-title">Active</h1>
                                <div class="ep-widget-count">Core: <b id="bdt-total-widgets-status-core"></b></div>
                                <div class="ep-widget-count">3rd Party: <b id="bdt-total-widgets-status-3rd"></b></div>
                                <div class="ep-widget-count">Extensions: <b id="bdt-total-widgets-status-extensions"></b></div>
                                <div class="ep-widget-count">Total: <b id="bdt-total-widgets-status-heading"></b></div>
                            </div>

                            <div class="ep-canvas-wrap">
                                <canvas id="bdt-total-widgets-status" style="height: 120px; width: 120px;" data-label="Total Active Widgets Status" data-labels="<?php echo esc_attr('Core, 3rd Party, Extensions'); ?>" data-bg="#0680d6, #B0EBFF, #E6F9FF" data-bg-hover="#0673e1, #B0EBFF, #b6f9e8">
                                </canvas>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-3@m ep-support-section">
                    <div class="ep-support-content bdt-card bdt-card-body">
                        <h1 class="ep-feature-title">Support And Feedback</h1>
                        <p>Feeling like to consult with an expert? Take live Chat support immediately from <a href="https://elementpack.pro" target="_blank" rel="">ElementPack</a>. We are always
                            ready to help
                            you 24/7.</p>
                        <p><strong>Or if you’re facing technical issues with our plugin, then please create a support
                                ticket</strong></p>
                        <a class="bdt-button bdt-btn-blue bdt-margin-small-top bdt-margin-small-right" target="_blank" rel="" href="https://bdthemes.com/all-knowledge-base-of-element-pack/">Knowledge
                            Base</a>
                        <a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" href="https://bdthemes.com/support/">Get Support</a>
                    </div>
                </div>

                <div class="bdt-width-2-3@m">
                    <div class="bdt-card bdt-card-body ep-system-requirement">
                        <h1 class="ep-feature-title bdt-margin-small-bottom">System Requirement</h1>
                        <?php $this->element_pack_system_requirement(); ?>
                    </div>
                </div>
            </div>

            <div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
                <div class="bdt-width-1-2@m ep-support-section">
                    <div class="bdt-card bdt-card-body ep-feedback-bg">
                        <h1 class="ep-feature-title">Missing Any Feature?</h1>
                        <p style="max-width: 520px;">Are you in need of a feature that’s not available in our plugin?
                            Feel free to do a feature request from here,</p>
                        <a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" rel="" href="https://feedback.elementpack.pro/b/3v2gg80n/feature-requests/idea/new">Request Feature</a>
                    </div>
                </div>

                <div class="bdt-width-1-2@m">
                    <div class="bdt-card bdt-card-body ep-tryaddon-bg">
                        <h1 class="ep-feature-title">Try Our Others Addons</h1>
                        <p style="max-width: 520px;">
                            <b>Prime Slider, Ultimate Post Kit, Ultimate Store Kit, Pixel Gallery & Live Copy Paste </b> addons for <b>Elementor</b> is the best slider, blogs and eCommerce plugin for WordPress.
                        </p>
                        <div class="bdt-others-plugins-link">
                            <a class="bdt-button bdt-btn-ps bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/bdthemes-prime-slider-lite/" bdt-tooltip="The revolutionary slider builder addon for Elementor with next-gen superb interface. It's Free! Download it.">Prime Slider</a>
                            <a class="bdt-button bdt-btn-upk bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-post-kit/" bdt-tooltip="Best blogging addon for building quality blogging website with fine-tuned features and widgets. It's Free! Download it.">Ultimate Post Kit</a>
                            <a class="bdt-button bdt-btn-usk bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-store-kit/" bdt-tooltip="The only eCommmerce addon for answering all your online store design problems in one package. It's Free! Download it.">Ultimate Store Kit</a>
                            <a class="bdt-button bdt-btn-live-copy bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/live-copy-paste/" bdt-tooltip="Superfast cross-domain copy-paste mechanism for WordPress websites with true UI copy experience. It's Free! Download it.">Live Copy Paste</a>
                            <a class="bdt-button bdt-btn-pg bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/pixel-gallery/" bdt-tooltip="Pixel Gallery provides more than 30+ essential elements for everyday applications to simplify the whole web building process. It's Free! Download it.">Pixel Gallery</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    <?php
    }

    /**
     * Get License Key
     *
     * @access public
     * @return string
     */

    public static function get_license_key()
    {
        $license_key = get_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'));
        if (empty($license_key)) {
            $license_key = get_option('element_pack_license_key');
            if (!empty($license_key)) {
                self::set_license_key($license_key);
                update_option('element_pack_license_key', '');
            }
        }
        return trim($license_key);
    }

    /**
     * Get License Email
     *
     * @access public
     * @return string
     */

    public static function get_license_email() {
        return trim(get_option('element_pack_license_email', get_bloginfo('admin_email')));
    }

    /**
     * Set License Key
     *
     * @access public
     * @return string
     */

    public static function set_license_key($license_key) {

        return update_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'), $license_key);
    }

    /**
     * Set License Email
     *
     * @access public
     * @return string
     */

    public static function set_license_email($license_email) {
        return update_option('element_pack_license_email', $license_email);
    }

    /**
     * Display License Page
     *
     * @access public
     */

    public function license_page() {

        if ($this->is_activated) {

            $this->license_activated();
        } else {
            if (!empty($licenseKey) && !empty($this->licenseMessage)) {
                $this->showMessage = true;
            }

            $this->license_form();
        }
    }

    /**
     * Display System Requirement
     *
     * @access public
     * @return void
     */

    public function element_pack_system_requirement() {
        $php_version = phpversion();
        $max_execution_time = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        $post_limit = ini_get('post_max_size');
        $uploads = wp_upload_dir();
        $upload_path = $uploads['basedir'];
        $yes_icon = '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
        $no_icon = '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';

        $environment = Utils::get_environment_info();

    ?>
        <ul class="check-system-status bdt-grid bdt-child-width-1-2@m bdt-grid-small ">
            <li>
                <div>

                    <span class="label1">PHP Version: </span>

                    <?php
                    if (version_compare($php_version, '7.4.0', '<')) {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 7.4 Recommended" bdt-tooltip>Currently: ' . $php_version . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $php_version . '</span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">Max execution time: </span>

                    <?php
                    if ($max_execution_time < '90') {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 90 Recommended" bdt-tooltip>Currently: ' . $max_execution_time . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $max_execution_time . '</span>';
                    }
                    ?>
                </div>
            </li>
            <li>
                <div>
                    <span class="label1">Memory Limit: </span>

                    <?php
                    if (intval($memory_limit) < '512') {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 512M Recommended" bdt-tooltip>Currently: ' . $memory_limit . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $memory_limit . '</span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">Max Post Limit: </span>

                    <?php
                    if (intval($post_limit) < '32') {
                        echo $no_icon;
                        echo '<span class="label2" title="Min: 32M Recommended" bdt-tooltip>Currently: ' . $post_limit . '</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently: ' . $post_limit . '</span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">Uploads folder writable: </span>

                    <?php
                    if (!is_writable($upload_path)) {
                        echo $no_icon;
                    } else {
                        echo $yes_icon;
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">MultiSite: </span>

                    <?php
                    if ($environment['wp_multisite']) {
                        echo $yes_icon;
                        echo '<span class="label2">MultiSite</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">No MultiSite </span>';
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">GZip Enabled: </span>

                    <?php
                    if ($environment['gzip_enabled']) {
                        echo $yes_icon;
                    } else {
                        echo $no_icon;
                    }
                    ?>
                </div>
            </li>

            <li>
                <div>
                    <span class="label1">Debug Mode: </span>
                    <?php
                    if ($environment['wp_debug_mode']) {
                        echo $no_icon;
                        echo '<span class="label2">Currently Turned On</span>';
                    } else {
                        echo $yes_icon;
                        echo '<span class="label2">Currently Turned Off</span>';
                    }
                    ?>
                </div>
            </li>

        </ul>

        <div class="bdt-admin-alert">
            <strong>Note:</strong> If you have multiple addons like <b>Element Pack</b> so you need some more
            requirement some
            cases so make sure you added more memory for others addon too.
        </div>
    <?php
    }

    /**
     * Display Plugin Page
     *
     * @access public
     * @return void
     */

    public function plugin_page() {

        ?>

        <div class="wrap element-pack-dashboard">
            <h1><?php echo BDTEP_TITLE; ?> Settings</h1>

            <?php $this->settings_api->show_navigation(); ?>


            <div class="bdt-switcher bdt-tab-container bdt-container-xlarge">
                <div id="element_pack_welcome_page" class="ep-option-page group">
                    <?php $this->element_pack_welcome(); ?>

                    <?php if (!defined('BDTEP_WL')) {
                        $this->footer_info();
                    } ?>
                </div>

                <?php
                $this->settings_api->show_forms();
                ?>

                <div id="element_pack_license_settings_page" class="ep-option-page group">

                    <?php $this->license_page(); ?>

                    <?php if (!defined('BDTEP_WL')) {
                        $this->footer_info();
                    } ?>
                </div>
            </div>

        </div>

        <?php

        $this->script();

        ?>

    <?php
    }

    /**
     * License Activate Action
     * @access public
     */

    public function action_activate_license() {
        check_admin_referer('el-license');

        $licenseKey = !empty($_POST['element_pack_license_key']) ? sanitize_text_field($_POST['element_pack_license_key']) : "";
        $licenseEmail = !empty($_POST['element_pack_license_email']) ? wp_unslash($_POST['element_pack_license_email']) : "";

        update_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'), $licenseKey);
        update_option("element_pack_license_email", $licenseEmail);

        wp_safe_redirect(admin_url('admin.php?page=' . 'element_pack_options#element_pack_license_settings'));
    }

    /**
     * License Deactivate Action
     * @access public
     */

    public function action_deactivate_license() {

        check_admin_referer('el-license');
        if (Element_Pack_Base::remove_license_key(BDTEP__FILE__, $message)) {
            update_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'), "");
        }
        wp_safe_redirect(admin_url('admin.php?page=' . 'element_pack_options#element_pack_license_settings'));
    }

    /**
     * Display License Activated
     *
     * @access public
     * @return void
     */

    public function license_activated() {
    ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="element_pack_deactivate_license" />
            <div class="el-license-container bdt-card bdt-card-body">


                <h3 class="el-license-title"><span class="dashicons dashicons-admin-network"></span> <?php _e("Element Pack License Information", 'bdthemes-element-pack'); ?>
                </h3>

                <ul class="element-pack-license-info bdt-list bdt-list-divider">
                    <li>
                        <div>
                            <span class="license-info-title"><?php _e('Status', 'bdthemes-element-pack'); ?></span>

                            <?php if (Element_Pack_Base::get_register_info()->is_valid) : ?>
                                <span class="license-valid">Valid License</span>
                            <?php else : ?>
                                <span class="license-valid">Invalid License</span>
                            <?php endif; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="license-info-title"><?php _e('License Type', 'bdthemes-element-pack'); ?></span>
                            <?php echo Element_Pack_Base::get_register_info()->license_title; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="license-info-title"><?php _e('License Expired on', 'bdthemes-element-pack'); ?></span>
                            <?php echo Element_Pack_Base::get_register_info()->expire_date; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="license-info-title"><?php _e('Support Expired on', 'bdthemes-element-pack'); ?></span>
                            <?php echo Element_Pack_Base::get_register_info()->support_end; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="license-info-title"><?php _e('License Email', 'bdthemes-element-pack'); ?></span>
                            <?php echo self::get_license_email(); ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="license-info-title"><?php _e('Your License Key', 'bdthemes-element-pack'); ?></span>
                            <span class="license-key"><?php echo esc_attr(substr(Element_Pack_Base::get_register_info()->license_key, 0, 9) . "XXXXXXXX-XXXXXXXX" . substr(Element_Pack_Base::get_register_info()->license_key, -9)); ?></span>
                        </div>
                    </li>
                </ul>

                <div class="el-license-active-btn">
                    <?php wp_nonce_field('el-license'); ?>
                    <?php submit_button('Deactivate License'); ?>
                </div>
            </div>
        </form>
    <?php
    }

    /**
     * Display License Form
     *
     * @access public
     * @return void
     */

    public function license_form() {
    ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="element_pack_activate_license" />
            <div class="el-license-container bdt-card bdt-card-body">

                <?php
                if (!empty($this->showMessage) && !empty($this->licenseMessage)) {
                ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo $this->licenseMessage; ?></p>
                    </div>
                <?php
                }
                ?>

                <h3 class="bdt-text-large">
                    <strong><?php _e('Enter your license key here, to activate Element Pack Pro, and get full feature updates and premium support.', 'bdthemes-element-pack'); ?></strong>
                </h3>

                <ol class="bdt-text-default">
                    <li><?php printf(__('Log in to your <a href="%1s" target="_blank">bdthemes fastspring</a> or <a href="%2s" target="_blank">envato</a> account to get your license key.', 'bdthemes-element-pack'), 'https://bdthemes.onfastspring.com/account', 'https://codecanyon.net/downloads'); ?></li>
                    <li><?php printf(__('If you don\'t yet have a license key, <a href="%s" target="_blank">get Element Pack Pro now</a>.', 'bdthemes-element-pack'), 'https://elementpack.pro/pricing/'); ?></li>
                    <li><?php _e('Copy the license key from your account and paste it below for work element pack properly.', 'bdthemes-element-pack'); ?></li>
                </ol>

                <div class="bdt-ep-license-field">
                    <label for="element_pack_license_email">License Email
                        <input type="text" class="regular-text code" name="element_pack_license_email" size="50" placeholder="example@email.com" required="required">
                    </label>
                </div>

                <div class="bdt-ep-license-field">
                    <label for="element_pack_license_key">License Code
                        <input type="text" class="regular-text code" name="element_pack_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
                    </label>
                </div>


                <div class="el-license-active-btn">
                    <?php wp_nonce_field('el-license'); ?>
                    <?php submit_button('Activate License'); ?>
                </div>
            </div>
        </form>
    <?php
    }

    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    public function script() {
    ?>
        <script>
            jQuery(document).ready(function() {
                jQuery('.ep-no-result').removeClass('bdt-animation-shake');
            });

            function filterSearch(e) {
                var parentID = '#' + jQuery(e).data('id');
                var search = jQuery(parentID).find('.bdt-search-input').val().toLowerCase();


                jQuery(".ep-options .ep-option-item").filter(function() {
                    jQuery(this).toggle(jQuery(this).attr('data-widget-name').toLowerCase().indexOf(search) > -1)
                });

                if (!search) {
                    jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "");
                    jQuery(parentID).find('.ep-widget-all').trigger('click');
                } else {
                    // if (search.length < 3) {
                    //     return;
                    // }
                    jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
                    jQuery(parentID).find('.bdt-search-input').removeClass('bdt-active');
                }
                jQuery(parentID).find('.bdt-search-input').trigger('click');

            }


            jQuery('.ep-options-parent').each(function(e, item) {
                var eachItem = '#' + jQuery(item).attr('id');
                jQuery(eachItem).on("beforeFilter", function() {
                    jQuery(eachItem).find('.ep-no-result').removeClass('bdt-animation-shake');
                });

                jQuery(eachItem).on("afterFilter", function() {
                    var isElementVisible = false;
                    var i = 0;

                    if (jQuery(eachItem).closest(".ep-options-parent").eq(i).is(":visible")) {} else {
                        isElementVisible = true;
                    }

                    while (!isElementVisible && i < jQuery(eachItem).find(".ep-option-item").length) {
                        if (jQuery(eachItem).find(".ep-option-item").eq(i).is(":visible")) {
                            isElementVisible = true;
                        }
                        i++;
                    }

                    if (isElementVisible === false) {
                        jQuery(eachItem).find('.ep-no-result').addClass('bdt-animation-shake');
                    }

                });
            });


            jQuery('.ep-widget-filter-nav li a').on('click', function(e) {
                jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('');
                jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('').attr('bdt-filter-control', '');
            });


            jQuery(document).ready(function($) {
                'use strict';

                function hashHandler() {
                    var $tab = jQuery('.element-pack-dashboard .bdt-tab');
                    if (window.location.hash) {
                        var hash = window.location.hash.substring(1);
                        bdtUIkit.tab($tab).show(jQuery('#bdt-' + hash).data('tab-index'));
                    }
                }

                jQuery(window).on('load', function() {
                    hashHandler();
                });

                window.addEventListener("hashchange", hashHandler, true);

                jQuery('.toplevel_page_element_pack_options > ul > li > a ').on('click', function(event) {
                    jQuery(this).parent().siblings().removeClass('current');
                    jQuery(this).parent().addClass('current');
                });

                jQuery('#element_pack_active_modules_page a.ep-active-all-widget').click(function(e) {
                    e.preventDefault();

                    jQuery('#element_pack_active_modules_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.ep-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#element_pack_active_modules_page a.ep-deactive-all-widget').click(function(e) {
                    e.preventDefault();

                    jQuery('#element_pack_active_modules_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.ep-active-all-widget').removeClass('bdt-active');
                });

                jQuery('#element_pack_third_party_widget_page a.ep-active-all-widget').click(function() {

                    jQuery('#element_pack_third_party_widget_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.ep-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#element_pack_third_party_widget_page a.ep-deactive-all-widget').click(function() {

                    jQuery('#element_pack_third_party_widget_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.ep-active-all-widget').removeClass('bdt-active');
                });

                jQuery('#element_pack_elementor_extend_page a.ep-active-all-widget').click(function() {

                    jQuery('#element_pack_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).attr('checked', 'checked').prop("checked", true);
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.ep-deactive-all-widget').removeClass('bdt-active');
                });

                jQuery('#element_pack_elementor_extend_page a.ep-deactive-all-widget').click(function() {

                    jQuery('#element_pack_elementor_extend_page .checkbox:visible').each(function() {
                        jQuery(this).removeAttr('checked');
                    });

                    jQuery(this).addClass('bdt-active');
                    jQuery('a.ep-active-all-widget').removeClass('bdt-active');
                });

                jQuery('form.settings-save').submit(function(event) {
                    event.preventDefault();

                    bdtUIkit.notification({
                        message: '<div bdt-spinner></div> <?php esc_html_e('Please wait, Saving settings...', 'bdthemes-element-pack') ?>',
                        timeout: false
                    });

                    jQuery(this).ajaxSubmit({
                        success: function() {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'bdthemes-element-pack') ?>',
                                status: 'primary'
                            });
                        },
                        error: function(data) {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'bdthemes-element-pack') ?>',
                                status: 'warning'
                            });
                        }
                    });

                    return false;
                });

            });
        </script>
    <?php
    }

    /**
     * Display Footer
     *
     * @access public
     * @return void
     */

    public function footer_info() {
    ?>

        <div class="element-pack-footer-info bdt-margin-medium-top">

            <div class="bdt-grid ">

                <div class="bdt-width-auto@s ep-setting-save-btn">



                </div>

                <div class="bdt-width-expand@s bdt-text-right">
                    <p class="">
                        Element Pack Pro plugin made with love by <a target="_blank" href="https://bdthemes.com">BdThemes</a> Team.
                        <br>All rights reserved by <a target="_blank" href="https://bdthemes.com">BdThemes.com</a>.
                    </p>
                </div>
            </div>

        </div>

<?php
    }

    /**
     * License Active Error
     *
     * @access public
     */

    public function license_activate_error_notice() {

        Notices::add_notice(
            [
                'id' => 'license-error',
                'type' => 'error',
                'dismissible' => true,
                'dismissible-time' => 43200,
                'title' => 'Sorry, Element Pack is not activated!',
                'message' => $this->licenseMessage,
            ]
        );
    }

    /**
     * License Active Notice
     *
     * @access public
     */

    public function license_activate_notice() {
        Notices::add_notice(
            [
                'id' => 'license-issue',
                'type' => 'error',
                'dismissible' => true,
                'dismissible-time' => HOUR_IN_SECONDS * 72,
                'title' => 'Sorry, Element Pack is not activated!',
                'message' => __('Thank you for purchase Element Pack. Please <a href="' . self::get_url() . '#element_pack_license_settings">activate your license</a> to get feature updates, premium support. Don\'t have Element Pack license? Purchase and download your license copy <a href="https://elementpack.pro/" target="_blank">from here</a>.', 'bdthemes-element-pack'),
            ]
        );
    }

    /**
     * Free V Require Notice
     * This notice is very important to show minimum 3 to 5 next update released version.
     *
     * @access public
     */

    public function free_v_require_notice() {
        Notices::add_notice(
            [
                'id'               => 'free-version-require',
                'type'             => 'warning',
                'dismissible'      => true,
                'dismissible-time' => DAY_IN_SECONDS * 15,
                'message'          => __('From version <strong>v7.5.0</strong>, the Pro/Premium versions of <strong>Element Pack</strong> would require the installation of the Free Version plugin. Please install the Free version if you are using the Element Pack Pro <strong>v7.5.0</strong> version so we can troubleshoot any issues faster. Thank you.', 'bdthemes-element-pack'),
            ]
        );
    }
    /**
     *
     * Check mini-Cart of Elementor Activated or Not
     * It's better to not use multiple mini-Cart on the same time.
     * Transient Expire on 15 days
     *
     * @access public
     */

    public function el_use_mini_cart() {
        Notices::add_notice(
            [
                'id' => 'ep-el-use-mini-cart',
                'type' => 'warning',
                'dismissible' => true,
                'dismissible-time' => MONTH_IN_SECONDS / 2,
                'title' => 'Oops, Possibilities to get errors',
                'message' => __('We can see you activated the <strong>Mini-Cart</strong> of Elementor Pro and also Element Pack Pro. We will recommend you to choose one of them, otherwise you will get conflict. Thank you.', 'bdthemes-element-pack'),
            ]
        );
    }

    /**
     *
     * Allow Tracker deactivated warning
     * If Allow Tracker disable in elementor then this notice will be show
     *
     * @access public
     */

    public function allow_tracker_activate_notice() {

        Notices::add_notice(
            [
                'id' => 'ep-allow-tracker',
                'type' => 'warning',
                'dismissible' => true,
                'dismissible-time' => MONTH_IN_SECONDS * 3,
                'title' => 'Sorry, Element Pack Widget Analytics Not Working!',
                'message' => __('Please activate <strong>Usage Data Sharing</strong> features from Elementor, otherwise Widgets Analytics will not work. Please activate the settings from <strong>Elementor > Settings > General Tab >  Usage Data Sharing.</strong> Thank you.', 'bdthemes-element-pack'),
            ]
        );
    }

    /**
     *
     * Allow Tracker deactivated warning only in Element Pack Dashboard
     * If Allow Tracker disable in elementor then this notice will be show
     *
     * @access public
     */

    public function allow_tracker_dashboard_notice() {
        Notices::add_notice(
            [
                'id' => 'ep-allow-tracker-dashboard',
                'type' => 'warning',
                'dismissible' => true,
                'dismissible-time' => MONTH_IN_SECONDS,
                'title' => 'Sorry, Element Pack Widget Analytics Not Working!',
                'message' => __('Please activate <strong>Usage Data Sharing</strong> features from Elementor, otherwise Widgets Analytics will not work. Please activate the settings from <strong>Elementor > Settings > General Tab >  Usage Data Sharing.</strong> Thank you.', 'bdthemes-element-pack'),
            ]
        );
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    public function get_pages() {
        $pages = get_pages();
        $pages_options = [];
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }
}

new ElementPack_Admin_Settings();
