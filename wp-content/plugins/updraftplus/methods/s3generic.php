<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

updraft_try_include_file('methods/s3.php', 'require_once');

/**
 * Converted to multi-options (Feb 2017-) and previous options conversion removed: Yes
 */
class UpdraftPlus_BackupModule_s3generic extends UpdraftPlus_BackupModule_s3 {

	protected $provider_can_use_aws_sdk = false;
	
	protected $provider_has_regions = false;

	/**
	 * Given an S3 object, possibly set the region on it
	 *
	 * @param Object $obj		  - like UpdraftPlus_S3
	 * @param String $region
	 * @param String $bucket_name
	 */
	protected function set_region($obj, $region = '', $bucket_name = '') {
		$config = $this->get_config();
		$endpoint = ('' != $region && 'n/a' != $region) ? $region : $config['endpoint'];
		$log_message = "Set endpoint (".get_class($obj)."): $endpoint";
		$log_message_append = '';
		if (is_string($endpoint) && preg_match('/^(.*):(\d+)$/', $endpoint, $matches)) {
			$endpoint = $matches[1];
			$port = $matches[2];
			$log_message_append = ", port=$port";
			$obj->setPort($port);
		}
		// This provider requires domain-style access. In future it might be better to provide an option rather than hard-coding the knowledge.
		if (is_string($endpoint) && preg_match('/\.aliyuncs\.com$/i', $endpoint)) {
			$obj->useDNSBucketName(true, $bucket_name);
		}
		global $updraftplus;
		if ($updraftplus->backup_time) $this->log($log_message.$log_message_append);
		$obj->setEndpoint($endpoint);
	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not mentioned are asuumed to not be supported)
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
			'accesskey' => '',
			'secretkey' => '',
			'path' => '',
			'endpoint' => '',
		);
	}

	/**
	 * Retrieve specific options for this remote storage module
	 *
	 * @param Boolean $force_refresh - if set, and if relevant, don't use cached credentials, but get them afresh
	 *
	 * @return Array - an array of options
	 */
	protected function get_config($force_refresh = false) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		$opts = $this->get_options();
		$opts['whoweare'] = 'S3';
		$opts['whoweare_long'] = __('S3 (Compatible)', 'updraftplus');
		$opts['key'] = 's3generic';
		return $opts;
	}

	/**
	 * Get the pre configuration template
	 */
	public function get_pre_configuration_template() {
		?>
		<tr class="{{get_template_css_classes false}} S3_pre_config_container">
			<td colspan="2">
				{{{pre_template_opening_html}}}
				<br>
				{{{xmlwriter_existence_label}}}
				{{{simplexmlelement_existence_label}}}
				{{{curl_existence_label}}}
				<br>
				<p>
					{{{ssl_certificates_errors_link_text}}}
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get partial templates of the S3-Generic remote storage, the partial template is recognised by its name. To find out a name of partial template, look for the partial call syntax in the template, it's enclosed by double curly braces (i.e. {{> partial_template_name }})
	 *
	 * @return Array an associative array keyed by name of the partial templates
	 */
	public function get_partial_templates() {
		$partial_templates = array();
		$partial_templates['s3generic_additional_configuration_top'] = '';
		ob_start();
		?>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_endpoint_label}}:</th>
			<td>
				<input data-updraft_settings_test="endpoint" type="text" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "endpoint"}}" name="{{get_template_input_attribute_value "name" "endpoint"}}" value="{{endpoint}}" />
			</td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_bucket_access_style_label}}:<br>{{{input_bucket_access_style_readmore}}}</th>
			<td>
				<select data-updraft_settings_test="bucket_access_style" id="{{get_template_input_attribute_value "id" "bucket_access_style"}}" name="{{get_template_input_attribute_value "name" "bucket_access_style"}}" class="udc-wd-600">
					{{#each input_bucket_access_style_option_labels}}
						<option {{#ifeq ../bucket_access_style @key}}selected="selected"{{/ifeq}} value="{{@key}}">{{this}}</option>
					{{/each}}
				</select>
			</td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_signature_version_label}}:<br>{{{input_signature_version_readmore}}}</th>
			<td>
				<select data-updraft_settings_test="signature_version" id="{{get_template_input_attribute_value "id" "signature_version"}}" name="{{get_template_input_attribute_value "name" "signature_version"}}" class="udc-wd-600">
					{{#each input_signature_version_option_labels}}
						<option {{#ifeq ../signature_version @key}}selected="selected"{{/ifeq}} value="{{@key}}">{{this}}</option>
					{{/each}}
				</select>
			</td>
		</tr>
		<?php
		$partial_templates['s3generic_additional_configuration_bottom'] = ob_get_clean();
		return wp_parse_args(apply_filters('updraft_'.$this->get_id().'_partial_templates', $partial_templates), parent::get_partial_templates());
	}
	
	/**
	 * Modifies handerbar template options
	 * The function require because It should override parent class's UpdraftPlus_BackupModule_s3::transform_options_for_template() functionality with no operation.
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		if (!empty($opts['instance_id']) && 'default' !== $opts['instance_id']) {
			if (!isset($opts['signature_version'])) { // this check is to find out whether we're dealing with a pre-existing configuration or not
				$opts['signature_version'] = 'v2'; // the pre-existing S3-Compatible configurations before signature_version was introduced by default use SigV2, so we wan't to keep it that way as we don't want to break what's already working
				$opts['signature_version'] = apply_filters('updraftplus_s3_signature_version', $opts['signature_version'], false, $this);
				if (!empty($opts['endpoint'])) {
					if (preg_match('/\.(leviia|r2\.cloudflarestorage)\.com$/i', $opts['endpoint']) || (preg_match('/\.amazonaws\.com$/i', $opts['endpoint']) && !empty($opts['bucket_access_style']) && 'virtual_host_style' === $opts['bucket_access_style'])) {
						// due to the merge of S3-generic bucket access style MR on March 2021, if virtual-host bucket access style is selected, connecting to an amazonaws bucket location where the user doesn't have an access to it will throw an S3 InvalidRequest exception. It requires the signature to be set to version 4
						$opts['signature_version'] = 'v4';
					}
				}
			}
			if (!$this->options_exist($opts)) $opts['signature_version'] = 'v4'; // if no pre-existing S3-Compatible configurations were setup, there would always be an initial instance id created with a blank configuration form (no access key, secret key, location, and endpoint), this initial instance id/form should use SigV4
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
		return (parent::options_exist($opts) && !empty($opts['endpoint']));
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
			'pre_template_opening_html' => wp_kses('<p>'.__('Examples of S3-compatible storage providers:', 'updraftplus').' <a href="https://updraftplus.com/use-updraftplus-digital-ocean-spaces/" target="_blank">DigitalOcean Spaces</a>, <a href="https://www.linode.com/products/object-storage/" target="_blank">Linode Object Storage</a>, <a href="https://www.cloudian.com" target="_blank">Cloudian</a>, <a href="https://www.mh.connectria.com/rp/order/cloud_storage_index" target="_blank">Connectria</a>, <a href="https://www.constant.com/cloud/storage/" target="_blank">Constant</a>, <a href="https://www.eucalyptus.cloud/" target="_blank">Eucalyptus</a>, <a href="http://cloud.nifty.com/storage/" target="_blank">Nifty</a>, <a href="http://www.ntt.com/business/services/cloud/iaas/cloudn.html" target="_blank">Cloudn</a>'.__('... and many more!', 'updraftplus').'</p>', $this->allowed_html_for_content_sanitisation()),
			'xmlwriter_existence_label' => !apply_filters('updraftplus_s3generic_xmlwriter_exists', 'UpdraftPlus_S3_Compat' != $this->indicate_s3_class() || !class_exists('XMLWriter')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__("Your web server's PHP installation does not include a required module (%s).", 'updraftplus'), 'XMLWriter').' '.__("Please contact your web hosting provider's support and ask for them to enable it.", 'updraftplus'), $this->get_id(), false), $this->allowed_html_for_content_sanitisation()) : '',
			'simplexmlelement_existence_label' => !apply_filters('updraftplus_s3generic_simplexmlelement_exists', class_exists('SimpleXMLElement')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__("Your web server's PHP installation does not include a required module (%s).", 'updraftplus'), 'SimpleXMLElement').' '.__("Please contact your web hosting provider's support.", 'updraftplus').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s.", 'updraftplus'), $updraftplus->backup_methods[$this->get_id()], 'SimpleXMLElement').' '.__('Please do not file any support requests; there is no alternative.', 'updraftplus'), $this->get_id(), false), $this->allowed_html_for_content_sanitisation()) : '',
			'curl_existence_label' => wp_kses($updraftplus_admin->curl_check($updraftplus->backup_methods[$this->get_id()], true, $this->get_id().' hide-in-udc', false), $this->allowed_html_for_content_sanitisation()),
			'ssl_certificates_errors_link_text' => wp_kses('<a href="'.apply_filters("updraftplus_com_link", "https://updraftplus.com/faqs/i-get-ssl-certificate-errors-when-backing-up-andor-restoring/").'" target="_blank">'.__('If you see errors about SSL certificates, then please go here for help.', 'updraftplus').'</a>', $this->allowed_html_for_content_sanitisation()),
			'input_access_key_label' => sprintf(__('%s access key', 'updraftplus'), 'S3'),
			'input_secret_key_label' => sprintf(__('%s secret key', 'updraftplus'), 'S3'),
			'input_secret_key_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_location_label' => sprintf(__('%s location', 'updraftplus'), 'S3'),
			'input_location_title' => __('Enter only a bucket name or a bucket and path.', 'updraftplus').' '.__('Examples: mybucket, mybucket/mypath', 'updraftplus'),
			'input_endpoint_label' => sprintf(__('%s end-point', 'updraftplus'), 'S3'),
			'input_bucket_access_style_label' => __('Bucket access style', 'updraftplus'),
			'input_bucket_access_style_readmore' => wp_kses('<a aria-label="'.esc_attr__('Read more about bucket access style', 'updraftplus').'" href="https://updraftplus.com/faqs/what-is-the-different-between-path-style-and-bucket-style-access-to-an-s3-compatible-bucket/" target="_blank"><em>'.__('(Read more)', 'updraftplus').'</em></a>', $this->allowed_html_for_content_sanitisation()),
			'input_bucket_access_style_option_labels' => array(
				'path_style' => __('Path style', 'updraftplus'),
				'virtual_host_style' => __('Virtual-host style', 'updraftplus'),
			),
			'input_signature_version_label' => __('Signature version', 'updraftplus'),
			'input_signature_version_readmore' => wp_kses('<a aria-label="'.esc_attr__('Read more about signature version', 'updraftplus').'" href="https://aws.amazon.com/blogs/aws/amazon-s3-update-sigv2-deprecation-period-extended-modified/" target="_blank"><em>'.__('(Read more)', 'updraftplus').'</em></a>', $this->allowed_html_for_content_sanitisation()),
			'input_signature_version_option_labels' => array(
				'v4' => __('SigV4', 'updraftplus'),
				'v2' => __('SigV2', 'updraftplus'),
			),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}

	/**
	 * Use DNS bucket name if the remote storage is found to be using s3generic and its bucket access style is set to virtual-host
	 *
	 * @param Object $storage - S3 Name
	 * @param String $bucket  - storage path
	 * @param Array  $config  - configuration - may not be complete at this stage, so be careful about which properties are used
	 *	 *
	 * @return Boolean true if currently processing s3generic remote storage that uses virtual-host style, false otherwise
	 */
	protected function maybe_use_dns_bucket_name($storage, $bucket, $config) {

		$signature_version = !empty($config['signature_version']) ? $config['signature_version'] : apply_filters('updraftplus_s3_signature_version', 'v2', false, $this); // the 'v2' value used to be handled by the use_v4 class variable, but since the signature_version (dropdown) setting has been introduced then use_v4 class variable has been removed and is no longer needed
		if (is_callable(array($storage, 'setSignatureVersion'))) $storage->setSignatureVersion($signature_version); // we don't prioritise the hardcoded endpoint if signature_version is set, which means users are aware of this new option and intentionally set this option and/or save the settings
		if ((!empty($config['endpoint']) && preg_match('/\.(leviia|aliyuncs|r2\.cloudflarestorage)\.com$/i', $config['endpoint'])) || (!empty($config['bucket_access_style']) && 'virtual_host_style' === $config['bucket_access_style'])) {
			// due to the recent merge of S3-generic bucket access style on March 2021, if virtual-host bucket access style is selected, connecting to an amazonaws bucket location where the user doesn't have an access to it will throw an S3 InvalidRequest exception. It requires the signature to be set to version 4
			// Cloudflare R2 supports V4 only
			if (empty($config['signature_version']) && preg_match('/\.(leviia|amazonaws|r2\.cloudflarestorage)\.com$/i', $config['endpoint'])) {
				if (is_callable(array($storage, 'setSignatureVersion'))) $storage->setSignatureVersion('v4');
			}
			return $this->use_dns_bucket_name($storage, '');
		}
		return false;
	}
}
