<?php
/**
 * @package Polylang-WC
 */

/**
 * Main class for managing Polylang for WooCommerce step in the Polylang Wizard.
 *
 * @since 1.4
 */
class PLLWC_Wizard {

	const PLUGIN_FILTER = array( 'woocommerce', 'polylang', 'polylang-pro', 'polylang-wc' );

	/**
	 * @var PLL_Model
	 */
	protected $model;

	/**
	 * @var PLL_Wizard
	 */
	protected $wizard;

	/**
	 * List of translation packages to download.
	 *
	 * @var stdClass[]
	 */
	protected $translation_updates;

	/**
	 * Constructor.
	 *
	 * @since 1.4
	 *
	 * @param PLL_Model  $model  Reference to PLL_Model object.
	 * @param PLL_Wizard $wizard Reference to PLL_Wizard object.
	 */
	public function __construct( $model, $wizard ) {
		$this->model  = $model;
		$this->wizard = $wizard;

		// Add the WooCommerce specific step in the Wizard at the right place.
		add_filter( 'pll_wizard_steps', array( $this, 'add_step_wc_pages' ), 600 );
	}

	/**
	 * Add the WooCommerce pages step in the wizard.
	 *
	 * @since 1.4
	 *
	 * @param array $steps List of steps.
	 * @return array List of steps updated.
	 */
	public function add_step_wc_pages( $steps ) {
		$this->translation_updates = $this->get_translation_updates();

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

		// See Polylang PLL_Wizard::steps property documentation https://github.com/polylang/polylang/blob/2.8.2/modules/wizard/wizard.php#L26 .
		$steps['wc-pages'] = array(
			'name'    => 'WooCommerce',
			'view'    => array( $this, 'display_step_wc_pages' ),
			'handler' => array( $this, 'save_step_wc_pages' ),
			'scripts' => array(),
			'styles'  => array( 'woocommerce_admin_styles' ),
		);
		return $steps;
	}

	/**
	 * Displays the WooCommerce pages step form.
	 *
	 * @since 1.4
	 *
	 * @return void
	 */
	public function display_step_wc_pages() {
		include __DIR__ . '/view-wizard-step-wc-pages.php';
	}

	/**
	 * Executes the WooCommerce pages step.
	 *
	 * @since 1.4
	 *
	 * @return void
	 */
	public function save_step_wc_pages() {
		check_admin_referer( 'pll-wizard', '_pll_nonce' );

		$translation_updates = $this->get_translation_updates();
		if ( count( $translation_updates ) > 0 ) {

			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			$url     = esc_url_raw( $this->wizard->get_next_step_link() );
			$nonce   = 'pll-wizard';
			$title   = esc_html__( 'Update translations', 'polylang-wc' );
			$context = WP_LANG_DIR;

			$upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin( compact( 'url', 'nonce', 'title', 'context' ) ) );
			$upgrader->bulk_upgrade( $translation_updates );
		}

		if ( Polylang_Woocommerce::instance()->admin_status_reports->get_woocommerce_pages_status()->is_error && pll_default_language() ) {
			// Ensure that all specific WooCommerce pages are created and translated.
			$admin_wc_install = new PLLWC_Admin_WC_Install();
			$admin_wc_install->init_translated_pages();
			$admin_wc_install->install_pages();
		}

		wp_safe_redirect( esc_url_raw( $this->wizard->get_next_step_link() ) );
		exit;
	}

	/**
	 * Filters the update_plugins transient last ckecked date.
	 *
	 * @since 1.4
	 *
	 * @param StdClass $updates Transient value of plugins which need to be updated.
	 * @return StdClass Filtered value of the transient.
	 */
	public function update_last_checked( $updates ) {
		// Substract one day to always search for the translations updates.
		$updates->last_checked = $updates->last_checked - ( 3600 * 24 );
		return $updates;
	}

	/**
	 * Filter the update_plugins transient by only returning translation updates.
	 *
	 * @since 1.4
	 *
	 * @param StdClass $updates Transient value of plugins which need to be updated.
	 * @return StdClass Filtered value of the transient.
	 */
	public function update_plugins( $updates ) {
		foreach ( $updates->translations as $key => $translation ) {
			// Remove plugin translation update which is not necessary yet.
			if ( ! in_array( $translation['slug'], self::PLUGIN_FILTER ) ) {
				unset( $updates->translations[ $key ] );
			}
		}
		return $updates;
	}

	/**
	 * Retrieves translation updates.
	 *
	 * @since 1.4
	 *
	 * @return stdClass[] List of translation packages to download.
	 */
	public function get_translation_updates() {
		add_filter( 'site_transient_update_plugins', array( $this, 'update_last_checked' ) );
		wp_update_plugins(); // To ensure the transient is updated recently.
		add_filter( 'site_transient_update_plugins', array( $this, 'update_plugins' ) );

		$translations = array();
		foreach ( get_site_transient( 'update_plugins' )->translations as $translation ) {
			$translations[] = (object) $translation;
		}
		return $translations;
	}
}
