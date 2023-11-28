<?php

/**
 * Template Name: Default
 */

use \Elementor\Group_Control_Image_Size;
use Essential_Addons_Elementor\Classes\Helper;

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( $settings['eael_show_fallback_img'] == 'yes' && !empty( $settings['eael_post_block_fallback_img']['url'] ) ) {
	$fallback_image_id = $settings['eael_post_block_fallback_img']['id'];
	$eael_fallback_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $fallback_image_id, 'image', $settings );
}

$enable_ratio = $settings['enable_post_block_image_ratio'] == 'yes' ? 'eael-image-ratio':'';

if ($settings['grid_style'] == 'post-block-style-overlay') {
    echo '<article class="eael-post-block-item eael-post-block-column">
        <div class="eael-post-block-item-holder">
            <div class="eael-post-block-item-holder-inner">';
                if (has_post_thumbnail() && $settings['eael_show_image'] == 'yes') {
                    echo '<div class="eael-entry-media">
                        <div class="eael-entry-thumbnail '.$enable_ratio.'">
                            <img src="' . wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
                        </div>
                    </div>';
                }else {
	                if ( $settings['eael_show_fallback_img'] == 'yes' && !empty( $settings['eael_post_block_fallback_img']['url'] ) ) {
		                echo '<div class="eael-entry-media">
                            <div class="eael-entry-thumbnail '.$enable_ratio.'">
                                <img src="' . $eael_fallback_thumb_url . '" alt="' . esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) . '">
                            </div>
                        </div>';
	                }
                }

                if ($settings['eael_show_title'] || $settings['eael_show_meta'] || $settings['eael_show_excerpt'] || isset( $settings['eael_show_post_terms'] ) ) {
                    echo '<div class="eael-entry-wrapper ' . $settings['post_block_hover_animation'] . '">
                        <header class="eael-entry-header">';
                            if ($settings['eael_show_title']) {
                                echo '<' . Helper::eael_validate_html_tag( $settings['title_tag'] ) . ' class="eael-entry-title"><a class="eael-grid-post-link" href="' . get_the_permalink() . '" title="' . get_the_title() . '"' . $link_settings['title_link_nofollow'] . '' . $link_settings['title_link_target_blank'] . '>' . get_the_title() . '</a></' . Helper::eael_validate_html_tag( $settings['title_tag'] ) . '>';
                            }

                            if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-header') {
                                echo '<div class="eael-entry-meta">';
                                    if($settings['eael_show_author'] === 'yes') {
                                        echo '<span class="eael-posted-by">' . get_the_author_posts_link() . '</span>';
                                    }
                                    if($settings['eael_show_date'] === 'yes') {
                                        echo '<span class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></span>';
                                    }
                                echo '</div>';
                            }
                        echo '</header>
                        
                        <div class="eael-entry-content">';
                            if ($settings['eael_show_post_terms'] === 'yes') {
                                if ($settings['eael_post_terms'] === 'category') {
                                    $terms = get_the_category();
                                }
                                if ($settings['eael_post_terms'] === 'tags') {
                                    $terms = get_the_tags();
                                }
                                if (!empty($terms)) {
                                    $html = '<ul class="post-meta-categories">';
                                    $count = 0;
                                    foreach ($terms as $term) {
                                        if ($count === intval($settings['eael_post_terms_max_length'])) {
                                            break;
                                        }
                                        if ($count === 0) {
                                            $html .= '<li class="meta-cat-icon"><i class="far fa-folder-open"></i></li>';
                                        }
                                        $link = ($settings['eael_post_terms'] === 'category') ? get_category_link($term->term_id) : get_tag_link($term->term_id);
                                        $html .= '<li>';
                                        $html .= '<a href="' . esc_url($link) . '">';
                                        $html .= $term->name;
                                        $html .= '</a>';
                                        $html .= '</li>';
                                        $count++;
                                    }
                                    $html .= '</ul>';
                                    echo $html;
                                }
                            }
                            if ($settings['eael_show_excerpt']) {
                                echo '<div class="eael-grid-post-excerpt">';
                                    if(empty($settings['eael_excerpt_length'])) {
                                        echo '<p>'.strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()).'</p>';
                                    }else {
                                        echo '<p>' . wp_trim_words(strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()), $settings['eael_excerpt_length'], $settings['expanison_indicator']) . '</p>';
                                    }

                                    if ('yes' == $settings['show_read_more_button']) {
                                        if (class_exists('WooCommerce') && $settings['post_type'] == 'product') {
                                            echo '<p class="eael-entry-content-btn">';
                                                woocommerce_template_loop_add_to_cart();
                                            echo '</p>';
                                        } else {
                                            echo '<a href="' . get_the_permalink() . '" class="eael-post-elements-readmore-btn"' . $link_settings['read_more_link_nofollow'] . '' . $link_settings['read_more_link_target_blank'] . '>' . esc_attr($settings['read_more_button_text']) . '</a>';
                                        }
                                    }
                                echo '</div>';
                            }
                        echo '</div>';

                        if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-footer') {
                            echo '<div class="eael-entry-footer">';

                                if($settings['eael_show_avatar'] === 'yes') {
                                    echo '<div class="eael-author-avatar">
                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_avatar(get_the_author_meta('ID'), 96) . '</a>
                                    </div>';
                                }
                                

                                echo '<div class="eael-entry-meta">';
                                    if($settings['eael_show_author'] === 'yes') {
                                        echo '<div class="eael-posted-by">' . get_the_author_posts_link() . '</div>';
                                    }

                                    if($settings['eael_show_date'] === 'yes') {
                                        echo '<div class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></div>';
                                    }
                                echo '</div>';

                            echo '</div>';
                        }
                        echo '<div class="eael-entry-overlay-ssss">
                        <a href="' . get_the_permalink() . '"' . $link_settings['image_link_nofollow'] . '' . $link_settings['image_link_target_blank'] . '>
                        </a>
                        </div>
                    </div>';
                }
            echo '</div>
        </div>
    </article>';
} else {
    echo '<article class="eael-post-block-item eael-post-block-column">
        <div class="eael-post-block-item-holder">
            <div class="eael-post-block-item-holder-inner">';
                if (has_post_thumbnail() && $settings['eael_show_image'] == 'yes') {
                    echo '<div class="eael-entry-media">
                        <div class="eael-entry-overlay ' . $settings['post_block_hover_animation'] . '">';
                        if( isset($settings['eael_post_block_bg_hover_icon']['url']) ) {
                            echo '<img class="eael-post-block-hover-svg-icon" src="' . $settings['eael_post_block_bg_hover_icon']['url'] . '" alt="'.esc_attr(get_post_meta($settings['eael_post_block_bg_hover_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                        }else {
                            echo '<i class="' . $settings['eael_post_block_bg_hover_icon'] . '" aria-hidden="true"></i>';
                        }
                        echo '<a href="' . get_the_permalink() . '"' . $link_settings['image_link_nofollow'] . '' . $link_settings['image_link_target_blank'] . '></a>
                        </div>
                        <div class="eael-entry-thumbnail '.$enable_ratio.'">
                            <img src="' . wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
                        </div>
                    </div>';
                } elseif($settings['eael_show_fallback_img'] == 'yes' && !empty( $settings['eael_post_block_fallback_img']['url'] )) {
	                echo '<div class="eael-entry-media hmm">
	                        <div class="eael-entry-overlay ' . $settings['post_block_hover_animation'] . '">';
				                if( isset($settings['eael_post_block_bg_hover_icon']['url']) ) {
					                echo '<img class="eael-post-block-hover-svg-icon" src="' . $settings['eael_post_block_bg_hover_icon']['url'] . '" alt="'.esc_attr(get_post_meta($settings['eael_post_block_bg_hover_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
				                }else {
					                echo '<i class="' . $settings['eael_post_block_bg_hover_icon'] . '" aria-hidden="true"></i>';
				                }
				                echo '<a href="' . get_the_permalink() . '"' . $link_settings['image_link_nofollow'] . '' . $link_settings['image_link_target_blank'] . '></a>
	                        </div>
	                       <div class="eael-entry-thumbnail '.$enable_ratio.'">
	                            <img src="' . $eael_fallback_thumb_url . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
	                        </div>
	                    </div>';
                }

                if ($settings['eael_show_title'] || $settings['eael_show_meta'] || $settings['eael_show_excerpt'] || isset( $settings['eael_show_post_terms'] ) ) {
                    echo '<div class="eael-entry-wrapper">
                        <header class="eael-entry-header">';
                            if ($settings['eael_show_title']) {
                                echo '<' . Helper::eael_validate_html_tag( $settings['title_tag'] ) . ' class="eael-entry-title"><a class="eael-grid-post-link" href="' . get_the_permalink() . '" title="' . get_the_title() . '"' . $link_settings['title_link_nofollow'] . '' . $link_settings['title_link_target_blank'] . '>' . get_the_title() . '</a></' . Helper::eael_validate_html_tag( $settings['title_tag'] ) . '>';
                            }

                            if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-header') {
                                echo '<div class="eael-entry-meta">';
                                    if($settings['eael_show_author'] === 'yes') {
                                        echo '<span class="eael-posted-by">' . get_the_author_posts_link() . '</span>';
                                    }

                                    if($settings['eael_show_date'] === 'yes') {
                                        echo '<span class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></span>';
                                    }
                                echo '</div>';
                            }
                        echo '</header>
                        
                        <div class="eael-entry-content">';
                            if ($settings['eael_show_post_terms'] === 'yes') {
                                if ($settings['eael_post_terms'] === 'category') {
                                    $terms = get_the_category();
                                }
                                if ($settings['eael_post_terms'] === 'tags') {
                                    $terms = get_the_tags();
                                }
                                if (!empty($terms)) {
                                    $html = '<ul class="post-meta-categories">';
                                    $count = 0;
                                    foreach ($terms as $term) {
                                        if ($count === intval($settings['eael_post_terms_max_length'])) {
                                            break;
                                        }
                                        if ($count === 0) {
                                            $html .= '<li class="meta-cat-icon"><i class="far fa-folder-open"></i></li>';
                                        }
                                        $link = ($settings['eael_post_terms'] === 'category') ? get_category_link($term->term_id) : get_tag_link($term->term_id);
                                        $html .= '<li>';
                                        $html .= '<a href="' . esc_url($link) . '">';
                                        $html .= $term->name;
                                        $html .= '</a>';
                                        $html .= '</li>';
                                        $count++;
                                    }
                                    $html .= '</ul>';
                                    echo $html;
                                }
                            }
                            if ($settings['eael_show_excerpt']) {
                                echo '<div class="eael-grid-post-excerpt">';
                                    if(empty($settings['eael_excerpt_length'])) {
                                        echo '<p>'.strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()).'</p>';
                                    }else {
                                        echo '<p>' . wp_trim_words(strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()), $settings['eael_excerpt_length'], $settings['expanison_indicator']) . '</p>';
                                    }

                                    if ('yes' == $settings['show_read_more_button']) {
                                        echo '<a href="' . get_the_permalink() . '" class="eael-post-elements-readmore-btn"' . $link_settings['read_more_link_nofollow'] . '' . $link_settings['read_more_link_target_blank'] . '>' . esc_attr($settings['read_more_button_text']) . '</a>';
                                    }

                                    if (class_exists('WooCommerce') && $settings['post_type'] == 'product') {
                                        echo '<p class="eael-entry-content-btn">';
                                        woocommerce_template_loop_add_to_cart();
                                        echo '</p>';
                                    }
                                echo '</div>';
                            }
                        echo '</div>
                    </div>';
                }

                if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-footer') {
                    echo '<div class="eael-entry-footer">';

                        if($settings['eael_show_avatar'] === 'yes') {
                            echo '<div class="eael-author-avatar">
                                <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_avatar(get_the_author_meta('ID'), 96) . '</a>
                            </div>';
                        }
                        

                        echo '<div class="eael-entry-meta">';
                            if($settings['eael_show_author'] === 'yes') {
                                echo '<div class="eael-posted-by">' . get_the_author_posts_link() . '</div>';
                            }

                            if($settings['eael_show_date'] === 'yes') {
                                echo '<div class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></div>';
                            }
                        echo '</div>';

                    echo '</div>';
                }
            
            echo '</div>
        </div>
    </article>';
}
