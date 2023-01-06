<?php

namespace WCML\Compatibility;

use WooCommerce;
use wpdb;
use WPML_Element_Translation_Package;

abstract class ComponentFactory implements \IWPML_Backend_Action_Loader, \IWPML_Frontend_Action_Loader {

	/**
	 * @return callable|\IWPML_Action|\IWPML_Action[]|void|null
	 */
	abstract public function create();

	/**
	 * @return wpdb
	 */
	protected static function getWpdb() {
		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		return $wpdb;
	}

	/**
	 * @return WooCommerce
	 */
	protected static function getWooCommerce() {
		/**
		 * @var WooCommerce $woocommerce
		 */
		global $woocommerce;

		return $woocommerce;
	}

	/**
	 * @return WPML_Element_Translation_Package|null
	 */
	protected static function getElementTranslationPackage() {
		return class_exists( 'WPML_Element_Translation_Package' ) ? new WPML_Element_Translation_Package() : null;
	}

	/**
	 * @return \WPML_Post_Translation|null
	 */
	protected static function getPostTranslations() {
		/**
		 * @var \WPML_Post_Translation|null $wpml_post_translations
		 */
		global $wpml_post_translations;

		return $wpml_post_translations;
	}
}
