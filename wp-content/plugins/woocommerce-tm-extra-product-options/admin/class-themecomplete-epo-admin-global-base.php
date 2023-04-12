<?php
/**
 * Extra Product Options Global Administration class
 *
 * @package Extra Product Options/Admin
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Global Administration class
 *
 * @package Extra Product Options/Admin
 * @version 4.9
 */
final class THEMECOMPLETE_EPO_ADMIN_Global_Base {

	/**
	 * The class THEMECOMPLETE_EPO_ADMIN_Global_List_Table
	 *
	 * @var object THEMECOMPLETE_EPO_ADMIN_Global_List_Table
	 */
	public $tm_list_table;
	/**
	 * The base post type.
	 *
	 * @var string|boolean
	 */
	public $basetype = false;

	/**
	 * The $post variable for use in scripts templates.
	 *
	 * @var object|boolean
	 */
	public $post = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_ADMIN_Global_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Add menu action.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 9 );

		// Pre-render actions.
		add_action( 'admin_init', [ $this, 'tm_admin_init' ], 9 );
		add_action( 'plugins_loaded', [ $this, 'tm_plugins_loaded' ], 100 );
		add_action( 'init', [ $this, 'tm_init' ], 100 );
		add_action( 'current_screen', [ $this, 'current_screen' ], 100 );

		// Add the plugin to WooCommerce screen ids so that we can load the generic WooCommerce files.
		add_filter( 'woocommerce_screen_ids', [ $this, 'woocommerce_screen_ids' ] );

		// Add list columns.
		add_filter( 'manage_' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE . '_posts_columns', [ $this, 'tm_list_columns' ] );
		add_action( 'manage_' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE . '_posts_custom_column', [ $this, 'tm_list_column' ], 10, 2 );

		// Export a form.
		add_action( 'wp_ajax_tm_export', [ $this, 'export' ] );

		// Variations check.
		add_action( 'wp_ajax_woocommerce_tm_variations_check', [ $this, 'tm_variations_check' ] );
		add_action( 'wp_ajax_nopriv_woocommerce_tm_variations_check', [ $this, 'tm_variations_check' ] );
		add_action( 'wp_ajax_woocommerce_tm_get_variations_array', [ $this, 'tm_get_variations_array' ] );
		add_action( 'wp_ajax_nopriv_woocommerce_tm_get_variations_array', [ $this, 'tm_get_variations_array' ] );

		// Ajax save settings.
		add_action( 'wp_ajax_tm_save_settings', [ $this, 'tm_save_settings' ] );
		// Ajax reset settings.
		add_action( 'wp_ajax_tm_reset_settings', [ $this, 'tm_reset_settings' ] );

		// File manager.
		add_action( 'wp_ajax_tm_mn_movetodir', [ $this, 'tm_mn_movetodir' ] );
		add_action( 'wp_ajax_tm_mn_deldir', [ $this, 'tm_mn_deldir' ] );
		add_action( 'wp_ajax_tm_mn_delfile', [ $this, 'tm_mn_delfile' ] );

		// Add extra functionality on list screen.
		add_action( 'bulk_actions-edit-product', [ $this, 'bulk_actions_edit_product' ], 10, 1 );
		add_action( 'load-edit.php', [ $this, 'bulk_actions_edit_product_do' ] );

		// Save options (ajax).
		add_action( 'wp_ajax_tm_save', [ $this, 'tm_save' ] );
		add_action( 'wp_ajax_nopriv_tm_save', [ $this, 'tm_save' ] );

		// Get the categories of a product.
		add_action( 'wp_ajax_wc_epo_get_product_categories', [ $this, 'wc_epo_get_product_categories' ] );
		// Search product in a category.
		add_action( 'wp_ajax_wc_epo_search_products_in_categories', [ $this, 'wc_epo_search_products_in_categories' ] );

		// For dashboard menu.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 11 );
	}

	/**
	 * Current screen
	 *
	 * @since 6.0
	 */
	public function current_screen() {
		$screen = get_current_screen();
		if ( is_object( $screen ) && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $screen->post_type ) {
			// save meta data.
			add_action( 'save_post', [ $this, 'tm_save_postdata' ], 1, 2 );

			$do_action = isset( $_GET['action'] ) ? wp_unslash( $_GET['action'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput, WordPress.Security.NonceVerification.Recommended

			if ( $do_action && 'clone' === $do_action ) {
				$post_id = 0;
				if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				if ( $post_id ) {
					$this->do_clone_form( $post_id, $post_id );
					if ( isset( $_SERVER['REQUEST_URI'] ) ) {
						wp_safe_redirect(
							esc_url_raw(
								admin_url( 'edit.php?post_type=' . THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE )
							)
						);
					}
					exit;
				}
			}
		}

	}

	/**
	 * Load scripts
	 *
	 * @param string $hook_suffix The current admin page.
	 * @since 1.0
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		// Dashboard Menu style.
		wp_enqueue_style( 'themecomplete-epo-admin-menu', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tm-global-admin-menu.css', [], THEMECOMPLETE_EPO_VERSION );

		// Option Templates.
		if ( in_array( $hook_suffix, [ 'post.php', 'post-new.php' ], true ) ) {
			$screen = get_current_screen();
			if ( is_object( $screen ) && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $screen->post_type ) {
				$this->register_admin_scripts( 1 );
			}
		}
	}

	/**
	 * Search for products and variations in categories
	 *
	 * @since  5.0
	 */
	public static function wc_epo_search_products_in_categories() {

		check_ajax_referer( 'search-products', 'security' );

		$include_category_ids = ! empty( $_GET['include'] ) && is_array( $_GET['include'] ) ? array_map( 'absint', $_GET['include'] ) : [];

		if ( empty( $include_category_ids ) ) {
			wp_die();
		}

		$include_category_slugs = get_terms(
			'product_cat',
			[
				'include' => $include_category_ids,
				'fields'  => 'id=>slug',
			]
		);

		if ( empty( $include_category_slugs ) ) {
			wp_die();
		}

		$product_ids = wc_get_products(
			[
				'category' => array_values( $include_category_slugs ),
				'return'   => 'ids',
				'limit'    => -1,
			]
		);

		$_GET['include'] = $product_ids;

		WC_AJAX::json_search_products();

	}

	/**
	 * Get the categories of a product.
	 *
	 * @since  5.0
	 */
	public static function wc_epo_get_product_categories() {

		check_ajax_referer( 'get-product-categories', 'security' );

		if ( empty( $_POST['product_id'] ) ) {
			die();
		}

		$product = wc_get_product( absint( $_POST['product_id'] ) );

		if ( ! $product ) {
			die();
		}

		wp_send_json(
			[
				'result'       => 'success',
				'category_ids' => $product->get_category_ids(),
			]
		);
	}

	/**
	 * Save options (ajax)
	 *
	 * @since 4.8
	 */
	public function tm_save() {

		check_ajax_referer( 'save-nonce', 'security' );

		$message = esc_html__( 'Something went wrong with the request!', 'woocommerce-tm-extra-product-options' );

		$json_result = [
			'result'  => 0,
			'message' => $message,
		];

		$post_id = 0;
		if ( isset( $_REQUEST['post_id'] ) ) {
			$post_id = absint( wp_unslash( $_REQUEST['post_id'] ) );
		}

		if ( ! empty( $post_id ) ) {

			$tm_metas = [];

			if ( isset( $_REQUEST['tm_uploadmeta'] ) ) {
				$tm_metas = wp_unslash( $_REQUEST['tm_uploadmeta'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$tm_metas = rawurldecode( $tm_metas );
				$tm_metas = nl2br( $tm_metas );
				$tm_metas = json_decode( $tm_metas, true );
			}

			if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
				$tm_meta = $tm_metas['tm_meta'];

				$meta = 'tm_meta';
				$post = get_post( $post_id );
				if ( $post && property_exists( $post, 'ID' ) && property_exists( $post, 'post_type' ) ) {

					$post_type = isset( $post->post_type ) ? $post->post_type : '';
					$basetype  = $this->basetype;
					if ( false === $basetype ) {
						$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
					}
					$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id, $post_type, $basetype );
					if ( ! $wpml_is_original_product ) {
						$meta = 'tm_meta_wpml';
					}

					$old_data = themecomplete_get_post_meta( $post_id, $meta, true );
					if ( isset( $old_data['priority'] ) ) {
						$tm_meta['priority'] = $old_data['priority'];
					}
					themecomplete_save_post_meta( $post_id, $tm_meta, $old_data, $meta );
					$json_result['message'] = esc_html__( 'Options saved!', 'woocommerce-tm-extra-product-options' );
					$json_result['result']  = 1;

				}
			}
		}

		wp_send_json( $json_result );

	}

	/**
	 * Add extra functionality on list screen (execute)
	 *
	 * @since 1.0
	 */
	public function bulk_actions_edit_product_do() {
		global $typenow;
		$post_type = $typenow;

		if ( 'product' === $post_type ) {

			// get the action
			// depending on your resource type this could be
			// WP_Users_List_Table, WP_Comments_List_Table, etc.
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			$post_ids = [];

			// make sure ids are submitted.
			// depending on the resource type, this may be 'media' or 'ids'.
			if ( isset( $_REQUEST['post'] ) && is_array( $_REQUEST['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$post_ids = array_map( 'intval', $_REQUEST['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			if ( empty( $post_ids ) ) {
				return;
			}

			$sendback = remove_query_arg( [ 'tc_updated', 'tc_removed', 'untrashed', 'deleted', 'ids' ], wp_get_referer() );
			if ( ! $sendback ) {
				$sendback = admin_url( "edit.php?post_type=$post_type" );
			}
			$pagenum  = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			$do_action = ( 'tcclear' === $action || 'tcproductclear' === $action || 'tcclearexclude' === $action || 'tcclearexcludeadd' === $action ) ? $action : substr( $action, 0, 3 );

			switch ( $do_action ) {
				case 'tc_':
					$tc_updated = 0;
					foreach ( $post_ids as $post_id ) {
						$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', true );
						if ( ! is_array( $tm_meta_cpf ) ) {
							$tm_meta_cpf = [];
						}
						if ( is_array( $tm_meta_cpf ) ) {
							if ( ! isset( $tm_meta_cpf['global_forms'] ) ) {
								$tm_meta_cpf['global_forms'] = [];
							}
							$tm_meta_cpf['global_forms'][] = substr( $action, 3 );
							$tm_meta_cpf['global_forms']   = array_unique( $tm_meta_cpf['global_forms'] );
							themecomplete_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );
						}

						$tc_updated ++;
					}
					$sendback = add_query_arg(
						[
							'tc_updated' => $tc_updated,
							'ids'        => join(
								',',
								$post_ids
							),
						],
						$sendback
					);
					break;

				case 'tcclear':
					$tc_removed = 0;
					foreach ( $post_ids as $post_id ) {
						$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', true );
						if ( ! is_array( $tm_meta_cpf ) ) {
							$tm_meta_cpf = [];
						}
						if ( is_array( $tm_meta_cpf ) ) {
							if ( ! isset( $tm_meta_cpf['global_forms'] ) ) {
								$tm_meta_cpf['global_forms'] = [];
							}
							unset( $tm_meta_cpf['global_forms'] );
							themecomplete_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );
						}
						$tc_removed ++;
					}
					$sendback = add_query_arg(
						[
							'tc_removed' => $tc_removed,
							'ids'        => join(
								',',
								$post_ids
							),
						],
						$sendback
					);
					break;

				case 'tcproductclear':
					$tc_removed = 0;
					foreach ( $post_ids as $post_id ) {
						themecomplete_update_post_meta( $post_id, 'tm_meta', '' );
						$tc_removed ++;
					}
					$sendback = add_query_arg(
						[
							'tc_removed' => $tc_removed,
							'ids'        => join(
								',',
								$post_ids
							),
						],
						$sendback
					);
					break;

				case 'tcclearexclude':
					$tc_removed = 0;
					foreach ( $post_ids as $post_id ) {
						$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', true );
						if ( ! is_array( $tm_meta_cpf ) ) {
							$tm_meta_cpf = [];
						}
						if ( is_array( $tm_meta_cpf ) ) {
							if ( ! isset( $tm_meta_cpf['exclude'] ) ) {
								$tm_meta_cpf['exclude'] = [];
							}
							unset( $tm_meta_cpf['exclude'] );
							themecomplete_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );
						}
						$tc_removed ++;
					}
					$sendback = add_query_arg(
						[
							'tc_removed' => $tc_removed,
							'ids'        => join(
								',',
								$post_ids
							),
						],
						$sendback
					);
					break;

				case 'tcclearexcludeadd':
					$tc_removed = 0;
					foreach ( $post_ids as $post_id ) {
						$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', true );
						if ( ! is_array( $tm_meta_cpf ) ) {
							$tm_meta_cpf = [];
						}
						if ( is_array( $tm_meta_cpf ) ) {
							if ( ! isset( $tm_meta_cpf['exclude'] ) ) {
								$tm_meta_cpf['exclude'] = [];
							}
							$tm_meta_cpf['exclude'] = '1';
							themecomplete_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );
						}
						$tc_removed ++;
					}
					$sendback = add_query_arg(
						[
							'tc_removed' => $tc_removed,
							'ids'        => join(
								',',
								$post_ids
							),
						],
						$sendback
					);
					break;

				default:
					return;
			}

			wp_safe_redirect( $sendback );
			exit;
		}
	}

	/**
	 * Add extra functionality on list screen
	 *
	 * @param array $actions An array of the available bulk actions.
	 * @since 1.0
	 */
	public function bulk_actions_edit_product( $actions ) {

		$meta_array = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', 'tm_meta_disable_categories', 1, '==', 'EXISTS' );
		$args       = [
			'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
			'post_status' => [ 'publish' ], // get only enabled global extra options.
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'asc',
		];

		THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
		$tmp_tmglobalprices = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
		THEMECOMPLETE_EPO_WPML()->restore_sql_filter();
		if ( $tmp_tmglobalprices ) {
			$actions['tcline'] = esc_html__( 'Include additional Global forms', 'woocommerce-tm-extra-product-options' );
			foreach ( $tmp_tmglobalprices as $key => $tmcp ) {
				$actions[ 'tc_' . $tmcp->ID ] = ( ( '' === $tmcp->post_title ) ? esc_html__( '(no title)', 'default' ) : $tmcp->post_title );
			}
			$actions['tcline2'] = '---';
		}
		$actions['tcclear'] = esc_html__( 'Clear all additional Global forms', 'woocommerce-tm-extra-product-options' );

		$actions['tcclearexclude']    = esc_html__( 'Disable Exclude from Global Options', 'woocommerce-tm-extra-product-options' );
		$actions['tcclearexcludeadd'] = esc_html__( 'Enable Exclude from Global Options', 'woocommerce-tm-extra-product-options' );
		$actions['tcproductclear']    = esc_html__( 'Delete all options on the product', 'woocommerce-tm-extra-product-options' );

		return $actions;

	}

	/**
	 * File manager (delete directory)
	 *
	 * @since 1.0
	 */
	public function tm_mn_deldir() {

		check_ajax_referer( 'settings-nonce', 'security' );

		if ( isset( $_POST['tmdir'] ) ) {
			$subdir = THEMECOMPLETE_EPO()->upload_dir . sanitize_text_field( wp_unslash( $_POST['tmdir'] ) );
			$param  = wp_upload_dir();
			if ( empty( $param['subdir'] ) ) {
				$param['path']   = $param['path'] . $subdir;
				$param['url']    = $param['url'] . $subdir;
				$param['subdir'] = $subdir;
			} else {
				$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
				$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
				$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
			}
			$html = THEMECOMPLETE_EPO_HELPER()->file_rmdir( $param['path'] );
		}

		$this->tm_mn_movetodir();
	}

	/**
	 * File manager (delete file)
	 *
	 * @since 1.0
	 */
	public function tm_mn_delfile() {

		check_ajax_referer( 'settings-nonce', 'security' );

		if ( isset( $_POST['tmfile'] ) && isset( $_POST['tmdir'] ) ) {
			$tmdir  = sanitize_text_field( wp_unslash( $_POST['tmdir'] ) );
			$subdir = THEMECOMPLETE_EPO()->upload_dir . $tmdir;
			$param  = wp_upload_dir();
			if ( empty( $param['subdir'] ) ) {
				$param['path']   = $param['path'] . $subdir;
				$param['url']    = $param['url'] . $subdir;
				$param['subdir'] = $subdir;
			} else {
				$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
				$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
				$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
			}
			$html = THEMECOMPLETE_EPO_HELPER()->file_delete( $param['path'] . '/' . sanitize_text_field( wp_unslash( $_POST['tmfile'] ) ) );
		}

		$this->tm_mn_movetodir();
	}

	/**
	 * File manager (move to a directory)
	 *
	 * @since 1.0
	 */
	public function tm_mn_movetodir() {

		check_ajax_referer( 'settings-nonce', 'security' );

		$html = '';
		if ( isset( $_POST['dir'] ) ) {
			$html = THEMECOMPLETE_EPO_HELPER()->file_manager( THEMECOMPLETE_EPO()->upload_dir, sanitize_text_field( wp_unslash( $_POST['dir'] ) ) );
		}
		if ( $html ) {
			wp_send_json(
				[
					'result' => $html,
				]
			);
		} else {
			wp_send_json(
				[
					'error'   => 1,
					'message' => esc_html__( 'File manager is not supported on your server.', 'woocommerce-tm-extra-product-options' ),
				]
			);
		}
		die();
	}

	/**
	 * Reset plugin settings (via ajax)
	 *
	 * @since 1.0
	 */
	public function tm_reset_settings() {

		check_ajax_referer( 'settings-nonce', 'security' );

		$error   = 0;
		$message = esc_html__( 'Your settings have been reset.', 'woocommerce-tm-extra-product-options' );

		$thissettings_options = THEMECOMPLETE_EPO_SETTINGS()->settings_options();
		$thissettings_array   = [];
		foreach ( $thissettings_options as $key => $value ) {
			$thissettings_array[ $key ] = THEMECOMPLETE_EPO_SETTINGS()->create_setting( $key, $value );
		}
		$settings = [];
		$settings = array_merge( $settings, [ [ 'type' => 'tm_tabs_header' ] ] );

		foreach ( $thissettings_array as $key => $value ) {
			$settings = array_merge( $settings, $value );
			foreach ( $value as $k => $v ) {
				if ( isset( $v['type'] ) && isset( $v['id'] ) && isset( $v['default'] ) && 'tm_title' !== $v['type'] ) {
					$_POST[ $v['id'] ] = $v['default'];
				}
			}
		}
		$options = apply_filters(
			'tm_' . THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID . '_settings',
			$settings
		);

		if ( class_exists( 'WC_Admin_Settings' ) && is_callable( [ 'WC_Admin_Settings', 'save' ] ) ) {
			WC_Admin_Settings::save_fields( $options );
		} else {
			$error   = 1;
			$message = esc_html__( 'There was an error saving the settings.', 'woocommerce-tm-extra-product-options' );
		}
		wp_send_json(
			[
				'error'   => $error,
				'message' => $message,
			]
		);
		die();
	}

	/**
	 * Save plugin settings (via ajax)
	 *
	 * @since 1.0
	 */
	public function tm_save_settings() {

		check_ajax_referer( 'settings-nonce', 'security' );

		$error   = 0;
		$message = esc_html__( 'Your settings have been saved.', 'woocommerce-tm-extra-product-options' );

		$thissettings_options = THEMECOMPLETE_EPO_SETTINGS()->settings_options();
		$thissettings_array   = [];
		foreach ( $thissettings_options as $key => $value ) {
			$thissettings_array[ $key ] = THEMECOMPLETE_EPO_SETTINGS()->create_setting( $key, $value );
		}
		$settings = [];
		$settings = array_merge( $settings, [ [ 'type' => 'tm_tabs_header' ] ] );

		foreach ( $thissettings_array as $key => $value ) {
			$settings = array_merge( $settings, $value );
		}
		$options = apply_filters(
			'tm_' . THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID . '_settings',
			$settings
		);

		if ( class_exists( 'WC_Admin_Settings' ) && is_callable( [ 'WC_Admin_Settings', 'save' ] ) ) {
			WC_Admin_Settings::save_fields( $options );
		} else {
			$error   = 1;
			$message = esc_html__( 'There was an error saving the settings.', 'woocommerce-tm-extra-product-options' );
		}
		wp_send_json(
			[
				'error'   => $error,
				'message' => $message,
			]
		);
		die();
	}

	/**
	 * Variations check
	 *
	 * @since 1.0
	 */
	public function tm_variations_check() {
		global $post, $tm_is_ajax;
		$tm_is_ajax  = true;
		$json_result = [
			'result' => 0,
			'html'   => '',
		];
		if ( isset( $_POST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$post_id = absint( $_POST['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification
			$builder = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
			$meta    = [];
			if ( isset( $builder['tmfbuilder'] ) && isset( $builder['tmfbuilder']['variations_options'] ) ) {
				$meta = $builder['tmfbuilder']['variations_options'];
			}
			ob_start();
			THEMECOMPLETE_EPO_BUILDER()->builder_sub_variations_options(
				[
					'meta'       => $meta,
					'product_id' => $post_id,
				]
			);
			$html                  = ob_get_clean();
			$json_result['result'] = 1;
			$json_result['html']   = $html;

			$returned_js = THEMECOMPLETE_EPO_BUILDER()->builder_sub_variations_options(
				[
					'meta'       => $meta,
					'product_id' => $post_id,
					'return_js'  => true,
				]
			);
			if ( is_array( $returned_js ) ) {
				$temp_array = [
					'id'       => 'multiple',
					'multiple' => [],
				];
				foreach ( $returned_js as $js_value ) {
					$temp_array['multiple'][] = THEMECOMPLETE_EPO_BUILDER()->remove_for_js( $js_value );
				}
				$json_result['jsobject'] = $temp_array;
			}
		}
		wp_send_json( $json_result );
		die();
	}

	/**
	 * Get available variations
	 *
	 * @param object $product The product object.
	 * @since 1.0
	 */
	public function get_available_variations( $product ) {
		$available_variations = [];

		foreach ( $product->get_children() as $child_id ) {
			$variation    = wc_get_product( $child_id );
			$variation_id = themecomplete_get_variation_id( $variation );
			// Hide out of stock variations if 'Hide out of stock items from the catalog' is checked.
			if ( empty( $variation_id ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
				continue;
			}

			// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price).
			if ( apply_filters( 'woocommerce_hide_invisible_variations', false, themecomplete_get_id( $product ), $variation ) && ! $variation->variation_is_visible() ) {
				continue;
			}

			$available_variations[] = $this->get_available_variation( $variation, $product );
		}

		return $available_variations;
	}

	/**
	 * Get available variation
	 *
	 * @param mixed  $variation The variation.
	 * @param object $product The product object.
	 * @since 1.0
	 */
	public function get_available_variation( $variation, $product ) {
		if ( is_numeric( $variation ) ) {
			$variation = wc_get_product( $variation );
		}

		return apply_filters(
			'tc_epo_woocommerce_available_variation',
			[
				'variation_id' => themecomplete_get_variation_id( $variation ),
				'attributes'   => $variation->get_variation_attributes(),
				'is_in_stock'  => $variation->is_in_stock(),
			],
			$product,
			$variation
		);
	}

	/**
	 * Get variations array
	 *
	 * @since 1.0
	 */
	public function tm_get_variations_array() {
		$variations = [];
		$attributes = [];
		if ( isset( $_POST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( class_exists( 'Woocommerce_Waitlist' ) ) {
				remove_filter( 'woocommerce_get_availability', [ Woocommerce_Waitlist::get_instance(), 'wew_check_product_availability' ], 2 );
				remove_filter( 'woocommerce_get_availability', [ Woocommerce_Waitlist::get_instance(), 'wew_check_product_availability' ] );
			}

			$product = wc_get_product( absint( wp_unslash( $_POST['post_id'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification

			if ( $product && is_object( $product ) && is_callable( [ $product, 'get_available_variations' ] ) ) {
				$variations     = $this->get_available_variations( $product );
				$attributes     = $product->get_variation_attributes(); // @phpstan-ignore-line
				$all_attributes = $product->get_attributes(); // @phpstan-ignore-line
				if ( $attributes ) {
					foreach ( $attributes as $key => $value ) {
						if ( ! $value ) {
							$attributes[ $key ] = array_map( 'trim', explode( '|', $all_attributes[ $key ]['value'] ) );
						}
					}
				}
			}
		}
		wp_send_json(
			[
				'variations' => $variations,
				'attributes' => $attributes,
			]
		);
		die();
	}

	/**
	 * Export a form.
	 *
	 * @since 1.0
	 */
	public function export() {

		$csv = new THEMECOMPLETE_EPO_ADMIN_CSV();
		$csv->export( 'metaserialized' );

	}

	/**
	 * Import a form.
	 *
	 * @since 1.0
	 */
	public function import() {

		$csv = new THEMECOMPLETE_EPO_ADMIN_CSV();
		$csv->import();

	}

	/**
	 * Download a form.
	 *
	 * @since 1.0
	 */
	public function download() {

		$csv = new THEMECOMPLETE_EPO_ADMIN_CSV();
		$csv->download();

	}

	/**
	 * Extra row actions.
	 *
	 * @param array  $actions An array of row action links.
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function row_actions_template( $actions, $post ) {

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		$can_do_clone = true;
		// Disable wpml cloning on translated forms.
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$ppid = absint( themecomplete_get_post_meta( $post->ID, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true ) );
			if ( ! empty( $ppid ) && absint( $post->ID ) !== $ppid ) {
				$can_do_clone = false;
			}
		}

		if ( $can_do_clone ) {
			// Clone a form.
			$nonce                    = wp_create_nonce( 'tmclone_form_nonce_' . $post->ID );
			$actions['tm_clone_form'] = '<a class="tm-clone-form" rel="' . esc_attr( $nonce ) . '" href="' . esc_url( admin_url( 'post.php?action=clone&amp;post=' . $post->ID . '&amp;_wpnonce=' . $nonce ) ) . '">' . esc_html__( 'Clone template', 'woocommerce-tm-extra-product-options' ) . '</a>';
		}

		ksort( $actions );

		return $actions;

	}

	/**
	 * Extra row actions.
	 *
	 * @param array  $actions An array of row action links.
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function row_actions( $actions, $post ) {

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		$can_do_clone = true;
		// Disable wpml cloning on translated forms.
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$ppid = absint( themecomplete_get_post_meta( $post->ID, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true ) );
			if ( ! empty( $ppid ) && absint( $post->ID ) !== $ppid ) {
				$can_do_clone = false;
			}
		}

		if ( $can_do_clone ) {
			// Clone a form.
			$nonce                    = wp_create_nonce( 'tmclone_form_nonce_' . $post->ID );
			$actions['tm_clone_form'] = '<a class="tm-clone-form" rel="' . esc_attr( $nonce ) . '" href="' . esc_url( admin_url( 'edit.php?post_type=product&amp;page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&amp;action=clone&amp;post=' . $post->ID . '&amp;_wpnonce=' . $nonce ) ) . '">' . esc_html__( 'Clone form', 'woocommerce-tm-extra-product-options' ) . '</a>';
		}

		// Export a form.
		$nonce                     = wp_create_nonce( 'tmexport_form_nonce_' . $post->ID );
		$actions['tm_export_form'] = '<a class="tm-export-form" rel="' . esc_attr( $nonce ) . '" href="' . esc_url( admin_url( 'edit.php?post_type=product&amp;page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&amp;action=export&amp;post=' . $post->ID . '&amp;_wpnonce=' . $nonce ) ) . '">' . esc_html__( 'Export form', 'woocommerce-tm-extra-product-options' ) . '</a>';
		ksort( $actions );

		return $actions;

	}

	/**
	 * Add menus
	 *
	 * @since 1.0
	 */
	public function admin_menu() {

		$page_hook = add_submenu_page(
			'edit.php?post_type=product',
			THEMECOMPLETE_EPO_POST_TYPES::instance()::$global_type->labels->name,
			THEMECOMPLETE_EPO_POST_TYPES::instance()::$global_type->labels->name,
			'manage_woocommerce',
			THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK,
			[ $this, 'admin_screen' ]
		);
		// Restrict loading scripts and functions unless we are on the plugin page.
		add_action( 'load-' . $page_hook, [ $this, 'tm_load_admin' ] );

		add_menu_page(
			esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
			esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
			'manage_woocommerce',
			'tcepo',
			[ $this, 'admin_epo_menu' ],
			'dashicons-screenoptions',
			'54.6'
		);

		$hook1 = add_submenu_page(
			'tcepo',
			esc_html__( 'Settings', 'woocommerce-tm-extra-product-options' ),
			esc_html__( 'Settings', 'woocommerce-tm-extra-product-options' ),
			'manage_woocommerce',
			'tcepo-settings',
			[ $this, 'admin_epo_submenu_settings' ]
		);
		add_action( 'load-' . $hook1, [ $this, 'preload_settings' ] );

		add_submenu_page(
			'tcepo',
			THEMECOMPLETE_EPO_POST_TYPES::instance()::$global_type->labels->name,
			THEMECOMPLETE_EPO_POST_TYPES::instance()::$global_type->labels->name,
			'manage_woocommerce',
			'tcepo-global',
			[ $this, 'admin_epo_submenu_global' ]
		);
		add_action( 'load-edit.php', [ $this, 'tm_load_admin_templates' ] );

	}

	/**
	 * Menu redirect
	 *
	 * @since 5.0
	 */
	public function admin_epo_menu() {

		wp_safe_redirect( admin_url( 'admin.php?page=tcepo-settings' ), 301 );
		exit;

	}

	/**
	 * Submenu "Settings"
	 *
	 * @since 5.0
	 */
	public function preload_settings() {
		global $current_tab;
		$current_tab = THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID;

		// Include settings pages.
		WC_Admin_Settings::get_settings_pages();

		do_action( 'woocommerce_settings_page_init' );
	}

	/**
	 * Submenu "Settings"
	 *
	 * @since 5.0
	 */
	public function admin_epo_submenu_settings() {

		WC_Admin_Settings::output();

	}

	/**
	 * Submenu "Option Templates"
	 *
	 * @param object $post The post object.
	 * @since 6.0
	 */
	public function preload_template_settings( $post ) {
		// Builder meta box.
		add_meta_box( 'tmformfieldsbuilder', esc_html__( 'Extra Product Options Form Builder', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_form_fields_builder_meta_box' ], THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE, 'normal', 'core' );
		// Description meta box (used for getting tinymce).
		add_meta_box( 'thepostexcerpt', esc_html__( 'Description', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_description_meta_box' ], null, 'normal', 'core' );
	}

	/**
	 * Submenu "Global Forms"
	 *
	 * @since 5.0
	 */
	public function admin_epo_submenu_global() {

		wp_safe_redirect( admin_url( 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK ), 301 );
		exit;

	}

	/**
	 * Load scripts
	 *
	 * @since 1.0
	 */
	public function tm_load_scripts() {

		// Load css and javascript files.
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ], 11 );

	}

	/**
	 * Loads plugin functionality
	 *
	 * @since 6.2
	 */
	public function tm_load_admin_templates() {
		global $typenow;
		$post_type = $typenow;

		if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post_type ) {
			// Extra row actions.
			add_filter( 'post_row_actions', [ $this, 'row_actions_template' ], 10, 2 );
		}

	}

	/**
	 * Loads plugin functionality
	 *
	 * @since 1.0
	 */
	public function tm_load_admin() {

		$this->tm_load_scripts();

		// Custom action to populate the filter select box.
		add_action( 'restrict_manage_posts', [ $this, 'tm_restrict_manage_posts' ] );

		// Add screen option.
		$this->tm_add_option();

		// Add meta boxes.
		$this->tm_add_metaboxes();

		// Extra row actions.
		add_filter( 'post_row_actions', [ $this, 'row_actions' ], 10, 2 );

	}

	/**
	 * Add list columns
	 *
	 * @param array $columns The columns array.
	 * @since 1.0
	 */
	public function tm_list_columns( $columns ) {
		$new_columns          = [];
		$new_columns['cb']    = isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox" />';
		$new_columns['title'] = isset( $columns['title'] ) ? $columns['title'] : esc_html__( 'Title', 'woocommerce-tm-extra-product-options' );
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$flags = '';
			foreach ( THEMECOMPLETE_EPO_WPML()->get_active_languages() as $key => $value ) {
				if ( THEMECOMPLETE_EPO_WPML()->get_lang() !== $key ) {
					$flags .= THEMECOMPLETE_EPO_WPML()->get_flag( $key );
				}
			}
			$new_columns['tm_icl_translations'] = '<span class="tm-icl-space">&nbsp;</span>' . $flags;
		}
		$new_columns['priority']            = esc_html__( 'Priority', 'woocommerce-tm-extra-product-options' );
		$new_columns['applied_on']          = esc_html__( 'Applied on', 'woocommerce-tm-extra-product-options' );
		$new_columns['product_cat']         = esc_html__( 'Categories', 'woocommerce-tm-extra-product-options' );
		$new_columns['product_ids']         = esc_html__( 'Products', 'woocommerce-tm-extra-product-options' );
		$new_columns['product_exclude_ids'] = esc_html__( 'Excluded Products', 'woocommerce-tm-extra-product-options' );

		unset( $columns['cb'] );
		unset( $columns['title'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * List column actions
	 *
	 * @param string  $column The column.
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function tm_list_column( $column, $post_id ) {
		switch ( $column ) {

			case 'tm_icl_translations':
				$main_post_id = 0;
				$tm_meta_lang = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
				if ( empty( $tm_meta_lang ) ) {
					$tm_meta_lang = THEMECOMPLETE_EPO_WPML()->get_default_lang();
					$main_post_id = $post_id;
				}
				if ( empty( $main_post_id ) ) {
					$main_post_id = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true );
				}
				THEMECOMPLETE_EPO_WPML()->get_flag( $tm_meta_lang, 1 );
				foreach ( THEMECOMPLETE_EPO_WPML()->get_active_languages() as $key => $value ) {
					if ( $key !== $tm_meta_lang || 'all' === THEMECOMPLETE_EPO_WPML()->get_lang() ) {

						if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $key ) {
							$query = new WP_Query(
								[
									'post_type'      => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
									'post_status'    => [ 'publish', 'draft' ],
									'numberposts'    => -1,
									'posts_per_page' => -1,
									'orderby'        => 'date',
									'order'          => 'asc',
									'no_found_rows'  => true,
									'p'              => $main_post_id,
								]
							);
						} else {

							$meta_query   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $key, '=', 'EXISTS' );
							$meta_query[] = [
								'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
								'value'   => $main_post_id,
								'compare' => '=',
							];

							$query = new WP_Query(
								[
									'post_type'      => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
									'post_status'    => [ 'publish', 'draft' ],
									'numberposts'    => -1,
									'posts_per_page' => -1,
									'orderby'        => 'date',
									'order'          => 'asc',
									'no_found_rows'  => true,
									'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery
								]
							);

						}

						if ( ! empty( $query->posts ) ) {
							THEMECOMPLETE_EPO_WPML()->edit_lang_link( $query->post->ID, $key, $value, $main_post_id, false, false, 1 );
						} elseif ( empty( $query->posts ) ) {
							THEMECOMPLETE_EPO_WPML()->add_lang_link( $main_post_id, $key, $value, false, 1 );
						}
					}
				}
				break;

			case 'applied_on':
				$tm_meta_cat = themecomplete_get_post_meta( $post_id, 'tm_meta_disable_categories', true );
				$tm_meta_id  = apply_filters( 'wc_epo_tm_meta_product_ids', themecomplete_get_post_meta( $post_id, 'tm_meta_product_ids', true ), $post_id );
				if ( is_array( $tm_meta_id ) && 1 === count( $tm_meta_id ) && empty( $tm_meta_id[0] ) ) {
					$tm_meta_id = false;
				}
				if ( $tm_meta_cat ) {
					if ( ! empty( $tm_meta_id ) && is_array( $tm_meta_id ) ) {
						echo '<span class="tc-color-active">' . esc_html__( 'Products', 'woocommerce-tm-extra-product-options' ) . '</span>';
					} else {
						echo '<span class="tc-color-error">' . esc_html__( 'Disabled', 'woocommerce-tm-extra-product-options' ) . '</span>';
					}
				} else {
					$terms = get_the_term_list( $post_id, 'product_cat', '', ' , ', '' );
					if ( is_string( $terms ) ) {
						if ( ! empty( $tm_meta_id ) && is_array( $tm_meta_id ) ) {
							echo '<span class="tc-color-active">' . esc_html__( 'Products and Categories', 'woocommerce-tm-extra-product-options' ) . '</span>';
						} else {
							echo '<span class="tc-color-active">' . esc_html__( 'Categories', 'woocommerce-tm-extra-product-options' ) . '</span>';
						}
					} elseif ( ! $terms ) {
						echo '<span class="tc-color-active">' . esc_html__( 'ALL products', 'woocommerce-tm-extra-product-options' ) . '</span>';
					}
				}

				break;

			case 'product_cat':
				$tm_meta = themecomplete_get_post_meta( $post_id, 'tm_meta_disable_categories', true );
				if ( $tm_meta ) {
					echo '<span class="tc-color-error">' . esc_html__( 'Disabled', 'woocommerce-tm-extra-product-options' ) . '</span>';
				} else {
					$terms = get_the_term_list( $post_id, 'product_cat', '', ' , ', '' );
					if ( is_string( $terms ) ) {
						echo wp_kses_post( $terms );
					} elseif ( ! $terms ) {
						echo '<span class="tc-color-active">' . esc_html__( 'ALL categories', 'woocommerce-tm-extra-product-options' ) . '</span>';
					}
				}
				break;

			case 'priority':
				$post_id = THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id, THEMECOMPLETE_EPO_GLOBAL_POST_TYPE );
				$tm_meta = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
				if ( is_array( $tm_meta ) ) {
					if ( ! isset( $tm_meta['priority'] ) ) {
						$tm_meta['priority'] = 10;
					}
					if ( is_array( $tm_meta['priority'] ) ) {
						$tm_meta['priority'] = $tm_meta['priority'][0];
					}
					echo esc_html( $tm_meta['priority'] );
				}
				break;

			case 'product_ids':
				$tm_meta = apply_filters( 'wc_epo_tm_meta_product_ids', themecomplete_get_post_meta( $post_id, 'tm_meta_product_ids', true ), $post_id );
				if ( is_array( $tm_meta ) && 1 === count( $tm_meta ) && empty( $tm_meta[0] ) ) {
					$tm_meta = false;
				}
				if ( ! empty( $tm_meta ) ) {
					if ( is_array( $tm_meta ) ) {
						if ( 1 === count( $tm_meta ) && ! empty( $tm_meta[0] ) ) {
							$title = get_the_title( $tm_meta[0] );
							echo '<a title="' . esc_attr( $title ) . '" href="' . esc_url( admin_url( 'post.php?action=edit&post=' . esc_attr( $tm_meta[0] ) ) ) . '">' . esc_html( $title ) . '</a>';
						} else {
							$comma_check = false;
							foreach ( $tm_meta as $key => $value ) {
								if ( ! empty( $value ) ) {
									$title = get_the_title( $value );
									if ( $comma_check ) {
										echo ' , ';
									}
									echo '<a class="tm-tooltip" title="' . esc_attr( $title ) . '" href="' . esc_url( admin_url( 'post.php?action=edit&post=' . esc_attr( $value ) ) ) . '">' . esc_html( $value ) . '</a>';
									$comma_check = true;
								}
							}
						}
					}
				} else {
					echo '<span class="tc-color-error">' . esc_html__( 'None', 'woocommerce-tm-extra-product-options' ) . '</span>';
				}
				break;

			case 'product_exclude_ids':
				$tm_meta = apply_filters( 'wc_epo_tm_meta_product_exclude_ids', themecomplete_get_post_meta( $post_id, 'tm_meta_product_exclude_ids', true ), $post_id );
				if ( is_array( $tm_meta ) && 1 === count( $tm_meta ) && empty( $tm_meta[0] ) ) {
					$tm_meta = false;
				}
				if ( ! empty( $tm_meta ) ) {
					if ( is_array( $tm_meta ) ) {
						if ( 1 === count( $tm_meta ) && ! empty( $tm_meta[0] ) ) {
							$title = get_the_title( $tm_meta[0] );
							echo '<a title="' . esc_attr( $title ) . '" href="' . esc_url( admin_url( 'post.php?action=edit&post=' . esc_attr( $tm_meta[0] ) ) ) . '">' . esc_html( $title ) . '</a>';
						} else {
							$comma_check = false;
							foreach ( $tm_meta as $key => $value ) {
								if ( ! empty( $value ) ) {
									$title = get_the_title( $value );
									if ( $comma_check ) {
										echo ' , ';
									}
									echo '<a class="tm-tooltip" title="' . esc_attr( $title ) . '" href="' . esc_url( admin_url( 'post.php?action=edit&post=' . esc_attr( $value ) ) ) . '">' . esc_html( $value ) . '</a>';
									$comma_check = true;
								}
							}
						}
					}
				} else {
					echo '<span class="tc-color-error">' . esc_html__( 'None', 'woocommerce-tm-extra-product-options' ) . '</span>';
				}
				break;

		}

	}

	/**
	 * Handle meta boxes
	 *
	 * @since 1.0
	 */
	public function tm_add_metaboxes() {
		// only continue if we are are on add/edit screen.
		if ( ! $this->tm_list_table || ! $this->tm_list_table->current_action() ) { // @phpstan-ignore-line
			return;
		}

		add_screen_option(
			'layout_columns',
			[
				'max'     => 2,
				'default' => 2,
			]
		);

		// WPML meta box.
		THEMECOMPLETE_EPO_WPML()->add_meta_box();

		// Publish meta box.
		add_meta_box( 'submitdiv', esc_html__( 'Publish' ), [ $this, 'tm_post_submit_meta_box' ], null, 'side', 'core' );

		// Taxonomies meta box.
		if ( $this->tm_list_table ) {
			THEMECOMPLETE_EPO_WPML()->remove_term_filters();

			foreach ( get_object_taxonomies( $this->tm_list_table->screen->post_type ) as $tax_name ) { // @phpstan-ignore-line
				$taxonomy = get_taxonomy( $tax_name );
				if ( ! $taxonomy->show_ui ) {
					continue;
				}
				if ( ! property_exists( $taxonomy, 'meta_box_cb' ) || false === $taxonomy->meta_box_cb ) {
					if ( $taxonomy->hierarchical ) {
						$taxonomy->meta_box_cb = 'post_categories_meta_box';
					} else {
						$taxonomy->meta_box_cb = 'post_tags_meta_box';
					}
				}
				$label = $taxonomy->labels->name;
				if ( ! is_taxonomy_hierarchical( $tax_name ) ) {
					$tax_meta_box_id = 'tagsdiv-' . $tax_name;
				} else {
					$tax_meta_box_id = $tax_name . 'div';
				}
				add_meta_box( $tax_meta_box_id, $label, $taxonomy->meta_box_cb, null, 'side', 'core', [ 'taxonomy' => $tax_name ] );
			}
			THEMECOMPLETE_EPO_WPML()->restore_term_filters();
		}

		// Products include meta box.
		add_meta_box( 'tc-product-search', esc_html__( 'Products', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_product_search_meta_box' ], null, 'side', 'core' );

		// Roles meta box.
		add_meta_box( 'tc-product-roles', esc_html__( 'Roles', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_product_roles_meta_box' ], null, 'side', 'core' );

		// Products exclude meta box.
		add_meta_box( 'tc-product-exclude', esc_html__( 'Exclude Products', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_product_exclude_meta_box' ], null, 'side', 'core' );

		// Description meta box.
		add_meta_box( 'postexcerpt', esc_html__( 'Description', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_description_meta_box' ], null, 'normal', 'core' );

		// Builder meta box.
		add_meta_box( 'tmformfieldsbuilder', esc_html__( 'Extra Product Options Form Builder', 'woocommerce-tm-extra-product-options' ), [ $this, 'tm_form_fields_builder_meta_box' ], null, 'normal', 'core' );
	}

	/**
	 * Description meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function tm_description_meta_box( $post ) {
		$settings = [
			'textarea_name' => 'excerpt',
			'quicktags'     => [ 'buttons' => 'em,strong,link' ],
			'tinymce'       => [
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			],
		];

		wp_editor( wp_specialchars_decode( $post->post_excerpt, ENT_COMPAT ), 'excerpt', apply_filters( 'woocommerce_product_short_description_editor_settings', $settings ) );
		echo '<p>' . esc_html__( 'The description will appear under the title.', 'woocommerce-tm-extra-product-options' ) . '</p>';
	}

	/**
	 * Roles meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function tm_product_roles_meta_box( $post ) {
		$disabled = '';
		if ( ! THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type ) ) {
			$disabled = 'disabled="disabled" ';
		}
		$meta           = $post->tm_meta;
		$enabled_roles  = isset( $meta['enabled_roles'] ) ? $meta['enabled_roles'] : '';
		$disabled_roles = isset( $meta['disabled_roles'] ) ? $meta['disabled_roles'] : '';
		$roles          = themecomplete_get_roles();

		if ( ! is_array( $enabled_roles ) ) {
			$enabled_roles = [ $enabled_roles ];
		}
		if ( ! is_array( $disabled_roles ) ) {
			$disabled_roles = [ $disabled_roles ];
		}

		echo '<div class="message0x0 tc-clearfix">' .
			'<div class="message2x1">' .
			'<label for="tc-enabled-options"><span>' . esc_html__( 'Enabled roles for this form', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
			'</div>' .
			'<div class="message2x2">' .
			'<select id="tc-enabled-options" name="tm_meta_enabled_roles[]" class="multiselect wc-enhanced-select" multiple="multiple">';
		foreach ( $roles as $option_key => $option_text ) {
			echo '<option value="' . esc_attr( $option_key ) . '" ';
			selected( in_array( $option_key, $enabled_roles, true ), 1, true );
			echo '>' . esc_html( $option_text ) . '</option>';
		}

		echo '</select>' .
			'</div>' .
			'</div>';
		echo '<div class="message0x0 tc-clearfix">' .
			'<div class="message2x1">' .
			'<label for="tc-disabled-options"><span>' . esc_html__( 'Disabled roles for this form', 'woocommerce-tm-extra-product-options' ) . '</span></label>' .
			'</div>' .
			'<div class="message2x2">' .
			'<select id="tc-disabled-options" name="tm_meta_disabled_roles[]" class="multiselect wc-enhanced-select" multiple="multiple">';
		foreach ( $roles as $option_key => $option_text ) {
			echo '<option value="' . esc_attr( $option_key ) . '" ';
			selected( in_array( $option_key, $disabled_roles, true ), 1, true );
			echo '>' . esc_html( $option_text ) . '</option>';
		}
		echo '</select>' .
			'</div>' .
			'</div>';
	}

	/**
	 * Exclude meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function tm_product_exclude_meta_box( $post ) {
		$disabled = false;
		$meta     = $post->tm_meta;
		if ( ! THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type ) ) {
			$disabled = true;
		}
		?>
		<label for="tm_product_exclude_ids"><?php esc_html_e( 'Exclude Product(s) from the form', 'woocommerce-tm-extra-product-options' ); ?></label>
		<select class="wc-product-search w100" multiple="multiple" id="tm_product_exclude_ids" name="tm_meta_product_exclude_ids[]" data-placeholder="<?php esc_html_e( 'Search for a product&hellip;', 'woocommerce-tm-extra-product-options' ); ?>" data-action="woocommerce_json_search_products_and_variations">
		<?php
		$_ids        = isset( $meta['product_exclude_ids'] ) ? $meta['product_exclude_ids'] : null;
		$product_ids = ! empty( $_ids ) ? array_map( 'absint', $_ids ) : null;
		if ( $product_ids ) {
			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( is_object( $product ) ) {
					echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $product->get_formatted_name() ) . '</option>';
				}
			}
		}
		?>
		</select>
		<?php
	}

	/**
	 * Product search meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function tm_product_search_meta_box( $post ) {
		$disabled = false;
		$meta     = $post->tm_meta;
		if ( ! THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type ) ) {
			$disabled = true;
		}
		?>
		<h3 id="tc-disabled-categories" class="hidden">
			<?php
			if ( ! empty( $disabled ) && (int) 1 === (int) $meta['disable_categories'] ) {
				?>

				<input type="hidden" value="1" id="tm_meta_disable_categories" name="tm_meta_disable_categories"/>
				<?php
			}
			?>
			<label for="tm_meta_disable_categories">
				<input type="checkbox"<?php disabled( $disabled, true, true ); ?> value="1" id="tm_meta_disable_categories" name="tm_meta_disable_categories" class="meta-disable-categories" <?php checked( $meta['disable_categories'], 1 ); ?>/>
				<?php esc_html_e( 'Disable categories', 'woocommerce-tm-extra-product-options' ); ?>
			</label></h3>
		<label for="tm_product_ids"><?php esc_html_e( 'Select the Product(s) to apply the options', 'woocommerce-tm-extra-product-options' ); ?></label>
		<?php
		// check for correct saved meta.
		$tm_meta_product_ids = themecomplete_get_post_meta( $post->ID, 'tm_meta_product_ids', true );
		if ( 'auto-draft' !== $post->post_status && $tm_meta_product_ids !== $meta['product_ids'] ) {
			echo '<div class="tc-info tc-error">';
			esc_html_e( 'Meta data not correctly saved. Please save the product!', 'woocommerce-tm-extra-product-options' );
			echo '</div>';
		}
		?>
		<select class="wc-product-search w100"
				multiple="multiple"
				id="tm_product_ids"
				name="tm_meta_product_ids[]"
				data-placeholder="<?php esc_html_e( 'Search for a product&hellip;', 'woocommerce-tm-extra-product-options' ); ?>"
				data-action="woocommerce_json_search_products_and_variations">
			<?php
			$_ids        = isset( $meta['product_ids'] ) ? $meta['product_ids'] : null;
			$product_ids = ! empty( $_ids ) ? array_map( 'absint', $_ids ) : null;
			if ( $product_ids ) {
				foreach ( $product_ids as $product_id ) {
					$product = wc_get_product( $product_id );
					if ( is_object( $product ) ) {
						echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $product->get_formatted_name() ) . '</option>';
					}
				}
			}
			?>
		</select>
		<?php
	}

	/**
	 * Builder meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function tm_form_fields_builder_meta_box( $post ) {

		// used in scripts template.
		$this->post = $post;

		$basetype = $this->basetype;
		if ( false === $basetype ) {
			if ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE === $post->post_type || THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $post->post_type ) {
				$basetype = $post->post_type;
			} else {
				$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			}
		}
		?>
		<div id="tmformfieldsbuilderwrap" class="tc-wrapper">
			<?php
			$wpml_is_original_product = true;
			$id_for_meta              = $post->ID;
			if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
				$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type, $basetype );
				if ( ! $wpml_is_original_product ) {
					$id_for_meta = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post->ID, $post->post_type, $basetype ) );
				}
			}

			$show_buttons = 'auto-draft' !== $post->post_status;

			// builder toolbar.
			echo '<div class="builder-selector">'
				. '<div class="tc-row">';
			// builder toolbar left.
			echo '<div class="tc-cell tc-col-auto">';
			if ( $wpml_is_original_product ) {
				if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
					echo '<button type="button" class="tc-add-element tc tc-button large" title="' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="tcfa tcfa-plus"></i> ' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
					echo '<button type="button" id="builder_add_section" class="builder_add_section tc tc-button large"><span class="tc-button-label"><i class="tcfa tcfa-layer-group"></i> ' . esc_html__( 'Add section', 'woocommerce-tm-extra-product-options' ) . '</span></button>'
						. '<button type="button" id="builder_add_variation" class="builder_add_variation tc tc-button large tm-hidden"><span class="tc-button-label"><i class="tcfa tcfa-bullseye"></i> ' . esc_html__( 'Style variations', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
				}
			}
			echo '</div>';
			// builder toolbar right.
			echo '<div class="tc-cell tc-col-auto tm-text-right">';
			if ( $show_buttons ) {
				if ( $wpml_is_original_product || ( empty( $_GET['tmparentpostid'] ) && empty( $_GET['tmaddlang'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					echo '<button type="button" id="builder_save" class="tc tc-button builder-save"><span class="tc-button-label">' . esc_html__( 'Save', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
					if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
						echo '<button type="button" id="builder_import" class="tc tc-button alt builder-import"><span class="tc-button-label">' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' ) . '</span></button>'
						. '<button type="button" id="builder_export" class="tc tc-button alt builder-export"><span class="tc-button-label">' . esc_html__( 'Export CSV', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
					}
				}
				echo '<button type="button" id="builder-fullsize-close" class="tc tc-button alt builder-fullsize-close" title="' . esc_attr__( 'Close', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="tcfa tcfa-times"></i></span></button>';
				echo '<button type="button" id="builder-fullsize" class="tc tc-button alt builder-fullsize" title="' . esc_attr__( 'Fullsize', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="tcfa tcfa-window-maximize"></i></span></button>';

				echo '<input id="builder_import_file" name="builder_import_file" type="file" class="builder-import-file">';
			} else {
				if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
					if ( $post->post_type === $basetype ) {
						echo '<div class="tc-warning-text">'
							. esc_html__( 'Please save the form before importing options!', 'woocommerce-tm-extra-product-options' )
							. '</div>';
					} else {
						echo '<div class="tc-warning-text">'
							. esc_html__( 'Please save the product before importing options!', 'woocommerce-tm-extra-product-options' )
							. '</div>';
					}
				} else {
					echo '<button type="button" id="builder-fullsize-close" class="tc tc-button alt builder-fullsize-close" title="' . esc_attr__( 'Close', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="tcfa tcfa-times"></i></span></button>';
					echo '<button type="button" id="builder-fullsize" class="tc tc-button alt builder-fullsize" title="' . esc_attr__( 'Fullsize', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="tcfa tcfa-window-maximize"></i></span></button>';
				}
			}
			echo '</div>'
				. '</div>'
				. '</div>';

			echo '<div class="builder-layout tm-hidden' . ( ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE === $basetype ) ? ' builder-template' : '' ) . '">';
			THEMECOMPLETE_EPO_BUILDER()->print_saved_elements( $id_for_meta, $post->ID, $wpml_is_original_product );
			echo '</div>';

			if ( $wpml_is_original_product && ( 'product' !== $this->post->type || 'yes' !== THEMECOMPLETE_EPO()->tm_epo_global_hide_product_builder_mode ) ) {
				echo '<div id="tc-welcome" class="tc-welcome">';
				if ( $show_buttons ) {
					echo '<div class="tc-info-text">'
							. esc_html__( 'No elements found!', 'woocommerce-tm-extra-product-options' )
							. '<br>'
							. esc_html__( 'Start adding elements', 'woocommerce-tm-extra-product-options' )
							. '</div>';
				} else {
					echo '<div class="tc-info-text">'
							. esc_html__( 'No elements found!', 'woocommerce-tm-extra-product-options' )
							. '</div>';
				}

				echo '<div class="tc-buttons">';
				echo '<button type="button" class="tm-animated tc-add-element tc tc-button large" title="' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="ddd"></i>' . esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
				if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
					echo '<button type="button" class="tm-animated tc-add-section tc tc-button large" title="' . esc_html__( 'Add section', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="ddd"></i>' . esc_html__( 'Add section', 'woocommerce-tm-extra-product-options' ) . '</span></button>'
									. '<button type="button" class="tm-animated builder_add_variation tm-hidden tc tc-button large" href="#"><span class="tc-button-label"><i class="ddd"></i> ' . esc_html__( 'Style variations', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
				}
				if ( $show_buttons ) {
					if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
						echo '<button type="button" class="tm-animated alt tc-add-import-csv tc tc-button large" title="' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="ddd"></i>' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' ) . '</span></button>';
					}
				} else {
					if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
						echo '<button type="button" class="disabled tm-animated alt tc-add-import-csv tc tc-button large" title="' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' ) . '"><span class="tc-button-label"><i class="ddd"></i>' . esc_html__( 'Import CSV', 'woocommerce-tm-extra-product-options' );
						if ( $post->post_type === $basetype ) {
							echo '<div class="tc-warning-text">'
									. esc_html__( 'Please save the form before importing options!', 'woocommerce-tm-extra-product-options' )
									. '</div>';
						} else {
							echo '<div class="tc-warning-text">'
									. esc_html__( 'Please save the product before importing options!', 'woocommerce-tm-extra-product-options' )
									. '</div>';
						}
						echo '</span></button>';
					}
				}
				echo '</div>';
				echo '</div>';
				if ( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $basetype ) {
					echo '<div class="builder-add-section-action">'
						. '<div class="tm-add-section-action"><button type="button" title="' . esc_html__( 'Add element in a new section', 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-section-and-element tc-button tc large"><span class="tc-button-label"><i class="tcfa tcfa-plus-square"></i> ' . esc_html__( 'Add element in a new section', 'woocommerce-tm-extra-product-options' ) . '</span></button></div>'
						. '</div>';
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Publish meta box
	 *
	 * @param object $post The post object.
	 * @since 1.0
	 */
	public function tm_post_submit_meta_box( $post ) {
		$meta = $post->tm_meta;
		?>
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="minor-publishing-actions">
					<div id="save-action">
						<span class="spinner"></span>
					</div>
					<div class="clear"></div>
				</div>
				<div id="misc-publishing-actions">
					<div class="misc-pub-section misc-pub-priority" id="priority">
						<?php if ( THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type ) ) { ?>
							<?php echo esc_attr__( 'Priority', 'woocommerce-tm-extra-product-options' ); ?>:
							<input type="number" value="<?php echo esc_attr( (int) $meta['priority'] ); ?>" maxlength="3"
								id="tm_meta_priority" name="tm_meta[priority]" class="meta-priority" min="1"
								step="1"/>
						<?php } ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">
					<?php
					if ( current_user_can( 'delete_post', $post->ID ) ) {
						if ( ! EMPTY_TRASH_DAYS ) {
							$delete_text = esc_html__( 'Delete Permanently', 'woocommerce-tm-extra-product-options' );
						} else {
							$delete_text = esc_html__( 'Move to Trash', 'woocommerce-tm-extra-product-options' );
						}
						?>
						<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_html( $delete_text ); ?></a>
					<?php } ?>
				</div>
				<div id="publishing-action">
					<span class="spinner"></span>
					<?php
					if ( ! in_array( $post->post_status, [ 'publish', 'future', 'private' ], true ) || (int) 0 === (int) $post->ID ) {
						if ( $meta['can_publish'] ) :
							?>
							<input name="original_publish" type="hidden" id="original_publish"
								value="<?php esc_attr_e( 'Publish', 'woocommerce-tm-extra-product-options' ); ?>"/>
							<?php submit_button( esc_html__( 'Publish', 'woocommerce-tm-extra-product-options' ), 'primary button-large', 'publish', false, [ 'accesskey' => 'p' ] ); ?>
							<?php
						else :
							?>
							<input name="original_publish" type="hidden" id="original_publish"
								value="<?php esc_attr_e( 'Submit for Review', 'woocommerce-tm-extra-product-options' ); ?>"/>
							<?php submit_button( esc_html__( 'Submit for Review', 'woocommerce-tm-extra-product-options' ), 'primary button-large', 'publish', false, [ 'accesskey' => 'p' ] ); ?>
							<?php
						endif;
					} else {
						?>
						<input name="original_publish" type="hidden" id="original_publish"
							value="<?php esc_attr_e( 'Update', 'woocommerce-tm-extra-product-options' ); ?>"/>
						<input name="save" type="submit" class="button button-primary button-large" id="publish"
							accesskey="p" value="<?php esc_attr_e( 'Update', 'woocommerce-tm-extra-product-options' ); ?>"/>
						<?php
					}
					?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Pre-render actions
	 *
	 * @since 1.0
	 */
	public function tm_plugins_loaded() {
		if ( ! isset( $_GET['page'] ) || ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK !== $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		// Remove cforms plugin tinymce buttons.
		remove_action( 'init', 'cforms_addbuttons' );
	}

	/**
	 * Pre-render actions
	 *
	 * @since 1.0
	 */
	public function tm_init() {

		if ( ! isset( $_GET['page'] ) || ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK !== $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// remove WooCommerce Product Stock Alert actions.
		if ( class_exists( 'WOO_Product_Stock_Alert' ) ) {
			global $WOO_Product_Stock_Alert; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			if ( $WOO_Product_Stock_Alert && property_exists( $WOO_Product_Stock_Alert, 'admin' ) && $WOO_Product_Stock_Alert->admin ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				remove_action( 'save_post', [ $WOO_Product_Stock_Alert->admin, 'check_product_stock_status' ], 5 ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}

	}

	/**
	 * Pre-render actions
	 *
	 * @since 1.0
	 */
	public function tm_admin_init() {

		// Custom filters for the edit and delete links.
		add_filter( 'get_edit_post_link', [ $this, 'tm_get_edit_post_link' ], 10, 3 );
		add_filter( 'get_delete_post_link', [ $this, 'tm_get_delete_post_link' ], 10, 3 );

		// Check if we are on the plugin page.
		if ( ! isset( $_GET['page'] ) || ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK !== $_GET['page'] ) ) {
			return;
		}

		// remove annoying messages that mess up the interface.
		remove_all_actions( 'admin_notices' );
		if ( class_exists( 'WC_Admin_Notices' ) && method_exists( 'WC_Admin_Notices', 'remove_all_notices' ) ) {
			WC_Admin_Notices::remove_all_notices();
		}

		// WPML: set correct language according to post.
		THEMECOMPLETE_EPO_WPML()->set_post_lang();

		// save meta data.
		add_action( 'save_post', [ $this, 'tm_save_postdata' ], 1, 2 );

		if ( ! class_exists( 'WP_List_Table' ) ) {
			wp_die( esc_html__( 'Something went wrong with WordPress.', 'woocommerce-tm-extra-product-options' ) );
		}

		global $bulk_counts, $bulk_messages, $general_messages;

		$post_type        = 'product';
		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object ) {
			wp_die( esc_html__( 'WooCommerce is not enabled!', 'woocommerce-tm-extra-product-options' ) );
		}
		if ( ! current_user_can( $post_type_object->cap->edit_posts ) ) {
			wp_die( esc_html__( 'Cheatin&#8217; uh?', 'woocommerce-tm-extra-product-options' ) );
		}

		$this->tm_list_table = $this->get_wp_list_table( 'THEMECOMPLETE_EPO_ADMIN_Global_List_Table' );
		$post_type           = $this->tm_list_table->screen->post_type;
		$pagenum             = $this->tm_list_table->get_pagenum();
		$parent_file         = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		$submenu_file        = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		$post_new_file       = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&action=add';
		$doaction            = $this->tm_list_table->current_action();
		$sendback            = remove_query_arg( [ 'trashed', 'untrashed', 'deleted', 'locked', 'ids' ], wp_get_referer() );
		if ( ! $sendback ) {
			$sendback = admin_url( $parent_file );
		}
		$sendback = add_query_arg( 'paged', $pagenum, $sendback );

		$sendback = esc_url_raw( $sendback );

		// Bulk actions.
		if ( $doaction && isset( $_REQUEST['tm_bulk'] ) ) {
			check_admin_referer( 'bulk-posts' );

			if ( 'delete_all' === $doaction && isset( $_REQUEST['post_status'] ) ) {
				$post_status = preg_replace( '/[^a-z0-9_-]+/i', '', sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) );
				if ( get_post_status_object( $post_status ) ) { // Check if the post status exists first.
					$post_ids = THEMECOMPLETE_EPO_HELPER()->get_cached_posts(
						[
							'post_type'   => $post_type,
							'post_status' => [ $post_status ],
							'numberposts' => -1,
							'fields'      => 'ids', // Only get post IDs.
						]
					);
				}
				$doaction = 'delete';
			} elseif ( isset( $_REQUEST['ids'] ) ) {
				$post_ids = explode( ',', wp_unslash( $_REQUEST['ids'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} elseif ( ! empty( $_REQUEST['post'] ) ) {
				$post_ids = array_map( 'intval', $_REQUEST['post'] );
			}
			if ( ! isset( $post_ids ) ) {
				wp_safe_redirect( $sendback );
				exit;
			}

			switch ( $doaction ) {
				case 'trash':
					$trashed = 0;
					$locked  = 0;

					foreach ( (array) $post_ids as $post_id ) {
						if ( ! current_user_can( 'delete_post', $post_id ) ) {
							wp_die( esc_html__( 'You are not allowed to move this item to the Trash.', 'woocommerce-tm-extra-product-options' ) );
						}
						if ( wp_check_post_lock( $post_id ) ) {
							$locked ++;
							continue;
						}

						if ( ! wp_trash_post( $post_id ) ) {
							wp_die( esc_html__( 'Error in moving to Trash.', 'woocommerce-tm-extra-product-options' ) );
						}

						$trashed ++;
					}

					$sendback = add_query_arg(
						[
							'from_bulk' => 1,
							'trashed'   => $trashed,
							'ids'       => join( ',', $post_ids ),
							'locked'    => $locked,
						],
						$sendback
					);
					break;
				case 'untrash':
					$untrashed = 0;
					foreach ( (array) $post_ids as $post_id ) {
						if ( ! current_user_can( 'delete_post', $post_id ) ) {
							wp_die( esc_html__( 'You are not allowed to restore this item from the Trash.', 'woocommerce-tm-extra-product-options' ) );
						}

						if ( ! wp_untrash_post( $post_id ) ) {
							wp_die( esc_html__( 'Error in restoring from Trash.', 'woocommerce-tm-extra-product-options' ) );
						}

						$untrashed ++;
					}
					$sendback = add_query_arg(
						[
							'from_bulk' => 1,
							'untrashed' => $untrashed,
						],
						$sendback
					);
					break;
				case 'delete':
					$deleted = 0;
					foreach ( (array) $post_ids as $post_id ) {
						$post_del = get_post( $post_id );

						if ( ! current_user_can( 'delete_post', $post_id ) ) {
							wp_die( esc_html__( 'You are not allowed to delete this item.', 'woocommerce-tm-extra-product-options' ) );
						}

						if ( 'attachment' === $post_del->post_type ) {
							if ( ! wp_delete_attachment( $post_id ) ) {
								wp_die( esc_html__( 'Error in deleting.', 'woocommerce-tm-extra-product-options' ) );
							}
						} else {
							if ( ! wp_delete_post( $post_id ) ) {
								wp_die( esc_html__( 'Error in deleting.', 'woocommerce-tm-extra-product-options' ) );
							}
						}
						$deleted ++;
					}
					$sendback = add_query_arg(
						[
							'from_bulk' => 1,
							'deleted'   => $deleted,
						],
						$sendback
					);

					break;
				case 'edit':
					if ( isset( $_REQUEST['bulk_edit'] ) ) {

						$done = bulk_edit_posts( $_REQUEST );

						if ( is_array( $done ) ) {
							$done['updated'] = count( $done['updated'] );
							$done['skipped'] = count( $done['skipped'] );
							$done['locked']  = count( $done['locked'] );
							$sendback        = add_query_arg( $done, $sendback );
						}
					}
					break;
			}

			$sendback = remove_query_arg( [ 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ], $sendback );
			$sendback = esc_url_raw( $sendback );
			wp_safe_redirect( $sendback );
			exit;
		} elseif ( $doaction && ! isset( $_REQUEST['tm_bulk'] ) ) { // Single actions.

			if ( isset( $_GET['post'] ) ) {
				$post_id = absint( $_GET['post'] );
				$post_ID = $post_id;
			} elseif ( isset( $_POST['post_ID'] ) ) {
				$post_id = absint( $_POST['post_ID'] );
				$post_ID = $post_id;
			} elseif ( isset( $_REQUEST['ids'] ) ) {
				$post_id = absint( $_REQUEST['ids'] );
				$post_ID = $post_id;
			} else {
				$post_id = 0;
				$post_ID = 0;
			}
			global $post;
			$post             = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$post_type        = null;
			$post_type_object = null;

			if ( $post_id ) {
				$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
			if ( $post ) {
				$post_type = $post->post_type;
				if ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE !== $post_type ) {
					$edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );
					wp_safe_redirect( $edit_link );
					exit;
				}
				$post_type_object = get_post_type_object( $post_type );
			}

			switch ( $doaction ) {
				case 'export':
					$this->tm_export_form_action( $post_id );
					if ( isset( $_SERVER['REQUEST_URI'] ) ) {
						wp_safe_redirect(
							esc_url_raw(
								add_query_arg(
									'message',
									21,
									remove_query_arg(
										[ 'action', 'post', '_wp_http_referer', '_wpnonce' ],
										esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
									)
								)
							)
						);
					}
					exit;
				case 'clone':
					$this->tm_clone_form_action( $post_id );
					if ( isset( $_SERVER['REQUEST_URI'] ) ) {
						wp_safe_redirect(
							esc_url_raw(
								remove_query_arg(
									[ 'action', 'post', '_wp_http_referer', '_wpnonce' ],
									esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
								)
							)
						);
					}
					exit;
				case 'trash':
					check_admin_referer( 'trash-post_' . $post_id );

					if ( ! $post ) {
						wp_die( esc_html__( 'The item you are trying to move to the Trash no longer exists.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! $post_type_object ) {
						wp_die( esc_html__( 'Unknown post type.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! current_user_can( 'delete_post', $post_id ) ) {
						wp_die( esc_html__( 'You are not allowed to move this item to the Trash.', 'woocommerce-tm-extra-product-options' ) );
					}

					$user_id = wp_check_post_lock( $post_id );
					if ( $user_id ) {
						$user = get_userdata( $user_id );
						/* translators: %s: User's display name. */
						wp_die( sprintf( esc_html__( 'You cannot move this item to the Trash. %s is currently editing.', 'woocommerce-tm-extra-product-options' ), esc_html( $user->display_name ) ) );
					}

					if ( ! wp_trash_post( $post_id ) ) {
						wp_die( esc_html__( 'Error in moving to Trash.', 'woocommerce-tm-extra-product-options' ) );
					}

					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								[
									'trashed' => 1,
									'ids'     => $post_id,
								],
								$sendback
							)
						)
					);
					exit;

				case 'untrash':
					check_admin_referer( 'untrash-post_' . $post_id );

					if ( ! $post ) {
						wp_die( esc_html__( 'The item you are trying to restore from the Trash no longer exists.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! $post_type_object ) {
						wp_die( esc_html__( 'Unknown post type.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! current_user_can( 'delete_post', $post_id ) ) {
						wp_die( esc_html__( 'You are not allowed to move this item out of the Trash.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! wp_untrash_post( $post_id ) ) {
						wp_die( esc_html__( 'Error in restoring from Trash.', 'woocommerce-tm-extra-product-options' ) );
					}

					wp_safe_redirect( esc_url_raw( add_query_arg( 'untrashed', 1, $sendback ) ) );
					exit;

				case 'delete':
					check_admin_referer( 'delete-post_' . $post_id );

					if ( ! $post ) {
						wp_die( esc_html__( 'This item has already been deleted.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! $post_type_object ) {
						wp_die( esc_html__( 'Unknown post type.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( ! current_user_can( 'delete_post', $post_id ) ) {
						wp_die( esc_html__( 'You are not allowed to delete this item.', 'woocommerce-tm-extra-product-options' ) );
					}

					$force = ! EMPTY_TRASH_DAYS;
					if ( 'attachment' === $post->post_type ) {
						$force = ( $force || ( defined( 'MEDIA_TRASH' ) && ! MEDIA_TRASH ) );
						if ( ! wp_delete_attachment( $post_id, $force ) ) {
							wp_die( esc_html__( 'Error in deleting.', 'woocommerce-tm-extra-product-options' ) );
						}
					} else {
						if ( ! wp_delete_post( $post_id, $force ) ) {
							wp_die( esc_html__( 'Error in deleting.', 'woocommerce-tm-extra-product-options' ) );
						}
					}

					wp_safe_redirect( esc_url_raw( add_query_arg( 'deleted', 1, $sendback ) ) );
					exit;
				case 'editpost':
					check_admin_referer( 'update-post_' . $post_id );

					$post_id = edit_post();

					// Session cookie flag that the post was saved.
					if ( isset( $_COOKIE[ 'wp-saving-post-' . $post_id ] ) ) {
						setcookie( 'wp-saving-post-' . $post_id, 'saved' );
					}

					$this->redirect_post( $post_id );
					exit;
				case 'edit':
					if ( empty( $post_id ) ) {
						wp_safe_redirect( admin_url( $parent_file ) );
						exit;
					}

					if ( ! $post ) {
						wp_die( esc_html__( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?', 'woocommerce-tm-extra-product-options' ) );
					}
					if ( ! $post_type_object ) {
						wp_die( esc_html__( 'Unknown post type.', 'woocommerce-tm-extra-product-options' ) );
					}
					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						wp_die( esc_html__( 'You are not allowed to edit this item.', 'woocommerce-tm-extra-product-options' ) );
					}

					if ( 'trash' === $post->post_status ) {
						wp_die( esc_html__( 'You can&#8217;t edit this item because it is in the Trash. Please restore it and try again.', 'woocommerce-tm-extra-product-options' ) );
					}
					break;
				case 'add':
					$post_type        = $this->tm_list_table->screen->post_type;
					$post_type_object = get_post_type_object( $post_type );
					if ( ! current_user_can( $post_type_object->cap->edit_posts ) || ! current_user_can( $post_type_object->cap->create_posts ) ) {
						wp_die( esc_html__( 'Cheatin&#8217; uh?', 'woocommerce-tm-extra-product-options' ) );
					}

					break;

				case 'import':
					$this->import();
					break;
				case 'download':
					$this->download();
					break;
			}
		} elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			wp_safe_redirect(
				esc_url_raw(
					remove_query_arg(
						[ '_wp_http_referer', '_wpnonce' ],
						esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
					)
				)
			);
			exit;
		}

		// We get here if we are in the list view.
		$bulk_counts = [
			'updated'   => isset( $_REQUEST['updated'] ) ? absint( $_REQUEST['updated'] ) : 0,
			'locked'    => isset( $_REQUEST['locked'] ) ? absint( $_REQUEST['locked'] ) : 0,
			'deleted'   => isset( $_REQUEST['deleted'] ) ? absint( $_REQUEST['deleted'] ) : 0,
			'trashed'   => isset( $_REQUEST['trashed'] ) ? absint( $_REQUEST['trashed'] ) : 0,
			'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
		];

		$bulk_messages               = [];
		$bulk_messages[ $post_type ] = [
			/* translators: %s: Number of posts. */
			'updated'   => esc_html( _n( '%s post updated.', '%s posts updated.', $bulk_counts['updated'], 'default' ) ),
			/* translators: %s: Number of posts. */
			'locked'    => esc_html( _n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', $bulk_counts['locked'], 'default' ) ),
			/* translators: %s: Number of posts. */
			'deleted'   => esc_html( _n( '%s post permanently deleted.', '%s posts permanently deleted.', $bulk_counts['deleted'], 'default' ) ),
			/* translators: %s: Number of posts. */
			'trashed'   => esc_html( _n( '%s post moved to the Trash.', '%s posts moved to the Trash.', $bulk_counts['trashed'], 'default' ) ),
			/* translators: %s: Number of posts. */
			'untrashed' => esc_html( _n( '%s post restored from the Trash.', '%s posts restored from the Trash.', $bulk_counts['untrashed'], 'default' ) ),
		];
		$bulk_counts                 = array_filter( $bulk_counts );

		$general_messages               = [];
		$general_messages[ $post_type ] = [
			21 => esc_html__( 'The selected form does not contain any sections', 'woocommerce-tm-extra-product-options' ),
		];
		$general_messages               = array_filter( $general_messages );

	}

	/**
	 * Redirect action
	 *
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	private function redirect_post( $post_id = 0 ) {
		$edit_post_link = admin_url( 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . "&action=edit&post=$post_id" );
		if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$status = get_post_status( $post_id );

			if ( isset( $_POST['publish'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				switch ( $status ) {
					case 'pending':
						$message = 8;
						break;
					case 'future':
						$message = 9;
						break;
					default:
						$message = 6;
				}
			} else {
				$message = 'draft' === $status ? 10 : 1;
			}

			$location = add_query_arg( 'message', $message, $edit_post_link );

		} else {
			$location = add_query_arg( 'message', 4, $edit_post_link );
		}
		$location = esc_url_raw( $location );

		wp_safe_redirect( apply_filters( 'redirect_post_location', $location, $post_id ) );

		exit;
	}

	/**
	 * Generate delete link
	 *
	 * @param string  $url The delete link.
	 * @param integer $post_id The post id.
	 * @param boolean $force_delete Whether to bypass the Trash and force deletion.
	 * @since 1.0
	 */
	public function tm_get_delete_post_link( $url, $post_id, $force_delete ) {
		// check we're in the right place, otherwise return.
		if ( ! ( ( isset( $_GET['page'] ) && THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK === $_GET['page'] ) || ( isset( $_POST['screen'] ) && THEMECOMPLETE_EPO_GLOBAL_POST_TYPE === $_POST['screen'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $url;
		}
		$vars        = [];
		$decoded_url = str_replace( '&amp;', '&', $url );
		$decoded_url = str_replace( '?', '&', $decoded_url );
		wp_parse_str( $decoded_url, $vars );
		if ( isset( $vars['action'] ) && isset( $vars['_wpnonce'] ) ) {
			if ( 'delete' === $vars['action'] ) {
				$url = admin_url( 'edit.php?post_type=product&amp;page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . "&amp;action=delete&amp;post=$post_id&amp;_wpnonce=" . $vars['_wpnonce'] );
			}
			if ( 'trash' === $vars['action'] ) {
				$url = admin_url( 'edit.php?post_type=product&amp;page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . "&amp;action=trash&amp;post=$post_id&amp;_wpnonce=" . $vars['_wpnonce'] );
			}
		}

		return $url;

	}

	/**
	 * Generate edit link
	 *
	 * @param string  $url The url link.
	 * @param integer $post_id The post id.
	 * @param string  $context The context.
	 * @since 1.0
	 */
	public function tm_get_edit_post_link( $url, $post_id, $context ) {
		// check we're in the right place, otherwise return.
		if ( ! ( ( isset( $_GET['page'] ) && THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK === $_GET['page'] ) || ( isset( $_POST['screen'] ) && THEMECOMPLETE_EPO_GLOBAL_POST_TYPE === $_POST['screen'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $url;
		}
		$vars        = [];
		$decoded_url = str_replace( '&amp;', '&', $url );
		$decoded_url = str_replace( '?', '&', $decoded_url );
		wp_parse_str( $decoded_url, $vars );
		if ( isset( $vars['action'] ) ) {
			if ( 'edit' === $vars['action'] ) {
				$url = admin_url( 'edit.php?post_type=product&amp;page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . "&amp;action=edit&amp;post=$post_id" );
			}
		}

		return $url;
	}

	/**
	 * Populate the filter select box.
	 *
	 * @since 1.0
	 */
	public function tm_restrict_manage_posts() {
		// check we're in the right place, otherwise return.
		if ( ! isset( $_GET['page'] ) || ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK !== $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		global $typenow, $wp_query;

		$custom_post_taxonomies = get_object_taxonomies( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE );

		if ( is_array( $custom_post_taxonomies ) && count( $custom_post_taxonomies ) > 0 ) {
			echo '<input class="tc-filter-toggle" type="checkbox" id="tc-filter-toggle">';
			echo '<label class="button tc-filter-label" for=tc-filter-toggle>';
			echo '<span class="tc-filter-show">' . esc_html__( 'Show filters', 'woocommerce-tm-extra-product-options' ) . '</span>';
			echo '<span class="tc-filter-hide">' . esc_html__( 'Hide filters', 'woocommerce-tm-extra-product-options' ) . '</span>';
			echo '</label>';
			echo '<div class="tc-filter-content">';
			foreach ( $custom_post_taxonomies as $tax ) {

				if ( 'translation_priority' === $tax ) {
					continue;
				}

				$args = [
					'pad_counts'         => 1,
					'show_count'         => 0,
					'hierarchical'       => 1,
					'hide_empty'         => 0,
					'show_uncategorized' => 1,
					'orderby'            => 'name',
					'selected'           => isset( $wp_query->query_vars[ $tax ] ) ? $wp_query->query_vars[ $tax ] : '',
					'menu_order'         => false,
					'show_option_none'   => esc_html__( 'Filter by', 'woocommerce-tm-extra-product-options' ) . ' ' . $tax,
					'option_none_value'  => '',
					'value_field'        => 'slug',
					'taxonomy'           => $tax,
					'name'               => esc_attr( $tax ),
					'class'              => 'dropdown_' . esc_attr( $tax ),
				];

				wp_dropdown_categories( $args );

			}
			echo '</div>';
		}

	}

	/**
	 * Screen option
	 *
	 * @since 1.0
	 */
	public function tm_add_option() {
		// only continue if we are are on list screen.
		if ( $this->tm_list_table && $this->tm_list_table->current_action() ) { // @phpstan-ignore-line
			return;
		}
		$option = 'per_page';

		$args = [
			'label'   => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
			'default' => 20,
			'option'  => 'edit_' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE . '_per_page',
		];
		add_screen_option( $option, $args );
	}

	/**
	 * Adds our custom screen id to WooCommerce so that we can load needed WooCommerce files.
	 *
	 * @param array $screen_ids The array of screen ids.
	 * @since 1.0
	 */
	public function woocommerce_screen_ids( $screen_ids ) {
		$screen_ids[] = 'product_page_' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		$screen_ids[] = sanitize_title( esc_attr__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ) ) . '_page_tcepo-settings';
		$screen_ids[] = THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE;
		return $screen_ids;
	}

	/**
	 * Returns the google font URL
	 *
	 * @since 4.8.5
	 */
	public function admin_font_url() {
		$font_url = '';

		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		 */
		if ( 'off' !== esc_html_x( 'on', 'Google font: on or off', 'woocommerce-tm-extra-product-options' ) ) {
			$font_url = add_query_arg( 'family', rawurlencode( 'Roboto:400,100,300,700,900,400italic,700italic&subset=latin,latin-ext' ), '//fonts.googleapis.com/css' );
		}

		return $font_url;
	}

	/**
	 * Enqueue plugin css and dequeue unwanted woocommerce css styles
	 *
	 * @param integer $override true 1 or 0.
	 * @since 1.0
	 */
	public function register_admin_styles( $override = 0 ) {
		if ( empty( $override ) || (int) 1 !== (int) $override ) {
			$screen = get_current_screen();
			if ( 'product_page_' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK !== $screen->id ) {
				return;
			}

			wp_dequeue_style( 'jquery-ui-style' );
			wp_dequeue_style( 'wp-color-picker' );
			wp_dequeue_style( 'woocommerce_admin_dashboard_styles' );

		}
		$ext = '.min';
		if ( 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$ext = '';
		}
		wp_enqueue_style( 'themecomplete-pagination', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tcpagination' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION, 'screen' );
		wp_enqueue_style( 'toastr', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/toastr' . $ext . '.css', false, '2.1.4', 'screen' );
		wp_enqueue_style( 'spectrum', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/spectrum' . $ext . '.css', false, '2.0', 'screen' );

		// The version of the fontawesome is customized.
		wp_enqueue_style( 'themecomplete-fontawesome', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/fontawesome' . $ext . '.css', false, '5.12', 'screen' );
		wp_enqueue_style( 'themecomplete-animate', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/animate' . $ext . '.css', false, '1.0' );
		wp_enqueue_style( 'themecomplete-global-epo-admin', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tm-global-epo-admin' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION );

		wp_enqueue_style( 'themecomplete-epo-admin-font', $this->admin_font_url(), [], '1.0.0' );
	}

	/**
	 * Enqueue plugin scripts and dequeue unwanted woocommerce scripts
	 *
	 * @param integer $override true 1 or 0.
	 * @since 1.0
	 */
	public function register_admin_scripts( $override = 0 ) {
		global $wp_query, $post;
		$ext = '.min';
		if ( 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$ext = '';
		}
		$this->register_admin_styles( $override );
		if ( empty( $override ) || (int) 1 !== (int) $override ) {
			$screen = get_current_screen();
			if ( 'product_page_' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK !== $screen->id ) {
				return;
			}
			wp_dequeue_script( 'woocommerce_admin' );
			wp_dequeue_script( 'iris' );
			wp_dequeue_script( 'post-edit-languages' );

			remove_all_filters( 'mce_external_plugins' );
			remove_all_filters( 'mce_buttons' );
		}

		// Dequeue DHVC Woocommerce products choosen scripts.
		wp_dequeue_script( 'dhvc-woo-admin' );

		add_action( 'admin_footer', [ $this, 'script_templates' ] );

		wp_register_script( 'spectrum', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/spectrum' . $ext . '.js', '', '2.0', true );

		wp_register_script( 'themecomplete-api', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-api' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

		wp_register_script( 'jquery-tcfloatbox', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tcfloatbox' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

		wp_register_script( 'jquery-tctooltip', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctooltip' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

		wp_register_script( 'themecomplete-tabs', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctabs' . $ext . '.js', [ 'themecomplete-api' ], THEMECOMPLETE_EPO_VERSION, true );

		wp_register_script( 'jquery-tcpagination', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/jquery.tcpagination' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

		wp_register_script( 'toastr', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/toastr' . $ext . '.js', '', '2.1.4', true );

		wp_register_script(
			'themecomplete-global-epo-admin',
			THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/tm-global-epo-admin' . $ext . '.js',
			[
				'jquery',
				'jquery-ui-droppable',
				'jquery-ui-sortable',
				'jquery-ui-tabs',
				'jquery-ui-resizable',
				'json2',
				'wp-util',
				'spectrum',
				'jquery-tcpagination',
				'toastr',
				'themecomplete-api',
				'themecomplete-tabs',
				'jquery-tcfloatbox',
				'jquery-tctooltip',
				'plupload-all',
			],
			THEMECOMPLETE_EPO_VERSION,
			true
		);
		$import_url = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&action=import';
		$import_url = admin_url( $import_url );

		$post_id   = isset( $post->ID ) ? floatval( $post->ID ) : '';
		$post_type = isset( $post->post_type ) ? $post->post_type : '';
		$basetype  = $this->basetype;
		if ( false === $basetype ) {
			$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
		}
		$original_post_id         = $post_id;
		$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id, $post_type, $basetype );
		if ( ! $wpml_is_original_product ) {
			$original_post_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id, $post_type, $basetype ) );
		}

		$params = [
			'post_id'                            => sprintf( '%d', $post_id ),
			'original_post_id'                   => sprintf( '%d', $original_post_id ),
			'is_original_post'                   => $post_id === $original_post_id,
			'get_products_categories_nonce'      => wp_create_nonce( 'get-product-categories' ),
			'search_products_nonce'              => wp_create_nonce( 'search-products' ),
			'export_nonce'                       => wp_create_nonce( 'export-nonce' ),
			'check_attributes_nonce'             => wp_create_nonce( 'check_attributes' ),
			'import_nonce'                       => wp_create_nonce( 'import-nonce' ),
			'save_nonce'                         => wp_create_nonce( 'save-nonce' ),
			// WPML 3.3.x fix.
			'ajax_url'                           => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ), // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
			'plugin_url'                         => THEMECOMPLETE_EPO_PLUGIN_URL,
			'import_url'                         => $import_url,
			'element_data'                       => $this->js_element_data(), // This is internal HTML code so we don't escape it.
			'i18n_builder_delete'                => esc_html__( 'Are you sure you want to delete this item?', 'woocommerce-tm-extra-product-options' ),
			'i18n_builder_clone'                 => esc_html__( 'Are you sure you want to clone this item?', 'woocommerce-tm-extra-product-options' ),
			'i18n_yes'                           => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
			'i18n_no'                            => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
			'i18n_update'                        => esc_html__( 'Update', 'woocommerce-tm-extra-product-options' ),
			'i18n_no_variations'                 => esc_html__( 'There are no saved variations yet.', 'woocommerce-tm-extra-product-options' ),
			'i18n_cancel'                        => esc_html__( 'Cancel', 'woocommerce-tm-extra-product-options' ),
			'i18n_edit_settings'                 => esc_html__( 'Edit settings', 'woocommerce-tm-extra-product-options' ),
			'i18n_element_uniqid'                => esc_html__( 'Element id', 'woocommerce-tm-extra-product-options' ),
			'i18n_section_uniqid'                => esc_html__( 'Section id', 'woocommerce-tm-extra-product-options' ),
			'i18n_is'                            => esc_html__( 'is', 'woocommerce-tm-extra-product-options' ),
			'i18n_is_not'                        => esc_html__( 'is not', 'woocommerce-tm-extra-product-options' ),
			'i18n_is_empty'                      => esc_html__( 'is empty', 'woocommerce-tm-extra-product-options' ),
			'i18n_is_not_empty'                  => esc_html__( 'is not empty', 'woocommerce-tm-extra-product-options' ),
			'i18n_starts_with'                   => esc_html__( 'starts with', 'woocommerce-tm-extra-product-options' ),
			'i18n_ends_with'                     => esc_html__( 'ends with', 'woocommerce-tm-extra-product-options' ),
			'i18n_greater_than'                  => esc_html__( 'greater than', 'woocommerce-tm-extra-product-options' ),
			'i18n_less_than'                     => esc_html__( 'less than', 'woocommerce-tm-extra-product-options' ),
			'i18n_greater_than_equal'            => esc_html__( 'greater than or equal to', 'woocommerce-tm-extra-product-options' ),
			'i18n_less_than_equal'               => esc_html__( 'less than or equal to', 'woocommerce-tm-extra-product-options' ),
			'i18n_cannot_apply_rules'            => esc_html__( 'Cannot apply rules on this element or section since there are not any value configured elements on other sections, or no other sections found.', 'woocommerce-tm-extra-product-options' ),
			'i18n_invalid_request'               => esc_html__( 'Invalid request!', 'woocommerce-tm-extra-product-options' ),
			'i18n_populate'                      => esc_html__( 'Populate', 'woocommerce-tm-extra-product-options' ),
			'i18n_invalid_extension'             => esc_html__( 'Invalid file extension', 'woocommerce-tm-extra-product-options' ),
			'i18n_importing'                     => esc_html__( 'Importing csv...', 'woocommerce-tm-extra-product-options' ),
			'i18n_saving'                        => esc_html__( 'Saving... Please wait.', 'woocommerce-tm-extra-product-options' ),
			'i18n_import_title'                  => esc_html__( 'Importing data', 'woocommerce-tm-extra-product-options' ),
			'i18n_error_title'                   => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
			'i18n_add_element'                   => esc_html__( 'Add element', 'woocommerce-tm-extra-product-options' ),
			'i18n_edit_price'                    => esc_html__( 'Edit price', 'woocommerce-tm-extra-product-options' ),
			'i18n_edit_tab'                      => esc_html__( 'Edit tab', 'woocommerce-tm-extra-product-options' ),
			'i18n_save'                          => esc_html__( 'Save', 'woocommerce-tm-extra-product-options' ),
			'i18n_overwrite_existing_elements'   => esc_html__( 'Overwrite existing elements', 'woocommerce-tm-extra-product-options' ),
			'i18n_append_new_elements'           => esc_html__( 'Append new elements', 'woocommerce-tm-extra-product-options' ),
			'i18n_form_is_applied_to_all'        => esc_html__( 'The form is being applied to all products', 'woocommerce-tm-extra-product-options' ),
			'i18n_form_not_applied_to_all'       => esc_html__( 'The form isn\'t being applied to any products', 'woocommerce-tm-extra-product-options' ),
			'i18n_no_title'                      => esc_html__( '(No title)', 'woocommerce-tm-extra-product-options' ),
			'i18n_epo'                           => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
			'i18n_loading'                       => esc_html__( 'Loading ...', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_product_variables'     => esc_html__( 'Product variables', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_global_variables'      => esc_html__( 'Global variables', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_quantity'              => esc_html__( 'Product quantity', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_product_price'         => esc_html__( 'Original product price', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_this_element'          => esc_html__( 'This element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_this_value'            => esc_html__( 'The value of this element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_this_value_length'     => esc_html__( 'The value length of this element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_this_count'            => esc_html__( 'The number of options the user has selected', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_this_count_quantity'   => esc_html__( 'The total quantity of this element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_this_quantity'         => esc_html__( 'The quantity of this element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_other_elements'        => esc_html__( 'Other elements', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_price'           => esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_price_tip'       => esc_html__( 'The price of the targeted element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_value'           => esc_html__( 'Value', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_value_tip'       => esc_html__( 'The value of the targeted element converted to a float', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_text'            => esc_html__( 'Text', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_text_tip'        => esc_html__( 'The raw value of the targeted element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_text_length'     => esc_html__( 'Text length', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_text_length_tip' => esc_html__( 'The text length of the targeted element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_quantity'        => esc_html__( 'Quantity', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_quantity_tip'    => esc_html__( 'The total quantity of the targeted element', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_count'           => esc_html__( 'Count', 'woocommerce-tm-extra-product-options' ),
			'i18n_formula_field_count_tip'       => esc_html__( 'The number of options the user has selected on the targeted element', 'woocommerce-tm-extra-product-options' ),
			'tcAdminNoPagination'                => apply_filters( 'wc_epo_admin_no_pagination', false ),

		];
		wp_localize_script( 'themecomplete-global-epo-admin', 'TMEPOGLOBALADMINJS', $params );
		wp_enqueue_script( 'themecomplete-global-epo-admin' );

	}

	/**
	 * Print script templates
	 *
	 * @since 1.0
	 */
	public function script_templates() {
		// The check is required in case other plugin do things that don't load the wc_get_template function.
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template( 'tc-js-admin-templates.php', [], null, THEMECOMPLETE_EPO_PLUGIN_PATH . '/assets/js/admin/' );
			wc_get_template( 'tc-js-admin-builder-templates.php', [], null, THEMECOMPLETE_EPO_PLUGIN_PATH . '/assets/js/admin/' );
		}

	}

	/**
	 * Generate JS element data
	 *
	 * @param string $button_class The class name.
	 * @since 1.0
	 */
	public function js_element_data( $button_class = '' ) {

		$drag_elements = [];
		$tags          = [];
		foreach ( THEMECOMPLETE_EPO_BUILDER()->get_elements() as $element => $settings ) {

			if ( $settings->show_on_backend ) {
				$tagclass = '';
				if ( $settings->is_addon ) {
					$tags[ $settings->namespace ][ sanitize_title( 'tc-' . $settings->tags ) ] = $settings->tags;
					$tagclass .= ' tc-' . sanitize_title( $settings->tags ) . ' tc-' . sanitize_title( $settings->namespace );
				} else {
					$tag = explode( ' ', $settings->tags );
					foreach ( $tag as $key => $value ) {
						$tags[ $settings->namespace ][ sanitize_title( $value ) ] = $value;
						$tagclass .= ' tc-' . sanitize_title( $value );
					}
				}

				$_drag_elements  = '<li class="transition tm-element-button' . $tagclass . '">';
				$_drag_elements .= "<div data-element='" . $element . "' class='" . $button_class . ' tc-element-button element-' . $element . "'>"
								. "<div class='tm-label'>"
								. "<div class='tm-icon-wrap'><i class='tmfa tcfa " . $settings->icon . "'></i></div> "
								. "<div class='tm-name-wrap'><span class='tm-element-name'>" . $settings->name . '</span>'
								. "<span class='tm-element-description'>" . $settings->description . '</span></div>'
								. '</div></div>';
				$_drag_elements .= '</li>';

				$drag_elements[ $settings->namespace ][] = $_drag_elements;

			}
		}

		$tm_drag_elements = $drag_elements[ THEMECOMPLETE_EPO_ELEMENTS_NAMESPACE ];
		unset( $drag_elements[ THEMECOMPLETE_EPO_ELEMENTS_NAMESPACE ] );
		$drag_elements_html = implode( '', $tm_drag_elements );
		foreach ( $drag_elements as $key => $value ) {
			$drag_elements_html .= implode( '', $value );
		}

		$tm_tags = $tags[ THEMECOMPLETE_EPO_ELEMENTS_NAMESPACE ];
		unset( $tags[ THEMECOMPLETE_EPO_ELEMENTS_NAMESPACE ] );

		$tags = array_map( 'unserialize', array_unique( array_map( 'serialize', $tags ) ) );

		$tag_counter = 1;
		$out         = '<div class="transition tm-tabs tm-tags-container">';

		$out .= '<div class="transition tm-tab-headers">';
		$out .= '<div class="tm-box tma-tab-label">'
				. '<h4 tabindex="0" class="tab-header open" data-tm-tag="' . esc_attr( 'all' ) . '" data-id="tc-tag' . $tag_counter . '-tab">' . esc_html__( 'All', 'woocommerce-tm-extra-product-options' ) . '</h4>'
				. '</div>';
		foreach ( $tm_tags as $key => $value ) {
			$tag_counter ++;
			$out .= '<div class="tm-box tma-tab-label">'
					. '<h4 tabindex="0" class="tab-header closed" data-tm-tag="tc-' . esc_attr( $key ) . '" data-id="tc-tag' . $tag_counter . '-tab">' . $value . '</h4>'
					. '</div>';
		}
		foreach ( $tags as $key => $value ) {
			$tag_counter ++;
			$out .= '<div class="tm-box tma-tab-label">'
					. '<h4 tabindex="0" class="tab-header closed" data-tm-tag="tc-' . esc_attr( sanitize_title( $key ) ) . '" data-id="tc-tag' . $tag_counter . '-tab">' . $key . '</h4>'
					. '</div>';
		}
		$out .= '</div>';

		$out .= '<div class="transition tm-tab tc-tag' . $tag_counter . '-tab">';
		$out .= '<ul class="tm-elements-container tm-bsbb-all">';
		$out .= $drag_elements_html;
		$out .= '</ul>';
		$out .= '</div>';

		$out .= '</div>';

		return $out;
	}

	/**
	 * Init List table class
	 *
	 * @param object|string $class The class object.
	 * @param array         $args Array of arguments.
	 * @since 1.0
	 */
	private function get_wp_list_table( $class = '', $args = [] ) {

		$args['screen'] = convert_to_screen( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE );

		return new $class( $args );

	}

	/**
	 * Merage imported data
	 *
	 * @param array $tm_metas The array data to save.
	 * @param array $import The array added data to save.
	 * @since 1.0
	 */
	public function import_array_merge( $tm_metas, $import ) {
		$clean_import = [];
		if ( ! isset( $tm_metas['tm_meta']['tmfbuilder'] ) ) {
			$tm_metas['tm_meta']['tmfbuilder'] = [];
		}
		foreach ( $import['tm_meta']['tmfbuilder'] as $key => $value ) {
			if ( ! isset( $tm_metas['tm_meta']['tmfbuilder'][ $key ] ) ) {
				$tm_metas['tm_meta']['tmfbuilder'][ $key ] = [];
			}
			if ( THEMECOMPLETE_EPO_HELPER()->str_startswith( $key, 'variations_' ) ) {
				if ( 'variations_disabled' !== $key ) {
					$tm_metas['tm_meta']['tmfbuilder'][ $key ] = $value;
				}
			} else {
				$tm_metas['tm_meta']['tmfbuilder'][ $key ] = array_merge( $tm_metas['tm_meta']['tmfbuilder'][ $key ], $value );
			}
		}

		return $tm_metas;
	}

	/**
	 * Save our meta data
	 *
	 * @param integer $post_id The post id.
	 * @param object  $post_object The post object.
	 * @since 1.0
	 */
	public function tm_save_postdata( $post_id, $post_object ) {
		if ( empty( $_POST ) || ! isset( $_POST['post_type'] ) || ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE !== $_POST['post_type'] && THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE !== $_POST['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$this->tm_save_postdata_do( $post_id, $post_object );
	}

	/**
	 * Save imported CSV
	 *
	 * @param integer $post_id The post id.
	 * @since 4.8
	 */
	public function save_imported_csv( $post_id ) {

		if ( empty( $post_id ) ) {
			return false;
		}

		$import = get_transient( 'tc_import_csv' );

		$tm_metas = [];

		if ( ! empty( $import ) ) {

			if ( isset( $_REQUEST['tm_uploadmeta'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$tm_metas = wp_unslash( $_REQUEST['tm_uploadmeta'] );// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$tm_metas = rawurldecode( $tm_metas );
				$tm_metas = nl2br( $tm_metas );
				$tm_metas = json_decode( $tm_metas, true );
			}

			if ( ! ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) ) {
				$tm_metas = [ 'tm_meta' => [ 'tmfbuilder' => [] ] ];
			}
			$import_override = get_transient( 'tc_import_override' );
			if ( false !== $import_override ) {
				unset( $tm_metas['tm_meta']['tmfbuilder'] );
				$tm_metas = $this->import_array_merge( $tm_metas, $import );
				delete_transient( 'tc_import_override' );
			} else {
				$tm_metas = $this->import_array_merge( $tm_metas, $import );
			}

			delete_transient( 'tc_import_csv' );
		}

		if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
			$tm_meta = $tm_metas['tm_meta'];

			$meta = 'tm_meta';
			$post = get_post( $post_id );
			if ( $post && property_exists( $post, 'ID' ) && property_exists( $post, 'post_type' ) ) {
				$post_type = isset( $post->post_type ) ? $post->post_type : '';
				$basetype  = $this->basetype;
				if ( false === $basetype ) {
					$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
				}
				$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id, $post_type, $basetype );
				$original_post_id         = $post_id;
				if ( ! $wpml_is_original_product ) {
					$meta             = 'tm_meta_wpml';
					$original_post_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post->ID, $post->post_type, $basetype ) );
				}

				$old_data = themecomplete_get_post_meta( $post_id, $meta, true );
				if ( isset( $old_data['priority'] ) ) {
					$tm_meta['priority'] = $old_data['priority'];
				}
				themecomplete_save_post_meta( $post_id, $tm_meta, $old_data, $meta );
				THEMECOMPLETE_EPO_BUILDER()->ajax_print_saved_elements( $original_post_id, $post_id, $wpml_is_original_product );
			}
		}

	}

	/**
	 * Save meta data
	 *
	 * @param integer $post_id The post id.
	 * @param object  $post_object The post object.
	 * @since 1.0
	 */
	public function tm_save_postdata_do( $post_id, $post_object ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( 'revision' === $post_object->post_type ) {
			return;
		}
		check_admin_referer( 'update-post_' . $post_id );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$import = get_transient( 'tc_import_csv' );

		if ( isset( $_POST['tm_meta_serialized'] ) ) {
			$tm_metas = wp_unslash( $_POST['tm_meta_serialized'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tm_metas = rawurldecode( $tm_metas );
			$tm_metas = nl2br( $tm_metas );
			$tm_metas = json_decode( $tm_metas, true );

			if ( $tm_metas ) {
				if ( ! empty( $import ) ) {
					$import_override = get_transient( 'tc_import_override' );
					if ( false !== $import_override ) {
						unset( $tm_metas['tm_meta']['tmfbuilder'] );
						$tm_metas = $this->import_array_merge( $tm_metas, $import );
						delete_transient( 'tc_import_override' );
					} else {
						$tm_metas = $this->import_array_merge( $tm_metas, $import );
					}
					delete_transient( 'tc_import_csv' );
				}
				if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
					$tm_meta  = $tm_metas['tm_meta'];
					$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
					themecomplete_save_post_meta( $post_id, $tm_meta, $old_data, 'tm_meta' );
				}
			}
		} elseif ( isset( $_POST['tm_meta_serialized_wpml'] ) ) {
			$tm_metas = wp_unslash( $_POST['tm_meta_serialized_wpml'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tm_metas = rawurldecode( $tm_metas );
			$tm_metas = nl2br( $tm_metas );
			$tm_metas = json_decode( $tm_metas, true );
			if ( $tm_metas ) {

				$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_wpml', true );

				if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
					$tm_meta = $tm_metas['tm_meta'];
					themecomplete_save_post_meta( $post_id, $tm_meta, $old_data, 'tm_meta_wpml' );
				} else {
					themecomplete_save_post_meta( $post_id, false, $old_data, 'tm_meta_wpml' );
				}
			}
		}
		if ( isset( $_POST['tm_meta_product_ids'] ) ) {
			$tm_meta_product_ids = wp_unslash( $_POST['tm_meta_product_ids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! is_array( $tm_meta_product_ids ) ) {
				$tm_meta_product_ids = explode( ',', $tm_meta_product_ids );
			}
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_product_ids', true );
			themecomplete_save_post_meta( $post_id, $tm_meta_product_ids, $old_data, 'tm_meta_product_ids' );
		} else {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_product_ids', true );
			themecomplete_save_post_meta( $post_id, [], $old_data, 'tm_meta_product_ids' );
		}
		if ( isset( $_POST['tm_meta_product_exclude_ids'] ) ) {
			$tm_meta_product_exclude_ids = wp_unslash( $_POST['tm_meta_product_exclude_ids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! is_array( $tm_meta_product_exclude_ids ) ) {
				$tm_meta_product_exclude_ids = explode( ',', $tm_meta_product_exclude_ids );
			}
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_product_exclude_ids', true );
			themecomplete_save_post_meta( $post_id, $tm_meta_product_exclude_ids, $old_data, 'tm_meta_product_exclude_ids' );
		} else {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_product_exclude_ids', true );
			themecomplete_save_post_meta( $post_id, [], $old_data, 'tm_meta_product_exclude_ids' );
		}
		if ( isset( $_POST['tm_meta_disable_categories'] ) ) {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_disable_categories', true );
			themecomplete_save_post_meta( $post_id, wp_unslash( $_POST['tm_meta_disable_categories'] ), $old_data, 'tm_meta_disable_categories' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} else {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_disable_categories', true );
			themecomplete_save_post_meta( $post_id, 0, $old_data, 'tm_meta_disable_categories' );
		}

		if ( isset( $_POST['tm_meta_enabled_roles'] ) ) {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_enabled_roles', true );
			themecomplete_save_post_meta( $post_id, wp_unslash( $_POST['tm_meta_enabled_roles'] ), $old_data, 'tm_meta_enabled_roles' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} else {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_enabled_roles', true );
			themecomplete_save_post_meta( $post_id, [], $old_data, 'tm_meta_enabled_roles' );
		}
		if ( isset( $_POST['tm_meta_disabled_roles'] ) ) {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_disabled_roles', true );
			themecomplete_save_post_meta( $post_id, wp_unslash( $_POST['tm_meta_disabled_roles'] ), $old_data, 'tm_meta_disabled_roles' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} else {
			$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_disabled_roles', true );
			themecomplete_save_post_meta( $post_id, [], $old_data, 'tm_meta_disabled_roles' );
		}
		// WPML fields.
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			if ( isset( $_POST[ THEMECOMPLETE_EPO_WPML_PARENT_POSTID ] ) ) {
				$old_data = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true );
				themecomplete_save_post_meta( $post_id, wp_unslash( $_POST[ THEMECOMPLETE_EPO_WPML_PARENT_POSTID ] ), $old_data, THEMECOMPLETE_EPO_WPML_PARENT_POSTID ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}
			if ( isset( $_POST[ THEMECOMPLETE_EPO_WPML_LANG_META ] ) && ! empty( $_POST[ THEMECOMPLETE_EPO_WPML_LANG_META ] ) ) {
				$old_data = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
				themecomplete_save_post_meta( $post_id, wp_unslash( $_POST[ THEMECOMPLETE_EPO_WPML_LANG_META ] ), $old_data, THEMECOMPLETE_EPO_WPML_LANG_META ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} else {
				$old_data = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
				themecomplete_save_post_meta( $post_id, THEMECOMPLETE_EPO_WPML()->get_default_lang(), $old_data, THEMECOMPLETE_EPO_WPML_LANG_META );
			}
		}

	}

	/**
	 * Init List table class
	 *
	 * @since 1.0
	 */
	public function admin_screen() {
		global $bulk_counts, $bulk_messages, $general_messages;

		$post_type        = $this->tm_list_table->screen->post_type; // @phpstan-ignore-line
		$post_type_object = get_post_type_object( $post_type );

		$parent_file   = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		$submenu_file  = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
		$post_new_file = 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&action=add';

		$doaction = $this->tm_list_table->current_action(); // @phpstan-ignore-line
		if ( $doaction && in_array( $doaction, [ 'add', 'export', 'clone', 'trash', 'untrash', 'delete', 'editpost', 'edit', 'import', 'download' ], true ) ) {
			$screen = get_current_screen();

			// edit screen.
			if ( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] && ( isset( $_REQUEST['post'] ) || isset( $_POST['post_ID'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_id = (int) $_GET['post']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_ID = $post_id;
				} elseif ( isset( $_POST['post_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$post_id = (int) $_POST['post_ID']; // phpcs:ignore WordPress.Security.NonceVerification
					$post_ID = $post_id;
				} else {
					$post_id = 0;
					$post_ID = 0;
				}
				if ( ! empty( $post_id ) ) {
					$editing = true;
					$post    = get_post( $post_id, OBJECT, 'edit' );
					if ( $post ) {
						$post_type        = $post->post_type;
						$post_type_object = get_post_type_object( $post_type );
						$title            = $post_type_object->labels->edit_item;
						$nonce_action     = 'update-post_' . $post_ID;

						$original_id = $post_ID;
						if ( ! THEMECOMPLETE_EPO_WPML()->is_original_product( $post->ID, $post->post_type ) ) {
							$original_id   = THEMECOMPLETE_EPO_WPML()->get_original_id( $post->ID, $post->post_type );
							$original_post = get_post( $original_id, OBJECT, 'edit' );
						}
						$_meta                     = themecomplete_get_post_meta( $original_id, 'tm_meta' );
						$_meta_product_ids         = apply_filters( 'wc_epo_tm_meta_product_ids', themecomplete_get_post_meta( $original_id, 'tm_meta_product_ids', true ), $original_id );
						$_meta_product_exclude_ids = apply_filters( 'wc_epo_tm_meta_product_exclude_ids', themecomplete_get_post_meta( $original_id, 'tm_meta_product_exclude_ids', true ), $original_id );
						$_meta_enabled_roles       = themecomplete_get_post_meta( $original_id, 'tm_meta_enabled_roles', true );
						$_meta_disabled_roles      = themecomplete_get_post_meta( $original_id, 'tm_meta_disabled_roles', true );
						$_meta_disable_categories  = themecomplete_get_post_meta( $original_id, 'tm_meta_disable_categories', true );
						$meta_fields               = [
							'priority'    => 10,
							'can_publish' => current_user_can( $post_type_object->cap->publish_posts ),
						];
						$meta                      = [];
						foreach ( $meta_fields as $key => $value ) {
							$meta[ $key ] = isset( $_meta[0][ $key ] ) ? themecomplete_maybe_unserialize( $_meta[0][ $key ] ) : $value;

						}
						unset( $_meta );
						$meta['product_ids']         = $_meta_product_ids;
						$meta['product_exclude_ids'] = $_meta_product_exclude_ids;
						$meta['enabled_roles']       = $_meta_enabled_roles;
						$meta['disabled_roles']      = $_meta_disabled_roles;
						$meta['disable_categories']  = $_meta_disable_categories;
						$post->tm_meta               = $meta;
						unset( $meta );

						wp_enqueue_script( 'post' );
						include 'views/html-tm-epo-fields-edit.php';
					}
				}
				// add screen.
			} elseif ( isset( $_REQUEST['action'] ) && 'add' === $_REQUEST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$post_type        = $this->tm_list_table->screen->post_type; // @phpstan-ignore-line
				$post_type_object = get_post_type_object( $post_type );

				$parent_post_meta                     = [];
				$parent_post_meta_product_ids         = [];
				$parent_post_meta_product_exclude_ids = [];
				$parent_post_meta_enabled_roles       = [];
				$parent_post_meta_disabled_roles      = [];
				$parent_post_meta_disable_categories  = 1;

				// WPML.
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					if ( isset( $_GET['tmparentpostid'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$parent_post                          = get_post( (int) $_GET['tmparentpostid'], OBJECT, 'edit' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$parent_post_meta                     = themecomplete_get_post_meta( $parent_post->ID, 'tm_meta' );
						$parent_post_meta_product_ids         = apply_filters( 'wc_epo_tm_meta_product_ids', themecomplete_get_post_meta( $parent_post->ID, 'tm_meta_product_ids', true ), $parent_post->ID );
						$parent_post_meta_product_exclude_ids = apply_filters( 'wc_epo_tm_meta_product_exclude_ids', themecomplete_get_post_meta( $parent_post->ID, 'tm_meta_product_exclude_ids', true ), $parent_post->ID );
						$parent_post_meta_enabled_roles       = themecomplete_get_post_meta( $parent_post->ID, 'tm_meta_enabled_roles', true );
						$parent_post_meta_disabled_roles      = themecomplete_get_post_meta( $parent_post->ID, 'tm_meta_disabled_roles', true );
						$parent_post_meta_disable_categories  = themecomplete_get_post_meta( $parent_post->ID, 'tm_meta_disable_categories', true );
						THEMECOMPLETE_EPO_WPML()->apply_wp_terms_checklist_args_filter( (int) $_GET['tmparentpostid'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					}
				}

				$post = get_default_post_to_edit( $post_type, true );
				if ( $post ) {
					$post_id = $post->ID;
					$post_ID = $post_id;

					// WPML.
					if ( ! empty( $parent_post ) ) {
						$post->post_title   = $parent_post->post_title;
						$post->post_excerpt = $parent_post->post_excerpt;
					}

					$title        = $post_type_object->labels->add_new;
					$nonce_action = 'update-post_' . $post_ID;

					$_meta       = [];
					$meta_fields = array_merge(
						[
							'priority'    => 10,
							'can_publish' => current_user_can( $post_type_object->cap->publish_posts ),
						],
						$parent_post_meta
					);
					$meta        = [];
					foreach ( $meta_fields as $key => $value ) {
						$meta[ $key ] = $value;
					}
					unset( $_meta );
					$meta['product_ids']         = $parent_post_meta_product_ids;
					$meta['product_exclude_ids'] = $parent_post_meta_product_exclude_ids;
					$meta['enabled_roles']       = $parent_post_meta_enabled_roles;
					$meta['disabled_roles']      = $parent_post_meta_disabled_roles;

					$meta['disable_categories'] = $parent_post_meta_disable_categories;
					$post->tm_meta              = $meta;
					unset( $meta );
					wp_enqueue_script( 'post' );
					include 'views/html-tm-epo-fields-edit.php';
				}
			}
			// list screen.
		} else {
			$this->tm_list_table->prepare_items(); // @phpstan-ignore-line
			wp_enqueue_script( 'inline-edit-post' );// list.
			add_action( 'tm_list_table_action', [ $this, 'tm_list_table_action' ], 10, 2 );
			include 'views/html-tm-epo-fields.php';
		}
	}

	/**
	 * List view actions
	 *
	 * @param string $action The action to perform.
	 * @param array  $args Array of arguments.
	 * @since 1.0
	 */
	public function tm_list_table_action( $action = '', $args = [] ) {
		if ( ! $action ) {
			return;
		}
		switch ( $action ) {
			case 'views':
				$this->tm_list_table->views(); // @phpstan-ignore-line
				break;
			case 'display':
				$this->tm_list_table->display(); // @phpstan-ignore-line
				break;
			case 'inline_edit':
				if ( $this->tm_list_table->has_items() ) { // @phpstan-ignore-line
					$this->tm_list_table->inline_edit(); // @phpstan-ignore-line
				}
				break;
			case 'search_box':
				$this->tm_list_table->search_box( $args['text'], $args['input_id'] ); // @phpstan-ignore-line
				break;
			default:
				break;
		}
	}

	/**
	 * Export form action
	 *
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function tm_export_form_action( $post_id = 0 ) {

		$csv = new THEMECOMPLETE_EPO_ADMIN_CSV();
		$csv->export_by_id( $post_id );

	}


	/**
	 * Clone form action
	 *
	 * @param integer      $post_id The post id.
	 * @param string|false $basetype The post type.
	 * @since 1.0
	 */
	public function tm_clone_form_action( $post_id = 0, $basetype = false ) {

		// Check the nonce.
		check_ajax_referer( 'tmclone_form_nonce_' . $post_id, 'security' );

		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {

			if ( false === $basetype ) {
				$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
			}

			$main_post_id = 0;
			$tm_meta_lang = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_LANG_META, true );
			if ( empty( $tm_meta_lang ) ) {
				$tm_meta_lang = THEMECOMPLETE_EPO_WPML()->get_default_lang();
				$main_post_id = $post_id;
			}
			if ( empty( $main_post_id ) ) {
				$main_post_id = themecomplete_get_post_meta( $post_id, THEMECOMPLETE_EPO_WPML_PARENT_POSTID, true );
			}

			$tm_meta                       = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
			$generate_recreate_element_ids = THEMECOMPLETE_EPO_HELPER()->generate_recreate_element_ids( $tm_meta );

			$id_for_meta = $this->do_clone_form( $main_post_id, $main_post_id, $generate_recreate_element_ids );

			foreach ( THEMECOMPLETE_EPO_WPML()->get_active_languages() as $key => $value ) {
				if ( $key !== $tm_meta_lang || 'all' === THEMECOMPLETE_EPO_WPML()->get_lang() ) {

					if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $key ) {
						$query = new WP_Query(
							[
								'post_type'      => $basetype,
								'post_status'    => [ 'publish' ],
								'numberposts'    => -1,
								'posts_per_page' => -1,
								'orderby'        => 'date',
								'order'          => 'asc',
								'no_found_rows'  => true,
								'p'              => $main_post_id,
							]
						);
					} else {

						$meta_query   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $key, '=', 'EXISTS' );
						$meta_query[] = [
							'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
							'value'   => $main_post_id,
							'compare' => '=',
						];

						$query = new WP_Query(
							[
								'post_type'      => $basetype,
								'post_status'    => [ 'publish' ],
								'numberposts'    => -1,
								'posts_per_page' => -1,
								'orderby'        => 'date',
								'order'          => 'asc',
								'no_found_rows'  => true,
								'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery
							]
						);

					}

					if ( ! empty( $query->posts ) ) {
						$clone = $this->do_clone_form( $query->post->ID, $id_for_meta, $generate_recreate_element_ids );
					}
				}
			}
		} else {
			$clone = $this->do_clone_form( $post_id, $post_id );
		}

	}

	/**
	 * Clones a form
	 *
	 * @param integer     $post_id The post id.
	 * @param integer     $id_for_meta The id to use for meta data.
	 * @param array|false $generate_recreate_element_ids Array of element ids.
	 * @since 1.0
	 */
	public function do_clone_form( $post_id = 0, $id_for_meta = 0, $generate_recreate_element_ids = false ) {

		// Get the post as an array.
		$duplicate = get_post( $post_id, 'ARRAY_A' );

		// Modify some of the elements.
		$duplicate['post_title'] = $duplicate['post_title'] . ' ' . esc_html__( 'Copy', 'woocommerce-tm-extra-product-options' );

		// Set the status.
		$duplicate['post_status'] = 'draft';

		// Set the post date.
		$duplicate['post_date'] = current_datetime()->format( 'Y-m-d H:i:s' );

		// Remove some of the keys.
		if ( isset( $duplicate['ID'] ) ) {
			unset( $duplicate['ID'] );
		}
		if ( isset( $duplicate['guid'] ) ) {
			unset( $duplicate['guid'] );
		}
		if ( isset( $duplicate['comment_count'] ) ) {
			unset( $duplicate['comment_count'] );
		}

		// Insert the post into the database.
		$duplicate_id = wp_insert_post( $duplicate );

		// Duplicate all the taxonomies/terms.
		$taxonomies = get_object_taxonomies( $duplicate['post_type'] );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'names' ] );
			wp_set_object_terms( $duplicate_id, $terms, $taxonomy );
		}

		// Duplicate all the custom fields.
		$custom_fields = get_post_custom( $post_id );

		foreach ( $custom_fields as $key => $value ) {
			if ( 'tm_meta' === $key || 'tm_meta_wpml' === $key ) {
				themecomplete_update_post_meta( $duplicate_id, $key, THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $value[0], $generate_recreate_element_ids ) );
			} elseif ( THEMECOMPLETE_EPO_WPML_LANG_META !== $key && THEMECOMPLETE_EPO_WPML_PARENT_POSTID !== $key ) {
				themecomplete_update_post_meta( $duplicate_id, $key, themecomplete_maybe_unserialize( $value[0] ) );
			} elseif ( THEMECOMPLETE_EPO_WPML_LANG_META === $key ) {
				themecomplete_update_post_meta( $duplicate_id, $key, themecomplete_maybe_unserialize( $value[0] ) );
			} elseif ( THEMECOMPLETE_EPO_WPML_PARENT_POSTID === $key ) {
				if ( (float) $post_id !== (float) $id_for_meta ) {
					themecomplete_update_post_meta( $duplicate_id, $key, $id_for_meta );
				} else {
					themecomplete_update_post_meta( $duplicate_id, $key, $duplicate_id );
				}
			}
		}

		return $duplicate_id;

	}

}

