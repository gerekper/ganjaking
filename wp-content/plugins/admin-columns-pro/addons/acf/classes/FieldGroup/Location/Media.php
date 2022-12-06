<?php

namespace ACA\ACF\FieldGroup\Location;

use ACA\ACF\FieldGroup\Query;

class Media implements Query {

	public function get_groups() {
		add_filter( 'acf/location/rule_match/user_type', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/page_type', '__return_true', 16 );

		add_filter( 'acf/location/rule_match/post', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/post_category', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/post_status', '__return_true', 16 );
		add_filter( 'acf/location/rule_match/post_taxonomy', '__return_true', 16 );

		add_filter( 'acf/location/rule_match/attachment', '__return_true', 16 );

		$groups = acf_get_field_groups( [ 'ac_dummy' => true ] ); // We need to pass an argument, otherwise the filters won't work

		remove_filter( 'acf/location/rule_match/user_type', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/page_type', '__return_true', 16 );

		remove_filter( 'acf/location/rule_match/post', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_category', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_status', '__return_true', 16 );
		remove_filter( 'acf/location/rule_match/post_taxonomy', '__return_true', 16 );

		remove_filter( 'acf/location/rule_match/attachment', '__return_true', 16 );

		return $groups;
	}

}