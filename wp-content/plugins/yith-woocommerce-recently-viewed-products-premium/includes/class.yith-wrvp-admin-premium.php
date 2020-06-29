<?php
/**
 * Admin Premium class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WRVP_Admin_Premium extends YITH_WRVP_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WRVP_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * An array of shortcodes data
		 *
		 * @var array
		 * @since 1.5.0
		 */
		private $_shortcodes_data;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WRVP_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WRVP_Admin_Premium
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// add image size for thumbs in mail
			add_action( 'woocommerce_admin_field_ywrvp_image_size', array( $this, 'custom_image_size' ), 10, 1 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// add tabs to plugin panel
			add_filter( 'yith_wrvp_admin_tabs', array( $this, 'add_tabs' ), 10, 1 );

			add_action( 'yith_wrvp_shortcode_tab', array( $this, 'shortcode_tab' ) );
			add_action( 'yith_wrvp_email_settings', array( $this, 'email_settings' ) );

			// Custom tinymce button
			add_action('admin_head', array( $this, 'tc_button' ) );

			// search product category in ajax
			add_action( 'wp_ajax_yith_wrvp_search_product_cat', array( $this, 'search_product_cat_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_wrvp_search_product_cat', array( $this, 'search_product_cat_ajax' ) );

			add_action( 'woocommerce_admin_field_ywrvp_custom_checklist', array( $this, 'custom_checklist_output' ), 10, 1 );

			// register Gutenberg block
            add_action( 'init', array( $this, 'register_gutenberg_block' ), 10 );

            // delete plugin transient on term add/update
            add_action( 'created_term', array( $this, 'delete_transient' ), 10 );
            add_action( 'edit_term', array( $this, 'delete_transient' ), 10 );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WRVP_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_WRVP_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}

			YIT_Plugin_Licence()->register( YITH_WRVP_INIT, YITH_WRVP_SECRET_KEY, YITH_WRVP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			if( ! class_exists( 'YIT_Plugin_Licence' ) ){
				require_once( YITH_WRVP_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WRVP_SLUG, YITH_WRVP_INIT );
		}

		/**
		 * Enqueue scripts for admin panel
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {
			$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_script( 'yith-wrvp-admin', YITH_WRVP_ASSETS_URL . '/js/yith-wrvp-admin'.$min.'.js', array( 'jquery' ), false, true );
			wp_register_style( 'yith-wrvp-admin', YITH_WRVP_ASSETS_URL . '/css/yith-wrvp-admin.css' );

			if( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wrvp_panel' ) {
				wp_enqueue_script( 'yith-wrvp-admin' );
				wp_enqueue_style( 'yith-wrvp-admin' );
			}
		}

		/**
		 * Add tabs to plugin setting panel
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $tabs
		 * @return array
		 * @author Francesco Licandro
		 */
		public function add_tabs( $tabs ) {

			$tabs['shortcode'] = __( 'Create Shortcode', 'yith-woocommerce-recently-viewed-products' );
			$tabs['email'] = __( 'Email Settings', 'yith-woocommerce-recently-viewed-products' );

			return $tabs;
		}

		/**
		 * Load the shortcode tab template on admin page
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function shortcode_tab() {
			$shortcode_tab_template = YITH_WRVP_TEMPLATE_PATH . '/admin/shortcode-tab.php';
			if( file_exists( $shortcode_tab_template ) ) {
				include_once($shortcode_tab_template);
			}
		}

		/**
		 * Duplicate email options in plugin settings
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function email_settings() {

			if( file_exists( YITH_WRVP_DIR . '/templates/admin/email-tab.php' ) ) {
				global $current_section;
				$current_section = 'yith_wrvp_mail';

				$mailer = WC()->mailer();
				$class = $mailer->emails['YITH_WRVP_Mail'];

				WC_Admin_Settings::get_settings_pages();

				if( ! empty( $_POST ) ) {
					$class->process_admin_options();

					do_action( 'yith_wrvp_mail_after_save_option' );
				}

				include_once( YITH_WRVP_DIR . '/templates/admin/email-tab.php' );
			}
		}

		/**
		 * Add custom image size to standard WC types
		 *
		 * @since 1.0.0
		 * @access public
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function custom_image_size( $value ){

			$option_values = get_option( $value['id'] );
			$width  = isset( $option_values['width'] ) ? $option_values['width'] : $value['default']['width'];
			$height = isset( $option_values['height'] ) ? $option_values['height'] : $value['default']['height'];
			$crop   = isset( $option_values['crop'] ) ? $option_values['crop'] : $value['default']['crop'];

			?><tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
			<td class="forminp yith_image_size_settings"
				<?php if( isset( $value['custom_attributes'] ) ) {
					foreach( $value['custom_attributes'] as $key => $data ) {
						echo ' ' . esc_html( $key ) .'="' . esc_html( $data ) . '"';
					}
				} ?>
				>
				<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo esc_html( $width ); ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo esc_html( $height ); ?>" />px

				<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" value="1" <?php checked( 1, $crop ); ?> /> <?php esc_html_e( 'Do you want to hard crop the image?', 'yith-woocommerce-recently-viewed-products' ); ?></label>

				<div><span class="description"><?php echo esc_html( $value['desc'] ); ?></span></div>

			</td>
			</tr><?php

		}

		/**
		 * Add a new button to tinymce
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		public function tc_button() {
			global $typenow;

			if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
				return;
			}

			if ( !isset( $_GET['page'] ) || $_GET['page'] != $this->_panel_page ) {
				return;
			}

			if ( get_user_option( 'rich_editing' ) == 'true' ) {
				add_filter( "mce_external_plugins", array( $this, 'add_tinymce_plugin' ) );
				add_filter( "mce_buttons", array( $this, 'register_tc_button' ) );
				add_filter( 'mce_external_languages', array( $this, 'add_tc_button_lang' ) );
			}
		}

		/**
		 * Add plugin button to tinymce from filter mce_external_plugins
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function add_tinymce_plugin( $plugin_array ) {
			$min = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';
			$plugin_array['tc_button'] = YITH_WRVP_ASSETS_URL . '/js/tinymce/text-editor' . $min . '.js';
			return $plugin_array;
		}

		/**
		 * Register the custom button to tinymce from filter mce_buttons
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function register_tc_button( $buttons ) {
			array_push( $buttons, "tc_button" );
			return $buttons;
		}

		/**
		 * Add multilingual to mce button from filter mce_external_languages
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function add_tc_button_lang( $locales ) {
			$locales ['tc_button'] = YITH_WRVP_DIR . 'includes/tinymce/tinymce-plugin-langs.php';
			return $locales;
		}

		/**
		 * Ajax action search product
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function search_product_cat_ajax(){
			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'orderby'           => 'name',
				'order'             => 'ASC',
				'hide_empty'        => false,
				'exclude'           => array(),
				'exclude_tree'      => array(),
				'include'           => array(),
				'number'            => '',
				'fields'            => 'all',
				'slug'              => '',
				'parent'            => '',
				'hierarchical'      => true,
				'child_of'          => 0,
				'childless'         => false,
				'get'               => '',
				'name__like'        => $term,
				'pad_counts'        => false,
				'offset'            => '',
				'search'            => '',
			);

			$terms = get_terms( 'product_cat', $args);
			$found_products = array();

			if ( $terms ) {
				foreach ( $terms as $term ) {
					$found_products[ $term->term_id ] = rawurldecode( $term->name );
				}
			}

			wp_send_json( $found_products );
		}

		/**
		 * Print the custom checklist output for admin settings panel
		 *
		 * @access public
		 * @since 1.0.4
		 * @param array $value
		 * @author Francesco Licandro
		 */
		public function custom_checklist_output( $value ) {
			$option_value = get_option( $value['id'] );

			$template = YITH_WRVP_TEMPLATE_PATH . '/admin/custom-checklist.php';
			if( file_exists( $template ) ) {
				include_once($template);
			}
		}

		/**
         * Register a block for Gutenberg editor
         *
         * @since 1.4.5
         * @author Francesco Licandro
         * @return void
         */
		public function register_gutenberg_block(){

            $shortcodes = $this->get_shortcodes_data();
            if( empty( $shortcodes ) ) {
                return;
            }

            foreach( $shortcodes as $shortcode_name => $data ) {
                if( empty( $data['block_id'] ) ) {
                    continue;
                }
                $the_block = array(
                    $data['block_id'] => array(
                        'title'          => $data['title'],
                        'description'    => $data['description'],
                        'shortcode_name' => $shortcode_name,
                        'do_shortcode'   => isset( $data['do_shortcode'] ) ? $data['do_shortcode'] : false,
                        'attributes'     => $data['attributes']
                    )
                );

                yith_plugin_fw_gutenberg_add_blocks( $the_block );
            }
        }

        /**
         * Get shortcodes data
         *
         * @since 1.5.0
         * @author Francesco Licandro
         * @return array
         */
        public function get_shortcodes_data(){
            empty( $this->_shortcodes_data ) && $this->_shortcodes_data = include( YITH_WRVP_DIR . '/plugin-options/shortcodes-data.php' );
            return $this->_shortcodes_data;
        }

        /**
         * Delete transient
         *
         * @since 1.5.0
         * @author Francesco Licandro
         * @return void
         */
        public function delete_transient(){
            delete_transient( 'yith_wrvp_categories_list' );
        }
	}
}
/**
 * Unique access to instance of YITH_WRVP_Admin_Premium class
 *
 * @return \YITH_WRVP_Admin_Premium
 * @since 1.0.0
 */
function YITH_WRVP_Admin_Premium(){
	return YITH_WRVP_Admin_Premium::get_instance();
}