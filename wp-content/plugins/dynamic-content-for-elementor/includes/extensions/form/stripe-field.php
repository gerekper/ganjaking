<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Stripe;
use ElementorPro\Modules\Forms\Fields;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class StripeField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-stripe'];
    private static $validated_intents = [];
    public function __construct()
    {
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        parent::__construct();
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Stripe';
    }
    public function get_label()
    {
        return __('Stripe', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_form_stripe';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    // return the conditions related to the customer information controls, which should only appear in certain circumstances.
    private function get_customer_info_condition()
    {
        return ['relation' => 'and', 'terms' => [['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()], ['relation' => 'or', 'terms' => [['name' => 'dce_stripe_attach_customer', 'operator' => '==', 'value' => 'yes'], ['name' => 'dce_stripe_is_subscription', 'operator' => '==', 'value' => 'yes']]]]];
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $field_controls = ['admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this form field.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        } else {
            $customer_info_conditions = $this->get_customer_info_condition();
            $field_controls = ['dce_stripe_is_subscription' => ['name' => 'dce_stripe_is_subscription', 'label' => __('This is a Subscription', 'dynamic-content-for-elementor'), 'description' => __('Please notice that Subscriptions are not validated.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'default' => 'no', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_form_stripe_price_id_from_field' => ['name' => 'dce_form_stripe_price_id_from_field', 'label' => __('Get Subscription Price ID dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => 'no', 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_subscription_schedule' => ['name' => 'dce_form_stripe_subscription_schedule', 'label' => __('Subscription Schedule', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'single-phase' => esc_html__('Simple - One phase (Experiment!)', 'dynamic-content-for-elementor')], 'description' => esc_html__('In case of None the subscription does not have a scheduled end date. It will continue until manually cancelled.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_subscription_single_phase_iterations' => ['name' => 'dce_form_stripe_subscription_single_phase_iterations', 'label' => __('Iterations for Schedule', 'dynamic-content-for-elementor'), 'description' => __('After the iterations the subscription is cancelled', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 12, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription' => 'yes', 'dce_form_stripe_subscription_schedule' => 'single-phase']], 'dce_form_stripe_price_id' => ['name' => 'dce_form_stripe_price_id', 'label' => __('Subscription Price ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \true], 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_price_id_from_field!' => 'yes', 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_price_id_field_id' => ['name' => 'dce_form_stripe_price_id_field_id', 'label' => __('Subscription Price ID Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_price_id_from_field' => 'yes', 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_currency' => ['name' => 'dce_form_stripe_currency', 'label' => __('Transaction Currency', 'dynamic-content-for-elementor'), 'description' => __('The currency three-letter ISO Code (for example USD or EUR).', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'USD', 'label_block' => 'true', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_value_from_field' => ['name' => 'dce_form_stripe_value_from_field', 'label' => __('Get Amount dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => 'no', 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_item_value' => ['name' => 'dce_form_stripe_item_value', 'label' => __('Transaction Amount', 'dynamic-content-for-elementor'), 'description' => __('Amount intended to be collected by this transaction in the currency unit', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '10.99', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \true], 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_value_from_field!' => 'yes', 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_value_field_id' => ['name' => 'dce_form_stripe_value_field_id', 'label' => __('Amount Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_value_from_field' => 'yes', 'dce_stripe_is_subscription!' => 'yes']], 'dce_stripe_attach_customer' => ['name' => 'dce_stripe_attach_customer', 'label' => __('Attach Customer Information to the Payment', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-control-field-description">' . __('The customer information taken from other fields will be attached to the payment and available in the Stripe Panel. Please insert the field IDs associated with each information, leave blank if not available. Notice that the Customer will be duplicated if they make multiple payments.', 'dynamic-content-for-elementor') . '</div>', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_stripe_future_usage' => ['name' => 'dce_stripe_future_usage', 'label' => __('Save the Customer payment method for future usage', 'dynamic-content-for-elementor'), 'description' => __('Associate the paying method with the Customer. You can then create recurring payments and subscriptions in the Stripe Panel. Customer email is required.', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes', 'dce_stripe_is_subscription!' => 'yes']], 'dce_stripe_customer_email_field_id' => ['name' => 'dce_stripe_customer_email_field_id', 'label' => __('Customer Email Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_stripe_customer_name_field_id' => ['name' => 'dce_stripe_customer_name_field_id', 'label' => __('Customer Full Name Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_stripe_customer_phone_field_id' => ['name' => 'dce_stripe_customer_phone_field_id', 'separator' => 'after', 'label' => __('Customer Phone Number Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_form_stripe_item_description' => ['name' => 'dce_form_stripe_item_description', 'label' => __('Item Description', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'placeholder' => __('Item Description', 'dynamic-content-for-elementor'), 'description' => __('You can also use tokens like [form:fieldid] to refer to other fields.', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['dce_stripe_is_subscription!' => 'yes', 'field_type' => $this->get_type()]], 'dce_form_stripe_item_sku' => ['name' => 'dce_form_stripe_item_sku', 'label' => __('Item SKU', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => __('Item SKU', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['dce_stripe_is_subscription!' => 'yes', 'field_type' => $this->get_type()]]];
        }
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $stripe = \DynamicContentForElementor\Plugin::instance()->stripe;
        $stripe_key = $stripe->get_publishable_key();
        if (empty($stripe_key)) {
            Helper::Notice(esc_html__('Stripe Error: Missing Publishable Key.', 'dynamic-content-for-elementor'));
            return;
        }
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-required', $item['required'] ? 'true' : 'false', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-publishable-key', $stripe_key, \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-field-index', $item_index, \true);
        $intent_url = admin_url('admin-ajax.php?action=dce_stripe_get_payment_intent');
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-intent-url', $intent_url, \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'style', 'padding-top: 10px;', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'class', 'elementor-field elementor-field-textual dce-stripe-elements', \true);
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<span class="stripe-error elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert" style="display: none;"></span>';
        echo '<div ' . $form->get_render_attribute_string('dce_stripe' . $item_index) . '></div>';
    }
    private static function get_field_settings($id, Classes\Form_Record $record)
    {
        $field_settings = $record->get_form_settings('form_fields');
        $field_settings = \array_filter($field_settings, function ($field) use($id) {
            return $field['custom_id'] === $id;
        });
        return \array_values($field_settings)[0];
    }
    private function is_subscription($settings)
    {
        if (($settings['dce_stripe_is_subscription'] ?? '') === 'yes') {
            return \true;
        }
        return \false;
    }
    /**
     * @param string $id
     * @return void
     */
    private function set_subscription_default_payment_method($subscription)
    {
        $invoice = $subscription->latest_invoice;
        if ($invoice === null) {
            return;
        }
        /**
         * @var \Stripe\Invoice $invoice
         */
        $intent = $invoice->payment_intent;
        if ($intent === null) {
            return;
        }
        /**
         * @var \Stripe\PaymentIntent $intent
         */
        $pm_id = $intent->payment_method;
        \DynamicOOOS\Stripe\Subscription::update($subscription->id, ['default_payment_method' => $pm_id]);
    }
    private function set_subscription_schedule($subscription, $settings)
    {
        $iterations = \intval($settings['dce_form_stripe_subscription_single_phase_iterations']);
        $sched = \DynamicOOOS\Stripe\SubscriptionSchedule::create(['from_subscription' => $subscription->id]);
        \DynamicOOOS\Stripe\SubscriptionSchedule::update($sched->id, ['phases' => [['items' => [['price' => $subscription->items->data[0]->price->id]], 'start_date' => $subscription->created, 'iterations' => $iterations]], 'end_behavior' => 'cancel']);
    }
    private function handle_subscription($id, $settings)
    {
        if (\strpos($id, 'sub_') !== 0) {
            return;
        }
        $subscription = \DynamicOOOS\Stripe\Subscription::retrieve(['id' => $id, 'expand' => ['latest_invoice.payment_intent']]);
        $this->set_subscription_default_payment_method($subscription);
        if ($settings['dce_form_stripe_subscription_schedule'] === 'single-phase') {
            $this->set_subscription_schedule($subscription, $settings);
        }
    }
    public function process_field($field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler)
    {
        $error_msg = __('There was an error while completing the payment, please try again later or contact the merchant directly.', 'dynamic-content-for-elementor');
        $id = $field['id'];
        $intent_id = $field['value'];
        $settings = self::get_field_settings($id, $record);
        if ($this->is_subscription($settings)) {
            $this->handle_subscription($intent_id, $settings);
            return;
            // no validation for subscriptions.
        }
        if (empty($intent_id)) {
            // Value is not allowed to be empty when field is required. So
            // if empty then the field is not required and no validation is
            // needed.
            return;
        }
        if (isset(self::$validated_intents[$intent_id])) {
            return;
            // good, already validated.
        }
        try {
            $intent = \DynamicOOOS\Stripe\PaymentIntent::retrieve($intent_id);
            // This filter gives the ability to check that the amount is the
            // one expected or abort.
            $intent = apply_filters('dynamicooo/stripe-field/payment-intent', $intent, $record, $ajax_handler);
            if (!$intent) {
                $ajax_handler->add_error($id, $error_msg);
                return;
            }
            $dce_id_expected = $record->get_form_settings('id') . '-' . $id;
            // we make sure the payment intent was created by this stripe
            // field and not elsewhere:
            if ($intent->metadata['dce_id'] !== $dce_id_expected) {
                $ajax_handler->add_error($id, $error_msg);
                return;
            }
            $intent->capture();
            if ('succeeded' !== $intent->status) {
                $ajax_handler->add_error($id, $error_msg);
            } else {
                self::$validated_intents[$intent_id] = \true;
            }
        } catch (\DynamicOOOS\Stripe\Exception\InvalidRequestException $e) {
            $ajax_handler->add_error($id, $error_msg);
        }
    }
}
