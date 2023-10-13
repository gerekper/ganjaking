<?php

namespace WCML\Compatibility\WcProductAddons;

use WPML\FP\Fns;

class SharedHooks implements \IWPML_Action {

	const TEMPLATE_FOLDER   = '/templates/compatibility/';
	const ADDONS_OPTION_KEY = '_product_addons';

	public function add_hooks() {
		$withoutRecursion = Fns::withoutRecursion( Fns::noop() );

		add_action( 'init', [ $this, 'loadAssets' ] );
		add_action( 'updated_post_meta', $withoutRecursion( [ $this, 'triggerGlobalAddonUpdated' ] ), 10, 4 );
		add_action( 'added_post_meta', $withoutRecursion( [ $this, 'triggerGlobalAddonUpdated' ] ), 10, 4 );
	}

	public function loadAssets() {
		global $pagenow;

		$isProductPage    = 'post.php' === $pagenow && isset( $_GET['post'] );
		$isProductNewPage = 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'];

		if ( $isProductPage || $isProductNewPage || self::isGlobalAddonEditPage() ) {
			wp_enqueue_script( 'wcml-product-addons', WCML_PLUGIN_URL . '/compatibility/res/js/wcml-product-addons' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION );
			wp_enqueue_style( 'wcml-product-addons', WCML_PLUGIN_URL . '/compatibility/res/css/wcml-product-addons.css', [ 'wpml-dialog' ], WCML_VERSION );
		}
	}

	/**
	 * @return bool
	 */
	public static function isGlobalAddonEditPage() {
		global $pagenow;

		return 'edit.php' === $pagenow &&
			   isset( $_GET['post_type'] ) &&
			   'product' === $_GET['post_type'] &&
			   isset( $_GET['page'] ) &&
			   ( 'global_addons' === $_GET['page'] || 'addons' === $_GET['page'] );
	}

	/**
	 * @param int    $metaId
	 * @param int    $id
	 * @param string $metaKey
	 * @param array  $addons
	 */
	public function triggerGlobalAddonUpdated( $metaId, $id, $metaKey, $addons ) {
		if ( self::ADDONS_OPTION_KEY === $metaKey && self::isGlobalAddon( $id ) ) {
			do_action( 'wcml_product_addons_global_updated', $metaId, $id, $metaKey, $addons );
		}
	}

	/**
	 * @param int $postId
	 *
	 * @return bool
	 */
	public static function isGlobalAddon( $postId ) {
		return 'global_product_addon' === get_post_type( $postId );
	}

	/**
	 * @param string $productId
	 *
	 * @return array
	 */
	public static function getProductAddons( $productId ) {
		$data = get_post_meta( $productId, self::ADDONS_OPTION_KEY, true );
		return $data ? maybe_unserialize( $data ) : [];
	}
}
