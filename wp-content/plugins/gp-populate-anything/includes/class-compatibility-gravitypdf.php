<?php

class GPPA_Compatibility_GravityPDF {

	private static $instance = null;

	private $_current_entry = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'gfpdf_pre_view_or_download_pdf', array( $this, 'hydrate_form_hook_for_pdf_view_or_download' ) );
		add_action( 'gfpdf_pre_generate_and_save_pdf_notification', array( $this, 'hydrate_form_hook' ), 10, 2 );
		add_action( 'gfpdf_pre_generate_and_save_pdf', array( $this, 'hydrate_form_hook' ), 10, 2 );
	}

	/**
	 * Store current entry field and add necessary filter to hydrate form field choices prior to PDF generation.
	 *
	 * @param $entry array Current entry.
	 */
	public function add_hydrate_form_hook( $entry ) {
		$this->_current_entry = $entry;

		/**
		 * Flush forms cache to allow the filter below to take effect
		 */
		GFFormsModel::flush_current_forms();

		add_filter( 'gform_form_post_get_meta', array( $this, 'hydrate_form_for_pdf' ) );
	}

	public function hydrate_form_hook_for_pdf_view_or_download( $entry_id ) {
		$this->add_hydrate_form_hook( GFAPI::get_entry( $entry_id ) );
	}

	public function hydrate_form_hook( $form, $entry ) {
		$this->add_hydrate_form_hook( $entry );
	}

	/**
	 * Hydrate form prior to PDF generation
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function hydrate_form_for_pdf( $form ) {
		/**
		 * Remove filter after to prevent recursion. Subsequent calls should be cached with
		 * GFFormsModel::$_current_forms
		 */
		remove_filter( 'gform_form_post_get_meta', array( $this, 'hydrate_form_for_pdf' ) );

		return gp_populate_anything()->hydrate_form( $form, $this->_current_entry );
	}

}

function gppa_compatibility_gravitypdf() {
	return GPPA_Compatibility_GravityPDF::get_instance();
}
