<?php

/**
 * Class CT_Ultimate_GDPR_Shortcode_Cookie_Popup
 */
class CT_Ultimate_GDPR_Shortcode_Cookie_Popup {

	/**
	 * @var string
	 */
	public static $tag = 'ultimate_gdpr_cookie_popup';

	/**
	 * CT_Ultimate_GDPR_Shortcode_Cookie_Popup constructor.
	 */
	public function __construct() {
		add_shortcode( self::$tag, array( $this, 'process' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_action' ) );
	}

	/**
	 *
	 */
	public function wp_enqueue_scripts_action() {

		if ( get_post() && false !== strpos( get_post()->post_content, "[{$this::$tag}]" ) ) {

			CT_Ultimate_GDPR::instance()
			                ->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )
			                ->wp_enqueue_scripts_action( true );

			wp_localize_script( 'ct-ultimate-gdpr-cookie-popup', 'ct_ultimate_gdpr_cookie_shortcode_popup',
				array(
					'always_visible' => true,
				)
			);

		}
	}


	/**
	 * Shortcode callback
	 *
	 * @param $atts
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function process( $atts, $content = '' ) {

		return $this->render( $content );

	}

	/**
	 * Render shortcode template
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function render( $content ) {

		$options = array_merge(
			CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )->get_default_options(),
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_options( CT_Ultimate_GDPR_Controller_Cookie::ID ),
			array( 'cookie_modal_always_visible' => true ),
			array( 'content' => $content )
		);

		ob_start();
		ct_ultimate_gdpr_locate_template(
			"cookie-group-popup",
			true,
			$options
		);

		return ob_get_clean();

	}
}

/**
 * @param string $panel_view
 * @param null $distance
 * @return mixed
 */
function ct_gdpr_is_panel( $panel_view = 'top_panel_', $distance = null ) {
    $panel['card_attr']             = '';
    $panel['popup_panel_open_tag']  = '';
    $panel['skin_location_class']   = '';
    $panel['panel_attr']            = '';

    if ( 'top_panel_' == $panel_view ) {

	    $panel['popup_panel_open_tag'] = "<div class='ct-container ct-ultimate-gdpr-cookie-popup-topPanel'>";
	    $panel['skin_location_class']  = 'ct-ultimate-gdpr-cookie-topPanel';
	    $panel['panel_attr']           = "top: 0px; width: 100%; border-radius: 0;";

    } elseif ( 'bottom_panel_' == $panel_view ) {

	    $panel['popup_panel_open_tag'] = "<div class='ct-container ct-ultimate-gdpr-cookie-popup-bottomPanel'>";
	    $panel['skin_location_class']  = 'ct-ultimate-gdpr-cookie-bottomPanel';
	    $panel['panel_attr']           = "bottom: 0px; width: 100%; border-radius: 0;";

    } elseif ( 'full_layout_panel_' == $panel_view ) {

	    $panel['popup_panel_open_tag'] = "<div class='ct-container ct-ultimate-gdpr-cookie-popup-fullPanel'>";
	    $panel['skin_location_class']  = 'ct-ultimate-gdpr-cookie-fullPanel';
	    $panel['panel_attr']           = "";

    } else {
		$replacement        = ": " . ( int ) $distance . "px; ";
		$panel['card_attr'] = str_replace( '_', $replacement, $panel_view );
	}

    $panel['close_tag'] = '</div>';
    return $panel;
}

/**
 * @param $class_attr_array
 * @return string
 */
function ct_gdpr_set_class_attr( $class_attr_array ) {
    return implode( ' ', array_filter( $class_attr_array ) );
}

/**
 * @param null $skin_name
 * @param null $cookie_position
 * @param null $btn_bg_color
 * @param null $btn_border_color
 * @param null $color
 * @return array
 */
function ct_gdpr_set_btn_css(
    $skin_name = null,
    $cookie_position = null,
    $btn_bg_color = null,
    $btn_border_color = null,
    $color = null
) {
    $btn_css = array();
    if ( isset( $skin_name ) ) {

        // ACCEPT BACKGROUND COLOR
        if (
            ( 'modern' == $skin_name && 'top_panel_' == $cookie_position )
            || ( 'modern' == $skin_name && 'bottom_panel_' == $cookie_position )
        ) :
            $btn_css['accept_bg_color'] = $btn_bg_color;
        elseif (
            'apas_blue' == $skin_name
            || 'oreo_blue' == $skin_name
            || 'wafer_blue' == $skin_name
            || 'wafer_black' == $skin_name
            || 'kichel_blue' == $skin_name
            || 'macaron_blue' == $skin_name
            || 'wibele_blue' == $skin_name
        ) :
            $btn_css['accept_bg_color'] = '#369ee3';
        elseif ( 'kahk_blue' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#313641';
        elseif (
            'apas_black' == $skin_name
            || 'kahk_black' == $skin_name
            || 'kahk_white' == $skin_name
            || 'oreo_black' == $skin_name
            || 'oreo_white' == $skin_name
            || 'wafer_white' == $skin_name
            || 'tareco_black' == $skin_name
            || 'macaron_black' == $skin_name
            || 'macaron_white' == $skin_name
            || 'wibele_black' == $skin_name
            || 'wibele_white' == $skin_name
        ) :
            $btn_css['accept_bg_color'] = '#45bba5';
        elseif ( 'apas_white' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#369ee3';
        elseif ( 'jumble_blue' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#369ee3;background-image: linear-gradient(to right, #4d6dc0 , #369ee3);';
        elseif ( 'jumble_black' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#7889b5;background-image: linear-gradient(to right, #6b7dac , #8393bc);';
        elseif ( 'jumble_white' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#46bcb9;background-image: linear-gradient(to right, #49bee0 , #45bba5);';
        elseif (
            'khapse_blue' == $skin_name
            || 'khapse_black' == $skin_name
            || 'khapse_white' == $skin_name
        ) :
            $btn_css['accept_bg_color'] = '#de7834';
        elseif (
            'tareco_blue' ==  $skin_name
            || 'tareco_white' ==  $skin_name
        ) :
            $btn_css['accept_bg_color'] = '#316ab1';
        elseif ( 'kichel_black' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#51971e';
        elseif ( 'kichel_white' == $skin_name ) :
            $btn_css['accept_bg_color'] = '#d71852';
        else :
            $btn_css['accept_bg_color'] = $btn_bg_color;
        endif;

        // ACCEPT BORDER COLOR
        if (
            'apas_blue' == $skin_name
            || 'oreo_blue' == $skin_name
            || 'wafer_blue' == $skin_name
            || 'wafer_black' == $skin_name
            || 'kichel_blue' == $skin_name
            || 'macaron_blue' == $skin_name
            || 'wibele_blue' == $skin_name
        ) :
            $btn_css['accept_border_color'] = '#369ee3';
        elseif ( 'kahk_blue' == $skin_name ) :
            $btn_css['accept_border_color'] = '#313641';
        elseif (
            'apas_black' == $skin_name
            || 'kahk_black' == $skin_name
            || 'kahk_white' == $skin_name
            || 'oreo_black' == $skin_name
            || 'oreo_white' == $skin_name
            || 'wafer_white' == $skin_name
            || 'tareco_black' == $skin_name
            || 'macaron_black' == $skin_name
            || 'macaron_white' == $skin_name
            || 'wibele_black' == $skin_name
            || 'wibele_white' == $skin_name
        ) :
            $btn_css['accept_border_color'] = '#45bba5';
        elseif ( 'apas_white' == $skin_name ) :
            $btn_css['accept_border_color'] = '#369ee3';
        elseif (
            'khapse_blue' == $skin_name
            || 'khapse_black' == $skin_name
            || 'khapse_white' == $skin_name
        ) :
            $btn_css['accept_border_color'] = '#de7834';
        elseif (
            'tareco_blue' == $skin_name
            || 'tareco_white' == $skin_name
        ) :
            $btn_css['accept_border_color'] = '#316ab1';
        elseif ( 'kichel_black' == $skin_name ) :
            $btn_css['accept_border_color'] = '#51971e';
        elseif ( 'kichel_white' == $skin_name ) :
            $btn_css['accept_border_color'] = '#d71852';
        else :
            $btn_css['accept_border_color'] = $btn_border_color;
        endif;

        // ACCEPT COLOR
        if ( 'apas_blue' == $skin_name ) :
            $btn_css['accept_color'] = '#e5e5e5';
        elseif (
            'apas_black' == $skin_name
            || 'apas_white' == $skin_name
            || 'kahk_blue' == $skin_name
            || 'kahk_black' == $skin_name
            || 'kahk_white' == $skin_name
            || 'oreo_blue' == $skin_name
            || 'oreo_black' == $skin_name
            || 'oreo_white' == $skin_name
            || 'wafer_blue' == $skin_name
            || 'wafer_black' == $skin_name
            || 'wafer_white' == $skin_name
            || 'jumble_blue' == $skin_name
            || 'jumble_black' == $skin_name
            || 'jumble_white' == $skin_name
            || 'khapse_blue' == $skin_name
            || 'khapse_black' == $skin_name
            || 'khapse_white' == $skin_name
            || 'tareco_blue' == $skin_name
            || 'tareco_black' == $skin_name
            || 'tareco_white' == $skin_name
            || 'kichel_blue' == $skin_name
            || 'kichel_black' == $skin_name
            || 'kichel_white' == $skin_name
            || 'macaron_blue' == $skin_name
            || 'macaron_black' == $skin_name
            || 'macaron_white' == $skin_name
            || 'wibele_blue' == $skin_name
            || 'wibele_black' == $skin_name
            || 'wibele_white' == $skin_name
        ) :
            $btn_css['accept_color'] = '#fff';
        elseif ( 'tareco_blue' == $skin_name ) :
            $btn_css['accept_color'] = '#316ab1';
        else :
            $btn_css['accept_color'] = $color;
        endif;

        // CHANGE SETTINGS BORDER COLOR
        if (
            'apas_black' == $skin_name
            || 'kahk_black' == $skin_name
            || 'kahk_white' == $skin_name
            || 'jumble_white' == $skin_name
            || 'wibele_white' == $skin_name
        ) :
            $btn_css['adv_set_border_color'] = '#45bba5';
        elseif ( 'kahk_blue' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#fff';
        elseif ( 'apas_white' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#e2f3ff';
        elseif (
            'wafer_blue' == $skin_name
            || 'wafer_black' == $skin_name
            || 'wafer_white' == $skin_name
            || 'jumble_blue' == $skin_name
            || 'jumble_black' == $skin_name
            || 'macaron_blue' == $skin_name
            || 'macaron_black' == $skin_name
            || 'macaron_black' == $skin_name
            || 'macaron_white' == $skin_name
            || 'wibele_blue' == $skin_name
            || 'wibele_black' == $skin_name
        ) :
            $btn_css['adv_set_border_color'] = '#808181';
        elseif (
            'oreo_blue' == $skin_name
            || 'oreo_white' == $skin_name
            || 'apas_blue' == $skin_name
        ) :
            $btn_css['adv_set_border_color'] = '#cacaca';
        elseif ( 'oreo_black' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#5f6165';
        elseif (
            'khapse_blue' == $skin_name
            || 'khapse_black' == $skin_name
        ) :
            $btn_css['adv_set_border_color'] = '#ccc';
        elseif ( 'khapse_white' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#252525';
        elseif (
            'tareco_blue' == $skin_name
            || 'tareco_black' == $skin_name
        ) :
            $btn_css['adv_set_border_color'] = '#f7f7f7';
        elseif ( 'tareco_white' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#e0e0e0';
        elseif ( 'kichel_blue' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#8b95ae';
        elseif ( 'kichel_black' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#8f9197';
        elseif ( 'kichel_white' == $skin_name ) :
            $btn_css['adv_set_border_color'] = '#131212';
        else :
            $btn_css['adv_set_border_color'] = $btn_border_color;
        endif;

        // CHANGE SETTINGS BACKGROUND COLOR
        if (
            ( 'modern' == $skin_name && 'top_panel_' == $cookie_position )
            || ( 'modern' == $skin_name && 'bottom_panel_' == $cookie_position )
        ) :
            $btn_css['adv_set_bg_color'] = esc_attr( $btn_bg_color );
        elseif (
            'apas_blue' == $skin_name
            || 'apas_black' == $skin_name
            || 'kahk_blue' == $skin_name
            || 'kahk_black' == $skin_name
            || 'kahk_white' == $skin_name
            || 'wafer_blue' == $skin_name
            || 'wafer_black' == $skin_name
            || 'wafer_white' == $skin_name
            || 'jumble_blue' == $skin_name
            || 'jumble_black' == $skin_name
            || 'jumble_white' == $skin_name
            || 'macaron_blue' == $skin_name
            || 'macaron_black' == $skin_name
            || 'macaron_white' == $skin_name
            || 'wibele_blue' == $skin_name
            || 'wibele_black' == $skin_name
            || 'wibele_white' == $skin_name
        ) :
            $btn_css['adv_set_bg_color'] = 'transparent';
        elseif ( 'apas_white' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#e2f3ff';
        elseif (
            'oreo_blue' == $skin_name
            || 'oreo_white' == $skin_name
        ) :
            $btn_css['adv_set_bg_color'] = '#cacaca';
        elseif ( 'oreo_black' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#5f6165';
        elseif (
            'khapse_blue' == $skin_name
            || 'khapse_black' == $skin_name
        ) :
            $btn_css['adv_set_bg_color'] = '#ccc';
        elseif ( 'khapse_white' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#252525';
        elseif (
            'tareco_blue' == $skin_name
            || 'tareco_black' == $skin_name
        ) :
            $btn_css['adv_set_bg_color'] = '#f7f7f7';
        elseif ( 'tareco_white' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#e0e0e0';
        elseif ( 'kichel_blue' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#8b95ae';
        elseif ( 'kichel_black' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#8f9197';
        elseif ( 'kichel_white' == $skin_name ) :
            $btn_css['adv_set_bg_color'] = '#131212';
        else :
            $btn_css['adv_set_bg_color'] = esc_attr( $btn_bg_color );
        endif;

        // CHANGE SETTINGS COLOR
        if ( 'apas_blue' == $skin_name ) :
            $btn_css['adv_set_color'] = '#a0a4af';
        elseif (
            'apas_black' == $skin_name
            || 'kahk_black' == $skin_name
            || 'kahk_white' == $skin_name
            || 'jumble_white' == $skin_name
            || 'tareco_black' == $skin_name
            || 'macaron_white' == $skin_name
            || 'wibele_white' == $skin_name
        ) :
            $btn_css['adv_set_color'] = '#45bba5';
        elseif ( 'apas_white' == $skin_name ) :
            $btn_css['adv_set_color'] = '#798e9d';
        elseif (
            'wafer_blue' == $skin_name
            || 'wafer_black' == $skin_name
            || 'jumble_blue' == $skin_name
            || 'jumble_black' == $skin_name
            || 'macaron_blue' == $skin_name
            || 'macaron_black' == $skin_name
            || 'wibele_blue' == $skin_name
            || 'wibele_black' == $skin_name
        ) :
            $btn_css['adv_set_color'] = '#cacaca';
        elseif (
            'oreo_blue' == $skin_name
            || 'oreo_white' == $skin_name
        ) :
            $btn_css['adv_set_color'] = '#525253';
        elseif (
            'oreo_black' == $skin_name
            || 'kahk_blue' == $skin_name
            || 'kichel_blue' == $skin_name
            || 'kichel_black' == $skin_name
            || 'kichel_white' == $skin_name
        ) :
            $btn_css['adv_set_color'] = '#fff';
        elseif ( 'wafer_white' == $skin_name ) :
            $btn_css['adv_set_color'] = '#7c7c7c';
        elseif (
            'khapse_blue' == $skin_name
            || 'khapse_black' == $skin_name
        ) :
            $btn_css['adv_set_color'] = '#333742';
        elseif ( 'khapse_white' == $skin_name ) :
            $btn_css['adv_set_color'] = '#cccccc';
        elseif (
            'tareco_blue' == $skin_name
            || 'tareco_white' == $skin_name
        ) :
            $btn_css['adv_set_color'] = '#316ab1';
        else :
            $btn_css['adv_set_color'] = $color;
        endif;

    }
    return $btn_css;
}

/**
 * @param null $skin_name
 * @return mixed
 */
function ct_gdpr_get_box_style_class_and_wrapper( $skin_name = null ) {
    $gdpr_pre = 'ct-ultimate-gdpr-cookie-popup-';
    $color_attr = "style='color: #";
    $class_wrap['content_style'] = "";
    $class_wrap['popup_btn_wrap_open_tag'] = "";
    $class_wrap['close_tag'] = '';
    $class_wrap['skin_set'] = '';
    if ( 'modern' == $skin_name ) :
        $class_wrap['box_style_class'] = 'ct-ultimate-gdpr-cookie-popup-modern';
        $class_wrap['popup_btn_wrap_open_tag'] = "<div class='ct-ultimate-gdpr-cookie-buttons ct-clearfix'>";
        $class_wrap['close_tag'] = '</div>';
    elseif ( 'classic_blue' == $skin_name ) :
        $class_wrap['box_style_class'] =  esc_attr( "ct-ultimate-gdpr-cookie-classic-blue {$gdpr_pre}classic" );
        $class_wrap['content_style'] = $color_attr . "fff;'";
    elseif ( 'classic_light' == $skin_name ) :
        $class_wrap['box_style_class'] =  esc_attr( "ct-ultimate-gdpr-cookie-classic-light {$gdpr_pre}classic" );
    elseif ( 'classic' == $skin_name ) :
        $class_wrap['box_style_class'] =  esc_attr( "{$gdpr_pre}classic" );
    elseif ( 'apas_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}apas {$gdpr_pre}apas-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'apas_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}apas {$gdpr_pre}apas-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'apas_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}apas {$gdpr_pre}apas-white" );
        $class_wrap['content_style'] = $color_attr . "808080;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'kahk_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}kahk {$gdpr_pre}kahk-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'kahk_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}kahk {$gdpr_pre}kahk-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'kahk_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}kahk {$gdpr_pre}kahk-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'oreo_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}oreo {$gdpr_pre}oreo-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'oreo_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}oreo {$gdpr_pre}oreo-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'oreo_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}oreo {$gdpr_pre}oreo-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'wafer_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}wafer {$gdpr_pre}wafer-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'wafer_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}wafer {$gdpr_pre}wafer-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'wafer_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}wafer {$gdpr_pre}wafer-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'jumble_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}jumble {$gdpr_pre}jumble-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'jumble_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}jumble {$gdpr_pre}jumble-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'jumble_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}jumble {$gdpr_pre}jumble-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'khapse_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}khapse {$gdpr_pre}khapse-blue" );
        $class_wrap['content_style'] = $color_attr . "b2b2b2;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'khapse_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}khapse {$gdpr_pre}khapse-black" );
        $class_wrap['content_style'] = $color_attr . "999999;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'khapse_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}khapse {$gdpr_pre}khapse-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'tareco_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}tareco {$gdpr_pre}tareco-blue" );
        $class_wrap['content_style'] = $color_attr . "316ab1;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'tareco_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}tareco {$gdpr_pre}tareco-black" );
        $class_wrap['content_style'] = $color_attr . "fff;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'tareco_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}tareco {$gdpr_pre}tareco-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'kichel_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}kichel {$gdpr_pre}kichel-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'kichel_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}kichel {$gdpr_pre}kichel-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'kichel_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}kichel {$gdpr_pre}kichel-white" );
        $class_wrap['content_style'] = $color_attr . "808080;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'macaron_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}macaron {$gdpr_pre}macaron-blue" );
        $class_wrap['content_style'] = $color_attr . "cccccc;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'macaron_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}macaron {$gdpr_pre}macaron-black" );
        $class_wrap['content_style'] = $color_attr . "cccccc;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'macaron_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}macaron {$gdpr_pre}macaron-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'wibele_blue' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}wibele {$gdpr_pre}wibele-blue" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'wibele_black' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}wibele {$gdpr_pre}wibele-black" );
        $class_wrap['content_style'] = $color_attr . "e5e5e5;'";
        $class_wrap['skin_set'] = '1';
    elseif ( 'wibele_white' == $skin_name ) :
        $class_wrap['box_style_class'] = esc_attr( "{$gdpr_pre}10-set {$gdpr_pre}wibele {$gdpr_pre}wibele-white" );
        $class_wrap['content_style'] = $color_attr . "666666;'";
        $class_wrap['skin_set'] = '1';
    endif;
    return $class_wrap;
}

/**
 * @param null $btn_settings
 * @param null $skin_name
 * @return mixed
 */
function ct_gdpr_get_icon( $btn_settings = null, $skin_name = null ) {
    $icons['arrow'] = '';
    $icons['btn_icon'] = "";
    $icons['check'] = "";
    $icons['right_cog'] = "";
    $icons['left_cog'] = '';
    if ( isset( $btn_settings ) ) :
        if ( ( 'text_icon_' == $btn_settings ) ) :
            $icons['arrow'] = '<span class="fa fa-long-arrow-right"></span>';
            $icons['btn_icon'] = "<span class='fa fa-long-arrow-right ct-ultimate-gdpr-button-icon-right'></span>";
            $icons['check'] = "<span class='fa fa-check ct-ultimate-gdpr-button-icon-left'></span>";
            if ( 'kichel' != $skin_name ) :
                $icons['right_cog'] = " <span class='fa fa-cog ct-ultimate-gdpr-button-icon-right'></span>";
            else :
                $icons['left_cog'] = "<span class='fa fa-cog ct-ultimate-gdpr-button-icon-right ct-ultimate-gdpr-cookie-popup-icon-left'></span>";
            endif;
        endif;
    endif;
    return $icons;
}

/**
 * @param null $btn_settings
 * @param null $skin_name
 * @param null $check
 * @param null $accept_label
 * @return string|null
 */
function ct_gdpr_get_accept_content( $btn_settings = null, $skin_name = null, $check = null, $accept_label = null ) {
    if (
        'text_icon_' == $btn_settings
        || 'classic_blue' == $skin_name
        || 'classic_light' == $skin_name
    ) :
        $accept_btn_content = $check . $accept_label;
    else :
        $accept_btn_content = $accept_label;
    endif;
    return $accept_btn_content;
}

/**
 * @param null $btn_settings
 * @param null $adv_set_label
 * @param null $left_cog
 * @param null $right_cog
 * @return string|null
 */
function ct_gdpr_get_adv_set_content(
    $btn_settings = null,
    $adv_set_label = null,
    $left_cog = null,
    $right_cog = null
) {
    if ( 'text_only_' == $btn_settings ) :
        $adv_set_content = $adv_set_label;
    else:
        $adv_set_content = $left_cog . $adv_set_label . $right_cog;
    endif;
    return $adv_set_content;
}

/**
 * @param null $skin_name
 * @param $options
 * @param $arrow
 * @return string
 */
function ct_gdpr_get_10_set_read_more_content( $skin_name = null, $options, $arrow ) {
    $read_more_10_set = '';
    if (
        'apas' == $skin_name
        || 'kahk' == $skin_name
        || 'oreo' == $skin_name
        || 'wafer' == $skin_name
        || 'jumble' == $skin_name
        || 'khapse' == $skin_name
        || 'tareco' == $skin_name
        || 'kichel' == $skin_name
        || 'macaron' == $skin_name
        || 'wibele' == $skin_name
    ) :
        if( $options['cookie_read_page'] || $options['cookie_read_page_custom'] ):
            $read_more_10_set = '<span id="ct-ultimate-gdpr-cookie-read-more">';
            $read_more_10_set .= esc_html(
                    ct_ultimate_gdpr_get_value(
                        'cookie_popup_label_read_more',
                        $options,
                        esc_html__( 'Read more', 'ct-ultimate-gdpr' ),
                        false
                    )
                ) . ' ' . $arrow;
            $read_more_10_set .= '</span>';
        endif;
    endif;
    return $read_more_10_set;
}

/**
 * @param null $bg_img
 * @param null $skin_name
 * @return mixed
 */
function ct_gdpr_get_box_bg( $bg_img = null, $skin_name = null ) {
    $attachment_image = wp_get_attachment_image_src( $bg_img, 'full' );
    $attachment_url = $attachment_image ? esc_url( $attachment_image[0] ) : ' ';
		if($attachment_url) {
			$box_bg['img'] = 'background-image:url(' . $attachment_url . '); background-size:cover; background-position: 100%;';
		}
    $box_bg['light_img'] = "";
    if ( 'classic_blue' == $skin_name ) :
        $box_bg['img'] = 'background-image:url(' . ct_ultimate_gdpr_url() . '/assets/css/images/Cookie-pop-up-bg.jpg );';
        $box_bg['img'] .= 'background-position: right -24px top -29px; background-color:#262626 !important;';
        $box_bg['img'] .= 'background-repeat: no-repeat;';
    elseif ( 'classic_light' == $skin_name ) :
        $box_bg['img'] = 'background-color:#ffffff !important; color:#333;';
        $box_bg['light_img'] = "<div class='style-light-icon'>";
        $box_bg['light_img'] .= "<img src='" . ct_ultimate_gdpr_url() . "/assets/css/images/cookie-icon-image.jpg'></div>";
    elseif (
        'apas_blue' == $skin_name
        || 'oreo_blue' == $skin_name
        || 'wafer_blue' == $skin_name
        || 'jumble_blue' == $skin_name
        || 'kichel_blue' == $skin_name
        || 'macaron_blue' == $skin_name
        || 'wibele_blue' == $skin_name
    ) :
        $box_bg['img'] = 'background-color: #2a3e71 !important;';
    elseif (
        'apas_black' == $skin_name
        || 'kahk_black' == $skin_name
        || 'oreo_black' == $skin_name
        || 'wafer_black' == $skin_name
        || 'jumble_black' == $skin_name
        || 'kichel_black' == $skin_name
        || 'macaron_black' == $skin_name
        || 'wibele_black' == $skin_name
    ) :
        $box_bg['img'] = 'background-color: #323742 !important;';
    elseif (
        'apas_white' == $skin_name
        || 'kahk_white' == $skin_name
        || 'oreo_white' == $skin_name
        || 'jumble_white' == $skin_name
        || 'wibele_white' == $skin_name
    ) :
        $box_bg['img'] = 'background-color: #fff !important;';
    elseif ( 'wafer_white' == $skin_name ) :
        $box_bg['img'] = 'background-color: #f4f4f4 !important;';
    elseif ( 'khapse_blue' == $skin_name ) :
        $box_bg['img'] = 'background-color: rgba(42, 62, 113, .97) !important;';
    elseif ( 'khapse_black' == $skin_name ) :
        $box_bg['img'] = 'background-color: rgba(50, 55, 66, .97) !important;';
    elseif ( 'khapse_white' == $skin_name ) :
        $box_bg['img'] = 'background-color: rgba(244, 244, 244, .97) !important;';
    elseif ( 'tareco_blue' == $skin_name ) :
        $box_bg['img'] = 'background-image: linear-gradient(to right, #a7cbfd , #bde4fb); !important;';
    elseif ( 'tareco_black' == $skin_name ) :
        $box_bg['img'] = 'background-image: linear-gradient(to right, #2d353f , #40404b); !important;';
    elseif ( 'tareco_white' == $skin_name ) :
        $box_bg['img'] = 'background-image: linear-gradient(to right, #e5dfdf , #faf9f9); !important;';
    elseif ( 'kahk_blue' == $skin_name ) :
        $box_bg['img'] = 'background-color: #44b49f !important;';
        $box_bg['img'] .= 'background-image: linear-gradient(to right, #44b49f, #44b49f, #44b49f, #43b49f, #43b49f);';
    elseif (
        'kichel_white' == $skin_name
        || 'macaron_white' == $skin_name
    ) :
        $box_bg['img'] = 'background-color: #f2f2f2 !important;';
    endif;
    return $box_bg;
}