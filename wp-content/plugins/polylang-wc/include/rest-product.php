<?php
/**
 * @package Polylang-WC
 */

/**
 * Expose the product language and translations in the REST API.
 *
 * @since 1.1
 */
class PLLWC_REST_Product extends PLL_REST_Translated_Object {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		if ( empty( PLL()->rest_api ) ) {
			return;
		}

		parent::__construct( PLL()->rest_api, array( 'product' => array( 'filters' => false ) ) );

		$this->type           = 'post';
		$this->id             = 'ID'; // Backward compatibility with Polylang Pro < 3.2.
		$this->setter_id_name = 'ID';

		$this->data_store = PLLWC_Data_Store::load( 'product_language' );

		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ) ); // Before Auto translate.

		add_filter( 'pllwc_language_for_unique_sku', array( $this, 'language_for_unique_sku' ) );
	}

	/**
	 * Returns the object language.
	 *
	 * @since 1.1
	 *
	 * @param array $object Product array.
	 * @return string|false
	 */
	public function get_language( $object ) {
		return $this->data_store->get_language( $object['id'] );
	}

	/**
	 * Sets the object language.
	 *
	 * @since 1.1
	 *
	 * @param string $lang   Language code.
	 * @param object $object Instance of WC_Product.
	 * @return bool
	 */
	public function set_language( $lang, $object ) {
		if ( $object instanceof WC_Product ) {
			$this->data_store->set_language( $object->get_id(), $lang );
		} else {
			parent::set_language( $lang, $object );
		}
		return true;
	}

	/**
	 * Returns the object translations.
	 *
	 * @since 1.1
	 *
	 * @param array $object Product array.
	 * @return array
	 */
	public function get_translations( $object ) {
		return $this->data_store->get_translations( $object['id'] );
	}

	/**
	 * Save the translations.
	 *
	 * @since 1.1
	 *
	 * @param int[]  $translations Array of translations with language codes as keys and object ids as values.
	 * @param object $object       Instance of WC_Product.
	 * @return bool
	 */
	public function save_translations( $translations, $object ) {
		if ( $object instanceof WC_Product ) {
			$translations[ $this->data_store->get_language( $object->get_id() ) ] = $object->get_id();
			$this->data_store->save_translations( $translations );
		} else {
			parent::save_translations( $translations, $object );
		}
		return true;
	}

	/**
	 * Deactivate Auto translate to allow queries of attribute terms in the right language.
	 *
	 * @since 1.1
	 *
	 * @param array $args WP_Term_Query arguments.
	 * @return array
	 */
	public function get_terms_args( $args ) {
		if ( ! empty( $args['include'] ) ) {
			$args['lang'] = '';
		}
		return $args;
	}

	/**
	 * Returns the language to use when searching if a sku is unique.
	 * Requires Polylang Pro 2.7+
	 *
	 * @since 1.3
	 *
	 * @param PLL_Language $language Language for unique sku.
	 * @return PLL_Language
	 */
	public function language_for_unique_sku( $language ) {
		if ( isset( $this->request['lang'] ) && in_array( $this->request['lang'], $this->model->get_languages_list( array( 'fields' => 'slug' ) ) ) ) {
			$language = PLL()->model->get_language( $this->request['lang'] );
		} elseif ( isset( $this->params['lang'] ) && in_array( $this->params['lang'], $this->model->get_languages_list( array( 'fields' => 'slug' ) ) ) ) {
			// Backward compatibility with Polylang Pro < 3.2.
			$language = PLL()->model->get_language( $this->params['lang'] );
		}

		return $language;
	}
}
