<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder updater
 *
 * @package WPBakeryPageBuilder
 *
 */

/**
 * Vc updating manager.
 */
class Vc_Updater {
	/**
	 * @var string
	 */
	protected $version_url = 'https://updates.wpbakery.com/';

	/**
	 * Proxy URL that returns real download link
	 *
	 * @var string
	 */
	protected $download_link_url = 'https://support.wpbakery.com/updates/download-link';

	/**
	 * @var bool
	 */
	protected $auto_updater;

	public function init() {
		add_filter( 'upgrader_pre_download', array(
			$this,
			'preUpgradeFilter',
		), 10, 4 );
	}

	/**
	 * Setter for manager updater.
	 *
	 * @param Vc_Updating_Manager $updater
	 */
	public function setUpdateManager( Vc_Updating_Manager $updater ) {
		$this->auto_updater = $updater;
	}

	/**
	 * Getter for manager updater.
	 *
	 * @return Vc_Updating_Manager|bool
	 */
	public function updateManager() {
		return $this->auto_updater;
	}

	/**
	 * Get url for version validation
	 * @return string
	 */
	public function versionUrl() {
		return $this->version_url;
	}

	/**
	 * Get unique, short-lived download link
	 *
	 * @return array|boolean JSON response or false if request failed
	 */
	public function getDownloadUrl() {
		$url = $this->getUrl();
		// FIX SSL SNI
		$filter_add = true;
		if ( function_exists( 'curl_version' ) ) {
			$version = curl_version();
			if ( version_compare( $version['version'], '7.18', '>=' ) ) {
				$filter_add = false;
			}
		}
		if ( $filter_add ) {
			add_filter( 'https_ssl_verify', '__return_false' );
		}
		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( $filter_add ) {
			remove_filter( 'https_ssl_verify', '__return_false' );
		}

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return json_decode( $response['body'], true );
	}

	/**
	 * @return string
	 */
	protected function getUrl() {
		$host = esc_url( vc_license()->getSiteUrl() );
		$key = rawurlencode( vc_license()->getLicenseKey() );

		$url = $this->download_link_url . '?product=vc&url=' . $host . '&key=' . $key . '&version=' . WPB_VC_VERSION;

		return $url;
	}

	/**
	 * @return string|void
	 */
	public static function getUpdaterUrl() {
		return vc_is_network_plugin() ? network_admin_url( 'admin.php?page=vc-updater' ) : admin_url( 'admin.php?page=vc-updater' );
	}

	/**
	 * Get link to newest VC
	 *
	 * @param $reply
	 * @param $package
	 * @param WP_Upgrader $updater
	 *
	 * @return mixed|string|WP_Error
	 */
	public function preUpgradeFilter( $reply, $package, $updater ) {
		$condition1 = isset( $updater->skin->plugin ) && vc_plugin_name() === $updater->skin->plugin;
		// Must use I18N otherwise France or other languages will not work
		$condition2 = isset( $updater->skin->plugin_info ) && __( 'WPBakery Page Builder', 'js_composer' ) === $updater->skin->plugin_info['Name'];
		if ( ! $condition1 && ! $condition2 ) {
			return $reply;
		}

		$res = $updater->fs_connect( array( WP_CONTENT_DIR ) );
		if ( ! $res ) {
			return new WP_Error( 'no_credentials', esc_html__( "Error! Can't connect to filesystem", 'js_composer' ) );
		}

		if ( ! vc_license()->isActivated() ) {
			if ( vc_is_as_theme() && vc_get_param( 'action' ) !== 'update-selected' ) {
				return false;
			}
			$url = self::getUpdaterUrl();

			return new WP_Error( 'no_credentials', sprintf( esc_html__( 'To receive automatic updates license activation is required. Please visit %sSettings%s to activate your WPBakery Page Builder.', 'js_composer' ), '<a href="' . esc_url( $url ) . '" target="_blank">', '</a>' ) . ' ' . sprintf( ' <a href="https://go.wpbakery.com/faq-update-in-theme" target="_blank">%s</a>', esc_html__( 'Got WPBakery Page Builder in theme?', 'js_composer' ) ) );
		}

		$updater->strings['downloading_package_url'] = esc_html__( 'Getting download link...', 'js_composer' );
		$updater->skin->feedback( 'downloading_package_url' );

		$response = $this->getDownloadUrl();

		if ( ! $response ) {
			return new WP_Error( 'no_credentials', esc_html__( 'Download link could not be retrieved', 'js_composer' ) );
		}

		if ( ! $response['status'] ) {
			return new WP_Error( 'no_credentials', $response['error'] );
		}

		$updater->strings['downloading_package'] = esc_html__( 'Downloading package...', 'js_composer' );
		$updater->skin->feedback( 'downloading_package' );

		$downloaded_archive = download_url( $response['url'] );
		if ( is_wp_error( $downloaded_archive ) ) {
			return $downloaded_archive;
		}

		$plugin_directory_name = dirname( vc_plugin_name() );

		// WP will use same name for plugin directory as archive name, so we have to rename it
		if ( basename( $downloaded_archive, '.zip' ) !== $plugin_directory_name ) {
			$new_archive_name = dirname( $downloaded_archive ) . '/' . $plugin_directory_name . time() . '.zip';
			if ( rename( $downloaded_archive, $new_archive_name ) ) {
				$downloaded_archive = $new_archive_name;
			}
		}

		return $downloaded_archive;
	}
}
