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
class ConditionalFieldsOldVersion extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    public static function get_plugin_depends()
    {
        return self::$depended_plugins;
    }
    public static function get_satisfy_dependencies($ret = \false)
    {
        return \true;
    }
    public function get_name()
    {
        return 'dce_form_visibility';
    }
    public function get_label()
    {
        return __('Field Condition', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_form_button_css_id/before_section_end', [$this, 'update_button_controls']);
        add_action('elementor_pro/forms/validation', array($this, '_validate_form'), 15, 2);
    }
    public function _validate_form($record, $ajax_handler)
    {
        // reset form validation
        $ajax_handler->errors = [];
        $ajax_handler->messages['errors'] = [];
        $ajax_handler->set_success(\true);
        $settings = $record->get('form_settings');
        $field_settings = array();
        foreach ($settings['form_fields'] as $afield) {
            $field_settings[$afield['custom_id']] = $afield;
        }
        $visible_step = \true;
        foreach ($record->get('fields') as $id => $field) {
            $field_type = $field['type'];
            if ('step' == $field_type) {
                $visible_step = \true;
                if (isset($field_settings[$id]['dce_field_visibility_mode']) && $field_settings[$id]['dce_field_visibility_mode'] != 'visible') {
                    $visible_step = \false;
                    // TODO: more controls with specific values
                }
            }
            if (!empty($field['required']) && '' === $field['value'] && !\in_array($field_type, array('upload', 'step')) && $visible_step || \in_array($field_type, array('recaptcha', 'recaptcha_v3'))) {
                // ADD CONDITIONAL VERIFICATION
                if (empty($field_settings[$id]['dce_field_visibility_mode']) || $field_settings[$id]['dce_field_visibility_mode'] == 'visible') {
                    $ajax_handler->add_error($id, \ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::FIELD_REQUIRED, $settings));
                }
            }
            /**
             * Elementor form field validation.
             *
             * Fires when a single form field is being validated.
             *
             * It allows developers to validate individual field types.
             *
             * The dynamic portion of the hook name, `$field_type`, refers to the field type.
             *
             * @since 2.0.0
             *
             * @param array        $field        Form field.
             * @param Form_Record  $this         An instance of the form record.
             * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
             */
            do_action("elementor_pro/forms/validation/{$field_type}", $field, $record, $ajax_handler);
        }
    }
    public function _render_form($content, $widget)
    {
        $js = '';
        $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_visibility';
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                \ob_start();
                // add custom js
                if (!empty($settings['form_fields'])) {
                    $conditions = array();
                    // BUTTON
                    if (!\in_array($settings['dce_field_visibility_mode'], array('visible', 'hidden'))) {
                        if (empty($settings['button_css_id'])) {
                            $settings['button_css_id'] = 'form-btn-submit-' . $widget->get_id();
                            $content = \str_replace('type="submit"', 'type="submit" id="' . $settings['button_css_id'] . '"', $content);
                        }
                        $conditions[$settings['button_css_id']] = array('dce_field_visibility_mode' => $settings['dce_field_visibility_mode'], 'dce_field_visibility_multiple' => $settings['dce_field_visibility_multiple'], 'dce_field_visibility_operator' => $settings['dce_field_visibility_operator'], 'dce_field_visibility_field' => $settings['dce_field_visibility_field'], 'dce_field_visibility_field_multiple' => $settings['dce_field_visibility_field_multiple'], 'dce_field_visibility_value' => $settings['dce_field_visibility_value'], 'dce_field_visibility_value_multiple' => $settings['dce_field_visibility_value_multiple'], 'dce_field_visibility_render' => $settings['dce_field_visibility_render'], 'dce_field_visibility_multiple_logic' => $settings['dce_field_visibility_multiple_logic'], 'element_css_id' => $settings['button_css_id'], 'element_css_group' => '.elementor-element-' . $widget->get_id() . ' .elementor-field-group.elementor-field-type-submit', 'field_type' => 'button');
                    }
                    // FIELDS
                    $form_fields = array();
                    foreach ($settings['form_fields'] as $key => $afield) {
                        $custom_id = $afield['custom_id'];
                        $form_fields[$afield['custom_id']] = $afield;
                        $afield['element_css_id'] = 'form-field-' . $custom_id;
                        $afield['element_css_group'] = '.elementor-element-' . $widget->get_id() . ' .elementor-field-group-' . $custom_id;
                        $conditions[$custom_id] = $afield;
                    }
                    foreach ($conditions as $key => $afield) {
                        $disabled_end = $afield['dce_field_visibility_mode'] == 'show' ? 'false' : 'true';
                        $disabled_init = $afield['dce_field_visibility_mode'] == 'show' ? 'true' : 'false';
                        $display_init = $afield['dce_field_visibility_mode'] == 'show' ? 'hide' : 'show';
                        $field_value_selector = \false;
                        $element_css_id = '.elementor-element-' . $widget->get_id() . ' #' . $afield['element_css_id'];
                        $display_condition = '';
                        if (isset($afield['field_type'])) {
                            $field_type = $afield['field_type'];
                            switch ($field_type) {
                                case 'radio':
                                case 'checkbox':
                                    $element_css_id = '.elementor-element-' . $widget->get_id() . ' .elementor-field-type-' . $field_type . '.elementor-field-group-' . $afield['custom_id'] . ' input[type=' . $field_type . ']';
                                    break;
                                case 'acceptance':
                                    $element_css_id = '.elementor-element-' . $widget->get_id() . ' .elementor-field-type-' . $field_type . '.elementor-field-group-' . $afield['custom_id'] . ' input[type=checkbox]';
                                    break;
                            }
                        }
                        if ($afield['dce_field_visibility_mode'] != 'visible') {
                            if ((empty($afield['dce_field_visibility_multiple']) || $afield['dce_field_visibility_multiple'] == 'single') && !empty($afield['dce_field_visibility_field'])) {
                                // Single Field
                                $field_change_selector = '.elementor-element-' . $widget->get_id() . ' #form-field-' . $afield['dce_field_visibility_field'];
                                $field_type_selector = \false;
                                if (isset($conditions[$afield['dce_field_visibility_field']]['field_type'])) {
                                    $field_type_selector = $conditions[$afield['dce_field_visibility_field']]['field_type'];
                                    switch ($field_type_selector) {
                                        case 'radio':
                                        case 'checkbox':
                                            $field_change_selector = '.elementor-field-type-' . $field_type_selector . '.elementor-field-group-' . $afield['dce_field_visibility_field'] . ' input[type=' . $field_type_selector . ']';
                                            if ($field_type_selector == 'checkbox' && $afield['dce_field_visibility_value']) {
                                                $field_change_selector .= '[value="' . $afield['dce_field_visibility_value'] . '"]';
                                            }
                                            break;
                                        case 'acceptance':
                                            $field_change_selector = '.elementor-field-type-' . $field_type_selector . '.elementor-field-group-' . $afield['dce_field_visibility_field'] . ' input[type=checkbox]';
                                            break;
                                    }
                                }
                                if (isset($conditions[$afield['dce_field_visibility_field']]['field_value'])) {
                                    $field_value_selector = $conditions[$afield['dce_field_visibility_field']]['field_value'];
                                }
                                switch ($field_type_selector) {
                                    case 'radio':
                                        $selector_value = $field_change_selector . ':checked';
                                        break;
                                    default:
                                        $selector_value = $field_change_selector;
                                }
                                if (!$selector_value) {
                                    continue;
                                }
                                $display_condition = $this->get_display_condition($afield['dce_field_visibility_value'], $selector_value, $afield['dce_field_visibility_operator'], $form_fields, $widget);
                            }
                            if (!empty($afield['dce_field_visibility_multiple']) && $afield['dce_field_visibility_multiple'] == 'yes' && !empty($afield['dce_field_visibility_field_multiple'])) {
                                // Multiple Values
                                $selector = array();
                                $mvalues = \explode(\PHP_EOL, $afield['dce_field_visibility_value_multiple']);
                                if ('string' === \gettype($afield['dce_field_visibility_field_multiple'])) {
                                    $multiple_fields = \explode(',', \str_replace(' ', '', $afield['dce_field_visibility_field_multiple']));
                                } else {
                                    $multiple_fields = $afield['dce_field_visibility_field_multiple'];
                                }
                                foreach ($multiple_fields as $mkey => $mfield) {
                                    if ($mfield && isset($conditions[$mfield])) {
                                        if (isset($mvalues[$mkey])) {
                                            $mvalue = $mvalues[$mkey];
                                        } else {
                                            $mvalue = \reset($mvalues);
                                        }
                                        $field_type_selector = $conditions[$mfield]['field_type'];
                                        switch ($field_type_selector) {
                                            case 'radio':
                                            case 'checkbox':
                                                $selector[$mkey] = '.elementor-element-' . $widget->get_id() . ' .elementor-field-type-' . $field_type_selector . '.elementor-field-group-' . $mfield . ' input[type=' . $field_type_selector . ']';
                                                if ($field_type_selector == 'checkbox' && $mvalue) {
                                                    $selector[$mkey] .= '[value="' . $mvalue . '"]';
                                                }
                                                break;
                                            case 'acceptance':
                                                $selector[$mkey] = '.elementor-element-' . $widget->get_id() . ' .elementor-field-type-' . $field_type_selector . '.elementor-field-group-' . $mfield . ' input[type=checkbox]';
                                                break;
                                            default:
                                                $selector[$mkey] = '.elementor-element-' . $widget->get_id() . ' #form-field-' . $mfield;
                                        }
                                    }
                                }
                                if (!empty($selector)) {
                                    $field_change_selector = \implode(', ', $selector);
                                    if (\count($multiple_fields) == 1 && \count($mvalues) > 1) {
                                        $mkey = 0;
                                        $mfield = \reset($multiple_fields);
                                        $field_type_selector = $conditions[$mfield]['field_type'];
                                        switch ($field_type_selector) {
                                            case 'radio':
                                                $selector_value = $selector[$mkey] . ':checked';
                                                break;
                                            default:
                                                $selector_value = $selector[$mkey];
                                        }
                                        if (!empty($afield['dce_field_visibility_operator_multiple'])) {
                                            $field_operator = \reset($afield['dce_field_visibility_operator_multiple']);
                                        } else {
                                            if (!empty($afield['dce_field_visibility_operator'])) {
                                                $field_operator = $afield['dce_field_visibility_operator'];
                                            } else {
                                                continue;
                                            }
                                        }
                                        foreach ($mvalues as $vkey => $mvalue) {
                                            if ($vkey) {
                                                $afield['dce_field_visibility_multiple_logic'] = !empty($afield['dce_field_visibility_multiple_logic']) ? $afield['dce_field_visibility_multiple_logic'] : '||';
                                                $display_condition .= ' ' . $afield['dce_field_visibility_multiple_logic'] . ' ';
                                            }
                                            $display_condition .= '(' . $this->get_display_condition($mvalue, $selector_value, $field_operator, $form_fields, $widget) . ')';
                                        }
                                    } else {
                                        foreach ($multiple_fields as $mkey => $mfield) {
                                            $field_type_selector = $conditions[$mfield]['field_type'];
                                            switch ($field_type_selector) {
                                                case 'radio':
                                                    $selector_value = $selector[$mkey] . ':checked';
                                                    break;
                                                default:
                                                    $selector_value = $selector[$mkey];
                                            }
                                            if (isset($mvalues[$mkey])) {
                                                $mvalue = $mvalues[$mkey];
                                            } else {
                                                $mvalue = \reset($mvalues);
                                            }
                                            if ($mkey) {
                                                $afield['dce_field_visibility_multiple_logic'] = !empty($afield['dce_field_visibility_multiple_logic']) ? $afield['dce_field_visibility_multiple_logic'] : '&&';
                                                $display_condition .= ' ' . $afield['dce_field_visibility_multiple_logic'] . ' ';
                                            }
                                            if (!empty($afield['dce_field_visibility_operator_multiple'])) {
                                                if (isset($afield['dce_field_visibility_operator_multiple'][$mkey])) {
                                                    $field_operator = $afield['dce_field_visibility_operator_multiple'][$mkey];
                                                } else {
                                                    $field_operator = \reset($afield['dce_field_visibility_operator_multiple']);
                                                }
                                            } else {
                                                if (!empty($afield['dce_field_visibility_operator'])) {
                                                    $field_operator = $afield['dce_field_visibility_operator'];
                                                }
                                            }
                                            $display_condition .= '(' . $this->get_display_condition($mvalue, $selector_value, $field_operator, $form_fields, $widget) . ')';
                                        }
                                    }
                                }
                            }
                            if (!$display_condition) {
                                continue;
                            }
                            ?>
							<script id="<?php 
                            echo $jkey;
                            ?>">
								(function ($) {
									var <?php 
                            echo $jkey;
                            ?> = function ($scope, $) {
										if ($scope.hasClass("elementor-element-<?php 
                            echo $widget->get_id();
                            ?>")) {
											jQuery('<?php 
                            echo $field_change_selector;
                            ?>').on('change', function () {

											<?php 
                            if ($afield['field_type'] == 'step') {
                                ?>
													jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').addClass('dce-form-visibility-step');
													jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').addClass('dce-form-visibility-step-<?php 
                                echo $afield['dce_field_visibility_mode'];
                                ?>-init');
												<?php 
                            }
                            ?>
												if (<?php 
                            echo $display_condition;
                            ?>) {
										<?php 
                            if ($afield['field_type'] == 'step') {
                                ?>
													jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').addClass('dce-form-visibility-step-<?php 
                                echo $afield['dce_field_visibility_mode'];
                                ?>');
													jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').find('.elementor-field-required input, .elementor-field-required select, .elementor-field-required textarea').each(function () {
														jQuery(this).prop('disabled', <?php 
                                echo $disabled_end;
                                ?>);
													});
													jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').find('input[type="url"], .elementor-field-url').each(function () {
														if (<?php 
                                echo $disabled_end;
                                ?>) {
																jQuery(this).addClass('elementor-field-url');
																jQuery(this).attr('type', 'text');
														} else {
															jQuery(this).attr('type', 'url');
														}
													});
													jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').find('input[type="email"], .elementor-field-email').each(function () {
														if (<?php 
                                echo $disabled_end;
                                ?>) {
																jQuery(this).addClass('elementor-field-email');
																jQuery(this).attr('type', 'text');
														} else {
															jQuery(this).attr('type', 'email');
														}
																	});
												<?php 
                            } else {
                                ?>
											<?php 
                                if (empty($afield['dce_field_visibility_render'])) {
                                    ?>jQuery('<?php 
                                    echo $afield['element_css_group'];
                                    ?>').<?php 
                                    echo $afield['dce_field_visibility_mode'];
                                    ?>();<?php 
                                }
                                ?>
																			jQuery('<?php 
                                echo $element_css_id;
                                ?>').prop('disabled', <?php 
                                echo $disabled_end;
                                ?>);
												<?php 
                            }
                            ?>
																	} else {
											<?php 
                            if ($afield['field_type'] == 'step') {
                                ?>
															jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').removeClass('dce-form-visibility-step-<?php 
                                echo $afield['dce_field_visibility_mode'];
                                ?>');
															jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').find('.elementor-field-required input, .elementor-field-required select, .elementor-field-required textarea').each(function () {
																jQuery(this).prop('disabled', <?php 
                                echo $disabled_init;
                                ?>);
															});
															jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').find('input[type="url"], .elementor-field-url').each(function () {
																if (<?php 
                                echo $disabled_init;
                                ?>) {
																		jQuery(this).addClass('elementor-field-url');
																		jQuery(this).attr('type', 'text');
																} else {
																	jQuery(this).attr('type', 'url');
																}
															});
															jQuery('<?php 
                                echo $afield['element_css_group'];
                                ?>').find('input[type="email"], .elementor-field-email').each(function () {
																if (<?php 
                                echo $disabled_init;
                                ?>) {
																		jQuery(this).addClass('elementor-field-email');
																		jQuery(this).attr('type', 'text');
																} else {
																	jQuery(this).attr('type', 'email');
																}
															});
												<?php 
                            } else {
                                ?>
												<?php 
                                if (empty($afield['dce_field_visibility_render'])) {
                                    ?>jQuery('<?php 
                                    echo $afield['element_css_group'];
                                    ?>').<?php 
                                    echo $display_init;
                                    ?>();<?php 
                                }
                                ?>
															jQuery('<?php 
                                echo $element_css_id;
                                ?>').prop('disabled', <?php 
                                echo $disabled_init;
                                ?>);
												<?php 
                            }
                            ?>
													}
												});
							<?php 
                            // if is pre-valorized
                            if (empty($afield['dce_field_visibility_multiple']) || $afield['dce_field_visibility_multiple'] == 'single') {
                                if (isset($conditions[$afield['dce_field_visibility_field']]['field_type'])) {
                                    $field_type_selector = $conditions[$afield['dce_field_visibility_field']]['field_type'];
                                    if ($field_type_selector == 'radio' && $field_value_selector) {
                                        $field_change_selector .= ':checked';
                                    }
                                }
                            } else {
                                foreach ($multiple_fields as $mkey => $mfield) {
                                    $field_type_selector = $conditions[$mfield]['field_type'];
                                    $field_value_selector = \false;
                                    if (isset($conditions[$mfield]['field_value'])) {
                                        $field_value_selector = $conditions[$mfield]['field_value'];
                                    }
                                    if ($field_type_selector == 'radio' && $field_value_selector) {
                                        $field_change_selector = \str_replace($selector[$mkey] . ':checked', $selector[$mkey], $field_change_selector);
                                    }
                                }
                            }
                            // trigger initial state
                            ?>
												jQuery('<?php 
                            echo $field_change_selector;
                            ?>').each(function () {
													jQuery(this).change();
												});

											}
										};
										$(window).on('elementor/frontend/init', function () {
											elementorFrontend.hooks.addAction('frontend/element_ready/form.default', <?php 
                            echo $jkey;
                            ?>);
										});
									})(jQuery, window);
							</script>
							<?php 
                        }
                    }
                }
                $js = \ob_get_clean();
                $js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $js);
            }
            if (isset($settings['dce_field_visibility_mode']) && $settings['dce_field_visibility_mode'] == 'hidden') {
                $content .= '<style>.elementor-element-' . $widget->get_id() . ' form > .elementor-form-fields-wrapper > .elementor-field-type-submit:last-child { display: none; }</style>';
            }
        }
        return $content;
    }
    public function get_display_condition($value, $selector_value, $operator, $form_fields, $widget)
    {
        if (\strpos($value, '[form:') !== \false) {
            $pieces = \explode('[form:', $value);
            foreach ($pieces as $apiece) {
                $tmp = \explode(']', $apiece, 2);
                if (\count($tmp) > 1) {
                    $field_name = \reset($tmp);
                    if (isset($form_fields[$field_name])) {
                        $field_input_name = '.elementor-element-' . $widget->get_id() . ' .elementor-field-group-' . $field_name . ' ';
                        if ($form_fields[$field_name]['field_type'] == 'select') {
                            $field_input_name .= 'select';
                        } else {
                            $field_input_name .= 'input';
                        }
                        switch ($form_fields[$field_name]['field_type']) {
                            case 'radio':
                            case 'acceptance':
                                $value = \str_replace('[form:' . $field_name . ']', 'jQuery("' . $field_input_name . ':checked").val()', $value);
                                break;
                            case 'checkbox':
                                $value = \str_replace('[form:' . $field_name . ']', "jQuery('" . $field_input_name . '[id^="form-field-' . $form_fields[$field_name]['custom_id'] . "-\"]:checked').val()", $value);
                                break;
                            default:
                                $value = \str_replace('[form:' . $field_name . ']', 'jQuery("' . $field_input_name . '").val()', $value);
                        }
                    } else {
                        $value = \str_replace('[form:' . $field_name . ']', '', $value);
                    }
                }
            }
        } elseif (!\is_numeric($value) && $value) {
            $value = "'" . $value . "'";
        }
        $display_condition = \false;
        $jsel_value = "jQuery('" . $selector_value . "').val()";
        switch ($operator) {
            case 'empty':
                $display_condition = "!jQuery('" . $selector_value . "').length || " . $jsel_value . " == ''";
                break;
            case 'not_empty':
                $display_condition = "jQuery('" . $selector_value . "').length && " . $jsel_value . " != ''";
                break;
            case 'gt':
                $display_condition = "jQuery('" . $selector_value . "').length && " . $jsel_value . ' > ' . $value;
                break;
            case 'ge':
                $display_condition = "jQuery('" . $selector_value . "').length && " . $jsel_value . ' >= ' . $value;
                break;
            case 'lt':
                $display_condition = "jQuery('" . $selector_value . "').length && " . $jsel_value . ' < ' . $value;
                break;
            case 'le':
                $display_condition = "jQuery('" . $selector_value . "').length && " . $jsel_value . ' <= ' . $value;
                break;
            case 'equal_to':
                $display_condition = "jQuery('" . $selector_value . "').length && " . $jsel_value . ' == ' . $value;
                break;
            case 'not_equal':
                $display_condition = "!jQuery('" . $selector_value . "').length || " . $jsel_value . ' != ' . $value;
                break;
            case 'contain':
                $display_condition = "jQuery('" . $selector_value . "').length && (" . $jsel_value . '.includes(' . $value . ') !== false && ' . $jsel_value . " != '')";
                break;
            case 'not_contain':
                $display_condition = "!jQuery('" . $selector_value . "').length || (" . $jsel_value . '.includes(' . $value . ') === false && ' . $jsel_value . " != '')";
                break;
            case 'is_checked':
                $display_condition = "jQuery('" . $selector_value . ":checked').length || jQuery('" . $selector_value . ":disabled').length";
                if ($value) {
                    $display_condition .= ' && ' . $jsel_value . ' == ' . $value;
                }
                break;
            case 'not_checked':
                $display_condition = "!jQuery('" . $selector_value . ":checked').length || jQuery('" . $selector_value . ":disabled').length";
                if ($value) {
                    $display_condition .= ' && ' . $jsel_value . ' == ' . $value;
                }
                break;
        }
        if ($value == '') {
            if (!\in_array($operator, array('empty', 'not_empty', 'is_checked', 'not_checked'))) {
                $display_condition = \false;
            }
        }
        return $display_condition;
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
        $field_controls = ['form_fields_visibility_tab' => ['type' => 'tab', 'tab' => 'visibility', 'label' => __('Condition', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_visibility_tab'], 'dce_field_visibility_deprecated' => ['name' => 'dce_field_visibility_deprecated', 'raw' => __('This extension is deprecated. You can continue to use it but we recommend that you use Conditional Fields instead.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab'], 'dce_field_visibility_mode' => ['label' => __('Condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['visible' => ['title' => __('Always Visible', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check'], 'show' => ['title' => __('Show if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye'], 'hide' => ['title' => __('Hide if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'toggle' => \false, 'default' => 'visible', 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab', 'name' => 'dce_field_visibility_mode'], 'dce_field_visibility_multiple' => ['name' => 'dce_field_visibility_multiple', 'type' => Controls_Manager::CHOOSE, 'label' => __('Multiple conditions', 'dynamic-content-for-elementor'), 'options' => ['single' => ['title' => __('Single', 'dynamic-content-for-elementor'), 'icon' => 'eicon-cog'], 'yes' => ['title' => __('Multiple', 'dynamic-content-for-elementor'), 'icon' => 'eicon-cogs']], 'condition' => array('dce_field_visibility_mode!' => 'visible'), 'default' => 'single', 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab'], 'dce_field_visibility_field' => ['type' => Controls_Manager::TEXT, 'tab' => 'visibility', 'label' => __('Field ID', 'dynamic-content-for-elementor'), 'placeholder' => __('name', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab', 'name' => 'dce_field_visibility_field', 'condition' => array('dce_field_visibility_mode!' => 'visible', 'dce_field_visibility_multiple' => ['', 'single', null])], 'dce_field_visibility_field_multiple' => ['name' => 'dce_field_visibility_field_multiple', 'label' => __('Field IDs', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('name,message', 'dynamic-content-for-elementor'), 'description' => __('Separate each field ID it with a comma', 'dynamic-content-for-elementor'), 'condition' => array('dce_field_visibility_mode!' => 'visible', 'dce_field_visibility_multiple' => 'yes'), 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab'], 'dce_field_visibility_operator' => ['label' => __('Operator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_string_comparison(), 'default' => 'equal_to', 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab', 'name' => 'dce_field_visibility_operator', 'condition' => ['dce_field_visibility_mode!' => 'visible']], 'dce_field_visibility_value' => ['type' => Controls_Manager::TEXT, 'tab' => 'visibility', 'label' => __('Value', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab', 'name' => 'dce_field_visibility_value', 'dynamic' => ['active' => \true], 'condition' => ['dce_field_visibility_mode!' => 'visible', 'dce_field_visibility_multiple' => ['', 'single', null], 'dce_field_visibility_operator' => array('equal_to', 'not_equal', 'gt', 'lt', 'ge', 'le', 'not_contain', 'contain', 'is_checked', 'not_checked')]], 'dce_field_visibility_value_multiple' => ['name' => 'dce_field_visibility_value_multiple', 'type' => Controls_Manager::TEXTAREA, 'tab' => 'visibility', 'label' => __('Multiple Values', 'dynamic-content-for-elementor'), 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab', 'description' => __('One value per line. Type them in the same order of the selected fields', 'dynamic-content-for-elementor'), 'condition' => ['dce_field_visibility_mode!' => 'visible', 'dce_field_visibility_multiple' => 'yes']], 'dce_field_visibility_multiple_logic' => ['name' => 'dce_field_visibility_multiple_logic', 'type' => Controls_Manager::SELECT, 'label' => __('Logic operator', 'dynamic-content-for-elementor'), 'options' => ['&&' => 'AND', '||' => 'OR'], 'condition' => ['dce_field_visibility_mode!' => 'visible', 'dce_field_visibility_multiple' => 'yes'], 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab'], 'dce_field_visibility_render' => ['label' => __('Disable only', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'tab' => 'visibility', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_visibility_tab', 'name' => 'dce_field_visibility_render', 'condition' => ['dce_field_visibility_mode!' => 'visible', 'field_type!' => 'step']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function update_button_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_field_visibility_mode' => ['name' => 'dce_field_visibility_mode', 'label' => __('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['visible' => ['title' => __('Always Visible', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check'], 'hidden' => ['title' => __('Always Hidden', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-square-o'], 'show' => ['title' => __('Show if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye'], 'hide' => ['title' => __('Hide if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'toggle' => \false, 'default' => 'visible', 'separator' => 'before'], 'dce_field_visibility_multiple' => ['name' => 'dce_field_visibility_multiple', 'type' => Controls_Manager::CHOOSE, 'label' => __('Multiple conditions', 'dynamic-content-for-elementor'), 'options' => ['single' => ['title' => __('Single', 'dynamic-content-for-elementor'), 'icon' => 'eicon-cog'], 'yes' => ['title' => __('Multiple', 'dynamic-content-for-elementor'), 'icon' => 'eicon-cogs']], 'default' => 'single', 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden']]], 'dce_field_visibility_field' => ['name' => 'dce_field_visibility_field', 'type' => Controls_Manager::TEXT, 'label' => __('Field ID', 'dynamic-content-for-elementor'), 'placeholder' => __('name', 'dynamic-content-for-elementor'), 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden'], 'dce_field_visibility_multiple' => ['', 'single', null]]], 'dce_field_visibility_field_multiple' => ['name' => 'dce_field_visibility_field_multiple', 'label' => __('Field IDs', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('name,message', 'dynamic-content-for-elementor'), 'description' => __('Separate each field ID it with a comma', 'dynamic-content-for-elementor'), 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden'], 'dce_field_visibility_multiple' => 'yes']], 'dce_field_visibility_operator' => ['name' => 'dce_field_visibility_operator', 'label' => __('Operator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_string_comparison(), 'default' => 'empty', 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden']]], 'dce_field_visibility_value' => ['name' => 'dce_field_visibility_value', 'type' => Controls_Manager::TEXT, 'label' => __('Value', 'dynamic-content-for-elementor'), 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden'], 'dce_field_visibility_multiple' => ['', 'single', null], 'dce_field_visibility_operator' => ['equal_to', 'not_equal', 'gt', 'lt', 'ge', 'le', 'not_contain', 'contain', 'is_checked', 'not_checked']]], 'dce_field_visibility_value_multiple' => ['name' => 'dce_field_visibility_value_multiple', 'type' => Controls_Manager::TEXTAREA, 'label' => __('Multiple Values', 'dynamic-content-for-elementor'), 'description' => __('One value per line, write them in same order of Fields IDs', 'dynamic-content-for-elementor'), 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden'], 'dce_field_visibility_multiple' => 'yes']], 'dce_field_visibility_multiple_logic' => ['name' => 'dce_field_visibility_multiple_logic', 'type' => Controls_Manager::CHOOSE, 'label' => __('Logic operator', 'dynamic-content-for-elementor'), 'options' => ['&&' => ['title' => __('AND', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], '||' => ['title' => __('OR', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle-o']], 'default' => '&&', 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden'], 'dce_field_visibility_multiple' => 'yes']], 'dce_field_visibility_render' => ['name' => 'dce_field_visibility_render', 'label' => __('Disable only', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_field_visibility_mode!' => ['visible', 'hidden']]]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
