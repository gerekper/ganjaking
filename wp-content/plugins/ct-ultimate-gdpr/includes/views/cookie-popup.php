<?php

/**
 * The template for displaying cookie popup on front
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr folder
 *
 * @version 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var array $options */

$distance = isset( $options['cookie_position_distance'] ) ? $options['cookie_position_distance'] : 0;
$skin_location_class = $box_style_class = $box_shape_class = $btn_shape_class = $btn_size_class = $top_panel_attr =
$bottom_panel_attr = $card_attr = $needle = $replacement = $haystack = $popup_panel_open_tag =
$popup_btn_wrap_open_tag = $close_tag = $cookie_box_bg = $box_css = $light_img = $content_style = $skin_name =
$arrow = $btn_icon = $check = $left_cog = $right_cog = $accept_border_color = $accept_bg_color = $accept_color =
$accept_btn_content = $is_10_set = $adv_set_border_color = $adv_set_bg_color = $adv_set_color =
$adv_set_btn_content = $panel_attr = $_10_set = $btn_wrapper = $attachment_url = $attachment_image = '';
$class_array = $attr_array = array();
$accept_label = esc_html(
    ct_ultimate_gdpr_get_value(
        'cookie_popup_label_accept',
        $options,
        esc_html__( 'Accept', 'ct-ultimate-gdpr' ),
        false
    )
);
$adv_set_label = esc_html(
    ct_ultimate_gdpr_get_value(
        'cookie_popup_label_settings',
        $options,
        esc_html__( 'Change Settings', 'ct-ultimate-gdpr' ),
        false
    )
);
$cookie_read_page_custom = isset( $options['cookie_read_page_custom'] ) ? $options['cookie_read_page_custom'] : '';
$cookie_read_page = isset( $options['cookie_read_page'] ) ? $options['cookie_read_page'] : '';

if ( isset( $options['cookie_position'] ) ) :
    $ct_gdpr_is_panel_array = ct_gdpr_is_panel( $options['cookie_position'], $distance );
    $skin_location_class = $ct_gdpr_is_panel_array['skin_location_class'];
    $panel_attr = $ct_gdpr_is_panel_array['panel_attr'];
    $popup_panel_open_tag = $ct_gdpr_is_panel_array['popup_panel_open_tag'];
    $close_tag = $ct_gdpr_is_panel_array['close_tag'];
endif;

if ( isset( $options['cookie_box_style'] ) ) :
    $box_css = $options['cookie_box_style'];

    $ct_gdpr_set_btn_css_array = ct_gdpr_set_btn_css(
        $box_css,
        $options['cookie_position'],
        $options['cookie_button_bg_color'],
        $options['cookie_button_border_color'],
        $options['cookie_button_text_color']
    );
    $accept_border_color = $ct_gdpr_set_btn_css_array['accept_border_color'];
    $accept_bg_color = $ct_gdpr_set_btn_css_array['accept_bg_color'];
    $accept_color = $ct_gdpr_set_btn_css_array['accept_color'];

    $adv_set_border_color = $ct_gdpr_set_btn_css_array['adv_set_border_color'];
    $adv_set_bg_color = $ct_gdpr_set_btn_css_array['adv_set_bg_color'];
    $adv_set_color = $ct_gdpr_set_btn_css_array['adv_set_color'];
endif;

if ( isset( $box_css ) ) :
    $cookie_box_style_array = ct_gdpr_get_box_style_class_and_wrapper( $box_css );
    $box_style_class = $cookie_box_style_array['box_style_class'];
    $content_style = $cookie_box_style_array['content_style'];
    $popup_btn_wrap_open_tag = $cookie_box_style_array['popup_btn_wrap_open_tag'];
    $close_tag = $cookie_box_style_array['close_tag'];
    $skin_set = $cookie_box_style_array['skin_set'];

    $btn_wrapper = $skin_set == '1' ? '<div class="ct-ultimate-gdpr-cookie-popup-btn-wrapper">' : '' ;

    $skin_name = strtok( $box_css, '_' );
    if ( isset( $options['cookie_button_settings'] ) ) :
        $btn_settings = $options['cookie_button_settings'];
        $ct_gdpr_get_icon_array = ct_gdpr_get_icon( $btn_settings, $skin_name );
        $arrow = $ct_gdpr_get_icon_array['arrow'];
        $btn_icon = $ct_gdpr_get_icon_array['btn_icon'];
        $check = $ct_gdpr_get_icon_array['check'];
        $right_cog = $ct_gdpr_get_icon_array['right_cog'];
        $left_cog = $ct_gdpr_get_icon_array['left_cog'];
        $accept_btn_content = ct_gdpr_get_accept_content( $btn_settings, $skin_name, $check, $accept_label );
        $adv_set_btn_content = ct_gdpr_get_adv_set_content( $btn_settings, $adv_set_label, $left_cog, $right_cog );
        $read_more_10_set = ct_gdpr_get_10_set_read_more_content( $skin_name, $options, $arrow );
    endif;
endif;

if ( isset( $options['cookie_box_shape'] ) ) :
    if ( $options['cookie_box_shape'] == 'squared' ) :
        $box_shape_class = esc_attr( 'ct-ultimate-gdpr-cookie-popup-squared' );
    endif;
endif;

if ( isset( $options['cookie_button_shape'] ) ) :
    if ( $options['cookie_button_shape'] == 'rounded' ) :
        $btn_shape_class = esc_attr( 'ct-ultimate-gdpr-cookie-popup-button-rounded' );
    endif;
endif;

if ( isset( $options['cookie_button_size'] ) ) :
    if ( $options['cookie_button_size'] == 'large' ) :
        $btn_size_class = esc_attr( 'ct-ultimate-gdpr-cookie-popup-button-large' );
    endif;
endif;

$ct_gdpr_get_box_bg_array = ct_gdpr_get_box_bg( $options['cookie_background_image'], $box_css );
$cookie_box_bg = $ct_gdpr_get_box_bg_array['img'];
$light_img = $ct_gdpr_get_box_bg_array['light_img'];

$class_array = array(
    $skin_location_class,
    $box_style_class,
    $box_shape_class,
    $btn_shape_class,
    $btn_size_class,
);
$attr_array = array(
    $panel_attr,
    $bottom_panel_attr,
    $ct_gdpr_is_panel_array['card_attr'],
    $cookie_box_bg,
);

$is_10_set = strtok( $box_style_class, ' ' );
$_10_set = $is_10_set == 'ct-ultimate-gdpr-cookie-popup-10-set' ? true : false;
?>

<div
    id="ct-ultimate-gdpr-cookie-popup"
    class="ct-ultimate-gdpr-cookie-popup-standard-settings <?php echo ct_gdpr_set_class_attr( $class_array ); ?>"
    style="background-color: <?php echo esc_attr( $options['cookie_background_color'] ); ?>;
            color: <?php echo esc_attr( $options['cookie_text_color'] ); ?>;
    <?php echo ct_gdpr_set_class_attr( $attr_array ); ?>
">

    <?php
    if( isset( $options['cookie_gear_close_box'] ) && $options['cookie_gear_close_box'] == 'on' ){
        ?>
        <a href = "javascript:void(0);" id = "ct-ultimate-cookie-close-modal"><i class="fa fa-times"></i></a>
        <?php
    }
    ?>

    <?php echo $popup_panel_open_tag; ?>
        <div id="ct-ultimate-gdpr-cookie-content" <?php echo $content_style; ?>>
            <?php echo $light_img; ?>
            <?php echo wp_kses_post( $options['cookie_content'] ); ?>
            <?php echo $read_more_10_set; ?>
        </div>

        <?php echo $popup_btn_wrap_open_tag; ?>
        <?php echo $btn_wrapper; ?>
            <div
                id="ct-ultimate-gdpr-cookie-accept"
                style="border-color: <?php echo esc_attr( $accept_border_color );
                        ?>; background-color: <?php echo esc_attr( $accept_bg_color );
                        ?>; color: <?php echo esc_attr( $accept_color );
            ?>;">
                <?php echo $accept_btn_content; ?>
            </div>

            <?php
            if (
                ! $_10_set
                && (
                    $cookie_read_page
                    || $cookie_read_page_custom
                )
            ) :
            ?>
            <div
                id="ct-ultimate-gdpr-cookie-read-more"
                style="border-color: <?php echo esc_attr( $options['cookie_button_border_color'] );
                        ?>; background-color: <?php echo esc_attr( $options['cookie_button_bg_color'] );
                        ?>; color: <?php echo esc_attr( $options['cookie_button_text_color'] );
            ?>;"><?php
                echo esc_html(
                    ct_ultimate_gdpr_get_value(
                        'cookie_popup_label_read_more',
                        $options,
                        esc_html__( 'Read more', 'ct-ultimate-gdpr' ),
                        false
                    )
                ) . $btn_icon;
            ?></div>
            <?php endif; ?>

        <?php echo $close_tag // .ct-ultimate-gdpr-cookie-buttons.ct-clearfix ?>

        <div class="ct-clearfix"></div>

    <?php echo $close_tag // .ct-container.ct-ultimate-gdpr-cookie-popup-[top/bottom]Panel ?>

</div><?php // #ct-ultimate-gdpr-cookie-popup ?>