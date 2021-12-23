<?php
/**
 * widget-wppm.php
 * The main Elementor widget file for WP Post Modules
 *
 * @since 1.0.0
 * @version 1.9.0
 *
 */

namespace WP_Post_Modules_El\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;
use WP_Query;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly


class Widget_WP_Post_Modules_El extends Widget_Base {

    public function get_name() {
        return 'wp-post-modules-el';
    }

    public function get_title() {
        return __( 'WP Post Modules', 'wppm-el' );
    }

    public function get_icon() {
        return 'eicon-posts-group';
    }

    public function get_script_depends() {
        return [
            'wppm-el-plugin-functions',
            'wppm-jq-owl-carousel',
            'wppm-jq-marquee',
            'wppm-jq-easing'
        ];
    }

    protected function register_controls() {

            // Categories array
            $cat_arr = array();
            $categories = get_categories();
            foreach( $categories as $category ){
              $cat_arr[ $category->term_id ] = $category->term_id;
            }

            // Post types
            $post_type_arr = array();
            foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
                if ( ! in_array( $post_type, array( 'revision', 'attachment', 'nav_menu_item' ) ) ) {
                    $post_type_arr[ $post_type ] = $post_type;
                }
            }
            global $sitepress;
            if ( isset( $sitepress ) ) {
                remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
            }
            // Taxonomies
            $taxonomies = get_taxonomies( array( 'public' => true ) );
            $tax_arr = array();
            $term_arr = array();
            $cat_limit = apply_filters( 'wppm_cat_limit', 999 );
            foreach ( $taxonomies as $taxonomy ) {
                if ( isset ( $taxonomy ) && is_array( $taxonomy ) ) {
                    $taxonomy = array_keys( $taxonomy );
                    $taxonomy = $taxonomy[0];
                }
                if ( isset ( $taxonomy ) && '' !== $taxonomy ) {
                    $tax = get_taxonomy( $taxonomy );

                    // Get terms for each taxonomy
                    //$term_arr = array();
                    $terms = get_terms( array(
                        'taxonomy' => $taxonomy,
                        'hide_empty' => true,
                        'number' => $cat_limit
                    ) );

                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) && is_array( $terms ) ){
                        foreach ( $terms as $term ) {
                            $term_arr[ $term->slug ] = $term->name;
                        }
                    }

                    // Store taxonomies in array
                    if ( ! in_array( $taxonomy, array( 'nav_menu', 'link_category', 'post_format', 'product_type', 'product_shipping_class' ) ) ) {
                        $tax_arr[$taxonomy] = $tax->labels->name;
                    }
                }
            }

            if ( isset( $sitepress ) ) {
                add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
            }

            // Get registered image sizes
            if ( function_exists( 'wppm_get_image_sizes' ) ) {
                $image_sizes = wppm_get_image_sizes();
            } else {
                $image_sizes = array(
                    'full' => __( 'Original', 'wppm-el' ),
                    'large' => __( 'Large', 'wppm-el' ),
                    'medium' => __( 'Medium', 'wppm-el' ),
                    'thumbnail' => __( 'Thumbnail', 'wppm-el' )
                );
            }
        // Author list
        $users = get_users();
        $user_arr = array();
        foreach ( $users as $user ) {
           $user_arr[$user->ID] = $user->display_name;
        }

        $this->start_controls_section(
            'section_query',
            [
                'label' => __('Query', 'wppm-el'),
            ]
        );

        $this->add_control(
        'post_type',
        [
        'label' => __( 'Show data from', 'wppm-el' ),
        'type' => Controls_Manager::SELECT2,
        'options' => $post_type_arr,
        'default' => ['post'],
        'multiple' => true
        ]
        );

        $this->add_control(
        'num',
        [
        'label' => __( 'Number of Posts', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '6',
        'min' => '1',
        'max' => '999',
        'step' => '1',
        ]
        );

        $this->add_control(
        'taxonomy',
        [
        'label' => __( 'Filter by Taxonomy', 'wppm-el' ),
        'type' => Controls_Manager::SELECT2,
        'options' => $tax_arr,
        'default' => ['category'],
        'multiple' => true
        ]
        );

        $this->add_control(
        'terms',
        [
        'label' => __( 'Select Terms', 'wppm-el' ),
        'type' => Controls_Manager::SELECT2,
        'options' => $term_arr,
        'default' => '',
        'multiple' => true
        ]
        );

        $this->add_control(
        'post__in',
        [
        'label' => __( 'Include only these Post/Page IDs', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide numeric IDs of pages or posts, separated by comma. E.g. 12,34,156,259', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'post__not_in',
        [
        'label' => __( 'Exclude these Post/Page IDs', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide numeric IDs of pages or posts, separated by comma. E.g. 12,34,156,259. Important: Use either include or exclude feature. Both do not work together.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'author__in',
        [
        'label' => __( 'Filter by User', 'wppm-el' ),
        'type' => Controls_Manager::SELECT2,
        'options' => $user_arr,
        'multiple' => true
        ]
        );

        $this->add_control(
        'offset',
        [
        'label' => __( 'Offset', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 999,
        'step' => 1,
        'default' => 0,
        'description' => __( 'Provide an offset number. E.g. 2. Offset is used to skip a particular number of posts from loop.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'order',
        [
        'label' => __( 'Order', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'DESC' => __( 'Descending', 'wppm-el' ),
        'ASC' => __( 'Ascending', 'wppm-el' ),
        ],
        'default' => 'DESC',
        ]
        );

        $this->add_control(
        'orderby',
        [
        'label' => __( 'Order by', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'none' => __( 'None', 'wppm-el' ),
        'ID' => __( 'ID', 'wppm-el' ),
        'author' => __( 'Author', 'wppm-el' ),
        'title' => __( 'Title', 'wppm-el' ),
        'name' => __( 'Name', 'wppm-el' ),
        'type' => __( 'Post Type', 'wppm-el' ),
        'date' => __( 'Date', 'wppm-el' ),
        'meta_value' => __( 'Meta Value', 'wppm-el' ),
        'meta_value_num' => __( 'Meta Value Num', 'wppm-el' ),
        'modified' => __( 'Last Modified', 'wppm-el' ),
        'parent' => __( 'Parent ID', 'wppm-el' ),
        'rand' => __( 'Random', 'wppm-el' ),
        'comment_count' => __( 'Comment Count', 'wppm-el' ),
        'menu_order' => __( 'Menu Order', 'wppm-el' ),
        'post__in' => __( 'Post In', 'wppm-el' ),
        'post_views' => __( 'Post Views', 'wppm-el' )
        ],
        'default' => 'date',
        ]
        );

        $this->add_control(
        'meta_key',
        [
        'label' => __( 'Show from specific meta key', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide a meta key for posts. E.g. price', 'wppm-el' ),
        'condition' => [ 'orderby' => ['meta_value','meta_value_num'] ]
        ]
        );

        $this->add_control(
        'relation',
        [
        'label' => __( 'Multiple Taxonomy Relation', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'OR' => __( 'OR', 'wppm-el' ),
        'AND' => __( 'AND', 'wppm-el' ),
        ],
        'default' => 'OR',
        'description' => __( 'Choose a taxonomy relation when multiple taxonomies are selected.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'operator',
        [
        'label' => __( 'Operator relation for multiple terms', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'IN' => __( 'IN', 'wppm-el' ),
        'NOT IN' => __( 'NOT IN', 'wppm-el' ),
        'AND' => __( 'AND', 'wppm-el' ),
        ],
        'default' => 'IN',
        'description' => __( 'Choose an operator relation between multiple terms of same taxonomy.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'year',
        [
        'label' => __( 'Year', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Filter posts by year. 4 digit year (e.g. 2011)', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'month',
        [
        'label' => __( 'Month', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Filter posts by month number (from 1 to 12)', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'week',
        [
        'label' => __( 'Week', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Filter posts by week of the year (from 0 to 53)', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'day',
        [
        'label' => __( 'Day', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Filter posts by day of the month (from 1 to 31)', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'before',
        [
        'label' => __( 'Before (Date)', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Date to retrieve posts before. E.g. January 1st, 2013', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'after',
        [
        'label' => __( 'After (Date)', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Date to retrieve posts after. E.g. January 1st, 2013', 'wppm-el' ),
        ]
        );

        $this->add_control(
        's',
        [
        'label' => __( 'Filter by Search term', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Filter posts by search term. Prepend with hyphen to exclude a term. E.g. Pillow -sofa will show all results for pillow but not sofa.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'time_custom',
        [
        'label' => __( 'Filter by time period', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::SELECT,
        'options' => [
        'none' => __( 'None', 'wppm-el' ),
        'today' => __( 'Today', 'wppm-el' ),
        'yesterday' => __( 'Yesterday', 'wppm-el' ),
        'prev_week' => __( 'Last 7 Days', 'wppm-el' ),
        'curr_month' => __( 'Current Month', 'wppm-el' ),
        'prev_month' => __( 'Previous Month', 'wppm-el' ),
        'curr_year' => __( 'Current year', 'wppm-el' ),
        'prev_year' => __( 'Previous Year', 'wppm-el' )
        ],
        'default' => 'none'
        ]
        );

        $this->add_control(
        'ignore_sticky_posts',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Ignore Sticky Posts', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'single_term_filter',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Single Post Term filtering', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Automatically show posts from similar terms on single post. NOTE: This option is useful when you are using the module as shortcode using Elementor Pro or <a href="https://wordpress.org/plugins/anywhere-elementor/">Anywhere Elementor</a> Plugin.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'author_archive_filter',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Author archive filtering', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Automatically show posts from same author when on author archive page. NOTE: This option is useful when you are using the module as shortcode using Elementor Pro or <a href="https://wordpress.org/plugins/anywhere-elementor/">Anywhere Elementor</a> Plugin.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'taxonomy_optional',
        [
        'label' => __( 'Restrict to Taxonomy', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide taxonomy names, separated by comma; to which posts shall be restricted when on single post. E.g. category, product', 'wppm-el' ),
        'condition' => [ 'single_term_filter' => ['true'] ]
        ]
        );

        $this->add_control(
        'hide_current_post',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Hide current single post', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Hide current post from post module when on single post.', 'wppm-el' ),
        'condition' => [ 'single_term_filter' => ['true'] ]
        ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_schema',
            [
                'label' => __('Schema', 'wppm-el'),
            ]
        );

        $this->add_control(
        'enable_schema',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable Schema', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Upon enabling this option, you can set schema type and properties for post elements. Schema properties can be found on schema.org website.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'container_type',
        [
        'label' => __( 'Post item container type', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'BlogPosting',
        'description' => __( 'The parent schema type of post item. E.g. Recipe, or NutritionInformation', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'container_prop',
        [
        'label' => __( 'Post item container property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'blogPost',
        'description' => __( 'The parent schema property of post item. E.g. nutrition', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'heading_prop',
        [
        'label' => __( 'Post title property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'headline mainEntityOfPage',
        'description' => __( 'The schema property of post title. E.g. name', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'excerpt_prop',
        [
        'label' => __( 'Post excerpt property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'text',
        'description' => __( 'The schema property of post excerpt. E.g. description', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'datecreated_prop',
        [
        'label' => __( 'Post creation date property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'datePublished',
        'description' => __( 'The schema property of post creation date. E.g. productionDate', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'datemodified_prop',
        [
        'label' => __( 'Post modified date property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'dateModified',
        'description' => __( 'The schema property of post creation date. E.g. releaseDate', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'publisher_type',
        [
        'label' => __( 'Post publisher type', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'Organization',
        'description' => __( 'The schema type of publisher. E.g. Person, or Organization', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'publisher_prop',
        [
        'label' => __( 'Post Publisher property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'publisher',
        'description' => __( 'The schema property of publisher. E.g. parent, or funder', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'publisher_name',
        [
        'label' => __( 'Post Publisher name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_attr( get_bloginfo( 'name' ) ),
        'description' => __( 'The name of post publisher. If Organization, your site name is taken as default value. E.g. John Doe', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'publisher_logo',
        [
        'label' => __( 'Post Publisher logo', 'wppm-el' ),
        'type' => Controls_Manager::MEDIA,
        'label_block' => true,
        'default' => ['url' => ''],
        'description' => __( 'The logo of publisher. In most cases, it will be your site logo, or can be a custom logo of your Organizatin.', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'authorbox_type',
        [
        'label' => __( 'Author container type', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'Person',
        'description' => __( 'The schema type of post Author. E.g. Person, or Brand', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'authorbox_prop',
        [
        'label' => __( 'Author container property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'author',
        'description' => __( 'The schema property of post Author. E.g. alumni', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'authorname_prop',
        [
        'label' => __( 'Author name property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'name',
        'description' => __( 'The schema property of post Author. E.g. alternateName', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'authoravatar_prop',
        [
        'label' => __( 'Author avatar property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'image',
        'description' => __( 'The schema property of Author avatar. E.g. image', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'category_prop',
        [
        'label' => __( 'Post categories property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'about',
        'description' => __( 'The schema property of post categories. E.g. genre', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'commentcount_prop',
        [
        'label' => __( 'Post comments count property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'commentCount',
        'description' => __( 'The schema property of comment count. E.g. upvoteCount', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );

        $this->add_control(
        'commenturl_prop',
        [
        'label' => __( 'Post comments URL property', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'discussionUrl',
        'description' => __( 'The schema property of comments URL. E.g. replyToUrl', 'wppm-el' ),
        'condition' => [ 'enable_schema' => ['true'] ]
        ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_advertisements',
            [
                'label' => __('Advertisements', 'wppm-el'),
            ]
        );

        $this->add_control(
        'ad_offset',
        [
        'label' => __( 'Show advertisement after every xx posts', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::NUMBER,
        'min' => '1',
        'max' => '999',
        'step' => '1',
        'description' => __( 'For example, if this number is 3, advertisements will be inserted after every multiple of 3 posts -- 3,6,9,12 and so on.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'ad_list',
        [
        'type' => Controls_Manager::REPEATER,
        'label' => __( 'Add advertisements', 'wppm-el' ),
        'default' => [],
        'fields' => [
        [
        'name' => 'ad_code',
        'label' => __( 'Advertisement Code', 'wppm-el' ),
        'type' => Controls_Manager::TEXTAREA,
        'description' => __( 'Provide advertisement code. It can be Google ad code or any similar code for advertisements.', 'wppm-el' ),
        ],

        ],
        'description' => __( 'Add advertisements to be inserted between posts. You can add any number of advertisements. The markup can contain HTML, iframe code, Google ad code, YouTube video embed, etc.', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list','tile'] ]
        ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_display',
            [
                'label' => __('Display', 'wppm-el'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'show_label' => false,
            ]
        );

        $this->add_control(
        'template',
        [
        'label' => __( 'Template Style', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::SELECT,
        'options' => [
        'grid' => __( 'Grid', 'wppm-el' ),
        'list' => __( 'List', 'wppm-el' ),
        'tile' => __( 'Tile', 'wppm-el' ),
        'ticker' => __( 'Ticker (Marquee)', 'wppm-el' ),
        'bullet-list' => __( 'Bullet List', 'wppm-el' )
        ],
        'default' => 'grid'
        ]
        );

        $this->add_control(
        'title_length',
        [
        'label' => __( 'Ticker title length', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '10',
        'min' => '1',
        'max' => '999',
        'step' => '1',
        'description' => __( 'The length in words at which post titles shall be trimmed in ticker. E.g. 10', 'wppm-el' ),
        'condition' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_control(
        'duration',
        [
        'label' => __( 'Ticker animation duration', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '15000',
        'min' => '1',
        'max' => '999999',
        'step' => '1',
        'description' => __( 'Animation duration (in ms) for ticker post titles.', 'wppm-el' ),
        'condition' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_control(
        'ticker_label',
        [
        'label' => __( 'Ticker label', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Breaking News', 'wppm-el' ),
        'description' => __( 'Provide a text label for the ticker. E.g. Latest Posts', 'wppm-el' ),
        'condition' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_responsive_control(
            'ticker_label_padding',
            [
                'label' => __( 'Label Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .ticker-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'template' => ['ticker'] ]
            ]
        );

        $this->add_responsive_control(
            'ticker_body_padding',
            [
                'label' => __( 'Ticker Content Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-ticker' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'description' => __( 'Note: Only top and bottom padding will be applied.', 'wppm-el' ),
                'condition' => [ 'template' => ['ticker'] ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ticker_label_typography',
                'label' => __( 'Ticker label Typography', 'wppm-el' ),
                'label_block' => true,
                'selector' => '{{WRAPPER}} .ticker-label',
                'condition' => [ 'template' => ['ticker'] ]
            ]
        );

        $this->add_control(
        'ticker_bg',
        [
        'label' => __( 'Ticker label background', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .ticker-label' => 'background: {{VALUE}}'],
        'condition' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_control(
        'ticker_clr',
        [
        'label' => __( 'Ticker label foreground color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .ticker-label' => 'color: {{VALUE}}'],
        'condition' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_control(
        'ticker_body_bg',
        [
        'label' => __( 'Ticker body background', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .wppm-ticker' => 'background: {{VALUE}}'],
        'condition' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_control(
        'sub_type_grid',
        [
        'label' => __( 'Sub Style', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::SELECT,
        'options' => [
        's1' => __( 'Big image + Title', 'wppm-el' ),
        's2' => __( 'Title + Big image', 'wppm-el' ),
        's3' => __( 'Title + Small image left', 'wppm-el' ),
        's4' => __( 'Title + Small image right', 'wppm-el' ),
        ],
        'default' => 's1',
        'condition' => [ 'template' => ['grid'] ],
        'description' => __( 'Select a sub style for grid temmplate.', 'wppm-el' ),
        ]
        );


        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'desktop_default' => [
                    'size' => 3
                ],
                'tablet_default' => [
                    'size' => 2
                ],
                'mobile_default' => [
                    'size' => 1
                ],
                'description' => __( 'Number of columns to show per row. E.g. 3', 'wppm-el' ),
                'selectors' => [
                            '{{WRAPPER}} .wppm:not(.posts-slider) .wppm-el-post' => 'flex-basis: calc(100% / {{size}}); max-width: calc(99.999% / {{size}});',
                             '{{WRAPPER}} .wppm:not(.posts-slider) .wppm-el-post:nth-of-type({{size}}n)' => 'border-right: 0'
                        ],
                'condition' => [
                    'template' => ['grid', 'list', 'tile']
                    ]
                ]
        );

        $rtl_css_1 = is_rtl() ? 'right: calc({{SIZE}}{{UNIT}} / 2); left: auto;' : 'left: calc({{SIZE}}{{UNIT}} / 2);';
        $this->add_responsive_control(
            'gutter_grid',
            [
                'label' => __( 'Column Spacing (Horiz)', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2);margin-right: calc(-{{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .wppm:not(.wppm-tile) .wppm-el-post' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2);padding-right: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .wppm-tile > .wppm-el-post' => 'padding: 0  calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .wppm .owl-carousel' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2);padding-right: calc({{SIZE}}{{UNIT}} / 2);',
                     '{{WRAPPER}}.sep-content .wppm-grid:not(.no-border) .wppm-el-post:after,{{WRAPPER}}.border-true .wppm-grid:not(.posts-slider) .wppm-el-post:after, {{WRAPPER}}.border-true .wppm-grid .owl-item:after,{{WRAPPER}} .wppm-list.full-border .wppm-el-post:after' => 'left: calc({{SIZE}}{{UNIT}} / 2);right: calc({{SIZE}}{{UNIT}} / 2);',
                     '{{WRAPPER}} .wppm.wppm-list.count-enabled > :before' => $rtl_css_1        ],
                'description' => __( 'Select horizontal space between columns of post module.', 'wppm-el' ),
        'condition' => [ 'template' => ['grid', 'tile', 'list'] ]
            ]
        );

        $this->add_responsive_control(
            'gutter_grid_vert',
            [
                'label' => __( 'Column Spacing (Vert)', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-el-post' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .wppm-list.content-border .wppm-el-post, {{WRAPPER}} .wppm-list.no-border .wppm-el-post, {{WRAPPER}}:not(.border-true) .wppm-grid .wppm-el-post' => 'padding-bottom: 0'
                ],
                'description' => __( 'Select vertical space between columns of post module.', 'wppm-el' ),
        'condition' => [ 'template' => ['grid', 'tile', 'list'] ]
            ]
        );

        $this->add_control(
            'equal_height',
            [
            'type' => Controls_Manager::SWITCHER,
            'label' => __( 'Equal Height Columns', 'wppm-el' ),
            'default' => '',
            'label_on' => __( 'On', 'wppm-el' ),
            'label_off' => __( 'Off', 'wppm-el' ),
            'return_value' => __( 'true', 'wppm-el' ),
            'prefix_class' => 'equal-height-',
            'description' => __( 'Enable equal height on grid columns.', 'wppm-el' ),
            'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );        

        $this->add_responsive_control(
            'img_align',
            [
                'label' => __( 'Image Alignment', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __( 'Left', 'wppm-el' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    '10' => [
                        'title' => __( 'Right', 'wppm-el' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => '1',
                'toggle' => true,
                'prefix_class' => 'row-',
                'condition' => [ 'template' => ['list'] ],
                'selectors' => ['{{WRAPPER}} .wppm-grid.list-enabled .post-img' => 'order: {{VALUE}}']
            ]
        );

        $this->add_responsive_control(
            'img_margin',
            [
                'label' => __( 'Image Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-el-post .post-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_responsive_control(
            'img_padding',
            [
                'label' => __( 'Image Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-el-post .post-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_responsive_control(
                    'list_split',
                    [
                        'label' => __( 'Image and Content ratio (%)', 'wppm-el' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ '%' ],
                        'range' => [
                            '%' => [
                                'min' => 0,
                                'max' => 80,
                                'step' => 1,
                            ]
                        ],
                        'default' => [
                            'unit' => '%',
                            'size' => 33,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wppm .post-img' => 'flex-basis: {{SIZE}}%; max-width: {{SIZE}}%',
                            '{{WRAPPER}} .wppm .entry-content' => 'flex-basis: calc(100% - {{SIZE}}%); max-width: calc(99.999% - {{SIZE}}%)',
                            '{{WRAPPER}}.sep-content-border .list-enabled .wppm-post-wrap:after' => 'left: {{SIZE}}%;',
                            '{{WRAPPER}}.sep-content-border.row-reverse .list-enabled .wppm-post-wrap:after,{{WRAPPER}}.sep-content-border.row-10 .list-enabled .wppm-post-wrap:after' => 'right: {{SIZE}}%; left: 0;'
                        ],
                        'description' => __( 'Select image width (in %) when image and content are inline.', 'wppm-el' ),
        'condition' => [ 'template' => ['list'] ]
                    ]
                );

        $this->add_control(
            'list_collapse_tablet',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'List Collapse (Tablet)', 'wppm-el' ),
                'default' => false,
                'label_on' => __( 'On', 'wppm-el' ),
                'label_off' => __( 'Off', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' ),
                'prefix_class' => 'list-collapse-tablet-',
                'description' => __( 'Use this option to collapse image and content for tablet view.', 'wppm-el' ),
                'condition' => [ 'template' => ['list'] ]
            ]
        );

        $this->add_control(
            'list_collapse_mobile',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'List Collapse (Mobile)', 'wppm-el' ),
                'default' => false,
                'label_on' => __( 'On', 'wppm-el' ),
                'label_off' => __( 'Off', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' ),
                'prefix_class' => 'list-collapse-mobile-',
                'description' => __( 'Use this option to collapse image and content for mobile view.', 'wppm-el' ),
                'condition' => [ 'template' => ['list'] ]
            ]
        );

        $this->add_control(
                    'grid_split',
                    [
                        'label' => __( 'Image and content ratio (%)', 'wppm-el' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ '%' ],
                        'range' => [
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                                'step' => 1,
                            ]
                        ],
                        'default' => [
                            'unit' => '%',
                            'size' => 33,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wppm-grid.s3 .post-img,{{WRAPPER}} .wppm-grid.s4 .post-img' => 'flex-basis: {{SIZE}}%; max-width: {{SIZE}}%;',
                            '{{WRAPPER}} .wppm-grid.s3 .post-text,{{WRAPPER}} .wppm-grid.s4 .post-text' => 'flex-basis: calc( 100% - {{SIZE}}% ); max-width: calc( 100% - {{SIZE}}% );'
                        ],
                        'description' => __( 'Select image width (in %) when image and content are inline.', 'wppm-el' ),
        'condition' => [ 'sub_type_grid' => ['s3', 's4'], 'template' => ['grid'] ]
                    ]
                );

        $this->add_control(
            'container_options',
            [
                'label' => __( 'Post Container Options', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_control(
        'content_bg',
            [
                'label' => __( 'Content Background', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                        '{{WRAPPER}} .wppm-post-wrap' => 'background: {{VALUE}}'
                ],
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Content Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-post-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'description' => __( 'Padding for the post container.', 'wppm-el' ),
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_control(
            'content_border_radius',
            [
                'label' => __( 'Content Border Radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-el-post, {{WRAPPER}} .wppm-post-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'description' => __( 'Border radius for the post container.', 'wppm-el' ),
                'condition' => [ 'template' => ['grid', 'list', 'tile'] ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_box_shadow',
                'label' => __( 'Content Box Shadow (Normal)', 'wppm-el' ),
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .wppm-grid .wppm-post-wrap, {{WRAPPER}} .wppm-tile .tile-wrap',
                'condition' => [ 'template' => ['grid', 'list', 'tile'] ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_box_shadow_hover',
                'label' => __( 'Box Shadow Hover', 'wppm-el' ),
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .wppm-post-wrap:hover,{{WRAPPER}} .wppm-tile .tile-wrap:hover',
                'condition' => [ 'template' => ['grid', 'list', 'tile'] ]
            ]
        );

        $this->add_control(
            'post_hover_animation',
            [
                'label' => __( 'Hover Animation', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                '' => __( '- Select -', 'wppm-el' ),
                'fadein' => __( 'Fade In', 'wppm-el' ),
                'rotate' => __( 'Rotate', 'wppm-el' ),
                'zoomin' => __( 'Zoom In', 'wppm-el' ),
                'zoomfade' => __( 'Zoom & Fade', 'wppm-el' ),
                'zoomrotate' => __( 'Zoom & Rotate', 'wppm-el' ),
                'rotatefade' => __( 'Rotate & Fade', 'wppm-el' ),
                ],
                'default' => '',
                'prefix_class' => 'wppm-animated post-effect-',
                'description' => __( 'Hover effect on each post item.', 'wppm-el' ),
                'condition' => [ 'template' => ['grid', 'list', 'tile', 'bullet-list'] ]
            ]
        );

        $this->add_control(
            'item_border_options',
            [
                'label' => __( 'Border Options', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_control(
            'grid_border_vert',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Side border', 'wppm-el' ),
                'default' => false,
                'label_on' => __( 'On', 'wppm-el' ),
                'label_off' => __( 'Off', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' ),
                'prefix_class' => 'border-vert-',
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $rtl_css_8 = is_rtl() ? 'border-left-width: {{SIZE}}{{UNIT}};' : 'border-right-width: {{SIZE}}{{UNIT}};';

        $this->add_responsive_control(
            'side_border_width',
            [
                'label' => __( 'Side Border Width', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-grid .wppm-el-post, .border-vert-true .wppm-list .wppm-el-post' => $rtl_css_8
                ],
                'description' => __( 'Select width for side border.', 'wppm-el' ),
                'condition' => [
                    'grid_border_vert' => 'true',
                    'template' => ['grid', 'list']
                ]
            ]
        );

        $this->add_control(
        'side_border_color',
            [
            'label' => __( 'Side Border Color', 'wppm-el' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .wppm-grid .wppm-el-post, .border-vert-true .wppm-list .wppm-el-post' => 'border-color: {{VALUE}}'],
            'condition' => [
                    'grid_border_vert' => 'true',
                    'template' => ['grid', 'list']
                ]
            ]
        );

        $this->add_control(
            'grid_border',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Bottom border', 'wppm-el' ),
                'default' => 'true',
                'label_on' => __( 'On', 'wppm-el' ),
                'label_off' => __( 'Off', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' ),
                'prefix_class' => 'border-',
                'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_control(
            'last_row_border',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Hide last item border', 'wppm-el' ),
                'default' => 'true',
                'label_on' => __( 'Yes', 'wppm-el' ),
                'label_off' => __( 'No', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' ),
                'prefix_class' => 'hide-last-border-',
                'condition' => [ 'template' => ['grid', 'list'] ],
                'description' => __( 'Check to disable last item border.', 'wppm-el' ),
            ]
        );

        $this->add_responsive_control(
            'bottom_border_width',
            [
                'label' => __( 'Bottom Border Width', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-grid:not(.no-border) .wppm-post-wrap' => 'border-bottom-width: {{SIZE}}{{UNIT}};'
                ],
                'description' => __( 'Select a bottom border width.', 'wppm-el' ),
                'condition' => [
                    'grid_border' => 'true',
                    'template' => ['grid', 'list']
                ]
            ]
        );

        $this->add_control(
        'bottom_border_color',
            [
            'label' => __( 'Bottom Border Color', 'wppm-el' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .wppm-grid:not(.no-border) .wppm-post-wrap' => 'border-bottom-color: {{VALUE}}'],
            'condition' => [
                    'grid_border' => 'true',
                    'template' => ['grid', 'list']
                ]
            ]
        );

        $this->add_responsive_control(
            'border_gap',
            [
                'label' => __( 'Border Spacing', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm:not(.wppm-tile) .wppm-post-wrap' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wppm.list-enabled .wppm-el-post:after' => 'padding-top: {{SIZE}}{{UNIT}};'
                ],
                'description' => __( 'Select a gap width between content and border.', 'wppm-el' ),
                'condition' => [
                    'grid_border' => 'true',
                    'template' => ['grid', 'list']
                ]
            ]
        );

        $this->add_control(
        'list_sep',
        [
        'label' => __( 'Separate list items by', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::SELECT,
        'options' => [
        'content-border' => __( 'Content Border', 'wppm-el' ),
        'full-border' => __( 'Full Border', 'wppm-el' )
        ],
        'default' => 'content-border',
        'prefix_class' => 'sep-',
        'description' => __( 'Choose how to separate each list item.', 'wppm-el' ),
        'condition' => [ 'template' => ['list'] ]
        ]
        );

        $this->add_control(
        'list_content_pos',
        [
        'label' => __( 'Content Position', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::SELECT,
        'options' => [
        '' => __( 'Default', 'wppm-el' ),
        'flex-start' => __( 'Top', 'wppm-el' ),
        'center' => __( 'Middle', 'wppm-el' ),
        'flex-end' => __( 'Bottom', 'wppm-el' )
        ],
        'default' => '',
        'selectors' => [
                    '{{WRAPPER}} .list-enabled .wppm-el-post' => 'align-items: {{VALUE}};',
                ],
                'condition' => [ 'template' => ['list'] ]
        ]
        );

        $this->add_responsive_control(
            'bullet_list_spacing',
            [
                'label' => __( 'List Spacing', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm.bullet-list > li' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
                'description' => __( 'Select space between list items.', 'wppm-el' ),
        'condition' => [ 'template' => ['bullet-list'] ]
            ]
        );

        $rtl_css_6 = is_rtl() ? 'padding: 0 {{SIZE}}{{UNIT}} 0 0;' : 'padding: 0 0 0 {{SIZE}}{{UNIT}};';
        $this->add_responsive_control(
            'bullet_spacing',
            [
                'label' => __( 'Bullet Spacing', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 1.25,
                    'unit' => 'em',
                ],
                'tablet_default' => [
                    'size' => 1.25,
                    'unit' => 'em',
                ],
                'mobile_default' => [
                    'size' => 1.25,
                    'unit' => 'em',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm.bullet-list .entry-title' => $rtl_css_6
                ],
                'description' => __( 'Select space between bullet and list items.', 'wppm-el' ),
        'condition' => [ 'template' => ['bullet-list'] ]
            ]
        );

        $this->add_responsive_control(
            'bullet_size',
            [
                'label' => __( 'Bullet Size', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => .25,
                    'unit' => 'em',
                ],
                'tablet_default' => [
                    'size' => .25,
                    'unit' => 'em',
                ],
                'mobile_default' => [
                    'size' => .25,
                    'unit' => 'em',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm.bullet-list .entry-title:before' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};'
                ],
                'description' => __( 'Select bullet size.', 'wppm-el' ),
        'condition' => [ 'template' => ['bullet-list'] ]
            ]
        );

        $this->add_control(
        'bullet_color',
        [
        'label' => __( 'Bullet Color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .entry-title:before' => 'background: {{VALUE}}'],
        'description' => __( 'Choose bullet color.', 'wppm-el' ),
        'condition' => [ 'template' => ['bullet-list'] ]
        ]
        );

        $this->add_control(
            'counter',
            [
            'type' => Controls_Manager::SWITCHER,
            'label' => __( 'Enable Post Count', 'wppm-el' ),
            'default' => '',
            'label_on' => __( 'On', 'wppm-el' ),
            'label_off' => __( 'Off', 'wppm-el' ),
            'return_value' => __( 'true', 'wppm-el' ),
            'description' => __( 'This will add number count before post snippet. Generally used to show trending posts.', 'wppm-el' ),
            'condition' => [ 'template' => ['grid', 'list'] ]
            ]
        );

        $this->add_control(
        'count_color',
            [
                'label' => __( 'Count Color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                        '{{WRAPPER}} .wppm-grid:not(.wppm-card).count-enabled div.entry-content:before, {{WRAPPER}} .wppm-grid.s2.count-enabled .post-text:before, {{WRAPPER}} .wppm-list.count-enabled > div:before' => 'color: {{VALUE}}'
                ],
                'condition' => [ 'template' => ['grid', 'list'], 'counter' => ['true'] ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'label' => __( 'Count Typography', 'wppm-el'),
                'selector' => '{{WRAPPER}} .wppm-grid:not(.wppm-card).count-enabled div.entry-content:before, {{WRAPPER}} .wppm-grid.s2.count-enabled .post-text:before, {{WRAPPER}} .wppm-list.count-enabled > div:before',
                'condition' => [ 'template' => ['grid', 'list'], 'counter' => ['true'] ]
            ]
        );

        $rtl_css_7 = is_rtl() ? 'padding-right:{{SIZE}}{{UNIT}}; padding-left: 0;' : 'padding-left:{{SIZE}}{{UNIT}};';
        $this->add_responsive_control(
            'count_spacing',
            [
                'label' => __( 'Count Spacing', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 32,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 16,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-grid:not(.wppm-card).count-enabled div.entry-content, {{WRAPPER}} .wppm-grid.s2.count-enabled .post-text, {{WRAPPER}} .wppm-list.count-enabled .wppm-el-post' => $rtl_css_7
                ],
                'description' => __( 'Select spacing between counter and content.', 'wppm-el' ),
                'condition' => [ 'template' => ['grid', 'list'], 'counter' => ['true'] ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tile_background',
            [
                'label' => __( 'Overlay Content', 'wppm-el' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
                'condition' => ['template' => ['tile']]
            ]
        );

        $this->start_controls_tabs( 'tabs_background' );

                $this->start_controls_tab(
                    'tab_background_normal',
                    [
                        'label' => __( 'Normal', 'elementor' )
                    ]
                );

                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'background',
                        'selector' => '{{WRAPPER}} .tile-overlay',
                    ]
                );

                $this->end_controls_tab();

                $this->start_controls_tab(
                    'tab_background_hover',
                    [
                        'label' => __( 'Hover', 'elementor' ),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'background_hover',
                        'selector' => '{{WRAPPER}} .tile-overlay:hover',
                    ]
                );

                $this->add_control(
                    'background_hover_transition',
                    [
                        'label' => __( 'Transition Duration', 'elementor' ),
                        'type' => Controls_Manager::SLIDER,
                        'default' => [
                            'size' => 0.3,
                        ],
                        'range' => [
                            'px' => [
                                'max' => 3,
                                'step' => 0.1,
                            ],
                        ],
                        'render_type' => 'ui',
                        'separator' => 'before',
                        'selectors' => [
                    '{{WRAPPER}} .tile-overlay' => 'transition: {{SIZE}}s;'
                ],
                    ]
                );

                $this->end_controls_tab();
                $this->end_controls_tabs();

        $this->add_responsive_control(
        'show_overlay',
        [
        'label' => __( 'Show Content', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::SELECT,
        'options' => [
        'always' => __( 'Always', 'wppm-el' ),
        'onhover' => __( 'On Hover', 'wppm-el' ),
        'never' => __( 'Never', 'wppm-el' ),
        ],
        'default' => 'always',
        'prefix_class' => 'show%s',
        'description' => __( 'Select content visibility.', 'wppm-el' ),
        'condition' => [ 'template' => ['tile'] ]
        ]
        );

        $this->add_control(
        'content_pos',
        [
        'label' => __( 'Content position', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        '' => __( 'Default', 'wppm-el' ),
        'top' => __( 'Top', 'wppm-el' ),
        'middle' => __( 'Middle', 'wppm-el' ),
        'bottom' => __( 'Bottom', 'wppm-el')
        ],
        'default' => 'top',
        'prefix_class' => 'content-',
        'condition' => [ 'template' => ['tile'] ]
        ]
        );

        $this->add_responsive_control(
            'overlay_width',
            [
                'label' => __( 'Content Width', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px', 'em', 'rem' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 999,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-tile .tile-overlay' => 'width: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [ 'template' => ['tile'] ]
            ]
        );

        $this->add_responsive_control(
            'overlay_height',
            [
                'label' => __( 'Content Height', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px', 'em', 'rem' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 999,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-tile .tile-overlay' => 'height: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [ 'template' => ['tile'] ]
            ]
        );

        $this->add_responsive_control(
            'overlay_padding',
            [
                'label' => __( 'Content Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .tile-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'template' => ['tile'] ]
            ]
        );

        $this->add_responsive_control(
            'overlay_margin',
            [
                'label' => __( 'Content Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .tile-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'template' => ['tile'] ]
            ]
        );

        $this->add_control(
            'overlay_border_radius',
            [
                'label' => __( 'Content Border Radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .tile-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'description' => __( 'Select border radius for overlay content.', 'wppm-el' ),
                'condition' => [ 'template' => ['tile'] ]
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_images',
            [
                'label' => __( 'Images', 'wppm-el' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'show_label' => false,
            ]
        );

        $this->add_control(
        'show_thumbnail',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Post thumbnail', 'wppm-el' ),
        'description' => __( 'Whether to show post thumbnails.', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition!' => [ 'template' => ['tile'] ]
        ]
        );

        $this->add_control(
            'img_source',
            [
            'label' => __( 'Image Source', 'wppm-el' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
            'featured' => __( 'Featured Image', 'wppm-el' ),
            'meta_box' => __( 'Meta Box', 'wppm-el' ),
            'custom_field' => __( 'Custom Field', 'wppm-el' )
            ],
            'default' => 'featured',
            'description' => __( 'Select source for the title text', 'wppm-el' ),
            ]
        );

        $this->add_control(
        'img_meta_box',
        [
        'label' => __( 'Meta box key name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide key name of meta box or options panel. E.g. my_theme_meta_box', 'wppm-el' ),
        'condition' => [ 'img_source' => ['meta_box'] ]
        ]
        );

        $this->add_control(
        'img_cust_field_key',
        [
        'label' => __( 'Custom field key name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide custom field key name. E.g. my_title', 'wppm-el' ),
        'condition' => [ 'img_source' => ['meta_box', 'custom_field'] ]
        ]
        );

        $this->add_control(
        'imglink',
        [
        'label' => __( 'Image link', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'permalink' => __( 'Permalink', 'wppm-el' ),
        'media' => __( 'Media File', 'wppm-el' ),
        'none' => __( 'None', 'wppm-el' )
        ],
        'default' => 'permalink',
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'imglightbox',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Lightbox', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition' => [ 'imglink' => ['media'] ]
        ]
        );

        $this->add_control(
        'imgwidth',
        [
        'label' => __( 'Image Width', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'min' => '10',
        'max' => '1600',
        'step' => '1',
        'description' => __( 'Image width in px (without unit). E.g. 600', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'imgheight',
        [
        'label' => __( 'Image Height', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'min' => '10',
        'max' => '1600',
        'step' => '1',
        'description' => __( 'Image height in px (without unit). E.g. 400', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'bfi',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable BFI Thumb', 'wppm-el' ),
        'default' => false,
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'If enabled, images will be resized on-the-fly. If not, you can use <a href="https://wordpress.org/plugins/otf-regenerate-thumbnails/" target="_blank">OTF Regenerate Thumbnails</a> plugin for generating image sizes as specified in these options.', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'imgcrop',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Hard Crop Images', 'wppm-el' ),
        'description' => __( 'Whether to hard crop images. Requires BFI to be enabled.', 'wppm-el' ),
        'default' => false,
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'imgquality',
        [
        'label' => __( 'Image Quality', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '80',
        'min' => '1',
        'max' => '100',
        'step' => '1',
        'description' => __( 'Image quality in range 1 to 100. E.g. 75. *Requires BFI to be enabled.', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'imggrayscale',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Convert images to grayscale', 'wppm-el' ),
        'description' => __( '*Requires BFI to be enabled.', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list', 'tile'] ]
        ]
        );

        $this->add_control(
        'show_embed',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable video embeds', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'The first video placed in post content will be shown.', 'wppm-el' ),
        'condition!' => [ 'template' => ['bullet-list','tile','ticker'] ]
        ]
        );

        $this->add_control(
        'post_format_icon',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Post format icon', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'imgsize',
        [
        'label' => __( 'Select image Size', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => $image_sizes,
        'default' => 'full',
        'description' => __( 'Select an image size. This setting will override custom image sizes and hard cropping.', 'wppm-el' ),
        'condition' => [ 'use_native_thumbs' => ['true'] ]
        ]
        );

        $this->add_control(
        'enable_captions',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Post thumbnail captions', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_responsive_control(
            'caption_margin',
            [
                'label' => __( 'Caption Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ '%', 'px', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wp-caption-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'enable_captions' => ['true'] ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'description' => __( 'Caption Typography', 'wppm-el' ),
                'selector' => '{{WRAPPER}} .wp-caption-text',
            ]
        );

        $this->add_control(
        'caption_color',
        [
        'label' => __( 'Caption text color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .wp-caption-text' => 'color: {{VALUE}}']
        ]
        );

        $this->add_control(
        'caption_bg_color',
        [
        'label' => __( 'Caption background color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .wp-caption-text' => 'background: {{VALUE}}']
        ]
        );

        $this->add_responsive_control(
            'caption_align',
            [
                'label' => __( 'Caption Align', 'wppm-el' ),
                'description' => __( '* Not applicable to Tile display style', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'wppm-el' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'wppm-el' ),
                        'icon' => 'fa fa-align-center',
                    ],

                    'right' => [
                        'title' => __( 'Right', 'wppm-el' ),
                        'icon' => 'fa fa-align-right',
                    ],

                    'justify' => [
                        'title' => __( 'Justified', 'wppm-el' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => 'right',
                'toggle' => true,
                'selectors' => ['{{WRAPPER}} .wp-caption-text' => 'text-align: {{VALUE}}']
            ]
        );

        $this->add_control(
            'thumbnail_border_radius',
            [
                'label' => __( 'Image border radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ '%', 'px', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .post-img img, {{WRAPPER}} .post-img .video-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );


        $this->add_control(
        'image_effect',
        [
        'label' => __( 'Image Effect', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'none' => __( 'None', 'wppm-el' ),
        'zoomin' => __( 'ZoomIn', 'wppm-el' ),
        'zoomout' => __( 'ZoomOut', 'wppm-el' ),
        'zoominrotate' => __( 'ZoomInRotate', 'wppm-el' ),
        'fadeout' => __( 'FadeOut', 'wppm-el' ),
        ],
        'default' => 'none',
        'description' => __( 'Select image effect on hover.', 'wppm-el' ),
        'condition' => [ 'template' => ['tile'] ]
        ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
                'section_title',
                [
                    'label' => __('Title', 'wppm-el'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'show_label' => false,
                ]
            );

        $this->add_control(
        'htag',
        [
        'label' => __( 'Title tag', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'h1' => __( 'h1', 'wppm-el' ),
        'h2' => __( 'h2', 'wppm-el' ),
        'h3' => __( 'h3', 'wppm-el' ),
        'h4' => __( 'h4', 'wppm-el' ),
        'h5' => __( 'h5', 'wppm-el' ),
        'h6' => __( 'h6', 'wppm-el' ),
        'p' => __( 'p', 'wppm-el' ),
        ],
        'default' => 'h2',
        'description' => __( 'Select html tag for post title.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'hsource',
        [
        'label' => __( 'Title Source', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'title' => __( 'Post Title', 'wppm-el' ),
        'meta_box' => __( 'Meta Box', 'wppm-el' ),
        'custom_field' => __( 'Custom Field', 'wppm-el' )
        ],
        'default' => 'title',
        'description' => __( 'Select source for the title text', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'h_meta_box',
        [
        'label' => __( 'Meta box key name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide key name of meta box or options panel. E.g. my_theme_meta_box', 'wppm-el' ),
        'condition' => [ 'hsource' => ['meta_box'] ]
        ]
        );

        $this->add_control(
        'h_cust_field_key',
        [
        'label' => __( 'Custom field key name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide custom field key name. E.g. my_title', 'wppm-el' ),
        'condition' => [ 'hsource' => ['meta_box', 'custom_field'] ]
        ]
        );

        $this->add_control(
        'heading_color',
        [
        'label' => __( 'Title heading color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .entry-title > a, {{WRAPPER}} .wppm-ticker a' => 'color: {{VALUE}}'],
        'description' => __( 'Choose color for title heading', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'heading_color_hover',
        [
        'label' => __( 'Title heading hover color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .entry-title > a:hover,{{WRAPPER}} .wppm-ticker a:hover' => 'color: {{VALUE}}'],
        'description' => __( 'Choose hover color for title heading', 'wppm-el' ),
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .entry-title,{{WRAPPER}} .wppm-ticker span',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .entry-title,{{WRAPPER}} .wppm-ticker span',
            ]
        );

        $this->add_responsive_control(
            'title_align',
            [
                'label' => __( 'Title Align', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'wppm-el' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'wppm-el' ),
                        'icon' => 'fa fa-align-center',
                    ],

                    'right' => [
                        'title' => __( 'Right', 'wppm-el' ),
                        'icon' => 'fa fa-align-right',
                    ],

                    'justify' => [
                        'title' => __( 'Justified', 'wppm-el' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => ['{{WRAPPER}} .entry-title' => 'text-align: {{VALUE}}']
            ]
        );

        $this->add_control(
        'h_length',
        [
        'label' => __( 'Title length', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'min' => '1',
        'max' => '999',
        'step' => '1',
        'description' => __( 'Provide a word length to which the title shall be trimmed. E.g. 8', 'wppm-el' ),
        ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __( 'Title Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm .entry-title, {{WRAPPER}} .wppm .js-marquee > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'description' => __( 'Select margin for post title.', 'wppm-el' )
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_excerpt',
            [
                'label' => __('Excerpt', 'wppm-el'),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
        'show_excerpt',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Post excerpt', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'psource',
        [
        'label' => __( 'Post text source', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'excerpt' => __( 'Excerpt (trimmed)', 'wppm-el' ),
        'content' => __( 'Content (with allowed tags)', 'wppm-el' ),
        'meta_box' => __( 'Meta Box', 'wppm-el' ),
        'custom_field' => __( 'Custom Field', 'wppm-el' ),
        ],
        'default' => 'excerpt',
        'description' => __( 'Select a source for post text. If chosen Excerpt, text will be stripped from custom excerpt or content. If chosen as Content, text is trimmed from post content with allowed tags.', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'allowed_tags',
        [
        'label' => __( 'Allowed tags for post text', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide allowed tags for post text when stripped from content. E.g. p,a,strong,br,span,div', 'wppm-el' ),
        'condition' => [ 'psource' => ['content'] ]
        ]
        );

        $this->add_control(
        'meta_box',
        [
        'label' => __( 'Meta box key name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide key name of meta box or options panel. E.g. my_theme_meta_box', 'wppm-el' ),
        'condition' => [ 'psource' => ['meta_box'] ]
        ]
        );

        $this->add_control(
        'content_filter',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Retain content formatting from source', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Enabling this option will retain p and br tags coming from source.', 'wppm-el' ),
        'condition' => [ 'psource' => ['custom_field','meta_box'] ]
        ]
        );

        $this->add_control(
        'cust_field_key',
        [
        'label' => __( 'Custom field key name', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide custom field key name. E.g. my_content', 'wppm-el' ),
        'condition' => [ 'psource' => ['meta_box','custom_field'] ]
        ]
        );

        $this->add_control(
        'ptag',
        [
        'label' => __( 'Post text tag', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'h1' => __( 'h1', 'wppm-el' ),
        'h2' => __( 'h2', 'wppm-el' ),
        'h3' => __( 'h3', 'wppm-el' ),
        'h4' => __( 'h4', 'wppm-el' ),
        'h5' => __( 'h5', 'wppm-el' ),
        'h6' => __( 'h6', 'wppm-el' ),
        'p' => __( 'p', 'wppm-el' ),
        'span' => __( 'span', 'wppm-el' ),
        'div' => __( 'div', 'wppm-el' ),
        ],
        'default' => 'p',
        'description' => __( 'Select html tag for post text.', 'wppm-el' ),
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'selector' => '{{WRAPPER}} .post-text',
            ]
        );$this->add_responsive_control(
            'excerpt_align',
            [
                'label' => __( 'Text Align', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'wppm-el' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'wppm-el' ),
                        'icon' => 'fa fa-align-center',
                    ],

                    'right' => [
                        'title' => __( 'Right', 'wppm-el' ),
                        'icon' => 'fa fa-align-right',
                    ],

                    'justify' => [
                        'title' => __( 'Justified', 'wppm-el' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => ['{{WRAPPER}} .post-text' => 'text-align: {{VALUE}}']
            ]
        );

        $this->add_control(
        'text_color',
        [
        'label' => __( 'Post excerpt color', 'wppm-el' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [ '{{WRAPPER}} .post-text' => 'color: {{VALUE}}']
        ]
        );

        $this->add_control(
        'excerpt_length',
        [
        'label' => __( 'Post text length', 'wppm-el' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '10',
        'min' => '1',
        'max' => '500',
        'step' => '1',
        'description' => __( 'Post text length in words. E.g. 10', 'wppm-el' ),
        ]
        );

        $this->add_responsive_control(
            'excerpt_margin',
            [
                'label' => __( 'Excerpt Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm .post-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'description' => __( 'Select margin for post excerpt.', 'wppm-el' )
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_post_meta',
            [
                'label' => __('Post Meta', 'wppm-el'),
                'tab' => Controls_Manager::TAB_STYLE,
                    'show_label' => false,
            ]
        );

        $this->add_control(
            'show_cats',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Category links', 'wppm-el' ),
                'default' => 'true',
                'label_on' => __( 'On', 'wppm-el' ),
                'label_off' => __( 'Off', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' )
            ]
        );

        $this->add_control(
            'cat_limit',
            [
                'label' => __( 'Links limit', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [ 'size' => 3 ],
                'description' => __( 'Limit category links to.', 'wppm-el' ),
                'condition' => [
                    'show_cats' => [ 'true' ]
                    ]
                ]
        );

        $rtl_css_9 = is_rtl() ? '0 auto 0 0' : '0 0 0 auto';

        $this->add_responsive_control(
            'cat_align',
            [
                'label' => __( 'Post Meta align', 'wppm-el' ),
                'description' => __( '* Not applicable when two columns are shown in post meta. i.e Cat links + Review stars. Or date meta + comments.', 'wppm-el' ),
                'label_block' => true,
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'inherit' => [
                        'title' => __( 'Left', 'wppm-el' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    '0 auto' => [
                        'title' => __( 'Center', 'wppm-el' ),
                        'icon' => 'fa fa-align-center',
                    ],

                    $rtl_css_9 => [
                        'title' => __( 'Right', 'wppm-el' ),
                        'icon' => 'fa fa-align-right',
                    ]
                ],
                'default' => 'inherit',
                'selectors' => [ '{{WRAPPER}} .meta-col:not(.col-60)' => 'margin: {{VALUE}}'],
                'toggle' => true
            ]
        );

        $this->add_control(
            'show_more_cats',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'More links dropdown', 'wppm-el' ),
                'default' => 'true',
                'label_on' => __( 'On', 'wppm-el' ),
                'label_off' => __( 'Off', 'wppm-el' ),
                'description' => __( 'Whether to show a more categories dropdown if there are more categories to display than the specified limit.', 'wppm-el' ),
                'return_value' => __( 'true', 'wppm-el' )
            ]
        );

        $this->add_control(
            'cat_link_options',
            [
                'label' => __( 'Category Link Colors', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [ 'show_cats' => ['true'] ]
            ]
        );

        $this->start_controls_tabs( 'tabs_cat_links' );

                $this->start_controls_tab(
                    'tab_cat_link_normal',
                    [
                        'label' => __( 'Normal', 'elementor' ),
                        'condition' => [ 'show_cats' => ['true'] ]
                    ]
                );

                    $this->add_control(
                    'cat_color',
                    [
                    'label' => __( 'Category links color', 'wppm-el' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [ '{{WRAPPER}} .post-cats > li > a, {{WRAPPER}} .cat-link' => 'color: {{VALUE}}; opacity: 1;'],
                    'condition' => [ 'show_cats' => ['true'] ]
                    ]
                    );

                    $this->add_control(
                    'cat_bg',
                    [
                    'label' => __( 'Category links background', 'wppm-el' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [ '{{WRAPPER}} .post-cats > li > a' => 'background: {{VALUE}}'],
                    'condition' => [ 'show_cats' => ['true'] ]
                    ]
                    );

                $this->end_controls_tab();

                $this->start_controls_tab(
                    'tab_cat_link_hover',
                    [
                        'label' => __( 'Hover', 'elementor' ),
                        'condition' => [ 'show_cats' => ['true'] ]
                    ]
                );

                $this->add_control(
                    'cat_color_hover',
                    [
                    'label' => __( 'Category links hover color', 'wppm-el' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [ '{{WRAPPER}} .post-cats > li > a:hover, {{WRAPPER}} .cat-link:hover' => 'color: {{VALUE}}'],
                    'condition' => [ 'show_cats' => ['true'] ]
                    ]
                    );

                    $this->add_control(
                    'cat_bg_hover',
                    [
                    'label' => __( 'Category links hover background', 'wppm-el' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [ '{{WRAPPER}} .post-cats > li > a:hover' => 'background: {{VALUE}}'],
                    'condition' => [ 'show_cats' => ['true'] ]
                    ]
                    );

                $this->end_controls_tab();
            $this->end_controls_tabs();

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'cat_links_typography',
                    'label' => __( 'Cat links Typography', 'wppm-el' ),
                    'label_block' => true,
                    'selector' => '{{WRAPPER}} .post-cats > li > a, {{WRAPPER}} .cat-link',
                    'condition' => [ 'show_cats' => ['true'] ]
                ]
            );

        $this->add_responsive_control(
            'cat_links_padding',
            [
                'label' => __( 'Links Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .post-cats > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'show_cats' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'cat_links_margin',
            [
                'label' => __( 'Links Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .post-cats > li > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'show_cats' => ['true'] ]
            ]
        );

        $this->add_control(
            'cat_links_zindex',
            [
                'label' => __( 'Links row z-index', 'wppm-el' ),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .meta-row.cat-row' => 'z-index: {{VALUE}};',
                ],
                'condition' => [ 'show_cats' => ['true'] ]
            ]
        );

        $this->add_control(
            'cat_links_border_radius',
            [
                'label' => __( 'Border radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .post-cats > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'show_cats' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'cat_row_margin',
            [
                'label' => __( 'Cat links row margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm .meta-row.cat-row' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'show_cats' => ['true'] ]
            ]
        );

        $this->add_control(
        'readmore',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Readmore link', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'readmore_text',
        [
        'label' => __( 'Readmore text', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Read more', 'wppm-el' ),
        'condition' => [ 'readmore' => ['true'] ]
        ]
        );

        $this->add_control(
            'readmore_options',
            [
                'label' => __( 'Readmore link', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [ 'readmore' => ['true'] ]
            ]
        );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'readmore_typography',
                    'label' => __( 'Readmore link Typography', 'wppm-el' ),
                    'label_block' => true,
                    'selector' => '{{WRAPPER}} .readmore-link',
                    'condition' => [ 'readmore' => ['true'] ]
                ]
            );

            $this->add_control(
                'rml_color',
                [
                'label' => __( 'Readmore link color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .readmore-link' => 'color: {{VALUE}}'],
                'description' => __( 'Choose foreground color for readmore link', 'wppm-el' ),
                'condition' => [ 'readmore' => ['true'] ]
                ]
            );

            $this->add_control(
                'rml_hover_color',
                [
                'label' => __( 'Readmore link hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .readmore-link:hover' => 'color: {{VALUE}}'],
                'description' => __( 'Choose foreground hover color for readmore link', 'wppm-el' ),
                'condition' => [ 'readmore' => ['true'] ]
                ]
            );

            $this->add_control(
                'rml_bg_color',
                [
                'label' => __( 'Readmore link background color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .readmore-link' => 'background: {{VALUE}}'],
                'description' => __( 'Choose background color for readmore link', 'wppm-el' ),
                'condition' => [ 'readmore' => ['true'] ]
                ]
            );

            $this->add_control(
                'rml_bg_hover_color',
                [
                'label' => __( 'Readmore link background hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .readmore-link:hover' => 'background: {{VALUE}}'],
                'description' => __( 'Choose background hover color for readmore link', 'wppm-el' ),
                'condition' => [ 'readmore' => ['true'] ]
                ]
            );

            $this->add_responsive_control(
            'rml_padding',
                [
                    'label' => __( 'Link Padding', 'wppm-el' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px','em', 'rem' ],
                    'selectors' => [
                        '{{WRAPPER}} .readmore-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => __( 'Select padding for readmore link.', 'wppm-el' ),
                'condition' => [ 'readmore' => ['true'] ]
                ]
            );

            $this->add_control(
                'rml_border_radius',
                [
                    'label' => __( 'Border radius', 'wppm-el' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px','em', 'rem' ],
                    'selectors' => [
                        '{{WRAPPER}} .readmore-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => __( 'Select border radius for readmore link.', 'wppm-el' ),
                'condition' => [ 'readmore' => ['true'] ]
                ]
            );

        $this->add_control(
        'show_author',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Author link', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'show_date',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Post date', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'date_format',
        [
        'label' => __( 'Date Format', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => get_option( 'date_format' ),
        'condition' => [ 'show_date' => ['true'] ],
        'description' => __( 'Use a valid date format for showing date. E.g. F j, Y.', 'wppm-el' )
        ]
        );

        $this->add_control(
        'show_comments',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Comments link', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'show_views',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Post Views', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Requires Post Views Counter Plugin.', 'wppm-el' )
        ]
        );

        $this->add_control(
        'show_reviews',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Reviews', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Requires WP Review Plugin.', 'wppm-el' )
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'reviews_typography',
                'label' => __( 'Review text typography', 'wppm-el' ),
                'label_block' => true,
                'selector' => '{{WRAPPER}} .review-type-percentage, {{WRAPPER}} .review-type-point',
                'condition' => [ 'show_reviews' => ['true'] ]
            ]
        );

        $this->add_control(
                'review_star_size',
                [
                    'label' => __( 'Review Stars Size', 'wppm-el' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px','em','rem' ],
                    'range' => [
                       'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'rem' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .review-result-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [ 'show_reviews' => ['true'] ]
                ]
            );

        $this->add_control(
                'star_back_color',
                [
                'label' => __( 'Stars background color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm .review-result-wrapper' => 'color: {{VALUE}} !important;'
                ],
                'condition' => [ 'show_reviews' => ['true'] ]
            ]
        );

        $this->add_control(
                'star_front_color',
                [
                'label' => __( 'Stars front color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm .review-result' => 'color: {{VALUE}} !important'
                ],
                'condition' => [ 'show_reviews' => ['true'] ]
            ]
        );

        $this->add_control(
        'show_avatar',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Author Avatar', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition!' => [ 'template' => ['tile'] ]
        ]
        );

        $this->add_control(
                'avatar_size',
                [
                    'label' => __( 'Avatar Size', 'wppm-el' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                       'px' => [
                            'min' => 16,
                            'max' => 80,
                            'step' => 1,
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .author-avatar-32' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [ 'show_avatar' => ['true'] ]
                ]
        );

        $this->add_control(
        'avatar_absolute',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Avatar Position Absolute', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'prefix_class' => 'avatar-absolute-',
        'condition' => [ 'show_avatar' => ['true'] ]
        ]
        );

        $this->add_control(
                'avatar_offset_left',
                [
                    'label' => __( 'Avatar Offset Left', 'wppm-el' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px','em', 'rem' ],
                    'range' => [
                       'px' => [
                            'min' => -9999,
                            'max' => 9999,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => -9999,
                            'max' => 9999,
                            'step' => 1,
                        ],
                        'rem' => [
                            'min' => -9999,
                            'max' => 9999,
                            'step' => 1,
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .author-avatar-32' => 'left: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [ 'avatar_absolute' => ['true'] ]
                ]
        );

        $this->add_control(
                'avatar_offset_top',
                [
                    'label' => __( 'Avatar Offset Top', 'wppm-el' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px','em', 'rem' ],
                    'range' => [
                       'px' => [
                            'min' => -9999,
                            'max' => 9999,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => -9999,
                            'max' => 9999,
                            'step' => 1,
                        ],
                        'rem' => [
                            'min' => -9999,
                            'max' => 9999,
                            'step' => 1,
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .author-avatar-32' => 'top: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [ 'avatar_absolute' => ['true'] ]
                ]
        );

        $this->add_control(
            'avatar_radius',
            [
                'label' => __( 'Avatar border radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ '%', 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .author-avatar-32, {{WRAPPER}} .author-avatar-32 img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'show_avatar' => ['true'] ]
            ]
        );       

        $this->add_responsive_control(
            'avatar_margin',
            [
                'label' => __( 'Avatar Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .author-avatar-32' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [ 'show_avatar' => ['true'] ]
            ]
        );

        $this->add_control(
        'custom_meta',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Custom Post Meta', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition!' => [ 'template' => ['ticker'] ]
        ]
        );

        $this->add_control(
        'meta_format',
        [
        'label' => __( 'Custom meta format', 'wppm-el' ),
        'type' => Controls_Manager::TEXTAREA,
        'description' => __( 'Use %1$s, %2$s, %3$s, %4$s, %5$s and %6$s for Author, Date, Updated date, Categories, Comments, and Permalink respectively. E.g. Posted by %1$s in %2$s', 'wppm-el' ),
        'language' => 'html',
        'default' => __( 'By %1$s in %4$s', 'wppm-el' ),
        'condition' => [ 'custom_meta' => ['true'] ]
        ]
        );

        $this->add_control(
        'meta_pos',
        [
        'label' => __( 'Custom Meta Position', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        '1' => __( 'Before Title', 'wppm-el' ),
        '2' => __( 'After Title', 'wppm-el' ),
        '3' => __( 'After Excerpt', 'wppm-el' ),
        ],
        'default' => '3',
        'prefix_class' => 'pos-',
        'description' => __( 'Select position for meta placement.', 'wppm-el' ),
        'condition' => [ 'custom_meta' => ['true'] ]
        ]
        );


        $this->add_control(
            'pml_options',
            [
                'label' => __( 'Post Meta text and links', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pml_typography',
                'label' => __( 'Post Meta Typography', 'wppm-el' ),
                'label_block' => true,
                'selector' => '{{WRAPPER}} .entry-meta, {{WRAPPER}} .wppm .meta-row .post-views:after, {{WRAPPER}} .wppm .meta-row .post-views, {{WRAPPER}} .wppm .meta-row .post-comment,{{WRAPPER}} .wppm .meta-row .post-comment:after'
            ]
        );

        $this->add_control(
                'pml_color',
                [
                'label' => __( 'Post meta links color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .entry-meta:not(.avatar-enabled) li:before' => 'background: {{VALUE}}'
                ],
                'description' => __( 'Choose foreground color for post meta links.', 'wppm-el' )
                ]
            );

            $this->add_control(
                'pml_hover_color',
                [
                'label' => __( 'Post meta links hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .entry-meta a:hover' => 'color: {{VALUE}}'],
                'description' => __( 'Choose foreground hover color for post meta links.', 'wppm-el' )
                ]
            );

            $this->add_responsive_control(
                'post_meta_margin',
                [
                    'label' => __( 'Post Meta Margin', 'wppm-el' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px','em', 'rem' ],
                    'selectors' => [
                        '{{WRAPPER}} .wppm .meta-row' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => __( 'Select margin for post meta.', 'wppm-el' )
                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_social',
            [
                'label' => __('Social', 'wppm-el'),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
        'sharing',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable social sharing for post modules', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' )
        ]
        );

        $this->add_control(
        'share_style',
        [
        'label' => __( 'Share style', 'wppm-el' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
        'popup' => __( 'Popup', 'wppm-el' ),
        'inline' => __( 'Inline', 'wppm-el' ),
        ],
        'default' => 'popup',
        'description' => __( 'Select a style for social sharing buttons', 'wppm-el' ),
        ]
        );

        $this->add_control(
        'share_btns',
        [
        'label' => __( 'Social sharing buttons', 'wppm-el' ),
        'type' => Controls_Manager::SELECT2,
        'options' => [
        'twitter' => __( 'Twitter', 'wppm-el' ),
        'facebook-f' => __( 'Facebook', 'wppm-el' ),
        'whatsapp' => __( 'Whatsapp', 'wppm-el' ),
        'google-plus-g' => __( 'Google Plus', 'wppm-el' ),
        'linkedin-in' => __( 'LinkedIn', 'wppm-el' ),
        //'line' => __( 'Line', 'wppm-el' ),
        'pinterest' => __( 'Pinterest', 'wppm-el' ),
        'vkontakte' => __( 'VK Ontakte', 'wppm-el' ),
        'reddit' => __( 'Reddit', 'wppm-el' ),
        'digg' => __( 'Digg', 'wppm-el' ),
        'tumblr' => __( 'Tumblr', 'wppm-el' ),
        'stumbleupon' => __( 'Stumbleupon', 'wppm-el' ),
        'yahoo' => __( 'Yahoo', 'wppm-el' ),
        'getpocket' => __( 'GetPocket', 'wppm-el' ),
        'skype' => __( 'Skype', 'wppm-el' ),
        'telegram' => __( 'Telegram', 'wppm-el' ),
        'xing' => __( 'Xing', 'wppm-el' ),
        'renren' => __( 'Ren Ren', 'wppm-el' ),
        'email' => __( 'E Mail', 'wppm-el' ),
        ],
        'default' => ['twitter', 'facebook', 'googleplus'],
        'description' => __( 'Select social share buttons. Use Ctrl + Select or Command + select for mutiple selection.', 'wppm-el' ),
        'multiple' => true
        ]
        );

        $this->add_responsive_control(
            'social_links_outer_margin',
            [
                'label' => __( 'Links outer margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm .wppm-el-sharing-container.inline' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'share_style' => ['inline'] ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_ajax',
            [
                'label' => __('Ajax Options', 'wppm-el'),
                'tab' => Controls_Manager::TAB_STYLE,
                    'show_label' => false,
            ]
        );

        $this->add_control(
        'ajaxnav',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable ajax navigation on post module', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'This will add next/prev ajax navigation for loading more posts', 'wppm-el' ),
        'condition' => [ 'template' => ['grid', 'list', 'bullet-list', 'tile'] ]
        ]
        );

        $this->add_control(
            'nav_align',
            [
                'label' => __( 'Navigation Position', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'above' => [
                        'title' => __( 'Above', 'wppm-el' ),
                        'icon' => 'fa fa-align-top',
                    ],
                    'below' => [
                        'title' => __( 'Below', 'wppm-el' ),
                        'icon' => 'fa fa-align-bottom',
                    ],
                ],
                'default' => 'below',
                'toggle' => true,
                'prefix_class' => 'navbar-',
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'nav_align_horiz',
            [
                'label' => __( 'Navigation Align', 'wppm-el' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '' => [
                        'title' => __( 'Left', 'wppm-el' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'wppm-el' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'wppm-el' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => '',
                'toggle' => true,
                'prefix_class' => 'nav-align-',
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'navigation_margin',
            [
                'label' => __( 'Navigation row margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_control(
            'nav_btn_color',
            [
                'label' => __( 'Nav button text color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a' => 'color: {{VALUE}}'
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_control(
            'nav_btn_color_hover',
            [
                'label' => __( 'Nav button text hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a:hover' => 'color: {{VALUE}}'
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_control(
            'nav_btn_bg',
            [
                'label' => __( 'Nav button text background color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a' => 'background: {{VALUE}}'
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_control(
            'nav_btn_bg_hover',
            [
                'label' => __( 'Nav button text background hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a:hover' => 'background: {{VALUE}}'
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'nav_arrow_size',
            [
                'label' => __( 'Nav arrow size', 'wppm-el' ),
                'label_block' => true,
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 12,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 12,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 12,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'nav_btn_padding',
            [
                'label' => __( 'Nav Button Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_control(
            'nav_btn_border_radius',
            [
                'label' => __( 'Nav button border radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ '%', 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-ajax-nav > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'ajaxnav' => ['true'] ]
            ]
        );

        $this->add_control(
        'nav_status',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Show ajax navigation status', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition' => [ 'template' => [ 'grid', 'list', 'bullet-list', 'tile'], 'ajaxnav' => ['true'] ]
        ]
        );

        $this->add_control(
        'nav_status_text',
        [
        'label' => __( 'Ajax navigation status text', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Provide nav status text in format %current% of %total%. The %current% and %total% will be replaced by current page and total page number. E.g. Showing %current% out of %total% pages', 'wppm-el' ),
        'condition' => [ 'template' => [ 'grid', 'list', 'bullet-list', 'tile'], 'ajaxnav' => ['true'], 'nav_status' => ['true'] ]
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'nav_status_typography',
                'label' => __( 'Status text typography', 'wppm-el' ),
                'label_block' => true,
                'selector' => '{{WRAPPER}} .nav-status',
                'condition' => [ 'ajaxnav' => ['true'], 'nav_status' => ['true'] ]
            ]
        );

        $this->add_control(
            'nav_status_color',
            [
                'label' => __( 'Nav status color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nav-status' => 'color: {{VALUE}}'
                ],
                'condition' => [ 'ajaxnav' => ['true'], 'nav_status' => ['true'] ]
            ]
        );

        $this->add_control(
        'ajaxloadmore',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable ajax loadmore on post module', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'This will add a loadmore button at the end of posts. Important: Use this option only if ajax navigation is not enabled. Both options will not work together.', 'wppm-el' ),
        'condition' => [ 'template' => ['grid', 'list', 'bullet-list', 'tile'] ]
        ]
        );

        $this->add_control(
        'loadmore_text',
        [
        'label' => __( 'Loadmore button text', 'wppm-el' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Load more', 'wppm-el' ),
        'description' => __( 'Provide custom text for the loadmore button', 'wppm-el' ),
        'condition' => [ 'template' => [ 'grid', 'list', 'bullet-list', 'tile'], 'ajaxloadmore' => ['true'] ]
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'loadmore_typography',
                'label' => __( 'Loadmore Button Typography', 'wppm-el' ),
                'label_block' => true,
                'selector' => '{{WRAPPER}} .wppm-more-link',
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_control(
            'lmb_color',
            [
                'label' => __( 'Button text color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link' => 'color: {{VALUE}}'
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_control(
            'lmb_color_hover',
            [
                'label' => __( 'Button text hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link:hover' => 'color: {{VALUE}}'
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_control(
            'lmb_bg',
            [
                'label' => __( 'Button text background color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link' => 'background: {{VALUE}}'
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_control(
            'lmb_bg_hover',
            [
                'label' => __( 'Button text background hover color', 'wppm-el' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link:hover' => 'background: {{VALUE}}'
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'lmb_padding',
            [
                'label' => __( 'Button Padding', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'lmb_margin',
            [
                'label' => __( 'Button Margin', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'lmb_border_radius',
            [
                'label' => __( 'Button border radius', 'wppm-el' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ '%', 'px','em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .wppm-more-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [ 'ajaxloadmore' => ['true'] ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_slider',
            [
                'label' => __('Slider Options', 'wppm-el'),
                'tab' => Controls_Manager::TAB_STYLE,
                    'show_label' => false,
            ]
        );

        $this->add_control(
        'enable_slider',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Enable Slider for this template', 'wppm-el' ),
        'default' => '',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'condition' => [ 'template' => ['grid','list','tile' ] ]
        ]
        );

        $this->add_responsive_control(
        'items',
        [
            'label' => __( 'Slides per view', 'wppm-el' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [
               '' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ]
            ],
            'devices' => [ 'desktop', 'tablet', 'mobile' ],
            'desktop_default' => [
                'size' => 3
            ],
            'tablet_default' => [
                'size' => 2
            ],
            'mobile_default' => [
                'size' => 1
            ],
        'description' => __( 'Provide number of slides to show per viewport. E.g. 3', 'wppm-el' ),
        'frontend_available' => true,
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_responsive_control(
        'slide_margin',
        [
            'label' => __( 'Slides Spacing', 'wppm-el' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [
               '' => [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                ]
            ],
            'devices' => [ 'desktop', 'tablet', 'mobile' ],
            'desktop_default' => [
                'size' => 24
            ],
            'tablet_default' => [
                'size' => 20
            ],
            'mobile_default' => [
                'size' => 0
            ],
        'description' => __( 'Choose space between each slide (in px). E.g. 10', 'wppm-el' ),
        'frontend_available' => true,
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'stagepadding',
        [
        'label' => __( 'Stage Padding', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 999,
        'step' => 1,
        'default' => 0,
        'description' => __( 'The left and right padding style (in px) onto stage wrapper', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'autoplay',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Auto Play', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Whether to start slider auotmatically?', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'timeout',
        [
        'label' => __( 'Autoplay Timeout', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::NUMBER,
        'min' => '1',
        'max' => '',
        'step' => '1',
        'default' => '5000',
        'description' => __( 'Time (in miliseconds), for how long slides should stay visible.', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'speed',
        [
        'label' => __( 'Animation Speed', 'wppm-el' ),
        'label_block' => true,
        'type' => Controls_Manager::NUMBER,
        'min' => '1',
        'max' => '',
        'step' => '1',
        'default' => '500',
        'description' => __( 'Provide animation speed (in miliseconds). E.g. 300', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'autoheight',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Auto Height', 'wppm-el' ),
        'default' => false,
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Whether to enable dynamic smooth height for slider?', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'loop',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Loop Animation', 'wppm-el' ),
        'default' => false,
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Whether to loop slides infinitely?', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'nav',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Prev / Next Buttons', 'wppm-el' ),
        'default' => 'true',
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Whether to show prev/next buttons?', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_control(
        'dots',
        [
        'type' => Controls_Manager::SWITCHER,
        'label' => __( 'Dots Navigation', 'wppm-el' ),
        'default' => false,
        'label_on' => __( 'On', 'wppm-el' ),
        'label_off' => __( 'Off', 'wppm-el' ),
        'return_value' => __( 'true', 'wppm-el' ),
        'description' => __( 'Whether to show dots navigation?', 'wppm-el' ),
        'condition' => [ 'enable_slider' => ['true'] ]
        ]
        );

        $this->add_responsive_control(
            'dots_size',
            [
                'label' => __( 'Dots Size', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 8,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 8,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 8,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .owl-theme .owl-dots .owl-dot span' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};'
                ],
                'description' => __( 'Select dot size.', 'wppm-el' ),
                'condition' => [ 'enable_slider' => ['true'], 'dots' => ['true'] ]
            ]
        );

        $this->add_responsive_control(
            'dots_gap',
            [
                'label' => __( 'Dots Spacing', 'wppm-el' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 6,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 6,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 6,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .owl-theme .owl-dots .owl-dot' => 'padding:{{SIZE}}{{UNIT}};'
                ],
                'description' => __( 'Select spacing between dots.', 'wppm-el' ),
                'condition' => [ 'enable_slider' => ['true'], 'dots' => ['true'] ]
            ]
        );

        $this->end_controls_section();
    }

    // Posts Shortcode
    function wppm_shortcode( $opts ) {
        extract( shortcode_atts( array(
            // WP Query Specific
            'author_name'           => null,
            'author__in'            => null,
            'cat'                   => null,
            'category_name'         => null,
            'tag'                   => null,
            'tag_id'                => null,
            'taxonomy'              => 'category',
            'relation'              => 'OR',
            'operator'              => 'IN',
            'terms'                 => '',
            'p'                     => null,
            'name'                  => null,
            'page_id'               => null,
            'pagename'              => null,
            'post__in'              => null,
            'post__not_in'          => null,
            'post_type'             => 'post',
            'post_status'           => 'publish',
            'num'                   => 6,
            'offset'                => 0,
            'ignore_sticky_posts'   => false,
            'order'                 => 'DESC',
            'orderby'               => 'date',
            'year'                  => null,
            'monthnum'              => null,
            'w'                     => null,
            'day'                   => null,
            'meta_key'              => null,
            'meta_value'            => null,
            'meta_value_num'        => null,
            'meta_compare'          => '=',
            's'                     => null,
            'cat_limit'             => 3,
            'show_more_cats'        => true,
            'time_custom'           => 'none',

            // Date parameters
            'year'                  => '',
            'month'                 => '',
            'week'                  => '',
            'day'                   => '',
            'before'                => '', // Date before
            'after'                 => '', // Date after
            'date_query'            => null,

            'single_term_filter'    => false,
            'author_archive_filter' => false,
            'taxonomy_optional '    => '',
            'hide_current_post'     => false,
            'blog_id'               => null,
            'template'              => 'grid',
            'masonry'               => false,
            'sub_type'              => 's1',
            'sub_type_grid'         => 's1',
            'list_split'            => '25',
            'grid_split'            => '33',
            'list_sep'              => 'content-border',
            'content_left'          => false,
            'counter'               => false,
            'mobile_wide'           => false,
            'circle_img'            => false,
            'columns'               => '3',
            'columns_tablet'        => '2',
            'columns_mobile'        => '1',
            'excerpt_length'        => '10',
            'h_length'              => '',
            'readmore'              => false,
            'readmore_text'         => __( 'Read more', 'wppm-el' ),
            'show_cats'             => 'true',
            'show_author'           => 'true',
            'show_date'             => 'true',
            'show_excerpt'          => 'true',
            'show_comments'         => 'true',
            'show_views'            => 'true',
            'show_reviews'          => 'true',
            'show_thumbnail'        => 'true',
            'post_format_icon' => 'true',
            'show_avatar'           => false,
            'use_native_thumbs'     => false,
            'enable_captions'       => false,
            'imgsize'               => 'full',
            'imgwidth'              => '',
            'imgheight'             => '',
            'imgcrop'               => false,
            'bfi'                   => false,
            'imgquality'            => '80',
            'imglink'               => 'permalink',
            'imglightbox'           => false,
            'imggrayscale'          => false,
            'date_format'           => '',
            'htag'                  => 'h2',
            'ptag'                  => 'p',
            'hsource'               => 'title',
            'h_meta_box'            => '',
            'h_cust_field_key'      => '',
            'img_source'            => 'feat',
            'img_meta_box'          => '',
            'img_cust_field_key'    => '',
            'content_pos'           => 'bl',
            'show_overlay'          => 'always',
            'image_effect'          => 'none',

            // Slider params
            'slider_type'           => 'grid',
            'enable_slider'         => '',
            'items'                 => 3,
            'items_tablet'          => 2,
            'items_mobile'          => 1,
            'loop'                  => 'false',
            'speed'                 => '300',
            'slide_margin'          => 24,
            'slide_margin_tablet'   => 20,
            'slide_margin_mobile'   => 0,
            'autoplay'              => 'true',
            'timeout'               => '5000',
            'autoheight'            => 'false',
            'nav'                   => 'true',
            'dots'                  => 'false',
            'animatein'             => false,
            'animateout'            => false,
            'stagepadding'          => 0,
            'data_props'            => false,

            // Schema Params
            'enable_schema'         => false,
            'container_type'        => 'BlogPosting',
            'container_prop'        => 'blogPost',
            'heading_prop'          => 'headline mainEntityOfPage',
            'excerpt_prop'          => 'text',
            'datecreated_prop'      => 'datePublished',
            'datemodified_prop'     => 'dateModified',
            'publisher_type'        => 'Organization',
            'publisher_prop'        => 'publisher',
            'publisher_name'        => esc_attr( get_bloginfo( 'name' ) ),
            'publisher_logo'        => plugin_dir_url( __FILE__ ) . 'assets/images/wppm.svg',
            'authorbox_type'        => 'Person',
            'authorbox_prop'        => 'author',
            'authorname_prop'       => 'name',
            'authoravatar_prop'     => 'image',
            'category_prop'         => 'about',
            'commentcount_prop'     => 'commentCount',
            'commenturl_prop'       => 'discussionUrl',
            'ratingbox_type'        => 'Rating',
            'rating_prop'           => 'ratingValue',

            // Misc
            'ext_link'              => false,
            'ajaxnav'               => false,
            'nav_status'            => false,
            'nav_status_text'       => '%current% of %total%',
            'ajaxloadmore'          => false,
            'loadmore_text'         => __( 'Load more', 'wppm-el' ),
            'sharing'               => false,
            'share_btns'            => '',
            'share_style'           => 'popup',
            'show_embed'            => false,
            'custom_meta'           => false,
            'meta_format'           => '',
            'meta_pos'              => 1,
            'psource'               => 'excerpt',
            'meta_box'              => '',
            'cust_field_key'        => '',
            'allowed_tags'          => 'p,br,a,em,i,strong,b',
            'content_filter'        => false,
            'ad_list'               => '',
            'ad_offset'             => '3',

            // News Ticker
            'title_length'      => '10',
            'ticker_label'      => __( 'Breaking News', 'wppm-el' ),
            'duration'          => 15000,
            'ticker_clr'        => '',
            'ticker_bg'         => ''
        ), $opts ) );

        // Filter terms for single posts
        if ( is_single() && ! is_page() ) {
            if ( $single_term_filter ) {
                global $post;
                $taxonomy = isset($taxonomy_optional) && '' != $taxonomy_optional ? explode(',', $taxonomy_optional) : ( isset( $taxonomy ) && is_array( $taxonomy ) && ! empty( $taxonomy ) ? $taxonomy : array( 'category' ) );

                if (isset($taxonomy) && is_array($taxonomy)) {
                    foreach ($taxonomy as $tax) {
                        $post_terms = get_the_terms($post->id, $tax);
                        if (isset($post_terms) && is_array($post_terms)) {
                            foreach ($post_terms as $t) {
                                $terms[] = $t->slug;
                            }
                        }
                    }
                }
            }

            if ( $hide_current_post ) {
                if ( '' != $post__not_in ) {
                    $post__not_in .= ',' . get_the_id();
                } else {
                    $post__not_in = get_the_id();
                }
                $atts['post__not_in'] = $post__not_in;
            }
        }

        // Sanitize WP Query args
        $author__in                 = isset( $author__in ) ? $author__in : null;
        $post__in                   = $post__in ? explode( ',', $post__in ) : null;
        $post__not_in               = $post__not_in ? explode( ',', $post__not_in ) : null;
        $terms                      = isset ( $terms ) ? $terms : null;
        $post_type                  = isset ( $post_type ) ? $post_type : null;
        $taxonomy                   = isset ( $taxonomy ) ? $taxonomy : null;
        $tax_query                  = null;

        if ( $taxonomy && $terms ) {
            $tax_query = array( 'relation' => $relation );

            if ( is_array( $taxonomy ) ) {
                foreach( $taxonomy as $tax ) {
                    $tax_query[] = array(
                        'taxonomy'  => $tax,
                        'field'     => 'slug',
                        'terms'     => $terms,
                        'operator'  => $operator // Allowed values AND, IN, NOT IN
                    );
                }
            }
        }


        // Date Params
        if ( $year || $month || $week || $day || $before || $after || $time_custom ) {
            $date_arr = array();
            if ( 'none' !== $time_custom ) {
                $t = date('d-m-Y');
                $today = strtolower( date( "d", strtotime($t) ) );
                $yesterday = intval($today - 1);
                $prev_week = date('Y-m-d', strtotime('-7 days'));
                $curr_month = date('m');
                $curr_year = date('Y');
                
                if ( 'today' == $time_custom ) {
                    $date_arr['day'] = $today;
                }
                if ( 'yesterday' == $time_custom ) {
                    $date_arr['day'] = $yesterday;
                }
                if ( 'prev_week' == $time_custom ) {
                    $date_arr['after'] = $prev_week;
                }
                if ( 'curr_month' == $time_custom ) {
                    $date_arr['month'] = $curr_month;
                    $date_arr['year'] = $curr_year;
                }
                if ( 'prev_month' == $time_custom ) {
                    $date_arr['month'] = intval( $curr_month - 1 );
                }
                if ( 'curr_year' == $time_custom ) {
                    $date_arr['year'] = $curr_year;
                }
                if ( 'prev_year' == $time_custom ) {
                    $date_arr['year'] = intval( $curr_year - 1 );
                }
            }

            if ( $year ) {
                $date_arr['year'] = $year;
            }

            if ( $month ) {
                $date_arr['month'] = $month;
            }

            if ( $week ) {
                $date_arr['week'] = $week;
            }

            if ( $day ) {
                $date_arr['day'] = $day;
            }               

            if ( $before ) {
                $date_arr['before'] = $before;
            }

            if ( $after ) {
                $date_arr['after'] = $after;
            }

            $date_query = array( $date_arr  );
        }

        // Author archive filtering
        if ( $author_archive_filter && is_author() ) {
            $author = get_queried_object();
            $author_name = $author->user_nicename;
        } else {
            $author_name = null;
        }

        // Allowed args in WP Query
        $custom_args = array(
            'author_name'           => $author_name,
            'author__in'            => $author__in,
            'cat'                   => $cat,
            'category_name'         => $category_name,
            'tag'                   => $tag,
            'tag_id'                => $tag_id,
            'tax_query'             => $tax_query,
            'p'                     => $p,
            'name'                  => $name,
            'page_id'               => $page_id,
            'pagename'              => $pagename,
            'post__in'              => $post__in,
            'post__not_in'          => $post__not_in,
            'post_type'             => $post_type,
            'post_status'           => $post_status,
            'posts_per_page'        => $num,
            'offset'                => $offset,
            'ignore_sticky_posts'   => $ignore_sticky_posts,
            'order'                 => $order,
            'orderby'               => $orderby,
            'meta_key'              => $meta_key,
            'meta_value'            => $meta_value,
            'meta_value_num'        => $meta_value_num,
            's'                     => $s,
            'date_query'            => $date_query
        );

        $new_args = array();

        // Set args which are provided by user
        foreach ( $custom_args as $key => $value ) {
            if ( isset( $value ) )
                $new_args[ $key ] = $value;
        }

        // Switch to blog id if multisite
        if ( is_multisite() ) {
            switch_to_blog( $blog_id );
        }

        $custom_query = new WP_Query( $new_args );

        // Set global count for ajax post container ID
        if ( isset( $GLOBALS['wppm_ajax_container_count'] ) ) {
            $GLOBALS['wppm_ajax_container_count']++;
        }
        else {
            $GLOBALS['wppm_ajax_container_count'] = 0;
        }

        // Start the loop
        if ( $custom_query->have_posts() ) :

            // Limit image dimensions between 20px to 4000px
            /*$imgwidth = (int)$imgwidth < 20 ? 20 : $imgwidth;
            $imgwidth = (int)$imgwidth > 4000 ? 4000 : $imgwidth;
            $imgheight = (int)$imgheight < 20 ? 20 : $imgheight;
            $imgheight = (int)$imgheight > 4000 ? 4000 : $imgheight;*/

            // Publisher logo
            $publisher_logo = wp_get_attachment_image_src ( $publisher_logo );
            if ( isset ( $publisher_logo ) && is_array( $publisher_logo ) ) {
                $publisher_logo = $publisher_logo[0];
            }

            // Set default template
            if ( '' == $template ) {
                $template = 'grid';
            }

            // Set default heading and p tags
            $htag = $htag == '' ? 'h2' : $htag;
            $ptag = $ptag == '' ? 'p' : $ptag;

            // Set sub template value
            /*if ( ( $template != 'tile' && $template != 'slider' ) && isset( ${ 'sub_type_' . $template } ) ) {*/
                $sub_type = $sub_type_grid;
            //}

            if ( $allowed_tags ) {
                $tags_arr = explode( ',', $allowed_tags );
                if ( isset( $tags_arr ) && is_array( $tags_arr ) ) {
                    $allowed_tags = '';
                    foreach( $tags_arr as $tag ) {
                        $allowed_tags .= '<' . $tag . '>';
                    }
                }
            }

            // Slider preparation
            if ( $enable_slider ) {
                $outp = '';
                $count = 0;
                $protocol = is_ssl() ? 'https' : 'http';

                $params = array(
                    'items'                     => $items,
                    'items_mobile'              => $items_mobile,
                    'items_tablet'              => $items_tablet,
                    'loop'                      => esc_attr( $loop ),
                    'slide_margin'              => $slide_margin,
                    'slide_margin_tablet'       => $slide_margin_tablet,
                    'slide_margin_mobile'       => $slide_margin_mobile,
                    'autoplay'                  => esc_attr( $autoplay ),
                    'timeout'                   => esc_attr( $timeout ),
                    'autoheight'                => esc_attr( $autoheight ),
                    'nav'                       => esc_attr( $nav ),
                    'dots'                      => esc_attr( $dots ),
                    'speed'                     => esc_attr( $speed ),
                    'animatein'                 => esc_attr( $animatein ),
                    'animateout'                => esc_attr( $animateout ),
                    'stagepadding'              => $stagepadding
                );

                $json = json_encode( $params, JSON_FORCE_OBJECT );

                $slider_id = 'slider-' . rand( 2, 400000 );
                $outp = sprintf( '<div%s class="wppm wppm-%s%s owl-wrap posts-slider%s%s" data-params=\'%s\'><div class="owl-carousel owl-loading" id="%s">',
                    $enable_schema ? ' itemscope="itemscope" itemtype="' . $protocol . '://schema.org/Blog"' : '',
                    'card' == $template || 'list' == $template ? 'grid' : $template,
                    'list' == $template ? ' list-enabled' : '',
                    ' ' . esc_attr( $sub_type ),
                    $counter ? ' count-enabled' : '',
                    $json,
                    $slider_id
                );
            }

            ob_start();

            $template_path = apply_filters( 'wppm_widget_template_path',  '/wppm-templates/' );

            if ( locate_template( $template_path . esc_attr( $template ) . '.php' ) ) {
                require( get_stylesheet_directory() . $template_path . esc_attr( $template ) . '.php' );
            }

            else {
                require( dirname( __FILE__ ) . $template_path . esc_attr( $template ) . '.php' );
            }

            $out = ob_get_contents();

            ob_end_clean();

            wp_reset_query();
            wp_reset_postdata();

            if ( is_multisite() ) {
                restore_current_blog();
            }

            if ( $enable_slider ) {
                $outp .= $out . '</div></div>';
                return $outp;
            } else {
                return $out;
            }
        else :
            return __( 'No posts found matching your criteria. Please modify Query parameters to show posts.', 'wppm-el' );
        endif;
    }

    protected function render() {
        $settings = $this->get_settings();
        echo $this->wppm_shortcode( $settings );
    }

    protected function content_template() {

    }
}