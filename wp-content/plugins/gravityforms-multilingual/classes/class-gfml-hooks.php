<?php

/**
 * @author OnTheGo Systems
 */
class GFML_Hooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {
	/**
	 * @var \GFML_TM_API
	 */
	private $gfml;

	public function __construct( GFML_TM_API $gfml ) {
		$this->gfml = $gfml;
	}

	/**
	 * Gravity Forms actions and filters hooks.
	 */
	public function add_hooks() {
		add_action( 'gform_post_form_duplicated', [ $this, 'gform_post_form_duplicated' ], 10, 2 );
		add_action( 'gform_forms_post_import', [ $this, 'gform_forms_post_import' ] );
		add_filter( 'wpml_tm_dashboard_date', [ $this, 'set_gfml_date_on_tm_dashboard' ], 10, 3 );
	}

	/**
	 * @param int $old_id
	 * @param int $new_id
	 */
	public function gform_post_form_duplicated( $old_id, $new_id ) {
		$form = GFAPI::get_form( $new_id );

		$this->gfml->update_form_translations( $form, true );
	}

	/**
	 * @param array $forms
	 */
	public function gform_forms_post_import( array $forms ) {
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$this->gfml->update_form_translations( $form, true );
			}
		}
	}

	/**
	 * @param int|false $current_time
	 * @param int       $package_id
	 * @param string    $type
	 *
	 * @return false|int
	 */
	public function set_gfml_date_on_tm_dashboard( $current_time, $package_id, $type ) {
		$date = $current_time;
		if ( 'package_' . $this->gfml->get_type() === $type && class_exists( 'GFAPI' ) ) {
			/** @var \WPML_Package $package */
			$package = apply_filters( 'wpml_st_get_string_package', false, $package_id );
			if ( $package && $package->name ) {
				$form = GFAPI::get_form( $package->name );
				$date = strtotime( $form['date_created'] );
			}
		}

		return $date;
	}

}
