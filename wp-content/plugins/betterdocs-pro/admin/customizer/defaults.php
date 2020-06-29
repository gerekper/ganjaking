<?php 

if ( ! function_exists( 'betterdocs_get_option_defaults_pro' ) ) :
	/**
	 * Set default options
	 */
	function betterdocs_get_option_defaults_pro() {
		$betterdocs_defaults_pro = array(
			'betterdocs_doc_page_content_overlap' => '135',
			'betterdocs_doc_page_cat_icon_size_l_3_4' => '60',
			'betterdocs_doc_page_cat_title_font_size2' => '18',
			'betterdocs_reactions_title' => '',
			'betterdocs_post_reactions' => true,
			'betterdocs_post_reactions_text' => esc_html__('What are your Feelings', 'betterdocs-pro'),
			'betterdocs_post_reactions_text_color' => '#566e8b',
			'betterdocs_post_reactions_icon_color' => '#00b88a',
			'betterdocs_doc_single_content_area_bg_color' => '', // from free
			'betterdocs_doc_single_content_area_padding_right' => '25', // from free
			'betterdocs_doc_single_content_area_padding_left' => '25', // from free

		);
		return apply_filters( 'betterdocs_option_defaults_pro', $betterdocs_defaults_pro );
	}
endif;

/**
*  Get default customizer option
*/
if ( ! function_exists( 'betterdocs_get_option_pro' ) ) :

	/**
	 * Get default customizer option
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function betterdocs_get_option_pro( $key ) {

		$default_options = betterdocs_get_option_defaults_pro();

		if ( empty( $key ) ) {
			return;
		}

		$theme_options = (array)get_theme_mod( 'theme_options' );
		$theme_options = wp_parse_args( $theme_options, $default_options );

		$value = null;

		if ( isset( $theme_options[ $key ] ) ) {
			$value = $theme_options[ $key ];
		}

		return $value;
	}

endif;


if( ! function_exists( 'betterdocs_generate_defaults_pro' ) ) : 

	function betterdocs_generate_defaults_pro(){

		$default_options = betterdocs_get_option_defaults_pro();
		$saved_options = get_theme_mods();

		$returned = [];

		if( ! $saved_options ) {
			return;
		}

		foreach( $default_options as $key => $option ) {
			if( array_key_exists( $key, $saved_options ) ) {
				$returned[ $key ] = get_theme_mod( $key );				
			} else {
				switch ( $key ) {
					default:
						$returned[ $key ] = $default_options[ $key ];
						break;
				}
			}
		}

		return $returned;

	}

endif;
 ?>