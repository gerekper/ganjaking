<?php namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

interface IntegrationSectionInterface {

	/**
	 * Get name of the section
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Get slug of the section
	 *
	 * @return string
	 */
	public function getSlug();

	/**
	 * Get all section fields
	 *
	 * @return array
	 */
	public function getFields();

}
