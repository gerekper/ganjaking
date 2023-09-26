<?php

namespace ACA\JetEngine\Editing;

use AC\MetaType;
use ACP;

trait EditableTrait {

	/**
	 * @return ACP\Editing\Service|false
	 */
	public function editing() {
		return ( new MetaServiceFactory() )->create( $this->field, new MetaType( $this->get_meta_type() ) );
	}

}