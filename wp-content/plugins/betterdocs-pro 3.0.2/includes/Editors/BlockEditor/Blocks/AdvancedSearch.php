<?php


namespace WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks;

use WPDeveloper\BetterDocs\Editors\BlockEditor\Blocks\SearchForm;


class AdvancedSearch extends SearchForm {
    protected $frontend_scripts = [
        'betterdocs-pro'
    ];

    protected $editor_scripts = [
        'advanced-search'
    ];

    public function get_default_attributes() {
        return [
            'blockId'           => '',
            'placeholderText'   => __( 'Search', 'betterdocs-pro' ),
            'popularSearchText' => __('Popular Search','betterdocs-pro'),
            'categorySearch'    => false,
            'searchButton'      => false,
            'popularSearch'     => false
        ];
    }

    public function view_params() {
        $settings = &$this->attributes;

        $_shortcode_attributes = [
            'placeholder'            => esc_html( $settings['placeholderText'] ),
            'popular_search_title'   => esc_html( $settings['popularSearchText'] ),
            'category_search'        => $settings['categorySearch'],
            'search_button'          => $settings['searchButton'],
            'popular_search'         => $settings['popularSearch'],
        ];

        return [
            'shortcode_attr' => $_shortcode_attributes
        ];
    }

}
