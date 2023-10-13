<?php

namespace WCML\Setup;

use WPML\FP\Obj;
use WPML\LIB\WP\Hooks;
use function WPML\FP\pipe;

class BeforeHooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {

	/** @var  \woocommerce_wpml */
	private $woocommerce_wpml;

	public function __construct( \woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		if ( ! $this->woocommerce_wpml->get_setting( 'set_up_wizard_run' ) ) {
			add_filter( 'get_translatable_documents_all', [ __CLASS__, 'blockProductTranslation' ] );
		}
	}

	/**
	 * @param array $translatablePostTypes
	 *
	 * @return array
	 */
	public static function blockProductTranslation( $translatablePostTypes ) {
		unset( $translatablePostTypes['product'], $translatablePostTypes['product_variation'] );
		return $translatablePostTypes;
	}
}
