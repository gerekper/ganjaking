<?php

namespace DynamicContentForElementor;

use Elementor\Controls_Manager;
use Elementor\Core\Settings\Base\CSS_Model;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\GlobalSettings;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Model extends CSS_Model
{
    /**
     * Get model name.
     *
     * Retrieve the model name.
     *
     * @see Elementor\Controls_Stack
     *
     * @access public
     *
     * @return string model name.
     */
    public function get_name()
    {
        return 'dce-settings_dce';
    }
    /**
     * Get panel page settings.
     *
     * Retrieve the page setting for the current panel.
     *
     * @see Elementor\Core\Settings\Base\Model
     *
     * @access public
     *
     * @return array panel page settings
     */
    public function get_panel_page_settings()
    {
        return ['title' => DCE_PRODUCT_NAME];
    }
    /**
     * Get CSS wrapper selector.
     *
     * Retrieve the wrapper selector for the current panel.
     *
     * @see Elementor\Core\Settings\Base\CSS_Model
     *
     * @access public
     *
     * @return string css selector
     */
    public function get_css_wrapper_selector()
    {
        return '';
    }
    public static function get_controls_list()
    {
        $target_smoothTransition = '';
        $selector_wrapper = get_option('selector_wrapper');
        if ($selector_wrapper) {
            $target_smoothTransition = ' ' . $selector_wrapper;
        }
        $controls = [];
        $global_settings = GlobalSettings::get_global_settings();
        foreach ($global_settings as $global_settings_info) {
            $class = '\\DynamicContentForElementor\\' . $global_settings_info['class'];
            $controls[$class] = $class::get_controls();
        }
        return [GlobalSettings::PANEL_TAB_SETTINGS => $controls];
    }
    /**
     * Register settings panel controls.
     *
     * Used to add new controls to settings panel.
     *
     * @see Elementor\Controls_Stack
     *
     * @access protected
     *
     * @return void
     */
    protected function register_controls()
    {
        $controls_list = self::get_controls_list();
        foreach ($controls_list as $tab_name => $sections) {
            foreach ($sections as $section_name => $section_data) {
                $this->start_controls_section($section_name, ['label' => $section_data['label'], 'tab' => $tab_name]);
                foreach ($section_data['controls'] as $control_name => $control_data) {
                    $this->add_control($control_name, $control_data);
                }
                $this->end_controls_section();
            }
        }
    }
}
