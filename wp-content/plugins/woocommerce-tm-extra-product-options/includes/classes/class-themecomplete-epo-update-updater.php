<?php
/**
 * Extra Product Options Update
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 * phpcs:disable Generic.Files.OneObjectStructurePerFile
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Updater class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_UPDATE_Updater {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_UPDATE_Updater|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * The plugin title
	 *
	 * @var string
	 */
	public $title = 'Extra Product Options & Add-Ons for WooCommerce';

	/**
	 * The api URL
	 *
	 * @var string
	 */
	public $version_url = 'https://themecomplete.com/api/?';

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @var THEMECOMPLETE_EPO_UPDATE_Updater|null
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->setup();
		add_filter( 'upgrader_pre_download', [ $this, 'upgrade_filter_from_envato' ], 10, 4 );
		add_action( 'upgrader_process_complete', [ $this, 'remove_temporary_dir' ] );
	}

	/**
	 * Setup the manager class
	 *
	 * @since 1.0
	 * @static
	 */
	public function setup() {
		$instance = new THEMECOMPLETE_EPO_UPDATE_Manager( THEMECOMPLETE_EPO_VERSION, $this->get_url(), THEMECOMPLETE_EPO_PLUGIN_SLUG, $this );
	}

	/**
	 * Get url with timestamp
	 *
	 * @since 1.0
	 * @static
	 */
	public function get_url() {
		return $this->version_url . time();
	}

	/**
	 * Pre-download functions
	 *
	 * @param boolean     $reply Whether to bail without returning the package. Default false.
	 * @param string      $package The package file name.
	 * @param WP_Upgrader $updater The WP_Upgrader instance.
	 * @since 1.0
	 * @static
	 */
	public function upgrade_filter_from_envato( $reply, $package, $updater ) {

		if ( ( isset( $updater->skin->plugin ) && THEMECOMPLETE_EPO_PLUGIN_SLUG === $updater->skin->plugin ) ||
			( isset( $updater->skin->plugin_info ) && isset( $updater->skin->plugin_info['Name'] ) && $updater->skin->plugin_info['Name'] === $this->title )
		) {
			$updater->strings['download_envato'] = esc_html__( 'Downloading package from Envato market...', 'woocommerce-tm-extra-product-options' );
			$updater->skin->feedback( 'download_envato' );
			$package_filename = 'woocommerce-tm-extra-product-options.zip';
			$res              = $updater->fs_connect( [ WP_CONTENT_DIR ] );
			if ( ! $res ) {
				return new WP_Error( 'no_credentials', esc_html__( "Error! Can't connect to filesystem", 'woocommerce-tm-extra-product-options' ) );
			}
			$envato_token  = get_option( 'tm_epo_envato_apikey' );
			$purchase_code = get_option( 'tm_epo_envato_purchasecode' );

			if ( ! THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
				return new WP_Error( 'no_credentials', esc_html__( 'To receive automatic updates license activation is required.', 'woocommerce-tm-extra-product-options' ) . sprintf( '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID ) ) . '">%s</a>', esc_html__( 'Please activate WooCommerce Extra Product Options.', 'woocommerce-tm-extra-product-options' ) ) );
			}

			$result = $this->envato_download_purchase_url( $envato_token, $purchase_code );

			$wordpress_plugin = isset( $result->wordpress_plugin );
			$download_url     = isset( $result->download_url );

			if ( ! $wordpress_plugin ) {
				if ( ! $download_url ) {
					return new WP_Error( 'no_credentials', esc_html__( 'Error! Envato API error', 'woocommerce-tm-extra-product-options' ) . ( isset( $result->error ) && isset( $result->description ) ? ': ' . $result->description : '.' ) );
				}
			}

			if ( $wordpress_plugin ) {
				$download_file = download_url( $result->wordpress_plugin );
			} else {
				$download_file = download_url( $result->download_url );
			}

			if ( is_wp_error( $download_file ) ) {
				return $download_file;
			}

			if ( $wordpress_plugin ) {
				$plugin_directory_name = 'woocommerce-tm-extra-product-options';
				if ( basename( $download_file, '.zip' ) !== $plugin_directory_name ) {
					$new_archive_name = dirname( $download_file ) . '/' . $plugin_directory_name . time() . '.zip';
					if ( rename( $download_file, $new_archive_name ) ) {
						$download_file = $new_archive_name;
					}
				}

				return $download_file;
			} else {
				global $wp_filesystem;
				$upgrade_folder = $wp_filesystem->wp_content_dir() . 'uploads/woocommerce-tm-extra-product-options-envato-package';
				if ( is_dir( $upgrade_folder ) ) {
					$wp_filesystem->delete( $upgrade_folder );
				}
				$result = unzip_file( $download_file, $upgrade_folder );
				if ( $result && is_file( $upgrade_folder . '/' . $package_filename ) ) {
					return $upgrade_folder . '/' . $package_filename;
				}
			}

			return new WP_Error( 'no_credentials', esc_html__( 'Error on unzipping package', 'woocommerce-tm-extra-product-options' ) );
		}

		return $reply;
	}

	/**
	 * Get download url
	 *
	 * @param string $envato_token The Envato token.
	 * @param string $purchase_code The purchase code.
	 * @since 1.0
	 * @static
	 */
	protected function envato_download_purchase_url( $envato_token, $purchase_code ) {

		$download_url = '';

		$request = wp_remote_post(
			$this->get_url(),
			[
				'body' =>
					[
						'api_key'       => $envato_token,
						'purchase_code' => $purchase_code,
						'type'          => 'download',
						'id'            => THEMECOMPLETE_EPO_PLUGIN_ID,
						'action'        => 'download',
					],
			]
		);

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {

			$data = themecomplete_maybe_unserialize( ( $request['body'] ) );
			if ( false === $data && isset( $request['body'] ) ) {
				$data = $request['body'];
			}
			$download_url = json_decode( $data );

		}

		return $download_url;
	}

	/**
	 * Remove temp directory
	 *
	 * @since 1.0
	 * @static
	 */
	public function remove_temporary_dir() {
		global $wp_filesystem;
		if ( is_dir( $wp_filesystem->wp_content_dir() . 'uploads/woocommerce-tm-extra-product-options-envato-package' ) ) {
			$wp_filesystem->delete( $wp_filesystem->wp_content_dir() . 'uploads/woocommerce-tm-extra-product-options-envato-package', true );
		}
	}

}

/**
 * Extra Product Options Update manager class
 *
 * @package Extra Product Options/Classes
 * @version 4.8
 */
final class THEMECOMPLETE_EPO_UPDATE_Manager {

	/**
	 * Plugin version
	 *
	 * @var mixed
	 */
	public $current_version;

	/**
	 * The API url
	 *
	 * @var mixed
	 */
	public $update_path;

	/**
	 * The plugin slug
	 *
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * The main plugin file name without the extension
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * The THEMECOMPLETE_EPO_UPDATE_Updater instance
	 *
	 * @var THEMECOMPLETE_EPO_UPDATE_Updater
	 */
	public $updater_instance;

	/**
	 * Plugin id
	 *
	 * @var mixed
	 */
	public $plugin_envato_id;

	/**
	 * Plugin link
	 *
	 * @var string
	 */
	protected $url = 'https://1.envato.market/3eOy';

	/**
	 * Class Constructor
	 *
	 * @param string                           $current_version Plugin current version.
	 * @param string                           $update_path The API url.
	 * @param string                           $plugin_slug The plugin slug.
	 * @param THEMECOMPLETE_EPO_UPDATE_Updater $instance The THEMECOMPLETE_EPO_UPDATE_Updater instance.
	 * @since 1.0
	 */
	public function __construct( $current_version, $update_path, $plugin_slug, $instance ) {

		$this->updater_instance = $instance;
		$this->plugin_envato_id = THEMECOMPLETE_EPO_PLUGIN_ID;
		$this->current_version  = $current_version;
		$this->plugin_slug      = $plugin_slug;
		$this->update_path      = $update_path;
		$this->slug             = explode( '/', $plugin_slug );
		$this->slug             = str_replace( '.php', '', $this->slug[1] );

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'tm_update_plugins' ] );
		add_filter( 'plugins_api', [ $this, 'tm_plugins_api' ], 10, 3 );
		add_action( 'in_plugin_update_message-' . $this->plugin_slug, [ $this, 'tm_update_message' ] );

	}

	/**
	 * Fill plugin update details when WordPress runs its update checker
	 *
	 * @param string $transient The WordPress update object.
	 * @since 1.0
	 */
	public function tm_update_plugins( $transient ) {

		if ( isset( $transient->response[ $this->plugin_slug ] ) ) {
			return $transient;
		}

		$remote_version = $this->remote_api_call( 'new_version', true );

		if ( $remote_version && version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
			$obj       = $remote_version;
			$obj->slug = $this->slug;

			if ( THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
				$obj->url     = $this->update_path;
				$obj->package = $this->update_path;
			} else {
				$obj->url     = '';
				$obj->package = '';
			}

			$obj->name                                 = $this->updater_instance->title;
			$transient->response[ $this->plugin_slug ] = $obj;
		}

		return $transient;
	}

	/**
	 * Perform a remote api call
	 *
	 * @param string  $action Type of action to perform.
	 * @param boolean $is_serialized If the response is serialized.
	 * @since 1.0
	 */
	public function remote_api_call( $action = '', $is_serialized = false ) {
		$request = wp_remote_post(
			$this->update_path,
			[
				'body' =>
						[
							'action' => $action,
							'id'     => $this->plugin_envato_id,
							'type'   => 'plugin',
						],
			]
		);

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return ( $is_serialized ) ? themecomplete_maybe_unserialize( ( $request['body'] ) ) : $request['body'];
		}

		return false;
	}

	/**
	 * Get plugin data for update
	 *
	 * @param false|object|array $result The result object or array. Default false.
	 * @param string             $action The type of information being requested from the Plugin Installation API.
	 * @param object             $args Plugin API arguments.
	 * @since 1.0
	 */
	public function tm_plugins_api( $result, $action, $args ) {
		if ( isset( $args->slug ) && $args->slug === $this->slug ) {
			$info = $this->remote_api_call( 'info', true );
			if ( $info ) {
				$info->name = $this->updater_instance->title;
				$info->slug = $this->slug;

				if ( THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
					$info->download_link = $this->update_path;
				}
			}

			return $info;
		}

		return $result;
	}

	/**
	 * Print update message
	 *
	 * @since 1.0
	 */
	public function tm_update_message() {
		$plugins = get_plugins();
		if ( ! THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
			echo '<br /><a href="' . esc_url( $this->url ) . '">' . esc_html__( 'Download new version from CodeCanyon', 'woocommerce-tm-extra-product-options' ) . '</a> ' . esc_html__( 'or register the plugin to receive automatic updates.', 'woocommerce-tm-extra-product-options' );
		}
	}

}
