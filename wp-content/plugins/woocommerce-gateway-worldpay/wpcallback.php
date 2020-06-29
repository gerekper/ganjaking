<?php
		$order 				  = '';
		$transId 			  = '';
		$transStatus 		  = '';
		$transTime 			  = '';
		$authAmount 		  = '';
		$authCurrency 		  = '';
		$authAmountString 	  = '';
		$rawAuthMessage 	  = '';
		$rawAuthCode 		  = '';
		$callbackPW 		  = '';
		$cardType 			  = '';
		$countryMatch 		  = '';
		$AVS 				  = '';			
		$url 				  = '';
		$MC_transactionNumber = '';
		$futurePayId		  = '';
		$futurePayStatusChange= '';

		if ( (isset($_REQUEST["transId"]) && $_REQUEST["transStatus"]=='Y') || (isset($_REQUEST["futurePayId"]) && $_REQUEST["transStatus"]=='Y') ) {
			global $wpdb, $woocommerce;

			/**
			 * Need to load wp-load.php so that we can use all of the
			 * WordPress / WooCommerce / Subscriptions functions
			 */
			$rooturl = str_replace( 'wp-content/plugins/woocommerce-gateway-worldpay/wpcallback.php','',$_SERVER['SCRIPT_FILENAME'] );
			// Bloody windows hosting!
			$rooturl = str_replace( 'wp-content\plugins\woocommerce-gateway-worldpay\wpcallback.php','',$rooturl );
			require( $rooturl . 'wp-load.php' );

			$woocommerce_worldpay_settings 	= get_option( 'woocommerce_worldpay_settings' );
			$settings_callbackPW			= $woocommerce_worldpay_settings['callbackPW'];

			$order 				  = addslashes( $_REQUEST["MC_order"] );
			$transId 			  = addslashes( $_REQUEST["transId"] );
			$transStatus 		  = addslashes( $_REQUEST["transStatus"] ); 	// (Y or C)
			$transTime 			  = addslashes( $_REQUEST["transTime"] );   	// (UnixTime)
			$authAmount 		  = addslashes( $_REQUEST["authAmount"] );
			$authCurrency 		  = addslashes( $_REQUEST["authCurrency"] );
			$authAmountString 	  = addslashes( $_REQUEST["authAmountString"] );
			$rawAuthMessage 	  = addslashes( $_REQUEST["rawAuthMessage"] );
			$rawAuthCode 		  = addslashes( $_REQUEST["rawAuthCode"] );
			$callbackPW 		  = addslashes( $_REQUEST["callbackPW"] );
			$cardType 			  = addslashes( $_REQUEST["cardType"] );
			$countryMatch 		  = addslashes( $_REQUEST["countryMatch"] );
			$AVS 				  = addslashes( $_REQUEST["AVS"] );			
			$url 				  = addslashes( $_REQUEST["MC_SuccessURL"] );
			$MC_transactionNumber = addslashes( $_REQUEST["MC_transactionNumber"] );

			if ( isset($_POST["futurePayId"]) ) {
				$futurePayId		  = addslashes( $_REQUEST["futurePayId"] );
			}

			if ( isset($_POST["futurePayStatusChange"]) ) {
				$futurePayStatusChange= addslashes( $_REQUEST["futurePayStatusChange"] );
			}
			
			// Windows hosting uses $_SERVER['HTTPS'] == 'off'
			$subscriptionurl = ( empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' )
    						 ? "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] 
    						 : "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
							 
			$subscriptionurl = str_replace( 'wp-content/plugins/woocommerce-gateway-worldpay/wpcallback.php','',$subscriptionurl );
			$subscriptionurl = $subscriptionurl . '&wc-api=WC_Gateway_Worldpay_Form';
			
			$worldpay_args_array = array(
				'order' 			  	=> $order,
				'transId' 				=> $transId,
				'transStatus' 			=> $transStatus,
				'transTime' 			=> $transTime,
				'authAmount' 			=> $authAmount,
				'authCurrency' 			=> $authCurrency,
				'rawAuthMessage' 		=> $rawAuthMessage,
				'rawAuthCode' 			=> $rawAuthCode,
				'callbackPW' 			=> $callbackPW,
				'cardType' 				=> $cardType,
				'countryMatch' 			=> $countryMatch,
				'AVS' 					=> $AVS,
				'MC_transactionNumber' 	=> $MC_transactionNumber,
				'futurePayId'			=> $futurePayId,
				'futurePayStatusChange'	=> $futurePayStatusChange,
				'subscriptionurl'		=> $subscriptionurl
			);

			/**
			 * Process Subscription orders here, redirect does not work
			 *
			 * MC_transactionNumber is only set for normal orders
			 */
			if ( !$MC_transactionNumber || $MC_transactionNumber == '' ) {

				// Get the order id based on the futurepayid
				$orderid = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta 
											WHERE meta_key = '_futurepayid' 
											AND meta_value = '".$futurePayId."'
											LIMIT 1"
										 );
										 
				$order 	 = new WC_Order( (int) $orderid->post_id );

				// Some kind of subscription update
				if ( (class_exists( 'WC_Subscriptions' ) && $futurePayStatusChange == 'Merchant Cancelled') || (class_exists( 'WC_Subscriptions' ) && $futurePayStatusChange == 'Customer Cancelled') ) {
					$order->add_order_note( __('WorldPay Subscription Notice : ' . $futurePayStatusChange, 'woothemes') );
					// Cancel the subscription
					WC_Subscriptions_Manager::cancel_subscriptions_for_order( $order->id );
				}
				
				if ( $rawAuthCode == 'D' && class_exists( 'WC_Subscriptions' ) ) {
					// Record failed payment
					WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order->id );
					// Cancel the subscription
					WC_Subscriptions_Manager::cancel_subscriptions_for_order( $order->id );				
				}
				
				if ( $rawAuthCode == 'A' && class_exists( 'WC_Subscriptions' ) ) {
					// Record successful payment
					WC_Subscriptions_Manager::process_subscription_payments_on_order( $order->id );				
				}

			 	/**
	 			 * Make sure the order notes contain the FuturePayID
				 * and add it as post_meta so we can find it easily when WorldPay sends 
				 * updates about payments / cancellations etc
				 */
				$orderNotes = '';

				if ( function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order ) ) ) {
					$orderNotes .=	'<br /><!-- FUTURE PAY-->';
					$orderNotes .=	'<br />FuturePayID : ' 	. $futurePayId;
					$orderNotes .=	'<br /><!-- FUTURE PAY-->';
					update_post_meta( $order->id, '_futurepayid', $futurePayId );
				} elseif( class_exists( 'WC_Subscriptions' ) && WC_Subscriptions_Order::order_contains_subscription( $order->id ) ) {
					$orderNotes .=	'<br /><!-- FUTURE PAY-->';
					$orderNotes .=	'<br />FuturePayID : ' 	. $futurePayId;
					$orderNotes .=	'<br /><!-- FUTURE PAY-->';
					update_post_meta( $order->id, '_futurepayid', $futurePayId );
				}

				$orderNotes .=	'<br />transId : ' 			. $transId;
				$orderNotes .=	'<br />transStatus : ' 		. $transStatus;
				$orderNotes .=	'<br />transTime : '		. $transTime;
				$orderNotes .=	'<br />authAmount : ' 		. $authAmount;
				$orderNotes .=	'<br />authCurrency : ' 	. $authCurrency;
				$orderNotes .=	'<br />authAmountString : ' . $authAmountString;
				$orderNotes .=	'<br />rawAuthMessage : ' 	. $rawAuthMessage;
				$orderNotes .=	'<br />rawAuthCode : ' 		. $rawAuthCode;
				$orderNotes .=	'<br />cardType : ' 		. $cardType;
				$orderNotes .=	'<br />countryMatch : ' 	. $countryMatch;
				$orderNotes .=	'<br />AVS : ' 				. $AVS;
				
				$order->add_order_note( __('WorldPay payment completed.' . $orderNotes, 'woothemes') );
				
			} else {

				/**
				 * This is an ordinary payment, carry on
				 */
				$worldpay_args 		= array();
				foreach( $worldpay_args_array as $param => $value ) {
					$worldpay_args[] = "$param=$value";
				}

				$worldpay_args 		= implode('&', $worldpay_args);

				$worldpaycrypt_b64  = $worldpay_args;
				$worldpaycrypt_b64 	= worldpaysimpleXor( $worldpaycrypt_b64, $settings_callbackPW );
				$worldpaycrypt_b64 	= base64_encode( $worldpaycrypt_b64 );

				// Save callback info to database
				update_post_meta( $order, '_worldpay_crypt', $worldpaycrypt_b64 );
			
				$url 				= $subscriptionurl .'&order='. $order .'&callback=y';
				
        		echo "<meta http-equiv='Refresh' content='1; Url=\"$url\"'>";
				
			}
			
		} else {

			$url = $_REQUEST["MC_FailureURL"];
        	echo "<meta http-equiv='Refresh' content='1; Url=\"$url\"'>";
			
		}

	// Added for debugging
	function mailcontent( $worldpay_args_array ) {
		
		$content = '';
		
		foreach ( $worldpay_args_array as $key => $value ) {
			$content .= $key . ' => ' . $value . "\n\r";
		}
		
		return $content;
		
	}

	/**
	 * A Simple Xor encryption algorithm
	 *
	 * [worldpaysimpleXor description]
	 * @param  [type] $text [description]
	 * @param  [type] $key  [description]
	 * @return [type]       [description]
	 */
	function worldpaysimpleXor($text, $key) {
	// Initialise key array
		$key_ascii_array = array();
	
		// Initialise output variable
		$output = "";
	
		// Convert $key into array of ASCII values
		for($i = 0; $i < strlen($key); $i++){
			$key_ascii_array[$i] = ord(substr($key, $i, 1));
		}
	
		// Step through string a character at a time
		for($i = 0; $i < strlen($text); $i++) {
			// Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the
			// two, get the character from the result
			$output .= chr(ord(substr($text, $i, 1)) ^ ($key_ascii_array[$i % strlen($key)]));
		}
	
		// Return the result
		return $output;
	} // END simpleXor

?>