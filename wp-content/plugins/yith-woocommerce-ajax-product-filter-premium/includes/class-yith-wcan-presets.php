<?php
/**
 * Manage presets, register CPT and offers utility to retrieve them
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Presets
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Presets' ) ) {
	/**
	 * Filter Presets Handling
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Presets {

		/**
		 * Define how many unpaged filters should be shown on preset edit page
		 *
		 * @const int
		 */
		const FILTERS_PER_PAGE = 10;

		/**
		 * Presets post type
		 *
		 * @var string $post_type
		 */
		protected $post_type = 'yith_wcan_preset';

		/**
		 * Single instance of this class
		 *
		 * @var YITH_WCAN_Presets
		 */
		protected static $instance;

		/**
		 * Constructor method for this class
		 *
		 * @return void
		 */
		public function __construct() {
			// register post type.
			add_action( 'init', array( $this, 'register_post_type' ) );

			// register data store.
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );

			// set new preset url.
			add_filter( 'yith_plugin_fw_add_new_post_url', array( $this, 'add_new_preset_url' ), 10, 2 );

			// show upgrade note.
			add_action( 'yith_wcan_before_presets_table', array( $this, 'show_upgrade_note_anchor' ) );
			add_action( 'yith_wcan_after_presets_table', array( $this, 'show_upgrade_note_modal' ) );

			// admin actions.
			add_action( 'admin_action_yith_wcan_save_preset', array( $this, 'save_preset' ) );
			add_action( 'admin_action_yith_wcan_clone_preset', array( $this, 'clone_preset' ) );
			add_action( 'admin_action_yith_wcan_delete_preset', array( $this, 'delete_preset' ) );
			add_action( 'admin_action_yith_wcan_hide_upgrade_note', array( $this, 'hide_upgrade_note' ) );
			add_action( 'admin_action_yith_wcan_do_widget_upgrade', array( $this, 'do_widget_upgrade' ) );

			// ajax actions.
			add_action( 'wp_ajax_yith_wcan_load_more_filters', array( $this, 'load_more_filters' ) );
			add_action( 'wp_ajax_yith_wcan_change_preset_status', array( $this, 'change_preset_status' ) );
			add_action( 'wp_ajax_yith_wcan_save_preset_filter', array( $this, 'save_preset_filter' ) );
			add_action( 'wp_ajax_yith_wcan_delete_preset_filter', array( $this, 'delete_preset_filter' ) );
		}

		/**
		 * Return post type slug
		 *
		 * @return string
		 */
		public function get_post_type() {
			return apply_filters( 'yith_wcan_presets_post_type', $this->post_type );
		}

		/**
		 * Register post type for presets
		 *
		 * @return void
		 */
		public function register_post_type() {
			$post_type_labels = array(
				'name'          => _x( 'Filter presets', '[Admin] name of presets custom post type', 'yith-woocommerce-ajax-navigation' ),
				'singular_name' => _x( 'Filter preset', '[Admin] singular name of presets custom post type', 'yith-woocommerce-ajax-navigation' ),
				'add_new_item ' => _x( 'Add new preset', '[Admin] add new filter preset label', 'yith-woocommerce-ajax-navigation' ),
			);
			$post_type_args   = array(
				'label'        => _x( 'Filter presets', '[Admin] name of presets custom post type', 'yith-woocommerce-ajax-navigation' ),
				'labels'       => $post_type_labels,
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'supports'     => array( 'title' ),
			);

			register_post_type( $this->get_post_type(), $post_type_args );
		}

		/**
		 * Register preset Data Store in the list of available data stores
		 *
		 * @param array $data_stores Array of available data stores.
		 *
		 * @return array Filtered array of data stores.
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['filter_preset']  = 'YITH_WCAN_Preset_Data_Store';
			$data_stores['filter_session'] = 'YITH_WCAN_Session_Data_Store';

			return $data_stores;
		}

		/* === PRESET PANEL CUSTOMIZATIONS === */

		/**
		 * Prints upgrade note anchor on preset panel
		 *
		 * @return void
		 */
		public function show_upgrade_note_anchor() {
			if ( ! get_option( 'yith_wcan_upgrade_note_status', 0 ) ) {
				return;
			}

			echo wp_kses_post( sprintf( '<a id="yith_wcan_update_to_presets" href="#">%s</a>', _x( 'Convert widgets in a preset', '[ADMIN] Convert widgets tools, in preset tab', 'yith-woocommerce-ajax-navigation' ) ) );
		}

		/**
		 * Prints upgrade note modal on preset panel
		 *
		 * @return void
		 */
		public function show_upgrade_note_modal() {
			if ( ! get_option( 'yith_wcan_upgrade_note_status', 0 ) ) {
				return;
			}

			// external urls.
			$demo_video_url = ''; // TODO: enter url when video is ready.
			$doc_url        = 'https://docs.yithemes.com/yith-woocommerce-ajax-product-filter/premium-settings/general-settings/update-yith-woocommerce-ajax-product-filter-to-version-4-0/';

			// action urls.
			$hide_upgrade_note_url = wp_nonce_url( add_query_arg( 'action', 'yith_wcan_hide_upgrade_note', admin_url( 'admin.php' ) ), 'hide_upgrade_note' );
			$do_widget_upgrade_url = wp_nonce_url( add_query_arg( 'action', 'yith_wcan_do_widget_upgrade', admin_url( 'admin.php' ) ), 'do_widget_upgrade' );

			include YITH_WCAN_DIR . 'templates/admin/upgrade-note-modal.php';
		}

		/**
		 * Filters url to New Preset page, to return custom plugin's one
		 *
		 * @param string $url Url to new post page.
		 * @param array  $params Array of params.
		 *
		 * @return string Filtered url.
		 */
		public function add_new_preset_url( $url, $params ) {
			if ( ! isset( $params['post_type'] ) || $this->get_post_type() !== $params['post_type'] ) {
				return $url;
			}

			return YITH_WCAN()->admin->get_panel_url(
				'filter-preset',
				array(
					'action' => 'create',
				)
			);
		}

		/* === ADMIN ACTIONS === */

		/**
		 * Save preset, crating a new post or updating an existing one
		 * Redirects then to preset edit page
		 *
		 * @return void
		 */
		public function save_preset() {
			$preset_id  = isset( $_POST['id'] ) ? (int) $_POST['id'] : false;
			$paged      = isset( $_POST['paged'] ) ? (int) $_POST['paged'] : false;
			$return_url = YITH_WCAN()->admin->get_panel_url();

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_preset' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			// retrieve preset, or create a blank one for saving purpose.
			try {
				$preset = new YITH_WCAN_Preset( $preset_id );
			} catch ( Exception $e ) {
				wp_safe_redirect( $return_url );
				die;
			}

			// retrieve preset title and save it.
			$title = isset( $_POST['preset_title'] ) ? wc_clean( wp_unslash( $_POST['preset_title'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			if ( $title ) {
				$preset->set_title( $title );
			}

			// retrieve preset layout and save it.
			$layout = isset( $_POST['preset_layout'] ) && in_array( $_POST['preset_layout'], array_keys( YITH_WCAN_Preset_Factory::get_supported_layouts() ), true ) ? sanitize_text_field( wp_unslash( $_POST['preset_layout'] ) ) : 'default';
			$preset->set_layout( $layout );

			// process filters and save them.
			if ( ! empty( $_POST['filters'] ) ) {
				$filters = $_POST['filters']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				$to_save = array();

				foreach ( $filters as $filter ) {
					$cleaned_filter = $this->clear_preset_filter( $filter );

					if ( ! $cleaned_filter ) {
						continue;
					}

					$to_save[] = $cleaned_filter;
				}

				if ( ! empty( $to_save ) ) {
					$preset->set_filters( $to_save, $paged );
				}
			}

			// save the preset.
			$preset_id = $preset->save();

			if ( $preset_id ) {
				// fires after preset saving procedure.
				do_action( 'yith_wcan_save_preset', $preset_id, $preset );
			}

			// redirect to edit page.
			$return_url = YITH_WCAN()->admin->get_panel_url(
				'filter-preset',
				array(
					'action' => 'edit',
					'preset' => $preset_id,
					'status' => 'success',
				)
			);

			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Clone preset when requested by admin
		 * Redirects then to Presets list
		 *
		 * @return void
		 */
		public function clone_preset() {
			$preset     = isset( $_GET['preset'] ) ? (int) $_GET['preset'] : false;
			$return_url = YITH_WCAN()->admin->get_panel_url();

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( ! $preset || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'clone_preset' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			$preset = YITH_WCAN_Preset_Factory::get_preset( $preset );

			if ( ! $preset || ! $preset->current_user_can( 'clone' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			try {
				WC_Data_Store::load( 'filter_preset' )->clone( $preset );
			} catch ( Exception $e ) {
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
			}

			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Delete preset when requested by admin
		 * Redirects then to Presets list
		 *
		 * @return void
		 */
		public function delete_preset() {
			$preset     = isset( $_GET['preset'] ) ? (int) $_GET['preset'] : false;
			$return_url = YITH_WCAN()->admin->get_panel_url();

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( ! $preset || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'delete_preset' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			$preset = YITH_WCAN_Preset_Factory::get_preset( $preset );

			if ( ! $preset || ! $preset->current_user_can( 'delete' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			try {
				WC_Data_Store::load( 'filter_preset' )->delete( $preset );
			} catch ( Exception $e ) {
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
			}

			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Hide upgrade note when user dismiss the modal
		 *
		 * @return void
		 */
		public function hide_upgrade_note() {
			$return_url = YITH_WCAN()->admin->get_panel_url();

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'hide_upgrade_note' ) ) {
				update_option( 'yith_wcan_upgrade_note_status', 0 );
			}

			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Create preset starting from sidebar's widget
		 *
		 * @return void
		 */
		public function do_widget_upgrade() {
			global $wp_registered_sidebars;

			$return_url = YITH_WCAN()->admin->get_panel_url();

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'do_widget_upgrade' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			$sidebars = yith_wcan_get_sidebar_with_filters();
			$options  = array();

			if ( ! empty( $sidebars ) ) {
				foreach ( $sidebars as $sidebar_id => $widgets ) {
					$sidebar = isset( $wp_registered_sidebars[ $sidebar_id ] ) ? $wp_registered_sidebars[ $sidebar_id ] : false;

					if ( ! $sidebar || empty( $widgets ) ) {
						continue;
					}

					// create new preset.
					$preset  = new YITH_WCAN_Preset();
					$filters = array();

					// set preset basics.
					$preset->set_title( $sidebar['name'] );
					$preset->set_slug( $sidebar['id'] );

					foreach ( $widgets as $widget ) {
						// if widgets isn't a filter instance, continue.
						if ( ! yith_wcan_is_filter_widget( $widget ) ) {
							continue;
						}

						preg_match( '/^(.+)-([0-9]+)$/', $widget, $matches );

						if ( empty( $matches ) ) {
							continue;
						}

						list( $widget, $widget_name, $widget_id ) = $matches;

						if ( ! isset( $options[ $widget_name ] ) ) {
							$options[ $widget_name ] = get_option( "widget_{$widget_name}", array() );
						}

						if ( ! isset( $options[ $widget_name ][ $widget_id ] ) ) {
							continue;
						}

						$widget_instance = $options[ $widget_name ][ $widget_id ];
						$filter          = false;

						switch ( $widget_name ) {
							case 'yith-woo-ajax-navigation-list-price-filter':
								if ( ! class_exists( 'YITH_WCAN_Filter_Price_Range' ) || empty( $widget_instance['prices'] ) ) {
									continue 2;
								}

								$filter = new YITH_WCAN_Filter_Price_Range();

								$filter->set_price_ranges( $widget_instance['prices'] );

								break;
							case 'yith-woo-ajax-navigation-stock-on-sale':
								if ( ! class_exists( 'YITH_WCAN_Filter_Stock_Sale' ) || ( empty( $widget_instance['onsale'] ) && empty( $widget_instance['instock'] ) ) ) {
									continue 2;
								}

								$filter = new YITH_WCAN_Filter_Stock_Sale();

								$filter->set_show_sale_filter( $widget_instance['onsale'] );
								$filter->set_show_sale_filter( $widget_instance['instock'] );

								break;
							case 'yith-woo-ajax-navigation-sort-by':
								if ( ! class_exists( 'YITH_WCAN_Filter_Orderby' ) ) {
									continue 2;
								}

								$filter = new YITH_WCAN_Filter_Orderby();

								$filter->set_order_options( array_keys( YITH_WCAN_Filter_Factory::get_supported_orders() ) );

								break;
							case 'yith-woo-ajax-navigation':
								$filter = new YITH_WCAN_Filter_Tax();

								// set taxonomy.
								if ( 'categories' === $widget_instance['type'] ) {
									$taxonomy = 'product_cat';
								} elseif ( 'tags' === $widget_instance['type'] ) {
									$taxonomy = 'product_tag';
								} else {
									$taxonomy = 'pa_' . $widget_instance['attribute'];
								}

								$filter->set_taxonomy( $taxonomy );
								$filter->set_multiple( true );

								// set terms.
								$terms_array = array();

								$terms = get_terms(
									array(
										'taxonomy'   => $filter->get_taxonomy(),
										'hide_empty' => true,
										'number'     => apply_filters( 'yith_wcan_upgrade_terms_limit', 0 ),
									)
								);

								if ( empty( $terms ) ) {
									continue 2;
								}

								foreach ( $terms as $term ) {
									$terms_array[ $term->term_id ] = array(
										'label'   => $term->name,
										'tooltip' => $term->name,
									);
								}

								switch ( $widget_instance['type'] ) {
									case 'color':
									case 'multicolor':
										$option_name  = 'color' === $widget_instance['type'] ? 'colors' : 'multicolor';
										$option_value = $widget_instance[ $option_name ];

										foreach ( $terms_array as $term_id => $term_options ) {
											if ( ! array_key_exists( $term_id, $option_value ) ) {
												unset( $terms_array[ $term_id ] );
											}

											if ( empty( $option_value[ $term_id ] ) || is_array( $option_value[ $term_id ] ) && empty( $option_value[ $term_id ][0] ) ) {
												unset( $terms_array[ $term_id ] );
											} elseif ( is_array( $option_value[ $term_id ] ) ) {
												$terms_array[ $term_id ]['color_1'] = $option_value[ $term_id ][0];
												$terms_array[ $term_id ]['color_2'] = ! empty( $option_value[ $term_id ][1] ) ? $option_value[ $term_id ][1] : $option_value[ $term_id ][0];
											} else {
												$terms_array[ $term_id ]['color_1'] = $option_value[ $term_id ];
											}
										}
										break;
									case 'label':
										$option_value = $widget_instance['labels'];

										foreach ( $terms_array as $term_id => $term_options ) {
											if ( ! array_key_exists( $term_id, $option_value ) ) {
												unset( $terms_array[ $term_id ] );
											}

											if ( empty( $option_value[ $term_id ] ) ) {
												unset( $terms_array[ $term_id ] );
											} else {
												$terms_array[ $term_id ]['label'] = $option_value[ $term_id ];
											}
										}
										break;
									case 'tags':
										$option_value = array_map( 'intval', array_keys( $widget_instance['tags_list'] ) );
										$exclude      = 'exclude' === $widget_instance['tags_list_query'];
										$include      = ! $exclude;

										foreach ( $terms_array as $term_id => $term_options ) {
											// if exclude and term exists in the option, or include and term doesn't exist in the option, unset it.
											$found = in_array( $term_id, $option_value, true );

											if ( $include && ! $found || $exclude && $found ) {
												unset( $terms_array[ $term_id ] );
											}
										}
										break;
									case 'categories':
									case 'list':
									case 'select':
									default:
										// do nothing, terms are already configured.
										break;
								}

								$filter->set_terms( $terms_array );

								// set design.
								$design = $widget_instance['type'];

								if ( 'categories' === $design || 'tags' === $design || 'list' === $design ) {
									$design = 'text';
								} elseif ( 'multicolor' === $design ) {
									$design = 'color';
								}

								$filter->set_filter_design( $design );

								// set relation.
								$filter->set_relation( $widget_instance['query_type'] );

								// set hierarchical.
								if ( 'all' === $widget_instance['display'] ) {
									$hierarchical = 'no';
								} elseif ( 'hierarchical' === $widget_instance['display'] ) {
									$hierarchical = 'expanded';
								} else {
									$hierarchical = 'parents_only';
								}

								$filter->set_hierarchical( $hierarchical );
								break;
							default:
								continue 2;
						}

						if ( $filter ) {
							$filter->set_title( $widget_instance['title'] );
							$filter->set_show_count( isset( $widget_instance['show_count'] ) && ! $widget_instance['show_count'] && ! in_array( $widget_instance['type'], array( 'color', 'multicolor', 'label' ), true ) );

							if ( ! empty( $widget_instance['dropdown'] ) ) {
								$filter->set_show_toggle( 'yes' );
								$filter->set_toggle_style( 'open' === $widget_instance['dropdown_type'] ? 'opened' : 'closed' );
							}

							$filters[] = $filter->get_data();
						}
					}

					$preset->set_filters( $filters );

					// if any filter was set, save the preset.
					if ( $preset->has_filters() ) {
						$preset->save();
					}
				}
			}

			// we're done, hide upgrade note.
			update_option( 'yith_wcan_upgrade_note_status', 0 );

			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Clear data contained in the filter, to make sure everything is fine
		 *
		 * @param array $filter Data from request.
		 *
		 * @return array Cleared data.
		 */
		protected function clear_preset_filter( $filter ) {
			// set missing information.
			$filter['enabled']              = isset( $filter['enabled'] ) ? 'yes' : 'no';
			$filter['show_toggle']          = isset( $filter['show_toggle'] ) ? 'yes' : 'no';
			$filter['show_count']           = isset( $filter['show_count'] ) ? 'yes' : 'no';
			$filter['show_search']          = isset( $filter['show_search'] ) ? 'yes' : 'no';
			$filter['multiple']             = isset( $filter['multiple'] ) ? 'yes' : 'no';
			$filter['show_stock_filter']    = isset( $filter['show_stock_filter'] ) ? 'yes' : 'no';
			$filter['show_sale_filter']     = isset( $filter['show_sale_filter'] ) ? 'yes' : 'no';
			$filter['show_featured_filter'] = isset( $filter['show_featured_filter'] ) ? 'yes' : 'no';
			$filter['customize_terms']      = isset( $filter['customize_terms'] ) ? 'yes' : 'no';

			if ( isset( $filter['terms_order'] ) ) {
				$filter['terms'] = $this->get_sorted_terms( $filter['terms'], array_map( 'intval', $filter['terms_order'] ) );
				unset( $filter['terms_order'] );
			}

			// address the case in which we're not using custom term options.
			if ( empty( $filter['terms'] ) && ! empty( $filter['term_ids'] ) ) {
				$filter['terms'] = $filter['term_ids'];
			}

			// use set methods of YITH_WCAN_Filter object to clean submitted data.
			$filter_obj = yith_wcan_get_filter( $filter );

			return $filter_obj->get_data();
		}

		/**
		 * Returns array of sorted terms, depending on the order passed as second argument
		 *
		 * @param array $terms Array of terms.
		 * @param array $order Desired order for the terms.
		 *
		 * @return array Array of sorted terms
		 */
		protected function get_sorted_terms( $terms, $order ) {
			$ordered_terms = array();

			foreach ( $order as $term_id ) {
				if ( ! isset( $terms[ $term_id ] ) ) {
					continue;
				}

				$ordered_terms[ $term_id ] = $terms[ $term_id ];
			}

			return $ordered_terms;
		}

		/* === AJAX ACTIONS === */

		/**
		 * Load more filters for a specific preset
		 *
		 * @return void
		 */
		public function load_more_filters() {
			check_ajax_referer( 'load_more_filters' );

			$preset = isset( $_GET['preset'] ) ? (int) $_GET['preset'] : false;
			$page   = isset( $_GET['page'] ) ? (int) $_GET['page'] : false;

			if ( ! $preset ) {
				die( '-1' );
			}

			$preset = YITH_WCAN_Preset_Factory::get_preset( $preset );

			if ( ! $preset || ! $preset->current_user_can( 'load_filters' ) ) {
				die( '-1' );
			}

			$filters  = $preset->get_raw_filters( 'edit', $page );
			$has_more = $page < $preset->get_pages();

			wp_send_json(
				array(
					'filters'  => $filters,
					'has_more' => $has_more,
				)
			);

			die;
		}

		/**
		 * Change preset status when correct ajax call is invoked
		 *
		 * @return void
		 */
		public function change_preset_status() {
			check_ajax_referer( 'change_preset_status' );

			$preset = isset( $_POST['preset'] ) ? (int) $_POST['preset'] : false;
			$status = isset( $_POST['status'] ) ? (int) $_POST['status'] : false;

			if ( ! $preset ) {
				die( '-1' );
			}

			$preset = YITH_WCAN_Preset_Factory::get_preset( $preset );

			if ( ! $preset || ! $preset->current_user_can( 'change_status' ) ) {
				die( '-1' );
			}

			$method = $status ? 'enable' : 'disable';

			$preset->{$method}();
			$preset->save();

			die;
		}

		/**
		 * Save a single filter of the preset via Ajax
		 * If preset still doesn't exists, it will be automatically created
		 *
		 * @return void
		 */
		public function save_preset_filter() {
			check_ajax_referer( 'save_preset_filter' );

			$preset_id = isset( $_POST['preset'] ) ? (int) $_POST['preset'] : false;
			$filter_id = isset( $_POST['filter_id'] ) ? (int) $_POST['filter_id'] : false;
			$filter    = isset( $_POST['filter'] ) ? $this->clear_preset_filter( $_POST['filter'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			if ( ! $filter ) {
				die( '-1' );
			}

			// retrieve preset, or create a blank one for saving purpose.
			try {
				$preset = new YITH_WCAN_Preset( $preset_id );
			} catch ( Exception $e ) {
				die( '-1' );
			}

			$preset->set_filter( $filter_id, $filter );
			$preset->save();

			wp_send_json(
				array(
					'id' => $preset->get_id(),
				)
			);
		}

		/**
		 * Delete a single filter of the preset via Ajax
		 *
		 * @return void
		 */
		public function delete_preset_filter() {
			check_ajax_referer( 'delete_preset_filter' );

			$preset_id = isset( $_POST['preset'] ) ? (int) $_POST['preset'] : false;
			$filter_id = isset( $_POST['filter_id'] ) ? (int) $_POST['filter_id'] : false;

			if ( ! $preset_id ) {
				die( '-1' );
			}

			// retrieve preset, or create a blank one for saving purpose.
			try {
				$preset = new YITH_WCAN_Preset( $preset_id );
			} catch ( Exception $e ) {
				die( '-1' );
			}

			if ( $preset->has_filter( $filter_id ) ) {
				$preset->delete_filter( $filter_id );
			}

			$preset->save();

			wp_send_json(
				array(
					'id' => $preset->get_id(),
				)
			);
		}

		/**
		 * Return single instance for this class
		 *
		 * @return YITH_WCAN_Presets
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

if ( ! function_exists( 'YITH_WCAN_Presets' ) ) {
	/**
	 * Return single instance for YITH_WCAN_Presets class
	 *
	 * @return YITH_WCAN_Presets
	 * @since 4.0.0
	 */
	function YITH_WCAN_Presets() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		return YITH_WCAN_Presets::instance();
	}
}
