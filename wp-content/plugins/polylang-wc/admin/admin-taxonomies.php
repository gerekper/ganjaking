<?php
/**
 * @package Polylang-WC
 */

/**
 * Handles the Woocommerce taxonomies on admin side.
 *
 * @since 0.1
 */
class PLLWC_Admin_Taxonomies {

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 11 ); // After Woocommerce.
	}

	/**
	 * Setups actions and filters.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'pll_copy_term_metas', array( $this, 'get_metas_to_copy' ), 10, 5 );
		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ), 5 ); // Before Polylang.

		if ( PLL()->options['media_support'] ) {
			add_filter( 'pll_translate_term_meta', array( $this, 'translate_meta' ), 10, 3 );

			// WooCommerce ( verified in 2.5.5 ) inconsistently uses created_term and edit_term so we can't use pll_save_term.
			add_action( 'created_product_cat', array( $this, 'fix_term_thumbnail' ), 999 );
			add_action( 'edited_product_cat', array( $this, 'fix_term_thumbnail' ), 999 );
		}

		// Attributes.
		add_action( 'create_term', array( $this, 'create_attribute_term' ), 10, 3 );

		/*
		 * Workaround WooComerce not providing access its WC_Admin_Taxonomies object.
		 * This is possible since WC 3.6 with WC_Admin_Taxonomies::get_instance().
		 * It would be better if filters could allow to pre-populate term meta the same way 'taxonomy_parent_dropdown_args' does.
		 */
		pll_remove_anonymous_object_filter( 'product_cat_add_form_fields', array( 'WC_Admin_Taxonomies', 'add_category_fields' ) );
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Adds term metas to copy or synchronize.
	 *
	 * @since 1.0
	 *
	 * @param string[] $to_copy List of term metas names.
	 * @param bool     $sync    True if it is synchronization, false if it is a copy.
	 * @param int      $from    Id of the term from which we copy informations.
	 * @param int      $to      Id of the term to which we paste informations.
	 * @param string   $lang    Language slug.
	 * @return string[]
	 */
	public function get_metas_to_copy( $to_copy, $sync, $from, $to, $lang ) {
		$term = get_term( $from );

		// Product categories.
		if ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) {
			$_to_copy = array(
				'display_type',
				'thumbnail_id',
			);

			if ( ! $sync ) {
				$_to_copy[] = 'order';
			}

			$to_copy = array_merge( $to_copy, $_to_copy );
		}

		// Add attributes order.
		if ( $term instanceof WP_Term && ! $sync && 0 === strpos( $term->taxonomy, 'pa_' ) ) {
			$metas = get_term_meta( $from );

			if ( ! empty( $metas ) ) {
				foreach ( array_keys( $metas ) as $key ) {
					if ( 0 === strpos( (string) $key, 'order_' ) ) {
						$to_copy[] = $key;
					}
				}
			}
		}

		/**
		 * Filters the term metas to copy or synchronize.
		 *
		 * @since 0.7
		 *
		 * @param string[] $to_copy list of custom fields names.
		 * @param bool     $sync    true if it is synchronization, false if it is a copy.
		 * @param int      $from    id of the term from which we copy informations.
		 * @param int      $to      id of the term to which we paste informations.
		 * @param string   $lang    language slug.
		 */
		return apply_filters( 'pllwc_copy_term_metas', $to_copy, $sync, $from, $to, $lang );
	}

	/**
	 * Suppresses the language filter in _get_term_hierarchy() specifically for product_cat
	 * because WC modifies the orderby arg to meta_value_num in wc_change_pre_get_terms().
	 *
	 * @see PLL_CRUD_Terms::get_terms_args()
	 *
	 * @since 1.2.1
	 *
	 * @param array $args WP_Term_Query arguments.
	 * @return array Modified arguments
	 */
	public function get_terms_args( $args ) {
		if ( 'all' === $args['get'] && 'meta_value_num' === $args['orderby'] && 'id=>parent' === $args['fields'] ) {
			$args['lang'] = '';
		}
		return $args;
	}

	/**
	 * Translates the thumbnail id.
	 *
	 * @since 1.0
	 *
	 * @param mixed  $value Meta value.
	 * @param string $key   Meta key.
	 * @param string $lang  Language of target.
	 * @return mixed
	 */
	public function translate_meta( $value, $key, $lang ) {
		if ( 'thumbnail_id' === $key && is_numeric( $value ) && ! empty( $value ) ) {
			$tr_value = pll_get_post( (int) $value, $lang );
			$value = $tr_value ? $tr_value : $value;
		}
		return $value;
	}

	/**
	 * Maybe fix the language of the product cat image.
	 * It is required because if the image was just uploaded,
	 * it is assigned the preferred language instead of the current language.
	 *
	 * @since 0.1
	 *
	 * @param int $term_id Term id.
	 * @return void
	 */
	public function fix_term_thumbnail( $term_id ) {
		$thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );
		$thumbnail_id = is_numeric( $thumbnail_id ) ? (int) $thumbnail_id : 0;

		$lang = pll_get_term_language( $term_id );

		if ( $thumbnail_id && $lang && pll_get_post_language( $thumbnail_id ) !== $lang ) {
			$translations = pll_get_post_translations( $thumbnail_id );

			if ( ! empty( $translations[ $lang ] ) ) {
				update_term_meta( $term_id, 'thumbnail_id', $translations[ $lang ] ); // Take the translation in the right language.
			} else {
				pll_set_post_language( $thumbnail_id, $lang ); // Or fix the language.
			}
		}
	}

	/**
	 * Saves the language of an attribute term when created from the product metabox.
	 *
	 * @since 1.0
	 *
	 * @param int    $term_id  Term id.
	 * @param int    $tt_id    Term taxonomy id.
	 * @param string $taxonomy Taxonomy name.
	 * @return void
	 */
	public function create_attribute_term( $term_id, $tt_id, $taxonomy ) {
		if ( doing_action( 'wp_ajax_woocommerce_add_new_attribute' ) && ! empty( $_POST['pll_post_id'] ) && 0 === strpos( $taxonomy, 'pa_' ) ) {
			check_ajax_referer( 'add-attribute', 'security' );

			/** @var PLLWC_Product_Language_CPT */
			$data_store = PLLWC_Data_Store::load( 'product_language' );
			$lang = $data_store->get_language( (int) $_POST['pll_post_id'] );
			if ( $lang ) {
				pll_set_term_language( $term_id, $lang );
			}
		}
	}

	/**
	 * Rewrites WC_Admin_Taxonomies::add_category_fields to populate the metas when creating a new translation.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function add_category_fields() {
		if ( isset( $_GET['taxonomy'], $_GET['from_tag'], $_GET['new_lang'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification
			$term = get_term( (int) $_GET['from_tag'], 'product_cat' );  // phpcs:ignore WordPress.Security.NonceVerification
		}

		if ( ! empty( $term ) ) {
			WC_Admin_Taxonomies::get_instance()->edit_category_fields( $term );
		} else {
			WC_Admin_Taxonomies::get_instance()->add_category_fields();
		}
	}

	/**
	 * Filters the media list when adding an image to a product category.
	 *
	 * @since 1.6
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( ! empty( $screen ) && in_array( $screen->base, array( 'edit-tags', 'term' ) ) && 'product_cat' === $screen->taxonomy ) {
			$this->load_scripts();
		}
	}

	/**
	 * Enqueues filter script.
	 *
	 * @since 1.7.2
	 *
	 * @return void
	 */
	public function load_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'pllwc_product_cat', plugins_url( '/js/build/filter-media-taxonomy' . $suffix . '.js', PLLWC_FILE ), array( 'jquery' ), PLLWC_VERSION, true );
	}
}
