<?php
namespace ACP\Parser\FileEncode;

use AC\ListScreenCollection;
use ACP\Parser\FileEncode;

class PhpEncoder extends FileEncode {

	public function format( ListScreenCollection $listScreens ) {
		return sprintf( '<?php return %s; ?>', var_export( $this->encode->encode( $listScreens ), true ) );
	}

	public function get_file_type() {
		return 'php';
	}

}