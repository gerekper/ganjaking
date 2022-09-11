<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: azure:Microsoft Azure Support
Description: Microsoft Azure Support
Version: 1.5
Shop: /shop/azure/
Include: includes/azure
IncludePHP: methods/addon-base-v2.php
RequiresPHP: 5.3.3
Latest Change: 1.13.12
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');

class UpdraftPlus_Addons_RemoteStorage_azure extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	// https://msdn.microsoft.com/en-us/library/azure/ee691964.aspx - maximum block size is 4MB
	private $chunk_size = 2097152;

	public function __construct() {
		// 3rd parameter: chunking? 4th: Test button?
		parent::__construct('azure', 'Azure', true, true);
		// https://msdn.microsoft.com/en-us/library/azure/ee691964.aspx - maximum block size is 4MB
		if (defined('UPDRAFTPLUS_UPLOAD_CHUNKSIZE') && UPDRAFTPLUS_UPLOAD_CHUNKSIZE > 0) $this->chunk_size = max(UPDRAFTPLUS_UPLOAD_CHUNKSIZE, 4194304);
	}
	
	public function do_upload($file, $from) {
		global $updraftplus;

		$opts = $this->options;
		$storage = $this->get_storage();

		if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
		if (!is_object($storage)) throw new Exception("Azure service error");
		
		$filesize = filesize($from);
		$directory = empty($opts['directory']) ? '' : trailingslashit($opts['directory']);
		
		$account_name = $opts['account_name']; // Used here only for logging
		
		// If the user is using OneDrive for Germany option
		if (isset($opts['endpoint']) && 'blob.core.cloudapi.de' === $opts['endpoint']) {
			$odg_warning = sprintf(__('Due to the shutdown of the %1$s endpoint, support for %1$s will be ending soon. You will need to migrate to the Global endpoint in your UpdraftPlus settings. For more information, please see: %2$s', 'updraftplus'), 'Azure Germany', 'https://www.microsoft.com/en-us/cloud-platform/germany-cloud-regions');
			// We only want to log this once per backup job
			$this->log($odg_warning, 'warning', 'azure_de_migrate');
		}
		
		// Create/check container
		$container_name = $opts['container'];
		$container = $this->create_container($container_name);
		if (is_wp_error($container)) {
			$this->log("error: ".$container->get_error_message());
			$this->log("error: ".$container->get_error_message(), 'error');
			return false;
		} elseif (false == $container) {
			$this->log("error when attempting to access container ($container_name)");
			$this->log("error when attempting to access container ($container_name)", 'error');
		}
		
		// Perhaps it already exists (if we didn't get the final confirmation
		try {
			$items = $this->listfiles($directory.$file);
			foreach ($items as $item) {
				if (basename($item['name']) == $file && $item['size'] >= $filesize) {
					$this->log("$file: already uploaded");
					return true;
				}
			}
		} catch (Exception $e) {
			$this->log("file check: exception: ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
		}
		
		if (false != ($handle = fopen($from, 'rb'))) {
			if ($filesize <= $this->chunk_size) {
				$this->log("will upload file in one operation (azure://$account_name/$container_name/$directory$file)");
				$storage->createBlockBlob($opts['container'], $directory.$file, $handle);
				fclose($handle);
			} else {
				// Set up chunked upload

				$hash_key = md5($directory.$file);
				$container = $opts['container'];

				// Stored last uploaded block
				$block_ids = $this->jobdata_get('block_ids_'.$hash_key, array(), 'az_block_ids_'.$hash_key);

				if (!is_array($block_ids)) $block_ids = array();
				$block = 1;
				while (isset($block_ids[$block])) {
					$block++;
				}

				$uploaded_size = $this->chunk_size * ($block - 1);
				$this->block = $block;
				$this->uploaded_size = $uploaded_size;

				if ($uploaded_size) {
					$this->log("Resuming upload to azure://$account_name/$container_name/$directory$file from byte: $uploaded_size; block/chunk: $block");
				} else {
					$this->log("Starting fresh upload to azure://$account_name/$container_name/$directory$file from byte: 0; block/chunk: 1");
				}

				$ret = $updraftplus->chunked_upload($this, $file, "azure://$account_name/$container_name/$directory", $this->description, $this->chunk_size, $uploaded_size, false);

				fclose($handle);
				return $ret;
			}
		} else {
			throw new Exception("Failed to open file for reading: $from");
		}
		
		return true;
	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param  Array $azure an array of Azure options
	 * @return Array - the returned array can either be the set of updated Azure settings or a WordPress error array
	 */
	public function options_filter($azure) {
		// Get the current options (and possibly update them to the new format)
		$opts = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format('azure');
		
		if (is_wp_error($opts)) {
			if ('recursion' !== $opts->get_error_code()) {
				$msg = "(".$opts->get_error_code()."): ".$opts->get_error_message();
				$this->log($msg);
				error_log("UpdraftPlus: Azure: $msg");
			}
			// The saved options had a problem; so, return the new ones
			return $azure;
		}

		if (!is_array($azure)) return $opts;

		if (!empty($opts['settings']) && is_array($opts['settings'])) {
			// Remove instances that no longer exist
			foreach ($opts['settings'] as $instance_id => $storage_options) {
				if (!isset($azure['settings'][$instance_id])) unset($opts['settings'][$instance_id]);
			}
		}
		
		if (empty($azure['settings'])) return $opts;
		
		foreach ($azure['settings'] as $instance_id => $storage_options) {
			foreach ($storage_options as $key => $value) {
				if ('folder' == $key) $value = trim(str_replace('\\', '/', $value), '/');
				// Only lower-case containers are permitted - enforce this
				if ('container' == $key) $value = strtolower($value);
				$opts['settings'][$instance_id][$key] = ('key' == $key || 'account_name' == $key) ? trim($value) : $value;
				// Convert one likely misunderstanding of the format to enter the account name in
				if ('account_name' == $key && preg_match('#^https?://(.*)\.blob\.core\.windows#i', $opts['settings'][$instance_id]['account_name'], $matches)) {
					$opts['settings'][$instance_id]['account_name'] = $matches[1];
				}
			}
		}
		return $opts;
	}
	
	/**
	 * Chunked Upload
	 *
	 * @param  string $file         FIle to be chunked
	 * @param  string $fp           FTP URL
	 * @param  string $chunk_index  This is the chunked index
	 * @param  string $upload_size  This is the upload size
	 * @param  string $upload_start This is the upload start position
	 * @param  string $upload_end   This is the Upload end positions
	 * @return boolean
	 */
	public function chunked_upload($file, $fp, $chunk_index, $upload_size, $upload_start, $upload_end) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		$opts = $this->options;
		$directory = !empty($opts['directory']) ? trailingslashit($opts['directory']) : "";
		$storage = $this->get_storage();

		// Already done?
		$block_ids_key = 'block_ids_'.md5($directory.$file);
		$block_ids = $this->jobdata_get($block_ids_key, array(), 'az_'.$block_ids_key);
		if (!is_array($block_ids)) $block_ids = array();
		// Return 1, not true, to prevent expensive database logging of all the previous chunks on each resumption
		if (isset($block_ids[$chunk_index])) return 1;
		
		// Each block needs id of the same length
		$block_id = str_pad($chunk_index, 6, "0", STR_PAD_LEFT);
		
		try {
			$data = fread($fp, $upload_size);
			$storage->createBlobBlock($opts['container'], $directory.$file, base64_encode($block_id), $data);
		} catch (Exception $e) {
			$this->log("upload: exception (".get_class($e)."): ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
			return false;
		}
		
		// Store the Block ID of uploaded block
		if (is_array($block_ids)) {
			$block_ids[$chunk_index] = $block_id;
		} else {
			$block_ids = array($chunk_index => $block_id);
		}
		
		$this->jobdata_set($block_ids_key, $block_ids);
		
		return true;
	}

	/**
	 * This method will send the final block of data to be written to file on the Azure remote storage location
	 *
	 * @param  String $file - the file to read from
	 * @return Boolean - a boolean value to indicate success or failure of the chunked upload finish call
	 */
	public function chunked_upload_finish($file) {
		$this->log("all chunks uploaded; now commmitting blob blocks");
		// Commit the blocks to create the blob

		$opts = $this->get_options();
		$storage = $this->get_storage();
		$directory = !empty($opts['directory']) ? trailingslashit($opts['directory']) : "";
		$hash_key = md5($directory.$file);

		$block_ids = $this->jobdata_get('block_ids_'.$hash_key, array(), 'az_block_ids_'.$hash_key);
		if (!is_array($block_ids)) return false;

		$blocks = array();
		foreach ($block_ids as $b_id) {
			$block = new WindowsAzure\Blob\Models\Block();
			$block->setBlockId(base64_encode($b_id));
			$block->setType('Uncommitted');
			array_push($blocks, $block);
		}

		try {
			$storage->commitBlobBlocks($opts['container'], $directory.$file, $blocks);
		} catch (Exception $e) {
			$message = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			$this->log("service error: ".$message);
			$this->log($message, 'error');
			return false;
		}
		// Prevent bloat
		$this->jobdata_delete('block_ids_'.$hash_key, null);
		return true;
	}

	public function do_download($file, $fullpath) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;

		$opts = $this->options;
		$storage = $this->get_storage();

		if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
		if (!is_object($storage)) throw new Exception("Azure service error");
		
		$container_name = $opts['container'];
		$directory = !empty($opts['directory']) ? trailingslashit($opts['directory']) : "";
		$this->azure_path = $directory.$file;
		
		try {
			$blob_properties = $storage->getBlobProperties($container_name, $this->azure_path)->getProperties();
		} catch (WindowsAzure\Common\ServiceException $e) {
			if (404 == $e->getCode()) {
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), 'Azure').": ".__('File not found', 'updraftplus'), 'error');
			}
			throw $e;
		}

		return $updraftplus->chunked_download($file, $this, $blob_properties->getContentLength(), true, $container_name, $this->chunk_size);

	}

	public function chunked_download($file, $headers, $container_name) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Filter use

		$storage = $this->get_storage();

		if (is_array($headers) && !empty($headers['Range']) && preg_match('/bytes=(\d+)-(\d+)$/', $headers['Range'], $matches)) {
			$options = new WindowsAzure\Blob\Models\GetBlobOptions;
			$options->setRangeStart($matches[1]);
			$options->setRangeEnd($matches[2]);
		} else {
			$options = null;
		}

		$blob = $storage->getBlob($container_name, $this->azure_path, $options);

		$headers = $blob->getProperties();

		// The Azure SDK turns the string into a stream. In the absence of other options, we change it back.
		$stream = $blob->getContentStream();
		return fread($stream, $headers->getContentLength());
	}
	
	/**
	 * Delete a single file from the service
	 *
	 * @param String $file - filename
	 * @return Boolean|String  - either a boolean or an error code string
	 */
	public function do_delete($file) {
		$opts = $this->options;
		$storage = $this->get_storage();
		
		$directory = !empty($opts['directory']) ? trailingslashit($opts['directory']) : "";
		$azure_path = $directory.$file;
		
		if (is_object($storage) && !is_wp_error($storage)) {
			// list blobs
			$blobs = $this->listfiles($file);
			
			// check if needed blob is there
			foreach ($blobs as $blob) {
				if (isset($blob['name']) && basename($blob['name']) == $file) {
					try {
						// if match, delete file
						$storage->deleteBlob($opts['container'], $azure_path);
						return true;
					} catch (WindowsAzure\Common\ServiceException $e) {
						$this->log("File delete failed: Service Exception");
						return 'file_delete_error';
					}
				}
			}
			
			// if no, log an error
			$this->log("file does not exist");
			return 'file_delete_error';
		}

		if (is_wp_error($storage)) {
			$this->log("service was not available (".$storage->get_error_message().")");
			return 'service_unavailable';
		}

		$this->log("delete error");
		return false;
	}

	/**
	 * This method is used to get a list of backup files for the remote storage option
	 *
	 * @param  string $match - a string to match when looking for files
	 * @return Array - returns an array of file locations or a WordPress error
	 */
	public function do_listfiles($match = 'backup_') {
		$opts = $this->get_options();
		
		$directory = !empty($opts['directory']) ? trailingslashit($opts['directory']) : "";
		
		try {
			$storage = $this->bootstrap();
			if (!is_object($storage)) throw new Exception('Azure service error');
		} catch (Exception $e) {
			$storage = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			return $storage;
		}
		
		try {
			$list_options = new WindowsAzure\Blob\Models\ListBlobsOptions;
			$list_options->setPrefix($directory.$match);
			$storage = $this->get_storage();
			$blob_list = $storage->listBlobs($opts['container'], $list_options);
		} catch (WindowsAzure\Common\ServiceException $e) {
			return new WP_Error('list_files_failed', 'List Files ServiceException');
		}
		
		$blobs = $blob_list->getBlobs();

		$results = array();
		foreach ($blobs as $blob) {
			$blob_name = basename($blob->getName());
			$blob_prop = $blob->getProperties();
			$blob_size = $blob_prop->getContentLength();
			$results[] = array('name' => $blob_name, 'size' => $blob_size);
		}
		
		return $results;
	}

	/**
	 * Get a list of parameters required to be present for a credential tests, plus descriptions
	 *
	 * @return Array
	 */
	public function get_credentials_test_required_parameters() {
		return array(
			'account_name' => 'Account Name',
			'key' => 'Account Key',
			'container' => 'Container',
		);
	}
	
	protected function do_credentials_test($testfile, $posted_settings = array()) {
		$storage = $this->get_storage();
		
		$container_name = $posted_settings['container'];

		$directory = !empty($posted_settings['directory']) ? trailingslashit($posted_settings['directory']) : "";
		try {
			$exists = $this->create_container($container_name);

			if (is_wp_error($exists)) {
				foreach ($exists->get_error_messages() as $msg) {
					echo "$msg\n";
				}
				return false;
			}

		} catch (Exception $e) {
			echo __('Could not access container', 'updraftplus').': '.$e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';

			return false;
		}
		try {
			$storage->createBlockBlob($container_name, $directory.$testfile, "UpdraftPlus temporary test file - you can remove this.");
		} catch (Exception $e) {
			echo 'Azure: '.__('Upload failed', 'updraftplus').': '.$e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			return false;
		}

		return true;
		
	}
	
	/**
	 * Delete a temporary file use for a credentials test. Output can be echo-ed.
	 *
	 * @param String $testfile		  - the basename of the file to delete
	 * @param Array  $posted_settings - the settings to use
	 *
	 * @return void
	 */
	protected function do_credentials_test_deletefile($testfile, $posted_settings) {
		$container_name = $posted_settings['container'];
		$directory = !empty($posted_settings['directory']) ? trailingslashit($posted_settings['directory']) : "";
		$storage = $this->get_storage();
		try {
			$storage->deleteBlob($container_name, $directory.$testfile);
		} catch (Exception $e) {
			echo __('Delete failed:', 'updraftplus').' '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
		}

	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not mentioned are assumed to not be supported)
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
			'account_name' => '',
			'key' => '',
			'container' => '',
			'endpoint' => 'blob.core.windows.net',
		);
	}
	
	public function do_bootstrap($opts) {

		// The Azure SDK requires PEAR modules - specifically,  HTTP_Request2, Mail_mime, and Mail_mimeDecode; however, an analysis of the used code paths shows that we only need HTTP_Request2
		if (false === strpos(get_include_path(), UPDRAFTPLUS_DIR.'/includes/PEAR')) set_include_path(UPDRAFTPLUS_DIR.'/includes/PEAR'.PATH_SEPARATOR.get_include_path());
		include_once(UPDRAFTPLUS_DIR.'/includes/WindowsAzure/WindowsAzure.php');
		include_once(UPDRAFTPLUS_DIR.'/includes/azure-extensions.php');
		// use WindowsAzure\Common\ServicesBuilder;
		
		// set up connection string
		// DefaultEndpointsProtocol=[http|https];AccountName=[yourAccount];AccountKey=[yourKey]
		if (empty($opts)) $opts = $this->get_options();
		
		$protocol = isset($opts['nossl']) ? ($opts['nossl'] ? 'http' : 'https') : (UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl') ? 'http' : 'https');

		$account_name = $opts['account_name'];
		$account_key = $opts['key'];

		// Not implemented
// $ssl_disableverify = isset($opts['ssl_disableverify']) ? $opts['ssl_disableverify'] : UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify');
		$ssl_useservercerts = isset($opts['ssl_useservercerts']) ? $opts['ssl_useservercerts'] : UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts');
		$ssl_ca_path = $ssl_useservercerts ? '' : UPDRAFTPLUS_DIR.'/includes/cacert.pem';

		$connection_string = "DefaultEndpointsProtocol=$protocol;AccountName=$account_name;AccountKey=$account_key";
		// Non-standard element that our extended builder uses
		if ('https' == $protocol) $connection_string .=';SSLCAPath='.$ssl_ca_path;

		$storage = $this->get_storage();
		$endpoint = empty($opts['endpoint']) ? 'blob.core.windows.net' : $opts['endpoint'];

		if (empty($storage)) {
			try {
				$blob_rest_proxy = WindowsAzure\Common\UpdraftPlus_ServicesBuilder::getInstance()->createBlobService($connection_string, $endpoint);
				$storage = $blob_rest_proxy;
				$this->set_storage($storage);
				return $blob_rest_proxy;
			} catch (Exception $e) {
				return new WP_Error('blob_service_failed', 'Error when attempting to setup Azure access: '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')');
			}
		} else {
			return $storage;
		}
	}
	
	/**
	 * Returns a list of container names. Currently unused method
	 *
	 * @return array
	 */
	protected function list_containers() {
		$storage = $this->get_storage();
		try {
			$containers = $storage->listContainers();
			$container_list = $containers->getContainers();
			return $container_list;
		} catch (Exception $e) {
			return new WP_Error('container_list_failed', 'Could not list containers: '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')');
		}
	}
	
	/**
	 * Check if the container exists (using list_containers above) and if not creates the container. Returns the container properties.
	 *
	 * @param  string  $container_name The container name
	 * @param  boolean $create_on_404  Checks if need to create a 404
	 * @return array
	 */
	protected function create_container($container_name, $create_on_404 = true) {
		$storage = $this->get_storage();
		try {
			$container_properties = $storage->getContainerProperties($container_name);
			return $container_properties;
		} catch (WindowsAzure\Common\ServiceException $e) {
			if ($create_on_404 && 404 == $e->getCode()) {
			} else {
				throw $e;
			}
		} catch (Exception $e) {
			return new WP_Error('container_create_failed', 'Could not create containers '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')');
		}

		try {
			$create_container_options = new WindowsAzure\Blob\Models\CreateContainerOptions();
			$create_container_options->setPublicAccess(WindowsAzure\Blob\Models\PublicAccessType::NONE);
			// This does not return anything - it will throw an exception if there's a problem
			$storage->createContainer($container_name, $create_container_options);
			return $this->create_container($container_name, false);
		} catch (WindowsAzure\Common\ServiceException $e) {
			return new WP_Error('container_creation_failed', __('Could not create the container', 'updraftplus'));
		}

		// Should not be possible to reach this point
		return false;
	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['account_name']) && !empty($opts['key'])) return true;
		return false;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		?>
		<tr class="{{get_template_css_classes false}} azure_pre_config_container">
			<td colspan="2">
				<?php
					/*$site_host = parse_url(network_site_url(), PHP_URL_HOST);
					/*if ('127.0.0.1' == $site_host || '::1' == $site_host || 'localhost' == $site_host) {
						// Of course, there are other things that are effectively 127.0.0.1. This is just to help.
						$callback_text = '<p><strong>'.htmlspecialchars(sprintf(__('Microsoft Azure is not compatible with sites hosted on a localhost or 127.0.0.1 URL - their developer console forbids these (current URL is: %s).','updraftplus'), site_url())).'</strong></p>';
					} else {
						$callback_text = '<p>'.htmlspecialchars(__('You must add the following as the authorised redirect URI in your Azure console (under "API Settings") when asked','updraftplus')).': <kbd>'.UpdraftPlus_Options::admin_page_url().'</kbd></p>';
					}*/
				?>
				<img width="434" src="{{storage_image_url}}">
				{{{simplexmlelement_existence_label}}}
				<p><a href="https://account.live.com/developers/applications/create" target="_blank">{{credentials_creation_link_text}}</a></p>
				<p><a href="https://updraftplus.com/faqs/microsoft-azure-setup-guide/" target="_blank">{{configuration_helper_link_text}}</a></p>
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
			<th>{{input_account_name_label}}:</th>
			<td><input title="{{input_account_name_title}}" data-updraft_settings_test="account_name" type="text" autocomplete="off" id="{{get_template_input_attribute_value "id" "account_name"}}" name="{{get_template_input_attribute_value "name" "account_name"}}" value="{{account_name}}" class="updraft_input--wide udc-wd-600" /><br><em>{{input_account_name_title}}</em></td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_key_label}}:</th>
			<td><input data-updraft_settings_test="key" type="{{input_key_type}}" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "key"}}" name="{{get_template_input_attribute_value "name" "key"}}" value="{{key}}" /></td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_container_label}}:</th>
			<td><input data-updraft_settings_test="container" title="" type="text" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "container"}}" name="{{get_template_input_attribute_value "name" "container"}}" value="{{container}}"><br><a href="https://azure.microsoft.com/en-gb/documentation/articles/storage-php-how-to-use-blobs/" target="_blank"><em>{{input_container_link_text}}</a></em></td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{{input_prefix_label}}}:</th>
			<td><input title="{{input_prefix_title}}" data-updraft_settings_test="directory" type="text" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "directory"}}" name="{{get_template_input_attribute_value "name" "directory"}}" value="{{directory}}"></td>
		</tr>
		<tr class="{{get_template_css_classes true}}">
			<th>{{input_endpoint_label}}:</th>
			<td>
				<select data-updraft_settings_test="endpoint" id="{{get_template_input_attribute_value "id" "endpoint"}}" name="{{get_template_input_attribute_value "name" "endpoint"}}" style="width: 140px" class="updraft_input--wide udc-wd-600">
					{{#each input_endpoint_option_labels}}
						<option {{#ifeq ../endpoint @key}}selected="selected"{{/ifeq}} value="{{@key}}">{{this}}</option>
					{{/each}}
				</select>
			</td>
		</tr>
		{{{get_template_test_button_html "Azure"}}}
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
		global $updraftplus_admin;
		$properties = array(
			'storage_image_url' => UPDRAFTPLUS_URL.'/images/azure.png',
			'simplexmlelement_existence_label' => !apply_filters('updraftplus_azure_simplexmlelement_exists', class_exists('SimpleXMLElement')) ? wp_kses($updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__("Your web server's PHP installation does not included a <strong>required</strong> (for %s) module (%s). Please contact your web hosting provider's support and ask for them to enable it.", 'updraftplus'), 'Azure', 'php-xml - SimpleXMLElement'), 'azure', false), $this->allowed_html_for_content_sanitisation()) : '',
			'credentials_creation_link_text' => __('Create Azure credentials in your Azure developer console.', 'updraftplus'),
			'configuration_helper_link_text' => __('For more detailed instructions, follow this link.', 'updraftplus'),
			'input_account_name_label' => sprintf(__('%s Account Name', 'updraftplus'), __('Azure', 'updraftplus')),
			'input_account_name_title' => __('This is not your Azure login - see the instructions if needing more guidance.', 'updraftplus'),
			'input_key_label' => sprintf(__('%s Key', 'updraftplus'), __('Azure', 'updraftplus')),
			'input_key_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_container_label' => sprintf(__('%s Container', 'updraftplus'), __('Azure', 'updraftplus')),
			'input_container_title' => sprintf(__('Enter the path of the %s you wish to use here.', 'updraftplus'), 'container').' '.sprintf(__('If the %s does not already exist, then it will be created.'), 'container'),
			'input_container_link_text' => __("See Microsoft's guidelines on container naming by following this link.", 'updraftplus'),
			'input_prefix_label' => wp_kses(sprintf(__('%s Prefix', 'updraftplus'), __('Azure', 'updraftplus')).' <em>('.__('optional', 'updraftplus').')</em>', $this->allowed_html_for_content_sanitisation()),
			'input_prefix_title' => sprintf(__('You can enter the path of any %s virtual folder you wish to use here.', 'updraftplus'), 'Azure').' '.sprintf(__('If you leave it blank, then the backup will be placed in the root of your %s', 'updraftplus').'.', __('container', 'updraftplus')),
			'input_endpoint_label' => __('Azure Account', 'updraftplus'),
			'input_endpoint_option_labels' => array(
				'blob.core.windows.net' => __('Azure Global', 'updraftplus'),
				'blob.core.cloudapi.de' => __('Azure Germany', 'updraftplus'),
				'blob.core.usgovcloudapi.net' => __('Azure Government', 'updraftplus'),
				'blob.core.chinacloudapi.cn' => __('Azure China', 'updraftplus'),
			),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), 'Azure'),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts - current options
	 * @return array - Filtered handerbar template options
	 */
	protected function do_transform_options_for_template($opts) {
		$opts['container'] = empty($opts['container']) ? '' : strtolower($opts['container']);
		return $opts;
	}
}

// Do *not* instantiate here; it is a storage module, so is instantiated on-demand
// $updraftplus_addons_azure = new UpdraftPlus_Addons_RemoteStorage_azure;
