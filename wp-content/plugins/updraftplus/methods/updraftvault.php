<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

updraft_try_include_file('methods/s3.php', 'require_once');

class UpdraftPlus_BackupModule_updraftvault extends UpdraftPlus_BackupModule_s3 {

	private $vault_mothership = 'https://vault.updraftplus.com/plugin-info/';
	
	private $vault_config;

	/**
	 * Decides whether vault config should be printed or not
	 *
	 * @var Boolean
	 */
	private $vault_in_config_print;
	
	protected $quota_transient_used = false;

	protected $provider_can_use_aws_sdk = true;
	
	protected $provider_has_regions = true;

	/**
	 * Register backup-related hooks (filters and actions) that get called in the parent method for uploading backup archives
	 *
	 * @param Array $backup_array - a list of file names (basenames) (within UD's directory) to be uploaded
	 *
	 * @return Mixed - return (boolean)false to indicate failure, or anything else to have it passed back at the delete stage (most useful for a storage object).
	 */
	public function backup($backup_array) {
		add_filter('updraft_updraftvault_storageclass', array($this, 'maybe_switch_to_ia_storage_class'));
		return parent::backup($backup_array);
	}

	/**
	 * Determine whether to switch to IA S3 storage class rather than use the existing one (WordPress filter updraft_updraftvault_storageclass)
	 *
	 * @param String $class Suggested storage class
	 *
	 * @return String Filtered value
	 */
	public function maybe_switch_to_ia_storage_class($class) {

		$files_schedule = UpdraftPlus_Options::get_updraft_option('updraft_interval');
		$db_schedule = UpdraftPlus_Options::get_updraft_option('updraft_interval_database');
		$retain_files = max(1, (int) UpdraftPlus_Options::get_updraft_option('updraft_retain'));
		$retain_db = max(1, (int) UpdraftPlus_Options::get_updraft_option('updraft_retain_db'));
		$schedules = wp_get_schedules();
		// $schedules will not contain the variable indexed by key named 'manual', so if it's a manual backup then the code below won't do anything
		switch ($this->current_upload_entity) { // 1296000 = 15 days in seconds
			case 'databases':
				if ('' !== $db_schedule && isset($schedules[$db_schedule]) && $schedules[$db_schedule]['interval'] * $retain_db > 1296000) $class = 'STANDARD_IA';
				break;
			case 'files':
				if ('' !== $files_schedule && isset($schedules[$files_schedule]) && $schedules[$files_schedule]['interval'] * $retain_files > 1296000) $class = 'STANDARD_IA';
				break;
		}
		return $class;
	}

	/**
	 * This function makes testing easier, rather than having to change the URLs in multiple places
	 *
	 * @param  Boolean|string $which_page specifies which page to get the URL for
	 * @return String
	 */
	private function get_url($which_page = false) {
		$base = defined('UPDRAFTPLUS_VAULT_SHOP_BASE') ? UPDRAFTPLUS_VAULT_SHOP_BASE : 'https://updraftplus.com/shop/';
		switch ($which_page) {
			case 'get_more_quota':
				return apply_filters('updraftplus_com_link', $base.'product-category/updraftplus-vault/');
				break;
			case 'more_vault_info_faqs':
				return apply_filters('updraftplus_com_link', 'https://updraftplus.com/support/updraftplus-vault-faqs/');
				break;
			case 'more_vault_info_landing':
				return apply_filters('updraftplus_com_link', 'https://updraftplus.com/landing/vault');
				break;
			case 'vault_forgotten_credentials_links':
				return apply_filters('updraftplus_com_link', 'https://updraftplus.com/my-account/lost-password/');
				break;
			default:
				return apply_filters('updraftplus_com_link', $base);
				break;
		}
	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not mentioned are asuumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'conditional_logic');
	}
	
	/**
	 * Retrieve default options for this remote storage module.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'token' => '',
			'email' => '',
			'quota' => -1
		);
	}

	/**
	 * Retrieve specific options for this remote storage module
	 *
	 * @param  Array $config an array of config options
	 * @return Array - an array of options
	 */
	protected function vault_set_config($config) {
		$config['whoweare'] = 'UpdraftVault';
		$config['whoweare_long'] = __('UpdraftVault', 'updraftplus');
		$config['key'] = 'updraftvault';
		$this->vault_config = $config;
	}

	/**
	 * Gets the UpdraftVault configuration and credentials
	 *
	 * @param Boolean $force_refresh - if set, and if relevant, don't use cached credentials, but get them afresh
	 *
	 * @return Array An array containing the Amazon S3 credentials (accesskey, secretkey, etc.)
	 *				 along with some configuration values.
	 */
	public function get_config($force_refresh = false) {

		global $updraftplus;
		
		if (!$force_refresh) {
			// Have we already done this?
			if (!empty($this->vault_config)) return $this->vault_config;

			// Stored in the job?
			if ($job_config = $this->jobdata_get('config', null, 'updraftvault_config')) {
				if (!empty($job_config) && is_array($job_config)) {
					$this->vault_config = $job_config;
					return $job_config;
				}
			}
		}

		// Pass back empty settings, if nothing better can be found - this ensures that the error eventually is raised in the right place
		$config = array('accesskey' => '', 'secretkey' => '', 'path' => '');
		$config['whoweare'] = 'Updraft Vault';
		$config['whoweare_long'] = __('Updraft Vault', 'updraftplus');
		$config['key'] = 'updraftvault';

		// Get the stored options
		$opts = $this->get_options();

		if (!is_array($opts) || empty($opts['token']) || empty($opts['email'])) {
			// Not connected. Skip DB so that it doesn't show in the UI, which confuses people (e.g. when rescanning remote storage)
			$this->log('this site has not been connected - check your settings', 'notice', false, true);
			$config['error'] = array('message' => 'site_not_connected', 'values' => array());
			
			$this->vault_config = $config;
			$this->jobdata_set('config', $config);
			return $config;
		}

		$site_id = $updraftplus->siteid();

		$this->log("requesting access details (sid=$site_id, email=".$opts['email'].")");

		// Request the credentials using our token
		$post_body = array(
			'e' => (string) $opts['email'],
			'sid' => $site_id,
			'token' => (string) $opts['token'],
			'su' => base64_encode(home_url())
		);

		if (!empty($this->vault_in_config_print)) {
			// In this case, all that the get_config() is being done for is to get the quota info. Send back the cached quota info instead (rather than have an HTTP trip every time the settings page is loaded). The config will get updated whenever there's a backup, or the user presses the link to update.
			$getconfig = get_transient('udvault_last_config');
		}

		// Use SSL to prevent snooping
		if (empty($getconfig) || !is_array($getconfig) || empty($getconfig['accesskey'])) {
			$config_array = apply_filters('updraftplus_vault_config_add_headers', array('timeout' => 25, 'body' => $post_body));
			
			$getconfig = wp_remote_post($this->vault_mothership.'/?udm_action=vault_getconfig', $config_array);
		}
		
		$details_retrieved = false;
		$cache_in_job = false;
		if (!is_wp_error($getconfig) && false != $getconfig && isset($getconfig['body'])) {

			$response_code = wp_remote_retrieve_response_code($getconfig);
		
			if ($response_code >= 200 && $response_code < 300) {
				$response = json_decode(wp_remote_retrieve_body($getconfig), true);
				if (is_array($response) && isset($response['user_messages']) && is_array($response['user_messages'])) {
					foreach ($response['user_messages'] as $message) {
						if (!is_array($message)) continue;
						$msg_txt = $this->vault_translate_remote_message($message['message'], $message['code']);
						$this->log($msg_txt, $message['level'], $message['code']);
					}
				}

				if (is_array($response) && isset($response['accesskey']) && isset($response['secretkey']) && isset($response['path'])) {
					$details_retrieved = true;
					$cache_in_job = true;
					$opts['last_config']['accesskey'] = $response['accesskey'];
					$opts['last_config']['secretkey'] = $response['secretkey'];
					$opts['last_config']['path'] = $response['path'];
					unset($opts['last_config']['quota_root']);
					if (!empty($response['quota_root'])) {
						$opts['last_config']['quota_root'] = $response['quota_root'];
						$config['quota_root'] = $response['quota_root'];
						$opts['quota_root'] = $response['quota_root'];
					}
					$opts['last_config']['time'] = time();
					// This is just a cache of the most recent setting
					if (isset($response['quota'])) {
						$opts['quota'] = $response['quota'];
						$config['quota'] = $response['quota'];
					}
					$this->set_options($opts, true);
					$config['accesskey'] = $response['accesskey'];
					$config['secretkey'] = $response['secretkey'];
					$config['path'] = $response['path'];
					$config['sessiontoken'] = (isset($response['sessiontoken']) ? $response['sessiontoken'] : '');
				} elseif (is_array($response) && isset($response['result']) && ('token_unknown' == $response['result'] || 'site_duplicated' == $response['result'])) {
					$this->log("This site appears to not be connected to UpdraftVault (".$response['result'].")");
					$config['error'] = array('message' => 'site_not_connected', 'values' => array($response['result']));
					
					$config['accesskey'] = '';
					$config['secretkey'] = '';
					$config['path'] = '';
					$config['sessiontoken'] = '';
					unset($config['quota']);
					if (!empty($response['message'])) $config['error_message'] = $response['message'];
					$details_retrieved = true;
					$cache_in_job = true;
				} elseif (is_array($response) && isset($response['result']) && 'error' == $response['result'] && 'gettempcreds_exception2' == $response['code']) {
					$this->log("An error occurred while fetching your Vault credentials. Please try again after a few minutes (".$response['code'].")");
					$config['error'] = array('message' => 'fetch_credentials_error', 'values' => array($response['code']));
					$config['accesskey'] = '';
					$config['secretkey'] = '';
					$config['path'] = '';
					$config['sessiontoken'] = '';
					$config['email'] = $opts['email']; // Pass along the email address used, as we need it to display our error message correctly
					unset($config['quota']);
					// We want to hide the AWS error message in this case
					$config['error_message'] = __('An error occurred while fetching your Vault credentials.', 'updraftplus').' '.__('Please try again after a few minutes.', 'updraftplus');
					$details_retrieved = true;
					$cache_in_job = true;
				} else {
					if (is_array($response) && !empty($response['result'])) {
						$cache_in_job = true;
						$msg = "response code: ".$response['result'];
						if (!empty($response['code'])) $msg .= " (".$response['code'].")";
						if (!empty($response['message'])) $msg .= " (".$response['message'].")";
						if (!empty($response['data'])) $msg .= " (".json_encode($response['data']).")";
						$this->log($msg);
						$config['error'] = array('message' => 'general_error_response', 'values' => array($msg));
					} else {
						$this->log("Received response, but it was not in the expected format: ".substr(wp_remote_retrieve_body($getconfig), 0, 100).' ...');
						$config['error'] = array('message' => 'unexpected_format', 'values' => array(substr(wp_remote_retrieve_body($getconfig), 0, 100).' ...'));
					}
				}
			} else {
				$this->log("Unexpected HTTP response code (please try again later): ".$response_code);
				$config['error'] = array('message' => 'unexpected_http_response', 'values' => array($response_code));
			}
		} elseif (is_wp_error($getconfig)) {
			$updraftplus->log_wp_error($getconfig);
			$config['error'] = array('message' => 'general_error_response', 'values' => array($getconfig));
		} else {
			if (!isset($getconfig['accesskey'])) {
				$this->log("wp_remote_post returned a result that was not understood (".gettype($getconfig).")");
				$config['error'] = array('message' => 'result_not_understood', 'values' => array(gettype($getconfig)));
			}
		}

		if (!$details_retrieved) {
			// Don't log anything yet, as this will replace the most recently logged message in the main panel
			if (!empty($opts['last_config']) && is_array($opts['last_config'])) {
				$last_config = $opts['last_config'];
				if (!empty($last_config['time']) && is_numeric($last_config['time']) && $last_config['time'] > time() - 86400*15) {
					if ($updraftplus->backup_time) $this->log("failed to retrieve access details from updraftplus.com: will attempt to use most recently stored configuration");
					if (!empty($last_config['accesskey'])) $config['accesskey'] = $last_config['accesskey'];
					if (!empty($last_config['secretkey'])) $config['secretkey'] = $last_config['secretkey'];
					if (isset($last_config['path'])) $config['path'] = $last_config['path'];
					if (isset($opts['quota'])) $config['quota'] = $opts['quota'];
					$cache_in_job = true;
				} else {
					if ($updraftplus->backup_time) $this->log("failed to retrieve access details from updraftplus.com: no recently stored configuration was found to use instead");
				}
			}
		}

		$config['server_side_encryption'] = 'AES256';
		$this->vault_config = $config;
		if ($cache_in_job) $this->jobdata_set('config', $config);
		// N.B. This isn't multi-server compatible
		set_transient('udvault_last_config', $config, 86400*7);
		return $config;
	}

	/**
	 * Whether to always use server-side encryption - which, with Vault, we do (and our marketing says so).
	 *
	 * @return Boolean
	 */
	protected function use_sse() {
		return true;
	}
	
	public function vault_translate_remote_message($message, $code) {
		switch ($code) {
			case 'premium_overdue':
				return __('Your UpdraftPlus Premium purchase is over a year ago.', 'updraftplus').' '.__('You should renew immediately to avoid losing the 12 months of free storage allowance that you get for being a current UpdraftPlus Premium customer.', 'updraftplus');
				break;
			case 'vault_subscription_overdue':
				return __('You have an UpdraftPlus Vault subscription with overdue payment.', 'updraftplus').' '.__('You are within the few days of grace period before it will be suspended, and you will lose your quota and access to data stored within it.', 'updraftplus').' '.__('Please renew as soon as possible!', 'updraftplus');
				break;
			case 'vault_subscription_suspended':
				return __("You have an UpdraftPlus Vault subscription that has not been renewed, and the grace period has expired.", 'updraftplus').' '.__("In a few days' time, your stored data will be permanently removed.", 'updraftplus').' '.__("If you do not wish this to happen, then you should renew as soon as possible.", 'updraftplus');
				// The following shouldn't be a possible response (the server can deal with duplicated sites with the same IDs) - but there's no harm leaving it in for now (Dec 2015)
				// This means that the site is accessing with a different home_url() than it was registered with.
				break;
			case 'site_duplicated':
				return __('No Vault connection was found for this site (has it moved?); please disconnect and re-connect.', 'updraftplus');
				break;
		}
		return $message;
	}

	/**
	 * This over-rides the method in UpdraftPlus_BackupModule and stops the hidden version field being output. This is so that blank settings are not returned and saved to the database as this storage option outputs no other fields.
	 *
	 * @return [boolean] - return false so that the hidden version field is not output
	 */
	public function print_shared_settings_fields() {
		return false;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return Void - currently does not have a pre config template, this method is needed to stop it taking it's parents
	 */
	public function get_pre_configuration_template() {

	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		
		ob_start();
		?>
			<tr class="{{get_template_css_classes true}}">
				<th><img id="vaultlogo" src="{{storage_image_url}}" alt="{{method_display_name}}" width="150" height="116"></th>
				<td valign="top" id="updraftvault_settings_cell">
					{{{simplexmlelement_existence_label}}}
					{{{curl_existence_label}}}
					<div id="updraftvault_settings_default"{{#if is_connected}} style="display:none;" class="updraft-hidden"{{/if}}>
						<p>
							{{{storage_long_description}}}
						</p>
						<div class="vault_primary_option clear-left">
							<div><strong>{{storage_package_options_label1}}</strong></div>
							<button aria-label="{{storage_package_options_label1}} {{storage_package_options_label2}}" id="updraftvault_showoptions" class="button-primary">{{storage_package_options_label2}}</button>
						</div>
						<div class="vault_primary_option">
							<div><strong>{{storage_already_registered_label1}}</strong></div>
							<button aria-label="{{storage_already_registered_label2}}" id="updraftvault_connect" class="button-primary">{{storage_already_registered_label3}}</button>
						</div>
						<p>
							<em>{{storage_long_description2}}<a target="_blank" href="{{more_vault_info_landing_url}}">{{storage_readmore_label}}</a> <a target="_blank" href="{{more_vault_info_faqs_url}}">{{storage_read_faq_label}}</a></em>
						</p>
					</div>
					<div id="updraftvault_settings_showoptions" style="display:none;" class="updraft-hidden">
						<p>{{{storage_package_options_label3}}}</p>
						<div class="vault-purchase-option-container">
							<div class="vault-purchase-option">
								<div class="vault-purchase-option-size">5 GB</div>
								<div class="vault-purchase-option-link"><b>{{price_5gb_package_label}}</b></div>
								<div class="vault-purchase-option-or">{{start_trial_option_label}}</div>
								<div class="vault-purchase-option-link"><b>{{discounted_price_5gb_package_label}}</b></div>
								<div class="vault-purchase-option-link"><a target="_blank" title="{{start_5gb_package_subscription_title}}" href="{{start_5gb_package_subscription_link}}" {{{checkout_embed_5gb_attribute}}}><button aria-label="{{start_trial_button_title}}" class="button-primary">{{start_trial_button_label}}</button></a></div>
							</div>
							<div class="vault-purchase-option">
								<div class="vault-purchase-option-size">15 GB</div>
								<div class="vault-purchase-option-link"><b>{{price_15gb_package_label}}</b></div>
								<div class="vault-purchase-option-or">{{discount_period_label}}</div>
								<div class="vault-purchase-option-link"><b>{{discounted_price_15gb_package_label}}</b></div>
								<div class="vault-purchase-option-link"><a target="_blank" title="{{start_15gb_package_subscription_title}}" href="{{start_15gb_package_subscription_link}}" {{{checkout_embed_15gb_attribute}}}><button aria-label="{{start_15gb_subscription_button_title}}" class="button-primary">{{start_subscription_button_label}}</button></a></div>
							</div>
							<div class="vault-purchase-option">
								<div class="vault-purchase-option-size">50 GB</div>
								<div class="vault-purchase-option-link"><b>{{price_50gb_package_label}}</b></div>
								<div class="vault-purchase-option-or">{{discount_period_label}}</div>
								<div class="vault-purchase-option-link"><b>{{discounted_price_50gb_package_label}}</b></div>
								<div class="vault-purchase-option-link"><a target="_blank" title="{{start_50gb_package_subscription_title}}" href="{{start_50gb_package_subscription_link}}" {{{checkout_embed_50gb_attribute}}}><button aria-label="{{start_50gb_subscription_button_title}}" class="button-primary">{{start_subscription_button_label}}</button></a></div>
							</div>
							<div class="vault-purchase-option">
								<div class="vault-purchase-option-size">250 GB</div>
								<div class="vault-purchase-option-link"><b>{{price_250gb_package_label}}</b></div>
								<div class="vault-purchase-option-or">{{discount_period_label}}</div>
								<div class="vault-purchase-option-link"><b>{{discounted_price_250gb_package_label}}</b></div>
								<div class="vault-purchase-option-link"><a target="_blank" title="{{start_250gb_package_subscription_title}}" href="{{start_250gb_package_subscription_link}}" {{{checkout_embed_250gb_attribute}}}><button aria-label="{{start_250gb_subscription_button_title}}" class="button-primary">{{start_subscription_button_label}}</button></a></div>
							</div>
						</div>
						<p class="clear-left padding-top-20px">
							{{subscription_payment_details_label}}
						</p>
						<p class="clear-left padding-top-20px">
							<em>{{storage_long_description2}} <a target="_blank" href="{{more_vault_info_landing_url}}">{{storage_readmore_label}}</a> <a target="_blank" href="{{more_vault_info_faqs_url}}">{{storage_read_faq_label}}</a></em>
						</p>
						<p>
							<a aria-label="{{go_back_link_label}}" href="{{current_clean_url}}" class="updraftvault_backtostart">{{go_back_link_text}}</a>
						</p>
					</div>
					<div id="updraftvault_settings_connect" data-instance_id="{{instance_id}}" style="display:none;" class="updraft-hidden">
						<p>{{connect_to_updraftplus_label}}</p>
						<p>
							<input title="{{input_email_title}}" id="updraftvault_email" class="udignorechange" type="text" placeholder="{{input_email_placeholder}}">
							<input title="{{input_password_title}}" id="updraftvault_pass" class="udignorechange" type="password" placeholder="{{input_password_placeholder}}">
							<button title="{{button_connect_title}}" id="updraftvault_connect_go" class="button-primary">{{button_connect_label}}</button>
						</p>
						<p class="padding-top-14px">
							<em>{{forgotten_password_label}} <a aria-label="{{forgotten_password_link_label}}" href="{{forgotten_password_link_url}}">{{forgotten_password_link_text}}</a></em>
						</p>
						<p class="padding-top-14px">
							<em><a aria-label="{{go_back_link_label}}" href="{{current_clean_url}}" class="updraftvault_backtostart">{{go_back_link_text}}</a></em>
						</p>
					</div>
					<div id="updraftvault_settings_connected"{{#unless is_connected}} style="display:none;" class="updraft-hidden"{{/unless}}>
						{{#if is_connected}}
							<p id="vault-is-connected">{{{site_is_already_connected_label}}}</p>
							<p>
								<strong>{{vault_owner_label}}:</strong> {{email}}
								<br><strong>{{vault_quota_label}}</strong>
								{{{quota_text}}}
							</p>
							<p><button id="updraftvault_disconnect" class="button-primary">{{button_disconnect_label}}</button></p>
						{{else}}
							<p>{{{vault_is_not_connected_label}}}</p>
						{{/if}}
					</div>
				</td>
			</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Retrieve a list of template properties by taking all the persistent variables and methods of the parent class and combining them with the ones that are unique to this module, also the necessary HTML element attributes and texts which are also unique only to this backup module
	 * NOTE: Please sanitise all strings that are required to be shown as HTML content on the frontend side (i.e. wp_kses()), or any other technique to prevent XSS attacks that could come via WP hooks
	 *
	 * @return Array an associative array keyed by names that describe themselves as they are
	 */
	public function get_template_properties() {
		global $updraftplus, $updraftplus_admin, $updraftplus_checkout_embed;
		// Used to decide whether we can afford HTTP calls or not, or would prefer to rely on cached data
		$this->vault_in_config_print = true;
		$properties = array(
			'storage_image_url' => UPDRAFTPLUS_URL.'/images/updraftvault-150.png',
			'simplexmlelement_existence_label' => !apply_filters('updraftplus_vault_simplexmlelement_exists', class_exists('SimpleXMLElement')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__("Your web server's PHP installation does not include a <strong>required</strong> (for %s) module (%s).", 'updraftplus'), 'UpdraftVault', 'SimpleXMLElement').' '.__("Please contact your web hosting provider's support and ask for them to enable it.", 'updraftplus'), $this->get_id(), false), $this->allowed_html_for_content_sanitisation()) : '',
			'curl_existence_label' => wp_kses($updraftplus_admin->curl_check($updraftplus->backup_methods[$this->get_id()], false, $this->get_id().' hidden-in-updraftcentral', false), $this->allowed_html_for_content_sanitisation()),
			'storage_long_description' => wp_kses(__('UpdraftVault brings you storage that is <strong>reliable, easy to use and a great price</strong>.', 'updraftplus').' '.__('Press a button to get started.', 'updraftplus'), $this->allowed_html_for_content_sanitisation()),
			'storage_package_options_label1' => __('Need to get space?', 'updraftplus'),
			'storage_package_options_label2' => __('Show the options', 'updraftplus'),
			'storage_already_registered_label1' => __('Already got space?', 'updraftplus'),
			'storage_already_registered_label2' => sprintf(__('Connect to your %s account', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
			'storage_already_registered_label3' => __('Connect', 'updraftplus'),
			'storage_long_description2' => __("UpdraftVault is built on top of Amazon's world-leading data-centres, with redundant data storage to achieve 99.999999999% reliability.", 'updraftplus'),
			'storage_readmore_label' => sprintf(__('Read more about %s here.', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
			'storage_read_faq_label' => sprintf(__('Read the %s FAQs here.', 'updraftplus'), 'Vault'),
			'more_vault_info_landing_url' => $this->get_url('more_vault_info_landing'),
			'more_vault_info_faqs_url' => $this->get_url('more_vault_info_faqs'),
			'storage_package_options_label3' => wp_kses(__('UpdraftVault brings you storage that is <strong>reliable, easy to use and a great price</strong>.', 'updraftplus').' '.__('Press a button to get started.', 'updraftplus'), $this->allowed_html_for_content_sanitisation()),
			'start_subscription_button_label' => __('Start Subscription', 'updraftplus'),
			'start_15gb_subscription_button_title' => sprintf(__('Start %s Subscription', 'updraftplus'), '15GB'),
			'start_50gb_subscription_button_title' => sprintf(__('Start %s Subscription', 'updraftplus'), '50GB'),
			'start_250gb_subscription_button_title' => sprintf(__('Start %s Subscription', 'updraftplus'), '250GB'),
			'start_trial_button_label' => __('Start Trial', 'updraftplus'),
			'start_trial_button_title' => sprintf(__('Start %s Trial', 'updraftplus'), '5GB'),
			'discount_period_label' => __('or (annual discount)', 'updraftplus'),
			'start_trial_option_label' => __('with the option of', 'updraftplus'),
			'price_5gb_package_label' => sprintf(__('%s per year', 'updraftplus'), '$35'),
			'price_15gb_package_label' => sprintf(__('%s per quarter', 'updraftplus'), '$20'),
			'price_50gb_package_label' => sprintf(__('%s per quarter', 'updraftplus'), '$50'),
			'price_250gb_package_label' => sprintf(__('%s per quarter', 'updraftplus'), '$125'),
			'discounted_price_5gb_package_label' => sprintf(__('%s month %s trial', 'updraftplus'), '1', '$1'),
			'discounted_price_15gb_package_label' => sprintf(__('%s per year', 'updraftplus'), '$70'),
			'discounted_price_50gb_package_label' => sprintf(__('%s per year', 'updraftplus'), '$175'),
			'discounted_price_250gb_package_label' => sprintf(__('%s per year', 'updraftplus'), '$450'),
			'start_5gb_package_subscription_title' => sprintf(__('Start a %s UpdraftVault Subscription', 'updraftplus'), '5GB'),
			'start_15gb_package_subscription_title' => sprintf(__('Start a %s UpdraftVault Subscription', 'updraftplus'), '15GB'),
			'start_50gb_package_subscription_title' => sprintf(__('Start a %s UpdraftVault Subscription', 'updraftplus'), '50GB'),
			'start_250gb_package_subscription_title' => sprintf(__('Start a %s UpdraftVault Subscription', 'updraftplus'), '250GB'),
			'start_5gb_package_subscription_link' => apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_vault_5')),
			'start_15gb_package_subscription_link' => apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_vault_15')),
			'start_50gb_package_subscription_link' => apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_vault_50')),
			'start_250gb_package_subscription_link' => apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_vault_250')),
			'go_back_link_text' => __('Back...', 'updraftplus'),
			'go_back_link_label' => sprintf(__('Back to other %s options'), 'Vault'),
			'current_clean_url' => UpdraftPlus::get_current_clean_url(),
			'subscription_payment_details_label' => __('Payments can be made in US dollars, euros or GB pounds sterling, via card or PayPal.', 'updraftplus').' '. __('Subscriptions can be cancelled at any time.', 'updraftplus'),
			'connect_to_updraftplus_label' => __('Enter your UpdraftPlus.Com email / password here to connect:', 'updraftplus'),
			'input_email_title' => sprintf(__('Please enter your %s email address', 'updraftplus'), 'UpdraftPlus.com'),
			'input_email_placeholder' => __('Email', 'updraftplus'),
			'input_password_title' => sprintf(__('Please enter your %s password', 'updraftplus'), 'UpdraftPlus.com'),
			'input_password_placeholder' => __('Password', 'updraftplus'),
			'button_connect_title' => sprintf(__('Connect to your %s'), 'Vault'),
			'button_connect_label' => __('Connect', 'updraftplus'),
			'forgotten_password_label' => __("Don't know your email address, or forgotten your password?", 'updraftplus'),
			'forgotten_password_link_label' => __("Don't know your email address, or forgotten your password?", 'updraftplus').__('Follow this link for help', 'updraftplus'),
			'forgotten_password_link_url' => $this->get_url('vault_forgotten_credentials_links'),
			'forgotten_password_link_text' => __('Go here for help', 'updraftplus'),
			'site_is_already_connected_label' => wp_kses(__('This site is <strong>connected</strong> to UpdraftVault.', 'updraftplus').' '.__("Well done - there's nothing more needed to set up.", 'updraftplus'), $this->allowed_html_for_content_sanitisation()),
			'vault_owner_label' => __('Vault owner', 'updraftplus'),
			'vault_quota_label' => __('Quota:', 'updraftplus'),
			'button_disconnect_label' => __('Disconnect', 'updraftplus'),
			'vault_is_not_connected_label' => wp_kses(__('You are <strong>not connected</strong> to UpdraftVault.', 'updraftplus'), $this->allowed_html_for_content_sanitisation()),
		);
		if ($updraftplus_checkout_embed) {
			$properties['checkout_embed_5gb_attribute'] = $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-5-gb') ? 'data-embed-checkout="'.esc_attr(apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-5-gb', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=settings'))).'"' : '';
			$properties['checkout_embed_15gb_attribute'] = $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-15-gb') ? 'data-embed-checkout="'.esc_attr(apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-15-gb', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=settings'))).'"' : '';
			$properties['checkout_embed_50gb_attribute'] = $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-50-gb') ? 'data-embed-checkout="'.esc_attr(apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-50-gb', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=settings'))).'"' : '';
			$properties['checkout_embed_250gb_attribute'] = $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-250-gb') ? 'data-embed-checkout="'.esc_attr(apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-250-gb', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=settings'))).'"' : '';
		}
		$this->vault_in_config_print = false;
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}

	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		if (!empty($opts['token']) || !empty($opts['email'])) {
			$opts['is_connected'] = true;
		}
		if (!isset($opts['quota']) || !is_numeric($opts['quota']) || $opts['quota'] < 0) {
			$opts['quota_text'] = __('Unknown', 'updraftplus');
		} else {
			$opts['quota_text'] = $this->s3_get_quota_info('text', $opts['quota']);
		}
		return $opts;
	}
	
	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['email'])) return true;
		return false;
	}
	
	/**
	 * Gives settings keys which values should not passed to handlebarsjs context.
	 * The settings stored in UD in the database sometimes also include internal information that it would be best not to send to the front-end (so that it can't be stolen by a man-in-the-middle attacker)
	 *
	 * @return Array - Settings array keys which should be filtered
	 */
	public function filter_frontend_settings_keys() {
		return array(
			'last_config',
			'quota',
			'quota_root',
			'token',
		);
	}
	
	private function connected_html($vault_settings = false, $error_message = false) {
		if (!is_array($vault_settings)) {
			$vault_settings = $this->get_options();
		}
		if (!is_array($vault_settings) || empty($vault_settings['token']) || empty($vault_settings['email'])) return '<p>'.__('You are <strong>not connected</strong> to UpdraftVault.', 'updraftplus').'</p>';

		$ret = '<p id="vault-is-connected">';
		
		$ret .= __('This site is <strong>connected</strong> to UpdraftVault.', 'updraftplus').' '.__("Well done - there's nothing more needed to set up.", 'updraftplus').'</p><p><strong>'.__('Vault owner', 'updraftplus').':</strong> '.htmlspecialchars($vault_settings['email']);

		$ret .= '<br><strong>'.__('Quota:', 'updraftplus').'</strong> ';
		if (!isset($vault_settings['quota']) || !is_numeric($vault_settings['quota']) || $vault_settings['quota'] < 0) {
			if (!$error_message) {
				$ret .= __('Unknown', 'updraftplus');
				$ret .= $this->get_quota_recount_links();
			} else {
				$ret .= $error_message;
				$ret .= $this->get_quota_recount_links();
			}
		} else {
			$ret .= $this->s3_get_quota_info('text', $vault_settings['quota']);
		}
		$ret .= '</p>';
		
		$ret .= '<p><button id="updraftvault_disconnect" class="button-primary">'.__('Disconnect', 'updraftplus').'</button></p>';

		return $ret;
	}

	/**
	 * This function will output to the backup log when s3 is out of quota, it will then also clear the vault quota transient so a recount will happen at some point.
	 *
	 * @param Integer $total  - the total amount of quota
	 * @param Integer $used   - the toal amount used
	 * @param Integer $needed - the amount needed for the upload
	 *
	 * @return void
	 */
	protected function s3_out_of_quota($total, $used, $needed) {
		$quota_transient_used = $this->quota_transient_used ? '(via transient)' : '';
		$this->log("Error: Quota exhausted (used=$used, total=$total, needed=$needed) $quota_transient_used");
		$this->log(sprintf(__('Error: you have insufficient storage quota available (%s) to upload this archive (%s) (%s).', 'updraftplus'), round(($total-$used)/1048576, 2).' MB', round($needed/1048576, 2).' MB', $quota_transient_used).' '.__('You can get more quota here', 'updraftplus').': '.$this->get_url('get_more_quota'), 'error');
		// The transient wasn't intended for 100% precision when that matters (e.g. out-of-quota), so we delete it - a fresh calculation will take place on the next operation
		delete_transient('updraftvault_quota_numeric');
	}

	/**
	 * This function will setup and record the UpdraftVault quota text transient
	 *
	 * @param Integer $quota_used - the amount of quota used
	 * @param Integer $quota      - the total quota
	 *
	 * @return void
	 */
	protected function s3_record_quota_info($quota_used, $quota) {

		$ret = __('Current use:', 'updraftplus').' '.round($quota_used / 1048576, 1).' / '.round($quota / 1048576, 1).' MB';
		$ret .= ' ('.sprintf('%.1f', 100*$quota_used / max($quota, 1)).' %)';

		$ret .= ' - <a href="'.esc_attr($this->get_url('get_more_quota')).'">'.__('Get more quota', 'updraftplus').'</a>';

		$ret_dashboard = $ret . ' - <a href="#" id="updraftvault_recountquota">'.__('Refresh current status', 'updraftplus').'</a>';

		set_transient('updraftvault_quota_text', $ret_dashboard, 86400*3);

	}

	public function s3_prune_retained_backups_finished() {
		$config = $this->get_config();
		$quota = $config['quota'];
		$quota_used = $this->s3_get_quota_info('numeric', $config['quota']);
		$quota_transient_used = $this->quota_transient_used ? ' (via transient)' : '';
		
		$ret = __('Current use:', 'updraftplus').' '.round($quota_used / 1048576, 1).' / '.round($quota / 1048576, 1).' MB'.$quota_transient_used;
		$ret .= ' ('.sprintf('%.1f', 100*$quota_used / max($quota, 1)).' %)';

		$ret_plain = $ret . ' - '.__('Get more quota', 'updraftplus').': '.$this->get_url('get_more_quota');

		$ret .= ' - <a href="'.esc_attr($this->get_url('get_more_quota')).'">'.__('Get more quota', 'updraftplus').'</a>';

		do_action('updraft_report_remotestorage_extrainfo', 'updraftvault', $ret, $ret_plain);
	}
	
	/**
	 * This function will return the S3 quota Information
	 *
	 * @param  String|integer $format n numeric, returns an integer or false for an error (never returns an error)
	 * @param  integer        $quota  S3 quota information
	 * @return String|integer
	 */
	protected function s3_get_quota_info($format = 'numeric', $quota = 0) {
		$ret = '';
		$counted = 0;

		if ($quota > 0) {

			if (!empty($this->vault_in_config_print) && 'text' == $format) {
				$quota_via_transient = get_transient('updraftvault_quota_text');
				if (is_string($quota_via_transient) && $quota_via_transient) return $quota_via_transient;
			} elseif ('numeric' == $format) {
				$quota_via_transient = get_transient('updraftvault_quota_numeric');
				if (is_numeric($quota_via_transient) && $quota_via_transient && round($quota - $quota_via_transient, 1048576) >= 1024) {
					$this->quota_transient_used = true;
					if (!defined('UPDRAFTVAULT_COUNT_QUOTA_ANYWAY') || !UPDRAFTVAULT_COUNT_QUOTA_ANYWAY) {
						return $quota_via_transient;
					}
				} else {
					$this->quota_transient_used = false;
				}
			}

			try {
				
				$config = $this->get_config();

				if (empty($config['quota_root'])) {
					// This next line is wrong: it lists the files *in this site's sub-folder*, rather than the whole Vault
					$current_files = $this->listfiles('');
				} else {
					$current_files = $this->listfiles_with_path($config['quota_root'], '', true);
				}

			} catch (Exception $e) {
				$this->log("Listfiles failed during quota calculation: ".$e->getMessage());
				$current_files = new WP_Error('listfiles_exception', $e->getMessage().' ('.get_class($e).')');
			}

			$ret .= __('Current use:', 'updraftplus').' ';

			if (is_wp_error($current_files)) {
				$ret .= __('Error:', 'updraftplus').' '.$current_files->get_error_message().' ('.$current_files->get_error_code().')';
			} elseif (!is_array($current_files)) {
				$ret .= __('Unknown', 'updraftplus');
			} else {
				foreach ($current_files as $file) {
					$counted += $file['size'];
				}
				if ($this->quota_transient_used && defined('UPDRAFTVAULT_COUNT_QUOTA_ANYWAY') && UPDRAFTVAULT_COUNT_QUOTA_ANYWAY) {
					$this->log("UpdraftVault: UPDRAFTVAULT_COUNT_QUOTA_ANYWAY set. Current quota: {$counted}");
				} else {
					set_transient('updraftvault_quota_numeric', $counted, 86400);
				}
				$ret .= round($counted / 1048576, 1);
				$ret .= ' / '.round($quota / 1048576, 1).' MB';
				$ret .= ' ('.sprintf('%.1f', 100*$counted / $quota).' %)';
			}
		} else {
			$ret .= '0';
		}
		
		$ret .= $this->get_quota_recount_links();
		
		if ('text' == $format) set_transient('updraftvault_quota_text', $ret, 86400*3);

		return ('text' == $format) ? $ret : $counted;
	}
	
	/**
	 * Build the links to recount used vault quota and to purchase more quota
	 *
	 * @return String
	 */
	private function get_quota_recount_links() {
		return ' - <a href="'.esc_attr($this->get_url('get_more_quota')).'">'.__('Get more quota', 'updraftplus').'</a> - <a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" id="updraftvault_recountquota">'.__('Refresh current status', 'updraftplus').'</a>';
	}

	public function ajax_vault_recountquota($echo_results = true) {
		// Force the opts to be refreshed
		$config = $this->get_config();

		if (empty($config['accesskey']) && !empty($config['error_message'])) {
			if (!empty($config['error']) && is_array($config['error']) && 'fetch_credentials_error' == $config['error']['message']) {
				$opts = array('token' => 'unknown', 'email' => $config['email'], 'quota' => -1);
				$results = array('html' => $this->connected_html($opts, $config['error_message']), 'connected' => 1);
			} else {
				$results = array('html' => htmlspecialchars($config['error_message']), 'connected' => 0);
			}
		} else {
			// Now read the opts
			$opts = $this->get_options();
			$results = array('html' => $this->connected_html($opts), 'connected' => 1);
		}
		if ($echo_results) {
			echo json_encode($results);
		} else {
			return $results;
		}
		
	}

	/**
	 * This method also gets called directly, so don't add code that assumes that it's definitely an AJAX situation
	 *
	 * @param  Boolean $echo_results check to see if the results need to be echoed
	 * @return Array
	 */
	public function ajax_vault_disconnect($echo_results = true) {
		$vault_settings = $this->get_options();
		$frontend_settings_keys = array_flip($this->filter_frontend_settings_keys());
		foreach ((array) $frontend_settings_keys as $key => $val) {
			$frontend_settings_keys[$key] = ('last_config' === $key) ? array() : '';
		}
		$this->set_options(array_merge($frontend_settings_keys, $this->get_default_options()), true);
		global $updraftplus;

		delete_transient('udvault_last_config');
		delete_transient('updraftvault_quota_text');

		$response = array('disconnected' => 1, 'html' => $this->connected_html());
		
		if ($echo_results) {
			$updraftplus->close_browser_connection(json_encode($response));
		}

		// If $_POST['reset_hash'] is set, then we were alerted by updraftplus.com - no need to notify back
		if (is_array($vault_settings) && isset($vault_settings['email']) && empty($_POST['reset_hash'])) {
		
			$post_body = array(
				'e' => (string) $vault_settings['email'],
				'sid' => $updraftplus->siteid(),
				'su' => base64_encode(home_url())
			);

			if (!empty($vault_settings['token'])) $post_body['token'] = (string) $vault_settings['token'];

			// Use SSL to prevent snooping
			wp_remote_post($this->vault_mothership.'/?udm_action=vault_disconnect', array(
				'timeout' => 20,
				'body' => $post_body,
			));
		}
		
		return $response;
		
	}

	/**
	 * This is called from the UD admin object
	 *
	 * @param  Boolean       $echo_results    A Flag to see if results need to be echoed or returned
	 * @param  Boolean|array $use_credentials Check if Vault needs to use credentials
	 * @return Array
	 */
	public function ajax_vault_connect($echo_results = true, $use_credentials = false) {
	
		if (empty($use_credentials)) $use_credentials = $_REQUEST;
	
		$connect = $this->vault_connect($use_credentials['email'], $use_credentials['pass']);
		if (true === $connect) {
			$response = array('connected' => true, 'html' => $this->connected_html(false));
		} else {
			$response = array(
				'e' => __('An unknown error occurred when trying to connect to UpdraftPlus.Com', 'updraftplus')
			);
			if (is_wp_error($connect)) {
				$response['e'] = $connect->get_error_message();
				$response['code'] = $connect->get_error_code();
				$response['data'] = serialize($connect->get_error_data());
			}
		}
		
		if ($echo_results) {
			echo json_encode($response);
		} else {
			return $response;
		}
	}

	/**
	 * Returns either true (in which case the Vault token will be stored), or false|WP_Error
	 *
	 * @param  String $email    Vault Email
	 * @param  String $password Vault Password
	 * @return Boolean|WP_Error
	 */
	private function vault_connect($email, $password) {

		// Username and password set up?
		if (empty($email) || empty($password)) return new WP_Error('blank_details', __('You need to supply both an email address and a password', 'updraftplus'));

		global $updraftplus;

		$remote_post_array = apply_filters('updraftplus_vault_config_add_headers', array(
			'timeout' => 20,
			'body' => array(
				'e' => $email,
				'p' => base64_encode($password),
				'sid' => $updraftplus->siteid(),
				'su' => base64_encode(home_url()),
				'v' => $updraftplus->version
			)
		));
		
		// Use SSL to prevent snooping
		$result = wp_remote_post($this->vault_mothership.'/?udm_action=vault_connect', $remote_post_array);

		if (is_wp_error($result) || false === $result) return $result;

		$response = json_decode(wp_remote_retrieve_body($result), true);

		if (!is_array($response) || !isset($response['mothership']) || !isset($response['loggedin'])) {

			if (preg_match('/has banned your IP address \(([\.:0-9a-f]+)\)/', $result['body'], $matches)) {
				return new WP_Error('banned_ip', sprintf(__("UpdraftPlus.com has responded with 'Access Denied'.", 'updraftplus').'<br>'.__("It appears that your web server's IP Address (%s) is blocked.", 'updraftplus').' '.__('This most likely means that you share a webserver with a hacked website that has been used in previous attacks.', 'updraftplus').'<br> <a href="'.apply_filters("updraftplus_com_link", "https://updraftplus.com/unblock-ip-address/").'" target="_blank">'.__('To remove the block, please go here.', 'updraftplus').'</a> ', $matches[1]));
			} else {
				return new WP_Error('unknown_response', sprintf(__('UpdraftPlus.Com returned a response which we could not understand (data: %s)', 'updraftplus'), wp_remote_retrieve_body($result)));
			}
		}

		switch ($response['loggedin']) {
			case 'connected':
				if (!empty($response['token'])) {
					// Store it
					$vault_settings = $this->get_options();
					if (!is_array($vault_settings)) $vault_settings = array();
					$vault_settings['email'] = $email;
					$vault_settings['token'] = (string) $response['token'];
					$vault_settings['quota'] = -1;
					unset($vault_settings['last_config']);
					if (isset($response['quota'])) $vault_settings['quota'] = $response['quota'];
					$this->set_options($vault_settings, true);
					if (!empty($response['config']) && is_array($response['config'])) {
						if (!empty($response['config']['accesskey'])) {
							$this->vault_set_config($response['config']);
						} elseif (!empty($response['config']['result']) && ('token_unknown' == $response['config']['result'] || 'site_duplicated' == $response['config']['result'])) {
							return new WP_Error($response['config']['result'], $this->vault_translate_remote_message($response['config']['message'], $response['config']['result']));
						}
						// else... would also be an error condition, but not one known possible (and it will show a generic error anyway)
					}
				} elseif (isset($response['quota']) && !$response['quota']) {
					return new WP_Error('no_quota', __('You do not currently have any UpdraftVault quota', 'updraftplus'));
				} else {
					return new WP_Error('unknown_response', __('UpdraftPlus.Com returned a response, but we could not understand it', 'updraftplus'));
				}
				break;
			case 'authfailed':
				if (!empty($response['authproblem'])) {
					if ('invalidpassword' == $response['authproblem']) {
						$authfail_error = new WP_Error('authfailed', __('Your email address was valid, but your password was not recognised by UpdraftPlus.Com.', 'updraftplus').' <a href="'.esc_attr($this->get_url('vault_forgotten_credentials_links')).'">'.__('If you have forgotten your password, then go here to change your password on updraftplus.com.', 'updraftplus').'</a>');
						return $authfail_error;
					} elseif ('invaliduser' == $response['authproblem']) {
						return new WP_Error('authfailed', __('You entered an email address that was not recognised by UpdraftPlus.Com', 'updraftplus'));
					}
				}
				return new WP_Error('authfailed', __('Your email address and password were not recognised by UpdraftPlus.Com', 'updraftplus'));
				break;
			case 'iamfailed':
				if (!empty($response['authproblem'])) {
					if ('gettempcreds_exception2' == $response['authproblem'] || 'gettempcreds_exception2' == $response['authproblem']) {
						$authfail_error = new WP_Error('authfailed', __('An error occurred while fetching your Vault credentials.', 'updraftplus').' '.__('Please try again after a few minutes.'));
					} else {
						$authfail_error = new WP_Error('authfailed', __('An unknown error occurred while connecting to Vault.', 'updraftplus').' '.__('Please try again.'));
					}
					return $authfail_error;
				}
				return new WP_Error('unknown_response', __('UpdraftPlus.Com returned a response, but we could not understand it', 'updraftplus'));
				break;
			default:
				return new WP_Error('unknown_response', __('UpdraftPlus.Com returned a response, but we could not understand it', 'updraftplus'));
				break;
		}

		return true;

	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param  Array $updraftvault - An array of UpdraftVault options
	 * @return Array - the set of updated UpdraftVault settings
	 */
	public function options_filter($updraftvault) {
		// Get the current options (and possibly update them to the new format)
		$opts = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format('updraftvault');
		
		if (is_wp_error($opts)) {
			if ('recursion' !== $opts->get_error_code()) {
				$msg = "(".$opts->get_error_code()."): ".$opts->get_error_message();
				$this->log($msg);
				error_log("UpdraftPlus: $msg");
			}
			// The saved options had a problem; so, return the new ones
			return $updraftvault;
		}
		
		// If the input is either empty or not as expected, then return the current options
		if (!isset($updraftvault['settings']) || !is_array($updraftvault['settings']) || empty($updraftvault['settings'])) return $opts;
		
		foreach ($updraftvault['settings'] as $instance_id => $storage_options) {
			if (!isset($opts['settings'][$instance_id])) continue;
			foreach ($storage_options as $storage_key => $storage_value) {
				$opts['settings'][$instance_id][$storage_key] = $storage_value;
			}
		}

		return $opts;
	}
}
