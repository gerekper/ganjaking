<?php
/**
 * The widget class.
 *
 * @package WC_Products_Compare
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Products_Compare_Widget class.
 *
 * phpcs:disable Squiz.Commenting.FunctionComment.Missing, WordPress.Security.NonceVerification.Recommended
 */
class WC_Products_Compare_Widget extends WP_Widget {

	/**
	 * Init
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'woocommerce woocommerce-products-compare-widget',
			'description' => __( 'Displays a running list of compared products.', 'woocommerce-products-compare' ),
		);

		parent::__construct( 'compared_products', __( 'WooCommerce Products Compare', 'woocommerce-products-compare' ), $widget_ops );
	}

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Compared Products', 'woocommerce-products-compare' ) : $instance['title'], $instance, $this->id_base );

		$html = '';

		$html .= $args['before_widget'];

		if ( $title ) {
			$html .= $args['before_title'] . $title . $args['after_title'];
		}

		$products = WC_Products_Compare_Frontend::get_compared_products();

		$endpoint = WC_Products_Compare_Frontend::get_endpoint();

		if ( $products ) {

			$html .= '<ul>' . PHP_EOL;

			foreach ( $products as $product ) {
				$product = wc_get_product( $product );

				if ( ! WC_Products_Compare::is_product( $product ) ) {
					continue;
				}

				$post = get_post( $product->get_id() );

				$html .= '<li data-product-id="' . esc_attr( $product->get_id() ) . '">' . PHP_EOL;

				$html .= '<a href="' . get_permalink( $product->get_id() ) . '" title="' . esc_attr( $post->post_title ) . '" class="product-link">' . PHP_EOL;

				$html .= $product->get_image( 'shop_thumbnail' ) . PHP_EOL;

				$html .= '<h3>' . $post->post_title . '</h3>' . PHP_EOL;

				$html .= '</a>' . PHP_EOL;

				$html .= '<a href="#" title="' . esc_attr__( 'Remove Product', 'woocommerce-products-compare' ) . '" class="remove-compare-product" data-remove-id="' . esc_attr( $product->get_id() ) . '">' . __( 'Remove Product', 'woocommerce-products-compare' ) . '</a>' . PHP_EOL;

				$html .= '</li>' . PHP_EOL;
			}

			$html .= '</ul>' . PHP_EOL;

			$html .= '<a href="#" title="' . esc_attr__( 'Remove all products', 'woocommerce-products-compare' ) . '" class="woocommerce-products-compare-remove-all-products">' . esc_html__( 'Remove all products', 'woocommerce-products-compare' ) . '</a>' . PHP_EOL;

		} else {
			$html .= '<p class="no-products">' . __( 'Add some products to compare.', 'woocommerce-products-compare' ) . '</p>' . PHP_EOL;
		}

		$html .= '<a href="' . esc_url( site_url() . '/' . $endpoint ) . '" title="' . esc_attr__( 'Compare Products', 'woocommerce-products-compare' ) . '" class="button woocommerce-products-compare-widget-compare-button">' . esc_html__( 'Compare Products', 'woocommerce-products-compare' ) . '</a>' . PHP_EOL;

		$html .= $args['after_widget'];

		echo $html; // phpcs:ignore
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Compare Products', 'woocommerce-products-compare' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'woocommerce-products-compare' ); ?></label>

			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

		</p>
		<?php
		return true;
	}
}
