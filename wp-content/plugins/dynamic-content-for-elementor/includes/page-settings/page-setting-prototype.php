<?php

namespace DynamicContentForElementor\PageSettings;

use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
/**
 * Page Setting Prototype
 *
 * Class to easily extend Elementor controls and functionality
 *
 */
class PageSettingPrototype
{
    /**
     * Name
     *
     * @var string
     */
    public $name = 'Document';
    /**
     * Is Common Document
     *
     * Defines if the current document is common for all element types or not
     *
     * @since 0.5.8
     * @access private
     *
     * @var bool
     */
    protected $is_common = \false;
    /**
     * Depended scripts.
     *
     * Holds all the Document depended scripts to enqueue.
     *
     * @since 0.5.8
     * @access private
     *
     * @var array<string>
     */
    private $depended_scripts = [];
    /**
     * Depended styles.
     *
     * Holds all the document depended styles to enqueue.
     *
     * @since 0.5.8
     * @access private
     *
     * @var array<string>
     */
    private $depended_styles = [];
    /**
     * Constructor
     *
     * @since 0.1.0
     * @access public
     */
    public function __construct()
    {
        // Enqueue scripts
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'enqueue_scripts']);
        // Enqueue styles
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        add_action('elementor/preview/enqueue_scripts', function () {
            $this->enqueue_all();
        });
        if ($this->is_common) {
            // Add the advanced section required to display controls
            $this->add_common_sections_actions();
        }
        $this->add_actions();
    }
    /**
     * Add script depends.
     *
     * Register new script to enqueue by the handler.
     *
     * @since 0.5.8
     * @access public
     *
     * @param string $handler Depend script handler.
     * @return void
     */
    public function add_script_depends($handler)
    {
        $this->depended_scripts[] = $handler;
    }
    /**
     * Add style depends.
     *
     * Register new style to enqueue by the handler.
     *
     * @since 0.5.8
     * @access public
     *
     * @param string $handler Depend style handler.
     * @return void
     */
    public function add_style_depends($handler)
    {
        $this->depended_styles[] = $handler;
    }
    /**
     * Get script dependencies.
     *
     * Retrieve the list of script dependencies the document requires.
     *
     * @since 0.5.8
     * @access public
     *
     * @return array<string> Widget scripts dependencies.
     */
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    /**
     * Enqueue scripts.
     *
     * Registers all the scripts defined as document dependencies and enqueues
     * them. Use `get_script_depends()` method to add custom script dependencies.
     *
     * @since 0.5.8
     * @access public
     * @return void
     */
    public final function enqueue_scripts()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->_enqueue_scripts();
        }
    }
    /**
     * Enqueue Scripts
     *
     * @return void
     */
    public function _enqueue_scripts()
    {
        $scripts = $this->get_script_depends();
        if (!empty($scripts)) {
            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
    }
    /**
     * Retrieve style dependencies.
     *
     * Get the list of style dependencies the document requires.
     *
     * @since 0.5.8
     * @access public
     *
     * @return array<string> Widget styles dependencies.
     */
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    /**
     * Enqueue styles.
     *
     * Registers all the styles defined as document dependencies and enqueues
     * them. Use `get_style_depends()` method to add custom style dependencies.
     *
     * @since 0.5.8
     * @access public
     * @return void
     */
    public final function enqueue_styles()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->_enqueue_styles();
        }
    }
    /**
     * Enqueue Styles
     *
     * @return void
     */
    public function _enqueue_styles()
    {
        $styles = $this->get_style_depends();
        if (!empty($styles)) {
            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }
    /**
     * Enqueue All
     *
     * @return void
     */
    public function enqueue_all()
    {
        $this->_enqueue_styles();
        $this->_enqueue_scripts();
    }
    /**
     * Add Common Sections
     *
     * @param \Elementor\Core\Base\Document $element
     * @return void|false
     */
    protected final function add_common_sections($element)
    {
        // The name of the section
        $section_name = 'section_dce_document_scroll';
        // Check if this section exists
        $section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), $section_name);
        if (!is_wp_error($section_exists)) {
            // We can't and should try to add this section to the stack
            return \false;
        }
        $post = $element->get_main_post();
        $post_type = $post->post_type;
        if ($post_type != 'elementor_library') {
            $element->start_controls_section($section_name, ['tab' => Controls_Manager::TAB_SETTINGS, 'label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Page Scroll', 'dynamic-content-for-elementor')]);
            $element->end_controls_section();
        }
    }
    /**
     * Add Common Sections - Actions
     *
     * @return void
     */
    protected function add_common_sections_actions()
    {
    }
    /**
     * Add Actions
     *
     * @return void
     */
    protected function add_actions()
    {
    }
    /**
     * Removes controls in bulk
     *
     * @param \Elementor\Core\Base\Document $element
     * @param mixed $controls
     * @return void
     */
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
}
