<?php

namespace ACA\ACF\FieldGroup\Location;

use ACA\ACF\FieldGroup;

class Post implements FieldGroup\Query {

	const POST = 'post';
	const PAGE = 'page';

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	public function get_groups() {
		add_filter( 'acf/location/rule_match/user_type', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/page_type', '__return_true', 16 );

		switch ( $this->post_type ) {
			case self::POST :
				add_filter( 'acf/location/rule_match/post_format', '__return_true', 16 );
				break;
			case self::PAGE :
				add_filter( 'acf/location/rule_match/page', '__return_true', 16 );
				add_filter( 'acf/location/rule_match/page_parent', '__return_true', 16 );
				add_filter( 'acf/location/rule_match/page_template', '__return_true', 16 );
				add_filter( 'acf/location/rule_match/post_template', '__return_true', 16 );
				break;
		}

		add_filter( 'acf/location/rule_match/post', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/post_category', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/post_status', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/post_taxonomy', '__return_true', 16 );

		$groups = acf_get_field_groups( [ 'post_type' => $this->post_type ] );

		remove_filter( 'acf/location/rule_match/user_type', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/page_type', '__return_true', 16 );

		remove_filter( 'acf/location/rule_match/post_format', '__return_true', 16 );

		remove_filter( 'acf/location/rule_match/page', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/page_parent', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/page_template', '__return_true', 16 );

		remove_filter( 'acf/location/rule_match/post', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_category', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_status', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_taxonomy', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_template', '__return_true', 16 );

		return $groups;
	}

}