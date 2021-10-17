<?php

namespace ACP\ListScreen;

use AC;
use AC\WpListTableFactory;
use ACP\Column;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;
use ReflectionException;
use WP_Term;
use WP_Terms_List_Table;

class Taxonomy extends AC\ListScreenWP
	implements Editing\ListScreen, Export\ListScreen, Filtering\ListScreen, Sorting\ListScreen {

	/**
	 * @var string Taxonomy name
	 */
	private $taxonomy;

	/**
	 * Constructor
	 *
	 * @param $taxonomy
	 *
	 * @since 1.2.0
	 */
	public function __construct( $taxonomy ) {
		$this->set_taxonomy( $taxonomy )
		     ->set_meta_type( AC\MetaType::TERM )
		     ->set_screen_base( 'edit-tags' )
		     ->set_screen_id( 'edit-' . $taxonomy )
		     ->set_key( 'wp-taxonomy_' . $taxonomy )
		     ->set_group( 'taxonomy' );
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return self
	 */
	protected function set_taxonomy( $taxonomy ) {
		$this->taxonomy = (string) $taxonomy;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * @see WP_Terms_List_Table::column_default
	 */
	public function set_manage_value_callback() {
		add_action( "manage_" . $this->get_taxonomy() . "_custom_column", [ $this, 'manage_value' ], 10, 3 );
	}

	/**
	 * @return WP_Terms_List_Table
	 */
	protected function get_list_table() {
		return ( new WpListTableFactory() )->create_taxonomy_table( $this->get_screen_id() );
	}

	/**
	 * @param int $term_id
	 *
	 * @return WP_Term
	 * @since 4.0
	 */
	protected function get_object( $term_id ) {
		return get_term_by( 'id', $term_id, $this->get_taxonomy() );
	}

	/**
	 * @return string|false
	 */
	public function get_label() {
		return $this->get_taxonomy_label_var( 'name' );
	}

	/**
	 * @return false|string
	 */
	public function get_singular_label() {
		return $this->get_taxonomy_label_var( 'singular_name' );
	}

	/**
	 * @param $wp_screen
	 *
	 * @return bool
	 * @since 3.7.3
	 */
	public function is_current_screen( $wp_screen ) {
		return parent::is_current_screen( $wp_screen ) && $this->get_taxonomy() === filter_input( INPUT_GET, 'taxonomy' );
	}

	/**
	 * Get screen link
	 * @return string Link
	 * @since 1.2.0
	 */
	public function get_screen_link() {
		$post_type = null;

		if ( $object_type = $this->get_taxonomy_var( 'object_type' ) ) {
			if ( post_type_exists( $object_type[0] ) ) {
				$post_type = $object_type[0];
			}
		}

		return add_query_arg( [ 'taxonomy' => $this->get_taxonomy(), 'post_type' => $post_type ], parent::get_screen_link() );
	}

	/**
	 * Manage value
	 *
	 * @param string $value
	 * @param string $column_name
	 * @param int    $term_id
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function manage_value( $value, $column_name, $term_id ) {
		return $this->get_display_value_by_column_name( $column_name, $term_id, $value );
	}

	/**
	 * @param $var
	 *
	 * @return string|false
	 */
	private function get_taxonomy_label_var( $var ) {
		$taxonomy = get_taxonomy( $this->get_taxonomy() );

		return $taxonomy && isset( $taxonomy->labels->{$var} ) ? $taxonomy->labels->{$var} : false;
	}

	private function get_taxonomy_var( $var ) {
		$taxonomy = get_taxonomy( $this->get_taxonomy() );

		return $taxonomy && isset( $taxonomy->{$var} ) ? $taxonomy->{$var} : false;
	}

	/**
	 * @throws ReflectionException
	 */
	protected function register_column_types() {
		$this->register_column_type( new Column\CustomField );
		$this->register_column_type( new Column\Actions );
		$this->register_column_types_from_dir( 'ACP\Column\Taxonomy' );
	}

	public function editing() {
		return new Editing\Strategy\Taxonomy( $this->taxonomy );
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\Taxonomy( $model );
	}

	public function sorting( $model ) {
		return new Sorting\Strategy\Taxonomy( $model, $this->get_taxonomy() );
	}

	public function export() {
		return new Export\Strategy\Taxonomy( $this );
	}

}