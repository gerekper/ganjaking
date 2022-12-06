<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Service\Editability;
use ACP\Editing\Storage;
use ACP\Editing\View;

class AlternateText extends BasicStorage implements Editability {

	public function __construct() {
		parent::__construct( new Storage\Post\Meta( '_wp_attachment_image_alt' ) );
	}

	public function is_editable( int $id ): bool {
		return wp_attachment_is_image( $id );
	}

	public function get_not_editable_reason( int $id ): string {
		return __( 'Item is not an image.', 'codepress-admin-columns' );
	}

	public function get_view( string $context ): ?View {
		return new View\Text();
	}

}