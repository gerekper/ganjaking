<?php
/**
 * Filter preset widget
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Widgets
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Filters_Widget' ) ) {
	/**
	 * Preset widget class
	 */
	class YITH_WCAN_Filters_Widget extends WP_Widget {
		/**
		 * Widget ID
		 *
		 * @const string
		 */
		const ID_BASE = 'yith-woocommerce-ajax-navigation-filters';

		/**
		 * Constructor method
		 *
		 * @return void
		 * @since 4.0.0
		 */
		public function __construct() {
			parent::__construct( self::ID_BASE, _x( 'YITH AJAX Filters Preset', '[ADMIN] Name of the preset widget', 'yith-woocommerce-ajax-navigation' ) );
		}

		/**
		 * Prints form of options for current widget
		 *
		 * @param array $instance Current instance.
		 *
		 * @return void
		 * @since 4.0.0
		 */
		public function form( $instance ) {
			$presets = YITH_WCAN_Preset_Factory::list_presets();

			?>
			<p>
				<label>
					<strong><?php echo esc_html_x( 'Preset', '[ADMIN] Preset widget options', 'yith-woocommerce-ajax-navigation' ); ?>:</strong><br/>

					<?php if ( ! empty( $presets ) ) : ?>
						<select name="<?php echo esc_attr( $this->get_field_name( 'preset' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'preset' ) ); ?>" style="width: 100%;">
							<?php foreach ( $presets as $preset_slug => $preset_title ) : ?>
								<option value="<?php echo esc_attr( $preset_slug ); ?>" <?php selected( isset( $instance['preset'] ) && $instance['preset'] === $preset_slug ); ?>>
									<?php echo esc_attr( $preset_title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<?php
						// translators: 1. Url to Ajax Product filter admin panel.
						echo wp_kses_post( sprintf( _x( 'Please, go to <a href="%s">WP Dashboard -> YITH -> Ajax Product Filter -> Presets</a> and create your first preset', '[ADMIN] Preset widget options', 'yith-woocommerce-ajax-navigation' ), YITH_WCAN()->admin->get_panel_url() ) );
						?>
					<?php endif; ?>
				</label>
			</p>
			<?php
		}

		/**
		 * Save new options submitted by the user
		 *
		 * @param array $new_instance Current instance.
		 * @param array $old_instance Previous instance.
		 *
		 * @return array New instance.
		 * @since 4.0.0
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$preset_slug = sanitize_title_with_dashes( $new_instance['preset'] );
			$presets     = array_keys( YITH_WCAN_Preset_Factory::list_presets() );

			if ( in_array( $preset_slug, $presets, true ) ) {
				$instance['preset'] = $preset_slug;
			}

			return $instance;
		}

		/**
		 * Prints the widget.
		 *
		 * @param array $args     General params, coming from the sidebar.
		 * @param array $instance Current instance.
		 *
		 * @return void
		 * @since 4.0.0
		 */
		public function widget( $args, $instance ) {
			$title       = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$title       = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$preset_slug = ! empty( $instance['preset'] ) ? $instance['preset'] : '';

			if ( ! $preset_slug ) {
				return;
			}

			$preset = YITH_WCAN_Preset_Factory::get_preset( $preset_slug );

			if ( ! $preset ) {
				return;
			}

			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $title ) ) {
				echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo do_shortcode( '[yith_wcan_filters slug="' . $preset->get_slug() . '"]' );

			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
