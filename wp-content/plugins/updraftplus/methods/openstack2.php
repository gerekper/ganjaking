<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

// SDK uses namespacing - requires PHP 5.3 (actually the SDK states its requirements as 5.3.3)
// @codingStandardsIgnoreLine
use OpenCloud\OpenStack;

updraft_try_include_file('methods/openstack-base.php', 'require_once');

class UpdraftPlus_BackupModule_openstack extends UpdraftPlus_BackupModule_openstack_base {

	public function __construct() {
		// 4th parameter is a relative (to UPDRAFTPLUS_DIR) logo URL, which should begin with /, should we get approved for use of the OpenStack logo in future (have requested info)
		parent::__construct('openstack', 'OpenStack', 'OpenStack (Swift)', '');
	}

	/**
	 * Get Openstack service
	 *
	 * @param  String  $opts             THis contains: 'tenant', 'user', 'password', 'authurl', (optional) 'region'
	 * @param  Boolean $useservercerts   User server certificates
	 * @param  String  $disablesslverify Check to disable SSL Verify
	 * @return Array
	 */
	public function get_openstack_service($opts, $useservercerts = false, $disablesslverify = null) {

		// 'tenant', 'user', 'password', 'authurl', 'path', (optional) 'region'
		extract($opts);

		if (null === $disablesslverify) $disablesslverify = UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify');

		if (empty($user) || empty($password) || empty($authurl)) throw new Exception(__('Authorisation failed (check your credentials)', 'updraftplus'));// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $user, $password and $authurl being extracted in extract() line 29

		updraft_try_include_file('vendor/autoload.php', 'include_once');
		global $updraftplus;
		$updraftplus->log("OpenStack authentication URL: ".$authurl);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $authurl being extracted in extract() line 29

		$client = new OpenStack($authurl, array(// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $authurl being extracted in extract() line 29
			'username' => $user,// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $user being extracted in extract() line 29
			'password' => $password,// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $password being extracted in extract() line 29
			'tenantName' => $tenant// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $tenant being extracted in extract() line 29
		));
		$this->client = $client;

		if ($disablesslverify) {
			$client->setSslVerification(false);
		} else {
			if ($useservercerts) {
				$client->setConfig(array($client::SSL_CERT_AUTHORITY => false));
			} else {
				$client->setSslVerification(UPDRAFTPLUS_DIR.'/includes/cacert.pem', true, 2);
			}
		}

		$client->authenticate();

		if (empty($region)) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- The variable is defined below.
			$catalog = $client->getCatalog();
			if (!empty($catalog)) {
				$items = $catalog->getItems();
				if (is_array($items)) {
					foreach ($items as $item) {
						$name = $item->getName();
						$type = $item->getType();
						if ('swift' != $name || 'object-store' != $type) continue;
						$eps = $item->getEndpoints();
						if (!is_array($eps)) continue;
						foreach ($eps as $ep) {
							if (is_object($ep) && !empty($ep->region)) {
								$region = $ep->region;
							}
						}
					}
				}
			}
		}

		$this->region = $region;

		return $client->objectStoreService('swift', $region);

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
			'authurl' => '',
			'password' => '',
			'tenant' => '',
			'path' => '',
			'region' => ''
		);
	}
	
	public function credentials_test($posted_settings) {

		if (empty($posted_settings['user'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('username', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['password'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('password', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['tenant'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), _x('tenant', '"tenant" is a term used with OpenStack storage - Google for "OpenStack tenant" to get more help on its meaning', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['authurl'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('authentication URI', 'updraftplus'));
			return;
		}

		$opts = array(
			'user' => $posted_settings['user'],
			'password' => $posted_settings['password'],
			'authurl' => $posted_settings['authurl'],
			'tenant' => $posted_settings['tenant'],
			'region' => empty($posted_settings['region']) ? '' : $posted_settings['region'],
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
		if (is_array($opts) && $opts['user'] && '' !== $opts['user'] && !empty($opts['authurl'])) return true;
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
				{{{curl_existence_label}}}
				<br>
				<p>{{openstack_text_description}} <a href="{{faq_link_url}}" target="_blank">{{faq_link_text}}</a></p>
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
			<th>{{input_authentication_uri_label}}:</th>
			<td><input title="{{input_authentication_uri_title}}" data-updraft_settings_test="authurl" type="text" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "authurl"}}" name="{{get_template_input_attribute_value "name" "authurl"}}" value="{{authurl}}" />
			<br>
			<em>{{input_authentication_uri_title}}</em>
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th><a href="{{input_tenant_link_url}}" title="{{input_tenant_link_title}}" target="_blank">{{input_tenant_label}}</a>:</th>
			<td><input data-updraft_settings_test="tenant" type="text" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "tenant"}}" name="{{get_template_input_attribute_value "name" "tenant"}}" value="{{tenant}}" />
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_region_label}}:</th>
			<td><input title="{{input_region_title}}" data-updraft_settings_test="region" type="text" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "region"}}" name="{{get_template_input_attribute_value "name" "region"}}" value="{{region}}" />
			<br>
			<em>{{input_region_title}}</em>
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_username_label}}:</th>
			<td><input data-updraft_settings_test="user" type="text" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "user"}}" name="{{get_template_input_attribute_value "name" "user"}}" value="{{user}}" />
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_password_label}}:</th>
			<td><input data-updraft_settings_test="password" type="{{input_password_type}}" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "password"}}" name="{{get_template_input_attribute_value "name" "password"}}" value="{{password}}" />
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_container_label}}:</th>
			<td><input data-updraft_settings_test="path" type="text" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "path"}}" name="{{get_template_input_attribute_value "name" "path"}}" value="{{path}}" /></td>
		</tr>
		{{{get_template_test_button_html "OpenStack (Swift)"}}}
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
			'mb_substr_existence_label' => !apply_filters('updraftplus_openstack_mbsubstr_exists', function_exists('mb_substr')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__('Your web server\'s PHP installation does not include a required module (%s).', 'updraftplus'), 'mbstring').' '.__('Please contact your web hosting provider\'s support.', 'updraftplus').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s.", 'updraftplus'), $this->desc, 'mbstring').' '.__('Please do not file any support requests; there is no alternative.', 'updraftplus'), $this->method, false), $this->allowed_html_for_content_sanitisation()) : '',
			'curl_existence_label' => wp_kses($updraftplus_admin->curl_check($this->long_desc, false, $this->method.' hidden-in-updraftcentral', false), $this->allowed_html_for_content_sanitisation()),
			'openstack_text_description' => __('Get your access credentials from your OpenStack Swift provider, and then pick a container name to use for storage.', 'updraftplus').' '.__('This container will be created for you if it does not already exist.', 'updraftplus'),
			'faq_link_text' => __('Also, you should read this important FAQ.', 'updraftplus'),
			'faq_link_url' => wp_kses(apply_filters("updraftplus_com_link", "https://updraftplus.com/faqs/there-appear-to-be-lots-of-extra-files-in-my-rackspace-cloud-files-container/"), array(), array('http', 'https')),
			'input_authentication_uri_label' => __('Authentication URI', 'updraftplus'),
			'input_authentication_uri_title' => _x('This needs to be a v2 (Keystone) authentication URI; v1 (Swauth) is not supported.', 'Keystone and swauth are technical terms which cannot be translated', 'updraftplus'),
			'input_tenant_label' => __('Tenant', 'updraftplus'),
			'input_tenant_link_url' => 'https://docs.openstack.org/openstack-ops/content/projects_users.html',
			'input_tenant_link_title' => __('Follow this link for more information', 'updraftplus'),
			'input_region_label' => __('Region', 'updraftplus'),
			'input_region_title' => __('Leave this blank, and a default will be chosen.', 'updraftplus'),
			'input_username_label' => __('Username', 'updraftplus'),
			'input_password_label' => __('Password', 'updraftplus'),
			'input_password_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_container_label' => __('Container', 'updraftplus'),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}
}
