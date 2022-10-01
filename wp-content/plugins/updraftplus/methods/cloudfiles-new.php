<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

// SDK uses namespacing - requires PHP 5.3 (actually the SDK states its requirements as 5.3.3)
use OpenCloud\Rackspace;

// New SDK - https://github.com/rackspace/php-opencloud and http://docs.rackspace.com/sdks/guide/content/php.html
// Uploading: https://github.com/rackspace/php-opencloud/blob/master/docs/userguide/ObjectStore/Storage/Object.md

require_once(UPDRAFTPLUS_DIR.'/methods/openstack-base.php');

class UpdraftPlus_BackupModule_cloudfiles_opencloudsdk extends UpdraftPlus_BackupModule_openstack_base {

	public function __construct() {
		parent::__construct('cloudfiles', 'Cloud Files', 'Rackspace Cloud Files', '/images/rackspacecloud-logo.png');
	}

	public function get_client() {
		return $this->client;
	}

	public function get_openstack_service($opts, $useservercerts = false, $disablesslverify = null) {

		$user = $opts['user'];
		$apikey = $opts['apikey'];
		$authurl = $opts['authurl'];
		$region = (!empty($opts['region'])) ? $opts['region'] : null;

		include_once(UPDRAFTPLUS_DIR.'/vendor/autoload.php');

		// The new authentication APIs don't match the values we were storing before
		$new_authurl = ('https://lon.auth.api.rackspacecloud.com' == $authurl || 'uk' == $authurl) ? Rackspace::UK_IDENTITY_ENDPOINT : Rackspace::US_IDENTITY_ENDPOINT;

		if (null === $disablesslverify) $disablesslverify = UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify');

		if (empty($user) || empty($apikey)) throw new Exception(__('Authorisation failed (check your credentials)', 'updraftplus'));

		$this->log("authentication URL: ".$new_authurl);

		$client = new Rackspace($new_authurl, array(
			'username' => $user,
			'apiKey' => $apikey
		));
		$this->client = $client;

		if ($disablesslverify) {
			$client->setSslVerification(false);
		} else {
			if ($useservercerts) {
				$client->setConfig(array($client::SSL_CERT_AUTHORITY, 'system'));
			} else {
				$client->setSslVerification(UPDRAFTPLUS_DIR.'/includes/cacert.pem', true, 2);
			}
		}

		return $client->objectStoreService('cloudFiles', $region);
	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic');
	}

	/**
	 * Retrieve default options for this remote storage module.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'user' => '',
			'authurl' => 'https://auth.api.rackspacecloud.com',
			'apikey' => '',
			'path' => '',
			'region' => null
		);
	}
	
	/**
	 * This gives the partial template string to the settings page for the CloudFiles  settings.
	 *
	 * @return String - the partial template, ready for substitutions to be carried out
	 */
	public function get_configuration_middlesection_template() {
		global $updraftplus;
		
		$classes = $this->get_css_classes();
		$template_str = '
		<tr class="'.$classes.'">
			<th title="'.__('Accounts created at rackspacecloud.com are US accounts; accounts created at rackspace.co.uk are UK accounts.', 'updraftplus').'">'.__('US or UK-based Rackspace Account', 'updraftplus').':</th>
			<td>
				<select data-updraft_settings_test="authurl" '.$this->output_settings_field_name_and_id('authurl', true).' title="'.__('Accounts created at rackspacecloud.com are US-accounts; accounts created at rackspace.co.uk are UK-based', 'updraftplus').'">
					<option {{#ifeq "https://auth.api.rackspacecloud.com" authurl}}selected="selected"{{/ifeq}} value="https://auth.api.rackspacecloud.com">'.__('US (default)', 'updraftplus').'</option>
					<option {{#ifeq "https://lon.auth.api.rackspacecloud.com" authurl}}selected="selected"{{/ifeq}} value="https://lon.auth.api.rackspacecloud.com">'.__('UK', 'updraftplus').'</option>
				</select>
			</td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.__('Cloud Files Storage Region', 'updraftplus').':</th>
			<td>
				<select data-updraft_settings_test="region" '.$this->output_settings_field_name_and_id('region', true).'>						
					{{#each regions as |desc reg|}}
						<option {{#ifeq ../region reg}}selected="selected"{{/ifeq}} value="{{reg}}">{{desc}}</option>
					{{/each}}
				</select>
			</td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.__('Cloud Files Username', 'updraftplus').':</th>
			<td><input data-updraft_settings_test="user" type="text" autocomplete="off" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('user', true).' value="{{user}}" />
			<div style="clear:both;">
				'.apply_filters('updraft_cloudfiles_apikeysetting', '<a href="'.$updraftplus->get_url('premium').'" target="_blank"><em>'.__('To create a new Rackspace API sub-user and API key that has access only to this Rackspace container, use Premium.', 'updraftplus').'</em></a>').'
			</div>
			</td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.__('Cloud Files API Key', 'updraftplus').':</th>
			<td><input data-updraft_settings_test="apikey" type="'.apply_filters('updraftplus_admin_secret_field_type', 'password').'" autocomplete="off" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('apikey', true).' value="{{apikey}}" />
			</td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.apply_filters('updraftplus_cloudfiles_location_description', __('Cloud Files Container', 'updraftplus')).':</th>
			<td><input data-updraft_settings_test="path" type="text" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('path', true).' value="{{path}}" /></td>
		</tr>';
		return $template_str;
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts handerbar template options
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		$opts['regions'] = array(
			'DFW' => __('Dallas (DFW) (default)', 'updraftplus'),
			'SYD' => __('Sydney (SYD)', 'updraftplus'),
			'ORD' => __('Chicago (ORD)', 'updraftplus'),
			'IAD' => __('Northern Virginia (IAD)', 'updraftplus'),
			'HKG' => __('Hong Kong (HKG)', 'updraftplus'),
			'LON' => __('London (LON)', 'updraftplus')
		);
		$opts['region'] = (empty($opts['region'])) ? 'DFW' : $opts['region'];
		if (isset($opts['apikey'])) {
			$opts['apikey'] = trim($opts['apikey']);
		}
		$opts['authurl'] = !empty($opts['authurl']) ? $opts['authurl'] : '';
		return $opts;
	}

	/**
	 * Perform a test of user-supplied credentials, and echo the result
	 *
	 * @param Array $posted_settings - settings to test
	 */
	public function credentials_test($posted_settings) {

		if (empty($posted_settings['apikey'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('API key', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['user'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('Username', 'updraftplus'));
			return;
		}

		$opts = array(
			'user' => $posted_settings['user'],
			'apikey' => $posted_settings['apikey'],
			'authurl' => $posted_settings['authurl'],
			'region' => (empty($posted_settings['region'])) ? null : $posted_settings['region']
		);

		$this->credentials_test_go($opts, $posted_settings['path'], $posted_settings['useservercerts'], $posted_settings['disableverify']);
	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && isset($opts['user']) && '' != $opts['user'] && !empty($opts['apikey'])) return true;
		return false;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		?>
		<tr class="{{get_template_css_classes false}} {{method_id}}_pre_config_container">
			<td colspan="2">
				{{#if storage_image_url}}
					<img alt="{{storage_long_description}}" src="{{storage_image_url}}">
				{{/if}}
				<br>
				{{{mb_substr_existence_label}}}
				{{{json_last_error_existence_label}}}
				{{{curl_existence_label}}}
				<br>
				<p>{{{rackspace_text_description}}} <a href="{{faq_link_url}}" target="_blank">{{faq_link_text}}</a></p>
			</td>
		</tr>
		<?php
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
			<th title="{{input_account_title}}">{{input_account_label}}:</th>
			<td>
				<select data-updraft_settings_test="authurl" class="udc-wd-600" id="{{get_template_input_attribute_value "id" "authurl"}}" name="{{get_template_input_attribute_value "name" "authurl"}}" title="{{input_account_title}}">
					{{#each input_account_option_labels}}
						<option {{#ifeq ../authurl @key}}selected="selected"{{/ifeq}} value="{{@key}}">{{this}}</option>
					{{/each}}
				</select>
			</td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_region_label}}:</th>
			<td>
				<select data-updraft_settings_test="region" class="udc-wd-600" id="{{get_template_input_attribute_value "id" "region"}}" name="{{get_template_input_attribute_value "name" "region"}}">
					{{#each regions as |desc reg|}}
						<option {{#ifeq ../region reg}}selected="selected"{{/ifeq}} value="{{reg}}">{{desc}}</option>
					{{/each}}
				</select>
			</td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_username_label}}:</th>
			<td><input data-updraft_settings_test="user" type="text" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "user"}}" name="{{get_template_input_attribute_value "name" "user"}}" value="{{user}}" />
			<div style="clear:both;">
				{{{input_username_title}}}
			</div>
			</td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_apikey_label}}:</th>
			<td><input data-updraft_settings_test="apikey" type="{{input_apikey_type}}" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "apikey"}}" name="{{get_template_input_attribute_value "name" "apikey"}}" value="{{apikey}}" />
			</td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_container_label}}:</th>
			<td><input data-updraft_settings_test="path" type="text" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "path"}}" name="{{get_template_input_attribute_value "name" "path"}}" value="{{path}}" /></td>
		</tr>';
		{{{get_template_test_button_html "Rackspace Cloud Files"}}}
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
		global $updraftplus, $updraftplus_admin;
		$properties = array(
			'storage_image_url' => !empty($this->img_url) ? UPDRAFTPLUS_URL.$this->img_url : '',
			'storage_long_description' => $this->long_desc,
			'mb_substr_existence_label' => !apply_filters('updraftplus_openstack_mbsubstr_exists', function_exists('mb_substr')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__('Your web server\'s PHP installation does not include a required module (%s). Please contact your web hosting provider\'s support.', 'updraftplus'), 'mbstring').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s. Please do not file any support requests; there is no alternative.", 'updraftplus'), $this->desc, 'mbstring'), $this->method, false), $this->allowed_html_for_content_sanitisation()) : '',
			'json_last_error_existence_label' => !apply_filters('updraftplus_rackspace_jsonlasterror_exists', function_exists('json_last_error')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__('Your web server\'s PHP installation does not include a required module (%s). Please contact your web hosting provider\'s support.', 'updraftplus'), 'json').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s. Please do not file any support requests; there is no alternative.", 'updraftplus'), 'Cloud Files', 'json'), 'cloudfiles', false), $this->allowed_html_for_content_sanitisation()) : '',
			'curl_existence_label' => wp_kses($updraftplus_admin->curl_check($this->long_desc, false, $this->method.' hidden-in-updraftcentral', false), $this->allowed_html_for_content_sanitisation()),
			'rackspace_text_description' => wp_kses(__('Get your API key <a href="https://mycloud.rackspace.com/" target="_blank">from your Rackspace Cloud console</a> (<a href="https://docs.rackspace.com/support/how-to/set-up-an-api-key-cloud-office-control-panel" target="_blank">read instructions here</a>), then pick a container name to use for storage. This container will be created for you if it does not already exist.', 'updraftplus'), $this->allowed_html_for_content_sanitisation()),
			'faq_link_text' => __('Also, you should read this important FAQ.', 'updraftplus'),
			'faq_link_url' => wp_kses(apply_filters("updraftplus_com_link", "https://updraftplus.com/faqs/there-appear-to-be-lots-of-extra-files-in-my-rackspace-cloud-files-container/"), array(), array('http', 'https')),
			'input_account_label' => __('US or UK-based Rackspace Account', 'updraftplus'),
			'input_account_title' => __('Accounts created at rackspacecloud.com are US accounts; accounts created at rackspace.co.uk are UK accounts.', 'updraftplus'),
			'input_account_option_labels' => array(
				'https://auth.api.rackspacecloud.com' => __('US (default)', 'updraftplus'),
				'https://lon.auth.api.rackspacecloud.com' => __('UK', 'updraftplus'),
			),
			'input_region_label' => __('Cloud Files Storage Region', 'updraftplus'),
			'input_username_label' => __('Cloud Files Username', 'updraftplus'),
			'input_username_title' => wp_kses(apply_filters('updraft_cloudfiles_apikeysetting', '<a href="'.$updraftplus->get_url('premium').'" target="_blank"><em>'.__('To create a new Rackspace API sub-user and API key that has access only to this Rackspace container, use Premium.', 'updraftplus').'</em></a>'), $this->allowed_html_for_content_sanitisation()),
			'input_apikey_label' => __('Cloud Files API Key', 'updraftplus'),
			'input_apikey_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_container_label' => wp_kses(apply_filters('updraftplus_cloudfiles_location_description', __('Cloud Files Container', 'updraftplus')), $this->allowed_html_for_content_sanitisation()),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}
}
