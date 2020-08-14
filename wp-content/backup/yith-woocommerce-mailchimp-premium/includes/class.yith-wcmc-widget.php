<?php
/**
 * Subscription form widget class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Widget' ) ) {
	/**
	 * WooCommerce Mailchimp Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Widget extends WP_Widget {

		/**
		 * Sets up the widgets
		 *
		 * @return \YITH_WCMC_Widget
		 * @since 1.0.0
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'   => 'yith-wcmc-subscription-form',
				'description' => __( 'Display a Mailchimp subscription form in sidebars', 'yith-woocommerce-mailchimp' )
			);
			parent::__construct( 'yith-wcmc-subscription-form', __( 'YITH Mailchimp Subscription Form', 'yith-woocommerce-mailchimp' ), $widget_ops );
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
				<?php echo sprintf( __( 'You can customize options for <b>Mailchimp Subscription Form</b> widget from YITH WooCommerce Mailchimp <a href="%s">admin page</a>', 'yith-woocommerce-mailchimp' ), esc_url( add_query_arg( array(
					'page' => 'yith_wcmc_panel',
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

			$defaults = array(
				'title'                        => get_option( 'yith_wcmc_widget_title' ),
				'submit_label'                 => get_option( 'yith_wcmc_widget_submit_button_label' ),
				'success_message'              => get_option( 'yith_wcmc_widget_success_message' ),
				'show_privacy_field'           => get_option( 'yith_wcmc_widget_show_privacy_field' ),
				'privacy_label'                => get_option( 'yith_wcmc_widget_privacy_label' ),
				'hide_form_after_registration' => get_option( 'yith_wcmc_widget_hide_after_registration' ),
				'email_type'                   => get_option( 'yith_wcmc_widget_email_type', 'html' ),
				'double_optin'                 => get_option( 'yith_wcmc_widget_double_optin' ),
				'update_existing'              => get_option( 'yith_wcmc_widget_update_existing' ),
				'list'                         => get_option( 'yith_wcmc_widget_mailchimp_list' ),
				'groups'                       => implode( '#%,%#', get_option( 'yith_wcmc_widget_mailchimp_groups', array() ) ),
				'groups_to_prompt'             => implode( '#%,%#', get_option( 'yith_wcmc_widget_mailchimp_groups_selectable', array() ) ),
				'widget'                       => 'yes'
			);

			$selected_fields = get_option( 'yith_wcmc_widget_custom_fields' );
			$textual_fields  = '';

			if ( ! empty( $selected_fields ) ) {
				$first = true;
				foreach ( $selected_fields as $field ) {
					if ( ! $first ) {
						$textual_fields .= '|';
					}

					$textual_fields .= $field['name'] . ',' . $field['merge_var'];

					$first = false;
				}
			}

			$fields_default = array( 'fields' => $textual_fields );
			$defaults       = array_merge( $defaults, $fields_default );

			// add defaults for style
			$style_defaults = array(
				'enable_style'           => get_option( 'yith_wcmc_widget_style_enable' ),
				'round_corners'          => get_option( 'yith_wcmc_widget_subscribe_button_round_corners', 'no' ),
				'background_color'       => get_option( 'yith_wcmc_widget_subscribe_button_background_color' ),
				'text_color'             => get_option( 'yith_wcmc_widget_subscribe_button_color' ),
				'border_color'           => get_option( 'yith_wcmc_widget_subscribe_button_border_color' ),
				'background_hover_color' => get_option( 'yith_wcmc_widget_subscribe_button_background_hover_color' ),
				'text_hover_color'       => get_option( 'yith_wcmc_widget_subscribe_button_hover_color' ),
				'border_hover_color'     => get_option( 'yith_wcmc_widget_subscribe_button_border_hover_color' ),
				'custom_css'             => get_option( 'yith_wcmc_widget_custom_css' ),
			);

			$defaults         = array_merge( $defaults, $style_defaults );
			$textual_defaults = "";

			foreach ( $defaults as $field_id => $field_value ) {
				$textual_defaults .= "{$field_id}='{$field_value}' ";
			}

			echo apply_filters( 'yith_wcmc_before_subscription_form_widget', $before_widget );
			echo do_shortcode( "[yith_wcmc_subscription_form {$textual_defaults}]" );
			echo apply_filters( 'yith_wcmc_after_subscription_form_widget', $after_widget );
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