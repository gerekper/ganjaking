<?php
/**
 * Smart Coupons category fields in coupons
 *
 * @author      StoreApps
 * @since       4.8.0
 * @version     1.2.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_SC_Coupon_Categories' ) ) {

	/**
	 * Class for handling Smart Coupons' field in coupons
	 */
	class WC_SC_Coupon_Categories {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Categories
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Get single instance of WC_SC_Coupon_Categories
		 *
		 * @return WC_SC_Coupon_Categories Singleton object of WC_SC_Coupon_Categories
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'wc_sc_coupons_add_category' ) );
			add_filter( 'parent_file', array( $this, 'wc_sc_make_menu_active' ) );
			add_action( 'admin_footer', array( $this, 'add_footer_script' ) );
			add_filter( 'manage_edit-sc_coupon_category_columns', array( $this, 'wc_sc_coupon_category_id_column' ) );
			add_filter( 'manage_sc_coupon_category_custom_column', array( $this, 'wc_sc_coupon_category_id_column_content' ), 10, 3 );
			add_filter( 'manage_edit-sc_coupon_category_sortable_columns', array( $this, 'wc_sc_define_sortable_id_columns' ) );

		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Register custom taxonomy called sc_coupon_category.
		 */
		public function wc_sc_coupons_add_category() {
			$labels = array(
				'name'              => __( 'Coupon categories', 'woocommerce-smart-coupons' ),
				'singular_name'     => __( 'Category', 'woocommerce-smart-coupons' ),
				'menu_name'         => _x( 'Categories', 'Admin menu name', 'woocommerce-smart-coupons' ),
				'search_items'      => __( 'Search coupon categories', 'woocommerce-smart-coupons' ),
				'all_items'         => __( 'All coupon categories', 'woocommerce-smart-coupons' ),
				'parent_item'       => __( 'Parent coupon category', 'woocommerce-smart-coupons' ),
				'parent_item_colon' => __( 'Parent coupon category:', 'woocommerce-smart-coupons' ),
				'edit_item'         => __( 'Edit coupon category', 'woocommerce-smart-coupons' ),
				'update_item'       => __( 'Update coupon category', 'woocommerce-smart-coupons' ),
				'add_new_item'      => __( 'Add new coupon category', 'woocommerce-smart-coupons' ),
				'new_item_name'     => __( 'New coupon category name', 'woocommerce-smart-coupons' ),
				'not_found'         => __( 'No coupon categories found', 'woocommerce-smart-coupons' ),
			);
			register_taxonomy(
				'sc_coupon_category',       // Taxonomy name.
				array( 'shop_coupon' ),       // object for which the taxonomy is created.
				array(
					'labels'            => $labels,
					'description'       => __( 'Manage coupon categories', 'woocommerce-smart-coupons' ),
					'public'            => true,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => false,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'rewrite'           => array( 'slug' => 'sc_coupon_category' ),
					'query_var'         => true,
				)
			);
			register_taxonomy_for_object_type( 'sc_coupon_category', 'shop_coupon' );
		}

		/**
		 * Function to render coupon category column on coupons dashboard.
		 *
		 * @param int $post_id The coupon ID.
		 */
		public function render_coupon_category_column( $post_id ) {
			$terms = get_the_terms( $post_id, 'sc_coupon_category' );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$content[] = '<a href="' . esc_url( admin_url( 'edit.php?sc_coupon_category=' . $term->slug . '&post_type=shop_coupon' ) ) . '">' . esc_html( $term->name ) . '</a>';
				}
				echo join( ', ', $content ); // phpcs:ignore
			} else {
				echo '<span class="na">&ndash;</span>';
			}
		}

		/**
		 * Function to set woocommerce menu active
		 *
		 * @param string $parent_file file reference for menu.
		 */
		public function wc_sc_make_menu_active( $parent_file ) {
			global $current_screen;

			$taxonomy = $current_screen->taxonomy;
			if ( 'sc_coupon_category' === $taxonomy ) {
				if ( $this->is_wc_gte_44() ) {
					$parent_file = 'woocommerce-marketing';
				} else {
					$parent_file = 'woocommerce';
				}
			}

			return $parent_file;
		}

		/**
		 * Function to add custom script in admin footer.
		 */
		public function add_footer_script() {
			global $pagenow, $post;
			$get_post_type = ( ! empty( $post->post_type ) ) ? $post->post_type : ( ( ! empty( $_GET['post_type'] ) ) ? wc_clean( wp_unslash( $_GET['post_type'] ) ) : '' ); // phpcs:ignore
			if ( 'shop_coupon' === $get_post_type ) {
				if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) {
					$manage_category_string = __( 'Manage coupon categories', 'woocommerce-smart-coupons' );
					?>
					<script type="text/javascript">
						jQuery(function() {
							jQuery('#sc_coupon_category-tabs').before('<div class="sc-manage-category"><a target="_blank" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=sc_coupon_category&post_type=shop_coupon' ) ); ?> "><?php echo esc_html( $manage_category_string ); ?></a></div>');
						});
					</script>
					<?php
				}
			}
		}

		/**
		 * Function for add id column in coupon category taxonomy page.
		 *
		 * @param array $columns - Existing headers.
		 * @return array
		 */
		public function wc_sc_coupon_category_id_column( $columns = array() ) {
			if ( ! empty( $columns ) ) {
				return array_slice( $columns, 0, 1, true ) + array( 'id' => 'ID' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
			}
			return $columns;
		}

		/**
		 * Function for add content to ID column of coupon category taxonomy page.
		 *
		 * @param string $content - column content.
		 * @param string $column_name - column_name.
		 * @param string $term_id - id.
		 * @return string
		 */
		public function wc_sc_coupon_category_id_column_content( $content = '', $column_name = '', $term_id = 0 ) {
			return $term_id;
		}

		/**
		 * Function for change ID column to sortable.
		 *
		 * @param array $columns - Existing columns.
		 * @return array
		 */
		public function wc_sc_define_sortable_id_columns( $columns = array() ) {
			$columns['id'] = 'id';
			return $columns;
		}


	}

}

WC_SC_Coupon_Categories::get_instance();
