<?php

    		$this->form_fields = array(
				'enabled' 				=> array(
												'title' 		=> __( 'Enable/Disable', 'woocommerce_worlday' ),
												'label' 		=> __( 'Enable WorldPay Form', 'woocommerce_worlday' ),
												'type' 			=> 'checkbox',
												'description' 	=> '',
												'default' 		=> $this->default_enabled
												),
				'method' 				=> array(
												'title' 		=> __( 'All Transactions/Subscription Renewals Only?', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('alltransactions'=>'All Transactions','renewalsonly'=>'Subscription Renewals Only'),
												'description' 	=> __( 'Please read the docs before changing this option from "All Transactions"', 'woocommerce_worlday' ),
												'default' 		=> $this->default_method
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
				    							'default'       => $this->default_debug
												),
				'status' 				=> array(
												'title' 		=> __( 'Status', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('live'=>'Live','testing'=>'Testing'),
												'description' 	=> __( 'Set WorldPay Live/Testing Status.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_status
												),
				'submission'			=> array(
												'title'         => __( 'Submission Method', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('form'=>'Form','url'=>'URL'),
												'description' 	=> __( 'Set WorldPay submission method. By default this is done using the form method which requires an extra (automated) step. You can choose to use the URL method which will submit the form to WorldPay as a URL with the variables appended. If you choose the URL method it is recommended that you also add an MD5 Secret below', 'woocommerce_worlday' ),
												'default' 		=> $this->default_submission
												),
				'instId' 				=> array(
												'title' 		=> __( 'Installation ID', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This should have been supplied by WorldPay when you created your account.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_instId
												),
				'callbackPW' 			=> array(
												'title' 		=> __( 'Payment Response password', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'You MUST set this value here and in your WorldPay Installation.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_callbackPW
												),
				'remoteid'				=> array(
												'title' 		=> __( 'Remote Administration Installation ID', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This is required if you want to use WorldPay for subscription payments or to process refunds', 'woocommerce_worlday' ),
												'default' 		=> $this->default_remoteid
												),
				'remotepw'				=> array(
												'title' 		=> __( 'Remote Administration Installation Password', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This is required if you want to use WorldPay for subscription payments or to process refunds', 'woocommerce_worlday' ),
												'default' 		=> $this->default_remotepw
												),
				'worldpaymd5'			=> array(
												'title' 		=> __( 'MD5 secret for transactions', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'Optional, this must match your entry in the WorldPay Installation. Must be between 20 and 30 characters long with no white space and contain at least one upper case letter, one lower case letter, one number and one symbol (<strong>do not use *</strong>). See docs for more information : <a href="http://docs.woothemes.com/document/worldpay/" target="_blank">http://docs.woothemes.com/document/worldpay/</a><br /><strong>Here is a unique MD5 that will work ' . $this->generate_md5() . '</strong>', 'woocommerce_worlday' ),
												'default' 		=> $this->default_worldpaymd5
												),
				'signaturefields'		=> array(
												'title' 		=> __( 'Signature Fields used to verify transaction', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This is required if you are using an MD5 password and must match your entry in the WorldPay Installation. See docs for more information on what fields can be used and how to build your list: <a href="https://docs.woocommerce.com/document/worldpay/#section-3" target="_blank">https://docs.woocommerce.com/document/worldpay/#section-3</a><br />', 'woocommerce_worlday' ),
												'default' 		=> $this->default_signaturefields
												),
				'dynamiccallback'		=> array(
				    							'title'         => __( 'Dynamic Callback', 'woocommerce_worlday' ),
				    							'type'          => 'checkbox',
				    							'options'       => array('no'=>'No','yes'=>'Yes'),
				    							'label'     	=> class_exists( 'WC_Subscriptions' ) ? __( 'Are you using a Dynamic Callback in your Payment Response URL field?<br /><strong>DO NOT check this option if you are using WorldPay to process your subscription payments / renewals<br />DO NOT check this option unless you are using your Installtion ID on more than one site.</strong>' , 'woocommerce_worlday' ) : __( 'Are you using a Dynamic Callback in your Payment Response URL field?' , 'woocommerce_worlday' ),
				    							'default'       => $this->default_dynamiccallback
												),
				'worldpaydebugemail' 	=> array(
												'title' 		=> __( 'Email Address for errors', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'Enter an email address to send any error messages to, if a subscription cancellation or refund fails, who should be notified?', 'woocommerce_worlday' ),
												'default' 		=> get_bloginfo( 'admin_email' )
												),
				'authMode' 				=> array(
												'title' 		=> __( 'Authorisation Mode', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('A'=>'Full Auth','E'=>'Pre Auth'),
												'description' 	=> __( 'Enable Full Auth or Pre Auth, only change this if you know what you are doing - Pre Auth preauthorises the card but DOES NOT take the funds!', 'woocommerce_worlday' ),
												'default' 		=> $this->default_authMode
												),
				'accid' 				=> array(
												'title' 		=> __( 'Payment Account ID', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This specifies which account will receive the funds. Only add account details here if you are not using the default account to receive money, most people will leave this blank.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_accid
												),
				'checkout_options' 		=> array(
												'title' 		=> __( 'Checkout Options', 'woocommerce_worlday' ),
												'type' 			=> 'title',
												'description' 	=> __( '<div style="display:block; border-bottom:1px dotted #000; width:100%; clear:both">This section controls what is shown on the checkout page.</div>', 'woocommerce_worlday' )
												),	

				'title' 				=> array(
												'title' 		=> __( 'Title', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_title
												),

				'description' 			=> array(
												'title' 		=> __( 'Description', 'woocommerce_worlday' ),
												'type' 			=> 'textarea',
												'description' 	=> __( 'This controls the description which the user sees during checkout.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_description
												),
				'wplogo' 				=> array(
												'title' 		=> __( 'WorldPay Logo', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'Include the "Payments Powered by WorldPay" logo on the checkout.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_wplogo
												),
				'vmelogo' 				=> array(
												'title' 		=> __( 'V.me Logo', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'Include the V.me logo on the checkout.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_vmelogo
												),

				'cardtypes'				=> array(
												'title' 		=> __( 'Accepted Cards', 'woocommerce_worlday' ), 
												'type' 			=> 'multiselect',
												'class'			=> 'wc-enhanced-select',
												'css'         	=> 'width: 350px;', 
												'description' 	=> __( 'Select which card types to accept.', 'woocommerce_worlday' ), 
												'default' 		=> '',
												'options' 		=> array(
																		'MasterCard'		=> 'MasterCard',
																		'Maestro'			=> 'Maestro', 
																		'Visa'				=> 'Visa',
																		'Visa Debit'		=> 'Visa Debit',
																		'Visa Electron'		=> 'Visa Electron',
																		'American Express' 	=> 'American Express',
																		'Diners'			=> 'Diners',
																		'JCB'				=> 'JCB',
																		'Laser'				=> 'Laser',
																		'ELV'				=> 'ELV',
																		'PayPal'			=> 'PayPal'
																	),
												),
				'smart_quotes' 			=> array(
												'title' 		=> __( 'Smart Quotes', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'If your customers can not checkout when there are apostophes in the name field then set this to Yes.', 'woocommerce_worlday' ),
												'default' 		=> 'yes'
												),
				'order_button_text'		=> array(
												'title' 		=> __( 'Checkout Pay Button Text', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This controls the pay button text shown during checkout.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_order_button_text
												),

				'worldpay_options' 		=> array(
												'title' 		=> __( 'WorldPay Options', 'woocommerce_worlday' ),
												'type' 			=> 'title',
												'description' 	=> __( '<div style="display:block; border-bottom:1px dotted #000; width:100%;">This section controls what is shown on the WorldPay Form.</div>', 'woocommerce_worlday' )
												),	

				'orderDesc' 			=> array(
												'title' 		=> __( 'Order Decription', 'woocommerce_worlday' ),
												'type' 			=> 'text',
												'description' 	=> __( 'This is what appears on the payment screen when the customer lands at WorldPay and is also shown on statements and emails between your store and the shopper. Add {ordernum} to include the order number in the description', 'woocommerce_worlday' ),
												'default' 		=> $this->default_orderDesc
												),
				'fixContact' 			=> array(
												'title' 		=> __( 'Fix Customer Info', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'If this is set to yes then the customer will not be able to change the information they entered on your site when they get to WorldPay', 'woocommerce_worlday' ),
												'default' 		=> $this->default_fixContact
												),

				'hideContact' 			=> array(
												'title' 		=> __( 'Hide Customer Info', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'If this is set to yes then the customer will not be able to see the information they entered on your site when they get to WorldPay', 'woocommerce_worlday' ),
												'default' 		=> $this->default_hideContact
												),

				'hideCurrency' 			=> array(
												'title' 		=> __( 'Hide Currency', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'If this is set to no then the customer will be able to change the currency at WorldPay. Exchange rates are set by WorldPay.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_hideCurrency
												),

				'lang' 					=> array(
												'title' 		=> __( 'Language', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'Set a default language shown at WorldPay. If you set the \'Remove Language Menu\' option to NO then this setting determines the Worldpay language.', 'woocommerce_worlday' ),
												'default' 		=> $this->default_lang
												),

				'noLanguageMenu' 		=> array(
												'title' 		=> __( 'Remove Language Menu', 'woocommerce_worlday' ),
												'type' 			=> 'select',
												'options' 		=> array('yes'=>'Yes','no'=>'No'),
												'description' 	=> __( 'This suppresses the display of the language menu at WorldPay', 'woocommerce_worlday' ),
												'default' 		=> $this->default_noLanguageMenu
												),
				'close_options' 		=> array(
												'title' 		=> __( '', 'woocommerce_worlday' ),
												'type' 			=> 'title',
												'description' 	=> __( '<div style="display:block; border-bottom:1px dotted #000; width:100%; clear:both"></div>', 'woocommerce_worlday' )
												),

			);