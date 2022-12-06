<?php

namespace ACP\Editing\ApplyFilter;

use AC;
use AC\ApplyFilter;
use ACP;
use ACP\Editing;
use ACP\Editing\Service;

class View implements ApplyFilter {

	/**
	 * @var AC\Column
	 */
	private $column;

	/**
	 * @var string
	 */
	private $context;

	/**
	 * @var Service
	 */
	private $service;

	public function __construct( AC\Column $column, string $context, Service $service ) {
		$this->column = $column;
		$this->context = $context;
		$this->service = $service;
	}

	/**
	 * @param Editing\View|null $value
	 *
	 * @return Editing\View|null
	 */
	public function apply_filters( $value ) {
		$value = apply_filters( 'acp/editing/view', $value, $this->column, $this->context, $this->service );

		return $value instanceof Editing\View
			? $value
			: null;
	}

}