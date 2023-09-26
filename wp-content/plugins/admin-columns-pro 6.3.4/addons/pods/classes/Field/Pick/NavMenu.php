<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACA\Pods\Search;
use ACP;

class NavMenu extends Field\Pick {

	use Editing\DefaultServiceTrait;

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->get_db_value( $id ) as $term_id ) {
			$term = get_term( $term_id );

			if ( $term ) {
				$values[] = $term->name;
			}
		}

		return implode( ', ', $values );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function get_raw_value( $id ) {
		return $this->get_db_value( $id );
	}

	public function filtering() {
		return new Filtering\Pick( $this->column() );
	}

	public function search() {
		return new Search\Pick( $this->column()->get_meta_key(), $this->column()->get_meta_type(), $this->get_options() );
	}

	public function get_options() {
		$menus = get_terms( 'nav_menu', [ 'hide_empty' => true ] );

		if ( ! $menus || is_wp_error( $menus ) ) {
			return [];
		}

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}

}