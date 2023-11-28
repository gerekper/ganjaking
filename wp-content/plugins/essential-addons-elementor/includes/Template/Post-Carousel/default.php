<?php
/**
 * Template Name: Default
 *
 */
use Essential_Addons_Elementor\Pro\Classes\Helper;
use \Elementor\Group_Control_Image_Size;

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if(isset($settings['title_tag'])){
	$settings['title_tag'] = Helper::eael_pro_validate_html_tag($settings['title_tag']);
}

if ( $settings['eael_show_fallback_img'] == 'yes' && ! empty( $settings['eael_post_carousel_fallback_img']['url'] ) ) {
	$fallback_image_id = $settings['eael_post_carousel_fallback_img']['id'];
	$eael_fallback_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $fallback_image_id, 'image', $settings );
}

$image_url = ! empty( $eael_fallback_thumb_url ) ? $eael_fallback_thumb_url : '';
$image_url = has_post_thumbnail() ? wp_get_attachment_image_url( get_post_thumbnail_id(), $settings['image_size'] ) : $image_url;

$enable_ratio = $settings['enable_post_carousel_image_ratio'] == 'yes' ? 'eael-image-ratio':'';

echo '<div class="swiper-slide">';
if ( $settings['eael_post_carousel_preset_style'] === 'two' ) {
    echo '<article class="eael-grid-post eael-post-grid-column">
    <div class="eael-grid-post-holder">
        <div class="eael-grid-post-holder-inner">';

    if (  ( $settings['eael_show_image'] == '0' || $settings['eael_show_image'] == 'yes' ) && ( has_post_thumbnail() || ! empty( $eael_fallback_thumb_url ) ) ) {
        echo '<div class="eael-entry-media eael-entry-medianone">';

	    if ($settings['eael_show_post_terms'] === 'yes') {
		    echo Helper::get_terms_as_list($settings['eael_post_terms'], $settings['eael_post_terms_max_length']);
	    }

        if ( isset( $settings['post_block_hover_animation'] ) && 'none' !== $settings['post_block_hover_animation'] ) {
            echo '<div class="eael-entry-overlay ' . ( $settings['post_block_hover_animation'] ) . '">';

            if( $settings['eael_post_carousel_item_style'] === 'eael-overlay' ) {
                // Show content
                echo "<div class='eael-entry-wrapper-fade-in'>";
                $this->print_entry_content_style_2( $settings );
                echo "</div>";
            } else {
                // Show icon
                if ( isset( $settings['__fa4_migrated']['eael_post_grid_bg_hover_icon_new'] ) || empty( $settings['eael_post_grid_bg_hover_icon'] ) ) {
                    echo '<i class="' . $settings['eael_post_grid_bg_hover_icon_new'] . '" aria-hidden="true"></i>';
                } else {
                    echo '<i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i>';
                }
            }

            echo '<a href="' . get_the_permalink() . '"' . $settings['image_link_nofollow'] . '' . $settings['image_link_target_blank'] . '></a></div>';
        }

        echo '<div class="eael-entry-thumbnail '.$enable_ratio.'">
                <img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) . '">
                <a href="' . get_the_permalink() . '"' . $settings['image_link_nofollow'] . '' . $settings['image_link_target_blank'] . '></a>
             </div>';
        echo '</div>';

    }
    
    if( $settings['eael_post_carousel_item_style'] !== 'eael-overlay' ) {
        $this->print_entry_content_style_2( $settings );
    }
    
    echo '</div></div></article>';
} else if ( $settings['eael_post_carousel_preset_style'] === 'three' ) {

    echo '<article class="eael-grid-post eael-post-grid-column">
    <div class="eael-grid-post-holder">
        <div class="eael-grid-post-holder-inner">';

    if (  ( $settings['eael_show_image'] == '0' || $settings['eael_show_image'] == 'yes' ) && ( has_post_thumbnail() || ! empty( $eael_fallback_thumb_url ) ) ) {
        echo '<div class="eael-entry-media eael-entry-medianone">';

	    if ($settings['eael_show_post_terms'] === 'yes') {
		    echo Helper::get_terms_as_list($settings['eael_post_terms'], $settings['eael_post_terms_max_length']);
	    }

        if ( isset( $settings['post_block_hover_animation'] ) && 'none' !== $settings['post_block_hover_animation'] ) {
            echo '<div class="eael-entry-overlay ' . ( $settings['post_block_hover_animation'] ) . '">';

            if ( $settings['eael_post_carousel_item_style'] === 'eael-overlay' ) {
                // Show content
                echo "<div class='eael-entry-wrapper-fade-in'>";
                $this->print_entry_content_style_3( $settings );
                echo "</div>";
            } else {
                // Show icon
                if ( isset( $settings['__fa4_migrated']['eael_post_grid_bg_hover_icon_new'] ) || empty( $settings['eael_post_grid_bg_hover_icon'] ) ) {
                    echo '<i class="' . $settings['eael_post_grid_bg_hover_icon_new'] . '" aria-hidden="true"></i>';
                } else {
                    echo '<i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i>';
                }
            }

            echo '<a href="' . get_the_permalink() . '"' . $settings['image_link_nofollow'] . '' . $settings['image_link_target_blank'] . '></a></div>';
        }
        
        echo '<div class="eael-entry-thumbnail '.$enable_ratio.'">
                <img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) . '">
                <a href="' . get_the_permalink() . '"></a>
            </div>';
        echo '</div>';

        if ( $settings['eael_show_date'] === 'yes' && $settings['eael_post_carousel_item_style'] !== 'eael-overlay' ) {
            echo '<span class="eael-meta-posted-on"><time datetime="' . get_the_date() . '"><span>' . get_the_date( 'd' ) . '</span>' . get_the_date( 'F' ) . '</time></span>';
        }
    }

    if( $settings['eael_post_carousel_item_style'] !== 'eael-overlay' ) {
        $this->print_entry_content_style_3( $settings );
    }

    echo '</div></div></article>';
} else {
    echo '<article class="eael-grid-post eael-post-grid-column">
    <div class="eael-grid-post-holder">
        <div class="eael-grid-post-holder-inner">';


        if (  ( $settings['eael_show_image'] == '0' || $settings['eael_show_image'] == 'yes' ) && ( has_post_thumbnail() || ! empty( $eael_fallback_thumb_url ) ) ) {
            echo '<div class="eael-entry-media eael-entry-medianone">';

	        if ($settings['eael_show_post_terms'] === 'yes') {
		        echo Helper::get_terms_as_list($settings['eael_post_terms'], $settings['eael_post_terms_max_length']);
	        }

            if ( isset( $settings['post_block_hover_animation'] ) && 'none' !== $settings['post_block_hover_animation'] ) {
                echo '<div class="eael-entry-overlay ' . ( $settings['post_block_hover_animation'] ) . '">';

                if( $settings['eael_post_carousel_item_style'] === 'eael-overlay' ) {
                    // Show content
                    echo "<div class='eael-entry-wrapper-fade-in'>";
                    $this->print_entry_content_style_1( $settings );
                    echo "</div>";
                } else {
                    // Show icon
                    if ( isset( $settings['__fa4_migrated']['eael_post_grid_bg_hover_icon_new'] ) || empty( $settings['eael_post_grid_bg_hover_icon'] ) ) {
                        echo '<i class="' . $settings['eael_post_grid_bg_hover_icon_new'] . '" aria-hidden="true"></i>';
                    } else {
                        echo '<i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i>';
                    }
                }
                
                echo '<a href="' . get_the_permalink() . '"' . $settings['image_link_nofollow'] . '' . $settings['image_link_target_blank'] . '></a></div>';
            }
            
            echo '<div class="eael-entry-thumbnail '.$enable_ratio.'">
                    <img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) . '">
                    <a href="' . get_the_permalink() . '"></a>
                </div>';
            echo '</div>';
        }

    if( $settings['eael_post_carousel_item_style'] !== 'eael-overlay' ) {
        $this->print_entry_content_style_1( $settings );
    }
    
    echo '</div></div></article>';
}
echo '</div>';
