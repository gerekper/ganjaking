<?php
/**
 * Color and Label module admin class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Color_Label_Variations_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 2.0.0
	 */
	class YITH_WAPO_Color_Label_Variations_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WAPO_Color_Label_Variations_Admin
		 */
		protected static $instance;

		/**
		 * Plugin option
		 *
		 * @since  1.0.0
		 * @var array
		 * @access public
		 */
		public $option = array();

		/**
		 * Plugin custom taxonomy
		 *
		 * @since  1.0.0
		 * @var array
		 * @access public
		 */
		public $custom_types = array();

		/**
		 * Panel
		 *
		 * @var $_panel Object
		 */
		protected $panel;

		/**
		 * Panel page
		 *
		 * @var string panel page
		 */
		protected $panel_page = 'yith_wapo_panel';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WAPO_Color_Label_Variations_Admin
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

			// Module option tab.
			add_filter( 'yith_wapo_panel_settings_options', array( $this, 'add_panel_settings_option' ), 10, 1 );

			$this->custom_types = YITH_WAPO_Color_Label_Variations::get_custom_attribute_types();
			// enqueue style and scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			// add description field to products attribute.
			add_action( 'admin_footer', array( $this, 'add_description_field' ) );
			add_action( 'woocommerce_attribute_added', array( $this, 'attribute_add_description_field' ), 10, 2 );
			add_action( 'woocommerce_attribute_updated', array( $this, 'attribute_update_description_field' ), 10, 3 );
			add_action( 'woocommerce_attribute_deleted', array( $this, 'attribute_delete_description_field' ), 10, 3 );
			// product attribute taxonomies.
			add_action( 'init', array( $this, 'attribute_taxonomies' ) );
			// print attribute field type.
			add_action( 'yith_wccl_print_attribute_field', array( $this, 'print_attribute_type' ), 10, 3 );
			// choose variations in product page.
			add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );
			// save new term.
			add_action( 'created_term', array( $this, 'attribute_save' ), 10, 3 );
			add_action( 'edit_term', array( $this, 'attribute_save' ), 10, 3 );
			// add gallery for variations.
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'gallery_variation_html' ), 10, 3 );
			add_action( 'admin_footer', array( $this, 'gallery_variation_template_js' ) );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_custom_meta' ), 10, 2 );
		}

		/**
		 * Filter settings tab to add the Color and Label sub-tab.
		 *
		 * @since 4.0.0
		 * @param string $settings The current settings of the tab.
		 */
		public function add_panel_settings_option( $settings ) {

            if ( ! function_exists( 'YITH_WCCL' ) ) {
                $settings['settings']['general-options']['sub-tabs']['settings-color-label-variations'] = array(
                    'title' => esc_html_x( 'Color and Label Variations', 'Admin title of tab', 'yith-woocommerce-product-add-ons' ),
                    'description' => esc_html_x( 'Set the options for the color and label variations you create.', 'Admin title of tab', 'yith-woocommerce-product-add-ons' ),
                );
            };

			return $settings;
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			global $pagenow;

			if (
				( ( 'edit-tags.php' === $pagenow || 'edit.php' === $pagenow || 'term.php' === $pagenow ) && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				|| ( 'post.php' === $pagenow && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				|| ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				|| ( isset( $_GET['tab'] ) && 'single-variations' === $_GET['tab'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {

				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_media();

				wp_enqueue_style( 'yith-wccl-admin', YITH_WAPO_WCCL_ASSETS_URL . 'css/admin' . $min . '.css', array( 'wp-color-picker' ), YITH_WAPO_VERSION );
				wp_enqueue_script( 'yith-wccl-admin', YITH_WAPO_WCCL_ASSETS_URL . 'js/admin' . $min . '.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-dialog' ), YITH_WAPO_VERSION, true );

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
		 * Add description field to add/edit products attribute
		 *
		 * @since  1.0.0
		 */
		public function add_description_field() {
			global $pagenow, $wpdb;

			if ( ! ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] && isset( $_GET['page'] ) && 'product_attributes' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$edit            = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$att_description = false;

			if ( $edit ) {
				$attribute_to_edit = $wpdb->get_var( 'SELECT meta_value FROM ' . $wpdb->prefix . "yith_wccl_meta WHERE wc_attribute_tax_id = '$edit'" ); // phpcs:ignore
				$att_description   = $attribute_to_edit ?? false;
			}

			ob_start();
			include YITH_WAPO_WCCL_DIR . 'templates/admin/description-field.php';
			$html = ob_get_clean();

			wp_localize_script( 'yith-wccl-admin', 'yith_wccl_admin', array( 'html' => $html ) );
		}

		/**
		 * Maybe sanitize a field
		 *
		 * @since  1.8.4
		 * @param string $field Field.
		 * @param mixed  $value Value.
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
		 * @param integer $id ID.
		 * @param mixed   $attribute Attribute.
		 */
		public function attribute_add_description_field( $id, $attribute ) {
			global $wpdb;

			// get attribute description.
			$descr = $_POST['attribute_description'] ?? ''; // phpcs:ignore

			// insert db value.
			if ( $descr ) {
				$attr = array();

				$attr['wc_attribute_tax_id'] = $id;
				// add description.
				$attr['meta_key']   = '_wccl_attribute_description'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$attr['meta_value'] = $descr; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value

				$wpdb->insert( $wpdb->prefix . 'yith_wccl_meta', $attr ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			}
		}

		/**
		 * Update product attribute description
		 *
		 * @since  1.0.0
		 * @param integer $id ID.
		 * @param mixed   $attribute Attribute.
		 * @param mixed   $old_attributes Old attributes.
		 */
		public function attribute_update_description_field( $id, $attribute, $old_attributes ) {
			global $wpdb;

			$descr = $_POST['attribute_description'] ?? ''; // phpcs:ignore

			// get meta value.
			$meta = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'yith_wccl_meta WHERE wc_attribute_tax_id = %d', $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( ! isset( $meta ) ) {
				$this->attribute_add_description_field( $id, $attribute );
			} elseif ( $meta->meta_value !== $descr ) {

				$attr = array();

				$attr['meta_value'] = $descr; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value

				$wpdb->update( $wpdb->prefix . 'yith_wccl_meta', $attr, array( 'meta_id' => $meta->meta_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			}
		}

		/**
		 * Delete product attribute description
		 *
		 * @since  1.0.0
		 * @param int    $attribute_id Attribute ID.
		 * @param string $attribute_name Attribute name.
		 * @param string $taxonomy Taxonomy.
		 */
		public function attribute_delete_description_field( $attribute_id, $attribute_name, $taxonomy ) {
			global $wpdb;

			$meta_id = $wpdb->get_var( $wpdb->prepare( 'SELECT meta_id FROM ' . $wpdb->prefix . 'yith_wccl_meta WHERE wc_attribute_tax_id = %d', $attribute_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( $meta_id ) {
				$wpdb->query( "DELETE FROM {$wpdb->prefix}yith_wccl_meta WHERE wc_attribute_tax_id = $attribute_id" );  // phpcs:ignore
			}
		}

		/**
		 * Init product attribute taxonomies
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function attribute_taxonomies() {

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			if ( $attribute_taxonomies ) {
				foreach ( $attribute_taxonomies as $tax ) {

					// check if tax is custom.
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
		 *
		 * @param string $taxonomy Taxonomy.
		 */
		public function add_attribute_field( $taxonomy ) {
			global $wpdb;

			$attribute = substr( $taxonomy, 3 );
			$attribute = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" ); // phpcs:ignore

			$values = array(
				'value'   => array(
					'value' => false,
					'label' => $this->custom_types[ $attribute->attribute_type ],
					'desc'  => '',
				),
				'tooltip' => array(
					'value' => false,
					'label' => __( 'Tooltip', 'yith-woocommerce-product-add-ons' ),
					'desc'  => __( 'Use this placeholder {show_image} to show the image on tooltip. Only available for image type', 'yith-woocommerce-product-add-ons' ),
				),
			);

			do_action( 'yith_wccl_print_attribute_field', $attribute->attribute_type, $values );
		}

		/**
		 * Edit field for each product attribute taxonomy
		 *
		 * @access public
		 * @since  1.0.0
		 *
		 * @param object $term Term.
		 * @param string $taxonomy Taxonomy.
		 */
		public function edit_attribute_field( $term, $taxonomy ) {
			global $wpdb;

			$attribute = substr( $taxonomy, 3 );
			$attribute = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" ); // phpcs:ignore

			$values = array(
				'value'   => array(
					'value' => yith_wapo_get_term_meta( $term->term_id, '_yith_wccl_value', true, $taxonomy ),
					'label' => $this->custom_types[ $attribute->attribute_type ],
					'desc'  => '',
				),
				'tooltip' => array(
					'value' => yith_wapo_get_term_meta( $term->term_id, '_yith_wccl_tooltip', true, $taxonomy ),
					'label' => __( 'Tooltip', 'yith-woocommerce-product-add-ons' ),
					'desc'  => __( 'Use this placeholder {show_image} to show the image on tooltip. Only available for image type', 'yith-woocommerce-product-add-ons' ),
				),
			);

			do_action( 'yith_wccl_print_attribute_field', $attribute->attribute_type, $values, true );
		}


		/**
		 * Print Attribute Tax Type HTML
		 *
		 * @access public
		 * @since  1.0.0
		 * @param string $type Type.
		 * @param mixed  $args Args.
		 * @param bool   $table Table.
		 */
		public function print_attribute_type( $type, $args, $table = false ) {

			foreach ( $args as $key => $arg ) :

				$data    = 'value' === $key ? 'data-type="' . $type . '"' : '';
				$id      = "term_{$key}";
				$name    = "term_{$key}";
				$values  = explode( ',', $arg['value'] );
				$value   = $values[0];
				$value_2 = '';
				if ( 'value' === $key && 'colorpicker' === $type ) {
					// change name.
					$name .= '[]';
					if ( isset( $values[1] ) ) {
						$value_2 = $values[1];
					}
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
					value="<?php echo esc_attr( $value ); ?>" <?php echo wp_kses_post( $data ); ?>/>
				<?php if ( 'value' === $key && 'colorpicker' === $type ) : ?>
				<span class="ywccl_add_color_icon"
					data-content="<?php echo $value_2 ? '+' : '-'; ?>"><?php echo $value_2 ? '-' : '+'; ?></span><br>
				<input type="text" class="ywccl hidden_empty" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>_2"
					value="<?php echo esc_attr( $value_2 ); ?>" <?php echo wp_kses_post( $data ); ?>/>
			<?php endif; ?>

				<p><?php echo wp_kses_post( $arg['desc'] ); ?></p>

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
		 * @param int    $term_id Term ID.
		 * @param int    $tt_id TT ID.
		 * @param string $taxonomy Taxonomy.
		 */
		public function attribute_save( $term_id, $tt_id, $taxonomy ) {

			$meta_value = $_POST['term_value'] ?? ''; // phpcs:ignore
			if ( $meta_value ) {
				if ( is_array( $meta_value ) ) {
					// first remove empty values.
					$array_values = array_filter( $meta_value );
					if ( empty( $array_values ) ) {
						$value = '';
					} else {
						$value = implode( ',', $array_values );
					}
				} else {
					$value = $meta_value;
				}

				update_term_meta( $term_id, '_yith_wccl_value', $value );
			}
			$term_tooltip = $_POST['term_tooltip'] ?? ''; // phpcs:ignore
			if ( $term_tooltip ) {
				update_term_meta( $term_id, '_yith_wccl_tooltip', $term_tooltip );
			}
		}

		/**
		 * Create new column for product attributes
		 *
		 * @access public
		 * @since  1.0.0
		 * @param mixed $columns Columns.
		 * @return mixed
		 */
		public function product_attribute_columns( $columns ) {

			if ( empty( $columns ) ) {
				return $columns;
			}

			$temp_cols = array();
			// checkbox.
			$temp_cols['cb'] = $columns['cb'];
			// value.
			$temp_cols['yith_wccl_value'] = __( 'Value', 'yith-woocommerce-product-add-ons' );

			unset( $columns['cb'] );
			$columns = array_merge( $temp_cols, $columns );

			return $columns;
		}

		/**
		 * Print the column content
		 *
		 * @access public
		 * @since  1.0.0
		 * @param mixed  $columns Columns.
		 * @param string $column Columns.
		 * @param int    $id ID.
		 * @return mixed
		 */
		public function product_attribute_column( $columns, $column, $id ) {
			global $taxonomy, $wpdb;

			if ( 'yith_wccl_value' === $column ) {

				$attribute = substr( $taxonomy, 3 );
				$attribute = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute'" ); // phpcs:ignore
				$att_type  = $attribute->attribute_type;

				$value    = yith_wapo_get_term_meta( $id, '_yith_wccl_value', true, $taxonomy );
				$columns .= $this->_print_attribute_column( $value, $att_type );
			}

			return $columns;
		}


		/**
		 * Print the column content according to attribute type
		 *
		 * @access public
		 * @since  1.0.0
		 * @param string $value Value.
		 * @param string $type Type.
		 * @return string
		 */
		protected function _print_attribute_column( $value, $type ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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
		 * @param string $taxonomy Taxonomy.
		 * @param int    $i Index.
		 */
		public function product_option_terms( $taxonomy, $i ) {

			if ( ! array_key_exists( $taxonomy->attribute_type, $this->custom_types ) ) {
				return;
			}

			global $thepostid;
			if ( is_null( $thepostid ) && isset( $_REQUEST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$thepostid = intval( $_REQUEST['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'yith-woocommerce-product-add-ons' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'yith-woocommerce-product-add-ons' ); ?></button>

			<?php
		}

		/**
		 * Get terms attributes array
		 *
		 * @since  1.3.0
		 * @param string $tax_name Tax name.
		 * @return array
		 */
		protected function get_terms( $tax_name ) {
			global $wp_version;

			if ( version_compare( $wp_version, '4.5', '<' ) ) {
				$terms = get_terms(
					$tax_name,
					array(
						'orderby'    => 'name',
						'hide_empty' => '0',
					)
				);
			} else {
				$args = array(
					'taxonomy'   => $tax_name,
					'orderby'    => 'name',
					'hide_empty' => '0',
				);
				// get terms.
				$terms = get_terms( $args );
			}

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
		 * Variation gallery template
		 *
		 * @since  1.8.0
		 * @param int      $loop Loop.
		 * @param array    $variation_data Variation data.
		 * @param \WP_Post $variation Variation.
		 */
		public function gallery_variation_html( $loop, $variation_data, $variation ) {
			$gallery = YITH_WAPO_Color_Label_Variations::get_variation_gallery( $variation );
			if ( ! is_array( $gallery ) ) {
				$gallery = array();
			}

			include YITH_WAPO_WCCL_DIR . 'templates/admin/variation-gallery.php';
		}

		/**
		 * Variation gallery single image template js
		 *
		 * @since  1.8.0
		 */
		public function gallery_variation_template_js() {
			?>
			<script type="text/html" id="tmpl-yith-wccl-variation-gallery-image">
				<li class="image" data-value="{{data.id}}">
					<a href="#" class="remove"
						title="<?php echo esc_html_x( 'Remove image', 'label for remove single image from variation gallery', 'yith-woocommerce-product-add-ons' ); ?>"></a>
					<img src="{{data.url}}">
				</li>
			</script>
			<?php
		}


		/**
		 * Save variation custom meta
		 *
		 * @since  1.8.0
		 * @param integer $variation_id Variation ID.
		 * @param int     $index Variation loop index.
		 * @return void
		 */
		public function save_variation_custom_meta( $variation_id, $index ) {
			// Get variation.
			$variation = wc_get_product( $variation_id );
			$gallery   = isset( $_POST['yith_wccl_variation_gallery'][ $index ] ) ? wc_clean( wp_unslash( $_POST['yith_wccl_variation_gallery'][ $index ] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput

			empty( $gallery ) ? $variation->delete_meta_data( '_yith_wccl_gallery' ) : $variation->update_meta_data( '_yith_wccl_gallery', array_map( 'intval', explode( ',', $gallery ) ) );
			$variation->save();
		}
	}
}
/**
 * Unique access to instance of YITH_WAPO_Color_Label_Variations_Admin class
 *
 * @since 1.0.0
 * @return YITH_WAPO_Color_Label_Variations_Admin
 */
function YITH_WAPO_Color_Label_Variations_Admin() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Color_Label_Variations_Admin::get_instance();
}

YITH_WAPO_Color_Label_Variations_Admin();
