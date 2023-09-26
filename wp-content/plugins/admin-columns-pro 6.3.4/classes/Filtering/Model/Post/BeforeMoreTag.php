<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class BeforeMoreTag extends Model {

	public function filter_by_before_moretag( $where ) {
		global $wpdb;

		$value = $this->get_filter_value();

		if ( $value ) {
			$sql = '';
			if ( 'cpac_empty' === $value ) {
				$sql = " NOT LIKE '%<!--more-->%'";
			} else if ( 'cpac_nonempty' === $value ) {
				$sql = " LIKE '%<!--more-->%'";
			}
			$where .= " AND {$wpdb->posts}.post_content" . $sql;
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_before_moretag' ] );

		return $vars;
	}

	public function get_filtering_data() {
		return [ 'empty_option' => true ];
	}

}