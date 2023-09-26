<?php

namespace ACA\JetEngine\Utils;

use Jet_Engine\Glossaries;
use Jet_Engine\Relations;
use Jet_Engine_Meta_Boxes;

final class Api {

	static function Relations(): Relations\Manager {
		return jet_engine()->relations;
	}

	static function MetaBox(): Jet_Engine_Meta_Boxes {
		return jet_engine()->meta_boxes;
	}

	static function GlossariesMeta(): Glossaries\Meta_Fields {
		return jet_engine()->glossaries->meta_fields;
	}

}