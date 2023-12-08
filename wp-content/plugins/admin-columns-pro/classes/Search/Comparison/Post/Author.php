<?php

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Helper\Select\User\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Author extends PostField
	implements SearchableValues {

	private $post_type;

	public function __construct( string $post_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::CURRENT_USER,
		] );

		$this->post_type = $post_type;

		parent::__construct( $operators );
	}

	private function formatter(): UserName {
		return new UserName();
	}

	public function format_label( $value ): string {
		return $value ? $this->formatter()->format_label( get_userdata( $value ) ): '';
	}

	protected function get_field(): string {
		return 'post_author';
	}

	private function get_author_ids( string $post_type ): array {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_type = %s;", $post_type ) );
	}

	public function get_values( string $search, int $page ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'  => $search,
			'paged'   => $page,
			'include' => $this->get_author_ids( $this->post_type ),
		], $this->formatter() );
	}

}