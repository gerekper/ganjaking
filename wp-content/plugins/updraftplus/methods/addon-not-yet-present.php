<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (!class_exists('UpdraftPlus_BackupModule')) updraft_try_include_file('methods/backup-module.php', 'require_once');

class UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule {

	private $method;

	private $description;

	private $required_php;

	private $image;

	private $error_msg;

	private $error_msg_trans;

	public function __construct($method, $description, $required_php = false, $image = null) {
		$this->method = $method;
		$this->description = $description;
		$this->required_php = $required_php;
		$this->image = $image;
		$this->error_msg = 'This remote storage method ('.$this->description.') requires PHP '.$this->required_php.' or later';
		$this->error_msg_trans = sprintf(__('This remote storage method (%s) requires PHP %s or later.', 'updraftplus'), $this->description, $this->required_php);
	}

	public function backup($backup_array) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is present because the function to perform backup for specific storage is not exist.

		$this->log("You do not have the UpdraftPlus ".$this->method.' add-on installed - get it from '.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").'');
		
		$this->log(sprintf(__('You do not have the UpdraftPlus %s add-on installed - get it from %s', 'updraftplus'), $this->description, ''.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").''), 'error', 'missingaddon-'.$this->method);
		
		return false;

	}

	/**
	 * Retrieve a list of supported features for this storage method
	 *
	 * Currently known features:
	 *
	 * - multi_options : indicates that the remote storage module
	 * can handle its options being in the Feb-2017 multi-options
	 * format. N.B. This only indicates options handling, not any
	 * other multi-destination options.
	 *
	 * - multi_servers : not implemented yet: indicates that the
	 * remote storage module can handle multiple servers at backup
	 * time. This should not be specified without multi_options.
	 * multi_options without multi_servers is fine - it will just
	 * cause only the first entry in the options array to be used.
	 *
	 * - config_templates : not implemented yet: indicates that
	 * the remote storage module can output its configuration in
	 * Handlebars format via the get_configuration_template() method.
	 *
	 * - conditional_logic : indicates that the remote storage module
	 * can handle predefined logics regarding how backups should be
	 * sent to the remote storage
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// The 'multi_options' options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates');
	}

	public function delete($files, $method_obj = false, $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is present because the function to perform delete for specific storage is not exist.

		$this->log('You do not have the UpdraftPlus '.$this->method.' add-on installed - get it from '.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").'');
		
		$this->log(sprintf(__('You do not have the UpdraftPlus %s add-on installed - get it from %s', 'updraftplus'), $this->description, ''.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").''), 'error', 'missingaddon-'.$this->method);

		return false;

	}

	public function listfiles($match = 'backup_') {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is present because the function to perform listfiles for specific storage is not exist.
		return new WP_Error('no_addon', sprintf(__('You do not have the UpdraftPlus %s add-on installed - get it from %s', 'updraftplus'), $this->description, ''.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/")));
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		ob_start();
	?>
		<tr class="{{css_class}} {{method_id}}">
			<th>{{description}}:</th>
			<td>{{{image}}}{{addon_text}} - <a href="{{premium_url}}" target="_blank">{{premium_url_text}}</a></td>
		</tr>
		{{#unless php_version_supported}}
		<tr class="{{css_class}} {{method_id}}">
		<th></th>
		<td>
			<em>{{error_msg_trans}} {{hosting_text}} {{php_version_text}}</em>
		</td>
		</tr>
		{{/unless}}
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
		global $updraftplus;
		$properties = array(
			'description' => $this->description,
			'php_version_supported' => (bool) apply_filters('updraftplus_storage_meets_php_requirement', version_compare(phpversion(), $this->required_php, '>='), $this->method),
			'image' => (!empty($this->image)) ? '<p><img src="'.UPDRAFTPLUS_URL.'/images/'.$this->image.'"></p>' : '',
			'error_msg_trans' => $this->error_msg_trans,
			'premium_url' => $updraftplus->get_url('premium'),
			'premium_url_text' => __('follow this link to get it', 'updraftplus'),
			'addon_text' => sprintf(__('%s support is available as an add-on', 'updraftplus'), $this->description),
			'php_version_text' => sprintf(__('Your PHP version: %s.', 'updraftplus'), phpversion()),
			'hosting_text' => __('You will need to ask your web hosting company to upgrade.', 'updraftplus'),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}
}
