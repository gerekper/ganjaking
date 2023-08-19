<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\ShopOrder\Customer $column
 */
class CustomerRole extends ACP\Filtering\Model\Meta {

	public function __construct( Column\ShopOrder\Customer $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		$users = get_users( [
				'role'   => $this->get_filter_value(),
				'fields' => 'id',
			]
		);

		$vars['meta_query'][] = [
			'key'     => $this->column->get_meta_key(),
			'value'   => $users,
			'compare' => 'IN',
		];

		return $vars;
	}

	public function get_filtering_data() {
		$user_ids = $this->get_meta_values();

		if ( ! $user_ids ) {
			return false;
		}

		$options = [];

		foreach ( $user_ids as $user_id ) {
			$user = get_user_by( 'id', $user_id );

			if ( ! $user ) {
				continue;
			}

			$options[] = $user->roles;
		}

		$options = call_user_func_array( 'array_merge', $options );

		return [
			'options' => ac_helper()->user->translate_roles( array_unique( $options ) ),
		];
	}

}