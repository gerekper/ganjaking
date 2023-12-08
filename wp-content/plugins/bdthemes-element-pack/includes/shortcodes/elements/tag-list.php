<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly
    ep_add_shortcode([
        'id'             => 'tag_list',
        'callback'       => 'ep_shortcode_tag_list',
        'name'           => __('Tag List', 'bdthemes-element-pack'),
        'type'           => 'single',
        'atts'           => [
            'class' => [
                'type'    => 'extra_css_class',
                'name'    => __('Extra CSS class', 'bdthemes-element-pack'),
                'desc'    => __('Additional CSS class name(s) separated by space(s)', 'bdthemes-element-pack'),
                'default' => '',
            ],
        ],
        'desc'           => __('Show Tag List', 'bdthemes-element-pack'),
    ]);

    function ep_shortcode_tag_list($atts = null) {

        $atts = shortcode_atts(array('class' => ''), $atts, 'tag-list');

        $tags = get_tags(array(
        'hide_empty' => false
        ));

        foreach ($tags as $tag) { ?>
            <a class="epsc-tag-list bdt-label <?php Element_Pack_Shortcodes::ep_get_css_class($atts); ?>" href="<?php bloginfo('url');?>/tag/<?php print_r($tag->slug);?>">
                <?php print_r($tag->name);?>
            </a>
        <?php }

        return;

    }
?>
