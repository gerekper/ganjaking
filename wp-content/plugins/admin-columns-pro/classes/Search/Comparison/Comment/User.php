<?php

namespace ACP\Search\Comparison\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Helper\Select\User\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use WP_User;

class User extends Field
	implements SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::CURRENT_USER,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators );
	}

	protected function get_field(): string {
		return 'user_id';
	}

	private function formatter(): UserName {
		return new UserName();
	}

	public function format_label( $value ): string {
		$user = get_user_by( 'id', $value );

		return $user instanceof WP_User
			? $this->formatter()->format_label( $user )
			: '';
	}

	public function get_values( string $search, int $page ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'  => $search,
			'paged'   => $page,
			'include' => $this->get_user_ids(),
		],
			$this->formatter()
		);
	}

	private function get_user_ids(): array {
		global $wpdb;

		return $wpdb->get_col( "SELECT DISTINCT user_id FROM $wpdb->comments;" );
	}

}