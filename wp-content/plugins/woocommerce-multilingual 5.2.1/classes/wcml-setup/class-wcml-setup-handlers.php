<?php

use WCML\Options\WPML;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Relation;

class WCML_Setup_Handlers {

	const KEY_TRANSLATION_OPTION    = 'translation-option';
	const KEY_DISPLAY_AS_TRANSLATED = 'display-as-translated';

	/** @var  woocommerce_wpml */
	private $woocommerce_wpml;

	public function __construct( woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function save_attributes( array $data ) {

		if ( isset( $data['attributes'] ) ) {
			$this->woocommerce_wpml->attributes->set_translatable_attributes( $data['attributes'] );
		}

	}

	public function save_multi_currency( array $data ) {

		$this->woocommerce_wpml->get_multi_currency();

		if ( Obj::prop( 'enabled', $data ) ) {
			$this->woocommerce_wpml->multi_currency->enable();
		} else {
			$this->woocommerce_wpml->multi_currency->disable();
		}

	}

	public function install_store_pages( array $data ) {

		if ( ! empty( $data['install_missing_pages'] ) ) {
			WC_Install::create_pages();
		}

		if ( ! empty( $data['install_missing_pages'] ) || ! empty( $data['create_pages'] ) ) {
			$this->woocommerce_wpml->store->create_missing_store_pages_with_redirect();
		}

	}

	/**
	 * @param array $data
	 */
	public function save_translation_options( $data ) {
		$isTranslateEverythingOption = Obj::prop( self::KEY_TRANSLATION_OPTION, $data ) === 'translate_everything';

		$this->set_product_translatable();
		$this->set_product_automatically_translated( $isTranslateEverythingOption );
	}

	/**
	 * This handler might shortcut the previous one,
	 * so we are re-saving the translation preference
	 * for the product and product_cat.
	 *
	 * @param array $data
	 */
	public function save_display_as_translated( $data ) {
		$isDisplayAsTranslated = Relation::propEq( self::KEY_DISPLAY_AS_TRANSLATED, 'yes', $data );
		$settings_helper       = wpml_load_settings_helper();

		$this->set_product_automatically_translated( false );

		if ( $isDisplayAsTranslated ) {
			$settings_helper->set_post_type_display_as_translated( 'product' );
			$settings_helper->set_post_type_translation_unlocked_option( 'product' );
			$settings_helper->set_taxonomy_display_as_translated( 'product_cat' );
			$settings_helper->set_taxonomy_translation_unlocked_option( 'product_cat' );
		} else {
			$this->set_product_translatable();
		}
	}

	/**
	 * @retrun void
	 */
	private function set_product_translatable() {
		$settings_helper = wpml_load_settings_helper();
		$settings_helper->set_post_type_translatable( 'product' );
		$settings_helper->set_post_type_translation_unlocked_option( 'product', false );
		$settings_helper->set_taxonomy_translatable( 'product_cat' );
		$settings_helper->set_taxonomy_translation_unlocked_option( 'product_cat', false );
	}

	/**
	 * @param bool $isAutomatic
	 *
	 * @return void
	 */
	private function set_product_automatically_translated( $isAutomatic ) {
		WPML::setAutomatic( 'product', $isAutomatic );
	}
}
