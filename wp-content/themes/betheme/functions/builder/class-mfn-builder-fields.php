<?php
/**
 * Custom meta fields | Fields
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

if( ! class_exists('Mfn_Builder_Fields') )
{
  class Mfn_Builder_Fields {

    private $sliders;
    private $animations;

    private $section;
    private $wrap;

    private $items;

    /**
      * Constructor
      */

    public function __construct() {

      $this->sliders = array(
        'layer' => Mfn_Builder_Helper::get_sliders('layer'),
        'rev' => Mfn_Builder_Helper::get_sliders('rev'),
      );

      $this->set_animations();

      $this->set_section();
      $this->set_wrap();

      $this->set_items();

    }

    /**
     * GET section fields
     */

    public function get_section(){

      return $this->section;

    }

    /**
     * GET wrap fields
     */

    public function get_wrap(){

      return $this->wrap;

    }

    /**
     * GET items
     */

    public function get_items(){

      return $this->items;

    }

    /**
     * GET item fields
     */

    public function get_item_fields( $item_type ){

      return $this->items[$item_type];

    }

		/**
		 * GET entrance animations
		 */

		public function get_animations(){

			return $this->animations;

		}

    /**
     * SET section fields
   	 */

    private function set_section()
  	{
  		$this->section = array(

  			array(
  				'id' 			=> 'title',
  				'type' 		=> 'text',
  				'title' 	=> __('Title', 'mfn-opts'),
  				'desc' 		=> __('This field is used as an Section Label in admin panel only', 'mfn-opts'),
  			),

  			// background
  			array(
  				'id' 			=> 'info_background',
  				'type' 		=> 'info',
  				'title' 	=> '',
  				'desc' 		=> __('Background', 'mfn-opts'),
  				'class' 	=> 'mfn-info',
  			),

  			array(
  				'id' 			=> 'bg_color',
  				'type' 		=> 'color',
  				'title' 	=> __('Background | Color', 'mfn-opts'),
  				'alpha'		=> true,
  			),

  			array(
  				'id'			=> 'bg_image',
  				'type'		=> 'upload',
  				'title'		=> __('Background | Image', 'mfn-opts'),
  				'desc' 		=> __('Recommended image size: <b>1920px x 1080px</b>', 'mfn-opts'),
  			),

  			array(
  				'id' 			=> 'bg_position',
  				'type' 		=> 'select',
  				'title' 	=> __('Background | Position', 'mfn-opts'),
  				'desc' 		=> __('iOS does <b>not</b> support background-position: fixed<br/>For parallax required background image size is 1920px x 1080px', 'mfn-opts'),
  				'options' => mfna_bg_position(),
  				'std' 		=> 'center top no-repeat',
  			),

  			array(
  				'id' 			=> 'bg_size',
  				'type' 		=> 'select',
  				'title' 	=> __('Background | Size', 'mfn-opts'),
  				'desc' 		=> __('Does <b>not</b> work with fixed position & parallax. Works only in modern browsers', 'mfn-opts'),
  				'options' => mfna_bg_size(),
  			),

  			array(
  				'id'			=> 'bg_video_mp4',
  				'type'		=> 'upload',
  				'title'		=> __('Background | Video HTML5', 'mfn-opts'),
  				'sub_desc'=> __('m4v [.mp4]', 'mfn-opts'),
  				'desc'		=> __('Please add both mp4 and ogv for cross-browser compatibility. Background Image will be used as video placeholder before video loads and on mobile devices', 'mfn-opts'),
  				'data'		=> 'video',
  			),

  			array(
  				'id'			=> 'bg_video_ogv',
  				'type'		=> 'upload',
  				'title'		=> __('Background | Video HTML5', 'mfn-opts'),
  				'sub_desc'=> __('ogg [.ogv]', 'mfn-opts'),
  				'data'		=> 'video',
  			),

  			// layout
  			array(
  				'id' => 'info_layout',
  				'type' => 'info',
  				'title' => '',
  				'desc' => __('Layout', 'mfn-opts'),
  				'class' => 'mfn-info',
  			),

  			array(
  				'id' => 'padding_top',
  				'type' => 'text',
  				'title' => __('Padding | Top', 'mfn-opts'),
  				'desc' => __('px', 'mfn-opts'),
  				'class' => 'small-text',
  				'std' => '0',
  			),

  			array(
  				'id' => 'padding_bottom',
  				'type' => 'text',
  				'title' => __('Padding | Bottom', 'mfn-opts'),
  				'desc' => __('px', 'mfn-opts'),
  				'class' => 'small-text',
  				'std' => '0',
  			),

  			array(
  				'id' => 'padding_horizontal',
  				'type' => 'text',
  				'title' => __('Padding | Horizontal', 'mfn-opts'),
  				'desc' => __('Use value with <b>px</b> or <b>%</b>', 'mfn-opts'),
  				'class' => 'small-text',
  				'std' => '0',
  			),

  			// options
  			array(
  				'id' 			=> 'info_options',
  				'type' 		=> 'info',
  				'title' 	=> '',
  				'desc' 		=> __('Options', 'mfn-opts'),
  				'class' 	=> 'mfn-info',
  			),

  			array(
  				'id' 			=> 'divider',
  				'type' 		=> 'select',
  				'title' 	=> __('Decoration SVG', 'mfn-opts'),
  				'desc' 		=> __('Works only with <b>background color</b> selected above. Does <b>not</b> work with parallax and some section\'s styles', 'mfn-opts'),
  				'options' => array(
  					'' 										=> __('None', 'mfn-opts'),
  					'circle up' 					=> __('Circle Up', 'mfn-opts'),
  					'square up' 					=> __('Square Up', 'mfn-opts'),
  					'triangle up' 				=> __('Triangle Up', 'mfn-opts'),
  					'triple-triangle up'	=> __('Triple Triangle Up', 'mfn-opts'),
  					'circle down' 				=> __('Circle Down', 'mfn-opts'),
  					'square down' 				=> __('Square Down', 'mfn-opts'),
  					'triangle down' 			=> __('Triangle Down', 'mfn-opts'),
  					'triple-triangle down'=> __('Triple Triangle Down', 'mfn-opts'),
  				),
  			),

  			array(
  				'id'			=> 'decor_top',
  				'type'		=> 'upload',
  				'title'		=> __('Decoration Image | Top', 'mfn-opts'),
  				'desc'		=> __('Please use only images <b>from Media Library</b>. Recommended width: 1920px', 'mfn-opts'),
  			),

  			array(
  				'id'			=> 'decor_bottom',
  				'type'		=> 'upload',
  				'title'		=> __('Decoration Image | Bottom', 'mfn-opts'),
  				'desc'		=> __('Please use only images <b>from Media Library</b>. Recommended width: 1920px', 'mfn-opts'),
  			),

  			array(
  				'id' 			=> 'navigation',
  				'type' 		=> 'select',
  				'title' 	=> __('Navigation', 'mfn-opts'),
  				'options' => array(
  					'' 				=> __('None', 'mfn-opts'),
  					'arrows' 	=> __('Arrows', 'mfn-opts'),
  				),
  			),

  			// advanced
  			array(
  				'id' 			=> 'info_advanced',
  				'type' 		=> 'info',
  				'title' 	=> '',
  				'desc' 		=> __('Advanced', 'mfn-opts'),
  				'class' 	=> 'mfn-info',
  			),

  			array(
  				'id' 				=> 'style',
  				'type' 			=> 'checkbox_pseudo',
  				'title' 		=> __('Style', 'mfn-opts'),
  				'sub_desc'	=> __('Predefined styles for section', 'mfn-opts'),
  				'options' 	=> array(
  					'no-margin-h'						=> __('Columns | remove horizontal margin', 'mfn-opts'),
  					'no-margin-v'	 					=> __('Columns | remove vertical margin', 'mfn-opts'),
  					'dark' 									=> __('Dark', 'mfn-opts'),
  					'equal-height'					=> __('Equal Height | items in wrap', 'mfn-opts'),
  					'equal-height-wrap'			=> __('Equal Height | wraps', 'mfn-opts'),
  					'full-screen'	 					=> __('Full Screen', 'mfn-opts'),
  					'full-width'	 					=> __('Full Width', 'mfn-opts'),
  					'full-width-ex-mobile'	=> __('Full Width | except mobile', 'mfn-opts'),
  					'highlight-left' 				=> __('Highlight | left', 'mfn-opts'),
  					'highlight-right' 			=> __('Highlight | right<span>in highlight section please use two 1/2 wraps</span>', 'mfn-opts'),
  				),
  			),

  			array(
  				'id' 			=> 'class',
  				'type' 		=> 'text',
  				'title' 	=> __('Custom | Classes', 'mfn-opts'),
  				'desc'		=> __('Multiple classes should be separated with SPACE. For sections with centered text you can use class: <strong>center</strong>', 'mfn-opts'),
  			),

  			array(
  				'id' 			=> 'section_id',
  				'type' 		=> 'text',
  				'title' 	=> __('Custom | ID', 'mfn-opts'),
  				'desc'		=> __('Use this option to create One Page sites.<br />Example: Your <b>Custom ID</b> is <strong>offer</strong> and you want to open this section, please use link: <strong>your-url/#offer</strong>', 'mfn-opts'),
  				'class' 	=> 'small-text',
  			),

  			array(
  				'id' 			=> 'visibility',
  				'type' 		=> 'select',
  				'title' 	=> __('Responsive Visibility', 'mfn-opts'),
  				'options' => array(
  					'' 							=> __('-- Default --', 'mfn-opts'),
  					'hide-desktop' 	=> __('Hide on Desktop | 960px +', 'mfn-opts'),			// 960 +
  					'hide-tablet' 	=> __('Hide on Tablet | 768px - 959px', 'mfn-opts'),		// 768 - 959
  					'hide-mobile' 	=> __('Hide on Mobile | - 768px', 'mfn-opts'),			// - 768
  					'hide-desktop hide-tablet' 	=> __('Hide on Desktop & Tablet', 'mfn-opts'),
  					'hide-desktop hide-mobile' 	=> __('Hide on Desktop & Mobile', 'mfn-opts'),
  					'hide-tablet hide-mobile'		=> __('Hide on Tablet & Mobile', 'mfn-opts'),
  				),
  			),

  			array(
  				'id' 			=> 'hide',
  				'type' 		=> 'text',
  				'title' 	=> __('Hide', 'mfn-opts'),
  				'class' 	=> 'hidden',
  			),

  		);

  	}

    /**
     * SET wrap fields
   	 */

    private function set_wrap()
  	{
  		$this->wrap = array(

  			array(
  				'id' 			=> 'bg_color',
  				'type' 		=> 'color',
  				'title' 	=> __('Background | Color', 'mfn-opts'),
  				'alpha'		=> true,
  			),

  			array(
  				'id'			=> 'bg_image',
  				'type'		=> 'upload',
  				'title'		=> __('Background | Image', 'mfn-opts'),
  				'desc'		=> __('Recommended image width: <b>320px - 1920px</b>, depending on size of the wrap', 'mfn-opts'),
  			),

  			array(
  				'id' 			=> 'bg_position',
  				'type' 		=> 'select',
  				'title' 	=> __('Background | Position', 'mfn-opts'),
  				'desc' 		=> __('iOS does <b>not</b> support background-position: fixed<br/>For parallax required background image size is 1920px x 1080px', 'mfn-opts'),
  				'options' => mfna_bg_position(),
  				'std' 		=> 'center top no-repeat',
  			),

  			array(
  				'id' 			=> 'bg_size',
  				'type' 		=> 'select',
  				'title' 	=> __('Background | Size', 'mfn-opts'),
  				'desc' 		=> __('Does <b>not</b> work with fixed position & parallax. Works only in modern browsers', 'mfn-opts'),
  				'options' => mfna_bg_size(),
  			),

  			// options
  			array(
  				'id' 		=> 'info_options',
  				'type' 		=> 'info',
  				'title' 	=> '',
  				'desc' 		=> __('Options', 'mfn-opts'),
  				'class' 	=> 'mfn-info',
  			),

  			array(
  				'id' 		=> 'move_up',
  				'type' 		=> 'text',
  				'title' 	=> __('Move Up', 'mfn-opts'),
  				'desc' 		=> __('px<br />Move this wrap to overflow on previous section. Does <b>not</b> work with <b>parallax</b>', 'mfn-opts'),
  				'class' 	=> 'small-text',
  			),

  			array(
  				'id' 		=> 'padding',
  				'type' 		=> 'text',
  				'title' 	=> __('Padding', 'mfn-opts'),
  				'desc' 		=> __('Use value with <b>px</b> or <b>%</b>. Example: <b>20px</b> or <b>20px 10px 20px 10px</b> or <b>20px 1%</b>', 'mfn-opts'),
  				'class' 	=> 'small-text',
  			),

  			// items
  			array(
  				'id' 		=> 'info_items',
  				'type' 		=> 'info',
  				'title' 	=> '',
  				'desc' 		=> __('Items <span>Options for inner items</span>', 'mfn-opts'),
  				'class' 	=> 'mfn-info',
  			),

  			array(
  				'id' 		=> 'column_margin',
  				'type' 		=> 'select',
  				'title' 	=> __('Margin Bottom', 'mfn-opts'),
  				'options' 	=> array(
  					''			=> __('-- Default --', 'mfn-opts'),
  					'0px'		=> '0px',
  					'10px'		=> '10px',
  					'20px'		=> '20px',
  					'30px'		=> '30px',
  					'40px'		=> '40px',
  					'50px'		=> '50px',
  				),
  			),

  			array(
  				'id' 		=> 'vertical_align',
  				'type' 		=> 'select',
  				'title' 	=> __('Vertical Align', 'mfn-opts'),
  				'desc' 		=> __('Use with Section Style: <b>Equal Height of Wraps</b>', 'mfn-opts'),
  				'options' 	=> array(
  					'top' 		=> __('Top', 'mfn-opts'),
  					'middle'	=> __('Middle', 'mfn-opts'),
  					'bottom'	=> __('Bottom', 'mfn-opts'),
  				),
  			),

  			// advanced
  			array(
  				'id' 		=> 'info_advanced',
  				'type' 		=> 'info',
  				'title' 	=> '',
  				'desc' 		=> __('Advanced', 'mfn-opts'),
  				'class' 	=> 'mfn-info',
  			),

  			array(
  				'id' 		=> 'class',
  				'type' 		=> 'text',
  				'title' 	=> __('Custom | Classes', 'mfn-opts'),
  				'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
  			),

  		);

  	}

    /**
     * SET items and their fields
   	 */

   	private function set_items(){

   		$this->items = array(

   			// Placeholder ----------------------------------------------------

   			'placeholder' => array(
   				'type' 		=> 'placeholder',
   				'title' 	=> __('- Placeholder', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'other',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('This is Muffin Builder Placeholder.', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   				),
   			),

   			// Accordion  -----------------------------------------------------

   			'accordion' => array(
   				'type' 		=> 'accordion',
   				'title' 	=> __('Accordion', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'blocks',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'tabs',
   						'type' 		=> 'tabs',
   						'title' 	=> __('Accordion', 'mfn-opts'),
   						'sub_desc' 	=> __('You can use Drag & Drop to set the order', 'mfn-opts'),
   						'desc' 		=> __('<b>JavaScript</b> content like Google Maps and some plugins shortcodes do <b>not work</b> in tabs', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'open1st',
   						'type' 		=> 'select',
   						'title' 	=> __('Open First', 'mfn-opts'),
   						'desc' 		=> __('Open first tab at start.', 'mfn-opts'),
   						'options'	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'openAll',
   						'type' 		=> 'select',
   						'options' 	=> array( 0 => 'No', 1 => 'Yes' ),
   						'title' 	=> __('Open All', 'mfn-opts'),
   						'desc' 		=> __('Open all tabs at start', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options'	=> array(
   							'accordion'	=> __('Accordion', 'mfn-opts'),
   							'toggle'	=> __('Toggle', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Article box  ---------------------------------------------------

   			'article_box' => array(
   				'type'		=> 'article_box',
   				'title'		=> __('Article box', 'mfn-opts'),
   				'size'		=> '1/3',
   				'cat' 		=> 'boxes',
   				'fields'	=> array(

   					array(
   						'id' 			=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   						'sub_desc'=> __('Featured Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>384px - 960px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'slogan',
   						'type' 		=> 'text',
   						'title' 	=> __('Slogan', 'mfn-opts'),
   						'desc' 		=> __('Allowed HTML tags: span, strong, b, em, i, u', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('Allowed HTML tags: span, strong, b, em, i, u', 'mfn-opts'),
   					),

   					// link
   					array(
   						'id' 			=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 					=> __('Default | _self', 'mfn-opts'),
   							1 					=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox'	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 			=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc'=> __('Entrance animation', 'mfn-opts'),
   						'options' => $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 			=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Before After  ---------------------------------------------------

   			'before_after' => array(
   				'type'		=> 'before_after',
   				'title'		=> __('Before After', 'mfn-opts'),
   				'size'		=> '1/3',
   				'cat' 		=> 'boxes',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'image_before',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image | Before', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'image_after',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image | After', 'mfn-opts'),
   						'desc' 		=> __('Both images <b>must have the same size</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Blockquote -----------------------------------------------------

   			'blockquote' => array(
   				'type' 		=> 'blockquote',
   				'title' 	=> __('Blockquote', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'typography',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'sub_desc' 	=> __('Blockquote content.', 'mfn-opts'),
   						'desc' 		=> __('Some HTML tags allowed.', 'mfn-opts')
   					),

   					array(
   						'id' 		=> 'author',
   						'type' 		=> 'text',
   						'title' 	=> __('Author', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   						'sub_desc' 	=> __('Link to company page.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Blog -----------------------------------------------------------

   			'blog' => array(
   				'type' 		=> 'blog',
   				'title' 	=> __('Blog', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'count',
   						'type' 		=> 'text',
   						'title' 	=> __('Count', 'mfn-opts'),
   						'sub_desc' 	=> __('Number of posts to show', 'mfn-opts'),
   						'std' 		=> '3',
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' => 'style',
   						'type' => 'select',
   						'title' => __('Style', 'mfn-opts'),
   						'desc' => __('If you do not know what <b>image size</b> is being used for selected style, please navigate to the: Appearance > <a target="_blank" href="admin.php?page=be-options">Theme Options</a> > Blog, Portfolio & Shop > <b>Featured Images</b>', 'mfn-opts'),
   						'options'	=> array(
   							'classic' => __('Classic - 1 column', 'mfn-opts'),
   							'grid' => __('Grid - 2-4 columns', 'mfn-opts'),
   							'masonry' => __('Masonry Blog Style - 2-4 columns', 'mfn-opts'),
   							'masonry tiles'	=> __('Masonry Tiles (vertical images) - 2-4 columns', 'mfn-opts'),
   							'photo' => __('Photo (horizontal images) - 1 column', 'mfn-opts'),
   							'photo2' => __('Photo 2 - 1-3 columns', 'mfn-opts'),
   							'timeline' => __('Timeline - 1 column', 'mfn-opts'),
   						),
   						'std' => 'grid',
   					),

   					array(
   						'id' => 'columns',
   						'type' => 'select',
   						'title' => __('Columns', 'mfn-opts'),
   						'desc' => __('This option works in: <b>Grid, Masonry, Photo 2</b>', 'mfn-opts'),
   						'options' => array(
   							2	=> 2,
   							3	=> 3,
   							4	=> 4,
   							5	=> 5,
   							6	=> 6,
   						),
   						'std' => 3,
   					),

						array(
   						'id' => 'title_tag',
   						'type' => 'select',
   						'title' => __('Title tag', 'mfn-opts'),
   						'options' => array(
   							'h2' => 'H2',
   							'h3' => 'H3',
   							'h4' => 'H4',
   							'h5' => 'H5',
   							'h6' => 'H6',
   						),
   						'std' => 'h2'
   					),

   					array(
   						'id' 		=> 'images',
   						'type' 		=> 'select',
   						'title' 	=> __('Post Image', 'mfn-opts'),
   						'desc' 		=> __('for all Blog styles except Masonry Tiles', 'mfn-opts'),
   						'options'	=> array(
   							'' 				=> 'Default',
   							'images-only' 	=> 'Featured Images only (replace sliders and videos with featured image)',
   						),
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title' 	=> __('Category', 'mfn-opts'),
   						'options' 	=> mfn_get_categories('category'),
   						'sub_desc' 	=> __('Select posts category', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories <b>slugs</b>', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'desc' 		=> __('Do not use random order with pagination or load more', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'exclude_id',
   						'type'		=> 'text',
   						'title'		=> __('Exclude Posts', 'mfn-opts'),
   						'sub_desc'	=> __('Posts <b>IDs</b>', 'mfn-opts'),
   						'desc'		=> __('IDs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id' => 'filters',
   						'type' => 'select',
   						'title' => __('Filters', 'mfn-opts'),
   						'desc' => __('This option works in <b>Category: All</b> and <b>Style: Masonry</b>. Does <b>not</b> work with any type od pagination.', 'mfn-opts'),
   						'options' => array(
   							'0' => __('Hide', 'mfn-opts'),
   							'1' => __('Show', 'mfn-opts'),
   							'only-categories' => __('Show only Categories', 'mfn-opts'),
   							'only-tags' => __('Show only Tags', 'mfn-opts'),
   							'only-authors' => __('Show only Authors', 'mfn-opts'),
   						),
   						'std' => '0'
   					),

						array(
   						'id' => 'excerpt',
   						'type' => 'select',
   						'options' => array( 0 => 'Hide', 1 => 'Show' ),
   						'title' => __('Excerpt', 'mfn-opts'),
   						'std' => 1,
   					),

   					array(
   						'id' => 'more',
   						'type' => 'select',
   						'options' => array( 0 => 'Hide', 1 => 'Show' ),
   						'title' => __('Read more', 'mfn-opts'),
   						'std' => 1,
   					),

   					// pagination

   					array(
   						'id' 		=> 'info_pagination',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Pagination', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'pagination',
   						'type' 		=> 'select',
   						'options' 	=> array( 0 => 'No', 1 => 'Yes' ),
   						'title' 	=> __('Pagination', 'mfn-opts'),
   						'desc' 		=> __('<strong>Notice:</strong> Pagination will <strong>not</strong> work if you put item on Homepage of WordPress Multilingual Site.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'load_more',
   						'type' 		=> 'select',
   						'title' 	=> __('Load More button', 'mfn-opts'),
   						'desc' 		=> __('<b>Sliders</b> will be replaced with featured images', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// Style
   					array(
   						'id' 		=> 'info_style',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Style', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> __('Greyscale Images', 'mfn-opts'),
   						'options' 	=> array( 0 => 'No', 1 => 'Yes' ),
   					),

   					array(
   						'id'		=> 'margin',
   						'type'		=> 'select',
   						'title'		=> __('Margin', 'mfn-opts'),
   						'desc'		=> __('for <b>Style: Masonry Tiles</b> only', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// Plugins
   					array(
   						'id' 		=> 'info_plugins',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Plugins', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'events',
   						'type'		=> 'select',
   						'title'		=> __('Include events', 'mfn-opts'),
   						'sub_desc'	=> __('The Events Calendar', 'mfn-opts'),
   						'desc'		=> __('This option works in <b>Category: All</b> and requires free <b>The Events Calendar</b> plugin', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Blog News ------------------------------------------------------

   			'blog_news' => array(
   				'type' 		=> 'blog_news',
   				'title' 	=> __('Blog News', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'desc' 		=> __('Image size for this item is the same as for Blog Page, please navigate to the: Appearance > <a target="_blank" href="admin.php?page=be-options">Theme Options</a> > Blog, Portfolio & Shop > <b>Featured Images</b> > Blog & Portfolio', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Default', 'mfn-opts'),
   							'featured'	=> __('Featured 1st', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'count',
   						'type' 		=> 'text',
   						'title' 	=> __('Count', 'mfn-opts'),
   						'sub_desc' 	=> __('Number of posts to show', 'mfn-opts'),
   						'std' 		=> '5',
   						'class' 	=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title' 	=> __('Category', 'mfn-opts'),
   						'options' 	=> mfn_get_categories('category'),
   						'sub_desc' 	=> __('Select posts category', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories <b>slugs</b>', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'desc' 		=> __('Do not use random order with pagination or load more', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'excerpt',
   						'type' 		=> 'select',
   						'title' 	=> __('Excerpt', 'mfn-opts'),
   						'options' 	=> array(
   							0 			=> __('Hide', 'mfn-opts'),
   							1 			=> __('Show', 'mfn-opts'),
   							'featured' 	=> __('Show for Featured only', 'mfn-opts'),
   						),
   					),

   					array(
   						'id'		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Button | Link', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'link_title',
   						'type' 		=> 'text',
   						'title' 	=> __('Button | Title', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Blog Slider ----------------------------------------------------

   			'blog_slider' => array(
   				'type'		=> 'blog_slider',
   				'title' 	=> __('Blog Slider', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'count',
   						'type' 		=> 'text',
   						'title' 	=> __('Count', 'mfn-opts'),
   						'sub_desc' 	=> __('Number of posts to show', 'mfn-opts'),
   						'desc'		=> __('We <strong>do not</strong> recommend use more than 10 items, because site will be working slowly.', 'mfn-opts'),
   						'std' 		=> '5',
   						'class' 	=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title' 	=> __('Category', 'mfn-opts'),
   						'options' 	=> mfn_get_categories('category'),
   						'sub_desc' 	=> __('Select posts category', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories <b>slugs</b>', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'desc' 		=> __('Do not use random order with pagination or load more', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'more',
   						'type' 		=> 'select',
   						'title' 	=> __('Show Read More button', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   						'std'		=> 1,
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options'	=> array(
   							''			=> __('Default', 'mfn-opts'),
   							'flat'		=> __('Flat', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'navigation',
   						'type' 		=> 'select',
   						'title' 	=> __('Navigation', 'mfn-opts'),
   						'options'	=> array(
   							''				=> __('Default', 'mfn-opts'),
   							'hide-arrows'	=> __('Hide Arrows', 'mfn-opts'),
   							'hide-dots'		=> __('Hide Dots', 'mfn-opts'),
   							'hide-nav'		=> __('Hide Navigation', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Blog Teaser ------------------------------------------------------

   			'blog_teaser' => array(
   				'type' 		=> 'blog_teaser',
   				'title' 	=> __('Blog Teaser', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Recommended wrap width: 1/1, minimum wrap width: 2/3', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title_tag',
   						'type' 		=> 'select',
   						'title' 	=> __('Title | Tag', 'mfn-opts'),
   						'desc' 		=> __('Title tag for 1st item, others use a smaller one', 'mfn-opts'),
   						'options' 	=> array(
   							'h1' => 'H1',
   							'h2' => 'H2',
   							'h3' => 'H3',
   							'h4' => 'H4',
   							'h5' => 'H5',
   							'h6' => 'H6',
   						),
   						'std'		=> 'h3'
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title' 	=> __('Category', 'mfn-opts'),
   						'options' 	=> mfn_get_categories('category'),
   						'sub_desc' 	=> __('Select posts category', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories <b>slugs</b>', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'desc' 		=> __('Do not use random order with pagination or load more', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'margin',
   						'type' 		=> 'select',
   						'title' 	=> __('Margin', 'mfn-opts'),
   						'options' 	=> array(
   							'1' 		=> __('Default', 'mfn-opts'),
   							'0'			=> __('No margins', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Button ----------------------------------------------------

   			'button' => array(
   				'type'		=> 'button',
   				'title' 	=> __('Button', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'typography',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'align',
   						'type' 		=> 'select',
   						'title' 	=> __('Align', 'mfn-opts'),
   						'options' 	=> array(
   							''			=> __('Left', 'mfn-opts'),
   							'center'	=> __('Center', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   						),
   					),

   					// icon
   					array(
   						'id' 		=> 'info_icon',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Icon', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					array(
   						'id' 		=> 'icon_position',
   						'type' 		=> 'select',
   						'title' 	=> __('Position', 'mfn-opts'),
   						'options'	=> array(
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   						),
   					),

   					// color

   					array(
   						'id' => 'info_color',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Color', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' => 'color',
   						'type' => 'color',
   						'title' => __('Background', 'mfn-opts'),
   						'desc' => __('For theme color button please enter <strong>theme</strong> in color filed', 'mfn-opts'),
   					),

   					array(
   						'id' => 'font_color',
   						'type' => 'color',
   						'title' => __('Font', 'mfn-opts'),
   					),

   					// style
   					array(
   						'id' 		=> 'info_style',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Style', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'size',
   						'type' 		=> 'select',
   						'title' 	=> __('Size', 'mfn-opts'),
   						'options'	=> array(
   							1 => __('Small', 'mfn-opts'),
   							2 => __('Default', 'mfn-opts'),
   							3 => __('Large', 'mfn-opts'),
   							4 => __('Very Large', 'mfn-opts'),
   						),
   						'std' 		=> 2,
   					),

   					array(
   						'id' 		=> 'full_width',
   						'type' 		=> 'select',
   						'title' 	=> __('Full Width', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'class',
   						'type' 		=> 'text',
   						'title' 	=> __('Class', 'mfn-opts'),
   						'desc' 		=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'rel',
   						'type' 		=> 'text',
   						'title' 	=> __('Rel', 'mfn-opts'),
   						'desc' 		=> __('Adds an rel="..." attribute to the link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'download',
   						'type' 		=> 'text',
   						'title' 	=> __('Download', 'mfn-opts'),
   						'sub_desc'	=> __('Download file when clicking on the link', 'mfn-opts'),
   						'desc'		=> __('Enter the new filename for the downloaded file', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'onclick',
   						'type' 		=> 'text',
   						'title' 	=> __('OnClick', 'mfn-opts'),
   						'desc' 		=> __('Adds an onclick="..." attribute to the link', 'mfn-opts'),
   					),

   				),
   			),

   			// Call to Action -------------------------------------------------

   			'call_to_action' => array(
   				'type' 		=> 'call_to_action',
   				'title' 	=> __('Call to Action', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'elements',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					array(
   						'id'		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed.', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'button_title',
   						'type' 		=> 'text',
   						'title' 	=> __('Button Title', 'mfn-opts'),
   						'desc' 		=> __('Leave this field blank if you want Call to Action with Big Icon', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					// link
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'class',
   						'type' 		=> 'text',
   						'title' 	=> __('Class', 'mfn-opts'),
   						'desc' 		=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Chart  ---------------------------------------------------------

   			'chart' => array(
   				'type' => 'chart',
   				'title' => __('Chart', 'mfn-opts'),
   				'size' => '1/4',
   				'cat' => 'boxes',
   				'fields' => array(

   					array(
   						'id' => 'title',
   						'type' => 'text',
   						'title' => __('Title', 'mfn-opts'),
   					),

   					// chart

   					array(
   						'id' 		=> 'info_chart',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Chart', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'percent',
   						'type' 		=> 'text',
   						'title' 	=> __('Percent', 'mfn-opts'),
   						'desc' 		=> __('Number between 0-100', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'label',
   						'type' 		=> 'text',
   						'title' 	=> __('Label', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					array(
   						'id'		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image size: <b>70px x 70px</b>', 'mfn-opts'),
   					),

   					// options

   					array(
   						'id' => 'info_options',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Options', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

						array(
   						'id' => 'color',
   						'type' => 'color',
   						'title' => __('Color', 'mfn-opts'),
   						'sub_desc' => __('optional', 'mfn-opts'),
   						'desc' => __('Overrides color set in Theme Options', 'mfn-opts'),
   					),

   					array(
   						'id' => 'line_width',
   						'type' => 'text',
   						'title' => __('Line Width', 'mfn-opts'),
   						'sub_desc' => __('optional', 'mfn-opts'),
   						'desc' => __('px', 'mfn-opts'),
   						'class' => 'small-text',
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Clients  -------------------------------------------------------

   			'clients' => array(
   				'type' 		=> 'clients',
   				'title' 	=> __('Clients', 'mfn-opts'),
   				'size'		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'in_row',
   						'type' 		=> 'text',
   						'title' 	=> __('Items in Row', 'mfn-opts'),
   						'sub_desc' 	=> __('Number of items in row', 'mfn-opts'),
   						'desc' 		=> __('Recommended number: 3-6', 'mfn-opts'),
   						'std' 		=> 6,
   						'class' 	=> 'small-text',
   					),

						array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							''			=> __('Default', 'mfn-opts'),
   							'tiles' 	=> __('Tiles', 'mfn-opts'),
   						),
   					),

						// options

   					array(
   						'id' => 'info_options',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Options', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('client-types'),
   						'sub_desc'	=> __('Select the client post category.', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'menu_order'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'ASC'
   					),

						// advanced

   					array(
   						'id' => 'info_advanced',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Advanced', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> 'Greyscale Images',
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

						// custom

   					array(
   						'id' => 'info_custom',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Custom', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Clients Slider -------------------------------------------------

   			'clients_slider' => array(
   				'type' 		=> 'clients_slider',
   				'title' 	=> __('Clients Slider', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('client-types'),
   						'sub_desc'	=> __('Select the client post category.', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'menu_order'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'ASC'
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Code  ----------------------------------------------------------

   			'code' => array(
   				'type' 		=> 'code',
   				'title' 	=> __('Code', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'other',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'class' 	=> 'full-width',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Column  --------------------------------------------------------

   			'column' => array(
   				'type' 		=> 'column',
   				'title' 	=> __('Column', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'typography',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('This field is used as an Item Label in admin panel only', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Shortcodes and HTML tags allowed. Some plugin\'s shortcodes work only in WordPress editor', 'mfn-opts'),
   						'class' 	=> 'full-width sc',
   						'validate' 	=> 'html',
   					),

   					array(
   						'id' 			=> 'align',
   						'type' 		=> 'select',
   						'title' 	=> __('Text Align', 'mfn-opts'),
   						'options' => array(
   							''				=> __('-- Default --', 'mfn-opts'),
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   							'center'	=> __('Center', 'mfn-opts'),
   							'justify'	=> __('Justify', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 				=> 'align-mobile',
   						'type' 			=> 'select',
   						'title' 		=> __('Text Align | Mobile', 'mfn-opts'),
   						'sub_desc' 	=> __('< 768px', 'mfn-opts'),
   						'options' 	=> array(
   							''					=> __('-- The same as selected above --', 'mfn-opts'),
   							'left'			=> __('Left', 'mfn-opts'),
   							'right'			=> __('Right', 'mfn-opts'),
   							'center'		=> __('Center', 'mfn-opts'),
   							'justify'		=> __('Justify', 'mfn-opts'),
   						),
   					),

   					// background
   					array(
   						'id' 			=> 'info_background',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Background', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'column_bg',
   						'type' 		=> 'color',
   						'title' 	=> __('Color', 'mfn-opts'),
   						'alpha'		=> true,
   					),

   					array(
   						'id'			=> 'bg_image',
   						'type'		=> 'upload',
   						'title'		=> __('Image', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'bg_position',
   						'type' 		=> 'select',
   						'title' 	=> __('Position', 'mfn-opts'),
   						'desc' 		=> __('This option can be used only with your custom image selected above', 'mfn-opts'),
   						'options' => mfna_bg_position('column'),
   						'std' 		=> 'center top no-repeat',
   					),

   					array(
   						'id' 		=> 'bg_size',
   						'type' 		=> 'select',
   						'title' 	=> __('Size', 'mfn-opts'),
   						'desc' 		=> __('Works only in modern browsers', 'mfn-opts'),
   						'options' 	=> mfna_bg_size(),
   					),

   					// layout
   					array(
   						'id' 		=> 'info_layout',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Layout', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'margin_bottom',
   						'type' 		=> 'select',
   						'title' 	=> __('Margin | Bottom', 'mfn-opts'),
   						'desc'		=> __('<b>Overrides</b> section settings', 'mfn-opts'),
   						'options' 	=> array(
   							''			=> __('-- Default --', 'mfn-opts'),
   							'0px'		=> '0px',
   							'10px'		=> '10px',
   							'20px'		=> '20px',
   							'30px'		=> '30px',
   							'40px'		=> '40px',
   							'50px'		=> '50px',
   						),
   					),

   					array(
   						'id' 		=> 'padding',
   						'type' 		=> 'text',
   						'title' 	=> __('Padding', 'mfn-opts'),
   						'desc' 		=> __('Use value with <b>px</b> or <b>%</b>. Example: <b>20px</b> or <b>20px 10px 20px 10px</b> or <b>20px 1%</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Styles', 'mfn-opts'),
   						'sub_desc'	=> __('Custom inline CSS Styles', 'mfn-opts'),
   						'desc'		=> __('Example: <b>border: 1px solid #999;</b>', 'mfn-opts'),
   					),

   				),
   			),

   			// Contact box ----------------------------------------------------

   			'contact_box' => array(
   				'type' 		=> 'contact_box',
   				'title' 	=> __('Contact Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'address',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Address', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'telephone',
   						'type' 		=> 'text',
   						'title' 	=> __('Phone', 'mfn-opts'),
   						'desc' 		=> __('Phone number', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'telephone_2',
   						'type' 		=> 'text',
   						'title' 	=> __('Phone 2nd', 'mfn-opts'),
   						'desc' 		=> __('Additional Phone number', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'fax',
   						'type' 		=> 'text',
   						'title' 	=> __('Fax', 'mfn-opts'),
   						'desc' 		=> __('Fax number', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'email',
   						'type' 		=> 'text',
   						'title' 	=> __('Email', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'www',
   						'type' 		=> 'text',
   						'title' 	=> __('WWW', 'mfn-opts'),
   					),

						// custom

   					array(
   						'id' => 'info_advanced',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Advanced', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

						array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Background Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

						// custom

   					array(
   						'id' => 'info_custom',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Custom', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Content  -------------------------------------------------------

   			'content' => array(
   				'type' 		=> 'content',
   				'title' 	=> __('Content WP', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'typography',
   				'fields'	 => array(

   					array(
   						'id' 		=> 'info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Adding this Item will show Content from WordPress Editor above Page Options. You can use it only once per page. Please also remember to turn on "Hide The Content" option.', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Countdown  -----------------------------------------------------

   			'countdown' => array(
   				'type' 		=> 'countdown',
   				'title' 	=> __('Countdown', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'boxes',
   				'fields'	=> array(

   					array(
   						'id' 			=> 'date',
   						'type' 		=> 'text',
   						'title' 	=> __('Launch Date', 'mfn-opts'),
   						'desc' 		=> __('Format: 12/30/2020 12:00:00 month/day/year hour:minute:second', 'mfn-opts'),
   						'std' 		=> '12/30/2020 12:00:00',
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 			=> 'timezone',
   						'type' 		=> 'select',
   						'title' 	=> __('UTC Timezone', 'mfn-opts'),
   						'options' => mfna_utc(),
   						'std' 		=> '0',
   					),

   					// options
   					array(
   						'id' 			=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'show',
   						'type' 		=> 'select',
   						'title' 	=> __('Show', 'mfn-opts'),
   						'options' 	=> array(
   							''				=> __('days hours minutes seconds', 'mfn-opts'),
   							'dhm' 		=> __('days hours minutes', 'mfn-opts'),
   							'dh' 			=> __('days hours', 'mfn-opts'),
   							'd' 			=> __('days', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Counter  -------------------------------------------------------

   			'counter' => array(
   				'type' 		=> 'counter',
   				'title' 	=> __('Counter', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					// counter
   					array(
   						'id' 		=> 'info_counter',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Counter', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'std' 		=> 'icon-lamp',
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'color',
   						'type' 		=> 'color',
   						'title' 	=> __('Icon Color', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('If you upload an image, icon will not show', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'prefix',
   						'type' 		=> 'text',
   						'title' 	=> __('Prefix', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'number',
   						'type' 		=> 'text',
   						'title' 	=> __('Number', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'label',
   						'type' 		=> 'text',
   						'title' 	=> __('Postfix', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'type',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'desc' 		=> __('Vertical style works only for column widths: 1/4, 1/3 & 1/2', 'mfn-opts'),
   						'options' 	=> array(
   							'horizontal'	=> __('Horizontal', 'mfn-opts'),
   							'vertical' 		=> __('Vertical', 'mfn-opts'),
   						),
   						'std'		=> 'vertical',
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Divider  -------------------------------------------------------

   			'divider' => array(
   				'type' => 'divider',
   				'title' => __('Divider', 'mfn-opts'),
   				'size' => '1/1',
   				'cat' => 'other',
   				'fields' => array(

   					array(
   						'id' => 'height',
   						'type' => 'text',
   						'title' => __('Divider height', 'mfn-opts'),
   						'desc' => __('px', 'mfn-opts'),
   						'class' => 'small-text',
   					),

   					array(
   						'id' => 'style',
   						'type' => 'select',
   						'title' => __('Style', 'mfn-opts'),
   						'options' => array(
   							'default' => __('Default', 'mfn-opts'),
   							'dots' => __('Dots', 'mfn-opts'),
   							'zigzag' => __('ZigZag', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' => 'line',
   						'type' => 'select',
   						'title' => __('Line', 'mfn-opts'),
   						'desc' => __('For <strong>style: default</strong> only', 'mfn-opts'),
   						'options' => array(
   							'default' => __('Default', 'mfn-opts'),
   							'narrow' => __('Narrow', 'mfn-opts'),
   							'wide' => __('Wide', 'mfn-opts'),
   							'' => __('No Line', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' => 'color',
   						'type' => 'color',
   						'title' => __('Color', 'mfn-opts'),
							'alpha' => true,
   					),

   					array(
   						'id' => 'themecolor',
   						'type' => 'select',
   						'title' => __('Theme Color', 'mfn-opts'),
   						'desc' => __('Theme Color overwrites color selected above', 'mfn-opts'),
   						'options' => array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

						// custom
   					array(
   						'id' => 'info_custom',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Custom', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Fancy Divider  -------------------------------------------------

   			'fancy_divider' => array(
   				'type' 		=> 'fancy_divider',
   				'title' 	=> __('Fancy Divider', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('This item can only be used on pages <strong>Without Sidebar</strong>', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							'circle up'		=> __('Circle Up', 'mfn-opts'),
   							'circle down'	=> __('Circle Down', 'mfn-opts'),
   							'curve up'		=> __('Curve Up', 'mfn-opts'),
   							'curve down'	=> __('Curve Down', 'mfn-opts'),
   							'stamp'			=> __('Stamp', 'mfn-opts'),
   							'triangle up'	=> __('Triangle Up', 'mfn-opts'),
   							'triangle down'	=> __('Triangle Down', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'color_top',
   						'type' 		=> 'color',
   						'title' 	=> __('Color Top', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'color_bottom',
   						'type' 		=> 'color',
   						'title' 	=> __('Color Bottom', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Fancy Heading --------------------------------------------------

   			'fancy_heading' => array(
   				'type' 		=> 'fancy_heading',
   				'title' 	=> __('Fancy Heading', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'h1',
   						'type' 		=> 'select',
   						'title' 	=> __('Use H1 tag', 'mfn-opts'),
   						'desc' 		=> __('Wrap title into H1 instead of H2', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class' 	=> 'full-width sc',
   						'validate' 	=> 'html',
   					),

   					// style
   					array(
   						'id' 		=> 'info_style',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Style', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							'icon'		=> __('Icon', 'mfn-opts'),
   							'line'		=> __('Line', 'mfn-opts'),
   							'arrows' 	=> __('Arrows', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'sub_desc' 	=> __('for <b>Style: Icon</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'slogan',
   						'type' 		=> 'text',
   						'title' 	=> __('Slogan', 'mfn-opts'),
   						'sub_desc' 	=> __('for <b>Style: Line</b>', 'mfn-opts'),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// FAQ  -----------------------------------------------------------

   			'faq' => array(
   				'type' 		=> 'faq',
   				'title' 	=> __('FAQ', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'blocks',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'tabs',
   						'type' 		=> 'tabs',
   						'title' 	=> __('FAQ', 'mfn-opts'),
   						'sub_desc' 	=> __('You can use Drag & Drop to set the order', 'mfn-opts'),
   						'desc' 		=> __('<b>JavaScript</b> content like Google Maps and some plugins shortcodes do <b>not work</b> in tabs', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'open1st',
   						'type' 		=> 'select',
   						'title' 	=> __('Open First', 'mfn-opts'),
   						'desc' 		=> __('Open first tab at start', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'openAll',
   						'type' 		=> 'select',
   						'title' 	=> __('Open All', 'mfn-opts'),
   						'desc' 		=> __('Open all tabs at start', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Feature Box -------------------------------------------------------

   			'feature_box' => array(
   				'type' 		=> 'feature_box',
   				'title' 	=> __('Feature Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 			=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>384px - 960px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('Allowed HTML tags: span, strong, b, em, i, u', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'validate'=> 'html',
   					),

   					array(
   						'id' 			=> 'background',
   						'type' 		=> 'color',
   						'title' 	=> __('Background color', 'mfn-opts'),
   					),

   					// link
   					array(
   						'id' 			=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   						'sub_desc'=> __('Image Link', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 					=> __('Default | _self', 'mfn-opts'),
   							1 					=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 			=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc'=> __('Entrance animation', 'mfn-opts'),
   						'options'	=> $this->get_animations(),
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Feature List ---------------------------------------------------

   			'feature_list' => array(
   				'type' 		=> 'feature_list',
   				'title' 	=> __('Feature List', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 	=> 'title',
   						'type' 	=> 'text',
   						'title' => __('Title', 'mfn-opts'),
   						'desc' 	=> __('This field is used as an Item Label in admin panel only', 'mfn-opts'),
   					),

   					array(
   						'id' => 'content',
   						'type' => 'textarea',
   						'title' => __('Content', 'mfn-opts'),
   						'desc' => __('Please use <strong>[item icon="" title="List item" link="" target=""]</strong> shortcodes.', 'mfn-opts'),
   						'std' => '[item icon="icon-lamp" title="List item" link="" target="" animate=""]',
   					),

   					array(
   						'id' 		=> 'columns',
   						'type' 		=> 'select',
   						'title' 	=> __('Columns', 'mfn-opts'),
   						'desc' 		=> __('Default: 4. Recommended: 2-4. Too large value may crash the layout.', 'mfn-opts'),
   						'options'	 => array(
   							2	=> 2,
   							3	=> 3,
   							4	=> 4,
   							5	=> 5,
   							6	=> 6,
   						),
   						'std' 		=> 4,
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Flat Box -------------------------------------------------------

   			'flat_box' 	=> array(
   				'type' 		=> 'flat_box',
   				'title' 	=> __('Flat Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 			=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('Allowed HTML tags: span, strong, b, em, i, u', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   						'validate'=> 'html',
   					),

   					// icon
   					array(
   						'id' 			=> 'info_icon',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Icon', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'std' 		=> 'icon-lamp',
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 			=> 'icon_image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Icon | Image', 'mfn-opts'),
   						'desc' 		=> __('You can use image icon instead of font icon', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'background',
   						'type' 		=> 'color',
   						'title' 	=> __('Background', 'mfn-opts'),
   					),

   					// link
   					array(
   						'id' 			=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 					=> __('Default | _self', 'mfn-opts'),
   							1 					=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 			=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc'=> __('Entrance animation', 'mfn-opts'),
   						'options' => $this->get_animations(),
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Helper -------------------------------------------------------

   			'helper' => array(
   				'type' 		=> 'helper',
   				'title' 	=> __('Helper', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'blocks',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' => 'title_tag',
   						'type' => 'select',
   						'title' => __('Title | Tag', 'mfn-opts'),
   						'options' => array(
   							'h1' => 'H1',
   							'h2' => 'H2',
   							'h3' => 'H3',
   							'h4' => 'H4',
   							'h5' => 'H5',
   							'h6' => 'H6',
   						),
   						'std' => 'h4',
   					),

   					array(
   						'id' 		=> 'info_item1',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Item 1', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'title1',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content1',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   						'validate'	=> 'html',
   					),

   					array(
   						'id' 		=> 'link1',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   						'desc' 		=> __('Use this field if you want to link to another page instead of showing the content', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target1',
   						'type' 		=> 'select',
   						'options' 	=> array( 0 => 'No', 1 => 'Yes' ),
   						'title' 	=> __('Link | Open in new window', 'mfn-opts'),
   						'desc' 		=> __('Adds a target="_blank" attribute to the link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'class1',
   						'type' 		=> 'text',
   						'title' 	=> __('Link | Class', 'mfn-opts'),
   						'desc' 		=> __('This option is useful when you want to use <b>prettyphoto</b> or <b>scroll</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'info_item2',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Item 2', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'title2',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content2',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   						'validate'	=> 'html',
   					),

   					array(
   						'id' 		=> 'link2',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   						'desc' 		=> __('Use this field if you want to link to another page instead of showing the content', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target2',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Open in new window', 'mfn-opts'),
   						'desc' 		=> __('Adds a target="_blank" attribute to the link', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'class2',
   						'type' 		=> 'text',
   						'title' 	=> __('Link | Class', 'mfn-opts'),
   						'desc' 		=> __('This option is useful when you want to use <b>prettyphoto</b> or <b>scroll</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Hover Box ------------------------------------------------------

   			'hover_box' => array(
   				'type' 		=> 'hover_box',
   				'title' 	=> __('Hover Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'image_hover',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image | Hover', 'mfn-opts'),
   						'desc' 		=> __('Both images <b>must have the same size</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Hover Color ----------------------------------------------------

   			'hover_color' => array(
   				'type' 		=> 'hover_color',
   				'title' 	=> __('Hover Color', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id'		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   					),

   					array(
   						'id' 		=> 'align',
   						'type' 		=> 'select',
   						'title' 	=> __('Text Align', 'mfn-opts'),
   						'options' 	=> array(
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   							''			=> __('Center', 'mfn-opts'),
   							'justify'	=> __('Justify', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'padding',
   						'type' 		=> 'text',
   						'title' 	=> __('Padding', 'mfn-opts'),
   						'sub_desc' 	=> __('default: 40px 30px', 'mfn-opts'),
   						'desc' 		=> __('Use value with <b>px</b> or <b>%</b>. Example: <b>20px</b> or <b>20px 10px 20px 10px</b> or <b>20px 1%</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> '40px 30px',
   					),

   					// background
   					array(
   						'id' 			=> 'info_background',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Background', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'background',
   						'type' 		=> 'color',
   						'title' 	=> __('Color', 'mfn-opts'),
   						// 'alpha'		=> true, // requires change to jquery because of background div
   					),

   					array(
   						'id' 		=> 'background_hover',
   						'type' 		=> 'color',
   						'title' 	=> __('Hover color', 'mfn-opts'),
   						// 'alpha'		=> true,
   					),

   					// border
   					array(
   						'id' 			=> 'info_border',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Border', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'border',
   						'type' 		=> 'color',
   						'title' 	=> __('Color', 'mfn-opts'),
   						'sub_desc'=> __('optional', 'mfn-opts'),
   						// 'alpha'		=> true,
   					),

   					array(
   						'id' 			=> 'border_hover',
   						'type' 		=> 'color',
   						'title' 	=> __('Hover color', 'mfn-opts'),
   						'sub_desc'=> __('optional', 'mfn-opts'),
   						// 'alpha'		=> true,
   					),

   					array(
   						'id' 			=> 'border_width',
   						'type' 		=> 'text',
   						'title' 	=> __('Width', 'mfn-opts'),
   						'sub_desc'=> __('default: 2px', 'mfn-opts'),
   						'desc' 		=> __('Use value with <b>px</b>. Example: <b>1px</b> or <b>2px 5px 2px 5px</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> '2px',
   					),

   					// link
   					array(
   						'id' 		=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'			=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Target', 'mfn-opts'),
   						'options'	=> array(
   							0 					=> __('Default | _self', 'mfn-opts'),
   							1 					=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 			=> 'class',
   						'type' 		=> 'text',
   						'title' 	=> __('Class', 'mfn-opts'),
   						'desc' 		=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
   					),

   					// custom
   					array(
   						'id' 			=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Styles', 'mfn-opts'),
   						'sub_desc'	=> __('Custom inline CSS Styles', 'mfn-opts'),
   						'desc'		=> __('Example: <b>opacity: 0.5;</b>', 'mfn-opts'),
   					),


   				),
   			),

   			// How It Works ---------------------------------------------------

   			'how_it_works' => array(
   				'type' 		=> 'how_it_works',
   				'title' 	=> __('How It Works', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Background Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended: Square Image with transparent background.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'number',
   						'type' 		=> 'text',
   						'title' 	=> __('Number', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   						'validate'	=> 'html',
   					),

   					// style
   					array(
   						'id' 		=> 'info_style',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Style', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'border',
   						'type' 		=> 'select',
   						'title' 	=> __('Line', 'mfn-opts'),
   						'sub_desc' 	=> __('Show right connecting line', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'sub_desc' 	=> __('Background Image style', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Small centered image (image size: max 116px)', 'mfn-opts'),
   							'fill' 		=> __('Fill the circle (image size: 200px x 200px)', 'mfn-opts'),
   						),
   					),

   					// link
   					array(
   						'id' 		=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Icon Box  ------------------------------------------------------

   			'icon_box' => array(
   				'type' 		=> 'icon_box',
   				'title' 	=> __('Icon Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title_tag',
   						'type' 		=> 'select',
   						'title' 	=> __('Title | Tag', 'mfn-opts'),
   						'options' 	=> array(
   							'h1' => 'H1',
   							'h2' => 'H2',
   							'h3' => 'H3',
   							'h4' => 'H4',
   							'h5' => 'H5',
   							'h6' => 'H6',
   						),
   						'std'		=> 'h4'
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   					),

   					// icon
   					array(
   						'id' 		=> 'info_icon',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Icon', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'std' 		=> 'icon-lamp',
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'icon_position',
   						'type' 		=> 'select',
   						'title' 	=> __('Icon Position', 'mfn-opts'),
   						'desc' 		=> __('Left position works only for column widths: 1/4, 1/3 & 1/2', 'mfn-opts'),
   						'options'	=> array(
   							'left'	=> __('Left', 'mfn-opts'),
   							'top'	=> __('Top', 'mfn-opts'),
   						),
   						'std'		=> 'top',
   					),

   					array(
   						'id' 		=> 'border',
   						'type' 		=> 'select',
   						'title' 	=> __('Border', 'mfn-opts'),
   						'sub_desc' 	=> __('Show right border', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// link
   					array(
   						'id' 		=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'class',
   						'type' 		=> 'text',
   						'title' 	=> __('Link | Class', 'mfn-opts'),
   						'desc' 		=> __('This option is useful when you want to use <b>scroll</b>', 'mfn-opts'),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Image  ---------------------------------------------------------

   			'image' => array(
   				'type' 		=> 'image',
   				'title' 	=> __('Image', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'typography',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'src',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'size',
   						'type' 		=> 'select',
   						'title' 	=> __('Image | Size', 'mfn-opts'),
   						'desc' 		=> __('Select image size from <a target="_blank" href="options-media.php">Settings > Media > Image sizes</a> (Media Library images only)<br />or use below fields for HTML resize', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Full size', 'mfn-opts'),
   							'large' 	=> __('Large', 'mfn-opts') .' | '. mfn_get_image_sizes('large', 1),
   							'medium' 	=> __('Medium', 'mfn-opts') .' | '. mfn_get_image_sizes('medium', 1),
   							'thumbnail' => __('Thumbnail', 'mfn-opts') .' | '. mfn_get_image_sizes('thumbnail', 1),
   						),
   					),

   					array(
   						'id' 		=> 'width',
   						'type' 		=> 'text',
   						'title' 	=> __('Image | Width', 'mfn-opts'),
   						'sub_desc' 	=> __('HTML resize | optional', 'mfn-opts'),
   						'desc' 		=> __('px', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'height',
   						'type' 		=> 'text',
   						'title' 	=> __('Image | Height', 'mfn-opts'),
   						'sub_desc' 	=> __('HTML resize | optional', 'mfn-opts'),
   						'desc' 		=> __('px', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'align',
   						'type' 		=> 'select',
   						'title' 	=> __('Align', 'mfn-opts'),
   						'desc' 		=> __('If you want image to be <b>resized</b> to column width use <b>align none</b>', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('None', 'mfn-opts'),
   							'left' 		=> __('Left', 'mfn-opts'),
   							'right' 	=> __('Right', 'mfn-opts'),
   							'center' 	=> __('Center', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'stretch',
   						'type' 		=> 'select',
   						'title' 	=> __('Stretch', 'mfn-opts'),
   						'sub_desc' 	=> __('Stretch image to column width', 'mfn-opts'),
   						'desc' 		=> __('The height of the image will be changed proportionally', 'mfn-opts'),
   						'options' 	=> array(
   							'0'			=> __('No', 'mfn-opts'),
   							'1' 		=> __('Yes', 'mfn-opts'),
   							'ultrawide' => __('Yes, on ultrawide screens only > 1920px', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'border',
   						'type' 		=> 'select',
   						'title' 	=> __('Border', 'mfn-opts'),
   						'sub_desc' 	=> __('Show Image Border', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'margin',
   						'type' 		=> 'text',
   						'title' 	=> __('Margin | Top', 'mfn-opts'),
   						'desc' 		=> __('px', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'margin_bottom',
   						'type' 		=> 'text',
   						'title' 	=> __('Margin | Bottom', 'mfn-opts'),
   						'desc' 		=> __('px', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// link
   					array(
   						'id' 		=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link_image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Zoomed image', 'mfn-opts'),
   						'desc' 		=> __('This <b>image or embed video</b> will be opened in lightbox.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Open in new window', 'mfn-opts'),
   						'desc' 		=> __('Adds a target="_blank" attribute to the link.', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'hover',
   						'type' 		=> 'select',
   						'title' 	=> __('Hover Effect', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('- Default -', 'mfn-opts'),
   							'disable' 	=> __('Disable', 'mfn-opts'),
   						),
   					),

   					// description
   					array(
   						'id' 		=> 'info_description',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Description', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'alt',
   						'type' 		=> 'text',
   						'title' 	=> __('Alternate Text', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'caption',
   						'type' 		=> 'text',
   						'title' 	=> __('Caption', 'mfn-opts'),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> 'Greyscale Images',
   						'desc'		=> 'Works only for images with link',
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Image Gallery  ---------------------------------------------------------

   			'image_gallery' => array(
   				'type' 		=> 'image_gallery',
   				'title' 	=> __('Image Gallery', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'typography',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'ids',
   						'type' 		=> 'upload_multi',
   						'title' 	=> __('Image Gallery', 'mfn-opts'),
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'columns',
   						'type' 		=> 'text',
   						'title' 	=> __('Columns', 'mfn-opts'),
   						'desc' 		=> __('min: <b>1</b>, max: <b>9</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> '3',
   					),

   					array(
   						'id' 		=> 'size',
   						'type' 		=> 'select',
   						'title' 	=> __('Size', 'mfn-opts'),
   						'options' 	=> array(
   							'thumbnail' => __('Thumbnail', 'mfn-opts'),
   							'medium' 	=> __('Medium', 'mfn-opts'),
   							'large' 	=> __('Large', 'mfn-opts'),
   							'full' 		=> __('Full Size', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Default', 'mfn-opts'),
   							'flat' 		=> __('Flat', 'mfn-opts'),
   							'fancy' 	=> __('Fancy', 'mfn-opts'),
   							'masonry' 	=> __('Masonry', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> __('Greyscale Images', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Info box -------------------------------------------------------

   			'info_box' => array(
   				'type' 		=> 'info_box',
   				'title' 	=> __('Info Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title'		=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed.', 'mfn-opts'),
   						'std' 		=> '<ul><li>list item 1</li><li>list item 2</li></ul>',
   					),

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Background Image', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// List -----------------------------------------------------------

   			'list' => array(
   				'type' 		=> 'list',
   				'title'		=> __('List', 'mfn-opts'),
   				'size'		=> '1/4',
   				'cat' 		=> 'blocks',
   				'fields'	=> array(

   					array(
   						'id' 		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Icon', 'mfn-opts'),
   						'std' 		=> 'icon-lamp',
   						'class'		=> 'small-text',
   					),

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   					),

						array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'desc' 		=> __('Only <strong>Vertical Style</strong> works for column widths 1/5 & 1/6', 'mfn-opts'),
   						'options' 	=> array(
   							1 => __('With background', 'mfn-opts'),
   							2 => __('Transparent', 'mfn-opts'),
   							3 => __('Vertical', 'mfn-opts'),
   							4 => __('Ordered list', 'mfn-opts'),
   						),
   					),

						// link

   					array(
   						'id' => 'info_link',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Link', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Open in new window', 'mfn-opts'),
   						'desc' 		=> __('Adds a target="_blank" attribute to the link.', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

						// advanced

   					array(
   						'id' => 'info_advanced',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Advanced', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

						// custom

   					array(
   						'id' => 'info_custom',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Custom', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Map Basic ------------------------------------------------------------

   			'map_basic' => array(
   				'type'		=> 'map_basic',
   				'title'		=> __('Map Basic', 'mfn-opts'),
   				'size'		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields'	=> array(

   					// iframe
   					array(
   						'id' 			=> 'info_iframe',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Iframe', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'info_iframe_info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Number of iframe map loads is unlimited.', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id' 			=> 'iframe',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Iframe', 'mfn-opts'),
   						'sub_desc'=> __('Leave this filed blank if you use Embed Map', 'mfn-opts'),
   						'desc'		=> __('Visit <a target="_blank" href="https://google.com/maps">Google Maps</a> and follow these instructions:<br />1. Find place. 2. Click the share button in the left panel. 3. Select "embed a map" 4. Choose size. 5. Click "copy HTML" and paste it above', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// embed
   					array(
   						'id' 			=> 'info_embed',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Embed', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'info_embed_info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Number of embed map loads is unlimited. Google Maps API key is required.<span>Please go to <a target="_blank" href="admin.php?page=be-options">Theme Options</a><strong> > Global > Advanced</strong> and paste your API key in the <strong>Google Maps API Key</strong> field.</span>', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id' 			=> 'address',
   						'type' 		=> 'text',
   						'title' 	=> __('Address or place name', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'zoom',
   						'type' 		=> 'text',
   						'title' 	=> __('Zoom', 'mfn-opts'),
   						'sub_desc'=> __('default: 13', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> 13,
   					),

   					array(
   						'id' 			=> 'height',
   						'type' 		=> 'text',
   						'title' 	=> __('Height', 'mfn-opts'),
   						'sub_desc'=> __('default: 300', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> 300,
   					),

   				),
   			),

   			// Map Advanced ------------------------------------------------------------

   			'map' => array(
   				'type'		=> 'map',
   				'title'		=> __('Map Advanced', 'mfn-opts'),
   				'size'		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields'	=> array(

   					array(
   						'id' 			=> 'info_advanced_info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Number of free dynamic map loads is limited. Google Maps API key is required.<span>Please go to <a target="_blank" href="admin.php?page=be-options">Theme Options</a><strong> > Global > Advanced</strong> and paste your API key in the <strong>Google Maps API Key</strong> field.<br />If you need more than 28500 map loads per month please check current Google Maps <a target="_blank" href="https://cloud.google.com/maps-platform/pricing/">Pricing & Plans</a> or choose Map Basic instead.</span>', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id' 			=> 'lat',
   						'type' 		=> 'text',
   						'title' 	=> __('Google Maps Lat', 'mfn-opts'),
   						'desc' 		=> __('The map will appear only if this field is filled correctly.<br />Example: <b>-33.87</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 			=> 'lng',
   						'type' 		=> 'text',
   						'title' 	=> __('Google Maps Lng', 'mfn-opts'),
   						'desc' 		=> __('The map will appear only if this field is filled correctly.<br />Example: <b>151.21</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 			=> 'zoom',
   						'type' 		=> 'text',
   						'title' 	=> __('Zoom', 'mfn-opts'),
   						'sub_desc'=> __('default: 13', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> 13,
   					),

   					array(
   						'id' 			=> 'height',
   						'type' 		=> 'text',
   						'title' 	=> __('Height', 'mfn-opts'),
   						'sub_desc'=> __('default: 300', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> 300,
   					),

   					// options
   					array(
   						'id' 			=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'type',
   						'type' 		=> 'select',
   						'title' 	=> __('Type', 'mfn-opts'),
   						'options' => array(
   							'ROADMAP' 	=> __('Map', 'mfn-opts'),
   							'SATELLITE' => __('Satellite', 'mfn-opts'),
   							'HYBRID' 		=> __('Satellite + Map', 'mfn-opts'),
   							'TERRAIN' 	=> __('Terrain', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 			=> 'controls',
   						'type' 		=> 'select',
   						'title' 	=> __('Controls', 'mfn-opts'),
   						'options' => array(
   							'' => __('Zoom', 'mfn-opts'),
   							'mapType' => __('Map Type', 'mfn-opts'),
   							'streetView'	=> __('Street View', 'mfn-opts'),
   							'zoom mapType' => __('Zoom & Map Type', 'mfn-opts'),
   							'zoom streetView' => __('Zoom & Street View', 'mfn-opts'),
   							'mapType streetView' => __('Map Type & Street View', 'mfn-opts'),
   							'zoom mapType streetView'	=> __('Zoom, Map Type & Street View', 'mfn-opts'),
   							'hide' => __('Hide All', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 			=> 'draggable',
   						'type' 		=> 'select',
   						'title' 	=> __('Draggable', 'mfn-opts'),
   						'options' => array(
   							'' => __('Enable', 'mfn-opts'),
   							'disable'	=> __('Disable', 'mfn-opts'),
   							'disable-mobile'=> __('Disable on Mobile', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 			=> 'border',
   						'type' 		=> 'select',
   						'title' 	=> __('Border', 'mfn-opts'),
   						'sub_desc'=> __('Show map border', 'mfn-opts'),
   						'options' => array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 			=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'icon',
   						'type' 		=> 'upload',
   						'title' 	=> __('Marker Icon', 'mfn-opts'),
   						'desc' 		=> __('.png', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'color',
   						'type' 		=> 'color',
   						'title' 	=> __('Map color', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'styles',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Styles', 'mfn-opts'),
   						'sub_desc'=> __('Google Maps API styles array', 'mfn-opts'),
   						'desc' 		=> __('You can get predefined styles from <a target="_blank" href="https://snazzymaps.com/explore">snazzymaps.com/explore</a> or generate your own <a target="_blank" href="https://snazzymaps.com/editor">snazzymaps.com/editor</a>', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'latlng',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Additional Markers | Lat,Lng,IconURL', 'mfn-opts'),
   						'desc' 		=> __('Separate Lat,Lng,IconURL[optional] with <b>coma</b> [ , ]<br />Separate multiple Markers with <b>semicolon</b> [ ; ]<br />Example: <b>-33.88,151.21,ICON_URL;-33.89,151.22</b>', 'mfn-opts'),
   					),

   					// contact
   					array(
   						'id' 			=> 'info_contact',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Contact Box', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 			=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Address', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed.', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'telephone',
   						'type' 		=> 'text',
   						'title' 	=> __('Telephone', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'email',
   						'type' 		=> 'text',
   						'title' 	=> __('Email', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'www',
   						'type' 		=> 'text',
   						'title' 	=> __('WWW', 'mfn-opts'),
   					),

   					array(
   						'id'			=> 'style',
   						'type'		=> 'select',
   						'title'		=> __('Style', 'mfn-opts'),
   						'options' => array(
   							'box'		=> __('Contact Box on the map (for full width column/wrap)', 'mfn-opts'),
   							'bar'		=> __('Bar at the top', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 			=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Offer Slider Full ----------------------------------------------

   			'offer' => array(
   				'type' 		=> 'offer',
   				'title' 	=> __('Offer Slider Full', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'info',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('This item can only be used on pages <strong>Without Sidebar</strong>.<br />Please also set Section Style to <strong>Full Width</strong> and use one Item in one Section.', 'mfn-opts'),
   						'class' 	=> 'mfn-info info',
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('offer-types'),
   					),

   					array(
   						'id'		=> 'align',
   						'type'		=> 'select',
   						'title'		=> __('Text Align', 'mfn-opts'),
   						'desc'		=> __('Text align center does not affect title if button is active', 'mfn-opts'),
   						'options' 	=> array(
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   							'center'	=> __('Center', 'mfn-opts'),
   							'justify'	=> __('Justify', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Offer Slider Thumb ---------------------------------------------

   			'offer_thumb' => array(
   				'type' 		=> 'offer_thumb',
   				'title' 	=> __('Offer Slider Thumb', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('offer-types'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options'	=> array(
   							'bottom'	=> __('Thumbnails at the bottom', 'mfn-opts'),
   							''			=> __('Thumbnails on the left', 'mfn-opts'),
   						),
   						'std'		=> 'bottom',
   					),

   					array(
   						'id'		=> 'align',
   						'type'		=> 'select',
   						'title'		=> __('Text Align', 'mfn-opts'),
   						'desc'		=> __('Text align center does not affect title if button is active', 'mfn-opts'),
   						'options' 	=> array(
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   							'center'	=> __('Center', 'mfn-opts'),
   							'justify'	=> __('Justify', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Opening Hours --------------------------------------------------

   			'opening_hours' => array(
   				'type' 		=> 'opening_hours',
   				'title' 	=> __('Opening Hours', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed.', 'mfn-opts'),
   						'std' 		=> "<ul>\n<li><label>Monday - Saturday</label><span>8am - 4pm</span></li>\n</ul>",
   					),

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Background Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Our team -------------------------------------------------------

   			'our_team' => array(
   				'type' 		=> 'our_team',
   				'title' 	=> __('Our Team', 'mfn-opts'),
   				'size'		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'heading',
   						'type' 		=> 'text',
   						'title' 	=> __('Heading', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Photo', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'subtitle',
   						'type' 		=> 'text',
   						'title' 	=> __('Subtitle', 'mfn-opts'),
   					),

   					// description
   					array(
   						'id' 		=> 'info_description',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Description', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'phone',
   						'type' 		=> 'text',
   						'title' 	=> __('Phone', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'content',
   						'type'		=> 'textarea',
   						'title'		=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   					),

   					array(
   						'id' 		=> 'email',
   						'type' 		=> 'text',
   						'title' 	=> __('E-mail', 'mfn-opts'),
   					),

   					// social
   					array(
   						'id' 		=> 'info_social',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Social', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'facebook',
   						'type' 		=> 'text',
   						'title' 	=> __('Facebook', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'twitter',
   						'type' 		=> 'text',
   						'title' 	=> __('Twitter', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'linkedin',
   						'type' 		=> 'text',
   						'title' 	=> __('LinkedIn', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'vcard',
   						'type' 		=> 'text',
   						'title' 	=> __('vCard', 'mfn-opts'),
   					),

   					// other
   					array(
   						'id' 		=> 'info_other',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Other', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'blockquote',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Blockquote', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options'	=> array(
   							'circle'		=> __('Circle', 'mfn-opts'),
   							'vertical'		=> __('Vertical', 'mfn-opts'),
   							'horizontal'	=> __('Horizontal [only: 1/2]', 'mfn-opts'),
   						),
   						'std'		=> 'vertical',
   					),

   					// link
   					array(
   						'id' 		=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link',
   						'type'		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Our team list --------------------------------------------------

   			'our_team_list' => array(
   				'type' 		=> 'our_team_list',
   				'title' 	=> __('Our Team List', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Photo', 'mfn-opts'),
   						'desc' 		=> __('Recommended minimum image width: <b>768px</b>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'subtitle',
   						'type' 		=> 'text',
   						'title' 	=> __('Subtitle', 'mfn-opts'),
   					),

   					// description
   					array(
   						'id' 		=> 'info_description',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Description', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'phone',
   						'type' 		=> 'text',
   						'title' 	=> __('Phone', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'content',
   						'type'		=> 'textarea',
   						'title'		=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   					),

   					array(
   						'id'		=> 'blockquote',
   						'type'		=> 'textarea',
   						'title'		=> __('Blockquote', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'email',
   						'type' 		=> 'text',
   						'title' 	=> __('E-mail', 'mfn-opts'),
   					),

   					// social
   					array(
   						'id' 		=> 'info_social',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Social', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'facebook',
   						'type' 		=> 'text',
   						'title' 	=> __('Facebook', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'twitter',
   						'type' 		=> 'text',
   						'title' 	=> __('Twitter', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'linkedin',
   						'type' 		=> 'text',
   						'title' 	=> __('LinkedIn', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'vcard',
   						'type' 		=> 'text',
   						'title' 	=> __('vCard', 'mfn-opts'),
   					),

   					// link
   					array(
   						'id' 		=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link',
   						'type'		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Photo Box ------------------------------------------------------

   			'photo_box' => array(
   				'type' 		=> 'photo_box',
   				'title' 	=> __('Photo Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id'			=> 'title',
   						'type'		=> 'text',
   						'title'		=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('Allowed HTML tags: span, strong, b, em, i, u', 'mfn-opts'),
   					),

   					array(
   						'id'			=> 'image',
   						'type'		=> 'upload',
   						'title'		=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id'			=> 'content',
   						'type'		=> 'textarea',
   						'title'		=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   					),

   					array(
   						'id'			=> 'align',
   						'type'		=> 'select',
   						'title'		=> __('Text Align', 'mfn-opts'),
   						'options' => array(
   							''				=> __('Center', 'mfn-opts'),
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   						),
   					),

   					// link
   					array(
   						'id' 			=> 'info_link',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Link', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'link',
   						'type'		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 					=> __('Default | _self', 'mfn-opts'),
   							1 					=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 			=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'			=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> __('Greyscale Images', 'mfn-opts'),
   						'desc'		=> __('Works only for images with link', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 			=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc'=> __('Entrance animation', 'mfn-opts'),
   						'options' => $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 			=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Portfolio ------------------------------------------------------

   			'portfolio' => array(
   				'type'		=> 'portfolio',
   				'title'		=> __('Portfolio', 'mfn-opts'),
   				'size'		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id'		=> 'count',
   						'type'		=> 'text',
   						'title'		=> __('Count', 'mfn-opts'),
   						'class'		=> 'small-text',
   						'std'		=> 3,
   					),

   					array(
   						'id'		=> 'style',
   						'type'		=> 'select',
   						'title'		=> __('Style', 'mfn-opts'),
   						'desc' 		=> __('If you do not know what <b>image size</b> is being used for selected style, please navigate to the: Appearance > <a target="_blank" href="admin.php?page=be-options">Theme Options</a> > Blog, Portfolio & Shop > <b>Featured Images</b>', 'mfn-opts'),
   						'options' 	=> array(
   							'flat' => __('Flat', 'mfn-opts'),
   							'grid' => __('Grid', 'mfn-opts'),
   							'masonry' => __('Masonry Blog Style', 'mfn-opts'),
   							'masonry-hover' => __('Masonry Hover Description', 'mfn-opts'),
   							'masonry-minimal' => __('Masonry Minimal', 'mfn-opts'),
   							'masonry-flat' => __('Masonry Flat', 'mfn-opts'),
   							'list' => __('List', 'mfn-opts'),
   							'exposure' => __('Exposure', 'mfn-opts'),
   						),
   						'std' 		=> 'grid'
   					),

   					array(
   						'id' 		=> 'columns',
   						'type' 		=> 'select',
   						'title' 	=> __('Columns', 'mfn-opts'),
   						'desc' 		=> __('Default: 3. Recommended: 2-4. Too large value may crash the layout.<br />This option works in styles: <b>Flat, Grid, Masonry Blog Style, Masonry Hover Description</b>', 'mfn-opts'),
   						'options'	 => array(
   							2	=> 2,
   							3	=> 3,
   							4	=> 4,
   							5	=> 5,
   							6	=> 6,
   						),
   						'std' 		=> 3,
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('portfolio-types'),
   						'wpml'		=> 'portfolio-types',
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories <b>slugs</b>', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'desc' 		=> __('Do not use random order with pagination or load more', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'exclude_id',
   						'type'		=> 'text',
   						'title'		=> __('Exclude Posts', 'mfn-opts'),
   						'sub_desc'	=> __('Posts <b>IDs</b>', 'mfn-opts'),
   						'desc'		=> __('IDs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'related',
   						'type' 		=> 'select',
   						'title' 	=> __('Use as Related Projects', 'mfn-opts'),
   						'sub_desc' 	=> __('use on Single Project page', 'mfn-opts'),
   						'desc' 		=> __('Exclude current Project. This option will overwrite Exclude Posts option above', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'filters',
   						'type' 		=> 'select',
   						'title' 	=> __('Filters', 'mfn-opts'),
   						'desc' 		=> __('Works only with <b>Category: All</b> or Multiple Categories (only selected categories show in filters)', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'pagination',
   						'type' 		=> 'select',
   						'title' 	=> __('Pagination', 'mfn-opts'),
   						'desc'		=> __('<strong>Notice:</strong> Pagination will <strong>not</strong> work if you put item on Homepage of WordPress Multilingual Site', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'load_more',
   						'type' 		=> 'select',
   						'title' 	=> __('Load More button', 'mfn-opts'),
   						'desc' 		=> __('This will replace all sliders on list with featured images. Please also <b>show Pagination</b>', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> 'Greyscale Images',
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Portfolio Grid -------------------------------------------------

   			'portfolio_grid' => array(
   				'type'		=> 'portfolio_grid',
   				'title'		=> __('Portfolio Grid', 'mfn-opts'),
   				'size'		=> '1/4',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id'		=> 'count',
   						'type'		=> 'text',
   						'title'		=> __('Count', 'mfn-opts'),
   						'std'		=> '4',
   						'class'		=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('portfolio-types'),
   						'wpml'		=> 'portfolio-types',
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories Slugs', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> 'Greyscale Images',
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Portfolio Photo ------------------------------------------------

   			'portfolio_photo' => array(
   				'type'		=> 'portfolio_photo',
   				'title'		=> __('Portfolio Photo', 'mfn-opts'),
   				'size'		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id'		=> 'count',
   						'type'		=> 'text',
   						'title'		=> __('Count', 'mfn-opts'),
   						'std'		=> '5',
   						'class'		=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('portfolio-types'),
   						'wpml'		=> 'portfolio-types',
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories <b>slugs</b>', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Open in new window', 'mfn-opts'),
   						'desc' 		=> __('Adds a target="_blank" attribute to the link.', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id'		=> 'greyscale',
   						'type'		=> 'select',
   						'title'		=> 'Greyscale Images',
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id'		=> 'margin',
   						'type'		=> 'select',
   						'title'		=> __('Margin', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Portfolio Slider -----------------------------------------------

   			'portfolio_slider' => array(
   				'type'		=> 'portfolio_slider',
   				'title'		=> __('Portfolio Slider', 'mfn-opts'),
   				'size'		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields'	=> array(

   					array(
   						'id'		=> 'count',
   						'type'		=> 'text',
   						'title'		=> __('Count', 'mfn-opts'),
   						'desc'		=> __('We <strong>do not</strong> recommend use more than 10 items, because site will be working slowly.', 'mfn-opts'),
   						'std'		=> '6',
   						'class'		=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'category',
   						'type'		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('portfolio-types'),
   						'sub_desc'	=> __('Select the portfolio post category.', 'mfn-opts'),
   						'wpml'		=> 'portfolio-types',
   					),

   					array(
   						'id'		=> 'category_multi',
   						'type'		=> 'text',
   						'title'		=> __('Multiple Categories', 'mfn-opts'),
   						'sub_desc'	=> __('Categories Slugs', 'mfn-opts'),
   						'desc'		=> __('Slugs should be separated with <strong>coma</strong> (,).', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'orderby',
   						'type'		=> 'select',
   						'title'		=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   							'rand'			=> __('Random', 'mfn-opts'),
   						),
   						'std'		=> 'date'
   					),

   					array(
   						'id'		=> 'order',
   						'type'		=> 'select',
   						'title'		=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std'		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'arrows',
   						'type'		=> 'select',
   						'title'		=> __('Navigation', 'mfn-opts'),
   						'sub_desc'	=> __('Navigation arrows', 'mfn-opts'),
   						'options'	=> array(
   							''			=> __('None', 'mfn-opts'),
   							'hover' 	=> __('Show on Hover', 'mfn-opts'),
   							'always' 	=> __('Always Show', 'mfn-opts'),
   						),
   					),

   					array(
   						'id'		=> 'size',
   						'type'		=> 'select',
   						'title'		=> __('Size', 'mfn-opts'),
   						'sub_desc'	=> __('Image size', 'mfn-opts'),
   						'options'	=> array(
   							'small'		=> __('Small', 'mfn-opts'),
   							'medium' 	=> __('Medium', 'mfn-opts'),
   							'large' 	=> __('Large', 'mfn-opts'),
   						),
   					),

   					array(
   						'id'		=> 'scroll',
   						'type'		=> 'select',
   						'title'		=> __('Slides to scroll', 'mfn-opts'),
   						'options'	=> array(
   							'page'		=> __('One Page', 'mfn-opts'),
   							'slide' 	=> __('Single Slide', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Pricing item ---------------------------------------------------

   			'pricing_item' => array(
   				'type' 		=> 'pricing_item',
   				'title' 	=> __('Pricing Item', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'blocks',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title' 	=> __('Image', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'sub_desc' 	=> __('Pricing item title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'price',
   						'type' 		=> 'text',
   						'title' 	=> __('Price', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'currency',
   						'type'		=> 'text',
   						'title' 	=> __('Currency', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'currency_pos',
   						'type'		=> 'select',
   						'title' 	=> __('Currency | Position', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'period',
   						'type' 		=> 'text',
   						'title' 	=> __('Period', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// description
   					array(
   						'id' 		=> 'info_description',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Description', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id'		=> 'subtitle',
   						'type'		=> 'text',
   						'title'		=> __('Subtitle', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed.', 'mfn-opts'),
   						'std' 		=> "<ul>\n<li><strong>List</strong> item</li>\n</ul>",
   					),

   					// button
   					array(
   						'id' 		=> 'info_button',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Button', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'link_title',
   						'type' 		=> 'text',
   						'title' 	=> __('Button | Title', 'mfn-opts'),
   						'desc' 		=> __('Button will appear only if this field will be filled.', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'icon',
   						'type' 		=> 'icon',
   						'title' 	=> __('Button | Icon', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Button | Link', 'mfn-opts'),
   						'desc' 		=> __('Button will appear only if this field will be filled.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Button | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'featured',
   						'type' 		=> 'select',
   						'title' 	=> __('Featured', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							'box'	=> __('Box', 'mfn-opts'),
   							'label'	=> __('Table Label', 'mfn-opts'),
   							'table'	=> __('Table', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Progress Bars  -------------------------------------------------

   			'progress_bars' => array(
   				'type' 		=> 'progress_bars',
   				'title' 	=> __('Progress Bars', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Please use <strong>[bar title="Title" value="50" size="20" color=""]</strong> shortcodes here.', 'mfn-opts'),
   						'std' 		=> '[bar title="Bar1" value="50" size="20" color=""]'."\n".'[bar title="Bar2" value="60" size="20" color=""]',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Promo Box ------------------------------------------------------

   			'promo_box' => array(
   				'type'		=> 'promo_box',
   				'title'		=> __('Promo Box', 'mfn-opts'),
   				'size'		=> '1/2',
   				'cat' 		=> 'boxes',
   				'fields'	=> array(

   					array(
   						'id'		=> 'image',
   						'type'		=> 'upload',
   						'title'		=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended minimum image width: <b>768px</b>', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'title',
   						'type'		=> 'text',
   						'title'		=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'content',
   						'type'		=> 'textarea',
   						'title'		=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   					),

   					// button
   					array(
   						'id' 		=> 'info_button',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Button', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'btn_text',
   						'type' 		=> 'text',
   						'title' 	=> __('Button | Text', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),
   					array(
   						'id' 		=> 'btn_link',
   						'type' 		=> 'text',
   						'title' 	=> __('Button | Link', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Button | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'position',
   						'type' 		=> 'select',
   						'title' 	=> __('Image position', 'mfn-opts'),
   						'options' 	=> array(
   							'left' 	=> __('Left', 'mfn-opts'),
   							'right' => __('Right', 'mfn-opts'),
   						),
   						'std'		=> 'left',
   					),

   					array(
   						'id' 		=> 'border',
   						'type' 		=> 'select',
   						'title' 	=> __('Border', 'mfn-opts'),
   						'sub_desc' 	=> __('Show right border', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Quick Fact -----------------------------------------------------

   			'quick_fact' => array(
   				'type' 		=> 'quick_fact',
   				'title' 	=> __('Quick Fact', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'heading',
   						'type' 		=> 'text',
   						'title' 	=> __('Heading', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   				),

   				array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   						'validate' 	=> 'html',
   					),

   					// quick fact
   					array(
   						'id' 		=> 'info_quick',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Quick Fact', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'number',
   						'type' 		=> 'text',
   						'title'		=> __('Number', 'mfn-opts'),
   						'class'		=> 'small-text',
   					),

   					array(
   						'id' 		=> 'prefix',
   						'type' 		=> 'text',
   						'title' 	=> __('Prefix', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'label',
   						'type' 		=> 'text',
   						'title' 	=> __('Postfix', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'align',
   						'type' 		=> 'select',
   						'title' 	=> __('Align', 'mfn-opts'),
   						'options' 	=> array(
   							''			=> __('Center', 'mfn-opts'),
   							'left'		=> __('Left', 'mfn-opts'),
   							'right'		=> __('Right', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Shop ----------------------------------------------------

   			'shop' => array(
   				'type' 		=> 'shop',
   				'title' 	=> __('Shop', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id' => 'limit',
   						'type' => 'text',
   						'title' => __('Number of products', 'mfn-opts'),
   						'std' => '6',
   						'class' => 'small-text',
   					),

   					array(
   						'id' => 'columns',
   						'type' => 'select',
   						'title' => __('Columns', 'mfn-opts'),
							'options' => array(
								2 => 2,
								3 => 3,
								4 => 4,
							),
   						'std' => '3',

   					),

						array(
   						'id' => 'type',
   						'type' => 'select',
   						'title' => __('Display', 'mfn-opts'),
   						'options'	=> array(
   							'products' => __('-- Default --', 'mfn-opts'),
   							'sale_products' => __('On sale', 'mfn-opts'),
   							'best_selling_products' => __('Best selling (order by: Sales)', 'mfn-opts'),
   							'top_rated_products' => __('Top-rated (order by: Rating)', 'mfn-opts'),
   						),
   					),

						// options

   					array(
   						'id' => 'info_options',
   						'type' => 'info',
   						'title' => '',
   						'desc' => __('Options', 'mfn-opts'),
   						'class' => 'mfn-info',
   					),

   					array(
   						'id' => 'category',
   						'type' => 'select',
   						'title' => __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('product_cat'),
   					),

   					array(
   						'id' => 'orderby',
   						'type' => 'select',
   						'title' => __('Order by', 'mfn-opts'),
   						'options' => array(
   							'date' => __('Date the product was published', 'mfn-opts'),
   							'id' => __('ID of the product', 'mfn-opts'),
   							'menu_order' => __('Menu order (if set)', 'mfn-opts'),
   							'popularity' => __('Popularity (number of purchases)', 'mfn-opts'),
   							'rating' => __('Rating', 'mfn-opts'),
   							'title' => __('Title', 'mfn-opts'),
								'rand' => __('Random (do not use with pagination)', 'mfn-opts'),
   						),
   						'std' => 'title'
   					),

   					array(
   						'id' => 'order',
   						'type' => 'select',
   						'title' => __('Order', 'mfn-opts'),
   						'options' => array(
   							'ASC' => __('Ascending', 'mfn-opts'),
   							'DESC' => __('Descending', 'mfn-opts'),
   						),
   						'std'	=> 'ASC'
   					),

						// advanced

						array(
							'id' => 'info_advanced',
							'type' => 'info',
							'title' => '',
							'desc' => __('Advanced', 'mfn-opts'),
							'class' => 'mfn-info',
						),

						array(
   						'id' => 'paginate',
   						'type' => 'select',
   						'title' => __('Pagination', 'mfn-opts'),
   						'options' => array(
   							0 => __('Hide', 'mfn-opts'),
   							1 => __('Show', 'mfn-opts'),
   						),
   						'std'	=> 0,
   					),


						// custom

						array(
							'id' => 'info_custom',
							'type' => 'info',
							'title' => '',
							'desc' => __('Custom', 'mfn-opts'),
							'class' => 'mfn-info',
						),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Shop Slider ----------------------------------------------------

   			'shop_slider' => array(
   				'type' 		=> 'shop_slider',
   				'title' 	=> __('Shop Slider', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'count',
   						'type' 		=> 'text',
   						'title' 	=> __('Count', 'mfn-opts'),
   						'sub_desc' 	=> __('Number of posts to show', 'mfn-opts'),
   						'desc'		=> __('We <strong>do not</strong> recommend use more than 10 items, because site will be working slowly.', 'mfn-opts'),
   						'std' 		=> '5',
   						'class' 	=> 'small-text',
   					),

   					array(
   						'id' 		=> 'show',
   						'type' 		=> 'select',
   						'title'		=> __('Show', 'mfn-opts'),
   						'options'	=> array(
   							''				=> __('All (or category selected below)', 'mfn-opts'),
   							'featured'		=> __('Featured', 'mfn-opts'),
   							'onsale'		=> __('Onsale', 'mfn-opts'),
   							'best-selling'	=> __('Best Selling (order by: Sales)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('product_cat'),
   						'sub_desc'	=> __('Select the products category', 'mfn-opts'),
   					),

   					array(
   						'id' => 'orderby',
   						'type' => 'select',
   						'title' => __('Order by', 'mfn-opts'),
   						'options' => array(
   							'date' => __('Date', 'mfn-opts'),
   							'title' => __('Title', 'mfn-opts'),
   						),
   						'std' => 'date'
   					),

   					array(
   						'id' 		=> 'order',
   						'type' 		=> 'select',
   						'title' 	=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std' 		=> 'DESC'
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Sidebar Widget -------------------------------------------------

   			'sidebar_widget' => array(
   				'type' 		=> 'sidebar_widget',
   				'title' 	=> __('Sidebar Widget', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'other',
   				'fields' 	=> array(

   					array(
   						'id'		=> 'sidebar',
   						'type' 		=> 'select',
   						'title' 	=> __('Select Sidebar', 'mfn-opts'),
   						'desc' 		=> __('1. Create Sidebar in Theme Options > Getting Started > Sidebars.<br />2. Add Widget.<br />3. Select your sidebar.', 'mfn-opts'),
   						'options' 	=> mfn_opts_get('sidebars'),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Slider ---------------------------------------------------------

   			'slider' => array(
   				'type' 		=> 'slider',
   				'title' 	=> __('Slider', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'blocks',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('slide-types'),
   						'sub_desc'	=> __('Select the slides category', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'orderby',
   						'type' 		=> 'select',
   						'title' 	=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   						),
   						'std' 		=> 'date'
   					),

   					array(
   						'id' 		=> 'order',
   						'type' 		=> 'select',
   						'title' 	=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std' 		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'options' 	=> array(
   							''				=> __('Default', 'mfn-opts'),
   							'flat' 			=> __('Flat', 'mfn-opts'),
   							'description'	=> __('Flat with title and description', 'mfn-opts'),
   							'carousel' 		=> __('Flat carousel with titles', 'mfn-opts'),
   							'center' 		=> __('Center mode', 'mfn-opts'),
   						),
   						'title' 	=> __('Style', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'navigation',
   						'type' 		=> 'select',
   						'title' 	=> __('Navigation', 'mfn-opts'),
   						'options'	=> array(
   							''					=> __('Default', 'mfn-opts'),
   							'hide-arrows'		=> __('Hide Arrows', 'mfn-opts'),
   							'hide-dots'			=> __('Hide Dots', 'mfn-opts'),
   							'hide'				=> __('Hide', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Slider Plugin --------------------------------------------------

   			'slider_plugin' => array(
   				'type' 		=> 'slider_plugin',
   				'title' 	=> __('Slider Plugin', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'other',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'rev',
   						'type' 		=> 'select',
   						'title' 	=> __('Slider | Revolution Slider', 'mfn-opts'),
   						'desc' 		=> __('Select one from the list of available <a target="_blank" href="admin.php?page=revslider">Revolution Sliders</a>', 'mfn-opts'),
   						'options' 	=> $this->sliders['rev'],
   					),

   					array(
   						'id' 		=> 'layer',
   						'type' 		=> 'select',
   						'title' 	=> __('Slider | Layer Slider', 'mfn-opts'),
   						'desc' 		=> __('Select one from the list of available <a target="_blank" href="admin.php?page=layerslider">Layer Sliders</a>', 'mfn-opts'),
   						'options' 	=> $this->sliders['layer'],
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Sliding Box ----------------------------------------------------

   			'sliding_box' => array(
   				'type' 		=> 'sliding_box',
   				'title' 	=> __('Sliding Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 			=> 'image',
   						'type' 		=> 'upload',
   						'title'		=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('Allowed HTML tags: span, strong, b, em, i, u', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 			=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 					=> __('Default | _self', 'mfn-opts'),
   							1 					=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

						// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc'=> __('Entrance animation', 'mfn-opts'),
   						'options' => $this->get_animations(),
   					),

						// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 			=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Story Box ------------------------------------------------------

   			'story_box' => array(
   				'type' 		=> 'story_box',
   				'title' 	=> __('Story Box', 'mfn-opts'),
   				'size' 		=> '1/2',
   				'cat' 		=> 'boxes',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'image',
   						'type' 		=> 'upload',
   						'title'		=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>750px - 1500px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							''			=> __('Horizontal Image', 'mfn-opts'),
   							'vertical' 	=> __('Vertical Image', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'textarea',
   						'title' 	=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('Some Shortcodes and HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width sc',
   						'validate' 	=> 'html',
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

						// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

						// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Tabs -----------------------------------------------------------

   			'tabs' => array(
   				'type' 		=> 'tabs',
   				'title' 	=> __('Tabs', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'blocks',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					// tabs
   					array(
   						'id' 		=> 'info_tabs',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Tabs', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'tabs',
   						'type' 		=> 'tabs',
   						'title' 	=> '',
   						'sub_desc' 	=> __('To add an <strong>icon</strong> in Title field, please use the following code:<br/><br/>&lt;i class=" icon-lamp"&gt;&lt;/i&gt; Tab Title', 'mfn-opts'),
   						'desc' 		=> __('<b>JavaScript</b> content like Google Maps and some plugins shortcodes do <b>not work</b> in tabs. You can use Drag & Drop to set the order', 'mfn-opts'),
   					),

   					// options
   					array(
   						'id' 		=> 'info_options',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Options', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'type',
   						'type' 		=> 'select',
   						'title' 	=> __('Style', 'mfn-opts'),
   						'desc' 		=> __('Vertical tabs works only for column widths: 1/2, 3/4 & 1/1', 'mfn-opts'),
   						'options' 	=> array(
   							'horizontal'	=> __('Horizontal', 'mfn-opts'),
   							'centered'		=> __('Horizontal (centered tab)', 'mfn-opts'),
   							'vertical' 		=> __('Vertical', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'padding',
   						'type' 		=> 'text',
   						'title' 	=> __('Content Padding', 'mfn-opts'),
   						'sub_desc' 	=> __('Leave empty to use defult padding', 'mfn-opts'),
   						'desc' 		=> __('Use value with <b>px</b> or <b>%</b>. Example: <b>20px</b> or <b>15px 20px 20px</b> or <b>20px 1%</b>', 'mfn-opts'),
   						'class' 	=> 'small-text',
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' => 'uid',
   						'type' => 'text',
   						'title' => __('Unique ID [optional]', 'mfn-opts'),
   						'sub_desc' => __('Allowed characters: "a-z" "-" "_"', 'mfn-opts'),
   						'desc' => __('Use this option if you want to open specified tab from link (does not work on the same page).<br />For example: Your Unique ID is <strong>offer</strong> and you want to open 2nd tab, please use link: <strong>your-url/#offer-2</strong>', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Testimonials ---------------------------------------------------

   			'testimonials' => array(
   				'type' 		=> 'testimonials',
   				'title' 	=> __('Testimonials', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('testimonial-types'),
   						'sub_desc'	=> __('Select the testimonial post category.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'orderby',
   						'type' 		=> 'select',
   						'title' 	=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   						),
   						'std' 		=> 'date'
   					),

   					array(
   						'id' 		=> 'order',
   						'type' 		=> 'select',
   						'title' 	=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std' 		=> 'DESC'
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' => 'style',
   						'type' => 'select',
   						'title' => __('Style', 'mfn-opts'),
   						'options' => array(
   							'' => __('Default', 'mfn-opts'),
   							'single-photo' 	=> __('Single Photo', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'hide_photos',
   						'type' 		=> 'select',
   						'title'		=> __('Hide Photos', 'mfn-opts'),
   						'options' 	=> array(
   							0 => __('No', 'mfn-opts'),
   							1 => __('Yes', 'mfn-opts'),
   						),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Testimonials List ----------------------------------------------

   			'testimonials_list' => array(
   				'type' 		=> 'testimonials_list',
   				'title' 	=> __('Testimonials List', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'loops',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'category',
   						'type' 		=> 'select',
   						'title'		=> __('Category', 'mfn-opts'),
   						'options'	=> mfn_get_categories('testimonial-types'),
   						'sub_desc'	=> __('Select the testimonial post category.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'orderby',
   						'type' 		=> 'select',
   						'title' 	=> __('Order by', 'mfn-opts'),
   						'options' 	=> array(
   							'date'			=> __('Date', 'mfn-opts'),
   							'menu_order' 	=> __('Menu order', 'mfn-opts'),
   							'title'			=> __('Title', 'mfn-opts'),
   						),
   						'std' 		=> 'date'
   					),

   					array(
   						'id' 		=> 'order',
   						'type' 		=> 'select',
   						'title' 	=> __('Order', 'mfn-opts'),
   						'options'	=> array(
   							'ASC' 	=> __('Ascending', 'mfn-opts'),
   							'DESC' 	=> __('Descending', 'mfn-opts'),
   						),
   						'std' 		=> 'DESC'
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title'		=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Default', 'mfn-opts'),
   							'quote' 	=> __('Quote above the author', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Timeline -------------------------------------------------------

   			'timeline' => array(
   				'type' 		=> 'timeline',
   				'title' 	=> __('Timeline', 'mfn-opts'),
   				'size' 		=> '1/1',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'tabs',
   						'type' 		=> 'tabs',
   						'title' 	=> __('Timeline', 'mfn-opts'),
   						'sub_desc' 	=> __('Please add <strong>date</strong> wrapped into <strong>span</strong> tag in Title field.<br/><br/>&lt;span&gt;2013&lt;/span&gt;Event Title', 'mfn-opts'),
   						'desc' 		=> __('You can use Drag & Drop to set the order.', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Trailer Box ----------------------------------------------------

   			'trailer_box' => array(
   				'type' => 'trailer_box',
   				'title' => __('Trailer Box', 'mfn-opts'),
   				'size' => '1/4',
   				'cat' => 'boxes',
   				'fields' => array(

   					array(
   						'id' => 'image',
   						'type' => 'upload',
   						'title' => __('Image', 'mfn-opts'),
   						'desc' => __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

						array(
   						'id' => 'orientation',
   						'type' => 'select',
   						'title' => __('Image | Orientation', 'mfn-opts'),
   						'options' => array(
   							'' => __('Vertical', 'mfn-opts'),
   							'horizontal' => __('Horizontal', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'slogan',
   						'type' 		=> 'text',
   						'title' 	=> __('Slogan', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'link',
   						'type' 		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					// advanced
   					array(
   						'id' 		=> 'info_advanced',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Advanced', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'style',
   						'type' 		=> 'select',
   						'title'		=> __('Style', 'mfn-opts'),
   						'options' 	=> array(
   							'' 			=> __('Default', 'mfn-opts'),
   							'plain' 	=> __('Plain', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'animate',
   						'type' 		=> 'select',
   						'title' 	=> __('Animation', 'mfn-opts'),
   						'desc' 		=> __('<b>Notice:</b> In some versions of Safari browser Hover works only if you select: <b>Not Animated</b> or <b>Fade In</b>', 'mfn-opts'),
   						'sub_desc' 	=> __('Entrance animation', 'mfn-opts'),
   						'options' 	=> $this->get_animations(),
   					),

   					// custom
   					array(
   						'id' 		=> 'info_custom',
   						'type' 		=> 'info',
   						'title' 	=> '',
   						'desc' 		=> __('Custom', 'mfn-opts'),
   						'class' 	=> 'mfn-info',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Video  --------------------------------------------

   			'video' => array(
   				'type' 		=> 'video',
   				'title' 	=> __('Video', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'elements',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'video',
   						'type' 		=> 'text',
   						'title' 	=> __('YouTube or Vimeo | Video ID', 'mfn-opts'),
   						'sub_desc' 	=> __('YouTube or Vimeo', 'mfn-opts'),
   						'desc' 		=> __('It`s placed in every YouTube & Vimeo video, for example:<br /><b>YouTube:</b> http://www.youtube.com/watch?v=<u>WoJhnRczeNg</u><br /><b>Vimeo:</b> http://vimeo.com/<u>62954028</u>', 'mfn-opts'),
   						'class' 	=> 'small-text'
   					),

   					array(
   						'id' 		=> 'parameters',
   						'type' 		=> 'text',
   						'title' 	=> __('YouTube or Vimeo | Parameters', 'mfn-opts'),
   						'sub_desc' 	=> __('YouTube or Vimeo', 'mfn-opts'),
   						'desc' 		=> __('Multiple parameters should be connected with "&"<br />For example: <b>autoplay=1&loop=1</b><br /><br />Vimeo authors may disable some parameters for their videos', 'mfn-opts'),
   					),

   					array(
   						'id'			=> 'mp4',
   						'type'		=> 'upload',
   						'title'		=> __('HTML5 | MP4 video', 'mfn-opts'),
   						'sub_desc'=> __('m4v [.mp4]', 'mfn-opts'),
   						'desc'		=> __('Please add both mp4 and ogv for cross-browser compatibility.', 'mfn-opts'),
   						'data'		=> 'video',
   					),

   					array(
   						'id'			=> 'ogv',
   						'type'		=> 'upload',
   						'title'		=> __('HTML5 | OGV video', 'mfn-opts'),
   						'sub_desc'=> __('ogg [.ogv]', 'mfn-opts'),
   						'data'		=> 'video',
   					),

   					array(
   						'id'		=> 'placeholder',
   						'type'		=> 'upload',
   						'title'		=> __('HTML5 | Placeholder image', 'mfn-opts'),
   						'desc'		=> __('Placeholder Image will be used as video placeholder before video loads and on mobile devices.', 'mfn-opts'),
   					),

   					array(
   						'id'			=> 'html5_parameters',
   						'type'		=> 'select',
   						'title'		=> __('HTML5 | Parameters', 'mfn-opts'),
   						'desc'		=> __('Recent versions of WebKit browsers and iOS do not support autoplay.', 'mfn-opts'),
   						'options' => array(
   							''				=> __('autoplay controls loop muted', 'mfn-opts'),
   							'a;c;l;'	=> __('autoplay controls loop', 'mfn-opts'),
   							'a;c;;m'	=> __('autoplay controls muted', 'mfn-opts'),
   							'a;;l;m'	=> __('autoplay loop muted', 'mfn-opts'),
   							'a;c;;'		=> __('autoplay controls', 'mfn-opts'),
   							'a;;l;'		=> __('autoplay loop', 'mfn-opts'),
   							'a;;;m'		=> __('autoplay muted', 'mfn-opts'),
   							'a;;;'		=> __('autoplay', 'mfn-opts'),
   							';c;l;m'	=> __('controls loop muted', 'mfn-opts'),
   							';c;l;'		=> __('controls loop', 'mfn-opts'),
   							';c;;m'		=> __('controls muted', 'mfn-opts'),
   							';c;;'		=> __('controls', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'width',
   						'type' 		=> 'text',
   						'title' 	=> __('Width', 'mfn-opts'),
   						'desc' 		=> __('px', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> 700,
   					),

   					array(
   						'id' 		=> 'height',
   						'type' 		=> 'text',
   						'title' 	=> __('Height', 'mfn-opts'),
   						'desc' 		=> __('px', 'mfn-opts'),
   						'class' 	=> 'small-text',
   						'std' 		=> 400,
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Visual Editor  -------------------------------------------------

   			'visual' => array(
   				'type' 		=> 'visual',
   				'title' 	=> __('Visual Editor', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'other',
   				'fields' 	=> array(

   					array(
   						'id' 		=> 'title',
   						'type' 		=> 'text',
   						'title' 	=> __('Title', 'mfn-opts'),
   						'desc' 		=> __('This field is used as an Item Label in admin panel only', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'content',
   						'type' 		=> 'visual',
   						'title' 	=> __('Visual Editor', 'mfn-opts'),
   // 						'param' 	=> 'editor',
   // 						'validate' 	=> 'html',
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   			// Zoom Box -------------------------------------------------------

   			'zoom_box' => array(
   				'type' 		=> 'zoom_box',
   				'title' 	=> __('Zoom Box', 'mfn-opts'),
   				'size' 		=> '1/4',
   				'cat' 		=> 'boxes',
   				'fields'	=> array(

   					array(
   						'id'		=> 'image',
   						'type'		=> 'upload',
   						'title'		=> __('Image', 'mfn-opts'),
   						'desc' 		=> __('Recommended image width: <b>768px - 1920px</b>, depending on size of the item', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'bg_color',
   						'type' 		=> 'color',
   						'title' 	=> __('Overlay background', 'mfn-opts'),
   						'std' 		=> '#CCCCCC',
   					),

   					array(
   						'id'		=> 'content_image',
   						'type'		=> 'upload',
   						'title'		=> __('Content Image', 'mfn-opts'),
   					),

   					array(
   						'id'		=> 'content',
   						'type'		=> 'textarea',
   						'title'		=> __('Content', 'mfn-opts'),
   						'desc' 		=> __('HTML tags allowed', 'mfn-opts'),
   						'class'		=> 'full-width',
   					),

   					array(
   						'id' 		=> 'link',
   						'type'		=> 'text',
   						'title' 	=> __('Link', 'mfn-opts'),
   					),

   					array(
   						'id' 		=> 'target',
   						'type' 		=> 'select',
   						'title' 	=> __('Link | Target', 'mfn-opts'),
   						'options'	=> array(
   							0 			=> __('Default | _self', 'mfn-opts'),
   							1 			=> __('New Tab or Window | _blank', 'mfn-opts'),
   							'lightbox' 	=> __('Lightbox (image or embed video)', 'mfn-opts'),
   						),
   					),

   					array(
   						'id' 		=> 'classes',
   						'type' 		=> 'text',
   						'title' 	=> __('Custom | Classes', 'mfn-opts'),
   						'sub_desc'	=> __('Custom CSS Item Classes Names', 'mfn-opts'),
   						'desc'		=> __('Multiple classes should be separated with SPACE', 'mfn-opts'),
   					),

   				),
   			),

   		);

   	}

		/**
		 * SET item entrance animations
		 */

		private function set_animations(){

			$this->animations = array(
				'' => esc_html__('- Not Animated -', 'mfn-opts'),
				'fadeIn' => esc_html__('Fade In', 'mfn-opts'),
				'fadeInUp' => esc_html__('Fade In Up', 'mfn-opts'),
				'fadeInDown' => esc_html__('Fade In Down ', 'mfn-opts'),
				'fadeInLeft' => esc_html__('Fade In Left', 'mfn-opts'),
				'fadeInRight' => esc_html__('Fade In Right ', 'mfn-opts'),
				'fadeInUpLarge' => esc_html__('Fade In Up Large', 'mfn-opts'),
				'fadeInDownLarge' => esc_html__('Fade In Down Large', 'mfn-opts'),
				'fadeInLeftLarge' => esc_html__('Fade In Left Large', 'mfn-opts'),
				'fadeInRightLarge' => esc_html__('Fade In Right Large', 'mfn-opts'),
				'zoomIn' => esc_html__('Zoom In', 'mfn-opts'),
				'zoomInUp' => esc_html__('Zoom In Up', 'mfn-opts'),
				'zoomInDown' => esc_html__('Zoom In Down', 'mfn-opts'),
				'zoomInLeft' => esc_html__('Zoom In Left', 'mfn-opts'),
				'zoomInRight' => esc_html__('Zoom In Right', 'mfn-opts'),
				'zoomInUpLarge' => esc_html__('Zoom In Up Large', 'mfn-opts'),
				'zoomInDownLarge' => esc_html__('Zoom In Down Large', 'mfn-opts'),
				'zoomInLeftLarge' => esc_html__('Zoom In Left Large', 'mfn-opts'),
				'bounceIn' => esc_html__('Bounce In', 'mfn-opts'),
				'bounceInUp' => esc_html__('Bounce In Up', 'mfn-opts'),
				'bounceInDown' => esc_html__('Bounce In Down', 'mfn-opts'),
				'bounceInLeft' => esc_html__('Bounce In Left', 'mfn-opts'),
				'bounceInRight' => esc_html__('Bounce In Right', 'mfn-opts'),
			);

		}

  }
}
