<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Method extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_method';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Method', 'dynamic-content-for-elementor');
    }
    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_options/after_section_start', [$this, 'add_controls_to_form']);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            if (!empty($settings['form_method']) && $settings['form_method'] != 'ajax') {
                foreach ($settings['form_fields'] as $key => $afield) {
                    $content = \str_replace('form_fields[' . $afield['custom_id'] . ']', $afield['custom_id'], $content);
                }
                if ($settings['form_method'] == 'get') {
                    $content = \str_replace('method="post"', 'method="' . $settings['form_method'] . '"', $content);
                }
                if (!empty($settings['form_action']['url'])) {
                    $content = \str_replace('<form ', '<form action="' . $settings['form_action']['url'] . '" ', $content);
                } else {
                    $content = \str_replace('<form ', '<form action="" ', $content);
                    // current page
                }
                if ($settings['form_action']['custom_attributes']) {
                    $attr_str = '';
                    $attrs = Helper::str_to_array(',', $settings['form_action']['custom_attributes']);
                    if (!empty($attrs)) {
                        foreach ($attrs as $anattr) {
                            list($attr, $value) = \explode('|', $anattr, 2);
                            $attr_str .= $attr . '="' . $value . '" ';
                        }
                    }
                    if ($attr_str) {
                        $content = \str_replace('<form ', '<form ' . $attr_str, $content);
                    }
                }
                if (!empty($settings['form_action']['is_external'])) {
                    $content = \str_replace('<form ', '<form target="_blank" ', $content);
                }
                if (!empty($settings['form_action']['nofollow'])) {
                    $content = \str_replace('<form ', '<form rel="nofollow" ', $content);
                }
                $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_action';
                // DOMContentLoaded is needed in case jQuery loading is
                // deferred. This should really be in its own script:
                $add_js = "<script id='{$jkey}'>document.addEventListener('DOMContentLoaded', function() {(function (\$) {";
                $add_js .= "const wid = 'elementor-element-{$widget->get_id()}';";
                $add_js .= <<<'END'
							const stopForm  = function ($scope, $) {
								if (! $scope.hasClass(wid)) {
									return;
								}
								let $submit = $scope.find('button[type="submit"]');
								$submit.on('click', (event) => {
										event.stopImmediatePropagation();
										$form = $scope.find('form').first();
										$form.off();
										if ($form[0].checkValidity()) {
											$form.submit();
										}
								})
							};
							$(window).on("elementor/frontend/init", function () {
								elementorFrontend.hooks.addAction("frontend/element_ready/form.default", stopForm);
							});
			})(jQuery)});
				</script>
END;
                $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
                return $content . $add_js;
            }
        }
        return $content;
    }
    public function add_controls_to_form($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $widget->add_control('form_method', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Method', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ajax' => __('AJAX (Default)', 'dynamic-content-for-elementor'), 'post' => 'POST', 'get' => 'GET'], 'toggle' => \false, 'default' => 'ajax']);
        $widget->add_control('form_action_hide', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('Using this method, all form Actions After Submit, validations, conditional fields and saving signature will not work!', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['form_method!' => 'ajax']]);
        $widget->add_control('form_action', ['label' => __('Action', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'condition' => ['form_method!' => 'ajax']]);
    }
}
