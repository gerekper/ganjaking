<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Pickup Locations Admin class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Pickup_Locations_Admin {


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// render WooCommerce Settings tabs while in the Pickup Locations edit screens
		add_action( 'all_admin_notices', array( $this, 'output_woocommerce_settings_tabs_html' ), 5 );

		// add Pickup Locations to the Shipping tab sections in WooCommerce settings
		add_filter( 'woocommerce_get_sections_shipping', array( $this, 'add_pickup_locations_section' ) );
		add_action( 'woocommerce_sections_shipping',     array( $this, 'load_pickup_locations_edit_screen' ) );

		// pickup Location admin screen columns
		add_filter( 'manage_edit-wc_pickup_location_columns',          array( $this, 'customize_columns' ) );
		add_filter( 'manage_edit-wc_pickup_location_sortable_columns', array( $this, 'customize_sortable_columns' ) );
		add_action( 'manage_wc_pickup_location_posts_custom_column',   array( $this, 'custom_column_content' ), 10, 2 );

		// sort Pickup Locations by address pieces
		add_filter( 'request', array( $this, 'sort_columns' ) );

		// search Pickup Locations by phone
		add_filter( 'posts_join',    array( $this, 'filter_posts_join' ) );
		add_filter( 'posts_where',   array( $this, 'filter_posts_where' ) );
		add_filter( 'posts_groupby', array( $this, 'filter_groupby' ) );

		// customize Pickup Locations admin screen row actions
		add_filter( 'post_row_actions', array( $this, 'customize_row_actions' ), 10, 2 );
		// customize Pickup Locations bulk actions
		add_filter( 'bulk_actions-edit-wc_pickup_location', array( $this, 'customize_bulk_actions' ) );

		// filter the "Enter title here" post title placeholder
		add_filter( 'enter_title_here', array( $this, 'pickup_location_title_placeholder' ) );

		// add a Pickup Location permanent delete action link that replaces the send to trash link
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_delete_pickup_location_action_link' ) );

		// add custom navigation buttons for the import and export admin pages
		add_action( 'load-edit.php', array( $this, 'add_import_export_buttons' ) );

		// process bulk geocoding from Pickup Locations edit screen bulk action
		add_action( 'load-edit.php', array( $this, 'process_bulk_geocoding' ) );
	}


	/**
	 * Filter the pickup location admin column keys.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $columns array of admin columns
	 * @return array
	 */
	public function customize_columns( array $columns ) {

		unset( $columns['date'] );

		if ( wc_local_pickup_plus()->geocoding_enabled() ) {

			$geocoding_status = array( 'geocoded_status' => '<span title="' . __( 'Geocoded Status', 'woocommerce-shipping-local-pickup-plus' ) .'" class="dashicons dashicons-admin-site"></span>' );

			$columns = Framework\SV_WC_Helper::array_insert_after( $columns, 'cb', $geocoding_status );
		}

		$columns['address']  = __( 'Address', 'woocommerce-shipping-local-pickup-plus' );
		$columns['city']     = __( 'City', 'woocommerce-shipping-local-pickup-plus' );
		$columns['postcode'] = __( 'Postcode', 'woocommerce-shipping-local-pickup-plus' );
		$columns['state']    = __( 'State', 'woocommerce-shipping-local-pickup-plus' );
		$columns['country']  = __( 'Country', 'woocommerce-shipping-local-pickup-plus' );

		return $columns;
	}


	/**
	 * Filter pickup locations sortable columns in admin table.
	 *
	 * @since 2.0.0
	 *
	 * @param array $columns sortable columns as associative array
	 * @return array
	 */
	public function customize_sortable_columns( array $columns ) {

		$columns['city']     = 'city';
		$columns['postcode'] = 'postcode';
		$columns['state']    = 'state';
		$columns['country']  = 'country';

		return $columns;
	}


	/**
	 * Filter the pickup location admin column content.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $column the column being displayed
	 * @param int $post_id the \WP_Post ID
	 */
	public function custom_column_content( $column, $post_id ) {

		$pickup_location = new \WC_Local_Pickup_Plus_Pickup_Location( $post_id );

		switch ( $column ) {

			case 'address':

				$address = $pickup_location->get_address()->get_street_address( 'string' );

				if ( ! empty( $address ) ) {
					echo $address;
				} else {
					echo '&ndash;';
				}

			break;

			case 'city':
			case 'postcode':

				$address = $pickup_location->get_address();
				$method  = 'get_' . $column;
				$piece   = method_exists( $address, $method ) ? $address->$method() : '';

				echo empty( $piece ) ? '&ndash;' : $piece;

			break;

			case 'country':
			case 'state':

				$name = '';

				if ( 'country' === $column ) {
					$name = $pickup_location->get_address()->get_country_name();
				} elseif ( 'state' === $column ) {
					$name = $pickup_location->get_address()->get_state_name();
				}

				echo empty( $name ) ? '&ndash;' : esc_html( $name );

			break;

			case 'geocoded_status' :

				if ( $pickup_location->has_coordinates() ) {
					echo '<span title="' . __( 'This pickup location has coordinates.', 'woocommerce-shipping-local-pickup-plus' ) .'" class="geocoded-status-dot has-coordinates"></span>';
				} else {
					echo '<span title="' . __( 'No coordinates have been set for this pickup location.', 'woocommerce-shipping-local-pickup-plus' ) .'" class="geocoded-status-dot no-coordinates"></span>';
				}

			break;

		}
	}


	/**
	 * Filter pickup locations row actions.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $actions associative array of actions and labels
	 * @param \WP_Post $post the post object
	 * @return array
	 */
	public function customize_row_actions( $actions, $post ) {

		if ( 'wc_pickup_location' === get_post_type( $post ) ) {

			unset( $actions['inline hide-if-no-js'], $actions['trash'] );

			if ( current_user_can( 'delete_post', $post->ID ) ) {
				$actions['delete'] = "<a class='submitdelete delete-pickup-location' title='" . esc_attr__( 'Delete this pickup location permanently', 'woocommerce-shipping-local-pickup-plus' ) . "' href='" . esc_url( get_delete_post_link( $post->ID, '', true ) ) . "'>" . esc_html__( 'Delete', 'woocommerce-shipping-local-pickup-plus' ) . "</a>";
			}
		}

		return $actions;
	}


	/**
	 * Filter pickup locations bulk actions.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $actions associative array
	 * @return array
	 */
	public function customize_bulk_actions( $actions ) {

		unset( $actions['trash'], $actions['edit'] );

		$actions['export']      = __( 'Export', 'woocommerce-shipping-local-pickup-plus' );

		if ( wc_local_pickup_plus()->geocoding_enabled() ) {
			$actions['geocode'] = __( 'Geocode', 'woocommerce-shipping-local-pickup-plus' );
		}

		$actions['delete']      = __( 'Delete', 'woocommerce-shipping-local-pickup-plus' );

		return $actions;
	}


	/**
	 * Changes the default text of the "Enter title here" for the Pickup Location post type.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $enter_title_here default text
	 * @return string
	 */
	public function pickup_location_title_placeholder( $enter_title_here ) {
		global $post_type;

		if ( 'wc_pickup_location' === $post_type ) {
			return __( 'Enter pickup location name here', 'woocommerce-shipping-local-pickup-plus' );
		}

		return $enter_title_here;
	}


	/**
	 * Add a permanent delete action link in the pickup location publish box.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post the current pickup location post
	 */
	public function add_delete_pickup_location_action_link( $post ) {

		if ( 'wc_pickup_location' === get_post_type( $post ) ) {

			echo '<div id="delete-pickup-location" class="misc-pub-section"> <span class="dashicons dashicons-trash"></span> <a class="submitdelete deletion delete-pickup-location" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . esc_html__( 'Permanently Delete', 'wooocommerce-shipping-local-pickup-plus' ) . '</a></div>';
		}
	}


	/**
	 * Add Pickup Locations to the sections of the WooCommerce Shipping settings tab.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $sections associative array of section keys and names
	 * @return array
	 */
	public function add_pickup_locations_section( array $sections ) {

		$sections['pickup_locations'] = __( 'Pickup Locations', 'woocommerce-shipping-local-pickup-plus' );

		return $sections;
	}


	/**
	 * Load the Pickup Locations edit screen from the corresponding WooCommerce Shipping section.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function load_pickup_locations_edit_screen() {
		global $current_screen;

		if (    isset( $current_screen->id, $_GET['tab'], $_GET['section'] )
		     && Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc-settings' ) === $current_screen->id
		     && 'pickup_locations' === $_GET['section'] ) {

			wp_safe_redirect( admin_url( 'edit.php?post_type=wc_pickup_location' ) );
			exit;
		}
	}


	/**
	 * Render WooCommerce core settings tabs while in Pickup Locations edit screens.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function output_woocommerce_settings_tabs_html() {
		global $typenow;

		if ( 'wc_pickup_location' === $typenow ) {
			wc_local_pickup_plus()->get_admin_instance()->output_woocommerce_tabs_html();
		}
	}


	/**
	 * Add custom navigation buttons before and after the posts table.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_import_export_buttons() {
		global $current_screen;

		if ( $current_screen && 'edit-wc_pickup_location' === $current_screen->id ) {
			add_action( 'all_admin_notices', array( $this, 'output_import_export_buttons_html' ) );
		}
	}


	/**
	 * Output custom navigation buttons before and after the posts table.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function output_import_export_buttons_html() {

		?>
		<div id="wc-local-pickup-plus-edit-pickup-locations-import-export" style="display: none;">
			<a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=wc_local_pickup_plus_import' ); ?>"><?php esc_html_e( 'Import Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ) ?></a>
			<a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=wc_local_pickup_plus_export' ); ?>"><?php esc_html_e( 'Export Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ) ?></a>
		</div>
		<?php

		// moves the additional navigation buttons to the table header and displays it
		wc_enqueue_js( "
			jQuery( document ).ready( function( $ ) {
				nav = $( '#wc-local-pickup-plus-edit-pickup-locations-import-export' );
				nav.insertAfter( '.tablenav.top > .actions' );
				nav.show();
			} ); 
		" );
	}


	/**
	 * Geocode pickup locations in bulk.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function process_bulk_geocoding() {

		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		if ( 'geocode' === $action ) {

			$local_pickup_plus = wc_local_pickup_plus();

			if ( ! current_user_can( 'manage_woocommerce' ) ) {

				wp_die( __( 'You are not allowed to perform this action.', 'woocommerce-shipping-local-pickup-plus' ) );

			} elseif ( $local_pickup_plus->geocoding_enabled() ) {

				$geocode  = $local_pickup_plus->get_geocoding_api_instance();
				$post_ids = isset( $_GET['post'] ) ? array_map( 'absint', $_GET['post'] ) : array();
				$requests = 0;
				$success  = 0;
				$failed   = 0;
				$start    = microtime( true );

				foreach ( $post_ids as $post_id ) {

					$post = get_post( $post_id );

					if ( 'wc_pickup_location' === $post->post_type ) {

						$pickup_location = wc_local_pickup_plus_get_pickup_location( $post );
						$address_array   = $pickup_location ? $pickup_location->get_address()->get_array() : '';
						$coordinates     = $geocode->get_coordinates( $address_array );

						if ( isset( $coordinates['lat'], $coordinates['lon'] ) ) {
							$pickup_location->set_coordinates( $coordinates['lat'],
								$coordinates['lon'] );
							$success ++;
						} else {
							$failed ++;
						}

						$requests++;
					}

					// sanity check for Google Maps Geocoding API: Google asks to limit to 50 the number of requests per second
					if ( 49 === $requests ) {

						$elapsed = ( microtime( true ) - $start );

						// if we are approaching 50 requests / second, sleep for around the same time
						if ( $elapsed >= 0.8 ) {
							sleep( 1 );
							$requests = 0;
						}
					}
				}

				$messages = $local_pickup_plus->get_message_handler();

				if ( $success > 0 ) {
					/* translators: Placeholder: %d number of pickup locations processed */
					$messages->add_message( sprintf( _n( 'Successfully geocoded %d pickup location address.', 'Successfully geocoded %d pickup location addresses.', $success ), $success ) );
				} elseif ( 0 === $success ) {
					$messages->add_error( __( 'No pickup location addresses were geocoded.', 'woocommerce-shipping-local-pickup-plus' ) );
				}

				if ( $failed > 0 ) {
					/* translators: Placeholder: %d number of pickup locations processed */
					$messages->add_error( sprintf( _n( 'Coordinates for %d pickup location could not be determined.', 'Coordinates for %d pickup locations could not be determined.', $failed ), $failed ) );
				}
			}
		}
	}


	/**
	 * Filter \WP_Query vars to make pickup location edit screen columns sortable.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_vars
	 * @return array
	 */
	public function sort_columns( $query_vars ) {
		global $current_screen;

		if (    isset( $current_screen->id )
		     && 'edit-wc_pickup_location' === $current_screen->id
		     && ! empty( $query_vars['orderby'] )
		     && in_array( $query_vars['orderby'], array( 'city', 'state', 'country', 'postcode' ), true ) ) {

			$query_vars = array_merge( $query_vars, array(
				'orderby'  => 'meta_value',
				'meta_key' => "_pickup_location_address_{$query_vars['orderby']}",
			) );
		}

		return $query_vars;
	}


	/**
	 * Check if the user is filtering pickup location posts by keyword search.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function is_search() {
		global $pagenow;

		return isset( $_GET['post_type'], $_GET['s'] )
		       && 'edit.php' === $pagenow
		       && 'wc_pickup_location' === $_GET['post_type']
		       && '' !== $_GET['s'];
	}


	/**
	 * Filter posts where query.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $where WHERE query
	 * @return string MySQL query piece
	 */
	public function filter_posts_where( $where ) {
		global $wpdb;

		if ( $this->is_search() ) {

			$keyword   = $_GET['s'];
			$pieces    = '';
			$meta_keys = array(
				'_pickup_location_phone',
				'_pickup_location_address_country',
				'_pickup_location_address_state',
				'_pickup_location_address_city',
				'_pickup_location_address_postcode',
				'_pickup_location_address_address_1',
				'_pickup_location_address_address_2',
			);

			foreach ( $meta_keys as $meta_key ) {
				$pieces .= " OR (({$wpdb->postmeta}.meta_key = '{$meta_key}') AND ({$wpdb->postmeta}.meta_value LIKE '%{$keyword}%'))";
			}

			$search  = "({$wpdb->posts}.post_title LIKE '%{$keyword}%')";
			$replace = "({$search} {$pieces})";
			$match   = preg_replace( $search, $replace, $where );

			$where   = null !== $match ? $match : $where;
		}

		return $where;
	}


	/**
	 * Filter posts join query.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $join JOIN query
	 * @return string MySQL query piece
	 */
	public function filter_posts_join( $join ) {
		global $wpdb;

		if ( $this->is_search() ) {
			$join = "LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ";
		}

		return $join;
	}


	/**
	 * Filter group by posts to avoid filter results duplicates.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $groupby GROUPBY query
	 * @return string MySQL query piece
	 */
	public function filter_groupby( $groupby ) {
		global $wpdb;

		if ( $this->is_search() ) {
			$groupby = "{$wpdb->posts}.ID";
		}

		return $groupby;
	}


}
