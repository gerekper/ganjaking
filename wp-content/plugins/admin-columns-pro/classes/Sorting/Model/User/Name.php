<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\Model;

class Name extends Model {

	public function get_sorting_vars() {
		$first = uniqid();
		$last = uniqid();

		$vars = [
			'meta_query' => [
				$first => [
					'key'     => 'first_name',
					'value'   => '',
					'compare' => '!=',
				],
				$last  => [
					'key'     => 'last_name',
					'value'   => '',
					'compare' => '!=',
				],
			],
			'orderby'    => $first . ' ' . $last,
		];

		if ( acp_sorting_show_all_results() ) {
			$vars['meta_query'] = [
				[
					'relation' => 'OR',
					$first     => [
						'key'     => 'first_name',
						'compare' => 'EXISTS',
					],
					[
						'key'     => 'first_name',
						'compare' => 'NOT EXISTS',
					],
				],
				[
					'relation' => 'OR',
					$last      => [
						'key'     => 'last_name',
						'compare' => 'EXISTS',
					],
					[
						'key'     => 'last_name',
						'compare' => 'NOT EXISTS',
					],
				],
			];
		}

		return $vars;
	}

}