<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Admin functions for the wc_product_tab post type
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;

add_filter( 'woocommerce_screen_ids', 'wc_tab_manager_load_wc_scripts' );

/**
 * Enqueue scripts & styles for the meta box
 *
 * @since 1.7.0
 * @return array
 */
function wc_tab_manager_load_wc_scripts( $screen_ids ) {

	$new_screen_ids = array(
		'wc_product_tab',
		'edit-wc_product_tab',
		'admin_page_tab_manager',
	);

	return array_merge( $screen_ids, $new_screen_ids );
}

add_action( 'admin_print_scripts', 'wc_tab_manager_disable_autosave_for_product_tabs' );

/**
 * Disable the auto-save functionality for Tabs.
 *
 * @access public
 */
function wc_tab_manager_disable_autosave_for_product_tabs() {
	global $post;

	if ( $post && 'wc_product_tab' === get_post_type( $post->ID ) ) {
		wp_dequeue_script( 'autosave' );
	}
}


add_filter( 'bulk_actions-edit-wc_product_tab', 'wc_tab_manager_edit_product_tab_bulk_actions' );

/**
 * Remove the bulk edit action for product tabs, it really isn't useful
 *
 * @access public
 * @param array $actions associative array of action identifier to name.
 *
 * @return array associative array of action identifier to name
 */
function wc_tab_manager_edit_product_tab_bulk_actions( $actions ) {

	unset( $actions['edit'] );

	// Remove the date filter dropdown as well.
	add_filter( 'disable_months_dropdown', '__return_true' );

	return $actions;
}


add_filter( 'views_edit-wc_product_tab', 'wc_tab_manager_edit_product_tab_views' );

/**
 * Modify the 'views' links, ie All (3) | Publish (1) | Draft (1) | Private (2) | Trash (3)
 * shown above the product tabs list table, to hide the publish/private states,
 * which are not important and confusing for product tab objects.
 *
 * @access public
 * @param array $views associative-array of view state name to link.
 *
 * @return array associative array of view state name to link
 */
function wc_tab_manager_edit_product_tab_views( $views ) {

	// Publish and private are not important distinctions for product tabs.
	unset( $views['publish'], $views['private'], $views['future'] );

	return $views;
}


add_filter( 'manage_edit-wc_product_tab_columns', 'wc_tab_manager_edit_product_tab_columns' );

/**
 * Columns for product tab page
 *
 * @access public
 * @param array $columns associative-array of column identifier to header names.
 *
 * @return array associative-array of column identifier to header names for the product tabs page
 */
function wc_tab_manager_edit_product_tab_columns( $columns ) {

	$columns = array(
		'cb'             => '<input type="checkbox" />',
		'name'           => esc_html__( 'Name', 'woocommerce-tab-manager' ),
		'type'           => esc_html__( 'Tab Type', 'woocommerce-tab-manager' ),
		'parent-product' => esc_html__( 'Parent Product', 'woocommerce-tab-manager' ),
		'product-cat'    => esc_html__( 'Categories', 'woocommerce-tab-manager' ),
	);

	if ( 'yes' === get_option( 'wc_tab_manager_enable_search', 'yes' ) ) {

		$is_searchable_text = esc_html__( 'Searchable?', 'woocommerce-tab-manager' );
		$is_searchable_html = '<span class="dashicons dashicons-search" title="' . $is_searchable_text . '" aria-label="' . $is_searchable_text . '"></span>';

		$columns['is-searchable'] = $is_searchable_html;
	}

	return $columns;
}


add_action( 'manage_wc_product_tab_posts_custom_column', 'wc_tab_manager_custom_product_tab_columns', 2 );


/**
 * Custom Column values for product tabs page
 *
 * @access public
 * @param string $column column identifier.
 */
function wc_tab_manager_custom_product_tab_columns( $column ) {
	global $post;

	switch ( $column ) {

		case 'name':

			echo '<strong>';

			// Add the parent product name if any.
			if ( $post->post_parent ) {
				$parent = wc_get_product( $post->post_parent );
				echo esc_html( $parent->get_title() ) . ' - ';
			}

			$edit_link = get_edit_post_link( $post->ID );
			echo '<a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html__( _draft_or_post_title(), 'woocommerce-tab-manager' ) . '</a>';

			// Display post states a little more selectively than `_post_states( $post )`.
			if ( 'draft' === $post->post_status ) {
				echo ' <span class="post-state">(' . esc_html__( 'Draft', 'woocommerce-tab-manager' ) . ')</span>';
			}

			echo '</strong>';

			// Get actions.
			$actions = array(
				'id'   => "ID: {$post->ID}",
				'slug' => "Slug: {$post->post_name}",
			);

			$post_type_object = get_post_type_object( $post->post_type );

			if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
				if ( 'trash' === $post->post_status ) {
					// Before: `post.php?post=%d`
					// After:  `post.php?post={$post->ID}&action=untrash`.
					$untrash_link = add_query_arg(
						array( 'action' => 'untrash' ),
						sprintf( $post_type_object->_edit_link, $post->ID )
					);

					/**
					 * `check_admin_referer( 'untrash-post_' . $post_id )` is
					 * used internally to check if the link is valid. The nonce
					 * action name has to match that or the link won't work.
					 *
					 * @link https://core.trac.wordpress.org/browser/trunk/src/wp-admin/post.php#L227
					 */
					$untrash_link       = wp_nonce_url(
						admin_url( $untrash_link ),
						"untrash-post_{$post->ID}"
					);
					$actions['untrash'] = '<a title="' . esc_attr__( 'Restore this item from the Trash', 'woocommerce-tab-manager' ) . '" href="' . esc_url( $untrash_link ) . '">' . esc_html__( 'Restore', 'woocommerce-tab-manager' ) . '</a>';
				} else {
					if ( defined( 'EMPTY_TRASH_DAYS' ) && EMPTY_TRASH_DAYS ) {
						$trash_link       = get_delete_post_link( $post->ID );
						$actions['trash'] = '<a class="submitdelete" title="' . esc_attr__( 'Move this item to the Trash', 'woocommerce-tab-manager' ) . '" href="' . esc_url( $trash_link ) . '">' . esc_html__( 'Trash', 'woocommerce-tab-manager' ) . '</a>';
					} else {
						$delete_link       = get_delete_post_link( $post->ID, '', true );
						$actions['delete'] = '<a class="submitdelete" title="' . esc_attr__( 'Delete this item permanently', 'woocommerce-tab-manager' ) . '" href="' . esc_url( $delete_link ) . '">' . esc_html__( 'Delete Permanently', 'woocommerce-tab-manager' ) . '</a>';
					}
				}
			}

			$actions = apply_filters( 'post_row_actions', $actions, $post );

			echo '<div class="row-actions">';

			$i            = 0;
			$action_count = count( $actions );

			foreach ( $actions as $action => $link ) {
				$sep = ( $i === $action_count - 1 ) ? '' : ' | ';
				$link .= $sep;
				// @codingStandardsIgnoreStart - phpcs complains about `$link` not being escaped.
				echo '<span class="' . sanitize_html_class( $action ) . '">' . $link . '</span>';
				// @codingStandardsIgnoreEnd
				$i ++;
			}
			echo '</div>';
		break;

		case 'type':

			if ( $post->post_parent ) {
				esc_html_e( 'Product', 'woocommerce-tab-manager' );
			} else {
				esc_html_e( 'Global', 'woocommerce-tab-manager' );
			}

		break;

		case 'parent-product':

			if ( $post->post_parent ) {
				$parent_product = wc_get_product( $post->post_parent );
				echo '<a href="' . esc_url( get_edit_post_link( $parent_product->get_id() ) ) . '">' . esc_html( $parent_product->get_title() ) . '</a>';
			} else {
				echo '<em>' . esc_html__( 'N/A', 'woocommerce-tab-manager' ) . '</em>';
			}

		break;

		case 'product-cat':

			$product_cats = get_post_meta( $post->ID, '_wc_tab_categories', true );

			if ( ! empty( $product_cats ) ) {

				foreach ( $product_cats as $term ) {

					$cat = get_term_by( 'id', $term, 'product_cat' );

					if ( $cat && isset( $cat->name ) ) {

						isset( $multiple ) ? edit_term_link( $cat->name, ', ', '', $cat ) : edit_term_link( $cat->name, '', '', $cat );

						$multiple = true;
					}
				}

			} else {

				echo '<em>' . esc_html__( 'N/A', 'woocommerce-tab-manager' ) . '</em>';
			}

		break;

		case 'is-searchable':

			if ( wc_tab_manager()->get_search_instance()->is_searchable_tab( $post->ID ) ) : ?>
				<?php
				$text = __( 'Yes', 'woocommerce-tab-manager' );
				?>
				<span class="dashicons dashicons-visibility" title="<?php echo esc_attr( $text ); ?>" aria-label="<?php echo esc_attr( $text ); ?>"></span>
			<?php else : ?>
				<?php
				$text = __( 'No', 'woocommerce-tab-manager' );
				?>
				<span class="dashicons dashicons-hidden" title="<?php echo esc_attr( $text ); ?>" aria-label="<?php echo esc_attr( $text ); ?>"></span>
			<?php endif;

		break;
	}
}


add_filter( 'parse_query', 'wc_tab_manager_admin_product_tab_filter_query' );

/**
 * On the Tabs page filter by tab type
 *
 * @access public
 * @param \WP_Query $query the query object.
 *
 * @return \WP_Query
 */
function wc_tab_manager_admin_product_tab_filter_query( $query ) {
	global $typenow, $wpdb;

	if ( 'wc_product_tab' === $typenow && isset( $_GET['product_tab_type'] ) ) {

		if ( 'global' === $_GET['product_tab_type'] ) {
			$query->set( 'post_parent', 0 );
		} else if ( 'product' === $_GET['product_tab_type'] ) {
			$query->set( 'post_parent__not_in', array( 0 ) );
		}
	}

	return $query;
}


add_action( 'restrict_manage_posts', 'wc_tab_manager_product_tabs_by_type', 20 );

/**
 * Render the "Show All Types" dropdown filter menu on the Product Tabs
 * page so that tabs can be filtered on their type (product/global)
 *
 * @access public
 */
function wc_tab_manager_product_tabs_by_type() {
	global $typenow;

	if ( 'wc_product_tab' === $typenow ) :
		$product_tab_type = isset( $_GET['product_tab_type'] ) ? $_GET['product_tab_type'] : '';
		?>
		<select name="product_tab_type" id="dropdown_product_tab_type">
			<option value="">
				<?php esc_html_e( 'Show all Tabs', 'woocommerce-tab-manager' ); ?>
			</option>
			<option value="product" <?php selected( 'product', $product_tab_type ); ?>>
				<?php esc_html_e( 'Show product tabs', 'woocommerce-tab-manager' ); ?>
			</option>
			<option value="global" <?php selected( 'global', $product_tab_type ); ?>>
				<?php esc_html_e( 'Show global tabs', 'woocommerce-tab-manager' ); ?>
			</option>
		</select>
		<?php
	endif;
}


add_filter( 'posts_join', 'wc_tab_manager_tabs_posts_join', 10, 2 );

/**
 * Modify the query to join back onto the posts table for the parent products to exclude
 * tabs for those that are in the trash.  The only drawback is that any global tabs
 * are returned multiple times, once for each product.  I deal with this by using
 * the groupby filter below.  Performance concerns?
 *
 * @since 1.0.6
 * @param  string   $join the join query.
 * @param \WP_Query $query the query object.
 * @return string Modified join query for product tab
 */
function wc_tab_manager_tabs_posts_join( $join, $query ) {
	global $wpdb, $typenow;

	if ( 'wc_product_tab' === $typenow ) {
		$join .= " JOIN {$wpdb->posts} AS product_parents ON ( {$wpdb->posts}.post_parent = 0 OR ( {$wpdb->posts}.post_parent = product_parents.ID AND product_parents.post_status != 'trash' ) )";
	}

	return $join;
}


add_filter( 'posts_groupby', 'wc_tab_manager_tabs_posts_groupby', 10, 2 );

/**
 * Group the returned tab posts for the Tabs table by ID.  This is to compensate
 * for the extra global tabs returend by the join query above.
 *
 * @since 1.0.6
 * @param  string   $groupby the group by query.
 * @param \WP_Query $query the query object.
 * @return string
 */
function wc_tab_manager_tabs_posts_groupby( $groupby, $query ) {
	global $wpdb, $typenow;

	if ( 'wc_product_tab' === $typenow ) {
		$groupby = "{$wpdb->posts}.ID";
	}

	return $groupby;
}


add_action( 'delete_post', 'wc_tab_manager_delete_post' );

/**
 * Invoked when a WordPress post is deleted.  If the post is a product
 * with child tabs, delete them as well to avoid leaving any orphans.
 *
 * @access public
 * @param int $post_id post identifier.
 */
function wc_tab_manager_delete_post( $post_id ) {

	if ( ! current_user_can( 'delete_posts' ) || ! $post_id ) {
		return;
	}

	// Does this post have any attached product tabs?
	$posts = get_posts( array(
		'numberposts' => - 1,
		'post_type'   => 'wc_product_tab',
		'post_parent' => $post_id,
	) );

	if ( $posts ) {

		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID );
		}

	}
}


add_action( 'woocommerce_duplicate_product', 'wc_tab_manager_duplicate_product', 10, 2 );

/**
 * Invoked when a product is duplicated and duplicates any product tabs
 *
 * @since 1.0.6
 */
function wc_tab_manager_duplicate_product( $new_id, $original_post ) {
	$tabs = get_post_meta( $new_id, '_product_tabs', true );

	if ( is_array( $tabs ) ) {

		$is_updated = false;
		foreach ( $tabs as $key => $tab ) {
			if ( 'product' === $tab['type'] ) {
				$tab_post = get_post( $tab['id'] );

				$new_tab_data = array(
					'post_title'    => $tab_post->post_title,
					'post_content'  => $tab_post->post_content,
					'post_status'   => 'publish',
					'ping_status'   => 'closed',
					'post_author'   => get_current_user_id(),
					'post_type'     => 'wc_product_tab',
					'post_parent'   => $new_id,
					'post_password' => uniqid( 'tab_' ),
					// Protects the post just in case.
				);

				// Link up the product to its new tab.
				$tabs[ $key ]['id'] = wp_insert_post( $new_tab_data );
				$is_updated         = true;
			}
		}

		if ( $is_updated ) {
			update_post_meta( $new_id, '_product_tabs', $tabs );
		}
	}
}
