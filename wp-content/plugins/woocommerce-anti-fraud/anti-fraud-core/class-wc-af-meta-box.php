<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Meta_Box' ) ) {

	class WC_AF_Meta_Box {

		public function __construct() {
			foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
				add_meta_box( 'woocommerce-af-risk', __( 'Fraud Risk', 'woocommerce-anti-fraud' ), array( $this, 'output' ), $type, 'side', 'high' );
			}
		}

		/**
		 * Output the metabox output
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function output() {

			// Post get must be set
			if ( ! isset( $_GET['post'] ) ) {
				return;
			}

			$order_id = $_GET['post'];

			// Create Score object and calculate score
			$score_points = get_post_meta( $order_id, 'wc_af_score', true );

			// Get meta
			$meta = WC_AF_Score_Helper::get_score_meta( $score_points, $order_id );

			// Check if there is an score order
			if ( '' != $score_points ) {

				// The label
				echo '<span class="mb-score-label" style="color:' . $meta['color'] . '">' . WC_AF_Score_Helper::invert_score( $score_points ) . ' % ' . $meta['label'] . '</span>' . PHP_EOL;

				// Circle points
				$circle_points = WC_AF_Score_Helper::invert_score( $score_points );

				// The circle
				echo '<input class="knob" data-fgColor="' . $meta['color'] . '" data-thickness=".4" data-readOnly=true value="0" rel="' . $circle_points . '">';

				// The rules
				$json_rules = get_post_meta( $order_id, 'wc_af_failed_rules', true );
				$whitelist_action = get_post_meta( $order_id, 'whitelist_action', true );
				$whitelist_action_style = ( $whitelist_action == 'user_email_whitelisted' ) ? 'style="color:grey"' : '';
				//echo '<pre>'; print_r($json_rules); echo '</pre>';

				// Failed Rules
				if ( is_array( $json_rules ) && ! empty( $json_rules ) ) {

					echo '<div class="woocommerce-af-risk-failure-list">' . PHP_EOL;

					echo '<ul>' . PHP_EOL;
					
					foreach ( $json_rules as $wc_af_failed_rule ) {
						$wc_af_failed_rule_decode = json_decode($wc_af_failed_rule,true);
						if ( $wc_af_failed_rule_decode['id'] == 'whitelist' ) {
							echo '<li class="failed" '.$whitelist_action_style.'>' . $wc_af_failed_rule_decode['label'] . '</li>' . PHP_EOL;
						}
					}

					foreach ( $json_rules as $json_rule ) {
						
						$rule = WC_AF_Rules::get()->get_rule_from_json( $json_rule );
						if ( ! is_a( $rule, 'WC_AF_Rule' ) ) {
							continue;
						}
						echo '<li class="failed" '.$whitelist_action_style.'>' . $rule->get_label() . '</li>' . PHP_EOL;
					}

					echo '</ul>' . PHP_EOL;
					//echo '<p><a href="#" data_id='.$order_id.' class="button button-primary test-fraud">' . __( 'Ajax Fraud Risk', 'woocommerce-anti-fraud' ) . '</a></p>' . PHP_EOL;
					echo '<a class="woocommerce-af-risk-failure-list-toggle" href="#" data-toggle="' . __( 'Hide details', 'woocommerce-anti-fraud' ) . '">' . __( 'Show fraud risk details', 'woocommerce-anti-fraud' ) . '</a>' . PHP_EOL;

					echo '</div>' . PHP_EOL;
				}

			} else {

				// Get order
				$order = wc_get_order( $order_id );

				// Check if we need to schedule an order
				if ( isset( $_GET['schedule_anti_fraud'] ) && ! WC_AF_Score_Helper::is_fraud_check_queued( $order_id ) ) {

					// Schedule fraud check
					$score_helper = new WC_AF_Score_Helper();
					$score_helper->schedule_fraud_check( $order_id );

					// Refetch order
					$order = wc_get_order( $order_id );
				}

				// Check if we're currently waiting for an audit
				if ( WC_AF_Score_Helper::is_fraud_check_queued( $order_id ) ) {

					echo '<p>' . __( 'This order is currently in queue for a fraud check.', 'woocommerce-anti-fraud' ) . '</p>' . PHP_EOL;

				} else {
					// No score found and order not scheduled for fraud check
					echo '<p>' . $meta['label'] . '</p>' . PHP_EOL;
					echo '<p><a href="' . admin_url( 'post.php?post=' . $_GET['post'] . '&action=edit&schedule_anti_fraud=1' ) . '" class="button button-primary">' . __( 'Calculate Fraud Risk', 'woocommerce-anti-fraud' ) . '</a></p>' . PHP_EOL;
				}

			}

		}

	}

}
