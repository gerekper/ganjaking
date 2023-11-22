<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class ExtensionPrototype
{
    public $name = 'Extension';
    public static $docs = 'https://www.dynamic.ooo';
    public $has_controls = \false;
    private $is_common = \true;
    private $depended_scripts = [];
    private $depended_styles = [];
    public static $depended_plugins = [];
    private $actions_added = \false;
    public $common_sections_actions = array(array('element' => 'common', 'action' => '_section_style'));
    public function __construct()
    {
        // Enqueue scripts
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'enqueue_scripts']);
        // Enqueue styles
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        if ($this->is_common()) {
            // Add the advanced section required to display controls
            $this->add_common_sections_actions();
        }
        if (!$this->actions_added) {
            $this->actions_added = \true;
            $this->add_actions();
        }
    }
    public function get_docs()
    {
        return self::$docs;
    }
    public static function get_satisfy_dependencies($ret = \false)
    {
        $widgetClass = \get_called_class();
        return $widgetClass::satisfy_dependencies($ret);
    }
    public static function get_plugin_depends()
    {
        return self::$depended_plugins;
    }
    public static function satisfy_dependencies($ret = \false, $deps = array())
    {
        if (empty($deps)) {
            $deps = self::get_plugin_depends();
        }
        $depsDisabled = array();
        if (!empty($deps)) {
            $isActive = \true;
            foreach ($deps as $pkey => $plugin) {
                if (!\is_numeric($pkey)) {
                    if (!Helper::is_plugin_active($pkey)) {
                        $isActive = \false;
                    }
                } else {
                    if (!Helper::is_plugin_active($plugin)) {
                        $isActive = \false;
                    }
                }
                if (!$isActive) {
                    if (!$ret) {
                        return \false;
                    }
                    $depsDisabled[] = $pkey;
                }
            }
        }
        if ($ret) {
            return $depsDisabled;
        }
        return \true;
    }
    public function add_script_depends($handler)
    {
        $this->depended_scripts[] = $handler;
    }
    public function add_style_depends($handler)
    {
        $this->depended_styles[] = $handler;
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function enqueue_scripts()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->_enqueue_scripts();
        }
    }
    public function _enqueue_scripts()
    {
        $scripts = $this->get_script_depends();
        if (!empty($scripts)) {
            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public static function get_description()
    {
        return '';
    }
    public final function enqueue_styles()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->_enqueue_styles();
        }
    }
    public function _enqueue_styles()
    {
        $styles = $this->get_style_depends();
        if (!empty($styles)) {
            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }
    public function enqueue_all()
    {
        $this->_enqueue_styles();
        $this->_enqueue_scripts();
    }
    /**
     * @return string
     */
    public function get_id()
    {
        $low_name = \strtolower($this->name);
        $low_name = \str_replace(' ', '_', $low_name);
        return $low_name;
    }
    public final function add_common_sections($element, $args)
    {
        $low_name = $this->get_id();
        $section_name = 'dce_section_' . $low_name . '_advanced';
        if (!$this->has_controls) {
            // no need settings
            return \false;
        }
        // Check if this section exists
        $section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), $section_name);
        if (!is_wp_error($section_exists)) {
            // We can't and should try to add this section to the stack
            return \false;
        }
        $this->get_control_section($section_name, $element);
    }
    public function get_control_section($section_name, $element)
    {
        $element->start_controls_section($section_name, ['tab' => Controls_Manager::TAB_ADVANCED, 'label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . $this->name]);
        $element->end_controls_section();
    }
    public function add_common_sections_actions()
    {
        foreach ($this->common_sections_actions as $action) {
            // Activate action for elements
            add_action('elementor/element/' . $action['element'] . '/' . $action['action'] . '/after_section_end', function ($element, $args) {
                $this->add_common_sections($element, $args);
            }, 10, 2);
        }
    }
    protected function add_actions()
    {
    }
    protected function remove_controls($element, $controls = null)
    {
        if (empty($controls)) {
            return;
        }
        if (\is_array($controls)) {
            $control_id = $controls;
            foreach ($controls as $control_id) {
                $element->remove_control($control_id);
            }
        } else {
            $element->remove_control($controls);
        }
    }
    public function is_common()
    {
        return $this->is_common;
    }
    /**
     * Register tags.
     *
     * Add all the available dynamic tags.
     *
     * @since 2.0.0
     * @access public
     *
     */
    public function add_dynamic_tag($class_name)
    {
        add_action('elementor/dynamic_tags/register', function ($dynamic_tags) use($class_name) {
            // To register that group as well before the tag
            $tags_config = \Elementor\Plugin::$instance->dynamic_tags->get_config();
            if (!isset($tags_config['groups']['dce'])) {
                \Elementor\Plugin::$instance->dynamic_tags->register_group('dce', ['title' => DCE_PRODUCT_NAME]);
            }
            if (!isset($tags_config['groups']['dce-dynamic-google-maps-directions'])) {
                \Elementor\Plugin::$instance->dynamic_tags->register_group('dce-dynamic-google-maps-directions', ['title' => DCE_PRODUCT_NAME . ' - Dynamic Google Maps Directions']);
            }
            $class_name = '\\DynamicContentForElementor\\Modules\\DynamicTags\\Tags\\' . $class_name;
            $dynamic_tags->register(new $class_name());
        });
    }
}
