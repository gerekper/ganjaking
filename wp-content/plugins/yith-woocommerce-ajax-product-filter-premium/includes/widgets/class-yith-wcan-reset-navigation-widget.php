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

if ( ! class_exists( 'YITH_WCAN_Reset_Navigation_Widget' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Reset_Navigation_Widget extends WP_Widget {

		/**
		 * Construct method
		 *
		 * @return void
		 */
		public function __construct() {
			$widget_ops  = array(
				'classname'   => 'yith-woocommerce-ajax-product-filter yith-woo-ajax-reset-navigation yith-woo-ajax-navigation woocommerce widget_layered_nav',
				'description' => _x( 'Reset all filters set by YITH WooCommerce AJAX Product Filter', '[Plugin Name]', 'yith-woocommerce-ajax-navigation' ),
			);
			$control_ops = array(
				'width'  => 400,
				'height' => 350,
			);

			parent::__construct( 'yith-woo-ajax-reset-navigation', __( 'YITH AJAX Reset Filter', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );

			/**
			 * Deprecated Filters Map
			 *
			 * @param mixed|array $deprecated_filters_map Array of deprecated filters
			 * @since 3.11.7
			 * @ return void
			 */
			$deprecated_filters_map = array(
				'yith-wcan-reset-navigation-label'        => array(
					'since'  => '3.11.7',
					'use'    => 'yith_wcan_reset_navigation_label',
					'params' => 3,
				),
				'yith-wcan-reset-navigation-button-class' => array(
					'since'  => '3.11.7',
					'use'    => 'yith_wcan_reset_navigation_button_class',
					'params' => 1,
				),
			);

			yith_wcan_deprecated_filter( $deprecated_filters_map );
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
			global $wp_query;
			$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();

			/**
			 * Extracted vars:
			 *
			 * @var $before_widget string
			 * @var $after_widget string
			 * @var $title string
			 * @var $before_title string
			 * @var $after_title string
			 */
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract

			$_attributes_array = yit_wcan_get_product_taxonomy();
			$request           = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
				return;
			}

			// Price.
			$min_price = isset( $request['min_price'] ) ? (float) $request['min_price'] : 0;
			$max_price = isset( $request['max_price'] ) ? (float) $request['max_price'] : 0;

			$after_widget  = apply_filters( 'yith_wcan_after_reset_widget', $after_widget );
			$before_widget = apply_filters( 'yith_wcan_before_reset_widget', $before_widget );

			if ( count( $_chosen_attributes ) > 0 || $min_price > 0 || $max_price > 0 || apply_filters( 'yith_woocommerce_reset_filters_attributes', false ) ) {
				$title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
				$label = isset( $instance['label'] ) ? apply_filters( 'yith_wcan_reset_navigation_label', $instance['label'], $instance, $this->id_base ) : '';

				$link = '';

				// clean the url.
				if ( ! isset( $request['source_id'] ) ) {
					$link = '';

					// Check if the user have enabled only WC PRice Filter.
					if ( yit_is_filtered_uri() && ( isset( $request['min_price'] ) || isset( $request['max_price'] ) ) && is_product_taxonomy() ) {
						$queried_object = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;

						if ( $queried_object instanceof WP_Term && ! isset( $request[ $queried_object->taxonomy ] ) ) {
							$link = get_term_link( $queried_object );
						}
					}

					$link = empty( $link ) || $link instanceof WP_Error ? get_post_type_archive_link( 'product' ) : $link;

					foreach ( (array) $_chosen_attributes as $taxonomy => $data ) {
						$taxonomy_filter = str_replace( 'pa_', '', $taxonomy );
						$link            = remove_query_arg( 'filter_' . $taxonomy_filter, $link );
					}

					$link = remove_query_arg( array( 'min_price', 'max_price', 'product_tag' ), $link );
				} else {
					// Start filter from Product category Page.
					$term = null;

					if ( ! empty( $request['source_id'] ) && ! empty( $request['source_tax'] ) ) {
						$term = get_term_by( 'term_id', sanitize_text_field( wp_unslash( $request['source_id'] ) ), sanitize_text_field( wp_unslash( $request['source_tax'] ) ) );
					}

					if ( $term instanceof WP_Term ) {
						$link = get_term_link( $term, $term->taxonomy );
					}
				}

				if ( is_search() && isset( $request['s'] ) && isset( $request['post_type'] ) ) {
					$s    = urlencode( sanitize_text_field( wp_unslash( $request['s'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
					$link = add_query_arg(
						array(
							's'         => $s,
							'post_type' => sanitize_text_field( wp_unslash( $request['post_type'] ) ),
						),
						get_home_url()
					);
				}

				$link = apply_filters( 'yith_woocommerce_reset_filter_link', $link );

				echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				if ( $title ) {
					echo $before_title . esc_html( $title ) . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				$button_class = apply_filters( 'yith_wcan_reset_navigation_button_class', 'yith-wcan-reset-navigation button' );
				$rel_nofollow = yith_wcan_add_rel_nofollow_to_url( true );

				echo '<div class="yith-wcan">';
				echo '<a ' . $rel_nofollow . ' class="' . esc_attr( $button_class ) . '" href="' . esc_url( $link ) . '">' . esc_html( $label ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</div>';

				echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				if ( strpos( $before_widget, '<!--widget' ) !== 0 ) {
					$before_widget = str_replace( '>', ' style="display:none">', $before_widget );
				}

				// print empty widget.
				echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Outputs the form to configure widget
		 *
		 * @param array $instance Current instance.
		 *
		 * @return void
		 */
		public function form( $instance ) {
			$defaults = array(
				'title' => '',
				'label' => __( 'Reset All Filters', 'yith-woocommerce-ajax-navigation' ),
			);

			$instance = wp_parse_args( (array) $instance, $defaults ); ?>

			<p>
				<label>
					<strong><?php esc_html_e( 'Title', 'yith-woocommerce-ajax-navigation' ); ?>:</strong><br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
				</label>
			</p>
			<p>
				<label>
					<strong><?php esc_html_e( 'Button Label', 'yith-woocommerce-ajax-navigation' ); ?>:</strong><br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label' ) ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>"/>
				</label>
			</p>

			<?php
		}

		/**
		 * Update instance
		 *
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 *
		 * @return array Formatted instance.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance          = $old_instance;
			$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
			$instance['label'] = wp_strip_all_tags( $new_instance['label'] );

			return $instance;
		}

	}
}
