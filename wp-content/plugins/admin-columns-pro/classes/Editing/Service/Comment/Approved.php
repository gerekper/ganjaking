<?php

namespace ACP\Editing\Service\Comment;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Approved extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Comment\Field( 'comment_approved' ) );
	}

	public function get_view( $context ) {
		$options = new ToggleOptions(
			new Option( 0, __( 'Unapprove' ) ),
			new Option( 1, __( 'Approve' ) )
		);

		return new View\Toggle( $options );
	}

}