<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Implements premium features of YIT WooCommerce Watermark plugin
 *
 * @class   YITH_WC_Watermark
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Watermark_Premium' ) ) {

	class YITH_WC_Watermark_Premium {

		/**
		 * @var YITH_WC_Watermark_Premium single instance of class
		 */
		protected static $_instance;
		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel;
		/**
		 * @var string Yith WooCommerce Watermark panel page
		 */
		protected $_panel_page = 'yith_ywcwat_panel';

		/**
		 * YITH_WC_Watermark_Premium constructor.
		 */
		public function __construct() {
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YWCWAT_DIR . '/' . basename( YWCWAT_FILE ) ), array(
				$this,
				'action_links'
			) );
			//Add row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			//Add Yith Watermark menu
			add_action( 'admin_menu', array( $this, 'add_ywcwat_menu' ), 5 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20 );

			add_action( 'woocommerce_admin_field_custom-button', array( $this, 'show_backup_btn' ) );
			add_action( 'woocommerce_admin_field_watermark-apply', array( $this, 'show_watermark_apply_field' ) );
			add_action( 'woocommerce_admin_field_watermark-insert-new', array(
				$this,
				'show_watermark_insert_new_field'
			) );
			add_action( 'woocommerce_admin_field_watermark-select', array( $this, 'show_watermark_select_field' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_product_meta_boxes' ), 10 );
			add_filter( 'product_type_options', array( $this, 'add_product_watermark_option' ) );
			add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'print_watermark_panels' ), 98 );
			add_action( 'woocommerce_admin_process_product_object', array(
				$this,
				'save_product_watermark_meta'
			), 20, 1 );
			//AJAX SECTION


			//add ajax action for load admin template
			add_action( 'wp_ajax_add_new_watermark_admin', array( $this, 'add_new_watermark_admin' ) );
			add_action( 'wp_ajax_add_new_product_watermark_admin', array( $this, 'add_new_product_watermark_admin' ) );
			//remove single watermark field
			add_action( 'wp_ajax_remove_watermark', array( $this, 'remove_watermark_admin' ) );
			//preview watermark
			add_action( 'wp_ajax_preview_watermark', array( $this, 'preview_watermark' ) );
			//add ajax action for apply all watermark
			add_action( 'wp_ajax_apply_all_watermark', array( $this, 'apply_all_watermark' ) );
			add_action( 'wp_ajax_reset_watermark', array( $this, 'reset_watermark' ) );

			add_action( 'wp_ajax_save_watermark_on_single_product', array(
				$this,
				'save_watermark_on_single_product'
			) );


			add_action( 'ywcwat_build_watermark_image', array( $this, 'build_watermark_image' ), 10, 2 );
			add_action( 'ywcwat_build_watermark_text', array( $this, 'build_watermark_text' ), 10, 2 );

		}

		/** return single instance of class
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_WC_Watermark_Premium
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/** Register plugins for activation tab
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YWCWAT_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YWCWAT_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWCWAT_INIT, YWCWAT_SECRET_KEY, YWCWAT_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( YWCWAT_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWCWAT_SLUG, YWCWAT_INIT );
		}

		/**
		 * load plugin fw
		 * @author YITH
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$is_premium = defined( 'YWCWAT_INIT' );
			$links      = yith_add_action_links( $links, $this->_panel_page, $is_premium );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCWAT_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']    = YWCWAT_SLUG;
				$new_row_meta_args['premium'] = true;

			}

			return $new_row_meta_args;

		}

		/* Add a panel under YITH Plugins tab
			*
			* @return   void
			* @since    1.0
			* @author   Andrea Grillo <andrea.grillo@yithemes.com>
			* @use     /Yit_Plugin_Panel class
			* @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_ywcwat_menu() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters( 'ywcwat_add_premium_tab', array(
				'general-settings' => __( 'Settings', 'yith-woocommerce-watermark' ),
				'watermark-list'   => __( 'Active Watermark', 'yith-woocommerce-watermark' )
			) );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Watermark', 'yith-woocommerce-watermark' ),
				'menu_title'       => 'Watermark',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCWAT_DIR . '/plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * include style and scripts
		 * @author YITH
		 * @since 1.2.0
		 */
		public function admin_enqueue_scripts() {

			wp_register_script( 'ywcwat_panel_admin', YWCWAT_ASSETS_URL . 'js/' . yit_load_js_file( 'ywcwat_admin.js' ), array(
				'jquery',
				'jquery-ui-progressbar',
				'jquery-ui-dialog',
				'yith-enhanced-select'
			), YWCWAT_VERSION, true );
			wp_register_script( 'ywcwat_product_admin', YWCWAT_ASSETS_URL . 'js/' . yit_load_js_file( 'ywcwat_admin_single_product.js' ), array(
				'jquery',
				'wp-color-picker',
				'jquery-ui-dialog'
			), YWCWAT_VERSION, true );
			wp_register_style( 'ywcwat_admin', YWCWAT_ASSETS_URL . 'css/ywcwat_admin.css', array(), YWCWAT_VERSION );

			$script_args = array(
				'ajax_url'                => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'attach_id'               => $this->get_ids_attach(),
				'messages'                => array(
					'complete_single_task'   => __( 'The watermark has been applied to', 'yith-woocommerce-watermark' ),
					'single_product'         => __( 'Product', 'yith-woocommerce-watermark' ),
					'on'                     => __( 'on', 'yith-woocommerce-watermark' ),
					'more_product'           => __( 'Products', 'yith-woocommerce-watermark' ),
					'complete_all_task'      => __( 'Completed', 'yith-woocommerce-watermark' ),
					'log_message'            => __( 'Attach id ', 'yith-woocommerce-watermark' ),
					'reset_confirm'          => __( 'Images will be restored, are you sure? ', 'yith-woocommerce-watermark' ),
					'singular_success_image' => __( 'Image has been deleted', 'yith-woocommerce-watermark' ),
					'plural_success_image'   => __( 'Images have been deleted', 'yith-woocommerce-watermark' ),
					'singular_error_image'   => __( 'Image has not been deleted', 'yith-woocommerce-watermark' ),
					'plural_error_image'     => __( 'Images have not been deleted', 'yith-woocommerce-watermark' ),
					'error_messages'         => $this->get_messages(),
					'shop_sizes'             => yith_watermark_get_image_size(),

				),
				'label_position'          => array(
					'top_left'      => __( 'TOP LEFT', 'yith-woocommerce-watermark' ),
					'top_center'    => __( 'TOP CENTER', 'yith-woocommerce-watermark' ),
					'top_right'     => __( 'TOP RIGHT', 'yith-woocommerce-watermark' ),
					'middle_left'   => __( 'LEFT CENTER', 'yith-woocommerce-watermark' ),
					'middle_center' => __( 'CENTER', 'yith-woocommerce-watermark' ),
					'middle_right'  => __( 'RIGHT CENTER', 'yith-woocommerce-watermark' ),
					'bottom_left'   => __( 'BOTTOM LEFT', 'yith-woocommerce-watermark' ),
					'bottom_center' => __( 'BOTTOM CENTER', 'yith-woocommerce-watermark' ),
					'bottom_right'  => __( 'BOTTOM RIGHT', 'yith-woocommerce-watermark' )
				),
				'delete_single_watermark' => array(
					'confirm_delete_watermark' => __( 'Do you want to delete this watermark ?', 'yith-woocommerce-watermark' )
				),
				'gd_version'              => $this->get_gd_version(),
				'block_loader'            => YWCWAT_ASSETS_URL . '/images/block-loader.gif',
				'actions'                 => array(
					'apply_all_watermark'              => 'apply_all_watermark',
					'reset_watermark'                  => 'reset_watermark',
					'change_thumbnail_image'           => 'change_thumbnail_image',
					'remove_watermark'                 => 'remove_watermark',
					'preview_watermark'                => 'preview_watermark',
					'remove_product_watermark'         => 'remove_product_watermark',
					'save_watermark_on_single_product' => 'save_watermark_on_single_product'
				)
			);

			wp_localize_script( 'ywcwat_panel_admin', 'ywcwat_params', $script_args );
			wp_localize_script( 'ywcwat_product_admin', 'ywcwat_product_param', $script_args );

			if ( isset( $_GET['page'] ) && 'yith_ywcwat_panel' == $_GET['page'] ) {
				wp_enqueue_style( 'ywcwat_admin' );
				wp_enqueue_script( 'ywcwat_panel_admin' );

			}

			global $post;

			if ( isset( $post ) && get_post_type( $post ) == 'product' ) {
				wp_enqueue_style( 'ywcwat_admin' );
				wp_enqueue_script( 'ywcwat_product_admin' );
			}
		}

		/**
		 * @author YITHEMES
		 *
		 * @param array $option
		 *
		 * @since 1.0.9
		 */
		public function show_backup_btn( $option ) {

			wc_get_template( 'admin/custom-button.php', array(), '', YWCWAT_TEMPLATE_PATH );
		}

		public function show_watermark_apply_field( $option ) {
			wc_get_template( 'admin/watermark-apply.php', array( 'option' => $option ), '', YWCWAT_TEMPLATE_PATH );
		}

		public function show_watermark_insert_new_field( $option ) {
			wc_get_template( 'admin/watermark-insert-new.php', array( 'option' => $option ), '', YWCWAT_TEMPLATE_PATH );

		}

		public function show_watermark_select_field( $option ) {
			wc_get_template( 'admin/watermark-select.php', array( 'option' => $option ), '', YWCWAT_TEMPLATE_PATH );
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.1.0
		 *
		 * @param int $message_id
		 *
		 * @return mixed
		 */
		public function get_messages( $message_id = - 1 ) {
			/*
			 * Error codes
			 * 0=> ok
			 */
			$messages = array(
				'watermark_created' => __( 'Watermark Created', 'yith-woocommerce-watermark' ),
				'empty_path'        => __( 'Empty Path', 'yith-woocommerce-watermark' ),
				'image_resize'      => __( 'Error when saving resize image', 'yith-woocommerce-watermark' ),
				'load_editor'       => __( 'Can\'t load the image editor', 'yith-woocommerce-watermark' ),
				'error_on_create'   => __( 'Error when creating watermark', 'yith-woocommerce-watermark' ),
				'size_name_empty'   => __( 'Image size doesn\'t exist', 'yith-woocommerce-watermark' )
			);

			return ( $message_id == - 1 ) ? $messages : ( isset( $messages[ $message_id ] ) ? $messages[ $message_id ] : false );
		}

		/**
		 * return gd version
		 * @author YITH
		 * @since 1.0.0
		 * @return mixed
		 */
		public function get_gd_version() {
			$gd_version = gd_info();
			preg_match( '/\d/', $gd_version['GD Version'], $match );
			$gd_ver = $match[0];

			return $gd_ver;
		}


		/** return new watermark metabox in admin
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_new_watermark_admin() {

			if ( isset( $_REQUEST['ywcwat_addnewwat'] ) && isset( $_REQUEST['ywcwat_unique_id'] ) ) {

				$params = array(
					'option_id'   => 'ywcwat_watermark_select',
					'current_row' => $_REQUEST['ywcwat_addnewwat'],
					'unique_id'   => $_REQUEST['ywcwat_unique_id'],

				);

				$params['params'] = $params;
				ob_start();
				wc_get_template( 'single-watermark-template.php', $params, YWCWAT_TEMPLATE_PATH, YWCWAT_TEMPLATE_PATH );
				$template = ob_get_contents();
				ob_end_clean();
				wp_send_json( array( 'result' => $template ) );
			}
		}


		/**
		 * delete a single watermark field
		 * @author Salvatore Strano
		 * @since 1.1.0
		 */

		public function remove_watermark_admin() {

			$update = false;
			if ( isset( $_REQUEST['ywcwat_unique_id'] ) ) {

				$all_watermarks      = get_option( 'ywcwat_watermark_select', array() );
				$watermark_to_delete = $_REQUEST['ywcwat_unique_id'];


				foreach ( $all_watermarks as $key => $watermark ) {

					if ( $watermark['ywcwat_id'] == $watermark_to_delete ) {
						unset( $all_watermarks[ $key ] );
						break;
					}
				}

				$update = update_option( 'ywcwat_watermark_select', $all_watermarks );


			}

			wp_send_json( array( 'result' => $update ) );
		}

		/** return new watermark metabox in edit product
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_new_product_watermark_admin()
		{

			if( isset( $_REQUEST['ywcwat_product_addnewwat'] ) ) {

				$optionid = $_REQUEST['ywcwat_product_option_id'];
				$current_row = $_REQUEST['ywcwat_product_addnewwat'];

				$params = array(
					'option_id' => $optionid,
					'current_row' => $current_row
				);

				$params['params'] = $params;
				ob_start();
				wc_get_template( 'metaboxes/single-product-watermark-template.php', $params, YWCWAT_TEMPLATE_PATH, YWCWAT_TEMPLATE_PATH );
				$template = ob_get_contents();
				ob_end_clean();

				wp_send_json( array( 'result' => $template ) );

			}
		}

		/** call ajax, apply watermark to single attach
		 * @author YITH
		 * @since 1.2.0
		 */
		public function apply_all_watermark() {
			if ( isset( $_REQUEST['ywcwat_attach_id'] ) ) {

				$attach_id = $_REQUEST['ywcwat_attach_id'];

				$results = $this->apply_all_watermark_for_attachment( $attach_id );
				wp_send_json( $results );
			}
		}

		/**when change featured image in edit product, apply watermark
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function save_watermark_on_single_product() {
			$product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;

			$product           = wc_get_product( $product_id );
			$attach_ids        = array();
			$is_custom_enabled = yit_get_prop( $product, '_enable_watermark' ) == 'yes';

			$custom_watermark = yit_get_prop( $product, '_ywcwat_product_watermark', true );
			$watermarks       = array();

			if ( $is_custom_enabled && ! empty( $custom_watermark ) ) {
				$watermarks = $custom_watermark;
			}

			if ( $product ) {

				$attach_ids[] = get_post_thumbnail_id( $product_id );

				if ( $product->is_type( 'variable' ) ) {
					$child_ids = $product->get_children();

					foreach ( $child_ids as $child_id ) {
						$attach_ids[] = get_post_thumbnail_id( $child_id );
					}
				}

				$gallery_ids = $product->get_gallery_image_ids();
				$attach_ids  = apply_filters( 'ywcwat_product_attach_ids', array_merge( $attach_ids, $gallery_ids ), $product );
				$results     = array();

				foreach ( $attach_ids as $attach_id ) {
					$results[] = $this->apply_all_watermark_for_attachment( $attach_id, $watermarks );

				}

				$this->regenerate_thumbnail( $attach_ids );

			}
		}

		/**
         * regenerate thumbnail
		 * @param array $attach_ids
		 */
		public function regenerate_thumbnail( $attach_ids ){

		    if( apply_filters( 'ywcwat_regenerate_thumbnail', false ) ) {

		        foreach ( $attach_ids as $id ) {
			        $fullsizepath = get_attached_file( $id );

			        if ( false !== $fullsizepath && @file_exists( $fullsizepath ) ) {
				        set_time_limit( 30 );
				        wp_update_attachment_metadata( $id, wp_generate_attachment_metadata_custom( $id, $fullsizepath ) );

			        }
		        }
		    }
        }

		/**
		 * @author YITH
		 * @since 1.3.0
		 */
		public function apply_all_watermark_for_attachment( $attach_id, $watermarks = array() ) {
			$results = array();

			if ( apply_filters( 'ywcwat_skip_this_attached_id', false, $attach_id ) ) {
				return $results;
			}

			$fullsizepath = get_attached_file( $attach_id );
			$backupfile   = ywcwat_backup_file_name( $fullsizepath );
			$size_types   = array_keys( yith_watermark_get_image_size() );

			$all_watermarks = empty( $watermarks ) ? get_option( 'ywcwat_watermark_select' ) : $watermarks;


			//if file exist

			if ( file_exists( $fullsizepath ) ) {

				if ( ! file_exists( $backupfile ) ) {
					ywcwat_backup_file( $fullsizepath );
				}
				$watermark_need_backup = array();

				foreach ( $size_types as $size ) {

					$watermarks_size = array_filter( $all_watermarks, function ( $v ) USE ( $size ) {
						return $v['ywcwat_watermark_sizes'] == $size;
					} );


					if ( $watermarks_size ) {
						foreach ( $watermarks_size as $watermark_size ) {

							if ( ! isset( $watermark_need_backup[ $watermark_size['ywcwat_watermark_sizes'] ] ) ) {
								$watermark_need_backup[ $watermark_size['ywcwat_watermark_sizes'] ] = true;
								$need_backup                                                        = true;
							} else {
								$need_backup = false;
							}

							$watermark_created = $this->create_watermark( $backupfile, $fullsizepath, $attach_id, $watermark_size, $need_backup );

							$results[] = array( $watermark_created, $size );
						}
					}
				}

			}


			return $results;
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.1.0
		 *
		 * @param string $backup_path
		 * @param string $path
		 * @param int $attachment_id
		 * @param array $watermark
		 *
		 * @return string
		 *
		 */
		public function create_watermark( $backup_path, $path, $attachment_id, $watermark, $need_backup = true ) {

			$size_name = isset( $watermark['ywcwat_watermark_sizes'] ) ? $watermark['ywcwat_watermark_sizes'] : false;
			$result    = 'size_name_empty';
			if ( $size_name ) {
				list( $error_code, $thumbnail_path ) = $this->create_image_resized( $backup_path, $path, $attachment_id, $size_name, $need_backup );

				if ( $error_code === 0 ) {

					$result = $this->save_image_with_watermark( $thumbnail_path, $attachment_id, $watermark );
				} else {
					$result = $error_code;
				}
			}

			return $result;
		}

		/** resize the original image and call save image with watermark
		 * @author Salvatore Strano
		 * @since 1.1.0
		 *
		 * @param string $path
		 * @param string $size_name
		 * @param int $attach_id
		 *
		 * @return array
		 */
		public function create_image_resized( $backup_path, $path, $attach_id, $size_name, $need_backup = true ) {
			$new_path   = '';
			$error_code = 0;
			if ( ! empty( $path ) ) {

				if ( $size_name == 'full' ) {

					if ( $need_backup ) {
						copy( $backup_path, $path );
					}
					$new_path = $path;

				} else {

					$img = wp_get_image_editor( $backup_path );

					if ( ! is_wp_error( $img ) ) {
						$size = wc_get_image_size( $size_name );

						$crop = isset( $size['crop'] ) && $size['crop'] == 1;
						$img->resize( $size['width'], $size['height'], $crop );

						$info = pathinfo( $path );

						$dir       = $info['dirname'];
						$ext       = $info['extension'];
						$suffix    = $img->get_suffix();
						$name      = wp_basename( $path, ".$ext" );
						$dest_file = trailingslashit( $dir ) . "{$name}-{$suffix}.{$ext}";

						if ( $need_backup ) {
							$saved = $img->save( $dest_file );

							if ( is_wp_error( $saved ) ) {
								$error_code = 'image_resize';
							} else {
								$new_path = $saved['path'];
							}
						} else {
							$new_path = $dest_file;
						}
					} else {
						$error_code = 'load_editor';
					}
				}
			} else {

				$error_code = 'empty_path';
			}

			return array( $error_code, $new_path );

		}

		/** save image+watermark
		 * overridden
		 * @author YITHEMES
		 *
		 * @param $filepath
		 *
		 * @return string
		 */
		public function save_image_with_watermark( $thumbnail_path, $attachment_id, $watermark ) {

			$mime_type      = strtolower( pathinfo( $thumbnail_path, PATHINFO_EXTENSION ) );
			$original_image = $this->createimagefrom( $thumbnail_path, $mime_type );
			$original_image = $this->get_truecolor_image( $original_image );

			$action             = ( empty( $watermark['ywcwat_watermark_type'] ) || $watermark['ywcwat_watermark_type'] == 'type_img' ) ? 'image' : 'text';
			$watermark_category = isset( $watermark['ywcwat_watermark_category'] ) ? $watermark['ywcwat_watermark_category'] : array();


			if ( ! empty( $watermark_category ) && function_exists( 'ywcwat_get_product_id_by_attach' ) ) {

				$products = ywcwat_get_product_id_by_attach( $attachment_id );

				$watermark_category = ! is_array( $watermark_category ) ? explode( ',', $watermark_category ) : $watermark_category;
				foreach ( $products as $product ) {

					$categories = wp_get_post_terms( $product->ID, 'product_cat', array( "fields" => "ids" ) );

					if ( count( array_intersect( $watermark_category, $categories ) ) > 0 ) {
						do_action( 'ywcwat_build_watermark_' . $action, $original_image, $watermark );
					}
				}
			} else {

				do_action( 'ywcwat_build_watermark_' . $action, $original_image, $watermark );

			}

			imagesavealpha( $original_image, true );

			$quality_img = get_option( 'ywcwat_quality_jpg', 100 );
			$result      = $this->generateimagefrom( $original_image, $thumbnail_path, $mime_type, $quality_img );
			imagedestroy( $original_image );
			if ( $result ) {

				return 'watermark_created';
			} else {
				return 'error_on_create';
			}

		}

		/**@author Salvatore Strano
		 * @since 1.1.0
		 *
		 * @param resource $image_content
		 *
		 * @return resource
		 */
		public function get_truecolor_image( $image_content ) {

			imagealphablending( $image_content, true );
			imagesavealpha( $image_content, true );
			$image_width  = imagesx( $image_content );
			$image_height = imagesy( $image_content );
			$truecolor    = imagecreatetruecolor( $image_width, $image_height );
			$transparent  = imagecolorallocatealpha( $truecolor, 0, 0, 0, 127 );
			imagefill( $truecolor, 0, 0, $transparent );
			imagecopyresampled( $truecolor, $image_content, 0, 0, 0, 0, $image_width, $image_height, $image_width, $image_height );

			return $truecolor;
		}


		/** print watermark in product image
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $original_image
		 * @param $overlay
		 * @param $overlay_path
		 * @param $size_name
		 * @param $watermark
		 */
		public function build_watermark_image( $original_image, $watermark ) {
			$watermark_id   = isset( $watermark['ywcwat_watermark_id'] ) ? $watermark['ywcwat_watermark_id'] : false;
			$watermark_path = get_attached_file( $watermark_id );
			if ( $watermark_id ) {
				$watermark_type = pathinfo( $watermark_path, PATHINFO_EXTENSION );
				if ( $watermark_type == 'jpg' ) {
					$watermark_type = 'jpeg';
				}

				$image_width  = imagesx( $original_image );
				$image_height = imagesy( $original_image );

				$create_function_watermark = 'imagecreatefrom' . $watermark_type;
				$watermark_content         = $create_function_watermark( $watermark_path );
				$watermark_width           = imagesx( $watermark_content );
				$watermark_height          = imagesy( $watermark_content );


				if ( $watermark_width > $image_width ) {
					$coeff_ratio = $image_width / $watermark_width;


					$watermark_width  = intval( round( $watermark_width * $coeff_ratio ) );
					$watermark_height = intval( round( $watermark_height * $coeff_ratio ) );

					$wat_info = array();

					$wat_info[] = imagesx( $watermark_content );
					$wat_info[] = imagesy( $watermark_content );

					$watermark_content = $this->resizeImage( $watermark_content, $watermark_width, $watermark_height, $wat_info );
				}


				list( $watermark_start_x, $watermark_start_y ) = $this->compute_watermark_position( $image_width, $image_height, $watermark_width, $watermark_height, $watermark );

				imagesavealpha( $watermark_content, true );
				imagealphablending( $watermark_content, true );

				$repeat = ( isset( $watermark['ywcwat_watermark_repeat'] ) && ( $watermark['ywcwat_watermark_type'] !== 'type_text' ) );

				if ( ! $repeat ) {
					imagecopyresampled( $original_image, $watermark_content, $watermark_start_x, $watermark_start_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height );
				} else {

					$this->repeat_watermark_image( $original_image, $watermark_content );
					do_action( 'ywcwat_build_watermark_repeat_image', $original_image, $watermark_content );
				}
			}
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $im
		 * @param $new_width
		 * @param $new_height
		 * @param $img_info
		 *
		 * @return resource
		 */
		private function resizeImage( $im, $new_width, $new_height, $img_info ) {
			$newImg = imagecreatetruecolor( $new_width, $new_height );
			imagealphablending( $newImg, false );
			imagesavealpha( $newImg, true );
			$transparent = imagecolorallocatealpha( $newImg, 255, 255, 255, 127 );
			imagefilledrectangle( $newImg, 0, 0, $new_width, $new_height, $transparent );

			imagecopyresampled( $newImg, $im, 0, 0, 0, 0, $new_width, $new_height, $img_info[0], $img_info[1] );

			return $newImg;
		}


		/**
		 * compute watermark position
		 * @author Salvatore Strano
		 * @since 1.1.0
		 *
		 * @param float $image_width
		 * @param float $image_height
		 * @param float $watermark_width
		 * @param float $watermark_height
		 * @param array $watermark
		 *
		 * @return array
		 */
		public function compute_watermark_position( $image_width, $image_height, $watermark_width, $watermark_height, $watermark ) {

			$position           = $watermark['ywcwat_watermark_position'];
			$margin_x_watermark = !empty( $watermark['ywcwat_watermark_margin_x'] ) ? $watermark['ywcwat_watermark_margin_x'] : 0;
			$margin_y_watermark = !empty( $watermark['ywcwat_watermark_margin_y'] ) ? $watermark['ywcwat_watermark_margin_y'] : 0 ;

			switch ( $position ) {

				case 'top_left':
					$watermark_start_x = $margin_x_watermark;
					$watermark_start_y = $margin_y_watermark;
					break;
				case 'top_center':
					$watermark_start_x = ( $image_width / 2 ) - ( $watermark_width / 2 ) + $margin_x_watermark;
					$watermark_start_y = $margin_y_watermark;
					break;
				case 'top_right':
					$watermark_start_x = $image_width - $watermark_width + $margin_x_watermark;
					$watermark_start_y = $margin_y_watermark;
					break;
				case 'middle_left':
					$watermark_start_x = $margin_x_watermark;
					$watermark_start_y = ( $image_height / 2 ) - ( $watermark_height / 2 ) + $margin_y_watermark;
					break;
				case 'middle_center':
					$watermark_start_x = ( $image_width / 2 ) - ( $watermark_width / 2 ) + $margin_x_watermark;
					$watermark_start_y = ( $image_height / 2 ) - ( $watermark_height / 2 ) + $margin_y_watermark;
					break;
				case 'middle_right':
					$watermark_start_x = $image_width - $watermark_width + $margin_x_watermark;
					$watermark_start_y = ( $image_height / 2 ) - ( $watermark_height / 2 ) + $margin_y_watermark;
					break;
				case 'bottom_left':
					$watermark_start_x = $margin_x_watermark;
					$watermark_start_y = $image_height - $watermark_height - $margin_y_watermark;
					break;
				case 'bottom_center':
					$watermark_start_x = ( $image_width / 2 ) - ( $watermark_width / 2 ) + $margin_x_watermark;
					$watermark_start_y = $image_height - $watermark_height - $margin_y_watermark;
					break;

				default:
					/*position button right*/
					$watermark_start_x = $image_width - $watermark_width - $margin_x_watermark;
					$watermark_start_y = $image_height - $watermark_height - $margin_y_watermark;
					break;

			}


			$watermark_position = apply_filters( 'ywcwat_watermark_position', array(
				$watermark_start_x,
				$watermark_start_y
			), $image_width, $image_height, $watermark_width, $watermark_height, $watermark );

			return $watermark_position;

		}

		/** generate new image from different type
		 * @author YITHEMES
		 *
		 * @param $original_image
		 * @param $path
		 * @param $type
		 * @param $quality
		 *
		 * @return bool
		 */
		protected function generateimagefrom( $original_image, $path, $type, $quality ) {
			$result = false;
			switch ( $type ) {

				case 'jpeg':
				case 'jpg' :
					$result = imagejpeg( $original_image, $path, $quality );
					break;
				case 'gif':
					$result = imagegif( $original_image, $path );
					break;
				case 'png':
					/* conversion quality from jpeg (0-100)  to png(0-9)
					 *
					 */
					$new_quality = ( $quality - 100 ) / 11.111111;
					$new_quality = round( abs( $new_quality ) );
					$result      = imagepng( $original_image, $path, $new_quality );
					break;
			}

			return $result;
		}

		/** create new image from different type (by path)
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $path
		 * @param $type
		 *
		 * @return bool|resource
		 */
		protected function createimagefrom( $path, $type ) {

			$original_image = false;
			switch ( $type ) {

				case 'jpeg' :
				case 'jpg':

					$original_image = imagecreatefromjpeg( $path );
					break;
				case 'gif':
					$original_image = imagecreatefromgif( $path );
					break;
				case 'png':
					$original_image = imagecreatefrompng( $path );
					break;
			}


			return $original_image;
		}

		/**
		 * add the repeat feature in type image
		 * @author Strano Salvatore
		 * @since 1.1.0
		 *
		 * @param $original_image
		 * @param $overlay
		 */
		public function repeat_watermark_image( $original_image, $watermark ) {

			$ww = imagesx( $watermark );
			$hh = imagesy( $watermark );
			$w  = imagesx( $original_image );
			$h  = imagesy( $original_image );

			$cur_w = 0;

			while ( $cur_w < $w ) {

				$cur_h = 0;
				while ( $cur_h < $h ) {
					imagecopyresampled( $original_image, $watermark, $cur_w, $cur_h, 0, 0, $ww, $hh, $ww, $hh );
					$cur_h += $hh;
				}
				$cur_w += $ww;
			}
		}

		/**
		 * @param $original_image
		 * @param $watermark
		 */
		public function build_watermark_text( $original_image, $watermark ) {

			$image_width  = imagesx( $original_image );
			$image_height = imagesy( $original_image );


			$watermark_content = $this->imagecreatefromtext( $original_image, $watermark );
			$watermark_width   = imagesx( $watermark_content );
			$watermark_height  = imagesy( $watermark_content );

			if ( $watermark_width > $image_width ) {

				$coeff_ratio = $image_width / $watermark_width;


				$width  = intval( round( $watermark_width * $coeff_ratio ) );
				$height = intval( round( $watermark_height * $coeff_ratio ) );

			} else {
				$width  = $watermark_width;
				$height = $watermark_height;
			}


			list( $watermark_start_x, $watermark_start_y ) = $this->compute_watermark_position( $image_width, $image_height, $width, $height, $watermark );

			imagesavealpha( $watermark_content, true );
			imagealphablending( $watermark_content, true );

			imagecopyresampled( $original_image, $watermark_content, $watermark_start_x, $watermark_start_y, 0, 0, $width, $height, $watermark_width, $watermark_height );
		}

		/**@author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $watermark
		 *
		 * @return resource
		 */
		public function imagecreatefromtext( $original, $watermark ) {

			$text      = $watermark['ywcwat_watermark_text'];
			$font_name = YWCWAT_DIR . 'assets/fonts/' . $watermark['ywcwat_watermark_font'];

			$width      = $watermark['ywcwat_watermark_width'];
			$height     = $watermark['ywcwat_watermark_height'];
			$font_color = $watermark['ywcwat_watermark_font_color'];
			$bg_color   = $watermark['ywcwat_watermark_bg_color'];
			$bg_opacity = $watermark['ywcwat_watermark_opacity'];

			$font_size = $watermark['ywcwat_watermark_font_size'];

			$width         = round( imagesx( $original ) * ( $width / 100 ) );
			$height        = round( imagesy( $original ) * ( $height / 100 ) );
			$line_height   = $watermark['ywcwat_watermark_line_height'] == - 1 ? $height / 2 : $watermark['ywcwat_watermark_line_height'];
			$angle         = isset( $watermark['ywcwat_watermark_angle'] ) ? $watermark['ywcwat_watermark_angle'] : 0;
			$text_box_info = $this->calculateTextBox( $text, $font_name, $font_size, $angle );

			$img_only_text = imagecreatetruecolor( $width, $height );
			$bg_color      = ywcwat_Hex2RGB( $bg_color );

			$bg_opacity = round( abs( ( ( (int) $bg_opacity - 100 ) / 0.78740 ) ) );

			imagefill( $img_only_text, 0, 0, imagecolorallocatealpha( $img_only_text, $bg_color[0], $bg_color[1], $bg_color[2], $bg_opacity ) );

			$font_color = ywcwat_Hex2RGB( $font_color );

			$color = imagecolorallocate( $img_only_text, $font_color[0], $font_color[1], $font_color[2] );
			$this->write_multiline_text( $img_only_text, $font_name, $font_size, $color, $text, $line_height, $angle );

			return $img_only_text;
		}

		private function calculateTextBox( $text, $fontFile, $fontSize, $fontAngle ) {
			/************
			 * simple function that calculates the *exact* bounding box (single pixel precision).
			 * The function returns an associative array with these keys:
			 * left, top:  coordinates you will pass to imagettftext
			 * width, height: dimension of the image you have to create
			 *************/
			$rect = imagettfbbox( $fontSize, $fontAngle, $fontFile, $text );
			$minX = min( array( $rect[0], $rect[2], $rect[4], $rect[6] ) );
			$maxX = max( array( $rect[0], $rect[2], $rect[4], $rect[6] ) );
			$minY = min( array( $rect[1], $rect[3], $rect[5], $rect[7] ) );
			$maxY = max( array( $rect[1], $rect[3], $rect[5], $rect[7] ) );


			return array(
				"left"   => abs( $minX ) - 1,
				"top"    => abs( $minY ) - 1,
				"width"  => $maxX - $minX,
				"height" => $maxY - $minY,
				"box"    => $rect
			);
		}

		/** split text in line
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $font_size
		 * @param $font
		 * @param $text
		 * @param $max_width
		 *
		 * @return array
		 */
		public function get_multiline_text( $font_size, $font, $text, $max_width, $angle ) {
			$words        = explode( " ", $text );
			$lines        = array( $words[0] );
			$current_line = 0;
			for ( $i = 1; $i < count( $words ); $i ++ ) {

				$dimension = $this->calculateTextBox( $lines[ $current_line ] . " " . $words[ $i ], $font, $font_size, $angle );

				$string_lenght = $dimension['width'];

				if ( $string_lenght < $max_width ) {

					$lines[ $current_line ] .= ' ' . $words[ $i ];
				} else {
					$current_line ++;
					$lines[ $current_line ] = $words[ $i ];
				}
			}

			return $lines;
		}

		/** print single line in image
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $image
		 * @param $font
		 * @param $font_size
		 * @param $color
		 * @param $text
		 * @param $start_y
		 * @param $line_height
		 */
		public function write_multiline_text( $image, $font, $font_size, $color, $text, $line_height, $angle ) {

			$image_w  = imagesx( $image );
			$image_h  = imagesy( $image );
			$lines    = $this->get_multiline_text( $font_size, $font, $text, $image_w, $angle );
			$tot_line = count( $lines );

			foreach ( $lines as $line ) {

				$dim = $this->calculateTextBox( $line, $font, $font_size, $angle );

				if ( $angle == 0 ) {

					$text_width  = $dim['width'];
					$text_height = $dim['height'];
					$text_height = $tot_line == 1 ? $text_height : $line_height;
					$x           = ceil( ( $image_w - $text_width ) / 2 );
					$y           = ceil( ( $image_h + $text_height ) / 2 ) - ( $tot_line - 1 ) * $text_height;
				} else {

					$dim = $dim['box'];
					$x   = ( $image_w / 2 ) - ( $dim[4] - $dim[0] ) / 2;
					$y   = ( ( $image_h / 2 ) - ( $dim[5] - $dim[1] ) / 2 );

				}
				$tot_line --;
				imagettftext( $image, $font_size, $angle, $x, $y, $color, $font, $line );


			}
		}

		/**
		 * restore all original image
		 * @author YITH
		 * @since 1.2.0
		 *
		 */
		public function reset_watermark() {
			if ( isset( $_REQUEST['ywcwat_reset_watermark'] ) ) {

				$count = array( 'success' => 0, 'error' => 0 );

				$wp_upload_dir = wp_upload_dir();
				$uploads_dir   = $wp_upload_dir['basedir'];
				$backup_dir    = $wp_upload_dir['basedir'] . '/' . YWCWAT_PRIVATE_DIR;

				$prefix = YWCWAT_BACKUP_FILE;

				foreach ( scandir( $backup_dir ) as $yfolder ) {
					if ( ! ( is_dir( "$backup_dir/$yfolder" ) && ! in_array( $yfolder, array( '.', '..' ) ) ) ) {
						continue;
					}

					$yfolder = basename( $yfolder );
					foreach ( scandir( "$backup_dir/$yfolder" ) as $mfolder ) {
						if ( ! ( is_dir( "$backup_dir/$yfolder/$mfolder" ) && ! in_array( $mfolder, array(
								'.',
								'..'
							) ) ) ) {
							continue;
						}

						$mfolder = basename( $mfolder );
						$images  = (array) glob( "$backup_dir/$yfolder/$mfolder/*.{jpg,png,gif}", GLOB_BRACE );
						foreach ( $images as $image ) {

							// $filename = str_replace( $prefix, '', $image );
							$filename = basename( $image );
							$dest_dir = "$uploads_dir/$yfolder/$mfolder/$filename";

							if ( copy( $image, $dest_dir ) ) {
								$count['success'] ++;
							} else {
								$count['error'] ++;
							}
						}
					}
				}

				wp_send_json( array( 'success' => $count['success'], 'error' => $count['error'] ) );

			}
		}

		/**
		 * get the preview watermark
		 * @author YITH
		 * @since 1.1.0
		 */
		public function preview_watermark() {

			$message = __( 'No preview is available for this watermark', 'yith-woocommerce-watermark' );
			$result  = false;


			if ( isset( $_REQUEST['ywcwat_args'] ) ) {
				// $watermark_id = $_REQUEST['ywcwat_id'];
				$watermark_args = array();
				$watermark_arg  = ( $_REQUEST['ywcwat_args'] );
				parse_str( $watermark_arg, $watermark_args );

				$context = isset( $_REQUEST['context'] ) ? 'ywcwat_custom_watermark' : 'ywcwat_watermark_select';

				$watermark = isset( $watermark_args[ $context ] ) ? array_shift( $watermark_args[ $context ] ) : false;

				$image_size = $watermark['ywcwat_watermark_sizes'];

				$size = wc_get_image_size( $image_size );

				$preview_width  = apply_filters( 'ywcwat_preview_width', ! empty( $size['width'] ) ? $size['width'] : 300, $image_size );
				$preview_height = apply_filters( 'ywcwat_preview_height', ! empty( $size['height'] ) ? $size['height'] : 300, $image_size );

				$preview_width  = $preview_width > 500 ? 500 : $preview_width;
				$preview_height = $preview_height > 500 ? 500 : $preview_height;


				if ( $watermark ) {
					$original_image = imagecreatetruecolor( $preview_width, $preview_height );

					$color = imagecolorallocate( $original_image, 224, 224, 224 );
					imagefill( $original_image, 0, 0, $color );

					if ( $watermark['ywcwat_watermark_type'] == 'type_text' ) {

						$this->build_watermark_text( $original_image, $watermark );
					} else {

						$this->build_watermark_image( $original_image, $watermark );
					}

					$res = $this->generateimagefrom( $original_image, YWCWAT_ASSETS_PATH . '/images/preview.jpg', 'jpg', 100 );

					if ( $res ) {
						$result  = true;
						$message = YWCWAT_ASSETS_URL . 'images/preview.jpg?now=' . time();
					}

					imagedestroy( $original_image );


				}
			}

			wp_send_json( array( 'result' => $result, 'message' => $message ) );
		}

		/**
		 * @author YITH
		 * @since 1.1.0
		 *
		 */
		public function add_product_meta_boxes() {
			add_meta_box( 'yith-ywcwat-metabox', __( 'Apply Watermark', 'yith-woocommerce-watermark' ), array(
				$this,
				'show_watermark_meta_box'
			), 'product', 'side', 'core' );

		}

		/**
		 * @author YITH
		 * @since 1.1.0
		 */
		public function show_watermark_meta_box() {
			wc_get_template( 'admin/apply-single-watermark.php', array(), YWCWAT_TEMPLATE_PATH, YWCWAT_TEMPLATE_PATH );
		}

		//single product : manage custom watermark

		/** add checkbox in product data header
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $type_options
		 *
		 * @return array
		 */
		public function add_product_watermark_option( $type_options ) {

			$watermark_option = array(
				'enable_watermark' => array(
					'id'            => '_ywcwat_product_enabled_watermark',
					'wrapper_class' => '',
					'label'         => __( 'Watermark', 'yith-woocommerce-watermark' ),
					'description'   => __( 'Add custom watermark for this product', 'yith-woocommerce-watermark' ),
					'default'       => 'no'
				)
			);

			return array_merge( $type_options, $watermark_option );
		}

		/** print watermark tab in product data
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function print_watermark_panels() {

			?>
            <style type="text/css">
                #woocommerce-product-data ul.wc-tabs .ywcwat_watermark_data_tab a:before {
                    content: '\e00c';
                    font-family: 'WooCommerce';
                    padding-right: 5px;

                }

            </style>
            <li class="ywcwat_watermark_data_tab show_if_custom_watermark_enabled">
                <a href="#ywcwat_watermark_data">
					<?php _e( 'Watermark', 'yith-woocommerce-watermark' ); ?>
                </a>
            </li>


			<?php
			add_action( 'woocommerce_product_data_panels', array( $this, 'write_watermark_panels' ) );

		}

		/**include the watermark tab content
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function write_watermark_panels() {

			include_once( YWCWAT_TEMPLATE_PATH . 'metaboxes/product_watermark.php' );
		}

		/** save the watermark product meta
		 * @author YITH
		 * @since 1.0.0
		 *
		 * @param WC_Product $product ,
		 *
		 */
		public function save_product_watermark_meta( $product ) {
			if ( isset( $_REQUEST['ywcwat_custom_watermark'] ) ) {

				$custom_watermark = $_REQUEST['ywcwat_custom_watermark'];

				$product->update_meta_data( '_ywcwat_product_watermark', $custom_watermark );

			} else {
				$product->delete_meta_data( '_ywcwat_product_watermark' );
			}

			if ( isset( $_REQUEST['_ywcwat_product_enabled_watermark'] ) ) {

				$product->update_meta_data( '_enable_watermark', 'yes' );
			} else {
				$product->delete_meta_data( '_enable_watermark' );
			}

		}


		/**
         * get all attach ids for the product
		 * @return string
		 */
		public function get_ids_attach() {

			$attach_ids = ywcwat_get_all_product_attach();

			$ids = array();

			$gallery_ids = ywcwat_get_all_product_img_gallery();

			foreach ( $gallery_ids as $gallery_id ) {

				$gallery_attach_ids = explode( ',', $gallery_id->ID );

				foreach ( $gallery_attach_ids as $attach_id ) {

					if ( ! in_array( $attach_id, $ids ) ) {
						$ids[] = $attach_id;
					}
				}
			}

			$ids = array_merge( $attach_ids, $ids );
			return json_encode( $ids );

		}

	}
}
