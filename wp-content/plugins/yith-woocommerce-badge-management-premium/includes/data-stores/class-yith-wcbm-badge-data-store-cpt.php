<?php
/**
 * Badge Data Store CPT
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagement\DataStores
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Data_Store_CPT' ) ) {
	/**
	 * Badge Data Store CPT Class
	 */
	class YITH_WCBM_Badge_Data_Store_CPT extends YITH_WCBM_Simple_Data_Store_CPT {

		/**
		 * Map that relates meta keys to properties for YITH_WCBM_Badge object
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'          => 'enabled',
			'_type'             => 'type',
			'_image'            => 'image',
			'_text'             => 'text',
			'_background_color' => 'background_color',
			'_size'             => 'size',
			'_padding'          => 'padding',
			'_border_radius'    => 'border_radius',
			'_position'         => 'position',
			'_alignment'        => 'alignment',
		);

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $object_type = 'badge';

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $object_post_type = 'yith-wcbm-badge';

		/**
		 * YITH_WCBM_Badge_Data_Store_CPT construct
		 */
		public function __construct() {
			$this->messages = array(
				'invalid_data' => _x( 'Invalid Badge.', '[Generic] Error that happens when trying to read a Badge that does not exist', 'yith-woocommerce-badges-management' ),
			);
		}

		/**
		 * Update Object post meta
		 *
		 * @param WC_Data $object The object.
		 * @param bool    $force  Force update. Used during create.
		 *
		 * @since 2.0
		 */
		public function update_post_meta( &$object, $force = false ) {
			$props_to_update = $force ? $this->meta_key_to_props : $this->get_props_to_update( $object, $this->meta_key_to_props );

			foreach ( $props_to_update as $meta_key => $prop ) {
				$value = $object->{"get_$prop"}( 'edit' );
				$value = is_string( $value ) ? wp_unslash( $value ) : $value;
				switch ( $prop ) {
					case 'text':
						yith_wcbm_wpml_register_string( 'yith-woocommerce-badges-management', $value, $value );
						break;
					default:
						$value = wc_clean( $value );
						break;
				}

				$updated = $this->update_or_delete_post_meta( $object, $meta_key, $value );

				if ( $updated ) {
					$this->updated_props[] = $prop;
				}
			}
		}

		/**
		 * Check for old Meta before reading the object.
		 *
		 * @param YITH_WCBM_Badge $badge The badge.
		 */
		public function read( &$badge ) {
			if ( $badge->get_meta( '_badge_meta' ) ) {
				yith_wcbm_update_badge_meta( $badge->get_id() );
			}
			parent::read( $badge );
		}
	}
}
