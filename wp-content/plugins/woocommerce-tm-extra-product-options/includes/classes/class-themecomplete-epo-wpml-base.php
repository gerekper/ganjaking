<?php
/**
 * Extra Product Options WPML class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options WPML class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_WPML_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_WPML_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * If WPML is active
	 *
	 * @var boolean
	 */
	private $is_wpml = false;

	/**
	 * The $sitepress object from WPML
	 *
	 * @var object|boolean
	 */
	private $sitepress = false;

	/**
	 * The post type for our language meta box
	 *
	 * @var string|boolean
	 */
	private $basetype = false;

	/**
	 * The post type hook for our language meta box
	 *
	 * @var string|boolean
	 */
	private $basetypehook = false;

	/**
	 * Flag to indicate the removal of sql filters
	 *
	 * @var integer
	 */
	public $remove_sql_filters_done = 0;

	/**
	 * Flag to indicate the removal of term filters
	 *
	 * @var integer
	 */
	public $remove_term_filters_done = 0;

	/**
	 * Flag to indicate the removal of the get_terms_args filter
	 *
	 * @var integer
	 */
	public $remove_get_terms_args_done = 0;

	/**
	 * Flag to indicate the removal of the get_term filter
	 *
	 * @var integer
	 */
	public $remove_get_term_done = 0;

	/**
	 * Flag to indicate the removal of the terms_clauses filter
	 *
	 * @var integer
	 */
	public $remove_terms_clauses_done = 0;

	/**
	 * The post ID to use in the taxonomy terms checklist arguments
	 * to mark category IDs to mark as checked
	 *
	 * @var integer
	 */
	public $tmparentpostid_for_filter = 0;

	/**
	 * Flag to indicate the removal of the posts_where filter
	 *
	 * @var integer
	 */
	public $removed_posts_filter = 0;

	/**
	 * Cache for is_original_product function
	 *
	 * @var array
	 */
	public $is_original_cache = [];

	/**
	 * Flag to indicate the removal of the get_terms filter
	 *
	 * @var integer
	 */
	public $remove_get_terms_done = 0;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		if ( class_exists( 'SitePress' ) ) {
			$this->is_wpml = true;
			global $sitepress;
			$this->sitepress = $sitepress;
		}
	}

	/**
	 * Check is WPML is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->is_wpml;
	}

	/**
	 * Check is WPML Multi Currency is active
	 *
	 * @return bool
	 */
	public function is_multi_currency() {
		global $woocommerce_wpml;
		$this_is_wpml                = $this->is_active();
		$is_wpml_multi_currency_old  = $this_is_wpml && $woocommerce_wpml && property_exists( $woocommerce_wpml, 'multi_currency' ) && $woocommerce_wpml->multi_currency;
		$is_wpml_multi_currency      = $this_is_wpml && $woocommerce_wpml && property_exists( $woocommerce_wpml, 'settings' ) && WCML_MULTI_CURRENCIES_INDEPENDENT === $woocommerce_wpml->settings['enable_multi_currency'];
		$this_is_wpml_multi_currency = $is_wpml_multi_currency_old || $is_wpml_multi_currency;

		return $this_is_wpml_multi_currency;
	}

	/**
	 * Returns WPML instance
	 */
	public function sitepress_instance() {
		return $this->sitepress;
	}

	/**
	 * Gets a flag image tag
	 *
	 * @param string  $lang The language code.
	 * @param integer $echo If to return or print the result.
	 *
	 * @return mixed
	 */
	public function get_flag( $lang = 'all', $echo = 0 ) {
		ob_start();

		if ( $this->is_wpml ) {
			$url = $this->get_flag_url( $lang );
			echo '<img src="' . esc_url( $url ) . '"/>';
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Returns the url of a flag image
	 *
	 * @param string $lang The language code.
	 * @since 1.0
	 */
	public function get_flag_url( $lang = 'all' ) {
		$url = '';
		if ( $this->is_wpml ) {
			if ( empty( $lang ) ) {
				$url = $this->sitepress->get_flag_url( $this->get_default_lang() );
			} elseif ( empty( $lang ) || 'all' === $lang ) {
				$url = ICL_PLUGIN_URL . '/res/img/icon.png';
			} else {
				$url = $this->sitepress->get_flag_url( $lang );
			}
			if ( empty( $url ) ) {
				$url = ICL_PLUGIN_URL . '/res/img/icon.png';
			}
		}

		return $url;
	}

	/**
	 * Get original post id
	 *
	 * @param integer      $id The product id.
	 * @param string       $post_type The post type.
	 * @param string|false $basetype The post type to check against for the given $id.
	 * @since 1.0
	 */
	public function get_original_id( $id = 0, $post_type = 'product', $basetype = false ) {

		if ( false === $basetype ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE' ) && THEMECOMPLETE_ECO_GLOBAL_POST_TYPE === $post_type ) {
				$basetype = THEMECOMPLETE_ECO_GLOBAL_POST_TYPE;
			}
		}
		if ( $this->is_wpml ) {
			$check_post = get_post( $id );
			if ( $check_post && property_exists( $check_post, 'ID' ) && property_exists( $check_post, 'post_type' ) ) {
				if ( ( 'auto-draft' === $check_post->post_status && 'AUTO-DRAFT' === $check_post->post_title ) || ! ( 'product' === $check_post->post_type || $check_post->post_type === $basetype ) ) {
					return (float) $id;
				}
			}
			if ( 'product' === $post_type ) {
				$trid = 0;
				if ( $id ) {
					global $wpdb;
					$res = $this->sitepress->get_element_language_details( $id, 'post_' . $post_type );
					if ( isset( $res->trid ) ) {
						$trid = (int) $res->trid;
					}
					if ( $trid ) {
						$element_lang_code = $res->language_code;
					} else {
						$translation_id    = $this->sitepress->set_element_language_details( $id, 'post_' . $post_type, null, $this->get_lang() );
						$trid              = $wpdb->get_var( $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE translation_id = %d", [ $translation_id ] ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$element_lang_code = $this->get_lang();
					}
				} else {
					$trid              = isset( $_GET['trid'] ) ? (int) $_GET['trid'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$element_lang_code = isset( $_GET['lang'] ) ? wp_strip_all_tags( wp_unslash( $_GET['lang'] ) ) : $this->get_lang(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				$translations = [];
				if ( $trid ) {
					$translations = $this->sitepress->get_element_translations( $trid, 'post_' . $post_type );
				}
				foreach ( $translations as $key => $value ) {
					if ( null === $value->source_language_code ) {
						return (float) $value->element_id;
					}
				}

				return (float) icl_object_id( $id, 'any', true, $this->get_default_lang() );
			} elseif ( $post_type === $basetype ) {
				if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					&& ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				) {
					return (float) $_GET['tmparentpostid']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} else {
					$tm_meta_parent_post_id = themecomplete_get_post_meta( $id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true );
					if ( $tm_meta_parent_post_id && (float) $tm_meta_parent_post_id !== (float) $id ) {
						return $tm_meta_parent_post_id;
					}

					return (float) $id;
				}
			}
		} else {
			return (float) $id;
		}
	}

	/**
	 * Get product post id of current lang or $lang
	 *
	 * @param integer      $id The product id.
	 * @param string       $post_type The post type.
	 * @param string       $lang The language code.
	 * @param string|false $basetype The post type to check against for the given $id.
	 * @since 1.0
	 */
	public function get_current_id( $id = 0, $post_type = 'product', $lang = null, $basetype = false ) {
		if ( false === $basetype ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE' ) && THEMECOMPLETE_ECO_GLOBAL_POST_TYPE === $post_type ) {
				$basetype = THEMECOMPLETE_ECO_GLOBAL_POST_TYPE;
			}
		}
		if ( $this->is_wpml ) {
			if ( null === $lang ) {
				$lang = $this->get_lang();
			}
			$check_post = get_post( $id );
			if ( $check_post && property_exists( $check_post, 'ID' ) && property_exists( $check_post, 'post_type' ) ) {
				if ( ! ( 'product' === $check_post->post_type || $check_post->post_type === $basetype ) ) {
					return $id;
				}
			}
			if ( 'product' === $post_type ) {

				if ( $id ) {
					global $wpdb;
					$trid = 0;
					$res  = $this->sitepress->get_element_language_details( $id, 'post_' . $post_type );
					if ( $res && property_exists( $res, 'trid' ) ) {
						$trid = (int) $res->trid;
					}
					if ( $trid ) {
						$element_lang_code = $res->language_code;
					} else {
						$translation_id    = $this->sitepress->set_element_language_details( $id, 'post_' . $post_type, null, $lang );
						$trid              = $wpdb->get_var( $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE translation_id = %d", [ $translation_id ] ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$element_lang_code = $lang;
					}
				} else {
					$trid              = isset( $_GET['trid'] ) ? (int) $_GET['trid'] : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$element_lang_code = isset( $_GET['lang'] ) ? wp_strip_all_tags( wp_unslash( $_GET['lang'] ) ) : $lang; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				$translations = [];
				if ( $trid ) {
					$translations = $this->sitepress->get_element_translations( $trid, 'post_' . $post_type );
				}
				if ( isset( $translations[ $lang ] ) ) {
					return $translations[ $lang ]->element_id;
				}

				return icl_object_id( $id, 'any', false, $lang );
			} else {
				return $id;
			}
		} else {
			return $id;
		}
	}

	/**
	 * Check if original product
	 *
	 * @param integer      $product_id The product id.
	 * @param string       $post_type The post type.
	 * @param string|false $basetype The post type to check against for the given $id.
	 * @since 1.0
	 */
	public function is_original_product( $product_id, $post_type = 'product', $basetype = false ) {
		$product_id = absint( $product_id );
		if ( false === $basetype ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE' ) && THEMECOMPLETE_ECO_GLOBAL_POST_TYPE === $post_type ) {
				$basetype = THEMECOMPLETE_ECO_GLOBAL_POST_TYPE;
			}
		}

		if ( ! isset( $this->is_original_cache[ $product_id ] ) ) {
			$this->is_original_cache[ $product_id ] = 0;
		}

		if ( 0 !== $this->is_original_cache[ $product_id ] ) {
			return $this->is_original_cache[ $product_id ];
		}

		if ( $this->is_wpml ) {
			global $wpdb;
			if ( 'product' === $post_type ) {
				$is_original = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT source_language_code IS NULL FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_product'", $product_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			} elseif ( $post_type === $basetype ) {
				if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					&& ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				) {
					$is_original = false;
				} else {
					$tm_meta_parent_post_id = absint( themecomplete_get_post_meta( $product_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true ) );
					if ( $tm_meta_parent_post_id && $tm_meta_parent_post_id !== $product_id ) {
						$is_original = false;
					} else {
						$is_original = true;
					}
				}
			}
			if ( ! isset( $is_original ) ) {
				$is_original = true;
			}

			$this->is_original_cache[ $product_id ] = $is_original;

			return $is_original;
		} else {
			$this->is_original_cache[ $product_id ] = true;
			return true;
		}
	}

	/**
	 * Remove sql filters
	 *
	 * @since 1.0
	 */
	public function remove_sql_filter() {
		if ( $this->is_wpml ) {
			remove_action( 'parse_query', [ $this->sitepress, 'parse_query' ] );
			$this->remove_sql_filters_done = 1;
		}
	}

	/**
	 * Restore sql filters
	 *
	 * @since 1.0
	 */
	public function restore_sql_filter() {
		if ( $this->is_wpml ) {
			// restore WPML term filters.
			add_action( 'parse_query', [ $this->sitepress, 'parse_query' ] );
			$this->remove_sql_filters_done = 0;
		}
	}

	/**
	 * Removes WPML term filters
	 *
	 * @since 1.0
	 */
	public function remove_term_filters() {
		if ( $this->is_wpml ) {
			// remove WPML term filters.
			if ( false !== has_filter( 'get_terms_args', [ $this->sitepress, 'get_terms_args_filter' ] ) ) {
				remove_filter( 'get_terms_args', [ $this->sitepress, 'get_terms_args_filter' ] );
				$this->remove_get_terms_args_done = 1;
			}
			if ( false !== has_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ] ) ) {
				remove_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1 );
				$this->remove_get_terms_done = 1;
			}
			if ( false !== has_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ] ) ) {
				remove_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ] );
				$this->remove_terms_clauses_done = 1;
			}
			$this->remove_term_filters_done = 1;
		}
	}

	/**
	 * Restores WPML term filters
	 *
	 * @since 1.0
	 */
	public function restore_term_filters() {
		if ( $this->is_wpml ) {
			// restore WPML term filters.
			if ( 1 === $this->remove_get_terms_args_done ) {
				add_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ], 10, 4 );
				$this->remove_get_terms_args_done = 0;
			}
			if ( 1 === $this->remove_get_terms_done ) {
				add_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1, 1 );
				$this->remove_get_terms_done = 0;
			}
			if ( 1 === $this->remove_terms_clauses_done ) {
				add_filter( 'get_terms_args', [ $this->sitepress, 'get_terms_args_filter' ], 10, 2 );
				$this->remove_terms_clauses_done = 0;
			}
			$this->remove_term_filters_done = 0;
		}
	}

	/**
	 * Applies the 'wp_terms_checklist_args' filter
	 *
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function apply_wp_terms_checklist_args_filter( $post_id ) {
		if ( $this->is_wpml ) {
			$this->tmparentpostid_for_filter = $post_id;
			add_filter( 'wp_terms_checklist_args', [ $this, 'wp_terms_checklist_args_filter' ], 10, 2 );
		}
	}

	/**
	 * Wp_terms_checklist_args filter
	 *
	 * @param array   $args Array of arguments.
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function wp_terms_checklist_args_filter( $args, $post_id ) {
		if ( $this->is_wpml ) {
			$args['selected_cats'] = wp_get_object_terms( $this->tmparentpostid_for_filter, 'product_cat', array_merge( $args, [ 'fields' => 'ids' ] ) );
			foreach ( $args['selected_cats'] as $key => $term ) {
				$args['selected_cats'][ $key ] = apply_filters( 'translate_object_id', $term, 'product_cat', false );
			}
		}

		return $args;
	}

	/**
	 * Applies the 'request' filter
	 *
	 * @since 1.0
	 */
	public function apply_query_filter() {
		if ( $this->is_wpml && 'all' !== $this->get_lang() ) {
			add_filter( 'request', [ $this, 'request_filter' ] );
			global $wpml_query_filter;
			if ( has_filter( 'posts_where', [ $wpml_query_filter, 'posts_where_filter' ] ) ) {
				remove_filter( 'posts_where', [ $wpml_query_filter, 'posts_where_filter' ], 10, 2 );
				$this->removed_posts_filter = 1;
			}
		}
	}

	/**
	 * Removes the 'request' filter
	 *
	 * @since 1.0
	 */
	public function remove_query_filter() {
		if ( $this->is_wpml && 'all' !== $this->get_lang() ) {
			remove_filter( 'request', [ $this, 'request_filter' ] );
			if ( $this->removed_posts_filter ) {
				global $wpml_query_filter;
				add_filter( 'posts_where', [ $wpml_query_filter, 'posts_where_filter' ], 10, 2 );
			}
		}
	}

	/**
	 * 'request' filter: adds meta args to query_vars
	 *
	 * @param array $query_vars Array of request query variables.
	 * @since 1.0
	 */
	public function request_filter( $query_vars ) {
		if ( $this->is_wpml && 'all' !== $this->get_lang() ) {
			if ( $this->get_lang() !== $this->get_default_lang() ) {
				$query_vars['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $this->get_lang(), '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
			} else {
				$query_vars['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', THEMECOMPLETE_EPO_WPML_LANG_META, $this->get_lang(), '=', 'NOT EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
			}
		}

		return $query_vars;
	}

	/**
	 * Order terms
	 *
	 * @param array $t1 Current terms.
	 * @param array $t2 Default terms.
	 * @since 1.0
	 */
	public function order_terms( $t1, $t2 ) {
		if ( ! $this->is_wpml ) {
			return $t1;
		}
		$d      = [];
		$o      = [];
		$new_t1 = [];
		foreach ( $t1 as $key => $value ) {
			$o[ $value->term_id ] = $value;
		}
		foreach ( $t2 as $key => $value ) {
			$d[ $value->term_id ] = $value;
		}

		foreach ( $d as $key => $value ) {
			if ( isset( $o[ $key ] ) ) {
				$new_t1[] = $o[ $key ];
			}
		}

		return $new_t1;
	}

	/**
	 * Merge terms
	 *
	 * @param array $t1 Current terms.
	 * @param array $t2 Default terms.
	 * @since 1.0
	 */
	public function merge_terms( $t1, $t2 ) {
		if ( ! $this->is_wpml ) {
			return $t1;
		}
		$d = [];
		$o = [];
		foreach ( $t1 as $key => $value ) {
			$o[ $value->trid ] = $value;
		}
		foreach ( $t2 as $key => $value ) {
			$d[ $value->trid ] = $value;
		}

		foreach ( $d as $key => $value ) {
			if ( isset( $o[ $key ] ) ) {
				$d[ $key ] = $o[ $key ];
			}
		}

		return $d;
	}

	/**
	 * Merge term slugs
	 *
	 * @param array $t1 Current terms.
	 * @param array $t2 Default terms.
	 * @since 1.0
	 */
	public function merge_terms_slugs( $t1, $t2 ) {
		$t2_slug = [];
		if ( ! $this->is_wpml ) {
			foreach ( $t1 as $key => $value ) {
				$o[ $value->trid ] = $value;
			}
			foreach ( $o as $trid => $term ) {
				$t2_slug[ $term->slug ] = $term->slug;
			}

			return $t2_slug;
		}
		$d       = [];
		$o       = [];
		$t1_slug = [];

		foreach ( $t1 as $key => $value ) {
			$o[ $value->trid ] = $value;
		}
		foreach ( $t2 as $key => $value ) {
			$d[ $value->trid ] = $value;
		}

		foreach ( $o as $trid => $term ) {
			$t1_slug[ $trid ] = $term->slug;
		}

		foreach ( $d as $trid => $term ) {
			if ( isset( $t1_slug[ $trid ] ) ) {
				$t2_slug[ $t1_slug[ $trid ] ] = $term->slug;
			} else {
				$t2_slug[ $term->slug ] = $term->slug;
			}
		}

		return $t2_slug;
	}

	/**
	 * Get taxonomy terms without WPML filters if lang is null (use to get all terms for all languages)
	 *
	 * @param string|null $lang The language code.
	 * @param string      $taxonomy The taxonomy.
	 * @param array       $args Array of arguments.
	 * @param integer     $post_id The post id.
	 * @since 1.0
	 */
	public function get_terms( $lang = null, $taxonomy = '', $args = [], $post_id = 0 ) {
		if ( ! $this->is_wpml ) {
			if ( ! empty( $post_id ) ) {
				$terms     = [];
				$all_terms = get_terms( $taxonomy, $args );
				foreach ( $all_terms as $term ) {
					if ( has_term( absint( $term->term_id ), $taxonomy, $post_id ) ) {
						$terms[] = $term;
					}
				}

				return $terms;
			} else {
				return get_terms( $taxonomy, $args );
			}
		}
		if ( null === $lang ) {
			$this->remove_term_filters();

			$all_terms = get_terms( $taxonomy, $args );
			if ( ! empty( $post_id ) ) {
				$terms = [];
				foreach ( $all_terms as $term ) {
					if ( has_term( absint( $term->term_id ), $taxonomy, $post_id ) ) {
						$terms[] = $term;
					}
				}
			} else {
				$terms = $all_terms;
			}
			$this->restore_term_filters();
		} else {
			$terms      = [];
			$terms_data = new WPML_Taxonomy_Translation_Screen_Data( $this->sitepress, $taxonomy );
			$terms_data = $terms_data->terms();
			if ( isset( $terms_data['terms'] ) ) {
				$terms_data = $terms_data['terms'];
			}

			foreach ( $terms_data as $key => $value ) {
				if ( isset( $value[ $lang ] ) ) {
					if ( ! empty( $post_id ) ) {
						if ( has_term( absint( $value[ $lang ]->term_id ), $taxonomy, $post_id ) ) {
							$terms[] = $value[ $lang ];
						}
					} else {
						$terms[] = $value[ $lang ];
					}
				}
			}
		}

		return $terms;
	}

	/**
	 * Sets WPML active language
	 *
	 * @param string $lang The language code.
	 * @since 1.0
	 */
	public function set_lang( $lang = '' ) {
		if ( $lang && $this->is_wpml ) {
			$this->sitepress->switch_lang( $lang );
		}
	}

	/**
	 * Gets WPML current displayed language
	 *
	 * @since 1.0
	 */
	public function get_lang() {
		if ( $this->is_wpml ) {
			return $this->sitepress->get_current_language();
		}

		return 'all';
	}

	/**
	 * Gets WPML default language
	 *
	 * @since 1.0
	 */
	public function get_default_lang() {

		if ( $this->is_wpml ) {
			return $this->sitepress->get_default_language();
		}

		return false;
	}

	/**
	 * Returns all WPML languages
	 *
	 * @since 1.0
	 */
	public function get_active_languages() {
		if ( $this->is_wpml ) {
			return $this->sitepress->get_active_languages();
		}

		return false;
	}

	/**
	 * Sets WPML current language depending on displayed global epo
	 *
	 * @param string|false $basetype The post type for our language meta box.
	 * @since 1.0
	 */
	public function set_post_lang( $basetype = false ) {
		if ( false === $basetype ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
		}
		if ( $this->is_wpml ) {
			remove_action( 'admin_footer', [ $this->sitepress, 'language_filter' ] );
			remove_action( 'admin_enqueue_scripts', [ $this->sitepress, 'language_filter' ] );

			$post_id = false;
			if (
				( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&&
				( isset( $_REQUEST['post'] ) || isset( $_POST['post_ID'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification
			) {

				if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_id = (int) $_GET['post']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} elseif ( isset( $_POST['post_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$post_id = (int) $_POST['post_ID']; // phpcs:ignore WordPress.Security.NonceVerification
				}

				if ( ! empty( $post_id ) ) {

					$meta_lang              = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
					$tm_meta_parent_post_id = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true );

					if ( (
							( empty( $tm_meta_parent_post_id ) && empty( $meta_lang ) )
							||
							( ! empty( $tm_meta_parent_post_id ) && ! empty( $meta_lang ) )
						)
						&& ! empty( $_GET['lang'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					) {

						if ( $_GET['lang'] !== $meta_lang ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

							$url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : home_url();
							$url = remove_query_arg( [ 'post', 'lang' ], $url );

							$args                 = [
								'post_type'   => $basetype,
								'post_status' => [ 'publish' ], // get only enabled global extra options.
								'numberposts' => -1,
								'orderby'     => 'date',
								'order'       => 'asc',
								'meta_query'  => THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, sanitize_text_field( wp_unslash( $_GET['lang'] ) ), '=', 'EXISTS' ), // phpcs:ignore WordPress.DB.SlowDBQuery, WordPress.Security.NonceVerification.Recommended
							];
							$args['meta_query'][] = [
								'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
								'value'   => $tm_meta_parent_post_id,
								'compare' => '=',
							];
							$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
							if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {// has $key code translation.
								$tm_meta_parent_post_id = $other_translations[0]->ID;
								$url                    = add_query_arg( 'post', $tm_meta_parent_post_id, $url );
							} else {
								$url = remove_query_arg( [ 'action', 'tmparentpostid', 'tmaddlang' ], $url );
								if ( empty( $tm_meta_parent_post_id ) ) {
									$tm_meta_parent_post_id = $post_id;
								}
								$url = add_query_arg(
									[
										'action'         => 'add',
										'tmparentpostid' => $tm_meta_parent_post_id,
										'tmaddlang'      => sanitize_text_field( wp_unslash( $_GET['lang'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									],
									$url
								);
							}
							$url = esc_url_raw( $url );
							wp_safe_redirect( $url );
							exit;
						}
					}
					if ( empty( $meta_lang ) ) {
						$this->set_lang( $this->get_default_lang() );
					} else {
						$this->set_lang( $meta_lang );
					}
				}
			} elseif ( ! empty( $_GET['tmparentpostid'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					&& ! empty( $_GET['tmaddlang'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					&& ( isset( $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					&& 'add' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {

				$args = [
					'post_type'   => $basetype,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
				];

				if ( ! empty( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$url = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
					$url = remove_query_arg( [ 'tmaddlang', 'lang' ], $url );
					$url = add_query_arg(
						[
							'tmaddlang' => sanitize_text_field( wp_unslash( $_GET['lang'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						],
						$url
					);

					$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, sanitize_text_field( wp_unslash( $_GET['lang'] ) ), '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery, WordPress.Security.NonceVerification.Recommended
					$args['meta_query'][] = [
						'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
						'value'   => sanitize_text_field( wp_unslash( $_GET['tmparentpostid'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						'compare' => '=',
					];
					$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
					if ( ! empty( $other_translations ) ) {
						$url = remove_query_arg( [ 'action', 'tmaddlang', 'tmparentpostid' ], $url );
						$url = add_query_arg(
							[
								'action' => 'edit',
								'post'   => $other_translations[0]->ID,
								'lang'   => sanitize_text_field( wp_unslash( $_GET['lang'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							],
							$url
						);
					}
					$url = esc_url_raw( $url );
					wp_safe_redirect( $url );
					exit;
				}

				$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, sanitize_text_field( wp_unslash( $_GET['tmaddlang'] ) ), '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery, WordPress.Security.NonceVerification.Recommended
				$args['meta_query'][] = [
					'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
					'value'   => sanitize_text_field( wp_unslash( $_GET['tmparentpostid'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'compare' => '=',
				];
				$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
				if ( ! empty( $other_translations ) ) {
					$url = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
					$url = remove_query_arg( [ 'action', 'tmaddlang', 'tmparentpostid' ], $url );
					$url = add_query_arg(
						[
							'action' => 'edit',
							'post'   => $other_translations[0]->ID,
							'lang'   => sanitize_text_field( wp_unslash( $_GET['tmaddlang'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						],
						$url
					);
					$url = esc_url_raw( $url );
					wp_safe_redirect( $url );
					exit;
				}

				$this->set_lang( sanitize_text_field( wp_unslash( $_GET['tmaddlang'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			} elseif ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ! empty( $_GET['lang'] ) && 'all' === $_GET['lang'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$url = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
					$url = remove_query_arg( [ 'lang' ], $url );
					$url = add_query_arg(
						[
							'lang' => $this->get_default_lang(),
						],
						$url
					);
					$url = esc_url_raw( $url );
					wp_safe_redirect( $url );
					exit;
				}
			}
		}
	}

	/**
	 * Returns add global epo link
	 *
	 * @param integer      $post_id The current post id.
	 * @param string       $lang The language code.
	 * @param array        $v Language data array from WPML.
	 * @param string|false $basetypehook The post type hook for our language meta box.
	 * @param integer      $echo If the result should be returned or printed.
	 * @since 1.0
	 */
	public function add_lang_link( $post_id, $lang, $v, $basetypehook = false, $echo = 0 ) {

		ob_start();
		if ( false === $basetypehook ) {
			$basetypehook = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		}

		$post_new_file = apply_filters( 'wc_epo_add_lang_link', 'edit.php?post_type=product&page=' . $basetypehook . '&action=add', $basetypehook );

		$post_new_file = admin_url( $post_new_file );

		if ( $this->is_wpml ) {
			/* translators: %s Language */
			$alt           = sprintf( esc_html__( 'Add translation to %s', 'sitepress' ), $v['display_name'] );
			$post_new_file = add_query_arg(
				[
					'tmparentpostid' => $post_id,
					'tmaddlang'      => $lang,
				],
				$post_new_file
			);
			echo '<a title="' . esc_attr( $alt ) . '" alt="' . esc_attr( wp_strip_all_tags( $alt ) ) . '" class="tmwpmllink" href="' . esc_url( $post_new_file ) . '"><i class="tcfa tcfa-plus"></i></a>';
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

		return $post_new_file;
	}

	/**
	 * Returns edit global epo link
	 *
	 * @param integer      $post_id The current post id.
	 * @param string       $lang The language code.
	 * @param array        $v Language data array from WPML.
	 * @param integer      $main_post_id The main post id..
	 * @param boolean      $noadd Add the url link arguments or not.
	 * @param string|false $basetypehook The post type hook for our language meta box.
	 * @param integer      $echo If the result should be returned or printed.
	 * @since 1.0
	 */
	public function edit_lang_link( $post_id, $lang, $v, $main_post_id, $noadd = false, $basetypehook = false, $echo = 0 ) {

		ob_start();

		if ( false === $basetypehook ) {
			$basetypehook = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		}

		$post_new_file = apply_filters( 'wc_epo_edit_lang_link', 'edit.php?post_type=product&page=' . $basetypehook . '&action=edit&post=' . $post_id );

		if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE_PAGE_HOOK' ) && THEMECOMPLETE_ECO_GLOBAL_POST_TYPE_PAGE_HOOK === $basetypehook ) {
			$post_new_file = 'admin.php?bpost_type=product&page=' . $basetypehook . '&action=edit&post=' . $post_id;
		}

		$post_new_file = admin_url( $post_new_file );
		if ( $this->is_wpml ) {
			/* translators: %s Language */
			$alt = sprintf( esc_html__( 'Edit the %s translation', 'sitepress' ), $v['display_name'] );
			if ( empty( $noadd ) ) {
				$post_new_file = add_query_arg(
					[
						'tmparentpostid' => $main_post_id,
						'tmaddlang'      => $lang,
					],
					$post_new_file
				);
			}
			echo '<a title="' . esc_attr( $alt ) . '" alt="' . esc_attr( wp_strip_all_tags( $alt ) ) . '" class="tmwpmllink" href="' . esc_url( $post_new_file ) . '"><i class="tcfa tcfa-edit"></i></a>';
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Adds WPML meta box
	 *
	 * @param string|false $basetype The post type for our language meta box.
	 * @param string|false $basetypehook The post type hook for our language meta box.
	 * @since 1.0
	 */
	public function add_meta_box( $basetype = false, $basetypehook = false ) {
		if ( false === $basetype ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
		}
		if ( false === $basetypehook ) {
			$basetypehook = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		}
		$this->basetype     = $basetype;
		$this->basetypehook = $basetypehook;
		if ( $this->is_wpml ) {
			add_meta_box( 'icl_div', esc_html__( 'Language', 'woocommerce-tm-extra-product-options' ), [ $this, 'meta_box' ], null, 'side', 'high' );
		}
	}

	/**
	 * Displayes WPML meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function meta_box( $post ) {
		$basetype     = $this->basetype;
		$basetypehook = $this->basetypehook;
		if ( $this->is_wpml ) {

			global $wp_post_types;
			$post_type_label        = ( '' !== $wp_post_types[ $basetype ]->labels->singular_name ? $wp_post_types[ $basetype ]->labels->singular_name : $wp_post_types[ $basetype ]->labels->name );
			$tmparentpostid         = 0;
			$tmaddlang              = '';
			$post_id                = absint( $post->ID );
			$tm_meta_lang           = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
			$tm_meta_parent_post_id = absint( themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true ) );
			$is_original            = false;
			$is_added_translation   = false;
			$is_original_lang       = '';
			$active_languages       = $this->get_active_languages();
			$is_new                 = false;
			$is_add                 = false;

			// existing global epo before WPML || new global epo after WPML.
			if ( ( ! empty( $post_id ) && empty( $tm_meta_lang ) && empty( $tm_meta_parent_post_id ) )
				|| ( ! empty( $post_id ) && 0 === $tm_meta_parent_post_id && ! empty( $tm_meta_lang ) )
				|| ( ! empty( $post_id ) && $tm_meta_parent_post_id === $post_id )
			) {
				$is_original = true;
			}

			if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&& ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {

				$tmparentpostid       = (int) $_GET['tmparentpostid']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$tmaddlang            = sanitize_text_field( wp_unslash( $_GET['tmaddlang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$is_added_translation = true;
				$is_original_lang     = themecomplete_get_post_meta( $tmparentpostid, THEMECOMPLETE_EPO_WPML_LANG_META, true );
				if ( empty( $is_original_lang ) ) {
					$is_original_lang = $this->get_default_lang();
				}
				if ( $is_original_lang !== $tmaddlang ) {
					$is_original = false;
				}
				$is_add = true;

			} else {
				if ( ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( empty( $_GET['tmparentpostid'] ) || empty( $_GET['tmaddlang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$is_original      = true;
						$is_original_lang = $this->get_lang();
						$tm_meta_lang     = $is_original_lang;
						$tmaddlang        = $is_original_lang;
						$is_new           = true;
					}
				}
			}

			if ( $is_original && empty( $tm_meta_lang ) ) {
				$is_original_lang = $this->get_default_lang();
				$tm_meta_lang     = $is_original_lang;
				$tmaddlang        = $is_original_lang;
			}

			if ( $is_original && empty( $tmparentpostid ) ) {
				$tmparentpostid = $post_id;
			}

			if ( ! $is_original && empty( $is_original_lang ) && ! empty( $tm_meta_parent_post_id ) ) {
				$is_original_lang = themecomplete_get_post_meta( $tm_meta_parent_post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
				if ( empty( $is_original_lang ) ) {
					$is_original_lang = $this->get_default_lang();
				}
				if ( empty( $tmparentpostid ) ) {
					$tmparentpostid = $tm_meta_parent_post_id;
				}
			}

			if ( ! $is_original && empty( $tmaddlang ) ) {
				$tmaddlang = $tm_meta_lang;
			}

			THEMECOMPLETE_EPO_HTML()->create_field(
				[
					'nodiv'   => 1,
					'id'      => 'tmparentpostid',
					'default' => $tmparentpostid,
					'type'    => 'hidden',
					'tags'    => [
						'id'   => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
						'name' => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
					],
				],
				1
			);
			THEMECOMPLETE_EPO_HTML()->create_field(
				[
					'nodiv'   => 1,
					'id'      => 'tmaddlang',
					'default' => $tmaddlang,
					'type'    => 'hidden',
					'tags'    => [
						'id'   => THEMECOMPLETE_EPO_WPML_LANG_META,
						'name' => THEMECOMPLETE_EPO_WPML_LANG_META,
					],
				],
				1
			);

			echo '<div class="tm-meta-wpml-lang">';
			/* translators: %s post type label */
			echo '<strong>' . sprintf( esc_html__( 'Language of this %s', 'sitepress' ), esc_html( $post_type_label ) ) . '</strong>: ';
			if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&& ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {
				$this->get_flag( sanitize_text_field( wp_unslash( $_GET['tmaddlang'] ) ), 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} else {
				$this->get_flag( $tm_meta_lang, 1 );
			}
			echo '</div>';

			if ( ! $is_original && '' !== $is_original_lang ) {
				echo '<div class="tm-meta-wpml-translation">';
				echo esc_html__( 'This is a translation of', 'sitepress' );
				echo ': <div class="tm-title added">';
				$this->get_flag( $is_original_lang, 1 );
				echo ' ' . esc_html( get_the_title( $tmparentpostid ) );
				$this->edit_lang_link(
					$tmparentpostid,
					$is_original_lang,
					$active_languages[ $is_original_lang ],
					$tmparentpostid,
					true,
					$basetypehook,
					1
				);
				echo '</div>';
				echo '</div>';

				$args = [
					'post_type'   => $basetype,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
				];

				if ( ! $is_add ) {
					foreach ( $active_languages as $key => $value ) {
						if ( $key !== $tm_meta_lang && $key !== $is_original_lang ) {
							$class              = 'tm-title';
							$args['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $key, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
							$other_translations = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
							$is_edit            = false;
							if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {// has $key code translation.
								$class   = 'tm-title added';
								$is_edit = true;
							}

							echo '<div class="' . esc_attr( $class ) . '">';
							$this->get_flag( $key, 1 );
							echo ' ' . esc_html( $value['display_name'] );
							if ( $is_edit ) {
								$this->edit_lang_link( $other_translations[0]->ID, $key, $active_languages[ $key ], $tmparentpostid, true, $basetypehook, 1 );
							} else { // no translation.
								$this->add_lang_link( $tmparentpostid, $key, $active_languages[ $key ], $basetypehook, 1 );
							}
							echo '</div>';
						}
					}
				}
			} elseif ( $is_original && ! $is_new ) {

				echo '<div class="tm-meta-wpml-translation">';
				esc_html_e( 'Translations', 'sitepress' );
				$args = [
					'post_type'   => $basetype,
					'post_status' => [ 'publish', 'draft' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
				];
				foreach ( $active_languages as $key => $value ) {
					if ( $key !== $tm_meta_lang ) {
						$class                = 'tm-title';
						$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $key, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
						$args['meta_query'][] = [
							'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
							'value'   => $tmparentpostid,
							'compare' => '=',
						];
						$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
						$is_edit              = false;
						if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {// has $key code translation.
							$class   = 'tm-title added';
							$is_edit = true;
						}

						echo '<div class="' . esc_attr( $class ) . '">';
						$this->get_flag( $key, 1 );
						echo ' ' . esc_html( $value['display_name'] );
						if ( $is_edit ) {
							$this->edit_lang_link( $other_translations[0]->ID, $key, $active_languages[ $key ], $tmparentpostid, true, $basetypehook, 1 );
						} else { // no translation.
							$this->add_lang_link( $tmparentpostid, $key, $active_languages[ $key ], $basetypehook, 1 );
						}
						echo '</div>';
					}
				}
				echo '</div>';
			}
		}
	}

	/**
	 * Returns translated options values
	 * If options are changed after the order this will return wrong results.
	 *
	 * @param integer $current_product_id The product id.
	 * @param boolean $override If the check should be overriden.
	 * @since 1.0
	 */
	public function get_wpml_translation_by_id( $current_product_id = 0, $override = false ) {
		$wpml_translation_by_id = [];
		if ( $this->is_wpml && ( $override || 'yes' === THEMECOMPLETE_EPO()->tm_epo_wpml_order_translate ) ) {
			$this_land_epos = THEMECOMPLETE_EPO()->get_product_tm_epos( $current_product_id );
			if ( isset( $this_land_epos['global'] ) && is_array( $this_land_epos['global'] ) ) {
				foreach ( $this_land_epos['global'] as $priority => $priorities ) {
					if ( is_array( $priorities ) ) {
						foreach ( $priorities as $pid => $field ) {
							if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
								foreach ( $field['sections'] as $section_id => $section ) {
									if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
										foreach ( $section['elements'] as $element ) {
											$wpml_translation_by_id[ $element['uniqid'] ]              = $element['label'];
											$wpml_translation_by_id[ 'options_' . $element['uniqid'] ] = $element['options_all'];
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $wpml_translation_by_id;
	}

	/**
	 * Return $price converted to the active $currency
	 *
	 * @param float  $price The price to convert.
	 * @param string $currency The currency to convert the price to.
	 */
	public function get_price_in_currency( $price = 0, $currency = '' ) {
		global $woocommerce_wpml;
		if ( $this->is_multi_currency() ) {
			if ( is_callable( [ $woocommerce_wpml->multi_currency, 'convert_price_amount' ] ) ) {
				$price = $woocommerce_wpml->multi_currency->convert_price_amount( $price, $currency );
			} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( [ $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ] ) ) {
				$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $currency );
			}
		}
		return $price;
	}
}
