<?php

namespace ACA\ACF\Search\Comparison;

use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use ACP;
use ACP\Helper\Select\User\LabelFormatter\UserName;
use ACP\Helper\Select\User\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class User extends ACP\Search\Comparison\Meta
	implements SearchableValues {

	protected $query;

	use ACP\Search\UserValuesTrait;

	public function __construct( string $meta_key, Query $query ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::CURRENT_USER,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->query = $query;

		parent::__construct( $operators, $meta_key );
	}

	protected function get_label_formatter(): UserName {
		return new UserName();
	}

	public function format_label( $value ): string {
		$user = get_userdata( $value );

		return $user
			? $this->get_label_formatter()->format_label( $user )
			: '';
	}

	public function get_values( string $search, int $page ): Paginated {
		$include = $page === 1 ? $this->get_used_user_ids() : [];

		return ( new PaginatedFactory() )->create( [
			'include' => $include,
			'paged'   => $page,
			'search'  => $search,
		], $this->get_label_formatter() );
	}

	public function get_used_user_ids(): array {
		return array_filter( $this->query->get(), 'is_numeric' );
	}

}