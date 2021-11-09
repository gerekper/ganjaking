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
	public function googledrive_parent_id($parent_id, $opts, $storage, $module, $one_only = true) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

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
	 * Returns the Google Drives addon HTML content to be displayed on the page
	 *
	 * @param  [String] $folder_opts          - the free HTML content that will be replaced by the content in this method
	 * @param  [Object] $backup_module_object - the backup module object this will allow us to get and use various functions
	 * @return [String] the premium HTML content that will be displayed on the page
	 */
	public function options_googledrive_others($folder_opts, $backup_module_object) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$classes = $backup_module_object->get_css_classes();
		return '<tr class="'.$classes.'">
					<th>'.__('Google Drive', 'updraftplus').' '.__('Folder', 'updraftplus').':</th>
					<td>
						<input title="'.esc_attr(sprintf(__('Enter the path of the %s folder you wish to use here.', 'updraftplus'), 'Google Drive').' '.__('If the folder does not already exist, then it will be created.').' '.sprintf(__('e.g. %s', 'updraftplus'), 'MyBackups/WorkWebsite.').' '.sprintf(__('If you leave it blank, then the backup will be placed in the root of your %s', 'updraftplus'), 'Google Drive')).' '.sprintf(__('In %s, path names are case sensitive.', 'updraftplus'), 'Google Drive').
						'" type="text" '.$backup_module_object->output_settings_field_name_and_id('folder', true).' value="{{folder}}" class="updraft_input--wide">
						<br>
						<em>'.htmlspecialchars(sprintf(__('In %s, path names are case sensitive.', 'updraftplus'), 'Google Drive')).'</em>
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
}
