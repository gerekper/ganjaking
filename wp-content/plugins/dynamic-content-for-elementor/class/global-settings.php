<?php

namespace DynamicContentForElementor;

use Elementor\Core\Files\CSS\Base;
use Elementor\Controls_Manager;
use Elementor\Core\Files\CSS\Global_CSS;
use Elementor\Core\Settings\Base\CSS_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Core\Settings\Manager;
use DynamicContentForElementor\Model as Settings_Model;
if (!\defined('ABSPATH')) {
    exit;
}
class GlobalSettings extends CSS_Manager
{
    const PANEL_TAB_SETTINGS = 'settings';
    const META_KEY = '_dce_general_settings';
    public static $global_settings = [];
    public static $registered_settings = [];
    /**
     * Settings manager constructor.
     *
     * Initializing DCE settings manager.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        self::add_panel_tabs();
        self::global_settings();
    }
    /**
     * Get manager name.
     *
     * Retrieve settings manager name.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access public
     *
     * @return string settings manager name
     */
    public function get_name()
    {
        return 'dynamicooo';
    }
    /**
     * Get CSS file name.
     *
     * Retrieve CSS file name for the settings base css manager.
     *
     * @see Elementor\Core\Settings\Base\CSS_Manager
     *
     * @access protected
     *
     * @return string CSS file name
     */
    protected function get_css_file_name()
    {
        return 'global';
    }
    /**
     * Get model for CSS file.
     *
     * Retrieve the model for the CSS file.
     *
     * @see Elementor\Core\Settings\Base\CSS_Manager
     *
     * @access protected
     *
     * @param CSS_File $css_file The requested CSS file.
     * @return CSS_Model
     */
    protected function get_model_for_css_file(Base $css_file)
    {
        return $this->get_model();
    }
    /**
     * Get CSS file for update.
     *
     * Retrieve the CSS file before updating it.
     *
     * @see Elementor\Core\Settings\Base\CSS_Manager
     *
     * @access protected
     *
     * @param int $id Post ID.
     * @return CSS_File
     */
    protected function get_css_file_for_update($id)
    {
        return Global_CSS::create('global.css');
    }
    /**
     * Get model for config.
     *
     * Retrieve the model for settings configuration.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access public
     *
     * @return Model The model object.
     */
    public function get_model_for_config()
    {
        return $this->get_model();
    }
    private function add_panel_tabs()
    {
        Controls_Manager::add_tab(self::PANEL_TAB_SETTINGS, __('Settings', 'dynamic-content-for-elementor'));
    }
    protected function global_settings()
    {
        $model_controls = Settings_Model::get_controls_list();
    }
    public function dce_settings()
    {
        return $this->get_saved_settings(0);
    }
    /**
     * Get saved settings.
     *
     * Retrieve the saved settings from the database.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access protected
     *
     * @param int $id Post ID
     * @return array
     */
    protected function get_saved_settings($id)
    {
        $model_controls = Settings_Model::get_controls_list();
        $settings = [];
        foreach ($model_controls as $tab_name => $sections) {
            foreach ($sections as $section_name => $section_data) {
                foreach ($section_data['controls'] as $control_name => $control_data) {
                    $saved_setting = get_option($control_name, null);
                    if (null !== $saved_setting) {
                        $settings[$control_name] = get_option($control_name);
                    }
                }
            }
        }
        return $settings;
    }
    /**
     * Save settings to DB.
     *
     * Save settings to the database.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access protected
     *
     * @param array $settings Settings
     * @param int $id Post ID
     * @return array
     */
    protected function save_settings_to_db(array $settings, $id)
    {
        $model_controls = Settings_Model::get_controls_list();
        $one_list_settings = [];
        foreach ($model_controls as $tab_name => $sections) {
            foreach ($sections as $section_name => $section_data) {
                foreach ($section_data['controls'] as $control_name => $control_data) {
                    if (isset($settings[$control_name])) {
                        $one_list_control_name = \str_replace('elementor_', '', $control_name);
                        $one_list_settings[$one_list_control_name] = $settings[$control_name];
                        update_option($control_name, $settings[$control_name]);
                    } else {
                        delete_option($control_name);
                    }
                }
            }
        }
        // Save all settings in one list for a future usage
        if (!empty($one_list_settings)) {
            update_option(self::META_KEY, $one_list_settings);
        } else {
            delete_option(self::META_KEY);
        }
    }
    public static function init()
    {
        self::on_settings_registered();
    }
    /**
     * On extensions Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public static function on_settings_registered()
    {
        self::register_settings();
        $settings_controls = \DynamicContentForElementor\Model::get_controls_list();
        if (!empty($settings_controls['settings'])) {
            \Elementor\Core\Settings\Manager::add_settings_manager(new \DynamicContentForElementor\GlobalSettings());
        }
    }
    public static function get_global_settings()
    {
        return \DynamicContentForElementor\Plugin::instance()->features->filter(['type' => 'global-setting']);
    }
    /**
     * On Controls Registered
     *
     * @since 1.0.4
     *
     * @access public
     */
    public static function register_settings()
    {
        $global_settings = self::get_global_settings();
        foreach ($global_settings as $global_setting_info) {
            if ($global_setting_info['status'] === 'active') {
                $class = '\\DynamicContentForElementor\\' . $global_setting_info['class'];
                if (\DynamicContentForElementor\Helper::check_plugin_dependencies(\false, $global_setting_info['plugin_depends'])) {
                    self::$registered_settings[] = new $class();
                }
            }
        }
    }
}
