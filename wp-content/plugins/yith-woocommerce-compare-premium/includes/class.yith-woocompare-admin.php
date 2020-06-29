<?php
/**
 * Admin class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Compare
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

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
		 * @var array
		 * @access public
		 * @since 1.0.0
		 */
		public $options = array();

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOOCOMPARE_VERSION;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-compare';

		/**
		 * @var string Compare panel page
		 */
		protected $_panel_page = 'yith_woocompare_panel';

		/**
		 * Various links
		 *
		 * @var string
		 * @access public
		 * @since 1.0.0
		 */
		public $doc_url = 'http://yithemes.com/docs-plugins/yith-woocommerce-compare/';

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5) ;

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WOOCOMPARE_DIR . '/' . basename( YITH_WOOCOMPARE_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'yith_woocompare_premium', array( $this, 'premium_tab' ) );

			add_action( 'admin_init', array( $this, 'register_pointer' ) );
			add_action( 'admin_init', array( $this, 'default_options'), 99 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 20 );

			add_action( 'woocommerce_admin_field_woocompare_image_width', array( $this, 'admin_fields_woocompare_image_width' ) );
			add_action( 'woocommerce_admin_field_woocompare_attributes', array( $this, 'admin_fields_attributes' ), 10, 1 );
			if( version_compare( preg_replace( '/-beta-([0-9]+)/', '', WC()->version ), '2.4', '<' ) ) {
				add_action( 'woocommerce_update_option_woocompare_attributes', array( $this, 'admin_update_custom_option_pre_24' ), 10, 1 );
			}
			else {
				add_filter( 'woocommerce_admin_settings_sanitize_option_yith_woocompare_fields_attrs', array( $this, 'admin_update_custom_option' ), 10, 3 );
			}

			// YITH WCWL Loaded
			do_action( 'yith_woocompare_loaded' );
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

			$links[] = '<a href="' . admin_url( "admin.php?page={$this->_panel_page}" ) . '">' . __( 'Settings', 'yith-woocommerce-compare' ) . '</a>';
			if ( defined( 'YITH_WOOCOMPARE_PREMIUM' ) && YITH_WOOCOMPARE_PREMIUM ) {
				$links[] = '<a href="' . YIT_Plugin_Licence()->get_license_activation_url() . '" target="_blank">' . __( 'License', 'yith-woocommerce-compare' ) . '</a>';
			}

			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
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
				'page_title'       => _x( 'YITH WooCommerce Compare', 'Admin Plugin Name', 'yith-woocommerce-compare' ),
				'menu_title'       => _x( 'Compare', 'Admin Plugin Name', 'yith-woocommerce-compare' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => apply_filters( 'yith_woocompare_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_WOOCOMPARE_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class()
			);


			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WOOCOMPARE_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel  = new YIT_Plugin_Panel_WooCommerce( $args );
			$this->options = $this->_panel->get_main_array_options();
		}

		/**
		 * Set default custom options
		 *
		 */
		public function default_options() {
			$this->_default_options();
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WOOCOMPARE_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}

		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {

			if ( defined( 'YITH_WOOCOMPARE_INIT' ) && YITH_WOOCOMPARE_INIT == $plugin_file ) {
                $new_row_meta_args['slug']   = YITH_WOOCOMPARE_SLUG;

                if( defined( 'YITH_WOOCOMPARE_PREMIUM' ) ){
                    $new_row_meta_args['is_premium'] = true;
                }
			}

            return $new_row_meta_args;
		}

		/**
		 * Register Pointer
		 */
		public function register_pointer(){

			if( ! class_exists( 'YIT_Pointers' ) ){
				include_once( 'plugin-fw/lib/yit-pointers.php' );
			}

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_woocompare_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'YITH WooCommerce Compare Activated', 'yith-woocommerce-compare' ),
					apply_filters( 'yith_woocompare_activated_pointer_content', sprintf( __( 'In the YIT Plugin tab you can find the YITH WooCommerce Compare options. With this menu, you can access to all the settings of our plugins that you have activated. YITH WooCommerce Compare is available in an outstanding PREMIUM version with many new options, <a href="%s">discover it now</a>.', 'yith-woocommerce-compare' ), $this->get_premium_landing_uri() ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => YITH_WOOCOMPARE_INIT
			);

			$args[] = array(
				'screen_id'  => 'update',
				'pointer_id' => 'yith_woocompare_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'YITH WooCommerce Compare Updated', 'yith-woocommerce-compare' ),
					apply_filters( 'yith_woocompare_updated_pointer_content', sprintf( __( 'From now on, you can find all the options of YITH WooCommerce Compare under YIT Plugin -> Compare instead of WooCommerce -> Settings -> Compare, as in the previous version. When one of our plugins is updated, a new voice will be added to this menu. YITH WooCommerce Compare has been updated with new available options, <a href="%s">discover the PREMIUM version.</a>', 'yith-woocommerce-compare' ), $this->get_premium_landing_uri() ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => YITH_WOOCOMPARE_INIT
			);

			YIT_Pointers()->register( $args );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing.'?refer_id=1030585';
		}

		/**
		 * Default options
		 *
		 * Sets up the default options used on the settings page
		 *
		 * @access protected
		 * @return void
		 * @since 1.0.0
		 */
		protected function _default_options() {

			foreach ( $this->options as $section ) {

				foreach ( $section as $value ) {

					if ( isset( $value['std'] ) && isset( $value['id'] ) ) {

						if ( $value['type'] == 'image_width' ) {
							add_option( $value['id'], $value['std'] );
						} elseif ( $value['type'] == 'woocompare_attributes' ) {

							$value_id = str_replace( '_attrs', '', $value['id'] );

							$in_db          = get_option( $value_id );
							$in_db_original = get_option( $value['id'] );

							// if options is already in db and not reset defaults continue
							if ( $in_db && $in_db_original != 'all' ) {
								continue;
							}

							if ( $value['default'] == 'all' ) {
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
		 * Create new Woocommerce admin field: checkboxes
		 *
		 * @access public
		 * @param array $value
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_fields_attributes( $value ) {
			$fields = YITH_Woocompare_Helper::standard_fields();
			$all    = array();
			$checked = get_option( str_replace( '_attrs', '', $value['id'] ), $value['default'] == 'all' ? $all : array() );

			foreach ( array_keys( $fields ) as $field ) {
				$all[ $field ] = true;
			}
			// then add fields that are not still saved
			foreach ( $checked as $k => $v ) {
				unset( $all[ $k ] );
			}
			$checkboxes = array_merge( $checked, $all );


			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['name'] ); ?></label>
				</th>

				<td class="forminp attributes">
					<p class="description"><?php echo wp_kses_post( $value['desc'] ); ?></p>
					<ul class="fields">
						<?php foreach ( $checkboxes as $slug => $checked ) :
							if( ! isset( $fields[ $slug ] ) )
								continue;
							?>
							<li>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( $value['id'] ); ?>[]" id="<?php echo esc_attr( $value['id'] ); ?>_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $slug ); ?>"<?php checked( $checked ) ?> /> <?php echo esc_attr( $fields[ $slug ] ); ?>
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
		 * @param array $value
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_fields_woocompare_image_width( $value ) {

			$width  = WC_Admin_Settings::get_option( $value['id'] . '[width]', $value['default']['width'] );
			$height = WC_Admin_Settings::get_option( $value['id'] . '[height]', $value['default']['height'] );
			$crop   = WC_Admin_Settings::get_option( $value['id'] . '[crop]' );
			$crop   = ( $crop == 'on' || $crop == '1' ) ? 1 : 0;
			$crop   = checked( 1, $crop, false );

			?>
			<tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
			<td class="forminp image_width_settings">

				<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo esc_attr( $width ); ?>" /> &times;
				<input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo esc_attr( $height ) ; ?>" />px

				<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?> /> <?php esc_html_e( 'Do you want to hard crop the image?', 'yith-woocommerce-compare' ); ?>
				</label>
				<p class="description"><?php echo wp_kses_post( $value['desc'] ); ?></p>

			</td>
			</tr><?php

		}

		/**
		 * Save the admin field: slider
		 *
		 * @access public
		 * @param mixed $value
		 * @param mixed $option
		 * @param mixed $raw_value
		 * @return mixed
		 * @since 1.0.0
		 */
		public function admin_update_custom_option( $value, $option, $raw_value ) {

			$val            = array();
			$checked_fields = isset( $_POST[ $option['id'] ] ) ? maybe_unserialize( $_POST[ $option['id'] ] ) : array();
			$fields         = array_map( 'trim', explode( ',', $_POST[ $option['id'] . '_positions' ] ) );

			foreach ( $fields as $field ) {
				$val[ $field ] = in_array( $field, $checked_fields );
			}

			update_option( str_replace( '_attrs', '', $option['id'] ), $val );

			return $value;
		}

		/**
		 * Save the admin field: slider
		 *
		 * @access public
		 * @param mixed $value
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_update_custom_option_pre_24( $value ) {

			$val            = array();
			$checked_fields = isset( $_POST[ $value['id'] ] ) ? $_POST[ $value['id'] ] : array();
			$fields         = array_map( 'trim', explode( ',', $_POST[ $value['id'] . '_positions' ] ) );

			foreach ( $fields as $field ) {
				$val[ $field ] = in_array( $field, $checked_fields );
			}

			update_option( str_replace( '_attrs', '', $value['id'] ), $val );
		}

		/**
		 * Enqueue admin styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {

			$min = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_woocompare_panel' ) {
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-slider' );
				wp_enqueue_script( 'jquery-ui-sortable' );

				wp_enqueue_style( 'yith_woocompare_admin', YITH_WOOCOMPARE_URL . 'assets/css/admin.css' );
				wp_enqueue_script( 'yith_woocompare', YITH_WOOCOMPARE_URL . 'assets/js/woocompare-admin'.$min.'.js', array( 'jquery', 'jquery-ui-sortable' ) );
			}

			do_action( 'yith_woocompare_enqueue_styles_scripts' );
		}
	}
}
