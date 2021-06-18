<?php

namespace ACP;

use AC;
use AC\Admin\AdminNetwork;
use AC\Admin\Main\Columns;
use AC\Admin\NetworkRequestHandler;
use AC\Admin\PageFactory;
use AC\Admin\PageRequestHandler;
use AC\Admin\RequestHandler;
use AC\Admin\WpMenuFactory;
use AC\Asset\Location;
use AC\Asset\Location\Absolute;
use AC\Capabilities;
use AC\ListScreenTypes;
use AC\PluginInformation;
use AC\Request;
use AC\Type\Url;
use ACP\Admin;
use ACP\Admin\MainFactory;
use ACP\Admin\MenuFactory;
use ACP\Bookmark;
use ACP\Migrate;
use ACP\Plugin;
use ACP\Plugin\NetworkUpdate;
use ACP\Plugin\Updater;
use ACP\Search;
use ACP\Settings;
use ACP\Settings\ListScreen\HideOnScreen;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\NativeSortableFactory;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use ACP\Storage\ListScreen\LegacyCollectionDecoderAggregate;
use ACP\ThirdParty;
use ACP\Transient\LicenseCheckTransient;
use ACP\Updates\AddonInstaller;

/**
 * The Admin Columns Pro plugin class
 */
final class AdminColumnsPro extends AC\Plugin {

	/**
	 * @var API
	 */
	private $api;

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @return AdminColumnsPro
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

		$location_core = new Absolute(
			AC()->get_url(),
			AC()->get_dir()
		);

		RequestHandler::add_handler(
			new PageRequestHandler(
				new PageFactory(
					new MenuFactory( admin_url( 'options-general.php' ) ),
					new MainFactory( $storage, $location, $site_url, new PluginInformation( $this->get_basename() ), new AC\Admin\MainFactory( $storage, $location_core ) )
				),
				Columns::NAME
			)
		);

		$plugins = $this->get_installed_plugins();

		$list_screen_order = new AC\Storage\ListScreenOrder();
		$segment_repository = new Bookmark\SegmentRepository();
		$request = new Request();

		$services = [
			new Admin\Settings( $storage, $location, $segment_repository ),
			new QuickAdd\Addon( $storage, $location, $request ),
			new Sorting\Addon( $storage, $location, new NativeSortableFactory(), new ModelFactory(), $segment_repository ),
			new Editing\Addon( $storage, $location, $request ),
			new Export\Addon( $location ),
			new Bookmark\Addon( $storage, $request, $segment_repository ),
			new Search\Addon( $storage, $location, $segment_repository ),
			new Filtering\Addon( $storage, $location, $request ),
			new ThirdParty\ACF\Addon(),
			new ThirdParty\bbPress\Addon(),
			new ThirdParty\WooCommerce\Addon(),
			new ThirdParty\YoastSeo\Addon(),
			new Table\Switcher( $storage ),
			new Table\HorizontalScrolling( $storage, $location ),
			new Table\StickyTableRow( $storage ),
			new Table\HideSearch(),
			new Table\HideSubMenu( new HideOnScreen\SubMenu\CommentStatus() ),
			new Table\HideSubMenu( new HideOnScreen\SubMenu\PostStatus() ),
			new Table\HideSubMenu( new HideOnScreen\SubMenu\Roles() ),
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
			new Controller\AjaxRequestSubscriptionUpdate( new LicenseCheckTransient(), $license_key_repository, $license_repository, $this->api, $site_url, $location ),
			new Controller\ListScreenCreate( $storage, $request, $list_screen_order ),
			new RequestParser( $this->api, $license_repository, $license_key_repository, $site_url, $plugins ),
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

		NetworkRequestHandler::add_handler(
			new PageRequestHandler(
				new PageFactory( new MenuFactory( network_admin_url( 'settings.php' ) ), new Admin\NetworkMainFactory( $storage, $location_core, $location, $site_url ) ),
				Columns::NAME
			)
		);

		if ( $this->is_network_active() ) {
			$services[] = new AdminNetwork( new NetworkRequestHandler(), new WpMenuFactory(), new AC\Admin\AdminScripts( $location_core ) );
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
	}

	/**
	 * @return Plugins
	 */
	private function get_installed_plugins() {
		$plugins = [
			new PluginInformation( $this->get_basename() ),
		];

		$addons = new AC\Integrations();

		foreach ( $addons->all() as $addon ) {
			$plugin = new PluginInformation( $addon->get_basename() );

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
	 * @since 4.0
	 */
	public function network_admin() {
		_deprecated_function( __METHOD__, '5.5.2' );
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
	 * @since      4.0
	 * @deprecated 5.0.0
	 */
	public function layouts() {
		_deprecated_function( __METHOD__, '5.0.0' );
	}

}