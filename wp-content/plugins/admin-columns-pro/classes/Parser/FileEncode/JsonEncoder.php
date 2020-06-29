<?php
namespace ACP\Parser\FileEncode;

use AC\ListScreenCollection;
use ACP\Parser\FileEncode;

class JsonEncoder extends FileEncode {

	public function format( ListScreenCollection $listScreens ) {
		return (string) json_encode( $this->encode->encode( $listScreens ), JSON_PRETTY_PRINT );
	}

	public function get_file_type() {
		return 'json';
	}

}