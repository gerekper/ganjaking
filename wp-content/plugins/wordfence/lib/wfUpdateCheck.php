<?php

class wfUpdateCheck {

	private $needs_core_update = false;
	private $core_update_version = 0;
	private $plugin_updates = array();
	private $all_plugins = array();
	private $plugin_slugs = array();
	private $theme_updates = array();
	private $api = null;

	public static function syncAllVersionInfo() {
		// Load the core/plugin/theme versions into the WAF configuration.
		wfConfig::set('wordpressVersion', wfUtils::getWPVersion());
		wfWAFConfig::set('wordpressVersion', wfUtils::getWPVersion(), wfWAF::getInstance(), 'synced');

		if (!function_exists('get_plugins')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}

		$pluginVersions = array();
		foreach (get_plugins() as $pluginFile => $pluginData) {
			$slug = plugin_basename($pluginFile);
			if (preg_match('/^([^\/]+)\//', $pluginFile, $matches)) {
				$slug = $matches[1];
			} else if (preg_match('/^([^\/.]+)\.php$/', $pluginFile, $matches)) {
				$slug = $matches[1];
			}
			$pluginVersions[$slug] = isset($pluginData['Version']) ? $pluginData['Version'] : null;
		}

		wfConfig::set_ser('wordpressPluginVersions', $pluginVersions);
		wfWAFConfig::set('wordpressPluginVersions', $pluginVersions, wfWAF::getInstance(), 'synced');

		if (!function_exists('wp_get_themes')) {
			require_once(ABSPATH . '/wp-includes/theme.php');
		}

		$themeVersions = array();
		foreach (wp_get_themes() as $slug => $theme) {
			$themeVersions[$slug] = isset($theme['Version']) ? $theme['Version'] : null;
		}

		wfConfig::set_ser('wordpressThemeVersions', $themeVersions);
		wfWAFConfig::set('wordpressThemeVersions', $themeVersions, wfWAF::getInstance(), 'synced');
	}

	public function __construct() {
		$this->api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
	}
	
	public function __sleep() {
		return array('needs_core_update', 'core_update_version', 'plugin_updates', 'all_plugins', 'plugin_slugs', 'theme_updates');
	}
	
	public function __wakeup() {
		$this->api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
	}
	
	/**
	 * @return bool
	 */
	public function needsAnyUpdates() {
		return $this->needsCoreUpdate() || count($this->getPluginUpdates()) > 0 || count($this->getThemeUpdates()) > 0;
	}

	/**
	 * Check for any core, plugin or theme updates.
	 *
	 * @return $this
	 */
	public function checkAllUpdates($useCachedValued = true) {
		return $this->checkCoreUpdates($useCachedValued)
			->checkPluginUpdates($useCachedValued)
			->checkThemeUpdates($useCachedValued);
	}

	/**
	 * Check if there is an update to the WordPress core.
	 *
	 * @return $this
	 */
	public function checkCoreUpdates($useCachedValued = true) {
		$this->needs_core_update = false;

		if (!function_exists('wp_version_check')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		if (!function_exists('get_preferred_from_update_core')) {
			require_once(ABSPATH . 'wp-admin/includes/update.php');
		}
		
		include(ABSPATH . WPINC . '/version.php'); //defines $wp_version
		
		$update_core = get_preferred_from_update_core();
		if ($useCachedValued && isset($update_core->last_checked) && isset($update_core->version_checked) && 12 * HOUR_IN_SECONDS > (time() - $update_core->last_checked) && $update_core->version_checked == $wp_version) { //Duplicate of _maybe_update_core, which is a private call
			//Do nothing, use cached value
		}
		else {
			wp_version_check();
			$update_core = get_preferred_from_update_core();
		}

		if (isset($update_core->response) && $update_core->response == 'upgrade') {
			$this->needs_core_update = true;
			$this->core_update_version = $update_core->current;
		}

		return $this;
	}

	private function checkPluginFile($plugin, &$installedPlugins) {
		if (!array_key_exists($plugin, $installedPlugins))
			return null;
		$file = wfUtils::getPluginBaseDir() . $plugin;
		if (!file_exists($file)) {
			unset($installedPlugins[$plugin]);
			return null;
		}
		return $file;
	}

	private function initializePluginUpdateData($plugin, &$installedPlugins, $checkVulnerabilities, $populator = null) {
		$file = $this->checkPluginFile($plugin, $installedPlugins);
		if ($file === null)
			return null;
		$data = $installedPlugins[$plugin];
		$data['pluginFile'] = $file;
		if ($populator !== null)
			$populator($data, $file);
		if (!array_key_exists('slug', $data) || empty($data['slug']))
			$data['slug'] = $this->extractSlug($plugin);
		$slug = $data['slug'];
		if ($slug !== null) {
			$vulnerable = $checkVulnerabilities ? $this->isPluginVulnerable($slug, $data['Version']) : null;
			$data['vulnerable'] = !empty($vulnerable);
			if ($data['vulnerable'] && is_string($vulnerable))
				$data['vulnerabilityLink'] = $vulnerable;
			$this->plugin_slugs[] = $slug;
			$this->all_plugins[$slug] = $data;
		}
		unset($installedPlugins[$plugin]);
		return $data;
	}

	public function extractSlug($plugin, $data = null) {
		$slug = null;
		if (is_array($data) && array_key_exists('slug', $data))
			$slug = $data['slug'];
		if (!is_string($slug) || empty($slug)) {
			if (preg_match('/^([^\/]+)\//', $plugin, $matches)) {
				$slug = $matches[1];
			}
			else if (preg_match('/^([^\/.]+)\.php$/', $plugin, $matches)) {
				$slug = $matches[1];
			}
		}
		return $slug;
	}

	private static function requirePluginsApi() {
		if (!function_exists('plugins_api'))
			require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
	}

	private function fetchPluginUpdates($useCache = true) {
		$update_plugins = get_site_transient('update_plugins');
		if ($useCache && isset($update_plugins->last_checked) && 12 * HOUR_IN_SECONDS > (time() - $update_plugins->last_checked)) //Duplicate of _maybe_update_plugins, which is a private call
			return $update_plugins;
		if (!function_exists('wp_update_plugins'))
			require_once(ABSPATH . WPINC . '/update.php');
		wp_update_plugins();
		return get_site_transient('update_plugins');
	}

	/**
	 * Check if any plugins need an update.
	 *
	 * @param bool $checkVulnerabilities whether or not to check for vulnerabilities while checking updates
	 *
	 * @return $this
	 */
	public function checkPluginUpdates($useCachedValued = true, $checkVulnerabilities = true) {
		if($checkVulnerabilities)
			$this->plugin_updates = array();

		self::requirePluginsApi();

		$update_plugins = $this->fetchPluginUpdates();
		
		//Get the full plugin list
		if (!function_exists('get_plugins')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}
		$installedPlugins = get_plugins();

		$context = $this;

		if ($update_plugins && !empty($update_plugins->response)) {
			foreach ($update_plugins->response as $plugin => $vals) {
				$data = $this->initializePluginUpdateData($plugin, $installedPlugins, $checkVulnerabilities, function (&$data, $file) use ($context, $plugin, $vals) {
					$vals = (array) $vals;
					$data['slug'] = $context->extractSlug($plugin, $vals);
					$data['newVersion'] = (isset($vals['new_version']) ? $vals['new_version'] : 'Unknown');
					$data['wpURL'] = (isset($vals['url']) ? rtrim($vals['url'], '/') : null);
					$data['updateAvailable'] = true;
				});

				if($checkVulnerabilities && $data !== null)
					$this->plugin_updates[] = $data;
			}
		}
		
		//We have to grab the slugs from the update response because no built-in function exists to return the true slug from the local files
		if ($update_plugins && !empty($update_plugins->no_update)) {
			foreach ($update_plugins->no_update as $plugin => $vals) {
				$this->initializePluginUpdateData($plugin, $installedPlugins, $checkVulnerabilities, function (&$data, $file) use ($context, $plugin, $vals) {
					$vals = (array) $vals;
					$data['slug'] = $context->extractSlug($plugin, $vals);
					$data['wpURL'] = (isset($vals['url']) ? rtrim($vals['url'], '/') : null);
				});
			}	
		}
		
		//Get the remaining plugins (not in the wordpress.org repo for whatever reason)
		foreach ($installedPlugins as $plugin => $data) {
			$data = $this->initializePluginUpdateData($plugin, $installedPlugins, $checkVulnerabilities);
		}

		return $this;
	}

	/**
	 * Check if any themes need an update.
	 *
	 * @param bool $checkVulnerabilities whether or not to check for vulnerabilities while checking for updates
	 *
	 * @return $this
	 */
	public function checkThemeUpdates($useCachedValued = true, $checkVulnerabilities = true) {
		if($checkVulnerabilities)
			$this->theme_updates = array();

		if (!function_exists('wp_update_themes')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		
		$update_themes = get_site_transient('update_themes');
		if ($useCachedValued && isset($update_themes->last_checked) && 12 * HOUR_IN_SECONDS > (time() - $update_themes->last_checked)) { //Duplicate of _maybe_update_themes, which is a private call
			//Do nothing, use cached value
		}
		else {
			wp_update_themes();
			$update_themes = get_site_transient('update_themes');
		}

		if ($update_themes && (!empty($update_themes->response)) && $checkVulnerabilities) {
			if (!function_exists('wp_get_themes')) {
				require_once(ABSPATH . '/wp-includes/theme.php');
			}
			$themes = wp_get_themes();
			foreach ($update_themes->response as $theme => $vals) {
				foreach ($themes as $name => $themeData) {
					if (strtolower($name) == $theme) {
						$vulnerable = false;
						if (isset($themeData['Version'])) {
							$vulnerable = $this->isThemeVulnerable($theme, $themeData['Version']);
						}
						
						$this->theme_updates[] = array(
							'newVersion' => (isset($vals['new_version']) ? $vals['new_version'] : 'Unknown'),
							'package'    => (isset($vals['package']) ? $vals['package'] : null),
							'URL'        => (isset($vals['url']) ? $vals['url'] : null),
							'Name'       => $themeData['Name'],
							'name'       => $themeData['Name'],
							'version'    => $themeData['Version'],
							'vulnerable' => $vulnerable
						);
					}
				}
			}
		}
		return $this;
	}
	
	public function checkAllVulnerabilities() {
		$this->checkPluginVulnerabilities();
		$this->checkThemeVulnerabilities();
	}

	private function initializePluginVulnerabilityData($plugin, &$installedPlugins, &$records, $values = null, $update = false) {
		$file = $this->checkPluginFile($plugin, $installedPlugins);
		if ($file === null)
			return null;
		$data = $installedPlugins[$plugin];
		$record = array(
			'slug' => $this->extractSlug($plugin, $values),
			'fromVersion' => isset($data['Version']) ? $data['Version'] : 'Unknown',
			'vulnerable' => false
		);
		if ($update && is_array($values))
			$record['toVersion'] = isset($values['new_version']) ? $values['new_version'] : 'Unknown';
		$records[] = $record;
		unset($installedPlugins[$plugin]);
	}

	/**
	 * @param bool $initial if true, treat as the initial scan run
	 */
	public function checkPluginVulnerabilities($initial=false) {

		self::requirePluginsApi();
		
		$vulnerabilities = array();
		
		//Get the full plugin list
		if (!function_exists('get_plugins')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}
		$installedPlugins = get_plugins();
		
		//Get the info for plugins on wordpress.org
		$update_plugins = $this->fetchPluginUpdates();
		if ($update_plugins) {
			if (!empty($update_plugins->response)) {
				foreach ($update_plugins->response as $plugin => $vals) {
					$this->initializePluginVulnerabilityData($plugin, $installedPlugins, $vulnerabilities, (array) $vals, true);
				}
			}
			
			if (!empty($update_plugins->no_update)) {
				foreach ($update_plugins->no_update as $plugin => $vals) {
					$this->initializePluginVulnerabilityData($plugin, $installedPlugins, $vulnerabilities, (array) $vals);
				}
			}
		}
		
		//Get the remaining plugins (not in the wordpress.org repo for whatever reason)
		foreach ($installedPlugins as $plugin => $data) {
			$this->initializePluginVulnerabilityData($plugin, $installedPlugins, $vulnerabilities, $data);
		}
		
		if (count($vulnerabilities) > 0) {
			try {
				$result = $this->api->call('plugin_vulnerability_check', array(), array(
					'plugins' => json_encode($vulnerabilities),
				));
				
				foreach ($vulnerabilities as &$v) {
					$vulnerableList = $result['vulnerable'];
					foreach ($vulnerableList as $r) {
						if ($r['slug'] == $v['slug']) {
							$v['vulnerable'] = !!$r['vulnerable'];
							if (isset($r['link'])) {
								$v['link'] = $r['link'];
							}
							break;
						}
					}
				}
			}
			catch (Exception $e) {
				//Do nothing
			}
			
			wfConfig::set_ser('vulnerabilities_plugin', $vulnerabilities);
		}
	}

	/**
	 * @param bool $initial whether or not this is the initial run
	 */
	public function checkThemeVulnerabilities($initial = false) {
		if (!function_exists('wp_update_themes')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		
		self::requirePluginsApi();
		
		$this->checkThemeUpdates(!$initial, false);
		$update_themes = get_site_transient('update_themes');
		
		$vulnerabilities = array();
		if ($update_themes && !empty($update_themes->response)) {
			if (!function_exists('get_plugin_data'))
			{
				require_once(ABSPATH . '/wp-admin/includes/plugin.php');
			}
			
			foreach ($update_themes->response as $themeSlug => $vals) {
				
				$valsArray = (array) $vals;
				$theme = wp_get_theme($themeSlug);
				
				$record = array();
				$record['slug'] = $themeSlug;
				$record['toVersion'] = (isset($valsArray['new_version']) ? $valsArray['new_version'] : 'Unknown');
				$record['fromVersion'] = $theme->version;
				$record['vulnerable'] = false;
				$vulnerabilities[] = $record;
			}
			
			try {
				$result = $this->api->call('theme_vulnerability_check', array(), array(
					'themes' => json_encode($vulnerabilities),
				));
				
				foreach ($vulnerabilities as &$v) {
					$vulnerableList = $result['vulnerable'];
					foreach ($vulnerableList as $r) {
						if ($r['slug'] == $v['slug']) {
							$v['vulnerable'] = !!$r['vulnerable'];
							break;
						}
					}
				}
			}
			catch (Exception $e) {
				//Do nothing
			}
			
			wfConfig::set_ser('vulnerabilities_theme', $vulnerabilities);
		}
	}
	
	public function isPluginVulnerable($slug, $version) {
		return $this->_isSlugVulnerable('vulnerabilities_plugin', $slug, $version, function(){ $this->checkPluginVulnerabilities(true); });
	}
	
	public function isThemeVulnerable($slug, $version) {
		return $this->_isSlugVulnerable('vulnerabilities_theme', $slug, $version, function(){ $this->checkThemeVulnerabilities(true); });
	}
	
	private function _isSlugVulnerable($vulnerabilitiesKey, $slug, $version, $populateVulnerabilities=null) {
		$vulnerabilities = wfConfig::get_ser($vulnerabilitiesKey, null);
		if($vulnerabilities===null){
			if(is_callable($populateVulnerabilities)){
				$populateVulnerabilities();
				return $this->_isSlugVulnerable($vulnerabilitiesKey, $slug, $version);
			}
			return false;
		}
		foreach ($vulnerabilities as $v) {
			if ($v['slug'] == $slug) {
				if ($v['fromVersion'] == 'Unknown' && $v['toVersion'] == 'Unknown') {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
				else if ((!isset($v['toVersion']) || $v['toVersion'] == 'Unknown') && version_compare($version, $v['fromVersion']) >= 0) {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
				else if ($v['fromVersion'] == 'Unknown' && isset($v['toVersion']) && version_compare($version, $v['toVersion']) < 0) {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
				else if (version_compare($version, $v['fromVersion']) >= 0 && isset($v['toVersion']) && version_compare($version, $v['toVersion']) < 0) {
					if ($v['vulnerable'] && isset($v['link']) && is_string($v['link'])) { return $v['link']; }
					return $v['vulnerable'];
				}
			}
		}
		return false;
	}

	/**
	 * @return boolean
	 */
	public function needsCoreUpdate() {
		return $this->needs_core_update;
	}

	/**
	 * @return int
	 */
	public function getCoreUpdateVersion() {
		return $this->core_update_version;
	}

	/**
	 * @return array
	 */
	public function getPluginUpdates() {
		return $this->plugin_updates;
	}
	
	/**
	 * @return array
	 */
	public function getAllPlugins() {
		return $this->all_plugins;
	}
	
	/**
	 * @return array
	 */
	public function getPluginSlugs() {
		return $this->plugin_slugs;
	}

	/**
	 * @return array
	 */
	public function getThemeUpdates() {
		return $this->theme_updates;
	}
}