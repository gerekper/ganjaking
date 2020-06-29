<?php
    /**
	 * called from revslider-front.class.php
	 * @since: 6.1.7
	 */

    if(!defined('ABSPATH')) exit();

    class RevSliderFusionbuilderShortcode {

		/**
		 * Adds our own RevSlider Element to the Fusion Builder
		 * @since: 6.1.7
		 */
		public static function fusionbuilder_add_revslider_shortcode() {
			if( function_exists('fusion_builder_map') ){
				fusion_builder_map( 
						array(
							'name'            => esc_attr__( 'Slider Revolution 6', 'fusion-builder' ),
							'shortcode'       => 'rev_slider6', // pseudo shortcode, will be replaced by the correct on in fusionbuilder_exchange_revslider_shortcode
							'icon'            => 'fusiona-font',
							//'preview'         => PLUGIN_DIR . 'js/previews/fusion-text-preview.php',
							'preview_id'      => 'fusion-builder-block-module-text-preview-template',
							'allow_generator' => true,
							'params'          => array(
								array(
									'type'        => 'tinymce',
									'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
									'description' => esc_attr__( 'Enter some content for this textblock.', 'fusion-builder' ),
									'param_name'  => 'element_content',
									'value'       => esc_attr__( 'Click edit button to change this text.', 'fusion-builder' ),
								),
							),
						) 
					);
			}
		}

		/**
		 * Removes Original RevSlider Element from the Fusion Builder
		 * @since: 6.1.7
		 */
		public static function fusionbuilder_exchange_revslider_shortcode( $elements ) {
			// Process $elements here
			if($elements['rev_slider6']){
				unset($elements['rev_slider']);
				$elements['rev_slider'] = $elements['rev_slider6'];
				unset($elements['rev_slider6']);
				$elements['rev_slider']['shortcode'] = "rev_slider";
			}
			return $elements;
		}
	}
?>