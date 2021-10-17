<?php

namespace ACP\Search;

abstract class Filter {

	/**
	 * @var string
	 */
	protected $name;

	/** @var Comparison */
	protected $comparison;

	/** @var string */
	protected $label;

	/**
	 * @param string     $name
	 * @param Comparison $comparison
	 * @param string     $label
	 */
	public function __construct( $name, Comparison $comparison, $label ) {
		$this->name = $name;
		$this->comparison = $comparison;
		$this->label = $label;
	}

	public abstract function __invoke();

}