<?php
/**
 * Abstract class for scheduling a recurring action.
 *
 * @package WC_Instagram/Abstracts
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Action.
 */
abstract class WC_Instagram_Action {

	/**
	 * Action prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'wc_instagram_action';

	/**
	 * Action name.
	 *
	 * @var string
	 */
	protected $action = 'scheduled_action';

	/**
	 * Action hook.
	 *
	 * @var string
	 */
	protected $hook;

	/**
	 * Action group.
	 *
	 * @var string
	 */
	protected $group = 'wc-instagram';

	/**
	 * The interval used for the scheduled action.
	 *
	 * @var mixed
	 */
	protected $interval;

	/**
	 * The arguments to pass to the scheduled action.
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $interval Optional. The interval used for the scheduled action. Default 300.
	 * @param array $args     Optional. The arguments to pass to the scheduled action. Default empty.
	 */
	public function __construct( $interval = 300, $args = array() ) {
		$this->interval = $interval;
		$this->args     = $args;

		$this->hook = $this->prefix . '_' . $this->action;

		add_action( 'init', array( $this, 'schedule' ) );
		add_action( $this->hook, array( $this, 'action' ) );
	}

	/**
	 * Gets the action hook.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_hook() {
		return $this->hook;
	}

	/**
	 * Gets the action group.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * Gets the action interval.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed
	 */
	public function get_interval() {
		/**
		 * Filters the action interval.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the action name.
		 *
		 * @since 4.0.0
		 *
		 * @oaram int $interval The action interval in seconds.
		 */
		return apply_filters( "wc_instagram_action_{$this->action}_interval", $this->interval );
	}

	/**
	 * Gets the action arguments.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function get_args() {
		/**
		 * Filters the action arguments.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the action name.
		 *
		 * @since 4.0.0
		 *
		 * @oaram array $args The action arguments.
		 */
		return apply_filters( "wc_instagram_action_{$this->action}_args", $this->args );
	}

	/**
	 * Schedules the action.
	 *
	 * @since 4.0.0
	 */
	public function schedule() {
		$transient_key = $this->get_transient_key();

		if ( 'yes' !== get_transient( $transient_key ) ) {
			if ( ! $this->get_next() ) {
				$this->schedule_recurring();
			}

			set_transient( $transient_key, 'yes', $this->get_interval() );
		}
	}

	/**
	 * Gets the date and time of the next scheduled occurrence of the action.
	 *
	 * @since 4.0.0
	 *
	 * @return WC_DateTime|null The date and time for the next occurrence, null otherwise.
	 */
	public function get_next() {
		return WC()->queue()->get_next( $this->hook, $this->get_args(), $this->group );
	}

	/**
	 * Un-schedules all events attached to the action.
	 *
	 * @since 4.0.0
	 */
	public function cancel() {
		WC()->queue()->cancel_all( $this->hook, $this->get_args(), $this->group );

		delete_transient( $this->get_transient_key() );
	}

	/**
	 * Schedules a recurring action.
	 *
	 * @since 4.0.0
	 */
	protected function schedule_recurring() {
		// Add a delay of one minute to the first run to avoid scheduling the action twice.
		WC()->queue()->schedule_recurring( time() + MINUTE_IN_SECONDS, $this->get_interval(), $this->hook, $this->get_args(), $this->group );
	}

	/**
	 * Gets the transient key.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	protected function get_transient_key() {
		return sprintf( '%s_checked', $this->hook );
	}

	/**
	 * Processes the action.
	 *
	 * @since 4.0.0
	 */
	abstract public function action();
}
