<?php

namespace Smush\Core\Integrations;

use Smush\Core\Helper;

/**
 * @method identify( int $user_id )
 * @method register( string $property, mixed $value )
 * @method registerAll( array $properties )
 * @method track( string $event, array $properties = array() )
 */
class Mixpanel {
	private $mixpanel;

	public function __construct( $project_token ) {
		$this->mixpanel = class_exists( '\Mixpanel' )
			? \Mixpanel::getInstance( $project_token, array(
				'error_callback' => array( $this, 'handle_error' ),
			) )
			: null;
	}

	public function handle_error( $code, $data ) {
		Helper::logger()->error( "$code: $data" );
	}

	public function __call( $name, $arguments ) {
		if ( method_exists( $this->mixpanel, $name ) ) {
			return call_user_func_array(
				array( $this->mixpanel, $name ),
				$arguments
			);
		}

		return null;
	}
}
