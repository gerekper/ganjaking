<?php

/**
 * The template for displaying age verification popup on front
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr folder
 *
 * @version 1.0
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

/** @var array $options */

// get default date value from options
try {
    $date_placeholder  = new DateTime($options['age_placeholder'] ?: 'now');
    $year_placeholder  = $date_placeholder->format('Y');
    $month_placeholder = $date_placeholder->format('m');
    $day_placeholder   = $date_placeholder->format('j');
} catch (Exception $e) {
    $year_placeholder  = date('Y');
    $month_placeholder = date('m');;
    $day_placeholder = date('j');;
}


$distance            = isset($options['age_position_distance']) ? $options['age_position_distance'] : 0;
$skin_location_class = $box_style_class = $box_shape_class = $btn_shape_class = $btn_size_class = $top_panel_attr =
$bottom_panel_attr = $card_attr = $needle = $replacement = $haystack = $popup_panel_open_tag =
$popup_btn_wrap_open_tag = $close_tag = $age_box_bg = $box_css = $light_img = $content_style = $skin_name =
$arrow = $btn_icon = $check = $left_cog = $right_cog = $accept_border_color = $accept_bg_color = $accept_color =
$accept_btn_content = $is_10_set = $adv_set_border_color = $adv_set_bg_color = $adv_set_color =
$adv_set_btn_content = $panel_attr = $_10_set = $btn_wrapper = $attachment_url = $attachment_image = '';
$class_array         = $attr_array = array();
$accept_label        = esc_html(
    ct_ultimate_gdpr_get_value(
        'age_popup_label_accept',
        $options,
        esc_html__('Accept', 'ct-ultimate-gdpr'),
        false
    )
);

if (isset($options['age_position'])) :
    $ct_gdpr_is_panel_array = ct_gdpr_is_panel($options['age_position'], $distance);
    $skin_location_class    = $ct_gdpr_is_panel_array['skin_location_class'];
    $panel_attr             = $ct_gdpr_is_panel_array['panel_attr'];
    $popup_panel_open_tag   = $ct_gdpr_is_panel_array['popup_panel_open_tag'];
    $close_tag              = $ct_gdpr_is_panel_array['close_tag'];
endif;

if (isset($options['age_box_style'])) :
    $box_css = $options['age_box_style'];

    $ct_gdpr_set_btn_css_array = ct_gdpr_set_btn_css(
        $box_css,
        $options['age_position'],
        $options['age_button_bg_color'],
        $options['age_button_border_color'],
        $options['age_button_text_color']
    );
    $accept_border_color       = $ct_gdpr_set_btn_css_array['accept_border_color'];
    $accept_bg_color           = $ct_gdpr_set_btn_css_array['accept_bg_color'];
    $accept_color              = $ct_gdpr_set_btn_css_array['accept_color'];

    $adv_set_border_color = $ct_gdpr_set_btn_css_array['adv_set_border_color'];
    $adv_set_bg_color     = $ct_gdpr_set_btn_css_array['adv_set_bg_color'];
    $adv_set_color        = $ct_gdpr_set_btn_css_array['adv_set_color'];
endif;

if (isset($box_css)) :
    $age_box_style_array     = ct_gdpr_get_box_style_class_and_wrapper($box_css);
    $box_style_class         = $age_box_style_array['box_style_class'];
    $content_style           = $age_box_style_array['content_style'];
    $popup_btn_wrap_open_tag = $age_box_style_array['popup_btn_wrap_open_tag'];
    $close_tag               = $age_box_style_array['close_tag'];
    $skin_set                = $age_box_style_array['skin_set'];

    $btn_wrapper = $skin_set == '1' ? '<div class="ct-ultimate-gdpr-age-popup-btn-wrapper">' : '';

    $skin_name = strtok($box_css, '_');
    if (isset($options['age_button_settings'])) :
        $btn_settings           = $options['age_button_settings'];
        $ct_gdpr_get_icon_array = ct_gdpr_get_icon($btn_settings, $skin_name);
        $arrow                  = $ct_gdpr_get_icon_array['arrow'];
        $btn_icon               = $ct_gdpr_get_icon_array['btn_icon'];
        $check                  = $ct_gdpr_get_icon_array['check'];
        $right_cog              = $ct_gdpr_get_icon_array['right_cog'];
        $left_cog               = $ct_gdpr_get_icon_array['left_cog'];
        $accept_btn_content     = ct_gdpr_get_accept_content($btn_settings, $skin_name, $check, $accept_label);
        $read_more_10_set       = ct_gdpr_get_10_set_read_more_content($skin_name, $options, $arrow);
    endif;
endif;

if (isset($options['age_box_shape'])) :
    if ($options['age_box_shape'] == 'squared') :
        $box_shape_class = esc_attr('ct-ultimate-gdpr-age-popup-squared');
    endif;
endif;

if (isset($options['age_button_shape'])) :
    if ($options['age_button_shape'] == 'rounded') :
        $btn_shape_class = esc_attr('ct-ultimate-gdpr-age-popup-button-rounded');
    endif;
endif;

if (isset($options['age_button_size'])) :
    if ($options['age_button_size'] == 'large') :
        $btn_size_class = esc_attr('ct-ultimate-gdpr-age-popup-button-large');
    endif;
endif;

$ct_gdpr_get_box_bg_array = ct_gdpr_get_box_bg($options['age_background_image'], $box_css);
$age_box_bg               = $ct_gdpr_get_box_bg_array['img'];
$light_img                = $ct_gdpr_get_box_bg_array['light_img'];

$class_array = array(
    $skin_location_class,
    $box_style_class,
);

$attr_array  = array(
    $panel_attr,
    $bottom_panel_attr,
    $ct_gdpr_is_panel_array['card_attr'],
);

?>

<div
    id="ct-ultimate-gdpr-age-popup"
    class="<?php echo ct_gdpr_set_class_attr($class_array); ?>"
    <?php if ($options['age_box_style'] == 'none') : ?>
        style="<?php if ($options['age_box_shape'] == 'rounded') echo 'border-radius: 8px'; ?>; background-color: <?php echo esc_attr($options['age_background_color']); ?>; color: <?php echo esc_attr($options['age_text_color']); ?>;
        <?php echo ct_gdpr_set_class_attr($attr_array); ?>
        <?php echo $ct_gdpr_get_box_bg_array['img']; ?>"
    <?php else : ?>
        style="<?php echo ct_gdpr_set_class_attr($attr_array); ?>"
    <?php endif; ?>
>

    <?php echo $popup_panel_open_tag; ?>

    <div class="ct-ultimate-gdpr-age-content" <?php echo $content_style; ?>>

        <h4 class="ct-ultimate-gdpr-age-content-title"><?php echo esc_html($options['age_popup_title']); ?></h4>

        <div class="ct-ultimate-gdpr-age-content-description"><?php echo wp_kses_post($options['age_popup_content']); ?></div>

        <div class="ct-ultimate-gdpr-age-content-form">
            <div class="ct-gdpr-select-wrapper">
                <select name="ct-ultimate-gdpr-age-date-of-birth-month" id="months" class="ct-gdpr-input ct-gdpr-input-month">
                    <?php
                        foreach (range(1, 12) as $month_number):

                            $temp_date  = DateTime::createFromFormat('!m', $month_number);
                            $month_name = $temp_date->format('F');

                            $selected = $month_number == $month_placeholder ? 'selected' : '';
                            echo "<option value='$month_number' $selected>$month_name</option>";

                        endforeach;
                    ?>
                </select>
            </div>

            <input name="ct-ultimate-gdpr-age-date-of-birth-day" type="number" max="31" value="<?php echo esc_attr($day_placeholder); ?>" class="ct-gdpr-input ct-gdpr-input-number ct-gdpr-input-day">

            <input name="ct-ultimate-gdpr-age-date-of-birth-year" type="number" max="<?php echo date('Y'); ?>" value="<?php echo esc_attr($year_placeholder); ?>" class="ct-gdpr-input ct-gdpr-input-number ct-gdpr-input-year">

            <button
                id="ct-ultimate-gdpr-age-accept"
                class="ct-gdpr-button ct-gdpr-popup-age__submit js-ct-gdpr-age-Accept"
                <?php if ($options['age_box_style'] == 'none') : ?>
                    style="<?php if ($options['age_button_shape'] == 'rounded') echo 'border-radius: 25px'; ?>; background-color: <?php echo esc_attr($options['age_button_bg_color']); ?>; color: <?php echo esc_attr($options['age_button_text_color']); ?>;"
                <?php endif; ?>
            >
                <?php echo wp_kses_post($options['age_popup_label_accept']); ?>
            </button>
        </div>
    </div>

</div><?php // #ct-ultimate-gdpr-age-popup ?>

