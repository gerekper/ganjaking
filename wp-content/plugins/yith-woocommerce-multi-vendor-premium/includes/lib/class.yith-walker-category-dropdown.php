<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Walker_CategoryDropdown' ) ) {


    /**
     * Create HTML dropdown list of Categories.
     *
     * @package WordPress
     * @since   2.1.0
     * @uses    Walker
     */
    class YITH_Walker_CategoryDropdown extends Walker {
        /**
         * @see   Walker::$tree_type
         * @since 2.1.0
         * @var string
         */
        public $tree_type = 'category';

        /**
         * @see   Walker::$db_fields
         * @since 2.1.0
         * @var array
         */
        public $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );

        /**
         * Start the element output.
         *
         * @see   Walker::start_el()
         * @since 2.1.0
         *
         * @param string $output   Passed by reference. Used to append additional content.
         * @param object $category Category data object.
         * @param int    $depth    Depth of category. Used for padding.
         * @param array  $args     Uses 'selected' and 'show_count' keys, if they exist. @see wp_dropdown_categories()
         */
        public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
            $pad = str_repeat( '&nbsp;', $depth * 3 );

            /** This filter is documented in wp-includes/category-template.php */
            $cat_name = apply_filters( 'list_cats', $category->name, $category );

            $output .= "\t<option class=\"level-$depth\" value=\"" . $category->slug . "\"";
            if ( $category->term_id == $args['selected'] ) {
                $output .= ' selected="selected"';
            }
            $output .= '>';
            $output .= $pad . $cat_name;
            if ( $args['show_count'] ) {
                $output .= '&nbsp;&nbsp;(' . number_format_i18n( $category->count ) . ')';
            }
            $output .= "</option>\n";
        }
    }
}


/**
 * Return a new instance of YITH_Walker_CategoryDropdown
 *
 * @return object
 * @since  1.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if( ! function_exists( 'YITH_Walker_CategoryDropdown' ) ) {
    function YITH_Walker_CategoryDropdown(){
        return new YITH_Walker_CategoryDropdown();
    }
}