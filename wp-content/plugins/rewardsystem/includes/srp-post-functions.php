<?php

/*
 * Post Function.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! function_exists( 'srp_create_new_birthday' ) ) {

	/**
	 * Create New Birthday.
	 *
	 * @return Object
	 */
	function srp_create_new_birthday( $meta_args, $post_args = array() ) {

		$object = new SRP_Birthday() ;

		return $object->create( $meta_args , $post_args ) ;
	}

}

if ( ! function_exists( 'srp_get_birthday' ) ) {

	/**
	 * Get Birthday.
	 *
	 * @return Object
	 */
	function srp_get_birthday( $id ) {

		return new SRP_Birthday( $id ) ;
	}

}

if ( ! function_exists( 'srp_get_birthday_ids' ) ) {

	/**
	 * Get Birthday Ids.
	 *
	 * @return Array
	 */
	function srp_get_birthday_ids( $args = array() ) {
		$default_args = array(
			'numberposts' => -1 ,
			'post_type'   => SRP_Register_Post_Type::BIRTHDAY_POSTTYPE ,
			'post_status' => 'publish' ,
			'order'       => 'ASC' ,
			'fields'      => 'ids' ,
				) ;

		$parsed_data = wp_parse_args( $args , $default_args ) ;

		return get_posts( $parsed_data ) ;
	}

}

if ( ! function_exists( 'srp_update_birthday' ) ) {

	/**
	 * Update Birthday.
	 *
	 * @return Object
	 */
	function srp_update_birthday( $id, $meta_args, $post_args = array() ) {

		$object = new SRP_Birthday( $id ) ;

		return $object->update( $meta_args , $post_args ) ;
	}

}

if ( ! function_exists( 'srp_delete_birthday' ) ) {

	/**
	 * Delete Birthday.
	 *
	 * @return bool
	 */
	function srp_delete_birthday( $id, $force = true ) {

		wp_delete_post( $id , $force ) ;

		return true ;
	}

}
