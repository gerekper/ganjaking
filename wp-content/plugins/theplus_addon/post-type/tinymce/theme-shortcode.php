<?php
	if ( ! class_exists( 'Pt_theplus_TinyMCE_Shortcode' ) ) {

	class Pt_theplus_TinyMCE_Shortcode {

		public function __construct() {
			add_action( 'admin_init', array( $this, 'pt_plus_shortcode_button' ) );
			add_action('admin_footer', array($this, 'pt_plus_get_shortcodes'));
		}


		public function pt_plus_shortcode_button() {
			if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'pt_plus_tinymce_plugins' ) );
				add_filter( 'mce_buttons', array( $this, 'pt_plus_register_buttons' ) );
			}
		}

		public function pt_plus_tinymce_plugins( $plugin_array ) {
			$plugin_array['pt_plus_shortcodes'] = THEPLUS_PLUGIN_URL. 'post-type/tinymce/tinymce-shortcode.js';

			return $plugin_array;
		}

		public function pt_plus_register_buttons( $buttons ) {
			array_push( $buttons, 'backcolor','styled_button' , 'separator', 'pt_plus_shortcodes' );

			return $buttons;
		}
		public function pt_plus_get_shortcodes() {
			global $shortcode_tags;

			$alowed_shortcodes = array(
				'1' => 'chapter',
				'2' => 'gallery'
			);

			echo '<script >
       		 var shortcodes_button = new Array();';

			$count = 0;

			foreach ( $shortcode_tags as $tag => $code ) {
				if ( in_array( $tag, $alowed_shortcodes ) ) {
					echo "shortcodes_button[{$count}] = '{$tag}';";
					$count ++;
				}
			}

			echo '</script>';
		}

	}
}

if ( class_exists( 'Pt_theplus_TinyMCE_Shortcode' ) ) {
	$Pt_theplus_TinyMCE_Shortcode = new Pt_theplus_TinyMCE_Shortcode;
}
/* ----------------------------
 * Ts Dropcap 
 * --------------------------- */
if( ! function_exists( 'tp_dropcap' ) )
{
	function tp_dropcap( $attr, $content = null )
	{
		extract(shortcode_atts(array(
			'font_family' 			=> '',
			'font_size' 			=> '40px',
			'background' 	=> '#ff214f',
			'color' 		=> '#fff',
			'style' 	=> '1',
			'shadow'		=> 'false',
		), $attr));

		$class = ' style-'.$style.' ';
		if($shadow=='true'){
			$class .=' shadow ';
		}
		$style_css = $css_style='';
		
		// font family
		if( $font_family ){
			$style_css .= "font-family:'". $font_family ."',Arial,Tahoma,sans-serif;";
			$font_slug = str_replace( ' ', '+', $font_family );
			wp_enqueue_style( $font_slug, 'http://fonts.googleapis.com/css?family='. $font_slug );
		}
 		
		
		// background
		if( $style !='2' && $style !='3' && $background ) $style_css .= 'background-color:'. $background .';';
		
		//style 2 border
		if( $style =='2') $style_css .=' border: 1px solid '.$background.';';
		
		// color
		if( $color ) $style_css .= ' color:'. $color .';';
		
		// font_size
		$size = intval( $font_size );	
		
		$style_css .= ' font-size:'. $size .'px;line-height:'. $size .'px;height:'. $size .'px;width:'. $size .'px;';
		
		if( $style_css ) $css_style = 'style="'. $style_css .'"';
			
		$output  = '<span class="pt_plus_dropcap'. $class .'" '. $css_style .'>';
			$output .= do_shortcode( $content );
		$output .= '</span>'."\n";

		return $output;
	}
}
add_shortcode( 'tp_dropcap', 'tp_dropcap' );

/* -------------------------------
 * Ts Blockquote
 * ------------------------------- */
if( ! function_exists( 'tp_blockquote' ) )
{
	function tp_blockquote( $attr, $content = null )
	{
		extract(shortcode_atts(array(
			'author'	=> 'Jhon Doe',
			'link'		=> '',
			'target'	=> '',
			'author_color' => '#fff',	
			'color'		=> '#fff',
			'background'=> '#ff004b',
			'style' 	=> '1',
			'quote_color' =>'#d71951',
			'border_color' =>'#ff92b2',
			'bottom_background' => '#fb5988',
		), $attr));
		
		// target
		if( $target == 'lightbox' ){
			$target = 'rel="prettyphoto"';
		} elseif( $target ){
			$target = 'target="_blank"';
		} else {
			$target = false;
		}
		$style_css=$author_css=$quote_clr='';
		// color
		if( $color ) $style_css .= ' color:'. esc_attr($color) .';';
		if( $author_color ) $author_css .= ' color:'. esc_attr($author_color) .';';
		if( $quote_color ) $quote_clr .= ' color:'. esc_attr($quote_color) .';';
		// background
		if($background ) $style_css .= 'background-color:'. esc_attr($background) .';';
			
		$uid=uniqid('ts-quote');
		$output = '<div class="pt_plus_blockquote pt_plus_blockquote-'.esc_attr($style).' '.esc_attr($uid).'">';
			$output .= '<blockquote style="'.$style_css.'"><i class="fa fa-quote-left qote-left" aria-hidden="true" style="'.$quote_clr.'"></i><span class="blockquote-content" >'. do_shortcode( $content ).'</span><i class="fa fa-quote-right qote-right" aria-hidden="true" style="'.$quote_clr.'"></i>';
			if( $style != "4" ){
				if( $author ){
					$output .= '<p class="author">';
						if( $link ){ 
							$output .= '<a href="'. esc_url($link) .'" '. $target .' style="'.$author_css.'><span class="author-desh"> </span>'. esc_html($author) .'</a>';
						} else {
							$output .= '<span style="'.$author_css.'"><span class="author-desh"> </span>'. esc_html($author) .'</span>';
						}
					$output .= '</p>';
				}
			}
		$output .= '</blockquote>';
		if( $style == "4" ){
				if( $author ){
					$output .= '<p class="author">';
						if( $link ){ 
							$output .= '<a href="'. esc_url($link) .'" '. $target .' style="'.$author_css.'><span class="author-desh"> </span>'. esc_html($author) .'</a>';
						} else {
							$output .= '<span style="'.$author_css.'"><span class="author-desh"> </span>'. esc_html($author) .'</span>';
						}
					$output .= '</p>';
				}
			}
			$output .= '</div>'."\n";
		$css_rule ='';
		$css_rule .='<style >';
		$css_rule .='.'.esc_js($uid).'.pt_plus_blockquote-1 blockquote {border-left-color: '.esc_js($border_color).' !important}.'.esc_js($uid).'.pt_plus_blockquote-5 .blockquote-content{-moz-box-shadow: 0 -6px 0 '.esc_js($border_color).';-webkit-box-shadow: 0 -6px 0 '.esc_js($border_color).';box-shadow: 0 -6px 0 '.esc_js($border_color).';}.'.esc_js($uid).'.pt_plus_blockquote-5 .fa.fa-quote-left.qote-left:before{background: '.esc_js($border_color).'}.'.esc_js($uid).'.pt_plus_blockquote-5 blockquote:before {border-left-color: '.esc_js($border_color).' !important}.'.esc_js($uid).'.pt_plus_blockquote-4 .author {background-color: '.esc_js($bottom_background).' }';
		$css_rule .= '</style>';
		return $css_rule.$output;
	}
}
add_shortcode( 'tp_blockquote', 'tp_blockquote' );

/* ---------------------------
 * Fancy Link
* ----------------------------*/
if( ! function_exists( 'tp_fancy_link' ) )
{
	function tp_fancy_link( $attr, $content = null )
	{
		extract(shortcode_atts(array(
			'title' 	=> 'Insert your content here',
			'link' 		=> '',
			'target' 	=> '',
			'style' 	=> '1',	// 1-8
			'class' 	=> '',
			'background' =>'#ff214f',
			'text_color'     =>'#252525',
                        'text_hover_color'     =>'#cccccc',
			'download' 	=> '',
		), $attr));
		
		
		// target
		if( $target ){
			$target = 'target="_blank"';
		} else {
			$target = false;
		}
		
		// download
		if( $download ){
			$download = 'download="'. $download .'"';
		} else {
			$download = false;
		}
		$uid=uniqid('fancy');	
		$output = '<span class="pt-plus-fancy-link-content '.esc_attr($uid).'" ><a class="pt-plus-fancy-link fancy-link-'. esc_attr($style) .' '. esc_attr($class) .'" href="'. esc_url($link) .'"  data-hover="'. esc_attr($title) .'" '. $target .' '. $download .'>';
			$output .= '<span data-hover="'. esc_attr($title) .'">'. esc_html($title) .'</span>';
		$output .= '</a></span>';
		
		$css_rule ='';
		$css_rule .='<style >';
		$css_rule .='.'.esc_js($uid).' .pt-plus-fancy-link{color: '.esc_js($text_color).'}.'.esc_js($uid).' .fancy-link-1:before,.'.esc_js($uid).' .fancy-link-2:before,.'.esc_js($uid).' .fancy-link-3:before,.'.esc_js($uid).' .fancy-link-4:before,.'.esc_js($uid).' .fancy-link-5:before{background: '.esc_js($background).' !important}.'.esc_js($uid).':hover .fancy-link-4{color: '.esc_js($background).' !important}.'.esc_js($uid).' .fancy-link-7:before{background-color: '.esc_js($background).' !important}.'.esc_js($uid).' .fancy-link-4:before,.'.esc_js($uid).' .fancy-link-6:before{border-color: '.esc_js($background).' !important}.'.esc_js($uid).' .fancy-link-7:before{color: '.esc_js($text_hover_color).' !important}.'.esc_js($uid).' .fancy-link-8:before,.'.esc_js($uid).' .fancy-link-8::after{background: '.esc_js($background).'}.'.esc_js($uid).' .fancy-link-9:before,.'.esc_js($uid).' .fancy-link-9::after{color: '.esc_js($background).'}.'.esc_js($uid).' .fancy-link-8::before{border-top-color: '.esc_js($background).'}.'.esc_js($uid).' .pt-plus-fancy-link:hover {color: '.esc_js($text_hover_color).'}';
		$css_rule .= '</style>';
		return $css_rule.$output;
	}
}
add_shortcode( 'tp_fancy_link', 'tp_fancy_link' );
/* ---------------------------
 * Hightlight 
 * ----------------------------*/


if( ! function_exists( 'tp_hightlight' ) )
{
	function tp_hightlight( $attr, $content = null )
	{
		extract(shortcode_atts(array(
			'title' 	=> 'Insert your content here',
			'class' 	=> '',
			'background' =>'#ff214f',
			'background_hover'=>'#1abc9c',
			'color' => '#ffffff',
'text_hover_color' =>'#121212',
			'animation' =>'yes',
		), $attr));
		
		
		// target
		if( $animation =='yes' ){
			$animation = 'highlight-hover';
			$animation_style = false;
		} else {
			$animation = 'highlight-normal';
			
			$animation_style = 'style="';
				if($background != "") {
					$animation_style .='background: '.esc_attr($background).';';
				}	
			$animation_style .= '";';
		}
		$animation_css = 'style="';
		$animation_css .='color: '.esc_attr($color).';';
		$animation_css .= '";';	
		$uid=uniqid('hightlight');
		$output = '<span class="pt-plus-hightlight '.esc_attr($uid).' '.esc_attr($animation).'"  '.$animation_css.'>';
			$output .= '<span class="highlight-title" '.$animation_style.'> '.esc_html($title).' </span>';
		$output .= '</span>';
		$css_rule ='';
		$css_rule .='<style >';
		$css_rule .='.pt-plus-hightlight.'.esc_js($uid).':after {background: '.esc_js($background).'}.pt-plus-hightlight.'.esc_js($uid).':before {background: '.esc_js($background_hover).'}';
		$css_rule .= '</style>';
		return $css_rule.$output;
	}
}
add_shortcode( 'tp_hightlight', 'tp_hightlight' );

/* ---------------------------
 * tooltip 
 * ----------------------------*/

if( ! function_exists( 'tp_tooltip_image' ) )
{
	function tp_tooltip_image( $attr, $content = null )
	{
		extract(shortcode_atts(array(
			
			'color'         =>'',
			'tooltip_style'    =>'default',
			'align'			=> 'top',  
			'image' 		=> '',
			'animation'		=> 'fade',
			'text' =>'',
			
		), $attr));
		wp_enqueue_style('tooltipster');
		wp_enqueue_style('tooltipster_theme');
		wp_enqueue_script('tooltipster_js');
		
$uid='pt-plus-tooltip-'.rand(1000000, 1500000);
		$output = '';
		$align_1= "{&quot;side&quot;: &quot;left&quot;}" ;

		if( $text || $image ){
			$output .= '<span class="pt-plus-tooltip tooltipster-light-preview" data-tooltip-content="#'.esc_attr($uid).'"  data-animation="'.$animation.'" data-align="'.$align.'" data-tooltip_style="'.$tooltip_style.'" style="color:'.$color.'">';
				$output .= do_shortcode( $content );
			$output .= '</span>';
			
				$output .= '<span class="tooltip_templates tooltipster-right"><span id="'.esc_attr($uid).'" >';
					if( $image )	$output .= '<img src="'. $image .'" />';
					if( $text )		$output .= '<span class="tooltip-text">'.$text.'</span>';
				$output .= '</span></span>';
		}
		$output .='<script>( function ( $ ) {"use strict";$(window).load(function () {$(".pt-plus-tooltip").each(function () {var animated= $(this).data("animation");var align= $(this).data("align");	var tooltip_style= $(this).data("tooltip_style");$(this).tooltipster({  theme: "tooltipster-" + tooltip_style, delay: 100, maxWidth: 200, speed: 300,
		interactive: true, position: align,animation: animated,	});	});	});	} ( jQuery ) );</script>';
		return $output;
	}
}
add_shortcode( 'tp_tooltip', 'tp_tooltip_image' );

/* ---------------------------
 * Code 
 * ----------------------------*/
 
if( ! function_exists( 'tp_code' ) )
{
	function tp_code( $attr, $content = null )
	{
		
		$output  = '<pre>';
			$output .= do_shortcode(htmlspecialchars($content));
		$output .= '</pre>';
		
	    return $output;
	}
}
add_shortcode( 'tp_code', 'tp_code' );