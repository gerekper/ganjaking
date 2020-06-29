<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_AutoComplete
 * Param type 'autocomplete'
 * Used to create input field with predefined or ajax values suggestions.
 * See usage example in bottom of this file.
 * @since 4.4
 */
class Vc_AutoComplete {
	/**
	 * @since 4.4
	 * @var array $settings - param settings
	 */
	protected $settings;
	/**
	 * @since 4.4
	 * @var string $value - current param value (if multiple it is splitted by ',' comma to make array)
	 */
	protected $value;
	/**
	 * @since 4.4
	 * @var string $tag - shortcode name(base)
	 */
	protected $tag;

	/**
	 * @param array $settings - param settings (from vc_map)
	 * @param string $value - current param value
	 * @param string $tag - shortcode name(base)
	 *
	 * @since 4.4
	 */
	public function __construct( $settings, $value, $tag ) {
		$this->tag = $tag;
		$this->settings = $settings;
		$this->value = $value;
	}

	/**
	 * @return string
	 * @since 4.4
	 * vc_filter: vc_autocomplete_{shortcode_tag}_{param_name}_render - hook to define output for autocomplete item
	 */
	public function render() {
		$output = sprintf( '<div class="vc_autocomplete-field"><ul class="vc_autocomplete%s">', ( isset( $this->settings['settings'], $this->settings['settings']['display_inline'] ) && true === $this->settings['settings']['display_inline'] ) ? ' vc_autocomplete-inline' : '' );

		if ( isset( $this->value ) && strlen( $this->value ) > 0 ) {
			$values = explode( ',', $this->value );
			foreach ( $values as $key => $val ) {
				$value = array(
					'value' => trim( $val ),
					'label' => trim( $val ),
				);
				if ( isset( $this->settings['settings'], $this->settings['settings']['values'] ) && ! empty( $this->settings['settings']['values'] ) ) {
					foreach ( $this->settings['settings']['values'] as $data ) {
						if ( trim( $data['value'] ) === trim( $val ) ) {
							$value['label'] = $data['label'];
							break;
						}
					}
				} else {
					// Magic is here. this filter is used to render value correctly ( must return array with 'value', 'label' keys )
					$value = apply_filters( 'vc_autocomplete_' . $this->tag . '_' . $this->settings['param_name'] . '_render', $value, $this->settings, $this->tag );
				}

				if ( is_array( $value ) && isset( $value['value'], $value['label'] ) ) {
					$output .= '<li data-value="' . $value['value'] . '"  data-label="' . $value['label'] . '" data-index="' . $key . '" class="vc_autocomplete-label vc_data"><span class="vc_autocomplete-label">' . $value['label'] . '</span> <a class="vc_autocomplete-remove">&times;</a></li>';
				}
			}
		}

		$output .= sprintf( '<li class="vc_autocomplete-input"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input class="vc_auto_complete_param" type="text" placeholder="%s" value="%s" autocomplete="off"></li><li class="vc_autocomplete-clear"></li></ul>', esc_attr__( 'Click here and start typing...', 'js_composer' ), $this->value );

		$output .= sprintf( '<input name="%s" class="wpb_vc_param_value  %s %s_field" type="hidden" value="%s" %s /></div>', $this->settings['param_name'], $this->settings['param_name'], $this->settings['type'], $this->value, ( isset( $this->settings['settings'] ) && ! empty( $this->settings['settings'] ) ) ? ' data-settings="' . htmlentities( wp_json_encode( $this->settings['settings'] ), ENT_QUOTES, 'utf-8' ) . '" ' : '' );

		return $output;
	}
}

/**
 * @action wp_ajax_vc_get_autocomplete_suggestion - since 4.4 used to hook ajax requests for autocomplete suggestions
 */
add_action( 'wp_ajax_vc_get_autocomplete_suggestion', 'vc_get_autocomplete_suggestion' );
/**
 * @since 4.4
 */
function vc_get_autocomplete_suggestion() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

	$query = vc_post_param( 'query' );
	$tag = wp_strip_all_tags( vc_post_param( 'shortcode' ) );
	$param_name = vc_post_param( 'param' );
	vc_render_suggestion( $query, $tag, $param_name );
}

/**
 * @param $query
 * @param $tag
 * @param $param_name
 *
 * vc_filter: vc_autocomplete_{tag}_{param_name}_callback - hook to get suggestions from ajax. (here you need to hook).
 * @since 4.4
 *
 */
function vc_render_suggestion( $query, $tag, $param_name ) {
	$suggestions = apply_filters( 'vc_autocomplete_' . stripslashes( $tag ) . '_' . stripslashes( $param_name ) . '_callback', $query, $tag, $param_name );
	if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
		die( wp_json_encode( $suggestions ) );
	}
	die( '' ); // if nothing found..
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 *
 * @param $settings
 * @param $value
 * @param $tag
 *
 * @return mixed rendered template for params in edit form
 * @since 4.4
 * vc_filter: vc_autocomplete_render_filter - hook to override output of edit for field "autocomplete"
 */
function vc_autocomplete_form_field( $settings, $value, $tag ) {

	$auto_complete = new Vc_AutoComplete( $settings, $value, $tag );

	return apply_filters( 'vc_autocomplete_render_filter', $auto_complete->render() );
}
