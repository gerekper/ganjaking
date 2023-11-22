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
class PasswordVisibility extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_password_visibility';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Password Visibility', 'dynamic-content-for-elementor');
    }
    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions()
    {
        // TODO Change hook to render_field
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
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_psw';
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
							if ($scope.data('dce-psw-set') === 'yes') return;
							$scope.data('dce-psw-set', 'yes');
				<?php 
            }
            $has_psw = \false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'password') {
                    if (!empty($afield['field_psw_visiblity'])) {
                        $has_psw = \true;
                        ?>
						jQuery('.elementor-element-<?php 
                        echo $widget->get_id();
                        ?> #form-field-<?php 
                        echo $afield['custom_id'];
                        ?>').addClass('dce-form-password-toggle');
						<?php 
                    }
                }
            }
            if ($has_psw) {
                wp_enqueue_style('font-awesome');
                ?>
				jQuery('.elementor-element-<?php 
                echo $widget->get_id();
                ?> .dce-form-password-toggle').each(function () {
					jQuery(this).wrap('<div class="dce-field-input-wrapper dce-field-input-wrapper-<?php 
                echo $afield['custom_id'];
                ?>"></div>');
					jQuery(this).parent().append('<span class="fa far fa-eye-slash field-icon dce-toggle-password"></span>');
					jQuery(this).next('.dce-toggle-password').on('click', function () {
						var input_psw = jQuery(this).prev();
						if (input_psw.attr('type') == 'password') {
							input_psw.attr('type', 'text');
						} else {
							input_psw.attr('type', 'password');
						}
						jQuery(this).toggleClass('fa-eye').toggleClass('fa-eye-slash');
					});
				});
				<?php 
            }
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
            if ($has_psw) {
                $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
                return $content . $add_js;
            }
        }
        return $content;
    }
    public function update_fields_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_psw_visiblity' => ['name' => 'field_psw_visiblity', 'label' => __('Password Visibility', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'true', 'separator' => 'before', 'default' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'password']]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
