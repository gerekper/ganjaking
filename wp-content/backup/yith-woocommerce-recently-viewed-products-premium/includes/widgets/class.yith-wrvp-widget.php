<?php
/**
 * YITH WooCommerce Recently Viewed Products List Widget
 *
 * @author        YITH
 * @category      Widgets
 * @package       YITH WooCommerce Recently Viewed Products
 * @version       1.0.0
 * @extends    WP_Widget
 */

if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WRVP_Widget' ) ) {

	class YITH_WRVP_Widget extends WP_Widget {

		function __construct() {
			$widget_ops = array (
				'classname' => 'woocommerce yith-wrvp-widget',
				'description' => __( 'The widget shows the list of products added in the compare table.', 'yith-woocommerce-recently-viewed-products' )
			);

			parent::__construct( 'yith-wrvp-widget', __( 'YITH WooCommerce Recently Viewed Products Widget', 'yith-woocommerce-recently-viewed-products' ), $widget_ops );
		}

		/**
		 * widget function.
		 *
		 * @see WP_Widget
		 * @access public
		 *
		 * @param array $args
		 * @param array $instance
		 *
		 * @return void
		 * @author Francesco Licandro
		 */
		function widget( $args, $instance ) {

			global $post;

			ob_start();

			extract($args);

			$title = apply_filters('widget_title', empty($instance['title']) ? __('YITH Recently Viewed Products', 'yith-woocommerce-recently-viewed-products' ) : $instance['title'], $instance, $this->id_base);

			// get similar products
			$products_list = apply_filters( 'yith_wrvp_widget_product_list', ( $instance['prod_type'] == 'similar' ) ? YITH_WRVP_Frontend_Premium()->get_similar_products() : YITH_WRVP_Frontend_Premium()->get_the_products_list());

			// remove current product from products list
			if( $post && get_post_type( $post ) == 'product' && is_product() ) {
				$product_id = intval( $post->ID );
				if( ( $key = array_search( $product_id, $products_list ) ) !== false ) {
					unset( $products_list[ $key ] );
				}
			}

			if( empty( $products_list ) ) {

				$content = apply_filters( 'yith_wrvp_widget_contentesc_html_empty_html', ob_get_clean(), $instance, $this );

				echo wp_kses_post( $content );
				return;
			}

			// sort array
			krsort( $products_list );

			$query_args = array(
				'post_type' => 'product',
				'ignore_sticky_posts' => 1,
				'no_found_rows' => 1,
				'posts_per_page' => $instance['num_prod'],
				'post__in' => $products_list,
				'order' => 'DESC'
			);

			switch( $instance['orderby'] ) {
				case 'sales':
					$query_args['meta_key'] = 'total_sales';
					$query_args['orderby']  = 'meta_value_num';
					break;
				case 'rand':
					$query_args['orderby']  = 'rand';
					break;
				case 'newest':
					$query_args['orderby'] = 'date';
					break;
				case 'high-low':
					$query_args['meta_key'] = '_price';
					$query_args['orderby']  = 'meta_value_num';
					break;
				case 'low-high':
					$query_args['meta_key'] = '_price';
					$query_args['orderby']  = 'meta_value_num';
					$query_args['order'] = 'ASC';
					break;
				default:
					$query_args['orderby']  = 'post__in';
					break;
			}

			// visibility condition
			$query_args = yit_product_visibility_meta( $query_args );

			$results = new WP_Query($query_args);

			if ( $results->have_posts() ) {

				echo wp_kses_post( $before_widget );

				if ($title) {
					echo wp_kses_post( $before_title ) . wp_kses_post( $title ) . wp_kses_post( $after_title );
				}

				echo '<div class="clear"></div>';
				echo wp_kses_post( apply_filters( 'woocommerce_before_ywrvp_widget_product_list', '<ul class="product_list_widget">' )) ;


				while ( $results->have_posts() ) {

					$results->the_post();

					/**
					 * Fix issue with Visual Composer: print reviews template
					 */
					$results->post->comment_status = false;

					wc_get_template('content-widget-product.php');
				}

				echo wp_kses_post( apply_filters( 'woocommerce_after_ywrvp_widget_product_list', '</ul>' ) );

				echo wp_kses_post( $after_widget );

				wp_reset_postdata();

			}

			$content = ob_get_clean();

			echo wp_kses_post( $content );
		}


		/**
		 * update function.
		 *
		 * @see WP_Widget->update
		 * @access public
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 * @return array
		 * @author Francesco Licandro
		 */
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['num_prod'] = intval( $new_instance['num_prod'] );
			$instance['orderby'] = $new_instance['orderby'];;
			$instance['prod_type'] = $new_instance['prod_type'];

			return $instance;
		}

		/**
		 * form function.
		 *
		 * @see WP_Widget->form
		 * @access public
		 * @param array $instance
		 * @return void
		 * @author Francesco Licandro
		 */
		function form($instance) {

			$defaults = array(
				'title' 	=> __( 'You may be interested in', 'yith-woocommerce-recently-viewed-products' ),
				'num_prod' 	=> 4,
				'orderby' 	=> 'viewed',
				'prod_type' => 'similar'
			);

			$instance = wp_parse_args($instance, $defaults);

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'yith-woocommerce-recently-viewed-products' ); ?>
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					       value="<?php echo esc_attr( $instance[ 'title' ] ); ?>"/>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'num_prod' ) ); ?>"><?php esc_html_e('Number of products', 'yith-woocommerce-recently-viewed-products'); ?>
					<br><input type="number" id="<?php echo esc_attr( $this->get_field_id( 'num_prod' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'num_prod') ); ?>"
					           value="<?php echo esc_attr( $instance['num_prod'] ); ?>" min="0"/>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e('Order by', 'yith-woocommerce-recently-viewed-products'); ?>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' )) ; ?>">
						<option value="viewed" <?php selected( $instance['orderby'], 'rand') ?>><?php esc_html_e('Viewed Order', 'yith-woocommerce-recently-viewed-products'); ?></option>
						<option value="rand" <?php selected( $instance['orderby'], 'rand') ?>><?php esc_html_e('Random', 'yith-woocommerce-recently-viewed-products'); ?></option>
						<option value="newest" <?php selected( $instance['orderby'], 'rand') ?>><?php esc_html_e('Newest', 'yith-woocommerce-recently-viewed-products'); ?></option>
						<option value="sales" <?php selected( $instance['orderby'], 'sales') ?>><?php esc_html_e( 'Sales', 'yith-woocommerce-recently-viewed-products' ); ?></option>
						<option value="high-low" <?php selected( $instance['orderby'], 'high-low') ?>><?php esc_html_e( 'Price: High to Low', 'yith-woocommerce-recently-viewed-products' ); ?></option>
						<option value="low-high" <?php selected( $instance['orderby'], 'low-high') ?>><?php esc_html_e( 'Price: Low to High', 'yith-woocommerce-recently-viewed-products' ); ?></option>
					</select>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'prod_type' ) ); ?>"><?php esc_html_e('Products to show', 'yith-woocommerce-recently-viewed-products'); ?>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'prod_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'prod_type' ) ); ?>">
						<option value="viewed" <?php selected( $instance['prod_type'], 'rand') ?>><?php esc_html_e('Only viewed products', 'yith-woocommerce-recently-viewed-products'); ?></option>
						<option value="similar" <?php selected( $instance['prod_type'], 'sales') ?>><?php esc_html_e( 'Include similar products', 'yith-woocommerce-recently-viewed-products' ); ?></option>
					</select>
				</label>
			</p>

			<?php
		}
	}
}