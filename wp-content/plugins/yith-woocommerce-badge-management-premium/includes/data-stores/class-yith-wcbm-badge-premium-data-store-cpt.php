<?php
/**
 * Badge Premium Data Store CPT
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\DataStores
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Premium_Data_Store_CPT' ) ) {
	/**
	 * Badge Data Store CPT Class
	 */
	class YITH_WCBM_Badge_Premium_Data_Store_CPT extends YITH_WCBM_Badge_Data_Store_CPT {

		/**
		 * Map that relates meta keys to properties for YITH_WCBM_Badge object
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'              => 'enabled',
			'_type'                 => 'type',
			'_image'                => 'image',
			'_uploaded__image_id'   => 'uploaded_image_id',
			'_uploaded_image_width' => 'uploaded_image_width',
			'_css'                  => 'css',
			'_advanced'             => 'advanced',
			'_advanced_display'     => 'advanced_display',
			'_text'                 => 'text',
			'_background_color'     => 'background_color',
			'_text_color'           => 'text_color',
			'_size'                 => 'size',
			'_padding'              => 'padding',
			'_border_radius'        => 'border_radius',
			'_margin'               => 'margin',
			'_opacity'              => 'opacity',
			'_rotation'             => 'rotation',
			'_use_flip_text'        => 'use_flip_text',
			'_flip_text'            => 'flip_text',
			'_position_type'        => 'position_type',
			'_anchor_point'         => 'anchor_point',
			'_position_values'      => 'position_values',
			'_position'             => 'position',
			'_alignment'            => 'alignment',
			'_scale_on_mobile'      => 'scale_on_mobile',
		);

		/**
		 * Check for old Meta before reading the object.
		 *
		 * @param YITH_WCBM_Badge_Premium $badge The badge.
		 */
		public function read( &$badge ) {
			if ( $badge->get_meta( '_badge_meta' ) ) {
				yith_wcbm_update_badge_meta_premium( $badge->get_id() );
			}
			parent::read( $badge );
		}
	}

}
