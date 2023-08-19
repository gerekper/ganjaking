<?php

namespace ACP\Editing\Service\Comment;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Status extends Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Comment\Status() );
	}

	public function get_view( string $context ): ?View {
		return new View\Select( [
			'1'     => __( 'Approved' ),
			'0'     => __( 'Pending' ),
			'spam'  => __( 'Spam' ),
			'trash' => __( 'Trash' ),
		] );
	}

}