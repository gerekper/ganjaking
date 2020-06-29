<?php

class WC_Catalog_Restrictions_User_Admin {

	public static $instance;

	public static function instance() {
		if ( !self::$instance ) {
			self::$instance = new WC_Catalog_Restrictions_User_Admin();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'show_user_profile', array($this, 'profile_fields') );
		add_action( 'edit_user_profile', array($this, 'profile_fields') );

		add_action( 'personal_options_update', array($this, 'save_profile_fields') );
		add_action( 'edit_user_profile_update', array($this, 'save_profile_fields') );
	}

	public function profile_fields( $user ) {

		$location = get_user_meta( $user->ID, '_wc_location', true );
		$can_change = get_user_meta( $user->ID, '_wc_location_user_changeable', true );
		$can_change = $can_change == 'yes' || empty( $can_change );

		if ( current_user_can( 'administrator' ) || $can_change ) {
			include 'views/user-profile-fields.php';
		}
	}

	public function save_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$location = isset( $_POST['location'] ) ? $_POST['location'] : '';
		$can_change = isset( $_POST['can_change'] ) ? $_POST['can_change'] : 'yes';

		update_user_meta( $user_id, '_wc_location', $location );
		update_user_meta( $user_id, '_wc_location_user_changeable', $can_change );
	}

}
