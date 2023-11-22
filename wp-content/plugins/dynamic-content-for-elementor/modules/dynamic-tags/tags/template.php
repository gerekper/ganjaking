<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Template extends Tag
{
    public function get_name()
    {
        return 'dce-template';
    }
    public function get_title()
    {
        return __('Template', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return ['base', 'text'];
    }
    public function get_docs()
    {
        return 'https://www.dynamic.ooo/widget/dynamic-tag-template/';
    }
    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('dce_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library']);
        $this->add_control('dce_template_new', ['type' => Controls_Manager::RAW_HTML, 'raw' => '<a class="elementor-button elementor-button-block elementor-field-textual elementor-size-sm elementor-button-success" href="' . admin_url('edit.php?post_type=elementor_library#add_new') . '" target="_blank"><i class="eicon-plus"></i> ' . __('Add new template', 'dynamic-content-for-elementor') . '</a>', 'condition' => ['dce_template' => '']]);
        $this->add_control('dce_template_post_id', ['label' => __('Post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Force Post content', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['dce_template!' => '']]);
        $this->add_control('dce_template_user_id', ['label' => __('User', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Force User content', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'users', 'condition' => ['dce_template!' => '']]);
        $this->add_control('dce_template_author_id', ['label' => __('Author', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Force Author content', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'users', 'condition' => ['dce_template!' => '']]);
        $this->add_control('dce_template_inline_css', ['label' => __('Inline CSS', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_template!' => '']]);
        $this->add_control('dce_template_help', ['type' => Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
    }
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (!empty($settings['dce_template'])) {
            $template = '[dce-elementor-template id="' . $settings['dce_template'] . '"';
            if ($settings['dce_template_post_id']) {
                $template .= ' post_id="' . $settings['dce_template_post_id'] . '"';
            }
            if ($settings['dce_template_user_id']) {
                $template .= ' user_id="' . $settings['dce_template_user_id'] . '"';
            }
            if ($settings['dce_template_author_id']) {
                $template .= ' author_id="' . $settings['dce_template_author_id'] . '"';
            }
            if ($settings['dce_template_inline_css']) {
                $template .= ' inlinecss="true"';
            }
            $template .= ']';
            echo do_shortcode($template);
        }
    }
}
