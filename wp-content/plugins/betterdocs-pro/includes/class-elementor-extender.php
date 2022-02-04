<?php

use Elementor\Controls_Manager;

class Betterdocs_Elmenetor_Extender
{

    public static function init()
    {
        add_action('betterdocs_elementor_sidebar', [__CLASS__, 'sidebar_display'], 10, 3);
    }

    public static function sidebar_display( $sidebar, $settings )
    {
        $elementor_sidebar = new BetterDocs_Elementor_Sidebar();
        $multiple_kb = BetterDocs_DB::get_settings('multiple_kb');
        $sticky_toc = ($settings['enable_sticky_toc'] == '1') ? $elementor_sidebar->get_toc() : '';
        if ($settings['betterdocs_sidebar_layout'] == 'layout-1') {
            if ($multiple_kb == 1) {
                $shortcode = do_shortcode('[betterdocs_category_grid disable_customizer_style="true" sidebar_list="true" posts_per_grid="-1" multiple_knowledge_base="true" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'" title_tag="'.$settings['category_title_tag'].'"]');
            } else {
                $shortcode = do_shortcode('[betterdocs_category_grid disable_customizer_style="true" sidebar_list="true" posts_per_grid="-1" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'" title_tag="'.$settings['category_title_tag'].'"]');
            }
            
            $sidebar = '<aside id="betterdocs-sidebar" class="betterdocs-el-single-sidebar"><div class="betterdocs-sidebar-content">'.$shortcode.'</div>'.$sticky_toc.'</aside>';
        } elseif ($settings['betterdocs_sidebar_layout'] == 'layout-2') { 
            if ($multiple_kb == 1) {
                $shortcode = do_shortcode('[betterdocs_category_list posts_per_grid="-1" multiple_knowledge_base="true" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'" title_tag="'.$settings['category_title_tag'].'"]');
            } else {
                $shortcode = do_shortcode('[betterdocs_category_list posts_per_grid="-1" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'" title_tag="'.$settings['category_title_tag'].'"]');
            }

            $sidebar = '<aside class="betterdocs-full-sidebar-left"><div class="betterdocs-sidebar-content">'.$shortcode.'</div></aside>';
        } elseif ($settings['betterdocs_sidebar_layout'] == 'layout-3') {
            if ($multiple_kb == 1) {
                $shortcode = do_shortcode('[betterdocs_category_grid disable_customizer_style="true" post_counter=0 sidebar_list="true" posts_per_grid="-1"  multiple_knowledge_base="true" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'" title_tag="'.$settings['category_title_tag'].'"]');
            } else {
                $shortcode = do_shortcode('[betterdocs_category_grid disable_customizer_style="true" post_counter=0 sidebar_list="true" posts_per_grid="-1" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'" title_tag="'.$settings['category_title_tag'].'"]');
            }

            $sidebar = '<aside class="betterdocs-full-sidebar-left"><div class="betterdocs-sidebar-content">'.$shortcode.'</div></aside>';
        } elseif ($settings['betterdocs_sidebar_layout'] == 'layout-4') {
            if ($multiple_kb == 1) {
                $shortcode = do_shortcode( '[betterdocs_category_grid disable_customizer_style="true" title_tag="'.$settings['category_title_tag'].'" icon=0 post_counter=0 sidebar_list="true" posts_per_grid="-1" multiple_knowledge_base=true terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'"]' );
            } else {
                $shortcode = do_shortcode( '[betterdocs_category_grid disable_customizer_style="true" title_tag="'.$settings['category_title_tag'].'" icon=0 post_counter=0 sidebar_list="true" posts_per_grid="-1" terms_orderby="'.$settings['orderby'].'" terms_order="'.$settings['order'].'"]' );

            }

            $sidebar = '<div class="betterdocs-single-layout4"><aside class="betterdocs-full-sidebar-left"><div class="betterdocs-sidebar-content">'.$shortcode.'</div></aside></div>';
        }
        return $sidebar;
    }
}

Betterdocs_Elmenetor_Extender::init();
