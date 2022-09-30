<?php
/**
 * Widget
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership Premium
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBSL_Messages_Widget' ) ) {
	/**
	 * YITH_WCBSL_Messages_Widget
	 *
	 * @since  1.0.0
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBSL_Messages_Widget extends WC_Widget {
		/**
		 * Constructor
		 */
		public function __construct() {
			$this->widget_cssclass    = 'yith_wcmbs_messages_widget';
			$this->widget_description = __( 'Display message widget for members.', 'yith-woocommerce-membership' );
			$this->widget_id          = 'yith_wcmbs_messages_widget';
			$this->widget_name        = __( 'YITH Membership - Messages', 'yith-woocommerce-membership' );

			$this->settings = array(
				'title' => array(
					'type'  => 'text',
					'std'   => __( 'Messages', 'yith-woocommerce-membership' ),
					'label' => __( 'Title', 'yith-woocommerce-membership' ),
				),
			);

			parent::__construct();
		}

		public function widget( $args, $instance ) {
			wp_enqueue_script( 'yith_wcmbs_frontend_js' );

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			ob_start();

			$user_id = get_current_user_id();
			$member  = YITH_WCMBS_Members()->get_member( $user_id );
			$plans   = $member->get_membership_plans( array( 'return' => 'id' ) );

			$plans = apply_filters( 'yith_wcmbs_messages_widget_allowed_plans', $plans );

			if ( ! empty( $plans ) ) {
				$this->widget_start( $args, $instance );

				$messages_count = YITH_WCMBS_Messages_Manager_Frontend()->get_messages_count_by_user_id( $user_id );

				echo '<div id="yith-wcmbs-get-older-messages">' . esc_html__( 'Get older messages', 'yith-woocommerce-membership' ) . '</div>';

				echo '<div id="yith-wcmbs-widget-messages-list-wrapper">';
				echo wp_kses_post( apply_filters( 'yith_wcmbs_before_widget_messages_list', '<ul id="yith-wcmbs-widget-messages-list" data-messages-count="' . absint( $messages_count ) . '">' ) );

				YITH_WCMBS_Messages_Manager_Frontend()->get_messages_by_user_id( $user_id );

				echo wp_kses_post( apply_filters( 'yith_wcmbs_after_widget_messages_list', '</ul>' ) );
				echo '</div>';
				?>
				<textarea id="yith-wcmbs-message-to-send"></textarea>
				<input id="yith-wcmbs-send-button" type="button" class="button" value="<?php esc_attr_e( 'Send', 'yith-woocommerce-membership' ); ?>">
				<?php

				$this->widget_end( $args );
			}

			wp_reset_postdata();
			echo $this->cache_widget( $args, ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}