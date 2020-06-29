<?php

namespace ACP\ThirdParty\YoastSeo\Filtering;

use ACP\Filtering;

class Title extends Filtering\Model {

	public function get_filtering_data() {
		return [
			'options' => [
				'default_title' => __( 'Has Default Title', 'codepress-admin-columns' ),
				'custom_title'  => __( 'Has Custom SEO Title', 'codepress-admin-columns' ),
			],
		];
	}

	public function get_filtering_vars( $vars ) {
		$is_default = ( 'default_title' === $this->get_filter_value() );

		$vars['meta_query']['seo_title'] = [
			'key'     => $this->get_meta_key(),
			'value'   => '',
			'compare' => $is_default ? '' : '!=',
		];

		if ( $is_default ) {
			$vars['meta_query']['seo_title']['relation'] = 'OR';
			$vars['meta_query']['seo_title'] = [
				'key'     => $this->get_meta_key(),
				'compare' => 'NOT EXISTS',
			];
		}

		return $vars;
	}

	public function get_meta_key() {
		return '_yoast_wpseo_title';
	}

}