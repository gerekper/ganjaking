<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: dropbox-folders:Dropbox folders
Description: Allows Dropbox to use sub-folders - useful if you are backing up many sites into one Dropbox
Version: 1.7
Shop: /shop/dropbox-folders/
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

add_filter('updraftplus_options_dropbox_options', array('UpdraftPlus_Addon_DropboxFolders', 'transform_options_dropbox_options'));
add_filter('updraftplus_dropbox_modpath', array('UpdraftPlus_Addon_DropboxFolders', 'change_path'), 10, 2);
add_filter('updraft_dropbox_partial_templates', array('UpdraftPlus_Addon_DropboxFolders', 'get_partial_templates'));
add_filter('updraft_dropbox_template_properties', array('UpdraftPlus_Addon_DropboxFolders', 'get_partial_template_properties'));


class UpdraftPlus_Addon_DropboxFolders {

	/**
	 * Get Dropbox partial templates of the folders addon, the partial template is recognised by its name. To find out a name of partial template, look for the partial call syntax in the template, it's enclosed by double curly braces (i.e. {{> partial_template_name }})
	 *
	 * @param Array $partial_templates A collection of filterable partial templates
	 * @return Array an associative array keyed by name of the partial templates
	 */
	public static function get_partial_templates($partial_templates) {
		if (!isset($partial_templates['dropbox_additional_configuration_top'])) $partial_templates['dropbox_additional_configuration_top'] = '';
		$partial_templates['dropbox_additional_configuration_top'] = self::get_configuration_template();
		return $partial_templates;
	}

	/**
	 * This method is hooked to a filter and going to be accessed by any code within WordPress environment, so instead of sanitising each value in this method and/or using any other technique to prevent XSS attacks, just make sure each partial template has all variables escaped
	 */
	public static function get_partial_template_properties() {
		return array(
			'input_store_at_label' => __('Store at', 'updraftplus'),
		);
	}

	/**
	 * Returns the Dropbox Folders addon HTML content to be displayed on the page
	 *
	 * @return [string] - the premium HTML content that will be displayed on the page
	 */
	private static function get_configuration_template() {
		ob_start();
		?>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_store_at_label}}:</th>
			<td>
				{{folder_path}}<input type="text" style="width: 292px" id="{{get_template_input_attribute_value "id" "folder"}}" name="{{get_template_input_attribute_value "name" "folder"}}" value="{{folder}}" />
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts handerbar template options
	 * @return array - New handerbar template options
	 */
	public static function transform_options_dropbox_options($opts) {
		$key = empty($opts['appkey']) ? '' : $opts['appkey'];
		$folder_path = '';
		if ('dropbox:' != substr($key, 0, 8)) {
			$folder_path .= 'apps/';
			// "upgraded" means that an OAuth1 token was upgraded to OAuth2. It was only possible to have an OAuth1 token if they authenticated on the old app (since new authentications after we added the new app were all on that), so this indicates the old app.
			if (empty($opts['upgraded']) && empty($opts['tk_request_token'])) {
				$folder_path .= 'UpdraftPlus.Com';
			} else {
				$folder_path .= 'UpdraftPlus';
			}
			$folder_path .= '/';
		}
		$opts['folder_path'] = $folder_path;
		return $opts;
	}
	
	/**
	 * This method will construct the path to the file that is passed to this method and return the path to the caller ready to be used.
	 *
	 * @param  [string] $file                 - the name of the file
	 * @param  [object] $backup_module_object - the backup module object this will allow us to get and use various functions
	 * @return [string] the real path where the users Dropbox file is stored
	 */
	public static function change_path($file, $backup_module_object) {
		$opts = $backup_module_object->get_options();
		$folder = empty($opts['folder']) ? '' : $opts['folder'];
		$dropbox_folder = trailingslashit($folder);
		return ('/' == $dropbox_folder || './' == $dropbox_folder) ? $file : $dropbox_folder.$file;
	}
}
