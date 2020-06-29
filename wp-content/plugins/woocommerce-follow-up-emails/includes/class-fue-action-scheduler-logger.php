<?php

/**
 * Class FUE_ActionScheduler_Logger
 *
 * Stops Action Scheduler from saving logs as WP Comments
 */
class FUE_ActionScheduler_Logger extends ActionScheduler_wpCommentLogger {
	public function log( $action_id, $message, DateTime $date = null ) {
		// @codingStandardsIgnoreStart
		$groups = wp_get_post_terms( $action_id, 'action-group' );
		// @codingStandardsIgnoreEnd

		$is_fue = false;

		foreach ( $groups as $group ) {
			if ( 'fue' === $group->slug ) {
				$is_fue = true;
				break;
			}
		}

		if ( get_the_title( $action_id ) === 'fue_send_summary' ) {
			$is_fue = true;
		}

		if ( ! $is_fue ) {
			parent::log( $action_id, $message, $date );
		}
	}
}
