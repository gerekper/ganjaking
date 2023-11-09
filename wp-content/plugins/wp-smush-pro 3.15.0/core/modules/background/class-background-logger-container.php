<?php

namespace Smush\Core\Modules\Background;

class Background_Logger_Container {
	private $logger;
	private $identifier;

	public function __construct( $identifier ) {
		$this->identifier = $identifier;
	}

	public function set_logger( $logger ) {
		$this->logger = $logger;
	}

	public function error( $message ) {
		$this->log( $message, 'error' );
	}

	public function notice( $message ) {
		$this->log( $message, 'notice' );
	}

	public function warning( $message ) {
		$this->log( $message, 'warning' );
	}

	public function info( $message ) {
		$this->log( $message, 'info' );
	}

	private function log( $message, $type ) {
		if ( $this->logger && method_exists( $this->logger, $type ) ) {
			$this->logger->$type(
				$this->prepare_message( $message )
			);
		}
	}

	private function prepare_message( $message ) {
		$identifier = $this->identifier;

		return "Background $identifier: $message";
	}
}