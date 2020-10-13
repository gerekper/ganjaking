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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * General handler of restrictions settings.
 *
 * Initializes restrictions in the frontend and provides an API to fetch and adjust restriction settings.
 *
 * @since 1.1.0
 */
class Restrictions {


	/** @var string the restriction mode option key */
	private $restriction_mode_option = 'wc_memberships_restriction_mode';

	/** @var string the restriction mode as per plugin settings */
	private $restriction_mode;

	/** @var string the redirect page ID option key */
	private $redirect_page_id_option = 'wc_memberships_redirect_page_id';

	/** @var int the ID of the page to redirect to, when redirection mode is enabled */
	private $redirect_page_id;

	/** @var string hiding restricted products option key */
	private $hide_restricted_products_option = 'wc_memberships_hide_restricted_products';

	/** @var bool whether we are hiding completely products from catalog & search, based on setting. */
	private $hiding_restricted_products;

	/** @var string showing restricted content excerpts option key */
	private $show_excerpts_option = 'wc_memberships_show_excerpts';

	/** @var bool whether we are showing excerpts on restricted content, based on setting. */
	private $showing_excerpts;

	/** @var string inherit restriction rules option key */
	private $inherit_restrictions_option = 'wc_memberships_inherit_restrictions';

	/** @var bool whether hierarchical post types should apply restrictions rules from parent to children */
	private $inherit_restrictions;

	/** @var array collection of post IDs that are forced public, grouped by post type */
	private $public_posts;

	/** @var string transient key to store cached IDs of posts forced public */
	private $public_posts_transient_key = 'wc_memberships_public_content';

	/** @var \SkyVerge\WooCommerce\Memberships\Restrictions\Posts instance of general content restrictions handler */
	private $posts_restrictions;

	/** @var \SkyVerge\WooCommerce\Memberships\Restrictions\Products instance of products restrictions handler */
	private $products_restrictions;

	/** @var array cached user access conditions */
	private $user_content_access_conditions = [];

	/** @var array cached products that grant access */
	private $products_that_grant_access = [];


	/**
	 * Initializes content restrictions settings and handlers.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		// init restriction options
		$this->restriction_mode           = $this->get_restriction_mode();
		$this->hiding_restricted_products = $this->hiding_restricted_products();
		$this->showing_excerpts           = $this->showing_excerpts();
		$this->inherit_restrictions       = $this->inherit_restriction_rules();

		// load restriction handlers for frontend & REST API responses
		$is_admin       = is_admin();
		$is_api_request = wc_memberships()->get_rest_api_instance()->is_rest_api_request();

		if ( ! $is_admin || $is_api_request ) {

			$this->posts_restrictions = $this->get_posts_restrictions_instance();

			/** @deprecated remove legacy class aliases when the plugin has fully migrated to namespaces */
			class_alias( \SkyVerge\WooCommerce\Memberships\Restrictions\Posts::class, 'WC_Memberships_Posts_Restrictions' );
		}

		if ( ! $is_admin ) {

			$this->products_restrictions = $this->get_products_restrictions_instance();

			/** @deprecated remove legacy class aliases when the plugin has fully migrated to namespaces */
			class_alias( \SkyVerge\WooCommerce\Memberships\Restrictions\Products::class, 'WC_Memberships_Products_Restrictions' );
		}
	}


	/**
	 * Gets the general content restrictions handler.
	 *
	 * @since 1.9.0
	 *
	 * @return \SkyVerge\WooCommerce\Memberships\Restrictions\Posts
	 */
	public function get_posts_restrictions_instance() {

		if ( ! $this->posts_restrictions instanceof \SkyVerge\WooCommerce\Memberships\Restrictions\Posts ) {

			require_once( wc_memberships()->get_plugin_path() . '/includes/Restrictions/Posts.php' );

			$this->posts_restrictions = new \SkyVerge\WooCommerce\Memberships\Restrictions\Posts();
		}

		return $this->posts_restrictions;
	}


	/**
	 * Gets the products restrictions handler.
	 *
	 * @since 1.9.0
	 *
	 * @return \SkyVerge\WooCommerce\Memberships\Restrictions\Products
	 */
	public function get_products_restrictions_instance() {

		if ( ! $this->products_restrictions instanceof \SkyVerge\WooCommerce\Memberships\Restrictions\Products ) {

			require_once( wc_memberships()->get_plugin_path() . '/includes/Restrictions/Products.php' );

			$this->products_restrictions = new \SkyVerge\WooCommerce\Memberships\Restrictions\Products();
		}

		return $this->products_restrictions;
	}


	/**
	 * Returns valid restriction modes.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $with_labels whether to return mode keys or including their labels
	 * @return array string array or associative array
	 */
	public function get_restriction_modes( $with_labels = true ) {

		$modes = array(
			'hide'         => __( 'Hide completely',   'woocommerce-memberships' ),
			'hide_content' => __( 'Hide content only', 'woocommerce-memberships' ),
			'redirect'     => __( 'Redirect to page',  'woocommerce-memberships' ),
		);

		return false === $with_labels ? array_keys( $modes ) : $modes;
	}


	/**
	 * Returns the current restriction mode.
	 *
	 * @since 1.7.4
	 *
	 * @return string Possible values: 'hide', 'redirect', or 'hide_content' (default mode).
	 */
	public function get_restriction_mode() {

		if ( null === $this->restriction_mode ) {

			$default_mode     = 'hide_content';
			$restriction_mode = get_option( $this->restriction_mode_option, $default_mode );

			$this->restriction_mode = in_array( $restriction_mode, $this->get_restriction_modes( false ), true ) ? $restriction_mode : $default_mode;
		}

		return $this->restriction_mode;
	}


	/**
	 * Checks which restriction mode is being used.
	 *
	 * @since 1.7.4
	 *
	 * @param string|array $mode Compare with one (string) or more modes (array).
	 * @return bool
	 */
	public function is_restriction_mode( $mode ) {
		return is_array( $mode ) ? in_array( $this->get_restriction_mode(), $mode, true ) : $mode === $this->restriction_mode;
	}


	/**
	 * Sets the content restriction mode.
	 *
	 * @since 1.9.0
	 *
	 * @param string $mode
	 */
	public function set_restriction_mode( $mode ) {

		if ( array_key_exists( $mode, $this->get_restriction_modes() ) ) {

			update_option( $this->restriction_mode_option, $mode );

			$this->restriction_mode = $mode;
		}
	}


	/**
	 * Returns the redirect page ID used when in 'redirect' restriction mode.
	 *
	 * @since 1.7.4
	 *
	 * @return int
	 */
	public function get_restricted_content_redirect_page_id() {

		if ( null === $this->redirect_page_id || ! ( $this->redirect_page_id > 0 ) ) {

			$this->redirect_page_id = (int) get_option( $this->redirect_page_id_option );
		}

		return $this->redirect_page_id;
	}


	/**
	 * Checks whether a page is the page to redirect to in restricted content redirection mode.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post|int|string $page_id page object, ID or slug
	 * @return bool
	 */
	public function is_restricted_content_redirect_page( $page_id ) {

		if ( $page_id instanceof \WP_Post ) {
			$page_id  = (int) $page_id->ID;
		} elseif( is_numeric( $page_id ) ) {
			$page_id  = (int) $page_id;
		} elseif ( is_string( $page_id ) ) {
			$post_obj = get_post( $page_id );
			$page_id  = $post_obj ? (int) $post_obj->ID : null;
		}

		return is_int( $page_id ) ? $page_id > 0 && $page_id === $this->get_restricted_content_redirect_page_id() : false;
	}


	/**
	 * Sets the page to redirect to when using redirection mode.
	 *
	 * @since 1.9.0
	 *
	 * @param int|string|\WP_Post $page_id page object, slug or ID
	 * @return bool success
	 */
	public function set_restricted_content_redirect_page_id( $page_id ) {

		$success = false;

		if ( is_string( $page_id ) && ! is_numeric( $page_id ) ) {
			$page    = get_post( $page_id );
			$page_id = $page ? $page->ID : 0;
		} elseif ( $page_id instanceof \WP_Post ) {
			$page_id = $page_id->ID;
		}

		if ( is_numeric( $page_id ) && 'page' === get_post_type( $page_id ) ) {

			$success = update_option( $this->redirect_page_id_option, (int) $page_id );

			if ( $success ) {
				$this->redirect_page_id = (int) $page_id;
			}
		}

		return $success;
	}


	/**
	 * Returns a restricted content redirect URL.
	 *
	 * @since 1.9.0
	 *
	 * @param int $redirect_from_id the ID of the page, product, post or term redirecting from
	 * @param string|null $redirect_from_object_type optional for posts and pages: the type of object to redirecting from (normally: post_type or taxonomy)
	 * @param string|null $redirect_from_object_type_name optional for posts and pages: the name of the type of object redirect from (e.g. category, product, post, product_cat...)
	 * @return string URL with query arguments
	 */
	public function get_restricted_content_redirect_url( $redirect_from_id, $redirect_from_object_type = null, $redirect_from_object_type_name = null ) {

		$redirect_args               = array( 'r' => (int) $redirect_from_id );
		$restricted_content_page_id  = $this->get_restricted_content_redirect_page_id();
		$restricted_content_page_url = $restricted_content_page_id > 0 ? get_permalink( $restricted_content_page_id ) : null;

		// special handling for when My Account is used as the Redirect Page
		if ( $restricted_content_page_url && $restricted_content_page_id === (int) wc_get_page_id( 'myaccount' ) ) {
			$restricted_content_page          = get_post( $restricted_content_page_id );
			$redirect_args['wcm_redirect_to'] = $restricted_content_page && 'page' === $restricted_content_page->post_type ? 'page' : 'post';
			$redirect_args['wcm_redirect_id'] = (int) $redirect_from_id;
		// additional arguments useful when redirecting from a term archive
		} elseif ( $redirect_from_object_type && $redirect_from_object_type_name && is_string( $redirect_from_object_type ) && is_string( $redirect_from_object_type_name ) ) {
			$redirect_args['wcm_redirect_to'] = $redirect_from_object_type_name;
			$redirect_args['wcm_redirect_id'] = (int) $redirect_from_id;
		}

		return add_query_arg( $redirect_args, ! $restricted_content_page_url ? home_url() : $restricted_content_page_url );
	}


	/**
	 * Checks whether it is chosen in settings to hide restricted products from catalog and search.
	 *
	 * @see \WC_Memberships_Restrictions::showing_restricted_products()
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function showing_restricted_products() {
		return ! $this->hiding_restricted_products();
	}


	/**
	 * Checks whether it is chosen in settings to hide restricted products from catalog and search.
	 *
	 * @see \WC_Memberships_Restrictions::hiding_restricted_products()
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	public function hiding_restricted_products() {

		if ( null === $this->hiding_restricted_products ) {

			$this->hiding_restricted_products = 'yes' === get_option( $this->hide_restricted_products_option );
		}

		return $this->hiding_restricted_products;
	}


	/**
	 * Sets the visibility of restricted products.
	 *
	 * @since 1.9.0
	 *
	 * @param string $visibility either 'hide' or 'show' (default)
	 */
	public function set_restricted_products_visibility( $visibility ) {

		if ( 'hide' === $visibility ) {

			update_option( $this->hide_restricted_products_option, 'yes' );

			$this->hiding_restricted_products = true;

		} else {

			update_option( $this->hide_restricted_products_option, 'no' );

			$this->hiding_restricted_products = false;
		}
	}


	/**
	 * Checks whether an option is set to show excerpts for restricted content.
	 *
	 * @see \WC_Memberships_Restrictions::hiding_excerpts()
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	public function showing_excerpts() {

		if ( null === $this->showing_excerpts ) {

			$this->showing_excerpts = 'yes' === get_option( $this->show_excerpts_option );
		}

		return $this->showing_excerpts;
	}


	/**
	 * Checks whether an option is set to hide excerpts for restricted content.
	 *
	 * @see \WC_Memberships_Restrictions::showing_excerpts()
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function hiding_excerpts() {
		return ! $this->showing_excerpts();
	}


	/**
	 * Sets the content excerpts visibility.
	 *
	 * @since 1.9.0
	 *
	 * @param string $visibility either 'hide' or 'show' (default)
	 */
	public function set_excerpts_visibility( $visibility ) {

		if ( 'hide' === $visibility ) {

			update_option( $this->show_excerpts_option, 'no' );

			$this->showing_excerpts = false;

		} else {

			update_option( $this->show_excerpts_option, 'yes' );

			$this->showing_excerpts = true;
		}
	}


	/**
	 * Checks whether an option is set to let hierarchical post types to apply restriction rules to their children.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function inherit_restriction_rules() {

		if ( null === $this->inherit_restrictions ) {

			$this->inherit_restrictions = 'yes' === get_option( $this->inherit_restrictions_option );
		}

		return $this->inherit_restrictions;
	}


	/**
	 * Enables inheritance for restriction rules.
	 *
	 * @since 1.12.3
	 *
	 * @return bool
	 */
	public function enable_restriction_rules_inheritance() {

		if ( $success = update_option( $this->inherit_restrictions_option, 'yes' ) ) {

			$this->inherit_restrictions = true;
		}

		return $success;
	}


	/**
	 * Disables inheritance for restriction rules.
	 *
	 * @since 1.12.3
	 *
	 * @return bool
	 */
	public function disable_restriction_rules_inheritance() {

		if ( $success = update_option( $this->inherit_restrictions_option, 'no' ) ) {

			$this->inherit_restrictions = false;
		}

		return $success;
	}


	/**
	 * Gets posts that have been marked for public access and ignore normal restriction rules.
	 *
	 * @since 1.12.0
	 *
	 * @param string|string[] $which_post_type get public posts by post type or all (default: any, all)
	 * @param bool $use_cache whether to look in a cached transient for results or update results via direct SQL query
	 * @return array|int[] array of post IDs or associative array of post IDs grouped by post type
	 */
	public function get_public_posts( $which_post_type = 'any', $use_cache = true ) {
		global $wpdb;

		if ( false === $use_cache || ! is_array( $this->public_posts ) ) {

			$posts = [];

			if ( true === $use_cache ) {

				$posts = get_transient( $this->public_posts_transient_key );

				// transient has expired, must query posts again and update cache
				if ( ! is_array( $posts ) ) {
					$posts = $this->get_public_posts( $which_post_type, false );
				}

			} else {

				$found_items = $wpdb->get_results( "
					SELECT p.ID, p.post_type
					FROM $wpdb->posts p
					LEFT JOIN $wpdb->postmeta pm
					ON p.ID = pm.post_id
					WHERE pm.meta_key = '_wc_memberships_force_public'
					AND pm.meta_value = 'yes'
				" );

				foreach ( $found_items as $item ) {

					if ( isset( $item->post_type, $item->ID ) && is_string( $item->post_type ) && is_numeric( $item->ID ) && $item->ID > 0 ) {

						$posts[ $item->post_type ][] = (int) $item->ID;
					}
				}

				if ( ! empty( $posts ) ) {

					// maybe add product variations to product results
					if ( ! empty( $posts['product'] ) ) {

						$parents    = implode( ',', $posts['product'] );
						$variations = $wpdb->get_col( "
							SELECT p.ID
							FROM {$wpdb->posts} p
							WHERE p.post_type = 'product_variation'
							AND p.post_parent IN ({$parents})
						" );

						$posts['product'] = array_merge( $posts['product'], array_map( 'absint', $variations ) );
					}

					$this->update_public_content_cache( [ 'posts' => $posts ] );
				}
			}

			$this->public_posts = $posts;
		}

		if ( in_array( $which_post_type, [ null, 'any', 'all' ], true ) ) {
			$results = $this->public_posts;
		} elseif ( is_array( $which_post_type ) ) {
			$results = ! empty( $this->public_posts ) && ! empty( $which_post_type ) ? array_intersect_key( $this->public_posts, array_combine( $which_post_type, $which_post_type ) ) : [];
		} elseif ( is_string( $which_post_type ) ) {
			$results = isset( $this->public_posts[ $which_post_type ] ) ? $this->public_posts[ $which_post_type ] : [];
		} else {
			$results = [];
		}

		return $results;
	}


	/**
	 * Gets product that have been marked for public access and ignore any restriction rule.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $use_cache whether to look in a cached transient for results or update results via direct SQL query
	 * @return int[]
	 */
	public function get_public_products( $use_cache = true ) {

		return $this->get_public_posts( 'product', $use_cache );
	}


	/**
	 * Checks whether a post content is forced public.
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WP_Post $post ID or post object
	 * @param null|string $post_type optional post type
	 * @return bool
	 */
	public function is_post_public( $post, $post_type = null ) {

		$is_post_public = false;

		if ( $post instanceof \WP_Post ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
		}

		if ( is_numeric( $post_id ) ) {

			$public_posts = $this->get_public_posts();

			if ( in_array( $post_type, array( null, 'any', 'all' ), true ) ) {

				foreach ( $public_posts as $post_ids_for_post_type ) {

					if ( in_array( $post_id, $post_ids_for_post_type, false ) ) {

						$is_post_public = true;
						break;
					}
				}

			} elseif ( ! empty( $public_posts[ $post_type ] ) && is_array( $public_posts[ $post_type ] ) ) {

				$is_post_public = in_array( $post_id, $public_posts[ $post_type ], false );
			}
		}

		/**
		 * Filters whether a post should be public (ie. not subject to any restriction for the current user or anonymous guest).
		 *
		 * @since 1.9.0
		 *
		 * @param bool $is_public whether the post is public (default false unless explicitly marked as public by an admin)
		 * @param int $post_id the ID of the post being evaluated
		 * @param null|string $post_type optional post type passed in method arguments
		 */
		$is_post_public = (bool) apply_filters( 'wc_memberships_is_post_public', $is_post_public, $post_id, $post_type );

		// if using redirect mode, the redirect page must be made public regardless
		if ( ! $is_post_public && $this->is_restriction_mode( 'redirect' ) && 'page' === get_post_type( $post ) ) {
			$is_post_public = (int) $post_id === $this->get_restricted_content_redirect_page_id();
		}

		return $is_post_public;
	}


	/**
	 * Checks whether a product is forced public.
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WC_Product|\WP_Post $product product object, ID or associated post
	 * @return bool
	 */
	public function is_product_public( $product ) {

		if ( $product instanceof \WP_Post ) {
			$product_id = $product->ID;
		} elseif ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} else {
			$product_id = $product;
		}

		return is_numeric( $product_id ) && $this->is_post_public( $product_id, 'product' );
	}


	/**
	 * Sets a piece of content to be forced public, or not.
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WC_Product|\WP_Post|int $object post, product or ID, or array of (used in bulk actions)
	 * @param string $force_public either 'yes' or 'no'
	 * @return int number of items set (0 for failure)
	 */
	private function set_content_forced_public( $object, $force_public ) {

		$success = 0;

		if ( in_array( $force_public, [ 'yes', 'no' ], true ) ) {

			$items = is_array( $object ) ? $object : [ $object ];

			foreach ( $items as $item ) {
				if ( wc_memberships_set_content_meta( $item, '_wc_memberships_force_public', $force_public ) ) {
					$success++;
				}
			}
		}

		if ( $success > 0 ) {
			$this->update_public_content_cache();
		}

		return $success;
	}


	/**
	 * Sets a post to be forced public (anyone will have access, regardless of rules).
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WP_Post|int $post ID or object, or array of
	 * @return int number of items set (0 for failure)
	 */
	public function set_content_public( $post ) {

		return $this->set_content_forced_public( $post, 'yes' );
	}


	/**
	 * Sets a product to be forced public (anyone will have access, regardless of rules).
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WC_Product|\WP_Post|int $product ID or object, or array of
	 * @return int number of items set (0 for failure)
	 */
	public function set_product_public( $product ) {

		return $this->set_content_public( $product );
	}


	/**
	 * Sets a post not to be forced public (normal restriction rules will apply).
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WP_Post|int $post ID or object, or array of
	 * @return int number of items set (0 for failure)
	 */
	public function unset_content_public( $post ) {

		return $this->set_content_forced_public( $post, 'no' );
	}


	/**
	 * Sets a product not to be forced public (normal restriction rules will apply).
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WC_Product|\WP_Post|int $product ID or object
	 * @return int number of items set (0 for failure)
	 */
	public function unset_product_public( $product ) {

		return $this->unset_content_public( $product );
	}


	/**
	 * Updates the public content cache.
	 *
	 * @since 1.12.0
	 *
	 * @param array $data optional data to store in cache
	 * @return bool success
	 */
	public function update_public_content_cache( $data = array() ) {

		/**
		 * Adjusts the expiration time for public content cache.
		 *
		 * @since 1.12.0
		 *
		 * @param int $expiration time in seconds (default uses WEEK_IN_SECONDS constant)
		 */
		$expiration = absint( apply_filters( 'wc_memberships_public_content_cache_expiration', WEEK_IN_SECONDS ) );

		if ( $expiration > 0 ) {
			$success = set_transient( $this->public_posts_transient_key, ! empty( $data['posts'] ) ? $data['posts'] : $this->get_public_posts( 'any', false ), max( MINUTE_IN_SECONDS, $expiration ) );
		} else {
			$success = $this->delete_public_content_cache();
		}

		return $success;
	}


	/**
	 * Deletes the public content cache.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function delete_public_content_cache() {

		return delete_transient( $this->public_posts_transient_key );
	}


	/**
	 * Returns content access conditions for the current user.
	 *
	 * Note: third party code should refrain from using or extending this method.
	 *
	 * @since 1.1.0
	 *
	 * @return array an associative array of restricted and granted content based on the content and product restriction rules
	 */
	public function get_user_content_access_conditions() {

		if ( empty( $this->user_content_access_conditions ) ) {

			// prevent infinite loops
			remove_filter( 'pre_get_posts', array( $this->get_posts_restrictions_instance(), 'exclude_restricted_posts' ), 999 );
			remove_filter( 'get_terms_args', array( $this->get_posts_restrictions_instance(), 'handle_get_terms_args' ), 999 );
			remove_filter( 'terms_clauses',  array( $this->get_posts_restrictions_instance(), 'handle_terms_clauses' ), 999 );

			$rules      = wc_memberships()->get_rules_instance()->get_rules( array( 'rule_type' => array( 'content_restriction', 'product_restriction' ), ) );
			$restricted = $granted = array(
				'posts'      => array(),
				'post_types' => array(),
				'terms'      => array(),
				'taxonomies' => array(),
			);

			$conditions = array(
				'restricted' => $restricted,
				'granted'    => $granted,
			);

			// shop managers/admins can access everything
			if ( is_user_logged_in() && current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {

				$this->user_content_access_conditions = $conditions;

			} else {

				// get all the content that is either restricted or granted for the user
				if ( ! empty( $rules ) ) {

					$user_id = get_current_user_id();

					foreach ( $rules as $rule ) {

						// skip rule if the plan is not published
						if ( 'publish' !== get_post_status( $rule->get_membership_plan_id() ) ) {
							continue;
						}

						// skip non-view product restriction rules
						if ( 'product_restriction' === $rule->get_rule_type() && 'view' !== $rule->get_access_type() ) {
							continue;
						}

						// check if user is an active member of the plan
						$plan_id          = $rule->get_membership_plan_id();
						$is_active_member = $user_id > 0 && wc_memberships_is_user_active_member( $user_id, $plan_id );
						$has_access       = false;

						// check if user has scheduled access to the content
						if ( $is_active_member && ( $user_membership = wc_memberships()->get_user_memberships_instance()->get_user_membership( $user_id, $plan_id ) ) ) {

							/** this filter is documented in includes/class-wc-memberships-capabilities.php **/
							$from_time = apply_filters( 'wc_memberships_access_from_time', $user_membership->get_start_date( 'timestamp' ), $rule, $user_membership );

							// sanity check: bail out if there's no valid set start date
							if ( ! $from_time || ! is_numeric( $from_time ) ) {
								break;
							}

							$inactive_time    = $user_membership->get_total_inactive_time();
							$current_time     = current_time( 'timestamp', true );
							$rule_access_time = $rule->get_access_start_time( $from_time );

							$has_access = $rule_access_time + $inactive_time <= $current_time;
						}

						$condition = $has_access ? 'granted' : 'restricted';

						// find posts that are either restricted or granted access to
						if ( 'post_type' === $rule->get_content_type() ) {

							if ( $rule->has_objects() ) {

								$rule_type  = $rule->get_rule_type();
								$post_type  = $rule->get_content_type_name();
								$object_ids = $rule->get_object_ids();
								$post_ids   = array();

								// maybe add children and leave out posts that have restrictions disabled
								foreach ( $object_ids as $post_id ) {

									if ( $this->inherit_restrictions && 'content_restriction' === $rule_type ) {

										$children = $rule->get_object_children_ids();

										foreach ( $children as $child_id ) {

											if ( ! $this->is_post_public( $child_id ) ) {
												$post_ids[] = (int) $child_id;
											}
										}
									}

									if ( ! $this->is_post_public( $post_id ) ) {
										$post_ids[] = (int) $post_id;
									}
								}

								// if there are no posts left, continue to next rule
								if ( empty( $post_ids ) ) {
									continue;
								}

								if ( ! isset( $conditions[ $condition ]['posts'][ $post_type ] ) ) {
									$conditions[ $condition ]['posts'][ $post_type ] = array();
								}

								$conditions[ $condition ]['posts'][ $post_type ] = array_unique( array_merge( $conditions[ $condition ][ 'posts' ][ $post_type ], $post_ids ) );

							} else {

								// find post types that are either restricted or granted access to
								$conditions[ $condition ]['post_types'] = array_unique( array_merge( $conditions[ $condition ][ 'post_types' ], (array) $rule->get_content_type_name() ) );
							}

						} elseif ( 'taxonomy' === $rule->get_content_type() ) {

							if ( $rule->has_objects() ) {

								// find taxonomy terms that are either restricted or granted access to
								$taxonomy = $rule->get_content_type_name();

								if ( ! isset( $conditions[ $condition ][ 'terms' ][ $taxonomy ] ) ) {
									$conditions[ $condition ]['terms'][ $taxonomy ] = array();
								}

								$object_ids = array( array() );

								// ensure child terms inherit any restriction from their ancestors
								foreach ( $rule->get_object_ids() as $object_id ) {

									$child_object_ids = get_term_children( $object_id, $taxonomy );

									if ( is_array( $child_object_ids ) ) {
										$object_ids[] = $child_object_ids;
									}

									$object_ids[] = array( $object_id );
								}

								$object_ids = call_user_func_array( 'array_merge', $object_ids );

								$conditions[ $condition ]['terms'][ $taxonomy ] = array_unique( array_merge( $conditions[ $condition ]['terms'][ $taxonomy ], $object_ids ) );

							} else {

								$conditions[ $condition ]['taxonomies'] = array_unique( array_merge( $conditions[ $condition ]['taxonomies'], (array) $rule->get_content_type_name() ) );
							}
						}
					}
				}

				// loop over granted content and check if the user has access to delayed content
				foreach ( $conditions['granted'] as $content_type => $values ) {

					if ( empty( $values ) || ! is_array( $values ) ) {
						continue;
					}

					foreach ( $values as $key => $value ) {

						switch ( $content_type ) {

							case 'posts':
								if ( is_array( $value ) ) {
									foreach ( $value as $post_key => $post_id ) {
										if ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
											unset( $conditions['granted'][ $content_type ][ $key ][ $post_key ] );
										}
									}
								}
							break;

							case 'post_types':
								if ( ! current_user_can( 'wc_memberships_view_delayed_post_type', $value ) ) {
									unset( $conditions['granted'][ $content_type ][ $key ] );
								}
							break;

							case 'taxonomies':
								if ( ! current_user_can( 'wc_memberships_view_delayed_taxonomy', $value ) ) {
									unset( $conditions['granted'][ $content_type ][ $key ] );
								}
							break;

							case 'terms':
								if ( is_array( $value ) ) {
									foreach ( $value as $term_key => $term ) {
										if ( ! current_user_can( 'wc_memberships_view_delayed_taxonomy_term', $key, $term ) ) {
											unset( $conditions['granted'][ $content_type ][ $key ][ $term_key ] );
										}
									}
								}
							break;
						}
					}
				}

				// remove restricted items that should be granted for the current user
				// content types are high-level restriction items - posts, post_types, terms, and taxonomies
				foreach ( $conditions['restricted'] as $content_type => $object_types ) {

					if ( empty( $conditions['granted'][ $content_type ] ) || empty( $object_types ) ) {
						continue;
					}

					// object types are child elements of a content type,
					// e.g. for the posts content type, object types are post_types( post and product)
					// for a term content type, object types are taxonomy names (e.g. category)
					foreach ( $object_types as $object_type_name => $object_ids ) {

						if ( empty( $conditions['granted'][ $content_type ][ $object_type_name ] ) || empty( $object_ids ) ) {
							continue;
						}

						if ( is_array( $object_ids ) ) {
							// if the restricted object ID is also granted, remove it from restrictions
							foreach ( $object_ids as $object_id_index => $object_id ) {

								if ( in_array( $object_id, $conditions['granted'][ $content_type ][ $object_type_name ], false ) ) {
									unset( $conditions['restricted'][ $content_type ][ $object_type_name ][ $object_id_index ] );
								}
							}
						} else {
							// post type handling
							if ( in_array( $object_ids, $conditions['granted'][ $content_type ], false ) ) {
								unset( $conditions['restricted'][ $content_type ][ array_search( $object_ids, $conditions['restricted'][ $content_type ], false ) ] );
							}
						}
					}
				}

				// grant access to posts that have restrictions disabled
				foreach ( $this->get_public_posts() as $post_type => $post_ids ) {

					if ( is_array( $post_ids ) && ! empty( $post_ids ) ) {

						if ( ! isset( $conditions['granted']['posts'][ $post_type ] ) ) {
							$conditions['granted']['posts'][ $post_type ] = array();
						}

						$conditions['granted']['posts'][ $post_type ] = array_unique( array_merge( $conditions['granted']['posts'][ $post_type ], array_map( 'absint', $post_ids ) ) );
					}
				}
			}

			$this->user_content_access_conditions = $conditions;

			// add back post restriction filters that were removed prior to calculating the access conditions, in order to prevent infinite filter loops
			add_filter( 'pre_get_posts',  array( $this->get_posts_restrictions_instance(), 'exclude_restricted_posts' ), 999 );
			add_filter( 'get_terms_args', array( $this->get_posts_restrictions_instance(), 'handle_get_terms_args' ), 999, 2 );
			add_filter( 'terms_clauses',  array( $this->get_posts_restrictions_instance(), 'handle_terms_clauses' ), 999 );
		}

		return $this->user_content_access_conditions;
	}


	/**
	 * Returns a list of object IDs for the specified access condition.
	 *
	 * General method to get a list of object IDs (posts or terms) that are either restricted or granted for the current user.
	 * The list can be limited to specific post types or taxonomies.
	 *
	 * @since 1.9.0
	 *
	 * @param string $condition either 'restricted' or 'granted'
	 * @param string $content_type either 'posts' or 'terms'
	 * @param string|string[]|null $content_type_name optional: post type or taxonomy name (or names) to get object IDs for; if empty (default) will return all object IDs
	 * @return int[]|null
	 */
	private function get_user_content_for_access_condition( $condition, $content_type, $content_type_name = null ) {

		$conditions = $this->get_user_content_access_conditions();

		if ( is_string( $content_type_name ) ) {

			$objects = isset( $conditions[ $condition ][ $content_type ][ $content_type_name ] ) ? $conditions[ $condition ][ $content_type ][ $content_type_name ] : null;

		} else {

			$objects    = array( array() );
			$conditions = ! empty( $conditions[ $condition ][ $content_type ] ) && is_array( $conditions[ $condition ][ $content_type ] ) ? $conditions[ $condition ][ $content_type ] : array();

			foreach ( $conditions as $restricted_content_type_name => $restricted_objects ) {

				if ( ! $content_type_name || in_array( $restricted_content_type_name, $content_type_name, true ) ) {

					$objects[] = $restricted_objects;
				}
			}

			$objects = call_user_func_array( 'array_merge', $objects );
		}

		return ! empty( $objects ) ? $objects : null;
	}


	/**
	 * Returns a list of restricted post IDs for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @param $post_type string optional post type to get restricted post IDs for - if empty, will return all post IDs
	 * @return int[]|null array of post IDs or null if none found
	 */
	public function get_user_restricted_posts( $post_type = null ) {
		return $this->get_user_content_for_access_condition( 'restricted', 'posts', $post_type );
	}


	/**
	 * Returns a list of granted post IDs for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @param $post_type string optional post type to get granted post IDs for - if empty, will return all post IDs
	 * @return int[]|null Array of post IDs or null if none found
	 */
	public function get_user_granted_posts( $post_type = null ) {
		return $this->get_user_content_for_access_condition( 'granted', 'posts', $post_type );
	}


	/**
	 * Returns a list of restricted term IDs for the current user.
	 *
	 * @since 1.9.0
	 *
	 * @param $taxonomy string|array optional taxonomy or array of taxonomies to get term IDs for - if empty, will return all term IDs
	 * @return int[]|null array of term IDs or null if none found
	 */
	public function get_user_restricted_terms( $taxonomy = null ) {
		return $this->get_user_content_for_access_condition( 'restricted', 'terms', $taxonomy );
	}


	/**
	 * Returns a list of granted term IDs for the current user.
	 *
	 * @since 1.9.0
	 *
	 * @param $taxonomy string|array optional taxonomy or array of taxonomies to get term IDs for - if empty, will return all term IDs
	 * @return int[]|null array of term IDs or null if none found
	 */
	public function get_user_granted_terms( $taxonomy = null ) {
		return $this->get_user_content_for_access_condition( 'granted', 'terms', $taxonomy );
	}


	/**
	 * Retrieves all products that may grant access to a plan for viewing a piece of content or product.
	 *
	 * For products, this may include either full access or purchase ability.
	 *
	 * @since 1.11.1
	 *
	 * @param \WP_Post|\WC_Product|\WP_Term|int $restricted_content a product, post or term
	 * @param array $args optional arguments used in filters
	 * @return int[] array of product IDs
	 */
	public function get_products_that_grant_access( $restricted_content, $args = [] ) {

		$access_products = [];

		if ( $restricted_content instanceof \WC_Product ) {
			$object_id   = (int) $restricted_content->get_id();
			$object_type = 'product';
		} elseif ( is_numeric( $restricted_content ) ) {
			// the ID in this case is assumed to be a post ID
			$object_id   = (int) $restricted_content;
			$object_type = 'post';
		} elseif ( $restricted_content instanceof \WP_Term ) {
			$object_id   = (int) $restricted_content->term_id;
			$object_type = $restricted_content->taxonomy;
		} else {
			// restricted content is assumed \WP_Post
			$object_id   = isset( $restricted_content->ID ) ? (int) $restricted_content->ID : 0;
			$object_type = 'post';
		}

		if ( ! $object_id ) {
			return $access_products;
		}

		$cache_key = http_build_query( array_merge( $args, [
			'object_type' => $object_type,
			'object_id'   => $object_id,
		] ) );

		if ( ! isset( $this->products_that_grant_access[ $cache_key ] ) ) {

			if ( in_array( $object_type, [ 'product', 'post' ], true ) ) {

				$rules_handler = wc_memberships()->get_rules_instance();

				if ( 'product' === $object_type ) {
					$rules     = $rules_handler->get_product_restriction_rules( $object_id );
					$rule_type = 'product_restriction';
				} else {
					$rules     = $rules_handler->get_post_content_restriction_rules( $object_id );
					$rule_type = 'content_restriction';
				}

				$access_products = $rules_handler->get_products_to_purchase_from_rules( $rules, $object_id, $rule_type, $args );

			} elseif ( $restricted_content instanceof \WP_Term ) {

				$terms = array_unique( array_merge( [ $object_id ], get_ancestors( $object_id, $object_type, 'taxonomy' ) ) );
				$args  = [
					'fields'    => 'ids',
					'nopaging'  => true,
					'tax_query' => [],
				];

				foreach ( $terms as $term_id ) {

					$args['tax_query'][] = [
						'taxonomy' => $object_type,
						'field'    => 'id',
						'terms'    => $term_id,
					];
				}

				if ( count( $args['tax_query'] ) > 1 ) {
					$args['tax_query']['relation'] = 'OR';
				}

				if ( 'product_cat' === $object_type ) {
					$args['post_type'] = 'product';
				}

				$posts       = get_posts( $args );
				$product_ids = [ [] ];

				foreach ( $posts as $post_id ) {
					$product_ids[] = $this->get_products_that_grant_access( $post_id, $args );
				}

				$access_products = array_merge( ...$product_ids );
			}

			$this->products_that_grant_access[ $cache_key ] = array_unique( $access_products );
		}

		return $this->products_that_grant_access[ $cache_key ];
	}


	/**
	 * Retrieves all products that may grant access to a plan that gives a discount on a product or product category.
	 *
	 * Passing a post that is not a product type or a category that is not a product category would obviously produce no results.
	 *
	 * @since 1.11.1
	 *
	 * @param \WP_Post|\WC_Product|\WP_Term|int $restricted_shop_content product, post object or term object
	 * @param array $args optional arguments used in filters
	 * @return int[] array of product IDs
	 */
	public function get_products_that_grant_discount( $restricted_shop_content, $args = array() ) {

		$discount_access_products = [];

		if ( $restricted_shop_content instanceof \WC_Product ) {
			$object = get_post( $restricted_shop_content->get_id() );
		} else {
			$object = $restricted_shop_content; // post or term: if it's an integer we must assume post ID
		}

		if ( is_numeric( $object ) || ( $object instanceof \WP_Post && 'product' === $object->post_type ) ) {

			$product_id               = is_numeric( $object ) ? $object : $object->ID;
			$rules_handler            = wc_memberships()->get_rules_instance();
			$rules                    = $rules_handler->get_product_purchasing_discount_rules( $product_id );
			$discount_access_products = $rules_handler->get_products_to_purchase_from_rules( $rules, $object, 'purchasing_discount', $args );

		} elseif ( $object instanceof \WP_Term && 'product_cat' === $object->taxonomy ) {

			$taxonomy = $object->taxonomy;
			$terms    = array_unique( array_merge( array( $object->term_id ), get_ancestors( $object->term_id, $taxonomy, 'taxonomy' ) ) );
			$args     = array(
				'post_type' => 'product',
				'fields'    => 'ids',
				'nopaging'  => true,
				'tax_query' => array()
			);

			foreach ( $terms as $term_id ) {

				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $term_id,
				);
			}

			if ( count( $args['tax_query'] ) > 1 ) {
				$args['tax_query']['relation'] = 'OR';
			}

			$posts       = get_posts( $args );
			$product_ids = array( array() );

			foreach ( $posts as $post_id ) {
				$product_ids[] = $this->get_products_that_grant_discount( $post_id, $args );
			}

			$discount_access_products = call_user_func_array( 'array_merge', $product_ids );
		}

		return array_unique( $discount_access_products );
	}


}
