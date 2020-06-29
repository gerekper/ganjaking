<?php
/**
 * Muffin Builder | Items
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

if( ! class_exists('Mfn_Builder_Items') )
{
  class Mfn_Builder_Items {

		/**
		 * [accordion]
		 */

    public static function item_accordion( $fields ){
			echo sc_accordion( $fields );
		}

		/**
		 * [article_box]
		 */

    public static function item_article_box( $fields ){
			echo sc_article_box( $fields );
		}

		/**
		 * [before_after]
		 */

    public static function item_before_after( $fields ){
			echo sc_before_after( $fields );
		}

		/**
		 * [blockquote]
		 */

    public static function item_blockquote( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_blockquote( $fields, $fields['content'] );
		}

		/**
		 * [blog]
		 */

    public static function item_blog( $fields ){
			echo sc_blog( $fields );
		}

		/**
		 * [blog_news]
		 */

    public static function item_blog_news( $fields ){
			echo sc_blog_news( $fields );
		}

		/**
		 * [blog_slider]
		 */

    public static function item_blog_slider( $fields ){
			echo sc_blog_slider( $fields );
		}

		/**
		 * [blog_teaser]
		 */

    public static function item_blog_teaser( $fields ){
			echo sc_blog_teaser( $fields );
		}

		/**
		 * [button]
		 */

		public static function item_button( $fields ){
			echo sc_button( $fields );
		}

		/**
		 * [call_to_action]
		 */

		public static function item_call_to_action( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_call_to_action( $fields, $fields['content'] );
		}

		/**
		 * [chart]
		 */

		public static function item_chart( $fields ){
			echo sc_chart( $fields );
		}

		/**
		 * [clients]
		 */

		public static function item_clients( $fields ){
			echo sc_clients( $fields );
		}

		/**
		 * [clients_slider]
		 */

		public static function item_clients_slider( $fields ){
			echo sc_clients_slider( $fields );
		}

		/**
		 * [code]
		 */

		public static function item_code( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_code( $fields, $fields['content'] );
		}

		/**
		 * [column]
		 */

		public static function item_column( $fields ){

			$column_class = '';
			$column_attr = '';
			$style = '';

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			// align

			if( ! empty( $fields['align']) ){
				$column_class	.= ' align_'. $fields['align'];
			}

			if( ! empty( $fields['align-mobile'] ) ) {
				$column_class	.= ' mobile_align_'. $fields['align-mobile'];
			}

			// animate

			if( ! empty( $fields['animate'] ) ) {
				$column_class .= ' animate';
				$column_attr .= ' data-anim-type="'. $fields['animate'] .'"';
			}

			// background

			if( ! empty( $fields['column_bg'] ) ) {
				$style .= 'background-color:'. $fields['column_bg'] .';';
			}

			if( ! empty( $fields['bg_image'] ) ) {

				// background image

				$style .= "background-image:url('". $fields['bg_image'] ."');";

				// background position

				if( ! empty( $fields['bg_position'] ) ) {

					$bg_pos = $fields['bg_position'];

					if ($bg_pos) {
						$background_attr = explode(';', $bg_pos);
						$aBg[] = 'background-repeat:'. $background_attr[0];
						$aBg[] = 'background-position:'. $background_attr[1];
					}
					$background = implode('; ', $aBg);

					$style .= implode(';', $aBg) .';';
				}

				// background size

				if (isset($fields['bg_size']) && ($fields['bg_size'] != 'auto')) {
					$column_class .= ' bg-'. $fields['bg_size'];
				}
			}

			// padding

			if( ! empty( $fields['padding'] ) ) {
				$style .= 'padding:'. $fields['padding'] .';';
			}

			// custom | style

			if( ! empty( $fields['style'] ) ) {
				$style .= $fields['style'];
			}

			// output -----

			echo '<div class="column_attr clearfix'. esc_attr($column_class) .'"'. $column_attr .' style="'. $style .'">';
				echo do_shortcode($fields['content']);
			echo '</div>';
		}

		/**
		 * [contact_box]
		 */

		public static function item_contact_box( $fields ){
			echo sc_contact_box( $fields );
		}

		/**
		 * [content]
		 */

		public static function item_content( $fields = false ){
			echo '<div class="the_content">';
				echo '<div class="the_content_wrapper">';
					the_content();
				echo '</div>';
			echo '</div>';
		}

		/**
		 * [countdown]
		 */

		public static function item_countdown( $fields ){
			echo sc_countdown( $fields );
		}

		/**
		 * [counter]
		 */

		public static function item_counter( $fields ){
			echo sc_counter( $fields );
		}

		/**
		 * [divider]
		 */

		public static function item_divider( $fields ){
			echo sc_divider( $fields );
		}

		/**
		 * [fancy_divider]
		 */

		public static function item_fancy_divider( $fields ){
			echo sc_fancy_divider( $fields );
		}

		/**
		 * [fancy_heading]
		 */

		public static function item_fancy_heading( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_fancy_heading( $fields, $fields['content'] );
		}

		/**
		 * [faq]
		 */

		public static function item_faq( $fields ){
			echo sc_faq( $fields );
		}

		/**
		 * [feature_box]
		 */

		public static function item_feature_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_feature_box( $fields, $fields['content'] );
		}

		/**
		 * [feature_list]
		 */

		public static function item_feature_list( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_feature_list( $fields, $fields['content'] );
		}

		/**
		 * [flat_box]
		 */

		public static function item_flat_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_flat_box( $fields, $fields['content'] );
		}

		/**
		 * [helper]
		 */

		public static function item_helper( $fields ){
			echo sc_helper( $fields );
		}

		/**
		 * [hover_box]
		 */

		public static function item_hover_box( $fields ){
			echo sc_hover_box( $fields );
		}

		/**
		 * [hover_color]
		 */

		public static function item_hover_color( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_hover_color( $fields, $fields['content'] );
		}

		/**
		 * [how_it_works]
		 */

		public static function item_how_it_works( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_how_it_works( $fields, $fields['content'] );
		}

		/**
		 * [icon_box]
		 */

		public static function item_icon_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_icon_box( $fields, $fields['content'] );
		}

		/**
		 * [image]
		 */

		public static function item_image( $fields ){
			echo sc_image( $fields );
		}

		/**
		 * [image_gallery]
		 */

		public static function item_image_gallery( $fields ){
			$fields['link'] = 'file';
			echo sc_gallery( $fields );
		}

		/**
		 * [info_box]
		 */

		public static function item_info_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_info_box( $fields, $fields['content'] );
		}

		/**
		 * [list]
		 */

		public static function item_list( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_list( $fields, $fields['content'] );
		}

		/**
		 * [map_basic]
		 */

		public static function item_map_basic( $fields ){
			echo sc_map_basic( $fields );
		}

		/**
		 * [map]
		 */

		public static function item_map( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_map( $fields, $fields['content'] );
		}

		/**
		 * [offer]
		 */

		public static function item_offer( $fields ){
			echo sc_offer( $fields );
		}

		/**
		 * [offer_thumb]
		 */

		public static function item_offer_thumb( $fields ){
			echo sc_offer_thumb( $fields );
		}

		/**
		 * [opening_hours]
		 */

		public static function item_opening_hours( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_opening_hours( $fields, $fields['content'] );
		}

		/**
		 * [our_team]
		 */

		public static function item_our_team( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_our_team( $fields, $fields['content'] );
		}

		/**
		 * [our_team_list]
		 */

		public static function item_our_team_list( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_our_team_list( $fields, $fields['content'] );
		}

		/**
		 * [photo_box]
		 */

		public static function item_photo_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_photo_box( $fields, $fields['content'] );
		}

		/**
		 * [placeholder]
		 */

		public static function item_placeholder( $fields ){
			echo '<div class="placeholder">&nbsp;</div>';
		}

		/**
		 * [portfolio]
		 */

		public static function item_portfolio( $fields ){
			echo sc_portfolio( $fields );
		}

		/**
		 * [portfolio_grid]
		 */

		public static function item_portfolio_grid( $fields ){
			echo sc_portfolio_grid( $fields );
		}

		/**
		 * [portfolio_photo]
		 */

		public static function item_portfolio_photo( $fields ){
			echo sc_portfolio_photo( $fields );
		}

		/**
		 * [portfolio_slider]
		 */

		public static function item_portfolio_slider( $fields ){
			echo sc_portfolio_slider( $fields );
		}

		/**
		 * [pricing_item]
		 */

		public static function item_pricing_item( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_pricing_item( $fields, $fields['content'] );
		}

		/**
		 * [progress_bars]
		 */

		public static function item_progress_bars( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_progress_bars( $fields, $fields['content'] );
		}

		/**
		 * [promo_box]
		 */

		public static function item_promo_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_promo_box( $fields, $fields['content'] );
		}

		/**
		 * [quick_fact]
		 */

		public static function item_quick_fact( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_quick_fact( $fields, $fields['content'] );
		}

		/**
		 * [shop]
		 */

		public static function item_shop( $fields ){

			if( class_exists( 'WC_Shortcode_Products' ) ){
				$shortcode = new WC_Shortcode_Products( $fields, $fields['type'] );
				echo $shortcode->get_content();
			}

		}

		/**
		 * [shop_slider]
		 */

		public static function item_shop_slider( $fields ){
			echo sc_shop_slider( $fields );
		}

		/**
		 * [sidebar_widget]
		 */

		public static function item_sidebar_widget( $fields ){
			echo sc_sidebar_widget( $fields );
		}

		/**
		 * [slider]
		 */

		public static function item_slider( $fields ){
			echo sc_slider( $fields );
		}

		/**
		 * [slider_plugin]
		 */

		public static function item_slider_plugin( $fields ){
			echo sc_slider_plugin( $fields );
		}

		/**
		 * [sliding_box]
		 */

		public static function item_sliding_box( $fields ){
			echo sc_sliding_box( $fields );
		}

		/**
		 * [story_box]
		 */

		public static function item_story_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_story_box( $fields, $fields['content'] );
		}

		/**
		 * [tabs]
		 */

		public static function item_tabs( $fields ){
			echo sc_tabs( $fields );
		}

		/**
		 * [testimonials]
		 */

		public static function item_testimonials( $fields ){
			echo sc_testimonials( $fields );
		}

		/**
		 * [testimonials_list]
		 */

		public static function item_testimonials_list( $fields ){
			echo sc_testimonials_list( $fields );
		}

		/**
		 * [timeline]
		 */

		public static function item_timeline( $fields ){
			echo sc_timeline( $fields );
		}

		/**
		 * [trailer_box]
		 */

		public static function item_trailer_box( $fields ){
			echo sc_trailer_box( $fields );
		}

		/**
		 * [video]
		 */

		public static function item_video( $fields ){
			echo sc_video( $fields );
		}

		/**
		 * [visual]
		 */

		public static function item_visual( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo do_shortcode($fields['content']);
		}

		/**
		 * [zoom_box]
		 */

		public static function item_zoom_box( $fields ){

			if( empty($fields['content']) ){
				$fields['content'] = '';
			}

			echo sc_zoom_box( $fields, $fields['content'] );
		}

  }
}
