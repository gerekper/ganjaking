<?php

namespace ACP\Column\User;

use AC;
use ACP\Sorting;
use ACP\Sorting\Sortable;

class UserPosts extends AC\Column implements Sortable, AC\Column\AjaxValue {

	public function __construct() {
		$this->set_type( 'column-user_posts' )
		     ->set_label( __( 'Posts by Author', 'codepress-admin-columns' ) );
	}

	public function get_value( $user_id ) {
		$posts = $this->get_raw_value( $user_id );

		if ( empty( $posts ) ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%s item', '%s items', number_format_i18n( count( $posts ) ) ), count( $posts ) );

		return ac_helper()->html->get_ajax_toggle_box_link( $user_id, $count, $this->get_name(), __( 'Hide' ) );
	}

	public function get_ajax_value( $user_id ) {
		$posts = $this->get_raw_value( $user_id );
		$value = [];

		foreach ( $posts as $post_id ) {
			$value[] = $this->get_formatted_value( $post_id, $post_id );
		}

		return implode( ', ', $value );
	}

	private function get_selected_post_type() {
		return $this->get_setting( 'post_type' )->get_post_type();
	}

	/**
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_raw_value( $user_id ) {
		return get_posts( [
			'fields'         => 'ids',
			'author'         => $user_id,
			'post_type'      => $this->get_selected_post_type(),
			'posts_per_page' => -1,
			'post_status'    => [ 'publish', 'private' ],
		] );
	}

	public function sorting() {
		return new Sorting\Model\User\PostCount( $this->get_post_types(), [ 'publish', 'private' ] );
	}

	/**
	 * @return array
	 */
	private function get_post_types() {
		$post_type = $this->get_selected_post_type();

		if ( 'any' === $post_type ) {
			$post_type = get_post_types();
		}

		return (array) $post_type;
	}

	protected function register_settings() {
		$this->add_setting( new AC\Settings\Column\PostType( $this, true ) )
		     ->add_setting( new AC\Settings\Column\Post( $this ) );
	}

}