<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: dropbox-folders:Dropbox folders
Description: Allows Dropbox to use sub-folders - useful if you are backing up many sites into one Dropbox
Version: 1.6
Shop: /shop/dropbox-folders/
Latest Change: 1.13.11
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

add_filter('updraftplus_dropbox_extra_config_template', array('UpdraftPlus_Addon_DropboxFolders', 'get_configuration_template'), 10, 2);
add_filter('updraftplus_options_dropbox_options', array('UpdraftPlus_Addon_DropboxFolders', 'transform_options_dropbox_options'));
add_filter('updraftplus_dropbox_modpath', array('UpdraftPlus_Addon_DropboxFolders', 'change_path'), 10, 2);

class UpdraftPlus_Addon_DropboxFolders {

	/**
	 * Returns the Dropbox Folders addon HTML content to be displayed on the page
	 *
	 * @param  [string] $defmsg               - the free HTML content that will be replaced by the content in this method
	 * @param  [object] $backup_module_object - the backup module object this will allow us to get and use various functions and storage options
	 * @return [string] - the premium HTML content that will be displayed on the page
	 */
	public static function get_configuration_template($defmsg, $backup_module_object) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Filter use
		ob_start();
		$classes = $backup_module_object->get_css_classes();
		?> 
		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Store at', 'updraftplus');?>:</th>
			<td>
				{{folder_path}}<input type="text" style="width: 292px" <?php echo $backup_module_object->output_settings_field_name_and_id('folder'); ?> value="{{folder}}" />
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
