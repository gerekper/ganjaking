<?php

namespace ACP\Settings\Column;

use AC;
use AC\View;

class LinkCount extends AC\Settings\Column implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $link_count_type;

	protected function set_name() {
		$this->name = 'link_count_type';
	}

	protected function define_options() {
		return [ 'link_count_type' ];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-label', 'update' )
		               ->set_options( [
			               ''         => __( 'Total Links', 'codepress-admin-columns' ),
			               'internal' => __( 'Internal Links', 'codepress-admin-columns' ),
			               'external' => __( 'External Links', 'codepress-admin-columns' ),
		               ] );

		return new View( [
			'setting' => $select,
			'label'   => __( 'Type', 'codepress-admin-columns' ),
		] );
	}

	public function set_link_count_type( $type ) {
		$this->link_count_type = $type;
	}

	public function get_link_count_type() {
		return $this->link_count_type;
	}

	private function trim_tooltip_url( $url ) {
		return ac_helper()->string->trim_characters( $url, 26 );
	}

	private function remove_home_url_prefix( $url ) {
		return str_replace( home_url(), '', $url );
	}

	private function format_tooltip( array $urls ) {
		return ac_helper()->html->tooltip(
			count( $urls ),
			implode( '<br>', array_map( [ $this, 'trim_tooltip_url' ], $urls ) )
		);
	}

	public function format( $value, $original_value ) {
		switch ( $this->link_count_type ) {
			case 'internal' :
				if ( ! is_array( $value ) || empty( $value[0] ) ) {
					return false;
				}

				$urls = array_map( [ $this, 'remove_home_url_prefix' ], $value[0] );

				return $this->format_tooltip( $urls );
			case 'external' :
				if ( ! is_array( $value ) || empty( $value[1] ) ) {
					return false;
				}

				return $this->format_tooltip( $value[1] );
			default:
				if ( ! $value || ! is_array( $value ) ) {
					return false;
				}

				$urls = array_merge( ...$value );
				$urls = array_map( [ $this, 'remove_home_url_prefix' ], $urls );

				return $this->format_tooltip( $urls );
		}
	}

}