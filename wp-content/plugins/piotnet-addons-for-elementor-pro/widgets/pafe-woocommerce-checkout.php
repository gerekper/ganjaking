<?php

class PAFE_Woocommerce_Checkout extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-woocommerce-checkout';
	}

	public function get_title() {
		return __( 'Woo Checkout', 'pafe' );
	}

	public function get_icon() {
		return 'icon-w-woocommerce-checkout';
	}

	public function get_categories() {
		return [ 'pafe-form-builder' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'woocommerce checkout' ];
	}

	protected function _register_controls() {

		if ( class_exists( 'WooCommerce' ) ) {

			$this->start_controls_section(
				'pafe_woocommerce_checkout_section',
				[
					'label' => __( 'Woocommerce Checkout', 'pafe' ),
				]
			);

			$this->add_control(
				'pafe_woocommerce_checkout_note',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'classes' => 'elementor-descriptor',
					'raw' => __( 'Note: You have to enter Regular price of Woocommerce Product. If your form has Repeater Fields, you have to enable Custom Order Item Meta.', 'pafe' ),
				]
			);

			$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
			
			$this->add_control(
				'pafe_woocommerce_checkout_form_id',
				[
					'label' => __( 'Form ID* (Required)', 'pafe' ),
					'type' => $pafe_forms ? \Elementor\Controls_Manager::HIDDEN : \Elementor\Controls_Manager::TEXT,
					'default' => $pafe_forms ? get_the_ID() : '',
					'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
					'render_type' => 'none',
				]
			);

			$this->add_control(
				'pafe_woocommerce_checkout_product_id',
				[
					'label' => __( 'Product ID* (Required)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);
            $this->add_control(
				'woocommerce_quantity_option',
				[
					'label' => __( 'Quantity', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

            $this->add_control(
				'woocommerce_quantity',
				[
					'label' => __( 'Quantity Shortcode', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
                    'condition' => [
                        'woocommerce_quantity_option' => 'yes'
                    ]
				]
			);
            
			$this->add_control(
				'pafe_woocommerce_checkout_redirect',
				[
					'label' => __( 'Redirect', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->add_control(
				'remove_empty_form_input_fields',
				[
					'label' => __( 'Remove Empty Form Input Fields', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'pafe_woocommerce_checkout_remove_fields',
				[
					'label' => __( 'Remove fields from WooCommerce Checkout Form', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::SELECT2,
					'multiple' => true,
					'options' => [
						'billing_first_name' => __( 'Billing First Name', 'pafe' ),
						'billing_last_name' => __( 'Billing Last Name', 'pafe' ),
						'billing_company' => __( 'Billing Company', 'pafe' ),
						'billing_address_1' => __( 'Billing Address 1', 'pafe' ),
						'billing_address_2' => __( 'Billing Address 2', 'pafe' ),
						'billing_city' => __( 'Billing City', 'pafe' ),
						'billing_postcode' => __( 'Billing Post Code', 'pafe' ),
						'billing_country' => __( 'Billing Country', 'pafe' ),
						'billing_state' => __( 'Billing State', 'pafe' ),
						'billing_phone' => __( 'Billing Phone', 'pafe' ),
						'billing_email' => __( 'Billing Email', 'pafe' ),
						'order_comments' => __( 'Order Comments', 'pafe' ),
						'shipping_first_name' => __( 'Shipping First Name', 'pafe' ),
						'shipping_last_name' => __( 'Shipping Last Name', 'pafe' ),
						'shipping_company' => __( 'Shipping Company', 'pafe' ),
						'shipping_address_1' => __( 'Shipping Address 1', 'pafe' ),
						'shipping_address_2' => __( 'Shipping Address 2', 'pafe' ),
						'shipping_city' => __( 'Shipping City', 'pafe' ),
						'shipping_postcode' => __( 'Shipping Post Code', 'pafe' ),
						'shipping_country' => __( 'Shipping Country', 'pafe' ),
						'shipping_state' => __( 'Shipping State', 'pafe' ),
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_price',
				[
					'label' => __( 'Price Field Shortcode', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'label_block' => true,
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_custom_order_item_meta_enable',
				[
					'label' => __( 'Custom Order Item Meta', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'description' => __( 'If your form has Repeater Fields, you have to enable it and enter Repeater Shortcode', 'pafe' ),
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_field_shortcode',
				[
					'label' => __( 'Field Shortcode, Repeater Shortcode', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
				]
			);

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_remove_if_field_empty',
				[
					'label' => __( 'Remove If Field Empty', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_custom_order_item_list',
				array(
					'type'    => Elementor\Controls_Manager::REPEATER,
					'fields'  => $repeater->get_controls(),
					'title_field' => '{{{ woocommerce_add_to_cart_custom_order_item_field_shortcode }}}',
					'condition' => [
						'woocommerce_add_to_cart_custom_order_item_meta_enable' => 'yes',
					],
				)
			);

			$this->add_control(
				'booking_enable',
				[
					'label' => __( 'Booking', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'booking_shortcode',
				[
					'label' => __( 'Booking Shortcode', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( '[field id="booking"]', 'pafe' ),
					'label_block' => true,
					'condition' => [
						'booking_enable' => 'yes',
					],
				]
			);

			$this->end_controls_section();
            $this->start_controls_section(
                'section_woocommerce_add_to_cart_form_options',
                [
                    'label' => __( 'Custom Messages', 'pafe' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'woocommerce_add_to_cart_required_field_message',
                [
                    'label' => __( 'Required Message', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __( 'This field is required.', 'pafe' ),
                    'placeholder' => __( 'This field is required.', 'pafe' ),
                    'label_block' => true,
                    'render_type' => 'none',
                ]
            );

            $this->end_controls_section();
    	}

	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
		$form_id = $pafe_forms ? get_the_ID() : $settings['pafe_woocommerce_checkout_form_id'];
		$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;
        $required_text = $settings['woocommerce_add_to_cart_required_field_message'];

		if (!empty($settings['pafe_woocommerce_checkout_product_id'])) :
	?>
		<div  data-pafe-woocommerce-checkout-form-id="<?php echo $form_id; ?>" data-pafe-woocommerce-checkout-custom-message="<?php echo $required_text; ?>" data-pafe-woocommerce-checkout-product-id="<?php echo $settings['pafe_woocommerce_checkout_product_id']; ?>" data-pafe-woocommerce-checkout-post-id="<?php echo get_the_ID(); ?>" data-pafe-woocommerce-checkout-id="<?php echo $this->get_id(); ?>" >
			<?php echo do_shortcode('[woocommerce_checkout]'); ?>
		</div>
	<?php
		endif;
	}
}
