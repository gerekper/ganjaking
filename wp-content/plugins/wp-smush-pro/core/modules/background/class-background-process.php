<?php

namespace Smush\Core\Modules\Background;

/**
 * Abstract WP_Background_Process class.
 *
 * @abstract
 * @extends Async_Request
 */
abstract class Background_Process extends Async_Request {
	const TASKS_PER_REQUEST_UNLIMITED = - 1;

	/**
	 * Start time of current process.
	 *
	 * (default value: 0)
	 *
	 * @var int
	 * @access protected
	 */
	private $start_time = 0;

	/**
	 * Cron_hook_identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	private $cron_hook_identifier;

	/**
	 * Cron_interval_identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	private $cron_interval_identifier;

	/**
	 * @var Background_Logger_Container
	 */
	private $logger_container;

	/**
	 * @var Background_Process_Status
	 */
	private $status;
	/**
	 * @var Background_Utils
	 */
	private $utils;

	private $tasks_per_request = self::TASKS_PER_REQUEST_UNLIMITED;

	/**
	 * Initiate new background process
	 */
	public function __construct( $identifier ) {
		parent::__construct( $identifier );

		$this->cron_hook_identifier     = $this->identifier . '_cron';
		$this->cron_interval_identifier = $this->identifier . '_cron_interval';

		add_action( $this->cron_hook_identifier, array( $this, 'handle_cron_healthcheck' ) );
		add_filter( 'cron_schedules', array( $this, 'schedule_cron_healthcheck' ) );

		$this->logger_container = new Background_Logger_Container( $this->identifier );
		$this->status           = new Background_Process_Status( $this->identifier );
		$this->utils            = new Background_Utils();
	}

	private function generate_instance_id() {
		return md5( microtime() . rand() );
	}

	/**
	 * Dispatch
	 *
	 * @access public
	 * @return array|\WP_Error
	 */
	public function dispatch( $instance_id ) {
		$this->logger()->info( "Dispatching a new request for instance $instance_id." );

		// Schedule the cron healthcheck.
		$this->schedule_event();

		// Perform remote post.
		return parent::dispatch( $instance_id );
	}

	public function spawn() {
		$instance_id = $this->generate_instance_id();

		$this->logger()->info( "Spawning a brand new instance (ID: $instance_id) for the process." );

		$this->set_active_instance_id( $instance_id );
		$this->dispatch( $instance_id );
	}

	/**
	 * Update queue
	 *
	 * @param array $tasks An array of tasks.
	 */
	private function update_queue( $tasks ) {
		if ( ! empty( $tasks ) ) {
			update_site_option( $this->get_queue_key(), $tasks );
		}
	}

	/**
	 * Delete queue
	 */
	private function delete_queue() {
		delete_site_option( $this->get_queue_key() );
	}

	/**
	 * Generate key
	 *
	 * Generates a unique key based on microtime. Queue items are
	 * given a unique key so that they can be merged upon save.
	 *
	 * @return string
	 */
	protected function get_queue_key() {
		return $this->identifier . '_queue';
	}

	/**
	 * Maybe process queue
	 *
	 * Checks whether data exists within the queue and that
	 * the process is not already running.
	 */
	public function maybe_handle() {
		// Don't lock up other requests while processing
		session_write_close();

		$this->mutex( function () {
			$instance_id = empty( $_GET['instance_id'] )
				? false
				: $_GET['instance_id'];

			if ( $this->is_queue_empty() ) {
				$this->logger()->warning( "Handler called with instance ID $instance_id but the queue is empty. Killing this instance." );

				return;
			}

			if ( ! $instance_id || ! $this->is_active_instance( $instance_id ) ) {
				// We thought the process died, so we spawned a new instance.
				// Kill this instance and let the new one continue.
				$active_instance_id = $this->get_active_instance_id();
				$this->logger()->warning( "Handler called with instance ID $instance_id but the active instance ID is $active_instance_id. Killing $instance_id so $active_instance_id can continue." );

				return;
			}

			if ( ! check_ajax_referer( $this->identifier, 'nonce', false ) ) {
				return;
			}

			$this->handle( $instance_id );
		} );

		wp_die();
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	protected function is_queue_empty() {
		return empty( $this->get_queue() );
	}

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	protected function is_process_running() {
		if ( get_site_transient( $this->get_last_run_transient_key() ) ) {
			// Process already running.
			return true;
		}

		return false;
	}

	protected function update_timestamp( $instance_id ) {
		$timestamp        = time();
		$this->start_time = $timestamp; // Set start time of current process.
		set_site_transient(
			$this->get_last_run_transient_key(),
			$timestamp,
			$this->get_instance_expiry_duration()
		);

		$human_readable_timestamp = wp_date( 'Y-m-d H:i:s', $timestamp );
		$this->logger()->info( "Setting last run timestamp for instance ID $instance_id to $human_readable_timestamp" );
	}

	/**
	 * Get queue
	 *
	 * @return array Return the first queue from the queue
	 */
	protected function get_queue() {
		$queue = $this->utils->get_site_option( $this->get_queue_key(), array() );

		return empty( $queue ) || ! is_array( $queue )
			? array()
			: $queue;
	}

	/**
	 * Handle
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 */
	protected function handle( $instance_id ) {
		$this->logger()->info( "Handling instance ID $instance_id." );
		$this->update_timestamp( $instance_id );

		$queue                 = $this->get_queue();
		$processed_tasks_count = 0;

		foreach ( $queue as $key => $value ) {
			$this->logger()->info( "Executing task $value." );
			$task = $this->task( $value );
			if ( $task ) {
				$this->status->task_successful();
			} else {
				$this->status->task_failed();
			}

			if ( $this->status->is_cancelled() ) {
				$this->logger()->info( "While we were busy doing the task $value, the process got cancelled. Clean up and stop." );

				return;
			}

			unset( $queue[ $key ] );

			$processed_tasks_count ++;
			if ( $this->task_limit_reached( $processed_tasks_count ) ) {
				$tasks_per_request = $this->get_tasks_per_request();
				$this->logger()->info( "Stopping because we are only supposed to perform $tasks_per_request tasks in a single request and we have reached that limit." );

				break;
			}

			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				$this->logger()->warning( "Time/Memory limits reached, save the queue and dispatch a new request." );
				break;
			}
		}

		if ( empty( $queue ) ) {
			$this->complete();
		} else {
			$this->update_queue( $queue );
			$this->dispatch( $instance_id );
		}
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;

		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_memory_exceeded', $return );
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return intval( $memory_limit ) * 1024 * 1024;
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the process never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + $this->get_time_limit();
		$return = false;

		if ( time() >= $finish ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_time_exceeded', $return );
	}

	/**
	 * Complete.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		$this->logger()->info( "Process completed." );
		$this->cleanup();
		$this->status->complete();

		$this->do_action( 'completed' );
	}

	/**
	 * Schedule cron healthcheck
	 *
	 * @access public
	 *
	 * @param mixed $schedules Schedules.
	 *
	 * @return mixed
	 */
	public function schedule_cron_healthcheck( $schedules ) {
		$interval = $this->get_cron_interval_seconds();

		// Adds every 5 minutes to the existing schedules.
		$schedules[ $this->identifier . '_cron_interval' ] = array(
			'interval' => $interval,
			/* translators: %s: Cron interval in minutes */
			'display'  => sprintf( __( 'Every %d Minutes', 'wp-smushit' ), $interval / MINUTE_IN_SECONDS ),
		);

		return $schedules;
	}

	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		$this->logger()->info( "Running scheduled health check." );

		if ( $this->is_process_running() ) {
			$this->logger()->info( "Health check: Process seems healthy, no action required." );
			exit;
		}

		if ( $this->is_queue_empty() ) {
			$this->logger()->info( "Health check: Process not in progress but the queue is empty, no action required." );
			$this->clear_scheduled_event();
			exit;
		}

		if ( $this->status->is_cancelled() ) {
			$this->logger()->info( "Health check: Process has been cancelled already, no action required." );
			$this->clear_scheduled_event();
			exit;
		}

		$this->logger()->warning( "Health check: Process instance seems to have died. Spawn a new instance." );
		$this->spawn();

		exit;
	}

	/**
	 * Schedule event
	 */
	protected function schedule_event() {
		$hook = $this->cron_hook_identifier;
		if ( ! wp_next_scheduled( $hook ) ) {
			$interval = $this->cron_interval_identifier;
			$next_run = time() + $this->get_cron_interval_seconds();
			wp_schedule_event( $next_run, $interval, $hook );

			$this->logger()->info( "Scheduling new event with hook $hook to run $interval." );
		}
	}

	/**
	 * Clear scheduled event
	 */
	protected function clear_scheduled_event() {
		$hook = $this->cron_hook_identifier;
		$this->logger()->info( "Cancelling event with hook $hook." );
		wp_clear_scheduled_hook( $hook );
	}

	/**
	 * Cancel Process
	 *
	 * Stop processing queue items, clear cronjob and delete queue.
	 */
	private function cancel_process() {
		$this->cleanup();
		$this->logger()->info( "Process cancelled." );
	}

	public function cancel() {
		// Update the cancel flag first
		$active_instance_id = $this->get_active_instance_id();
		$this->logger()->info( "Starting cancellation (Instance: $active_instance_id)." );
		$this->status->cancel();

		// Since actual cancellation involves deletion of the queue and the handler
		// might be in the middle of updating the queue, we need to use a mutex
		$mutex = new Mutex( $this->get_handler_mutex_id() );
		$mutex
			->set_break_on_timeout( false ) // Since this is a user operation, we must cancel, even if there is a timeout
			->set_timeout( $this->get_time_limit() ) // Shouldn't take more time than the time allocated to the process itself
			->execute( function () use ( $active_instance_id ) {
				$this->logger()->info( "Cancelling the process (Instance: $active_instance_id)." );
				$this->cancel_process();
				$this->logger()->info( "Cancellation completed (Instance: $active_instance_id)." );

				$this->do_action( 'cancelled' );
			} );
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $task Queue item to iterate over.
	 *
	 * @return mixed
	 */
	abstract protected function task( $task );

	private function is_active_instance( $instance_id ) {
		return $instance_id === $this->get_active_instance_id();
	}

	/**
	 * Save the unique ID of the process we are presuming to be dead, so we can prevent it from coming back.
	 *
	 * @param $instance_id
	 *
	 * @return void
	 */
	private function set_active_instance_id( $instance_id ) {
		update_site_option( $this->get_active_instance_option_id(), $instance_id );
	}

	private function get_active_instance_id() {
		return get_site_option( $this->get_active_instance_option_id(), '' );
	}

	private function get_active_instance_option_id() {
		return $this->identifier . '_active_instance';
	}

	public function set_logger( $logger ) {
		$this->logger_container->set_logger( $logger );
	}

	/**
	 * @return Background_Logger_Container
	 */
	private function logger() {
		return $this->logger_container;
	}

	public function get_status() {
		return $this->status;
	}

	/**
	 * @param $tasks array
	 *
	 * @return void
	 */
	public function start( $tasks ) {
		$total_items = count( $tasks );
		$this->status->start( $total_items );

		$this->logger()->info( "Starting new process with $total_items tasks" );

		$this->update_queue( $tasks );
		$this->spawn();

		$this->do_action( 'started' );
	}

	private function mutex( $operation ) {
		$mutex = new Mutex( $this->get_handler_mutex_id() );
		$mutex->set_break_on_timeout( true ) // Let the previous handler do its thing 
		      ->set_timeout( $this->get_lock_duration() )
		      ->execute( $operation );
	}

	private function get_handler_mutex_id() {
		return $this->identifier . '_handler_lock';
	}

	private function get_time_limit() {
		return apply_filters( $this->identifier . '_default_time_limit', 20 ); // 20 seconds
	}

	private function get_lock_duration() {
		$lock_duration = ( property_exists( $this, 'queue_lock_time' ) ) ? $this->queue_lock_time : 60; // 1 minute

		return apply_filters( $this->identifier . '_queue_lock_time', $lock_duration );
	}

	private function get_instance_expiry_duration() {
		return apply_filters( $this->identifier . '_instance_expiry_time', 60 * 2 ); // 2 minutes
	}

	private function get_last_run_transient_key() {
		return $this->identifier . '_last_run';
	}

	private function clear_last_run_timestamp() {
		delete_site_transient( $this->get_last_run_transient_key() );
	}

	private function cleanup() {
		// Delete options and transients
		$this->delete_queue();
		delete_site_option( $this->get_active_instance_option_id() );
		$this->clear_last_run_timestamp();

		// Cancel all events
		$this->clear_scheduled_event();
	}

	private function task_limit_reached( $processed_tasks_count ) {
		if ( $this->get_tasks_per_request() === self::TASKS_PER_REQUEST_UNLIMITED ) {
			return false;
		}

		return $processed_tasks_count >= $this->get_tasks_per_request();
	}

	public function get_tasks_per_request() {
		return $this->tasks_per_request;
	}

	/**
	 * @param int $tasks_per_request
	 */
	public function set_tasks_per_request( $tasks_per_request ) {
		$this->tasks_per_request = $tasks_per_request;
	}

	private function do_action( $action ) {
		do_action( "{$this->identifier}_$action", $this->identifier, $this );
	}

	private function get_cron_interval_seconds() {
		$minutes = property_exists( $this, 'cron_interval' )
			? $this->cron_interval
			: 5;

		$interval = apply_filters( $this->identifier . '_cron_interval', $minutes );

		return $interval * MINUTE_IN_SECONDS;
	}
}
