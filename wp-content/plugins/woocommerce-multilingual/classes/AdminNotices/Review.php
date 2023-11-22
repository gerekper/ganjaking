<?php

namespace WCML\AdminNotices;

use WCML\StandAlone\IStandAloneAction;
use WPML_Notices;
use IWPML_Backend_Action;
use IWPML_Frontend_Action;
use IWPML_DIC_Action;

class Review implements IWPML_Backend_Action, IWPML_Frontend_Action, IWPML_DIC_Action, IStandAloneAction {

	const OPTION_NAME = 'wcml-rate-notice';

	/** @var WPML_Notices $wpmlNotices */
	private $wpmlNotices;

	/**
	 * @param WPML_Notices $wpmlNotices
	 */
	public function __construct( WPML_Notices $wpmlNotices ) {
		$this->wpmlNotices = $wpmlNotices;
	}

	public function add_hooks() {
		add_action( 'admin_notices', [ $this, 'addNotice' ] );
		add_action( 'woocommerce_after_order_object_save', [ $this, 'onNewOrder' ] );
	}

	public function addNotice() {

		if ( $this->shouldDisplayNotice() ) {
			$notice = $this->wpmlNotices->get_new_notice( 'wcml-rate', $this->getNoticeText(), 'wcml-admin-notices' );

			if ( $this->wpmlNotices->is_notice_dismissed( $notice ) ) {
				return;
			}

			$notice->set_css_class_types( 'info' );
			$notice->set_css_classes( [ 'otgs-notice-wcml-rating' ] );
			$notice->set_dismissible( true );

			$reviewLink   = 'https://wordpress.org/support/plugin/woocommerce-multilingual/reviews/?filter=5#new-post';
			$reviewButton = $this->wpmlNotices->get_new_notice_action( __( 'Review WooCommerce Multilingual & Multicurrency', 'woocommerce-multilingual' ), $reviewLink, false, false, true );
			$notice->add_action( $reviewButton );

			$notice->set_restrict_to_screen_ids( RestrictedScreens::get() );
			$notice->add_capability_check( [ 'manage_options', 'wpml_manage_woocommerce_multilingual' ] );
			$this->wpmlNotices->add_notice( $notice );
		}
	}

	/**
	 * @return string
	 */
	private function getNoticeText() {
		$text  = '<h2>';
		$text .= __( 'Congrats! You\'ve just earned some money using WooCommerce Multilingual & Multicurrency.', 'woocommerce-multilingual' );
		$text .= '</h2>';

		$text .= '<p>';
		$text .= __( 'How do you feel getting your very first order in foreign language or currency?', 'woocommerce-multilingual' );
		$text .= '<br />';
		$text .= __( 'We for sure are super thrilled about your success! Will you help WCML improve and grow?', 'woocommerce-multilingual' );
		$text .= '</p>';

		$text .= '<p><strong>';
		$text .= __( 'Give us <span class="rating">5.0 <i class="otgs-ico-star"></i></span> review now.', 'woocommerce-multilingual' );
		$text .= '</strong></p>';

		return $text;
	}

	/**
	 * @return bool
	 */
	private function shouldDisplayNotice() {
		return get_option( self::OPTION_NAME, false );
	}

	/**
	 * @param \WC_Order $order
	 */
	public function onNewOrder( $order ) {
		if ( ! $this->shouldDisplayNotice() ) {
			$this->maybeAddOptionToShowNotice( $order );
		}
	}

	/**
	 * @param \WC_Order $order
	 */
	private function maybeAddOptionToShowNotice( $order ) {
		$isOrderInSecondLanguage = $order->get_meta( 'wpml_language' ) !== apply_filters( 'wpml_default_language', '' );

		$isOrderInSecondCurrency = wcml_is_multi_currency_on()
			&& $order->get_currency() !== wcml_get_woocommerce_currency_option();

		if ( $isOrderInSecondLanguage || $isOrderInSecondCurrency ) {
			add_option( self::OPTION_NAME, true );
		}
	}

}
