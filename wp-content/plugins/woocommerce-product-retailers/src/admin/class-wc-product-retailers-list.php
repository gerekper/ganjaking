<?php
/**
 * WooCommerce Product Retailers
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Product Retailers Admin List Screen.
 *
 * @since 1.0.0
 */
class WC_Product_Retailers_List {


	/** @var string used to store the meta sql 'where' clause for modification during search to include post meta */
	private $meta_sql_where_clause;


	/**
	 * Initialize and setup the admin retailer list screen.
	 *
	 * @since 1.0.0
	 */
	public function  __construct() {

		add_filter( 'bulk_actions-edit-wc_product_retailer', array( $this, 'retailers_bulk_actions' ) );

		add_filter( 'views_edit-wc_product_retailer', array( $this, 'retailers_views' ) );

		add_filter( 'manage_edit-wc_product_retailer_columns', array( $this, 'retailers_column_headers' ) );

		add_action( 'manage_wc_product_retailer_posts_custom_column', array( $this, 'retailers_column_content' ) );

		// add _product_retailer_default_url into the post search meta query
		add_filter( 'parse_query',  array( $this, 'retailers_search_meta_fields' ) );

		// pull the _product_retailer_default_url clause out of the post search meta query 'where' clause
		add_filter( 'get_meta_sql', array( $this, 'get_meta_sql' ), 10, 6 );

		// add the _product_retailer_default_url clause
		add_filter( 'posts_search', array( $this, 'retailers_search' ), 10, 2 );
	}


	/**
	 * Adds the _product_retailer_default_url meta field into the search query.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Query $wp_query the query object
	 * @return \WP_Query the query object
	 */
	public function retailers_search_meta_fields( $wp_query ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow || empty( $wp_query->query_vars['s'] ) || 'wc_product_retailer' !== $wp_query->query_vars['post_type'] ) {
			return $wp_query;
		}

		$wp_query->query_vars['meta_query'][] =
			array(
				'key'     => '_product_retailer_default_url',
				'value'   => $wp_query->query_vars['s'],
				'compare' => 'LIKE',
			);

		return $wp_query;
	}


	/**
	 * Removes the _product_retailer_default_url 'where' clause, while leaving it in the 'from' clause.
	 *
	 * This allows us to include the meta table in the search query, while not limiting ourselves to the matching rows.
	 * We'll inject the 'where' clause that we remove into the query search clause with an 'OR' so we can search over the union of post name, content and meta.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $clauses the meta clauses 'join' and 'where'
	 * @param array $meta_query the meta_query
	 * @param string $type the type, ie 'post'
	 * @param string $primary_table the primary table, ie 'wp_posts'
	 * @param string $primary_id_column the primary id column, ie 'ID'
	 * @param \WP_Query $wp_query the current wp query object
	 * @return array the meta clauses 'join' and 'where'
	 */
	public function get_meta_sql( $clauses, $meta_query, $type, $primary_table, $primary_id_column, $wp_query ) {
		global $pagenow;

		// if searching for product retailers
		if ( 'edit.php' !== $pagenow || empty( $wp_query->query_vars['s'] ) || 'wc_product_retailer' !== $wp_query->query_vars['post_type'] ) {
			return $clauses;
		}

		// initialize the where clause variable (important)
		$this->meta_sql_where_clause = null;

		// determine the relation
		if ( isset( $meta_query['relation'] ) && 'OR' === strtoupper( $meta_query['relation'] ) ) {
			$relation = 'OR';
		} else {
			$relation = 'AND';
		}

		// get the individual meta queries
		$clauses_where = explode( "\n", $clauses['where'] );

		$new_clauses_where = array();

		foreach ( $clauses_where as $index => $clause ) {

			if ( false !== strpos( $clause, "'_product_retailer_default_url'" ) ) {
				// found the clause we're looking for

				// this was the first clause, pull the leading ' AND ('
				if ( 0 === $index ) {
					$clause = substr( $clause, 6 );
				}

				// this was the last clause
				if ( $index === count( $clauses_where ) - 1 ) {
					// trim off the trailing ')'
					$clause = rtrim( $clause );
					$clause = substr( $clause, 0, -1 );

					// fix the new last clause, if there is one, by adding the required ' )'
					if ( count( $new_clauses_where ) > 1 ) {
						$new_clauses_where[ count( $new_clauses_where ) - 1 ] .= ' )';
					}
				}

				// pull of the leading AND/OR if needed
				if ( 0 === strpos( $clause, $relation ) ) {
					$clause = substr( $clause, strlen( $relation ) );
				}

				$this->meta_sql_where_clause = $clause;

			} else {

				// some other clause we don't care about
				if ( 0 === count( $new_clauses_where ) && $this->meta_sql_where_clause ) {
					// promote this clause to the new first clause if we removed the first clause
					$clause = ' AND ( ' . $clause;
				}
				$new_clauses_where[] = $clause;
			}

		}

		// Create the new set of where clauses.
		// If the extracted clause results in empty AND statement, ignore it completely.
		// This prevents a MYSQL syntax error with an empty AND() in the query.
		$clauses['where'] = count( $new_clauses_where ) > 2 ? implode("\n ", $new_clauses_where) : '';

		return $clauses;
	}


	/**
	 * Modify the query search clause to include our product retailers meta where clause if we have one.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $search_clause the SQL search clause
	 * @param \WP_Query $wp_query the query object
	 * @return string the SQL search clause
	 */
	public function retailers_search( $search_clause, $wp_query ) {
		global $pagenow, $wpdb;

		// if searching for product retailers
		if ( 'edit.php' !== $pagenow || empty( $wp_query->query_vars['s'] ) || 'wc_product_retailer' !== $wp_query->query_vars['post_type'] ) {
			return $search_clause;
		}

		if ( ! empty( $wp_query->meta_query->queries ) ) {

			// gather the required clause from the meta query
			$wp_query->meta_query->get_sql( 'post', $wpdb->posts, 'ID', $wp_query );

			if ( $search_clause && $this->meta_sql_where_clause ) {

				// trim any trailing whitespace and then two closing paren
				$search_clause = rtrim( $search_clause );
				$search_clause = substr( $search_clause, 0, -2 );
				$search_clause .= ' OR ' . $this->meta_sql_where_clause . ')) ';

			}
		}

		return $search_clause;
	}


	/**
	 * Removes the bulk edit action for product retailers, it really isn't useful.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions associative array of action identifier to name
	 * @return array associative array of action identifier to name
	 */
	public function retailers_bulk_actions( $actions ) {

		unset( $actions['edit'] );

		return $actions;
	}


	/**
	 * Modifies the 'views' links.
	 *
	 * ie All (3) | Publish (1) | Draft (1) | Private (2) | Trash (3)
	 * shown above the retailers list table, to hide the publish/private/draft states,
	 * which are not important and confusing for retailer objects.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $views associative-array of view state name to link
	 * @return array associative array of view state name to link
	 */
	public function retailers_views( $views ) {

		// these views are not important distinctions for product retailers
		unset( $views['publish'], $views['private'], $views['draft'] );

		return $views;
	}


	/**
	 * Sets up columns for Retailers list.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns associative-array of column identifier to header names
	 * @return array associative-array of column identifier to header names for the retailers page
	 */
	public function retailers_column_headers( $columns ){

		return array(
			'cb'           => '<input type="checkbox" />',
			'name'         => esc_html__( 'Name', 'woocommerce-product-retailers' ),
			'default_url'  => esc_html__( 'Default URL', 'woocommerce-product-retailers' ),
		);
	}


	/**
	 * Handles custom Column values for Retailers page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $column column identifier
	 */
	public function retailers_column_content( $column ) {
		global $post;

		try {
			$retailer = new \WC_Product_Retailers_Retailer( $post );
		} catch ( \Exception $e ) {
			return;
		}

		switch ( $column ) {

			case 'name':

				$edit_link        = get_edit_post_link( $post->ID );
				$title            = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );

				if ( current_user_can( $post_type_object->cap->edit_post, $post->ID ) ) {
					echo '<strong><a class="row-title" href="' . $edit_link . '">' . $title . '</a></strong>';
				} else {
					echo '<strong class="row-title">' . $title . '</strong>';
				}

				// Get actions
				$actions = array();

				$actions['id'] = 'ID: ' . $post->ID;

				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {

					if ( 'trash' === $post->post_status ) {

						// if we use `wp_nonce_url()` untrash action will check for a valid
						// WordPress nonce, but won't get one, so we need to create our own
						$_wpnonce    = wp_create_nonce( 'untrash-post_' . $post->ID );
						$untrash_url = admin_url( 'post.php?post=' . $post->ID . '&action=untrash&_wpnonce=' . $_wpnonce );

						$actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', 'woocommerce-product-retailers' ) ) . '" href="' . esc_url( $untrash_url ) . '">' . __( 'Restore', 'woocommerce-product-retailers' ) . '</a>';

					} elseif ( EMPTY_TRASH_DAYS ) {

						$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this item to the Trash', 'woocommerce-product-retailers' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'woocommerce-product-retailers' ) . '</a>';

					}

					if ( 'trash' === $post->post_status || ! EMPTY_TRASH_DAYS ) {

						$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently', 'woocommerce-product-retailers' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'woocommerce-product-retailers' ) . '</a>';

					}
				}

				$actions = apply_filters( 'post_row_actions', $actions, $post );

				echo '<div class="row-actions">';

				$i = 0;
				$action_count = count( $actions );

				foreach ( $actions as $action => $link ) {
					( $action_count - 1 === $i ) ? $sep = '' : $sep = ' | ';
					echo '<span class="' . $action . '">' . $link . $sep . '</span>';
					$i++;
				}

				echo '</div>';

			break;

			case 'default_url':

				if ( $url = $retailer->get_url() ) {
					echo '<a href="' . esc_url( $url ) . '" rel="nofollow" target="_blank">' . esc_html( $url ) . '</a>';
				} else {
					echo '&ndash;';
				}

			break;

			default:
			break;

		}
	}


}
