<?php

/**
 * Mocks out necessary properties to look like a term object after construction.
 */
class MockWpTerm {

	/**
	 * The term ID.
	 * @var int
	 */
	public $term_id;

	/**
	 * The term name.
	 * @var string
	 */
	public $name;

	/**
	 * The term slug.
	 *
	 * This will be auto-generated based on the during construction.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Initialise the mock with
	 * @param [type] $term_id [description]
	 * @param [type] $name    [description]
	 */
	public function __construct( $term_id, $name ) {
		$this->term_id = $term_id;
		$this->name = $name;
		$this->slug = strtolower( $name );
	}

	public function mock_set_gpf_config( $config ) {
		\WP_Mock::userFunction( 'get_term_meta', array(
			'args'   => [ $this->term_id, '_woocommerce_gpf_data', true ],
			'return' => $config,
		) );
	}
}
