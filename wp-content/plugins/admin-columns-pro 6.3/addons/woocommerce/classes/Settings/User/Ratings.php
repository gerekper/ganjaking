<?php

namespace ACA\WC\Settings\User;

use AC;

/**
 * @since 3.0
 */
class Ratings extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $rating_display;

	protected function set_name() {
		$this->name = 'user_ratings';
	}

	protected function define_options() {
		return [
			'rating_display' => 'total',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_options( $this->get_display_options() );

		return new AC\View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	protected function get_display_options() {
		$options = [
			'total' => __( 'Number of ratings', 'codepress-admin-columns' ),
			'avg'   => __( 'Average rating', 'codepress-admin-columns' ),
		];

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_rating_display() {
		return $this->rating_display;
	}

	/**
	 * @param string $rating_display
	 */
	public function set_rating_display( $rating_display ) {
		$this->rating_display = $rating_display;
	}

	public function format( $value, $original_value ) {
		switch ( $this->get_rating_display() ) {
			case 'avg':
				if ( $value ) {
					$value = ac_helper()->html->tooltip( ac_helper()->html->stars( $value, 5 ), $value );
				}

				break;
			default:
				if ( $value ) {
					$comments_url = add_query_arg( [ 'user_id' => $original_value, 'post_type' => 'product' ], admin_url( 'edit-comments.php' ) );
					$value = ac_helper()->html->link( $comments_url, $value );
				}
		}

		return $value;
	}
}