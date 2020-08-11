<?php
/**
 * Extra Product Options Update class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_UPDATE_Updater {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	public $title = 'WooCommerce TM Extra Product Options';
	public $version_url = 'https://themecomplete.com/api/?';

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->setup();
		add_filter( 'upgrader_pre_download', array( $this, 'upgradeFilterFromEnvato' ), 10, 4 );
		add_action( 'upgrader_process_complete', array( $this, 'removeTemporaryDir' ) );
	}

	/**
	 * Setup the manager class
	 *
	 * @since 1.0
	 * @static
	 */
	public function setup() {
		$instance = new THEMECOMPLETE_EPO_UPDATE_Manager ( THEMECOMPLETE_EPO_VERSION, $this->get_url(), THEMECOMPLETE_EPO_PLUGIN_SLUG, $this );
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
	 * @since 1.0
	 * @static
	 */
	public function upgradeFilterFromEnvato( $reply, $package, $updater ) {


		if ( ( isset( $updater->skin->plugin ) && $updater->skin->plugin === THEMECOMPLETE_EPO_PLUGIN_SLUG ) ||
		     ( isset( $updater->skin->plugin_info ) && isset( $updater->skin->plugin_info['Name'] ) && $updater->skin->plugin_info['Name'] === $this->title )
		) {
			$updater->strings['download_envato'] = esc_html__( 'Downloading package from Envato market...', 'woocommerce-tm-extra-product-options' );
			$updater->skin->feedback( 'download_envato' );
			$package_filename = 'woocommerce-tm-extra-product-options.zip';
			$res              = $updater->fs_connect( array( WP_CONTENT_DIR ) );
			if ( ! $res ) {
				return new WP_Error( 'no_credentials', esc_html__( "Error! Can't connect to filesystem", 'woocommerce-tm-extra-product-options' ) );
			}
			$api_key       = get_option( 'tm_epo_envato_apikey' );
			$purchase_code = get_option( 'tm_epo_envato_purchasecode' );

			if ( ! THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
				return new WP_Error( 'no_credentials', esc_html__( "To receive automatic updates license activation is required.", 'woocommerce-tm-extra-product-options' ) . sprintf( '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID ) ) . '">%s</a>', esc_html__( "Please activate WooCommerce Extra Product Options.", 'woocommerce-tm-extra-product-options' ) ) );
			}

			$result = $this->envatoDownloadPurchaseUrl( $api_key, $purchase_code );

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
	 * @since 1.0
	 * @static
	 */
	protected function envatoDownloadPurchaseUrl( $api_key, $purchase_code ) {

		$download_url = '';

		$request = wp_remote_post( $this->get_url(),
			array( 'body' =>
				       array( 'api_key'       => $api_key,
				              'purchase_code' => $purchase_code,
				              'type'          => 'download',
				              'id'            => THEMECOMPLETE_EPO_PLUGIN_ID,
				              'action'        => 'download',
				       )
			) );

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {

			$data         = unserialize( ( $request['body'] ) );
			if ($data === FALSE && isset($request['body'])){
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
	public function removeTemporaryDir() {
		global $wp_filesystem;
		if ( is_dir( $wp_filesystem->wp_content_dir() . 'uploads/woocommerce-tm-extra-product-options-envato-package' ) ) {
			$wp_filesystem->delete( $wp_filesystem->wp_content_dir() . 'uploads/woocommerce-tm-extra-product-options-envato-package', TRUE );
		}
	}

}

/**
 * Extra Product Options Update anager class
 *
 * @package Extra Product Options/Classes
 * @version 4.8
 */
final class THEMECOMPLETE_EPO_UPDATE_Manager {

	public $current_version;
	public $update_path;
	public $plugin_slug;
	public $slug;
	public $TM_Updater_instance;
	public $plugin_envato_id;

	protected $url = 'https://1.envato.market/c/1268006/275988/4415';

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct( $current_version, $update_path, $plugin_slug, $instance ) {

		$this->TM_Updater_instance = $instance;
		$this->plugin_envato_id    = THEMECOMPLETE_EPO_PLUGIN_ID;
		$this->current_version     = $current_version;
		$this->plugin_slug         = $plugin_slug;
		$this->update_path         = $update_path;
		$this->slug                = explode( '/', $plugin_slug );
		$this->slug                = str_replace( '.php', '', $this->slug[1] );

		$plugin_id = "woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php";

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'tm_update_plugins' ) );
		add_filter( 'plugins_api', array( $this, 'tm_plugins_api' ), 10, 3 );
		add_action( 'in_plugin_update_message-' . $this->plugin_slug, array( $this, 'tm_update_message' ) );

	}

	/**
	 * Check for new plugin version
	 *
	 * @since 1.0
	 */
	public function tm_update_plugins( $transient ) {

		if ( isset( $transient->response[ $this->plugin_slug ] ) ) {
			return $transient;
		}

		$remote_version = $this->remote_api_call( 'new_version', TRUE );

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

			$obj->name                                 = $this->TM_Updater_instance->title;
			$transient->response[ $this->plugin_slug ] = $obj;
		}

		return $transient;
	}

	/**
	 * Perform a remote api call
	 *
	 * @since 1.0
	 */
	public function remote_api_call( $action = "", $is_serialized = FALSE ) {
		$request = wp_remote_post( $this->update_path,
			array( 'body' =>
				       array( 'action' => $action,
				              'id'     => $this->plugin_envato_id,
				              'type'   => 'plugin',
				       )
			) );

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return ( $is_serialized ) ? unserialize( ( $request['body'] ) ) : $request['body'];
		}

		return FALSE;
	}

	/**
	 * Get plugin data for update
	 *
	 * @since 1.0
	 */
	public function tm_plugins_api( $false, $action, $arg ) {
		if ( isset( $arg->slug ) && $arg->slug === $this->slug ) {
			$info = $this->remote_api_call( 'info', TRUE );
			if ( $info ) {
				$info->name = $this->TM_Updater_instance->title;
				$info->slug = $this->slug;

				if ( THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
					$info->download_link = $this->update_path;
				}
			}

			return $info;
		}

		return $false;
	}

	/**
	 * Print update message
	 *
	 * @since 1.0
	 */
	public function tm_update_message() {
		$plugins = get_plugins();
		if ( ! THEMECOMPLETE_EPO_LICENSE()->check_license() ) {
			echo '<br /><a href="' . esc_url( $this->url ) . '">' . esc_html__( 'Download new version from CodeCanyon', 'woocommerce-tm-extra-product-options' ) . '</a>' . ' ' . esc_html__( 'or register the plugin to receive automatic updates.', 'woocommerce-tm-extra-product-options' );
		}
	}

}
