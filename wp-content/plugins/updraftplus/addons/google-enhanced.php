<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: google-enhanced:Google Drive, enhanced
Description: Adds enhanced capabilities for Google Drive users
Version: 1.1
Shop: /shop/google-drive-enhanced/
Latest Change: 1.16.15
*/
// @codingStandardsIgnoreEnd

 new UpdraftPlus_Addon_Google_Enhanced;

class UpdraftPlus_Addon_Google_Enhanced {

	public function __construct() {
		add_filter('updraftplus_options_googledrive_others', array($this, 'options_googledrive_others'), 10, 2);
		add_filter('updraftplus_options_googledrive_options', array($this, 'transform_options_googledrive_options'));
		add_filter('updraftplus_googledrive_parent_id', array($this, 'googledrive_parent_id'), 10, 5);
		add_filter('updraftplus_options_googledrive_foldername', array($this, 'options_googledrive_foldername'), 10, 2);
		add_filter('updraftplus_jstree_googledrive', array($this, 'jstree_googledrive'), 10, 2);
		add_filter('updraft_googledrive_partial_templates', array($this, 'get_partial_templates'), 10);
		add_filter('updraft_googledrive_template_properties', array($this, 'partial_template_properties'));
	}

	public function options_googledrive_foldername($opt, $orig) {
		return $orig;
	}

	/**
	 * WordPress filter updraftplus_googledrive_parent_id
	 *
	 * @param String|Boolean $parent_id - parent ID value prior to filtering
	 * @param Array			 $opts		- service options
	 * @param Object		 $storage	- service object
	 * @param Object		 $module	- UpdraftPlus_BackupModule_googledrive object
	 * @param Boolean		 $one_only	- whether to return all results or just the oldest
	 *
	 * @return String|Boolean - filtered value
	 */
	public function googledrive_parent_id($parent_id, $opts, $storage, $module, $one_only = true) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Unused parameters are for future use.

		if (isset($opts['folder'])) {
			$folder = $opts['folder'];
		} else {
			if (isset($opts['parentid'])) {
				if (empty($opts['parentid'])) {
					$folder = '';
				} else {
					if (is_array($opts['parentid'])) {
						$folder = '#'.$opts['parentid']['id'];
					} else {
						$folder = '#'.$opts['parentid'];
					}
				}
			} else {
				$folder = 'UpdraftPlus';
			}
		}

		if ('#' === substr($folder, 0, 1)) {
			return substr($folder, 1);
		} else {
			return $module->id_from_path($folder, $one_only);
		}
	}

	/**
	 * Get partial templates of the Google Drive remote storage, the partial template is recognised by its name. To find out a name of partial template, look for the partial call syntax in the template, it's enclosed by double curly braces (i.e. {{> partial_template_name }})
	 *
	 * @param Array $partial_templates A collection of filterable partial templates
	 * @return Array an associative array keyed by name of the partial templates
	 */
	public function get_partial_templates($partial_templates) {
		ob_start();
		?>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_folder_label}}:</th>
				<td>
					<input title="{{input_enhanced_folder_title}}" type="text" id="{{get_template_input_attribute_value "id" "folder"}}" name="{{get_template_input_attribute_value "name" "folder"}}" value="{{folder}}" class="updraft_input--wide">
					{{#if is_authenticate_with_google}}
					<a href="#" class="updraft_googledrive_select_folder">{{input_select_folder_label}}</a>
					{{/if}}
					<br>
					<em>{{input_enhanced_folder_label}}</em>
					{{#if is_authenticate_with_google}}
					<div class="updraft_googledrive_container hidden-in-updraftcentral" style="clear:left;">
						<div class="updraft_googledrive_jstree_container">
							<div class="updraft_googledrive_jstree"></div>
						</div>
						<div id="updraft_jstree_buttons_googledrive">
							<button class="button updraft_googledrive_jstree_cancel">{{input_cancel_label}}</button> 
							<button class="button button-primary updraft_googledrive_jstree_confirm">{{input_confirm_label}}</button>
						</div>
					</div>
					{{/if}}
				</td>
			</tr>
		<?php
		if (!isset($partial_templates['gdrive_additional_configuration_top'])) $partial_templates['gdrive_additional_configuration_top'] = '';
		$partial_templates['gdrive_additional_configuration_top'] .= ob_get_clean();
		return $partial_templates;
	}

	/**
	 * This method is hooked to a filter and going to be accessed by any code within WordPress environment, so instead of sanitising each value in this method and/or using any other technique to prevent XSS attacks, just make sure each partial template has all variables escaped
	 */
	public function partial_template_properties() {
		return array(
			'input_enhanced_folder_title' => sprintf(__('Enter the path of the %s folder you wish to use here.', 'updraftplus'), 'Google Drive').' '.__('If the folder does not already exist, then it will be created.').' '.sprintf(__('e.g. %s', 'updraftplus'), 'MyBackups/WorkWebsite.').' '.sprintf(__('If you leave it blank, then the backup will be placed in the root of your %s', 'updraftplus'), 'Google Drive').' '.sprintf(__('In %s, path names are case sensitive.', 'updraftplus'), 'Google Drive'),
			'input_enhanced_folder_label' => sprintf(__('In %s, path names are case sensitive.', 'updraftplus'), 'Google Drive'),
		);
	}

	/**
	 * Returns the Google Drives addon HTML content to be displayed on the page
	 * DEVELOPER NOTE: Please don't use/call this method anymore as it was used as a partial template of Google Drive storage, and it's consider to be removed in future versions. Once the Google Drive template is CSP-compliant, this should be removed and should be placed in the class child instead of the base class. @see get_partial_templates()
	 *
	 * @param  [String] $folder_opts          - the free HTML content that will be replaced by the content in this method
	 * @param  [Object] $backup_module_object - the backup module object this will allow us to get and use various functions
	 * @return [String] the premium HTML content that will be displayed on the page
	 */
	public function options_googledrive_others($folder_opts, $backup_module_object) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Unused parameters are for future use.
		$classes = $backup_module_object->get_css_classes();
		return '<tr class="'.$classes.'">
					<th>'.__('Google Drive', 'updraftplus').' '.__('Folder', 'updraftplus').':</th>
					<td>
						<input title="'.esc_attr(sprintf(__('Enter the path of the %s folder you wish to use here.', 'updraftplus'), 'Google Drive').' '.__('If the folder does not already exist, then it will be created.').' '.sprintf(__('e.g. %s', 'updraftplus'), 'MyBackups/WorkWebsite.').' '.sprintf(__('If you leave it blank, then the backup will be placed in the root of your %s', 'updraftplus'), 'Google Drive')).' '.sprintf(__('In %s, path names are case sensitive.', 'updraftplus'), 'Google Drive').
						'" type="text" '.$backup_module_object->output_settings_field_name_and_id('folder', true).' value="{{folder}}" class="updraft_input--wide">
						{{#if is_authenticate_with_google}}
						<a href="#" class="updraft_googledrive_select_folder">{{input_select_folder_label}}</a>
						{{/if}}
						<br>
						<em>'.htmlspecialchars(sprintf(__('In %s, path names are case sensitive.', 'updraftplus'), 'Google Drive')).'</em>
						
						{{#if is_authenticate_with_google}}
						<div class="updraft_googledrive_container hidden-in-updraftcentral" style="clear:left;">
							<div class="updraft_googledrive_jstree_container">
								<div class="updraft_googledrive_jstree"></div>
							</div>
							<div id="updraft_jstree_buttons_googledrive">
								<button class="button updraft_googledrive_jstree_cancel">{{input_cancel_label}}</button> 
								<button class="button button-primary updraft_googledrive_jstree_confirm">{{input_confirm_label}}</button>
							</div>
						</div>
						{{/if}}
					</td>
				</tr>';
	}
	
	/**
	 * Modifies handlebar template options
	 *
	 * @param array $opts
	 * @return array - New handerbar template options
	 */
	public function transform_options_googledrive_options($opts) {
		if (!isset($opts['folder'])) {
			if (isset($opts['parentid'])) {
				if (is_array($opts['parentid'])) {
					if (isset($opts['parentid']['name'])) {
						$opts['folder'] = $opts['parentid']['name'];
					} else {
						$opts['folder'] = empty($opts['parentid']['id']) ? '' : '#'.$opts['parentid']['id'];
					}
				} else {
					$opts['folder'] = empty($opts['parentid']) ? '' : '#'.$opts['parentid'];
				}
			} else {
				$opts['folder'] = 'UpdraftPlus';
			}
		}
		return $opts;
	}

	/**
	 * This function will return all user directories in Google Drive.
	 *
	 * @param Array $node_array - The default value
	 * @param Array $params     - The search parameters
	 * @return Array $node_array - Array results for JSTree
	 */
	public function jstree_googledrive($node_array, $params) {
		if (!isset($params['node']['id']) || !isset($params['instance_id'])) return $node_array;

		$options = UpdraftPlus_Options::get_updraft_option('updraft_googledrive');
		$instance_id = $params['instance_id'];

		if (!isset($options['settings'][$instance_id])) return $node_array;

		if ('#' == $params['node']['id']) {
			$search = 'root';
		} else {
			$search = $params['node']['id'];
		}
		
		$googledrive = UpdraftPlus_Storage_Methods_Interface::get_storage_object('googledrive');
		$googledrive->set_options($options['settings'][$instance_id], false, $instance_id);
		$folders = $googledrive->list_folders($search);

		if ($folders) {
			foreach ($folders as $folder) {
				$node_array[] = array(
					'text' => $folder['name'],
					'parent' => $params['node']['id'],
					'children' => true,
					'id' => $folder['id'],
					'icon' => 'jstree-folder',
					'data' => $folder
				);
			}
		}
		
		return $node_array;
	}
}
