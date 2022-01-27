<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

/**
 * Class TrackConversionSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Track Conversion settings section fields on settings page
 */
class TrackConversionSection implements IntegrationSectionInterface {


	public function getTitle() {
		return __( 'Track Conversion settings', 'woocommerce-pinterest' );
	}

	public function getSlug() {
		return 'track_conversion_section';
	}

	public function getFields() {
		$fields = array(
			'enable_track_conversion' => array(
				'title'    => __( 'Track Conversion', 'woocommerce-pinterest' ),
				'type'     => 'checkbox',
				'label'    => __( 'Enable Conversion tracking', 'woocommerce-pinterest' ),
				'desc_tip' => __( 'Enable or disable Pinterest conversion tracking.', 'woocommerce-pinterest' )
			),

			'tag_id' => array(
				'title'       => __( 'Tag ID', 'woocommerce-pinterest' ),
				'type'        => 'text',
				'label'       => __( 'Enable Conversion tracking', 'woocommerce-pinterest' ),
				'desc_tip'    => __( 'Your tag ID created on Pinterest', 'woocommerce-pinterest' ),
				'description' => $this->renderTagIdDescription()
			),
		);


		foreach ( $this->getTrackingEvents() as $key => $event ) {
			$fields[ $key ] = array(
				'type'        => 'checkbox',
				/* translators: '%s replaced with event description' */
				'label'       => sprintf( __(
					'Track %s Event', 'woocommerce-pinterest'
				),
					'<b>' . $event['title'] . '</b>'
				),
				'description' => $event['description'],
			);
		}

		$fields['enable_enhanced_match'] = array(
			'type' => 'checkbox',
			'label'       => __(
				'Enable Enhanced Match', 'woocommerce-pinterest'
			),
			/* translators: '%s replaced with documentation link' */
				  'description' => sprintf( __(
				'See %s', 'woocommerce-pinterest'
				  ), '<a target="_blank" href="https://help.pinterest.com/en/business/article/enhanced-match">Documentation</a>' ),
		);

		return $fields;
	}

	/**
	 * Render Tag Id description
	 *
	 * @return string
	 */
	private function renderTagIdDescription() {
		/* translators: '%s' is replaced with <a> tag with 'documentation' word */
		return sprintf( __(
			'See %s.', 'woocommerce-pinterest' ),
			'<a target="_blank" href="https://help.pinterest.com/en/business/article/track-conversions-with-pinterest-tag">'
			. __( 'documentation', 'woocommerce-pinterest' ) . '</a>' );
	}

	/**
	 * Return list of tracking events
	 *
	 * @return array[]
	 */
	private function getTrackingEvents() {
		return array(
			'event_Checkout'     => array(
				'title'       => __( 'Checkout', 'woocommerce-pinterest' ),
				'description' => __( 'Event fires on the thank you page after the check out.', 'woocommerce-pinterest' )
			),
			'event_AddToCart'    => array(
				'title'       => __( 'AddToCart', 'woocommerce-pinterest' ),
				'description' => __( 'Event fires when a user adds a product to the cart.', 'woocommerce-pinterest' ),
			),
			'event_PageVisit'    => array(
				'title'       => __( 'PageVisit', 'woocommerce-pinterest' ),
				'description' => __( 'Event fires when a user visits any page.', 'woocommerce-pinterest' )
			,
			),
		/*          'event_Lead'         => array(
				'title'       => __( 'Lead', 'woocommerce-pinterest' ),
				'description' => __( 'Event fires when a user visits a product page.', 'woocommerce-pinterest' ),
			),*/
			'event_Search'       => array(
				'title'       => __( 'Search', 'woocommerce-pinterest' ),
				'description' => __( 'Event fires when user launches search.', 'woocommerce-pinterest' ),
			),
			'event_ViewCategory' => array(
				'title'       => __( 'ViewCategory', 'woocommerce-pinterest' ),
				'description' => __( 'Event fires when user visits a product category page.', 'woocommerce-pinterest' ),
			),
		);
	}
}
