<?php
/**
 * All the form related functions definitions are here
 *
 * @package Happy_Addons
 * @author HappyMonster
 */
defined( 'ABSPATH' ) || die();

/**
 * Check if contact form 7 is activated
 *
 * @return bool
 */
function ha_is_cf7_activated() {
	return class_exists( '\WPCF7' );
}

/**
 * Check if WPForms is activated
 *
 * @return bool
 */
function ha_is_wpforms_activated() {
	return class_exists( '\WPForms\WPForms' ) ;
}

/**
 * Check if Ninja Form is activated
 *
 * @return bool
 */
function ha_is_ninjaforms_activated() {
	return class_exists( '\Ninja_Forms' );
}

/**
 * Check if Caldera Form is activated
 *
 * @return bool
 */
function ha_is_calderaforms_activated() {
	return class_exists( '\Caldera_Forms' );
}

/**
 * Check if We Form is activated
 *
 * @return bool
 */
function ha_is_weforms_activated() {
	return class_exists( '\WeForms' );
}

/**
 * Check if Gravity Forms is activated
 *
 * @return bool
 */
function ha_is_gravityforms_activated() {
	return class_exists( '\GFForms' );
}

/*
 * Check if Fluent Form is activated
 *
 * @return bool
 */
function ha_is_fluent_form_activated() {
	return defined( 'FLUENTFORM' );
}

/**
 * Get a list of all CF7 forms
 *
 * @return array
 */
function ha_get_cf7_forms() {
	$forms = [];

	if ( ha_is_cf7_activated() ) {
		$_forms = get_posts( [
			'post_type'      => 'wpcf7_contact_form',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );

		if ( ! empty( $_forms ) ) {
			$forms = wp_list_pluck( $_forms, 'post_title', 'ID' );
		}
	}

	return $forms;
}

/**
 * Get a list of all WPForms
 *
 * @return array
 */
function ha_get_wpforms() {
	$forms = [];

	if ( ha_is_wpforms_activated() ) {
		$_forms = get_posts( [
			'post_type' => 'wpforms',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		] );

		if ( ! empty( $_forms ) ) {
			$forms = wp_list_pluck( $_forms, 'post_title', 'ID' );
		}
	}

	return $forms;
}

/**
 * Get a list of all Ninja Form
 *
 * @return array
 */
function ha_get_ninjaform() {
	$forms = [];

	if ( ha_is_ninjaforms_activated() ) {
		$_forms = \Ninja_Forms()->form()->get_forms();

		if ( ! empty( $_forms ) && ! is_wp_error( $_forms ) ) {
			foreach ( $_forms as $form ) {
				$forms[ $form->get_id( )] = $form->get_setting('title');
			}
		}
	}

	return $forms;
}

/**
 * Get a list of all Caldera Form
 *
 * @return array
 */
function ha_get_caldera_form() {
	$forms = [];

	if ( ha_is_calderaforms_activated() ) {
		$_forms = \Caldera_Forms_Forms::get_forms(true, true);

		if ( ! empty( $_forms ) && ! is_wp_error( $_forms ) ) {
			foreach ( $_forms as $form ) {
				$forms[ $form['ID'] ] = $form['name'];
			}
		}
	}

	return $forms;
}

/**
 * Get a list of all WeForm
 *
 * @return array
 */
function ha_get_we_forms() {
	$forms = [];

	if ( ha_is_weforms_activated() ) {
		$_forms = get_posts( [
			'post_type' => 'wpuf_contact_form',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		] );

		if ( ! empty( $_forms ) ) {
			$forms = wp_list_pluck( $_forms, 'post_title', 'ID' );
		}
	}

	return $forms;
}

/**
 * Get a list of all GravityForms
 *
 * @return array
 */
function ha_get_gravity_forms() {
	$forms = [];

	if ( ha_is_gravityforms_activated() ) {
		$gravity_forms = \RGFormsModel::get_forms( null, 'title' );

		if ( ! empty( $gravity_forms ) && ! is_wp_error( $gravity_forms ) ) {
			foreach ( $gravity_forms as $gravity_form ) {
				$forms[ $gravity_form->id ] = $gravity_form->title;
			}
		}
	}

	return $forms;
}

/*
 * Get a list of all Fluent Forms
 *
 * @return array
 */
function ha_get_fluent_forms() {
	$forms = [];

	if ( ha_is_fluent_form_activated() ) {
		global $wpdb;

		$table = $wpdb->prefix . 'fluentform_forms';
		$query = "SELECT * FROM {$table}";
		$fluent_forms = $wpdb->get_results( $query );

		if ( $fluent_forms ) {
			foreach( $fluent_forms as $form ) {
				$forms[ $form->id ] = $form->title;
			}
		}
	}

	return $forms;
}
