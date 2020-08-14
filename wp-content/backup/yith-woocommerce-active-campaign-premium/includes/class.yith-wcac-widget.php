<?php
/**
 * Subscription form widget class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAC_Widget' ) ) {
	/**
	 * WooCommerce Active Campaign Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Widget extends WP_Widget {

		/**
		 * Sets up the widgets
		 *
		 * @return \YITH_WCAC_Widget
		 * @since 1.0.0
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'   => 'yith-wcac-subscription-form',
				'description' => __( 'Display an Active Campaign subscription form in sidebars', 'yith-woocommerce-active-campaign' )
			);
			parent::__construct( 'yith-wcac-subscription-form', 'YITH Active Campaign Subscription Form', $widget_ops );
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function form( $instance ) {
			?>
			<p>
				<?php echo sprintf( __( 'You can customize the options of the <b>Active Campaign Subscription Form</b> widget from YITH WooCommerce Active Campaign <a href="%s">admin
page</a>', 'yith-woocommerce-active-campaign' ), esc_url( add_query_arg( array(
					'page' => 'yith_wcac_panel',
					'tab'  => 'widget'
				), admin_url( 'admin.php' ) ) ) ) ?>
			</p>
			<?php
		}

		/**
		 * Output the widget template
		 *
		 * @param $args     mixed Widget arguments
		 * @param $instance mixed Widget saved options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function widget( $args, $instance ) {
			extract( $args );

			$defaults = YITH_WCAC()->get_default_attributes( 'widget' );

			$textual_fields = '';

			if ( ! empty( $defaults['fields'] ) ) {
				$first = true;
				foreach ( $defaults['fields'] as $field ) {
					if ( ! $first ) {
						$textual_fields .= '|';
					}

					$textual_fields .= $field['name'] . ',' . $field['merge_var'];

					$first = false;
				}
			}

			$defaults['fields'] = $textual_fields;

			$textual_defaults = "";

			foreach ( $defaults as $field_id => $field_value ) {
				$textual_defaults .= $field_id . '="' . $field_value . '" ';
			}

			echo apply_filters( 'yith_wcac_before_subscription_form_widget', $before_widget );
			echo do_shortcode( "[yith_wcac_subscription_form " . $textual_defaults . "]" );
			echo apply_filters( 'yith_wcac_after_subscription_form_widget', $after_widget );
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 *
		 * @return array Instance to save
		 * @since 1.0.0
		 */
		public function update( $new_instance, $old_instance ) {
			return $new_instance;
		}
	}
}