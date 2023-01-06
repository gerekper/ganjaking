<?php

namespace WCML\Compatibility\WoobeBulkEditor;

use WCML\Compatibility\ComponentFactory;
use WCML_Woobe;
use function WCML\functions\getSitePress;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Woobe( getSitePress(), self::getPostTranslations() );
	}
}
