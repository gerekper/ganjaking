<?php

add_action( 'init', 'userpro_init_setup', 11 );
function userpro_init_setup()
{

				global $userpro;

				if ( !empty( $userpro -> fields ) && !get_option( 'userpro_pre_icons_setup' ) ) {
								$userpro -> update_field_icons();
				}
				if ( !get_option( 'userpro_pre_icons_setup' ) ) {
								$userpro -> update_field_icons();
				}

				/* Setup Fields */
				if ( !get_option( 'userpro_fields' ) ) {
								$userpro_fields[ 'first_name' ]        = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'First Name',
								];
								$userpro_fields[ 'last_name' ]         = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Last Name',
								];
								$userpro_fields[ 'display_name' ]      = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Profile Display Name',
												'help'     => 'Your profile name/nickname that is displayed to public.',
								];
								$userpro_fields[ 'user_login' ]        = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Username',
								];
								$userpro_fields[ 'user_email' ]        = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'E-mail Address',
								];
								$userpro_fields[ 'username_or_email' ] = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Username or E-mail',
								];
								$userpro_fields[ 'user_pass' ]         = [
												'_builtin' => 1,
												'type'     => 'password',
												'label'    => 'Password',
												'help'     => 'Your password must be 8 characters long at least.',
								];
								$userpro_fields[ 'user_pass_confirm' ] = [
												'_builtin' => 1,
												'type'     => 'password',
												'label'    => 'Confirm your Password',
								];
								$userpro_fields[ 'passwordstrength' ]  = [
												'_builtin'    => 1,
												'type'        => 'passwordstrength',
												'label'       => __( 'Password Strength Meter', 'userpro' ),
												'too_short'   => __( 'Password too short', 'userpro' ),
												'very_strong' => __( 'Very Strong', 'userpro' ),
												'strong'      => __( 'Strong', 'userpro' ),
												'good'        => __( 'Good', 'userpro' ),
												'weak'        => __( 'Weak', 'userpro' ),
												'very_weak'   => __( 'Very Weak', 'userpro' ),
								];
								$userpro_fields[ 'country' ]           = [
												'_builtin'    => 1,
												'type'        => 'select',
												'label'       => 'Country/Region',
												'options'     => userpro_filter_to_array( 'country' ),
												'placeholder' => 'Select your Country',
								];
								$userpro_fields[ 'role' ]              = [
												'_builtin'    => 1,
												'type'        => 'select',
												'label'       => 'Role',
												'options'     => userpro_filter_to_array( 'roles' ),
												'placeholder' => 'Select your account type',
								];
								$userpro_fields[ 'profilepicture' ]    = [
												'_builtin'    => 1,
												'type'        => 'picture',
												'label'       => 'Profile Picture',
												'button_text' => 'Upload a profile picture',
												'help'        => 'Upload a picture that presents you across the site.',
								];
								$userpro_fields[ 'gender' ]            = [
												'_builtin' => 1,
												'type'     => 'radio',
												'label'    => 'Gender',
												'options'  => [ 'male' => 'Male', 'female' => 'Female' ],
								];
								$userpro_fields[ 'description' ]       = [
												'_builtin' => 1,
												'type'     => 'textarea',
												'label'    => 'Biography',
												'help'     => 'Describe yourself.',
												'html'     => 1,
								];
								$userpro_fields[ 'facebook' ]          = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Facebook Page',
								];
								$userpro_fields[ 'twitter' ]           = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Twitter',
								];
								$userpro_fields[ 'google_plus' ]       = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Google+',
								];
								$userpro_fields[ 'user_url' ]          = [
												'_builtin' => 1,
												'type'     => 'text',
												'label'    => 'Website (URL)',
								];
								/**
									* Security Question Answer new Filed Start
									* By Rahul
									* On 21 NOV 2014
									*/
								$userpro_fields[ 'securityqa' ] = [
												'_builtin' => 1,
												'type'     => 'securityqa',
												'label'    => 'Are You A Human?',
								];
								/**
									* Security Question Answer new Filed End
									* By Rahul
									* On 21 NOV 2014
									*/
								update_option( 'userpro_fields', $userpro_fields );
								update_option( 'userpro_fields_builtin', $userpro_fields );
				}

				/* Setup Field Groups */

				if ( !get_option( 'userpro_fields_groups' ) ) {
								$userpro_fields_groups[ 'register' ][ 'default' ] = [

												'accountinfo'       => userpro_add_section( __( 'Account Details', 'userpro' ), 1, 0 ),
												'user_login'        => userpro_add_field( 'user_login', 0, 0, 1, 'username_exists' ),
												'user_email'        => userpro_add_field( 'user_email', 1, 0, 1, 'email_exists' ),
												'user_pass'         => userpro_add_field( 'user_pass', 0, 0, 1, NULL ),
												'user_pass_confirm' => userpro_add_field( 'user_pass_confirm', 0, 0, 0, NULL ),
												'passwordstrength'  => userpro_add_field( 'passwordstrength', 0, 0, 0, NULL ),

												'profile'        => userpro_add_section( __( 'Profile Details', 'userpro' ), 1, 1 ),
												'display_name'   => userpro_add_field( 'display_name', 0, 0, 0, NULL ),
												'profilepicture' => userpro_add_field( 'profilepicture', 0, 0, 0, NULL ),
												'gender'         => userpro_add_field( 'gender', 0, 0, 0, NULL ),
												'country'        => userpro_add_field( 'country', 0, 0, 0, NULL ),

												'social'      => userpro_add_section( __( 'Social Profiles', 'userpro' ), 1, 1 ),
												'facebook'    => userpro_add_field( 'facebook', 0, 0, 0, NULL ),
												'twitter'     => userpro_add_field( 'twitter', 0, 0, 0, NULL ),
												'google_plus' => userpro_add_field( 'google_plus', 0, 0, 0, NULL ),
												'user_url'    => userpro_add_field( 'user_url', 0, 0, 0, NULL ),

								];

								$userpro_fields_groups[ 'login' ][ 'default' ] = [
												'username_or_email' => userpro_add_field( 'username_or_email', 0, 0, 1, NULL ),
												'user_pass'         => userpro_add_field( 'user_pass', 0, 0, 1, NULL ),
								];

								$userpro_fields_groups[ 'edit' ][ 'default' ] = [

												'profile'        => userpro_add_section( __( 'Profile Details', 'userpro' ), 1, 0 ),
												'display_name'   => userpro_add_field( 'display_name', 0, 0, 0, NULL ),
												'profilepicture' => userpro_add_field( 'profilepicture', 0, 0, 0, NULL ),
												'first_name'     => userpro_add_field( 'first_name', 0, 0, 0, NULL ),
												'last_name'      => userpro_add_field( 'last_name', 0, 0, 0, NULL ),
												'description'    => userpro_add_field( 'description', 0, 0, 0, NULL ),
												'gender'         => userpro_add_field( 'gender', 0, 0, 0, NULL ),
												'country'        => userpro_add_field( 'country', 0, 0, 0, NULL ),

												'social'      => userpro_add_section( __( 'Social Profiles', 'userpro' ), 1, 0 ),
												'facebook'    => userpro_add_field( 'facebook', 0, 0, 0, NULL ),
												'twitter'     => userpro_add_field( 'twitter', 0, 0, 0, NULL ),
												'google_plus' => userpro_add_field( 'google_plus', 0, 0, 0, NULL ),
												'user_url'    => userpro_add_field( 'user_url', 0, 0, 0, NULL ),

												'accountinfo'       => userpro_add_section( __( 'Account Details', 'userpro' ), 1, 0 ),
												'user_email'        => userpro_add_field( 'user_email', 1, 0, 0, 'email_domain_check' ),
												'user_pass'         => userpro_add_field( 'user_pass', 0, 0, 0, NULL ),
												'user_pass_confirm' => userpro_add_field( 'user_pass_confirm', 0, 0, 0, NULL ),
												'passwordstrength'  => userpro_add_field( 'passwordstrength', 0, 0, 0, NULL ),

								];

								$userpro_fields_groups[ 'view' ][ 'default' ] = $userpro_fields_groups[ 'edit' ][ 'default' ];

								$userpro_fields_groups[ 'social' ][ 'default' ] = [
												'user_email'  => userpro_add_field( 'user_email' ),
												'facebook'    => userpro_add_field( 'facebook' ),
												'twitter'     => userpro_add_field( 'twitter' ),
												'google_plus' => userpro_add_field( 'google_plus' ),
												'user_url'    => userpro_add_field( 'user_url' ),
								];

								update_option( 'userpro_fields_groups', $userpro_fields_groups );
								update_option( 'userpro_fields_groups_default', $userpro_fields_groups );
								update_option( 'userpro_fields_groups_default_register', $userpro_fields_groups[ 'register' ][ 'default' ] );
								update_option( 'userpro_fields_groups_default_login', $userpro_fields_groups[ 'login' ][ 'default' ] );
								update_option( 'userpro_fields_groups_default_edit', $userpro_fields_groups[ 'edit' ][ 'default' ] );
								update_option( 'userpro_fields_groups_default_view', $userpro_fields_groups[ 'edit' ][ 'default' ] );
								update_option( 'userpro_fields_groups_default_social', $userpro_fields_groups[ 'social' ][ 'default' ] );
				}

}
