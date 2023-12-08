<?php

namespace ACA\BP\Helper\Select\Groups;

use AC;
use ACA\BP\Helper\Select\Groups\LabelFormatter\GroupName;
use BP_Groups_Group;

class Options extends AC\Helper\Select\Options {

	private $labels = [];

	/**
	 * @var LabelFormatter
	 */
	private $label_formatter;

	public function __construct( array $groups, $label_formatter = null  ) {
		$this->label_formatter = $label_formatter ?: new GroupName();

		array_map( [ $this, 'set_group' ], $groups );


		parent::__construct( $this->get_options() );
	}

	private function set_group( BP_Groups_Group $group ) {
		$this->labels[ $group->id ] = $this->label_formatter->format_label( $group );
	}

	private function get_options() {
		return self::create_from_array( $this->labels )->get_copy();
	}

}