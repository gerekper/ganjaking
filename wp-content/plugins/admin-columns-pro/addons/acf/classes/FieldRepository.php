<?php

namespace ACA\ACF;

use AC\ListScreen;

class FieldRepository {

	private $query_factory;

	public function __construct( FieldGroup\QueryFactory $query_factory ) {
		$this->query_factory = $query_factory;
	}

	public function find_by_list_screen( ListScreen $list_screen ): array {
		$group_query = $this->query_factory->create( $list_screen );

		if ( ! $group_query instanceof FieldGroup\Query ) {
			return [];
		}

		do_action( 'acp/acf/before_get_field_options', $list_screen );
		$groups = $group_query->get_groups();
		do_action( 'acp/acf/after_get_field_options', $list_screen );

		if ( ! $groups ) {
			return [];
		}

		$fields = array_map( 'acf_get_fields', $groups );

		return array_filter( array_merge( ...$fields ) );
	}

}