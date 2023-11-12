<?php
/**
 * BSF Rollback Version
 *
 * @package     bsf-core
 * @author      Brainstorm Force
 * @link        http://wpastra.com/
 */

/**
 * BSF_Core_Update initial setup
 */
class BSF_Rollback_Version {
	/**
	 * Package URL.
	 *
	 * Holds the package URL.
	 * This will be the actual download URL od zip file.
	 *
	 * @access protected
	 *
	 * @var string Package URL.
	 */
	protected $package_url;

	/**
	 * Product URL.
	 *
	 * @access protected
	 *  @var string Product URL.
	 */
	protected $product_url;

	/**
	 * Version.
	 *
	 * Holds the version.
	 *
	 * @access protected
	 *
	 * @var string Package URL.
	 */
	protected $version;

	/**
	 * Plugin name.
	 *
	 * Holds the plugin name.
	 *
	 * @access protected
	 *
	 * @var string Plugin name.
	 */
	protected $plugin_name;

	/**
	 * Plugin slug.
	 *
	 * Holds the plugin slug.
	 *
	 * @access protected
	 *
	 * @var string Plugin slug.
	 */
	protected $plugin_slug;

	/**
	 * Product Title.
	 *
	 * Holds the Product Title.
	 *
	 * @access protected
	 *
	 * @var string Plugin Title.
	 */
	protected $product_title;

	/**
	 * HOlds the Product ID.
	 *
	 * @access protected
	 * @var string Product ID.
	 */
	protected $product_id;

	/**
	 *
	 * Initializing Rollback.
	 *
	 * @access public
	 *
	 * @param array $args Optional.Rollback arguments. Default is an empty array.
	 */
	public function __construct( $args = array() ) {
		foreach ( $args as $key => $value ) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Apply package.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function apply_package() {
		$update_products = get_site_transient( 'update_plugins' );
		if ( ! is_object( $update_products ) ) {
			$update_products = new stdClass();
		}

		$product_info              = new stdClass();
		$product_info->new_version = $this->version;
		$product_info->slug        = $this->plugin_slug;
		$product_info->package     = $this->package_url; // This will be the actual download URL of zip file..
		$product_info->url         = $this->product_url;

		$update_products->response[ $this->plugin_name ] = $product_info;

		set_site_transient( 'update_plugins', $update_products );
	}

	/**
	 * Upgrade.
	 *
	 * Run WordPress upgrade to Rollback to previous version.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function upgrade() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader_args = array(
			'url'    => 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $this->plugin_name ),
			'plugin' => $this->plugin_name,
			'nonce'  => 'upgrade-plugin_' . $this->plugin_name,
			'title'  => apply_filters( 'bsf_rollback_' . $this->product_id . '_title', '<h1>Rollback ' . bsf_get_white_lable_product_name( $this->product_id, $this->product_title ) . ' to version ' . $this->version . ' </h1>' ),
		);

		$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( $upgrader_args ) );
		$upgrader->upgrade( $this->plugin_name );
	}

	/**
	 *
	 * Rollback to previous versions.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		$this->apply_package();
		$this->upgrade();
	}

	/**
	 * Get All versions of product.
	 *
	 * @param string $product_id Product ID.
	 */
	public static function bsf_get_product_versions( $product_id ) {
		if ( empty( $product_id ) ) {
			return array();
		}

		// Check is transient is expire or User has Enalbed/Disabled the beta version.
		$versions_transient = get_site_transient( 'bsf-product-versions-' . $product_id );
		if ( false !== $versions_transient && false === self::is_beta_enabled_rollback( $product_id ) ) {
			return $versions_transient;
		}

		$per_page = apply_filters( 'bsf_show_versions_to_rollback_' . $product_id, 10 );
		$path     = bsf_get_api_site( false, true ) . 'versions/' . $product_id . '?per_page=' . $per_page;
		if ( BSF_Update_Manager::bsf_allow_beta_updates( $product_id ) ) {
			$path = add_query_arg( 'include_beta', 'true', $path );
		}

		$response = wp_remote_get(
			$path,
			array(
				'timeout' => '10',
			)
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return array();
		}

		$response_versions = json_decode( wp_remote_retrieve_body( $response ), true );
		// Cache product version for 24 hrs.
		set_site_transient( 'bsf-product-versions-' . $product_id, $response_versions, 24 * HOUR_IN_SECONDS );

		return $response_versions;
	}
	/**
	 * This will filter the versions and return the versions less than current installed version.
	 *
	 * @param array  $version_arr array of versions.
	 * @param string $current_version Current install version.
	 *
	 * @return array
	 */
	public static function sort_product_versions( $version_arr, $current_version ) {
		$rollback_versions = array();

		foreach ( $version_arr as $version ) {
			if ( version_compare( $version, $current_version, '>=' ) ) {
				continue;
			}
			$rollback_versions[] = $version;
		}

		return $rollback_versions;
	}

	/**
	 * This function is added to update the trasient data of product version on beta update enabled/disabled action.
	 * This will set the flag in db options that should beta versions include/removed in the rollback versions list based on enabled/disabled beta updates for the product.
	 *
	 * @param string $product_id Product ID.
	 *
	 * @return bool
	 */
	public static function is_beta_enabled_rollback( $product_id ) {
		$allow_beta_update = BSF_Update_Manager::bsf_allow_beta_updates( $product_id );
		$is_beta_enable    = ( false === $allow_beta_update ) ? '0' : '1';

		// Set the initial flag for is beta enelbled/ disabled.
		if ( false === get_option( 'is_beta_enable_rollback_' . $product_id ) ) {
			update_option( 'is_beta_enable_rollback_' . $product_id, $is_beta_enable );
			return false;
		}

		// If user has enalbed/ disabled beta update then upadate the rollback version transient data.
		if ( get_option( 'is_beta_enable_rollback_' . $product_id ) !== $is_beta_enable ) {
			update_option( 'is_beta_enable_rollback_' . $product_id, $is_beta_enable );
			return true;
		}
		return false;
	}

}
