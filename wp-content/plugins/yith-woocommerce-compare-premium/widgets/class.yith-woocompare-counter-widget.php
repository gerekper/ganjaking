<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Main class for counter widget
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.1.4
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOOCOMPARE' ) ) {
	/**
	 * YITH WooCommerce Compare Counter Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_Woocompare_Widget_Counter extends WP_Widget {

		/**
		 * Sets up the widgets
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'   => 'yith-woocompare-counter-widget',
				'description' => __(
					'The widget shows a counter of products added in the comparison table.',
					'yith-woocommerce-compare'
				),
			);

			parent::__construct( 'yith-woocompare-counter-widget', _x( 'YITH WooCommerce Compare Counter Widget', 'The widget name', 'yith-woocommerce-compare' ), $widget_ops );
		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args General widgets argumetns.
		 * @param array $instance Widget specific instance.
		 */
		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->get_default() );

			/**
			 * WPML Support
			 */
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

			do_action( 'wpml_register_single_string', 'Widget', 'widget_yit_compare_title_text', $instance['title'] );
			$localized_widget_title = apply_filters( 'wpml_translate_single_string', $instance['title'], 'Widget', 'widget_yit_compare_title_text' );

			echo wp_kses_post( $before_widget . $before_title . $localized_widget_title . $after_title );
			echo do_shortcode( '[yith_woocompare_counter type="' . $instance['type'] . '" show_icon="' . $instance['show_icon'] . '" text="' . $instance['text'] . '" icon="' . $instance['icon'] . '"]' );
			echo wp_kses_post( $after_widget );
		}

		/**
		 * Get default widget settings.
		 *
		 * @return array
		 */
		public function get_default() {
			return array(
				'title'     => '',
				'type'      => 'text',
				'show_icon' => 'yes',
				'text'      => '',
				'icon'      => '',
			);
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->get_default() );

			?>
			<p>
				<label>
					<?php esc_html_e( 'Title', 'yith-woocommerce-compare' ); ?>:<br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
					<?php echo esc_html_x( 'Counter style', 'The widget counter style', 'yith-woocommerce-compare' ); ?>:<br/>
					<select id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
						<option value="text" <?php selected( 'text', $instance['type'] ); ?>><?php echo esc_html__( 'Number and text', 'yith-woocommerce-compare' ); ?></option>
						<option value="number" <?php selected( 'number', $instance['type'] ); ?>><?php echo esc_html__( 'Only number', 'yith-woocommerce-compare' ); ?></option>
					</select>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_icon' ) ); ?>" <?php checked( 'yes', $instance['show_icon'] ); ?> value="yes"/>
					<?php esc_html_e( 'Show counter icon', 'yith-woocommerce-compare' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>">
					<?php esc_html_e( 'Icon url', 'yith-woocommerce-compare' ); ?>:<br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>" value="<?php echo esc_attr( $instance['icon'] ); ?>"/>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>">
					<?php esc_html_e( 'Counter text', 'yith-woocommerce-compare' ); ?>:<br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" value="<?php echo esc_attr( $instance['text'] ); ?>"/>
				</label>
				<span class="description">
					<?php esc_html_e( 'Use {{count}} as placeholder of products counter.', 'yith-woocommerce-compare' ); ?>
				</span>
			</p>

			<?php
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']     = wp_strip_all_tags( $new_instance['title'] );
			$instance['type']      = $new_instance['type'];
			$instance['show_icon'] = $new_instance['show_icon'];
			$instance['text']      = $new_instance['text'];
			$instance['icon']      = esc_url( $new_instance['icon'] );

			return $instance;
		}
	}
}
