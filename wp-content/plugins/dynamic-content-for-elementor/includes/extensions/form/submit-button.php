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
class SubmitButton extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public function __construct()
    {
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        add_filter('elementor_pro/forms/render/item/submit', [$this, 'remove_label']);
        parent::__construct();
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return __('Submit', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'submit';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function render($item, $item_index, $form)
    {
        // Remove default class 'elementor-field' else it will conflict with the class 'elementor-button'
        $form->remove_render_attribute('input' . $item_index, 'class', 'elementor-field');
        $form->add_render_attribute('input' . $item_index, 'class', 'elementor-button');
        if (!empty($item['button_size'])) {
            $form->add_render_attribute('input' . $item_index, 'class', 'elementor-size-' . $item['button_size']);
        }
        if (!empty($item['button_type'])) {
            $form->add_render_attribute('input' . $item_index, 'class', 'elementor-button-' . $item['button_type']);
        }
        if (!empty($item['button_hover_animation'])) {
            $form->add_render_attribute('input' . $item_index, 'class', 'elementor-animation-' . $item['button_hover_animation']);
        }
        ?>

		<button <?php 
        $form->print_render_attribute_string('input' . $item_index);
        ?> >
			<span>
			<?php 
        if (!empty($item['field_icon'])) {
            ?>
				<span class="elementor-align-icon-left elementor-button-icon">
					<?php 
            Icons_Manager::render_icon($item['field_icon'], ['aria-hidden' => 'true']);
            ?>
				</span>
				<?php 
        }
        // Submit Text contains the label value
        if (!empty($item['submit_text'])) {
            ?>
				<span class="elementor-button-text"><?php 
            echo $item['submit_text'];
            ?></span>
			<?php 
        } else {
            ?>
				<span class="elementor-button-text"><?php 
            _e('Submit', 'dynamic-content-for-elementor');
            ?></span>
			<?php 
        }
        ?>
			</span>
		</button>
		<?php 
    }
    public function update_fields_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['button_size' => ['name' => 'button_size', 'label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function remove_label($item)
    {
        $item['submit_text'] = $item['field_label'] ?? '';
        $item['field_label'] = '';
        return $item;
    }
}
