<?php

namespace DynamicContentForElementor\Includes\Settings;

use Elementor\Controls_Manager;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class TrackerHeader extends \DynamicContentForElementor\Includes\Settings\DCE_Settings_Prototype
{
    public static $name = 'Tracker Header';
    public function get_name()
    {
        return 'dce-settings_trackerheader';
    }
    public function get_css_wrapper_selector()
    {
        return 'body.dce-trackerheader';
    }
    public static function get_controls()
    {
        $wrapper = 'body.dce-trackerheader';
        $target_trackerheader = ' header';
        $selector_header = get_option('selector_header');
        $listselectors = Helper::str_to_array(',', $selector_header);
        if (\count($listselectors) > 1) {
            $selector_header = '#trackerheader-wrap';
        }
        if ($selector_header) {
            $target_trackerheader = ' ' . $selector_header;
        }
        return ['label' => __('Tracker Header', 'dynamic-content-for-elementor'), 'controls' => ['enable_trackerheader' => ['label' => __('Tracker Header', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before', 'default' => ''], 'selector_header' => ['label' => __('Selector Header', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('Type CSS selector (e.g.:#header)', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'label_block' => \true, 'dynamic' => ['active' => \false], 'condition' => ['enable_trackerheader' => 'yes']], 'dce_trackerheader_class_debug' => ['type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="dce-class-debug">...</div>', 'content_classes' => 'dce_class_debug', 'condition' => ['enable_trackerheader' => 'yes']], 'dce_trackerheader_class_controller' => ['label' => __('Controller', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '', 'render_type' => 'ui', 'frontend_available' => \true, 'selectors' => [$wrapper . $target_trackerheader => '
                            z-index: 999;
                            right: 0;
                            left: 0;
                            top: 0;
                            position: fixed;
                            -webkit-transition: background-color .8s ease-in-out, transform .5s ease-in-out;
                            -moz-transition: background-color .8s ease-in-out, transform .5s ease-in-out;
                              -o-transition: background-color .8s ease-in-out, transform .5s ease-in-out;
                                 transition: background-color .8s ease-in-out, transform .5s ease-in-out;'], 'condition' => ['enable_trackerheader' => 'yes', 'selector_header!' => '']], 'dce_trackerheader_settings_note' => ['type' => Controls_Manager::RAW_HTML, 'raw' => __('The selector wrapper is very important for the proper functioning of the transitions. indicates the part of the page that needs to be transformed. <a href="https://help.dynamic.ooo/en/articles/4952536-html-structure-of-themes" target="_blank">This article can help you.</a>', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['enable_trackerheader' => 'yes', 'dce_trackerheader_class_controller!' => '', 'selector_header!' => '']], 'dce_trackerheader_options' => ['label' => __('Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['enable_trackerheader' => 'yes', 'dce_trackerheader_class_controller!' => '', 'selector_header!' => '']], 'trackerheader_overlay' => ['label' => __('Is Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true, 'condition' => ['enable_trackerheader' => 'yes', 'dce_trackerheader_class_controller!' => '', 'selector_header!' => '']], 'dce_trackerheader_zindex' => ['label' => __('Z-Index', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 999, 'min' => 0, 'max' => 10000, 'step' => 1, 'selectors' => [$wrapper . $target_trackerheader => 'z-index: {{VALUE}};'], 'condition' => ['enable_trackerheader' => 'yes', 'dce_trackerheader_class_controller!' => '', 'selector_header!' => '', 'trackerheader_overlay!' => '']], 'dce_trackerheader_css_note' => ['type' => Controls_Manager::RAW_HTML, 'raw' => __('During the course of the tracker-header, classes will be applied to the wrapper that you can use to change the appearance of the elements from css: <ul><li>.trackerheader--top</li> <li>.trackerheader--pinned </li > <li>.trackerheader--unpinned </li> <li>.trackerheader--bottom </li> </ul> <a href="https://help.dynamic.ooo/en/articles/4952557-tracker-header" target="_blank">This article can help you.</a>', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'before', 'condition' => ['enable_trackerheader' => 'yes', 'dce_trackerheader_class_controller!' => '', 'selector_header!' => '']], 'responsive_trackerheader' => ['label' => __('Apply Tracker Header on', 'dynamic-content-for-elementor'), 'description' => __('Responsive mode will take place on preview or live pages only, not while editing in Elementor.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'separator' => 'before', 'label_block' => \true, 'options' => \array_combine(Helper::get_active_devices_list(), Helper::get_active_devices_list()), 'default' => ['desktop', 'tablet', 'mobile'], 'frontend_available' => \true, 'condition' => ['enable_trackerheader' => 'yes', 'dce_trackerheader_class_controller!' => '', 'selector_header!' => '']]]];
    }
}
