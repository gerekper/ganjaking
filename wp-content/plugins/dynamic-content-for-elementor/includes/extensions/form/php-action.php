<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class PhpAction extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    /**
     * @var string
     */
    public $name = 'PHP Action for Elementor Pro Form';
    /**
     * @var array<string>
     */
    public static $depended_plugins = ['elementor-pro'];
    /**
     * @var bool
     */
    public $has_action = \true;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_php_action';
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return __('PHP Action', 'dynamic-content-for-elementor');
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return [];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return [];
    }
    /**
     * @return void
     */
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_php_action_code');
    }
    /**
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     *
     * @return void
     */
    public function run($record, $ajax_handler)
    {
        $code = $record->get_form_settings('dce_php_action_code');
        $raw_fields = $record->get_field([]);
        $fields = [];
        foreach ($raw_fields as $id => $content) {
            $fields[$id] = $content['value'];
        }
        // phpcs:ignore Squiz.PHP.Eval.Discouraged
        eval($code);
    }
    /**
     * @param \ElementorPro\Modules\Forms\Widgets\Form $widget
     * @return void
     */
    public function register_settings_section($widget)
    {
        if (!Helper::can_register_unsafe_controls()) {
            return;
        }
        $widget->start_controls_section('section_dce_php_action', ['label' => '<span class="color-dce icon icon-dyn-logo-dce pull-right ml-1"></span> ' . __('PHP Action', 'dynamic-content-for-elementor'), 'condition' => ['submit_actions' => $this->get_name()]]);
        $widget->add_control('dce_php_action_code', ['label' => __('PHP Action Code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'php', 'default' => '', 'separator' => 'after', 'description' => __('Use the variable $fields to access fields values (eg $fields["field_id"]).', 'dynamic-content-for-elementor')]);
        $widget->end_controls_section();
    }
    public function on_export($element)
    {
        return $element;
    }
}
