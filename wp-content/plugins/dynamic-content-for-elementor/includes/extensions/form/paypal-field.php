<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use ElementorPro\Modules\Forms\Fields;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
use DynamicOOOS\PayPalCheckoutSdk\Core\PayPalHttpClient;
use DynamicOOOS\PayPalCheckoutSdk\Core\SandboxEnvironment;
use DynamicOOOS\PayPalCheckoutSdk\Core\ProductionEnvironment;
use DynamicOOOS\PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use DynamicOOOS\PayPalCheckoutSdk\Orders\OrdersGetRequest;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class PaypalField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $has_action = \false;
    public $depended_scripts = ['dce-paypal'];
    public static $validated_orders = [];
    public function __construct()
    {
        add_action('elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        parent::__construct();
    }
    public function add_control_section_to_form($element, $args)
    {
        $element->start_controls_section('dce_section_paypal_buttons_style', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('PayPal', 'dynamic-content-for-elementor'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
        $element->add_control('dce_paypal_center', ['label' => __('Center PayPal Buttons', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes']);
        $element->add_control('dce_paypal_layout', ['label' => __('Vertical layout (More Payment Options)', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'horizontal', 'return_value' => 'vertical']);
        $element->add_control('dce_paypal_height', ['label' => __('PayPal Buttons Height', 'dynamic-content-for-elementor'), 'description' => __('Buttons Height in pixels: min 25, max 55.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 35, 'min' => 25, 'max' => 55, 'ste' => 1]);
        $element->end_controls_section();
    }
    public static function get_satisfy_dependencies()
    {
        return \true;
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'PayPal';
    }
    public function get_label()
    {
        return __('Paypal', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_form_paypal';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function render($item, $item_index, $form)
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<div style="width: 100%;">';
            echo esc_html__('The PayPal Field is only displayed in the frontend.', 'dynamic-content-for-elementor');
            echo '</div>';
            return;
        }
        if (!wp_script_is('dce-paypal-sdk', 'registered')) {
            echo '<div style="width: 100%;">';
            echo esc_html__('There was an error loading PayPal. Is the PayPal Client ID valid?', 'dynamic-content-for-elementor');
            echo '</div>';
            return;
        }
        $settings = $form->get_settings_for_display();
        $name = Helper::get_dynamic_value($item['dce_form_paypal_item_name']);
        $value = Helper::get_dynamic_value($item['dce_form_paypal_item_value']);
        $sku = Helper::get_dynamic_value($item['dce_form_paypal_item_sku']);
        $description = Helper::get_dynamic_value($item['dce_form_paypal_item_description']);
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $form->add_render_attribute('paypal-buttons' . $item_index, 'data-currency', get_option('dce_paypal_api_currency', 'USD'));
        if ($item['dce_form_paypal_name_from_field'] === 'yes') {
            $form->add_render_attribute('paypal-buttons' . $item_index, 'data-name-field-id', $item['dce_form_paypal_name_field_id']);
        } else {
            $form->add_render_attribute('paypal-buttons' . $item_index, 'data-name', $name);
        }
        if ($item['dce_form_paypal_value_from_field'] === 'yes') {
            $form->add_render_attribute('paypal-buttons' . $item_index, 'data-value-field-id', $item['dce_form_paypal_value_field_id']);
        } else {
            $form->add_render_attribute('paypal-buttons' . $item_index, 'data-value', $value);
        }
        $form->add_render_attribute('paypal-buttons' . $item_index, 'data-sku', $sku);
        $form->add_render_attribute('paypal-buttons' . $item_index, 'data-description', $description);
        $form->add_render_attribute('paypal-buttons' . $item_index, 'data-height', $settings['dce_paypal_height']);
        $form->add_render_attribute('paypal-buttons' . $item_index, 'data-layout', 'vertical' === $settings['dce_paypal_layout'] ? 'vertical' : 'horizontal');
        $form->add_render_attribute('paypal-buttons' . $item_index, 'class', 'dce-paypal-buttons');
        $form->add_render_attribute('paypal-buttons' . $item_index, 'class', 'dce-paypal-buttons');
        $form->add_render_attribute('paypal-buttons' . $item_index, 'id', 'dce-buttons-' . $form->get_id() . '-' . $item_index);
        $center = $settings['dce_paypal_center'];
        $wrapper_style = 'display: block; width: 100%;';
        if ('yes' === $center) {
            $form->add_render_attribute('paypal-buttons' . $item_index, 'style', 'margin: auto;');
            $wrapper_style .= 'text-align: center';
        }
        $form->add_render_attribute('paypal-wrapper' . $item_index, 'style', $wrapper_style);
        echo '<div ' . $form->get_render_attribute_string('paypal-wrapper' . $item_index) . '>';
        echo '<div class="dce-paypal-approved" style="display: none;">' . esc_html($item['dce_form_paypal_message_approved']) . '</div>';
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<div ' . $form->get_render_attribute_string('paypal-buttons' . $item_index) . '></div>';
        echo '</div>';
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $currencies = ['USD', 'AUD', 'BRL', 'GBP', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'INR', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'RUB', 'SGD', 'SEK', 'CHF', 'THB'];
        $currencies_options = [];
        foreach ($currencies as $curr) {
            $currencies_options[$curr] = $curr;
        }
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $field_controls = ['admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this form field.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        } else {
            $field_controls = ['dce_form_paypal_disable_validation' => ['name' => 'dce_form_paypal_disable_validation', 'label' => __('Disable Amount Validation', 'dynamic-content-for-elementor'), 'description' => __('By switching on this setting you disable validation of the transaction amount. There will still be validation to make sure a valid transaction has been sent. This is useful for payments that can have arbitrary amounts, when passed from another form with a request/get parameter', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'no', 'return_value' => 'yes', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_paypal_value_from_field!' => 'yes']], 'dce_form_paypal_name_from_field' => ['name' => 'dce_form_paypal_name_from_field', 'label' => __('Get item name dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'default' => 'no', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_form_paypal_name_field_id' => ['name' => 'dce_form_paypal_name_field_id', 'label' => __('Name Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_paypal_name_from_field' => 'yes']], 'dce_form_paypal_item_name' => ['name' => 'dce_form_paypal_item_name', 'label' => __('Item Name', 'dynamic-content-for-elementor'), 'description' => __('Enter the item name to identify the product or service for this PayPal transaction.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => __('Item Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => 'no name', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['field_type' => $this->get_type(), 'dce_form_paypal_name_from_field!' => 'yes']], 'dce_form_paypal_value_from_field' => ['name' => 'dce_form_paypal_value_from_field', 'label' => __('Get item value dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'default' => 'no', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_form_paypal_value_field_id' => ['name' => 'dce_form_paypal_value_field_id', 'label' => __('Value Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_paypal_value_from_field' => 'yes']], 'dce_form_paypal_item_value' => ['name' => 'dce_form_paypal_item_value', 'label' => __('Item Value', 'dynamic-content-for-elementor'), 'description' => __('Enter the item value for the product or service in this PayPal transaction.', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '0.1', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \true], 'condition' => ['field_type' => $this->get_type(), 'dce_form_paypal_value_from_field!' => 'yes']], 'dce_form_paypal_item_description' => ['name' => 'dce_form_paypal_item_description', 'label' => __('Item Description', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'placeholder' => __('Item Description', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['field_type' => $this->get_type()]], 'dce_form_paypal_item_sku' => ['name' => 'dce_form_paypal_item_sku', 'label' => __('Item Number (SKU)', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => __('Item SKU', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['field_type' => $this->get_type()]], 'dce_form_paypal_message_approved' => ['name' => 'dce_form_paypal_message_approved', 'label' => __('Order Approved Message', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label_block' => 'true', 'default' => __('Your PayPal order has been approved. Please submit the form to complete this payment.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        }
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public static function paypal_client()
    {
        if (get_option('dce_paypal_api_mode', 'sandbox') === 'sandbox') {
            $client_id = get_option('dce_paypal_api_client_id_sandbox');
            $client_secret = get_option('dce_paypal_api_client_secret_sandbox');
            $env = new SandboxEnvironment($client_id, $client_secret);
        } else {
            $client_id = get_option('dce_paypal_api_client_id_live');
            $client_secret = get_option('dce_paypal_api_client_secret_live');
            $env = new ProductionEnvironment($client_id, $client_secret);
        }
        return new PayPalHttpClient($env);
    }
    /**
     * The form settings that are passed to the validation function have
     * the dynamic controls not parsed (not sure why). So we do it
     * manually.  This code is adapted from Elementor controls-stack code.
     * There might be a simpler way of doing it.
     */
    private static function get_parsed_item_value($field_settings)
    {
        $name = 'dce_form_paypal_item_value';
        if (!isset($field_settings['__dynamic__'])) {
            return $field_settings[$name];
        }
        $dynamic = $field_settings['__dynamic__'];
        if (!isset($dynamic[$name])) {
            return $field_settings[$name];
        }
        $post_id = \intval($_POST['post_id']);
        $form_id = \intval($_POST['form_id']);
        $elementor = \Elementor\Plugin::instance();
        $document = $elementor->documents->get($post_id);
        if ($document) {
            $form = \ElementorPro\Modules\Forms\Module::find_element_recursive($document->get_elements_data(), sanitize_text_field($_POST['form_id']));
        } else {
            throw new \Exception();
        }
        $widget = $elementor->elements_manager->create_element_instance($form);
        $control = $widget->get_controls()['form_fields']['fields'][$name];
        /**
         * @var \Elementor\Base_Data_Control $control_obj
         */
        $control_obj = \Elementor\Plugin::$instance->controls_manager->get_control(\Elementor\Controls_Manager::TEXT);
        $dynamic_settings = $control_obj->get_settings('dynamic');
        $dynamic_settings = \array_merge($dynamic_settings, $control['dynamic']);
        return $control_obj->parse_tags($field_settings['__dynamic__'][$name], $dynamic_settings);
    }
    /**
     * validate uploaded file field
     *
     * @param array                $field
     * @param Classes\Form_Record  $record
     * @param Classes\Ajax_Handler $ajax_handler
     */
    public function process_field($field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler)
    {
        $order_id = $field['raw_value'];
        if (empty($order_id)) {
            // Value is not allowed to be empty when field is required. So
            // if empty then the field is not required and no validation is
            // needed.
            return;
        }
        if (isset(self::$validated_orders[$order_id])) {
            return;
            // good, already validated.
        }
        $error_msg = __('There was an error while completing the paypal transaction, please try again later or contact the merchant directly.', 'dynamic-content-for-elementor');
        $id = $field['id'];
        $request = new OrdersCaptureRequest($order_id);
        $request->headers['prefer'] = 'return=representation';
        try {
            // Will throw an error if the order capture is not succesful.
            $response = self::paypal_client()->execute($request);
            $field_settings = Helper::get_form_field_settings($id, $record);
            if ('yes' !== $field_settings['dce_form_paypal_disable_validation'] && 'yes' !== $field_settings['dce_form_paypal_value_from_field']) {
                // Check that the total payed in the order is the same as that
                // of the item value set in the field settings. This is needed
                // because the order is created in the browser and it could be
                $settings_value = self::get_parsed_item_value($field_settings);
                $settings_value = Helper::get_dynamic_value($settings_value);
                $settings_currency = get_option('dce_paypal_api_currency', 'USD');
                $order_amount = $response->result->purchase_units[0]->amount;
                $order_value = $order_amount->value;
                $order_currency = $order_amount->currency_code;
                // 2.2204460492503E-16 is float espilon, it is not availabe in PHP < 7.2.
                if (\abs(\floatval($order_value) - \floatval($settings_value)) > 2.2204460492503E-16 || $order_currency !== $settings_currency) {
                    $ajax_handler->add_error($id, $error_msg);
                    return;
                }
            }
        } catch (\DynamicOOOS\PayPalHttp\HttpException $e) {
            $ajax_handler->add_admin_error_message($e->getMessage());
            $ajax_handler->add_error_message(__('Could not complete the payment, try again later or contact the administrator.', 'dynamic-content-for-elementor'));
            $ajax_handler->send();
            return;
        }
        self::$validated_orders[$order_id] = \true;
    }
}
