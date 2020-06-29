<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Freshdesk Abstract Integration.
 *
 * Interface to integrations.
 *
 * @package  WC_Freshdesk_Abstract_Integration
 * @category Integration
 * @author   WooThemes
 */
abstract class WC_Freshdesk_Abstract_Integration {

	/**
	 * Integration.
	 *
	 * @since  1.0.0
	 *
	 * @param string $url     Freshdesk URL.
	 * @param string $api_key API Key.
	 * @param string $debug   Debug mode.
	 */
    public function __construct( $url, $api_key, $debug ) {
        $this->id          = WC_Freshdesk::get_integration_id();
        $this->log         = WC_Freshdesk::get_logger();
        $this->url         = $url;
        $this->api_key     = $api_key;
        $this->debug       = $debug;
    }
}
