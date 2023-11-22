<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Utils;
use Elementor\Repeater;
use DynamicContentForElementor\Plugin;
use DynamicContentForElementor\Widgets;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Group_Control_Outline;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
/**
 * WidgetPrototype Base widget class
 *
 * Base class for Dynamic.ooo - Dynamic Content for Elementor
 *
 * @since 0.4.0
 */
abstract class WidgetPrototype extends Widget_Base
{
    /**
     * Settings.
     *
     * Holds the object settings.
     *
     * @access public
     *
     * @var array
     */
    public $settings;
    public $categories;
    public $name;
    public $title;
    public $description;
    public $icon;
    public $plugin_depends = [];
    public $doc_url = 'https://www.dynamic.ooo';
    public $keywords = [];
    public $admin_only;
    /**
     * @var array<string>
     */
    public $tags;
    /**
     * Raw Data.
     *
     * Holds all the raw data including the element type, the child elements,
     * the user data.
     *
     * @access public
     *
     * @var null|array
     */
    public $data;
    public static $info;
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $class = \explode('\\', \get_called_class());
        $class = \array_pop($class);
        $class = \substr(\get_called_class(), 27);
        // remove main namespace prefix
        $info = Plugin::instance()->features->filter(['class' => $class]);
        $info = \reset($info);
        if (isset($info['category'])) {
            $this->categories = $info['category'];
        }
        if (isset($info['name'])) {
            $this->name = $info['name'];
        }
        if (isset($info['title'])) {
            $this->title = $info['title'];
        }
        if (isset($info['description'])) {
            $this->description = $info['description'];
        }
        if (isset($info['icon'])) {
            $this->icon = $info['icon'];
        }
        if (isset($info['plugin_depends'])) {
            $this->plugin_depends = $info['plugin_depends'];
        }
        if (isset($info['doc_url'])) {
            $this->doc_url = $info['doc_url'];
        }
        if (isset($info['keywords'])) {
            $this->keywords = $info['keywords'];
        }
        $this->admin_only = $info['admin_only'] ?? \false;
        $this->tags = $info['tag'] ?? [];
    }
    public function run_once()
    {
        if ($this->admin_only) {
            \DynamicContentForElementor\Plugin::instance()->save_guard->register_unsafe_widget($this->get_name());
        }
    }
    public function get_name()
    {
        return $this->name;
    }
    public function get_title()
    {
        return $this->title;
    }
    public function get_description()
    {
        return $this->description;
    }
    public function get_docs()
    {
        return $this->doc_url;
    }
    public function get_keywords()
    {
        return $this->keywords;
    }
    public function get_help_url()
    {
        return 'https://help.dynamic.ooo';
    }
    public function get_custom_help_url()
    {
        return $this->get_docs();
    }
    public function get_icon()
    {
        return $this->icon;
    }
    /**
     * Is Reload Preview Required
     *
     * @return boolean
     */
    public function is_reload_preview_required()
    {
        return \false;
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['dynamic-content-for-elementor-' . \strtolower($this->categories)];
    }
    public function get_plugin_depends()
    {
        return $this->plugin_depends;
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected final function register_controls()
    {
        if ($this->admin_only && !Helper::can_register_unsafe_controls()) {
            $this->register_controls_non_admin_notice();
        } else {
            $this->safe_register_controls();
        }
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected abstract function safe_register_controls();
    /**
     * Show a notice for non administrators
     *
     * @return void
     */
    protected function register_controls_non_admin_notice()
    {
        $this->start_controls_section('section_non_admin', ['label' => $this->get_title() . __(' - Notice', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $this->end_controls_section();
    }
    public function show_in_panel()
    {
        if ($this->admin_only && !current_user_can('administrator')) {
            return \false;
        }
        return \true;
    }
    /**
     * Render
     *
     * @return void
     */
    protected final function render()
    {
        if ($this->admin_only && !Helper::can_register_unsafe_controls()) {
            $this->render_non_admin_notice();
        }
        if (\in_array('loop', $this->tags, \true)) {
            $this->add_render_attribute('_wrapper', 'class', 'dce-fix-background-loop');
        }
        $this->safe_render();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected abstract function safe_render();
    /**
     * Render Non Admin Notice
     *
     * @return void
     */
    protected function render_non_admin_notice()
    {
        _e('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor');
    }
    /**
     * Content Template
     *
     * @return void
     */
    protected function content_template()
    {
    }
    public final function update_settings($key, $value = null)
    {
        $widget_id = $this->get_id();
        Helper::set_settings_by_id($widget_id, $key, $value);
        $this->set_settings($key, $value);
    }
}
