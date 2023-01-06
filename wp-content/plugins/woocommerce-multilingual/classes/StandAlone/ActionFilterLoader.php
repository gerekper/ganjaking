<?php

namespace WCML\StandAlone;

use WPML_Action_Filter_Loader;

use function WCML\functions\isStandAlone;

class ActionFilterLoader {

	/**
	 * @var WPML_Action_Filter_Loader
	 */
	private $loader;

	public function __construct( WPML_Action_Filter_Loader $loader = null ) {
		$this->loader = null === $loader ? new WPML_Action_Filter_Loader() : $loader;
	}

	/**
	 * Load action filter limiting the loaders processed depending on whether WPML is installed
	 *
	 * @param string[] $loaders Action loaders.
	 */
	public function load( $loaders ) {
		$this->loader->load( $this->mayBeFilterLoaders( $loaders ) );
	}

	/**
	 * Only pass through IStandAloneAction loaders if WPML is not available.
	 *
	 * @param string[] $loaders Action loaders.
	 * @return string[] $loaders.
	 */
	private function mayBeFilterLoaders( $loaders ) {
		if ( isStandAlone() ) {
			$filtered_loaders = [];
			foreach ( $loaders as $loader ) {
				if ( is_subclass_of( $loader, IStandAloneAction::class ) ) {
					$filtered_loaders[] = $loader;
				}
			}
			return $filtered_loaders;
		}
		return $loaders;
	}
}
