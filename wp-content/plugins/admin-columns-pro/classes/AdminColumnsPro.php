<?php

namespace ACP;

use AC;
use AC\Admin\AdminNetwork;
use AC\Admin\AdminScripts;
use AC\Admin\PageNetworkRequestHandler;
use AC\Admin\PageNetworkRequestHandlers;
use AC\Admin\PageRequestHandler;
use AC\Admin\PageRequestHandlers;
use AC\ColumnSize;
use AC\DefaultColumnsRepository;
use AC\IntegrationRepository;
use AC\ListScreenTypes;
use AC\Plugin\Version;
use AC\PluginInformation;
use AC\Registrable;
use AC\Request;
use AC\Storage\ListScreenOrder;
use AC\Storage\NetworkOptionFactory;
use AC\Storage\OptionFactory;
use AC\Table\ScreenTools;
use AC\Type\Url;
use ACP\Access\ActivationKeyStorage;
use ACP\Access\ActivationStorage;
use ACP\Access\ActivationUpdater;
use ACP\Access\PermissionChecker;
use ACP\Access\PermissionsStorage;
use ACP\Access\Rule\LocalServer;
use ACP\Admin;
use ACP\Admin\MenuFactory;
use ACP\Admin\PageFactory;
use ACP\Bookmark;
use ACP\Bookmark\SegmentRepository;
use ACP\Export;
use ACP\Migrate;
use ACP\Plugin\SetupFactory;
use ACP\Search;
use ACP\Service;
use ACP\Settings;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use ACP\Storage\ListScreen\LegacyCollectionDecoderAggregate;
use ACP\Table\Scripts;
use ACP\ThirdParty;
use ACP\Transient\UpdateCheckTransient;
use ACP\Updates\PeriodicUpdateCheck;
use ACP\Updates\PluginDataUpdater;

final class AdminColumnsPro extends AC\Plugin {

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @var API
	 */
	private $api;

	protected function __construct() {
		parent::__construct( ACP_FILE, new Version( ACP_VERSION ) );

		$basename = $this->get_basename();
		$plugin_information = new PluginInformation( $basename );
		$is_network_active = $plugin_information->is_network_active();
		$version = $this->get_version();

		$meta = [
			'php_version' => PHP_VERSION,
			'acp_version' => $version->get_value(),
			'is_network'  => $is_network_active,
		];

		if ( isset( $_SERVER['REMOTE_ADDR'] ) && $_SERVER['REMOTE_ADDR'] ) {
			$meta['ip'] = $_SERVER['REMOTE_ADDR'];
		}

		$this->api = new API();
		$this->api
			->set_url( Url\Site::URL )
			->set_proxy( 'https://api.admincolumns.com' )
			->set_request_meta( $meta );

		$option_factory = $is_network_active
			? new NetworkOptionFactory()
			: new OptionFactory();

		$site_url = new Type\SiteUrl( $is_network_active ? network_site_url() : site_url() );

		$license_key_storage = new LicenseKeyRepository( $option_factory );
		$activation_key_storage = new ActivationKeyStorage( $option_factory );
		$activation_token_factory = new ActivationTokenFactory( $activation_key_storage, $license_key_storage );
		$activation_storage = new ActivationStorage( $option_factory );

		$storage = AC()->get_storage();
		$list_screen_types = ListScreenTypes::instance();
		$list_screen_encoder = new Encoder( $version );
		$list_screen_decoder_factory = new DecoderFactory( $list_screen_types );

		$legacy_collection_decoder = new LegacyCollectionDecoderAggregate( [
			new LegacyCollectionDecoder\Version332( $list_screen_types ),
			new LegacyCollectionDecoder\Version384( $list_screen_types ),
			new LegacyCollectionDecoder\Version400( $list_screen_types ),
		] );

		$location = $this->get_location();
		$location_core = AC()->get_location();

		$integration_repository = new IntegrationRepository();
		$plugin_repository = new PluginRepository( $basename, $integration_repository );
		$permission_storage = new PermissionsStorage( $option_factory );
		$default_column_repository = new DefaultColumnsRepository();

		$menu_factory = new MenuFactory( admin_url( 'options-general.php' ), $location_core, $activation_token_factory, $integration_repository );

		$page_handler = new PageRequestHandler();
		$page_handler->add( 'columns', new PageFactory\Columns( $location_core, $storage, $default_column_repository, $menu_factory ) )
		             ->add( 'settings', new PageFactory\Settings( $location_core, $menu_factory ) )
		             ->add( 'addons', new PageFactory\Addons( $location_core, $integration_repository, $permission_storage, $menu_factory ) )
		             ->add( 'import-export', new PageFactory\Tools( $location, $storage, $menu_factory ) )
		             ->add( 'license', new PageFactory\License( $location, $menu_factory, $site_url, $activation_token_factory, $activation_storage, $permission_storage, $license_key_storage, $plugin_repository, $is_network_active ) )
		             ->add( 'help', new AC\Admin\PageFactory\Help( $location, $menu_factory ) );

		PageRequestHandlers::add_handler( $page_handler );

		$network_menu_factory = new Admin\MenuNetworkFactory( network_admin_url( 'settings.php' ), $location_core, $activation_token_factory, $integration_repository );

		$page_network_handler = new PageNetworkRequestHandler();
		$page_network_handler->add( 'columns', new Admin\NetworkPageFactory\Columns( $location_core, $default_column_repository, $storage, $network_menu_factory ) )
		                     ->add( 'import-export', new Admin\NetworkPageFactory\Tools( $location, $storage, $network_menu_factory ) )
		                     ->add( 'addons', new PageFactory\Addons( $location_core, $integration_repository, $permission_storage, $network_menu_factory ) )
		                     ->add( 'license', new PageFactory\License( $location, $network_menu_factory, $site_url, $activation_token_factory, $activation_storage, $permission_storage, $license_key_storage, $plugin_repository, $is_network_active ) );

		PageNetworkRequestHandlers::add_handler( $page_network_handler );

		$request = new Request();
		$segment_repository = new SegmentRepository();

		$permission_checker = new PermissionChecker( $permission_storage );
		$permission_checker->add_rule( new LocalServer() );

		$plugin_data_storage = new Storage\PluginsData();
		$plugin_data_updater = new PluginDataUpdater( $this->api, $site_url, $plugin_data_storage );
		$activation_updater = new ActivationUpdater( $activation_key_storage, $activation_storage, $license_key_storage, $this->api, $site_url, $plugin_repository, $permission_checker );

		$column_size_user_storage = new ColumnSize\UserStorage( new ColumnSize\UserPreference( get_current_user_id() ) );
		$column_size_list_storage = new ColumnSize\ListStorage( $storage );

		$request_ajax_handlers = new RequestAjaxHandlers();
		$request_ajax_handlers->add( 'acp-ajax-install-addon', new RequestHandler\Ajax\AddonInstaller( $this->api, $site_url, $activation_storage, $activation_token_factory, $integration_repository, $is_network_active ) )
		                      ->add( 'acp-ajax-activate', new RequestHandler\Ajax\LicenseActivate( $activation_key_storage, $this->api, $site_url, $plugin_data_updater, $activation_updater, $permission_checker ) )
		                      ->add( 'acp-daily-subscription-update', new RequestHandler\Ajax\SubscriptionUpdate( $activation_storage, $activation_key_storage, $license_key_storage, $permission_checker, $this->api, $site_url, $activation_token_factory, $plugin_repository ) )
		                      ->add( 'acp-update-plugins-check', new RequestHandler\Ajax\UpdatePlugins( $activation_token_factory, $plugin_data_updater, new UpdateCheckTransient() ) )
		                      ->add( 'acp-layout-get-users', new RequestHandler\Ajax\ListScreenUsers() )
		                      ->add( 'acp-update-layout-order', new RequestHandler\Ajax\ListScreenOrder( new ListScreenOrder() ) )
		                      ->add( 'acp-send-feedback', new RequestHandler\Ajax\Feedback( $version ) )
		                      ->add( 'acp-permalinks', new RequestHandler\Ajax\Permalinks() )
		                      ->add( 'acp-user-column-width', new RequestHandler\Ajax\ColumnWidthUser( $column_size_user_storage ) )
		                      ->add( 'acp-user-column-width-reset', new RequestHandler\Ajax\ColumnWidthUserReset( $column_size_user_storage ) )
		                      ->add( 'acp-user-column-width-reset-all', new RequestHandler\Ajax\ColumnWidthUserResetAll( $column_size_user_storage ) )
		                      ->add( 'acp-list-column-width', new RequestHandler\Ajax\ColumnWidthList( $column_size_list_storage, $column_size_user_storage ) );

		$request_handler_factory = new RequestHandlerFactory( new Request() );
		$request_handler_factory->add( 'acp-license-activate', new RequestHandler\LicenseActivate( $activation_key_storage, $this->api, $site_url, $plugin_data_updater, $activation_updater, $permission_checker ) )
		                        ->add( 'acp-license-deactivate', new RequestHandler\LicenseDeactivate( $license_key_storage, $activation_key_storage, $activation_storage, $this->api, $site_url, $activation_token_factory, $plugin_data_updater, $permission_checker ) )
		                        ->add( 'acp-license-update', new RequestHandler\LicenseUpdate( $activation_token_factory, $activation_updater ) )
		                        ->add( 'acp-force-plugin-updates', new RequestHandler\ForcePluginUpdates( $plugin_data_updater, $activation_token_factory ) )
		                        ->add( 'create-layout', new RequestHandler\ListScreenCreate( $storage, new ListScreenOrder() ) )
		                        ->add( 'delete-layout', new RequestHandler\ListScreenDelete( $storage ) );

		$services = [
			new Admin\Settings( $storage, $location, $segment_repository ),
			new QuickAdd\Addon( $storage, $location, $request ),
			new Sorting\Addon( $storage, $location, $segment_repository ),
			new Editing\Addon( $storage, $location, $request ),
			new Export\Addon( $location, $storage ),
			new Bookmark\Addon( $storage, $request, $segment_repository ),
			new Search\Addon( $storage, $location, $segment_repository ),
			new Filtering\Addon( $storage, $location, $request ),
			new ThirdParty\ACF\Addon(),
			new ThirdParty\BeaverBuilder\Addon(),
			new ThirdParty\bbPress\Addon(),
			new ThirdParty\Polylang\Addon(),
			new ThirdParty\WooCommerce\Addon(),
			new ThirdParty\YoastSeo\Addon(),
			new Table\Switcher( $storage ),
			new Table\HorizontalScrolling( $storage, $location ),
			new Table\StickyTableRow( $storage ),
			new Table\HideElements(),
			new ListScreens(),
			new Scripts( $location, $column_size_user_storage, $column_size_list_storage ),
			new Localize( $this->get_dir() ),
			new NativeTaxonomies(),
			new IconPicker(),
			new TermQueryInformation(),
			new Migrate\Export\Request( $storage, new Migrate\Export\ResponseFactory( $list_screen_encoder ) ),
			new Migrate\Import\Request( $storage, $list_screen_decoder_factory, $legacy_collection_decoder ),
			new RequestParser( $request_handler_factory ),
			new RequestAjaxParser( $request_ajax_handlers ),
			new Service\ForcePluginUpdate( $activation_token_factory, $plugin_data_updater ),
			new Service\PluginUpdater( $this->api, $plugin_repository, $plugin_data_storage ),
			new PeriodicUpdateCheck( $location, new UpdateCheckTransient() ),
			new PluginActionLinks( $basename, $permission_storage ),
			new Check\Activation( $basename, $activation_token_factory, $activation_storage, $permission_storage, $is_network_active ),
			new Check\Expired( $basename, $activation_token_factory, $activation_storage, $site_url ),
			new Check\Renewal( $basename, $activation_token_factory, $activation_storage, $site_url ),
			new Check\LockedSettings( $basename, $permission_storage, $is_network_active ),
			new Check\RecommendedAddons( $integration_repository ),
			new Admin\Scripts( $location, $permission_storage, $is_network_active ),
			new Service\Templates( $this->get_dir() ),
			new Service\Banner(),
			new ScreenTools(),
		];

		if ( $is_network_active ) {
			$services[] = new AdminNetwork( new PageNetworkRequestHandlers(), $location_core, new AdminScripts( $location_core ) );
		}

		$setup_factory = new SetupFactory( 'acp_version', $this->get_version() );

		$services[] = new AC\Service\Setup( $setup_factory->create( AC\Plugin\SetupFactory::SITE ) );

		if ( $is_network_active ) {
			$services[] = new AC\Service\Setup( $setup_factory->create( AC\Plugin\SetupFactory::NETWORK ) );
		}

		$services[] = new Service\Storage(
			$storage,
			new ListScreenRepository\FileFactory( $list_screen_encoder, $list_screen_decoder_factory ),
			new AC\EncodedListScreenDataFactory(),
			$legacy_collection_decoder
		);

		$services[] = new Service\Permissions( $permission_storage, $permission_checker );

		if ( $version->is_beta() ) {
			$services[] = new Check\Beta( new Admin\Feedback( $location ) );
		}

		array_map( static function ( Registrable $service ) {
			$service->register();
		}, $services );
	}

	/**
	 * @return AdminColumnsPro
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return API
	 */
	public function get_api() {
		return $this->api;
	}

	/**
	 * For backwards compatibility with the `Depedencies` class
	 *
	 * @param string
	 *
	 * @return bool
	 */
	public function is_version_gte( $version ) {
		return $this->get_version()->is_gte( new Version( (string) $version ) );
	}

	/**
	 * @return bool
	 * @deprecated 5.7
	 */
	public function is_network_active() {
		_deprecated_function( __METHOD__, '5.7' );

		return ( new PluginInformation( $this->get_basename() ) )->is_network_active();
	}

	/**
	 * @since      4.0
	 * @deprecated 5.5.2
	 */
	public function network_admin() {
		_deprecated_function( __METHOD__, '5.5.2' );
	}

	/**
	 * @since      4.0
	 * @deprecated 5.0.0
	 */
	public function layouts() {
		_deprecated_function( __METHOD__, '5.0.0' );
	}

}