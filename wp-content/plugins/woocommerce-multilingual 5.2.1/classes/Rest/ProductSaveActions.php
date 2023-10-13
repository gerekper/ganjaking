<?php

namespace WCML\Rest;

use function WCML\functions\getId;

/**
 * We need to reuse the `after_save_post` protected method
 * of the \WPML_Post_Translation and the only way is by inheritance.
 * The abstract methods are not used in the parent, but
 * we need implement it to respect the contract.
 * We are also including the WCML logic to `synchronize_products`.
 */
class ProductSaveActions extends \WPML_Post_Translation {

	/** @var \SitePress $sitepress */
	private $sitepress;

	/** @var \WCML_Synchronize_Product_Data $productDataSync */
	private $productDataSync;

	public function __construct(
		array $settings,
		\wpdb $wpdb,
		\SitePress $sitepress,
		\WCML_Synchronize_Product_Data $productDataSync
	) {
		parent::__construct( $settings, $wpdb );
		$this->sitepress       = $sitepress;
		$this->productDataSync = $productDataSync;
	}

	/**
	 * @param object|\WC_Abstract_Legacy_Product $product
	 * @param int|null                           $trid
	 * @param string                             $langCode
	 * @param int|null                           $translationOf
	 */
	public function run( $product, $trid, $langCode, $translationOf ) {
		$productId      = getId( $product );
		$trid           = $trid ? $trid : $this->get_save_post_trid( $productId, null );
		$langCode       = $langCode ? $langCode : parent::get_save_post_lang( $productId, $this->sitepress );
		$sourceLangCode = $this->get_element_lang_code( $translationOf );

		$this->after_save_post( $trid, get_post( $productId, ARRAY_A ), $langCode, $sourceLangCode );
		$this->productDataSync->synchronize_products( $productId, get_post( $productId ), true );
	}

	public function save_post_actions( $postId, $post ) {
		throw new \Exception( 'This method should not be called, use `run` instead.' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_save_post_trid( $postId, $post_status ) {
		return $this->get_element_trid( $postId );
	}

	/**
	 * @inheritDoc
	 */
	protected function get_save_post_source_lang( $trid, $language_code, $default_language ) {
		return $this->get_source_lang_code( $this->get_element_id( $language_code, $trid ) );
	}
}
