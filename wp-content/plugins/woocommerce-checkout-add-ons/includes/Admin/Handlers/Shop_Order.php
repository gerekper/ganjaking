<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Handlers;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Shop Order CPT class
 *
 * Handles modifications to the shop order CPT on both View Orders list table and Edit Order screen
 *
 * @since 1.0
 */
class Shop_Order {


	/** @var array File labels container, used for unescaping file labels **/
	private $file_labels = array();


	/**
	 * Add actions/filters for View Orders/Edit Order screen
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add listable checkout add-on column titles to the orders list table
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'render_column_titles' ), 15 );

		// add listable checkout add-on column content to the orders list table
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_column_content' ), 5 );

		// add sortable checkout add-ons
		add_filter( 'manage_edit-shop_order_sortable_columns', array( $this, 'add_sortable_columns' ) );

		// process sorting
		add_filter( 'posts_orderby', array( $this, 'add_sortable_orderby' ), 10, 2 );

		// make add-ons filterable
		add_filter( 'posts_join',  array( $this, 'add_order_itemmeta_join' ) );
		add_filter( 'posts_where', array( $this, 'add_filterable_where' ) );

		// handle filtering
		add_action( 'restrict_manage_posts', array( $this, 'restrict_orders' ), 15 );

		// make add-ons searchable
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'add_search_fields' ) );

		// display add-on values in order edit screen
		add_filter( 'esc_html', array( $this, 'unescape_file_link_html' ), 20, 2 );
	}


	/** Listable Columns ******************************************************/


	/**
	 * Add any listable columns
	 *
	 * @since 1.0
	 * @param array $columns associative array of column id to display name
	 * @return array of column id to display name
	 */
	public function render_column_titles( $columns ) {

		// get all columns up to and excluding the 'order_actions' column
		$new_columns = array();

		foreach ( $columns as $name => $value ) {

			if ( 'order_actions' === $name ) {
				prev( $columns );
				break;
			}

			$new_columns[ $name ] = $value;
		}

		// inject our columns
		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			if ( $add_on->is_listable() ) {
				$new_columns[ $add_on->get_key() ] = $add_on->get_name();
			}
		}

		// add the 'order_actions' column, and any others
		foreach ( $columns as $name => $value ) {
			$new_columns[ $name ] = $value;
		}

		return $new_columns;
	}


	/**
	 * Display the values for the listable columns
	 *
	 * @since 1.0
	 * @param string $column the column name
	 */
	public function render_column_content( $column ) {
		global $post, $wpdb;

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			if ( $column === $add_on->get_key() ) {

				$query = $wpdb->prepare( "
					SELECT
						woi.order_item_id
					FROM {$wpdb->prefix}woocommerce_order_itemmeta woim
					RIGHT JOIN {$wpdb->prefix}woocommerce_order_items woi ON woim.order_item_id = woi.order_item_id
					WHERE 1=1
						AND woi.order_id = %d
						AND woim.meta_key = '_wc_checkout_add_on_id'
						AND woim.meta_value = %s
					",
					$post->ID,
					$add_on->get_id()
				);

				$item_id = $wpdb->get_var( $query );

				if ( $item_id ) {

					switch ( $add_on->get_type() ) {

						case 'checkbox':
							echo wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_value', true ) ? '&#x2713;' : '';
						break;

						case 'file':

							$file_ids    = explode( ',', wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_value', true ) );
							$files_count = count( $file_ids );
							$file_labels = array();

							echo '<a href="#" class="wc-checkout-add-ons-files-toggle">' . sprintf( _n( '%d file', '%d files', $files_count, 'woocommerce-checkout-add-ons' ), $files_count ) . '</a>';

							echo '<ul class="wc-checkout-add-ons-files">';
							foreach ( $file_ids as $key => $file_id ) {
								if ( $url = get_edit_post_link( $file_id ) ) {
									echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $file_id ) ) . '</a></li>';
								} else {
									echo '<li>' . esc_html__( '(File has been removed)', 'woocommerce-checkout-add-ons' ) . '</li>';
								}
							}
							echo '</ul>';

						break;

						case 'text':

							$label = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_label', true );
							echo $add_on->truncate_label( $label );

						break;

						case 'textarea':

							$label = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_value', true );
							$label = $add_on->normalize_value( $label, false );
							echo $add_on->truncate_label( $label );

						break;

						default:

							$label = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_label', true );
							echo is_array( $label ) ? implode( ', ', $label ) : $label;
					}

				}

				break;
			}
		}
	}


	/** Sortable Columns ******************************************************/


	/**
	 * Make order columns sortable
	 *
	 * @since 1.0
	 * @param array $columns associative array of column name to id
	 * @return array of column name to id
	 */
	public function add_sortable_columns( $columns ) {

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			if ( $add_on->is_sortable() ) {
				$columns[ $add_on->get_key() ] = $add_on->get_key();
			}
		}

		return $columns;
	}


	/**
	 * Modify SQL ORDEBY clause for sorting the orders by any sortable checkout add-ons
	 *
	 * @since 1.0
	 * @param string $orderby ORDERBY part of the sql query
	 * @return string $orderby modified ORDERBY part of sql query
	 */
	public function add_sortable_orderby( $orderby, $wp_query ) {
		global $typenow, $wpdb;

		if ( 'shop_order' !== $typenow ) {
			return $orderby;
		}

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {
			// if the add-on is filterable and selected by the user, and the join has not bee altered yet
			if ( $add_on->is_sortable() && isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] === $add_on->get_key() ) {

				// Sort by subquery results
				$orderby = $wpdb->prepare( "(
					SELECT
						woim_value.meta_value
					FROM {$wpdb->prefix}woocommerce_order_items woi
					RIGHT JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim_id ON woi.order_item_id = woim_id.order_item_id
					RIGHT JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim_value ON woi.order_item_id = woim_value.order_item_id
					WHERE 1=1
						AND woi.order_id = {$wpdb->prefix}posts.ID
						AND woim_id.meta_key = '_wc_checkout_add_on_id'
						AND woim_id.meta_value = %s
						AND woim_value.meta_key = '_wc_checkout_add_on_value'
					)",
					$add_on->get_id()
				);

				// Sorting order
				$orderby .= 'asc' === $wp_query->query['order'] ? ' ASC' : ' DESC';

				break;
			}
		}

		return $orderby;
	}


	/** Filterable Columns ******************************************************/


	/**
	 * Render dropdowns for any filterable checkout add-ons
	 *
	 * @since 1.0
	 */
	public function restrict_orders() {
		global $typenow;

		if ( 'shop_order' !== $typenow ) {
			return;
		}

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			// if the add-on is filterable
			if ( $add_on->is_filterable() ) {

				if ( $add_on->has_options() ) {

					// filterable multi item add-on field (select, multiselect, radio, checkbox), provide a dropdown
					?>
					<select
						name="<?php echo esc_attr( $add_on->get_key() ); ?>"
						id="<?php echo esc_attr( $add_on->get_key() ); ?>"
						class="wc-enhanced-select"
						data-placeholder="<?php
						/* translators: Placeholder: %s - Add on name */
						printf( esc_attr( __( 'Show all %s', 'woocommerce-checkout-add-ons' ) ), $add_on->get_name() ); ?>"
						data-allow_clear="true"
						style="min-width:200px;">
						<option value=""></option>
						<?php foreach ( $add_on->get_options() as $option ) : ?>
							<?php if ( '' === $option['label'] ) { continue; } ?>
							<?php $value    = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' ); ?>
							<?php $selected = isset( $_GET[ $add_on->get_key() ] ) ? selected( $value, $_GET[ $add_on->get_key() ], false ) : ''; ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php echo $selected; ?>><?php echo esc_html__( $option['label'], 'woocommerce-checkout-add-ons' ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php

				} elseif ( 'text' === $add_on->get_type() ) { ?>

					<select
						class="sv-wc-enhanced-search"
						name="<?php echo esc_attr( $add_on->get_key() ); ?>"
						style="min-width:200px;"
						data-placeholder="<?php
						/* translators: Placeholder: %s - Add on label */
						printf( __( 'Show all %s', 'woocommerce-checkout-add-ons' ), $add_on->get_label() ); ?>"
						value="<?php echo ( empty( $_GET[ $add_on->get_key() ] ) ? '' : esc_attr( $_GET[ $add_on->get_key() ] ) ); ?>"
						data-allow_clear="true"
						data-action="wc_checkout_add_ons_json_search_field"
						data-nonce="<?php echo wp_create_nonce( 'search-field' ); ?>"
						data-request_data="<?php echo esc_attr( json_encode( array( 'add_on_id' => $add_on->get_id(), 'default' => addslashes( __( 'Show all ', 'woocommerce-checkout-add-ons' ) . $add_on->get_name() ) ) ) ) ?>">
						data-selected="<?php echo ( empty( $_GET[ $add_on->get_key() ] ) ? '' : $_GET[ $add_on->get_key() ] ); ?>"
						<?php $key = isset( $_GET[ $add_on->get_key() ] ) ? $_GET[ $add_on->get_key() ] : null; ?>
						<?php if ( ! empty( $key ) ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $add_on->get_name() ); ?></option>
						<?php endif; ?>
					</select>

					<?php Framework\SV_WC_Helper::render_select2_ajax();

				} elseif ( 'checkbox' === $add_on->get_type() || 'file' === $add_on->get_type() ) {

					?>
					<label class="wc-checkout-add-on-checkbox-filter">

						<input
							type="checkbox"
							id="<?php echo esc_attr( $add_on->get_key() ); ?>"
							name="<?php echo esc_attr( $add_on->get_key() ); ?>"
							value="1"
							<?php checked( isset( $_GET[ $add_on->get_key() ] ) && $_GET[ $add_on->get_key() ], true, true ); ?> />

						<?php echo esc_html( $add_on->get_name() ); ?>
					</label>
					<?php

				}
			}
		}
	}


	/**
	 * Modify SQL JOIN for filtering the orders by any filterable checkout add-ons
	 *
	 * @since 1.0
	 * @param string $join JOIN part of the sql query
	 * @return string $join modified JOIN part of sql query
	 */
	public function add_order_itemmeta_join( $join ) {
		global $typenow, $wpdb;

		if ( 'shop_order' !== $typenow ) {
			return $join;
		}

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			$filtering = $add_on->is_filterable() && isset( $_GET[ $add_on->get_key() ] ) && $_GET[ $add_on->get_key() ];

			// if the join has not been altered yet, and the add-on is filterable
			if ( $filtering ) {

				$join .= "
					LEFT JOIN {$wpdb->prefix}woocommerce_order_items woi ON {$wpdb->posts}.ID = woi.order_id
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim_id ON woi.order_item_id = woim_id.order_item_id
					JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim_value ON woi.order_item_id = woim_value.order_item_id";

				// Break the foreach loop - we only need to alter the join clause once
				break;
			}

		}

		return $join;
	}


	/**
	 * Modify SQL WHERE for filtering the orders by any filterable checkout add-ons
	 *
	 * @since 1.0
	 * @param string $where WHERE part of the sql query
	 * @return string $where modified WHERE part of sql query
	 */
	public function add_filterable_where( $where ) {
		global $typenow, $wpdb;

		if ( 'shop_order' !== $typenow ) {
			return $where;
		}

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			// if the add-on is filterable and selected by the user, and the join has not bee altered yet
			if ( $add_on->is_filterable() && isset( $_GET[ $add_on->get_key() ] ) && $_GET[ $add_on->get_key() ] ) {

				$value = $_GET[ $add_on->get_key() ];

				// Main WHERE query part
				$where .= $wpdb->prepare( " AND woim_id.meta_key='_wc_checkout_add_on_id' AND woim_id.meta_value=%s AND woim_value.meta_key='_wc_checkout_add_on_value'", $add_on->get_id() );

				// Add-on type specific comparison logic
				switch ( $add_on->get_type() ) {

					case 'file':
						$where .= " AND woim_value.meta_value IS NOT NULL";
					break;

					case 'multiselect':
					case 'multicheckbox':

						$like = '%' . $wpdb->esc_like( $value ) . '%';
						$where .= $wpdb->prepare( " AND woim_value.meta_value LIKE %s ", $like );

					break;

					default:
						$where .= $wpdb->prepare( " AND woim_value.meta_value='%s' ", $value );

				}
			}
		}

		return $where;
	}


	/** Searchable ******************************************************/


	/**
	 * Add our checkout add-ons to the set of search fields so that
	 * the admin search functionality is maintained
	 *
	 * @since 1.0
	 * @param array $search_fields array of post meta fields to search by
	 * @return array of post meta fields to search by
	 */
	public function add_search_fields( $search_fields ) {

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {
			$search_fields[] = $add_on->get_key();
		}

		return $search_fields;
	}


	/**
	 * Unescape file link HTML
	 *
	 * Because all order fee item meta gets HTML escaped, the link
	 * will not display correctly. We unescape the HTML here so that the links
	 * to uploaded files work
	 *
	 * @since 1.0
	 * @param string $safe_text
	 * @param string $text
	 * @return string Escaped or unescaped text
	 */
	public function unescape_file_link_html( $safe_text, $text ) {

		if ( ! empty( $this->file_labels ) ) {

			foreach ( $this->file_labels as $key => $label ) {

				if ( false === strpos( $text, $label ) ) {

					$safe_text = $text;
					unset( $this->file_labels[ $key ] );
				}
			}
		}

		return $safe_text;
	}


}
