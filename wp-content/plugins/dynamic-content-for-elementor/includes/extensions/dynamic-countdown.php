<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicCountdown extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public function get_script_depends()
    {
        return ['dce-dynamic-countdown', 'dce-dayjs'];
    }
    protected function add_actions()
    {
        add_action('elementor/element/countdown/section_countdown/before_section_end', [$this, 'add_dynamic_countdown'], 10, 2);
        add_action('elementor/frontend/widget/before_render', [$this, 'render_countdown'], 1, 1);
    }
    public function add_dynamic_countdown($element, $args)
    {
        $element->add_control('dynamic_due_date', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Dynamic Due Date', 'dynamic-content-for-elementor'), 'description' => __('This field, if not empty, overwrites the due date value. This value is shown only on frontend mode. It should be in the format "Y-m-d H:i:s"', 'dynamic-content-for-elementor'), 'label_block' => \true, 'separator' => 'before', 'placeholder' => 'Y-m-d H:i:s', 'frontend_available' => \true, 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'condition' => ['countdown_type' => 'due_date']]);
    }
    public function render_countdown($element)
    {
        if ('countdown' === $element->get_name()) {
            $this->enqueue_all();
        }
    }
}
