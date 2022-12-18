<?php
/**
 * Porto WooCommerce Pre-Order Admin
 *
 * @author     Porto Themes
 * @category   Library
 * @since      5.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Porto_Pre_Order_Admin' ) ) :
	class Porto_Pre_Order_Admin {

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( 'post.php' == $GLOBALS['pagenow'] || porto_is_ajax() || ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_REQUEST['post_type'] ) && 'product' == $_REQUEST['post_type'] ) ) {
				// add pre-order fields to product edit page
				add_filter( 'product_type_options', array( $this, 'add_pre_order_checkbox' ), 5 );
				add_action( 'woocommerce_product_options_pricing', array( $this, 'add_pre_order_fields' ) );

				add_action( 'woocommerce_variation_options', array( $this, 'add_pre_order_variable_checkbox' ), 10, 3 );
				add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_pre_order_fields' ), 10, 3 );
				add_action( 'woocommerce_process_product_meta', array( $this, 'save_pre_order_fields' ), 10, 1 );
				add_action( 'woocommerce_save_product_variation', array( $this, 'save_pre_order_fields' ), 10, 2 );

				add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'save_parent_pre_order' ) );
			}

			// view pre-orders on admin orders list
			add_filter( 'views_edit-shop_order', array( $this, 'add_pre_ordered_in_orders' ) );
			add_action( 'pre_get_posts', array( $this, 'woocommerce_pre_orders' ) );

			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_pre_order_itemmeta' ) );
		}

		public function add_pre_order_checkbox( $options ) {
			$options['porto_pre_order'] = array(
				'id'            => '_porto_pre_order',
				'wrapper_class' => 'show_if_simple hide_if_bundle',
				'label'         => esc_html__( 'Pre-Order', 'porto' ),
				'description'   => esc_html__( 'Check this option to set this product to the "Pre-Order" status.', 'porto' ),
				'default'       => 'no',
			);

			return $options;
		}

		public function add_pre_order_variable_checkbox( $loop, $variation_data, $variation ) {
			$is_pre_order = get_post_meta( $variation->ID, '_porto_pre_order', true );
			?>
			<label>
				<input type="checkbox" class="checkbox variable_is_preorder" name="_porto_pre_order[<?php echo esc_attr( $loop ); ?>]"
					<?php checked( $is_pre_order, 'yes' ); ?> />
				<?php esc_html_e( 'Pre-Order', 'porto' ); ?>
				<?php echo wc_help_tip( esc_html__( 'Check this option to set this variation to the "Pre-Order" status.', 'porto' ) ); ?>
			</label>
			<?php
		}

		public function add_pre_order_fields( $loop = false, $variation_data = false, $variation = false ) {
			if ( empty( $variation ) ) {
				$class = 'form-field';
				$name  = '_porto_pre_order_date';
				$value = get_post_meta( get_the_ID(), '_porto_pre_order_date', true );
			} else {
				$class = 'form-row';
				$name  = '_porto_pre_order_date[' . $loop . ']';
				$value = get_post_meta( $variation->ID, '_porto_pre_order_date', true );
			}

			?>
			<p class="show_if_pre_order <?php echo esc_attr( $class ); ?>">
				<label><?php esc_html_e( 'Pre-Order Available Date', 'porto' ); ?></label>
				<input type="text" class="pre_order_available_date short" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" maxlength="10" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />
			</p>
			<?php
		}

		public function save_pre_order_fields( $post_id, $index = false ) {
			if ( false === $index ) {
				$pre_order      = isset( $_POST['_porto_pre_order'] ) && ! is_array( $_POST['_porto_pre_order'] ) ? 'yes' : '';
				$pre_order_date = $_POST['_porto_pre_order_date'];
			} else {
				$pre_order      = isset( $_POST['_porto_pre_order'][ $index ] ) ? 'yes' : '';
				$pre_order_date = $_POST['_porto_pre_order_date'][ $index ];
			}

			update_post_meta( $post_id, '_porto_pre_order', $pre_order );
			update_post_meta( $post_id, '_porto_pre_order_date', empty( $pre_order_date ) ? '' : sanitize_title( $pre_order_date ) );
		}

		public function save_parent_pre_order( $post_id ) {
			update_post_meta( $post_id, '_porto_variation_pre_order', isset( $_POST['_porto_pre_order'] ) ? 'yes' : '' );
		}

		public function add_pre_ordered_in_orders( $views ) {
			$order_statuses = wc_get_order_statuses();
			unset( $order_statuses['wc-completed'] );

			$args = array(
				'post_type'      => wc_get_order_types(),
				'post_status'    => array_keys( $order_statuses ),
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_porto_pre_order',
						'value'   => 'yes',
						'compare' => '=',
					),
				),
			);

			$pre_orders = new WP_Query( $args );

			if ( $pre_orders->have_posts() ) {
				$views['pre-orders'] = sprintf(
					'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
					esc_url(
						add_query_arg(
							array(
								'post_type'       => 'shop_order',
								'porto_pre_order' => true,
							),
							admin_url( 'edit.php' )
						)
					),
					isset( $_GET['porto_pre_order'] ) ? 'current' : '',
					esc_html__( 'Pre-Orders', 'porto' ),
					(int) $pre_orders->found_posts
				);
			}
			return $views;
		}

		public function woocommerce_pre_orders() {
			if ( isset( $_GET['porto_pre_order'] ) && $_GET['porto_pre_order'] ) {
				add_filter( 'posts_join', array( $this, 'view_order_join' ) );
				add_filter( 'posts_where', array( $this, 'view_order_where' ) );
			}
		}

		public function view_order_join( $join ) {
			global $wpdb;
			return $join . " LEFT JOIN {$wpdb->postmeta} as m ON {$wpdb->posts}.ID = m.post_id";
		}

		public function view_order_where( $where ) {
			global $wpdb;
			return $where . $wpdb->prepare( " AND m.meta_key = %s AND m.meta_value = %s AND {$wpdb->posts}.post_status != 'completed'", '_porto_pre_order', 'yes' );
		}

		public function hide_pre_order_itemmeta( $array ) {
			$array[] = '_porto_pre_order_item';
			$array[] = '_porto_pre_order_item_date';
			return $array;
		}
	}
endif;
