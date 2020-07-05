<?php

namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

/**
 * Class TrackConversionSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 */
class DebugSection implements IntegrationSectionInterface {

	public function getTitle() {
		return __( 'Troubleshooting', 'woocommerce-pinterest' );
	}

	public function getSlug() {
		return 'pinterest_debug_section';
	}

	public function getFields() {
		return array(
			'enable_debug' => array(
				'title'    => __( 'Logs', 'woocommerce-pinterest' ),
				'type'     => 'checkbox',
				'label'    => __( 'Enable debug', 'woocommerce-pinterest' ),
				'desc_tip' => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-pinterest' )
			),
		);
	}

}
