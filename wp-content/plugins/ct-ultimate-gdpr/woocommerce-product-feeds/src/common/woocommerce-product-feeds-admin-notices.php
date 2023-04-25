<?php

use Ademti\DismissibleWpNotices\DismissibleWpNoticeManager;

class WoocommerceProductFeedsAdminNotices {
	/**
	 * @var DismissibleWpNoticeManager
	 */
	protected $notice_manager;

	/**
	 * @var WoocommerceGpfTemplateLoader
	 */
	protected $template;

	/**
	 * @var array[]
	 */
	private $admin_notices = [
		[ 'woocommerce-gpf-feedback', true, 604800, false ],
	];

	/**
	 * @param DismissibleWpNoticeManager $notice_manager
	 * @param WoocommerceGpfTemplateLoader $template
	 */
	public function __construct( DismissibleWpNoticeManager $notice_manager, WoocommerceGpfTemplateLoader $template ) {
		$this->notice_manager = $notice_manager;
		$this->template       = $template;
	}

	/**
	 * @return void
	 */
	public function initialise() {
		add_action( 'admin_init', [ $this, 'register_admin_notices' ] );
		add_action( 'admin_notices', [ $this, 'feedback_admin_notice' ] );
	}

	/**
	 * @return void
	 */
	public function register_admin_notices() {
		foreach ( $this->admin_notices as $admin_notice ) {
			$this->notice_manager->register_notice( $admin_notice[0], $admin_notice[1], $admin_notice[2], $admin_notice[3] );
		}
	}

	/**
	 * Show an admin notice if the extension has been active for at least a week.
	 *
	 * @return void
	 */
	public function feedback_admin_notice() {
		// Only show on settings pages.
		$current_page = $_GET['page'] ?? '';
		if ( 'wc-settings' !== $current_page && 'woocommerce-gpf-manage-feeds' !== $current_page ) {
			return;
		}
		// Only show if we've been active for at least a week.
		$install_ts = get_option( 'woocommerce_gpf_install_ts', false );
		if ( ! $install_ts || $install_ts > ( time() - 604800 ) ) {
			return;
		}

		// Only show if not snoozed / dismissed.
		if ( ! $this->notice_manager->is_notice_visible( 'woocommerce-gpf-feedback' ) ) {
			return;
		}

		$this->template->output_template_with_variables( 'woo-gpf', 'admin-notices-feedback', [] );
	}
}
