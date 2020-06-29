<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

/**
 * Automatic Updater Class
 *
 * Receive updates for plugins and themes on-the-fly from
 * self-hosted repositories using the WordPress Updates API.
 *
 * @package KM_Updates
 * @since 4.6.3
 * @author John Gera
 * @copyright Copyright (c) 2013  John Gera, George Krupa, and Kreatura Media Kft.
 */


class KM_UpdatesV3 {


	/**
	 * Repository API version
	 */
	const API_VERSION = 3;


	/**
	 * Adds additional one minute caching between WP API calls
	 * to prevent parallel requests of self-hosted repository.
	 */
	const TIMEOUT = 60;


	/**
	 * @var array $config Stores API an plugin details
	 * @access protected
	 */
	protected $config;


	/**
	 * @var object $data Received update data.
	 * @access protected
	 */
	protected $data;




	/**
	 * Init class and set up config for update checking
	 *
	 * @since 4.6.3
	 * @access public
	 * @param array $config Config for setting up auto updates
	 * @return void
	 */
	public function __construct($config = array()) {

		// Get and check params
		extract($config, EXTR_SKIP);
		if(!isset($repoUrl, $root, $version, $itemID, $codeKey, $authKey, $channelKey)) {
			wp_die('Missing params in $config for KM_Updates constructor');
		}

		// Bug fix in v5.3.0: WPLANG is not always defined
		if( ! defined('WPLANG')) { define('WPLANG', ''); }

		// Build config
		$this->config = array_merge( $config, array(

			'slug' 			=> basename( dirname( $config['root'] ) ),
			'base' 			=> plugin_basename( $config['root'] ),
			'channel' 		=> get_option( $config['channelKey'], 'stable' ),
			'license' 		=> get_option( $config['codeKey'], '' ),
			'activation_id' => get_option( $config['activationKey'], '' ),
			'domain' 		=> $_SERVER['SERVER_NAME'],
			'siteurl' 		=> esc_url( site_url() ),
			'option' 		=> strtolower( basename( dirname( $config['root'] ) ) ) . '_update_info',
			'locale' 		=> get_locale()
		));
	}




	/**
	 * Adds self-hosted updates for site transients.
	 *
	 * @since 4.6.3
	 * @access public
	 * @param object $transient WP plugin updates site transient
	 * @return object $transient WP plugin updates site transient
	 */
	public function set_update_transient($transient) {

		$this->_check_updates();

		if( empty($transient) || ! is_object($transient) ) {
			$transient = new stdClass;
		}

		if(!isset($transient->response)) {
			$transient->response = array();
		}


		if(!empty($this->data->basic) && is_object($this->data->basic)) {
			if(version_compare($this->config['version'], $this->data->basic->version, '<')) {

				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[$this->config['base']] = $this->data->basic;
			}
		} else {
			unset($transient->response[$this->config['base']]);
		}

		return $transient;
	}




	/**
	 * Adds self-hosted updates for WP Updates API.
	 *
	 * @since 4.6.3
	 * @access public
	 * @param object $result Result object containing update info
	 * @param string $action WP Updates API action
	 * @param object $args Object containing additional information
	 * @return object $result Result object containing update info
	 */
	public function set_updates_api_results($result, $action, $args) {

		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->config['slug'] && $action == 'plugin_information') {
			if(is_object($this->data->full) && !empty($this->data->full)) {
				$result = $this->data->full;
			}
		}

		return $result;
	}



	/**
	 * Check and handle activation before downloading the update file.
	 *
	 * @since 6.0.0
	 * @access public
	 * @param bool $reply Whether to bail without returning the package. Default false.
	 * @param string $package The package file name.
	 * @param WP_Upgrader $this The WP_Upgrader instance.
	 * @return mixed $result void or WP_error on failure
	 */
	public function pre_download_filter( $reply, $package, $updater ) {

		// We care only about an empty $package as it can
		// indicate an unregistered copy of LayerSlider.
		if( empty( $package ) ) {

			$skin = $updater->skin;

			// Verify if the current update process is indeed dealing
			// with LayerSlider and not another 3rd party item.
			if( ( isset( $skin->plugin ) && $skin->plugin === $this->config['base'] ) ||
				( isset( $skin->plugin_info ) && $skin->plugin_info['Name'] === $this->config['name'] ) ) {

				return new WP_Error('ls_update_error', sprintf(
					__('License activation is required to receive updates. Please read our %sonline documentation%s to learn more.', 'LayerSlider'),
					'<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">',
					'</a>')
				);
			}

		}

		return $reply;
	}



	/**
	 * Provide an update message in the Plugins list row.
	 *
	 * @since 6.1.5
	 * @access public
	 * @param array $plugin_data An array of plugin metadata.
	 * @param array $response An array of metadata about the available plugin update.
	 * @return void
	 */
	public function update_message( $plugin_data, $response ) {

		// Provide license activation warning on non-activated sites
		if( ! LS_Config::isActivatedSite() && empty( $response->package ) ) {
			echo ' ';
			printf(__('License activation is required in order to receive updates for LayerSlider. %sPurchase a license%s or %sread the documentation%s to learn more. %sGot LayerSlider in a theme?%s', 'installer'),
							'<a href="'.LS_Config::get('purchase_url').'" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation-bundles" target="_blank">', '</a>');
		}
	}



	/**
	 *  In case of receiving a "Not activated" flag, make sure to display
	 *	the "Canceled activation" notification to let users know about
	 *	potential issues if their site is still in an activated state.
	 *
	 *	This usually happens due to remote deactivation via our online tools,
	 *	or because users ask us to reset their license key on their behalf.
	 *	Alternatively, the purhcase code might no longer be valid due to a
	 *	refund, sale reversal, or any other reason.
	 *
	 * @since 6.1.5
	 * @access public
	 */
	public function check_activation_state() {

		if( LS_Config::isActivatedSite() ) {

			delete_option( $this->config['codeKey'] );
			delete_option( $this->config['authKey'] );
			delete_option( $this->config['activationKey'] );

			update_option( 'ls-show-canceled_activation_notice', 1 );
			update_option( 'layerslider_cancellation_update_info', $this->data );
		}
	}




	/**
	 * Check for update info.
	 *
	 * @since 4.6.3
	 * @access protected
	 * @param boolean $forceCheck Ignore the update interval and force refreshing update info
	 * @return void
	 */
	protected function _check_updates( $forceCheck = false ) {

		// Get data
		if(empty($this->data)) {
			$data = get_option($this->config['option'], false);
			$data = $data ? $data : new stdClass;
			$this->data = is_object($data) ? $data : maybe_unserialize($data);
		}

		// Just installed
		if(!isset($this->data->checked)) {
			$this->data->checked = time();
		}

		// Check for updates
		if( $forceCheck || $this->data->checked < time() - self::TIMEOUT) {
			$response = $this->sendApiRequest($this->config['repoUrl'].'updates/');

			if(!empty($response) && $newData = maybe_unserialize($response)) {
				if(is_object($newData)) {
					$this->data = $newData;
					$this->data->checked = time();
				}
			}


			// Store version number of the latest release
			// to notify unauthorized site owners
			if( ! empty( $this->data->_latest_version ) ) {
				update_option('ls-latest-version', $this->data->_latest_version);
			}


			// Check activation state on client side in
			// case of receiving a "Not Activated" flag
			if( ! empty( $this->data->_not_activated ) ) {
				$this->check_activation_state();
			}


			// Use activation_id when
			if( empty( $this->config['activation_id'] ) ) {
				if( ! empty( $this->data->activation_id ) ) {
					update_option( $this->config['activationKey'], $this->data->activation_id );
				}
			}

			if( ! empty( $this->data->full->p_url ) ) {
				update_option('ls-p-url', $this->data->full->p_url );
			} else {
				delete_option('ls-p-url');
			}
		}

		// Save results
		update_option($this->config['option'], $this->data);
	}



	/**
	 * Retrieves API method info from self-hosted repository.
	 *
	 * @since 4.6.3
	 * @access protected
	 * @param string $url API URL to be called
	 * @return string API response
	 */
	public function sendApiRequest($url) {

		if(empty($url)) { return false; }

		// Build request
		$request = wp_remote_post($url, array(
			'method' => 'POST',
			'timeout' => 60,
			'user-agent' => 'WordPress/'.$GLOBALS['wp_version'].'; '.get_bloginfo('url'),
			'body' => array(
				'slug' 			=> $this->config['slug'],
				'base' 			=> $this->config['base'],
				'version' 		=> $this->config['version'],
				'channel' 		=> $this->config['channel'],
				'license' 		=> $this->config['license'],
				'activation_id' => $this->config['activation_id'],
				'item_id' 		=> $this->config['itemID'],
				'domain' 		=> $this->config['domain'],
				'siteurl' 		=> $this->config['siteurl'],
				'locale' 		=> $this->config['locale'],
				'api_version' 	=> self::API_VERSION
			)
		));

		return wp_remote_retrieve_body($request);
	}




	/**
	 * Parses JSON API responses
	 *
	 * @since 4.6.3
	 * @access protected
	 * @param string $response JSON string to be parsed
	 * @return array Array of the raw and parsed JSON
	 */
	public function parseApiResponse( $response ) {

		// Get response
		$json = !empty( $response ) ? json_decode( $response ) : false;

		// ERR: Unexpected error
		if( empty( $json ) ) {

			die( json_encode( array(
				'message' => 'An unexpected error occurred. Please try again later. If this error persist, it’s most likely a web server configuration issue. Please contact your web host and ask them to allow external connection to the following domain: repository.kreaturamedia.com. If you need further assistance in resolving this issue, please visit layerslider.kreaturamedia.com/help/',
				'errCode' => 'ERR_UNEXPECTED_ERROR')
			) );
		}

		return array( $response, $json );
	}



	/**
	 * Handles repository authorization and saving auto-update settings
	 *
	 * @since 4.6.3
	 * @access public
	 * @return string JSON string of authorization status data
	 */
	public function handleActivation() {

		// Required informations
		if( empty( $_POST['purchase_code'] ) || empty( $_POST['channel'] ) ) {
			die( json_encode( array(
				'status' => 'Please enter your license key.',
				'errCode' => 'ERR_INVALID_DATA_RECEIVED')
			) );
		}

		// Re-validation
		if(get_option('layerslider-validated', null) === '1' && !empty($this->config['license']) && get_option('layerslider-authorized-site', null) === null) {
			$_POST['purchase_code'] = $this->config['license'];
		}

		// Save release channel
		update_option($this->config['channelKey'], $_POST['channel']);

		// Only update release channel?
		if( LS_Config::isActivatedSite() ) {
			if( strpos($_POST['purchase_code'], '●') === 0 || $this->config['license'] == $_POST['purchase_code']) {
				die(json_encode(array('message' => __('Your settings were successfully saved.', 'LayerSlider'))));
			}
		}

		// Validate purchase
		$this->config['license'] = $_POST['purchase_code'];
		$data = $this->sendApiRequest( $this->config['repoUrl'].'authorize/' );
		list( $response, $json ) = $this->parseApiResponse( $data );

		// Failed authorization
		if( ! empty( $json->errCode ) ) {
			update_option( $this->config['authKey'], 0 );
			update_option( $this->config['codeKey'], '' );
			update_option( $this->config['activationKey'], '' );

		// Successful authorization
		} else {
			$json->code = base64_encode( $_POST['purchase_code'] );
			update_option( $this->config['authKey'], 1 );
			update_option( $this->config['codeKey'], $_POST['purchase_code'] );
			update_option( $this->config['activationKey'], $json->activation_id );


			// v6.1.5: Make sure to empty the stored update data from cache,
			// so we can avoid issues caused by outdated and potentially
			// unreliable information like special flags set by the update server.
			//
			// Force checking updates to immediately replace the missing update info
			// with fresh data. Suppressing error reporting to make sure that nothing
			// can break the JSON output, as user feedback is crucial here.
			delete_option( $this->config['option'] );
			@$this->_check_updates( true );

			// v6.2.0: Automatically hide the "Canceled activation" notice when
			// re-activating the plugin for the sake of clarity and consistency.
			update_option( 'ls-show-canceled_activation_notice', 0 );

			// v6.6.3: Empty slider caches (if any) to immediately hide the premium
			// notice displayed above sliders on the front-end after activation.
			layerslider_delete_caches();
		}


		die( json_encode( $json ) );
	}



	/**
	 * Handles repository deauthorization
	 *
	 * @since 4.6.3
	 * @access public
	 * @return string JSON string of deauthorization status data
	 */
	public function handleDeactivation() {

		// Get response
		$data = $this->sendApiRequest($this->config['repoUrl'].'deauthorize/');
		list($response, $json) = $this->parseApiResponse($data);

		// Deauthorize
		delete_option( $this->config['codeKey'] );
		delete_option( $this->config['authKey'] );
		delete_option( $this->config['activationKey'] );

		// v6.6.3: Empty slider caches (if any) to re-enable displaying the premium
		// notice above sliders on the front-end after deactivation.
		layerslider_delete_caches();

		die($response);
	}
}
