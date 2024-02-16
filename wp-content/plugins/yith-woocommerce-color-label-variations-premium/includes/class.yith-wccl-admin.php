<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

defined( 'YITH_WCCL' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCCL_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCCL_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCCL_Admin
		 */
		protected static $instance;

		/**
		 * Plugin option
		 *
		 * @since  1.0.0
		 * @var array
		 */
		public $option = array();

		/**
		 * Plugin custom taxonomy
		 *
		 * @since  1.0.0
		 * @var array
		 */
		public $custom_types = array();

		/**
		 * The admin panel instance
		 *
		 * @var object $panel
		 */
		protected $panel;

		/**
		 * @var string panel page
		 */
		protected $panel_page = 'yith_wccl_panel';

		/**
		 * Various links
		 *
		 * @since  1.0.0
		 * @var string
		 * @access public
		 */
		public $doc_url = 'http://yithemes.com/docs-plugins/yith-woocommerce-color-label-variations';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCCL_Admin
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			$this->custom_types = ywccl_get_custom_tax_types();

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCCL_DIR . '/' . basename( YITH_WCCL_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// Enqueue style and scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'dequeue_scripts_styles' ), 20 );

			// Add description field to products attribute.
			add_action( 'admin_footer', array( $this, 'add_description_field' ) );
			add_action( 'woocommerce_attribute_added', array( $this, 'attribute_add_description_field' ), 10, 2 );
			add_action( 'woocommerce_attribute_updated', array( $this, 'attribute_update_description_field' ), 10, 3 );
			add_action( 'woocommerce_attribute_deleted', array( $this, 'attribute_delete_description_field' ), 10, 3 );

			// Product attribute taxonomies.
			add_action( 'init', array( $this, 'attribute_taxonomies' ) );

			// Print attribute field type.
			add_action( 'yith_wccl_print_attribute_field', array( $this, 'print_attribute_type' ), 10, 3 );

			// Choose variations in product page.
			add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );

			// Add term directly from product variation.
			add_action( 'admin_footer', array( $this, 'product_option_add_terms_form' ) );

			// Save new term.
			add_action( 'created_term', array( $this, 'attribute_save' ), 10, 3 );
			add_action( 'edit_term', array( $this, 'attribute_save' ), 10, 3 );

			// AJAX add attribute.
			add_action( 'wp_ajax_yith_wccl_add_new_attribute', array( $this, 'yith_wccl_add_new_attribute_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_wccl_add_new_attribute', array( $this, 'yith_wccl_add_new_attribute_ajax' ) );

			// Add gallery for variations.
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'gallery_variation_html' ), 10, 3 );
			add_action( 'admin_footer', array( $this, 'gallery_variation_template_js' ) );
			// Add option to show/hide variable in loop.
			add_filter( 'product_type_options', array( $this, 'show_variable_in_loop_opt' ), 10, 1 );
			// Add option to show/hide single variation in loop.
			add_action( 'woocommerce_variation_options', array( $this, 'show_variation_in_loop_opt' ), 10, 3 );
			// Save custom meta.
			add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_variable_custom_meta' ), 10, 1 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_custom_meta' ), 10, 2 );
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $links Links plugin array.
		 * @return   mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true, YITH_WCCL_SLUG );

			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'           => __( 'Settings', 'yith-woocommerce-color-label-variations' ),
				'single-variations' => __( 'Single Variations', 'yith-woocommerce-color-label-variations' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'YITH WooCommerce Color and Label Variations', 'yith-woocommerce-color-label-variations' ),
				'menu_title'       => __( 'Color and Label Variations', 'yith-woocommerce-color-label-variations' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => apply_filters( 'yith-wccl-admin-tabs', $admin_tabs ),
				'options-path'     => YITH_WCCL_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			// Fixed: not updated theme.
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCCL_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * plugin_row_meta
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 * @param $plugin_data
		 * @param $status
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @return   Array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {

			if ( defined( 'YITH_WCCL_INIT' ) && YITH_WCCL_INIT === $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCCL_SLUG;

				$new_row_meta_args['live_demo'] = array(
					'url' => 'https://plugins.yithemes.com/yith-woocommerce-color-and-label-variations/',
				);

				if ( defined( 'YITH_WCCL_PREMIUM' ) ) {
					$new_row_meta_args['is_premium'] = true;
				}
			}

			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCCL_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCCL_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCCL_INIT, YITH_WCCL_SECRET_KEY, YITH_WCCL_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCCL_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WCCL_SLUG, YITH_WCCL_INIT );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function enqueue_scripts() {
			global $pagenow;

			if ( ( ( 'edit-tags.php' === $pagenow || 'edit.php' === $pagenow || 'term.php' === $pagenow ) && isset( $_GET['post_type'] ) && 'product' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) )
				 || ( 'post.php' === $pagenow && isset( $_GET['action'] ) && 'edit' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) )
				 || ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) )
				 || ( isset( $_GET['tab'] ) && 'single-variations' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) )
				 || apply_filters( 'yith_wccl_enqueue_admin_scripts', false ) ) {

				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_media();

				wp_enqueue_style( 'yith-wccl-admin', YITH_WCCL_URL . '/assets/css/yith-wccl-admin.css', array( 'wp-color-picker' ), YITH_WCCL_VERSION );
				wp_enqueue_script(
					'yith-wccl-admin',
					YITH_WCCL_URL . '/assets/js/yith-wccl-admin' . $min . '.js',
					array(
						'jquery',
						'wp-color-picker',
						'jquery-ui-dialog',
					),
					YITH_WCCL_VERSION,
					true
				);

				wp_localize_script(
					'yith-wccl-admin',
					'yith_wccl_admin',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}

		/**
		 * Dequeue wp-color-picker-alpha from FUSION Avada theme to fix issue with plugin colorpicker
		 *
		 * @since 1.10.3
		 * @return void
		 */
		public function dequeue_scripts_styles() {
			global $pagenow;

			if ( ( ( 'edit-tags.php' === $pagenow || 'edit.php' === $pagenow || 'term.php' === $pagenow ) && isset( $_GET['post_type'] ) && 'product' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) )
				 || ( 'post.php' === $pagenow && isset( $_GET['action'] ) && 'edit' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) )
				 || ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) )
				 || ( isset( $_GET['tab'] ) && 'single-variations' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) ) {

				if ( defined( 'FUSION_LIBRARY_URL' ) ) {
					wp_dequeue_script( 'wp-color-picker-alpha' );
				}
			}
		}

		/**
		 * Add description field to add/edit products attribute
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_description_field() {
			global $pagenow, $wpdb;

			if ( ! ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) )
					 && isset( $_GET['page'] ) && 'product_attributes' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) ) {
				return;
			}

			$edit            = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : false;
			$att_description = false;

			if ( $edit ) {
				$attribute_to_edit = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}yith_wccl_meta WHERE wc_attribute_tax_id = '$edit'" );
				$att_description   = isset( $attribute_to_edit ) ? $attribute_to_edit : false;
			}

			ob_start();
			include YITH_WCCL_DIR . 'templates/admin/description-field.php';
			$html = ob_get_clean();

			wp_localize_script( 'yith-wccl-admin', 'yith_wccl_admin', array( 'html' => $html ) );
		}

		/**
		 * Maybe sanitize a field
		 *
		 * @since  1.8.4
		 * @author Francesco Licandro
		 * @param string $field
		 * @param mixed  $value
		 * @return string
		 */
		protected function maybe_sanitize_field( $field, $value ) {
			if ( ! apply_filters( 'yith_wccl_sanitize_field_' . $field, '__return_true' ) ) {
				return $value;
			}

			return wc_clean( $value );
		}

		/**
		 * Add new product attribute description
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed   $attribute
		 * @param integer $id
		 */
		public function attribute_add_description_field( $id, $attribute ) {
			global $wpdb;

			// Get attribute description.
			$descr = isset( $_POST['attribute_description'] ) ? $this->maybe_sanitize_field( 'attribute_description', $_POST['attribute_description'] ) : '';

			// Insert db value.
			if ( $descr ) {
				$attr = array();

				$attr['wc_attribute_tax_id'] = $id;
				// Add description.
				$attr['meta_key']   = '_wccl_attribute_description';
				$attr['meta_value'] = $descr;

				$wpdb->insert( $wpdb->prefix . 'yith_wccl_meta', $attr );
			}
		}

		/**
		 * Update product attribute description
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed   $attribute
		 * @param mixed   $old_attributes
		 * @param integer $id
		 */
		public function attribute_update_description_field( $id, $attribute, $old_attributes ) {
			global $wpdb;

			$descr = isset( $_POST['attribute_description'] ) ? $this->maybe_sanitize_field( 'attribute_description', $_POST['attribute_description'] ) : '';

			// Get meta value.
			$meta = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}yith_wccl_meta WHERE wc_attribute_tax_id = %d", $id ) );

			if ( ! isset( $meta ) ) {
				$this->attribute_add_description_field( $id, $attribute );
			} elseif ( $meta->meta_value !== $descr ) {
				$attr               = array();
				$attr['meta_value'] = $descr;

				$wpdb->update( $wpdb->prefix . 'yith_wccl_meta', $attr, array( 'meta_id' => $meta->meta_id ) );
			}
		}

		/**
		 * Delete product attribute description
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $attribute_name
		 * @param $taxonomy
		 * @param $attribute_id
		 */
		public function attribute_delete_description_field( $attribute_id, $attribute_name, $taxonomy ) {
			global $wpdb;

			$meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->prefix}yith_wccl_meta WHERE wc_attribute_tax_id = %d", $attribute_id ) );

			if ( $meta_id ) {
				$wpdb->query( "DELETE FROM {$wpdb->prefix}yith_wccl_meta WHERE wc_attribute_tax_id = $attribute_id" );
			}
		}

		/**
		 * Init product attribute taxonomies
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function attribute_taxonomies() {

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			if ( $attribute_taxonomies ) {
				foreach ( $attribute_taxonomies as $tax ) {

					// Check if tax is custom.
					if ( ! array_key_exists( $tax->attribute_type, $this->custom_types ) ) {
						continue;
					}

					$name = wc_attribute_taxonomy_name( $tax->attribute_name );
					add_action( $name . '_add_form_fields', array( $this, 'add_attribute_field' ) );
					add_action( $name . '_edit_form_fields', array( $this, 'edit_attribute_field' ), 10, 2 );

					add_filter( 'manage_edit-' . $name . '_columns', array( $this, 'product_attribute_columns' ) );
					add_filter( 'manage_' . $name . '_custom_column', array( $this, 'product_attribute_column' ), 10, 3 );
				}
			}
		}

		/**
		 * Add field for each product attribute taxonomy
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_attribute_field( $taxonomy ) {
			global $wpdb;

			$attribute = substr( $taxonomy, 3 );
			$attribute = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" );

			$values = array(
				'value'   => array(
					'value' => false,
					'label' => $this->custom_types[ $attribute->attribute_type ],
					'desc'  => '',
				),
				'tooltip' => array(
					'value' => false,
					'label' => __( 'Tooltip', 'yith-woocommerce-color-label-variations' ),
					'desc'  => __( 'Use this placeholder {show_image} to show the image on tooltip. Only available for image type', 'yith-woocommerce-color-label-variations' ),
				),
			);

			do_action( 'yith_wccl_print_attribute_field', $attribute->attribute_type, $values );
		}

		/**
		 * Edit field for each product attribute taxonomy
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function edit_attribute_field( $term, $taxonomy ) {
			global $wpdb;

			$attribute = substr( $taxonomy, 3 );
			$attribute = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" );

			$values = array(
				'value'   => array(
					'value' => ywccl_get_term_meta( $term->term_id, '_yith_wccl_value', true, $taxonomy ),
					'label' => $this->custom_types[ $attribute->attribute_type ],
					'desc'  => '',
				),
				'tooltip' => array(
					'value' => ywccl_get_term_meta( $term->term_id, '_yith_wccl_tooltip', true, $taxonomy ),
					'label' => __( 'Tooltip', 'yith-woocommerce-color-label-variations' ),
					'desc'  => __( 'Use this placeholder {show_image} to show the image on tooltip. Only available for image type', 'yith-woocommerce-color-label-variations' ),
				),
			);

			do_action( 'yith_wccl_print_attribute_field', $attribute->attribute_type, $values, true );
		}


		/**
		 * Print Attribute Tax Type HTML
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed $args
		 * @param bool  $table
		 * @param       $type
		 */
		public function print_attribute_type( $type, $args, $table = false ) {

			foreach ( $args as $key => $arg ) :

				$data    = 'value' === $key ? 'data-type="' . $type . '"' : '';
				$id      = $name = "term_{$key}";
				$values  = explode( ',', $arg['value'] );
				$value   = $values[0];
				$value_2 = '';
				if ( 'value' === $key && 'colorpicker' === $type ) {
					// Change name.
					$name                          .= '[]';
					isset( $values[1] ) && $value_2 = $values[1];
				}

				if ( $table ) : ?>
					<tr class="form-field">
					<th scope="row" valign="top">
						<label for="term_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $arg['label'] ); ?></label>
					</th>
					<td>
				<?php else : ?>
					<div class="form-field">
					<label for="term_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $arg['label'] ); ?></label>
				<?php endif ?>

				<input type="text" class="ywccl" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>"
						value="<?php echo esc_attr( $value ); ?>" <?php echo $data; ?>/>
				<?php if ( 'value' === $key && 'colorpicker' === $type ) : ?>
				<span class="ywccl_add_color_icon"
						data-content="<?php echo $value_2 ? '+' : '-'; ?>"><?php echo $value_2 ? '-' : '+'; ?></span>
				<br>
				<input type="text" class="ywccl hidden_empty" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>_2"
						value="<?php echo esc_attr( $value_2 ); ?>" <?php echo $data; ?>/>
			<?php endif; ?>

				<p><?php echo $arg['desc']; ?></p>

				<?php if ( $table ) : ?>
				</td>
				</tr>
			<?php else : ?>
				</div>
				<?php
			endif;
			endforeach;
		}

		/**
		 * Save attribute field
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemec.com>
		 * @param $tt_id
		 * @param $taxonomy
		 * @param $term_id
		 */
		public function attribute_save( $term_id, $tt_id, $taxonomy ) {
			if ( isset( $_POST['term_value'] ) ) {

				if ( is_array( $_POST['term_value'] ) ) {
					// First remove empty values.
					$array_values = array_filter( $_POST['term_value'] );
					if ( empty( $array_values ) ) {
						$value = '';
					} else {
						$value = implode( ',', $array_values );
					}
				} else {
					$value = $_POST['term_value'];
				}

				ywccl_update_term_meta( $term_id, '_yith_wccl_value', $value );
			}
			if ( isset( $_POST['term_tooltip'] ) ) {
				ywccl_update_term_meta( $term_id, '_yith_wccl_tooltip', sanitize_text_field( wp_unslash( $_POST['term_tooltip'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}

		/**
		 * Create new column for product attributes
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $columns
		 * @return mixed
		 */
		public function product_attribute_columns( $columns ) {

			if ( empty( $columns ) ) {
				return $columns;
			}

			$temp_cols                    = array();
			$temp_cols['cb']              = $columns['cb'];
			$temp_cols['yith_wccl_value'] = esc_html__( 'Value', 'yith-woocommerce-color-label-variations' );

			unset( $columns['cb'] );
			$columns = array_merge( $temp_cols, $columns );

			return $columns;
		}

		/**
		 * Print the column content
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $column
		 * @param $id
		 * @param $columns
		 * @return mixed
		 */
		public function product_attribute_column( $columns, $column, $id ) {
			global $taxonomy, $wpdb;

			if ( 'yith_wccl_value' === $column ) {

				$attribute = substr( $taxonomy, 3 );
				$attribute = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" );
				$att_type  = $attribute->attribute_type;

				$value    = ywccl_get_term_meta( $id, '_yith_wccl_value', true, $taxonomy );
				$columns .= $this->print_attribute_column( $value, $att_type );
			}

			return $columns;
		}


		/**
		 * Print the column content according to attribute type
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $type
		 * @param string $value
		 * @return string
		 */
		protected function print_attribute_column( $value, $type ) {
			$output = '';

			if ( 'colorpicker' === $type ) {

				$values = explode( ',', $value );
				if ( isset( $values[1] ) && $values[1] ) {
					$style  = "border-bottom-color:{$values[0]};border-left-color:{$values[1]}";
					$output = '<span class="yith-wccl-color"><span class="yith-wccl-bicolor" style="' . $style . '"></span></span>';
				} else {
					$output = '<span class="yith-wccl-color" style="background-color:' . $values[0] . '"></span>';
				}
			} elseif ( 'label' === $type ) {
				$output = '<span class="yith-wccl-label">' . esc_attr( $value ) . '</span>';
			} elseif ( 'image' === $type ) {
				$output = '<img class="yith-wccl-image" src="' . esc_url( $value ) . '" alt="" />';
			}

			return $output;
		}

		/**
		 * Print select for product variations
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemec.com>
		 * @param $i
		 * @param $taxonomy
		 */
		public function product_option_terms( $taxonomy, $i ) {

			if ( ! array_key_exists( $taxonomy->attribute_type, $this->custom_types ) ) {
				return;
			}

			global $thepostid;
			if ( is_null( $thepostid ) && ! empty( $_REQUEST['post_id'] ) ) {
				$thepostid = absint( $_REQUEST['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			$attribute_taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
			?>

			<select multiple="multiple" data-placeholder="<?php esc_html_e( 'Select terms', 'woocommerce' ); ?>"
					class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo intval( $i ); ?>][]">
				<?php
				$all_terms = $this->get_terms( $attribute_taxonomy_name );
				if ( $all_terms ) {
					foreach ( $all_terms as $term ) {
						echo '<option value="' . esc_attr( $term['value'] ) . '" ' . selected( has_term( absint( $term['id'] ), $attribute_taxonomy_name, $thepostid ), true, false ) . '>' . esc_html( $term['name'] ) . '</option>';
					}
				}
				?>
			</select>
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'yith-woocommerce-color-label-variations' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'yith-woocommerce-color-label-variations' ); ?></button>
			<button class="button fr plus yith_wccl_add_new_attribute" data-type_input="<?php echo esc_attr( $taxonomy->attribute_type ); ?>"><?php esc_html_e( 'Add new', 'yith-woocommerce-color-label-variations' ); ?></button>

			<?php
		}

		/**
		 * Get terms attributes array
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param string $tax_name
		 * @return array
		 */
		protected function get_terms( $tax_name ) {

			$args = array(
				'taxonomy'   => $tax_name,
				'orderby'    => 'name',
				'hide_empty' => '0',
			);
			// Get terms.
			$terms = get_terms( $args );

			$all_terms = array();
			foreach ( $terms as $term ) {
				$all_terms[] = array(
					'id'    => $term->term_id,
					'value' => $term->term_id,
					'name'  => $term->name,
				);
			}

			return $all_terms;
		}

		/**
		 * Add form in footer to add new attribute from edit product page
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function product_option_add_terms_form() {

			global $pagenow, $post;

			if ( apply_filters( 'yith_wccl_add_product_add_terms_form', ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) || ( isset( $post ) && 'product' !== get_post_type( $post->ID ) ) ) ) {
				return;
			}

			ob_start();

			?>

			<div id="yith_wccl_dialog_form"
					title="<?php esc_html_e( 'Create new attribute term', 'yith-woocommerce-color-label-variations' ); ?>"
					style="display:none;">
				<span class="dialog_error"></span>
				<form>
					<fieldset>
						<label for="term_name"><?php esc_html_e( 'Name', 'yith-woocommerce-color-label-variations' ); ?>:
							<input type="text" name="term_name" id="term_name" value="">
						</label>
						<label for="term_slug"><?php esc_html_e( 'Slug', 'yith-woocommerce-color-label-variations' ); ?>:
							<input type="text" name="term_slug" id="term_slug" value="">
						</label>
						<div class="label-input">
							<?php esc_html_e( 'Value', 'yith-woocommerce-color-label-variations' ); ?>:
							<input type="text" class="ywccl" name="term_value[]" id="term_value" value="" data-type="label">
							<span class="ywccl_add_color_icon" data-content="-">+</span><br>
							<input type="text" class="ywccl hidden_empty" name="term_value[]" id="term_value_2" value="" data-type="label">
						</div>
						<label for="term_tooltip"><?php esc_html_e( 'Tooltip', 'yith-woocommerce-color-label-variations' ); ?>:
							<input type="text" name="term_tooltip" id="term_tooltip" value="">
						</label>
					</fieldset>
				</form>
			</div>

			<?php

			echo ob_get_clean(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Ajax action to add new attribute terms
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_wccl_add_new_attribute_ajax() {

			if ( ! isset( $_POST['taxonomy'] ) || ! isset( $_POST['term_name'] ) || ! isset( $_POST['term_value'] ) ) {
				die();
			}

			$tax     = esc_attr( $_POST['taxonomy'] );
			$term    = wc_clean( $_POST['term_name'] );
			$slug    = wc_clean( $_POST['term_slug'] );
			$value   = wc_clean( implode( ',', array_filter( $_POST['term_value'] ) ) );
			$tooltip = wc_clean( $_POST['term_tooltip'] );
			$args    = array();

			if ( empty( $value ) ) {
				wp_send_json(
					array(
						'error' => __( 'A value is required for this term', 'yith-woocommerce-color-label-variations' ),
					)
				);
			}

			if ( taxonomy_exists( $tax ) ) {

				if ( $slug ) {
					$args['slug'] = $slug;
				}

				$result = wp_insert_term( $term, $tax, $args );
				if ( is_wp_error( $result ) ) {
					wp_send_json(
						array(
							'error' => $result->get_error_message(),
						)
					);
				} else {
					$term = get_term_by( 'id', $result['term_id'], $tax );
					ywccl_update_term_meta( $term->term_id, '_yith_wccl_value', $value );
					if ( $tooltip ) {
						ywccl_update_term_meta( $term->term_id, '_yith_wccl_tooltip', $tooltip );
					}

					wp_send_json(
						array(
							'id'    => $term->term_id,
							'value' => $term->term_id,
							'name'  => $term->name,
						)
					);
				}
			}

			die();
		}

		/**
		 * Variation gallery template
		 *
		 * @since  1.8.0
		 * @author Francesco Licandro
		 * @param int      $loop
		 * @param array    $variation_data
		 * @param \WP_Post $variation
		 */
		public function gallery_variation_html( $loop, $variation_data, $variation ) {
			$gallery = yith_wccl_get_variation_gallery( $variation );
			if ( ! is_array( $gallery ) ) {
				$gallery = array();
			}

			include YITH_WCCL_DIR . 'templates/admin/variation-gallery.php';
		}

		/**
		 * Variation gallery single image template js
		 *
		 * @since  1.8.0
		 * @author Francesco Licandro
		 */
		public function gallery_variation_template_js() {
			?>
			<script type="text/html" id="tmpl-yith-wccl-variation-gallery-image">
				<li class="image" data-value="{{data.id}}">
					<a href="#" class="remove"
							title="<?php echo esc_html_x( 'Remove image', 'label for remove single image from variation gallery', 'yith-woocommerce-color-label-variations' ); ?>"></a>
					<img src="{{data.url}}">
				</li>
			</script>
			<?php
		}

		/**
		 * Show option to enable/disable single variation in loop
		 *
		 * @since  1.9.4
		 * @author Francesco licandro
		 * @param int     $loop
		 * @param array   $variation_data
		 * @param WP_Post $variation
		 * @return void
		 */
		public function show_variation_in_loop_opt( $loop, $variation_data, $variation ) {

			if ( 'yes' !== get_option( 'yith-wccl-show-single-variations-loop', 'no' ) ) {
				return;
			}

			$value = ! isset( $variation_data['_yith_wccl_in_loop'] )

			?>
			<label class="tips"
					data-tip="<?php esc_attr_e( 'Enable this option to show this variation in archive pages', 'yith-woocommerce-color-label-variations' ); ?>">
				<?php esc_html_e( 'Show in archive pages?', 'yith-woocommerce-color-label-variations' ); ?>
				<input type="checkbox" class="checkbox"
						name="yith_wccl_variation_in_loop[<?php echo esc_attr( $loop ); ?>]" <?php checked( $value, true ); ?> />
			</label>
			<?php
		}

		/**
		 * Add option to enable/disable variable in loop
		 *
		 * @since  1.9.4
		 * @author Francesco Licandro
		 * @param array $opts
		 * @return array
		 */
		public function show_variable_in_loop_opt( $opts ) {
			if ( 'yes' !== get_option( 'yith-wccl-show-single-variations-loop', 'no' ) || 'yes' !== get_option( 'yith-wccl-hide-parent-products-loop', 'no' ) ) {
				return $opts;
			}

			$opts['yith_wccl_variable_in_loop'] = array(
				'id'            => '_yith_wccl_variable_in_loop',
				'wrapper_class' => 'show_if_variable',
				'label'         => __( 'Hide in archive pages?', 'yith-woocommerce-color-label-variations' ),
				'description'   => '',
				'default'       => 'yes',
			);

			return $opts;
		}

		/**
		 * Save variation custom meta
		 *
		 * @since  1.8.0
		 * @author Francesco Licandro
		 * @param integer|string $variation_id
		 * @param integer        $index
		 * @return void
		 */
		public function save_variation_custom_meta( $variation_id, $index ) {
			$gallery = isset( $_POST['yith_wccl_variation_gallery'][ $index ] ) ? $_POST['yith_wccl_variation_gallery'][ $index ] : '';
			$in_loop = isset( $_POST['yith_wccl_variation_in_loop'][ $index ] );
			// get variation
			$variation = wc_get_product( $variation_id );

			if ( $variation instanceof WC_Product ) {
				empty( $gallery ) ? $variation->delete_meta_data( '_yith_wccl_gallery' ) : $variation->update_meta_data( '_yith_wccl_gallery', array_map( 'intval', explode( ',', $gallery ) ) );
				$in_loop ? $variation->delete_meta_data( '_yith_wccl_in_loop' ) : $variation->update_meta_data( '_yith_wccl_in_loop', 'no' );
				$variation->save();
			}
		}

		/**
		 * Save variable custom meta
		 *
		 * @since  1.9.6
		 * @author Francesco Licandro
		 * @param integer $post_id
		 * @return void
		 */
		public function save_variable_custom_meta( $post_id ) {
			if ( isset( $_POST['_yith_wccl_variable_in_loop'] ) ) {
				delete_post_meta( $post_id, '_yith_wccl_variable_in_loop' );
			} else {
				update_post_meta( $post_id, '_yith_wccl_variable_in_loop', 'no' );
			}
		}
	}
}
/**
 * Unique access to instance of YITH_WCCL_Admin class
 *
 * @since 1.0.0
 * @return YITH_WCCL_Admin
 */
function YITH_WCCL_Admin() {
	return YITH_WCCL_Admin::get_instance();
}
