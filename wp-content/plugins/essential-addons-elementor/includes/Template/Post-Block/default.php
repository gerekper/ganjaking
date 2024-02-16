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
                        <div class="eael-entry-thumbnail '. esc_attr( $enable_ratio ) .'">
                            <img src="' . esc_url( wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) ) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
                        </div>
                    </div>';
                }else {
	                if ( $settings['eael_show_fallback_img'] == 'yes' && !empty( $settings['eael_post_block_fallback_img']['url'] ) ) {
		                echo '<div class="eael-entry-media">
                            <div class="eael-entry-thumbnail ' . esc_attr( $enable_ratio ) . '">
                                <img src="' . esc_url( $eael_fallback_thumb_url ) . '" alt="' . esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) . '">
                            </div>
                        </div>';
	                }
                }

                if ($settings['eael_show_title'] || $settings['eael_show_meta'] || $settings['eael_show_excerpt'] || isset( $settings['eael_show_post_terms'] ) ) {
                    echo '<div class="eael-entry-wrapper ' . esc_attr( $settings['post_block_hover_animation'] ) . '">
                        <header class="eael-entry-header">';
                            if ($settings['eael_show_title']) {
                                $title_tag = Helper::eael_validate_html_tag( $settings['title_tag'] );
                                $title = '<' . $title_tag . ' class="eael-entry-title">
                                        <a class="eael-grid-post-link"
                                        href="' . esc_url( get_the_permalink() ) . '" 
                                        title="' . esc_attr( get_the_title() ) . '"
                                        ' . ( $settings['title_link_nofollow'] ? 'rel="nofollow"' : '' ) . ' 
                                        ' . ( $settings['title_link_target_blank'] ? 'target="_blank"' : '' ) . '>
                                        ' . esc_html( get_the_title() ) . '
                                        </a>
                                    </' . $title_tag . '>';

                                echo wp_kses( $title, Helper::eael_allowed_tags() );
                            }

                            if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-header') {
                                $meta = '<div class="eael-entry-meta">';
                                    if($settings['eael_show_author'] === 'yes') {
                                        $meta .= '<span class="eael-posted-by ">' . get_the_author_posts_link() . '</span>';
                                    }
                                    if($settings['eael_show_date'] === 'yes') {
                                        $meta .= '<span class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></span>';
                                    }
                                    $meta .= '</div>';

                                echo wp_kses( $meta, Helper::eael_allowed_tags() );
                            }
                        echo '</header>
                        
                        <div class="eael-entry-content ss">';
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
                                    echo wp_kses( $html, Helper::eael_allowed_tags() );
                                }
                            }
                            if ($settings['eael_show_excerpt']) {
                                echo '<div class="eael-grid-post-excerpt">';
                                    $content = get_the_excerpt() ? get_the_excerpt() : get_the_content();
                                    $content = strip_shortcodes($content);
                                    if(empty($settings['eael_excerpt_length'])) {
                                        echo '<p>'. wp_kses( $content, Helper::eael_allowed_tags() ) .'</p>';
                                    }else {
                                        $content = wp_trim_words($content, $settings['eael_excerpt_length'], $settings['expanison_indicator']);
                                        echo '<p>' . wp_kses( $content, Helper::eael_allowed_tags() ) . '</p>';
                                    }

                                    if ('yes' == $settings['show_read_more_button']) {
                                        if (class_exists('WooCommerce') && $settings['post_type'] == 'product') {
                                            echo '<p class="eael-entry-content-btn">';
                                                woocommerce_template_loop_add_to_cart();
                                            echo '</p>';
                                        } else {
                                            echo '<a href="' . esc_url( get_the_permalink() ) . '" 
                                                class="eael-post-elements-readmore-btn"
                                                ' . ( $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '' ) . '
                                                ' . ( $settings['read_more_link_target_blank'] ? 'target="_blank"' : '' ) . '>
                                                ' . esc_html( $settings['read_more_button_text'] ) . '</a>';
                                        }
                                    }
                                echo '</div>';
                            }
                        echo '</div>';

                        if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-footer') {
                            echo '<div class="eael-entry-footer">';

                                if($settings['eael_show_avatar'] === 'yes') {
                                    echo '<div class="eael-author-avatar">
                                        <a href="' . esc_url( get_author_posts_url(get_the_author_meta('ID')) ) . '">' . get_avatar(get_the_author_meta('ID'), 96) . '</a>
                                    </div>';
                                }
                                

                                $entry_meta = '<div class="eael-entry-meta">';
                                    if($settings['eael_show_author'] === 'yes') {
                                        $entry_meta .= '<div class="eael-posted-by">' . get_the_author_posts_link() . '</div>';
                                    }

                                    if($settings['eael_show_date'] === 'yes') {
                                        $entry_meta .= '<div class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></div>';
                                    }
                                $entry_meta .=  '</div>';

                                echo wp_kses( $entry_meta, Helper::eael_allowed_tags() );

                            echo '</div>';
                        }
                        echo '<div class="eael-entry-overlay-ssss">
                            <a href="' . esc_url( get_the_permalink() ) . '"
                            ' . ( $settings['image_link_nofollow'] ? 'rel="nofollow"' : '' ) . '
                            ' . ( $settings['image_link_target_blank'] ? 'target="_blank"' : '' ) . '>
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
                        <div class="eael-entry-overlay ' . esc_attr( $settings['post_block_hover_animation'] ) . '">';
                        if( isset($settings['eael_post_block_bg_hover_icon']['url']) ) {
                            echo '<img class="eael-post-block-hover-svg-icon" src="' . esc_url( $settings['eael_post_block_bg_hover_icon']['url'] ) . '" alt="'.esc_attr(get_post_meta($settings['eael_post_block_bg_hover_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                        }else {
                            echo '<i class="' . esc_attr( $settings['eael_post_block_bg_hover_icon'] ) . '" aria-hidden="true"></i>';
                        }
                        echo '<a href="' . esc_url( get_the_permalink() ) . '"
                        ' . ( $settings['image_link_nofollow'] ? 'rel="nofollow"' : '' ) . '
                        ' . ( $settings['image_link_target_blank'] ? 'target="_blank"' : '' ) . '>
                        </a>
                        </div>
                        <div class="eael-entry-thumbnail ' . esc_attr( $enable_ratio ) . '">
                            <img src="' . esc_url( wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) ) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
                        </div>
                    </div>';
                } elseif($settings['eael_show_fallback_img'] == 'yes' && !empty( $settings['eael_post_block_fallback_img']['url'] )) {
	                echo '<div class="eael-entry-media hmm">
	                        <div class="eael-entry-overlay ' . esc_attr( $settings['post_block_hover_animation'] ) . '">';
				                if( isset($settings['eael_post_block_bg_hover_icon']['url']) ) {
					                echo '<img class="eael-post-block-hover-svg-icon" src="' . esc_url( $settings['eael_post_block_bg_hover_icon']['url'] ) . '" alt="'.esc_attr(get_post_meta($settings['eael_post_block_bg_hover_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
				                }else {
					                echo '<i class="' . esc_attr( $settings['eael_post_block_bg_hover_icon'] ) . '" aria-hidden="true"></i>';
				                }
				                echo '<a 
                                href="' . esc_url( get_the_permalink() ) . '"
                                ' . ( $settings['image_link_nofollow'] ? 'rel="nofollow"' : '' ) . '
                                ' . ( $settings['image_link_target_blank'] ? 'target="_blank"' : '' ) . '>
                                </a>
	                        </div>
	                       <div class="eael-entry-thumbnail ' . esc_attr( $enable_ratio ) . '">
	                            <img src="' . esc_url( $eael_fallback_thumb_url ) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
	                        </div>
	                    </div>';
                }

                if ($settings['eael_show_title'] || $settings['eael_show_meta'] || $settings['eael_show_excerpt'] || isset( $settings['eael_show_post_terms'] ) ) {
                    echo '<div class="eael-entry-wrapper">';

                    $header = '<header class="eael-entry-header">';
                            if ($settings['eael_show_title']) {
                                $title_tag = Helper::eael_validate_html_tag( $settings['title_tag'] );
                                $header .= '<' . $title_tag . ' class="eael-entry-title">
                                        <a class="eael-grid-post-link" 
                                        href="' . esc_url( get_the_permalink() ) . '" 
                                        title="' . esc_attr( get_the_title() ) . '"
                                        ' . ( $settings['title_link_nofollow'] ? 'rel="nofollow"' : '' ) . ' 
                                        ' . ( $settings['title_link_target_blank'] ? 'target="_blank"' : '' ) . '>
                                        ' . esc_html( get_the_title() ) . '
                                        </a>

                                    </' . $title_tag . '>';
                            }

                            if ($settings['eael_show_meta'] && $settings['meta_position'] == 'meta-entry-header') {
                                $header .= '<div class="eael-entry-meta">';
                                    if($settings['eael_show_author'] === 'yes') {
                                        $header .= '<span class="eael-posted-by">' . get_the_author_posts_link() . '</span>';
                                    }

                                    if($settings['eael_show_date'] === 'yes') {
                                        $header .= '<span class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></span>';
                                    }
                                    $header .= '</div>';
                            }
                            $header .= '</header>';

                        echo wp_kses( $header, Helper::eael_allowed_tags() );
                        
                        echo '<div class="eael-entry-content">';
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
                                    echo wp_kses( $html, Helper::eael_allowed_tags() );
                                }
                            }
                            if ($settings['eael_show_excerpt']) {
                                echo '<div class="eael-grid-post-excerpt">';
                                    $content = strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content());
                                    if(empty($settings['eael_excerpt_length'])) {
                                        echo '<p>'. wp_kses( $content, Helper::eael_allowed_tags() ) .'</p>';
                                    }else {
                                        $content = wp_trim_words( $content, $settings['eael_excerpt_length'], $settings['expanison_indicator']);
                                        echo '<p>' . wp_kses( $content, Helper::eael_allowed_tags() ) . '</p>';
                                    }

                                    if ('yes' == $settings['show_read_more_button']) {
                                        echo '<a 
                                        href="' . esc_url( get_the_permalink() ) . '" 
                                        class="eael-post-elements-readmore-btn"
                                        ' . ( $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '' ) . '
                                        ' . ($settings['read_more_link_target_blank'] ? 'target="_blank"' : '' ) . '>
                                        ' . esc_html( $settings['read_more_button_text'] ) . '</a>';
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
                                <a href="' . esc_url( get_author_posts_url(get_the_author_meta('ID')) ) . '">' . get_avatar(get_the_author_meta('ID'), 96) . '</a>
                            </div>';
                        }
                       
                        $entry_meta = '<div class="eael-entry-meta">';
                            if($settings['eael_show_author'] === 'yes') {
                                $entry_meta .= '<div class="eael-posted-by">' . get_the_author_posts_link() . '</div>';
                            }

                            if($settings['eael_show_date'] === 'yes') {
                                $entry_meta .= '<div class="eael-posted-on"><time datetime="' . get_the_date() . '">' . get_the_date() . '</time></div>';
                            }
                        $entry_meta .= '</div>';

                        echo wp_kses( $entry_meta, Helper::eael_allowed_tags() );

                    echo '</div>';
                }
            
            echo '</div>
        </div>
    </article>';
}
