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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Tab Manager search integration.
 *
 * Modifies the main search query to include the tab content associated with
 * each product while keeping the individual product tab posts hidden from
 * the search results. In other words, if a product tab's content matches a
 * search query, the associated tab is included in the search results, even if
 * the product itself doesn't match the search query.
 *
 * @since 1.4.0
 */
class WC_Tab_Manager_Search extends \WP_Query {


	/**
	 * Constructor function.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

		// insert search-related inputs above default actions metabox inputs
		add_action( 'woocommerce_tab_manager_product_tab_actions_top', array( $this, 'tab_actions_meta_box_inputs' ), 10, 1 );

		// add dropdown to product pages to filter by tab layout
		add_action( 'restrict_manage_posts', array( $this, 'tab_layout_filter_dropdown' ), 20 );
		add_action( 'parse_query', array( $this, 'tab_layout_filter_query' ), 20 );

		// update tab content meta for products when a tab's post status changes
		add_action( 'wc_tab_manager_product_tabs_updated',         array( $this, 'on_product_tabs_updated' ), 10, 2 );
		add_action( 'wc_tab_manager_default_layout_before_update', array( $this, 'on_default_tabs_updated' ), 10, 2 );

		// add tab content to search results
		add_filter( 'posts_clauses', array( $this, 'modify_search_clauses' ), 20, 2 );

		add_action( 'all_admin_notices', array( $this, 'update_products_after_upgrade_nag' ) );

		add_action( 'activated_plugin',   array( $this, 'detect_relevanssi_activation' ), 10, 2 );
		add_action( 'deactivated_plugin', array( $this, 'detect_relevanssi_deactivation' ), 10, 2 );
	}


	/**
	 * Returns the main WC_Tab_Manager.
	 *
	 * @see \wc_tab_manager()
	 *
	 * @since 1.4.0
	 */
	public function get_plugin() {
		return wc_tab_manager();
	}


	/**
	 * Enqueues any search-related JS or CSS.
	 *
	 * @since 1.4.0
	 */
	public function scripts_and_styles() {

		$script_settings = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'wc_tab_manager_nonce' ),
		);

		$asset_url = $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-tab-manager-admin-batch-product-update.min.js';

		wp_enqueue_script( 'wc_tab_manager_batch_product_update', $asset_url, array( 'jquery', 'wp-util' ), \WC_Tab_Manager::VERSION, true );

		wp_localize_script( 'wc_tab_manager_batch_product_update', 'wc_tab_manager_admin_params', $script_settings );
	}


	/**
	 * Adds a tab ID to the list of searchable tabs.
	 *
	 * @since 1.4.0
	 * @param mixed $tab A post ID / object / array that corresponds to the tab you want to add.
	 * @param bool  $update_post_meta true to update the product tab post meta.
	 * @return bool|null
	 */
	public function add_searchable_tab( $tab, $update_post_meta = true ) {

		$tab = $this->get_plugin()->ensure_post( $tab );

		// Bail if invalid post object or product-level tab.
		if ( empty( $tab ) || $tab->post_parent ) {
			return false;
		}

		// Get an array containing each searchable tab's ID.
		$tabs = $this->get_searchable_tabs();

		// Make sure the current tab is in the list.
		if ( ! in_array( $tab->ID, $tabs, false ) ) {
			$tabs[] = $tab->ID;
		}

		// Update the product tab postmeta.
		if ( $update_post_meta ) {
			update_post_meta( $tab->ID, '_include_in_search', 'yes' );
		}

		// Update the list.
		$this->update_searchable_tabs( $tabs );
	}


	/**
	 * Removes a tab ID from the list of searchable tabs.
	 *
	 * @since 1.4.0
	 * @param mixed $tab A post ID / object / array that corresponds to the tab you want to remove.
	 * @param bool  $update_post_meta true to update the product tab post meta.
	 * @return bool|void
	 */
	public function remove_searchable_tab( $tab, $update_post_meta = true ) {

		$tab = $this->get_plugin()->ensure_post( $tab );

		// Bail if invalid post object or product-level tab.
		if ( empty( $tab ) || $tab->post_parent ) {
			return false;
		}

		// Get an array containing each searchable tab's ID.
		$tabs = $this->get_searchable_tabs();

		// See if the specified tab ID is in the list of searchable tabs.
		if ( in_array( $tab->ID, $tabs, false ) ) {

			// If so, remove the tab ID from the list.
			$tabs = array_diff( $tabs, array( $tab->ID ) );

			// Re-index the array (not sure if this is necessary).
			$tabs = array_merge( $tabs );

			// Update the product tab postmeta.
			if ( $update_post_meta ) {
				delete_post_meta( $tab->ID, '_include_in_search' );
			}

			// Update the list.
			$this->update_searchable_tabs( $tabs );
		}
	}


	/**
	 * Gets the saved list of searchable tabs.
	 *
	 * @since 1.4.0
	 * @return array An array containing the numeric post ID for each tab.
	 */
	public function get_searchable_tabs() {
		return get_option( 'wc_tab_manager_searchable_tabs', array() );
	}


	/**
	 * Updates the saved list of searchable tabs.
	 *
	 * @since 1.4.0
	 * @param array $tabs An array of post IDs / objects / arrays where each array value corresponds to a tab you want to include in the new list.
	 */
	public function update_searchable_tabs( $tabs = array() ) {

		// Bail out if it is not an array.
		if ( ! is_array( $tabs ) ) {
			return;
		}

		// Remove duplicate entries, convert to integers, and remove 0 values.
		$tabs = array_map( 'absint', array_unique( $tabs ) );
		$tabs = array_filter( $tabs );

		update_option( 'wc_tab_manager_searchable_tabs', $tabs );

		$this->update_relevanssi_searchable_tab_meta();
	}


	/**
	 * Function to determine if a product tab is searchable.
	 *
	 * @since 1.4.0
	 * @param mixed $tab A post ID / object / array that corresponds to the tab you want to check.
	 * @return bool Whether the specified product tab is searchable.
	 */
	public function is_searchable_tab( $tab ) {

		// Make sure we have a valid post object before continuing.
		$tab = $this->get_plugin()->ensure_post( $tab );
		if ( empty( $tab ) ) {
			return false;
		}

		// All product-level tabs are searchable.
		if ( $tab->post_parent ) {
			return true;
		}

		// Check searchable setting in post meta.
		if ( 'yes' === get_post_meta( $tab->ID, '_include_in_search', true ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Function to determine if a product tab is in the default layout.
	 *
	 * @since 1.4.0
	 * @param mixed $tab A post ID / object / array that corresponds to the tab you want to check.
	 * @return bool Whether the specified product tab is in the default layout.
	 */
	public function is_default_tab( $tab ) {

		// Make sure we have a valid post object before continuing.
		$tab = $this->get_plugin()->ensure_post( $tab );
		if ( empty( $tab ) ) {
			return false;
		}

		$default_tabs = get_option( 'wc_tab_manager_default_layout', array() );
		if ( empty( $default_tabs ) ) {
			return false;
		}

		$default_tab_ids = $this->get_plugin()->get_numeric_ids( $default_tabs );

		return in_array( $tab->ID, $default_tab_ids, false );
	}


	/**
	 * Outputs search-related form controls on the product tab actions metabox.
	 *
	 * @since 1.4.0
	 * @param WP_Post $post The post object for the tab currently being edited.
	 */
	public function tab_actions_meta_box_inputs( $post ) {

		if ( ! $post->post_parent && 'yes' === get_option( 'wc_tab_manager_enable_search', 'yes' ) ) : ?>

			<li class="wide">
				<?php
				$include_in_search = $this->is_searchable_tab( $post->ID );
				$input_checked_att = $include_in_search ? 'checked="checked"' : '';
				?>
				<input type="hidden" name="tab_type" value="global" />
				<label for="wc_product_tab_include_in_search">
					<input type="checkbox" id="wc_product_tab_include_in_search" name="_include_in_search" value="yes" <?php echo esc_attr( $input_checked_att ); ?> />
					<?php esc_html_e( 'Include tab content in search results?', 'woocommerce-tab-manager' ); ?>
				</label>
			</li>

		<?php else : ?>

			<input type="hidden" name="tab_type" value="product" />

		<?php endif;
	}


	/**
	 * Render a dropdown on product pages so customers can filter products by
	 * tab layout type (any, default, or custom).
	 *
	 * @since 1.4.0
	 */
	public function tab_layout_filter_dropdown() {

		if ( 'product' !== $this->get_plugin()->get_current_post_type() ) {
			return;
		}

		$current_type = isset( $_GET['product_tab_layout'] ) ? $_GET['product_tab_layout'] : '';

		?>
		<select name="product_tab_layout">
			<option value="">
				<?php esc_html_e( 'Any tab layout', 'woocommerce-tab-manager' ); ?>
			</option>
			<option value="default" <?php selected( 'default', $current_type ); ?>>
				<?php esc_html_e( 'Default layout', 'woocommerce-tab-manager' ); ?>
			</option>
			<option value="custom" <?php selected( 'custom', $current_type ); ?>>
				<?php esc_html_e( 'Custom layout', 'woocommerce-tab-manager' ); ?>
			</option>
		</select>
		<?php
	}


	/**
	 * Modifies the main query on product pages when the tab layout filter is in use.
	 *
	 * @since 1.4.0
	 *
	 * @param \WP_Query $query The main WP_Query object.
	 * @return \WP_Query
	 */
	public function tab_layout_filter_query( $query ) {

		$tab_layout = isset( $_GET['product_tab_layout'] ) ? $_GET['product_tab_layout'] : '';

		if ( empty( $tab_layout ) ) {
			return $query;
		}

		$meta_query = $query->get( 'meta_query' );
		if ( empty( $meta_query ) ) {
			$meta_query = array();
		}

		$tab_meta_query = $this->get_tab_layout_meta_query( $tab_layout );
		$meta_query     = array_merge( $meta_query, $tab_meta_query );

		$query->set( 'meta_query', $meta_query );

		return $query;
	}


	/**
	 * Returns an array containing the meta type for each tab type; "combined"
	 * tabs have a single meta entry that contains the combined content of each
	 * tab, while "separate" tabs create a new meta entry for each tab. Each
	 * tab type is filtered by `wc_tab_manager_tab_meta_type_{tab_type}` and
	 * then the entire list is run through the `wc_tab_manager_tab_meta_types`
	 * filter. Useful if a customer needs to change the default settings.
	 *
	 * @since 1.4.0
	 * @return array associative array where each key is a tab type ('product' or 'global') and each value is a meta type ('combined' or 'separate').
	 */
	public function get_tab_meta_types() {

		$meta_types = array(
			'product' => 'combined',
			'global'  => 'separate',
		);

		$filtered_types = array();

		foreach ( $meta_types as $tab_type => $meta_type ) {

			/**
			 * Individual tab meta type filters.
			 *
			 * Allows customer to modify the default meta types on an individual
			 * basis. The filter name incorporates the tab type at the end (e.g.
			 * wc_tab_manager_tab_meta_type_product is used to filter the meta type
			 * for product-level tabs).
			 *
			 * @since 1.4.0
			 * @param string $meta_type The meta type setting for the specified tab type.
			 */
			$filtered_types[ $tab_type ] = apply_filters( "wc_tab_manager_tab_meta_type_{$tab_type}", $meta_type );
		}

		/**
		 * All tab meta types filter.
		 *
		 * Allows customer to modify the default meta types. For example, if
		 * you wanted to combine global tab content you would do something like
		 * this in your filter callback:
		 *
		 * `$meta_types['global'] = 'combined'`
		 *
		 * @since 1.4.0
		 * @param array $filtered_types The entire array of meta types.
		 */
		return apply_filters( 'wc_tab_manager_tab_meta_types', $filtered_types );
	}


	/**
	 * Triggered when a product is saved that overrides the default tab layout
	 * and has custom product tabs. Checks for any new or deleted tabs and
	 * updates the tab content meta accordingly for the current product.
	 *
	 * @since 1.4.0
	 * @param array $new_tabs The current set of tabs (after the update).
	 * @param array $old_tabs The previous set of tabs (before the update).
	 */
	public function on_product_tabs_updated( $new_tabs, $old_tabs ) {
		$this->on_tabs_added_or_removed( $new_tabs, $old_tabs, 'custom' );
	}


	/**
	 * Triggered when the default tab layout is updated. Checks for any new or
	 * deleted tabs and updates the tab content accordingly for any products
	 * that use the default layout.
	 *
	 * @since 1.4.0
	 * @param array $new_tabs The current set of tabs (after the update).
	 * @param array $old_tabs The previous set of tabs (before the update).
	 */
	public function on_default_tabs_updated( $new_tabs, $old_tabs ) {
		$this->on_tabs_added_or_removed( $new_tabs, $old_tabs, 'default' );
	}


	/**
	 * Generic function to check for added or removed tabs whenever a list of
	 * tabs is saved. Updates tab content meta for any products associated with
	 * the added or removed tabs.
	 *
	 * @since 1.4.0
	 * @see WC_Tab_Manager_Search::update_products_for_tabs()
	 * @param array  $new_tabs The current set of tabs (after the update).
	 * @param array  $old_tabs The previous set of tabs (before the update).
	 * @param string $target   Determines products will be updated if a product ID can't be determined automatically from the current context.
	 */
	public function on_tabs_added_or_removed( $new_tabs, $old_tabs, $target ) {

		if ( ! is_array( $new_tabs ) || ! is_array( $old_tabs ) ) {
			return;
		}

		// Extract tab IDs excluding non-numeric IDs (e.g. core tabs).
		$new_tabs = $this->get_plugin()->get_numeric_ids( $new_tabs );
		$old_tabs = $this->get_plugin()->get_numeric_ids( $old_tabs );

		// `array_diff` isn't a bi-directional comparison, it returns an array
		// containing all elements that are in array 1 but not array 2.
		$added   = array_diff( $new_tabs, $old_tabs );
		$removed = array_diff( $old_tabs, $new_tabs );

		// Bail if no tabs have been added or removed.
		if ( empty( $added ) && empty( $removed ) ) {
			return;
		}

		// Update product meta for tabs that were added / removed.
		if ( ! empty( $added ) ) {

			$args = array(
				'action' => 'update',
				'target' => $target,
			);

			$this->update_products_for_tabs( $added, $args );
		}

		if ( ! empty( $removed ) ) {

			$args = array(
				'action' => 'remove',
				'target' => $target,
			);

			$this->update_products_for_tabs( $removed, $args );
		}
	}


	/**
	 * Finds all products associated with a specific tab and updates the tab
	 * content meta for each product.
	 *
	 * @since 1.4.0
	 * @see \WC_Tab_Manager_Search::update_products_for_tabs()
	 * @see \WC_Tab_Manager_Search::get_tab_products() for more details about the `$args` parameter.
	 * @param int|string $tab_id A numeric post ID for the tab to process.
	 * @param array $args Optional. An array of arguments. Default empty.
	 * @return bool
	 */
	public function update_products_for_tab( $tab_id, $args = array() ) {
		if ( ! is_numeric( $tab_id ) ) {
			return false;
		}

		$this->update_products_for_tabs( $tab_id, $args );
	}


	/**
	 * Loops through an array of tab IDs and finds all products associated with
	 * each tab, then updates the tab content meta for each product.
	 *
	 * @since 1.4.0
	 * @see \WC_Tab_Manager_Search::get_tab_products() for more details about the `$args` parameter.
	 * @param mixed $tab_id_list Either a numeric post ID or an array of numeric post IDs for the tab(s) to process.
	 * @param array $args Optional. An array of args. Default empty.
	 * @return bool
	 */
	public function update_products_for_tabs( $tab_id_list, $args = array() ) {

		// The `target` arg determines which tabs are updated; `all` means all
		// tabs, `custom` means only tabs that override the default layout, and
		// `default` means only tabs that don't override the default layout.
		$defaults = array(
			'action'     => 'update', // Can be 'update' or 'remove'.
			'target'     => 'custom', // Can be 'all', 'custom', or 'default'.
			'product_id' => 0,
		);

		$args   = wp_parse_args( $args, $defaults );
		$target = $args['target'];

		if ( ! is_array( $tab_id_list ) && is_numeric( $tab_id_list ) ) {
			$tab_id_list = array( $tab_id_list );
		}

		$tab_id_list = array_filter( $tab_id_list, 'absint' );

		// If a product ID was specified, use that for the product ID list. If
		// not and we're on a product page or editing a product-level tab, only
		// update the tab meta for that product; otherwise, update all products
		// associated with each tab.
		$product_id = isset( $args['product_id'] ) ? absint( $args['product_id'] ) : $this->get_plugin()->maybe_get_tab_product_id();

		if ( $product_id ) {
			$product_id_list = array( $product_id );
		} else {
			$product_id_list = $this->get_tab_products( $tab_id_list, $args );
		}

		if ( empty( $product_id_list ) || empty( $tab_id_list ) ) {
			return false;
		}

		$tab_id_list     = array_unique( $tab_id_list );
		$product_id_list = array_unique( $product_id_list );

		$meta_key   = '';
		$meta_types = $this->get_tab_meta_types();

		foreach ( $product_id_list as $product_id ) {

			$override = '';

			if ( $product = wc_get_product( $product_id ) ) {
				$override = $product->get_meta( '_override_tab_layout' );
			}

			// If we're targeting products that use custom layouts and the
			// current product doesn't use a custom layout we should remove
			// the tab content meta.
			if ( 'custom' === $target && 'yes' !== $override ) {
				$action = 'remove';
			} else {
				$action = $args['action'];
			}

			foreach ( $tab_id_list as $tab_id ) {

				// Make sure we have a valid post object before continuing.
				$tab = $this->get_plugin()->ensure_post( $tab_id );
				if ( empty( $tab ) ) {
					continue;
				}

				if ( isset( $tab->post_parent ) && $tab->post_parent ) {
					$tab_type = 'product';
				} else {
					$tab_type = 'global';
				}

				// Product tabs only apply to custom tab layouts, so if the
				// current product isn't using a custom layout we should remove
				// the tab content meta.
				if ( 'product' === $tab_type && 'yes' !== $override ) {
					$action = 'remove';
				} else {
					$action = $args['action'];
				}

				// `$meta_type` determines whether we should store each tab's
				// content in a separate meta field or combine their content
				// and store the result in a single meta field.
				$meta_type = $meta_types[ $tab_type ];
				if ( 'separate' === $meta_type ) {
					$meta_key = "_{$tab_type}_tab_{$tab_id}_content";
				} else if ( 'combined' === $meta_type ) {
					$meta_key = "_{$tab_type}_tab_content";
				}

				if ( 'separate' === $meta_type ) {
					if ( 'update' === $action ) {
						update_post_meta( $product_id, $meta_key, $tab->post_content );
					} else if ( 'remove' === $action ) {
						delete_post_meta( $product_id, $meta_key );
					}
				} else if ( 'combined' === $meta_type ) {
					if ( 'update' === $action ) {
						if ( ! isset( $combined_content[ $tab_type ] ) ) {
							$combined_content[ $tab_type ] = '';
						}
						$combined_content[ $tab_type ] .= $tab->post_content . ' ';
					} else {
						delete_post_meta( $product_id, $meta_key );
					}
				}
			}

			if ( ! empty( $combined_content ) && 'update' === $action ) {

				foreach ( $combined_content as $tab_type => $content ) {

					if ( $product = wc_get_product( $product_id ) ) {

						$product->update_meta_data( "_{$tab_type}_tab_content", trim( $content ) );
						$product->save_meta_data();
					}
				}
			}

			$this->update_relevanssi_index_for_product( $product_id );
		}
	}


	/**
	 * Gets all products that include the specified tab ID(s).
	 *
	 * The type of products that are targeted will vary depending on the tab
	 * being processed:
	 *
	 * Product-level tabs: Returns the parent product
	 * Global default tabs: Returns all products that use the default layout
	 * Global non-default tabs: Returns all products that don't use the default layout
	 *
	 * @since 1.4.0
	 * @param mixed $tab_id_list Optional. Either a numeric tab ID or an array of numeric tab IDs. Default empty.
	 * @param array $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type string     `action`     The action to take on the tab content meta for each product found.
	 *                                   Default 'update'. Accepts 'update', 'remove'.
	 *     @type string     `target`     The type of products that should be targeted for each tab. Only used when a product ID is not specified and one can't be automatically determined.
	 *                                   Default 'custom'. Accepts 'all', 'custom', 'default'.
	 *                                   Note: 'all' targets all products, 'custom' targets products that use a custom tab layout, and 'default' targets products that use the default tab layout.
	 *     @type int|string `product_id` A numeric ID for the product to update. If an ID isn't specified it is automatically determined.
	 *                                   When saving a product, the current product ID is used. When updating a product-level tab from the Tab Manager, the tab's parent product ID is used.
	 *                                   If an ID still hasn't been found, `get_posts()` is used to find products for each tab based on the value of `target`.
	 * }
	 * @return array An array of numeric product IDs.
	 */
	public function get_tab_products( $tab_id_list = array(), $args = array() ) {

		$args = wp_parse_args( $args, array(
			'action' => 'update',
			'target' => 'custom',
			'offset' => 0,
			'limit'  => -1,
		) );

		$target = $args['target'];
		$offset = $args['offset'];
		$limit  = $args['limit'];

		// See if we have any default global tabs that are searchable.
		$default_tabs = get_option( 'wc_tab_manager_default_layout', array() );

		foreach ( $default_tabs as $tab ) {

			if ( 'global' !== $tab['type'] ) {
				continue;
			}

			// Check tab meta to see if tab is included in search results.
			$tab_id = $tab['id'];

			if ( $this->is_searchable_tab( $tab_id ) ) {

				$searchable_tabs[] = $tab_id;
			}
		}

		// Get all products that use the updated global tab.
		$query_args = array(
			'fields'         => 'ids',
			'post_type'      => 'product',
			'posts_per_page' => $limit,
			'offset'         => $offset,
		);

		// Get meta query to target tabs with the specified layout.
		$meta_query = $this->get_tab_layout_meta_query( $target );

		// See if we have any specific custom tabs to check.
		if ( 'custom' === $target ) {

			// If so, restrict the query to products that have one of the
			// specified tab IDs in `_product_tabs`.
			if ( ! empty( $tab_id_list ) ) {

				// Make sure we have an array of tab IDs.
				if ( ! is_array( $tab_id_list ) ) {
					$tab_id_list = array( $tab_id_list );
				}

				$tab_id_list_serialized  = array();

				// Get the serialized value for each tab ID.
				foreach ( $tab_id_list as $tab_id ) {

					if ( ! is_numeric( $tab_id ) ) {
						continue;
					}

					// `_product_tabs` is a serialized array with data for each
					// tab, hence the `sprintf()` for the meta value.
					$tab_id_list_serialized[] = sprintf( '("product_tab_%d")', $tab_id );
				}

				// Convert the serialized tab ID list to a RegEx pattern.
				$tab_id_list_regex = implode( '|', $tab_id_list_serialized );

				// Modify the meta query.
				$meta_query['relation'] = 'AND';
				$meta_query[] = array(
					'key'     => '_product_tabs',
					'value'   => $tab_id_list_regex,
					'compare' => 'RLIKE',
				);
			}
		}

		if ( 'all' !== $target && ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		return get_posts( $query_args );
	}


	/**
	 * Wrapper function for @see \WP_Query::parse_search_terms()
	 *
	 * @since 1.4.0
	 * @param string|array $terms Terms to check
	 * @return array Terms that are not stopwords
	 */
	public function parse_search_terms( $terms ) {

		if ( empty( $terms ) ) {
			$terms = '';
		}

		if ( is_string( $terms ) ) {
			$terms = explode( ' ', $terms );
		}

		return parent::parse_search_terms( $terms );
	}


	/**
	 * Returns a meta query designed to target products with a specific tab
	 * layout.
	 *
	 * @since  1.4.0
	 *
	 * @link   https://core.trac.wordpress.org/ticket/23268
	 *
	 * @param  string $layout The tab layout you want to target. Can be 'default' or 'custom'.
	 * @return string The meta query if a valid `$layout` value was passed; an empty string otherwise.
	 */
	public function get_tab_layout_meta_query( $layout = 'all' ) {

		if ( 'default' === $layout ) {

			return array(
				'relation' => 'OR',
				// Prior to 3.9 the `NOT EXISTS` comparator would only work
				// if `value` was set and was not null, empty, or 0.
				array(
					'key'     => '_override_tab_layout',
					'value'   => 'bug #23268',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'   => '_override_tab_layout',
					'value' => 'no',
				),
			);
		}

		if ( 'custom' === $layout ) {

			return array(
				'relation' => 'AND',
				array(
					'key'   => '_override_tab_layout',
					'value' => 'yes',
				),
			);
		}

		return '';
	}


	/**
	 * Adds the meta keys associated with product-level and global-level tab
	 * content to the main search query so that all product-level tabs will be
	 * included in the search results, as well as any global tabs that have
	 * been designated to be included in the search results.
	 *
	 * @since 1.4.0
	 * @param  array    $clauses   An array of search clauses where the key is the clause type (where, join, etc.) and the value is the SQL for that clause.
	 * @param  WP_Query $_wp_query The WP_Query object for the current search.
	 * @return array    The updated array of clauses.
	 */
	public function modify_search_clauses( $clauses, $_wp_query ) {
		global $wpdb;

		if ( ! $_wp_query instanceof \WP_Query || 'no' === get_option( 'wc_tab_manager_enable_search', 'yes' ) || ! $_wp_query->is_search || is_admin() ) {
			return $clauses;
		}

		// get the current search terms and list of searchable tabs
		$search_query    = $_wp_query->get( 's' );
		$search_terms    = $this->parse_search_terms( $search_query );
		$searchable_tabs = $this->get_searchable_tabs();

		// get product-level tabs (which must have a parent value)
		$custom_tabs = array_filter( get_posts( array(
			'fields'         => 'id=>parent',
			'post_type'      => 'wc_product_tab',
			'posts_per_page' => - 1,
		) ) );

		// bail if either the term list or the tab ID list is empty
		if ( empty( $search_terms ) || ( empty( $searchable_tabs ) && empty( $custom_tabs ) ) ) {
			return $clauses;
		}

		// get list of meta keys to search
		$meta_keys = array( '_product_tab_content' );
		if ( ! empty( $searchable_tabs ) ) {
			foreach ( $searchable_tabs as $tab_id ) {
				$meta_keys[] = "_global_tab_{$tab_id}_content";
			}
		}

		// add tab content to WHERE
		$where = '';
		foreach ( $search_terms as $term ) {
			foreach ( $meta_keys as $meta_key ) {
				$meta_like = '%' . $wpdb->esc_like( $term ) . '%';
				$where_sql = " OR (
					{$wpdb->postmeta}.meta_key = %s
					AND {$wpdb->postmeta}.meta_value LIKE %s
				)";
				$where .= $wpdb->prepare( $where_sql, $meta_key, $meta_like );
			}
		}
		$clauses['where'] .= $where;

		// JOIN to include postmeta in search
		if ( false === strpos( $clauses['join'], "INNER JOIN {$wpdb->postmeta} ON" ) ) {
			$clauses['join'] .= " INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )";
		}

		// GROUP BY post ID so we don't get duplicate posts
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		return $clauses;
	}


	/**
	 * Displays a notice when upgrading the plugin from an older version that
	 * doesn't support tab content search to one that does. The notice informs
	 * the user that if they'd like to use these search features, they need to
	 * manually update their product meta entries.
	 *
	 * @since 1.4.0
	 */
	public function update_products_after_upgrade_nag() {
		$user = wp_get_current_user();

		if ( isset( $_GET['wc_tab_manager_show_update_products_nag'] ) && 'false' === $_GET['wc_tab_manager_show_update_products_nag'] ) {
			delete_user_meta( $user->ID, 'wc_tab_manager_show_update_products_nag' );
		}

		if ( isset( $_GET['wc_tab_manager_show_relevanssi_nag'] ) && 'false' === $_GET['wc_tab_manager_show_relevanssi_nag'] ) {
			delete_user_meta( $user->ID, 'wc_tab_manager_show_relevanssi_nag' );
		}

		$show_update_nag     = get_user_meta( $user->ID, 'wc_tab_manager_show_update_products_nag', true );
		$show_relevanssi_nag = get_user_meta( $user->ID, 'wc_tab_manager_show_relevanssi_nag', true );

		if ( ! empty( $show_update_nag ) ) {
			$nag_text  = __( 'Hey there, Tab Manager has a nifty new search feature. If you\'d like to take advantage of this you\'ll need to update your products and tabs (you only need to do this once).', 'woocommerce-tab-manager' );
			$url_param = 'wc_tab_manager_show_update_products_nag';
		} else if ( ! empty( $show_relevanssi_nag ) ) {
			$nag_text  = __( 'It looks like you recently installed the Relevanssi plugin. If you\'d like to use the new Tab Manager search feature with Relevanssi, you\'ll need to update your products and tabs so they can be indexed.', 'woocommerce-tab-manager' );
			$url_param = 'wc_tab_manager_show_relevanssi_nag';
		} else {
			return;
		}

		$update_url       = admin_url( "admin.php?page=wc-settings&tab=products&section=wc_tab_manager&{$url_param}=false" );
		$update_url_text  = __( 'Go to the update page.', 'woocommerce-tab-manager' );
		$dismiss_url      = add_query_arg( $url_param, 'false' );
		$dismiss_url_text = __( 'OK, don\'t remind me again.', 'woocommerce-tab-manager' );

		?>
		<div class="notice updated">
			<p>
				<?php echo esc_html( $nag_text ); ?>
			</p>
			<p>
				<a href="<?php echo esc_url( $update_url ); ?>">
					<?php echo esc_html( $update_url_text ); ?>
				</a> |
				<a href="<?php echo esc_url( $dismiss_url ); ?>">
					<?php echo esc_html( $dismiss_url_text ); ?>
				</a>
			</p>
		</div>
		<?php
	}


	/**
	 * Fires whenever a plugin is activated and checks to see if the plugin
	 * was Relevanssi. If so, a notice is scheduled informing the user that they
	 * need to update their products and tabs in order for them to be indexed.
	 *
	 * @since 1.4.0
	 * @param string $plugin The slug of the plugin that was just activated (e.g. `plugin-folder/plugin-file.php`).
	 * @param bool   $network_activation True if the plugin was activated network-wide; false otherwise.
	 */
	public function detect_relevanssi_activation( $plugin, $network_activation ) {

		if ( is_string( $plugin ) && 'relevanssi/relevanssi.php' === $plugin ) {

			$user     = wp_get_current_user();
			$show_nag = get_user_meta( $user->ID, 'wc_tab_manager_show_relevanssi_nag', true );

			if ( empty( $show_nag ) ) {

				update_user_meta( $user->ID, 'wc_tab_manager_show_relevanssi_nag', true );
			}
		}
	}


	/**
	 * Fires whenever a plugin is deactivated and checks to see if the plugin
	 * was Relevanssi. If so, the notice that was scheduled on de-activation is
	 * un-scheduled in case it hasn't been dismissed yet.
	 *
	 * @since 1.4.0
	 * @param string $plugin The slug of the plugin that was just activated (e.g. `plugin-folder/plugin-file.php`).
	 * @param bool   $network_activation True if the plugin was activated network-wide; false otherwise.
	 */
	public function detect_relevanssi_deactivation( $plugin, $network_activation ) {

		if ( is_string( $plugin ) && 'relevanssi/relevanssi.php' === $plugin ) {

			$user = wp_get_current_user();

			delete_user_meta( $user->ID, 'wc_tab_manager_show_relevanssi_nag' );
		}
	}


	/**
	 * Forces Relevanssi to re-index a specific product. Used to keep the index
	 * up-to-date whenever a tab or product is updated.
	 *
	 * @since 1.4.0
	 * @param int|string $product_id The numeric post ID for the product that should be re-indexed.
	 */
	public function update_relevanssi_index_for_product( $product_id ) {

		if ( is_callable( 'relevanssi_index_doc' ) && is_callable( 'relevanssi_get_custom_fields' ) ) {

			$custom_fields = relevanssi_get_custom_fields();

			relevanssi_index_doc( $product_id, true, $custom_fields, true );
		}
	}


	/**
	 * Checks if Relevanssi is installed; if so, builds the index if it hasn't
	 * been built already.
	 *
	 * @since 1.4.0
	 */
	public function maybe_build_relevanssi_index() {

		// Check if Relevanssi is installed and active.
		if ( is_callable( 'relevanssi_build_index' ) ) {

			// Make sure that products are searchable.
			$index_post_types = get_option( 'relevanssi_index_post_types' );
			if ( ! is_array( $index_post_types ) ) {
				$index_post_types = array( 'product' );
			} else {
				if ( ! in_array( 'product', $index_post_types, true ) ) {
					$index_post_types[] = 'product';
				}
			}
			update_option( 'relevanssi_index_post_types', $index_post_types );

			// Check if the index has been built yet.
			if ( ! get_option( 'relevanssi_indexed' ) ) {

				// If not, build it now.
				relevanssi_build_index();
			}
		}
	}


	/**
	 * Checks if the Relevanssi plugin is installed and active and, if so, loops
	 * updates the `relevanssi_index_fields` option to ensure that all of our
	 * tab content meta fields are indexed.
	 *
	 * @since 1.4.0
	 */
	public function update_relevanssi_searchable_tab_meta() {

		// Check if Relevanssi is installed and active.
		if ( is_callable( 'relevanssi_get_custom_fields' ) ) {

			// Get the current list of searchable tabs.
			$tabs = get_option( 'wc_tab_manager_searchable_tabs', array() );

			// Get the current list of indexed custom fields.
			$custom_fields = get_option( 'relevanssi_index_fields' );

			// If any indexed fields were returned, split them into an array.
			// Otherwise use an empty array.
			if ( empty( $custom_fields ) ) {
				$custom_fields = array();
			} else {
				$custom_fields = explode( ',', $custom_fields );
				$custom_fields = array_map( 'trim', $custom_fields );
			}

			// Make sure our product tab content meta key is in the array.
			if ( ! in_array( '_product_tab_content', $custom_fields, true ) ) {
				$custom_fields[] = '_product_tab_content';
			}

			// Loop through the searchable tabs and make sure each tabs' meta
			// key is in the array.
			foreach ( $tabs as $tab_id ) {

				$tab_field = "_global_tab_{$tab_id}_content";

				if ( ! in_array( $tab_field, $custom_fields, true ) ) {

					$custom_fields[] = $tab_field;
				}
			}

			// Loop through each field and make sure there aren't any meta keys
			// for tabs that aren't searchable.
			foreach ( $custom_fields as $index => $value ) {

				$id = str_replace( array( '_global_tab_', '_content' ), '', $value );

				if ( is_numeric( $id ) && ! in_array( $id, $tabs, false ) ) {

					unset( $custom_fields[ $index ] );
				}
			}

			// Convert back to a comma-separated list and save.
			$custom_fields = implode( ',', $custom_fields );

			update_option( 'relevanssi_index_fields', $custom_fields );
		}
	}


}
