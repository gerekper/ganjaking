<?php

namespace WCML\StandAlone\UI;

use WCML\Utilities\AdminPages;
use WCML_Admin_Menus;
use WCML_Multi_Currency_UI;
use WPML\Core\ISitePress;
use WCML\StandAlone\NullSitePress;
use SitePress;
use woocommerce_wpml;
use WPML\FP\Str;

class AdminMenu extends \WCML_Menu_Wrap_Base {

	/** @var SitePress|NullSitePress */
	private $sitepress;

	/**
	 * WCML_Menus_Wrap constructor.
	 *
	 * @param SitePress|NullSitePress $sitepress
	 * @param woocommerce_wpml        $woocommerce_wpml
	 */
	public function __construct( ISitePress $sitepress, $woocommerce_wpml ) {
		parent::__construct( $woocommerce_wpml );

		$this->sitepress = $sitepress;
	}

	/**
	 * @return array
	 */
	protected function get_child_model() {
		$current_tab = AdminPages::getTabToDisplay();

		$model = [
			'strings'             => [
				'title'              => WCML_Admin_Menus::getWcmlLabel(),
			],
			'is_standalone'       => true,
			'menu'                => [
				'multilingual'          => [
					'title'  => __( 'Multilingual', 'woocommerce-multilingual' ),
					'active' => 'multilingual' === $current_tab ? 'nav-tab-active' : '',
					'url'    => admin_url( 'admin.php?page=wpml-wcml&tab=multilingual' ),
				],
				'multi_currency'    => [
					'name'   => __( 'Multicurrency', 'woocommerce-multilingual' ),
					'active' => 'multi-currency' === $current_tab ? 'nav-tab-active' : '',
					'url'    => admin_url( 'admin.php?page=wpml-wcml&tab=multi-currency' ),
				],
			],
			'content'             => $this->get_current_menu_content( $current_tab ),
		];

		return $model;
	}

	protected function get_current_menu_content( $current_tab ) {
		$content = '';
		switch ( $current_tab ) {
			case 'multilingual':
				$inP       = Str::wrap( '<p>', '</p>' );
				$inWrapper = Str::wrap( '<div class="wcml-banner">', '</div>' );

				$wrapLink = function( $text, $url, $isExternal = true ) {
					$attrs = $isExternal ? ' target="_blank" class="wpml-external-link" ' : '';
					return sprintf( $text, '<a href="' . $url . '"' . $attrs . '">', '</a>' );
				};

				/* translators: %1$s and %2$s are opening and closing HTML link tags */
				$content .= $inP( $wrapLink( esc_html__( 'To run your store in multiple languages, you need to use the %1$sWPML plugin%2$s.', 'woocommerce-multilingual' ), \WCML_Tracking_Link::getWpmlHome( true ) ) );

				$content .= $inP(
					/* translators: %1$s and %2$s are opening and closing HTML link tags */
					$wrapLink( esc_html__( 'If you have it already, install and activate it. Otherwise, %1$sbuy WPML%2$s.', 'woocommerce-multilingual' ), \WCML_Tracking_Link::getWpmlPurchase( true ) )
					. ' ' . esc_html__( 'You will need either the Multilingual CMS or Multilingual Agency package to use WPML with WooCommerce.', 'woocommerce-multilingual' )
				);

				$content .= $inP(
					$wrapLink(
						/* translators: %1$s and %2$s are opening and closing HTML link tags */
						esc_html__( 'You can still use the %1$smulticurrency features%2$s without buying anything.', 'woocommerce-multilingual' ),
						admin_url( 'admin.php?page=wpml-wcml&tab=multi-currency' ),
						false
					)
				);

				$content = $inWrapper( $content );
				break;

			case 'multi-currency':
			default:
				if ( current_user_can( 'wpml_operate_woocommerce_multilingual' ) ) {
					$wcml_mc_ui = new WCML_Multi_Currency_UI( $this->woocommerce_wpml, $this->sitepress );
					$content    = $wcml_mc_ui->get_view();
				}

				break;
		}

		return $content;

	}

}
