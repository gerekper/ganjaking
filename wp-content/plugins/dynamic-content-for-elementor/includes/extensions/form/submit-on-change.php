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
class SubmitOnChange extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_onchange';
    }
    public function get_label()
    {
        return __('Onchange', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
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
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_onchange';
            \ob_start();
            ?>
		  <script id="<?php 
            echo $jkey;
            ?>">
			  (function ($) {
			<?php 
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
					  var <?php 
                echo $jkey;
                ?> = function ($scope, $) {
						  if ($scope.hasClass("elementor-element-<?php 
                echo $widget->get_id();
                ?>")) {
				<?php 
            }
            $has_onchange = \false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if (!empty($afield['field_onchange'])) {
                    $has_onchange = \true;
                    ?>
				  jQuery('.elementor-element-<?php 
                    echo $widget->get_id();
                    ?> .elementor-field-group-<?php 
                    echo $afield['custom_id'];
                    ?> input, .elementor-element-<?php 
                    echo $widget->get_id();
                    ?> .elementor-field-group-<?php 
                    echo $afield['custom_id'];
                    ?> select').on('change', function () {
					  var field = jQuery(this).closest('.elementor-field-group');
					  if (field.siblings('.dce-form-step-bnt-next').length) {
						  // step
						  field.siblings('.dce-form-step-bnt-next').find('button').trigger('click');
					  } else {
						  // submit
						  jQuery(this).closest('form').find('.elementor-field-type-submit button').trigger('click');
					  }
				  });
					<?php 
                }
            }
            ?>
						  jQuery('.elementor-element-<?php 
            echo $widget->get_id();
            ?> .select2-selection__arrow').remove();
			<?php 
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
						  }
					  };
					  $(window).on("elementor/frontend/init", function () {
						  elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php 
                echo $jkey;
                ?>);
					  });
		  <?php 
            }
            ?>
			  })(jQuery, window);
		  </script>
			<?php 
            $add_js = \ob_get_clean();
            if ($has_onchange) {
                $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
                return $content . $add_js;
            }
        }
        return $content;
    }
    public function update_fields_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_onchange' => ['name' => 'field_onchange', 'label' => __('Submit on Change', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['field_type' => ['radio', 'select']], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
