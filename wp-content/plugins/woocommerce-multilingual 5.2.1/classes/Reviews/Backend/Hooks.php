<?php

namespace WCML\Reviews\Backend;

use SitePress;

class Hooks implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	const AFTER_SITEPRESS_JS_LOAD = 3;

	/**
	 * @var SitePress $sitepress
	 */
	private $sitepress;

	/**
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'disableLanguageQuickLinks' ], self::AFTER_SITEPRESS_JS_LOAD );
	}

	public function disableLanguageQuickLinks() {
		/* phpcs:ignore WordPress.Security.NonceVerification.Recommended */
		$getData = wpml_collect( $_GET );

		if ( 'product' === $getData->get( 'post_type' ) && 'product-reviews' === $getData->get( 'page' ) ) {
			remove_action( 'admin_enqueue_scripts', [ $this->sitepress, 'language_filter' ] );
		}
	}

}
