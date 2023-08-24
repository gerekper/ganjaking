<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Meta_Box' ) ) {

	/**
	 * Class for WC_AF_Meta_Box
	 */
	class WC_AF_Meta_Box {
		/**
		 * Class for construct
		 */
		public function __construct() {

			foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
				opmc_hpos_add_meta_box( 'woocommerce-af-risk', __( 'Fraud Risk', 'woocommerce-anti-fraud' ), array( $this, 'output' ), $type, 'side', 'high' );
			}
		}

		/**
		 * Output the metabox output
		 *
		 * @since  1.0.0
		 */
		public function output() {

			// Post get must be set.
			if ( ! isset( $_GET['post'] ) ) {
				return;
			}

			$order_id = sanitize_text_field( $_GET['post'] );

			// Create Score object and calculate score.
			$score_points = opmc_hpos_get_post_meta( $order_id, 'wc_af_score', true );

			// Get meta.
			$meta = WC_AF_Score_Helper::get_score_meta( $score_points, $order_id );

			// Check if there is an score order.
			if ( '' != $score_points ) {

				// The label.
				echo '<span class="mb-score-label" style="color:' . esc_attr( $meta['color'] ) . '">' . esc_attr( WC_AF_Score_Helper::invert_score( $score_points ) ) . ' % ' . esc_attr__( $meta['label'], 'woocommerce-anti-fraud' ) . '</span>' . PHP_EOL;

				// Circle points.
				$circle_points = WC_AF_Score_Helper::invert_score( $score_points );

				// The circle.
				echo '<input class="knob" data-fgColor="' . esc_attr( $meta['color'] ) . '" data-thickness=".4" data-readOnly=true value="0" rel="' . esc_attr( $circle_points ) . '">';

				// The rules
				$json_rules = opmc_hpos_get_post_meta( $order_id, 'wc_af_failed_rules', true );
				$whitelist_action = opmc_hpos_get_post_meta( $order_id, 'whitelist_action', true );
				$whitelist_action_style = ( 'user_email_whitelisted' == $whitelist_action ) ? 'style="color:grey"' : '';
				// echo '<pre>'; print_r($json_rules); echo '</pre>';.

				// Failed Rules.
				if ( is_array( $json_rules ) && ! empty( $json_rules ) ) {

					echo '<div class="woocommerce-af-risk-failure-list">' . PHP_EOL;

					echo '<ul>' . PHP_EOL;

					foreach ( $json_rules as $wc_af_failed_rule ) {
						$wc_af_failed_rule_decode = json_decode( $wc_af_failed_rule, true );
						if ( 'whitelist' == $wc_af_failed_rule_decode['id'] ) {
							echo '<li class="failed" ' . esc_attr( $whitelist_action_style ) . '>' . esc_attr( $wc_af_failed_rule_decode['label'] ) . '</li>' . PHP_EOL;
						}
					}

					foreach ( $json_rules as $json_rule ) {

						$rule = WC_AF_Rules::get()->get_rule_from_json( $json_rule );
						if ( ! is_a( $rule, 'WC_AF_Rule' ) ) {
							continue;
						}
						echo '<li class="failed" ' . esc_attr( $whitelist_action_style ) . '>' . esc_attr( $rule->get_label() ) . '</li>' . PHP_EOL;
					}

					echo '</ul>' . PHP_EOL;
					// echo '<p><a href="#" data_id='.$order_id.' class="button button-primary test-fraud">' . __( 'Ajax Fraud Risk', 'woocommerce-anti-fraud' ) . '</a></p>' . PHP_EOL;.
					echo '<a class="woocommerce-af-risk-failure-list-toggle" href="#" data-toggle="' . esc_html__( 'Hide details', 'woocommerce-anti-fraud' ) . '">' . esc_html__( 'Show fraud risk details', 'woocommerce-anti-fraud' ) . '</a>' . PHP_EOL;

					echo '</div>' . PHP_EOL;
				}
			} else {

				// Get order.
				$order = wc_get_order( $order_id );

				// Check if we need to schedule an order.
				if ( isset( $_GET['schedule_anti_fraud'] ) && ! WC_AF_Score_Helper::is_fraud_check_queued( $order_id ) ) {

					// Schedule fraud check.
					$score_helper = new WC_AF_Score_Helper();
					$score_helper->schedule_fraud_check( $order_id );

					// Refetch order.
					$order = wc_get_order( $order_id );
				}

				if ( isset( $_GET['cancel_schedule_anti_fraud'] ) && WC_AF_Score_Helper::is_fraud_check_queued( $order_id ) ) {

					// Schedule fraud check.
					$score_helper = new WC_AF_Score_Helper();
					$score_helper->cancel_schedule_fraud_check( $order_id );

					// Refetch order.
					$order = wc_get_order( $order_id );
				}

				// Check if we're currently waiting for an audit.
				if ( WC_AF_Score_Helper::is_fraud_check_queued( $order_id ) ) {

					echo '<p>' . esc_html__( 'This order is currently in queue for a fraud check.', 'woocommerce-anti-fraud' ) . '</p>' . PHP_EOL;
					echo '<p><a href="' . esc_url( admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&cancel_schedule_anti_fraud=1' ) ) . '" class="button button-danger">' . esc_html__( 'Cancel fraud check queue', 'woocommerce-anti-fraud' ) . '</a></p>' . PHP_EOL;

				} else {
					// No score found and order not scheduled for fraud check.
					echo '<p>' . esc_attr( $meta['label'], 'woocommerce-anti-fraud' ) . '</p>' . PHP_EOL;
					echo '<p><a href="' . esc_url( admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&schedule_anti_fraud=1' ) ) . '" class="button button-primary">' . esc_html__( 'Calculate Fraud Risk', 'woocommerce-anti-fraud' ) . '</a></p>' . PHP_EOL;
				}
			}

		}

	}

}
