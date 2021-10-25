<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Blockquote $widget */

$settings = array(
    'content'        => '',
    'tstm_author' => '',
    'sub_name'      => '',
    'image' => '',
    'link'         => '',
    'item_align' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$css_class = array(
    'gt3_blockquote'
);

if(!empty($settings['item_align'])) {
    $css_class[] = 'text_align-'.esc_attr($settings['item_align']);
}

if(!empty($settings['image']) && !empty($settings['image']['id'])) {
    $css_class[] = 'gt3_blockquote--has_image';
}

if (!empty($settings['quote_icon']) && $settings['quote_icon'] === 'yes' ) {
    $css_class[] = 'gt3_blockquote--quote_icon';
}

if (!empty($settings['link']['url'])) {
    $link = '<a class="gt3_blockquote__link" href="'.esc_url($settings['link']['url']).'" '.($settings['link']['is_external'] ? ' target="_blank"' : '').' '.($settings['link']['nofollow'] ? ' rel="nofollow"' : '').'>';
}

$widget->add_render_attribute('wrapper', 'class', $css_class);

$quote_src = plugins_url( '/core/elementor/assets/image/quote.png', GT3_THEMES_CORE_PLUGIN_FILE );
$quote_src = apply_filters( 'gt3_testimonial_quote_src', $quote_src );

$widget->add_render_attribute('wrapper', 'data-quote-src', $quote_src);


?><div <?php $widget->print_render_attribute_string('wrapper') ?>>
    <?php
    if (!empty($link)) {
        echo $link;
    }
    if (!empty($settings['quote_icon']) && $settings['quote_icon'] === 'yes' ) {
        ?><div class="gt3_blockquote__quote_icon"></div><?php
    }
    ?>
    <div class="gt3_blockquote__text"><?php echo $settings['content']; ?></div>
    <?php
    if (!empty($settings['tstm_author']) || !empty($settings['sub_name'])) {
        ?><div class="gt3_blockquote__author_wrapper"><?php

            if (empty($settings['item_align']) || (!empty($settings['item_align']) && $settings['item_align'] != 'right')) {
                ?><div class="gt3_blockquote__author_divider"></div><?php
            }
            ?><div class="gt3_blockquote__author_container"><?php
                if (!empty($settings['image']) && !empty($settings['image']['id'])) {

                    $src = Utils::get_placeholder_image_src();
                    $alt = '';
                    if(isset($settings['image']['id']) && (bool) $settings['image']['id']) {
                        $image = wp_get_attachment_image_src($settings['image']['id'], 'single-post-thumbnail');
	                    $image_obj = wp_prepare_attachment_for_js($settings['image']['id']);
	                    $alt = $image_obj['alt'];

                        if($image) {
                            if (!empty($settings['image_size']) && is_array($settings['image_size']) && !empty($settings['image_size']['size']) ) {
                                $src = aq_resize($image[0], (int)$settings['image_size']['size']*2, (int)$settings['image_size']['size']*2, true, true, true);

                            }else{
                                $src = $image[0];
                            }
                        }
                    }

                    $avatar = '<div class="gt3_blockquote__author_photo"><img src="'.esc_url($src).'" style="width:'.esc_attr((int)$settings['image_size']['size']).'px;height:'.esc_attr((int)$settings['image_size']['size']).'px;" alt="'.$alt.'" /></div>';

                }else{
                    $avatar = '';
                }


                if (!empty($avatar) && (empty($settings['item_align']) || (!empty($settings['item_align']) && $settings['item_align'] != 'right'))) {
                    echo $avatar;
                }

                if (!empty($settings['tstm_author'])) {
                    echo '<div class="gt3_blockquote__author_name">'.esc_html($settings['tstm_author']).'</div>';
                }
                if (!empty($settings['sub_name'])) {
                    echo '<div class="gt3_blockquote__author_sub_name">'.esc_html($settings['sub_name']).'</div>';
                }
            ?></div><?php
            if (!empty($settings['item_align']) && $settings['item_align'] == 'right') {
                ?><div class="gt3_blockquote__author_divider"></div><?php
                if (!empty($avatar)) {
                    echo $avatar;
                }
            }
            ?>
        </div><?php
    }
    if (!empty($link)) {
        echo '</a>';
    }
    ?>
</div>
