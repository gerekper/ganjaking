<?php

/**
 * Insights SDK File
 * SDK Version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Insights_SDK')) {

	/**
	 * Insights SDK Class
	 */
	class Insights_SDK {

		public $version = '1.1.0';

		public $dci_name;
		public $dci_allow_name;
		public $dci_date_name;
		public $dci_count_name;
		public $nonce;
		public $params;

		/**
		 * Insights SDK Version
		 * param array $params
		 * @return void
		 */
		public function __construct($params) {
			$this->params = $params;

			// add_action( 'admin_enqueue_scripts', array( $this, 'dci_enqueue_scripts' ) );
			add_action('wp_ajax_dci_sdk_insights', array($this, 'dci_sdk_insights'));
			add_action('wp_ajax_dci_sdk_dismiss_notice', array($this, 'dci_sdk_dismiss_notice'));

			$security_key         = md5($params['plugin_name']);
			$this->dci_name       = 'dci_' . str_replace('-', '_', sanitize_title($params['plugin_name']) . '_' . $security_key);
			$this->dci_allow_name = 'dci_allow_status_' . $this->dci_name;
			$this->dci_date_name  = 'dci_status_date_' . $this->dci_name;
			$dci_count_name       = 'dci_attempt_count_' . $this->dci_name;
			$dci_status_db        = get_option($this->dci_allow_name, false);

			$this->nonce = wp_create_nonce($this->dci_allow_name);

			/**
			 * Modal Trigger if not init
			 * Show Notice Modal
			 */
			 if (!$dci_status_db) {
				$this->notice_modal($params);
			 	 return;
			}

			/**
			 * If Disallow
			 */
			if ('disallow' == $dci_status_db) {
				return;
			}

			/**
			 * Skip & Date Not Expired
			 * Show Notice Modal
			 */
			if ('skip' == $dci_status_db && true == $this->check_date()) {
				$this->notice_modal($params);
				return;
			}

			/**
			 * Allowed & Date not Expired
			 * No need send data to server
			 * Else Send Data to Server
			 */
			if (!$this->check_date()) {
				return;
			}

			/**
			 * Count attempt every time
			 */
			$dci_attempt = get_option($dci_count_name, 0);

			if (!$dci_attempt) {
				update_option($dci_count_name, 1);
			}
			update_option($dci_count_name, $dci_attempt + 1);

			/**
			 * Next schedule date for attempt
			 */
			update_option($this->dci_date_name, gmdate('Y-m-d', strtotime("+1 month")));

			/**
			 * Prepare data
			 */
			$this->data_prepare($params);
		}

		/**
		 * Notice Modal
		 *
		 * @return void
		 */
		public function notice_modal($params) {

			add_action('admin_enqueue_scripts', array($this, 'dci_enqueue_scripts'));

			if ($params['current_page'] !== $params['menu_slug']) {
				if (!get_transient('dismissed_notice_' . $this->dci_name)) {
					add_action('admin_notices', array($this, 'display_global_notice'));
				}
				return;
			}
			add_action('admin_notices', array($this, 'display_global_notice'));

			$dci_data               = array();
			$dci_data['name']       = $this->dci_name;
			$dci_data['date_name']  = $this->dci_date_name;
			$dci_data['allow_name'] = $this->dci_allow_name;
			$dci_data['nonce']      = wp_create_nonce('dci_sdk');

			include_once dirname(__FILE__) . '/notice.php';

			add_action(
				'in_admin_header',
				function () use ($dci_data) {
					if (function_exists('dci_popup_notice')) {
						dci_popup_notice($dci_data);
					}
				},
				99999
			);
		}

		/**
		 * If date is expired immidiate action
		 *
		 * @return boolean
		 */
		public function check_date() {
			$current_date    = strtotime(gmdate('Y-m-d'));
			$dci_status_date = strtotime(get_option($this->dci_date_name, false));

			if (!$dci_status_date) {
				return true;
			}

			if ($dci_status_date && $current_date >= $dci_status_date) {
				return true;
			}
			return false;
		}

		/**
		 * Modal Trigger
		 *
		 * Not used
		 * @return boolean
		 */
		public function modal_trigger() {

			if (!wp_verify_nonce($this->dci_allow_name, $this->nonce)) {
				echo 'Nonce Verification Failed';
				return false;
			}

			$sanitized_status = sanitize_text_field($_GET['dci_allow_status']);

			if ($sanitized_status == 'skip') {
				update_option($this->dci_allow_name, 'skip');
				/**
				 * Next schedule date for attempt
				 */
				update_option($this->dci_date_name, gmdate('Y-m-d', strtotime("+1 month")));
				return false;
			} elseif ($sanitized_status == 'yes') {
				update_option($this->dci_allow_name, 'yes');
				return true;
			}

			return false;
		}

		/**
		 * Reset Options Settings
		 * @return void
		 */
		public function reset_settings() {
			delete_option($this->dci_allow_name);
			delete_option($this->dci_date_name);
		}

		/**
		 * Data prepare for send server
		 *
		 * @param array $server_url
		 * @return void
		 */
		public function data_prepare($params) {
			$server_url  = isset($params['api_endpoint']) ? $params['api_endpoint'] : false;
			$public_key  = isset($params['public_key']) ? $params['public_key'] : false;
			$custom_data = isset($params['custom_data']) ? $params['custom_data'] : false;
			$product_id  = isset($params['product_id']) ? $params['product_id'] : false;

			if (!$server_url || !$public_key) {
				return;
			}

			/**
			 * ==================================
			 * 
			 * Start Own Custom Data Important
			 * 
			 * ==================================
			 */
			$custom_data = array(
				'active_modules' => get_option('element_pack_active_modules', false),
				'third_party'    => get_option('element_pack_third_party_widget', false),
				'extend'         => get_option('element_pack_elementor_extend', false),
				'other_settings' => get_option('element_pack_other_settings', false),
			);

			$custom_data = wp_json_encode($custom_data, true);

			/**
			 * ==================================
			 * 
			 * End Own Custom Data Important
			 * 
			 * ==================================
			 */

			$data                = array();
			$data['public_key']  = $public_key;
			$data['product_id']  = $product_id;
			$data['custom_data'] = $custom_data;

			$non_sensitive_data = $this->dci_non_sensitve_data();
			$data               = array_merge($data, $non_sensitive_data);

			$this->dci_send_data_to_server($server_url, $data);
		}

		/**
		 * Non sensitive data
		 *
		 * @return array
		 */
		public function dci_non_sensitve_data() {
			$current_user = wp_get_current_user();

			$first_name = $current_user->first_name;
			$last_name  = $current_user->last_name;

			if (empty($first_name) && empty($last_name)) {
				$first_name = null;
				$last_name  = $current_user->display_name;
			}

			$data = array(
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'email'        => $current_user->user_email,
				'user_role'    => $current_user->roles[0],
				'website_url'  => $current_user->user_url,
				'website_data' => array(
					'website_name' => get_bloginfo('name'),
					'wp_version'   => get_bloginfo('version'),
					'php_version'  => phpversion(),
					'locale'       => get_locale(),
					'sdk_version'  => $this->version,
				),
			);

			return $data;
		}

		/**
		 * Send data to server
		 *
		 * @param [string] $server_url
		 * @param [array] $data
		 * @return void
		 */
		public function dci_send_data_to_server($server_url, $data = null) {

			$args = array(
				'method'  => 'POST',
				'timeout' => 60,
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-API-KEY'    => $data['public_key'],
				),
				'body'    => json_encode($data),
			);

			$response = wp_remote_request($server_url, $args);

			if (is_wp_error($response)) {
				// echo 'Error: ' . $response->get_error_message();
				$this->reset_settings();
			} else {
				$response_data = wp_remote_retrieve_body($response);
				$response_data = json_decode($response_data, true);
				// print_r( $response_data );
				if (isset($response_data['data']['status']) && 401 == $response_data['data']['status']) {
					update_option($this->dci_date_name, gmdate('Y-m-d', strtotime("+3 days")));
				}
			}
		}

		/**
		 * Ajax callback
		 */
		public function dci_sdk_insights() {
			$sanitized_status = isset($_POST['button_val']) ? sanitize_text_field($_POST['button_val']) : '';
			$nonce            = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			$allow_name       = isset($_POST['allow_name']) ? sanitize_text_field($_POST['allow_name']) : '';
			$date_name        = isset($_POST['date_name']) ? sanitize_text_field($_POST['date_name']) : '';

			if (!wp_verify_nonce($nonce, 'dci_sdk')) {
				wp_send_json(array(
					'status'  => 'error',
					'title'   => 'Error',
					'message' => 'Nonce verification failed',
				));
				wp_die();
			}

			if ('disallow' == $sanitized_status) {
				update_option($allow_name, 'disallow');
			}

			if ($sanitized_status == 'skip') {
				update_option($allow_name, 'skip');
				/**
				 * Next schedule date for attempt
				 */
				update_option($date_name, gmdate('Y-m-d', strtotime("+1 month")));
			} elseif ($sanitized_status == 'yes') {
				update_option($allow_name, 'yes');
			}

			wp_send_json(array(
				'status'  => 'success',
				'title'   => 'Success',
				'message' => 'Success.',
			));
			wp_die();
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 1.0.0
		 */
		public function dci_enqueue_scripts() {
			wp_enqueue_style('dci-sdk', plugins_url('assets/css/dci.css', __FILE__), array(), '1.1.0');
			wp_enqueue_script('dci-sdk', plugins_url('assets/js/dci.js', __FILE__), array('jquery'), '1.1.0', true);
		}

		/**
		 * Display Global Notice
		 *
		 * @return void
		 */
		public function display_global_notice() {
			$menu_slug = isset($this->params['menu_slug']) ? $this->params['menu_slug'] : 'javascript:void(0);';

			$admin_url = add_query_arg(array(
				'page' => $menu_slug,
			), admin_url('admin.php'));

			$plugin_title = isset($this->params['plugin_title']) ? $this->params['plugin_title'] : '';
			$plugin_msg   = isset($this->params['plugin_msg']) ? $this->params['plugin_msg'] : '';
			$plugin_icon  = isset($this->params['plugin_icon']) ? $this->params['plugin_icon'] : '';

?>
			<div class="dci-global-notice dci-notice-data notice notice-success is-dismissible">
				<div class="dci-global-header bdt-dci-notice-global-header">
					<?php if (!empty($plugin_icon)) : ?>
						<div class="bdt-dci-notice-logo">
							<img src="<?php echo esc_url($plugin_icon); ?>" alt="icon">
						</div>
					<?php endif; ?>
					<div class="bdt-dci-notice-content">
						<h3>
							<?php printf($plugin_title); ?>
						</h3>
						<?php printf($plugin_msg); ?>
						<p>
							<a href="<?php echo esc_url($admin_url); ?>">Learn More</a>?
						</p>
						<input type="hidden" name="dci_name" value="<?php echo esc_html($this->dci_name); ?>">
						<input type="hidden" name="dci_date_name" value="<?php echo esc_html($this->dci_date_name); ?>">
						<input type="hidden" name="dci_allow_name" value="<?php echo esc_html($this->dci_allow_name); ?>">
						<input type="hidden" name="nonce" value="<?php echo esc_html(wp_create_nonce('dci_sdk')); ?>">
			
						<div class="bdt-dci-notice-button-wrap">
							<button name="dci_allow_status" value="yes" class="dci-button-allow">
								Yes, I'd Love To Contribute
							</button>
							<button name="dci_allow_status" value="skip" class="dci-button-skip">
								Skip For Now
							</button>
							<button name="dci_allow_status" value="disallow" class="dci-button-disallow dci-button-danger">
								No Thanks
							</button>
						</div>
					</div>
				</div>
						
			</div>

<?php
		}

		/**
		 * Dismiss Notice
		 *
		 * @return void
		 */
		public function dci_sdk_dismiss_notice() {
			$nonce    = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			$dci_name = isset($_POST['dci_name']) ? sanitize_text_field($_POST['dci_name']) : '';

			if (!wp_verify_nonce($nonce, 'dci_sdk')) {
				wp_send_json(array(
					'status'  => 'error',
					'title'   => 'Error',
					'message' => 'Nonce verification failed',
				));
				wp_die();
			}

			set_transient('dismissed_notice_' . $dci_name, true, 30 * DAY_IN_SECONDS);

			wp_send_json(array(
				'status'  => 'success',
				'title'   => 'Success',
				'message' => 'Success.',
			));
			wp_die();
		}
	}
}

/**
 * Main Insights Function
 */
if (!function_exists('dci_sdk_insights')) {
	function dci_sdk_insights($params) {
		if (class_exists('Insights_SDK')) {
			new Insights_SDK($params);
		}
	}
}
