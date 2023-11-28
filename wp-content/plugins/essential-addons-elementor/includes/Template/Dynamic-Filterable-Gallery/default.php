<?php
/**
 * Template Name: Default
 *
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$helperClass                  = new Essential_Addons_Elementor\Pro\Classes\Helper();
$show_category_child_items    = ! empty( $settings['category_show_child_items'] ) && 'yes' === $settings['category_show_child_items'] ? 1 : 0;
$show_product_cat_child_items = ! empty( $settings['product_cat_show_child_items'] ) && 'yes' === $settings['product_cat_show_child_items'] ? 1 : 0;
$classes                      = $helperClass->get_dynamic_gallery_item_classes( $show_category_child_items, $show_product_cat_child_items );

$linkNofollow = $settings['link_nofollow'] ? 'rel="nofollow"' : '';
$imageNofollow = $settings['image_link_nofollow'] ? 'rel="nofollow"' : '';
$titleNofollow = $settings['title_link_nofollow'] ? 'rel="nofollow"' : '';
$readMoreNofollow = $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '';
$linkTarget = $settings['link_target_blank'] ? 'target="_blank"' : '';
$imageTarget = $settings['image_link_target_blank'] ? 'target="_blank"' : '';
$titleTarget = $settings['title_link_target_blank'] ? 'target="_blank"' : '';
$readMoreTarget = $settings['read_more_link_target_blank'] ? 'target="_blank"' : '';
$image_clickable = 'yes' === $settings['eael_dfg_full_image_clickable'] && $settings['eael_fg_grid_style'] == 'eael-cards';

if ($settings['eael_fg_grid_style'] == 'eael-hoverer') {
        echo '<div class="dynamic-gallery-item ' . esc_attr(urldecode(implode(' ', $classes))) . '">
            <div class="dynamic-gallery-item-inner" data-itemid=" ' . esc_attr( get_the_ID() ) . ' ">
                <div class="dynamic-gallery-thumbnail">';

                    if(has_post_thumbnail()) {
                        echo '<img src="' . wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">';
                    }else {
                        echo '<img src="'.\Elementor\Utils::get_placeholder_image_src().'">';
                    }

                    if ('eael-none' !== $settings['eael_fg_grid_hover_style']) {
                        echo  '<div class="caption ' . esc_attr($settings['eael_fg_grid_hover_style']) . ' ">';
                            if ('true' == $settings['eael_fg_show_popup']) {
                                if ('media' == $settings['eael_fg_show_popup_styles']) {
                                    echo '<a href="' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . '" class="popup-media eael-magnific-link"></a>';
                                } elseif ('buttons' == $settings['eael_fg_show_popup_styles']) {
                                    echo '<div class="item-content">';
                                        if($settings['eael_show_hover_title']) {
                                            echo '<h2 class="title"><a href="' . get_the_permalink() . '"'.$titleNofollow . '' . $titleTarget .'>' . get_the_title() . '</a></h2>';
                                        }
                                        if($settings['eael_show_hover_excerpt']) {
                                            echo '<p>' . wp_trim_words(strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()), $settings['eael_post_excerpt'], '<a class="eael_post_excerpt_read_more" href="' . get_the_permalink() . '"'.$readMoreNofollow . '' . $readMoreTarget .'> ' . $settings['eael_post_excerpt_read_more'] . '</a>') . '</p>';
                                        }
                                    echo '</div>';
                                    echo '<div class="buttons">';
                                        if (!empty($settings['eael_section_fg_zoom_icon'])) {
                                            if(has_post_thumbnail()) {
                                                echo  '<a href="' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . '" class="eael-magnific-link">';
                                            }else { // If there is no real image on this post/page then change of anchor tag with placeholder image src
                                                echo '<a href="'.\Elementor\Utils::get_placeholder_image_src().'" class="eael-magnific-link">';
                                            }
                                                if( isset($settings['eael_section_fg_zoom_icon']['url']) ) {
                                                    echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_zoom_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_zoom_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                                }else if ( ! empty( $settings['eael_section_fg_zoom_icon_new'] ) ) {
                                                    \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_zoom_icon_new'], ['aria-hidden' => 'true']);
                                                } else {
                                                    echo '<i class="sss ' . esc_attr($settings['eael_section_fg_zoom_icon']) . '"></i>';
                                                }
                                            echo '</a>';
                                        }

                                        if (!empty($settings['eael_section_fg_link_icon'])) {
                                            echo  '<a href="' . get_the_permalink() . '"'.$linkNofollow . '' . $linkTarget .'>';
                                                if( isset($settings['eael_section_fg_link_icon']['url'])) {
                                                    echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_link_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_link_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                                }else if ( ! empty( $settings['eael_section_fg_link_icon_new'] ) ) {
                                                    \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_link_icon_new'], ['aria-hidden' => 'true']);
                                                } else {
                                                    echo '<i class="' . esc_attr($settings['eael_section_fg_link_icon']) . '"></i>';
                                                }
                                            echo '</a>';
                                        }
                                    echo '</div>';
                                }
                            }
                        echo '</div>';
                    }
                echo '</div>
            </div>
        </div>';
} else if ($settings['eael_fg_grid_style'] == 'eael-cards') {
    echo '<div class="dynamic-gallery-item ' . esc_attr(implode(' ', $classes)) . '">
        <div class="dynamic-gallery-item-inner" data-itemid=" ' . esc_attr( get_the_ID() ) . ' ">';

		if ( $image_clickable ){
			echo '<a href="' . get_the_permalink() . '"'.$imageNofollow . '' . $imageTarget .'>';
		}
           echo '<div class="dynamic-gallery-thumbnail">';
                if(has_post_thumbnail()) {
                    echo '<img src="' . wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">';
                }else {
                    echo '<img src="'.\Elementor\Utils::get_placeholder_image_src().'">';
                }

                if ('media' == $settings['eael_fg_show_popup_styles'] && 'eael-none' == $settings['eael_fg_grid_hover_style']) {
                    if(has_post_thumbnail()) {
                        echo '<a href="' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . '" class="popup-only-media eael-magnific-link"></a>';
                    }else {
                        echo '<a href="'.\Elementor\Utils::get_placeholder_image_src().'" class="popup-only-media eael-magnific-link"></a>';
                    }
                }

                if ('eael-none' !== $settings['eael_fg_grid_hover_style'] && ! $image_clickable ) {
                    if ('media' == $settings['eael_fg_show_popup_styles']) {
                        echo '<div class="caption media-only-caption">';
                    } else {
                        echo '<div class="caption ' . esc_attr($settings['eael_fg_grid_hover_style']) . ' ">';
                    }
                    if ('true' == $settings['eael_fg_show_popup']) {
                        if ('media' == $settings['eael_fg_show_popup_styles']) {
                            if(has_post_thumbnail()) { // If there is no real image on this post/page then change of anchor tag with placeholder image src
                                echo '<a href="' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . '" class="popup-media eael-magnific-link"></a>';
                            }else {
                                echo '<a href="'.\Elementor\Utils::get_placeholder_image_src().'" class="popup-media eael-magnific-link"></a>';
                            }
                        } elseif ('buttons' == $settings['eael_fg_show_popup_styles']) {
                            echo '<div class="buttons">';
                                if (!empty($settings['eael_section_fg_zoom_icon'])) {
                                    if( has_post_thumbnail() ) {
                                        echo  '<a href="' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . '" class="eael-magnific-link">';
                                    }else {
                                        echo  '<a href="'.\Elementor\Utils::get_placeholder_image_src().'" class="eael-magnific-link">';
                                    }
                                        if( isset($settings['eael_section_fg_zoom_icon']['url']) ) {
                                            echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_zoom_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_zoom_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                        }else if ( ! empty( $settings['eael_section_fg_zoom_icon_new'] ) ) {
                                            \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_zoom_icon_new'], ['aria-hidden' => 'true']);
                                        }else {
                                            echo '<i class="' . esc_attr($settings['eael_section_fg_zoom_icon']) . '"></i>';
                                        }
                                    echo '</a>';
                                }

                                if (!empty($settings['eael_section_fg_link_icon'])) {
                                    echo  '<a href="' . get_the_permalink() . '"'.$linkNofollow . '' . $linkTarget .'>';
                                        if( isset($settings['eael_section_fg_link_icon']['url'])) {
                                            echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_link_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_link_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                        }else if ( ! empty( $settings['eael_section_fg_link_icon_new'] ) ) {
                                            \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_link_icon_new'], ['aria-hidden' => 'true']);
                                        }else {
                                            echo '<i class="' . esc_attr($settings['eael_section_fg_link_icon']) . '"></i>';
                                        }
                                    echo '</a>';
                                }
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                }
            echo '</div>';

		if ( $image_clickable ){
			echo '</a>';
		}

          echo ' <div class="item-content">';
             if($settings['eael_show_hover_title']) {
                echo '<h2 class="title"><a href="' . get_the_permalink() . '"'.$titleNofollow . '' . $titleTarget .'>' . get_the_title() . '</a></h2>';
            } if($settings['eael_show_hover_excerpt']) {
                 echo '<p>' . wp_trim_words(strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()), $settings['eael_post_excerpt'], '<a class="eael_post_excerpt_read_more" href="' . get_the_permalink() . '"'.$readMoreNofollow . '' . $readMoreTarget .'> ' . $settings['eael_post_excerpt_read_more'] . '</a>') . '</p>';
             }

                if (('buttons' == $settings['eael_fg_show_popup_styles']) && ('eael-none' == $settings['eael_fg_grid_hover_style'])) {
                    echo '<div class="buttons entry-footer-buttons">';
                        if (!empty($settings['eael_section_fg_zoom_icon'])) {
                            echo '<a href="' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . '" class="eael-magnific-link"><i class="' . esc_attr($settings['eael_section_fg_zoom_icon']) . '"></i></a>';
                        }
                        if (!empty($settings['eael_section_fg_link_icon'])) {
                            echo '<a href="' . get_the_permalink() . '"'.$linkNofollow . '' . $linkTarget .'><i class="' . esc_attr($settings['eael_section_fg_link_icon']) . '"></i></a>';
                        }
                    echo '</div>';
                }
            echo '</div>
        </div>
    </div>';
}
