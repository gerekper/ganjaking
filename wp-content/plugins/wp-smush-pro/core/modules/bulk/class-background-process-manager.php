<?php

namespace Smush\Core\Modules\Bulk;

use Smush\Core\Modules\Background\Background_Process;
use Smush\Core\Modules\Background\Mutex;

class Background_Process_Manager {
	const ACTIVE_PROCESSES_EXPIRATION = DAY_IN_SECONDS;
	const ACTIVE_PROCESSES_KEY = 'wp_smush_bulk_smush_active_processes';
	const MAX_TASKS_PER_REQUEST = 8;

	private $is_multisite;
	private $current_site_id;

	public function __construct( $is_multisite, $current_site_id ) {
		$this->is_multisite    = $is_multisite;
		$this->current_site_id = $current_site_id;
	}

	public function create_process() {
		$identifier         = $this->make_process_identifier();
		$background_process = new Bulk_Smush_Background_Process( $identifier );
		$background_process->set_tasks_per_request( $this->calculate_tasks_per_request() );

		$this->register( $identifier );

		return $background_process;
	}

	public function register( $identifier ) {
		$register   = function ( $identifier ) {
			$this->register_active_process( $identifier );
		};
		$unregister = function ( $identifier ) {
			$this->unregister_process( $identifier );
		};

		add_action( "{$identifier}_started", $register );
		add_action( "{$identifier}_completed", $unregister );
		add_action( "{$identifier}_cancelled", $unregister );
	}

	private function make_process_identifier() {
		$identifier = 'wp_smush_bulk_smush_background_process';
		if ( $this->is_multisite ) {
			$post_fix   = "_" . $this->current_site_id;
			$identifier .= $post_fix;
		}

		return $identifier;
	}

	private function get_active_processes() {
		$active_processes = get_site_transient( self::ACTIVE_PROCESSES_KEY );

		return empty( $active_processes ) || ! is_array( $active_processes )
			? array()
			: $active_processes;
	}

	private function mutex( $operation ) {
		$mutex = new Mutex( self::ACTIVE_PROCESSES_KEY );
		$mutex->execute( $operation );
	}

	private function register_active_process( $identifier ) {
		$this->mutex( function () use ( $identifier ) {
			$active_processes                = $this->get_active_processes();
			$active_processes[ $identifier ] = $identifier;
			$this->set_active_processes( $active_processes );
		} );
	}

	private function unregister_process( $identifier ) {
		$this->mutex( function () use ( $identifier ) {
			$active_processes = $this->get_active_processes();
			unset( $active_processes[ $identifier ] );
			$this->set_active_processes( $active_processes );
		} );
	}

	private function set_active_processes( $active_processes ) {
		set_site_transient(
			self::ACTIVE_PROCESSES_KEY,
			array_unique( $active_processes ),
			self::ACTIVE_PROCESSES_EXPIRATION
		);
	}

	private function calculate_tasks_per_request() {
		$active_processes_count = count( $this->get_active_processes() );
		$should_limit           = $this->is_multisite && $active_processes_count > 1;

		if ( ! $should_limit ) {
			return Background_Process::TASKS_PER_REQUEST_UNLIMITED;
		}

		// Divide the available slots between the active processes
		$tasks_per_request = intval( floor( self::MAX_TASKS_PER_REQUEST / $active_processes_count ) );

		// At least 1 task per request
		return max( $tasks_per_request, 1 );
	}
}
