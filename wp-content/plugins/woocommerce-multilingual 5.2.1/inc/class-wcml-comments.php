<?php

use WCML\Utilities\DB;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\Convert\Ids;

class WCML_Comments {

	const WCML_AVERAGE_RATING_KEY = '_wcml_average_rating';
	const WCML_RATING_COUNT_KEY   = '_wcml_rating_count';
	const WCML_REVIEW_COUNT_KEY   = '_wcml_review_count';
	const WC_AVERAGE_RATING_KEY   = '_wc_average_rating';
	const WC_RATING_COUNT_KEY     = '_wc_rating_count';
	const WC_REVIEW_COUNT_KEY     = '_wc_review_count';

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var WPML_Post_Translation */
	private $post_translations;
	/** @var wpdb */
	private $wpdb;

	/**
	 * WCML_Comments constructor.
	 *
	 * @param woocommerce_wpml      $woocommerce_wpml
	 * @param SitePress             $sitepress
	 * @param WPML_Post_Translation $post_translations
	 * @param wpdb                  $wpdb
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, WPML_Post_Translation $post_translations, wpdb $wpdb ) {
		$this->woocommerce_wpml  = $woocommerce_wpml;
		$this->sitepress         = $sitepress;
		$this->post_translations = $post_translations;
		$this->wpdb              = $wpdb;
	}

	public function add_hooks() {

		add_action( 'wp_insert_comment', [ $this, 'add_comment_rating' ] );
		add_action( 'woocommerce_review_before_comment_meta', [ $this, 'add_comment_flag' ], 9 );
		add_action( 'woocommerce_review_before_comment_text', [ $this, 'open_lang_div' ] );
		add_action( 'woocommerce_review_after_comment_text', [ $this, 'close_lang_div' ] );

		add_action( 'added_comment_meta', [ $this, 'maybe_duplicate_comment_rating' ], 10, 4 );

		add_filter( 'get_post_metadata', [ $this, 'filter_average_rating' ], 10, 4 );
		add_filter( 'comments_clauses', [ $this, 'comments_clauses' ], 10, 2 );
		add_action( 'comment_form_before', [ $this, 'comments_link' ] );

		add_filter( 'wpml_is_comment_query_filtered', [ $this, 'is_comment_query_filtered' ], 10, 3 );
		add_action( 'trashed_comment', [ $this, 'recalculate_average_rating_on_comment_hook' ], 10, 2 );
		add_action( 'deleted_comment', [ $this, 'recalculate_average_rating_on_comment_hook' ], 10, 2 );
		add_action( 'untrashed_comment', [ $this, 'recalculate_average_rating_on_comment_hook' ], 10, 2 );
		// before WCML_Synchronize_Product_Data::sync_product_translations_visibility hook.
		add_action(
			'woocommerce_product_set_visibility',
			Fns::withoutRecursion( Fns::noop(), [ $this, 'recalculate_comment_rating' ] ),
			9
		);

		if ( ! defined( 'WPSEO_VERSION' )
			 && 'all' === WPML\FP\Obj::prop( 'clang', $_GET )
			 && ! $this->is_reviews_in_all_languages_by_default_selected()
		) {
			add_action( 'wp_head', [ $this, 'no_index_all_reviews_page' ], 10 );
		}

		add_filter( 'woocommerce_top_rated_products_widget_args', [ $this, 'top_rated_products_widget_args' ] );
		add_filter( 'woocommerce_rating_filter_count', [ $this, 'woocommerce_rating_filter_count' ], 10, 3 );

		add_filter( 'the_comments', [ $this, 'translate_product_ids' ] );
	}

	/**
	 * Add comment rating
	 *
	 * @param int $comment_id
	 */
	public function add_comment_rating( $comment_id ) {

		if ( isset( $_POST['comment_post_ID'] ) ) {

			$product_id = sanitize_text_field( $_POST['comment_post_ID'] );

			if ( 'product' === get_post_type( $product_id ) ) {

				$this->recalculate_comment_rating( $product_id );
			}
		}
	}

	/**
	 * Calculate rating field for comments based on reviews in all languages.
	 *
	 * @param int $product_id
	 */
	public function recalculate_comment_rating( $product_id ) {

		$translations          = $this->post_translations->get_element_translations( $product_id );
		$average_ratings_sum   = 0;
		$average_ratings_count = 0;
		$reviews_count         = 0;
		$ratings_count         = [];

		foreach ( $translations as $translation ) {
			$product = wc_get_product( $translation );

			$ratings      = WC_Comments::get_rating_counts_for_product( $product );
			$review_count = WC_Comments::get_review_count_for_product( $product );

			if ( is_array( $ratings ) ) {
				foreach ( $ratings as $rating => $count ) {
					$average_ratings_sum   += $rating * $count;
					$average_ratings_count += $count;
					if ( isset( $ratings_count[ $rating ] ) ) {
						$ratings_count[ $rating ] += $count;
					} else {
						$ratings_count[ $rating ] = $count;
					}
				}
			}

			if ( $review_count ) {
				$reviews_count += $review_count;
			} else {
				update_post_meta( $translation, self::WCML_AVERAGE_RATING_KEY, null );
				update_post_meta( $translation, self::WCML_REVIEW_COUNT_KEY, null );
				update_post_meta( $translation, self::WCML_RATING_COUNT_KEY, null );
			}
		}

		if ( $average_ratings_sum ) {

			$average_rating = number_format( $average_ratings_sum / $average_ratings_count, 2, '.', '' );

			foreach ( $translations as $translation ) {
				update_post_meta( $translation, self::WCML_AVERAGE_RATING_KEY, $average_rating );
				update_post_meta( $translation, self::WCML_REVIEW_COUNT_KEY, $reviews_count );
				update_post_meta( $translation, self::WCML_RATING_COUNT_KEY, $ratings_count );

				WC_Comments::clear_transients( $translation );
			}
		}

	}

	/**
	 * Filter WC reviews meta.
	 *
	 * @param null|array|string $value     get_metadata() should return a single value or array of values.
	 * @param int               $object_id Post ID.
	 * @param string            $meta_key  Meta key.
	 * @param bool              $single
	 *
	 * @return array|null|string Filtered metadata value, array of values, or null.
	 */
	public function filter_average_rating( $value, $object_id, $meta_key, $single ) {

		$filtered_value = $value;

		if ( in_array( $meta_key, [ self::WC_AVERAGE_RATING_KEY, self::WC_REVIEW_COUNT_KEY, self::WC_RATING_COUNT_KEY ], true ) && 'product' === get_post_type( $object_id ) ) {

			if ( ! metadata_exists( 'post', $object_id, self::WCML_RATING_COUNT_KEY ) ) {
				$this->recalculate_comment_rating( $object_id );
			}

			switch ( $meta_key ) {
				case self::WC_AVERAGE_RATING_KEY:
					$filtered_value = get_post_meta( $object_id, self::WCML_AVERAGE_RATING_KEY, $single );
					if ( empty( $filtered_value ) ) {
						$filtered_value = 0;
					}
					break;
				case self::WC_REVIEW_COUNT_KEY:
					if ( $this->is_reviews_in_all_languages( $object_id ) ) {
						$filtered_value = get_post_meta( $object_id, self::WCML_REVIEW_COUNT_KEY, $single );
					}
					break;
				case self::WC_RATING_COUNT_KEY:
					$filtered_value = get_post_meta( $object_id, self::WCML_RATING_COUNT_KEY, $single );
					if ( $single ) {
						$filtered_value = [ $filtered_value ];
					}
					break;
			}
		}

		return ! empty( $filtered_value ) || $filtered_value === 0 ? $filtered_value : $value;
	}

	/**
	 * Filters comment queries to display in all languages if needed
	 *
	 * @param string[]         $clauses
	 * @param WP_Comment_Query $obj
	 *
	 * @return string[]
	 */
	public function comments_clauses( $clauses, $obj ) {

		if ( $this->is_reviews_in_all_languages( $obj->query_vars['post_id'] ) ) {
			$ids = $this->get_translations_ids( $obj->query_vars['post_id'] );

			$clauses['where'] = str_replace( 'comment_post_ID = ' . $obj->query_vars['post_id'], 'comment_post_ID IN (' . DB::prepareIn( $ids, '%d' ) . ')', $clauses['where'] );
		}

		return $clauses;
	}

	/**
	 * Get list of translated ids for product
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	private function get_translations_ids( $product_id ) {

		$translations = $this->post_translations->get_element_translations( $product_id );

		return array_filter( $translations );

	}

	/**
	 * Display link to show rating in all/current language
	 */
	public function comments_link() {

		if ( is_product() ) {
			if ( $this->is_reviews_in_all_languages( get_the_ID() ) ) {
				$this->show_link_to_current_language_reviews();
			} else {
				$this->show_link_to_all_reviews();
			}
		}
	}

	/**
	 * @return bool
	 */
	private function is_reviews_in_all_languages_by_default_selected() {
		return (bool) $this->woocommerce_wpml->get_setting( 'reviews_in_all_languages', false );
	}

	/**
	 * Echoes link to product page with all reviews.
	 */
	private function show_link_to_all_reviews() {
		$comments_link                  = add_query_arg( [ 'clang' => 'all' ] );
		$all_languages_reviews_count    = $this->get_reviews_count( 'all' );
		$current_language_reviews_count = $this->get_reviews_count();

		if ( $all_languages_reviews_count > $current_language_reviews_count ) {
			/* translators: %s is the number of reviews */
			$comments_link_text = sprintf( __( 'Show reviews in all languages  (%s)', 'woocommerce-multilingual' ), $all_languages_reviews_count );
			echo '<p><a id="lang-comments-link" href="' . $comments_link . '" rel="nofollow" class="all-languages-reviews" >' . $comments_link_text . '</a></p>';
		}
	}

	/**
	 * Echoes link to product page with reviews in current language.
	 */
	private function show_link_to_current_language_reviews() {

		$current_language_reviews_count = $this->get_reviews_count();
		$current_language = $this->sitepress->get_current_language();

		$comments_link    = add_query_arg( [ 'clang' => $current_language ] );
		$language_details = $this->sitepress->get_language_details( $current_language );
		/* translators: %1$s is a language name and %2$s is the number of reviews */
		$comments_link_text = sprintf( __( 'Show only reviews in %1$s (%2$s)', 'woocommerce-multilingual' ), $language_details['display_name'], $current_language_reviews_count );

		echo '<p><a id="lang-comments-link" href="' . $comments_link . '" rel="nofollow" class="current-language-reviews" >' . $comments_link_text . '</a></p>';

	}

	/**
	 * Checks if comments needs filtering by language.
	 *
	 * @param bool             $filtered
	 * @param int              $post_id
	 * @param WP_Comment_Query $comment_query
	 * @return bool
	 */
	public function is_comment_query_filtered( $filtered, $post_id, $comment_query = null ) {

		if ( $this->is_reviews_in_all_languages( $post_id, $comment_query ) ) {
			$filtered = false;
		}

		return $filtered;
	}

	/**
	 * Add flag to comment description
	 *
	 * @param WP_Comment $comment
	 */
	public function add_comment_flag( $comment ) {
		$comment_language = $this->get_comment_language_on_all_languages_reviews( $comment );
		if ( $comment_language ) {
			printf(
				'<div style="float: left; padding: 6px 5px 0 0;"><img src="%s" width="18" height="12" alt="%s"></div>',
				esc_url( $this->sitepress->get_flag_url( $comment_language ) ),
				esc_attr( $this->sitepress->get_display_language_name( $comment_language ) )
			);
		}
	}

	/**
	 * @param WP_Comment $comment
	 */
	public function open_lang_div( $comment ) {
		$comment_language = $this->get_comment_language_on_all_languages_reviews( $comment );
		if ( $comment_language ) {
			printf( '<div lang="%s">', $comment_language );

			if ( self::is_translated( $comment ) ) {
				echo '<span class="wcml-review-translated">(' . esc_html__( 'translated', 'woocommerce-multilingual' ) . ')</span>';
			}
		}
	}

	/**
	 * @param WP_Comment $comment
	 */
	public function close_lang_div( $comment ) {
		if ( $this->get_comment_language_on_all_languages_reviews( $comment ) ) {
			print( '</div>' );
		}
	}

	/**
	 * Return review language code only if it displayed on mulilingual reviews list.
	 *
	 * @param WP_Comment $comment
	 * @return string|null Review language or null.
	 */
	private function get_comment_language_on_all_languages_reviews( $comment ) {
		if ( self::is_translated( $comment ) ) {
			return $this->sitepress->get_current_language();
		} elseif ( $this->is_reviews_in_all_languages( $comment->comment_post_ID ) ) {
			return $this->post_translations->get_element_lang_code( $comment->comment_post_ID );
		}
		return null;
	}

	/**
	 * Checks if reviews in all languages should be displayed.
	 *
	 * @param int              $product_id
	 * @param WP_Comment_Query $comment_query
	 *
	 * @return bool
	 */
	public function is_reviews_in_all_languages( $product_id, $comment_query = null ) {
		$reviewsLang = Obj::prop( 'clang', $_GET );
		$post_type   = Obj::path( [ 'query_vars', 'post_type' ], $comment_query );

		if ( ! $post_type && $product_id ) {
			$post_type = get_post_type( $product_id );
		}

		return (
				'all' === $reviewsLang
				|| ( ! $reviewsLang && $this->is_reviews_in_all_languages_by_default_selected() )
			) && 'product' === $post_type;
	}

	/**
	 * Return reviews count in language
	 *
	 * @param string|false $language
	 *
	 * @return int
	 */
	public function get_reviews_count( $language = false ) {

		remove_filter( 'get_post_metadata', [ $this, 'filter_average_rating' ], 10 );

		if ( ! metadata_exists( 'post', get_the_ID(), self::WCML_REVIEW_COUNT_KEY ) ) {
			$this->recalculate_comment_rating( get_the_ID() );
		}

		if ( 'all' === $language ) {
			$reviews_count = get_post_meta( get_the_ID(), self::WCML_REVIEW_COUNT_KEY, true );
		} else {
			$reviews_count = get_post_meta( get_the_ID(), self::WC_REVIEW_COUNT_KEY, true );
		}

		add_filter( 'get_post_metadata', [ $this, 'filter_average_rating' ], 10, 4 );

		return $reviews_count;
	}

	/**
	 * @param int             $comment_id
	 * @param WP_Comment|null $comment
	 */
	public function recalculate_average_rating_on_comment_hook( $comment_id, $comment ) {

		if ( ! $comment ) {
			$comment = get_comment( $comment_id );
		}

		if ( in_array( get_post_type( $comment->comment_post_ID ), [ 'product', 'product_variation' ] ) ) {
			$this->recalculate_comment_rating( (int) $comment->comment_post_ID );
		}
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function top_rated_products_widget_args( $args ) {
		$args['meta_key'] = self::WCML_AVERAGE_RATING_KEY;

		return $args;
	}

	/**
	 * @param string $label
	 * @param int $count
	 * @param int $rating
	 *
	 * @return string
	 */
	public function woocommerce_rating_filter_count( $label, $count, $rating ) {

		$ratingTerm = get_term_by( 'name', 'rated-' . $rating, 'product_visibility' );

		$productsCountInCurrentLanguage = $this->wpdb->get_var( $this->wpdb->prepare( "                
                SELECT COUNT( DISTINCT tr.object_id ) 
                FROM {$this->wpdb->term_relationships} tr
                LEFT JOIN {$this->wpdb->prefix}icl_translations t ON t.element_id = tr.object_id 
                WHERE tr.term_taxonomy_id = %d AND t.element_type='post_product' AND t.language_code = %s                 
        ", $ratingTerm->term_taxonomy_id, $this->sitepress->get_current_language() ) );

		return "({$productsCountInCurrentLanguage})";
	}

	/**
	 * @param int    $meta_id
	 * @param int    $comment_id
	 * @param string $meta_key
	 * @param string $meta_value
	 */
	public function maybe_duplicate_comment_rating( $meta_id, $comment_id, $meta_key, $meta_value ) {
		if ( 'rating' === $meta_key && wpml_get_setting_filter( null, 'sync_comments_on_duplicates' ) ) {
			remove_action( 'added_comment_meta', [ $this, 'maybe_duplicate_comment_rating' ], 10 );
			foreach ( $this->get_duplicated_comments( $comment_id ) as $duplicate ) {
				add_comment_meta( $duplicate, 'rating', $meta_value );

			}
			$product_id = get_comment( $comment_id )->comment_post_ID;
			$this->recalculate_comment_rating( $product_id );
			add_action( 'added_comment_meta', [ $this, 'maybe_duplicate_comment_rating' ], 10, 4 );
		}
	}

	/**
	 * @param int $comment_id
	 *
	 * @return array
	 */
	private function get_duplicated_comments( $comment_id ) {
		return $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT comment_id
				FROM {$this->wpdb->commentmeta}
				WHERE meta_key = '_icl_duplicate_of'
				AND meta_value = %d", $comment_id
			)
		);
	}

	public function no_index_all_reviews_page() {
			echo '<meta name="robots" content="noindex">';
	}

	/**
	 * @param WP_Comment[] $comments
	 *
	 * @return WP_Comment[]
	 */
	public function translate_product_ids( $comments ) {
		$convertProductId = function( $comment ) {
			if ( 'review' === Obj::prop( 'comment_type', $comment ) ) {
				$comment = Obj::assoc(
					'comment_post_ID',
					Ids::convert( Obj::prop( 'comment_post_ID', $comment ), 'product', true ),
					$comment
				);
			}

			return $comment;
		};

		return wpml_collect( $comments )
			->map( $convertProductId )
			->toArray();
	}

	/**
	 * @see \WCML\Reviews\Translations::translateReview
	 *
	 * @param WP_Comment $comment
	 *
	 * @return bool
	 */
	private static function is_translated( $comment ) {
		return (bool) Obj::prop( 'is_translated', $comment );
	}
}
