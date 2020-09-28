<?php
/**
 * Extra Product Options WPML class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_WPML_base {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	private $is_wpml = FALSE;

	private $sitepress = FALSE;

	private $basetype = FALSE;
	private $basetypehook = FALSE;

	public $remove_sql_filters_done = 0;
	public $remove_term_filters_done = 0;
	public $tmparentpostid_for_filter = 0;
	public $removed_posts_filter = 0;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		if ( class_exists( 'SitePress' ) ) {
			$this->is_wpml = TRUE;
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
		$is_wpml_multi_currency      = $this_is_wpml && $woocommerce_wpml && property_exists( $woocommerce_wpml, 'settings' ) && $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT;
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
	 * @param string $lang
	 *
	 * @return string
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
	 * @since 1.0
	 */
	public function get_flag_url( $lang = 'all' ) {
		$url = '';
		if ( $this->is_wpml ) {
			if ( empty( $lang ) ) {
				$url = $this->sitepress->get_flag_url( $this->get_default_lang() );
			} elseif ( empty( $lang ) || $lang == "all" ) {
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
	 * @since 1.0
	 */
	public function get_original_id( $id = 0, $post_type = 'product', $basetype = FALSE ) {
		
		if ( $basetype === FALSE ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE' ) && $post_type == THEMECOMPLETE_ECO_GLOBAL_POST_TYPE ) {
				$basetype = THEMECOMPLETE_ECO_GLOBAL_POST_TYPE;
			}
		}
		if ( $this->is_wpml ) {
			$check_post = get_post( $id );
			if ( $check_post && property_exists( $check_post, 'ID' ) && property_exists( $check_post, 'post_type' ) ) {
				if ( ( $check_post->post_status == "auto-draft" && $check_post->post_title == 'AUTO-DRAFT' ) || ! ( $check_post->post_type == "product" || $check_post->post_type == $basetype ) ) {
					return (double) $id;
				}
			}
			if ( $post_type == 'product' ) {
				$trid = 0;
				if ( $id ) {
					global $wpdb;
					$res = $this->sitepress->get_element_language_details( $id, 'post_' . $post_type );
					if ( isset( $res->trid ) ) {
						$trid = intval( $res->trid );
					}
					if ( $trid ) {
						$element_lang_code = $res->language_code;
					} else {
						$translation_id    = $this->sitepress->set_element_language_details( $id, 'post_' . $post_type, NULL, $this->get_lang() );
						$trid_sql          = "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE translation_id = %d";
						$trid_prepared     = $wpdb->prepare( $trid_sql, array( $translation_id ) );
						$trid              = $wpdb->get_var( $trid_prepared );
						$element_lang_code = $this->get_lang();
					}
				} else {
					$trid              = isset( $_GET['trid'] ) ? intval( $_GET['trid'] ) : FALSE;
					$element_lang_code = isset( $_GET['lang'] ) ? wp_strip_all_tags( $_GET['lang'] ) : $this->get_lang();
				}

				$translations = array();
				if ( $trid ) {
					$translations = $this->sitepress->get_element_translations( $trid, 'post_' . $post_type );
				}
				foreach ( $translations as $key => $value ) {
					if ( $value->source_language_code === NULL ) {
						return (double) $value->element_id;
					}
				}

				return (double) icl_object_id( $id, 'any', TRUE, $this->get_default_lang() );
			} elseif ( $post_type == $basetype ) {
				if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] )
				     && ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' )
				) {
					return (double) $_GET['tmparentpostid'];
				} else {
					$tm_meta_parent_post_id = get_post_meta( $id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, TRUE );
					if ( $tm_meta_parent_post_id && $tm_meta_parent_post_id != $id ) {
						return $tm_meta_parent_post_id;
					}

					return (double) $id;
				}
			}
		} else {
			return (double) $id;
		}
	}

	/**
	 * Get product post id of current lang or $lang
	 *
	 * @since 1.0
	 */
	public function get_current_id( $id = 0, $post_type = 'product', $lang = NULL, $basetype = FALSE ) {
		if ( $basetype === FALSE ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE' ) && $post_type == THEMECOMPLETE_ECO_GLOBAL_POST_TYPE ) {
				$basetype = THEMECOMPLETE_ECO_GLOBAL_POST_TYPE;
			}
		}
		if ( $this->is_wpml ) {
			if ( $lang == NULL ) {
				$lang = $this->get_lang();
			}
			$check_post = get_post( $id );
			if ( $check_post && property_exists( $check_post, 'ID' ) && property_exists( $check_post, 'post_type' ) ) {
				if ( ! ( $check_post->post_type == "product" || $check_post->post_type == $basetype ) ) {
					return $id;
				}
			}
			if ( $post_type == 'product' ) {

				if ( $id ) {
					global $wpdb;
					$res  = $this->sitepress->get_element_language_details( $id, 'post_' . $post_type );
					$trid = @intval( $res->trid );
					if ( $trid ) {
						$element_lang_code = $res->language_code;
					} else {
						$translation_id    = $this->sitepress->set_element_language_details( $id, 'post_' . $post_type, NULL, $lang );
						$trid_sql          = "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE translation_id = %d";
						$trid_prepared     = $wpdb->prepare( $trid_sql, array( $translation_id ) );
						$trid              = $wpdb->get_var( $trid_prepared );
						$element_lang_code = $lang;
					}
				} else {
					$trid              = isset( $_GET['trid'] ) ? intval( $_GET['trid'] ) : FALSE;
					$element_lang_code = isset( $_GET['lang'] ) ? wp_strip_all_tags( $_GET['lang'] ) : $lang;
				}

				$translations = array();
				if ( $trid ) {
					$translations = $this->sitepress->get_element_translations( $trid, 'post_' . $post_type );
				}
				if ( isset( $translations[ $lang ] ) ) {
					return $translations[ $lang ]->element_id;
				}

				return icl_object_id( $id, 'any', FALSE, $lang );
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
	 * @since 1.0
	 */
	public function is_original_product( $product_id, $post_type = 'product', $basetype = FALSE ) {
		if ( $basetype === FALSE ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE' ) && $post_type == THEMECOMPLETE_ECO_GLOBAL_POST_TYPE ) {
				$basetype = THEMECOMPLETE_ECO_GLOBAL_POST_TYPE;
			}
		}
		if ( $this->is_wpml ) {
			global $wpdb;
			if ( $post_type == 'product' ) {
				$is_original = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT source_language_code IS NULL FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_product'", $product_id ) );
			} elseif ( $post_type == $basetype ) {
				if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] )
				     && ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' )
				) {
					$is_original = FALSE;
				} else {
					$tm_meta_parent_post_id = get_post_meta( $product_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, TRUE );
					if ( $tm_meta_parent_post_id && $tm_meta_parent_post_id != $product_id ) {
						$is_original = FALSE;
					} else {
						$is_original = TRUE;
					}
				}
			}
			if ( ! isset( $is_original ) ) {
				$is_original = TRUE;
			}

			return $is_original;
		} else {
			return TRUE;
		}
	}

	/**
	 * Remove sql filters
	 *
	 * @since 1.0
	 */
	public function remove_sql_filter() {
		if ( $this->is_wpml ) {
			remove_action( 'parse_query', array( $this->sitepress, 'parse_query' ) );
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
			// restore WPML term filters
			add_action( 'parse_query', array( $this->sitepress, 'parse_query' ) );
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
			// remove WPML term filters
			remove_filter( 'get_terms_args', array( $this->sitepress, 'get_terms_args_filter' ) );
			remove_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1 );
			remove_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ) );
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
			// restore WPML term filters
			add_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 4 );
			add_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1, 1 );
			add_filter( 'get_terms_args', array( $this->sitepress, 'get_terms_args_filter' ), 10, 2 );
			$this->remove_term_filters_done = 0;
		}
	}

	/**
	 * Applies the 'wp_terms_checklist_args' filter
	 *
	 * @since 1.0
	 */
	public function apply_wp_terms_checklist_args_filter( $post_id ) {
		if ( $this->is_wpml ) {
			$this->tmparentpostid_for_filter = $post_id;
			add_filter( 'wp_terms_checklist_args', array( $this, 'wp_terms_checklist_args_filter' ), 10, 2 );
		}
	}

	/**
	 * wp_terms_checklist_args filter
	 *
	 * @since 1.0
	 */
	public function wp_terms_checklist_args_filter( $args, $post_id ) {
		if ( $this->is_wpml ) {
			$args['selected_cats'] = wp_get_object_terms( $this->tmparentpostid_for_filter, 'product_cat', array_merge( $args, array( 'fields' => 'ids' ) ) );
			foreach ( $args['selected_cats'] as $key => $term ) {
				$args['selected_cats'][ $key ] = apply_filters( 'translate_object_id', $term, 'product_cat', FALSE );
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
		if ( $this->is_wpml && $this->get_lang() != 'all' ) {
			add_filter( 'request', array( $this, 'request_filter' ) );
			global $wpml_query_filter;
			if ( has_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ) ) ) {
				remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
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
		if ( $this->is_wpml && $this->get_lang() != 'all' ) {
			remove_filter( 'request', array( $this, 'request_filter' ) );
			if ( $this->removed_posts_filter ) {
				global $wpml_query_filter;
				add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
			}
		}
	}

	/**
	 * 'request' filter: adds meta args to query_vars
	 *
	 * @since 1.0
	 */
	public function request_filter( $query_vars ) {
		if ( $this->is_wpml && $this->get_lang() != 'all' ) {
			if ( $this->get_lang() != $this->get_default_lang() ) {
				$query_vars['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $this->get_lang(), '=', 'EXISTS' );
			} else {
				$query_vars['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', THEMECOMPLETE_EPO_WPML_LANG_META, $this->get_lang(), '=', 'NOT EXISTS' );
			}
		}

		return $query_vars;
	}

	/**
	 * Order terms
	 *
	 * @since 1.0
	 */
	public function order_terms( $t1, $t2 ) {
		if ( ! $this->is_wpml ) {
			return $t1;
		}
		$d      = array();
		$o      = array();
		$new_t1 = array();
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
	 * @since 1.0
	 */
	public function merge_terms( $t1, $t2 ) {
		if ( ! $this->is_wpml ) {
			return $t1;
		}
		$d = array();
		$o = array();
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
	 * @since 1.0
	 */
	public function merge_terms_slugs( $t1, $t2 ) {
		$t2_slug = array();
		if ( ! $this->is_wpml ) {
			foreach ( $t1 as $key => $value ) {
				$o[ $value->trid ] = $value;
			}
			foreach ( $o as $trid => $term ) {
				$t2_slug[ $term->slug ] = $term->slug;
			}

			return $t2_slug;
		}
		$d       = array();
		$o       = array();
		$t1_slug = array();

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
	 * @since 1.0
	 */
	public function get_terms( $lang = NULL, $taxonomy = "", $args = array(), $post_id = 0 ) {
		if ( ! $this->is_wpml ) {
			if ( ! empty( $post_id ) ) {
				$terms     = array();
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
		if ( $lang === NULL ) {
			$this->remove_term_filters();

			$all_terms = get_terms( $taxonomy, $args );
			if ( ! empty( $post_id ) ) {
				$terms = array();
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
			$terms      = array();
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

		return FALSE;
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

		return FALSE;
	}

	/**
	 * Sets WPML current language depending on displayed global epo
	 *
	 * @since 1.0
	 */
	public function set_post_lang( $basetype = FALSE ) {
		if ( $basetype === FALSE ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
		}
		if ( $this->is_wpml ) {
			remove_action( 'admin_footer', array( $this->sitepress, 'language_filter' ) );
			remove_action( 'admin_enqueue_scripts', array( $this->sitepress, 'language_filter' ) );

			$post_id = FALSE;
			if (
				( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' )
				&&
				( isset( $_REQUEST['post'] ) || isset( $_POST['post_ID'] ) )
			) {

				if ( isset( $_GET['post'] ) ) {
					$post_id = (int) $_GET['post'];
				} elseif ( isset( $_POST['post_ID'] ) ) {
					$post_id = (int) $_POST['post_ID'];
				}

				if ( ! empty( $post_id ) ) {

					$meta_lang              = get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
					$tm_meta_parent_post_id = get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, TRUE );

					if (
						( ( empty( $tm_meta_parent_post_id ) && empty( $meta_lang ) ) ||
						  ( ! empty( $tm_meta_parent_post_id ) && ! empty( $meta_lang ) )
						)
						&& ! empty( $_GET['lang'] )
					) {

						if ( $_GET['lang'] != $meta_lang ) {

							$url = $_SERVER['REQUEST_URI'];
							$url = remove_query_arg( array( 'post', 'lang' ), $url );

							$args                 = array(
								'post_type'   => $basetype,
								'post_status' => array( 'publish' ), // get only enabled global extra options
								'numberposts' => - 1,
								'orderby'     => 'date',
								'order'       => 'asc',
								'meta_query'  => THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $_GET['lang'], '=', 'EXISTS' ),
							);
							$args['meta_query'][] = array(
								'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
								'value'   => $tm_meta_parent_post_id,
								'compare' => '=',
							);
							$other_translations   = get_posts( $args );
							if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {//has $key code translation
								$tm_meta_parent_post_id = $other_translations[0]->ID;
								$url                    = add_query_arg( 'post', $tm_meta_parent_post_id, $url );
							} else {
								$url = remove_query_arg( array( 'action', 'tmparentpostid', 'tmaddlang' ), $url );
								if ( empty( $tm_meta_parent_post_id ) ) {
									$tm_meta_parent_post_id = $post_id;
								}
								$url = add_query_arg( array(
									'action'         => 'add',
									'tmparentpostid' => $tm_meta_parent_post_id,
									'tmaddlang'      => $_GET['lang'],
								), $url );
							}
							$url = esc_url_raw( $url );
							wp_redirect( $url );
							exit;
						}
					}
					if ( empty( $meta_lang ) ) {
						$this->set_lang( $this->get_default_lang() );
					} else {
						$this->set_lang( $meta_lang );
					}

				}

			} elseif ( ! empty( $_GET['tmparentpostid'] )
			           && ! empty( $_GET['tmaddlang'] )
			           && ( isset( $_REQUEST['action'] )
			                && $_REQUEST['action'] == 'add' )
			) {

				$args = array(
					'post_type'   => $basetype,
					'post_status' => array( 'publish' ), // get only enabled global extra options
					'numberposts' => - 1,
					'orderby'     => 'date',
					'order'       => 'asc',
				);

				if ( ! empty( $_GET['lang'] ) ) {
					$url                  = $_SERVER['REQUEST_URI'];
					$url                  = remove_query_arg( array( 'tmaddlang', 'lang' ), $url );
					$url                  = add_query_arg( array(
						'tmaddlang' => $_GET['lang'],
					), $url );
					$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $_GET['lang'], '=', 'EXISTS' );
					$args['meta_query'][] = array(
						'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
						'value'   => $_GET['tmparentpostid'],
						'compare' => '=',
					);
					$other_translations   = get_posts( $args );
					if ( ! empty( $other_translations ) ) {
						$url = remove_query_arg( array( 'action', 'tmaddlang', 'tmparentpostid' ), $url );
						$url = add_query_arg( array(
							'action' => 'edit',
							'post'   => $other_translations[0]->ID,
							'lang'   => $_GET['lang'],
						), $url );
					}
					$url = esc_url_raw( $url );
					wp_redirect( $url );
					exit;
				}

				$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $_GET['tmaddlang'], '=', 'EXISTS' );
				$args['meta_query'][] = array(
					'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
					'value'   => $_GET['tmparentpostid'],
					'compare' => '=',
				);
				$other_translations   = get_posts( $args );
				if ( ! empty( $other_translations ) ) {
					$url = $_SERVER['REQUEST_URI'];
					$url = remove_query_arg( array( 'action', 'tmaddlang', 'tmparentpostid' ), $url );
					$url = add_query_arg( array(
						'action' => 'edit',
						'post'   => $other_translations[0]->ID,
						'lang'   => $_GET['tmaddlang'],
					), $url );
					$url = esc_url_raw( $url );
					wp_redirect( $url );
					exit;
				}

				$this->set_lang( $_GET['tmaddlang'] );

			} elseif ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' ) {
				if ( ! empty( $_GET['lang'] ) && $_GET['lang'] == 'all' ) {
					$url = $_SERVER['REQUEST_URI'];
					$url = remove_query_arg( array( 'lang' ), $url );
					$url = add_query_arg( array(
						'lang' => $this->get_default_lang(),
					), $url );
					$url = esc_url_raw( $url );
					wp_redirect( $url );
					exit;
				}
			}
		}
	}

	/**
	 * Returns add global epo link
	 *
	 * @since 1.0
	 */
	public function add_lang_link( $post_id, $lang, $v, $basetypehook = FALSE, $echo = 0 ) {

		ob_start();
		if ( $basetypehook === FALSE ) {
			$basetypehook = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		}

		$post_new_file = apply_filters( 'wc_epo_add_lang_link', "edit.php?post_type=product&page=" . $basetypehook . "&action=add" );

		if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE_PAGE_HOOK' ) && $basetypehook == THEMECOMPLETE_ECO_GLOBAL_POST_TYPE_PAGE_HOOK ) {
			$post_new_file = "admin.php?bpost_type=product&page=" . $basetypehook . "&action=add";
		}

		$post_new_file = admin_url( $post_new_file );

		if ( $this->is_wpml ) {
			$alt           = sprintf( esc_html__( 'Add translation to %s', 'sitepress' ), $v['display_name'] );
			$post_new_file = add_query_arg( array( "tmparentpostid" => $post_id, "tmaddlang" => $lang ), $post_new_file );
			echo '<a title="' . esc_attr( $alt ) . '" alt="' . esc_attr( strip_tags( $alt ) ) . '" class="tmwpmllink" href="' . esc_url( $post_new_file ) . '"><i class="tcfa tcfa-plus"></i></a>';
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
	 * @since 1.0
	 */
	public function edit_lang_link( $post_id, $lang, $v, $main_post_id, $noadd = FALSE, $basetypehook = FALSE, $echo = 0 ) {

		ob_start();

		if ( $basetypehook === FALSE ) {
			$basetypehook = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		}

		$post_new_file = apply_filters( 'wc_epo_edit_lang_link', "edit.php?post_type=product&page=" . $basetypehook . "&action=edit&post=" . $post_id );

		if ( defined( 'THEMECOMPLETE_ECO_GLOBAL_POST_TYPE_PAGE_HOOK' ) && $basetypehook == THEMECOMPLETE_ECO_GLOBAL_POST_TYPE_PAGE_HOOK ) {
			$post_new_file = "admin.php?bpost_type=product&page=" . $basetypehook . "&action=edit&post=" . $post_id;
		}

		$post_new_file = admin_url( $post_new_file );
		if ( $this->is_wpml ) {
			$alt = sprintf( esc_html__( 'Edit the %s translation', 'sitepress' ), $v['display_name'] );
			if ( empty( $noadd ) ) {
				$post_new_file = add_query_arg( array( "tmparentpostid" => $main_post_id, "tmaddlang" => $lang ), $post_new_file );
			}
			echo '<a title="' . esc_attr( $alt ) . '" alt="' . esc_attr( strip_tags( $alt ) ) . '" class="tmwpmllink" href="' . esc_url( $post_new_file ) . '"><i class="tcfa tcfa-edit"></i></a>';
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
	 * @since 1.0
	 */
	public function add_meta_box( $basetype = FALSE, $basetypehook = FALSE ) {
		if ( $basetype === FALSE ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
		}
		if ( $basetypehook === FALSE ) {
			$basetypehook = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		}
		$this->basetype     = $basetype;
		$this->basetypehook = $basetypehook;
		if ( $this->is_wpml ) {
			add_meta_box( 'icl_div', esc_html__( 'Language', 'woocommerce-tm-extra-product-options' ), array( $this, 'meta_box' ), NULL, 'side', 'high' );
		}
	}

	/**
	 * Displayes WPML meta box
	 *
	 * @since 1.0
	 */
	public function meta_box( $post ) {
		$basetype     = $this->basetype;
		$basetypehook = $this->basetypehook;
		if ( $this->is_wpml ) {

			global $wp_post_types;
			$post_type_label        = ( $wp_post_types[ $basetype ]->labels->singular_name != "" ? $wp_post_types[ $basetype ]->labels->singular_name : $wp_post_types[ $basetype ]->labels->name );
			$tmparentpostid         = 0;
			$tmaddlang              = '';
			$tm_meta_lang           = get_post_meta( $post->ID, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
			$tm_meta_parent_post_id = get_post_meta( $post->ID, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, TRUE );
			$is_original            = FALSE;
			$is_added_translation   = FALSE;
			$is_original_lang       = '';
			$active_languages       = $this->get_active_languages();
			$is_new                 = FALSE;
			$is_add                 = FALSE;

			// existing global epo before WPML || new global epo after WPML
			if ( ( ! empty( $post->ID ) && empty( $tm_meta_lang ) && empty( $tm_meta_parent_post_id ) )
			     || ( ! empty( $post->ID ) && $tm_meta_parent_post_id === 0 && ! empty( $tm_meta_lang ) )
			     || ( ! empty( $post->ID ) && $tm_meta_parent_post_id == $post->ID )
			) {
				$is_original = TRUE;
			}

			if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] )
			     && ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' )
			) {

				$tmparentpostid       = (int) $_GET['tmparentpostid'];
				$tmaddlang            = $_GET['tmaddlang'];
				$is_added_translation = TRUE;
				$is_original_lang     = get_post_meta( $tmparentpostid, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
				if ( empty( $is_original_lang ) ) {
					$is_original_lang = $this->get_default_lang();
				}
				if ( $is_original_lang != $tmaddlang ) {
					$is_original = FALSE;
				}
				$is_add = TRUE;

			} else {
				if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' ) ) {
					if ( empty( $_GET['tmparentpostid'] ) || empty( $_GET['tmaddlang'] ) ) {
						$is_original      = TRUE;
						$is_original_lang = $tm_meta_lang = $tmaddlang = $this->get_lang();
						$is_new           = TRUE;
					}
				} else {

				}
			}

			if ( $is_original && empty( $tm_meta_lang ) ) {
				$is_original_lang = $tm_meta_lang = $tmaddlang = $this->get_default_lang();
			}

			if ( $is_original && empty( $tmparentpostid ) ) {
				$tmparentpostid = $post->ID;
			}

			if ( ! $is_original && empty( $is_original_lang ) && ! empty( $tm_meta_parent_post_id ) ) {
				$is_original_lang = get_post_meta( $tm_meta_parent_post_id, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
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

			THEMECOMPLETE_EPO_HTML()->tm_make_field( array(
				"nodiv"   => 1,
				"id"      => "tmparentpostid",
				"default" => $tmparentpostid,
				"type"    => "hidden",
				"tags"    => array( "id" => THEMECOMPLETE_EPO_WPML_PARENT_POSTID, "name" => THEMECOMPLETE_EPO_WPML_PARENT_POSTID ),
			), 1 );
			THEMECOMPLETE_EPO_HTML()->tm_make_field( array(
				"nodiv"   => 1,
				"id"      => "tmaddlang",
				"default" => $tmaddlang,
				"type"    => "hidden",
				"tags"    => array( "id" => THEMECOMPLETE_EPO_WPML_LANG_META, "name" => THEMECOMPLETE_EPO_WPML_LANG_META ),
			), 1 );

			echo '<div class="tm-meta-wpml-lang">';
			echo '<strong>' . sprintf( esc_html__( 'Language of this %s', 'sitepress' ), $post_type_label ) . '</strong>: ';
			if ( ! empty( $_GET['tmparentpostid'] ) && ! empty( $_GET['tmaddlang'] )
			     && ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' )
			) {
				$this->get_flag( $_GET['tmaddlang'], 1 );
			} else {
				$this->get_flag( $tm_meta_lang, 1 );
			}
			echo '</div>';

			if ( ! $is_original && $is_original_lang !== '' ) {
				echo '<div class="tm-meta-wpml-translation">';
				echo esc_html__( 'This is a translation of', 'sitepress' );
				echo ': <div class="tm-title added">';
				$this->get_flag( $is_original_lang, 1 );
				echo ' ' . esc_html( get_the_title( $tmparentpostid ) );
				$this->edit_lang_link( $tmparentpostid,
					$is_original_lang,
					$active_languages[ $is_original_lang ],
					$tmparentpostid, TRUE, $basetypehook, 1 );
				echo '</div>';
				echo '</div>';

				$args = array(
					'post_type'   => $basetype,
					'post_status' => array( 'publish' ), // get only enabled global extra options
					'numberposts' => - 1,
					'orderby'     => 'date',
					'order'       => 'asc',
				);

				if ( ! $is_add ) {
					foreach ( $active_languages as $key => $value ) {
						if ( $key != $tm_meta_lang && $key != $is_original_lang ) {
							$class              = "tm-title";
							$args['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $key, '=', 'EXISTS' );
							$other_translations = get_posts( $args );
							$is_edit            = FALSE;
							if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {//has $key code translation
								$class   = "tm-title added";
								$is_edit = TRUE;
							}

							echo '<div class="' . esc_attr( $class ) . '">';
							$this->get_flag( $key, 1 );
							echo ' ' . esc_html( $value['display_name'] );
							if ( $is_edit ) {
								$this->edit_lang_link( $other_translations[0]->ID, $key, $active_languages[ $key ], $tmparentpostid, TRUE, $basetypehook, 1 );
							} else {// no translation
								$this->add_lang_link( $tmparentpostid, $key, $active_languages[ $key ], $basetypehook, 1 );
							}
							echo '</div>';
						}
					}
				}

			} elseif ( $is_original && ! $is_new ) {

				echo '<div class="tm-meta-wpml-translation">';
				esc_html_e( 'Translations', 'sitepress' );
				$args = array(
					'post_type'   => $basetype,
					'post_status' => array( 'publish', 'draft' ), // get only enabled global extra options
					'numberposts' => - 1,
					'orderby'     => 'date',
					'order'       => 'asc',
				);
				foreach ( $active_languages as $key => $value ) {
					if ( $key != $tm_meta_lang ) {
						$class                = "tm-title";
						$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $key, '=', 'EXISTS' );
						$args['meta_query'][] = array(
							'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
							'value'   => $tmparentpostid,
							'compare' => '=',
						);
						$other_translations   = get_posts( $args );
						$is_edit              = FALSE;
						if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {//has $key code translation
							$class   = "tm-title added";
							$is_edit = TRUE;
						}

						echo '<div class="' . esc_attr( $class ) . '">';
						$this->get_flag( $key, 1 );
						echo ' ' . esc_html( $value['display_name'] );
						if ( $is_edit ) {
							$this->edit_lang_link( $other_translations[0]->ID, $key, $active_languages[ $key ], $tmparentpostid, TRUE, $basetypehook, 1 );
						} else {// no translation
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
	 * @since 1.0
	 */
	public function get_wpml_translation_by_id( $current_product_id = 0, $override = FALSE ) {
		$wpml_translation_by_id = array();
		if ( $this->is_wpml && ( $override || THEMECOMPLETE_EPO()->tm_epo_wpml_order_translate == "yes" ) ) {			
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
											$wpml_translation_by_id[ "options_" . $element['uniqid'] ] = $element['options_all'];
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
}

