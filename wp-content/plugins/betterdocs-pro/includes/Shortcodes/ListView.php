<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocsPro\Shortcodes\CategoryBoxTwo;

class ListView extends CategoryBoxTwo {
    protected $layout_class = 'layout-3';
    protected $is_pro = true;

    /**
     * A list of deprecated attributes
     * @var array
     */
    protected $deprecated_attributes = [
        'kb_description' => 'show_description'
    ];

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_list_view';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'taxonomy'                 => 'doc_category',
            'show_description'         => (bool) $this->customizer->get( 'betterdocs_doc_page_cat_desc', false ),
            'nested_subcategory'       => (bool) $this->settings->get( 'nested_subcategory', false ),
            'terms'                    => '',
            'kb_slug'                  => '',
            'terms_order'              => $this->settings->get( 'terms_order', 'ASC' ),
            'terms_orderby'            => $this->settings->get( 'terms_orderby', 'betterdocs_order' ),
            'show_icon'                => true,
            'multiple_knowledge_base'  => false,
            'disable_customizer_style' => false,
            'title_tag'                => 'h2'
        ];
    }

    public function view_params() {
        $_view_params = [
            'wrapper_attr' => [
                'class' => ['betterdocs-category-list-view-wrapper']
            ]
        ];

        return $this->merge( parent::view_params(), $_view_params );
    }
}
