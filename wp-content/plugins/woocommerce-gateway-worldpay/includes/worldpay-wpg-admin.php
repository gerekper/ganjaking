<?php

		$this->form_fields = array(
			'enabled' 				=> array(
											'title' 		=> __( 'Enable/Disable', 'woocommerce_worlday' ),
											'label' 		=> __( 'Enable Worldpay Worldwide Gateway', 'woocommerce_worlday' ),
											'type' 			=> 'checkbox',
											'description' 	=> '',
											'default' 		=> $this->default_enabled
											),
			'initial_options' 		=> array(
											'title' 		=> __( 'Initial Setup Options', 'woocommerce_worlday' ),
											'type' 			=> 'title',
											'description' 	=> __( '<div style="display:block; border-bottom:1px dotted #000; width:100%; clear:both"></div>', 'woocommerce_worlday' )
											),
			'debug'     			=> array(
			    							'title'         => __( 'Logging', 'woocommerce_worlday' ),
			    							'type'          => 'checkbox',
			    							'options'       => array('no'=>'No','yes'=>'Yes'),
			    							'label'     	=> __( 'Enable logging', 'woocommerce_worlday' ),
			    							'default'       => $this->default_debug,
											'description' 	=> __( 'Turn on logging? Always on for test orders.', 'woocommerce_worlday' ),
											),
			'status' 				=> array(
											'title' 		=> __( 'Status', 'woocommerce_worlday' ),
											'type' 			=> 'select',
											'options' 		=> array('live'=>'Live','testing'=>'Testing'),
											'description' 	=> __( 'Set Live/Testing Status.', 'woocommerce_worlday' ),
											'default' 		=> $this->default_status
											),
			'submission'			=> array(
											'title'         => __( 'Submission Method', 'woocommerce_worlday' ),
											'type' 			=> 'select',
											'options' 		=> array('direct'=>'Direct','hosted'=>'Hosted'),
											'description' 	=> __( 'Direct or Hosted?', 'woocommerce_worlday' ),
											'default' 		=> $this->default_submission
											),
			'merchantcode' 			=> array(
											'title' 		=> __( 'Worldpay Merchant Code', 'woocommerce_worlday' ),
											'type' 			=> 'text',
											'description' 	=> __( '', 'woocommerce_worlday' ),
											'default' 		=> ''
											),
			'username' 				=> array(
											'title' 		=> __( 'Worldpay Username', 'woocommerce_worlday' ),
											'type' 			=> 'text',
											'description' 	=> __( '', 'woocommerce_worlday' ),
											'default' 		=> __( 'Created in the Worldpay Merchant Administration Interface', 'woocommerce_worlday' )
											),
			'xmlpassword'			=> array(
											'title' 		=> __( 'Worldpay XML Password', 'woocommerce_worlday' ),
											'type' 			=> 'text',
											'description' 	=> __( '', 'woocommerce_worlday' ),
											'default' 		=> __( 'Created in the Worldpay Merchant Administration Interface', 'woocommerce_worlday' )
											),
			'close_options' 		=> array(
											'title' 		=> __( '', 'woocommerce_worlday' ),
											'type' 			=> 'title',
											'description' 	=> __( '<div style="display:block; border-bottom:1px dotted #000; width:100%; clear:both"></div>', 'woocommerce_worlday' )
											),
			'secondary_options' 	=> array(
											'title' 		=> __( 'Payment Options', 'woocommerce_worlday' ),
											'type' 			=> 'title',
											'description' 	=> __( '<div style="display:block; border-bottom:1px dotted #000; width:100%; clear:both"></div>', 'woocommerce_worlday' )
											),
			'payment_methods'			=> array(
											'title'         => __( 'Paymenr Methods', 'woocommerce_worlday' ),
											'type' 			=> 'select',
											'options' 		=> array('direct'=>'Direct','hosted'=>'Hosted'),
											'description' 	=> __( 'Direct or Hosted?', 'woocommerce_worlday' ),
											'default' 		=> $this->default_payment_methods
											),
			'enable_apple_pay'			=> array(
											'title'         => __( 'Enable Apple Pay?', 'woocommerce_worlday' ),
											'type'          => 'checkbox',
			    							'options'       => array('no'=>'No','yes'=>'Yes'),
											'description' 	=> __( 'Enable Apple pay? This must be enabled by Worldpay in your account.', 'woocommerce_worlday' ),
											'default' 		=> $this->default_applepay
											),
			'enable_google_pay'			=> array(
											'title'         => __( 'Enable Google Pay?', 'woocommerce_worlday' ),
											'type'          => 'checkbox',
			    							'options'       => array('no'=>'No','yes'=>'Yes'),
											'description' 	=> __( 'Enable Google pay? This must be enabled by Worldpay in your account.', 'woocommerce_worlday' ),
											'default' 		=> $this->default_googlepay
											),

		);