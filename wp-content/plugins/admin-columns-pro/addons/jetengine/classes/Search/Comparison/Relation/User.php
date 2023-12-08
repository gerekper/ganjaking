<?php

namespace ACA\JetEngine\Search\Comparison\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Search\Comparison\Relation;
use ACP\Helper\Select;
use WP_User;

class User extends Relation {

	public function format_label( $value ): string {
		$user = get_user_by( 'id', $value );

		return $user instanceof WP_User
			? ( new Select\User\LabelFormatter\UserName() )->format_label( $user )
			: '';
	}

	public function get_values( string $search, int $page ): Paginated {
		return ( new Select\User\PaginatedFactory() )->create( [
			'search' => $search,
			'paged'  => $page,
		] );
	}

}