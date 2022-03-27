<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Product_Slider_Widget' ) ) {

	/**
	 * YITH_Product_Slider_Widget
	 */
	class YITH_Product_Slider_Widget extends WP_Widget {

		/**
		 * __construct function
		 */
		public function __construct() {
			parent::__construct(
				'yith-wc-product-slider-carousel',
				__( 'YITH WooCommerce Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
				array( 'description' => __( 'Show your Product Slider in sidebar!', 'yith-woocommerce-product-slider-carousel' ) )
			);
		}

		/**
		 * Print widget form
		 *
		 * @param array $instance instance.
		 *
		 * @since 1.0.0
		 * @package YITH
		 */
		public function form( $instance ) {

			$default = array(
				'anim_in'          => '',
				'anim_out'         => '',
				'slider_id'        => '',
				'hide_price'       => 'on',
				'hide_add_to_cart' => 'on',
				'images_for_row'   => 1,
				'items_in_desktop' => 1,
				'items_in_tablet'  => 1,
				'items_in_mobile'  => 1,
			);

			$animations = ywcps_animations_list();

			$instance = wp_parse_args( $instance, $default );

			$query = array(
				'posts_per_page'   => - 1,
				'post_type'        => 'yith_wcps_type',
				'post_status'      => 'publish',
				'orderby'          => 'title',
				'order'            => 'ASC',
				'suppress_filters' => false,
			);

			$product_sliders = get_posts( $query );

			?>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'slider_id' ) ); ?>"><?php esc_html_e( 'Select a slider', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <select id="<?php esc_attr( $this->get_field_id( 'slider_id' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'slider_id' ) ); ?>">
                    <option
                            value="" <?php selected( '', $instance['slider_id'] ); ?>><?php esc_html_e( 'Select a slider', 'yith-woocommerce-product-slider-carousel' ); ?></option>
					<?php foreach ( $product_sliders as $slider ) : ?>
                        <option
                                value="<?php echo esc_attr( $slider->ID ); ?>" <?php selected( $slider->ID, $instance['slider_id'] ); ?>><?php echo esc_html( $slider->post_title ); ?></option>
					<?php endforeach; ?>
                </select>
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'images_for_row' ) ); ?>"><?php echo esc_html__( 'Images for row', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <input type="number" step="1" min="1" value="<?php echo esc_attr( $instance['images_for_row'] ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'images_for_row' ) ); ?>">
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'items_in_desktop' ) ); ?>"><?php echo esc_html__( 'Items in Desktop', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <input type="number" step="1" min="1" value="<?php echo esc_attr( $instance['items_in_desktop'] ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'items_in_desktop' ) ); ?>">
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'items_in_tablet' ) ); ?>"><?php echo esc_html__( 'Items in tablet', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <input type="number" step="1" min="1" value="<?php echo esc_attr( $instance['items_in_tablet'] ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'items_in_tablet' ) ); ?>">
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'items_in_mobile' ) ); ?>"><?php echo esc_html__( 'Items in mobile', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <input type="number" step="1" min="1" value="<?php echo esc_attr( $instance['items_in_mobile'] ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'items_in_mobile' ) ); ?>">
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'anim_in' ) ); ?>"><?php esc_html_e( 'Animation In', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'anim_in' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'anim_in' ) ); ?>">
                    <option
                            value="" <?php selected( '', $instance['anim_in'] ); ?>><?php esc_html_e( 'Select an animation', 'yith-woocommerce-product-slider-carousel' ); ?></option>
					<?php foreach ( $animations as $animation => $it ) : ?>
                        <optgroup label="<?php echo esc_attr( $animation ); ?>">
							<?php foreach ( $animations[ $animation ] as $key ) : ?>
                                <option
                                        value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $instance['anim_in'] ); ?>><?php echo esc_html( $key ); ?></option>
							<?php endforeach; ?>
                        </optgroup>
					<?php endforeach; ?>
                </select>
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'anim_out' ) ); ?>"><?php esc_html_e( 'Animation Out', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'anim_out' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'anim_out' ) ); ?>">
                    <option
                            value="" <?php selected( '', $instance['anim_out'] ); ?>><?php esc_html_e( 'Select an animation', 'yith-woocommerce-product-slider-carousel' ); ?></option>
					<?php foreach ( $animations as $animation => $it ) : ?>
                        <optgroup label="<?php echo esc_attr( $animation ); ?>">
							<?php foreach ( $animations[ $animation ] as $key ) : ?>
                                <option
                                        value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $instance['anim_out'] ); ?>><?php echo esc_html( $key ); ?></option>
							<?php endforeach; ?>
                        </optgroup>
					<?php endforeach; ?>
                </select>
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'hide_add_to_cart' ) ); ?>"><?php esc_html_e( 'Hide "Add to cart"', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <input type="checkbox" <?php checked( 'on', $instance['hide_add_to_cart'] ); ?>
                       id="<?php echo esc_attr( $this->get_field_id( 'hide_add_to_cart' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'hide_add_to_cart' ) ); ?>">
            </p>
            <p>
                <label
                        for="<?php echo esc_attr( $this->get_field_id( 'hide_price' ) ); ?>"><?php esc_html_e( 'Hide product price', 'yith-woocommerce-product-slider-carousel' ); ?></label>
                <input type="checkbox" <?php checked( 'on', $instance['hide_price'] ); ?>
                       id="<?php echo esc_attr( $this->get_field_id( 'hide_price' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'hide_price' ) ); ?>">
            </p>
			<?php
		}

		/**
		 * Update widget args
		 *
		 * @param array $new_instance new_instance.
		 * @param array $old_instance old_instance.
		 *
		 * @return array
		 * @since 1.0.0
		 * @package YITH
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = array();

			$instance['anim_in']          = isset( $new_instance['anim_in'] ) ? $new_instance['anim_in'] : '';
			$instance['anim_out']         = isset( $new_instance['anim_out'] ) ? $new_instance['anim_out'] : '';
			$instance['slider_id']        = isset( $new_instance['slider_id'] ) ? $new_instance['slider_id'] : '';
			$instance['hide_price']       = isset( $new_instance['hide_price'] ) ? $new_instance['hide_price'] : 'off';
			$instance['hide_add_to_cart'] = isset( $new_instance['hide_add_to_cart'] ) ? $new_instance['hide_add_to_cart'] : 'off';
			$instance['images_for_row']   = isset( $new_instance['images_for_row'] ) ? $new_instance['images_for_row'] : 1;
			$instance['items_in_desktop'] = isset( $new_instance['items_in_desktop'] ) ? $new_instance['items_in_desktop'] : 1;
			$instance['items_in_tablet']  = isset( $new_instance['items_in_tablet'] ) ? $new_instance['items_in_tablet'] : 1;
			$instance['items_in_mobile']  = isset( $new_instance['items_in_mobile'] ) ? $new_instance['items_in_mobile'] : 1;

			return $instance;

		}

		/**
		 * Print widget in front-end
		 *
		 * @param array $args args.
		 * @param array $instance instance.
		 *
		 * @package YITH
		 * @since 1.0.0
		 */
		public function widget( $args, $instance ) {

			$original_slider_id = $instance['slider_id'];
			$slider_id          = yit_wpml_object_id( $instance['slider_id'], 'yith_wcps_type', true );
			$is_loop            = yith_plugin_fw_is_true( get_post_meta( $original_slider_id, '_ywcps_check_loop', true ) ) ? 'true' : 'false';
			$stop_hov           = yith_plugin_fw_is_true( get_post_meta( $original_slider_id, '_ywcps_stop_hover', true ) ) ? 'true' : 'false';
			$show_nav           = yith_plugin_fw_is_true( get_post_meta( $original_slider_id, '_ywcps_show_navigation', true ) ) ? 'true' : 'false';
			$show_dot_nav       = yith_plugin_fw_is_true( get_post_meta( $original_slider_id, '_ywcps_show_dot_navigation', true ) ) ? 'true' : 'false';


			$extra_params = array(
				'en_responsive'     => get_option( 'ywcps_check_responsive' ) === 'yes' ? 'true' : 'false',
				'n_item_desk_small' => $instance['items_in_desktop'],
				'n_item_tablet'     => $instance['items_in_tablet'],
				'n_item_mobile'     => $instance['items_in_mobile'],
				'is_rtl'            => get_option( 'ywcps_check_rtl' ) === 'yes' ? 'true' : 'false',
				'id'                => $instance['slider_id'],
				'posts_per_page'    => get_option( 'ywcps_n_posts_per_page' ),

				// Slider Settings!
				'title'             => get_the_title( $slider_id ),
				'how_category'      => get_post_meta( $instance['slider_id'], '_ywcps_all_cat', true ),
				'how_brands'        => get_post_meta( $instance['slider_id'], '_ywcps_all_brand', true ),
				'product_type'      => get_post_meta( $instance['slider_id'], '_ywcps_product_type', true ),
				'show_title'        => get_post_meta( $instance['slider_id'], '_ywcp_show_title', true ),
				'hide_add_to_cart'  => isset( $instance['hide_add_to_cart'] ) && 'on' === $instance['hide_add_to_cart'],
				'hide_price'        => isset( $instance['hide_price'] ) && 'on' === $instance['hide_price'],
				'n_items'           => isset( $instance['images_for_row'] ) ? $instance['images_for_row'] : 1,
				'order_by'          => get_post_meta( $instance['slider_id'], '_ywcps_order_by', true ),
				'order'             => get_post_meta( $instance['slider_id'], '_ywcps_order_type', true ),
				'is_loop'           => $is_loop,
				'page_speed'        => get_post_meta( $instance['slider_id'], '_ywcps_pagination_speed', true ),
				'auto_play'         => get_post_meta( $instance['slider_id'], '_ywcps_auto_play', true ),
				'stop_hov'          => $stop_hov,
				'show_nav'          => $show_nav,
				'anim_in'           => $instance['anim_in'],
				'anim_out'          => $instance['anim_out'],
				'anim_speed'        => get_post_meta( $instance['slider_id'], '_ywcps_animation_speed', true ),
				'show_dot_nav'      => $show_dot_nav,
				'template_slider'   => get_post_meta( $instance['slider_id'], '_ywcps_layout_type', true ),
			);

			$extra_params['atts'] = $extra_params;
			extract( $args ); //phpcs:ignore WordPress.PHP.DontExtract
			ob_start();
			echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput

			yit_plugin_get_template( YWCPS_DIR, 'product_slider_view.php', $extra_params, false );

			echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput
			$content = ob_get_clean();
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}