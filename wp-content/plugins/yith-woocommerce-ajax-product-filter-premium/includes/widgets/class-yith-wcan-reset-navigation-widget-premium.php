<?php
/**
 * Reset button widget
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Widgets
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Reset_Navigation_Widget_Premium' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Reset_Navigation_Widget_Premium extends YITH_WCAN_Reset_Navigation_Widget {

		/**
		 * Outputs the form to configure widget
		 *
		 * @param array $instance Current instance.
		 *
		 * @return void
		 */
		public function form( $instance ) {
			parent::form( $instance );

			$defaults = array(
				'custom_style'           => 0,
				'background_color'       => '',
				'background_color_hover' => '',
				'text_color'             => '',
				'text_color_hover'       => '',
				'border_color'           => '',
				'border_color_hover'     => '',
			);

			$instance = wp_parse_args( (array) $instance, $defaults ); ?>

			<p id="yith-wcan-enable-custom-style-<?php echo esc_attr( $instance['custom_style'] ); ?>" class="yith-wcan-enable-custom-style">
				<label for="<?php echo esc_attr( $this->get_field_id( 'custom_style' ) ); ?>"><?php esc_html_e( 'Use custom style for reset button', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'custom_style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'custom_style' ) ); ?>" value="1" <?php checked( $instance['custom_style'], 1, true ); ?> class="yith-wcan-enable-custom-style-check widefat"/>
				</label>
			</p>

			<div class="yith-wcan-reset-custom-style" style="display: <?php echo empty( $instance['custom_style'] ) ? 'none' : 'block'; ?>">
				<p>
					<label class="yith-wcan-reset-table">
						<strong><?php esc_html_e( 'Background color', 'yith-woocommerce-ajax-navigation' ); ?>:</strong>
					</label>
					<input class="widefat yith-colorpicker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'background_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'background_color' ) ); ?>" value="<?php echo esc_attr( $instance['background_color'] ); ?>"/>
				</p>

				<p>
					<label class="yith-wcan-reset-table">
						<strong><?php esc_html_e( 'Background color on hover', 'yith-woocommerce-ajax-navigation' ); ?>:</strong>
					</label>
					<input class="widefat yith-colorpicker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'background_color_hover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'background_color_hover' ) ); ?>" value="<?php echo esc_attr( $instance['background_color_hover'] ); ?>"/>
				</p>

				<p>
					<label class="yith-wcan-reset-table">
						<strong><?php esc_html_e( 'Text color', 'yith-woocommerce-ajax-navigation' ); ?>:</strong>
					</label>
					<input class="widefat yith-colorpicker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'text_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_color' ) ); ?>" value="<?php echo esc_attr( $instance['text_color'] ); ?>"/>
				</p>

				<p>
					<label class="yith-wcan-reset-table">
						<strong><?php esc_html_e( 'Text color on hover', 'yith-woocommerce-ajax-navigation' ); ?>:</strong>
					</label>
					<input class="widefat yith-colorpicker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'text_color_hover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_color_hover' ) ); ?>" value="<?php echo esc_attr( $instance['text_color_hover'] ); ?>"/>
				</p>

				<p>
					<label class="yith-wcan-reset-table">
						<strong><?php esc_html_e( 'Border color', 'yith-woocommerce-ajax-navigation' ); ?>:</strong>
					</label>
					<input class="widefat yith-colorpicker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" value="<?php echo esc_attr( $instance['border_color'] ); ?>"/>
				</p>

				<p>
					<label class="yith-wcan-reset-table">
						<strong><?php esc_html_e( 'Border color on hover', 'yith-woocommerce-ajax-navigation' ); ?>:</strong>
					</label>
					<input class="widefat yith-colorpicker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_color_hover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color_hover' ) ); ?>" value="<?php echo esc_attr( $instance['border_color_hover'] ); ?>"/>
				</p>
			</div>
			<script>jQuery(document).trigger('yith_colorpicker');</script>
			<?php
		}

		/**
		 * Update intance
		 *
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 *
		 * @return array Formatted instance.
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = parent::update( $new_instance, $old_instance );

			$instance['custom_style']           = ( isset( $new_instance['custom_style'] ) && yith_plugin_fw_is_true( $new_instance['custom_style'] ) ) ? 1 : 0;
			$instance['background_color']       = $new_instance['background_color'];
			$instance['background_color_hover'] = $new_instance['background_color_hover'];
			$instance['text_color']             = $new_instance['text_color'];
			$instance['text_color_hover']       = $new_instance['text_color_hover'];
			$instance['border_color']           = $new_instance['border_color'];
			$instance['border_color_hover']     = $new_instance['border_color_hover'];

			return $instance;
		}

		/**
		 * Prints the widget
		 *
		 * @param array $args General widget arguments.
		 * @param array $instance Current instance arguments.
		 *
		 * @return void
		 */
		public function widget( $args, $instance ) {

			if ( ! empty( $instance['custom_style'] ) ) {
				$css_selector = "#{$args['widget_id']} .yith-wcan .yith-wcan-reset-navigation.button";

				$style  = '<style>';
				$style .= "$css_selector {";
				$style .= ! empty( $instance['background_color'] ) ? "background-color: {$instance['background_color']};" : '';
				$style .= ! empty( $instance['text_color'] ) ? "color: {$instance['text_color']};" : '';
				$style .= ! empty( $instance['border_color'] ) ? "border: 1px solid {$instance['border_color']};" : '';
				$style .= '}';
				$style .= "$css_selector:hover {";
				$style .= ! empty( $instance['background_color_hover'] ) ? "background-color: {$instance['background_color_hover']};" : '';
				$style .= ! empty( $instance['text_color_hover'] ) ? "color: {$instance['text_color_hover']};" : '';
				$style .= ! empty( $instance['border_color_hover'] ) ? "border: 1px solid {$instance['border_color_hover']};" : '';
				$style .= '}';
				$style .= '</style>';

				echo wp_kses( $style, array( 'style' => array() ) );
			}

			$brands  = yit_get_brands_taxonomy();
			$request = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			add_filter( 'yith_woocommerce_reset_filter_link', 'yith_remove_premium_query_arg' );

			if (
				isset( $request['orderby'] ) ||
				isset( $request['instock_filter'] ) ||
				isset( $request['onsale_filter'] ) ||
				isset( $request['product_tag'] ) ||
				isset( $request[ $brands ] )
			) {
				add_filter( 'yith_woocommerce_reset_filters_attributes', '__return_true' );
			}

			if ( isset( $request['product_cat'] ) ) {
				$_chosen_categories = yith_wcan_separate_terms( urlencode( sanitize_text_field( wp_unslash( $request['product_cat'] ) ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				if ( is_array( $_chosen_categories ) && count( $_chosen_categories ) === 1 ) {
					$category_slug = array_shift( $_chosen_categories );
					$term          = get_term_by( 'slug', $category_slug, 'product_cat' );
					if ( ! empty( $term ) && $term->count ) {
						add_filter( 'yith_woocommerce_reset_filters_attributes', '__return_true' );
					}
				} else {
					add_filter( 'yith_woocommerce_reset_filters_attributes', '__return_true' );
				}
			}

			parent::widget( $args, $instance );
		}
	}
}
