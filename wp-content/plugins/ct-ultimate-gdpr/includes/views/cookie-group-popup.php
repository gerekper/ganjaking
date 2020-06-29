<?php

/**
 * The template for displaying cookie group popup on front
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr folder
 *
 * @version 1.0
 *
 */

if (!defined('ABSPATH')) {
	exit;
}

/* MODAL VARIABLES */
?>
<style>
    #ct-ultimate-gdpr-cookie-modal-body h1,
    #ct-ultimate-gdpr-cookie-modal-body h2,
    #ct-ultimate-gdpr-cookie-modal-body h3,
    #ct-ultimate-gdpr-cookie-modal-body h4,
    #ct-ultimate-gdpr-cookie-modal-body h5,
    #ct-ultimate-gdpr-cookie-modal-body h6 {
        color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;
    }
</style>

<?php
// class for number of groups
$number_of_groups = 0;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_1'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_2'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_3'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_4'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_5'] ) ) ? ++ $number_of_groups : $number_of_groups;

$group_class = 'ct-ultimate-gdpr--Groups-' . ( 5 - $number_of_groups );
$group_class = ( empty( $options['cookie_group_popup_hide_level_1'] ) ) ? $group_class : $group_class . ' ct-ultimate-gdpr--NoBlockGroup';

if ( isset ( $options['cookie_trigger_modal_bg_shape'] ) ) :
	if ( $options['cookie_trigger_modal_bg_shape'] == 'round' ):
		$cookie_trigger_modal_bg_shape = 'ct-ultimate-gdpr-trigger-modal-round';
    elseif ( $options['cookie_trigger_modal_bg_shape'] == 'rounded' ) :
		$cookie_trigger_modal_bg_shape = 'ct-ultimate-gdpr-trigger-modal-rounded';
    elseif ( $options['cookie_trigger_modal_bg_shape'] == 'squared' ) :
		$cookie_trigger_modal_bg_shape = 'ct-ultimate-gdpr-trigger-modal-squared';
	endif;
else :
	$cookie_trigger_modal_bg_shape = '';
endif;

/*Modal Skin*/
if ( $options['cookie_modal_skin'] == 'style-one' ) :
	$cookie_modal_skin = esc_attr( 'ct-ultimate-gdpr-cookie-skin-one' );
	$block_icon = ct_ultimate_gdpr_url() . '/assets/css/images/block-all.svg';
	$ess_icon = ct_ultimate_gdpr_url() . '/assets/css/images/essential.svg';
	$func_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin1-func.svg';
	$ana_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin1-ana.svg';
	$adv_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin1-adv.svg';
elseif ( $options['cookie_modal_skin'] == 'style-two' ) :
	$cookie_modal_skin = esc_attr( 'ct-ultimate-gdpr-cookie-skin-two' );
	$block_icon = ct_ultimate_gdpr_url() . '/assets/css/images/block-all.svg';
	$ess_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-ess.svg';
	$func_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-func.svg';
	$ana_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-ana.svg';
	$adv_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-adv.svg';
elseif ( $options['cookie_modal_skin'] == 'default' ) :
	$cookie_modal_skin = "";
	$block_icon = ct_ultimate_gdpr_url() . '/assets/css/images/block-all.svg';
	$ess_icon = ct_ultimate_gdpr_url() . '/assets/css/images/essential.svg';
	$func_icon =  ct_ultimate_gdpr_url() . '/assets/css/images/functionality.svg';
	$ana_icon = ct_ultimate_gdpr_url() . '/assets/css/images/statistics.svg';
	$adv_icon = ct_ultimate_gdpr_url() . '/assets/css/images/targeting.svg';
else :
	$block_icon = $ess_icon = $func_icon = $ana_icon = $adv_icon = '';
	$cookie_modal_skin = $options['cookie_modal_skin'];
endif;

$cookie_modal_type = '';
if ( $cookie_modal_skin == 'compact-green' ) {
	$cookie_modal_type = ' ' . 'ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green';
} elseif ( $cookie_modal_skin == 'compact-light-blue' ) {
	$cookie_modal_type = ' ' . 'ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue';
} elseif ( $cookie_modal_skin == 'compact-dark-blue' ) {
	$cookie_modal_type = ' ' . 'ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue';
}

// SHORTCODE
if ( ! empty( $options['content'] ) ) : ?>
    <a href="#" class="ct-ultimate-triggler-modal-sc"><?php echo esc_html( $options['content'] ); ?></a>
<?php
endif;

/* END MODAL VARIABLES */

/** @var array $options */

/*
*Check user agent
*Return false
 */




if ( empty( $options['cookie_modal_always_visible'] ) ) :

    $distance = isset( $options['cookie_position_distance'] ) ? $options['cookie_position_distance'] : 0;
	$skin_location_class = $box_style_class = $box_shape_class = $btn_shape_class = $btn_size_class = $top_panel_attr =
	$bottom_panel_attr = $card_attr = $needle = $replacement = $haystack = $popup_panel_open_tag =
	$popup_btn_wrap_open_tag = $close_tag = $cookie_box_bg = $box_css = $light_img = $content_style = $skin_name =
	$arrow = $btn_icon = $check = $left_cog = $right_cog = $close_tags = $accept_border_color = $accept_bg_color = $accept_color =
	$accept_btn_content = $is_10_set = $adv_set_border_color = $adv_set_bg_color = $adv_set_color =
	$adv_set_btn_content = $_10_set = $btn_wrapper = $btn_wrapper_end = $attachment_url = $attachment_image = '';
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
		$close_tags = $ct_gdpr_is_panel_array['close_tag'];
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
		$btn_wrapper_end = $skin_set == '1' ? '</div>' : '' ;

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
	<?php
	if ( $options['cookie_position'] == "full_layout_panel_" ) {
		?>
        <div class="ct-ultimate-gdpr-cookie-fullPanel-overlay"></div>
		<?php
	}
	?>
<div
        id="ct-ultimate-gdpr-cookie-popup"
        class="<?php echo ct_gdpr_set_class_attr( $class_array ); ?>"
        style="background-color: <?php echo esc_attr( $options['cookie_background_color'] ); ?>;
                color: <?php echo esc_attr( $options['cookie_text_color'] ); ?>;
		<?php echo ct_gdpr_set_class_attr( $attr_array ); ?>
                ">

	<?php
	if( isset( $options['cookie_gear_close_box'] ) && $options['cookie_gear_close_box'] == 'on' ){
		?>
        <a href = "javascript:void(0);" id = "ct-ultimate-cookie-close-modal" title="Decline Cookie"><i class="fa fa-times"></i></a>
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

    <div
    id="ct-ultimate-gdpr-cookie-change-settings"
    style="border-color:<?php echo $adv_set_border_color;
	?>;background-color:<?php echo $adv_set_bg_color;
	?>;color:<?php echo $adv_set_color;
	?>"><?php
	echo $adv_set_btn_content
	?></div><?php // #ct-ultimate-gdpr-cookie-change-settings ?>

	<?php echo $close_tags // .ct-ultimate-gdpr-cookie-buttons.ct-clearfix ?><!-- .ct-ultimate-gdpr-cookie-buttons.ct-clearfix -->

    <div class="ct-clearfix"></div>
	<?php echo $btn_wrapper_end."<!-- End of ct-ultimate-gdpr-cookie-popup-btn-wrapper -->"; ?>
	<?php echo $close_tags // .ct-container.ct-ultimate-gdpr-cookie-popup-[top/bottom]Panel ?><!-- .ct-container.ct-ultimate-gdpr-cookie-popup-[top/bottom]Panel -->

    </div><?php // #ct-ultimate-gdpr-cookie-popup ?><!-- #ct-ultimate-gdpr-cookie-popup -->

    <div id="ct-ultimate-gdpr-cookie-open"
         class="<?php echo esc_attr($cookie_trigger_modal_bg_shape); ?>"
         style="background-color: <?php echo(isset($options['cookie_trigger_modal_bg']) ? esc_attr($options['cookie_trigger_modal_bg']) : ''); ?>;color: <?php echo esc_attr($options['cookie_gear_icon_color']); ?>;
	     <?php
	     if (isset($options['cookie_gear_icon_position'])) :
		     if ($options['cookie_gear_icon_position'] == 'top_center_') :
			     echo esc_attr("top: " . (int)$distance . "px; left: 50%; right: auto; bottom: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'top_left_') :
			     echo esc_attr("top: " . (int)$distance . "px; left:" . (int)$distance . "px;bottom: auto; right: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'top_right_') :
			     echo esc_attr("top: " . (int)$distance . "px; right:" . (int)$distance . "px; bottom: auto; left: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'bottom_center_') :
			     echo esc_attr("bottom: " . (int)$distance . "px; left: 50%; right: auto; top: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'bottom_left_') :
			     echo esc_attr("bottom: " . (int)$distance . "px; left: " . (int)$distance . "px;right: auto; top: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'bottom_right_') :
			     echo esc_attr("bottom: " . (int)$distance . "px; right: " . (int)$distance . "px; top: auto; left: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'center_left_') :
			     echo esc_attr("top: 50%; left: " . (int)$distance . "px; right: auto; bottom: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'center_right_') :
			     echo esc_attr("top: 50%; right: " . (int)$distance . "px; bottom: auto; left: auto;");
		     else :
			     echo str_replace('_', ": " . (int)$distance . "px; ", esc_attr($options['cookie_gear_icon_position']));
		     endif;
	     endif;
	     ?>">
		<?php
		if (isset($options['cookie_settings_trigger'])) :
			if ($options['cookie_settings_trigger'] == 'icon_only_' || $options['cookie_settings_trigger'] == 'text_icon_') : ?>
                <span class="<?php echo esc_attr($options['cookie_trigger_modal_icon']); ?>" aria-hidden="true"></span>
                <span class="sr-only"><?php esc_html_e('Cookie Box Settings', 'ct-ultimate-gdpr'); ?></span>
			<?php endif;
			if ($options['cookie_settings_trigger'] == 'text_only_' || $options['cookie_settings_trigger'] == 'text_icon_') :
				if (!empty($options['cookie_trigger_modal_text'])) :
					echo esc_html($options['cookie_trigger_modal_text']);
				else :
					echo esc_html__('Trigger', 'ct-ultimate-gdpr');
				endif;
			endif;
		else : ?>
            <span class="<?php echo esc_attr($options['cookie_trigger_modal_icon']); ?>" aria-hidden="true"></span>
            <span class="sr-only"><?php esc_html_e('Cookie Box Settings', 'ct-ultimate-gdpr'); ?></span>
		<?php endif; ?>
    </div>
    <div id="ct-ultimate-gdpr-cookie-open"
         class="<?php echo esc_attr($cookie_trigger_modal_bg_shape); ?>"
         style="background-color: <?php echo(isset($options['cookie_trigger_modal_bg']) ? esc_attr($options['cookie_trigger_modal_bg']) : ''); ?>;color: <?php echo esc_attr($options['cookie_gear_icon_color']); ?>;
	     <?php
	     if (isset($options['cookie_gear_icon_position'])) :
		     if ($options['cookie_gear_icon_position'] == 'top_center_') :
			     echo esc_attr("top: " . (int)$distance . "px; left: 50%; right: auto; bottom: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'top_left_') :
			     echo esc_attr("top: " . (int)$distance . "px; left:" . (int)$distance . "px;bottom: auto; right: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'top_right_') :
			     echo esc_attr("top: " . (int)$distance . "px; right:" . (int)$distance . "px; bottom: auto; left: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'bottom_center_') :
			     echo esc_attr("bottom: " . (int)$distance . "px; left: 50%; right: auto; top: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'bottom_left_') :
			     echo esc_attr("bottom: " . (int)$distance . "px; left: " . (int)$distance . "px;right: auto; top: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'bottom_right_') :
			     echo esc_attr("bottom: " . (int)$distance . "px; right: " . (int)$distance . "px; top: auto; left: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'center_left_') :
			     echo esc_attr("top: 50%; left: " . (int)$distance . "px; right: auto; bottom: auto;");
             elseif ($options['cookie_gear_icon_position'] == 'center_right_') :
			     echo esc_attr("top: 50%; right: " . (int)$distance . "px; bottom: auto; left: auto;");
		     else :
			     echo str_replace('_', ": " . (int)$distance . "px; ", esc_attr($options['cookie_gear_icon_position']));
		     endif;
	     endif;
	     ?>">
		<?php
		if (isset($options['cookie_settings_trigger'])) :
			if ($options['cookie_settings_trigger'] == 'icon_only_' || $options['cookie_settings_trigger'] == 'text_icon_') : ?>
                <span class="<?php echo esc_attr($options['cookie_trigger_modal_icon']); ?>" aria-hidden="true"></span>
                <span class="sr-only"><?php esc_html_e('Cookie Box Settings', 'ct-ultimate-gdpr'); ?></span>
			<?php endif;
			if ($options['cookie_settings_trigger'] == 'text_only_' || $options['cookie_settings_trigger'] == 'text_icon_') :
				if (!empty($options['cookie_trigger_modal_text'])) :
					echo esc_html($options['cookie_trigger_modal_text']);
				else :
					echo esc_html__('Trigger', 'ct-ultimate-gdpr');
				endif;
			endif;
		else : ?>
            <span class="<?php echo esc_attr($options['cookie_trigger_modal_icon']); ?>" aria-hidden="true"></span>
            <span class="sr-only"><?php esc_html_e('Cookie Box Settings', 'ct-ultimate-gdpr'); ?></span>
		<?php endif; ?>
    </div>

<?php endif; ?>
<div id="ct-ultimate-gdpr-cookie-modal" class="<?php echo esc_attr($group_class) . esc_attr( $cookie_modal_type ); ?>">

    <!-- Modal content -->
    <div class="ct-ultimate-gdpr-cookie-modal-content <?php echo esc_attr($cookie_modal_skin); ?>">
		<?php
		if (
			! $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue'
			|| ! $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue'
			|| ! $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green'
		) :
			?>
            <div id="ct-ultimate-gdpr-cookie-modal-close"></div>
		<?php endif; ?>
        <div id="ct-ultimate-gdpr-cookie-modal-body"
             class="<?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'ct-ultimate-gdpr-slider-block' : 'ct-ultimate-gdpr-slider-not-block'; ?>">

			<?php
			if (
				$cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green'
				|| $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue'
				|| $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue'
			) :
				?>
                <div id="ct-ultimate-gdpr-cookie-modal-compact-close"></div>
			<?php endif; ?>

			<?php
			if (!empty($options['cookie_group_popup_header_content'])) : ?>
                <div style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>"> <?php echo wp_kses_post($options['cookie_group_popup_header_content']); ?> </div>

			<?php
			else:
				ct_ultimate_gdpr_locate_template('cookie-group-popup-header-content', true, $options);
			endif; ?>
            <form action="#" id="ct-ultimate-gdpr-cookie-modal-slider-form">
                <div class="ct-ultimate-gdpr-slider"></div>
                <ul class="ct-ultimate-gdpr-cookie-modal-slider">
					<?php $group_counter = 0; ?>
					<?php if (empty($options['cookie_group_popup_hide_level_1'])) : ?>
						<?php ++$group_counter; ?>
                        <li id="ct-ultimate-gdpr-cookie-modal-slider-item-block"
                            class="ct-ultimate-gdpr-cookie-modal-slider-item <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'ct-ultimate-gdpr-cookie-modal-slider-item--active' : ''; ?>"
                            data-count="<?php echo esc_attr($group_counter); ?>">
                            <div>
                                <img class="ct-svg"
                                     src="<?php echo esc_url( $block_icon )?>"
                                     alt="Block all">
                            </div>
                            <input type="radio" id="cookie0"
                                   name="radio-group" <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'checked' : ''; ?>
                                   class="ct-ultimate-gdpr-cookie-modal-slider-radio"
                                   value="<?php echo CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL; ?>"
                                   data-count="<?php echo esc_attr($group_counter); ?>">
                            <label for="cookie0"
                                   style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;">
								<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL)); ?>
                            </label>
                        </li>

					<?php endif; ?>

					<?php if (empty($options['cookie_group_popup_hide_level_2'])) : ?>
						<?php ++$group_counter; ?>
                        <li class="ct-ultimate-gdpr-cookie-modal-slider-item <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'ct-ultimate-gdpr-cookie-modal-slider-item--active' : ''; ?>">
                            <div>
                                <img class="ct-svg"
                                     src="<?php echo esc_url($ess_icon) ?>"
                                     alt="Essential">
                            </div>
                            <input data-count="<?php echo esc_attr($group_counter); ?>" type="radio" id="cookie1"
                                   name="radio-group" <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'checked' : ''; ?>
                                   class="ct-ultimate-gdpr-cookie-modal-slider-radio"
                                   value="<?php echo CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY; ?>">
                            <label for="cookie1"
                                   style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;">
								<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY)); ?>
                            </label>
                        </li>

					<?php endif; ?>

					<?php if (empty($options['cookie_group_popup_hide_level_3'])) : ?>
						<?php ++$group_counter; ?>
                        <li class="ct-ultimate-gdpr-cookie-modal-slider-item <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'ct-ultimate-gdpr-cookie-modal-slider-item--active' : ''; ?>"
                            data-count="<?php echo esc_attr($group_counter); ?>">
                            <div>
                                <img class="ct-svg"
                                     src="<?php echo esc_url( $func_icon)?>"
                                     alt="Functionality">
                            </div>
                            <input data-count="<?php echo esc_attr($group_counter); ?>" type="radio" id="cookie2"
                                   name="radio-group" <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'checked' : ''; ?>
                                   class="ct-ultimate-gdpr-cookie-modal-slider-radio"
                                   value="<?php echo CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE; ?>">
                            <label for="cookie2"
                                   style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;">
								<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE)); ?>
                            </label>
                        </li>

					<?php endif; ?>

					<?php if (empty($options['cookie_group_popup_hide_level_4'])) : ?>
						<?php ++$group_counter; ?>
                        <li class="ct-ultimate-gdpr-cookie-modal-slider-item <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'ct-ultimate-gdpr-cookie-modal-slider-item--active' : ''; ?>"
                            data-count="<?php echo esc_attr($group_counter); ?>">
                            <div>
                                <img class="ct-svg"
                                     src="<?php echo esc_url( $ana_icon )?>"
                                     alt="Analytics">
                            </div>
                            <input data-count="<?php echo esc_attr($group_counter); ?>" type="radio" id="cookie3"
                                   name="radio-group" <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'checked' : ''; ?>
                                   class="ct-ultimate-gdpr-cookie-modal-slider-radio"
                                   value="<?php echo CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS; ?>">
                            <label for="cookie3"
                                   style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;">
								<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS)); ?>
                            </label>
                        </li>

					<?php endif; ?>

					<?php if (empty($options['cookie_group_popup_hide_level_5'])) : ?>
						<?php ++$group_counter; ?>
                        <li class="ct-ultimate-gdpr-cookie-modal-slider-item <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'ct-ultimate-gdpr-cookie-modal-slider-item--active' : ''; ?>">
                            <div>
                                <img class="ct-svg"
                                     src="<?php echo esc_url( $adv_icon ) ?>"
                                     alt="Advertising">
                            </div>
                            <input data-count="<?php echo esc_attr($group_counter); ?>" type="radio" id="cookie4"
                                   name="radio-group" <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'checked' : ''; ?>
                                   class="ct-ultimate-gdpr-cookie-modal-slider-radio"
                                   value="<?php echo CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING; ?>">
                            <label for="cookie4"
                                   style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;">
								<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING)); ?>
                            </label>
                        </li>
					<?php endif; ?>
					<?php if ( strlen( $cookie_modal_type ) > 0 ) : ?>
                        <li class="hide-btn-wrapper">
                            <div class="hide-btn">
								<?php echo esc_html__( 'Hide details', 'ct-ultimate-gdpr' ); ?> <span class="fa fa-chevron-up"></span>
                            </div>
                        </li>
					<?php endif; ?>

					<?php
					if (
						$cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue'
						|| $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue'
						|| $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green'
					) :
						?>
                        <li class="ct-ultimate-gdpr-cookie-modal-btn save compact">
                            <a href="#">
								<?php
								echo esc_html(
									ct_ultimate_gdpr_get_value(
										'cookie_group_popup_label_save',
										$options,
										esc_html__('OK', 'ct-ultimate-gdpr'),
										false
									)
								);
								?>
                            </a>
                        </li>
					<?php endif; ?>

                </ul>

            </form>
			<?php if ( $group_counter ) : ?>
            <div class="ct-ultimate-gdpr-cookie-modal-slider-wrap">
				<?php if ( strlen( $cookie_modal_type ) > 0 ) : ?>
                    <ul class="cookie-modal-tab-wrapper">
						<?php if (empty($options['cookie_group_popup_hide_level_1'])) : ?>
                            <li class="tab cookie0" style="height:calc(135px/<?php echo esc_attr( $group_counter ); ?>);">
                        <span style="color:<?php echo esc_attr($options['cookie_modal_header_color']); ?>">
                            <?php echo esc_html( CT_Ultimate_GDPR_Model_Group::get_label( CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL ) ); ?>
                        </span>
                            </li>
						<?php endif; ?>
						<?php if (empty($options['cookie_group_popup_hide_level_2'])) : ?>
                            <li class="tab cookie1" style="height:calc(135px/<?php echo esc_attr( $group_counter ); ?>);">
                        <span style="color:<?php echo esc_attr($options['cookie_modal_header_color']); ?>">
                            <?php echo esc_html( CT_Ultimate_GDPR_Model_Group::get_label( CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY ) ); ?>
                        </span>
                            </li>
						<?php endif; ?>
						<?php if (empty($options['cookie_group_popup_hide_level_3'])) : ?>
                            <li class="tab cookie2" style="height:calc(135px/<?php echo esc_attr( $group_counter ); ?>);">
                        <span style="color:<?php echo esc_attr($options['cookie_modal_header_color']); ?>">
                            <?php echo esc_html( CT_Ultimate_GDPR_Model_Group::get_label( CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE ) ); ?>
                        </span>
                            </li>
						<?php endif; ?>
						<?php if (empty($options['cookie_group_popup_hide_level_4'])) : ?>
                            <li class="tab cookie3" style="height:calc(135px/<?php echo esc_attr( $group_counter ); ?>);">
                        <span style="color:<?php echo esc_attr($options['cookie_modal_header_color']); ?>">
                            <?php echo esc_html( CT_Ultimate_GDPR_Model_Group::get_label( CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ) ); ?>
                        </span>
                            </li>
						<?php endif; ?>
						<?php if (empty($options['cookie_group_popup_hide_level_5'])) : ?>
                            <li class="tab cookie4" style="height:calc(135px/<?php echo esc_attr( $group_counter ); ?>);">
                        <span style="color:<?php echo esc_attr($options['cookie_modal_header_color']); ?>">
                            <?php echo esc_html( CT_Ultimate_GDPR_Model_Group::get_label( CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING ) ); ?>
                        </span>
                            </li>
						<?php endif; ?>
                    </ul>
				<?php endif; ?>

                <div class="ct-ultimate-gdpr-cookie-modal-slider-inner-wrap">
                    <div class="ct-ultimate-gdpr-cookie-modal-slider-info cookie1">
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_will", $options, __('This website will:', 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_2", $options, "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected;");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_wont", $options, __("This website wont't:", 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-not-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">


								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_nonavailable_group_2", $options, "Remember your login details; Functionality: Remember social media settings; Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-clearfix"></div>
                    </div>

                    <div class="ct-ultimate-gdpr-cookie-modal-slider-info cookie2">
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_will", $options, __('This website will:', 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_3", $options, "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settings; Functionality: Remember selected region and country;");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_wont", $options, __('This website will:', 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-not-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_nonavailable_group_3", $options, "Remember your login details; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Tailor information and advertising to your interests based on e.g. the content you have visited before. (Currently we do not use targeting or targeting cookies.; Advertising: Gather personally identifiable information such as name and location;");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-clearfix"></div>
                    </div>

                    <div class="ct-ultimate-gdpr-cookie-modal-slider-info cookie3">
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_will", $options, __('This website will:', 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_4", $options, "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settingsl Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions;");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_wont", $options, __("This website wont't:", 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-not-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_nonavailable_group_4", $options, "Remember your login details; Advertising: Use information for tailored advertising with third parties; Advertising: Allow you to connect to social sites; Advertising: Identify device you are using; Advertising: Gather personally identifiable information such as name and location");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-clearfix"></div>
                    </div>
                    <div class="ct-ultimate-gdpr-cookie-modal-slider-info cookie4">
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_will", $options, __('This website will:', 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_5", $options, "Essential: Remember your cookie permission setting; Essential: Allow session cookies; Essential: Gather information you input into a contact forms, newsletter and other forms across all pages; Essential: Keep track of what you input in a shopping cart; Essential: Authenticate that you are logged into your user account; Essential: Remember language version you selected; Functionality: Remember social media settingsl Functionality: Remember selected region and country; Analytics: Keep track of your visited pages and interaction taken; Analytics: Keep track about your location and region based on your IP number; Analytics: Keep track of the time spent on each page; Analytics: Increase the data quality of the statistics functions; Advertising: Use information for tailored advertising with third parties; Advertising: Allow you to connect to social sitesl Advertising: Identify device you are using; Advertising: Gather personally identifiable information such as name and location");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-ultimate-gdpr-cookie-modal-slider-desc">
                            <h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_wont", $options, __("This website wont't:", 'ct-ultimate-gdpr'))); ?></h4>
                            <ul class="ct-ultimate-gdpr-cookie-modal-slider-not-able"
                                style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<?php

								$option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_nonavailable_group_5", $options, "Remember your login details");
								$features = array_filter(array_map('trim', explode(';', $option_string)));

								foreach ($features as $feature) :

									echo "<li>" . esc_html($feature) . "</li>";

								endforeach;

								?>

                            </ul>
                        </div>
                        <div class="ct-clearfix"></div>
                    </div>
                </div>
				<?php endif; ?>
            </div>
            <div class="ct-ultimate-gdpr-cookie-modal-btn save">
                <a href="#"><?php echo esc_html(ct_ultimate_gdpr_get_value('cookie_group_popup_label_save', $options, esc_html__('Save & Close', 'ct-ultimate-gdpr'), false)); ?></a>
            </div>
        </div>
    </div>
</div>