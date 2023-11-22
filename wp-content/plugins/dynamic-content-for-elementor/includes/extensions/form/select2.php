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
class Select2 extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_select2';
    }
    public function get_label()
    {
        return __('Select2', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_field_style/before_section_end', [$this, 'update_style_controls']);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        if (!is_admin()) {
            wp_register_script('jquery-elementor-select2', ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full.min.js', ['jquery'], '4.0.6-rc.1', \true);
            wp_register_style('elementor-select2', ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2.min.css', [], '4.0.6-rc.1');
            wp_register_style('font-awesome', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/font-awesome.min.css', [], '4.7.0');
            wp_register_style('fontawesome', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/fontawesome.min.css', [], '5.9.0');
        }
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_select2';
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
            $has_select2 = \false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'select') {
                    if (!empty($afield['field_select2'])) {
                        $has_select2 = \true;
                        ?>
					if (jQuery.fn.select2) {
						var field2 = jQuery('.elementor-element-<?php 
                        echo $widget->get_id();
                        ?> #form-field-<?php 
                        echo $afield['custom_id'];
                        ?>');
						field2.addClass('dce-ext-select2');
						let form = $scope.find('form')[0];
						field2.on('select2:select', () => {
							let evtChange = document.createEvent("HTMLEvents");
							evtChange.initEvent("change", false, true);
							let evtInput = document.createEvent("HTMLEvents");
							evtInput.initEvent("input", false, true);
							form.dispatchEvent(evtChange);
							form.dispatchEvent(evtInput);
						});
						var classes = field2.attr('class');
						var $select2 = field2.select2({
							//containerCssClass: classes,
						<?php 
                        if (!empty($afield['field_select2_placeholder'])) {
                            ?>placeholder: <?php 
                            echo \json_encode($afield['field_select2_placeholder']);
                            ?>,<?php 
                        }
                        ?>
									});
									$select2.data('select2').$container.find('.select2-selection').addClass(classes);
								}
						<?php 
                    }
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
            if ($has_select2) {
                $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
                wp_enqueue_script('jquery-elementor-select2');
                wp_enqueue_style('elementor-select2');
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
        $control_data['fields']['field_select2'] = array('name' => 'field_select2', 'label' => __('Select2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'return_value' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'select']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
        $control_data['fields']['field_select2_placeholder'] = array('name' => 'field_select2_placeholder', 'label' => __('Placeholder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'select'], ['name' => 'field_select2', 'value' => 'true']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
        $widget->update_control('form_fields', $control_data);
    }
    public function update_style_controls($widget)
    {
        Helper::update_elementor_control($widget, 'field_background_color', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'background-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'background-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .mce-panel'] = 'background-color: {{VALUE}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_text_color', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = 'color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} ..select2-container--default .select2-selection--multiple .select2-selection__rendered'] = 'color: {{VALUE}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_typography', function ($control_data) {
            if (!empty($control_data['selectors'])) {
                $values = \reset($control_data['selectors']);
                $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = $values;
                $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = $values;
                $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single, {{WRAPPER}} .select2-container--default .select2-selection--multiple'] = 'height: auto;';
            }
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_border_color', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-color: {{VALUE}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-color: {{VALUE}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_border_width', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            return $control_data;
        });
        Helper::update_elementor_control($widget, 'field_border_radius', function ($control_data) {
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
            return $control_data;
        });
    }
}
