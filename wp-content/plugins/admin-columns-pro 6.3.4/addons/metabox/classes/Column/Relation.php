<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Entity;
use ACP;

abstract class Relation extends AC\Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	/**
	 * @var Entity\Relation
	 */
	protected $relation;

	public function __construct() {
		$this->set_group( 'metabox_relation' )
		     ->set_label( __( 'Metabox Relation', 'codepress-admin-columns' ) );
	}

	public function set_relation( Entity\Relation $relation ) {
		$this->relation = $relation;
	}

	public function get_value( $id ) {
		$ids = $this->get_raw_value( $id );

		return empty( $ids )
			? $this->get_empty_char()
			: implode( ', ', array_map( [ $this, 'get_formatted_value' ], $ids ) );
	}

	public function get_raw_value( $id ) {
		return $this->relation->get_related_ids( $id );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}