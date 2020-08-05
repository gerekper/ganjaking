<?php

namespace Rocketgenius\Gravity_Forms\Settings\Fields;

use Rocketgenius\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

class HTML extends Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'html';





	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function markup()  {

		// Prepare markup.
		return rgobj( $this, 'html' );

	}

}

Fields::register( 'html', '\Rocketgenius\Gravity_Forms\Settings\Fields\HTML' );
