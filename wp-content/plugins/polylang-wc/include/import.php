<?php
/**
 * @package Polylang-WC
 */

/**
 * A class to import languages and translations of products from CSV files.
 *
 * @since 0.8
 */
class PLLWC_Import {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * @var WC_Product_CSV_Importer
	 */
	protected $importer;

	/**
	 * Constructor.
	 * Setups filters and actions.
	 *
	 * @since 0.8
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'product_language' );

		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'default_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'mapping_options' ), 1 );
		add_action( 'woocommerce_product_import_inserted_product_object', array( $this, 'inserted_product_object' ), 10, 2 );

		add_action( 'woocommerce_product_importer_before_set_parsed_data', array( $this, 'before_set_parsed_data' ), 10, 2 );
		add_action( 'woocommerce_product_import_before_import', array( $this, 'set_language' ) );
		add_action( 'woocommerce_product_import_before_process_item', array( $this, 'set_language' ) );

		add_filter( 'woocommerce_product_importer_formatting_callbacks', array( $this, 'formatting_callbacks' ), 10, 2 );
	}

	/**
	 * Add the language and translation group to the default columns.
	 * Hooked to the filter 'woocommerce_csv_product_import_mapping_default_columns'.
	 *
	 * @since 0.8
	 *
	 * @param string[] $mappings Importer columns mappings.
	 * @return string[]
	 */
	public function default_columns( $mappings ) {
		return array_merge(
			$mappings,
			array(
				__( 'Language', 'polylang-wc' )          => 'language',
				__( 'Translation group', 'polylang-wc' ) => 'translations',
			)
		);
	}

	/**
	 * Adds the language and translation group to the mapping options
	 * between "Description" and "Date sale price starts".
	 * Hooked to the filter 'woocommerce_csv_product_import_mapping_options'.
	 *
	 * @since 0.8
	 *
	 * @param string[] $options Mapping options.
	 * @return string[]
	 */
	public function mapping_options( $options ) {
		if ( $n = array_search( 'price', array_keys( $options ) ) ) {
			$end     = array_slice( $options, $n );
			$options = array_slice( $options, 0, $n );
		}

		$options = array_merge(
			$options,
			array(
				'language'     => __( 'Language', 'polylang-wc' ),
				'translations' => __( 'Translation group', 'polylang-wc' ),
			)
		);

		return isset( $end ) ? array_merge( $options, $end ) : $options;
	}

	/**
	 * Imports the language and translation group.
	 * Hooked to the action 'woocommerce_product_import_inserted_product_object'.
	 *
	 * @since 0.8
	 *
	 * @param WC_Product $object Product object.
	 * @param array      $data   Data in a row of the CSV file.
	 * @return void
	 */
	public function inserted_product_object( $object, $data ) {
		$id = $object->get_id();

		if ( isset( $data['language'] ) && PLL()->model->get_language( $data['language'] ) ) {
			if ( isset( $data['translations'] ) ) {
				$this->set_translation_group( $id, $data );
			}

			// Shared slug.
			if ( ! empty( $data['name'] ) ) {
				$object->set_slug( $data['name'] ); // WooCommerce keeps the slug empty in the product object.
				$object->save();
			}
		}
	}

	/**
	 * Assigns the translations group
	 *
	 * @since 0.8
	 *
	 * @param int   $id   Product id.
	 * @param array $data Data in a row of the CSV file.
	 * @return void
	 */
	public function set_translation_group( $id, $data ) {
		$taxonomy = 'post_translations';
		$group = $data['translations'];
		$term = get_term_by( 'name', $group, $taxonomy );

		if ( empty( $term ) ) {
			$translations = array( $data['language'] => $id );
			$term = wp_insert_term( $group, $taxonomy, array( 'description' => serialize( $translations ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
			if ( ! is_wp_error( $term ) ) {
				wp_set_object_terms( $id, $term['term_id'], $taxonomy );
			}
		} elseif ( $term instanceof WP_Term ) {
			$translations = unserialize( $term->description ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
			$translations[ $data['language'] ] = $id;
			$this->data_store->save_translations( $translations );
		}
	}

	/**
	 * Setups filters for the import.
	 * Sets the preferred language when parsing data for terms to be created in the right language.
	 * Hooked to the action 'woocommerce_product_importer_before_set_parsed_data' ( first action available during the import ).
	 *
	 * @since 0.8
	 *
	 * @param array    $row         Row values.
	 * @param string[] $mapped_keys Mapped keys.
	 * @return void
	 */
	public function before_set_parsed_data( $row, $mapped_keys ) {
		// Add filters which must be used only during the import.
		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ), 5 ); // Before Polylang.
		add_filter( 'woocommerce_get_product_id_by_sku', array( $this, 'get_product_id_by_sku' ), 10, 2 );
		add_filter( 'pllwc_language_for_unique_sku', array( $this, 'language_for_unique_sku' ) );

		add_filter( 'pllwc_copy_post_metas', '__return_empty_array', 999 ); // Avoids _children, _crosssell_ids, etc.. to be wrongly overwritten.

		// Preferred language for terms.
		$col = array_search( 'language', $mapped_keys );
		if ( ! empty( $col ) && ! empty( $row[ $col ] ) && $language = PLL()->model->get_language( $row[ $col ] ) ) {
			PLL()->pref_lang = $language;
		}
	}

	/**
	 * Saves the language of the current item being imported for future use.
	 *
	 * @since 0.8
	 *
	 * @param array $data Data in a row of the CSV file.
	 * @return void
	 */
	public function set_language( $data ) {
		if ( isset( $data['language'] ) && $language = PLL()->model->get_language( $data['language'] ) ) {
			PLL()->pref_lang = $language;
		}
	}

	/**
	 * Filters get_terms according to the language of the current item.
	 * This allows get_term_by (slug or name) to return the term in the correct language.
	 * Hooked to the filter 'get_terms_args'.
	 *
	 * @since 0.8
	 *
	 * @param array $args WP_Term_Query arguments.
	 * @return array
	 */
	public function get_terms_args( $args ) {
		if ( ! isset( $args['lang'] ) && ! empty( PLL()->pref_lang ) ) {
			$args['lang'] = PLL()->pref_lang->slug;
		}

		return $args;
	}

	/**
	 * When searching a product id by sku, returns the product id in the current language.
	 * Hooked to the filter 'woocommerce_get_product_id_by_sku'.
	 *
	 * @since 0.9
	 *
	 * @param int    $product_id Product id found by WooCommerce.
	 * @param string $sku        Product SKU.
	 * @return int
	 */
	public function get_product_id_by_sku( $product_id, $sku ) {
		if ( $sku && ! empty( PLL()->pref_lang ) ) {
			$product_id = $this->data_store->get_product_id_by_sku( $sku, PLL()->pref_lang->slug );
		}

		return $product_id;
	}

	/**
	 * Returns the language to use when searching if a sku is unique.
	 * Hooked to the filter 'pllwc_language_for_unique_sku'.
	 *
	 * @since 0.9
	 *
	 * @return PLL_Language|null
	 */
	public function language_for_unique_sku() {
		return PLL()->pref_lang;
	}

	/**
	 * Replace the categories and tags parsing callback by our own callbacks.
	 * Hooked to the filter 'woocommerce_product_importer_formatting_callbacks'.
	 *
	 * @since 1.0.3
	 *
	 * @param callable[]              $callbacks Array of parsing callbacks.
	 * @param WC_Product_CSV_Importer $importer  WC_Product_CSV_Importer object.
	 * @return callable[]
	 */
	public function formatting_callbacks( $callbacks, $importer ) {
		$this->importer = $importer;

		if ( false !== $key = array_search( 'category_ids', $importer->get_mapped_keys() ) ) {
			$callbacks[ $key ] = array( $this, 'parse_categories_field' );
		}

		if ( false !== $key = array_search( 'tag_ids', $importer->get_mapped_keys() ) ) {
			$callbacks[ $key ] = array( $this, 'parse_tags_field' );
		}

		return $callbacks;
	}

	/**
	 * Parse a category field from a CSV.
	 * Categories are separated by commas and subcategories are "parent > subcategory".
	 *
	 * @since 1.0.3
	 *
	 * @param string $value Field value.
	 * @return array of arrays with "parent" and "name" keys.
	 */
	public function parse_categories_field( $value ) {
		if ( ! empty( PLL()->pref_lang ) ) {
			// Worst hack ever, for shared slug.
			$_POST['term_lang_choice'] = PLL()->pref_lang->slug;
			$_REQUEST['_pll_nonce'] = wp_create_nonce( 'pll_language' );
		}

		return $this->importer->parse_categories_field( $value );
	}

	/**
	 * Parse a tag field from a CSV.
	 *
	 * @since 1.0.3
	 *
	 * @param string $value Field value.
	 * @return array
	 */
	public function parse_tags_field( $value ) {
		// Worst hack ever, for shared slug.
		if ( ! empty( PLL()->pref_lang ) ) {
			$_POST['term_lang_choice'] = PLL()->pref_lang->slug;
			$_REQUEST['_pll_nonce'] = wp_create_nonce( 'pll_language' );
		}

		return $this->importer->parse_tags_field( $value );
	}
}
