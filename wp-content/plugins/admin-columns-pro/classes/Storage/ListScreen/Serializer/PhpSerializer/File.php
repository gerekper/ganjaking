<?php

namespace ACP\Storage\ListScreen\Serializer\PhpSerializer;

use ACP\Storage\ListScreen\Serializer\PhpSerializer;

class File extends PhpSerializer {

	public function serialize( array $encoded_list_screen ) {
		return '<?php' . "\n\n" . 'return ' . parent::serialize( $encoded_list_screen ) . ';';
	}

}