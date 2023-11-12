<?php
/**
 * BSF Rollback Version manager class file.
 *
 * @package bsf-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BSF Rollback Version manager.
 */
class BSF_Rollback_Version_Manager {

	/**
	 * This is set for page reload cache.
	 *
	 * @var $reload_page_cache
	 */
	public static $reload_page_cache = 1;
	/**
	 * Constructor function that initializes required sections.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'bsf_download_rollback_version' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	/**
	 * Display Rollback to privious versions form.
	 *
	 * @param string $product_id Product ID.
	 * @since 1.0.0
	 */
	public function render_rollback_version_form( $product_id ) {
		add_action( 'admin_footer', array( $this, 'rollback_version_popup' ) );

		// Enqueue scripts only when this function is called.
		wp_enqueue_script( 'bsf-core-version-rollback' );
		wp_enqueue_style( 'bsf-core-version-rollback-css' );

		$product_details   = get_brainstorm_product( $product_id );
		$installed_version = isset( $product_details['version'] ) ? $product_details['version'] : '';
		$product_versions  = BSF_Rollback_Version::bsf_get_product_versions( $product_id ); // Get Remote versions
		// Show versions above than latest install version of the product.
		$sorted_versions = BSF_Rollback_Version::sort_product_versions( $product_versions, $installed_version );

		if ( empty( $sorted_versions ) ) {
			echo esc_html__( 'No Versions Found! ', 'bsf-core' );
			return;
		}

		$product_name     = isset( $product_details['name'] ) ? $product_details['name'] : '';
		$white_label_name = bsf_get_white_lable_product_name( $product_id, $product_name );
		?>
		<div class="bsf-rollback-version">
			<input type="hidden" name="product-name" id="bsf-product-name" value="<?php echo esc_attr( $white_label_name ); ?>">
			<select class="bsf-rollback-version-select">
			<?php
			foreach ( $sorted_versions as $version ) {
				?>
					<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $version ); ?> </option>
				<?php
			}
			?>
			</select>
			<a data-placeholder-text=" <?php echo esc_html__( 'Rollback', 'bsf-core' ); ?>" href="<?php echo esc_url( add_query_arg( 'version_no', $sorted_versions[0], wp_nonce_url( admin_url( 'index.php?action=bsf_rollback&product_id=' . $product_id ), 'bsf_rollback' ) ) ); ?>"
			data-placeholder-url="<?php echo esc_url( wp_nonce_url( admin_url( 'index.php?action=bsf_rollback&version_no=VERSION&product_id=' . $product_id ), 'bsf_rollback' ) ); ?>" class="button bsf-rollback-button"><?php echo esc_html__( 'Rollback', 'bsf-core' ); ?> </a>
		</div>
		<?php
	}

	/**
	 * Download Product Version.
	 */
	public function bsf_download_rollback_version() {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		if ( empty( $_GET['version_no'] ) || empty( $_GET['product_id'] ) || ! isset( $_GET['action'] ) ) {
			return false;
		}

		check_admin_referer( 'bsf_rollback' );

		$bsf_update_manager  = new BSF_Update_Manager();
		$version_no          = sanitize_text_field( $_GET['version_no'] );
		$product_id          = sanitize_text_field( $_GET['product_id'] );
		$product_details     = get_brainstorm_product( $product_id );
		$plugin_slug         = ! empty( $product_details['slug'] ) ? $product_details['slug'] : '';
		$plugin_name         = ! empty( $product_details['template'] ) ? $product_details['template'] : '';
		$bundled_plugin_name = ! empty( $product_details['init'] ) ? $product_details['init'] : '';
		$purchase_key        = $bsf_update_manager->get_purchse_key( $product_id );
		$product_title       = ! empty( $product_details['name'] ) ? $product_details['name'] : '';
		$plugin_name         = ! empty( $plugin_name ) ? $plugin_name : $bundled_plugin_name;

		$download_params = array(
			'version_no'   => $version_no,
			'purchase_key' => $purchase_key,
			'site_url'     => get_site_url(),
		);

		$download_url = bsf_get_api_site( false, true ) . 'download/' . $product_id . '?' . http_build_query( $download_params );

		$rollback = new BSF_Rollback_Version(
			array(
				'version'       => $version_no,
				'plugin_name'   => $plugin_name,
				'plugin_slug'   => $plugin_slug,
				'package_url'   => $download_url,
				'product_title' => $product_title,
				'product_id'    => $product_id,
			)
		);
		$rollback->run();
		// Delete product versions transient data after update.
		bsf_clear_versions_cache( $product_id );
		wp_die();
	}

	/**
	 * Load Scripts
	 *
	 * @since 1.0.0
	 *
	 * @param  string $hook Current Hook.
	 * @return void
	 */
	public function load_scripts( $hook = '' ) {
		wp_register_script( 'bsf-core-version-rollback', bsf_core_url( '/assets/js/version-rollback.js' ), array( 'jquery' ), BSF_UPDATER_VERSION, true );
		wp_register_style( 'bsf-core-version-rollback-css', bsf_core_url( '/assets/css/rollback-version.css' ), array(), BSF_UPDATER_VERSION );
	}

	/**
	 * Version rollback Confirmation popup.
	 *
	 * @since 1.0.0
	 */
	public function rollback_version_popup() {
		// This is set to fix the duplicate markup on page load.
		if ( 1 !== self::$reload_page_cache ) {
			return;
		}

		self::$reload_page_cache = 0;
		?>
		<div class="bsf-confirm-rollback-popup" style="display:none;">
			<div class="bsf-core-rollback-overlay"></div>
			<div class="bsf-confirm-rollback-popup-content">
				<h3 class="bsf-rollback-heading bsf-confirm-heading" data-text="<?php esc_html_e( 'Rollback #PRODUCT_NAME# Version', 'bsf-core' ); ?>"></h3>
				<p class="bsf-confirm-text bsf-rollback-text" data-text="<?php esc_html_e( 'Are you sure you want to rollback #PRODUCT_NAME# to version #VERSION#?', 'bsf-core' ); ?>" ></p>
				<div class="bsf-confirm-rollback-popup-buttons-wrapper">
					<button class="button bsf-product-license button-default bsf-confirm-cancel"><?php esc_html_e( 'Cancel', 'bsf-core' ); ?></button>
					<button class="button button-primary  bsf-confirm-ok"><?php esc_html_e( 'Continue', 'bsf-core' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}

}

new BSF_Rollback_Version_Manager();

/**
 * Render Rollback versoin form.
 *
 * @param string $product_id Product ID.
 */
function bsf_get_version_rollback_form( $product_id ) {
	if ( ! bsf_display_rollback_version_form( $product_id ) ) {
		return false;
	}
	$instance = new BSF_Rollback_Version_Manager();
	$instance->render_rollback_version_form( $product_id );
}
