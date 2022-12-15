<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'trait-warranty-util.php';

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use WooCommerce\Warranty\Warranty_Util;

if ( ! class_exists( 'Warranty_Admin' ) ) :
	/**
	 * Warranty_Admin class
	 */
	class Warranty_Admin {

		use Warranty_Util;

		/**
		 * Number of columns
		 *
		 * @var int
		 */
		public static $shop_order_columns = 1;

		/**
		 * Register the hooks
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'add_notices' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 11 );
			add_action( 'admin_footer', array( $this, 'variable_script' ) );

			// metaboxes.
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_warranty' ) );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_warranty' ), 10, 2 );

			// order actions.
			add_filter( 'woocommerce_order_actions', array( $this, 'add_order_action' ) );
			add_action( 'woocommerce_order_action_generate_rma', array( $this, 'redirect_order_to_rma_form' ) );

			// variable products support.
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variables_panel' ), 10, 3 );

			// Update request from the admin.
			add_action( 'admin_post_warranty_create', array( $this, 'create_warranty' ) );
			add_action( 'admin_post_warranty_delete', array( $this, 'warranty_delete' ) );
			add_action( 'admin_post_warranty_print', array( $this, 'warranty_print' ) );

			add_action( 'admin_post_warranty_upload_shipping_label', array( $this, 'attach_shipping_label' ) );

			// return stock.
			add_action( 'admin_post_warranty_return_inventory', array( $this, 'return_inventory' ) );

			// refund order item.
			add_action( 'admin_post_warranty_refund_item', array( $this, 'refund_item' ) );

			// CSV Import.
			add_filter( 'woocommerce_csv_product_post_columns', array( $this, 'csv_import_fields' ) );

			// bulk edit.
			add_action( 'admin_post_warranty_bulk_edit', array( $this, 'bulk_edit' ) );

			// save settings.
			add_action( 'admin_post_wc_warranty_settings_update', array( $this, 'update_settings' ) );

			add_filter( 'manage_shop_order_posts_columns', array( $this, 'count_shop_order_columns' ), 1000 );
			add_action( 'woocommerce_admin_order_actions_end', array( $this, 'order_inline_edit_actions' ) );
			add_action( 'admin_footer', array( $this, 'order_inline_edit_template' ) );

			add_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'add_line_item_warranty_meta' ), 10, 2 );
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'maybe_render_addon_options' ), 10, 3 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_line_item_warranty_indices' ), 10, 2 );
			add_action( 'woocommerce_saved_order_items', array( $this, 'save_line_item_warranty_indices' ), 9, 2 );
			add_action( 'woocommerce_saved_order_items', array( $this, 'add_addon_price_to_line_item' ), 10, 2 );

			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Init
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'panel_data_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'panel_add_custom_box' ) );
		}

		/**
		 * Register the menu items
		 */
		public function admin_menu() {
			add_menu_page( esc_html__( 'Warranties', 'wc_warranty' ), esc_html__( 'Warranties', 'wc_warranty' ), 'manage_warranties', 'warranties', array( $this, 'admin_controller' ), 'dashicons-update', '54.52' );
			add_submenu_page( 'warranties', esc_html__( 'RMA Requests', 'wc_warranty' ), esc_html__( 'RMA Requests', 'wc_warranty' ), 'manage_warranties', 'warranties', array( $this, 'admin_controller' ) );
			add_submenu_page( 'warranties', esc_html__( 'New Request', 'wc_warranty' ), esc_html__( 'New Request', 'wc_warranty' ), 'manage_warranties', 'warranties-new', array( $this, 'admin_controller' ) );
			add_submenu_page( 'warranties', esc_html__( 'Manage Warranties', 'wc_warranty' ), esc_html__( 'Manage Warranties', 'wc_warranty' ), 'manage_woocommerce', 'warranties-bulk-update', array( $this, 'admin_controller' ) );
			add_submenu_page( 'warranties', esc_html__( 'Reports', 'wc_warranty' ), esc_html__( 'Reports', 'wc_warranty' ), 'manage_woocommerce', 'warranties-reports', array( $this, 'admin_controller' ) );
			add_submenu_page( 'warranties', esc_html__( 'Settings', 'wc_warranty' ), esc_html__( 'Settings', 'wc_warranty' ), 'manage_woocommerce', 'warranties-settings', array( $this, 'admin_controller' ) );

			if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Menu' ) ) {
				return;
			}

			Menu::add_plugin_category(
				array(
					'id'         => 'warranties-category',
					'title'      => esc_html__( 'Warranties', 'wc_warranty' ),
					'capability' => 'manage_warranties',
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties',
					'parent'     => 'warranties-category',
					'title'      => esc_html__( 'RMA Requests', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties',
					'capability' => 'manage_warranties',
					'order'      => 0,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-new',
					'parent'     => 'warranties-category',
					'title'      => esc_html__( 'New Request', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-new',
					'capability' => 'manage_warranties',
					'order'      => 1,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-bulk-update',
					'parent'     => 'warranties-category',
					'title'      => esc_html__( 'Manage Warranties', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-bulk-update',
					'capability' => 'manage_woocommerce',
					'order'      => 2,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-reports',
					'parent'     => 'warranties-category',
					'title'      => esc_html__( 'Reports', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-reports',
					'capability' => 'manage_woocommerce',
					'order'      => 3,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-settings',
					'parent'     => 'warranties-category',
					'title'      => esc_html__( 'Settings', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-settings',
					'capability' => 'manage_woocommerce',
					'order'      => 4,
				)
			);
		}

		/**
		 * Display notices in the admin panel
		 */
		public function add_notices() {

			$get_data = warranty_request_get_data();
			// shipping label attached.
			if ( ! empty( $get_data['post_type'] ) && 'shop_order' === $get_data['post_type'] && ! empty( $get_data['shipping_label_attached'] ) ) {
				echo '<div class="updated"><p>' . esc_html__( 'Shipping label attached', 'wc_warranty' ) . '</p></div>';
			}
		}

		/**
		 * Load scripts and styles selectively, depending on the current screen and page
		 */
		public function admin_scripts() {
			global $woocommerce;

			$pages = array(
				'warranties',
				'warranties-new',
				'warranties-bulk-update',
				'warranties-reports',
				'warranties-settings',
			);

			$get_data = warranty_request_get_data();
			if ( isset( $get_data['page'] ) && in_array( $get_data['page'], $pages, true ) ) {
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_style( 'select2' );
				wp_enqueue_script( 'wc-enhanced-select' );

				wp_enqueue_script( 'user-email-search', plugins_url( 'assets/js/user-email-search.js', WooCommerce_Warranty::$plugin_file ), array( 'wc-enhanced-select' ), WOOCOMMERCE_WARRANTY_VERSION );

				add_thickbox();
				wp_enqueue_media();

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC()->version, true );

				wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), WOOCOMMERCE_WARRANTY_VERSION );

				wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array( 'jquery' ), '2.70', true );
				wp_enqueue_script( 'jquery-tiptip' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-core', null, array( 'jquery' ) );

				$js = '
						jQuery(".warranty-delete").click(function(e) {
							return confirm("' . __( 'Do you really want to delete this request?', 'wc_warranty' ) . '");
						});
						var tiptip_args = {
							"attribute" : "data-tip",
							"fadeIn" : 50,
							"fadeOut" : 50,
							"delay" : 200
						};
						$(".tips, .help_tip").tipTip( tiptip_args );
					';

				if ( function_exists( 'wc_enqueue_js' ) ) {
					wc_enqueue_js( $js );
				} else {
					$woocommerce->add_inline_js( $js );
				}
			}

			// settings css.
			$pages[] = 'wc-settings';
			$pages[] = 'woocommerce_warranty';

			if ( isset( $get_data['page'] ) ) {
				if ( 'warranties' === $get_data['page'] ) {
					wp_enqueue_script( 'warranties_list', plugins_url( 'assets/js/list.js', WooCommerce_Warranty::$plugin_file ), array( 'jquery' ), WOOCOMMERCE_WARRANTY_VERSION, true );
				}

				if ( in_array( $get_data['page'], $pages, true ) ) {
					wp_enqueue_style( 'warranty_admin_css', plugins_url( 'assets/css/admin.css', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION );

					wp_enqueue_script( 'jquery-ui' );
					wp_enqueue_script( 'jquery-ui-sortable' );
					wp_enqueue_script( 'warranty_form_builder', plugins_url( 'assets/js/form-builder.js', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION, true );

					$data = array(
						'help_img_url' => plugins_url() . '/woocommerce/assets/images/help.png',
						'tips'         => array_map( 'wc_sanitize_tooltip', WooCommerce_Warranty::$tips ),
					);

					wp_localize_script( 'warranty_form_builder', 'WFB', $data );
				}
			}

			$screen = get_current_screen();

			if ( 'edit-shop_order' === $screen->id ) {
				add_thickbox();
				wp_enqueue_media();
				wp_enqueue_style( 'warranty_admin_css', plugins_url( 'assets/css/admin.css', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION );
				wp_enqueue_script( 'warranty_shop_order', plugins_url( 'assets/js/orders.js', WooCommerce_Warranty::$plugin_file ), array( 'jquery' ), WOOCOMMERCE_WARRANTY_VERSION, true );
			}

			wp_enqueue_style( 'wc-form-builder', plugins_url( 'assets/css/form-builder.css', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION );

			$js = '
					if ( jQuery( \'select.multi-select2\' ).length ) {
						jQuery( \'select.multi-select2\' ).selectWoo();
					}
			';

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( $js );
			} else {
				$woocommerce->add_inline_js( $js );
			}

			if ( $this->is_updater_view() ) {
				wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui-progressbar', false, array( 'jquery', 'jquery-ui' ) );
				wp_enqueue_script(
					'warranty_data_updater',
					plugins_url( '/assets/js/data-updater.js', WooCommerce_Warranty::$plugin_file ),
					array(
						'jquery',
						'jquery-ui-progressbar',
					),
					WOOCOMMERCE_WARRANTY_VERSION,
					true
				);
			}
		}

		/**
		 * Render JS on the footer that controls variable fields for variable products
		 */
		public function variable_script() {
			$screen   = get_current_screen();
			$currency = get_woocommerce_currency_symbol();
			if ( 'product' === $screen->id ) {
				?>
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						$( '.wc-metaboxes-wrapper' )
							.on( 'click', '.wc-metabox h3', function( event ) {
								$( 'select.variable-warranty-type' ).trigger( 'change.warranty' );
							} );
						var $variable_product_options = $( '#variable_product_options' );

						$variable_product_options.on( 'change.warranty', '.warranty_default_checkbox', function() {
							var id = $( this ).data( 'id' );

							if ( $( this ).is( ':checked' ) ) {
								$( '.warranty_' + id ).attr( 'disabled', true );
							} else {
								$( '.warranty_' + id ).attr( 'disabled', false );
							}
						} );

						$variable_product_options.on( 'change.warranty', '.variable-warranty-type', function() {
							var loop                      = $( this ).parents( '.warranty-variation' ).data( 'loop' ),
								show_if_included_warranty = $( '.variable_show_if_included_warranty_' + loop ),
								show_if_addon_warranty    = $( '.variable_show_if_addon_warranty_' + loop );

							show_if_included_warranty.hide();
							show_if_addon_warranty.hide();

							if ( 'included_warranty' === $( this ).val() ) {
								show_if_included_warranty.show();
							} else if ( 'addon_warranty' === $( this ).val() ) {
								show_if_addon_warranty.show();
							}
						} );

						$variable_product_options.on( 'change.warranty', '.variable-included-warranty-length', function() {
							var loop = $( this ).parents( '.warranty-variation' ).data( 'loop' );

							if ( 'limited' === $( this ).val() ) {
								$( '.variable_limited_warranty_length_field_' + loop ).show();
							} else {
								$( '.variable_limited_warranty_length_field_' + loop ).hide();
							}
						} );

						var variable_tmpl = "<tr>\
								<td valign=\"middle\">\
									<span class=\"input\"><b>+</b> <?php echo esc_html( $currency ); ?></span>\
									<input type=\"text\" name=\"variable_addon_warranty_amount[_loop_][]\" value=\"\" class=\"input-text sized warranty__loop_\" style=\"min-width:50px; width:50px;\" />\
								</td>\
								<td valign=\"middle\">\
									<input type=\"text\" class=\"input-text sized warranty__loop_\" style=\"width:50px;\" name=\"variable_addon_warranty_length_value[_loop_][]\" value=\"\" />\
									<select name=\"variable_addon_warranty_length_duration[_loop_][]\" class=\"warranty__loop_\" style=\"width: auto !important;\">\
										<option value=\"days\"><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>\
										<option value=\"weeks\"><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>\
										<option value=\"months\"><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>\
										<option value=\"years\"><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>\
									</select>\
								</td>\
								<td><a class=\"button warranty_addon_remove warranty_addon_remove_variable warranty__loop_\" href=\"#\">&times;</a></td>\
							</tr>";

						$variable_product_options.on( 'click', '.btn-add-warranty-variable', function( e ) {
							e.preventDefault();
							if ( $( this ).attr( 'disabled' ) ) {
								return;
							}
							var loop = $( this ).data( 'loop' );

							$( '#variable_warranty_addons_' + loop ).append( variable_tmpl.replace( /_loop_/g, loop ) );
						} );

						$variable_product_options.on( 'click', '.warranty_addon_remove', function( e ) {
							e.preventDefault();
							if ( $( this ).attr( 'disabled' ) ) {
								return;
							}
							$( this )
								.closest( '.warranty-variation' )
								.find( '.warranty_default_checkbox' )
								.trigger( 'change' );

							$( this ).parents( 'tr' ).eq( 0 ).remove();
						} );

						var $woocommerce_product_data = $( '#woocommerce-product-data' );
						$woocommerce_product_data.on( 'woocommerce_variations_loaded', function() {
							$( '.warranty_default_checkbox, .variable-warranty-type, .variable-warranty-length' ).trigger( 'change.warranty' );
						} );
						$woocommerce_product_data.on( 'woocommerce_variations_added', function() {
							$( '.warranty_default_checkbox, .variable-warranty-type, .variable-warranty-length' ).trigger( 'change.warranty' );
						} );
					} );
				</script>
				<?php
			}
		}

		/**
		 * Adds a 'Warranty' tab to a product's data tabs
		 */
		public function panel_data_tab() {
			echo ' <li class="warranty_tab tax_options hide_if_external"><a href="#warranty_product_data"><span>' . esc_html__( 'Warranty', 'woocommerce' ) . '</span></a></li>';
		}

		/**
		 * Outputs the form for the Warranty data tab
		 */
		public function panel_add_custom_box() {
			global $post;

			$warranty_type_value = get_post_meta( $post->ID, '_warranty_type', true );

			if ( empty( trim( $warranty_type_value ) ) ) {
				update_post_meta( $post->ID, '_warranty_type', 'no_warranty' );
				$warranty_type_value = 'no_warranty';
			}

			$warranty_duration_value = get_post_meta( $post->ID, '_warranty_duration', true );

			if ( empty( trim( $warranty_duration_value ) ) ) {
				update_post_meta( $post->ID, '_warranty_duration', 0 );
				$warranty_duration_value = 0;
			}

			$warranty_unit_value = get_post_meta( $post->ID, '_warranty_unit', true );

			if ( empty( trim( $warranty_unit_value ) ) ) {
				update_post_meta( $post->ID, '_warranty_unit', 'day' );
				$warranty_unit_value = 'day';
			}

			$currency = get_woocommerce_currency_symbol();
			$inline   = '
				var warranty_fields_toggled = false;
				$("#product_warranty_default").change(function() {

					if ($(this).is(":checked")) {
						$(".warranty_field").attr("disabled", true);
					} else {
						$(".warranty_field").attr("disabled", false);
					}

				}).change();

				$("#product_warranty_type").change(function() {
					$(".show_if_included_warranty, .show_if_addon_warranty").hide();

					if ($(this).val() == "included_warranty") {
						$(".show_if_included_warranty").show();
					} else if ($(this).val() == "addon_warranty") {
						$(".show_if_addon_warranty").show();
					}
				}).change();

				$("#included_warranty_length").change(function() {
					if ($(this).val() == "limited") {
						$(".limited_warranty_length_field").show();
					} else {
						$(".limited_warranty_length_field").hide();
					}
				}).change();

				var tmpl = "<tr>\
								<td valign=\"middle\">\
									<span class=\"input\"><b>+</b> ' . $currency . '</span>\
									<input type=\"text\" name=\"addon_warranty_amount[]\" class=\"input-text sized\" size=\"4\" value=\"\" />\
								</td>\
								<td valign=\"middle\">\
									<input type=\"text\" class=\"input-text sized\" size=\"3\" name=\"addon_warranty_length_value[]\" value=\"\" />\
									<select name=\"addon_warranty_length_duration[]\">\
										<option value=\"days\">' . __( 'Days', 'wc_warranty' ) . '</option>\
										<option value=\"weeks\">' . __( 'Weeks', 'wc_warranty' ) . '</option>\
										<option value=\"months\">' . __( 'Months', 'wc_warranty' ) . '</option>\
										<option value=\"years\">' . __( 'Years', 'wc_warranty' ) . '</option>\
									</select>\
								</td>\
								<td><a class=\"button warranty_addon_remove\" href=\"#\">&times;</a></td>\
							</tr>";

				$(".btn-add-warranty").click(function(e) {
					e.preventDefault();

					$("#warranty_addons").append(tmpl);
				});

				$("#warranty_addons").on("click", ".warranty_addon_remove", function(e) {
					e.preventDefault();

					$(this).parents("tr").remove();
				});

				$("#variable_warranty_control").change(function() {
					if ($(this).val() == "variations") {
						$(".hide_if_control_variations").hide();
						$(".show_if_control_variations").show();
					} else {
						$(".hide_if_control_variations").show();
						$(".show_if_control_variations").hide();
						$("#warranty_product_data :input[id!=variable_warranty_control]").change();
					}
				}).change();

				$("#variable_product_options").on("woocommerce_variations_added", function() {
					$("#variable_warranty_control").change();
				});

				$("#woocommerce-product-data").on("woocommerce_variations_loaded", function() {
					$("#variable_warranty_control").change();
				});
				';

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( $inline );
			} else {
				WC()->add_inline_js( $inline );
			}

			$warranty         = warranty_get_product_warranty( $post->ID );
			$warranty_label   = $warranty['label'];
			$default_warranty = false;
			$control_type     = 'parent';

			$product = wc_get_product( $post->ID );

			if ( $product->is_type( 'variable' ) ) {
				$control_type = get_post_meta( $post->ID, '_warranty_control', true );
				if ( ! $control_type ) {
					$control_type = 'variations';
				}
			}

			$default_warranty = isset( $warranty['default'] ) ? $warranty['default'] : false;

			if ( empty( $warranty_label ) ) {
				$warranty_label = __( 'Warranty', 'wc_warranty' );
			}

			include WooCommerce_Warranty::$base_path . '/templates/admin/product-panel.php';
		}

		/**
		 * Undocumented function
		 *
		 * @param int     $loop           Position in the loop.
		 * @param array   $variation_data Variation data.
		 * @param WP_Post $variation      Post data.
		 * @return void
		 */
		public function variables_panel( $loop, $variation_data, $variation ) {
			$warranty         = warranty_get_product_warranty( $variation->ID, false );
			$warranty_label   = $warranty['label'];
			$warranty_default = isset( $warranty['default'] ) ? $warranty['default'] : false;

			if ( empty( $warranty_label ) ) {
				$warranty_label = __( 'Warranty', 'wc_warranty' );
			}
			$currency = get_woocommerce_currency_symbol();

			include WooCommerce_Warranty::$base_path . '/templates/variables-panel-list.php';
		}

		/**
		 * Save product warranty data
		 *
		 * No need to check for a valid nonce because WooCommerce
		 * does this for us with the woocommerce_meta_nonce.
		 *
		 * @param int $product_id WC_Product ID.
		 * @return void
		 */
		public function save_product_warranty( $product_id ) {

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return;
			}

			$product->update_meta_data( '_warranty_control', self::post_get_string( 'variable_warranty_control', 'parent' ) );

			if ( self::post_field_equals( 'product_warranty_default', 'yes' ) ) {
				$product->delete_meta_data( '_warranty' );
				$product->save();
				return;
			}

			if ( self::post_is_empty( 'product_warranty_type' ) ) {
				return;
			}

			$label = self::post_get_string( 'warranty_label' );
			if ( ! empty( $label ) ) {
				$product->update_meta_data( '_warranty_label', $label );
			}

			$warranty = self::build_warranty_array( warranty_request_post_data(), self::post_get_string( 'product_warranty_type' ) );
			if ( ! empty( $warranty ) ) {
				$product->update_meta_data( '_warranty', $warranty );
			}

			$product->save();
		}

		/**
		 * Save product variation warranty data
		 *
		 * No need to check for a valid nonce because WooCommerce
		 * does this for us with the woocommerce_meta_nonce.
		 *
		 * @param int        $variation_id WC_Product_Variation ID.
		 * @param int|string $x the loop index.
		 * @return void
		 */
		public function save_variation_warranty( $variation_id, $x ) {
			$post_data = warranty_request_post_data();
			$variation = wc_get_product( $variation_id );
			$defaults  = isset( $post_data['variable_product_warranty_default'] ) ? $post_data['variable_product_warranty_default'] : array();
			$types     = isset( $post_data['variable_product_warranty_type'] ) ? $post_data['variable_product_warranty_type'] : array();
			$labels    = isset( $post_data['variable_warranty_label'] ) ? $post_data['variable_warranty_label'] : array();

			if ( isset( $defaults[ $x ] ) && 'on' === $defaults[ $x ] ) {
				$variation->delete_meta_data( '_warranty' );
				$variation->save();
				return;
			}

			$warranty = self::build_warranty_array_inside_loop( $post_data, $types, $x );

			if ( $labels[ $x ] ) {
				$variation->update_meta_data( '_warranty_label', sanitize_text_field( stripslashes( $labels[ $x ] ) ) );
			}

			$variation->update_meta_data( '_warranty', $warranty );
			$variation->save();
		}

		/**
		 * Add generate_rma action to woocommerce_order_actions.
		 *
		 * @param array $actions Woocommerce order actions.
		 * @return array $actions
		 */
		public function add_order_action( $actions ) {
			$actions['generate_rma'] = get_option( 'warranty_button_text', __( 'Create Warranty Request', 'wc_warranty' ) );

			return $actions;
		}

		/**
		 * Redirect to Warranty Request Admin Form
		 *
		 * @param WC_Order $order WC_Order object.
		 * @return void
		 */
		public function redirect_order_to_rma_form( $order ) {
			$url = admin_url( 'admin.php?page=warranties-new&search_key=order_id&search_term=' . $order->get_id() );
			wp_safe_redirect( $url );
			exit;
		}

		/**
		 * Routes the request to the correct page/file
		 */
		public function admin_controller() {
			global $wpdb;

			$get_data = warranty_request_get_data();
			$page     = isset( $get_data['page'] ) ? $get_data['page'] : 'warranties';

			if ( $this->is_updater_view() ) {
				$this->updater_page();

				return;
			}

			switch ( $page ) {

				case 'warranties':
					include WooCommerce_Warranty::$base_path . 'templates/list.php';
					break;

				case 'warranties-new':
					$orders    = array();
					$searched  = false;
					$form_view = false;

					if ( ! empty( $get_data['search_key'] ) && ! empty( $get_data['search_term'] ) ) {
						$searched = true;

						if ( 'customer' === $get_data['search_key'] ) {
							if ( is_email( $get_data['search_term'] ) ) {
								$sql = $wpdb->prepare(
									"SELECT DISTINCT post_id AS id
									FROM {$wpdb->postmeta} pm, {$wpdb->posts} p
									WHERE pm.post_id = p.ID
									AND pm.meta_key = '_billing_email'
									AND pm.meta_value LIKE %s",
									$get_data['search_term']
								);
							} else {
								$sql = $wpdb->prepare(
									"SELECT DISTINCT post_id AS id
									FROM {$wpdb->postmeta} pm, {$wpdb->posts} p
									WHERE pm.post_id = p.ID
									AND pm.meta_key = '_customer_user'
									AND pm.meta_value LIKE %s",
									$get_data['search_term']
								);
							}

							$orders = $wpdb->get_col( $sql );
						} else {
							$orders = array_unique( array_merge( $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ID AS id FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND ID LIKE %s", $get_data['search_term'] . '%' ) ), $wpdb->get_col( $wpdb->prepare( "SELECT post_id AS id FROM {$wpdb->postmeta} WHERE meta_key = '_order_number' AND meta_value LIKE %s", $get_data['search_term'] . '%' ) ) ) );
						}
					} elseif ( isset( $get_data['order_id'] ) && isset( $get_data['idx'] ) ) {
						$form_view = true;
					}

					include WooCommerce_Warranty::$base_path . 'templates/new.php';
					break;

				case 'warranties-bulk-update':
					include WooCommerce_Warranty::$base_path . 'templates/manage.php';
					break;

				case 'warranties-reports':
					$orders = $wpdb->get_results( "SELECT DISTINCT ID AS id FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status = 'publish' ORDER BY post_date DESC" );
					include WooCommerce_Warranty::$base_path . 'templates/report.php';
					break;

				case 'warranties-settings':
					include WooCommerce_Warranty::$base_path . 'templates/settings.php';
					break;
			}

			do_action( 'warranty_page_controller' );
		}

		/**
		 * Check if you visiting Warranty updater route.
		 *
		 * @return boolean
		 */
		public function is_updater_view() {
			$get_data = warranty_request_get_data();
			$page     = isset( $get_data['page'] ) ? $get_data['page'] : '';
			$view     = isset( $get_data['view'] ) ? $get_data['view'] : '';

			return ( 'warranties' === $page && 'updater' === $view );
		}

		/**
		 * Render Warranty Updater Page.
		 *
		 * @return void
		 */
		public function updater_page() {
			$get_data = warranty_request_get_data();
			if ( isset( $get_data['act'] ) && 'migrate_products' === $get_data['act'] ) {
				$args = array(
					'page_title'            => 'Data Update',
					'return_url'            => admin_url( 'admin.php?page=warranties&warranty-data-updated=true' ),
					'ajax_endpoint'         => 'warranty_migrate_products',
					'entity_label_singular' => 'request',
					'entity_label_plural'   => 'requests',
					'action_label'          => 'updated',
				);
			} else {
				wp_die( 'Unknown action passed. Please go back and try again' );
			}

			include WooCommerce_Warranty::$base_path . '/templates/admin/updater.php';
		}

		/**
		 * Create a warranty request from POST
		 *
		 * @return void|false Redirect and exit on success, return false on failure.
		 */
		public function create_warranty() {
			$post_data = warranty_request_post_data();
			$order_id  = isset( $post_data['order_id'] ) ? $post_data['order_id'] : 0;
			$type      = isset( $post_data['warranty_request_type'] ) ? $post_data['warranty_request_type'] : '';
			$qtys      = isset( $post_data['warranty_qty'] ) ? $post_data['warranty_qty'] : 0;
			$idxs      = array_keys( $qtys );
			$requests  = array();
			$errors    = array();

			$order = wc_get_order( $order_id );
			$items = $order->get_items();

			$products = array();
			foreach ( $idxs as $idx ) {
				$products[] = ! empty( $items[ $idx ]['variation_id'] ) ? $items[ $idx ]['variation_id'] : $items[ $idx ]['product_id'];
			}

			if ( ! warranty_user_has_access( wp_get_current_user(), $order ) ) {
				return false;
			}

			$request_id = warranty_create_request(
				array(
					'type'       => $type,
					'order_id'   => $order_id,
					'product_id' => $products,
					'index'      => $idxs,
					'qty'        => $qtys,
				)
			);

			if ( is_wp_error( $request_id ) ) {
				$result   = $request_id;
				$error    = $result->get_error_message( 'wc_warranty' );
				$errors[] = $error;
			} else {
				// save the custom forms.
				$result = WooCommerce_Warranty::process_warranty_form( $request_id );

				if ( is_wp_error( $result ) ) {
					$errors[] = $result->get_error_messages();
					$back     = admin_url( 'admin.php?page=warranty_requests&tab=new&order_id=' . $order_id );
					$back     = add_query_arg( 'errors', wp_json_encode( $errors ), $back );

					warranty_delete_request( $request_id );

					wp_safe_redirect( $back );
					exit;
				}

				if ( $order ) {
					$rma = get_post_meta( $request_id, '_code', true );
					// translators: %1: anchor opening tag, %2: request code, %3: anchor closing tag, %4: product name.
					$message = sprintf( esc_html__( '%1$sRMA (%3$s)%2$s has been created for %4$s', 'wc_warranty' ), '<a href="' . esc_url( admin_url( 'admin.php?page=warranties&s=' . $rma ) ) . '">', '</a>', $rma, get_the_title( $items[ $idx ]['product_id'] ) );
					$order->add_order_note( $message );
				}

				// set the initial status and send the emails.
				warranty_update_status( $request_id, 'new' );
			}

			$back = admin_url( 'admin.php?page=warranties' );
			$back = add_query_arg( 'updated', rawurlencode( esc_html__( 'Warranty request created', 'wc_warranty' ) ), $back );

			if ( ! empty( $errors ) ) {
				$back = add_query_arg( 'errors', wp_json_encode( $errors ), $back );
			}

			wp_safe_redirect( $back );
			exit;
		}

		/**
		 * Handle file upload request and attach the uploaded file to the specified RMA request
		 *
		 * @return void
		 */
		public function attach_shipping_label() {
			check_admin_referer( 'shipping_label_image', 'shipping_label_image_upload_nonce' );
			$post_data         = warranty_request_get_data();
			$request_id        = isset( $post_data['request_id'] ) ? absint( $post_data['request_id'] ) : 0;
			$shipping_label_id = media_handle_upload( 'shipping_label_image', $request_id );

			if ( is_int( $shipping_label_id ) ) {
				add_post_meta( $request_id, '_warranty_shipping_label', $shipping_label_id );
			}

			if ( isset( $post_data['redirect'] ) ) {
				$path = admin_url( 'edit.php?post_type=shop_order&shipping_label_attached=1' );
			} else {
				$path = admin_url( 'admin.php?page=warranties&updated=' . rawurlencode( esc_html__( 'Shipping label uploaded', 'wc_warranty' ) ) );
			}
			wp_safe_redirect( $path );
			exit;
		}

		/**
		 * Return inventory.
		 *
		 * @return void
		 */
		public function return_inventory() {

			check_admin_referer( 'warranty_return_inventory' );
			$request_data = warranty_request_data();
			$request_id   = absint( $request_data['id'] );

			warranty_return_product_stock( $request_id );
			warranty_update_request( $request_id, array( 'returned' => 'yes' ) );

			wp_safe_redirect( admin_url( 'admin.php?page=warranties&updated=' . rawurlencode( esc_html__( 'Product stock returned', 'wc_warranty' ) ) ) );
			exit;
		}

		public function refund_item() {

			check_admin_referer( 'warranty_update' );
			$request_data = warranty_request_data();
			$request_id   = absint( $request_data['id'] );
			$amount       = ! empty( $request_data['amount'] ) ? $request_data['amount'] : null;
			$add_notice   = isset( $request_data['add_notice'] ) ? (bool) $request_data['add_notice'] : false;

			$refund = warranty_refund_item( $request_id, $amount );

			if ( $add_notice ) {
				if ( is_wp_error( $refund ) ) {
					$message = $refund->get_error_message();
				} else {
					$message = esc_html__( 'Item marked as Refunded', 'wc_warranty' );
				}
				wc_add_notice( $message );
			}

			wp_safe_redirect( admin_url( 'admin.php?page=warranties&updated=' . rawurlencode( $message ) ) );
			exit;
		}

		public function csv_import_fields( $fields ) {
			$fields['_warranty_type']     = 'meta:_warranty_type';
			$fields['_warranty_duration'] = 'meta:_warranty_duration';
			$fields['_warranty_unit']     = 'meta:_warranty_unit';
			$fields['_warranty']          = 'meta:_warranty';
			$fields['_warranty_label']    = 'meta:_warranty_label';

			return $fields;
		}

		/**
		 * Get the available actions for the warranty based on its status
		 *
		 * @param int  $id Warranty Request ID.
		 * @param bool $html Pass TRUE to return in an HTML snippet. Defaults to false.
		 *
		 * @return array|string|false
		 */
		public static function get_warranty_actions( $id, $html = false ) {
			$request = warranty_load( $id );

			if ( ! $request ) {
				return false;
			}

			$actions      = array();
			$manage_stock = '';

			$returned         = get_option( 'warranty_returned_status', 'completed' );
			$completed_status = warranty_get_completed_status();
			$product          = wc_get_product( $request['product_id'] );
			$request_type     = ( ! isset( $request['request_type'] ) ) ? 'replacement' : $request['request_type'];

			// nonces.
			$nonces = array(
				'refund'    => wp_create_nonce( 'warranty_refund_item' ),
				'coupon'    => wp_create_nonce( 'warranty_send_coupon' ),
				'status'    => wp_create_nonce( 'warranty_update_status' ),
				'tracking'  => wp_create_nonce( 'warranty_tracking' ),
				'inventory' => wp_create_nonce( 'warranty_return_inventory' ),
			);

			if ( $product && $product->is_type( 'variation' ) ) {
				$product_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id();
				$stock      = get_post_meta( $product_id, '_stock', true );

				if ( $stock > 0 ) {
					$manage_stock = 'yes';
				}
			} else {
				$manage_stock = get_post_meta( $request['product_id'], '_manage_stock', true );
			}

			if ( $request['status'] === $returned && 'yes' === $manage_stock ) {
				if ( 'yes' === get_post_meta( $request['ID'], '_returned', true ) ) {
					$actions['inventory-return'] = array(
						'text'     => esc_html__( 'Stock returned', 'wc_warranty' ),
						'disabled' => true,
						'class'    => '',
					);
				} else {
					$actions['inventory-return'] = array(
						'text'     => esc_html__( 'Return Stock', 'wc_warranty' ),
						'disabled' => false,
						'data'     => array(
							'security' => $nonces['inventory'],
							'id'       => $request['ID'],
						),
						'class'    => 'warranty-inventory-return',
					);
				}
			}

			if ( 'completed' === $request['status'] ) {
				$refunded = get_post_meta( $request['ID'], '_refunded', true );

				if ( 'yes' === $refunded ) {
					$request_type = 'refund';
				}

				if ( 'refund' === $request_type ) {
					$item_amount     = warranty_get_item_amount( $request['ID'] );
					$amount_refunded = get_post_meta( $request['ID'], '_refund_amount', true );

					$actions['item-refund'] = array(
						'text'     => esc_html__( 'Refund Item', 'wc_warranty' ),
						'disabled' => false,
						'data'     => array(
							'security' => $nonces['refund'],
							'id'       => $request['ID'],
							'amount'   => $item_amount,
						),
						'class'    => 'warranty-item-refund',
					);
				} elseif ( 'coupon' === $request_type ) {
					$actions['item-coupon'] = array(
						'text'     => esc_html__( 'Send Coupon', 'wc_warranty' ),
						'data'     => array(
							'security' => $nonces['coupon'],
							'id'       => $request['ID'],
						),
						'disabled' => false,
						'class'    => 'warranty-item-coupon',
					);
				}
			}

			if ( ! $html ) {
				return $actions;
			}

			$out = '';

			foreach ( $actions as $action_key => $action ) {
				$disabled = ( $action['disabled'] ) ? 'disabled' : '';
				$data     = '';

				if ( ! empty( $action['data'] ) ) {
					foreach ( $action['data'] as $key => $value ) {
						$data .= ' data-' . $key . '="' . $value . '"';
					}
				}

				$out .= '<p><input type="button" class="button-primary full-width ' . $action['class'] . '" ' . $disabled . ' ' . $data . ' value="' . $action['text'] . '"/></p>';
			}

			return $out;
		}

		/**
		 * Handle delete requests and immediately redirects to the Warranties page
		 */
		public function warranty_delete() {

			check_admin_referer( 'warranty_delete' );
			$request_data = warranty_request_data();
			$request_id   = isset( $request_data['id'] ) ? $request_data['id'] : 0;
			warranty_delete_request( $request_id );

			wp_safe_redirect( admin_url( 'admin.php?page=warranties&updated=' . rawurlencode( esc_html__( 'Warranty request deleted', 'wc_warranty' ) ) ) );
			exit;
		}

		/**
		 * Renders a print-friendly version of the warranty request
		 */
		public function warranty_print() {
			$get_data   = warranty_request_get_data();
			$request_id = absint( $get_data['request'] );

			check_admin_referer( 'warranty_print' );

			$warranty = warranty_load( $request_id );

			if ( ! $warranty ) {
				return false;
			}

			$form   = get_option( 'warranty_form' );
			$inputs = json_decode( $form['inputs'] );

			$product_name = $warranty['product_name'];

			if ( isset( $warranty['qty'] ) ) {
				$product_name = $product_name . ' &times; ' . $warranty['qty'];
			}

			// array to be passed to the template file.
			$args = array(
				// header.
				'logo'          => get_option( 'warranty_print_logo', false ),
				'show_url'      => get_option( 'warranty_print_url', false ),
				'tracking_html' => self::get_tracking_html( $warranty ),
				'product_name'  => $product_name,
				'first_name'    => $warranty['first_name'],
				'last_name'     => $warranty['last_name'],
				'email'         => $warranty['email'],
				'form'          => $form,
				'inputs'        => $inputs,
				'warranty'      => $warranty,
				'order'         => wc_get_order( $warranty['order_id'] ),
			);

			wc_get_template( 'print.php', $args, 'wc-warranty', dirname( WooCommerce_Warranty::$plugin_file ) . '/templates/' );
		}

		/**
		 * Get tracking html.
		 *
		 * @param array $warranty Warranty Request.
		 * @return string
		 */
		private static function get_tracking_html( $warranty ) {
			$tracking_html = '';
			$tracking      = warranty_get_tracking_data( $warranty['ID'] );
			if ( empty( $tracking ) ) {
				return '-';
			}

			if ( ! empty( $tracking['store'] ) ) {
				$tracking_html .= '<p><strong>' . esc_html__( 'Store:', 'wc_warranty' ) . '</strong> ' . $warranty['return_tracking_code'];

				if ( ! empty( $warranty['return_tracking_provider'] ) ) {
					$tracking_html .= ' (' . ucwords( $warranty['return_tracking_provider'] ) . ')';
				}

				$tracking_html .= '</p>';
			}

			if ( ! empty( $tracking['customer'] ) ) {
				$tracking_html .= '<p><strong>' . esc_html__( 'Customer:', 'wc_warranty' ) . '</strong> ' . $warranty['tracking_code'];

				if ( ! empty( $warranty['tracking_provider'] ) ) {
					$tracking_html .= ' (' . ucwords( $warranty['tracking_provider'] ) . ')';
				}

				$tracking_html .= '</p>';
			}

			return $tracking_html;
		}

		/**
		 * If $post['bulk_edit'] is not empty, attempt to bulk edit the
		 * checked/selected products.
		 *
		 * @param array $post The submitted $_POST data.
		 *
		 * @return bool FALSE if $post['bulk_edit'] is empty. TRUE otherwise.
		 */
		public function maybe_bulk_edit_products( array $post ) {
			if ( empty( $post['bulk_edit'] ) ) {
				return false;
			}

			$product_ids = ! empty( $post['post'] ) ? $post['post'] : array();

			/**
			 * If there are no product IDs, go ahead and return true
			 * because there is nothing to change.
			 */
			if ( empty( $product_ids ) ) {
				return true;
			}

			$unique_product_ids = array_unique( $product_ids );
			$products           = $this->get_valid_product_objects_from_array( $unique_product_ids );

			/**
			 * If there are no WC_Product objects, go ahead and return true
			 * because there is nothing valid to change.
			 */
			if ( empty( $products ) ) {
				return true;
			}

			$default = ! empty( $post['warranty_default_bulk'] ) ? sanitize_text_field( $post['warranty_default_bulk'] ) : 'no';

			/**
			 * If we are bulk setting products to use the default warranty,
			 * we can update the products' metadata and go ahead
			 * and return true.
			 */
			if ( 'yes' === $default ) {
				foreach ( $products as $product ) {
					$product->delete_meta_data( '_warranty' );
					$product->save();
				}

				return true;
			}

			$args                                     = array();
			$args['included_warranty_length']         = ! empty( $post['included_warranty_length_bulk'] ) ? $post['included_warranty_length_bulk'] : '';
			$args['limited_warranty_length_value']    = ! empty( $post['limited_warranty_length_value_bulk'] ) ? $post['limited_warranty_length_value_bulk'] : '';
			$args['limited_warranty_length_duration'] = ! empty( $post['limited_warranty_length_duration_bulk'] ) ? $post['limited_warranty_length_duration_bulk'] : '';
			$args['addon_warranty_amount']            = ! empty( $post['addon_warranty_amount']['bulk'] ) ? $post['addon_warranty_amount']['bulk'] : array();
			$args['addon_warranty_length_value']      = ! empty( $post['addon_warranty_length_value']['bulk'] ) ? $post['addon_warranty_length_value']['bulk'] : array();
			$args['addon_warranty_length_duration']   = ! empty( $post['addon_warranty_length_duration']['bulk'] ) ? $post['addon_warranty_length_duration']['bulk'] : array();
			$args['no_warranty_option']               = ! empty( $post['addon_no_warranty_bulk'] ) ? $post['addon_no_warranty_bulk'] : 'no';

			$warranty_type = ! empty( $post['warranty_type_bulk'] ) ? sanitize_text_field( $post['warranty_type_bulk'] ) : 'no_warranty';
			$warranty      = self::build_warranty_array( $args, $warranty_type );
			$label         = ! empty( $post['warranty_label_bulk'] ) ? sanitize_text_field( $post['warranty_label_bulk'] ) : '';

			/**
			 * Loop through the checked products and update the appropriate
			 * metadata.
			 */
			foreach ( $products as $product ) {

				if ( ! empty( $label ) ) {
					$product->update_meta_data( '_warranty_label', $label );
				}

				$product->update_meta_data( '_warranty', $warranty );
				$product->save();
			}

			return true;
		}

		/**
		 * Process bulk edit request
		 */
		public function bulk_edit() {
			if ( ! isset( $_POST['warranty_admin_bulk_edit'] ) || ! wp_verify_nonce( sanitize_key( $_POST['warranty_admin_bulk_edit'] ), 'warranty_admin_bulk_edit' ) ) {
				wp_die( esc_html__( 'Edit failed. Please refresh the page and retry.', 'woocommerce-warranty' ) );
			}

			$post_data = warranty_request_post_data();

			// Maybe bulk edit products.
			$bulk_edited_products = $this->maybe_bulk_edit_products( $post_data );
			if ( $bulk_edited_products ) {
				$this->redirect_and_exit();
			}

			// If warranty_type is empty, we have no products to loop through, so exit.
			$type = ! empty( $post_data['warranty_type'] ) ? $post_data['warranty_type'] : array();
			if ( empty( $type ) ) {
				$this->redirect_and_exit();
			}

			$product_ids = array_keys( $type );

			$products = $this->get_valid_product_objects_from_array( $product_ids );
			if ( empty( $products ) ) {
				$this->redirect_and_exit();
			}

			$label   = ! empty( $post_data['warranty_label'] ) ? $post_data['warranty_label'] : array();
			$default = ! empty( $post_data['warranty_default'] ) ? $post_data['warranty_default'] : array();

			foreach ( $products as $product ) {
				$product_id = $product->get_id();

				if ( ! empty( $default[ $product_id ] ) && 'yes' === $default[ $product_id ] ) {
					$product->delete_meta_data( '_warranty' );
					$product->save();

					continue;
				}

				$warranty = self::build_warranty_array_inside_loop( $post_data, $type, $product_id );

				if ( ! empty( $label[ $product_id ] ) ) {
					$product->update_meta_data( '_warranty_label', sanitize_text_field( $label[ $product_id ] ) );
				}

				$product->update_meta_data( '_warranty', $warranty );
				$product->save();
			}

			$this->redirect_and_exit();
		}

		public function update_settings() {
			$post_data = warranty_request_post_data();
			$tab       = ! empty( $post_data['tab'] ) ? $post_data['tab'] : false;
			$fields    = Warranty_Settings::get_settings_fields();

			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'wc_warranty_settings_save' ) ) {
				die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! $tab ) {
				die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! empty( $post_data['warranty_override_all'] ) && 'yes' === $post_data['warranty_override_all'] ) {
				WooCommerce_Warranty::clear_all_product_warranties();
			}

			if ( ! empty( $post_data['warranty_reset_statuses'] ) && '1' === $post_data['warranty_reset_statuses'] ) {
				warranty_reset_statuses();
			}

			if ( isset( $fields[ $tab ] ) ) {
				WC_Admin_Settings::save_fields( $fields[ $tab ] );
			}

			update_option( 'warranty_reset_statuses', 'no' );

			wp_safe_redirect( admin_url( 'admin.php?page=warranties-settings&tab=' . $tab . '&updated=1' ) );
			exit;
		}

		public function count_shop_order_columns( $columns ) {
			self::$shop_order_columns = count( $columns ) - 1;

			return $columns;
		}

		/**
		 * Display the RMA button if the order has any outstanding warranty requests
		 *
		 * @param WC_Order $order WC_Order.
		 */
		public function order_inline_edit_actions( $order ) {
			if ( ! warranty_order_has_warranty_requests( $order->get_id() ) ) {
				return;
			}
			?>
			<a class="button tips inline-rma dashicons-before dashicons-controls-repeat" data-tip="<?php echo wc_sanitize_tooltip( esc_html__( 'Manage Return', 'wc_warranty' ) ); ?>" href="#"> </a>
			<?php
		}

		/**
		 * The template for inline-editing RMA requests
		 */
		public function order_inline_edit_template() {
			global $wp_query, $wp_list_table;
			$screen   = get_current_screen();
			$statuses = warranty_get_statuses();

			// nonces.
			$update_nonce = wp_create_nonce( 'warranty_update' );
			$coupon_nonce = wp_create_nonce( 'warranty_send_coupon' );

			$requests_str = array(
				'replacement' => esc_html__( 'Replacement item', 'wc_warranty' ),
				'refund'      => esc_html__( 'Refund', 'wc_warranty' ),
				'coupon'      => esc_html__( 'Refund as store credit', 'wc_warranty' ),
			);

			if ( 'edit-shop_order' !== $screen->id || empty( $wp_query->query_vars['post_type'] ) || 'shop_order' !== $wp_query->query_vars['post_type'] || ! have_posts() ) {
				return;
			}
			?>
			<table style="display: none">
				<tbody id="inlineedit">
					<?php
					foreach ( $wp_query->posts as $post ) :
						$requests = get_posts(
							array(
								'post_type'  => 'warranty_request',
								'nopaging'   => true,
								'fields'     => 'ids',
								'meta_query' => array(
									array(
										'key'   => '_order_id',
										'value' => $post->ID,
									),
								),
							)
						);

						if ( empty( $requests ) ) {
							continue;
						}
						?>
						<tr id="inline-edit-post-<?php echo esc_attr( $post->ID ); ?>" class="inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-order">
							<td colspan="<?php echo esc_attr( self::$shop_order_columns ); ?>">
								<?php
								foreach ( $requests as $request_id ) :
									$request = warranty_load( $request_id );

									if ( ! $request ) {
										continue;
									}

									$permissions = get_option( 'warranty_permissions', array() );
									$term        = wp_get_post_terms( $request['ID'], 'shop_warranty_status' );
									$status      = ( ! empty( $term ) ) ? $term[0] : $statuses[0];
									$me          = wp_get_current_user();
									$readonly    = true;
									$refunded    = get_post_meta( $request_id, '_refund_amount', true );
									?>
									<div class="warranty-request" id="warranty_request_<?php echo esc_attr( $request_id ); ?>">
										<h2>
											<?php
											// translators: Request code.
											printf( esc_html__( 'RMA %s', 'wc_warranty' ), esc_html( $request['code'] ) );
											?>
										</h2>

										<div class="warranty-update-message warranty-updated hidden">
											<p></p>
										</div>

										<fieldset class="inline-edit-col-right">
											<div class="inline-edit-col">
												<h3><?php esc_html_e( 'Products', 'wc_warranty' ); ?></h3>

												<?php foreach ( $request['products'] as $request_product ) : ?>
													<div class="field">
														<span class="label">
															<?php
															// translators: Product title, Quantity.
															printf( esc_html__( '%1$s &times; %2$d', 'wc_warranty' ), esc_html( get_the_title( $request_product['product_id'] ) ), esc_html( $request_product['quantity'] ) );
															?>
														</span>
													</div>
												<?php endforeach; ?>

												<h3><?php esc_html_e( 'RMA Data', 'wc_warranty' ); ?></h3>

												<div class="field">
													<span class="label"><?php esc_html_e( 'Request Type:', 'wc_warranty' ); ?></span>
													<span class="value"><?php echo esc_html( $requests_str[ $request['request_type'] ] ); ?></span>
												</div>
												<?php

												if ( 'refund' === $request['request_type'] && $refunded > 0 ) :
													?>
													<div class="field">
														<span class="label"><?php esc_html_e( 'Refunded:', 'wc_warranty' ); ?></span> <span class="value"><?php echo wc_price( $refunded ); ?></span>
													</div>
													<?php
												endif;

												$form   = get_option( 'warranty_form' );
												$inputs = json_decode( $form['inputs'] );

												foreach ( $inputs as $input ) {
													$key   = $input->key;
													$type  = $input->type;
													$field = $form['fields'][ $input->key ];

													if ( 'paragraph' === $type ) {
														continue;
													}

													$value = get_post_meta( $request['ID'], '_field_' . $key, true );

													if ( is_array( $value ) ) {
														$value = implode( ',<br/>', $value );
													}

													if ( 'file' === $type && ! empty( $value ) ) {
														$value = WooCommerce_Warranty::get_uploaded_file_anchor_tag( $value, 'customer' );
													}

													if ( empty( $value ) && ! empty( $item['reason'] ) && ! $this->row_reason_injected ) {
														$value = $item['reason'];
													}

													if ( ! $value ) {
														$value = '-';
													}
													?>
													<div class="field">
														<span class="label"><?php echo esc_html( $field['name'] ); ?>:</span> <span class="value"><?php echo wp_kses_post( $value ); ?></span>
													</div>

													<?php
												}
												?>
											</div>
										</fieldset>

										<fieldset class="inline-edit-col-left">
											<div class="inline-edit-col">
												<?php

												if ( in_array( 'administrator', $me->roles, true ) ) {
													$readonly = false;
												} elseif ( ! isset( $permissions[ $status->slug ] ) || empty( $permissions[ $status->slug ] ) ) {
													$readonly = false;
												} elseif ( in_array( $me->ID, $permissions[ $status->slug ], true ) ) {
													$readonly = false;
												}

												if ( $readonly ) {
													$status_content = ucfirst( $status->name );
												} else {
													$status_content = '<select class="warranty-status" name="status" id="status_' . $request['ID'] . '">';

													foreach ( $statuses as $_status ) :
														$sel             = ( $status->slug === $_status->slug ) ? 'selected' : '';
														$status_content .= '<option value="' . $_status->slug . '" ' . $sel . '>' . ucfirst( $_status->name ) . '</option>';
													endforeach;

													$status_content .= '</select>';
													// <button class="button-primary warranty-update-status" type="button" title="Update" data-id="'. $request['ID'] .'" data-security="'. $nonces['status'] .'"><span>'. __('Update', 'wc_warranty') .'</span></button>';
												}
												?>

												<h3><?php esc_html_e( 'RMA Status', 'wc_warranty' ); ?></h3>
												<?php
												echo $status_content; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
												?>

												<div class="codes_form closeable">
													<h4><?php esc_html_e( 'Return shipping details', 'wc_warranty' ); ?></h4>
													<?php
													$shipping_label_id = get_post_meta( $request['ID'], '_warranty_shipping_label', true );

													if ( $shipping_label_id ) {
														$lnk = wp_get_attachment_url( $shipping_label_id );
														echo '<a href="' . esc_url( $lnk ) . '"><strong>' . esc_html__( 'Download the Shipping Label', 'wc_warranty' ) . '</strong></a>';
													} else {
														?>
														<input name="shipping_label_image" id="shipping_label_<?php echo esc_attr( $request_id ); ?>" class="shipping-label-url short-text" type="text" value="" />
														<input name="shipping_label_image_id" id="shipping_label_id_<?php echo esc_attr( $request_id ); ?>" type="hidden" value="" />
														<input class="rma-upload-button button" type="button" data-id="<?php echo esc_attr( $request_id ); ?>" data-uploader_title="<?php esc_attr_e( 'Set Shipping Label', 'wc_warranty' ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Set Shipping Label', 'wc_warranty' ); ?>" value="<?php esc_attr_e( 'Select Shipping Label', 'wc_warranty' ); ?>" />
														<?php
													} // End final If Checking the attachment :).
													?>
												</div>
											</div>

											<div class="inline-edit-col">
												<h3><?php esc_html_e( 'Return Tracking Details', 'wc_warranty' ); ?></h3>

												<?php
												// if tracking code is being requested, notify the admin.
												$class = 'hidden';
												if ( 'y' === $request['request_tracking_code'] && empty( $request['tracking_code'] ) ) :
													$class = '';
												endif;
												?>
												<div class="codes_form closeable">
													<div class="wc-tracking-requested warranty-updated <?php echo esc_attr( $class ); ?>">
														<p><?php esc_html_e( 'Tracking information requested from customer', 'wc_warranty' ); ?></p>
													</div>

													<?php
													// Tracking code hasnt been requested yet.
													if ( 'y' !== $request['request_tracking_code'] ) :
														?>
														<div class="request-tracking-div">
															<label><input type="checkbox" name="request_tracking" value="1" />
																<strong><?php esc_html_e( 'Request tracking code from the Customer', 'wc_warranty' ); ?></strong></label>
														</div>
														<?php
													else : // tracking code requested.
														// if tracking code is not empty, it has already been provided.
														if ( ! empty( $request['tracking_code'] ) ) {
															echo '<strong>' . esc_html__( 'Customer Provided Tracking', 'wc_warranty' ) . ':</strong>&nbsp;';

															if ( ! empty( $request['tracking_provider'] ) ) {
																$all_providers = array();

																foreach ( WooCommerce_Warranty::get_providers() as $providers ) {
																	foreach ( $providers as $provider => $format ) {
																		$all_providers[ sanitize_title( $provider ) ] = $format;
																	}
																}

																$provider      = esc_html( $request['tracking_provider'] );
																$tracking_code = esc_html( $request['tracking_code'] );
																$link          = $all_providers[ $provider ];
																$link          = str_replace( '%1$s', $tracking_code, $link );
																$link          = str_replace( '%2$s', '', $link );
																printf( __( '%s via %s (<a href="' . esc_url( $link ) . '" target="_blank">Track Shipment</a>)', 'wc_warranty' ), $tracking_code, $provider, $link );
															} else {
																echo esc_html( $request['tracking_code'] );
															}
														}
													endif;
													?>
												</div>

												<div class="codes_form closeable">
													<?php
													if ( ! empty( $request['return_tracking_provider'] ) ) :
														?>
														<p>
															<label for="return_tracking_provider_<?php echo esc_attr( $request['ID'] ); ?>">
																<strong><?php esc_html_e( 'Shipping Provider', 'wc_warranty' ); ?></strong></label>
															<select class="return_tracking_provider" name="return_tracking_provider" id="return_tracking_provider_<?php echo esc_attr( $request['ID'] ); ?>">
																<?php
																foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
																	echo '<optgroup label="' . esc_attr( $provider_group ) . '">';
																	foreach ( $providers as $provider => $url ) {
																		echo '<option value="' . esc_attr( sanitize_title( $provider ) ) . '" ' . selected( sanitize_title( $provider ), $request['return_tracking_provider'], false ) . '>' . esc_html( $provider ) . '</option>';
																	}
																	echo '</optgroup>';
																}
																?>
															</select>
														</p>
														<p>
															<label for="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>">
																<strong><?php esc_html_e( 'Tracking details', 'wc_warranty' ); ?></strong></label>
															<input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>" value="<?php echo esc_attr( $request['return_tracking_code'] ); ?>" placeholder="<?php esc_attr_e( 'Enter the shipment tracking number', 'wc_warranty' ); ?>" />
															<span class="description"><?php esc_html_e( 'Shipping Details/Tracking', 'wc_warranty' ); ?></span>
														</p>
													<?php else : ?>
														<p>
															<label for="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>">
																<strong><?php esc_html_e( 'Tracking details', 'wc_warranty' ); ?></strong></label>
															<input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo esc_attr( $request['ID'] ); ?>" value="<?php echo esc_attr( $request['return_tracking_code'] ); ?>" placeholder="<?php esc_attr_e( 'Enter the shipment tracking number', 'wc_warranty' ); ?>" />
															<span class="description"><?php esc_html_e( 'Shipping Details/Tracking', 'wc_warranty' ); ?></span>
														</p>
													<?php endif; ?>
												</div>
											</div>
										</fieldset>

										<fieldset class="inline-edit-col-center">
											<div class="inline-edit-col">
												<h3><?php esc_html_e( 'Actions', 'wc_warranty' ); ?></h3>

												<div class="actions-block">
													<?php
													echo $this->get_warranty_actions( $request['ID'], true ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
													?>
												</div>
											</div>
										</fieldset>
										<br class="clear" />

										<div class="submit inline-edit-save">
											<input type="button" class="button close_tr" value="<?php esc_attr_e( 'Close', 'wc_warranty' ); ?>" />

											<div class="alignright">
												<a class="button-primary" target="_blank" href="<?php echo esc_url( wp_nonce_url( 'admin-post.php?action=warranty_print&request=' . $request['ID'], 'warranty_print' ) ); ?>"><?php esc_html_e( 'Print', 'wc_warranty' ); ?></a>
												<input type="button" class="button-primary rma-update" data-id="<?php echo esc_attr( $request_id ); ?>" data-security="<?php echo esc_attr( $update_nonce ); ?>" value="<?php esc_attr_e( 'Update', 'wc_warranty' ); ?>" />
												<input type="button" class="button-secondary warranty-trash" data-id="<?php echo esc_attr( $request_id ); ?>" data-security="<?php echo esc_attr( wp_create_nonce( 'warranty_delete' ) ); ?>" value="<?php esc_attr_e( 'Delete', 'wc_warranty' ); ?>" />
											</div>
										</div>

										<?php
										if ( 'refund' === $request['request_type'] ) :
											$item_amount = warranty_get_item_amount( $request_id );
											$available   = max( 0, $item_amount - floatval( $refunded ) );
											?>
											<div id="warranty-refund-modal-<?php echo esc_attr( $request_id ); ?>" style="display:none;">
												<table class="form-table">
													<tr>
														<th>
															<span class="label"><?php esc_html_e( 'Amount refunded:', 'wc_warranty' ); ?></span>
														</th>
														<td>
															<span class="value">
															<?php
															echo wc_price( $refunded ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
															?>
																</span>
														</td>
													</tr>
													<tr>
														<th>
															<span class="label"><?php esc_html_e( 'Item cost:', 'wc_warranty' ); ?></span>
														</th>
														<td>
															<span class="value">
															<?php
															echo wc_price( $item_amount ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
															?>
																</span>
														</td>
													</tr>
													<tr>
														<th>
															<span class="label"><?php esc_html_e( 'Refund amount:', 'wc_warranty' ); ?></span>
														</th>
														<td>
															<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
															<input type="text" class="input-short amount" value="<?php echo esc_attr( $available ); ?>" size="5" />
														</td>
													</tr>
												</table>

												<p class="submit alignright">
													<input type="button" class="warranty-process-refund button-primary" value="<?php esc_attr_e( 'Process Refund', 'wc_warranty' ); ?>" data-id="<?php echo esc_attr( $request_id ); ?>" data-security="<?php echo esc_attr( $update_nonce ); ?>" />
												</p>
											</div>
											<?php
										elseif ( 'coupon' === $request['request_type'] ) :
											$item_amount = warranty_get_item_amount( $request_id );
											?>
											<div id="warranty-coupon-modal-<?php echo esc_attr( $request_id ); ?>" style="display:none;">
												<table class="form-table">
													<tr>
														<th>
															<span class="label"><?php esc_html_e( 'Amount refunded:', 'wc_warranty' ); ?></span>
														</th>
														<td>
															<span class="value">
															<?php
															echo wc_price( $refunded ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
															?>
																</span>
														</td>
													</tr>
													<tr>
														<th>
															<span class="label"><?php esc_html_e( 'Item cost:', 'wc_warranty' ); ?></span>
														</th>
														<td>
															<span class="value">
															<?php
															echo wc_price( $item_amount ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
															?>
																</span>
														</td>
													</tr>
													<tr>
														<th>
															<span class="label"><?php esc_html_e( 'Coupon amount:', 'wc_warranty' ); ?></span>
														</th>
														<td>
															<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
															<input type="text" class="input-short amount" value="<?php echo esc_attr( $item_amount ); ?>" size="5" />
														</td>
													</tr>
												</table>

												<p class="submit alignright">
													<input type="button" class="warranty-process-coupon button-primary" value="<?php esc_html_e( 'Send Coupon', 'wc_warranty' ); ?>" data-id="<?php echo esc_attr( $request_id ); ?>" data-security="<?php echo esc_attr( $coupon_nonce ); ?>" />
												</p>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}

		/**
		 * Add warranty data to manually added line items
		 *
		 * @param int                 $item_id Order item ID.
		 * @param WC_Order_Item|false $item WC_Order_Item object.
		 */
		public function add_line_item_warranty_meta( $item_id, $item ) {
			$product_id     = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$warranty       = warranty_get_product_warranty( $product_id );
			$warranty_label = $warranty['label'];
			$warranty_index = null;

			if ( 'no_warranty' === $warranty['type'] ) {
				return;
			}

			if ( 'addon_warranty' === $warranty['type'] ) {
				wc_add_order_item_meta( $item_id, '_item_warranty_needs_index', 1 );
			} elseif ( 'included_warranty' === $warranty['type'] ) {
				if ( 'lifetime' === $warranty['length'] ) {
					wc_add_order_item_meta( $item_id, $warranty_label, __( 'Lifetime', 'wc_warranty' ) );
				} elseif ( 'limited' === $warranty['length'] ) {
					$string = warranty_get_warranty_string( 0, $warranty );

					wc_add_order_item_meta( $item_id, $warranty_label, $string );
				}
			}

			wc_add_order_item_meta( $item_id, '_item_warranty', $warranty );
		}

		/**
		 * Allow admins to set the correct warranty addon for a line item. This is mostly used by
		 * newly added line items from the edit order screen.
		 *
		 * @param int        $item_id wC_Order_Item ID.
		 * @param array      $item wC_Order_Item.
		 * @param WC_Product $product WC_Product.
		 */
		public function maybe_render_addon_options( $item_id, $item, $product ) {
			global $wc_warranty;

			if ( ! $product instanceof WC_Product ) {
				return;
			}

			$warranty    = warranty_get_product_warranty( $product ? $product->get_id() : null );
			$needs_index = wc_get_order_item_meta( $item_id, '_item_warranty_needs_index', true );

			if ( 'addon_warranty' === $warranty['type'] && $needs_index ) {
				$addons = $warranty['addons'];
				?>
				<table cellspacing="0" class="display_meta">
					<tr>
						<th><?php echo wp_kses_post( $warranty['label'] ); ?>:</th>
						<td>
							<select name="warranty_index[<?php echo esc_attr( $item_id ); ?>]">
								<?php
								if ( isset( $warranty['no_warranty_option'] ) && 'yes' === $warranty['no_warranty_option'] ) {
									echo '<option value="-1">' . esc_html__( 'No warranty', 'wc_warranty' ) . '</option>';
								}
								?>
								<?php foreach ( $addons as $idx => $addon ) : ?>
									<option value="<?php echo esc_attr( $idx ); ?>"><?php echo esc_html( $wc_warranty->get_warranty_string( $addon['value'], $addon['duration'] ) ); ?></option>
								<?php endforeach ?>
							</select>
						</td>
					</tr>
				</table>
				<?php
			}
		}

		/**
		 * Add hidden order item meta.
		 *
		 * @param array $hidden_meta Hidden item meta.
		 * @return array $hidden_meta
		 */
		public function hidden_order_item_meta( $hidden_meta ) {
			$hidden_meta[] = '_item_warranty_needs_index';
			$hidden_meta[] = '_item_warranty_selected';
			$hidden_meta[] = '_item_warranty_manually_added';

			return $hidden_meta;
		}

		/**
		 * When a product with an add-on warranty gets added to an order,
		 * we display the available add-ons in a select box. This method stores
		 * the selected add-on when the order is saved.
		 *
		 * @param int           $post_id Post ID.
		 * @param WP_Post|array $post WP_Post.
		 */
		public function save_line_item_warranty_indices( $post_id, $post ) {
			$warranty_index = array();
			$post_data      = warranty_request_post_data();
			if ( isset( $post_data['warranty_index'] ) && is_array( $post_data['warranty_index'] ) ) {
				$warranty_index = $post_data['warranty_index'];
			} elseif ( is_array( $post ) && ! empty( $post['warranty_index'] ) && is_array( $post['warranty_index'] ) ) {
				$warranty_index = $post['warranty_index'];
			}

			foreach ( $warranty_index as $item_id => $index ) {

				if ( wc_get_order_item_meta( $item_id, '_item_warranty_needs_index', true ) ) {
					wc_add_order_item_meta( $item_id, '_item_warranty_selected', $index );
					wc_add_order_item_meta( $item_id, '_item_warranty_manually_added', true );
					wc_delete_order_item_meta( $item_id, '_item_warranty_needs_index' );

					$warranty = wc_get_order_item_meta( $item_id, '_item_warranty', true );

					if ( isset( $warranty['type'] ) && 'addon_warranty' === $warranty['type'] ) {
						$addon = isset( $warranty['addons'][ $index ] ) ? $warranty['addons'][ $index ] : false;

						if ( $addon && $addon['amount'] > 0 ) {
							$total  = wc_get_order_item_meta( $item_id, '_line_total', true );
							$total += $addon['amount'];
							wc_update_order_item_meta( $item_id, '_line_total', $total );
						}
					}
				}
			}
		}

		/**
		 * Add addon price to line item.
		 *
		 * @param int   $order_id WC_Order ID.
		 * @param array $items Order items to save.
		 * @return void
		 */
		public function add_addon_price_to_line_item( $order_id, $items ) {
			$added = 0;

			if ( ! empty( $items['order_item_id'] ) ) {
				foreach ( $items['order_item_id'] as $item_id ) {
					$index          = wc_get_order_item_meta( $item_id, '_item_warranty_selected', true );
					$manually_added = wc_get_order_item_meta( $item_id, '_item_warranty_manually_added', true );

					if ( ! is_numeric( $index ) || ! $manually_added ) {
						continue;
					}

					wc_delete_order_item_meta( $item_id, '_item_warranty_manually_added' );
					$warranty = wc_get_order_item_meta( $item_id, '_item_warranty', true );

					if ( isset( $warranty['type'] ) && 'addon_warranty' === $warranty['type'] ) {
						$addon = isset( $warranty['addons'][ $index ] ) ? $warranty['addons'][ $index ] : false;

						if ( $addon && $addon['amount'] > 0 ) {
							$total    = $items['line_total'][ $item_id ];
							$subtotal = $items['line_subtotal'][ $item_id ];

							$subtotal += $addon['amount'];
							$total    += $addon['amount'];
							$added    += $addon['amount'];

							wc_update_order_item_meta( $item_id, '_line_total', $total );
							wc_update_order_item_meta( $item_id, '_line_subtotal', $subtotal );
						}
					}
				}
			}
		}

		/**
		 * Redirect and exit
		 *
		 * @return void
		 */
		public function redirect_and_exit() {
			wp_safe_redirect( admin_url( 'admin.php?page=warranties-bulk-update&updated=1' ) );
			exit;
		}

		/**
		 * Get valid WooCommerce Products.
		 *
		 * @param int[] $possible_product_ids List of WC_Product IDs.
		 *
		 * @return WC_Product[]
		 */
		public function get_valid_product_objects_from_array( array $possible_product_ids ) {
			$products = array();
			foreach ( $possible_product_ids as $product_id ) {
				if ( 1 > absint( $product_id ) ) {
					continue;
				}

				$product = wc_get_product( $product_id );
				if ( ! $product instanceof WC_Product ) {
					continue;
				}

				$products[] = $product;
			}

			return $products;
		}
	}
endif;

return new Warranty_Admin();
