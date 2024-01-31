<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocs\Shortcodes\CategoryBox;

class CategoryBoxTwo extends CategoryBox {
    protected $layout_class = 'layout-2';
    protected $is_pro       = true;
    /**
     * Summary of get_id
     * @return string
     */
    public function get_name() {
        return 'betterdocs_category_box_2';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'column'                   => $this->settings->get( 'column_number', 3 ),
            'nested_subcategory'       => $this->settings->get( 'nested_subcategory', false ),
            'terms'                    => '',
            'terms_order'              => $this->settings->get( 'terms_order', 'ASC' ),
            'terms_orderby'            => $this->settings->get( 'terms_orderby', 'betterdocs_order' ),
            'kb_slug'                  => '',
            'multiple_knowledge_base'  => false,
            'disable_customizer_style' => false,
            'title_tag'                => 'h2',
            'show_description'         => (bool) $this->customizer->get( 'betterdocs_doc_page_cat_desc', false ),
            'show_icon'                => true
        ];
    }

    public function header_sequence( $_layout_sequence, $layout, $widget_type, $_defined_vars ) {
        $_new_layout_sequence = ['category_icon', [
            'class'    => 'betterdocs-category-title-counts',
            'sequence' => ['category_title', 'category_description', 'category_counts']
        ]];

        return $_new_layout_sequence;
    }

    /**
     * Summary of render
     *
     * @param mixed $atts
     * @param mixed $content
     * @return mixed
     */
    public function render( $atts, $content = null ) {
        parent::render( $atts, $content );
    }

    public function view_params() {
        $_view_params = [
            'show_icon' => $this->attributes['show_icon']
        ];

        return $this->merge( parent::view_params(), $_view_params );
    }
}
