<?php

namespace DynamicContentForElementor\Includes\Settings;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class SmoothTransition extends \DynamicContentForElementor\Includes\Settings\DCE_Settings_Prototype
{
    public static $name = 'Smooth Transition';
    public function __construct()
    {
        if (get_option('enable_smoothtransition')) {
            add_filter('body_class', array($this, 'dce_add_class'), 10);
        }
    }
    public function get_name()
    {
        return 'dce-settings_smoothtransition';
    }
    public function get_css_wrapper_selector()
    {
        return 'body.dce-smoothtransition';
    }
    public static function dce_add_class($classes)
    {
        $classes[] = 'dce-smoothtransition';
        if (get_option('smoothtransition_enable_overlay')) {
            $classes[] = 'smoothtransition-overlay';
        }
        return $classes;
    }
    public static function get_controls()
    {
        global $wp_version;
        $wrapper = 'body.dce-smoothtransition';
        $target_smoothTransition = '';
        $selector_wrapper = get_option('selector_wrapper');
        if ($selector_wrapper) {
            $target_smoothTransition = ' ' . $selector_wrapper;
        }
        return ['label' => __('Smooth Transition', 'dynamic-content-for-elementor'), 'controls' => [
            'enable_smoothtransition' => ['label' => __('Smooth Transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'separator' => 'before'],
            'selector_wrapper' => ['label' => __('Selector Wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => \true, 'placeholder' => __('Type CSS selector (e.g.:#wrapper)', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'dynamic' => ['active' => \false], 'condition' => ['enable_smoothtransition' => 'yes']],
            'dce_smoothtransition_class_debug' => ['type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="dce-class-debug">...</div>', 'content_classes' => 'dce_class_debug', 'condition' => ['enable_smoothtransition' => 'yes']],
            'dce_smoothtransition_class_controller' => ['label' => __('Controller', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '', 'frontend_available' => \true, 'selectors' => [$wrapper . $target_smoothTransition => '
						position: relative;
						opacity: 0;
						will-change: opacity;
						-webkit-animation-fill-mode: both;
                      	animation-fill-mode: both;', $wrapper . '.elementor-editor-active' . $target_smoothTransition . ', ' . $wrapper . '.elementor-editor-preview' . $target_smoothTransition => 'opacity: 1;'], 'condition' => ['enable_smoothtransition' => 'yes']],
            'dce_smoothtransition_settings_note' => ['type' => Controls_Manager::RAW_HTML, 'raw' => __('The selector wrapper is very important for the proper functioning of the transitions. It indicates the part of the page that needs to be transformed. <a href="https://help.dynamic.ooo/en/articles/4952536-html-structure-of-themes" target="_blank">This article can help you.</a>', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['enable_smoothtransition' => 'yes']],
            'a_class' => ['label' => __('Target [a href] CLASS', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'label_block' => \true, 'row' => 3, 'default' => 'a:not([target="_blank"]):not([href=""]):not([href^="uploads"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.no-transition):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox):not(.is-lightbox):not(.elementor-icon):not(.download-link):not([href*="elementor-action"]):not(.dialog-close-button):not([data-elementor-open-lightbox="yes"])', 'placeholder' => 'a:not([target="_blank"]):not([href=""]):not([href^="uploads"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.no-transition):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox):not(.is-lightbox):not(.elementor-icon):not(.download-link):not([href*="elementor-action"]):not(.dialog-close-button)', 'frontend_available' => \true, 'separator' => 'before', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            // OUT
            'dce_smoothtransition_animation_out_heading' => ['label' => __('Animation OUT', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'dce_smoothtransition_style_out' => ['label' => __('Style of transition OUT', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'exitToFade', 'groups' => Helper::get_anim_close(), 'frontend_available' => \true, 'selectors' => [$wrapper . $target_smoothTransition . '.dce-anim-style-out' => 'animation-name: {{VALUE}}, fade-out; -webkit-animation-name: {{VALUE}}, fade-out;'], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'smoothtransition_speed_out' => ['label' => __('Speed Out', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 500], 'range' => ['px' => ['min' => 0, 'max' => 2000, 'step' => 10]], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'frontend_available' => \true],
            'smoothtransition_timingFuncion_out' => ['label' => __('Timing function OUT', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'frontend_available' => \true, 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'selectors' => [$wrapper . $target_smoothTransition . '.dce-anim-style-out' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};']],
            // IN
            'dce_smoothtransition_animation_in_heading' => ['label' => __('Animation IN', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'dce_smoothtransition_style_in' => ['label' => __('Style of transition IN', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'enterFromFade', 'groups' => Helper::get_anim_open(), 'selectors' => [$wrapper . $target_smoothTransition . '.dce-anim-style-in' => 'animation-name: {{VALUE}}, fade-in; -webkit-animation-name: {{VALUE}}, fade-in;'], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'smoothtransition_speed_in' => ['label' => __('Speed In', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 500], 'range' => ['px' => ['min' => 0, 'max' => 2000, 'step' => 10]], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'frontend_available' => \true],
            'smoothtransition_timingFuncion_in' => ['label' => __('Timing function IN', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'frontend_available' => \true, 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'selectors' => [$wrapper . $target_smoothTransition . '.dce-anim-style-in' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};']],
            /* OVERLAY */
            'smoothtransition_overlay_heading' => ['type' => Controls_Manager::RAW_HTML, 'raw' => '<strong><i class="fa fa-copy" aria-hidden="true"></i> ' . __('Overlay effect', 'dynamic-content-for-elementor') . '</strong>', 'content_classes' => '', 'separator' => 'before', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'smoothtransition_enable_overlay' => ['label' => __('Use overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'smoothtransition_overlay_style' => ['label' => __('Overlay Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => __('Left', 'dynamic-content-for-elementor'), 'top' => __('Top', 'dynamic-content-for-elementor'), 'bottom' => __('Bottom', 'dynamic-content-for-elementor')], 'condition' => ['enable_smoothtransition' => 'yes', 'smoothtransition_enable_overlay!' => ''], 'selectors' => [$wrapper . '.smoothtransition-overlay:after' => 'animation-name: dce-overlay-out-{{VALUE}}; -webkit-animation-name: dce-overlay-out-{{VALUE}};', $wrapper . '.smoothtransition-overlay.overlay-out:after' => 'animation-name: dce-overlay-in-{{VALUE}}; -webkit-animation-name: dce-overlay-in-{{VALUE}};'], 'frontend_available' => \true],
            'smoothtransition_overlay_color' => ['label' => __('Overlay Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['enable_smoothtransition' => 'yes', 'smoothtransition_enable_overlay!' => ''], 'selectors' => [$wrapper . '.smoothtransition-overlay:after' => 'background-color: {{VALUE}};']],
            'smoothtransition_overlay_image' => ['label' => __('Overlay Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => Utils::get_placeholder_image_src()], 'condition' => ['enable_smoothtransition' => 'yes', 'smoothtransition_enable_overlay!' => ''], 'selectors' => [$wrapper . '.smoothtransition-overlay:after', $wrapper . '.smoothtransition-overlay.overlay-out:after' => 'background-image: url({{URL}});']],
            'smoothtransition_loading_heading' => ['type' => Controls_Manager::RAW_HTML, 'raw' => '<strong><i class="fa fa-spinner" aria-hidden="true"></i> ' . __('Loading Spin', 'dynamic-content-for-elementor') . '</strong>', 'content_classes' => '', 'separator' => 'before', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
            'smoothtransition_debug_loading' => ['label' => __('Enable loading on editor mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('ON', 'dynamic-content-for-elementor'), 'label_on' => __('OFF', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'default' => '', 'frontend_available' => \true, 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'selectors' => [$wrapper . '.elementor-editor-active' . $target_smoothTransition . ', ' . $wrapper . '.elementor-editor-preview' . $target_smoothTransition => 'opacity: 1;', $wrapper . '.elementor-editor-active .animsition-loading, ' . $wrapper . '.elementor-editor-preview .animsition-loading' => 'display: none;']],
            'smoothtransition_loading_mode' => ['label' => __('Loading Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['circle' => ['title' => __('Circle', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle-o-notch'], 'image' => ['title' => __('Image', 'dynamic-content-for-elementor'), 'icon' => 'eicon-image'], 'none' => ['title' => __('None', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'render_type' => 'template', 'default' => 'circle', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'frontend_available' => \true],
            'smoothtransition_loading_style' => ['label' => __('Loading Animation Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'fade', 'options' => ['rotate' => __('Rotate', 'dynamic-content-for-elementor'), 'pulse' => __('Pulse', 'dynamic-content-for-elementor'), 'fade' => __('Fade', 'dynamic-content-for-elementor'), 'none' => __('None', 'dynamic-content-for-elementor')], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '', 'smoothtransition_loading_mode' => 'image'], 'frontend_available' => \true],
            'smoothtransition_loading_image' => ['label' => __('Loading Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => Utils::get_placeholder_image_src()], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '', 'smoothtransition_loading_mode' => 'image'], 'selectors' => [$wrapper . ' .animsition-loading.loading-mode-image' => 'background-image: url({{URL}});']],
            'smoothtransition_loading_color_circle' => ['label' => __('Circle Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '', 'smoothtransition_loading_mode' => 'circle'], 'selectors' => [$wrapper . ' .animsition-loading' => 'border-top-color: {{VALUE}}; border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}};']],
            'smoothtransition_loading_color_progress' => ['label' => __('Circle Progress Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '', 'smoothtransition_loading_mode' => 'circle'], 'selectors' => [$wrapper . ' .animsition-loading' => 'border-left-color: {{VALUE}};']],
            'smoothtransition_loading_size' => ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 32], 'size_units' => ['px', 'vw', 'vh'], 'range' => ['px' => ['min' => 0, 'max' => 500, 'step' => 1], 'vw' => ['min' => 0, 'max' => 100, 'step' => 1], 'vh' => ['min' => 0, 'max' => 100, 'step' => 1]], 'condition' => ['smoothtransition_loading_mode!' => 'none', 'enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => ''], 'selectors' => [$wrapper . ' .animsition-loading' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2);']],
            'smoothtransition_loading_extendimage' => ['label' => __('Extend image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '', 'smoothtransition_loading_mode' => 'image'], 'selectors' => [$wrapper . ' .animsition-loading.loading-mode-image' => 'width: 100%; height: 100%; margin: 0; top: 0; left: 0; background-size: cover;']],
            'smoothtransition_loading_weight' => ['label' => __('Circle Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'range' => ['px' => ['min' => 1, 'max' => 50, 'step' => 1]], 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '', 'smoothtransition_loading_mode' => 'circle'], 'selectors' => [$wrapper . ' .animsition-loading' => 'border-width: {{SIZE}}{{UNIT}};']],
            'responsive_smoothtransition' => ['label' => __('Apply Smooth Transition on:', 'dynamic-content-for-elementor'), 'description' => __('Responsive mode will take place on preview or live pages only, not while editing in Elementor.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'separator' => 'before', 'label_block' => \true, 'options' => \array_combine(Helper::get_active_devices_list(), Helper::get_active_devices_list()), 'default' => ['desktop', 'tablet', 'mobile'], 'frontend_available' => \true, 'render_type' => 'template', 'condition' => ['enable_smoothtransition' => 'yes', 'dce_smoothtransition_class_controller!' => '']],
        ]];
    }
}
