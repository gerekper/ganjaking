<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_TRUST_SWIFTLY' ) ) {


	/**
	 * Class WC_AF_TRUST_SWIFTLY.
	 */
	class WC_AF_TRUST_SWIFTLY {

		/**
		 *
		 * Fuction construct() for all hooks with callback functions
		 */
		public function __construct() {

			add_action('woocommerce_after_checkout_billing_form', array( $this, 'verifyButtonOnCheckout'), 10);
			add_action('woocommerce_checkout_before_order_review', array( $this, 'getVerifiedUser'), 10);

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkBeforeCheckout' ), 10, 2 );

			$baseUrl = get_option( 'wc_af_trust_swiftly_base_url' );
			get_option('trust_api_keys_validated');
			$this->full_endpoint = $baseUrl;

			add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'displayCustomOrderData'));
			add_action( 'woocommerce_view_order', array( $this, 'verifyBtnAfterOrderPlaced' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'verifyBtnAfterOrderPlaced' ) );
			add_action('woocommerce_before_checkout_form', array( $this, 'createUserFirst'), 10);
			
		}

		public function createUserFirst(){

			$ts_setting_enable = get_option( 'wc_af_trust_swiftly_type' );
			$verify_method = get_option( 'wc_af_trust_when_to_verify' );
			
			if ('yes' === $ts_setting_enable && 'before_checkout' === $verify_method) {
			
				if ( is_user_logged_in() ) {

					$userstatus = $this->getVerifiedUser();
					if (empty($userstatus)) {

						$current_user = wp_get_current_user();
						$user_id = $current_user->ID;
						$user_email = $current_user->user_email;
						$this->createTsNewUser($user_id, $user_email);
					}
				}
			}
					
		}



		public function verifyButtonOnCheckout() {

			$ts_setting_enable = get_option( 'wc_af_trust_swiftly_type' );
			$verify_method = get_option( 'wc_af_trust_when_to_verify' );
			
			if ('yes' === $ts_setting_enable && 'before_checkout' === $verify_method) {
			
				if ( is_user_logged_in() ) {

					$userstatus = $this->getVerifiedUser();
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
					$user_email = $current_user->user_email;
					$tsVerifyBtn = plugin_dir_url( __FILE__ ) . 'pill-solid-color-ts.png';
					//print_r($userstatus);
					$verify_url = get_user_meta( $user_id, 'trust_swiftly_verify_url', true ); 

					if (is_array($userstatus) && ( 'Pending' === $userstatus['status'] ) && ( 0 === $userstatus['value'] )) {
						update_option('user_verified_from_ts', 'no');
						echo '<div class=ts_verify_btn><a class="" href="' . esc_url($verify_url) . '" target="_blank" role="button">
							<img src="' . esc_url($tsVerifyBtn) . '" alt="trustswiftly" width="60%" height="50%">
						</a></div>';
						echo 'Clicking above button will open Trust Swiftly page in the new tab to continue with your verification process. The current checkout page will keep reloading every 10 seconds until you get verified.';


					} elseif (is_array($userstatus) && ( 'In Process' === $userstatus['status'] ) && ( 1 === $userstatus['value'] )) {
						update_option('user_verified_from_ts', 'no');
						echo '<div class=ts_verify_btn><a class="" href="' . esc_url($verify_url) . '" target="_blank" role="button">
							<img src="' . esc_url($tsVerifyBtn) . '" alt="trustswiftly" width="60%" height="50%">
						</a></div>';
						echo 'Clicking above button will open Trust Swiftly page in the new tab to continue with your verification process. The current checkout page will keep reloading every 10 seconds until you get verified.';

					} elseif (is_array($userstatus) && ( 'Complete' === $userstatus['status'] ) && ( 2 === $userstatus['value'] )) {

						if (isset($verify_url) && !empty($verify_url)) {
							
							update_option('user_verified_from_ts', 'yes');
							echo '<p style="color:green">Trust Swiftly verification is completed.</p>';
						}

						return false;

					} elseif (is_array($userstatus) && ( 'Rejected' === $userstatus['status'] ) && ( 3 === $userstatus['value'] )) {
						update_option('user_verified_from_ts', 'no');
						/*echo '<a class="" href="' . esc_url($verify_url) . '" target="_blank" role="button">
							<img src="' . esc_url($tsVerifyBtn) . '" alt="trustswiftly" width="60%" height="50%">
						</a>'; */
						echo '<p style="color:red">Trust Swiftly verification is Rejected. Please contact administrator.</p>';


					} elseif (is_array($userstatus) && ( 'Complete' === $userstatus['status'] ) && ( 4 === $userstatus['value'] )) {
						update_option('user_verified_from_ts', 'no');
						echo '<div class=ts_verify_btn><a class="" href="' . esc_url($verify_url) . '" target="_blank" role="button">
							<img src="' . esc_url($tsVerifyBtn) . '" alt="trustswiftly" width="60%" height="50%">
						</a></div>';
						echo 'Clicking above button will open Trust Swiftly page in the new tab to continue with your verification process. The current checkout page will keep reloading every 10 seconds until you get verified.';


					} else {

						$this->createTsNewUser($user_id, $user_email);
					} ?> 
					<script type="text/javascript">
						jQuery(document).ready(function () { 

							var btn_clicked = '';
						    btn_clicked = sessionStorage.getItem('verify_btn_click');
								console.log(btn_clicked);

							if('yes' === btn_clicked) {

								console.log('btn_clicked');

								setTimeout(function () {
						        location.reload();
						      }, 10000); 
							}

						    jQuery('.ts_verify_btn').click(function(){
						    
						      setTimeout(function () {

						        sessionStorage.setItem('verify_btn_click', 'yes');
						        console.log('sessionStorage.setItem');

						        location.reload();
						      }, 10000); 
						    }); 

						    jQuery('form').on('submit', function(event){
						       clearTimeout();
						    	console.log('sessionStorage.removeItem');
						       sessionStorage.removeItem('verify_btn_click');
						       
						    }); 

						}); 
					</script>
				<?php }
			}
		}

		/**
		 * Function verifyBtnAfterOrderPlaced 
		 *
		 * @param $order_id
		 */
		public function verifyBtnAfterOrderPlaced() {

			$ts_setting_enable = get_option( 'wc_af_trust_swiftly_type' );
			$verify_method = get_option( 'wc_af_trust_when_to_verify' );
			
			// Load link template
			$tsVerifyBtn = plugin_dir_url( __FILE__ ) . 'pill-solid-color-ts.png';
					

			if ('yes' === $ts_setting_enable && 'after_checkout' === $verify_method) {
			
				if ( is_user_logged_in() ) {

					$userstatus = $this->getVerifiedUser();
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
					$user_email = $current_user->user_email;
					//echo 'Trust Swiftly Verifications is Rejected. Please Contact Administrator!';

					//print_r($userstatus);
					$verify_url = get_user_meta( $user_id, 'trust_swiftly_verify_url', true ); 

					if (is_array($userstatus) && ( 'Pending' === $userstatus['status'] ) && ( 0 === $userstatus['value'] )) {
						
						update_option('user_verified_from_ts', 'no');
						wc_get_template( 'view-order.php', array(
							'tsVerifyBtn' => $tsVerifyBtn,
							'verify_url' => $verify_url
						),
						'woocommerce-view-order',
						untrailingslashit( plugin_dir_path( WooCommerce_Anti_Fraud::get_plugin_file() ) ) . '/templates/' );

						//echo 'Trust Swiftly Verifications is Pending';


					} elseif (is_array($userstatus) && ( 'In Process' === $userstatus['status'] ) && ( 1 === $userstatus['value'] )) {
						update_option('user_verified_from_ts', 'no');

						wc_get_template( 'view-order.php', array(
							'tsVerifyBtn' => $tsVerifyBtn,
							'verify_url' => $verify_url
						),
						'woocommerce-view-order',
						untrailingslashit( plugin_dir_path( WooCommerce_Anti_Fraud::get_plugin_file() ) ) . '/templates/' );

						//echo 'Trust Swiftly Verifications is In Process';

					} elseif (is_array($userstatus) && ( 'Complete' === $userstatus['status'] ) && ( 2 === $userstatus['value'] )) {

						if (isset($verify_url) && !empty($verify_url)) {
							
							update_option('user_verified_from_ts', 'yes');
							echo '<p style="color:green">Trust Swiftly verification is completed.</p>';
						}

						return false;

					} elseif (is_array($userstatus) && ( 'Rejected' === $userstatus['status'] ) && ( 3 === $userstatus['value'] )) {

						/*wc_get_template( 'view-order.php', array(
							'tsVerifyBtn' => $tsVerifyBtn,
							'verify_url' => $verify_url
						),
						'woocommerce-view-order',
						untrailingslashit( plugin_dir_path( WooCommerce_Anti_Fraud::get_plugin_file() ) ) . '/templates/' );*/
						update_option('user_verified_from_ts', 'no');
						
						echo '<p style="color:red">Trust Swiftly verification is Rejected. Please contact administrator.</p>';


					} elseif (is_array($userstatus) && ( 'Complete' === $userstatus['status'] ) && ( 4 === $userstatus['value'] )) {
						update_option('user_verified_from_ts', 'no');
						
						wc_get_template( 'view-order.php', array(
							'tsVerifyBtn' => $tsVerifyBtn,
							'verify_url' => $verify_url
						),
						'woocommerce-view-order',
						untrailingslashit( plugin_dir_path( WooCommerce_Anti_Fraud::get_plugin_file() ) ) . '/templates/' );

						echo 'Trust Swiftly Verifications is Under Review';


					} else {

						$this->createTsNewUser($user_id, $user_email);
					}
				}
			}
		}

		public function createTsNewUser( $user_id, $user_email) {

			$template_id = get_option('wc_af_trust_swiftly_veri_template');
			$post_data = array(
				'email' => $user_email,
				'template_id' => $template_id
			);
			$apiEndPoint = '/api/users';
			$response = $this->remotePostJson($post_data, $apiEndPoint);

			if (isset($response) && !empty($response)) {
				
				$verfy_url = $response['magic_link'];
				$ts_user_id = $response['id'];
				update_user_meta( $user_id, 'trust_swiftly_verify_url', $verfy_url );
				update_user_meta( $user_id, 'trust_swiftly_user_id', $ts_user_id );
			}
		}


		public function checkBeforeCheckout( $order_id, $errors ) {

			if ( ! is_numeric( $order_id ) ) {
				return;
			}

			$order = wc_get_order( $order_id );		

			$ts_setting_enable = get_option( 'wc_af_trust_swiftly_type' );
			$verify_method = get_option( 'wc_af_trust_when_to_verify' );
			
			if ('yes' === $ts_setting_enable && 'before_checkout' === $verify_method) {
				
				$pre_payment_block_message = __( ' Please get verified with Trust Swiftly, and then try again.', 'woocommerce-anti-fraud' );
				

				$ts_risk_score = get_option( 'wc_settings_anti_fraud_strust_swiftly_score' );
				$score_helper = new WC_AF_Score_Helper();
				$score_helper->schedule_fraud_check( $order_id, true );

				$score_points = opmc_hpos_get_post_meta( $order_id, 'wc_af_score', true );
				$circle_points = WC_AF_Score_Helper::invert_score( $score_points );

				if ( $ts_risk_score <= $circle_points ) {

					$order->update_status( 'failed', ' Fraud Check: Calculated risk score is above Trust Swiftly risk threshold.', true );

					$return = array(
						'result' => 'failure',
						'messages' => "<ul class='woocommerce-error' role='alert'><li id='ts_error'>" . $pre_payment_block_message . '</li></ul>',
					);

					wp_send_json( $return );
					wp_die();
				}
			}
		}


		/**
		* Build the HTTP message with the appropriate authentication headers
		*/
		private function create_headers() {

				$apiKey = get_option( 'wc_af_trust_swiftly_api_key' );
				$baseUrl = get_option( 'wc_af_trust_swiftly_base_url' );

			if (isset($apiKey) && !empty($apiKey) && isset($baseUrl) ) {
				
				$headers = array(
				'Authorization' => 'Bearer ' . $apiKey,
				'Content-Type' => 'application/json',
				'User-Agent' => 'opmc/1.0'
				);

				return $headers;
			}
		}


		/**
		* Wrapper for a generic POST call to Trust Swiftly API
		*
		* @return NULL on error
		* @return decoded JSON on success
		* @return false on empty response
		*/
		public function remotePostJson( $post_data, $apiEndPoint) {

			$response = wp_remote_post( $this->full_endpoint . $apiEndPoint, array(
				'headers' => $this->create_headers(),
				'body'    => json_encode($post_data)
			));

			if ( is_wp_error( $response ) ) {

				return false;
			}

			$retrieve_body = wp_remote_retrieve_body( $response );
			$body_data = json_decode( $retrieve_body );
			$body_datas = json_decode(json_encode($body_data), true);
			// print_r($body_datas);
			$response_code = wp_remote_retrieve_response_code($response);

			if ( json_last_error() === JSON_ERROR_NONE ) {

				if ( '200' == $response_code ) {

					return $body_datas;

				}
			}
			return false;
		}

		/**
		* Wrapper for a generic POST call to Trust Swiftly API
		*
		* @return NULL on error
		* @return decoded JSON on success
		* @return false on empty response
		*/
		/*public function remote_put_json( $uri, $params) {

		}*/

		/**
		* Perform a get request to TrustSwiftly and return result as JSON
		*
		* @return JSON response
		* @return NULL on error response
		* @return false on empty response
		*/

		/*public function remote_get_json( $uri, $params = null) {

		}*/

		public function getVerifiedUser() {
			$current_user = wp_get_current_user();
			$user_email = $current_user->user_email;

			$response = $this->remoteGetFilterJson($user_email);
			return $response;
		}

		/**
		* Perform a get request to and return result
		*
		* @return false on empty response
		*/

		public function remoteGetFilterJson( $user_email) {

			$response = wp_remote_get( $this->full_endpoint . '/api/users/?filter[email]=' . $user_email, array(
				'headers' => $this->create_headers(),
			));

			if ( is_wp_error( $response ) ) {

				return false;
			}

			$retrieve_body = wp_remote_retrieve_body( $response );
			$body_data = json_decode( $retrieve_body );
			$body_datas = json_decode(json_encode($body_data), true);
			if ( json_last_error() === JSON_ERROR_NONE ) {
				if ( ! empty( $body_datas ) ) {

					$userVrifySatus = array();

					foreach ( $body_datas as $data ) {
							
						foreach ( $data as $dataa ) {
		
							$userVrifySatus['ts_id'] = isset($dataa['id']) ? $dataa['id'] : '';
							$verifications = isset($dataa['verifications']) ? $dataa['verifications'] : array();

							if (! empty($verifications) && is_array($verifications)) {
								foreach ( $dataa['verifications'] as $dataas ) {

									$userVrifySatus['status'] = $dataas['status']['friendly'];
									$userVrifySatus['value'] = $dataas['status']['value'];
									return $userVrifySatus;
								}
							}
						}	
					}
				}
			}
		}


		public function displayCustomOrderData( $order) {
			/** @var \WC_Order $order */
			$user = $order->get_user();
			$tsUserId = get_user_meta($user->ID, 'trust_swiftly_user_id', true);
			$user_email = $user->user_email;
			
			if (! $tsUserId) {
				return;
			}

			$url = '';

			$url = $this->getTSUserShowUrl($tsUserId);
			if (! $url) {
				return;
			}
			$isVerified = $this->remoteGetFilterJson($user_email);

			if (is_array($isVerified)) {
				
				if (2 === $isVerified['value']) {

					$isVerified = 'yes';

				} else {

					$isVerified = '';
				}
			}

			$msg = __('View User', 'trustswiftly-verifications');

			printf(
				'<h3>%s</h3><a href="%s" target="_blank">%s</a> - %s', 
				'Trust Swiftly', 
				esc_url($url), 
				esc_attr($msg),
				esc_attr($isVerified) ? esc_attr('Complete', 'trustswiftly-verification') : esc_attr('Pending', 'trustswiftly-verification')
			);
		}

		public function getTSUserShowUrl( $tsUserId) {
			
			$ts_setting_enable = get_option( 'wc_af_trust_swiftly_type' );
			$baseUrl = $this->full_endpoint;
			if (! $baseUrl) {
				return '';
			}

			$baseUrl = untrailingslashit($baseUrl) . "/user/{$tsUserId}/show";

			return $baseUrl;
		}
	}
}

