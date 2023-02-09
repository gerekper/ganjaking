<?php

/**
 * Class WPML_GFML_Filter_Country_Field
 */
class WPML_GFML_Filter_Country_Field {

	public function add_hooks() {
		add_filter( 'gform_countries', [ $this, 'fix_countries_order' ] );
	}

	/**
	 * @param array $countries
	 *
	 * @return array mixed
	 */
	public function fix_countries_order( $countries ) {
		sort( $countries );
		return $countries;
	}
}
