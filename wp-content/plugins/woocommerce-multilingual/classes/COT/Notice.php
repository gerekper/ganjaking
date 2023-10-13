<?php

namespace WCML\COT;

use WPML\LIB\WP\Hooks as WPHooks;
use WCML\StandAlone\IStandAloneAction;
use WPML_Notices;

class Notice implements \IWPML_Backend_Action, \IWPML_DIC_Action, IStandAloneAction {

	const NOTICE_GROUP = 'wcml-admin-notices';
	const NOTICE_ID    = 'hpos-sync-disabled';

	/**
	 * @var WPML_Notices
	 */
	private $wpmlNotices;

	/**
	 * @param WPML_Notices $wpmlNotices
	 */
	public function __construct( WPML_Notices $wpmlNotices ) {
		$this->wpmlNotices = $wpmlNotices;
	}

	public function add_hooks() {
		WPHooks::onAction( 'admin_init' )->then( [ $this, 'AddOrRemoveNotice' ] );
	}

	public function AddOrRemoveNotice() {
		$isFeatureEnabled = 'yes' === get_option( 'woocommerce_custom_orders_table_enabled' );
		$isSyncEnabled    = 'yes' === get_option( 'woocommerce_custom_orders_table_data_sync_enabled' );

		if ( $isFeatureEnabled && ! $isSyncEnabled ) {
			$this->addNotice();
		} else {
			$this->removeNotice();
		}
	}

	private function addNotice() {
		$notice = $this->wpmlNotices->get_new_notice( self::NOTICE_ID, $this->getNoticeText(), self::NOTICE_GROUP );

		$notice->set_css_class_types( 'error' );

		/**
		 * Enabling sync is handled by WooCommerce directly:
		 *
		 * @see https://github.com/woocommerce/woocommerce/blob/trunk/plugins/woocommerce/src/Internal/Features/FeaturesController.php#L1225
		 */
		$syncButtonLink = add_query_arg( [
			'woocommerce_custom_orders_table_data_sync_enabled' => 1,
			'_feature_nonce'                                    => wp_create_nonce( 'change_feature_enable' ),
		] );

		$syncButton = $this->wpmlNotices->get_new_notice_action( esc_html__( 'Enable compatibility mode', 'woocommerce-multilingual' ), $syncButtonLink, false, false, true );
		$notice->add_action( $syncButton );

		$notice->set_restrict_to_screen_ids( $this->getRestrictedScreens() );
		$notice->add_capability_check( [ 'manage_options', 'wpml_manage_woocommerce_multilingual' ] );
		$this->wpmlNotices->add_notice( $notice );
	}

	private function removeNotice() {
		if ( $this->wpmlNotices->get_notice( self::NOTICE_ID, self::NOTICE_GROUP ) ) {
			$this->wpmlNotices->remove_notice( self::NOTICE_GROUP, self::NOTICE_ID );
		}
	}

	/**
	 * @return string
	 */
	private function getNoticeText() {
		$text  = '<h2>';
		$text .= esc_html__( 'WCML 5.2.1 and below requires compatibility mode to work with the new way WooCommerce stores orders', 'woocommerce-multilingual' );
		$text .= '</h2>';

		$text .= '<p>';
		$text .= esc_html__( 'We will soon provide an update. In the meantime, to view Analytics and Reports by language, click below to enable compatibility mode.', 'woocommerce-multilingual' );
		$text .= '</p>';

		$text .= '<p><a href="https://wpml.org/compatibility/2023/10/woocommerce-multilingual-5-2-1-compatibility-fix/">';
		$text .= esc_html__( 'Learn more about this issue', 'woocommerce-multilingual' );
		$text .= '</a></p>';

		return $text;
	}

	/**
	 * @return string[]
	 */
	private function getRestrictedScreens() {
		return [
			'dashboard',
			'plugins',
			'woocommerce_page_wc-settings',
			'woocommerce_page_wpml-wcml',
			'woocommerce_page_wc-admin',
			'woocommerce_page_wc-reports',
		];
	}

}
