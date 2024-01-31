<?php

namespace ElementPack\Wincher;

use WP_REST_Server;

/**
 * The Plugin class.
 */
class Plugin {
	/**
	 * @var string
	 */
	public const SLUG = 'ep-wincher';

	/**
	 * @var string
	 */
	public const VERSION = '1.0.0';

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var WincherOAuthClient
	 */
	protected $client;

	/**
	 * Plugin constructor.
	 */
	public function __construct() {

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'element_pack_options' ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueGlobalAssets' ] );
			add_action( 'admin_init', [ $this, 'enqueue_style' ] );
		}

		add_action( 'rest_api_init', [ $this, 'registerApiRoutes' ], 9999 );

		$this->client = new WincherOAuthClient();

		if ( ! $this->client->hasValidTokens() ) {
			// add_action( 'pre_current_active_plugins', [ $this, 'showActivateButton' ] );
		}
	}

	/**
	 * Gets the client instance.
	 *
	 * @return WincherOAuthClient the client instance
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * Fired when the plugin activates.
	 *
	 * @return void
	 */
	public function activate() {
	}

	/**
	 * Fires when the plugin deactivates.
	 *
	 * @return void
	 */
	public function deactivate() {
		delete_option( WincherOAuthClient::TOKEN_OPTION );
	}

	/**
	 * Enqueues the global assets.
	 *
	 * @return void
	 */
	public function enqueue_style() {
		wp_enqueue_style( 'ep-wincher', BDT_EP_WINCHER_URL . 'assets/css/wincher.css', [], '1.0.0' );
	}

	public function enqueueGlobalAssets() {

		$default_domain = 'empty';
		$domains        = isset( $this->client->getWebsites()['data'] ) ? $this->client->getWebsites()['data'] : [];
		if ( $domains ) {
			$default_domain = $domains[0]['id'];
		}

		// error_log(print_r($domains, true));

		wp_enqueue_script( 'ep-wincher', BDT_EP_WINCHER_URL . 'assets/js/wincher.min.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ep-wincher', 'EP_WINCHER_CONFIG', [ 
			'apiNonce'    => wp_create_nonce( 'wp_rest' ),
			'apiBaseUrl'  => rest_url( $this->getApiNamespace() ),
			'apiVersion'  => self::VERSION,
			'tokenStatus' => $this->client->hasValidTokens(),
			'domain_id'   => get_option( 'bdt_ep_wincher_domain', $default_domain ),
		] );
	}

	/**
	 * Shows the activate button.
	 *
	 * @return void
	 */
	public function showActivateButton() {
		?>
				<div class="wincher-activate">
					<span>Don't forget to activate Wincher to keep track of your Google rankings!</span>
					<a href="admin.php?page=<?php echo self::SLUG; ?>">Start using Wincher now</a>
				</div>
		<?php
	}

	/**
	 * Registers the API routes.
	 *
	 * @return void
	 */
	public function registerApiRoutes() {
		$routes = [ 
			[ 
				'route'   => 'token',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'AuthController::token',
			],
			[ 
				'route'   => 'authorization-url',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'AuthController::authorization_url',
			],
			[ 
				'route'   => 'status',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'StatusController::get',
			],
			[ 
				'route'   => 'search-engines',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'DashboardController::getSearchEngines',
			],
			[ 
				'route'   => 'dashboard',
				'methods' => WP_REST_Server::CREATABLE,
				'action'  => 'DashboardController::getDashboardData',
			],
			[ 
				'route'   => 'ranking',
				'methods' => WP_REST_Server::CREATABLE,
				'action'  => 'DashboardController::getRankingHistory',
			],
			[ 
				'route'   => 'keywords',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'DashboardController::getKeywords',
			],
			[ 
				'route'   => 'keywords',
				'methods' => WP_REST_Server::CREATABLE,
				'action'  => 'DashboardController::createKeyword',
			],
			[ 
				'route'   => 'keywords',
				'methods' => WP_REST_Server::DELETABLE,
				'action'  => 'DashboardController::deleteKeywords',
			],
			[ 
				'route'   => 'competitors',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'DashboardController::competitorsList',
			],
			[ 
				'route'   => 'website-data',
				'methods' => WP_REST_Server::CREATABLE,
				'action'  => 'DashboardController::websiteData',
			],
			[ 
				'route'   => 'save-domain',
				'methods' => WP_REST_Server::CREATABLE,
				'action'  => 'DashboardController::saveDomain',
			],
			[ 
				'route'   => 'competitors-ranking-summaries',
				'methods' => WP_REST_Server::READABLE,
				'action'  => 'DashboardController::competitorsRankingSummaries',
			]
		];

		$namespace = $this->getApiNamespace();

		foreach ( $routes as $opts ) {
			list( $class, $callbackMethod ) = explode( '::', $opts['action'] );
			$class                        = '\\ElementPack\\Wincher\\Controller\\' . $class;
			$controller                   = new $class( $this->getClient() );

			register_rest_route( $namespace, $opts['route'], [ 
				'methods'             => $opts['methods'],
				'callback'            => [ $controller, $callbackMethod ],
				// 'permission_callback' => [ $controller, 'hasPermission' ],
				'permission_callback' => '__return_true',
			] );
		}
	}

	/**
	 * Gets the API namespace.
	 *
	 * @return string the API namespace
	 */
	private function getApiNamespace() {
		return self::SLUG . '/v' . self::VERSION;
	}

	/**
	 * Gets the image's base64 code.
	 *
	 * @param string $path the path to the image
	 *
	 * @return string the image's base64 encoded string
	 */
	private function getImageBase64( $path ) {
		$data = base64_encode( file_get_contents( $path ) );

		return 'data:image/svg+xml;base64,' . $data;
	}
}
