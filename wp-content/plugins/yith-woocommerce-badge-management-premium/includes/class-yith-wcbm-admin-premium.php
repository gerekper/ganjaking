<?php
/**
 * Admin Premium Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Classes
 * @since   1.0.0
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 */
	class YITH_WCBM_Admin_Premium extends YITH_WCBM_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Metabox IDs in which the savings will not be handled by the plugin-fw
		 *
		 * @var array
		 */
		protected $custom_savings_metabox = array( 'yith-wcbm-metabox', 'yith-wcbm-badge-rules' );

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {
			parent::__construct();

			add_filter( 'yith_wcbm_panel_settings_options', array( $this, 'add_advanced_options' ) );

			add_filter( 'yith_wcbm_settings_admin_tabs', array( $this, 'add_premium_admin_tab' ) );
			add_filter( 'yith_wcbm_settings_admin_panel_args', array( $this, 'handle_premium_panel_args' ) );

			// Add Badge column in Product list.
			add_filter( 'manage_product_posts_columns', array( $this, 'add_columns' ), 15 );
			add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );

			// Add bulk edit for Badge assigned to a product.
			add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'print_badge_options_in_product_bulk_edit' ) );
			add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'save_badge_options_in_product_bulk_edit' ) );

			add_action( 'woocommerce_product_quick_edit_end', array( $this, 'print_badge_options_in_product_quick_edit' ) );
			add_action( 'woocommerce_product_quick_edit_save', array( $this, 'save_badge_options_in_product_quick_edit' ) );

			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'print_badge_options_in_variations' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_badge_options_in_variations' ), 10, 2 );

			add_filter( 'yith_plugin_fw_add_new_post_url', array( $this, 'add_new_rule_url' ), 10, 2 );
			add_action( 'yith_wcbm_edit_badge_rule_page', array( $this, 'print_edit_badge_rule_page' ), 10 );

			add_action( 'yith_wcbm_print_badges_select', array( $this, 'print_badges_select' ), 10, 1 );
			add_action( 'yith_wcbm_print_badge_rules_newer_than_field', array( $this, 'print_badge_rules_newer_than_field' ), 10, 1 );
		}

		/**
		 * Add Admin notices
		 */
		public function admin_notices() {
			if ( yith_wcbm_admin()->is_panel() ) {
				if ( yith_wcbm_update_is_running() ) {
					// translators: %s is the name of the plugin that is updating data in background. Don't use the full stop at the end because there will be a smile emoji.
					$text        = sprintf( __( '<b>Update of %s:</b> we are updating badges, settings and generating badge rules. The progress is automatic and can take few minutes. Please wait', 'yith-woocommerce-badges-management' ), YITH_WCBM_PLUGIN_NAME );
					$spinner_url = YITH_WCBM_ASSETS_URL . 'images/spinner.gif';
					echo '<div class="notice notice-success is-dismissible yith-wcbm-updating-notice"><img class="yith-wcbm-updating-notice__loader" src=" ' . esc_url( $spinner_url ) . '"><p>' . wp_kses_post( $text ) . '</p></div>';
				}
			}
		}

		/**
		 * Retrieve Metaboxes
		 *
		 * @return array
		 */
		protected function get_metaboxes() {
			return file_exists( YITH_WCBM_DIR . '/plugin-options/metaboxes/metaboxes-premium.php' ) ? include_once YITH_WCBM_DIR . '/plugin-options/metaboxes/metaboxes-premium.php' : parent::get_metaboxes();
		}

		/**
		 * Add metaboxes Premium
		 */
		public function add_metaboxes() {
			parent::add_metaboxes();

			remove_meta_box( 'submitdiv', YITH_WCBM_Post_Types_Premium::$badge_rule, 'side' );
		}

		/**
		 * Badge options in Variations.
		 *
		 * @param int     $loop           Position in the loop.
		 * @param array   $variation_data Variation data.
		 * @param WP_Post $variation      Post data.
		 *
		 * @since 2.0
		 */
		public function print_badge_options_in_variations( $loop, $variation_data, $variation ) {
			$product = wc_get_product( $variation );

			$options = array(
				'badges'        => $product->get_meta( 'yith_wcbm_badge_options_badges' ),
				'schedule'      => $product->get_meta( 'yith_wcbm_badge_options_schedule' ),
				'schedule_from' => $product->get_meta( 'yith_wcbm_badge_options_schedule_from' ),
				'schedule_to'   => $product->get_meta( 'yith_wcbm_badge_options_schedule_to' ),
			);

			yith_wcbm_get_view( 'variation-badge-options.php', array_merge( compact( 'loop' ), $options ) );
		}

		/**
		 * Save Badge options in variations
		 *
		 * @param int $product_id The product ID.
		 * @param int $index      Variation index.
		 *
		 * @since 2.0
		 */
		public function save_badge_options_in_variations( $product_id, $index ) {
			$product = wc_get_product( $product_id );
			if ( $product && $index >= 0 && isset( $_POST['yith_wcbm_badge_options'][ $index ], $_POST['yith_wcbm_badge_options'][ $index ]['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcbm_badge_options'][ $index ]['security'] ) ), 'yith_wcbm_badge_options_in_variation_' . $index ) ) {
				$options = array_merge(
					array(
						'badges'   => array(),
						'schedule' => 'no',
					),
					wp_unslash( $_POST['yith_wcbm_badge_options'][ $index ] ) //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				);
				foreach ( $options as $option => $value ) {
					switch ( $option ) {
						case 'badges':
							$value = array_filter( array_map( 'sanitize_text_field', $value ) );
							break;
						case 'schedule_from':
						case 'schedule_to':
							$value = $value ? strtotime( 'schedule_from' === $option ? '00:00:01' : '23:59:59', strtotime( $value ) ) : '';
							break;
						default:
							$value = sanitize_text_field( $value );
							break;
					}
					$product->update_meta_data( 'yith_wcbm_badge_options_' . $option, $value );
					$product->save_meta_data();
					$this->delete_badge_meta_transient_for_product( $product );
				}
			}
		}

		/**
		 * Filters url to New Badge Rule page, to return custom plugin's one
		 *
		 * @param string $url    Url to new post page.
		 * @param array  $params Array of params.
		 *
		 * @return string
		 */
		public function add_new_rule_url( $url, $params ) {
			if ( isset( $params['post_type'] ) && 'yith_wcbm_badge_rule' === $params['post_type'] ) {
				$url = add_query_arg( array( 'action' => 'yith_wcbm_create_badge_rule' ), yith_wcbm_get_panel_url( 'badge-rules' ) );
			}

			return $url;
		}

		/**
		 * Add Premium Tabs
		 *
		 * @param array $admin_tabs_free Admin tabs.
		 *
		 * @return array
		 */
		public function add_premium_admin_tab( $admin_tabs_free ) {
			$settings_index = array_search( 'settings', array_keys( $admin_tabs_free ), true );

			return array_merge( array_slice( $admin_tabs_free, 0, $settings_index ), array( 'badge-rules' => esc_html__( 'Badge Rules', 'yith-woocommerce-badges-management' ) ), array_slice( $admin_tabs_free, $settings_index ) );
		}

		/**
		 * Handle premium version panel args
		 *
		 * @param array $args Admin panel args.
		 *
		 * @return array
		 */
		public function handle_premium_panel_args( $args ) {
			unset( $args['premium_tab'] );

			$args['help_tab'] = array(
				'main_video' => array(
					'desc' => _x( 'Check this video to learn how to <b>create custom badges to promote offers and products</b>', '[HELP TAB] Video title', 'yith-woocommerce-badges-management' ),
					'url'  => array(
						'en' => 'https://www.youtube.com/embed/y5vuhxsi-Qs',
						'it' => 'https://www.youtube.com/embed/637wBIFk-zQ',
						'es' => 'https://www.youtube.com/embed/3EQDsFoWzWI',
					),
				),
				'playlists'  => array(
					'en' => 'https://www.youtube.com/watch?v=y5vuhxsi-Qs&list=PLDriKG-6905lyEPsoPTxmRKB0-LvU7sG5',
					'it' => 'https://www.youtube.com/watch?v=637wBIFk-zQ&list=PL9c19edGMs0_C3Mg-E9V01Wa-I-hal5yq',
					'es' => 'https://www.youtube.com/watch?v=3EQDsFoWzWI&list=PL9Ka3j92PYJM2r00Ni74UBrGWgIkCFMnt',
				),
				'hc_url'     => 'https://support.yithemes.com/hc/en-us/categories/360003469177-YITH-WOOCOMMERCE-BADGE-MANAGEMENT',
				'doc_url'    => 'https://docs.yithemes.com/yith-woocommerce-badge-management/',
			);

			return $args;
		}

		/**
		 * Filter Settings Options
		 *
		 * @return array
		 */
		public function add_advanced_options() {
			return include YITH_WCBM_DIR . 'plugin-options/advanced-options.php';
		}

		/**
		 * Enqueue Scripts
		 */
		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id = ! ! $screen ? $screen->id : false;
			wp_register_script( 'yith_wcbm_product_badge_metabox', YITH_WCBM_ASSETS_JS_URL . '/product_badge_metabox.js', array( 'jquery' ), YITH_WCBM_VERSION, true );
			wp_register_script( 'yith_wcbm_badge_rules', YITH_WCBM_ASSETS_JS_URL . '/badge-rules.js', array( 'jquery', 'jquery-blockui', 'selectWoo' ), YITH_WCBM_VERSION, true );

			wp_register_style( 'yith_wcbm_product_admin', YITH_WCBM_ASSETS_CSS_URL . '/product-admin.css', array(), YITH_WCBM_VERSION );
			wp_register_style( 'yith_wcbm_badge_rules', YITH_WCBM_ASSETS_CSS_URL . '/badge-rules.css', array( 'yith_wcbm_admin_icons' ), YITH_WCBM_VERSION );

			if ( in_array( $screen_id, array( 'product', 'edit-product' ), true ) ) {
				wp_enqueue_script( 'yith_wcbm_product_badge_metabox' );
				wp_enqueue_style( 'yith_wcbm_product_admin' );
			}

			if ( in_array( $screen_id, array( YITH_WCBM_Post_Types::$badge, 'edit-' . YITH_WCBM_Post_Types::$badge ), true ) ) {
				YITH_WCBM_Frontend()->enqueue_scripts();
			}

			if ( in_array( $screen_id, array( YITH_WCBM_Post_Types_Premium::$badge_rule, 'edit-' . YITH_WCBM_Post_Types_Premium::$badge_rule ), true ) ) {
				wp_enqueue_style( 'yith_wcbm_badge_rules' );
				wp_enqueue_script( 'yith_wcbm_badge_rules' );
				wp_enqueue_style( 'yith_wcbm_admin_style' );
			}

			$data_to_localize = array(
				'yith_wcbm_metabox_options' => array(
					'object_name' => 'yithWcbmMetaboxPremiumOptions',
					'data'        => array(
						'badgePlaceholders' => yith_wcbm_get_badges_placeholders_values( 'template' ),
						'badgeList'         => yith_wcbm_get_badge_list_with_data(),
						'cssBadges'         => $this->get_badges_to_localize( 'css' ),
						'advancedBadges'    => $this->get_badges_to_localize( 'advanced' ),
						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
						'modals'            => array(
							'badgePlaceholders' => array(
								'title'   => __( 'Placeholders', 'yith-woocommerce-badges-management' ),
								'content' => yith_wcbm_get_view_html( 'badge-placeholders-modal-content.php' ),
							),
						),
						'actions'           => array(
							'addBadgeToLibrary' => 'yith_wcbm_add_badge_to_library',
						),
						'security'          => array(
							'addBadgeToLibrary' => wp_create_nonce( 'yith_wcbm_add_badge_to_library' ),
						),
					),
				),
				'yith_wcbm_badge_rules'     => array(
					'object_name' => 'yithWcbmBadgeRules',
					'data'        => array(
						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
						'security'          => wp_create_nonce( 'yith_wcbm_badge_rules' ),
						'actions'           => array(
							'toggleRuleEnable' => 'yith_wcbm_badge_rule_toggle_enable',
						),
						'addBadgeRuleModal' => array(
							'title'   => __( 'Rule type', 'yith-woocommerce-badges-management' ),
							'content' => yith_wcbm_get_view_html( 'add-badge-rule-modal-content.php' ),
						),
						'i18n'              => array(
							'deleteBadgeRuleModal' => array(
								'title'         => _x( 'Confirm delete', '[ADMIN] Confirm delete modal title on badge rules page', 'yith-woocommerce-badges-management' ),
								// translators: %s is the badge rule name.
								'message'       => _x( 'Are you sure you want to delete <b>"%s"</b>?', '[ADMIN] Confirm delete modal message on badge rules page', 'yith-woocommerce-badges-management' ),
								'confirmButton' => _x( 'Delete', '[ADMIN] Confirm delete modal button on badge rules page', 'yith-woocommerce-badges-management' ),
							),
						),
					),
				),
			);

			foreach ( $data_to_localize as $handle => $data ) {
				wp_localize_script( $handle, $data['object_name'], $data['data'] );
			}

			if ( yith_wcbm_is_editing_badge_page() ) {
				ob_start();
				for ( $i = 1; $i <= 30; $i++ ) {
					$badge_style = array(
						'type'  => 'css',
						'style' => $i,
					);
					if ( $i <= 15 ) {
						yith_wcbm_get_badge_style( $badge_style );
					}
					$badge_style['type'] = 'advanced';
					yith_wcbm_get_badge_style( $badge_style );
				}
				$css = ob_get_clean();
				wp_add_inline_style( 'yith_wcbm_admin_style', $css );
			}
		}

		/**
		 * AJAX get Advanced Badge style
		 *
		 * @depreacted Used in old Badge editing metabox to load the badge style through AJAX
		 */
		public function get_advanced_badge_style() {
			exit();
		}

		/**
		 * AJAX get Badge style
		 *
		 * @depreacted Used in old Badge editing metabox to load the badge style through AJAX
		 */
		public function get_css_badge_style() {
			exit();
		}

		/**
		 * =========================================
		 *             QUICK AND BULK EDIT
		 * =========================================
		 *
		 * Add column in product table list
		 *
		 * @param array $columns Product Table Columns.
		 *
		 * @since  1.0.0
		 */
		public function add_columns( $columns ) {
			$date = isset( $columns['date'] ) ? $columns['date'] : '';
			unset( $columns['date'] );
			$columns['yith_wcbm_badge'] = _x( 'Badge', 'Admin:title of column in products table', 'yith-woocommerce-badges-management' );
			if ( ! empty( $date ) ) {
				$columns['date'] = $date;
			}

			return $columns;
		}

		/**
		 * Set the post status to publish when a badge is untrashed
		 *
		 * @param string $new_status New Post Status.
		 * @param int    $post_id    Post ID.
		 *
		 * @return string
		 */
		public function untrash_badge_post_status( $new_status, $post_id ) {
			return in_array( get_post_type( $post_id ), array( YITH_WCBM_Post_Types::$badge, YITH_WCBM_Post_Types_Premium::$badge_rule ), true ) ? 'publish' : $new_status;
		}

		/**
		 * Render custom columns in product table list
		 *
		 * @param array $column  Column.
		 * @param int   $post_id Post ID.
		 *
		 * @since  1.0.0
		 */
		public function custom_columns( $column, $post_id ) {
			if ( 'yith_wcbm_badge' === $column ) {
				$product   = wc_get_product( $post_id );
				$badge_ids = yith_wcbm_get_product_badge_ids_from_meta( $product, apply_filters( 'yith_wcbm_show_product_variation_badges_in_badge_column', true, $product ) );

				if ( ! $badge_ids ) {
					echo '<span class="na">–</span>';
				} else {
					$html_array = array();
					foreach ( $badge_ids as $badge_id ) {
						$title       = esc_html( get_the_title( $badge_id ) );
						$post_status = get_post_status( $badge_id );
						if ( 'publish' !== $post_status ) {
							$title .= "($post_status)";
						}
						$link         = esc_url( get_edit_post_link( $badge_id ) );
						$html_array[] = "<a href='{$link}'>{$title}</a>";
					}
					if ( $html_array ) {
						echo implode( ', ', $html_array ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					$badge_titles = array_map( 'get_the_title', $badge_ids );
					echo "<input type=hidden class='yith-wcbm-product-badges' value='" . esc_attr( wp_json_encode( array_combine( $badge_ids, $badge_titles ) ) ) . "'>";
				}
			}
		}

		/**
		 * Print Badge Library Field
		 *
		 * @param array $args Field Args.
		 */
		public function print_badge_library_field( $args = array() ) {
			$defaults        = array(
				'id'           => '',
				'library'      => array(),
				'allow_upload' => 'no',
				'url'          => '',
			);
			$args            = wp_parse_args( $args, $defaults );
			$args['library'] = array_flip( $args['library'] );
			foreach ( $args['library'] as $badge_id => &$badge_url ) {
				$badge_key = str_replace( '.svg', '', $badge_id );
				if ( array_key_exists( $badge_key, $args['importable_badges'] ) ) {
					$badge_url = $args['importable_badges'][ $badge_key ]['previewUrl'];
					unset( $args['importable_badges'][ $badge_key ] );
				} else {
					$badge_url = $args['url'] . $badge_id;
				}
			}
			if ( ! empty( $args['id'] ) && ! empty( $args['library'] ) && ! empty( $args['url'] ) ) {
				yith_wcbm_get_view( '/fields/badge-library-premium.php', compact( 'args' ) );
			}
		}

		/**
		 * Print badge fields in product bulk editing panel
		 */
		public function print_badge_options_in_product_bulk_edit() {
			static $print_nonce = true;
			if ( $print_nonce ) {
				$print_nonce = false;
				wp_nonce_field( YITH_WCBM_INIT, 'bulk_badge_edit_nonce' );
			}

			yith_wcbm_get_view( 'fields/bulk-edit-product-badge-field.php' );
		}

		/**
		 * Save badge options on product bulk editing
		 *
		 * @param WC_Product $product The product.
		 */
		public function save_badge_options_in_product_bulk_edit( $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_REQUEST['yith_wcbm_bulk_badge_ids'] ) ) {
				$badge_ids = is_array( $_REQUEST['yith_wcbm_bulk_badge_ids'] ) ? array_map( 'absint', $_REQUEST['yith_wcbm_bulk_badge_ids'] ) : array( absint( $_REQUEST['yith_wcbm_bulk_badge_ids'] ) );
				if ( $badge_ids ) {
					$product_badges = $product->get_meta( '_yith_wcbm_badge_ids' );
					$product_badges = is_array( $product_badges ) && $product_badges ? array_unique( array_merge( $badge_ids, $product_badges ) ) : $badge_ids;
					$product->update_meta_data( '_yith_wcbm_badge_ids', defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM ? $product_badges : array( current( $product_badges ) ) );
					$product->save_meta_data();
					$this->delete_badge_meta_transient_for_product( $product );
				}
			}
			// phpcs:enable
		}

		/**
		 * Add badges options in Quick Edit
		 *
		 * @since  2.0
		 */
		public function print_badge_options_in_product_quick_edit() {
			yith_wcbm_get_view( 'fields/quick-edit-product-badge-field.php' );
		}

		/**
		 * Save badges in Quick Edit
		 *
		 * @param WC_Product $product Product.
		 *
		 * @since 2.0
		 */
		public function save_badge_options_in_product_quick_edit( $product ) {
			$badge_ids = ! empty( $_REQUEST['yith_wcbm_quick_badge_ids'] ) ? array_map( 'absint', wp_unslash( $_REQUEST['yith_wcbm_quick_badge_ids'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product->update_meta_data( '_yith_wcbm_badge_ids', defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM ? $badge_ids : array( current( $badge_ids ) ) );
			$product->save_meta_data();
			$this->delete_badge_meta_transient_for_product( $product );
		}

		/**
		 * Check if is a panel page
		 */
		public function is_panel() {
			$screen_id = is_callable( 'get_current_screen' ) ? get_current_screen()->id ?? false : false;

			return is_admin() && ( ( $screen_id && in_array( str_replace( 'edit-', '', $screen_id ), array( 'yith-wcbm-badge', 'ywcbm-badge-rule' ), true ) ) || ( isset( $_GET['page'] ) && $this->panel_page === $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Print the newer than field for badge rules editing page
		 *
		 * @param array $field Field.
		 */
		public function print_badge_rules_newer_than_field( $field ) {
			$field['type'] = 'number';
			// translators: %s is the input used to insert the number of days.
			echo sprintf( __( '%s days', 'yith-woocommerce-badges-management' ), yith_plugin_fw_get_field( $field ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/** ------------------------------------------
		 * Deprecated Methods
		 */

		/**
		 * Add Premium Tabs
		 *
		 * @param array $admin_tabs_free Admin tabs.
		 *
		 * @return array
		 * @depreacted since 2.0.0
		 */
		public function add_advanced_admin_tab( $admin_tabs_free ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::add_advanced_admin_tab', '2.0.0', 'YITH_WCBM_Admin_Premium::add_premium_admin_tab' );

			return $this->add_premium_admin_tab( $admin_tabs_free );
		}

		/**
		 * Add premium badge metabox fields
		 *
		 * @return array
		 *
		 * @deprecated since 2.0.0
		 */
		public function add_badge_metabox_options() {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::add_badge_metabox_options', '2.0.0' );

			return array();
		}

		/**
		 * Add Bulk edit for badges assigned to a product
		 *
		 * @access     public
		 * @depreacted since 2.0
		 * @use        print_badge_fields_in_product_bulk_edit() method instead
		 * @since      1.0.0
		 */
		public function woocommerce_product_bulk_edit_end() {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::woocommerce_product_bulk_edit_end', '2.0.0', 'YITH_WCBM_Admin_Premium::print_badge_options_in_product_bulk_edit' );
			$this->print_badge_options_in_product_bulk_edit();
		}

		/**
		 * Save charts for bulk edit [AJAX]
		 *
		 * @param WC_Product $product Product.
		 *
		 * @access     public
		 * @depreacted since 2.0.0
		 * @use        save_badge_options_in_products_bulk_edit() method instead
		 * @since      1.0.0
		 */
		public function save_bulk_edit( $product ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::save_bulk_edit', '2.0.0', 'YITH_WCBM_Admin_Premium::save_badge_options_in_product_bulk_edit' );
			$this->save_badge_options_in_product_bulk_edit( $product );
		}

		/**
		 * Add badges options in Quick Edit
		 *
		 * @since      1.3.22
		 * @depreacted since 2.0
		 * @use        print_badge_options_in_product_quick_edit() method instead
		 */
		public function quick_edit_badges() {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::quick_edit_badges', '2.0.0', 'YITH_WCBM_Admin_Premium::print_badge_options_in_product_quick_edit' );
			$this->print_badge_options_in_product_quick_edit();
		}

		/**
		 * Save badges in Quick Edit
		 *
		 * @param WC_Product $product Product.
		 *
		 * @since      1.3.22
		 * @depreacted since 2.0.0
		 * @use        save_badge_options_in_product_quick_edit() method instead.
		 */
		public function save_quick_edit( $product ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::badge_settings_save', '2.0.0', 'YITH_WCBM_Admin_Premium::save_badge_options_in_product_quick_edit' );
			$this->save_badge_options_in_product_quick_edit( $product );
		}

		/**
		 * Save product badge settings
		 *
		 * @param int $product_id The product ID.
		 *
		 * @depreacted since 2.0.0 - Now the options are saved through the Data-Store.
		 */
		public function badge_settings_save( $product_id ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::badge_settings_save', '2.0.0' );
		}

		/**
		 * Print the select for badges in panel
		 *
		 * @param array $field Field.
		 *
		 * @depracated since 2.0.0
		 */
		public function print_badges_select( $field ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin_Premium::print_badges_select', '2.0.0' );

			static $badge_options = array();

			$badge_options = apply_filters( 'yith_wcbm_print_badges_select_badge_options', $badge_options, $field );

			if ( ! $badge_options ) {
				$badge_options = array(
					'none' => __( 'None', 'yith-woocommerce-badges-management' ),
				);

				$badge_ids = yith_wcbm_get_badges();
				foreach ( $badge_ids as $badge_id ) {
					$badge_options[ $badge_id ] = get_the_title( $badge_id );
				}
			}

			$field['type']    = 'select';
			$field['options'] = $badge_options;

			yith_plugin_fw_get_field( $field, true );
		}
	}
}
