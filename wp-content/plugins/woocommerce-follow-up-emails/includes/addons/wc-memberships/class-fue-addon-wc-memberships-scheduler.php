<?php

class FUE_Addon_WC_Memberships_Scheduler {

	public function __construct() {
		add_action( 'wc_memberships_user_membership_status_changed', array( $this, 'queue_status_emails' ), 10, 3 );
		add_action( 'wc_memberships_user_membership_created', array( $this, 'queue_membership_active_emails' ), 10, 2 );
		add_action( 'wc_memberships_user_membership_saved', array( $this, 'queue_membership_active_emails' ), 10, 2 );
		add_action( 'wc_memberships_grant_membership_access_from_purchase', array( $this, 'schedule_reminders_from_purchase' ), 10, 2 );
		add_filter( 'fue_skip_email_sending', array( $this, 'skip_sending_if_status_changed' ), 10, 3 );
		add_action( 'fue_record_order', array( $this, 'maybe_apply_membership' ) );
	}

	/**
	 * Return all emails with type of memberships given an interval type.
	 *
	 * @param string $interval_type Interval type to query.
	 * @return array
	 */
	private function get_membership_emails( $interval_type = '' ) {
		$meta_query = array(
			'meta_query'    => array(
				array(
					'key'   => '_prev_type',
					'value' => 'wc_memberships',
				),
			),
		);

		if ( ! empty( $interval_type ) ) {
			$meta_query['meta_query'][] = array(
				'key'   => '_interval_type',
				'value' => $interval_type,
			);
		}

		return fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, $meta_query );
	}

	/**
	 * Queue follow-ups when membership status changes
	 *
	 * @param WC_Memberships_User_Membership $membership
	 * @param string $old_status Old status, without the wcm- prefix
	 * @param string $new_status New status, without the wcm- prefix
	 */
	public function queue_status_emails( $membership, $old_status, $new_status ) {
		$emails = $this->get_membership_emails( 'wcm-' . $new_status );

		foreach ( $emails as $email ) {
			if ( ! empty( $email->meta['plan_id'] ) && $membership->get_plan_id() != $email->meta['plan_id'] ) {
				continue;
			}

			// look for duplicates
			$items = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'is_sent'   => 0,
				'email_id'  => $email->id,
				'user_id'   => $membership->get_user_id(),
			));

			if ( $this->membership_id_matches( $membership->get_id(), $items ) ) {
				continue;
			}

			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'user_id'       => $membership->get_user_id(),
				'email_id'      => $email->id,
				'meta'          => array( 'membership_id' => $membership->get_id() ),
			);
			FUE_Sending_Scheduler::queue_email( $insert, $email );
		}

		if ( 'active' == $new_status ) {
			$this->schedule_expiration_reminders( $membership );
		} elseif ( 'active' == $old_status ) {
			$this->clear_expiration_reminders( $membership );
		}
	}

	/**
	 * Trigger membership status emails after a new membership has been created
	 *
	 * @param WC_Memberships_Membership_Plan $plan
	 * @param array $membership_data
	 */
	public function queue_membership_active_emails( $plan, $membership_data ) {
		$membership = wc_memberships_get_user_membership( $membership_data['user_membership_id'] );
		$this->queue_status_emails( $membership, 'pending', $membership->get_status() );
	}

	/**
	 * Schedule reminder emails after access is granted from a product purchase
	 * @param WC_Memberships_Membership_Plan $plan
	 * @param array $data
	 */
	public function schedule_reminders_from_purchase( $plan, $data ) {
		$membership = wc_memberships_get_user_membership( $data['user_membership_id'] );

		$this->schedule_expiration_reminders( $membership );
	}

	/**
	 * Add reminder emails to the queue
	 *
	 * @param WC_Memberships_User_Membership $membership
	 */
	public function schedule_expiration_reminders( $membership ) {
		if ( ! is_a( $membership, 'WC_Memberships_User_Membership' ) ) {
			return;
		}

		$end_date = $membership->get_end_date( 'timestamp' );

		if ( current_time( 'timestamp', true ) > $end_date ) {
			return;
		}

		$emails = $this->get_membership_emails( 'membership_before_expire' );

		foreach ( $emails as $email ) {
			if ( ! empty( $email->meta['plan_id'] ) && $membership->get_plan_id() != $email->meta['plan_id'] ) {
				continue;
			}

			// look for duplicates
			$items = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'is_sent'   => 0,
				'email_id'  => $email->id,
				'user_id'   => $membership->get_user_id(),
			));

			if ( $this->membership_id_matches( $membership->get_id(), $items ) ) {
				continue;
			}

			// add this email to the queue
			$interval   = (int) $email->interval_num;
			$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
			$send_on    = $end_date - $add;

			// do not queue emails where the send date is in the past
			if ( $send_on < current_time( 'timestamp', true ) ) {
				continue;
			}

			$insert = array(
				'send_on'       => $send_on,
				'user_id'       => $membership->get_user_id(),
				'email_id'      => $email->id,
				'meta'          => array( 'membership_id' => $membership->get_id() ),
			);
			FUE_Sending_Scheduler::queue_email( $insert, $email );
		}
	}

	/**
	 * Remove expiration reminder emails when a previously active membership becomes inactive
	 * @param WC_Memberships_User_Membership $membership
	 */
	public function clear_expiration_reminders( $membership ) {
		$emails = $this->get_membership_emails( 'membership_before_expire' );

		foreach ( $emails as $email ) {
			$items = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email->id,
				'user_id'   => $membership->get_user_id(),
			));

			foreach ( $items as $item ) {
				if (
					!empty( $item->meta['membership_id'] ) &&
					$item->meta['membership_id'] == $membership->get_id()
				) {
					Follow_Up_Emails::instance()->scheduler->delete_item( $item->id );
				}
			}
		}
	}

	/**
	 * Do not send email if the status has changed from the time it was queued
	 *
	 * @param bool      $skip
	 * @param FUE_Email $email
	 * @param object    $queue_item
	 *
	 * @return bool
	 */
	public function skip_sending_if_status_changed( $skip, $email, $queue_item ) {
		if (
			$email->type != 'wc_memberships' ||
			$skip ||
			empty( $queue_item->meta['membership_id'] )
		) {
			return $skip;
		}

		$membership = wc_memberships_get_user_membership( $queue_item->meta['membership_id'] );

		if ( $membership ) {
			if ( $this->membership_status_changed( $membership, $email ) ) {
				$skip = true;
			}

			if ( $skip ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
			}
		}

		return $skip;
	}

	private function membership_id_matches( $membership_id, $items ) {
		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				if ( ! empty( $item->meta['membership_id'] ) && $item->meta['membership_id'] == $membership_id ) {
					return true;
				}
			}
		}

		return false;
	}

	private function membership_status_changed( $membership, $email ) {
		$current_status = $membership->get_status();
		$changed        = false;

		if ( 'membership_before_expire' == $email->trigger && 'active' != $current_status ) {
			$changed = true;
		} else {
			$statuses = wc_memberships_get_user_membership_statuses();
			foreach ( array_keys( $statuses ) as $status ) {
				$trimmed_status = str_replace( 'wcm-', '', $status );
				if ( $email->trigger == $status && $trimmed_status != $current_status ) {
					$changed = true;
					break;
				}
			}
		}

		return $changed;
	}

	public function maybe_apply_membership( $order_id ) {
		$granted_memberships = wc_memberships_get_order_access_granted_memberships( $order_id );

		// this order grants a membership, attempt to schedule
		if ( ! empty( $granted_memberships ) ) {
			foreach ( array_keys( $granted_memberships ) as $user_membership_id ) {
				$user_membership = wc_memberships_get_user_membership( $user_membership_id );

				// maybe schedule, since this function has duplicate checks
				$this->schedule_expiration_reminders( $user_membership );
			}
		}
	}
}
