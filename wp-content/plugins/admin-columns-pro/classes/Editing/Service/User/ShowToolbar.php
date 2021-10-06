<?php

namespace ACP\Editing\Service\User;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP\Editing;

class ShowToolbar extends Editing\Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Editing\Storage\User\Meta( 'show_admin_bar_front' ) );
	}

	public function get_view( $context ) {
		return new Editing\View\Toggle(
			new ToggleOptions(
				new Option( 'true', __( 'True', 'codepress-admin-columns' ) ),
				new Option( 'false', __( 'False', 'codepress-admin-columns' ) )
			)
		);
	}

}