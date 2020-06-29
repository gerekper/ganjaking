<?php


/**
 * Mocks out necessary properties to look like a taxonomy object after
 * construction.
 */
class MockWpTaxonomy {

	/**
	 * The taxonomy name.
	 * @var string
	 */
	public $name;

	/**
	 * The taxonomy label.
	 * @var string
	 */
	public $label;

	/**
	 * The taxonomy labels.
	 *
	 * Currently, only the name sub-property is populated.
	 *
	 * @var object
	 */
	public $labels;

	/**
	 * Constructor.
	 *
	 * Populate / calculate properties.
	 */
	public function __construct( $slug ) {
		$this->name = $slug;
		$this->label = ucfirst( str_replace( '_', ' ', $slug ) );
		$this->labels = new stdClass();
		$this->labels->name = $this->label;
	}
}
