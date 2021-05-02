<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;

if ( ! class_exists( 'Warranty_Admin' ) ) :
	class Warranty_Admin {

		public static $shop_order_columns = 1;

		/**
		 * Register the hooks
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'add_notices' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 11 );
			add_action( 'admin_footer', array( $this, 'variable_script' ) );

			// metaboxes
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_warranty' ) );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_warranty' ), 10, 2 );

			// order actions
			add_filter( 'woocommerce_order_actions', array( $this, 'add_order_action' ) );
			add_action( 'woocommerce_order_action_generate_rma', array( $this, 'redirect_order_to_rma_form' ) );

			// variable products support
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variables_panel' ), 10, 3 );

			// Update request from the admin
			add_action( 'admin_post_warranty_create', array( $this, 'create_warranty' ) );
			add_action( 'admin_post_warranty_delete', array( $this, 'warranty_delete' ) );
			add_action( 'admin_post_warranty_print', array( $this, 'warranty_print' ) );

			add_action( 'admin_post_warranty_upload_shipping_label', array( $this, 'attach_shipping_label' ) );

			// return stock
			add_action( 'admin_post_warranty_return_inventory', array( $this, 'return_inventory' ) );

			// refund order item
			add_action( 'admin_post_warranty_refund_item', array( $this, 'refund_item' ) );

			// CSV Import
			add_filter( 'woocommerce_csv_product_post_columns', array( $this, 'csv_import_fields' ) );

			// bulk edit
			add_action( 'admin_post_warranty_bulk_edit', array( $this, 'bulk_edit' ) );

			// save settings
			add_action( 'admin_post_wc_warranty_settings_update', array( $this, 'update_settings' ) );

			add_filter( 'manage_shop_order_posts_columns', array( $this, 'count_shop_order_columns' ), 1000 );
			add_action( 'woocommerce_admin_order_actions_end', array( $this, 'order_inline_edit_actions' ) );
			add_action( 'admin_footer', array( $this, 'order_inline_edit_template' ) );

			add_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'add_line_item_warranty_meta' ), 10, 2 );
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'maybe_render_addon_options' ), 10, 3 );
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'render_order_item_warranty' ), 10, 3 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_line_item_warranty_indices' ), 10, 2 );
			add_action( 'woocommerce_saved_order_items', array( $this, 'save_line_item_warranty_indices' ), 9, 2 );
			add_action( 'woocommerce_saved_order_items', array( $this, 'add_addon_price_to_line_item' ), 10, 2 );

			add_action( 'woocommerce_order_item_meta_end', array( $this, 'render_order_item_warranty' ), 10, 3 );
			add_action( 'init', array( $this, 'init' ) );
		}

		public function init() {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'panel_data_tab' ) );

			if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
				add_action( 'woocommerce_product_write_panels', array( $this, 'panel_add_custom_box' ) );
			} else {
				add_action( 'woocommerce_product_data_panels', array( $this, 'panel_add_custom_box' ) );
			}
		}

		/**
		 * Register the menu items
		 */
		public function admin_menu() {
			add_menu_page( __( 'Warranties', 'wc_warranty' ), __( 'Warranties', 'wc_warranty' ), 'manage_warranties', 'warranties', 'Warranty_Admin::admin_controller', 'dashicons-update', '54.52' );
			add_submenu_page( 'warranties', __( 'RMA Requests', 'wc_warranty' ), __( 'RMA Requests', 'wc_warranty' ), 'manage_warranties', 'warranties', 'Warranty_Admin::admin_controller' );
			add_submenu_page( 'warranties', __( 'New Request', 'wc_warranty' ), __( 'New Request', 'wc_warranty' ), 'manage_warranties', 'warranties-new', 'Warranty_Admin::admin_controller' );
			add_submenu_page( 'warranties', __( 'Manage Warranties', 'wc_warranty' ), __( 'Manage Warranties', 'wc_warranty' ), 'manage_woocommerce', 'warranties-bulk-update', 'Warranty_Admin::admin_controller' );
			add_submenu_page( 'warranties', __( 'Reports', 'wc_warranty' ), __( 'Reports', 'wc_warranty' ), 'manage_woocommerce', 'warranties-reports', 'Warranty_Admin::admin_controller' );
			add_submenu_page( 'warranties', __( 'Settings', 'wc_warranty' ), __( 'Settings', 'wc_warranty' ), 'manage_woocommerce', 'warranties-settings', 'Warranty_Admin::admin_controller' );

			if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Menu' ) ) {
				return;
			}

			Menu::add_plugin_category(
				array(
					'id'         => 'warranties-category',
					'title'      => __( 'Warranties', 'wc_warranty' ),
					'capability' => 'manage_warranties',
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties',
					'parent'     => 'warranties-category',
					'title'      => __( 'RMA Requests', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties',
					'capability' => 'manage_warranties',
					'order'      => 0,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-new',
					'parent'     => 'warranties-category',
					'title'      => __( 'New Request', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-new',
					'capability' => 'manage_warranties',
					'order'      => 1,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-bulk-update',
					'parent'     => 'warranties-category',
					'title'      => __( 'Manage Warranties', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-bulk-update',
					'capability' => 'manage_woocommerce',
					'order'      => 2,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-reports',
					'parent'     => 'warranties-category',
					'title'      => __( 'Reports', 'wc_warranty' ),
					'url'        => 'admin.php?page=warranties-reports',
					'capability' => 'manage_woocommerce',
					'order'      => 3,
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'warranties-settings',
					'parent'     => 'warranties-category',
					'title'      => __( 'Settings', 'wc_warranty' ),
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

			// shipping label attached
			if (
				!empty( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' &&
				!empty( $_GET['shipping_label_attached'] )
			) {
				echo '<div class="updated"><p>' . __( 'Shipping label attached', 'wc_warranty' ) . '</p></div>';
			}

		}

		/**
		 * Load scripts and styles selectively, depending on the current screen and page
		 */
		function admin_scripts() {
			global $woocommerce;

			$pages = array( 'warranties', 'warranties-new', 'warranties-bulk-update', 'warranties-reports', 'warranties-settings' );

			if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pages ) ) {
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_style( 'select2' );
				wp_enqueue_script( 'wc-enhanced-select' );

				wp_enqueue_script( 'user-email-search', plugins_url( 'assets/js/user-email-search.js', WooCommerce_Warranty::$plugin_file ), array( 'wc-enhanced-select' ) );

				add_thickbox();
				wp_enqueue_media();

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array('jquery'), WC()->version );

				wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );

				wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array( 'jquery' ), '2.70', true );
				wp_enqueue_script( 'jquery-tiptip' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-core', null, array('jquery') );

				$js = '
						jQuery(".warranty-delete").click(function(e) {
							return confirm("'. __('Do you really want to delete this request?', 'wc_warranty') .'");
						});
						var tiptip_args = {
							"attribute" : "data-tip",
							"fadeIn" : 50,
							"fadeOut" : 50,
							"delay" : 200
						};
						$(".tips, .help_tip").tipTip( tiptip_args );
					';

				if ( function_exists( 'wc_enqueue_js') ) {
					wc_enqueue_js( $js );
				} else {
					$woocommerce->add_inline_js( $js );
				}
			}

			// settings css
			$pages[] = 'wc-settings';
			$pages[] = 'woocommerce_warranty';

			if ( isset( $_GET['page'] ) ) {
				if ( $_GET['page'] == 'warranties' ) {
					wp_enqueue_script( 'warranties_list', plugins_url( 'assets/js/list.js', WooCommerce_Warranty::$plugin_file ), array('jquery') );
				}

				if ( in_array( $_GET['page'], $pages ) ) {
					wp_enqueue_style( 'warranty_admin_css', plugins_url( 'assets/css/admin.css', WooCommerce_Warranty::$plugin_file) );

					wp_enqueue_script( 'jquery-ui' );
					wp_enqueue_script( 'jquery-ui-sortable' );
					wp_enqueue_script( 'warranty_form_builder', plugins_url( 'assets/js/form-builder.js', WooCommerce_Warranty::$plugin_file ) );

					$data = array(
						'help_img_url' => plugins_url() . '/woocommerce/assets/images/help.png',
						'tips' => array_map( 'wc_sanitize_tooltip', WooCommerce_Warranty::$tips ),
					);

					wp_localize_script( 'warranty_form_builder', 'WFB', $data );
				}
			}

			$screen = get_current_screen();

			if ( $screen->id == 'edit-shop_order' ) {
				add_thickbox();
				wp_enqueue_media();
				wp_enqueue_style( 'warranty_admin_css', plugins_url( 'assets/css/admin.css', WooCommerce_Warranty::$plugin_file ) );
				wp_enqueue_script( 'warranty_shop_order', plugins_url( 'assets/js/orders.js', WooCommerce_Warranty::$plugin_file ), array( 'jquery' ) );
			}

			wp_enqueue_style( 'wc-form-builder', plugins_url( 'assets/css/form-builder.css', WooCommerce_Warranty::$plugin_file ) );

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

			if ( self::is_updater_view() ) {
				wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui-progressbar', false, array( 'jquery', 'jquery-ui' ) );
				wp_enqueue_script(
					'warranty_data_updater',
					plugins_url( '/assets/js/data-updater.js', WooCommerce_Warranty::$plugin_file ),
					array( 'jquery', 'jquery-ui-progressbar' )
				);
			}

		}

		/**
		 * Render JS on the footer that controls variable fields for variable products
		 */
		public function variable_script() {
			$screen     = get_current_screen();
			$currency   = get_woocommerce_currency_symbol();
			if ( $screen->id == 'product' ) {
			?>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						$( '.wc-metaboxes-wrapper' ).on( 'click' , '.wc-metabox h3' , function( event ) {
							$( 'select.variable-warranty-type' ).trigger( 'change.warranty' );
						});
						var $variable_product_options = $("#variable_product_options");

                        $variable_product_options.on("change.warranty", ".warranty_default_checkbox", function() {
							var id = $(this).data("id");

							if ($(this).is(":checked")) {
								$(".warranty_"+id).attr("disabled", true);
							} else {
								$(".warranty_"+id).attr("disabled", false);
							}
						})

                        $variable_product_options.on("change.warranty", ".variable-warranty-type", function() {
							var loop = $(this).parents(".warranty-variation").data("loop");

							$(".variable_show_if_included_warranty_"+ loop).hide()
							$(".variable_show_if_addon_warranty_"+loop).hide();

							if ($(this).val() == "included_warranty") {
								$(".variable_show_if_included_warranty_"+ loop).show();
							} else if ($(this).val() == "addon_warranty") {
								$(".variable_show_if_addon_warranty_"+ loop).show();
							}
						})

                        $variable_product_options.on("change.warranty", ".variable-included-warranty-length", function() {
							var loop = $(this).parents(".warranty-variation").data("loop");

							if ($(this).val() == "limited") {
								$(".variable_limited_warranty_length_field_"+ loop ).show();
							} else {
								$(".variable_limited_warranty_length_field_"+ loop ).hide();
							}
						})

						var variable_tmpl = "<tr>\
								<td valign=\"middle\">\
									<span class=\"input\"><b>+</b> <?php echo $currency; ?></span>\
									<input type=\"text\" name=\"variable_addon_warranty_amount[_loop_][]\" value=\"\" class=\"input-text sized warranty__loop_\" style=\"min-width:50px; width:50px;\" />\
								</td>\
								<td valign=\"middle\">\
									<input type=\"text\" class=\"input-text sized warranty__loop_\" style=\"width:50px;\" name=\"variable_addon_warranty_length_value[_loop_][]\" value=\"\" />\
									<select name=\"variable_addon_warranty_length_duration[_loop_][]\" class=\"warranty__loop_\" style=\"width: auto !important;\">\
										<option value=\"days\"><?php _e('Days', 'wc_warranty'); ?></option>\
										<option value=\"weeks\"><?php _e('Weeks', 'wc_warranty'); ?></option>\
										<option value=\"months\"><?php _e('Months', 'wc_warranty'); ?></option>\
										<option value=\"years\"><?php _e('Years', 'wc_warranty'); ?></option>\
									</select>\
								</td>\
								<td><a class=\"button warranty_addon_remove warranty_addon_remove_variable warranty__loop_\" href=\"#\">&times;</a></td>\
							</tr>";

                        $variable_product_options.on("click", ".btn-add-warranty-variable", function(e) {
							e.preventDefault();
							if ( $(this).attr('disabled') ) {
							    return;
                            }
							var loop = $(this).data("loop");

							$("#variable_warranty_addons_"+ loop).append( variable_tmpl.replace(/_loop_/g, loop) );
						});

						$("#variable_product_options").on("click", ".warranty_addon_remove", function(e) {
							e.preventDefault();
                            if ( $(this).attr('disabled') ) {
                                return;
                            }
                            $(this).closest( '.warranty-variation' ).find('.warranty_default_checkbox').trigger('change');

							$(this).parents("tr").eq(0).remove();
						});

						var $woocommerce_product_data = $( '#woocommerce-product-data' );
                        $woocommerce_product_data.on( 'woocommerce_variations_loaded' , function() {
							$( '.warranty_default_checkbox, .variable-warranty-type, .variable-warranty-length' ).trigger( 'change.warranty' );
						});
                        $woocommerce_product_data.on( 'woocommerce_variations_added', function() {
							$( '.warranty_default_checkbox, .variable-warranty-type, .variable-warranty-length' ).trigger( 'change.warranty' );
						});
					});
				</script>
			<?php
			}

		}

		/**
		 * Adds a 'Warranty' tab to a product's data tabs
		 */
		function panel_data_tab() {
			echo ' <li class="warranty_tab tax_options hide_if_external"><a href="#warranty_product_data"><span>' . __( 'Warranty', 'woocommerce' ) . '</span></a></li>';
		}

		/**
		 * Outputs the form for the Warranty data tab
		 */
		function panel_add_custom_box() {
			global $post, $wpdb, $thepostid, $woocommerce;

			$warranty_type_value = get_post_meta( $post->ID, '_warranty_type', true );

			if ( trim( $warranty_type_value ) == '' ) {
				update_post_meta( $post->ID, '_warranty_type', 'no_warranty' );
				$warranty_type_value = 'no_warranty';
			}

			$warranty_duration_value = get_post_meta( $post->ID, '_warranty_duration', true );

			if ( trim( $warranty_duration_value ) == '' ) {
				update_post_meta( $post->ID, '_warranty_duration', 0 );
				$warranty_duration_value = 0;
			}

			$warranty_unit_value = get_post_meta( $post->ID, '_warranty_unit', true );

			if ( trim( $warranty_unit_value )=='' ) {
				update_post_meta( $post->ID, '_warranty_unit', 'day' );
				$warranty_unit_value = 'day';
			}

			$currency = get_woocommerce_currency_symbol();
			$inline = '
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
									<span class=\"input\"><b>+</b> '. $currency .'</span>\
									<input type=\"text\" name=\"addon_warranty_amount[]\" class=\"input-text sized\" size=\"4\" value=\"\" />\
								</td>\
								<td valign=\"middle\">\
									<input type=\"text\" class=\"input-text sized\" size=\"3\" name=\"addon_warranty_length_value[]\" value=\"\" />\
									<select name=\"addon_warranty_length_duration[]\">\
										<option value=\"days\">'. __('Days', 'wc_warranty') .'</option>\
										<option value=\"weeks\">'. __('Weeks', 'wc_warranty') .'</option>\
										<option value=\"months\">'. __('Months', 'wc_warranty') .'</option>\
										<option value=\"years\">'. __('Years', 'wc_warranty') .'</option>\
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
				$woocommerce->add_inline_js( $inline );
			}

			$warranty       = warranty_get_product_warranty( $post->ID );
			$warranty_label = $warranty['label'];
			$default_warranty = false;
			$control_type   = 'parent';

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

		function variables_panel( $loop, $data, $variation ) {
			$warranty       = warranty_get_product_warranty( $variation->ID, false );
			$warranty_label = $warranty['label'];
			$warranty_default = isset( $warranty['default'] ) ? $warranty['default'] : false;

			if ( empty( $warranty_label ) ) {
				$warranty_label = __( 'Warranty', 'wc_warranty' );
			}
			$currency = get_woocommerce_currency_symbol();

			include WooCommerce_Warranty::$base_path . '/templates/variables-panel-list.php';
		}

		/**
		 * Save product warranty data
		 * @param int $post_ID
		 */
		public function save_product_warranty( $post_ID ) {

			$control = ( isset( $_POST['variable_warranty_control'] ) ) ? $_POST['variable_warranty_control'] : 'parent';
            update_post_meta( $post_ID, '_warranty_control', $control );

			if ( ! empty( $_POST['product_warranty_default'] ) && $_POST['product_warranty_default'] == 'yes' ) {
				delete_post_meta( $post_ID, '_warranty' );
			} elseif ( isset( $_POST['product_warranty_type'] ) ) {
				if ( $_POST['product_warranty_type'] == 'no_warranty' ) {
					$product_warranty = array( 'type' => 'no_warranty' );
					update_post_meta( $post_ID, '_warranty', $product_warranty );
				} elseif ( $_POST['product_warranty_type'] == 'included_warranty' ) {
					$product_warranty = array(
						'type'      => 'included_warranty',
						'length'    => $_POST['included_warranty_length'],
						'value'     => $_POST['limited_warranty_length_value'],
						'duration'  => $_POST['limited_warranty_length_duration']
					);
					update_post_meta( $post_ID, '_warranty', $product_warranty );
				} elseif ( $_POST['product_warranty_type'] == 'addon_warranty' ) {
					$no_warranty= ( isset( $_POST['addon_no_warranty'] ) ) ? $_POST['addon_no_warranty'] : 'no';
					$amounts    = $_POST['addon_warranty_amount'];
					$values     = $_POST['addon_warranty_length_value'];
					$durations  = $_POST['addon_warranty_length_duration'];
					$addons     = array();

					for ( $x = 0; $x < count( $amounts ); $x++ ) {
						if ( ! isset( $amounts[ $x ] ) || ! isset( $values[ $x ] ) || ! isset( $durations[ $x ] ) ) continue;

						$addons[] = array(
							'amount'    => $amounts[ $x ],
							'value'     => $values[ $x ],
							'duration'  => $durations[ $x ]
						);
					}

					$product_warranty = array(
						'type'                  => 'addon_warranty',
						'addons'                => $addons,
						'no_warranty_option'    => $no_warranty
					);
					update_post_meta( $post_ID, '_warranty', $product_warranty );
				}

				if ( isset( $_POST['warranty_label'] ) ) {
					update_post_meta( $post_ID, '_warranty_label', stripslashes( $_POST['warranty_label'] ) );
				}
			}
		}

		/**
		 * Save product variation warranty data
		 *
		 * @param int $variation_id
         * @param int $x
		 */
		public function save_variation_warranty( $variation_id, $x ) {

			$defaults       = ( isset( $_POST['variable_product_warranty_default'] ) ) ? $_POST['variable_product_warranty_default'] : array();
			$types          = ( isset( $_POST['variable_product_warranty_type'] ) ) ? $_POST['variable_product_warranty_type'] : array();
			$labels         = ( isset( $_POST['variable_warranty_label'] ) ) ? $_POST['variable_warranty_label'] : array();
			$inc_lengths    = ( isset( $_POST['variable_included_warranty_length'] ) ) ? $_POST['variable_included_warranty_length'] : array();
			$ltd_lengths    = ( isset( $_POST['variable_limited_warranty_length_value'] ) ) ? $_POST['variable_limited_warranty_length_value'] : array();
			$ltd_durations  = ( isset( $_POST['variable_limited_warranty_length_duration'] ) ) ? $_POST['variable_limited_warranty_length_duration'] : array();
			$addon_amounts  = ( isset( $_POST['variable_addon_warranty_amount'] ) ) ? $_POST['variable_addon_warranty_amount'] : array();
			$addon_lengths  = ( isset( $_POST['variable_addon_warranty_length_value'] ) ) ? $_POST['variable_addon_warranty_length_value'] : array();
			$addon_durations= ( isset( $_POST['variable_addon_warranty_length_duration'] ) ) ? $_POST['variable_addon_warranty_length_duration'] : array();
			$no_warranties  = ( isset( $_POST['variable_addon_no_warranty'] ) ) ? $_POST['variable_addon_no_warranty'] : array();

            if ( isset( $defaults[ $x ] ) && 'on' == $defaults[ $x ] ) {
                delete_post_meta( $variation_id, '_warranty' );
                return;
            }

            if ( $types[$x] == 'no_warranty' ) {
                $product_warranty = array('type' => 'no_warranty');
                update_post_meta( $variation_id, '_warranty', $product_warranty );
            } elseif ( $types[$x] == 'included_warranty' ) {
                $product_warranty = array(
                    'type'      => 'included_warranty',
                    'length'    => $inc_lengths[ $x ],
                    'value'     => $ltd_lengths[ $x ],
                    'duration'  => $ltd_durations[ $x ]
                );
                update_post_meta( $variation_id, '_warranty', $product_warranty );
            } elseif ( $types[ $x ] == 'addon_warranty' ) {
                $no_warranty= ( isset( $no_warranties[ $x ] ) ) ? $no_warranties[ $x ] : 'no';
                $amounts    = $addon_amounts[ $x ];
                $values     = $addon_lengths[ $x ];
                $durations  = $addon_durations[ $x ];
                $addons     = array();

                for ( $i = 0; $i < count( $amounts ); $i++ ) {
                    if ( ! isset( $amounts[ $i ] ) || ! isset( $values[ $i ] ) || ! isset( $durations[ $i ] ) ) continue;

                    $addons[] = array(
                        'amount'    => $amounts[ $i ],
                        'value'     => $values[ $i ],
                        'duration'  => $durations[ $i ]
                    );
                }

                $product_warranty = array(
                    'type'                  => 'addon_warranty',
                    'addons'                => $addons,
                    'no_warranty_option'    => $no_warranty
                );
                update_post_meta( $variation_id, '_warranty', $product_warranty );
            }

            if ( $labels[ $x ] ) {
                update_post_meta( $variation_id, '_warranty_label', stripslashes( $labels[ $x ] ) );
            }
		}

		public function add_order_action( $actions ) {
			$actions['generate_rma'] = get_option( 'warranty_button_text', __( 'Create Warranty Request', 'wc_warranty' ) );

			return $actions;
		}

		public function redirect_order_to_rma_form( $order ) {
			$url = admin_url( 'admin.php?page=warranties-new&search_key=order_id&search_term=' . WC_Warranty_Compatibility::get_order_prop( $order, 'id' ) );
			wp_redirect( $url );
			exit;
		}

		/**
		 * Routes the request to the correct page/file
		 */
		public static function admin_controller() {
			global $wpdb;

			$page = isset( $_GET['page'] ) ? $_GET['page'] : 'warranties';

			if ( self::is_updater_view() ) {
				self::updater_page();
				return;
			}

			switch ( $page ) {

				case 'warranties':
					include WooCommerce_Warranty::$base_path .'templates/list.php';
					break;

				case 'warranties-new':
					$orders         = array();
					$searched       = false;
					$form_view      = false;

					if ( ! empty( $_GET['search_key'] ) && ! empty( $_GET['search_term'] ) ) {
						$searched   = true;

						if ( $_GET['search_key'] == 'customer' ) {
							if ( is_email( $_GET['search_term'] ) ) {
								$sql = $wpdb->prepare(
									"SELECT DISTINCT post_id AS id
									FROM {$wpdb->postmeta} pm, {$wpdb->posts} p
									WHERE pm.post_id = p.ID
									AND pm.meta_key = '_billing_email'
									AND pm.meta_value LIKE %s",
									$_GET['search_term']
								);
							} else {
								$sql = $wpdb->prepare(
									"SELECT DISTINCT post_id AS id
									FROM {$wpdb->postmeta} pm, {$wpdb->posts} p
									WHERE pm.post_id = p.ID
									AND pm.meta_key = '_customer_user'
									AND pm.meta_value LIKE %s",
									$_GET['search_term']
								);
							}

							$orders = $wpdb->get_col( $sql );
						} else {
							$orders = array_unique( array_merge(
								$wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ID AS id FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND ID LIKE %s", $_GET['search_term'] . '%' ) ),
								$wpdb->get_col( $wpdb->prepare( "SELECT post_id AS id FROM {$wpdb->postmeta} WHERE meta_key = '_order_number' AND meta_value LIKE %s", $_GET['search_term'] . '%' ) )
							) );
						}
					} elseif ( isset( $_GET['order_id'] ) && isset( $_GET['idx'] ) ) {
						$form_view  = true;
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

		public static function is_updater_view() {
			$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$view = isset( $_GET['view'] ) ? $_GET['view'] : '';

			return ( $page == 'warranties' && $view == 'updater' );
		}

		public static function updater_page() {
			if ( $_GET['act'] == 'migrate_products' ) {
				$args = array(
					'page_title'    => 'Data Update',
					'return_url'    => admin_url( 'admin.php?page=warranties&warranty-data-updated=true' ),
					'ajax_endpoint' => 'warranty_migrate_products',
					'entity_label_singular' => 'request',
					'entity_label_plural'   => 'requests',
					'action_label'          => 'updated'
				);
			} else {
				wp_die( 'Unknown action passed. Please go back and try again' );
			}

			include WooCommerce_Warranty::$base_path . '/templates/admin/updater.php';
		}

		/**
		 * Create a warranty request from POST
		 */
		public function create_warranty() {
			$order_id   = $_POST['order_id'];
			$type       = $_POST['warranty_request_type'];
			$qtys       = $_POST['warranty_qty'];
			$idxs       = array_keys( $qtys );
			$requests   = array();
			$errors     = array();

			$order = wc_get_order( $order_id );
			$items  = $order->get_items();

			$products = array();
			foreach ( $idxs as $i => $idx ) {
				$products[] = ! empty( $items[ $idx ]['variation_id'] )
					? $items[ $idx ]['variation_id']
					: $items[ $idx ]['product_id'];
			}

			$request_id = warranty_create_request( array(
				'type'          => $type,
				'order_id'      => $order_id,
				'product_id'    => $products,
				'index'         => $idxs,
				'qty'           => $qtys
			) );

			if ( is_wp_error( $request_id ) ) {
				$result = $request_id;
				$error  = $result->get_error_message( 'wc_warranty' );
				$errors[] = $error;
			} else {
				// save the custom forms
				$result = WooCommerce_Warranty::process_warranty_form( $request_id );

				if ( is_wp_error($result) ) {
					$errors[] = $result->get_error_messages();
					$back   = 'admin.php?page=warranty_requests&tab=new&order_id=' . $order_id;
					$back   = add_query_arg( 'errors', wp_json_encode($errors), $back );

					warranty_delete_request( $request_id );

					wp_redirect( $back );
					exit;
				}

				if ( $order ) {
					$rma = get_post_meta( $request_id, '_code', true );
					$message = sprintf(
						__( '<a href="admin.php?page=warranties&s=%s">RMA (%s)</a> has been created for %s', 'wc_warranty' ),
						$rma,
						$rma,
						get_the_title( $items[ $idx ]['product_id'] )
					);
					$order->add_order_note( $message );
				}

				// set the initial status and send the emails
				warranty_update_status( $request_id, 'new' );
			}

			$back   = 'admin.php?page=warranties';
			$back   = add_query_arg( 'updated', urlencode( __( 'Warranty request created', 'wc_warranty' ) ), $back );

			if ( ! empty( $errors ) ) {
				$back = add_query_arg( 'errors', wp_json_encode( $errors ), $back );
			}

			wp_redirect( $back );
			exit;
		}

		/**
		 * Handle file upload request and attach the uploaded file to the specified RMA request
		 */
		public function attach_shipping_label() {
			check_admin_referer( 'shipping_label_image', 'shipping_label_image_upload_nonce' );

			$request_id = $_POST['request_id'];
			$shipping_label_id = media_handle_upload( 'shipping_label_image', $request_id );

			if ( is_int( $shipping_label_id ) ) {
				add_post_meta( $request_id, '_warranty_shipping_label', $shipping_label_id );
			}

			if ( isset( $_POST['redirect' ] ) ) {
				$path = 'edit.php?post_type=shop_order&shipping_label_attached=1';
			} else {
				$path = 'admin.php?page=warranties&updated='. urlencode( __( 'Shipping label uploaded', 'wc_warranty' ) );
			}
			wp_redirect( $path );
			exit;
		}

		public function return_inventory() {

			check_admin_referer( 'warranty_return_inventory' );

			$request_id     = absint( $_REQUEST['id'] );

			warranty_return_product_stock( $request_id );
			warranty_update_request( $request_id, array( 'returned' => 'yes' ) );

			wp_redirect( 'admin.php?page=warranties&updated='. urlencode( __( 'Product stock returned', 'wc_warranty' ) ) );
			exit;
		}

		public function refund_item() {

			check_admin_referer( 'warranty_update' );

			$request_id = absint( $_REQUEST['id'] );
			$amount     = !empty( $_REQUEST['amount'] ) ? $_REQUEST['amount'] : null;
			$add_notice = isset( $_REQUEST['add_notice'] ) ? (bool)$_REQUEST['add_notice'] : false;

			$refund = warranty_refund_item( $request_id, $amount );

			if ( $add_notice ) {
				if ( is_wp_error( $refund ) ) {
					$message = $refund->get_error_message();
				} else {
					$message = __( 'Item marked as Refunded', 'wc_warranty' );
				}
				wc_add_notice( $message );
			}

			wp_redirect( 'admin.php?page=warranties&updated='. urlencode( $message ) );
			exit;

		}

		public function csv_import_fields( $fields ) {
			$fields['_warranty_type']       = 'meta:_warranty_type';
			$fields['_warranty_duration']   = 'meta:_warranty_duration';
			$fields['_warranty_unit']       = 'meta:_warranty_unit';
			$fields['_warranty']            = 'meta:_warranty';
			$fields['_warranty_label']      = 'meta:_warranty_label';

			return $fields;
		}

		/**
		 * Get the available actions for the warranty based on its status
		 *
		 * @param int   $id
		 * @param bool  $html Pass TRUE to return in an HTML snippet. Defaults to false
		 *
		 * @return array|string
		 */
		public static function get_warranty_actions( $id, $html = false ) {
			$request        = warranty_load( $id );
			$actions        = array();
			$manage_stock   = '';

			$returned           = get_option( 'warranty_returned_status', 'completed' );
			$completed_status   = warranty_get_completed_status();
			$product            = wc_get_product( $request['product_id'] );
			$request_type       = ( ! isset( $request['request_type'] ) ) ? 'replacement' : $request['request_type'];

			// nonces
			$nonces = array(
				'refund'            => wp_create_nonce( 'warranty_refund_item' ),
				'coupon'            => wp_create_nonce( 'warranty_send_coupon' ),
				'status'            => wp_create_nonce( 'warranty_update_status' ),
				'tracking'          => wp_create_nonce( 'warranty_tracking' ),
				'inventory'         => wp_create_nonce( 'warranty_return_inventory' )
			);

			if ( $product && $product->is_type('variation') ) {
				$product_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id();
				$stock = get_post_meta( $product_id, '_stock', true );

				if ($stock > 0)
					$manage_stock = 'yes';
			} else {
				$manage_stock = get_post_meta( $request['product_id'], '_manage_stock', true );
			}

			if ( $request['status'] == $returned && $manage_stock == 'yes' ) {
				if ( get_post_meta( $request['ID'], '_returned', true ) == 'yes' ) {
					$actions['inventory-return'] = array(
						'text'      => __('Stock returned', 'wc_warranty'),
						'disabled'  => true,
						'class'     => ''
					);
				} else {
					$actions['inventory-return'] = array(
						'text'      => __('Return Stock', 'wc_warranty'),
						'disabled'  => false,
						'data'      => array('security' => $nonces['inventory'], 'id' => $request['ID']),
						'class'     => 'warranty-inventory-return',
					);
				}
			}

			if ( 'completed' === $request['status'] ) {
				$refunded = get_post_meta( $request['ID'], '_refunded', true );

				if ( $refunded == 'yes' ) {
					$request_type = 'refund';
				}

				if ( $request_type == 'refund' ) {
					$item_amount        = warranty_get_item_amount( $request['ID'] );
					$amount_refunded    = get_post_meta( $request['ID'], '_refund_amount', true );

					$actions['item-refund'] = array(
						'text'      => __( 'Refund Item', 'wc_warranty' ),
						'disabled'  => false,
						'data'      => array( 'security' => $nonces['refund'], 'id' => $request['ID'], 'amount' => $item_amount ),
						'class'     => 'warranty-item-refund',
					);
				} elseif ( $request_type == 'coupon' ) {
					$actions['item-coupon'] = array(
						'text'      => __( 'Send Coupon', 'wc_warranty' ),
						'data'      => array( 'security' => $nonces['coupon'], 'id' => $request['ID'] ),
						'disabled'  => false,
						'class'     => 'warranty-item-coupon',
					);
				}
			}

			if ( !$html ) {
				return $actions;
			}

			$out = '';

			foreach ( $actions as $action_key => $action ) {
				$disabled = ($action['disabled']) ? 'disabled' : '';
				$data = '';

				if ( !empty( $action['data'] ) ) {
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

			warranty_delete_request( $_REQUEST['id'] );

			wp_redirect( 'admin.php?page=warranties&updated=' . urlencode( __( 'Warranty request deleted', 'wc_warranty' ) ) );
			exit;
		}

		/**
		 * Renders a print-friendly version of the warranty request
		 */
		public function warranty_print() {
			$request_id = absint( $_GET['request'] );

			check_admin_referer( 'warranty_print' );

			$warranty   = warranty_load( $request_id );
			$order_id   = $warranty['order_id'];
			$product_id = $warranty['product_id'];
			$order    = wc_get_order( $order_id );

			$form   = get_option( 'warranty_form' );
			$inputs = json_decode( $form['inputs'] );

			// customer
			$first_name = ( isset( $warranty['first_name'] ) ) ? $warranty['first_name'] : '';
			$last_name  = ( isset( $warranty['last_name'] ) ) ? $warranty['last_name'] : '';
			$email      = ( isset( $warranty['email'] ) ) ? $warranty['email'] : '';

			if ( ! $first_name || ! $last_name ) {
				$first_name = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_first_name' );
				$last_name  = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_last_name' );
				$email      = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_email' );
			}

			// product
			$product_name = ( isset( $warranty['product_name'] ) ) ? $warranty['product_name'] : '';

			if ( ! $product_name ) {
				$product = wc_get_product( $product_id );
				$product_title = get_the_title( $product_id );

				if ( is_object( $product ) && $product->is_type( 'variation' ) && version_compare( WC_VERSION, '3.0', '<' ) ) {
					$product_id = $product->id;
					$product_title = get_the_title( $product->variation_id );
				}

				$product_name   = $product_title . ' &ndash; #' . $product_id;
			}

			if ( isset( $warranty['qty'] ) ) {
				$product_name = $product_name . ' &times; ' . $warranty['qty'];
			}

			// tracking
			$tracking_html = '';
			$tracking = warranty_get_tracking_data( $warranty['ID'] );

			if ( empty( $tracking ) ) {
				$tracking_html = '-';
			} else {
				if ( ! empty( $tracking['store'] ) ) {
					$tracking_html .= '<p><strong>' . __('Store:', 'wc_warranty') . '</strong> ' . $warranty['return_tracking_code'];

					if ( ! empty( $warranty['return_tracking_provider'] ) ) {
						$tracking_html .= ' (' . ucwords( $warranty['return_tracking_provider'] ) . ')';
					}

					$tracking_html .= '</p>';
				}

				if ( ! empty( $tracking['customer'] ) ) {
					$tracking_html .= '<p><strong>' . __( 'Customer:', 'wc_warranty' ) . '</strong> ' . $warranty['tracking_code'];

					if ( ! empty( $warranty['tracking_provider'] ) ) {
						$tracking_html .= ' (' . ucwords( $warranty['tracking_provider'] ) . ')';
					}

					$tracking_html .= '</p>';
				}
			}

			// header
			$logo       = get_option( 'warranty_print_logo', false );
			$show_url   = get_option( 'warranty_print_url', false );

			// array to be passed to the template file
			$args = array(
				'logo'      => $logo,
				'show_url'  => $show_url,
				'tracking_html' => $tracking_html,
				'product_name'  => $product_name,
				'first_name'    => $first_name,
				'last_name'     => $last_name,
				'email'         => $email,
				'form'          => $form,
				'inputs'        => $inputs,
				'warranty'      => $warranty,
				'order'         => $order
			);

			wc_get_template( 'print.php', $args, 'wc-warranty', dirname( WooCommerce_Warranty::$plugin_file ) .'/templates/' );
		}

		/**
		 * Process bulk edit request
		 */
		public function bulk_edit() {
			$post   = array_map( 'stripslashes_deep', $_POST );

			// catch 'bulk_edit' action
			if ( ! empty( $post['bulk_edit'] ) ) {
				$product_ids = $post['post'];
				$default        = ! empty( $post['warranty_default_bulk'] ) ? $post['warranty_default_bulk'] : 'no';
				$type           = isset( $post['warranty_type_bulk'] ) ? $post['warranty_type_bulk'] : array();
				$label          = isset( $post['warranty_label_bulk'] ) ? $post['warranty_label_bulk'] : array();
				$warranty       = array();

				if ( $default == 'yes' ) {
					foreach ( $product_ids as $product_id ) {
						delete_post_meta( $product_id, '_warranty' );
					}

					wp_redirect( "admin.php?page=warranties-bulk-update&updated=1" );
					exit;
				}

				if ( $type == 'included_warranty' ) {
					$warranty = array(
						'type'      => 'included_warranty',
						'length'    => $post['included_warranty_length_bulk'],
						'value'     => $post['limited_warranty_length_value_bulk'],
						'duration'  => $post['limited_warranty_length_duration_bulk']
					);
				} elseif ( $type == 'addon_warranty' ) {
					$no_warranty= ( isset( $post['addon_no_warranty_bulk'] ) ) ? $post['addon_no_warranty_bulk'] : 'no';
					$amounts    = $post['addon_warranty_amount']['bulk'];
					$values     = $post['addon_warranty_length_value']['bulk'];
					$durations  = $post['addon_warranty_length_duration']['bulk'];
					$addons     = array();

					for ( $x = 0; $x < count( $amounts ); $x++ ) {
						if ( ! isset( $amounts[ $x ] ) || ! isset( $values[ $x ] ) || ! isset( $durations[ $x ] ) ) continue;

						$addons[] = array(
							'amount'    => $amounts[ $x ],
							'value'     => $values[ $x ],
							'duration'  => $durations[ $x ]
						);
					}

					$warranty = array(
						'type'                  => 'addon_warranty',
						'addons'                => $addons,
						'no_warranty_option'    => $no_warranty
					);
				} else {
					$warranty = array(
						'type'  => 'no_warranty'
					);
				}

				if ( isset( $post['warranty_label_bulk'] ) ) {
					foreach ( $product_ids as $product_id ) {
						update_post_meta( $product_id, '_warranty_label', $post['warranty_label_bulk'] );
					}
				}

				foreach ( $product_ids as $product_id ) {
					update_post_meta( $product_id, '_warranty', $warranty );
				}

				wp_redirect( "admin.php?page=warranties-bulk-update&updated=1" );
				exit;

			}

			$ids     = array_keys( $post['warranty_type'] );
			$type    = $post['warranty_type'];
			$label   = $post['warranty_label'];
			$default = $post['warranty_default'];

			foreach ( $ids as $id ) {
				$warranty = array();

				if ( ! empty( $default[ $id ] ) && $default[ $id ] == 'yes' ) {
					// skip
					delete_post_meta( $id, '_warranty' );
					continue;
				}

				if ( $type[ $id ] == 'no_warranty' ) {
					update_post_meta( $id, '_warranty', $warranty );
				} elseif ( $type[$id] == 'included_warranty' ) {
					$warranty = array(
						'type'      => 'included_warranty',
						'length'    => $post['included_warranty_length'][ $id ],
						'value'     => $post['limited_warranty_length_value'][ $id ],
						'duration'  => $post['limited_warranty_length_duration'][ $id ]
					);
					update_post_meta( $id, '_warranty', $warranty );
				} elseif ( $type[ $id ] == 'addon_warranty' ) {
					$no_warranty= ( isset( $post['addon_no_warranty'][ $id ] ) ) ? $post['addon_no_warranty'][ $id ] : 'no';
					$amounts    = ( isset( $post['addon_warranty_amount'][ $id ] ) ) ? $post['addon_warranty_amount'] : array();
					$values     = ( isset( $post['addon_warranty_length_value'][ $id ] ) ) ? $post['addon_warranty_length_value'] : array();
					$durations  = ( isset( $post['addon_warranty_length_duration'][ $id ] ) ) ? $post['addon_warranty_length_duration'] : array();
					$addons     = array();

					for ( $x = 0; $x < count( $amounts ); $x++ ) {
						if ( ! isset( $amounts[ $x ] ) || ! isset( $values[ $x ] ) || ! isset( $durations[ $x ] ) ) continue;

						$addons[] = array(
							'amount'    => $amounts[ $x ],
							'value'     => $values[ $x ],
							'duration'  => $durations[ $x ]
						);
					}

					$warranty = array(
						'type'                  => 'addon_warranty',
						'addons'                => $addons,
						'no_warranty_option'    => $no_warranty
					);
					update_post_meta( $id, '_warranty', $warranty );
				}

				if ( isset( $post['warranty_label'][ $id ] ) ) {
					update_post_meta( $id, '_warranty_label', $post['warranty_label'][ $id ] );
				}
			}

			wp_redirect( "admin.php?page=warranties-bulk-update&updated=1" );
			exit;
		}

		public function update_settings() {
			$post   = stripslashes_deep( $_POST );
			$tab    = !empty( $post['tab'] ) ? $post['tab'] : false;
			$fields = Warranty_Settings::get_settings_fields();

			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc_warranty_settings_save' ) ) {
				die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! $tab ) {
				die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! empty( $_POST['warranty_override_all'] ) && $_POST['warranty_override_all'] == 'yes' ) {
				WooCommerce_Warranty::clear_all_product_warranties();
			}

			if ( ! empty( $_POST['warranty_reset_statuses'] ) && $_POST['warranty_reset_statuses'] == '1' ) {
				warranty_reset_statuses();
			}

			if ( isset( $fields[ $tab ] ) ) {
				WC_Admin_Settings::save_fields( $fields[ $tab ] );
			}

			update_option( 'warranty_reset_statuses', 'no' );

			wp_redirect( 'admin.php?page=warranties-settings&tab='. $tab .'&updated=1' );
			exit;

		}

		public function count_shop_order_columns( $columns ) {
			self::$shop_order_columns = count( $columns ) - 1;

			return $columns;
		}

		/**
		 * Display the RMA button if the order has any outstanding warranty requests
		 *
		 * @param WC_Order $order
		 */
		public function order_inline_edit_actions( $order ) {
			if ( ! warranty_order_has_warranty_requests( WC_Warranty_Compatibility::get_order_prop( $order, 'id' ) ) ) {
				return;
			}
		?>
			<a class="button tips inline-rma dashicons-before dashicons-controls-repeat" data-tip="<?php echo wc_sanitize_tooltip( __( 'Manage Return', 'wc_warranty' ) ); ?>" href="#">
			</a>
		<?php
		}

		/**
		 * The template for inline-editing RMA requests
		 */
		public function order_inline_edit_template() {
			global $wp_query, $wp_list_table;
			$screen     = get_current_screen();
			$statuses   = warranty_get_statuses();

			// nonces
			$update_nonce = wp_create_nonce( 'warranty_update' );
			$coupon_nonce = wp_create_nonce( 'warranty_send_coupon' );

			$requests_str = array(
				'replacement'   => __('Replacement item', 'wc_warranty'),
				'refund'        => __('Refund', 'wc_warranty'),
				'coupon'        => __('Refund as store credit', 'wc_warranty')
			);

			if (
				$screen->id != 'edit-shop_order' ||
				empty( $wp_query->query_vars['post_type'] ) ||
				$wp_query->query_vars['post_type'] != 'shop_order' ||
				! have_posts()
			) {
				return;
			}
			?>
			<table style="display: none"><tbody id="inlineedit">
				<?php
				foreach ( $wp_query->posts as $post ) :
					$requests = get_posts( array(
						'post_type'     => 'warranty_request',
						'nopaging'      => true,
						'fields'        => 'ids',
						'meta_query'    => array(
							array(
								'key'   => '_order_id',
								'value' => $post->ID
							)
						)
					) );

					if ( empty( $requests ) ) {
						continue;
					}
				?>
				<tr id="inline-edit-post-<?php echo $post->ID; ?>"  class="inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-order">
					<td colspan="<?php echo self::$shop_order_columns; ?>">
						<?php
						foreach ( $requests as $request_id ):
							$request    = warranty_load( $request_id );

							$permissions= get_option( 'warranty_permissions', array() );
							$term       = wp_get_post_terms( $request['ID'], 'shop_warranty_status' );
							$status     = ( ! empty( $term ) ) ? $term[0] : $statuses[0];
							$me         = wp_get_current_user();
							$readonly   = true;
							$refunded   = get_post_meta( $request_id, '_refund_amount', true );
						?>
							<div class="warranty-request" id="warranty_request_<?php echo $request_id; ?>">
								<h2><?php printf( __( 'RMA %s', 'wc_warranty' ), $request['code'] ); ?></h2>

								<div class="warranty-update-message warranty-updated hidden"><p></p></div>

								<fieldset class="inline-edit-col-right">
									<div class="inline-edit-col">
										<h3><?php _e( 'Products', 'wc_warranty' ); ?></h3>

										<?php foreach ( $request['products'] as $request_product ): ?>
										<div class="field">
											<span class="label"><?php printf( __( '%s &times; %d', 'wc_warranty' ), get_the_title( $request_product['product_id'] ), $request_product['quantity'] ); ?></span>
										</div>
										<?php endforeach; ?>

										<h3><?php _e( 'RMA Data', 'wc_warranty' ); ?></h3>

										<div class="field">
											<span class="label"><?php _e( 'Request Type:', 'wc_warranty' ); ?></span>
											<span class="value"><?php echo $requests_str[ $request['request_type'] ]; ?></span>
										</div>
										<?php

										if ( $request['request_type'] == 'refund' && $refunded > 0 ):
										?>
											<div class="field">
												<span class="label"><?php _e( 'Refunded:', 'wc_warranty' ); ?></span>
												<span class="value"><?php echo wc_price( $refunded ); ?></span>
											</div>
										<?php
										endif;

										$form   = get_option( 'warranty_form' );
										$inputs = json_decode( $form['inputs'] );

										foreach ( $inputs as $input ) {
											$key   = $input->key;
											$type  = $input->type;
											$field = $form['fields'][ $input->key ];

											if ( $type == 'paragraph' ) {
												continue;
											}

											$value = get_post_meta( $request['ID'], '_field_' . $key, true );

											if ( is_array( $value ) ) {
												$value = implode( ',<br/>', $value );
											}

											if ( $type == 'file' && ! empty( $value ) ) {
												$wp_uploads = wp_upload_dir();
												$value      = '<a href="' . $wp_uploads['baseurl'] . $value . '">' . basename( $value ) . '</a>';
											}

											if ( empty( $value ) && ! empty( $item['reason'] ) && ! $this->row_reason_injected ) {
												$value = $item['reason'];
											}

											if ( ! $value ) {
												$value = '-';
											}
											?>
											<div class="field">
												<span class="label"><?php echo $field['name']; ?>:</span>
												<span class="value"><?php echo wp_kses_post( $value ); ?></span>
											</div>

										<?php
										}
										?>
									</div>
								</fieldset>

								<fieldset class="inline-edit-col-left">
									<div class="inline-edit-col">
										<?php

										if ( in_array( 'administrator', $me->roles ) ) {
											$readonly = false;
										} elseif ( ! isset( $permissions[ $status->slug ] ) || empty( $permissions[ $status->slug ] ) ) {
											$readonly = false;
										} elseif ( in_array( $me->ID, $permissions[ $status->slug ] ) ) {
											$readonly = false;
										}

										if ( $readonly ) {
											$status_content = ucfirst( $status->name );
										} else {
											$status_content = '<select class="warranty-status" name="status" id="status_'. $request['ID'] .'">';

											foreach ( $statuses as $_status ):
												$sel                = ( $status->slug == $_status->slug ) ? 'selected' : '';
												$status_content    .= '<option value="'. $_status->slug .'" '. $sel .'>'. ucfirst( $_status->name ) .'</option>';
											endforeach;

											$status_content .= '</select>';
														//<button class="button-primary warranty-update-status" type="button" title="Update" data-id="'. $request['ID'] .'" data-security="'. $nonces['status'] .'"><span>'. __('Update', 'wc_warranty') .'</span></button>';
										}
										?>

										<h3><?php _e( 'RMA Status', 'wc_warranty' ); ?></h3>
										<?php echo $status_content; ?>

										<div class="codes_form closeable">
											<h4><?php _e( 'Return shipping details', 'wc_warranty' ); ?></h4>
											<?php
											$shipping_label_id = get_post_meta( $request['ID'], '_warranty_shipping_label', true );

											if ( $shipping_label_id ) {
												$lnk = wp_get_attachment_url( $shipping_label_id );
												echo '<a href="'. $lnk .'"><strong>'. __( 'Download the Shipping Label', 'wc_warranty' ) .'</strong></a>';
											} else {
												?>
												<input name="shipping_label_image" id="shipping_label_<?php echo $request_id; ?>" class="shipping-label-url short-text" type="text" value="" />
												<input name="shipping_label_image_id" id="shipping_label_id_<?php echo $request_id; ?>" type="hidden" value="" />
												<input class="rma-upload-button button" type="button" data-id="<?php echo $request_id; ?>" data-uploader_title="<?php _e('Set Shipping Label', 'wc_warranty'); ?>" data-uploader_button_text="<?php _e('Set Shipping Label', 'wc_warranty'); ?>" value="<?php _e('Select Shipping Label', 'wc_warranty'); ?>" />
											<?php
											} // End final If Checking the attachment :)
											?>
										</div>
									</div>

									<div class="inline-edit-col">
										<h3><?php _e( 'Return Tracking Details', 'wc_warranty' ); ?></h3>

										<?php
										// if tracking code is being requested, notify the admin
										$class = 'hidden';
										if ( $request['request_tracking_code'] == 'y' && empty( $request['tracking_code'] ) ):
											$class = '';
										endif;
										?>
										<div class="codes_form closeable">
											<div class="wc-tracking-requested warranty-updated <?php echo $class; ?>"><p><?php _e( 'Tracking information requested from customer', 'wc_warranty' ); ?></p></div>

											<?php
											// Tracking code hasnt been requested yet
											if ($request['request_tracking_code'] != 'y'):
												?>
												<div class="request-tracking-div">
													<label>
														<input type="checkbox" name="request_tracking" value="1" />
														<strong><?php _e( 'Request tracking code from the Customer', 'wc_warranty' ); ?></strong>
													</label>
												</div>
											<?php
											else: // tracking code requested
												// if tracking code is not empty, it has already been provided
												if ( ! empty( $request['tracking_code'] ) ) {
													echo '<strong>'. __( 'Customer Provided Tracking', 'wc_warranty' ) .':</strong>&nbsp;';

													if ( ! empty( $request['tracking_provider'] ) ) {
														$all_providers = array();

														foreach ( WooCommerce_Warranty::get_providers() as $providers ) {
															foreach ( $providers as $provider => $format ) {
																$all_providers[ sanitize_title( $provider ) ] = $format;
															}
														}

														$provider   = $request['tracking_provider'];
														$link       = $all_providers[ $provider ];
														$link       = str_replace( '%1$s', $request['tracking_code'], $link );
														$link       = str_replace( '%2$s', '', $link );
														printf( __( '%s via %s (<a href="' . $link . '" target="_blank">Track Shipment</a>)', 'wc_warranty' ), $request['tracking_code'], $provider, $link );
													} else {
														echo $request['tracking_code'];
													}
												}
											endif;
											?>
										</div>

										<div class="codes_form closeable">
											<?php
											if ( ! empty( $request['return_tracking_provider'] ) ) : ?>
												<p>
													<label for="return_tracking_provider_<?php echo $request['ID']; ?>"><strong><?php _e('Shipping Provider', 'wc_warranty'); ?></strong></label>
													<select class="return_tracking_provider" name="return_tracking_provider" id="return_tracking_provider_<?php echo $request['ID']; ?>">
														<?php
														foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
															echo '<optgroup label="' . $provider_group . '">';
															foreach ( $providers as $provider => $url ) {
																$selected = ( sanitize_title( $provider ) == $request['return_tracking_provider'] ) ? 'selected' : '';
																echo '<option value="' . sanitize_title( $provider ) . '" '. $selected .'>' . $provider . '</option>';
															}
															echo '</optgroup>';
														}
														?>
													</select>
												</p>
												<p>
													<label for="return_tracking_code_<?php echo $request['ID']; ?>"><strong><?php _e( 'Tracking details', 'wc_warranty' ); ?></strong></label>
													<input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo $request['ID']; ?>" value="<?php echo $request['return_tracking_code']; ?>" placeholder="<?php _e( 'Enter the shipment tracking number', 'wc_warranty' ); ?>" />
													<span class="description"><?php _e( 'Shipping Details/Tracking', 'wc_warranty' ); ?></span>
												</p>
											<?php else: ?>
												<p>
													<label for="return_tracking_code_<?php echo $request['ID']; ?>"><strong><?php _e( 'Tracking details', 'wc_warranty' ); ?></strong></label>
													<input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo $request['ID']; ?>" value="<?php echo $request['return_tracking_code']; ?>" placeholder="<?php _e( 'Enter the shipment tracking number', 'wc_warranty' ); ?>" />
													<span class="description"><?php _e( 'Shipping Details/Tracking', 'wc_warranty' ); ?></span>
												</p>
											<?php endif; ?>
										</div>
									</div>
								</fieldset>

								<fieldset class="inline-edit-col-center">
									<div class="inline-edit-col">
										<h3><?php _e( 'Actions', 'wc_warranty' ); ?></h3>

										<div class="actions-block">
										<?php
										echo  self::get_warranty_actions( $request['ID'], true );
										?>
										</div>
									</div>
								</fieldset>
								<br class="clear"/>

								<div class="submit inline-edit-save">
									<input type="button" class="button close_tr" value="<?php _e( 'Close', 'wc_warranty' ); ?>" />

									<div class="alignright">
										<a class="button-primary" target="_blank" href="<?php echo wp_nonce_url( 'admin-post.php?action=warranty_print&request='. $request['ID'], 'warranty_print' ); ?>"><?php _e( 'Print', 'wc_warranty' ); ?></a>
										<input type="button" class="button-primary rma-update" data-id="<?php echo $request_id; ?>" data-security="<?php echo $update_nonce; ?>" value="<?php _e( 'Update', 'wc_warranty' ); ?>" />
										<input type="button" class="button-secondary warranty-trash" data-id="<?php echo $request_id; ?>" data-security="<?php echo wp_create_nonce( 'warranty_delete' ); ?>" value="<?php _e( 'Delete', 'wc_warranty' ); ?>" />
									</div>
								</div>

								<?php
								if ( $request['request_type'] == 'refund' ):
									$item_amount    = warranty_get_item_amount( $request_id );
									$available      = max(0, $item_amount - $refunded);
								?>
									<div id="warranty-refund-modal-<?php echo $request_id; ?>" style="display:none;">
										<table class="form-table">
											<tr>
												<th><span class="label"><?php _e( 'Amount refunded:', 'wc_warranty' ); ?></span></th>
												<td><span class="value"><?php echo wc_price( $refunded ); ?></span></td>
											</tr>
											<tr>
												<th><span class="label"><?php _e( 'Item cost:', 'wc_warranty' ); ?></span></th>
												<td><span class="value"><?php echo wc_price( $item_amount ); ?></span></td>
											</tr>
											<tr>
												<th><span class="label"><?php _e( 'Refund amount:', 'wc_warranty' ); ?></span></th>
												<td>
													<?php echo get_woocommerce_currency_symbol(); ?>
													<input type="text" class="input-short amount" value="<?php echo esc_attr( $available ); ?>" size="5" />
												</td>
											</tr>
										</table>

										<p class="submit alignright">
											<input
												type="button"
												class="warranty-process-refund button-primary"
												value="<?php _e( 'Process Refund', 'wc_warranty' ); ?>"
												data-id="<?php echo $request_id; ?>"
												data-security="<?php echo $update_nonce; ?>"
											/>
										</p>
									</div>
								<?php elseif ( $request['request_type'] == 'coupon' ):
									$item_amount    = warranty_get_item_amount( $request_id );
									?>
									<div id="warranty-coupon-modal-<?php echo $request_id; ?>" style="display:none;">
										<table class="form-table">
											<tr>
												<th><span class="label"><?php _e( 'Amount refunded:', 'wc_warranty' ); ?></span></th>
												<td><span class="value"><?php echo wc_price( $refunded ); ?></span></td>
											</tr>
											<tr>
												<th><span class="label"><?php _e( 'Item cost:', 'wc_warranty' ); ?></span></th>
												<td><span class="value"><?php echo wc_price( $item_amount ); ?></span></td>
											</tr>
											<tr>
												<th><span class="label"><?php _e( 'Coupon amount:', 'wc_warranty' ); ?></span></th>
												<td>
													<?php echo get_woocommerce_currency_symbol(); ?>
													<input type="text" class="input-short amount" value="<?php echo esc_attr( $item_amount ); ?>" size="5" />
												</td>
											</tr>
										</table>

										<p class="submit alignright">
											<input
												type="button"
												class="warranty-process-coupon button-primary"
												value="<?php _e( 'Send Coupon', 'wc_warranty' ); ?>"
												data-id="<?php echo $request_id; ?>"
												data-security="<?php echo $coupon_nonce; ?>"
												/>
										</p>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody></table>
			<?php
		}

		/**
		 * Add warranty data to manually added line items
		 *
		 * @param array $item
		 * @param int   $item_id
		 */
		public function add_line_item_warranty_meta( $item_id, $item ) {
			$product_id     = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$warranty       = warranty_get_product_warranty( $product_id );
			$warranty_label = $warranty['label'];
			$warranty_index = null;

			if ( $warranty['type'] == 'no_warranty' ) {
				return;
			}

			if ( $warranty['type'] == 'addon_warranty' ) {
				wc_add_order_item_meta( $item_id, '_item_warranty_needs_index', 1 );
			} elseif ( $warranty['type'] == 'included_warranty' ) {
				if ( $warranty['length'] == 'lifetime' ) {
					wc_add_order_item_meta( $item_id, $warranty_label, __( 'Lifetime', 'wc_warranty' ) );
				} elseif ( $warranty['length'] == 'limited' ) {
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
		 * @param int           $item_id
		 * @param array         $item
		 * @param WC_Product    $product
		 */
		public function maybe_render_addon_options( $item_id, $item, $product ) {
			global $wc_warranty;

			$warranty       = warranty_get_product_warranty( $product ? $product->get_id() : null );
			$needs_index    = wc_get_order_item_meta( $item_id, '_item_warranty_needs_index', true );

			if ( $warranty['type'] == 'addon_warranty' && $needs_index ) {
				$addons = $warranty['addons'];
				?>
				<table cellspacing="0" class="display_meta">
					<tr>
						<th><?php echo wp_kses_post( $warranty['label'] ); ?>:</th>
						<td>
							<select name="warranty_index[<?php echo $item_id; ?>]">
							<?php
								if ( isset( $warranty['no_warranty_option'] ) && 'yes' == $warranty['no_warranty_option'] ) {
									echo '<option value="-1">' . __( 'No warranty', 'wc_warranty' ) . '</option>';
								}
							?>
							<?php foreach ( $addons as $idx => $addon ): ?>
								<option value="<?php echo $idx; ?>"><?php echo $wc_warranty->get_warranty_string( $addon['value'], $addon['duration'] ); ?></option>
							<?php endforeach ?>
							</select>
						</td>
					</tr>
				</table>
				<?php
			}
		}

		/**
		 * Display an order item's warranty data
		 *
		 * @param int           $item_id
		 * @param array         $item
		 * @param WC_Product    $product
		 */
		public function render_order_item_warranty( $item_id, $item, $product ) {
			global $post;

			if ( $item['type'] != 'line_item' ) {
				return;
			}

			$warranty = wc_get_order_item_meta( $item_id, '_item_warranty', true );

			if ( $post ) {
				$order_id = $post->ID;
			} elseif ( isset( $_POST['order_id'] ) ) {
				$order_id = $_POST['order_id'];
			}

			if ( $warranty && ! empty( $order_id ) ) {
				include WooCommerce_Warranty::$base_path . '/templates/admin/order-item-warranty.php';
			}
		}

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
		 * @param int           $post_id
		 * @param WP_Post|array $post
		 */
		public function save_line_item_warranty_indices( $post_id, $post ) {
			$warranty_index = array();

			if ( isset( $_POST['warranty_index'] ) && is_array( $_POST['warranty_index'] ) ) {
				$warranty_index = $_POST['warranty_index'];
			} elseif ( is_array($post) && !empty( $post['warranty_index'] ) && is_array( $post['warranty_index'] ) ) {
				$warranty_index = $post['warranty_index'];
			}

			foreach ( $warranty_index as $item_id => $index ) {

				if ( wc_get_order_item_meta( $item_id, '_item_warranty_needs_index', true ) ) {
					wc_add_order_item_meta( $item_id, '_item_warranty_selected', $index );
					wc_add_order_item_meta( $item_id, '_item_warranty_manually_added', true );
					wc_delete_order_item_meta( $item_id, '_item_warranty_needs_index' );

					$warranty = wc_get_order_item_meta( $item_id, '_item_warranty', true );

					if ( $warranty && $warranty['type'] == 'addon_warranty' ) {
						$addon  = isset( $warranty['addons'][ $index ] ) ? $warranty['addons'][ $index ] : false;

						if ( $addon && $addon['amount'] > 0 ) {
							$total = wc_get_order_item_meta( $item_id, '_line_total', true );
							$total += $addon['amount'];
							wc_update_order_item_meta( $item_id, '_line_total', $total );
						}
					}
				}
			}
		}

		public function add_addon_price_to_line_item( $order_id, $items ) {
			$added = 0;

			if ( ! empty( $items['order_item_id'] ) ) {
				foreach ( $items['order_item_id'] as $item_id ) {
					$index = wc_get_order_item_meta( $item_id, '_item_warranty_selected', true );
					$manually_added = wc_get_order_item_meta( $item_id, '_item_warranty_manually_added', true );

					if ( ! is_numeric( $index ) || !$manually_added ) {
						continue;
					}

					wc_delete_order_item_meta( $item_id, '_item_warranty_manually_added' );
					$warranty = wc_get_order_item_meta( $item_id, '_item_warranty', true );

					if ( $warranty && $warranty['type'] == 'addon_warranty' ) {
						$addon  = isset( $warranty['addons'][ $index ] ) ? $warranty['addons'][ $index ] : false;

						if ( $addon && $addon['amount'] > 0 ) {
							$total      = $items['line_total'][ $item_id ];
							$subtotal   = $items['line_subtotal'][ $item_id ];

							$subtotal += $addon['amount'];
							$total += $addon['amount'];
							$added += $addon['amount'];

							wc_update_order_item_meta( $item_id, '_line_total', $total );
							wc_update_order_item_meta( $item_id, '_line_subtotal', $subtotal );
						}
					}
				}
			}
		}
	}
endif;

return new Warranty_Admin();
