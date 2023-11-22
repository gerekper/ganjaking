<?php
/**
 * Premium Grid Template.
 */

namespace PremiumAddonsPro\Includes\GridBuilder;

use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Section_Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Premium_Grid extends Theme_Section_Document {

	public function get_name() {
		return 'premium-grid';
	}

	public static function get_type() {
		return 'premium-grid';
	}

	public static function get_properties() {

		$properties = parent::get_properties();
		// general for now, this won't show the condition window that appears when saving the template, we can
		// create our own condition and add it here but I have no idea why.
		$properties['condition_type']      = 'premium_grid';
		$properties['location']            = 'single';
		$properties['support_kit']         = true;
		$properties['support_site_editor'] = true;

		return $properties;
	}

	// don't what's that for yet.
	protected static function get_site_editor_type() {
		return 'premium_grid';
	}

	protected static function get_site_editor_thumbnail_url() {
		return PREMIUM_PRO_ADDONS_URL . 'assets/frontend/images/person-image.svg';
	}

	protected static function get_site_editor_icon() {
		return 'eicon-posts-group';
	}

	public static function get_title() {
		return __( 'Premium Grid', 'premium-addons-pro' );
	}

	/**
	 * This category will be used for the loop item placeholder widget.
	 */
	protected static function get_editor_panel_categories() {

		$categories = array(
			'premium-grid' => array(
				'title' => __( 'Premium Grid', 'premium-addon-pro' ),
			),
		);

		return $categories + parent::get_editor_panel_categories();
	}
}

