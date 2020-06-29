<?php

namespace ACP\Parser\FileDecode;

use ACP\Parser\FileDecode;
use SplFileInfo;

class PhpDecoder extends FileDecode {

	public function get_data_from_file( SplFileInfo $file ) {
		return ( require $file->getRealPath() );
	}

}