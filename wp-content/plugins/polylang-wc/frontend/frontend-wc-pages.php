<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages WC pages when they are used as home page.
 *
 * @since 1.0
 */
class PLLWC_Frontend_WC_Pages {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'pll_set_language_from_query', array( $this, 'pll_set_language_from_query' ), 5, 2 ); // Before Polylang.
	}

	/**
	 * Get the queried page_id ( if it exists ).
	 *
	 * If permalinks are used, WordPress does set and use $query->queried_object_id and sets $query->query_vars['page_id'] to 0
	 * and does set and use $query->query_vars['page_id'] if permalinks are not used :(
	 *
	 * @since 0.3.2
	 *
	 * @param WP_Query $query Instance of WP_Query.
	 * @return int page_id
	 */
	protected function get_page_id( $query ) {
		if ( ! empty( $query->query_vars['pagename'] ) && isset( $query->queried_object_id ) ) {
			return $query->queried_object_id;
		}

		if ( isset( $query->query_vars['page_id'] ) ) {
			return $query->query_vars['page_id'];
		}

		return 0; // No page queried.
	}

	/**
	 * Fixes query vars on translated front page when the front page displays the shop, my account or the checkout.
	 *
	 * @since 0.3.2
	 *
	 * @param PLL_Language|false $lang  False or language object.
	 * @param WP_Query           $query WP_Query object.
	 * @return PLL_Language|false
	 */
	public function pll_set_language_from_query( $lang, $query ) {
		$qvars     = $query->query_vars;
		$languages = PLL()->model->get_languages_list();
		$pages     = wp_list_pluck( $languages, 'page_on_front' );

		// Shop on front.
		if ( in_array( wc_get_page_id( 'shop' ), $pages ) ) {
			// Redirect the language page to the homepage when using a static front page.
			if (
				( PLL()->options['redirect_lang'] || PLL()->options['hide_default'] )
				&& $query->is_tax( 'language' )
				&& $this->is_front_page( $query )
			) {
				$lang = PLL()->model->get_language( $qvars['lang'] );
				$query->is_home              = false;
				$query->is_tax               = false;
				$query->is_page              = true;
				$query->is_post_type_archive = true;
				$query->set( 'page_id', $lang->page_on_front );
				$query->set( 'post_type', 'product' );
				unset( $query->query_vars['lang'] );
				// Reset queried object.
				$query->queried_object    = null;
				$query->queried_object_id = 0;
			}

			// Set the language when requesting a static front page.
			elseif ( ( $page_id = $this->get_page_id( $query ) ) && false !== $n = array_search( $page_id, $pages ) ) {
				$lang = $languages[ $n ];
				$query->is_home              = false;
				$query->is_page              = true;
				$query->is_post_type_archive = true;
				$query->set( 'page_id', $page_id );
				$query->set( 'post_type', 'product' );
			}

			// Multiple domains (when the url contains the page slug).
			elseif ( is_post_type_archive( 'product' ) && ! empty( PLL()->curlang ) ) {
				$query->is_page = true;
				$query->set( 'page_id', PLL()->curlang->page_on_front );
			}

			// Language set from the content.
			elseif ( is_post_type_archive( 'product' ) && ! empty( $qvars['lang'] ) && $lang = PLL()->model->get_language( $qvars['lang'] ) ) {
				$query->is_page = true;
				$query->set( 'page_id', $lang->page_on_front );
			}
		}

		// My Account and checkout endpoints.
		if ( array_intersect( array( wc_get_page_id( 'myaccount' ), wc_get_page_id( 'checkout' ) ), $pages ) && array_intersect( array_keys( $query->query ), WC()->query->get_query_vars() ) ) {
			if ( ! $this->get_page_id( $query ) ) {
				if ( empty( $qvars['lang'] ) || ! $lang = PLL()->model->get_language( $qvars['lang'] ) ) {
					// Language set from the content + language code hidden in the url.
					$lang = PLL()->model->get_language( PLL()->options['default_lang'] );
				}
				$query->is_home     = false;
				$query->is_tax      = false;
				$query->is_archive  = false;
				$query->is_page     = true;
				$query->is_singular = true;
				$query->set( 'page_id', $lang->page_on_front );
				// Reset queried object.
				$query->queried_object    = null;
				$query->queried_object_id = 0;
			}
			add_filter( 'redirect_canonical', '__return_false' );
			add_filter( 'pll_check_canonical_url', '__return_false' );
		}

		return $lang;
	}

	/**
	 * Tells if the given query corresponds to the front page.
	 * This method inspects `WP_Query::$query` and uses a list of query vars that can be set without changing the type
	 * of the page displayed. For example, the front page with `?rating_filter=5` is still the front page
	 * (`rating_filter` comes from WC's widget "Products by Rating list").
	 *
	 * @see WC_Widget_Layered_Nav_Filters
	 *
	 * @since 1.8.1
	 *
	 * @param WP_Query $query An instance of the main query.
	 * @return bool
	 */
	private function is_front_page( WP_Query $query ) {
		if ( empty( $query->query['lang'] ) ) {
			// This is not the front page you're looking for (handled by PLL).
			return false;
		}

		$vars = array(
			// WP.
			'cpage',
			'orderby',
			'page',
			'paged',
			'preview',
			// PLL.
			'lang',
			// WC.
			'max_price',
			'min_price',
			'rating_filter',
		);

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$vars[] = "filter_{$attribute->attribute_name}";
			$vars[] = "query_type_{$attribute->attribute_name}";
		}

		/**
		 * Allows to filter the list of query vars that can be set without changing the type of the page displayed.
		 *
		 * @since 1.8.1
		 *
		 * @param string[] $var   The list of query vars.
		 * @param WP_Query $query An instance of the main query.
		 */
		$vars = apply_filters( 'pllwc_front_page_query_vars', $vars, $query );

		return empty( array_diff_key( $query->query, array_flip( $vars ) ) );
	}
}
