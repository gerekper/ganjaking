<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Unwrap extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $name = 'Unwrap';
    public $has_controls = \true;
    public $common_sections_actions = array(array('element' => 'common', 'action' => '_section_style'), array('element' => 'column', 'action' => 'section_advanced'), array('element' => 'section', 'action' => 'section_advanced'));
    private function add_controls($element, $args)
    {
        $element->add_control('dce_unwrap', ['label' => __('Unwrap Element', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $element->add_control('dce_unwrap_warning', ['raw' => '<strong>' . __('Please note!', 'dynamic-content-for-elementor') . '</strong> ' . __('Removing the wrappers could disable all styles and javascript features on the element', 'dynamic-content-for-elementor') . '<br><a target="_blank" href="https://help.dynamic.ooo/en/articles/4952484-unwrap">' . __('Read how and why you should use it', 'dynamic-content-for-elementor') . '</a>', 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'render_type' => 'ui', 'condition' => ['dce_unwrap!' => '']]);
        $element->add_control('dce_unwrap_style', ['label' => __('Try to force Element Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['dce_unwrap!' => '']]);
        $element->add_control('dce_unwrap_strip', ['label' => __('Strip selected Tags', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'div, p, span', 'condition' => ['dce_unwrap!' => '']]);
    }
    protected function add_actions()
    {
        // Activate sections for document
        add_action('elementor/documents/register_controls', function ($element) {
            // The name of the section
            $section_name = 'dce_section_unwrap_advanced';
            // Check if this section exists
            $section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), $section_name);
            if (!is_wp_error($section_exists)) {
                // We can't and should try to add this section to the stack
                return;
            }
            $element->start_controls_section($section_name, ['tab' => Controls_Manager::TAB_ADVANCED, 'label' => __('Unwrap', 'dynamic-content-for-elementor')]);
            $element->end_controls_section();
        }, 10, 1);
        // Activate controls for elements
        add_action('elementor/element/wp-post/dce_section_unwrap_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/element/wp-page/dce_section_unwrap_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/element/page/dce_section_unwrap_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/element/section/dce_section_unwrap_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/element/column/dce_section_unwrap_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/element/common/dce_section_unwrap_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/frontend/before_render', array($this, 'start_element'), 10, 2);
        add_action('elementor/frontend/after_render', array($this, 'end_element'));
    }
    public function start_element($element = \false, $template_id = 0)
    {
        $settings = $element->get_settings_for_display();
        if (!empty($settings['dce_unwrap'])) {
            \ob_start();
        }
        return $element;
    }
    public function end_element($element = \false)
    {
        $settings = $element->get_settings_for_display();
        if (empty($settings['dce_unwrap'])) {
            return;
        }
        $id = 0;
        $template = \Elementor\Plugin::$instance->documents->get_current();
        if (!$template) {
            return;
        }
        if (\is_object($element)) {
            $type = $element->get_type();
            $name = $element->get_name();
            $id = $element->get_id();
            $unwrap_element = $element;
        } else {
            $type = 'template';
            $unwrap_element = $template;
            $id = $template->get_main_id();
        }
        if ($id) {
            $settings = $unwrap_element->get_settings_for_display();
            if (!empty($settings['dce_unwrap'])) {
                if ($type == 'template') {
                    $content = $element;
                } else {
                    $content = \ob_get_clean();
                }
                if (!empty($content)) {
                    if ($template) {
                        $template_id = $template->get_main_id();
                    } else {
                        $template_id = $id;
                    }
                    if ($type == 'template') {
                        $content_unwrapped = $content;
                    } else {
                        $content_unwrapped = '<div class="elementor-' . $template_id . '">' . $content . '</div>';
                    }
                    if ($settings['dce_unwrap_style']) {
                        $css = Helper::get_post_css($template_id);
                        $cssToInlineStyles = new \DynamicOOOS\TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
                        $content_unwrapped = $cssToInlineStyles->convert($content_unwrapped, $css);
                        list($tmp, $content_unwrapped) = \explode('<body>', $content_unwrapped, 2);
                        list($content_unwrapped, $tmp) = \explode('</body>', $content_unwrapped, 2);
                    }
                    switch ($type) {
                        case 'template':
                            list($tmp, $content_unwrapped) = \explode('elementor-section-wrap', $content_unwrapped, 2);
                            list($tmp, $content_unwrapped) = \explode('>', $content_unwrapped, 2);
                            for ($i = 0; $i < 3; $i++) {
                                $pos = \strrpos($content_unwrapped, '</div>');
                                if ($pos !== \false) {
                                    $content_unwrapped = \substr_replace($content_unwrapped, '', $pos, \strlen('</div>'));
                                }
                            }
                            break;
                        case 'section':
                            $settings['html_tag'] = !empty($settings['html_tag']) ? \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']) : 'section';
                            list($tmp, $content_unwrapped) = \explode('elementor-row', $content_unwrapped, 2);
                            list($tmp, $content_unwrapped) = \explode('>', $content_unwrapped, 2);
                            $pos = \strrpos($content_unwrapped, '</' . \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']) . '>');
                            if ($pos !== \false) {
                                $content_unwrapped = \substr_replace($content_unwrapped, '', $pos, \strlen('</' . \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']) . '>'));
                            }
                            for ($i = 0; $i < 3; $i++) {
                                $pos = \strrpos($content_unwrapped, '</div>');
                                if ($pos !== \false) {
                                    $content_unwrapped = \substr_replace($content_unwrapped, '', $pos, \strlen('</div>'));
                                }
                            }
                            break;
                        case 'column':
                            list($tmp, $content_unwrapped) = \explode('elementor-widget-wrap', $content_unwrapped, 2);
                            list($tmp, $content_unwrapped) = \explode('>', $content_unwrapped, 2);
                            for ($i = 0; $i < 4; $i++) {
                                $pos = \strrpos($content_unwrapped, '</div>');
                                if ($pos !== \false) {
                                    $content_unwrapped = \substr_replace($content_unwrapped, '', $pos, \strlen('</div>'));
                                }
                            }
                            break;
                        case 'widget':
                            list($tmp, $content_unwrapped) = \explode('elementor-widget-container', $content_unwrapped, 2);
                            $tmp = \explode('<', $tmp);
                            \array_pop($tmp);
                            $pre = \implode('<', $tmp);
                            list($tmp, $content_unwrapped) = \explode('>', $content_unwrapped, 2);
                            for ($i = 0; $i < 3; $i++) {
                                $pos = \strrpos($content_unwrapped, '</div>');
                                if ($pos !== \false) {
                                    $content_unwrapped = \substr_replace($content_unwrapped, '', $pos, \strlen('</div>'));
                                }
                            }
                            break;
                    }
                    if ($settings['dce_unwrap_strip']) {
                        $tags = Helper::str_to_array(',', $settings['dce_unwrap_strip']);
                        if (!empty($tags)) {
                            foreach ($tags as $atag) {
                                $content_unwrapped = Helper::strip_tag($content_unwrapped, $atag);
                            }
                        }
                    }
                    if ($type == 'template') {
                        return $content_unwrapped;
                    }
                    echo $content_unwrapped;
                }
            }
        }
        return $element;
    }
}
