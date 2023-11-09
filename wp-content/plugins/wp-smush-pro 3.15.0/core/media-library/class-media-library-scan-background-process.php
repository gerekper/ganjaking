<?php

namespace Smush\Core\Media_Library;

use Smush\Core\Modules\Background\Background_Process;

class Media_Library_Scan_Background_Process extends Background_Process {
	/**
	 * @var Media_Library_Scanner
	 */
	private $scanner;

	public function __construct( $identifier, $scanner ) {
		parent::__construct( $identifier );
		$this->scanner = $scanner;
	}

	protected function task( $slice_id ) {
		$this->scanner->scan_library_slice( $slice_id );

		return true;
	}

	protected function attempt_restart_during_health_check() {
		return false;
	}
}