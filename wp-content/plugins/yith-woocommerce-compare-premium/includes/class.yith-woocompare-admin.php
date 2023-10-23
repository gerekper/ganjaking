<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Admin class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 2.0.0
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_Woocompare_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_Woocompare_Admin {
		/**
		 * Plugin options
		 *
		 * @since 1.0.0
		 * @var array
		 * @access public
		 */
		public $options = array();

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WOOCOMPARE_VERSION;

		/**
		 * Panel Object
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * Premium tab template file name
		 *
		 * @var string
		 */
		protected $premium = 'premium.php';

		/**
		 * Premium version landing link
		 *
		 * @var string
		 */
		protected $premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-compare';

		/**
		 * Compare panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_woocompare_panel';

		/**
		 * Various links
		 *
		 * @since 1.0.0
		 * @var string
		 * @access public
		 */
		public $doc_url = 'http://yithemes.com/docs-plugins/yith-woocommerce-compare/';

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WOOCOMPARE_DIR . '/' . basename( YITH_WOOCOMPARE_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'yith_woocompare_premium', array( $this, 'premium_tab' ) );

			add_action( 'admin_init', array( $this, 'register_pointer' ) );
			add_action( 'admin_init', array( $this, 'default_options' ), 99 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 20 );

			add_action( 'woocommerce_admin_field_woocompare_image_width', array( $this, 'admin_fields_woocompare_image_width' ) );
			add_action( 'woocommerce_admin_field_woocompare_attributes', array( $this, 'admin_fields_attributes' ), 10, 1 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_yith_woocompare_fields_attrs', array( $this, 'admin_update_custom_option' ), 10, 3 );

			// YITH WCWL Loaded.
			/**
			 * DO_ACTION: yith_woocompare_loaded
			 *
			 * Allows to trigger some action when the plugin is loaded.
			 */
			do_action( 'yith_woocompare_loaded' );
		}

		/**
		 * Action Links: add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @param array $links Links plugin array.
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true, YITH_WOOCOMPARE_SLUG );
			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @use     /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general' => __( 'Settings', 'yith-woocommerce-compare' ),
			);

			if ( ! ( defined( 'YITH_WOOCOMPARE_PREMIUM' ) && YITH_WOOCOMPARE_PREMIUM ) ) {
				$admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-compare' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Compare',
				'menu_title'       => 'Compare',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				/**
				 * APPLY_FILTERS: yith_woocompare_admin_tabs
				 *
				 * Filter the available tabs in the plugin panel.
				 *
				 * @param array $admin_tabs Admin tabs.
				 */
				'admin-tabs'       => apply_filters( 'yith_woocompare_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_WOOCOMPARE_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
                'plugin_slug'      => YITH_WOOCOMPARE_SLUG,
                'is_premium'       => defined( YITH_WOOCOMPARE_PREMIUM ),

            );

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WOOCOMPARE_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel   = new YIT_Plugin_Panel_WooCommerce( $args );
			$this->options = $this->panel->get_main_array_options();
		}

		/**
		 * Set default custom options
		 *
		 * @since 1.0.0
		 */
		public function default_options() {

			foreach ( $this->options as $section ) {

				foreach ( $section as $value ) {

					if ( isset( $value['std'] ) && isset( $value['id'] ) ) {

						if ( 'image_width' === $value['type'] ) {
							add_option( $value['id'], $value['std'] );
						} elseif ( 'woocompare_attributes' === $value['type'] ) {

							$value_id = str_replace( '_attrs', '', $value['id'] );

							$in_db          = get_option( $value_id );
							$in_db_original = get_option( $value['id'] );

							// If options is already in db and not reset defaults continue.
							if ( $in_db && 'all' !== $in_db_original ) {
								continue;
							}

							if ( 'all' === $value['default'] ) {
								$fields = YITH_Woocompare_Helper::standard_fields();
								$all    = array();

								foreach ( array_keys( $fields ) as $field ) {
									$all[ $field ] = true;
								}

								update_option( $value_id, $all );
							} else {
								update_option( $value_id, $value['std'] );
							}
						}
					}
				}
			}
		}

		/**
		 * Premium Tab Template
		 * Load the premium tab template on admin page
		 *
		 * @since    1.0
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WOOCOMPARE_TEMPLATE_PATH . '/admin/' . $this->premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}

		}

		/**
		 * Add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @param array    $new_row_meta_args An array of plugin row meta.
		 * @param string[] $plugin_meta An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
		 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param array    $plugin_data An array of plugin data.
		 * @param string   $status Status of the plugin. Defaults are 'All', 'Active',
		 *                                    'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                                    'Drop-ins', 'Search', 'Paused'.
		 * @return   array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {

			if ( defined( 'YITH_WOOCOMPARE_INIT' ) && YITH_WOOCOMPARE_INIT === $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WOOCOMPARE_SLUG;

				if ( defined( 'YITH_WOOCOMPARE_PREMIUM' ) ) {
					$new_row_meta_args['is_premium'] = true;
				}
			}

			return $new_row_meta_args;
		}

		/**
		 * Register Pointer
		 *
		 * @since 1.0.0
		 * @deprecated
		 */
		public function register_pointer() {
			return false;
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing;
		}

		/**
		 * Create new Woocommerce admin field: checkboxes
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $value The field value.
		 * @return void
		 */
		public function admin_fields_attributes( $value ) {
			$fields  = YITH_Woocompare_Helper::standard_fields();
			$all     = array();
			$checked = get_option( str_replace( '_attrs', '', $value['id'] ), 'all' === $value['default'] ? $all : array() );

			foreach ( array_keys( $fields ) as $field ) {
				$all[ $field ] = true;
			}
			// Then add fields that are not still saved.
			foreach ( $checked as $k => $v ) {
				unset( $all[ $k ] );
			}

			/**
			 * APPLY_FILTERS: yith_woocompare_admin_fields_attributes
			 *
			 * Filters the fields attributes to show in the comparison table.
			 *
			 * @param array $attributes Field attributes.
			 * @param array $fields     Fields to show.
			 * @param array $checked    Checked attributes to show.
			 *
			 * @return array
			 */
			$checkboxes = apply_filters( 'yith_woocompare_admin_fields_attributes', array_merge( $checked, $all ), $fields, $checked );

			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
				</th>

				<td class="forminp attributes">
					<p class="description"><?php echo wp_kses_post( $value['desc'] ); ?></p>
					<ul class="fields">
						<?php
						foreach ( $checkboxes as $slug => $checked ) :
							if ( ! isset( $fields[ $slug ] ) ) {
								continue;
							}
							?>
							<li>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( $value['id'] ); ?>[]" id="<?php echo esc_attr( $value['id'] ); ?>_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $slug ); ?>"<?php checked( $checked ); ?> /> <?php echo esc_attr( $fields[ $slug ] ); ?>
								</label>
							</li>
							<?php
						endforeach;
						?>
					</ul>
					<input type="hidden" name="<?php echo esc_attr( $value['id'] ); ?>_positions" value="<?php echo implode( ',', array_keys( $checkboxes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
				</td>
			</tr>
			<?php
		}

		/**
		 * Create new Woocommerce admin field: yit_wc_image_width
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $value Field value.
		 * @return void
		 */
		public function admin_fields_woocompare_image_width( $value ) {

			$width  = WC_Admin_Settings::get_option( $value['id'] . '[width]', $value['default']['width'] );
			$height = WC_Admin_Settings::get_option( $value['id'] . '[height]', $value['default']['height'] );
			$crop   = WC_Admin_Settings::get_option( $value['id'] . '[crop]' );
			$crop   = ( 'on' === $crop || '1' === $crop ) ? 1 : 0;
			$crop   = checked( 1, $crop, false );

			?>
			<tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
			<td class="forminp image_width_settings">

				<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo esc_attr( $width ); ?>" /> &times;
				<input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo esc_attr( $height ); ?>" /> px

				<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> /> <?php esc_html_e( 'Do you want to hard crop the image?', 'yith-woocommerce-compare' ); ?>
				</label>
				<p class="description"><?php echo wp_kses_post( $value['desc'] ); ?></p>

			</td>
			</tr>
			<?php

		}

		/**
		 * Save the admin field: slider
		 *
		 * @access public
		 * @since 1.0.0
		 * @param mixed $value The option value.
		 * @param mixed $option The options array.
		 * @param mixed $raw_value The option raw value.
		 * @return mixed
		 */
		public function admin_update_custom_option( $value, $option, $raw_value ) {

			$val            = array();
			$checked_fields = isset( $_POST[ $option['id'] ] ) ? maybe_unserialize( wp_unslash( $_POST[ $option['id'] ] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$fields         = isset( $_POST[ $option['id'] . '_positions' ] ) ? array_map( 'wc_clean', explode( ',', wp_unslash( $_POST[ $option['id'] . '_positions' ] ) ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			foreach ( $fields as $field ) {
				$val[ $field ] = in_array( $field, $checked_fields, true );
			}

			update_option( str_replace( '_attrs', '', $option['id'] ), $val );

			return $value;
		}

		/**
		 * Enqueue admin styles and scripts
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function enqueue_styles_scripts() {

			$min = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';

			if ( isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === $this->panel_page ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-slider' );
				wp_enqueue_script( 'jquery-ui-sortable' );

				wp_enqueue_style( 'yith_woocompare_admin', YITH_WOOCOMPARE_URL . 'assets/css/admin.css', array(), YITH_WOOCOMPARE_VERSION );
				wp_enqueue_script( 'yith_woocompare', YITH_WOOCOMPARE_URL . 'assets/js/woocompare-admin' . $min . '.js', array( 'jquery', 'jquery-ui-sortable' ), YITH_WOOCOMPARE_VERSION, true );
			}

			/**
			 * DO_ACTION: yith_woocompare_enqueue_styles_scripts
			 *
			 * Allows to trigger some action when the styles and scripts are enqueued.
			 */
			do_action( 'yith_woocompare_enqueue_styles_scripts' );
		}
	}
}
