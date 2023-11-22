<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Controls\Group_Control_Animation_Element;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicAnimations extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $name = 'Animations';
    public $has_controls = \true;
    protected $is_common = \true;
    private function add_controls($element, $args)
    {
        $element_type = $element->get_type();
        $element->add_control('enabled_animations', ['label' => __('Animations', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes']);
        $element->add_group_control(Group_Control_Animation_Element::get_type(), ['name' => 'animate_image', 'selector' => '{{WRAPPER}} .dce-animations', 'condition' => ['enabled_animations' => 'yes']]);
    }
    protected function add_actions()
    {
        wp_register_style('dce-animations', DCE_URL . 'assets/css/animations.css', [], DCE_VERSION);
        wp_enqueue_style('dce-animations');
        // Activate controls for widgets
        add_action('elementor/element/common/dce_section_animations_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_filter('elementor/widget/print_template', array($this, 'animations_print_template'), 10, 2);
        add_action('elementor/widget/render_content', array($this, 'animations_render_template'), 10, 2);
    }
    public function animations_print_template($content, $widget)
    {
        if (!$content) {
            return '';
        }
        $content = '<# if ( settings.enabled_animations ) { #><div class="dce-animations">' . $content . '</div><# } else { #>' . $content . '<# } #>';
        return $content;
    }
    public function animations_render_template($content, $widget)
    {
        $settings = $widget->get_settings_for_display();
        if ($settings['enabled_animations']) {
            $content = '<div class="dce-animations">' . $content . '</div>';
        }
        return $content;
    }
}
