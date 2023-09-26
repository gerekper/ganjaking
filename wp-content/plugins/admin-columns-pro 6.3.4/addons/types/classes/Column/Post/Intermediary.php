<?php

namespace ACA\Types\Column\Post;

use AC;
use ACA\Types\Search;
use ACA\Types\Settings;
use ACP;
use IToolset_Relationship_Definition;
use Toolset_Relationship_Definition_Repository;

class Intermediary extends AC\Column
	implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-types_relationship_intermediary' )
		     ->set_label( 'Toolset Types - Relationship' ) // Same name as other column, but this one is only used for Intermediary relationships
		     ->set_group( 'types' );
	}

	public function get_value( $id ) {
		$posts = $this->get_raw_value( $id );

		if ( empty( $posts ) ) {
			return $this->get_empty_char();
		}

		$values = [];
		foreach ( $posts as $post_id ) {
			$values[] = $this->get_formatted_value( $post_id, $post_id );
		}

		return implode( ',', $values );
	}

	public function get_raw_value( $id ) {
		return toolset_get_related_posts( $id, $this->get_post_type(), [ 'query_by_role' => 'intermediary', 'role_to_return' => $this->get_relationship_type(), 'limit' => -1 ] );
	}

	/**
	 * @param IToolset_Relationship_Definition $relationship
	 *
	 * @return bool|string
	 */
	private function get_relationship_type() {
		$post_type = $this->get_relationship_setting()->get_value();
		$relationship = toolset_get_relationship( $this->get_post_type() );

		return in_array( $post_type, $relationship['roles']['parent']['types'] )
			? 'parent'
			: 'child';
	}

	/**
	 * @return null|Settings\IntermediaryRelationship
	 */
	private function get_relationship_setting() {
		$setting = $this->get_setting( 'intermediary_relationship' );

		return $setting instanceof Settings\IntermediaryRelationship
			? $setting
			: null;
	}

	/**
	 * @return string
	 */
	private function get_current_post_type() {
		return ( 'parent' === $this->get_relationship_type() )
			? $this->get_relationship_object()->get_parent_type()->get_types()[0]
			: $this->get_relationship_object()->get_child_type()->get_types()[0];
	}

	/**
	 * @return IToolset_Relationship_Definition|null
	 */
	private function get_relationship_object() {
		$relationships = Toolset_Relationship_Definition_Repository::get_instance();

		return $relationships->get_definition( $this->get_post_type() );
	}

	public function is_valid() {
		if ( ! apply_filters( 'toolset_is_m2m_enabled', false ) ) {
			return false;
		}

		return ! empty( toolset_get_relationship( $this->get_post_type() ) );
	}

	public function register_settings() {
		$this->add_setting( new Settings\IntermediaryRelationship( $this ) );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Post\IntermediaryRelationship( $this->get_post_type(), $this->get_current_post_type(), $this->get_relationship_type() );
	}

}