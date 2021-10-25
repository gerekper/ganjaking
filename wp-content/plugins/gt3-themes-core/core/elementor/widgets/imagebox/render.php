<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageBox $widget */

$settings = $widget->get_settings_for_display();

$el_class = '';

if (!empty($settings['_background_hover_image']['url'])) {
    $background_hover_image_url = $settings['_background_hover_image']['url'];
    $background_hover_position = $settings['_background_hover_position'];
    $background_hover_attachment = $settings['_background_hover_attachment'];
    $background_hover_repeat = $settings['_background_hover_repeat'];
    $background_hover_size = $settings['_background_hover_size'];
    $background_hover_transition = $settings['background_hover_transition'];

    $background_hover_image_css = '';
    if (!empty($background_hover_image_url)) {
        $background_hover_image_css .= 'background-image: url('.esc_url($background_hover_image_url).');';
    }
    if (!empty($background_hover_position)) {
        $background_hover_image_css .= 'background-position: '.esc_attr($background_hover_position).';';
    }
    if (!empty($background_hover_repeat)) {
        $background_hover_image_css .= 'background-repeat: '.esc_attr($background_hover_repeat).';';
    }
    if (!empty($background_hover_size)) {
        $background_hover_image_css .= 'background-size: '.esc_attr($background_hover_size).';';
    }
    if (!empty($background_hover_attachment)) {
        $background_hover_image_css .= 'background-attachment: '.esc_attr($background_hover_attachment).';';
    }
    if (!empty($background_hover_image_css)) {
        $background_hover_image_css = ' style="'.$background_hover_image_css.'"';
    }

    $widget->add_render_attribute('_wrapper', 'class', array(
        ' has_hover_background_image',
    ));
}

if (!empty($settings['_background_image']['url'])) {
    $background_image_url = $settings['_background_image']['url'];
    $background_position = $settings['_background_position'];
    $background_attachment = $settings['_background_attachment'];
    $background_repeat = $settings['_background_repeat'];
    $background_size = $settings['_background_size'];

    $background_image_css = '';
    if (!empty($background_image_url)) {
        $background_image_css .= 'background-image: url('.esc_url($background_image_url).');';
    }
    if (!empty($background_position)) {
        $background_image_css .= 'background-position: '.esc_attr($background_position).';';
    }
    if (!empty($background_repeat)) {
        $background_image_css .= 'background-repeat: '.esc_attr($background_repeat).';';
    }
    if (!empty($background_size)) {
        $background_image_css .= 'background-size: '.esc_attr($background_size).';';
    }
    if (!empty($background_attachment)) {
        $background_image_css .= 'background-attachment: '.esc_attr($background_attachment).';';
    }
    if (!empty($background_image_css)) {
        $background_image_css = ' style="'.$background_image_css.'"';
    }

    $widget->add_render_attribute('_wrapper', 'class', array(
        ' has_background_image',
    ));
}

$has_bg_image = '';

if (!empty($background_image_url) || !empty($background_hover_image_url)) {
    $has_bg_image .= '<div class="gt3_background_image_cover">';
        if (!empty($background_image_url)) {
            $has_bg_image .='<div class="gt3_background_image_cover__front"'.$background_image_css.'></div>';
        }
        if (!empty($background_hover_image_url)) {
            $has_bg_image .='<div class="gt3_background_image_cover__back"'.$background_hover_image_css.'></div>';
        }

    $has_bg_image .= '</div>';
}

if ($settings['type'] === 'image') {

    $has_content = !empty($settings['title_text']) || !empty($settings['description_text']);

    $html = '';
    $html .= $has_bg_image;
    $html .= '<div class="gt3-core-imagebox-wrapper elementor-image_icon-position-'.esc_attr($settings['position']).esc_attr($el_class).'">';



    if (!empty($settings['link']['url'])) {
        $widget->add_render_attribute('link', 'href', $settings['link']['url']);

        if ($settings['link']['is_external']) {
            $widget->add_render_attribute('link', 'target', '_blank');
        }

        if (!empty($settings['link']['nofollow'])) {
            $widget->add_render_attribute('link', 'rel', 'nofollow');
        }
    }

    if (!empty($settings['image']['url'])) {
        $widget->add_render_attribute('image', 'src', $settings['image']['url']);
        $widget->add_render_attribute('image', 'alt', Control_Media::get_image_alt($settings['image']));
        $widget->add_render_attribute('image', 'title', Control_Media::get_image_title($settings['image']));

        if (strpos($settings['image']['url'],'.svg') !== false) {
		    $image_html = file_get_contents(get_attached_file($settings['image']['id']));
	    } else {
		    $image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'image');
	    }

        $image_hover_class = "";

        if (!empty($settings['image_hover']['url'])) {
            $widget->add_render_attribute('image_hover', 'src', $settings['image']['url']);
            $widget->add_render_attribute('image_hover', 'alt', Control_Media::get_image_alt($settings['image_hover']));
            $widget->add_render_attribute('image_hover', 'title', Control_Media::get_image_title($settings['image_hover']));
            $image_hover_class = " gt3-core-imagebox-img_hover";

	        if (strpos($settings['image_hover']['url'],'.svg') !== false) {
		        $image_html .= file_get_contents(file_get_contents(get_attached_file($settings['image_hover']['id'])));
	        } else {
		        $image_html .= Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'image_hover');
	        }
        }

        if ($settings['hover_animation']) {
            $image_hover_class .= ' elementor-animation-' . $settings['hover_animation'];
        }

        if (!empty($settings['link']['url'])) {
            $image_html = '<a ' . $widget->get_render_attribute_string('link') . '>' . $image_html . '</a>';
        }

	    $image_html = '<figure class="gt3-core-imagebox-img'.esc_attr($image_hover_class).'">' . $image_html . '</figure>';

	    if ( $settings['position'] === 'default' || empty($settings['title_text']) ){
		    $html .= $image_html;
	    }

    }

    if ($has_content) {

        $html .= '<div class="gt3-core-imagebox-content">';

        if (!empty($settings['title_text'])) {
            $widget->add_render_attribute('title_text', 'class', 'gt3-core-imagebox-title');

            $widget->add_inline_editing_attributes('title_text', 'none');

            $title_html = $settings['title_text'];

            if (!empty($settings['link']['url'])) {
                $title_html = '<a ' . $widget->get_render_attribute_string('link') . '>' . $title_html . '</a>';
            }

            $html .= '<div class="gt3-core-imagebox-title">';

            if ($settings['position'] !== 'default'){
	            $html .= $image_html;
            }

            $html .= sprintf('<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $widget->get_render_attribute_string('title_text'), $title_html);

	        $html .= '</div>';
        }

        if (!empty($settings['description_text'])) {
            $widget->add_render_attribute('description_text', 'class', 'gt3-core-imagebox-description');

            $widget->add_inline_editing_attributes('description_text');

            $html .= sprintf('<p %1$s>%2$s</p>', $widget->get_render_attribute_string('description_text'), $settings['description_text']);
        }

        $html .= '</div>';
    }

    $html .= '</div>';

    if (!empty($settings['link']['url'])) {
        $html .= '<a ' . $widget->get_render_attribute_string('link') . '></a>';
    }

    echo ''.$html;

}else{

    $widget->add_render_attribute( 'icon', 'class', [ 'elementor-icon', 'elementor-animation-' . $settings['hover_animation'] ] );

    $icon_tag = 'span';
    $has_icon = ! empty( $settings['icon'] );

    if ( ! empty( $settings['link']['url'] ) ) {
        $widget->add_render_attribute( 'link', 'href', $settings['link']['url'] );
        $icon_tag = 'a';

        if ( $settings['link']['is_external'] ) {
            $widget->add_render_attribute( 'link', 'target', '_blank' );
        }

        if ( $settings['link']['nofollow'] ) {
            $widget->add_render_attribute( 'link', 'rel', 'nofollow' );
        }
    }

    if ( $has_icon ) {
        $widget->add_render_attribute( 'i', 'class', $settings['icon'] );
        $widget->add_render_attribute( 'i', 'aria-hidden', 'true' );
    }

    $icon_attributes = $widget->get_render_attribute_string( 'icon' );
    $link_attributes = $widget->get_render_attribute_string( 'link' );

    $widget->add_render_attribute( 'description_text', 'class', 'gt3-core-imagebox-description' );

    $widget->add_inline_editing_attributes( 'title_text', 'none' );
    $widget->add_inline_editing_attributes( 'description_text' );

	$icon_html = '';
    ?>
    <?php echo $has_bg_image; ?>
    <div class="gt3-core-imagebox-wrapper<?php echo esc_attr($el_class); ?>">
	<?php if ( $has_icon ) {
		$icon_html .= '<'.implode( ' ', [ $icon_tag, $icon_attributes, $link_attributes ] ).'>';
        $icon_html .= '<i '. $widget->get_render_attribute_string( 'i' ).'></i>';
        $icon_html .= '</'.$icon_tag.'>';

        if ($settings['position'] === 'default'){
	        echo '<div class="gt3-core-imagebox-icon">';
            echo ''.$icon_html;
	        echo '</div>';
        }
	} ?>
    <div class="gt3-core-imagebox-content">

    <<?php echo ''.$settings['title_size']; ?> class="gt3-core-imagebox-title">
    <?php echo ($settings['position'] !== 'default' ? $icon_html : ''); ?>
    <<?php echo implode( ' ', [
		$icon_tag,
		$link_attributes
	] ); ?><?php echo ''.$widget->get_render_attribute_string( 'title_text' ); ?>
    ><?php echo ''.$settings['title_text']; ?></<?php echo ''.$icon_tag; ?>>
    </<?php echo ''.$settings['title_size']; ?>>
    <p <?php echo ''.$widget->get_render_attribute_string( 'description_text' ); ?>><?php echo ''.$settings['description_text']; ?></p>
    </div>
    </div>
    <?php
    if (!empty($settings['link']['url'])) {
        echo '<a ' . $widget->get_render_attribute_string('link') . '></a>';
    }
}


