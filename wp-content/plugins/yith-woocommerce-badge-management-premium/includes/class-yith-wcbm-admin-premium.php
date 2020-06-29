<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCBM_PREMIUM' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Badge Management
 *
 * @class   YITH_WCBM_Admin_Premium
 * @package YITH WooCommerce Badge Management
 * @since   1.0.0
 * @author  Yithemes
 */

if ( ! class_exists( 'YITH_WCBM_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBM_Admin_Premium extends YITH_WCBM_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $_instance;

		/**
		 * Constructor
		 *
		 * @access public
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// AJAX Action for metabox_options_premium.js
			add_action( 'wp_ajax_yith_get_advanced_badge_style', array( $this, 'get_advanced_badge_style' ) );
			add_action( 'wp_ajax_yith_get_css_badge_style', array( $this, 'get_css_badge_style' ) );

			add_filter( 'yith_wcbm_panel_settings_options', array( $this, 'add_advanced_options' ) );

			add_filter( 'yith_wcbm_settings_admin_tabs', array( $this, 'add_advanced_admin_tab' ) );

			// Add Badge column in Product list
			add_filter( 'manage_product_posts_columns', array( $this, 'add_columns' ), 15 );
			add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );

			// Add bulk edit for Badge assigned to a product
			add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'woocommerce_product_bulk_edit_end' ) );
			add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'save_bulk_edit' ), 10, 2 );

			add_action( 'woocommerce_product_quick_edit_end', array( $this, 'quick_edit_badges' ) );
			add_action( 'woocommerce_product_quick_edit_save', array( $this, 'save_quick_edit' ), 10, 2 );

			add_action( 'yith_wcbm_print_badges_select', array( $this, 'print_badges_select' ), 10, 1 );

			/**
			 * manage columns in Badge List
			 *
			 * @since 1.2.29
			 */
			add_filter( 'manage_' . YITH_WCBM_Post_Types::$badge . '_posts_columns', array( $this, 'badge_manage_columns' ), 15 );
			add_action( 'manage_' . YITH_WCBM_Post_Types::$badge . '_posts_custom_column', array( $this, 'badge_custom_columns' ), 10, 2 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * add the columns in Badge List
		 *
		 * @param $columns
		 *
		 * @return array
		 * @since 1.2.29
		 */
		public function badge_manage_columns( $columns ) {
			$columns['yith_wcbm_preview'] = __( 'Preview', 'yith-woocommerce-badges-management' );

			$sorded_columns = array();
			$custom_sort    = array( 'cb', 'title', 'yith_wcbm_preview' );
			foreach ( $custom_sort as $cs ) {
				if ( isset( $columns[ $cs ] ) ) {
					$sorded_columns[ $cs ] = $columns[ $cs ];
				}
			}

			if ( isset( $columns['date'] ) ) {
				$sorded_columns['date'] = $columns['date'];
			}

			return $sorded_columns;
		}

		/**
		 * Render the columns in Badge List
		 *
		 * @param $column
		 * @param $post_id
		 *
		 * @return string
		 * @since 1.2.29
		 */
		public function badge_custom_columns( $column, $post_id ) {
			switch ( $column ) {
				case 'yith_wcbm_preview':
					echo yith_wcbm_get_badge_premium( $post_id, 'preview' );
					break;
			}
		}


		/**
		 * @param WP_Post $post
		 */
		function badge_settings_tabs( $post ) {
			$product    = wc_get_product( $post->ID );
			$badge_info = yith_wcbm_get_product_badge_info( $product );
			?>
			<p class="form-field">
				<select name="_yith_wcbm_product_meta[id_badge][]" class="wc-enhanced-select select" multiple>
					<?php $badges = yith_wcbm_get_badges( array( 'suppress_filters' => false ) ); ?>
					<?php foreach ( $badges as $current_badge_id ) : ?>
						<option value="<?php echo $current_badge_id ?>" <?php selected( in_array( $current_badge_id, $badge_info['badge_ids'] ) ) ?>><?php echo get_the_title( $current_badge_id ) ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<div id="yith-wcbm-metabox-schedule-options">
				<h3><?php _e( 'Schedule', 'yith-woocommerce-badges-management' ) ?></h3>

				<div id="yith-wcbm-metabox-schedule-container">
					<p>
						<label for="yith-wcbm-badge-start-date"><?php _e( 'Starting Date', 'yith-woocommerce-badges-management' ) ?></label>
						<input id="yith-wcbm-badge-start-date" type="text" class="yith-wcbm-datepicker" name="_yith_wcbm_product_meta[start_date]" value="<?php echo $badge_info['start_date']; ?>"
								placeholder="YYYY-MM-DD">
						<span class="dashicons dashicons-no-alt yith-wcbm-delete-input"></span>
					</p>

					<p>
						<label for="yith-wcbm-badge-end-date"><?php _e( 'Ending Date', 'yith-woocommerce-badges-management' ) ?></label>
						<input id="yith-wcbm-badge-end-date" type="text" class="yith-wcbm-datepicker" name="_yith_wcbm_product_meta[end_date]" value="<?php echo $badge_info['end_date']; ?>"
								placeholder="YYYY-MM-DD">
						<span class="dashicons dashicons-no-alt yith-wcbm-delete-input"></span>
					</p>
				</div>
			</div>
			<?php
		}


		public function add_advanced_admin_tab( $admin_tabs_free ) {
			$admin_tabs_free['category']       = __( 'Category Badges', 'yith-woocommerce-badges-management' );
			$admin_tabs_free['shipping-class'] = __( 'Shipping Class Badges', 'yith-woocommerce-badges-management' );
			unset( $admin_tabs_free['premium'] );

			return $admin_tabs_free;
		}

		public function add_advanced_options() {
			return include YITH_WCBM_DIR . 'plugin-options/advanced-options.php';
		}

		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();
			$screen = get_current_screen();

			if ( 'product' === $screen->id ) {
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
				wp_enqueue_script( 'yith_wcbm_product_badge_metabox', YITH_WCBM_ASSETS_URL . '/js/product_badge_metabox.js', array( 'jquery' ), YITH_WCBM_VERSION, true );
			}

			$enqueue_frontend_css_in = array(
				'edit-' . YITH_WCBM_Post_Types::$badge,
				YITH_WCBM_Post_Types::$badge,
			);

			if ( in_array( $screen->id, $enqueue_frontend_css_in ) ) {
				YITH_WCBM_Frontend()->enqueue_scripts();
			}

			if ( 'edit-product' === $screen->id ) {
				wp_enqueue_script( 'yith_wcbm_admin_edit', YITH_WCBM_ASSETS_URL . '/js/admin_edit.js', array( 'jquery' ), YITH_WCBM_VERSION, true );
			}

		}

		/**
		 * Metabox Render [Override]
		 *
		 * @access public
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since  1.0.0
		 */
		public function metabox_render( $post ) {
			$bm_meta = get_post_meta( $post->ID, '_badge_meta', true );

			$default = array(
				'type'                        => 'text',
				'text'                        => '',
				'txt_color_default'           => '#000000',
				'txt_color'                   => '#000000',
				'bg_color_default'            => '#2470FF',
				'bg_color'                    => '#2470FF',
				'advanced_bg_color'           => '',
				'advanced_bg_color_default'   => '',
				'advanced_text_color'         => '',
				'advanced_text_color_default' => '',
				'advanced_badge'              => 1,
				'advanced_display'            => 'percentage',
				'css_badge'                   => 1,
				'css_bg_color'                => '',
				'css_bg_color_default'        => '',
				'css_text_color'              => '',
				'css_text_color_default'      => '',
				'css_text'                    => '',
				'width'                       => '100',
				'height'                      => '50',
				'position'                    => 'top-left',
				'image_url'                   => '',
				'pos_top'                     => 0,
				'pos_bottom'                  => 0,
				'pos_left'                    => 0,
				'pos_right'                   => 0,
				'border_top_left_radius'      => 0,
				'border_top_right_radius'     => 0,
				'border_bottom_right_radius'  => 0,
				'border_bottom_left_radius'   => 0,
				'padding_top'                 => 0,
				'padding_bottom'              => 0,
				'padding_left'                => 0,
				'padding_right'               => 0,
				'font_size'                   => 13,
				'line_height'                 => - 1,
				'opacity'                     => 100,
				'rotation'                    => array( 'x' => 0, 'y' => 0, 'z' => 0 ),
				'flip_text_horizontally'      => false,
				'flip_text_vertically'        => false,
				'scale_on_mobile'             => 1,
			);

			if ( ! isset( $bm_meta['pos_top'] ) ) {
				$position = ( isset( $bm_meta['position'] ) ) ? $bm_meta['position'] : 'top-left';
				if ( $position == 'top-right' ) {
					$default['pos_bottom'] = 'auto';
					$default['pos_left']   = 'auto';
				} else if ( $position == 'bottom-left' ) {
					$default['pos_top']   = 'auto';
					$default['pos_right'] = 'auto';
				} else if ( $position == 'bottom-right' ) {
					$default['pos_top']  = 'auto';
					$default['pos_left'] = 'auto';
				} else {
					$default['pos_bottom'] = 'auto';
					$default['pos_right']  = 'auto';
				}
			}

			$args = wp_parse_args( $bm_meta, $default );

			$args = apply_filters( 'yith_wcbm_metabox_options_content_args', $args );

			yith_wcbm_metabox_options_content_premium( $args );
		}

		public function metabox_save( $post_id ) {
			if ( ! empty( $_POST['_badge_meta'] ) ) {
				$badge_meta['type']      = ( ! empty( $_POST['_badge_meta']['type'] ) ) ? $_POST['_badge_meta']['type'] : '';
				$badge_meta['text']      = ( ! empty( $_POST['_badge_meta']['text'] ) ) ? $_POST['_badge_meta']['text'] : '';
				$badge_meta['txt_color'] = ( ! empty( $_POST['_badge_meta']['txt_color'] ) ) ? $_POST['_badge_meta']['txt_color'] : '';
				$badge_meta['bg_color']  = ( ! empty( $_POST['_badge_meta']['bg_color'] ) ) ? esc_url( $_POST['_badge_meta']['bg_color'] ) : '';
				$badge_meta['width']     = ( ! empty( $_POST['_badge_meta']['width'] ) ) ? $_POST['_badge_meta']['width'] : '';
				$badge_meta['height']    = ( ! empty( $_POST['_badge_meta']['height'] ) ) ? $_POST['_badge_meta']['height'] : '';
				$badge_meta['position']  = ( ! empty( $_POST['_badge_meta']['position'] ) ) ? $_POST['_badge_meta']['position'] : 'top-left';
				$badge_meta['image_url'] = ( ! empty( $_POST['_badge_meta']['image_url'] ) ) ? $_POST['_badge_meta']['image_url'] : '';
				// P R E M I U M
				$badge_meta['advanced_bg_color']          = ( ! empty( $_POST['_badge_meta']['advanced_bg_color'] ) ) ? $_POST['_badge_meta']['advanced_bg_color'] : '';
				$badge_meta['advanced_text_color']        = ( ! empty( $_POST['_badge_meta']['advanced_text_color'] ) ) ? $_POST['_badge_meta']['advanced_text_color'] : '';
				$badge_meta['advanced_badge']             = ( ! empty( $_POST['_badge_meta']['advanced_badge'] ) ) ? $_POST['_badge_meta']['advanced_badge'] : 1;
				$badge_meta['advanced_display']           = ( ! empty( $_POST['_badge_meta']['advanced_display'] ) ) ? $_POST['_badge_meta']['advanced_display'] : 'percentage';
				$badge_meta['css_bg_color']               = ( ! empty( $_POST['_badge_meta']['css_bg_color'] ) ) ? $_POST['_badge_meta']['css_bg_color'] : '';
				$badge_meta['css_text_color']             = ( ! empty( $_POST['_badge_meta']['css_text_color'] ) ) ? $_POST['_badge_meta']['css_text_color'] : '';
				$badge_meta['css_text']                   = ( ! empty( $_POST['_badge_meta']['css_text'] ) ) ? $_POST['_badge_meta']['css_text'] : '';
				$badge_meta['css_badge']                  = ( ! empty( $_POST['_badge_meta']['css_badge'] ) ) ? $_POST['_badge_meta']['css_badge'] : 1;
				$badge_meta['pos_top']                    = ( ! empty( $_POST['_badge_meta']['pos_top'] ) ) ? $_POST['_badge_meta']['pos_top'] : 0;
				$badge_meta['pos_bottom']                 = ( ! empty( $_POST['_badge_meta']['pos_bottom'] ) ) ? $_POST['_badge_meta']['pos_bottom'] : 0;
				$badge_meta['pos_left']                   = ( ! empty( $_POST['_badge_meta']['pos_left'] ) ) ? $_POST['_badge_meta']['pos_left'] : 0;
				$badge_meta['pos_right']                  = ( ! empty( $_POST['_badge_meta']['pos_right'] ) ) ? $_POST['_badge_meta']['pos_right'] : 0;
				$badge_meta['border_top_left_radius']     = ( ! empty( $_POST['_badge_meta']['border_top_left_radius'] ) ) ? $_POST['_badge_meta']['border_top_left_radius'] : 0;
				$badge_meta['border_top_right_radius']    = ( ! empty( $_POST['_badge_meta']['border_top_right_radius'] ) ) ? $_POST['_badge_meta']['border_top_right_radius'] : 0;
				$badge_meta['border_bottom_right_radius'] = ( ! empty( $_POST['_badge_meta']['border_bottom_right_radius'] ) ) ? $_POST['_badge_meta']['border_bottom_right_radius'] : 0;
				$badge_meta['border_bottom_left_radius']  = ( ! empty( $_POST['_badge_meta']['border_bottom_left_radius'] ) ) ? $_POST['_badge_meta']['border_bottom_left_radius'] : 0;
				$badge_meta['padding_top']                = ( ! empty( $_POST['_badge_meta']['padding_top'] ) ) ? $_POST['_badge_meta']['padding_top'] : 0;
				$badge_meta['padding_bottom']             = ( ! empty( $_POST['_badge_meta']['padding_bottom'] ) ) ? $_POST['_badge_meta']['padding_bottom'] : 0;
				$badge_meta['padding_left']               = ( ! empty( $_POST['_badge_meta']['padding_left'] ) ) ? $_POST['_badge_meta']['padding_left'] : 0;
				$badge_meta['padding_right']              = ( ! empty( $_POST['_badge_meta']['padding_right'] ) ) ? $_POST['_badge_meta']['padding_right'] : 0;
				$badge_meta['font_size']                  = ( ! empty( $_POST['_badge_meta']['font_size'] ) ) ? $_POST['_badge_meta']['font_size'] : 13;
				$badge_meta['line_height']                = ( ! empty( $_POST['_badge_meta']['line_height'] ) ) ? $_POST['_badge_meta']['line_height'] : - 1;
				$badge_meta['opacity']                    = ( ! empty( $_POST['_badge_meta']['opacity'] ) ) ? $_POST['_badge_meta']['opacity'] : 100;
				$badge_meta['rotation']                   = ( ! empty( $_POST['_badge_meta']['rotation'] ) ) ? $_POST['_badge_meta']['rotation'] : array( 'x' => 0, 'y' => 0, 'z' => 0 );
				$badge_meta['flip_text_horizontally']     = ( ! empty( $_POST['_badge_meta']['flip_text_horizontally'] ) ) ? true : false;
				$badge_meta['flip_text_vertically']       = ( ! empty( $_POST['_badge_meta']['flip_text_vertically'] ) ) ? true : false;
				$badge_meta['scale_on_mobile']            = ( ! empty( $_POST['_badge_meta']['scale_on_mobile'] ) ) ? $_POST['_badge_meta']['scale_on_mobile'] : 1;


				//--wpml-------------
				yith_wcbm_wpml_register_string( 'yith-woocommerce-badges-management', sanitize_title( $badge_meta['text'] ), $badge_meta['text'] );
				yith_wcbm_wpml_register_string( 'yith-woocommerce-badges-management', sanitize_title( $badge_meta['css_text'] ), $badge_meta['css_text'] );
				//-------------------

				update_post_meta( $post_id, '_badge_meta', $badge_meta );
			}
		}

		public function get_advanced_badge_style() {
			if ( isset( $_POST['id_badge_style'], $_POST['color'], $_POST['text_color'] ) ) {
				$args = array(
					'type'                   => 'advanced',
					'id_advanced_badge'      => null,
					'id_badge_style'         => $_POST['id_badge_style'],
					'advanced_bg_color'      => $_POST['color'],
					'advanced_text_color'    => $_POST['text_color'],
					'flip_text_horizontally' => $_POST['flip_text_horizontally'],
					'flip_text_vertically'   => $_POST['flip_text_vertically'],
				);
				yith_wcbm_get_badge_style( $args );
				wp_die();
			}
		}

		public function get_css_badge_style() {
			if ( isset( $_POST['id_badge_style'], $_POST['color'], $_POST['text_color'] ) ) {
				$args = array(
					'type'           => 'css',
					'id_css_badge'   => null,
					'id_badge_style' => $_POST['id_badge_style'],
					'css_bg_color'   => $_POST['color'],
					'css_text_color' => $_POST['text_color'],
				);
				yith_wcbm_get_badge_style( $args );
				wp_die();
			}
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( function_exists( 'YIT_Plugin_Licence' ) ) {
				YIT_Plugin_Licence()->register( YITH_WCBM_INIT, YITH_WCBM_SECRET_KEY, YITH_WCBM_SLUG );
			}
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( function_exists( 'YIT_Upgrade' ) ) {
				YIT_Upgrade()->register( YITH_WCBM_SLUG, YITH_WCBM_INIT );
			}
		}


		/** =========================================
		 *             QUICK AND BULK EDIT
		 *  =========================================
		 */
		/**
		 * Add column in product table list
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
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
		 * Add content in custom column in product table list
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function custom_columns( $column, $post_id ) {
			if ( $column == 'yith_wcbm_badge' ) {
				$product    = wc_get_product( $post_id );
				$badge_info = yith_wcbm_get_product_badge_info( $product );
				$badge_ids  = $badge_info['badge_ids'];

				if ( ! $badge_ids ) {
					echo '<span class="na">–</span>';
				} else {
					$html_array = array();
					foreach ( $badge_ids as $badge_id ) {
						$title       = get_the_title( $badge_id );
						$post_status = get_post_status( $badge_id );
						if ( 'publish' !== $post_status ) {
							$title .= "($post_status)";
						}
						$link         = get_edit_post_link( $badge_id );
						$html_array[] = "<a href='{$link}'>{$title}</a>";
					}
					if ( $html_array ) {
						echo implode( ', ', $html_array );
					}

					$json_badges = json_encode( $badge_ids );
					echo "<input type=hidden class='yith-wcbm-product-badges' value='{$json_badges}'>";
				}
			}
		}

		/**
		 * Add Bulk edit for badges assigned to a product
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_product_bulk_edit_end() {
			static $printNonce = true;
			if ( $printNonce ) {
				$printNonce = false;
				wp_nonce_field( YITH_WCBM_INIT, 'bulk_badge_edit_nonce' );
			}
			?>
			<label>
				<span class="title"><?php esc_html_e( 'Select Badge', 'yith-woocommerce-badges-management' ); ?></span>
				<span class="input-text-wrap">
					<select name="yith_wcbm_bulk_badge_id">
                        <option value="" selected><?php _e( '— No Change —', 'woocommerce' ) ?></option>
                        <option value="none"><?php _e( 'None', 'yith-woocommerce-badges-management' ) ?></option>
                        <?php
						$badges = yith_wcbm_get_badges();
						foreach ( $badges as $badge_id ) {
							$title = get_the_title( $badge_id );
							echo "<option value='{$badge_id}'>{$title}</option>";
						}
						?>
                    </select>
			</span>
			</label>
			<?php
		}

		/**
		 * Save charts for bulk edit [AJAX]
		 *
		 * @param WC_Product $product
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function save_bulk_edit( $product ) {
			if ( ! empty( $_REQUEST['yith_wcbm_bulk_badge_id'] ) ) {
				$badge_id = $_REQUEST['yith_wcbm_bulk_badge_id'];
				if ( $badge_id == 'none' ) {
					yit_delete_prop( $product, '_yith_wcbm_product_meta' );
				} else {
					$bm_meta             = yit_get_prop( $product, '_yith_wcbm_product_meta', true );
					$bm_meta             = ! empty( $bm_meta ) ? $bm_meta : array();
					$bm_meta['id_badge'] = $badge_id;
					yit_save_prop( $product, '_yith_wcbm_product_meta', $bm_meta );
				}
			}
		}

		/**
		 * Add badge section in Quick Edit
		 *
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since  1.3.22
		 */
		public function quick_edit_badges() {
			$badges = yith_wcbm_get_badges();
			?>
			<div class="inline-edit-group yith-wcbm-inline-edit-col">
				<span class="title"><?php _e( 'Badges', 'yith-woocommerce-badges-management' ); ?></span>
				<ul class="badges-checklist product_badges-checklist cat-checklist product_cat-checklist">
					<?php
					foreach ( $badges as $badge_id ) {
						$_title = get_the_title( $badge_id );
						echo "<li id='badge-{$badge_id}'><label class='selectit'>
                                    <input value='{$badge_id}' name='yith_wcbm_product_badges[]' id='in-badge-{$badge_id}' type='checkbox' />{$_title}
                                </label></li>";
					}
					?>
				</ul>
			</div>
			<?php
		}

		/**
		 * Save badges in Quick Edit
		 *
		 * @param WC_Product $product
		 *
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since  1.3.22
		 */
		public function save_quick_edit( $product ) {
			$badges = ! empty( $_REQUEST['yith_wcbm_product_badges'] ) ? $_REQUEST['yith_wcbm_product_badges'] : array();

			$meta             = $product->get_meta( '_yith_wcbm_product_meta' );
			$meta             = ! empty( $meta ) && is_array( $meta ) ? $meta : array();
			$meta['id_badge'] = $badges;
			$product->update_meta_data( '_yith_wcbm_product_meta', $meta );
			$product->save_meta_data();
		}

		/**
		 * Save product badge settings
		 *
		 * @param int $product_id The product ID.
		 */
		public function badge_settings_save( $product_id ) {
			// todo: sanitize each element of the array as the free version
			if ( ! empty( $_POST['_yith_wcbm_product_meta'] ) ) {
				update_post_meta( $product_id, '_yith_wcbm_product_meta', $_POST['_yith_wcbm_product_meta'] );
			}
		}

		/**
		 * Print the select for badges in panel
		 *
		 * @param array $field
		 *
		 * @since 1.4.0
		 */
		public function print_badges_select( $field ) {
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

/**
 * Unique access to instance of YITH_WCBM_Admin_Premium class
 *
 * @return YITH_WCBM_Admin_Premium
 * @deprecated since 1.3.0 use YITH_WCBM_Admin() instead
 * @since      1.0.0
 */
function YITH_WCBM_Admin_Premium() {
	return YITH_WCBM_Admin();
}
