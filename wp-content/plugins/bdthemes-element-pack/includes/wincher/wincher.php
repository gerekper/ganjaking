<?php

namespace ElementPack\Wincher;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wincher {

	/**
	 * @var WincherOAuthClient
	 */
	protected $client;


	public function __construct() {
		define( 'BDT_EP_WINCHER_PATH', BDTEP_INC_PATH . 'wincher/' );
		define( 'BDT_EP_WINCHER_URL', BDTEP_URL . 'includes/wincher/' );

		add_action( 'bdt_wincher_seo_performance', [ $this, 'display_page' ], 99 );


		require_once BDT_EP_WINCHER_PATH . 'vendor/autoload.php';

		require_once BDT_EP_WINCHER_PATH . 'plugin.php';
		require_once BDT_EP_WINCHER_PATH . 'WincherOAuthClient.php';


		new \ElementPack\Wincher\Plugin();
		$this->client = new \ElementPack\Wincher\WincherOAuthClient();

		/**
		 * @var bool
		 * true if the client has valid tokens, false otherwise
		 */
		$tokenStatus = $this->client->hasValidTokens();
		// var_dump( $tokenStatus );

		/**
		 * Refresh the tokens if they are invalid.
		 */
		if ( false === $tokenStatus ) {
			$this->client->getTokens();
		}
	}

	public function display_page() {
		require_once BDTEP_INC_PATH . 'wincher/views/dashboard.php';
	}
}


if ( ! function_exists( 'element_pack_wincher' ) ) {
	function element_pack_wincher() {
		new Wincher();
	}
	element_pack_wincher();
}