<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Restrictions;

use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handler responsible for restricting access to generic content such as posts.
 *
 * @since 1.9.0
 */
class Posts {


	/** @var int[] memoization of post IDs that have been processed for restriction */
	private $content_restricted = array();

	/** @var array memoization of post content data indexed by post ID */
	private $restricted_post_content = array();

	/** @var int[] memoized array of sticky post IDs that perhaps need to be restricted */
	private $restricted_sticky_posts = array();

	/** @var array memoized array of restricted posts by term IDs by member */
	private $restricted_comments_by_post_id = array();


	/**
	 * Handles generic content restrictions.
	 *
	 * The constructor normally runs during `wp` action time.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		// handle content restriction according to the chosen restriction mode
		add_action( 'wp',            [ $this, 'handle_restriction_modes' ] );
		add_action( 'rest_api_init', [ $this, 'handle_restriction_modes' ] );

		// handle restrictions to individual posts or terms in REST API requests
		add_filter( 'rest_request_after_callbacks', [ $this, 'exclude_rest_restricted_content' ], 999, 3 );

		// adjust queries to account for restricted content
		add_filter( 'posts_clauses',  [ $this, 'handle_posts_clauses' ], 999, 2 );
		add_filter( 'get_terms_args', [ $this, 'handle_get_terms_args' ], 999, 2 );
		add_filter( 'terms_clauses',  [ $this, 'handle_terms_clauses' ], 999 );

		// handle post and page queries to exclude restricted content to non-members
		add_filter( 'pre_get_posts',       [ $this, 'exclude_restricted_posts' ], 999 );
		add_filter( 'option_sticky_posts', [ $this, 'exclude_restricted_sticky_posts' ], 999 );
		add_filter( 'get_pages',           [ $this, 'exclude_restricted_pages' ], 999 );

		// handle comment queries to hide comments to comment that is restricted to non-members
		add_filter( 'the_posts',        [ $this, 'exclude_restricted_content_comments' ], 999, 2 );
		add_filter( 'pre_get_comments', [ $this, 'exclude_restricted_comments' ], 999 );

		// handle single post previous/next pagination links
		add_filter( 'get_previous_post_where', [ $this, 'exclude_restricted_adjacent_posts' ], 1, 5 );
		add_filter( 'get_next_post_where',     [ $this, 'exclude_restricted_adjacent_posts' ], 1, 5 );

		// redirect to restricted content or product upon login
		add_filter( 'woocommerce_login_redirect', [ $this, 'redirect_to_member_content_upon_login' ], 40 );
	}


	/**
	 * Handles restriction modes.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function handle_restriction_modes() {
		global $post, $wp_query;

		$is_rest_api_request = doing_action( 'rest_api_init' ) && wc_memberships()->get_rest_api_instance()->is_rest_api_request();
		$restriction_mode    = wc_memberships()->get_restrictions_instance()->get_restriction_mode();

		if ( 'hide' !== $restriction_mode || $is_rest_api_request ) {

			// restrict the post by filtering the post object and replacing the content with a message and maybe excerpt
			add_action( 'the_post', array( $this, 'restrict_post' ), 0 );

			// ensure the restricted post content data is persisted even when third parties try to filter it
			add_filter( 'the_content', array( $this, 'handle_restricted_post_content_filtering' ), 999 );
		}

		if ( $is_rest_api_request ) {
			return;
		}

		if ( 'hide_content' === $restriction_mode ) {

			// maybe display a restricted notice for a taxonomy term
			add_action( 'loop_start', array( $this, 'display_restricted_taxonomy_term_notice' ), 1 );

			// ensure that RSS enclosures are restricted to avoid leaking of restricted embeds, etc.
			add_filter( 'rss_enclosure', array( $this, 'hide_restricted_content_feed_enclosures' ), 999 );

			// restrict content comments
			$this->hide_restricted_content_comments();

		} elseif ( 'redirect' === $restriction_mode ) {

			$term = $wp_query && ( $wp_query->is_tax() || $wp_query->is_category() || $wp_query->is_tag() ) ? get_queried_object() : null;

			if ( $term instanceof \WP_Term ) {
				$this->redirect_restricted_content( $term->term_id, 'taxonomy', $term->taxonomy );
			} elseif ( $post instanceof \WP_Post ) {
				$this->redirect_restricted_content( $post->ID, 'post_type', $post->post_type );
			}
		}
	}


	/**
	 * Hides restricted content comments (including product reviews).
	 *
	 * @since 1.9.0
	 */
	private function hide_restricted_content_comments() {
		global $post, $wp_query;

		if ( $post ) {

			if ( in_array( $post->post_type, array( 'product', 'product_variation' ), true ) ) {
				$restricted = wc_memberships_is_product_viewing_restricted() && ! current_user_can( 'wc_memberships_view_restricted_product',      $post->ID );
			} else {
				$restricted = wc_memberships_is_post_content_restricted()    && ! current_user_can( 'wc_memberships_view_restricted_post_content', $post->ID );
			}

			if ( $restricted ) {
				$wp_query->comment_count   = 0;
				$wp_query->current_comment = 999999;
			}
		}
	}


	/**
	 * Restricts content feed enclosures if the related post is restricted and current user doesn't have access.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param string $enclosure feed enclosure
	 * @return string
	 */
	public function hide_restricted_content_feed_enclosures( $enclosure )  {
		global $post;

		$can_view = true;

		if ( $enclosure && $post ) {
			if ( 'product' === get_post_type( $post ) ) {
				$can_view = wc_memberships_is_product_viewing_restricted( $post ) ? current_user_can( 'wc_memberships_view_restricted_product_content' ) && current_user_can( 'wc_memberships_view_delayed_product' )       : true;
			} else {
				$can_view = wc_memberships_is_post_content_restricted( $post )    ? current_user_can( 'wc_memberships_view_restricted_post_content' )    && current_user_can( 'wc_memberships_view_delayed_post_content' )  : true;
			}
		}

		return $can_view ? $enclosure : '';
	}


	/**
	 * Redirects restricted content based on content/product restriction rules.
	 *
	 * @see \WC_Memberships_Posts_Restrictions::redirect_to_member_content_upon_login()
	 *
	 * @since 1.9.0
	 *
	 * @param int $object_id ID of the object redirecting from (post ID, term ID)
	 * @param string $object_type type of object redirecting from (post_type, taxonomy)
	 * @param string $object_type_name name of the type of object redirecting form (e.g. post, product, product_cat, category...)
	 */
	private function redirect_restricted_content( $object_id, $object_type, $object_type_name ) {

		// bail out early if no valid ID
		if ( (int) $object_id < 1 ) {
			return;
		}

		$restricted       = false;
		$redirect_page_id = wc_memberships()->get_restrictions_instance()->get_restricted_content_redirect_page_id();

		if ( empty( $redirect_page_id ) ) {

			$restricted = false; // we don't have a page to redirect to (shouldn't happen)

		} elseif ( 'post_type' === $object_type ) {

			if ( (int) $object_id === $redirect_page_id ) {
				$restricted = false; // the restricted content page cannot be itself restricted
			} elseif ( ! is_shop() && in_array( $object_type_name, array( 'product', 'product_variation' ), true ) ) {
				$restricted = wc_memberships_is_product_viewing_restricted() && ! current_user_can( 'wc_memberships_view_restricted_product',      $object_id );
			} elseif ( is_singular() ) {
				$restricted = wc_memberships_is_post_content_restricted()    && ! current_user_can( 'wc_memberships_view_restricted_post_content', $object_id );
			}

		} elseif ( 'taxonomy' === $object_type ) {

			$terms    = array_merge( array( $object_id ), get_ancestors( $object_id, $object_type_name, $object_type ) );
			$taxonomy = $object_type_name;

			foreach ( $terms as $term_id ) {

				if ( 'product_cat' === $taxonomy ) {
					$restricted = wc_memberships_is_product_category_viewing_restricted( $term_id ) && ! current_user_can( 'wc_memberships_view_restricted_product_taxonomy_term', $taxonomy, $term_id );
				} else {
					$restricted = wc_memberships_is_term_restricted( $term_id, $taxonomy ) && ! current_user_can( 'wc_memberships_view_restricted_taxonomy_term', $taxonomy, $term_id );
				}

				if ( $restricted ) {

					$object_id = $term_id;
					break;
				}
			}
		}

		if ( $restricted ) {

			if ( 'post_type' === $object_type ) {

				// bail out as the above conditions evaluate a possible interaction with WC AJAX when the home page is restricted:
				if (    isset( $_GET['wc-ajax'] )
				     && defined( 'DOING_AJAX' )
				     && DOING_AJAX
				     && (int) $object_id === (int) get_option( 'page_on_front' )
				     && has_action( 'wp_ajax_nopriv_woocommerce_' . $_GET['wc-ajax'] ) ) {

					return;
				}

				// bail out if we are on a product category page but the post was not hidden from showing - otherwise this would redirect the whole category page!
				if (      'product' === $object_type_name
				     && ! is_singular( 'product' )
				     &&   is_tax( 'product_cat' )
				     && ! wc_memberships()->get_restrictions_instance()->hiding_restricted_products() ) {

					return;
				}
			}

			wp_redirect( wc_memberships()->get_restrictions_instance()->get_restricted_content_redirect_url( $object_id, $object_type, $object_type_name ) );
			exit;
		}
	}


	/**
	 * Determines if a RSS feed should be restricted (helper method).
	 *
	 * @since 1.11.0
	 *
	 * @param null $the_query
	 * @return bool
	 */
	private function is_feed_restricted( $the_query = null ) {
		global $wp_query;

		if ( null === $the_query ) {
			$the_query = $wp_query;
		}

		$feed_is_restricted = $the_query instanceof \WP_Query && $the_query->is_feed() && ! wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide_content' );

		/**
		 * Toggles whether the RSS feed should be restricted.
		 *
		 * @since 1.12.3
		 *
		 * @param bool $feed_is_restricted whether the feed should be restricted
		 * @param \WP_Query $the_query the query object
		 */
		return (bool) apply_filters( 'wc_memberships_is_feed_restricted', $feed_is_restricted, $the_query );
	}


	/**
	 * Displays content restricted notices when browsing restricted terms archives.
	 *
	 * Applies when the restriction mode is "Hide content only":
	 * @see \WC_Memberships_Posts_Restrictions::handle_restriction_modes()
	 *
	 * @internal
	 *
	 * @since 1.10.5
	 *
	 * @param \WP_Query $wp_query WordPress query object, passed by reference
	 */
	public function display_restricted_taxonomy_term_notice( $wp_query ) {

		if ( $wp_query instanceof \WP_Query && $wp_query->is_archive() && ! current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {

			$term = $restricted_term = $wp_query->get_queried_object();

			if ( $term instanceof \WP_Term ) {

				$message_code = '';
				$taxonomy     = $term->taxonomy;
				$terms        = array_merge( array( $term->term_id ), get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );

				foreach ( $terms as $term_id ) {

					if ( wc_memberships_is_term_restricted( $term_id, $taxonomy ) ) {

						$message_code = $this->get_restricted_taxonomy_term_message_code( $term_id, $taxonomy );

						if ( '' !== $message_code ) {

							$restricted_term = get_term( $term_id, $taxonomy );
							break;
						}
					}
				}

				// if the message code is non-empty, it means that the user has restricted or delayed access and one restriction message should be shown
				if ( '' !== $message_code ) {

					if ( $restricted_term instanceof \WP_Term ) {
						$args    = array( 'term' => $restricted_term );
						$term_id = $restricted_term->term_id;
					} else {
						$args    = array( 'term' => $term );
						$term_id = $term->term_id;
					}

					// if the message is for delayed access, we need to pass the access datetime to the message handler
					if ( Framework\SV_WC_Helper::str_ends_with( $message_code, 'delayed' ) ) {
						$args['access_time'] = wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_taxonomy_term( get_current_user_id(), $taxonomy, $term_id );
					}

					// finally output the message
				    echo \WC_Memberships_User_Messages::get_message_html( $message_code, $args );
				}
			}
		}
	}


	/**
	 * Returns a message code for a given restricted term (helper method).
	 *
	 * @since 1.10.5
	 *
	 * @param int $term_id term ID
	 * @param string $taxonomy term taxonomy
	 * @return string message code or empty string
	 */
	private function get_restricted_taxonomy_term_message_code( $term_id, $taxonomy ) {

		$message_code = '';

		if ( 'product_cat' === $taxonomy ) {

			if ( ! current_user_can( 'wc_memberships_view_restricted_product_taxonomy_term', $taxonomy, $term_id ) ) {
				$message_code = 'product_category_viewing_restricted';
			} elseif ( ! current_user_can( 'wc_memberships_view_delayed_product_taxonomy_term', $taxonomy, $term_id ) ) {
				$message_code = 'product_category_viewing_delayed';
			}

		} elseif ( ! current_user_can( 'wc_memberships_view_restricted_taxonomy_term', $taxonomy, $term_id ) ) {
			$message_code = 'content_category_restricted';
		} elseif( ! current_user_can( 'wc_memberships_view_delayed_taxonomy_term', $taxonomy, $term_id ) ) {
			$message_code = 'content_category_delayed';
		}

		return $message_code;
	}


	/**
	 * Redirects user to restricted content after successful login.
	 *
	 * @see \WC_Memberships_Posts_Restrictions::redirect_restricted_content()
	 * This callback must have a higher priority than custom login redirects:
	 * @see \WC_Memberships_Frontend::redirect_to_page_upon_woocommerce_login()
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $redirect_to URL to redirect to
	 * @return string
	 */
	public function redirect_to_member_content_upon_login( $redirect_to ) {

		$restricted_content_link = null;

		if ( isset( $_GET['wcm_redirect_to'], $_GET['wcm_redirect_id'] ) ) {

			if ( in_array( $_GET['wcm_redirect_to'], get_post_types(), true ) ) {
				$restricted_content_link = get_permalink( (int) $_GET['wcm_redirect_id'] );
			} elseif ( taxonomy_exists( $_GET['wcm_redirect_to'] ) ) {
				$restricted_content_link = get_term_link( (int) $_GET['wcm_redirect_id'], $_GET['wcm_redirect_to'] );
			}
		}

		if ( '' !== $restricted_content_link && is_string( $restricted_content_link ) ) {
			$redirect_to = $restricted_content_link;
		}

		return $redirect_to;
	}


	/**
	 * Excludes restricted post types, taxonomies & terms by altering posts query clauses.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $pieces SQL clause pieces
	 * @param \WP_Query $wp_query instance of WP_Query
	 * @return array modified pieces
	 */
	public function handle_posts_clauses( $pieces, \WP_Query $wp_query ) {

		// - bail out if:
		//  a) user is an admin / can access all restricted content
		//  b) query is for user memberships or membership plans post types
		//  c) the following applies:
		//      1. we are on products query;
		//      2. restriction mode is not "hide" completely;
		//      3. we are not hiding restricted products from archive and search
		if (    current_user_can( 'wc_memberships_access_all_restricted_content' )
		     || in_array( $wp_query->get( 'post_type' ), array( 'wc_user_membership', 'wc_membership_plan' ), true )
		     || ( ! ( $this->is_feed_restricted( $wp_query ) || wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) && ! ( wc_memberships()->get_restrictions_instance()->hiding_restricted_products() && 'product_query' === $wp_query->get( 'wc_query' ) ) ) ) {

			return $pieces;
		}

		$conditions = wc_memberships()->get_restrictions_instance()->get_user_content_access_conditions();

		// some post types are restricted: exclude them from the query
		if ( ! empty( $conditions['restricted']['post_types'] ) && is_array( $conditions['restricted']['post_types'] ) ) {
			$pieces['where'] .= $this->exclude_restricted_posts_types( $conditions['restricted']['post_types'] );
		}

		// some taxonomies are restricted: exclude them from query
		if ( ! empty( $conditions['restricted']['taxonomies'] ) && is_array( $conditions['restricted']['taxonomies'] ) ) {
			$pieces['where'] .= $this->exclude_restricted_taxonomies( $conditions['restricted']['taxonomies'] );
		}

		// exclude taxonomy terms
		if ( ! empty( $conditions['restricted']['terms'] ) && is_array( $conditions['restricted']['terms'] ) ) {
			$pieces['where'] .= $this->exclude_restricted_terms( $conditions['restricted']['terms'] );
		}

		return $pieces;
	}


	/**
	 * Excludes restricted post types from the query.
	 *
	 * @since 1.9.0
	 *
	 * @param string[] $restricted_post_types conditions
	 * @return string SQL clause
	 */
	private function exclude_restricted_posts_types( array $restricted_post_types ) {
		global $wpdb;

		$post_type_taxonomies = $this->get_taxonomies_for_post_types( $restricted_post_types );
		$granted_posts        = wc_memberships()->get_restrictions_instance()->get_user_granted_posts( $restricted_post_types );
		$granted_terms        = wc_memberships()->get_restrictions_instance()->get_user_granted_terms( $post_type_taxonomies );
		$granted_taxonomies   = array_intersect( $restricted_post_types, $post_type_taxonomies );

		// no special handling: simply restrict access to all the restricted post types
		if ( empty( $granted_posts ) && empty( $granted_terms ) && empty( $granted_taxonomies ) ) {

			$post_types = implode( ', ', array_fill( 0, count( $restricted_post_types ), '%s' ) );
			$clause     = $wpdb->prepare( " AND $wpdb->posts.post_type NOT IN ($post_types) ", $restricted_post_types );

		// while general access to these post types is restricted,
		// there are extra rules that grant the user access to some taxonomies, terms or posts in one or more restricted post types
		} else {

			$post_types = implode( ', ', array_fill( 0, count( $restricted_post_types ), '%s' ) );

			// Prepare main subquery, which gets all post IDs with the restricted post types.
			// The main idea behind the following queries is as follows:
			// 1. Instead of excluding post types, use a subquery to get IDs of all posts of the restricted post types and exclude them from the results.
			// 2. If user has access to specific posts, taxonomies or terms that would be restricted by the post type, use subqueries to exclude posts that user should have access to from the exclusion list.
			$subquery  = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type IN ($post_types)", $restricted_post_types );
			// allow access to whole taxonomies
			$subquery .= $this->get_taxonomy_access_where_clause( $granted_taxonomies );
			// allow access to specific terms
			$subquery .= $this->get_term_access_where_clause( $granted_terms );
			// allow access to specific posts
			$subquery .= $this->get_post_access_where_clause( $granted_posts );

			// we are checking that post ID is not one of the restricted post IDs:
			$clause = " AND $wpdb->posts.ID NOT IN ($subquery) ";
		}

		return $clause;
	}


	/**
	 * Excludes restricted taxonomies from the query.
	 *
	 * @since 1.9.0
	 *
	 * @param string[] $restricted_taxonomies conditions
	 * @return string SQL clause
	 */
	private function exclude_restricted_taxonomies( array $restricted_taxonomies ) {
		global $wpdb;

		$clause              = '';
		$taxonomy_post_types = array();

		foreach ( $restricted_taxonomies as $taxonomy ) {
			if ( $the_taxonomy = get_taxonomy( $taxonomy ) ) {
				$taxonomy_post_types[ $taxonomy ] = $this->get_post_types_for_taxonomies( (array) $taxonomy );
			}
		}

		if ( ! empty( $taxonomy_post_types ) ) {

			// Use case statement to check if the post type for the object is registered for the restricted taxonomy.
			// If it is not, then don't restrict.
			// This fixes issues when a taxonomy was once registered for a post type but is not anymore, but restriction rules still apply to that post type via term relationships in database.
			$case       = '';
			// main taxonomy query is always the same, regardless if user has access to specific terms or posts under these taxonomies
			$taxonomies = implode( ', ', array_fill( 0, count( $restricted_taxonomies ), '%s' ) );

			foreach ( $taxonomy_post_types as $tax => $post_types ) {

				$args                   = array_merge( array( $tax ), $post_types );
				$post_types_placeholder = implode( ', ', array_fill( 0, count( $post_types ), '%s' ) );

				$case .= $wpdb->prepare( " WHEN $wpdb->term_taxonomy.taxonomy = %s THEN $wpdb->posts.post_type IN ( $post_types_placeholder )", $args );
			}

			$subquery = $wpdb->prepare( "
				SELECT object_id FROM $wpdb->term_relationships
				LEFT JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
				LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
				WHERE CASE $case END
				AND $wpdb->term_taxonomy.taxonomy IN ($taxonomies)
			", $restricted_taxonomies );

			$granted_posts = wc_memberships()->get_restrictions_instance()->get_user_granted_posts( $this->get_post_types_for_taxonomies( $restricted_taxonomies ) );
			$granted_terms = wc_memberships()->get_restrictions_instance()->get_user_granted_terms( $restricted_taxonomies );

			// It looks like while general access to these taxonomies is restricted,
			// there are some rules that grant the user access to some terms or posts in one or more restricted taxonomies.
			if ( ! empty( $granted_terms ) || ! empty( $granted_posts ) ) {
				// allow access to specific terms
				$subquery .= $this->get_term_access_where_clause( $granted_terms );
				// allow access to specific posts
				$subquery .= $this->get_post_access_where_clause( $granted_posts );
			}

			$clause = " AND $wpdb->posts.ID NOT IN ($subquery) ";
		}

		return $clause;
	}


	/**
	 * Excludes restricted terms from the query.
	 *
	 * @since 1.9.0
	 *
	 * @param string[]|int[] $restricted_terms conditions
	 * @return string SQL clause
	 */
	private function exclude_restricted_terms( array $restricted_terms ) {
		global $wpdb;

		$clause     = '';
		$term_ids   = array( array() );
		$taxonomies = array_keys( $restricted_terms );

		foreach ( $restricted_terms as $taxonomy => $terms ) {
			$term_ids[] = $terms;
		}

		$term_ids = array_unique( array_merge( ...$term_ids ) );

		if ( ! empty( $term_ids ) ) {

			$taxonomy_post_types = array();

			foreach ( $taxonomies as $taxonomy ) {
				if ( get_taxonomy( $taxonomy ) ) {
					$taxonomy_post_types[ $taxonomy ] = $this->get_post_types_for_taxonomies( (array) $taxonomy );
				}
			}

			if ( ! empty ( $taxonomy_post_types ) ) {

				// main term query is always the same, regardless if user has access
				// to specific posts under with these terms
				$taxonomy_terms = implode( ', ', array_fill( 0, count( $term_ids ), '%d' ) );

				// Use case statement to check if the post type for the object is registered for the restricted taxonomy.
				// If it is not, then don't restrict.
				// This fixes issues when a taxonomy was once registered for a post type but is not anymore, but restriction rules still apply to that post type via term relationships in database.
				$case = '';

				foreach ( $taxonomy_post_types as $tax => $post_types ) {

					$args                   = array_merge( array( $tax ), $post_types );
					$post_types_placeholder = implode( ', ', array_fill( 0, count( $post_types ), '%s' ) );

					$case .= $wpdb->prepare( " WHEN $wpdb->term_taxonomy.taxonomy = %s THEN $wpdb->posts.post_type IN ( $post_types_placeholder )", $args );
				}

				$subquery = $wpdb->prepare( "
					SELECT object_id FROM $wpdb->term_relationships
					LEFT JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
					LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
					WHERE CASE $case END
					AND term_id IN ($taxonomy_terms)
				", $term_ids );

				$all_taxonomy_post_types = $this->get_post_types_for_taxonomies( $taxonomies );
				$granted_posts           = wc_memberships()->get_restrictions_instance()->get_user_granted_posts( $all_taxonomy_post_types );

				// It looks like while general access to these terms is restricted,
				// there are some rules that grant the user access to some posts in one or more restricted terms.
				if ( ! empty( $granted_posts ) ) {
					$subquery .= $this->get_post_access_where_clause( $granted_posts );
				}

				$clause = " AND $wpdb->posts.ID NOT IN ($subquery) ";
			}
		}

		return $clause;
	}


	/**
	 * Hides restricted content in WordPress REST API responses when "Hide completely" is the restriction mode.
	 *
	 * The REST API needs separate handling when individual content is queried.
	 * Restricted content will produce a 404 error response to GET requests, to be coherent with front end handling.
	 * Other restrictions modes are handled by standard WordPress callbacks in this class.
	 * @see Posts::handle_restriction_modes()
	 *
	 * @internal
	 *
	 * @since 1.19.1
	 *
	 * @param null|\WP_REST_Response|\WP_Error $response default response to be returned
	 * @param array $handler route handler data used for the request
	 * @param \WP_REST_Request $request request used to generate the response
	 * @return null|\WP_REST_Response|\WP_Error
	 */
	public function exclude_rest_restricted_content( $response, $handler, $request ) {

		// don't bother if response is already a REST API error
		if ( ! $response instanceof \WP_REST_Response || $response->get_status() >= 300 ) {
			return $response;
		}

		// sanity checks: we need only to handle GET requests
		if ( ! $request instanceof \WP_REST_Request || 'GET' !== $request->get_method() ) {
			return $response;
		}

		$response_data = is_callable( [ $response, 'get_data' ] ) ? $response->get_data() : [];

		// default content values
		$post_type         = '';
		$taxonomy          = '';
		$content_id        = 0;
		$parent_content_id = 0;

		// make sure we are reading from an array
		if ( is_array( $response_data ) ) {

			if ( isset( $response_data['type'] ) && is_string( $response_data['type'] ) ) {
				$post_type = $response_data['type'];
			}

			if ( isset( $response_data['taxonomy'] ) && is_string( $response_data['taxonomy'] ) ) {
				$taxonomy = $response_data['taxonomy'];
			}

			if ( isset( $response_data['id'] ) && is_numeric( $response_data['id'] ) ) {
				$content_id = (int) $response_data['id'];
			}

			if ( isset( $response_data['post'] ) && is_numeric( $response_data['post'] ) ) {
				$parent_content_id = (int) $response_data['post'];
			}
		}

		$content_type = '' !== $post_type ? $post_type : $taxonomy;

		// we only need to handle content restricted completely as other callbacks in this class will handle partial restrictions already
		if ( 'comment' !== $content_type && ! wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) {
			return $response;
		}

		// handle individual content pieces only
		if ( '' !== $content_type && $content_id > 0 ) {

			// individual comments
			if ( 'comment' === $content_type ) {

				$restricted_posts = wc_memberships()->get_restrictions_instance()->get_user_restricted_posts();

				if ( $parent_content_id > 0 && is_array( $restricted_posts ) && in_array( $parent_content_id, $restricted_posts, false ) ) {

					$response = new \WP_Error(
						'rest_comment_invalid_id',
						_x( 'Invalid comment ID', 'REST API error message for restricted post comment requests', 'woocommerce-memberships' ),
						[ 'status' => 404 ]
					);
				}

			// posts or pages
			} elseif ( array_key_exists( $content_type, get_post_types() ) ) {

				$restricted_posts = wc_memberships()->get_restrictions_instance()->get_user_restricted_posts();

				if ( is_array( $restricted_posts ) && in_array( $content_id, $restricted_posts, false ) ) {

					$response = new \WP_Error(
						'rest_post_invalid_id',
						_x( 'Invalid post ID', 'REST API error message for restricted post requests', 'woocommerce-memberships' ),
						[ 'status' => 404 ]
					);
				}

			// terms
			} elseif ( array_key_exists( $content_type, get_taxonomies() ) ) {

				$restricted_terms = wc_memberships()->get_restrictions_instance()->get_user_restricted_terms( $content_type );

				if ( is_array( $restricted_terms ) && in_array( $content_id, $restricted_terms, false ) ) {

					$response = new \WP_Error(
						'rest_term_invalid',
						_x( 'Term does not exist.', 'REST API error message for restricted term requests', 'woocommerce-memberships' ),
						[ 'status' => 404 ]
					);
				}
			}
		}

		return $response;
	}


	/**
	 * Hides restricted posts/products based on content/product restriction rules.
	 *
	 * This method works by modifying the $query object directly.
	 * Since WP_Query does not support excluding whole post types or taxonomies, we need to use custom SQL clauses for them.
	 * Also, tax_query is not respected on is_singular(), so we need to use custom SQL for specific term restrictions as well.
	 * @see \WC_Memberships_Restrictions::get_user_content_access_conditions()
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Query $wp_query instance of WP_Query
	 */
	public function exclude_restricted_posts( \WP_Query $wp_query ) {

		if ( ! current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {

			// restriction mode is set to "hide completely" or we are on a feed:
		    if ( $this->is_feed_restricted( $wp_query ) || wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) {

				$restricted_posts = wc_memberships()->get_restrictions_instance()->get_user_restricted_posts();

				// exclude restricted posts and products from queries
				if ( ! empty( $restricted_posts ) ) {
					$wp_query->set( 'post__not_in', array_unique( array_merge(
						$wp_query->get( 'post__not_in' ),
						$restricted_posts
					) ) );
				}

			// products should be hidden in the catalog and search content if related option is set:
			} elseif ( 'product_query' === $wp_query->get( 'wc_query' ) && wc_memberships()->get_restrictions_instance()->hiding_restricted_products() ) {

				$conditions = wc_memberships()->get_restrictions_instance()->get_user_content_access_conditions();

				if ( isset( $conditions['restricted']['posts']['product'] ) ) {
					$wp_query->set( 'post__not_in', array_unique( array_merge(
						$wp_query->get( 'post__not_in' ),
						$conditions['restricted']['posts']['product']
					) ) );
				}
			}
		}
	}


	/**
	 * Removes sticky posts from ever showing up when using the "hide completely" restriction mode and the user doesn't have access.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param int[] $sticky_posts array of sticky post IDs
	 * @return int[]
	 */
	public function exclude_restricted_sticky_posts( $sticky_posts ) {
		global $wp_query;

		if ( ! empty( $sticky_posts ) ) {

			if ( ! empty( $this->restricted_sticky_posts ) && is_array( $this->restricted_sticky_posts ) ) {

				$sticky_posts = $this->restricted_sticky_posts;

			} elseif ( $this->is_feed_restricted( $wp_query ) || wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) {

				$restricted_sticky_posts = array();

				// avoid infinite filter loops as the capability check might incur checking the sticky posts again
				remove_filter( 'option_sticky_posts', array( $this, 'exclude_restricted_sticky_posts' ), 999 );

				foreach ( $sticky_posts as $sticky_post_id ) {
					if ( is_numeric( $sticky_post_id ) && ! current_user_can( 'wc_memberships_view_restricted_post_content', $sticky_post_id ) ) {
						$restricted_sticky_posts[] = $sticky_post_id;
					}
				}

				if ( ! empty( $restricted_sticky_posts ) ) {
					$sticky_posts = array_diff( $sticky_posts, $restricted_sticky_posts );
				}

				// reinstate the current filter
				add_filter( 'option_sticky_posts', array( $this, 'exclude_restricted_sticky_posts' ), 999 );

				$this->restricted_sticky_posts = $sticky_posts;
			}
		}

		return $sticky_posts;
	}


	/**
	 * Excludes restricted pages from `get_pages()` calls.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post[] $pages indexed array of page objects
	 * @return \WP_Post[]
	 */
	public function exclude_restricted_pages( $pages ) {
		global $wp_query;

		// sanity check: if restriction mode is not to "hide completely" (and we are not on a feed), return all pages
		if ( $this->is_feed_restricted( $wp_query ) || wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) {

			foreach ( $pages as $index => $page ) {
				if (    ! current_user_can( 'wc_memberships_view_restricted_post_content', $page->ID )
				     && ! current_user_can( 'wc_memberships_view_delayed_post_content',    $page->ID ) ) {
					unset( $pages[ $index ] );
				}
			}

			$pages = array_values( $pages );
		}

		return $pages;
	}


	/**
	 * Excludes restricted taxonomies by filtering `terms_clauses`.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $pieces terms query SQL clauses (associative array)
	 * @return array modified clauses
	 */
	public function handle_terms_clauses( $pieces ) {
		global $wpdb, $wp_query;

		// sanity check: if restriction mode is not "hide all content", return all posts
		if (    ! current_user_can( 'wc_memberships_access_all_restricted_content' )
		     &&   ( $this->is_feed_restricted( $wp_query ) || wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) ) {

			$conditions = wc_memberships()->get_restrictions_instance()->get_user_content_access_conditions();

			if ( ! empty( $conditions['restricted']['taxonomies'] ) ) {

				$restricted_taxonomies = $conditions['restricted']['taxonomies'];
				$granted_terms         = wc_memberships()->get_restrictions_instance()->get_user_granted_terms( $restricted_taxonomies );
				// main taxonomy query is always the same, regardless if user has access to specific terms under these taxonomies
				$taxonomies            = implode( ', ', array_fill( 0, count( $restricted_taxonomies ), '%s' ) );
				$subquery              = $wpdb->prepare("
					SELECT sub_t.term_id FROM $wpdb->terms AS sub_t
					INNER JOIN $wpdb->term_taxonomy AS sub_tt ON sub_t.term_id = sub_tt.term_id
					WHERE sub_tt.taxonomy IN ($taxonomies)
				", $restricted_taxonomies );

				// it looks like while general access to these taxonomies is restricted, there are some rules that grant the user access to some terms or posts in one or more restricted taxonomies
				if ( ! empty( $granted_terms ) ) {
					// allow access to specific terms
					$subquery .= $this->get_term_access_where_clause( $granted_terms, 'taxonomies' );
				}

				$pieces['where'] .= " AND t.term_id NOT IN ($subquery) ";
			}
		}

		return $pieces;
	}


	/**
	 * Adjusts `get_terms` arguments, exclude restricted terms.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $args query arguments
	 * @param string|array $taxonomies the taxonomies for the queried terms
	 * @return array
	 */
	public function handle_get_terms_args( $args, $taxonomies ) {
		global $wp_query;

		// sanity check: if restriction mode is not to "hide all content", return all posts
		if (    ! current_user_can( 'wc_memberships_access_all_restricted_content' )
		     &&   ( $this->is_feed_restricted( $wp_query ) || wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) ) {

			$conditions = wc_memberships()->get_restrictions_instance()->get_user_content_access_conditions();
			$conditions = isset( $conditions['restricted']['terms'] ) && is_array( $conditions['restricted']['terms'] ) ? $conditions['restricted']['terms'] : array();

			if ( ! empty( $conditions ) && array_intersect( array_keys( $conditions ), $taxonomies ) ) {

				$args['exclude'] = $args['exclude'] ? wp_parse_id_list( $args['exclude'] ) : array();

				foreach ( $conditions as $tax => $terms ) {
					$args['exclude'] = array_unique( array_merge( $terms, $args['exclude'] ) );
				}
			}
		}

		return $args;
	}


	/**
	 * Handles exclude taxonomies WHERE SQL clause.
	 *
	 * @since 1.9.0
	 *
	 * @param array $taxonomies array of taxonomies
	 * @return string SQL clause
	 */
	private function get_taxonomy_access_where_clause( $taxonomies ) {
		global $wpdb;

		if ( ! empty( $taxonomies ) ) {

			$term_taxonomies = implode( ', ', array_fill( 0, count( $taxonomies ), '%s' ) );
			$subquery        = $wpdb->prepare( "
				SELECT object_id FROM $wpdb->term_relationships
				LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
				WHERE $wpdb->term_taxonomy.taxonomy IN ($term_taxonomies)
			", $taxonomies );

			$clause          = " AND $wpdb->posts.ID NOT IN ($subquery) ";

		} else {

			$clause          = '';
		}

		return $clause;
	}


	/**
	 * Handles exclude term IDs WHERE SQL clause.
	 *
	 * @since 1.9.0
	 *
	 * @param int[] $term_ids array of term IDs
	 * @param string $query_type optional, either 'posts' (default) or 'taxonomies'
	 * @return string SQL clause
	 */
	private function get_term_access_where_clause( $term_ids, $query_type = 'posts' ) {
		global $wpdb;

		$clause = '';

		if ( ! empty( $term_ids ) ) {

			$placeholder = implode( ', ', array_fill( 0, count( $term_ids ), '%d' ) );

			if ( 'posts' === $query_type ) {

				$subquery = $wpdb->prepare( "
					SELECT object_id FROM $wpdb->term_relationships
					LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
					WHERE term_id IN ($placeholder)
				", $term_ids );

				$clause   = " AND $wpdb->posts.ID NOT IN ( " . $subquery . " ) ";

			} elseif ( 'taxonomies' === $query_type ) {

				$clause = $wpdb->prepare( " AND sub_t.term_id NOT IN ($placeholder) ", $term_ids );
			}
		}

		return $clause;
	}


	/**
	 * Handles exclude post IDs WHERE SQL clause.
	 *
	 * @since 1.9.0
	 *
	 * @param int[] $post_ids Array of post IDs
	 * @return string SQL clause
	 */
	private function get_post_access_where_clause( $post_ids ) {
		global $wpdb;

		if ( ! empty( $post_ids ) ) {
			$placeholder = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
			$clause      = $wpdb->prepare( " AND ID NOT IN ($placeholder)", $post_ids );
		} else {
			$clause      = '';
		}

		return $clause;
	}


	/**
	 * Helper method that returns taxonomies that apply to provided post types.
	 *
	 * @since 1.9.0
	 *
	 * @param string[] $post_types array of post types
	 * @return string[] array with taxonomy names
	 */
	private function get_taxonomies_for_post_types( $post_types ) {

		$taxonomies = array( array() );

		foreach ( $post_types as $post_type ) {
			$taxonomies[] = get_object_taxonomies( $post_type );
		}

		return array_unique( array_merge( ...$taxonomies ) );
	}


	/**
	 * Helper method that returns post types that the provided taxonomies are registered for.
	 *
	 * @since 1.9.0
	 *
	 * @param string[] $taxonomies array of taxonomy names
	 * @return string[] array with post types
	 */
	private function get_post_types_for_taxonomies( $taxonomies ) {

		$post_types = array();

		foreach ( $taxonomies as $taxonomy ) {
			if ( $the_taxonomy = get_taxonomy( $taxonomy ) ) {
				foreach ( $the_taxonomy->object_type as $object_type ) {
					$post_types[] = $object_type;
				}
			}
		}

		return ! empty( $post_types ) ? array_unique( $post_types ) : array();
	}


	/**
	 * Restricts a post based on content restriction rules.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $post the post object, passed by reference
	 */
	public function restrict_post( $post ) {

		if (    ! in_array( $post->ID, $this->content_restricted, false )
		     &&   wc_memberships_is_post_content_restricted( $post->ID ) ) {

			$message_code = null;

			// current user is an admin user: remind them they might be viewing restricted content
			if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post->ID ) ) {
				$message_code = 'restricted';
			// current user has delayed access
			} elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post->ID ) ) {
				$message_code = 'delayed';
			}

			if ( null !== $message_code ) {

				$args = array(
					'post'         => $post,
					'message_type' => $message_code,
				);

				if ( 'delayed' === $message_code ) {
					$args['access_time'] = wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_post( get_current_user_id(), $post->ID );
				}

				$message_code = \WC_Memberships_User_Messages::get_message_code_shorthand_by_post_type( $post, $args );
				$content      = \WC_Memberships_User_Messages::get_message_html( $message_code, $args );

				$this->restrict_post_content( $post, $content );
				$this->restrict_post_comments( $post );
			}
		}

		// flag post processed for restrictions
		$this->content_restricted[] = (int) $post->ID;
	}


	/**
	 * Restricts post content.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $post the post object, passed by reference
	 * @param string $restricted_content the new content HTML
	 */
	private function restrict_post_content( \WP_Post $post, $restricted_content ) {
		global $page, $pages, $multipages, $numpages;

		// update the post object passed by reference
		$post->post_content = $restricted_content;
		$post->post_excerpt = $restricted_content;

		/* @see \WP_Query::setup_postdata() for globals being updated here*/
		$page       = 1;
		$pages      = array( $restricted_content );
		$multipages = 0;
		$numpages   = 1;

		/* @see \WC_Memberships_Posts_Restrictions::handle_restricted_post_content_filtering() */
		$this->restricted_post_content[ $post->ID ] = $restricted_content;
	}


	/**
	 * Closes comments when post content is restricted.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $post the post object, passed by reference
	 */
	private function restrict_post_comments( \WP_Post $post ) {

		$post->comment_status = 'closed';
		$post->comment_count  = 0;
	}


	/**
	 * Makes sure the restricted content data is persisted.
	 *
	 * Some page builders or third party code may still attempt to change entirely the post content by filtering `the_content`.
	 * This late `the_content` callback method will reinstate any restricted content rendered previously by Memberships, voiding any foreign filter on it.
	 *
	 * @see \WC_Memberships_Posts_Restrictions::$restricted_post_content()
	 *
	 * @internal
	 *
	 * @since 1.10.4
	 *
	 * @param string $the_restricted_content
	 * @return string may contain HTML
	 */
	public function handle_restricted_post_content_filtering( $the_restricted_content ) {
		global $post;

		if ( $post && ! empty( $post->ID ) && array_key_exists( $post->ID, $this->restricted_post_content ) ) {
			/* @see \WC_Memberships_User_Messages::get_message_html() for filters to change or append HTML to the restricted content */
			$the_restricted_content = $this->restricted_post_content[ $post->ID ];
		}

		return $the_restricted_content;
	}


	/**
	 * Handles restricted posts in queries for adjacent (previous/next) posts.
	 *
	 * These queries are normally used in building prev/next post links in single post views.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $where_clause `WHERE` clause in the SQL
	 * @param bool $in_same_term whether post should be in a same taxonomy term (optional)
	 * @param int[] $excluded_terms array of excluded term IDs (optional)
	 * @param string $taxonomy taxonomy name used to identify the term used when `$in_same_term` is true (optional)
	 * @param \WP_Post $post related post object the adjacent posts are retrieved for
	 * @return string updated `WHERE` clause
	 */
	public function exclude_restricted_adjacent_posts( $where_clause, $in_same_term, $excluded_terms, $taxonomy, $post ) {

		if ( '' !== $where_clause
		     && $post instanceof \WP_Post
		     && ! current_user_can( 'wc_memberships_access_all_restricted_content' )
		     && wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) {

			$restricted_post_ids = wc_memberships()->get_restrictions_instance()->get_user_restricted_posts( $post->post_type );
			$restricted_post_ids = ! empty( $restricted_post_ids ) ? Strings_Helper::esc_sql_in_ids( $restricted_post_ids ) : null;

			if ( ! empty( $restricted_post_ids ) ) {

				$where_clause .= " AND p.ID NOT IN ({$restricted_post_ids}) ";
			}
		}

		return $where_clause;
	}


	/**
	 * Excludes restricted comments from comment feed.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post[] $posts array of posts
	 * @param \WP_Query $query instance of query
	 * @return \WP_Post[]
	 */
	public function exclude_restricted_content_comments( $posts, \WP_Query $query ) {

		if ( ! empty( $query->comment_count ) && is_comment_feed() ) {

			foreach ( $query->comments as $key => $comment ) {

				$post_id = (int) $comment->comment_post_ID;

				if ( in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
					// products
					$can_view = current_user_can( 'wc_memberships_view_restricted_product',      $post_id );
				} else {
					// posts
					$can_view = current_user_can( 'wc_memberships_view_restricted_post_content', $post_id );
				}

				// if not, exclude this comment from the feed
				if ( ! $can_view ) {
					unset( $query->comments[ $key ] );
				}
			}

			// re-index and re-count comments
			$query->comments      = array_values( $query->comments );
			$query->comment_count = count( $query->comments );
		}

		return $posts;
	}


	/**
	 * Filters the comment query to exclude posts the user doesn't have access to.
	 *
	 * @internal
	 *
	 * @since 1.9.6
	 *
	 * @param \WP_Comment_Query $comment_query the comment query
	 */
	public function exclude_restricted_comments( \WP_Comment_Query $comment_query ) {
		global $post;

		/**
		 * Filters the restrictable comment types.
		 *
		 * @since 1.10.0
		 *
		 * @param string[] $restrictable_comment_types array of comment types
		 */
		$restrictable_comment_types = apply_filters( 'wc_memberships_restrictable_comment_types', array( '', 'comment', 'trackback', 'pingback', 'review', 'contribution_comment' ) );

		if ( isset( $comment_query->query_vars['type'] ) && in_array( $comment_query->query_vars['type'], $restrictable_comment_types, true ) ) {

			$can_view    = current_user_can( 'wc_memberships_access_all_restricted_content' );
			$the_post_id = ! empty( $comment_query->query_vars['post_id'] ) && is_numeric( $comment_query->query_vars['post_id'] ) ? (int) $comment_query->query_vars['post_id'] : 0;
			$the_post_id = 0 === $the_post_id && ! empty( $comment_query->query_vars['parent__in'] ) && is_array( $comment_query->query_vars['parent__in'] ) && 1 === count( $comment_query->query_vars['parent__in'] ) ? current( $comment_query->query_vars['parent__in'] ) : $the_post_id;

			if ( ! $can_view && $the_post_id > 0 && $post && $the_post_id === $post->ID && is_singular() ) {

				if ( 'product' === get_post_type( $post ) ) {
					$can_view = current_user_can( 'wc_memberships_view_restricted_product', $post->ID );
				} else {
					$can_view = current_user_can( 'wc_memberships_view_restricted_post_content', $post->ID );
				}
			}

			if ( ! $can_view ) {

				$member_id = get_current_user_id();

				if ( isset( $this->restricted_comments_by_post_id[ $member_id ] ) ) {

					$post__not_in = $this->restricted_comments_by_post_id[ $member_id ];

				} else {

					$restrictions         = wc_memberships()->get_restrictions_instance();
					$post__not_in         = isset( $comment_query->query_vars['post__not_in'] ) && is_array( $comment_query->query_vars['post__not_in'] ) ? array_filter( $comment_query->query_vars['post__not_in'] ) : array();
					$original_post_not_in = $post__not_in; // used later to make sure posts marked for exclusion are not removed from this array
					$restricted_posts     = $restrictions->get_user_restricted_posts();

					// exclude restricted posts from the query
					if ( ! empty( $restricted_posts ) ) {
						$post__not_in = array_merge( $restricted_posts, (array) $post__not_in );
					}

					// get all restricted post types
					$restricted_post_types = array();

					foreach ( get_post_types() as $post_type ) {

						if ( ! current_user_can( 'wc_memberships_view_restricted_post_type', $post_type ) ) {

							$restricted_post_types[] = $post_type;
						}
					}

					// exclude all posts from restricted post type collections
					if ( ! empty( $restricted_post_types  ) ) {

						remove_filter( 'posts_clauses', array( $this, 'handle_posts_clauses' ), 999 );
						remove_filter( 'pre_get_posts', array( $this, 'exclude_restricted_posts' ), 999 );

						$restricted_posts_by_post_type = get_posts( array(
							'fields'    => 'ids',
							'nopaging'  => true,
							'post_type' => $restricted_post_types,
						) );

						add_filter( 'posts_clauses', array( $this, 'handle_posts_clauses' ), 999, 2 );
						add_filter( 'pre_get_posts', array( $this, 'exclude_restricted_posts' ), 999 );
					}

					if ( ! empty( $restricted_posts_by_post_type ) ) {
						$post__not_in = array_merge( $restricted_posts_by_post_type, (array) $post__not_in );
					}

					// exclude posts belonging to restricted terms from the query
					$taxonomies = get_taxonomies( array(), 'objects' );
					$tax_query  = array();

					if ( ! empty( $taxonomies ) ) {

						foreach ( $taxonomies as $taxonomy ) {

							$restricted_terms = isset( $taxonomy->name ) ? $restrictions->get_user_restricted_terms( $taxonomy->name ) : null;

							if ( ! empty( $restricted_terms ) ) {

								$tax_query[] = array(
									'taxonomy' => $taxonomy->name,
									'field'    => 'id',
									'terms'    => $restricted_terms,
								);
							}
						}

						// if querying more than one taxonomy, we can get posts for any relationship
						if ( count( $tax_query ) > 1 ) {
							$tax_query['relation'] = 'OR';
						}

						if ( ! empty( $tax_query ) ) {

							remove_filter( 'posts_clauses',  array( $this, 'handle_posts_clauses' ), 999 );
							remove_filter( 'get_terms_args', array( $this, 'handle_get_terms_args' ), 999 );
							remove_filter( 'terms_clauses',  array( $this, 'handle_terms_clauses' ), 999 );

							$restricted_posts_by_taxonomy = get_posts( array(
								'fields'    => 'ids',
								'nopaging'  => true,
								'tax_query' => $tax_query,
							) );

							add_filter( 'posts_clauses',  array( $this, 'handle_posts_clauses' ), 999, 2 );
							add_filter( 'get_terms_args', array( $this, 'handle_get_terms_args' ), 999, 2 );
							add_filter( 'terms_clauses',  array( $this, 'handle_terms_clauses' ), 999 );

							if ( ! empty( $restricted_posts_by_taxonomy ) ) {
								$post__not_in = array_merge( $restricted_posts_by_taxonomy, (array) $post__not_in );
							}
						}
					}

					// handles exclusions
					if ( ! empty( $post__not_in ) ) {
						foreach ( $post__not_in as $i => $post_id ) {
							if ( ! in_array( $post_id, $original_post_not_in, false ) && wc_memberships()->get_restrictions_instance()->is_post_public( $post_id ) ) {
								unset( $post__not_in[ $i ] );
							}
						}
					}

					$post__not_in = array_unique( $post__not_in );

					$this->restricted_comments_by_post_id[ $member_id ] = $post__not_in;
				}

				if ( ! empty( $post__not_in ) ) {
					$comment_query->query_vars['post__not_in'] = $post__not_in;
				}
			}
		}
	}


}
