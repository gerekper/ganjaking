<?php
/**
 * Ajax Search Widget
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAS_Ajax_Search_Widget' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAS_Ajax_Search_Widget extends WP_Widget {
		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {

			/* Widget variable settings. */
			$this->woo_widget_cssclass    = 'woocommerce widget_product_search yith_woocommerce_ajax_search';
			$this->woo_widget_description = __( 'An Ajax Search box for products only.', 'yith-woocommerce-ajax-search' );
			$this->woo_widget_idbase      = 'yith_woocommerce_ajax_search';
			$this->woo_widget_name        = __( 'YITH WooCommerce Ajax Product Search', 'yith-woocommerce-ajax-search' );

			/* Widget settings. */
			$widget_ops = array(
				'classname'   => $this->woo_widget_cssclass,
				'description' => $this->woo_widget_description,
			);

			/* Create the widget. */
			parent::__construct( 'yith_woocommerce_ajax_search', $this->woo_widget_name, $widget_ops );
		}


		/**
		 * Widget function.
		 *
		 * @see WP_Widget
		 * @access public
		 * @param array $args Array of arguments.
		 * @param array $instance Array of instance.
		 * @return void
		 */
		public function widget( $args, $instance ) {

			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			}

			echo do_shortcode( '[yith_woocommerce_ajax_search]' );

			echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Update function.
		 *
		 * @see WP_Widget->update
		 * @access public
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance['title'] = wp_strip_all_tags( stripslashes( $new_instance['title'] ) );
			return $instance;
		}

		/**
		 * Form function.
		 *
		 * @see WP_Widget->form
		 * @access public
		 * @param array $instance Instance.
		 * @return void
		 */
		public function form( $instance ) {
			global $wpdb;
			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'woocommerce' ); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="
																  <?php
																	if ( isset( $instance['title'] ) ) {
																		echo esc_attr( $instance['title'] );}
																	?>
				" /></p>
			<?php
		}
	}
}
