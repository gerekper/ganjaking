<?php

namespace ACA\WC\Search\ShopOrder\Customer\Meta;

use ACA\WC\Search\ShopOrder\Customer\Meta;
use ACP\Search\Comparison\Values;

abstract class Serialized extends Meta
	implements Values {

	/**
	 * @param string $value
	 *
	 * @return array
	 */
	protected function get_user_ids( $value ) {
		return get_users( [
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'meta_query'     => [
				[
					'key'     => $this->related_meta_key,
					'value'   => serialize( $value ),
					'compare' => 'LIKE',
				],
			],
		] );
	}

}