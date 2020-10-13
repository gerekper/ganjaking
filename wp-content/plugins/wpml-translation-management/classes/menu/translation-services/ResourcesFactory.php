<?php

namespace WPML\TM\Menu\TranslationServices;

class ResourcesFactory implements \IWPML_Backend_Action_Loader {
	/**
	 * @return Resources
	 */
	public function create() {
		return new Resources();
	}
}