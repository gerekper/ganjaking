<?php
/** 
 * EventON Template Blocks
 * @version 4.1.2
 */

class EVO_Temp_Blocks{
	const PLUGIN_SLUG = 'eventon/eventon';


 	// return the constructed template object for the query
 	public function get_single_event_template(){
 		
 		$slug = 'single-ajde_events';

 		$template_content = file_get_contents(
			EVO()->plugin_path() . '/templates/blocks/'. $slug.'.php'
		);

		$template                 = new WP_Block_Template();
		$template->id             = self::PLUGIN_SLUG . '//' . $slug;
		$template->content        = self::inject_theme_attribute_in_content( $template_content );
		$template->slug           = $slug;
		$template->path           = EVO()->plugin_path() . '/templates/blocks/'. $slug.'.php';
		$template->source         = 'custom';
		$template->theme          = 'EventON';
		$template->type           = 'wp_template';
		$template->title          = esc_html__( 'Single Event Page', 'eventon' );
		$template->description 	  = __('Template used to display single event.', 'eventon');
		$template->status         = 'publish';
		$template->has_theme_file = true;
		$template->is_custom      = true;
		$template->post_types     = array();
		$template->origin     = '';

		
		return $template;
 	}

 	// parse wp_template content and inject the current theme stylesheet as theme attribute into teach wp_template_part
 	public static function inject_theme_attribute_in_content( $template_content){
		$has_updated_content = false;
		$new_content         = '';
		$template_blocks     = parse_blocks( $template_content );

		$blocks = static::flatten_blocks( $template_blocks );
		foreach ( $blocks as &$block ) {
			if (
				'core/template-part' === $block['blockName'] &&
				! isset( $block['attrs']['theme'] )
			) {
				$block['attrs']['theme'] = wp_get_theme()->get_stylesheet();
				$has_updated_content     = true;
			}
		}

		if ( $has_updated_content ) {
			foreach ( $template_blocks as &$block ) {
				$new_content .= serialize_block( $block );
			}

			return $new_content;
		}

		return $template_content;
	}

	//Returns an array containing the references of the passed blocks and their inner blocks.
	public static function flatten_blocks( &$blocks ) {
		$all_blocks = [];
		$queue      = [];

		foreach ( $blocks as &$block ) {
			$queue[] = &$block;
		}

		$queue_count = count( $queue );

		while ( $queue_count > 0 ) {
			$block = &$queue[0];
			array_shift( $queue );
			$all_blocks[] = &$block;

			if ( ! empty( $block['innerBlocks'] ) ) {
				foreach ( $block['innerBlocks'] as &$inner_block ) {
					$queue[] = &$inner_block;
				}
			}

			$queue_count = count( $queue );
		}

		return $all_blocks;
	}
}