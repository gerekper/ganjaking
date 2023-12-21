<?php

namespace Happy_Addons_Pro;

use Happy_Addons\Elementor\Condition_Manager as Free_Condition_Manager;

defined('ABSPATH') || die();


// ██████╗░░█████╗░███╗░░██╗██╗████████╗  ░██████╗████████╗███████╗░█████╗░██╗░░░░░
// ██╔══██╗██╔══██╗████╗░██║╚█║╚══██╔══╝  ██╔════╝╚══██╔══╝██╔════╝██╔══██╗██║░░░░░
// ██║░░██║██║░░██║██╔██╗██║░╚╝░░░██║░░░  ╚█████╗░░░░██║░░░█████╗░░███████║██║░░░░░
// ██║░░██║██║░░██║██║╚████║░░░░░░██║░░░  ░╚═══██╗░░░██║░░░██╔══╝░░██╔══██║██║░░░░░
// ██████╔╝╚█████╔╝██║░╚███║░░░░░░██║░░░  ██████╔╝░░░██║░░░███████╗██║░░██║███████╗
// ╚═════╝░░╚════╝░╚═╝░░╚══╝░░░░░░╚═╝░░░  ╚═════╝░░░░╚═╝░░░╚══════╝╚═╝░░╚═╝╚══════╝
class Condition_Manager {

    public static function init() {
        add_filter('happyaddons/conditions/archive', [__CLASS__, 'nullify_condition']);
        add_filter('happyaddons/conditions/singular', [__CLASS__, 'nullify_condition']);
        add_filter('happyaddons/conditions/check/cond_sub_id', [__CLASS__, 'check_sub_conditions'], 9999999, 2);
    }

    public static function nullify_condition($conds) {
        array_walk_recursive($conds, function (&$v, $k) {
            if ($k == 'is_pro') {
                $v = false;
            }
        });

        return $conds;
    }

    public static function check_sub_conditions($sub_name, $parsed_condition) {
        $name = $parsed_condition['name'];

        if ($name == 'archive') {
            switch ($sub_name):
                case 'post_archive':
                    return is_post_type_archive('post') || is_home();
                    break;
                case 'author':
                    return is_author();
                    break;
                case 'date':
                    return is_date();
                    break;
                case 'search':
                    return is_search();
                    break;
                default:
                    return false;
                    break;
            endswitch;
        }

        if ($name == 'singular') {
            $id = isset($parsed_condition['sub_id']) ? $parsed_condition['sub_id'] : null;

            switch ($sub_name):
                case 'front_page':
                    return is_front_page();
                    break;

                case 'post':
                    if ($id) {
                        $id = (int) $id;
                        return is_singular() && get_queried_object_id() === $id;
                    }
                    return is_singular('post');
                    break;

                case 'in_category':
                    return is_singular() && has_term((int) $id, 'category');
                    break;

                case 'in_category_children':
                    $id = (int) $id;
                    if (!is_singular() || !$id) {
                        return false;
                    }
                    $child_terms = get_term_children($id, 'category');
                    return !empty($child_terms) && has_term($child_terms, 'category');
                    break;

                case 'in_post_tag':
                    return is_singular() && has_term((int) $id, 'post_tag');
                    break;

                case 'post_by_author':
                    return is_singular('post') && get_post_field('post_author') === $id;
                    break;

                case 'page':
                    if ($id) {
                        $id = (int) $id;
                        return is_singular() && get_queried_object_id() === $id;
                    }
                    return is_singular('page');
                    break;

                case 'page_by_author':
                    return is_singular('page') && get_post_field('post_author') === $id;
                    break;

                case 'child_of':
                    if (!is_singular()) {
                        return false;
                    }
                    $id = (int) $id;
                    $parent_id = wp_get_post_parent_id(get_the_ID());
                    return ((0 === $id && 0 < $parent_id) || ($parent_id === $id));
                    break;

                case 'any_child_of':
                    if ( ! is_singular() ) {
                        return false;
                    }
                    $id = (int) $id;
                    $parents = get_post_ancestors( get_the_ID() );
                    return ( ( 0 === $id && ! empty( $parents ) ) || in_array( $id, $parents ) );

                    break;

                case 'by_author':
                    return is_singular() && get_post_field('post_author') === $id;
                    break;

                case 'not_found404':
                    return is_404();
                    break;
                default:
                    return false;
                    break;
            endswitch;
        }
    }
}

Condition_Manager::init();
