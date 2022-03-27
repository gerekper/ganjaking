<?php

/**
 * Admin Custom Post Type.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'SRP_Register_Post_Type' ) ) {

	/**
	 * Class.
	 */
	class SRP_Register_Post_Type {

		/**
		 * Birthday Post Type.
		 */
		const BIRTHDAY_POSTTYPE = 'srp_birthday' ;

		/**
		 * Class initialization.
		 */
		public static function init() {
			add_action( 'init' , array( __CLASS__ , 'register_custom_post_types' ) , 5 ) ;
		}

		/**
		 * Register Custom Post types.
		 */
		public static function register_custom_post_types() {
			if ( ! is_blog_installed() ) {
				return ;
			}

			$custom_post_type = array(
				self::BIRTHDAY_POSTTYPE       => array( 'SRP_Register_Post_Type' , 'birthday_post_type_args' ) ,
					) ;

			$custom_post_type = apply_filters( 'srp_add_custom_post_type' , $custom_post_type ) ;

			if ( ! srp_check_is_array( $custom_post_type ) ) {
				return ;
			}

			foreach ( $custom_post_type as $post_type => $args_function ) {
				$args = array() ;
				if ( $args_function ) {
					$args = call_user_func_array( $args_function , $args ) ;
				}

				if ( ! post_type_exists( $post_type ) ) {

					// Register custom post type.
					register_post_type( $post_type , $args ) ;
				}
			}
		}

		/**
		 * Prepare Rules Post type arguments
		 */
		public static function birthday_post_type_args() {

			return apply_filters( 'srp_rules_post_type_args' , array(
				'label'           => esc_html__( 'Birthday' , 'rewardsystem' ) ,
				'public'          => false ,
				'hierarchical'    => false ,
				'supports'        => false ,
				'capability_type' => 'post' ,
				'rewrite'         => false ,
					)
					) ;
		}

	}

	SRP_Register_Post_Type::init() ;
}
