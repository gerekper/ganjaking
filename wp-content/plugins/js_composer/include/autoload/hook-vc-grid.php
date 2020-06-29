<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Hooks_Vc_Grid
 * @since 4.4
 */
class Vc_Hooks_Vc_Grid {
	protected $grid_id_unique_name = 'vc_gid'; // if you change this also change in vc-basic-grid.php

	/**
	 * Initializing hooks for grid element,
	 * Add actions to save appended shortcodes to post meta (for rendering in preview with shortcode id)
	 * And add action to hook request for grid data, to output it.
	 * @since 4.4
	 */
	public function load() {

		// Hook for set post settings meta with shortcodes data
		/**
		 * @since 4.4.3
		 */
		add_filter( 'vc_hooks_vc_post_settings', array(
			$this,
			'gridSavePostSettingsId',
		), 10, 3 );
		/**
		 * Used to output shortcode data for ajax request. called on any page request.
		 */
		add_action( 'wp_ajax_vc_get_vc_grid_data', array(
			$this,
			'getGridDataForAjax',
		) );
		add_action( 'wp_ajax_nopriv_vc_get_vc_grid_data', array(
			$this,
			'getGridDataForAjax',
		) );
	}

	/**
	 * @return string
	 * @since 4.4.3
	 */
	private function getShortcodeRegexForId() {
		return '\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. '([\\w\-_]+)'                     // 2: Shortcode name
			. '(?![\\w\-])'                       // Not followed by word character or hyphen
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			. '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			. '(?:' . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			. '[^\\]\\/]*'               // Not a closing bracket or forward slash
			. ')*?'

			. '(?:' . '(' . $this->grid_id_unique_name // 4: GridId must exist
			. '[^\\]\\/]*'               // Not a closing bracket or forward slash
			. ')+' . ')'

			. ')' . '(?:' . '(\\/)'                        // 5: Self closing tag ...
			. '\\]'                          // ... and closing bracket
			. '|' . '\\]'                          // Closing bracket
			. '(?:' . '('                        // 6: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			. '[^\\[]*+'             // Not an opening bracket
			. '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			. '[^\\[]*+'         // Not an opening bracket
			. ')*+' . ')' . '\\[\\/\\2\\]'             // Closing shortcode tag
			. ')?' . ')' . '(\\]?)';            // 7: Optional second closing brocket for escaping shortcodes: [[tag]]
	}

	/**
	 * @param array $settings
	 * @param $post_id
	 * @param $post
	 *
	 * @return array
	 * @since 4.4.3
	 *
	 */
	public function gridSavePostSettingsId( array $settings, $post_id, $post ) {
		$pattern = $this->getShortcodeRegexForId();
		$content = stripslashes( $post->post_content );
		preg_match_all( "/$pattern/", $content, $found ); // fetch only needed shortcodes
		if ( is_array( $found ) && ! empty( $found[0] ) ) {
			$to_save = array();
			if ( isset( $found[1] ) && is_array( $found[1] ) ) {
				foreach ( $found[1] as $key => $parse_able ) {
					if ( empty( $parse_able ) || '[' !== $parse_able ) {
						$id_pattern = '/' . $this->grid_id_unique_name . '\:([\w\-_]+)/';
						$id_value = $found[4][ $key ];

						preg_match( $id_pattern, $id_value, $id_matches );

						if ( ! empty( $id_matches ) ) {
							$id_to_save = $id_matches[1];

							// why we need to check if shortcode is parse able?
							// 1: if it is escaped it must not be displayed (parsed)
							// 2: so if 1 is true it must not be saved in database meta
							$shortcode_tag = $found[2][ $key ];
							$shortcode_atts_string = $found[3][ $key ];
							/** @var array $atts */
							$atts = shortcode_parse_atts( $shortcode_atts_string );
							$content = $found[6][ $key ];
							$data = array(
								'tag' => $shortcode_tag,
								'atts' => $atts,
								'content' => $content,
							);

							$to_save[ $id_to_save ] = $data;
						}
					}
				}
			}
			if ( ! empty( $to_save ) ) {
				$settings['vc_grid_id'] = array( 'shortcodes' => $to_save );
			}
		}

		return $settings;
	}

	/**
	 * @throws \Exception
	 * @since 4.4
	 *
	 * @output/@return string - grid data for ajax request.
	 */
	public function getGridDataForAjax() {
		$tag = str_replace( '.', '', vc_request_param( 'tag' ) );
		$allowed = apply_filters( 'vc_grid_get_grid_data_access', vc_verify_public_nonce() && $tag, $tag );
		if ( $allowed ) {
			$shortcode_fishbone = visual_composer()->getShortCode( $tag );
			if ( is_object( $shortcode_fishbone ) && vc_get_shortcode( $tag ) ) {
				/** @var WPBakeryShortcode_Vc_Basic_Grid $vc_grid */
				$vc_grid = $shortcode_fishbone->shortcodeClass();
				if ( method_exists( $vc_grid, 'isObjectPageable' ) && $vc_grid->isObjectPageable() && method_exists( $vc_grid, 'renderAjax' ) ) {
					// @codingStandardsIgnoreLine
					$renderAjaxResponse = apply_filters( 'vc_get_vc_grid_data_response', $vc_grid->renderAjax( vc_request_param( 'data' ), $tag, $vc_grid ) );
					wp_die( $renderAjaxResponse );
				}
			}
		}
	}
}

/**
 * @since 4.4
 * @var Vc_Hooks_Vc_Grid $hook
 */
$hook = new Vc_Hooks_Vc_Grid();

// when WPBakery Page Builder initialized let's trigger Vc_Grid hooks.
add_action( 'vc_after_init', array(
	$hook,
	'load',
) );

if ( 'vc_edit_form' === vc_post_param( 'action' ) ) {
	VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Basic_Grid' );

	add_filter( 'vc_edit_form_fields_attributes_vc_basic_grid', array(
		'WPBakeryShortCode_Vc_Basic_Grid',
		'convertButton2ToButton3',
	) );

	add_filter( 'vc_edit_form_fields_attributes_vc_media_grid', array(
		'WPBakeryShortCode_Vc_Basic_Grid',
		'convertButton2ToButton3',
	) );

	add_filter( 'vc_edit_form_fields_attributes_vc_masonry_grid', array(
		'WPBakeryShortCode_Vc_Basic_Grid',
		'convertButton2ToButton3',
	) );

	add_filter( 'vc_edit_form_fields_attributes_vc_masonry_media_grid', array(
		'WPBakeryShortCode_Vc_Basic_Grid',
		'convertButton2ToButton3',
	) );
}
