<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Addons_Migration_3_0 {
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->run();
	}

	/**
	 * Run the migration.
	 *
	 * @since 3.0.0
	 */
	private function run() {
		$this->migrate_global_addons();
	}

	/**
	 * Migrate all Global add-ons.
	 *
	 * @since 3.0.0
	 * @todo We should remove the saved backup "_product_addons_old" in the future.
	 */
	private function migrate_global_addons() {
		$args = array(
			'posts_per_page'  => -1,
			'orderby'         => 'title',
			'order'           => 'ASC',
			'post_type'       => 'global_product_addon',
			'post_status'     => 'any',
			'suppress_filters' => true,
			'meta_query' => array(
				array(
					'key' => '_product_addons',
				),
			),
		);

		$global_addon_posts = get_posts( $args );

		foreach ( $global_addon_posts as $post ) {
			$converted = get_post_meta( $post->ID, '_product_addons_converted', true );

			if ( empty( $converted ) ) {
				$addon_fields = get_post_meta( $post->ID, '_product_addons', true );

				// Save a backup of non converted addons just in case.
				update_post_meta( $post->ID, '_product_addons_old', $addon_fields );

				if ( empty( $addon_fields ) ) {
					continue;
				}

				$updated_addon_fields = WC_Product_Addons_3_0_Conversion_Helper::do_conversion( $addon_fields );
				update_post_meta( $post->ID, '_product_addons', $updated_addon_fields );
				update_post_meta( $post->ID, '_product_addons_converted', 'yes' );
			}
		}
	}
}

new WC_Product_Addons_Migration_3_0();
