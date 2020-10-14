<?php
namespace WPManageNinja\Lead;

class LeadOptIn
{
	private $apiUrl = 'https://wpmanageninja.com/?wpmn_api=product_users';
	private $options;
	private $dismissTime = 2592000; // 30 days

	public function __construct($optionArray) {
		$this->options = $optionArray;
	}

	/**
	 * Check If User already consent. If consented then don't show
	 * Or If user dismissed then check if $this->dismissTime is over.
	 * If within the time then don't show
	 * Otherwise we can show this message
	 * @return bool
	 */
	public function noticeable()
	{
		$optStatus = $this->optStatus();
		if( $optStatus == 'yes' || $optStatus == 'no') {
			return false;
		}
		// check if user dismissed
		$dismissTime = $this->getValue('lead_optin_dismiss');
		if( $dismissTime && ( time() - intval($dismissTime) < $this->dismissTime) ) {
			return false;
		}
		return true;
	}

	public function isAccepted()
	{
		return $this->optStatus() == 'yes';
	}

	public function optStatus()
	{
		return $this->getValue('lead_optin_status');
	}

	private function getValue($key) {
		if (isset($this->options[$key])) {
			return $this->options[$key];
		}
		return false;
	}

	public function getNotice() {
		return 'We made a few tweaks to the plugin, <a href="#">Opt in to make <b>"Ninja Tables" Better!</b></a>';
	}

	public function subscribe() {
		$currentUser = wp_get_current_user();
		$data = array(
			'first_name' => $currentUser->first_name,
			'last_name' => $currentUser->last_name,
			'display_name' => $currentUser->display_name,
			'email' => $currentUser->user_email,
			'site_url' => site_url(),
			'request_from' => $this->get_request_from(),
            'plugins' => $this->getPluginsInfo(),
            'ninja_doing_action' => 'activate'
		);
		wp_remote_post($this->apiUrl, array(
            'method' => 'POST',
            'sslverify' => false,
            'body' => $data
 		));
	}


	// Function to get the client IP address
	public function get_request_from() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}


	private function getPluginsInfo()
    {
        $activePlugins = get_option('active_plugins', array());
        $inActivePlugins = array();
        $all_plugins = get_plugins();
        foreach ($all_plugins as $pluginName => $plugin) {
            if(!in_array($pluginName, $activePlugins)) {
                $inActivePlugins[] = $pluginName;
            }
        }

        return array(
            'actives' => $activePlugins,
            'inactives' => $inActivePlugins
        );
    }
}
