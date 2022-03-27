<?php

namespace ACP\Editing\ApplyFilter;

use AC;
use ACP\Editing\Service;

class View implements AC\ApplyFilter {

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

	public function __construct( AC\Column $column, $context, Service $service ) {
		$this->column = $column;
		$this->context = (string) $context;
		$this->service = $service;
	}

	public function apply_filters( $value ) {
		return apply_filters( 'acp/editing/view', $value, $this->column, $this->context, $this->service );
	}

}