<?php

namespace ACP;

use AC;
use AC\Asset\Location;
use AC\Capabilities;
use AC\ListScreenTypes;
use AC\Request;
use AC\Type\Url;
use ACP\Admin;
use ACP\Migrate;
use ACP\Plugin;
use ACP\Plugin\NetworkUpdate;
use ACP\Plugin\Updater;
use ACP\Search;
use ACP\Settings;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use ACP\Storage\ListScreen\LegacyCollectionDecoderAggregate;
use ACP\ThirdParty;
use ACP\Updates\AddonInstaller;

/**
 * The Admin Columns Pro plugin class
 * @since 1.0
 */
final class AdminColumnsPro extends AC\Plugin {

	/**
	 * @var AC\Admin
	 */
	private $network_admin;

	/**
	 * @var API
	 */
	private $api;

	/**
	 * @since 3.8
	 */
	private static $instance = null;

	/**
	 * @return AdminColumnsPro
	 * @since 3.8
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->api = new API();
		$this->api
			->set_url( Url\Site::URL )
			->set_proxy( 'https://api.admincolumns.com' )
			->set_request_meta( [
				'php_version' => PHP_VERSION,
				'acp_version' => $this->get_version(),
			] );

		$storage = AC()->get_storage();
		$list_screen_types = ListScreenTypes::instance();
		$list_screen_encoder = new Encoder( $this->get_version() );
		$list_screen_decoder_factory = new DecoderFactory( $list_screen_types );

		$legacy_collection_decoder = new LegacyCollectionDecoderAggregate( [
			new LegacyCollectionDecoder\Version332( $list_screen_types ),
			new LegacyCollectionDecoder\Version384( $list_screen_types ),
			new LegacyCollectionDecoder\Version400( $list_screen_types ),
		] );

		$license_key_repository = new LicenseKeyRepository( $this->is_network_active() );
		$license_repository = new LicenseRepository( $this->is_network_active() );

		$location = $this->get_asset_location();
		$site_url = new Type\SiteUrl( $this->is_network_active() ? network_site_url() : site_url(), $this->is_network_active() );

		$admin = ( new AdminFactory( AC()->admin(), $location, $storage, $license_repository, $license_key_repository, $site_url, $this->is_network_active() ) )->create();

		$list_screen_order = new AC\Storage\ListScreenOrder();

		$plugins = $this->get_installed_plugins();

		$services = [
			new Admin\Settings( $storage, $location ),
			new QuickAdd\Addon( $storage, $location, new Request() ),
			new Sorting\Addon( $storage, $location, $admin ),
			new Editing\Addon( $storage, $location, new Request() ),
			new Export\Addon( $location ),
			new Search\Addon( $storage, $location ),
			new Filtering\Addon( $storage, $location, new Request() ),
			new ThirdParty\ACF\Addon(),
			new ThirdParty\bbPress\Addon(),
			new ThirdParty\WooCommerce\Addon(),
			new ThirdParty\YoastSeo\Addon(),
			new Table\Switcher( $storage, $location ),
			new Table\HorizontalScrolling( $storage, $location ),
			new Table\HideSearch(),
			new Table\HideBulkActions(),
			new Table\HideFilters(),
			new ListScreens(),
			new Localize( $this->get_dir() ),
			new NativeTaxonomies(),
			new IconPicker(),
			new TermQueryInformation(),
			new Migrate\Export\Request( $storage, new Migrate\Export\ResponseFactory( $list_screen_encoder ) ),
			new Migrate\Import\Request( $storage, $list_screen_decoder_factory, $legacy_collection_decoder ),
			new Controller\AjaxRequestListScreenUsers(),
			new Controller\AjaxRequestListScreenOrder( $list_screen_order ),
			new Controller\AjaxRequestFeedback( $this->get_version() ),
			new Controller\ListScreenCreate( $storage, new Request(), $list_screen_order ),
			new Controller\License( $this->api, $license_repository, $license_key_repository, $site_url, $plugins ),
			new Updates( $this->api, $license_key_repository, $site_url, $plugins ),
			new AddonInstaller( $this->api, $license_repository, $license_key_repository, $site_url ),
			new Check\Activation( $this->get_basename(), $license_repository, $license_key_repository ),
			new PluginActionLinks( $this->get_basename() ),
			new Check\Expired( $license_repository, $license_key_repository, $this->get_basename(), $site_url ),
			new Check\Renewal( $license_repository, $license_key_repository, $this->get_basename(), $site_url ),
		];

		$services[] = new Service\Storage(
			$storage,
			new ListScreenRepository\FileFactory( $list_screen_encoder, $list_screen_decoder_factory ),
			new AC\EncodedListScreenDataFactory(),
			$legacy_collection_decoder
		);

		if ( $this->is_beta() ) {
			$services[] = new Check\Beta( new Admin\Feedback( $location ) );
		}

		foreach ( $services as $service ) {
			if ( $service instanceof AC\Registrable ) {
				$service->register();
			}
		}

		$this->set_installer( new Plugin\Installer() );

		add_action( 'init', [ $this, 'install' ], 1000 );
		add_action( 'init', [ $this, 'install_network' ], 1000 );
		add_action( 'ac/table_scripts', [ $this, 'table_scripts' ] );
		add_filter( 'ac/view/templates', [ $this, 'templates' ] );
		add_filter( 'ac/show_banner', '__return_false' );

		// Register Network Admin
		$this->network_admin = ( new AdminNetworkFactory( $location, $admin->get_location(), $storage, $license_repository, $license_key_repository, $site_url, $this ) )->create();
		$this->network_admin->register();
	}

	/**
	 * @return Plugins
	 */
	private function get_installed_plugins() {
		$plugins = [
			new AC\PluginInformation( $this->get_basename() ),
		];

		$addons = new AC\Integrations();

		foreach ( $addons->all() as $addon ) {
			$plugin = new AC\PluginInformation( $addon->get_basename() );

			if ( $plugin->is_installed() ) {
				$plugins[] = $plugin;
			}
		}

		return new Plugins( $plugins );
	}

	/**
	 * @return Location\Absolute
	 */
	private function get_asset_location() {
		return new Location\Absolute(
			$this->get_url(),
			$this->get_dir()
		);
	}

	public function install_network() {
		if ( ! current_user_can( Capabilities::MANAGE ) || ! is_network_admin() ) {
			return;
		}

		$updater = new Updater\Network( $this->get_version() );

		$updater->add_update( new NetworkUpdate\V5000( $updater->get_stored_version() ) )
		        ->parse_updates();
	}

	/**
	 * @return API
	 */
	public function get_api() {
		return $this->api;
	}

	/**
	 * @return string
	 */
	protected function get_file() {
		return ACP_FILE;
	}

	/**
	 * @return string
	 */
	protected function get_version_key() {
		return 'acp_version';
	}

	/**
	 * @since 4.0
	 */
	public function network_admin() {
		return $this->network_admin;
	}

	/**
	 * @return void
	 */
	public function table_scripts() {
		$assets = [
			new AC\Asset\Style( 'acp-table', $this->get_asset_location()->with_suffix( 'assets/core/css/table.css' ) ),
			new AC\Asset\Script( 'acp-table', $this->get_asset_location()->with_suffix( 'assets/core/js/table.js' ) ),
		];

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

	/**
	 * @param array $templates
	 *
	 * @return array
	 */
	public function templates( $templates ) {
		$templates[] = $this->get_dir() . 'templates';

		return $templates;
	}

	/**
	 * @since      4.0
	 * @deprecated 4.5
	 */
	public function editing() {
		_deprecated_function( __METHOD__, '4.5' );
	}

	/**
	 * @deprecated 4.5
	 * @since      4.0
	 */
	public function filtering() {
		_deprecated_function( __METHOD__, '4.5' );
	}

	/**
	 * @since      4.0
	 * @deprecated 4.5
	 */
	public function sorting() {
		_deprecated_function( __METHOD__, '4.5' );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @since      4.0
	 * @deprecated 5.0.0
	 */
	public function layouts( AC\ListScreen $list_screen ) {
		_deprecated_function( __METHOD__, '5.0.0' );
	}

}