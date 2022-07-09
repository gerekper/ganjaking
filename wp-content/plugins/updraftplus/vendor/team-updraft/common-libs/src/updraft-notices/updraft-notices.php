<?php

if (!defined('ABSPATH')) die('No direct access allowed');

abstract class Updraft_Notices_1_2 {

	protected $notices_content;
	
	// These variables are just short-hands to be used in advert content.
	protected $dashboard_top = array('top');

	protected $dashboard_top_or_report = array('top', 'report', 'report-plain');

	protected $dashboard_bottom_or_report = array('bottom', 'report', 'report-plain');

	protected $anywhere = array('top', 'bottom', 'report', 'report-plain');

	protected $autobackup = array('autobackup');

	protected $autobackup_bottom_or_report = array('autobackup', 'bottom', 'report', 'report-plain');

	/**
	 * Global adverts that appear in all products will be returned to the child to display
	 *
	 * @return array
	 */
	protected function populate_notices_content() {
		return array();
	}
	
	/**
	 * Call this method to setup the notices.
	 */
	abstract protected function notices_init();
	
	/**
	 * Checks if the plugin is installed and checks status if needed.
	 *
	 * @param null    $product             - Plugin to check
	 * @param boolean $also_require_active - bool to indicate if active status is required or not
	 *
	 * @return boolean Returns true, if plugin is installed otherwise false
	 */
	protected function is_plugin_installed($product = null, $also_require_active = false) {
		if ($also_require_active) return class_exists($product);
		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$plugins = get_plugins();
		foreach ($plugins as $value) {
			if ($value['TextDomain'] == $product) {
				// We have found the plugin so return false so that we do not display this advert.
				return false;
			}
		}
		return true;
	}

	/**
	 * Checks if translation is needed or not
	 *
	 * @param string $plugin_base_dir - Base directory of plugin
	 * @param string $product_name    - Plugin name
	 *
	 * @return boolean Returns true if translation is needed, otherwise false
	 */
	protected function translation_needed($plugin_base_dir, $product_name) {
		$wplang = get_locale();
		if (strlen($wplang) < 1 || 'en_US' == $wplang || 'en_GB' == $wplang) return false;
		if (defined('WP_LANG_DIR') && is_file(WP_LANG_DIR.'/plugins/'.$product_name.'-'.$wplang.'.mo')) return false;
		if (is_file($plugin_base_dir.'/languages/'.$product_name.'-'.$wplang.'.mo')) return false;
		return true;
	}
	
	/**
	 * Generates the start of a HTML URL
	 *
	 * @param boolean $html_allowed - indicates if HTML is allowed or not
	 * @param string $url           - the URL
	 * @param boolean $https        - the protocol to use
	 * @param string $website_home  - the product website name
	 *
	 * @return string returns a partial HTML URL
	 */
	protected function url_start($html_allowed, $url, $https = false, $website_home = null) {
		$proto = ($https) ? 'https' : 'http';
		if (strpos($url, $website_home) !== false) {
			return $html_allowed ? "<a href=".apply_filters(str_replace('.', '_', $website_home).'_link', $proto.'://'.$url).'>' : '';
		} else {
			return $html_allowed ? '<a href="'.$proto.'://'.$url.'">' : '';
		}
	}

	/**
	 * Generate the end of a HTML URL
	 *
	 * @param boolean $html_allowed - indicates if HTML is allowed or not
	 * @param string  $url          - the URL
	 * @param boolean $https        - the protocol to use
	 *
	 * @return string returns a partial HTML URL
	 */
	protected function url_end($html_allowed, $url, $https = false) {
		$proto = $https ? 'https' : 'http';
		return $html_allowed ? '</a>' : ' ('.$proto.'://'.$url.')';
	}

	/**
	 * Renders notice
	 *
	 * @param mixed   $notice                 - a specific notice to render or false
	 * @param string  $position               - position of the notice
	 * @param boolean $return_instead_of_echo - indicates if we should echo notice or return as string
	 *
	 * @return mixed Returns string or echos notice
	 */
	public function do_notice($notice = false, $position = 'top', $return_instead_of_echo = false) {

		$this->notices_init();
	
		if (false === $notice) $notice = apply_filters('updraft_notices_force_id', false, $this);
		
		$notice_content = $this->get_notice_data($notice, $position);
		
		if (false != $notice_content) {
			return $this->render_specified_notice($notice_content, $return_instead_of_echo, $position);
		}
	}

	/**
	 * This method will return a notice ready for display.
	 *
	 * @param  boolean $notice   - a specific notice to render or false
	 * @param  string  $position - position of the notice
	 *
	 * @return array returns notice data
	 */
	protected function get_notice_data($notice = false, $position = 'top') {

		// If a specific notice has been passed to this method then return that notice.
		if ($notice) {
			if (!isset($this->notices_content[$notice])) return false;
		
			// Does the notice support the position specified?
			if (isset($this->notices_content[$notice]['supported_positions']) && !in_array($position, $this->notices_content[$notice]['supported_positions'])) return false;

			// First check if the advert passed can be displayed and hasn't been dismissed, we do this by checking what dismissed value we should be checking.
			$dismiss_time = $this->notices_content[$notice]['dismiss_time'];

			$dismiss = $this->check_notice_dismissed($dismiss_time);

			if ($dismiss) return false;

			// If the advert has a validity function, then require the advert to be valid
			if (!empty($this->notices_content[$notice]['validity_function']) && !call_user_func(array($this, $this->notices_content[$notice]['validity_function']))) return false;

			return $this->notices_content[$notice];
		}

		// Create an array to add non-seasonal adverts to so that if a seasonal advert can't be returned we can choose a random advert from this array.
		$available_notices = array();

		// If Advert wasn't passed then next we should check to see if a seasonal advert can be returned.
		foreach ($this->notices_content as $notice_id => $notice_data) {
			// Does the notice support the position specified?
			if (isset($this->notices_content[$notice_id]['supported_positions']) && !in_array($position, $this->notices_content[$notice_id]['supported_positions'])) continue;
			
			// If the advert has a validity function, then require the advert to be valid.
			if (!empty($notice_data['validity_function']) && !call_user_func(array($this, $notice_data['validity_function']))) continue;
		
		
			if (isset($notice_data['valid_from']) && isset($notice_data['valid_to'])) {
				if ($this->skip_seasonal_notices($notice_data)) return $notice_data;
			} else {
				$dismiss_time = $this->notices_content[$notice_id]['dismiss_time'];
				$dismiss = $this->check_notice_dismissed($dismiss_time);
			
				if (!$dismiss) $available_notices[$notice_id] = $notice_data;
			}
		}
		
		if (empty($available_notices)) return false;

		// If a seasonal advert can't be returned then we will return a random advert.

		// Here we give a 25% chance for the rate advert to be returned before selecting a random advert from the entire collection which also includes the rate advert
		if (0 == rand(0, 3) && isset($available_notices['rate'])) return $available_notices['rate'];
		
		// Using shuffle here as something like rand which produces a random number and uses that as the array index fails, this is because in future an advert may not be numbered and could have a string as its key which will then cause errors.
		shuffle($available_notices);
		return $available_notices[0];
	}

	/**
	 * Skip seasonal notices
	 *
	 * @param array $notice_data - an array of data for the chosen notice
	 *
	 * @return boolean
	 */
	protected function skip_seasonal_notices($notice_data) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return false;
	}

	/**
	 * Returns affiliate ID
	 *
	 * @return mixed Returns affiliate ID
	 */
	public function get_affiliate_id() {
		return $this->self_affiliate_id;
	}

	/**
	 * Checks if the notice has been dismissed
	 *
	 * @param string $dismiss_time - dismiss time
	 *
	 * @return mixed
	 */
	abstract protected function check_notice_dismissed($dismiss_time);
}
