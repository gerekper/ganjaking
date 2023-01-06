<?php

namespace WCML\AdminNotices;

use woocommerce_wpml;
use WPML\FP\Obj;
use WPML_Notices;

/**
 * Manage showing a reminder notice when multi-currency mode is not configured completely.
 */
class MultiCurrencyMissing implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	const NOTICE_ID = 'wcml-multi-currency-missing';

	/** @var woocommerce_wpml */
	private $wcml;

	/** @var WPML_Notices */
	private $notices;

	public function __construct( woocommerce_wpml $wcml, WPML_Notices $notices ) {
		$this->wcml    = $wcml;
		$this->notices = $notices;
	}

	/**
	 * Add hooks to manage visibility of notice.
	 */
	public function add_hooks() {
		$notice      = $this->notices->get_notice( self::NOTICE_ID );
		$needsNotice = wcml_is_multi_currency_on() && $this->hasOneUniqueCurrency();

		if ( $needsNotice && ! $notice ) {
			add_action( 'admin_init', [ $this, 'addNotice' ] );
		} elseif ( ! $needsNotice && $notice ) {
			add_action( 'admin_init', [ $this, 'removeNotice' ] );
		}
	}

	/**
	 * Add a notice reminding admin about missing secondary currency.
	 */
	public function addNotice() {
		$text  = '<h2>' . __( "You haven't added any secondary currencies", 'woocommerce-multilingual' ) . '</h2>';
		$text .= '<p>' . __( "Please add another currency to fully utilize multicurrency mode. If you do not need multiple currencies, you can disable this setting to improve your site's performance.", 'woocommerce-multilingual' ) . '</p>';
		$text .= '<a href="' . admin_url( 'admin.php?page=wpml-wcml&tab=multi-currency' ) . '">' . __( 'Configure multicurrency mode', 'woocommerce-multilingual' ) . '</a>';

		$notice = $this->notices->create_notice( self::NOTICE_ID, $text );
		$notice->set_css_class_types( 'notice-warning' );
		$notice->set_restrict_to_screen_ids( RestrictedScreens::get() );
		$notice->set_dismissible( true );

		$this->notices->add_notice( $notice );
	}

	/**
	 * Remove the notice if the problem has been fixed
	 */
	public function removeNotice() {
		$notice = $this->notices->get_notice( self::NOTICE_ID );
		$this->notices->remove_notice( $notice->get_group(), $notice->get_id() );
	}

	/**
	 * @return bool
	 */
	private function hasOneUniqueCurrency() {
		return count( (array) Obj::path( [ 'settings', 'currency_options' ], $this->wcml ) ) <= 1;
	}
}
