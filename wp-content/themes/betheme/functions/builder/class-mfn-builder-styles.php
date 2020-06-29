<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Builder_Styles {

  private $tmp_ID = 1;

  /**
   * GET temporary ID and SET next one
   */

  private function get_tmp_ID(){

    $ID = $this->tmp_ID;
    $this->tmp_ID += $ID;

    return $ID;
  }

	/**
	 * GET inline styles
	 */

	public function get_styles(){

		$custom_css = '';
    $tmp_id = 1;

    if( ! get_the_ID() ){
      return false;
    }

    // get builder items

    $builder = get_post_meta(get_the_ID(), 'mfn-page-items', true);
    $builder = apply_filters('mfn-builder-get', $builder);

    // SECTION

    foreach( $builder as $section ){

      if( ! isset($section['attr']) ){
        continue;
      }

      // if unique ID is not set

      if( empty( $section['uid'] ) ){
        $section['uid'] = 'tmp-'. $this->get_tmp_ID();
      }

      $selector = '.mcb-section-'. $section['uid'];

      // get styles

      $section_style = $section_bg = array();

      if( $section['attr']['bg_color'] ){
        $section_style[] = 'background-color:'. esc_attr($section['attr']['bg_color']);
      }

      $section_style[] = 'padding-top:'. intval($section['attr']['padding_top']) .'px';
      $section_style[] = 'padding-bottom:'. intval($section['attr']['padding_bottom']) .'px';

      // background image attributes

      if ($section['attr']['bg_image']) {

        $section_bg_attr = explode(';', $section['attr']['bg_position']);

        $section_bg[] = 'background-image:url('. esc_url($section['attr']['bg_image']) .')';

        $section_bg[] = 'background-repeat:'. esc_attr($section_bg_attr[0]);
        $section_bg[] = 'background-position:'. esc_attr($section_bg_attr[1]);

        if( $section_bg_attr[2] ){
          $section_bg['attachment'] = 'background-attachment:'. esc_attr($section_bg_attr[2]);
        }
        if( $section_bg_attr[3] ){
          $section_bg[] = 'background-size:'. esc_attr($section_bg_attr[3]);
        }

        // parallax

        if ( 'fixed' == $section_bg_attr[2] ) {
          if ( empty($section_bg_attr[4]) || $section_bg_attr[4] != 'still' ) {
            if ( 'translate3d' == mfn_parallax_plugin() ) {
              if ( mfn_is_mobile() ) {
                $section_bg['attachment'] = 'background-attachment:scroll';
              } else {
                $section_bg = array();
              }
            }
          }
        }

      }

      // prepare styles

      $section_style = array_merge($section_style, $section_bg);
      $section_style = implode(';', $section_style);

      if( $section_style ){
        $custom_css .= $selector. '{'. $section_style .'}';
      }

      // WRAP

      foreach ($section['wraps'] as $wrap) {

        if( ! isset($wrap['attr']) ){
          continue;
        }

        // if unique ID is not set

        if( empty( $wrap['uid'] ) ){
          $wrap['uid'] = 'tmp-'. $this->get_tmp_ID();
        }

        $selector = '.mcb-wrap-'. $wrap['uid'];

        // styles

        // get styles

        $wrap_style = $wrap_bg = array();

        if ( ! empty($wrap['attr']['padding']) ) {
          $wrap_style[] = 'padding:'. esc_attr($wrap['attr']['padding']);
        }

        if ( ! empty($wrap['attr']['bg_color']) ) {
          $wrap_style[] = 'background-color:'. esc_attr($wrap['attr']['bg_color']);
        }

        if ( ! empty($wrap['attr']['move_up']) ) {
          $wrap_style[] = 'margin-top:-'. intval($wrap['attr']['move_up']) .'px';
        }

        if ($wrap['attr']['bg_image']) {

          $wrap_bg_attr = explode(';', $wrap['attr']['bg_position']);

          $wrap_bg[] = 'background-image:url('. esc_url($wrap['attr']['bg_image']) .')';

          $wrap_bg[] = 'background-repeat:'. esc_attr($wrap_bg_attr[0]);
          $wrap_bg[] = 'background-position:'. esc_attr($wrap_bg_attr[1]);

          if($wrap_bg_attr[2]){
            $wrap_bg[] = 'background-attachment:'. esc_attr($wrap_bg_attr[2]);
          }
          if($wrap_bg_attr[3]){
            $wrap_bg[] = 'background-size:'. esc_attr($wrap_bg_attr[3]);
          }

          // parallax

          if ( 'fixed' == $wrap_bg_attr[2] ) {
            if ( empty($wrap_bg_attr[4]) || $wrap_bg_attr[4] != 'still' ) {
              if ( 'translate3d' == mfn_parallax_plugin() ) {
                if ( mfn_is_mobile() ) {
                  $wrap_bg['attachment'] = 'background-attachment:scroll';
                } else {
                  $wrap_bg = array();
                }
              }
            }
          }

        }

        // prepare styles

        $wrap_style = array_merge($wrap_style, $wrap_bg);
        $wrap_style = implode(';', $wrap_style);

        if( $wrap_style ){
          $custom_css .= $selector. '{'. $wrap_style .'}';
        }

      }

    }

    return $custom_css;

	}

}
