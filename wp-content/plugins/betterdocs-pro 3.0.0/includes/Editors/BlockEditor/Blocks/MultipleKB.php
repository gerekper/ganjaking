<?php
namespace WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks;

use WPDeveloper\BetterDocs\Editors\BlockEditor\Block;

class MultipleKB extends Block {

    public $is_pro = true;

    protected $editor_scripts = [
        'betterdocs-pro-blocks-editor'
    ];

    protected $editor_styles = [
        'betterdocs-fontawesome',
        'betterdocs-pro-blocks-editor',
        'betterdocs-blocks-category-box'
    ];

    protected $frontend_styles = [
        'betterdocs-fontawesome',
        'betterdocs-blocks-category-box'
    ];

    /**
     * unique name of block
     * @return string
     */
    public function get_name() {
        return 'multiple-kb';
    }

    public function get_default_attributes() {
        return [
            'blockId'           => '',
            'categories'        => [],
            'includeCategories' => '',
            'excludeCategories' => '',
            'boxPerPage'        => 9,
            'orderBy'           => 'name',
            'order'             => 'asc',
            'layout'            => 'default',
            'showIcon'          => true,
            'showTitle'         => true,
            'titleTag'          => 'h2',
            'showCount'         => true,
            'prefix'            => '',
            'suffix'            => __( 'articles', 'betterdocs' ),
            'suffixSingular'    => __( 'article', 'betterdocs' ),
            'colRange'          => 3,
            'TABcolRange'       => 2,
            'MOBcolRange'       => 1
        ];
    }

    public function view_params() {
        $attributes = &$this->attributes;

        $terms_object = [
            'taxonomy'   => 'knowledge_base',
            'order'      => $attributes['order'],
            'orderby'    => $attributes['orderBy'],
            'number'     => isset( $attributes['boxPerPage'] ) ? $attributes['boxPerPage'] : 5,
            'hide_empty' => true
        ];

        if ( 'kb_order' === $attributes['orderBy'] ) {
            $terms_object['meta_key'] = 'kb_order';
            $terms_object['orderby']  = 'meta_value_num';
        }

        $includes = $this->string_to_array( $attributes['includeCategories'] );
        $excludes = $this->string_to_array( $attributes['excludeCategories'] );

        if ( ! empty( $includes ) ) {
            $terms_object['include'] = array_diff( $includes, (array) $excludes );
        }

        if ( ! empty( $excludes ) ) {
            $terms_object['exclude'] = $excludes;
        }

        $_wrapper_classes = [
            'betterdocs-category-box-wrapper',
            'betterdocs-category-list-view-wrapper',
            'betterdocs-multiple-kb-list-wrapper',
            'betterdocs-multiple-kb-wrapper',
            'betterdocs-pro'
        ];

        $_inner_wrapper_classes = [
            'betterdocs-category-box-inner-wrapper',
            'layout-flex',
            $attributes['layout'] === 'default' ? 'layout-1' : $attributes['layout'],
            "betterdocs-column-" . $attributes['colRange'],
            "betterdocs-column-tablet-" . $attributes['TABcolRange'],
            "betterdocs-column-mobile-" . $attributes['MOBcolRange']
        ];

        $wrapper_attr = [
            'class' => $_wrapper_classes
        ];
        $inner_wrapper_attr = [
            'class'               => $_inner_wrapper_classes,
            'data-column_desktop' => $attributes['colRange'],
            'data-column_tab'     => $attributes['TABcolRange'],
            'data-column_mobile'  => $attributes['MOBcolRange']
        ];

        $_params = [
            'wrapper_attr'            => $wrapper_attr,
            'inner_wrapper_attr'      => $inner_wrapper_attr,
            'terms_query_args'        => $terms_object,
            'widget_type'             => 'category-box',
            'multiple_knowledge_base' => false,
            'kb_slug'                 => '',
            'nested_subcategory'      => false,
            'show_header'             => true,
            'show_description'        => false
        ];

        return $_params;
    }

    public function filter_header_sequence( $_layout_sequence, $layout, $widget_type, $_defined_vars ) {
        $_new_layout_sequence = ['category_icon', [
            'class'    => 'betterdocs-category-title-counts',
            'sequence' => ['category_title', 'category_description', 'category_counts']
        ]];

        return $_new_layout_sequence;
    }

    public function render( $attributes, $content ) {
        add_filter( 'betterdocs_header_layout_sequence', [$this, 'filter_header_sequence'], 10, 4 );
        $this->views( 'layouts/base' );
        remove_filter( 'betterdocs_header_layout_sequence', [$this, 'filter_header_sequence'], 10 );
    }
}
