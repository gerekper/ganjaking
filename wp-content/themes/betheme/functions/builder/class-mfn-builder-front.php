<?php
/**
 * Muffin Builder | Front
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

if( ! class_exists('Mfn_Builder_Front') )
{
  class Mfn_Builder_Front {

    public $post_id = false;
    public $content_field = false; // use post field instead of the_content()

    /**
     * Constructor
     */

    public function __construct($post_id, $content_field = false) {

      $this->post_id = $post_id;
      $this->content_field = $content_field;

    }

  	/**
  	 * Show WordPress Editor Content
  	 */

  	public function the_content(){

			// FIX: Elementor - prevent showing first post content on blog page

			if( ( 'post' == get_post_type() ) && ( ! is_singular() ) ){
				return false;
			}

      // check if editor content exists

			$content = get_post_field('post_content', $this->post_id);
  		$class = $content ? 'has_content' : 'no_content' ;

  		// output -----

  		echo '<div class="section the_content '. esc_attr($class) .'">';
  			if (! get_post_meta($this->post_id, 'mfn-post-hide-content', true)) {
  				echo '<div class="section_wrapper">';
  					echo '<div class="the_content_wrapper">';
  						if ($this->content_field) {
  							echo apply_filters('the_content', $content);
  						} else {
  							the_content();
  						}
  					echo '</div>';
  				echo '</div>';
  			}
  		echo '</div>';

  	}

    public function show(){

  		// convert item size to class

  		$classes = array(
  			'divider' => 'divider',
  			'1/6' => 'one-sixth',
  			'1/5' => 'one-fifth',
  			'1/4' => 'one-fourth',
  			'1/3' => 'one-third',
  			'2/5' => 'two-fifth',
  			'1/2' => 'one-second',
  			'3/5' => 'three-fifth',
  			'2/3' => 'two-third',
  			'3/4' => 'three-fourth',
  			'4/5' => 'four-fifth',
  			'5/6' => 'five-sixth',
  			'1/1' => 'one'
  		);

  		// GET sidebars

  		$sidebars = mfn_opts_get('sidebars');

  		// GET builder items

  		$mfn_items = get_post_meta($this->post_id, 'mfn-page-items', true);

  		// FIX | Muffin builder 2 compatibility

  		if ($mfn_items && ! is_array($mfn_items)) {
  			$mfn_items = unserialize(call_user_func('base'.'64_decode', $mfn_items));
  		}

  		// WordPress Editor | before builder

  		if ( 1 == mfn_opts_get('display-order') ) {
  			$this->the_content();
  		}

  		// Muffin Builder

  		if (post_password_required()) {

  			// password protected page

  			if (get_post_meta($this->post_id, 'mfn-post-hide-content', true)) {
  				echo '<div class="section the_content">';
  					echo '<div class="section_wrapper">';
  						echo '<div class="the_content_wrapper">';
  							echo get_the_password_form();
  						echo '</div>';
  					echo '</div>';
  				echo '</div>';
  			}

			} elseif ( function_exists('wc_memberships') && ( ! current_user_can('wc_memberships_view_restricted_post_content', $this->post_id) ) ){

				// do not show builder if wc memberships active do not allow current user

  		} elseif (is_array($mfn_items)) {

  			// SECTIONS -----

  			foreach ($mfn_items as $section) {

  				// hidden sections

  				if ($_GET && key_exists('mfn-show', $_GET)) {
  					// do nothing
  				} elseif (key_exists('hide', $section['attr']) && $section['attr']['hide']) {
  					continue;
  				}

  				// section attributes

  				// classes ---

  				$section_class = array();

  				// unique ID

  				if( ! empty($section['uid']) ) {
  					$section_class[] = 'mcb-section-'. $section['uid'];
  				}

  				// custom style & class

  				if( ! empty($section['attr']['style']) ) {
  					$section_class[] = $section['attr']['style'];
  				}
  				if( ! empty($section['attr']['class']) ) {
  					$section_class[] = $section['attr']['class'];
  				}

  				// visibility

  				if( ! empty($section['attr']['visibility']) ) {
  					$section_class[] = $section['attr']['visibility'];
  				}

  				// background video

					if( ! empty($section['attr']['bg_video_mp4']) ) {
  					$section_class[] = 'has-video';
  				}

  				// navigation arrows

					if( ! empty($section['attr']['navigation']) ) {
  					$section_class[] = 'has-navi';
  				}

  				// background size

  				if( isset($section['attr']['bg_size']) && ($section['attr']['bg_size'] != 'auto') ) {
  					$section_class[] = 'bg-'. $section['attr']['bg_size'];
  				}

  				$section_class = implode(' ', $section_class);

  				// styles ---

  				$section_style = $section_bg = array();

					if( ! empty($section['attr']['padding_top']) ) {
						$section_style[] = 'padding-top:'. intval($section['attr']['padding_top']) .'px';
					}
					if( ! empty($section['attr']['padding_bottom']) ) {
						$section_style[] = 'padding-bottom:'. intval($section['attr']['padding_bottom']) .'px';
					}
					if( ! empty($section['attr']['padding_horizontal']) ) {
						if( is_numeric($section['attr']['padding_horizontal']) ){
							$section['attr']['padding_horizontal'] .= 'px';
						}
						$section_style[] = 'padding-left:'. esc_attr($section['attr']['padding_horizontal']);
						$section_style[] = 'padding-right:'. esc_attr($section['attr']['padding_horizontal']);
					}
					if( ! empty($section['attr']['bg_color']) ) {
						$section_style[] = 'background-color:'. $section['attr']['bg_color'];
					}

  				// background image attributes

  				if( $section['attr']['bg_image'] ) {

  					$section_bg['image'] = 'background-image:url('. $section['attr']['bg_image'] .')';

						$section_bg_attr = explode(';', $section['attr']['bg_position']);

						if( $section_bg_attr[0] ) {
	  					$section_bg['repeat'] = 'background-repeat:'. $section_bg_attr[0];
						}
						if( $section_bg_attr[1] ) {
  						$section_bg['position'] = 'background-position:'. $section_bg_attr[1];
						}
						if( $section_bg_attr[2] ) {
	  					$section_bg['attachment'] = 'background-attachment:'. $section_bg_attr[2];
						}
						if( $section_bg_attr[3] ) {
  						$section_bg['size'] = 'background-size:'. $section_bg_attr[3];
						}
  				}

  				// parallax

  				$parallax = false;
  				if ($section['attr']['bg_image'] && ($section_bg_attr[2] == 'fixed')) {
  					if (! key_exists(4, $section_bg_attr) || $section_bg_attr[4] != 'still') {

  						// parallax
  						$parallax = mfn_parallax_data();

  						if (mfn_parallax_plugin() == 'translate3d') {
  							if (mfn_is_mobile()) {
  								$section_bg['attachment'] = 'background-attachment:scroll';
  							} else {
  								$section_bg = array();
  							}
  						}

  					} else {

  						// cover
  						$section_class .= ' bg-cover';

  					}
  				}

  				$section_style = array_merge($section_style, $section_bg);
  				$section_style = implode(';', $section_style);

  				// custom section ID

  				if (key_exists('section_id', $section['attr']) && $section['attr']['section_id']) {
  					$section_id = 'id="'. $section['attr']['section_id'] .'"';
  				} else {
  					$section_id = false;
  				}

  				// output SECTION -----

  				echo '<div class="section mcb-section '. $section_class .'" '. $section_id .' style="'. $section_style .'" '. $parallax .'>'; // 100%

  					// background: parallax | translate3d background image

  					if (! mfn_is_mobile() && $parallax && mfn_parallax_plugin() == 'translate3d') {
  						echo '<img class="mfn-parallax" src="'. $section['attr']['bg_image'] .'" alt="parallax background" style="opacity:0" />';
  					}

  					// background: video

  					if (key_exists('bg_video_mp4', $section['attr']) && ($mp4 = $section['attr']['bg_video_mp4'])) {
  						echo '<div class="section_video">';

  							echo '<div class="mask"></div>';

  							$poster = $section['attr']['bg_image'];

  							echo '<video poster="'. $poster .'" autoplay="true" loop="true" muted="muted">';

  								echo '<source type="video/mp4" src="'. $mp4 .'" />';
  								if (key_exists('bg_video_ogv', $section['attr']) && $ogv = $section['attr']['bg_video_ogv']) {
  									echo '<source type="video/ogg" src="'. $ogv .'" />';
  								}

  							echo '</video>';

  						echo '</div>';
  					}

  					// decoration: SVG

  					if (key_exists('divider', $section['attr']) && $divider = $section['attr']['divider']) {
  						echo '<div class="section-divider '. $divider .'"></div>';
  					}

  					// decoration: image top

  					if (key_exists('decor_top', $section['attr']) && $decor_top = $section['attr']['decor_top']) {
  						echo '<div class="section-decoration top" style="background-image:url('. $decor_top .');height:'. mfn_get_attachment_data($decor_top, 'height') .'px"></div>';
  					}

  					// navigation arrows

  					if (key_exists('navigation', $section['attr']) && $section['attr']['navigation']) {
  						echo '<div class="section-nav prev"><i class="icon-up-open-big"></i></div>';
  						echo '<div class="section-nav next"><i class="icon-down-open-big"></i></div>';
  					}

  					echo '<div class="section_wrapper mcb-section-inner">';

  						// WRAPS -----

  						// FIX | Muffin Builder 2 compatibility
  						// there were no wraps inside section in Muffin Builder 2

  						if (! key_exists('wraps', $section) && is_array($section['items'])) {
  							$fix_wrap = array(
  								'size'	=> '1/1',
  								'items'	=> $section['items'],
  							);
  							$section['wraps'] = array( $fix_wrap );
  						}

  						// print inside wraps

  						if (key_exists('wraps', $section) && is_array($section['wraps'])) {
  							foreach ($section['wraps'] as $wrap) {

  								// wrap attributes

  								$wrap_class = array();

  								// unique ID

  								if( ! empty($wrap['uid']) ) {
  									$wrap_class[] = 'mcb-wrap-'. $wrap['uid'];
  								}

  								// classes ---

  								$wrap_class[] = $classes[ $wrap['size'] ];

  								if( key_exists('attr', $wrap) ) {

  									$wrap_class[] = $wrap['attr']['class'];

  									// items margin

  									if( $wrap['attr']['column_margin'] ) {
  										$wrap_class[] = 'column-margin-'. $wrap['attr']['column_margin'];
  									}

  									// items vertical align

  									if( ! empty($wrap['attr']['vertical_align']) ) {
  										$wrap_class[] = 'valign-'. $wrap['attr']['vertical_align'];
  									}

  									// background size

  									if( ! empty($wrap['attr']['bg_size']) && ($wrap['attr']['bg_size'] != 'auto') ) {
  										$wrap_class[] = 'bg-'. $wrap['attr']['bg_size'];
  									}

  								}

  								// styles ---

  								$wrap_style = $wrap_bg = array();
  								$wrap_data = array();
  								$parallax = false;

  								if( key_exists('attr', $wrap) ){

  									// padding

  									if( $wrap['attr']['padding'] ) {
  										$wrap_style[] = 'padding:'. $wrap['attr']['padding'];
  									}

  									// background color

  									if( $wrap['attr']['bg_color'] ){
  										$wrap_style[] = 'background-color:'. $wrap['attr']['bg_color'];
  									}

  									// move up

  									if( ! empty($wrap['attr']['move_up']) ) {
  										$wrap_class[] = 'move-up';
  										$wrap_style[] = 'margin-top:-'. intval($wrap['attr']['move_up']) .'px';

  										if ($moveup = mfn_opts_get('builder-wrap-moveup')) {
  											if ('no-tablet' == $moveup) {
  												$wrap_data[] = 'data-tablet="no-up"';
  											}
  											$wrap_data[] = 'data-mobile="no-up"';
  										}
  									}

  									// background image attributes

  									if( $wrap['attr']['bg_image'] ){

											$wrap_bg[] = 'background-image:url('. $wrap['attr']['bg_image'] .')';

  										$wrap_bg_attr = explode(';', $wrap['attr']['bg_position']);

											if( ! empty($wrap_bg_attr[0]) ) {
  											$wrap_bg[] = 'background-repeat:'. $wrap_bg_attr[0];
											}
											if( ! empty($wrap_bg_attr[1]) ) {
  											$wrap_bg[] = 'background-position:'. $wrap_bg_attr[1];
											}
											if( ! empty($wrap_bg_attr[2]) ) {
  											$wrap_bg['attachment'] = 'background-attachment:'. $wrap_bg_attr[2];
											}
											if( ! empty($wrap_bg_attr[3]) ) {
  											$wrap_bg[] = 'background-size:'. $wrap_bg_attr[3];
											}
  									}

  									// parallax

  									if ($wrap['attr']['bg_image'] && ($wrap_bg_attr[2] == 'fixed')) {
  										if (! key_exists(4, $wrap_bg_attr) || $wrap_bg_attr[4] != 'still') {
  											$parallax = mfn_parallax_data();

  											if (mfn_parallax_plugin() == 'translate3d') {
  												if (mfn_is_mobile()) {
  													$wrap_bg['attachment'] = 'background-attachment:scroll';
  												} else {
  													$wrap_bg = array();
  												}
  											}
  										}
  									}

  								}

  								$wrap_class	= implode(' ', $wrap_class);

  								$wrap_style = array_merge($wrap_style, $wrap_bg);
  								$wrap_style = implode(';', $wrap_style);

  								$wrap_data = implode(' ', $wrap_data);

  								// output WRAP -----

  								echo '<div class="wrap mcb-wrap '. $wrap_class .' clearfix" style="'. $wrap_style .'" '. $parallax .' '. $wrap_data .'>';

  									// parallax | translate3d background image

  									if (! mfn_is_mobile() && $parallax && mfn_parallax_plugin() == 'translate3d') {
  										echo '<img class="mfn-parallax" src="'. $wrap['attr']['bg_image'] .'" alt="parallax background" style="opacity:0" />';
  									}

  									echo '<div class="mcb-wrap-inner">';

  										// ITEMS -----

  										if (is_array($wrap['items'])) {
  											foreach ($wrap['items'] as $item) {

													$type = 'item_'. $item['type'];

													if (method_exists('Mfn_Builder_Items', $type)) {

  													$item_class = array();

														if( ! isset( $item['fields'] ) ){
															$item['fields'] = array();
														}

  													// unique ID

  													if ( ! empty($item['uid']) ) {
  														$item_class[] = 'mcb-item-'. $item['uid'];
  													}

  													// size

  													$item_class[] = $classes[$item['size']];

  													// type

  													$item_class[] = 'column_'. $item['type'];

  													// custom classes

  													if ( ! empty($item['fields']['classes']) ) {
  														$item_class[] = $item['fields']['classes'];
  													}

  													// margin bottom

  													if ($item['type'] == 'column' && (! empty($item['fields']['margin_bottom']))) {
  														$item_class[] = 'column-margin-'. $item['fields']['margin_bottom'];
  													}

  													$item_class	= implode(' ', $item_class);

  													// output -----

  													echo '<div class="column mcb-column '. $item_class .'">';
															echo Mfn_Builder_Items::$type( $item['fields'] );
  													echo '</div>';
  												}

  											}
  										}

  									echo '</div>';

  								echo '</div>';
  							}
  						}


  					echo '</div>';

  					// decoration: image top

  					if( ! empty($section['attr']['decor_bottom']) ) {
							$decor_bottom = $section['attr']['decor_bottom'];
  						echo '<div class="section-decoration bottom" style="background-image:url('. $decor_bottom .');height:'. mfn_get_attachment_data($decor_bottom, 'height') .'px"></div>';
  					}

  				echo '</div>';
  			}
  		}

  		// WordPress Editor | after builder

  		if ( 0 == mfn_opts_get('display-order') ) {
  			$this->the_content();
  		}

  	}

  }
}
