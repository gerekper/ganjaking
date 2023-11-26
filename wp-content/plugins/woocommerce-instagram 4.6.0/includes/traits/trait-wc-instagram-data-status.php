<?php
/**
 * Data status.
 *
 * Handles the status of a data object.
 *
 * @package WC_Instagram/Traits
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Instagram_Data_Status.
 */
trait WC_Instagram_Data_Status {

	/**
	 * Stores data about status changes so relevant hooks can be fired.
	 *
	 * @var bool|array
	 */
	protected $status_transition = false;

	/**
	 * Gets the catalog status.
	 *
	 * @since 4.0.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Sets the status.
	 *
	 * @since 4.0.0
	 *
	 * @param string $status New status.
	 */
	public function set_status( $status ) {
		$old_status = $this->get_status();

		$this->set_prop( 'status', $status );

		$this->set_status_transition(
			array(
				'from' => $old_status,
				'to'   => $status,
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Status transition
	|--------------------------------------------------------------------------
	|
	| Methods for handling the status transition.
	*/

	/**
	 * Sets the status transition.
	 *
	 * @since 4.0.0
	 *
	 * @param array $transition An array representing the status transition.
	 */
	protected function set_status_transition( $transition ) {
		if ( false === $this->object_read || empty( $transition['from'] ) || $transition['from'] === $transition['to'] ) {
			return;
		}

		$this->status_transition = array(
			'from' => ( ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $transition['from'] ),
			'to'   => $transition['to'],
		);
	}

	/**
	 * Handles the status transition.
	 *
	 * @since 4.0.0
	 */
	protected function status_transition() {
		if ( ! $this->status_transition ) {
			return;
		}

		$transition = $this->status_transition;

		// Reset status transition.
		$this->status_transition = false;

		/**
		 * Fires when the status of the object changes.
		 *
		 * @since 4.0.0
		 *
		 * @param mixed  $object Data object.
		 * @param string $from   The status transition from.
		 * @param string $to     The status transition to.
		 */
		do_action( "wc_{$this->object_type}_status_changed", $this, $transition['from'], $transition['to'] );
	}

	/**
	 * Saves the progressive discount.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function save() {
		parent::save();

		$this->status_transition();

		return $this->get_id();
	}
}
