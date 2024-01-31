<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocsPro\Traits\MKB;
use WPDeveloper\BetterDocs\Shortcodes\CategoryBox;

class MultipleKB extends CategoryBox {
    use MKB;

    protected $is_pro = true;

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_multiple_kb';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'terms'                    => '',
            'title_tag'                => 'h2',
            'disable_customizer_style' => false,
            'column'                   => $this->settings->get( 'column_number' ),
            'show_description'         => (bool) $this->customizer->get( 'betterdocs_mkb_desc', false ),
            'terms_order'              => $this->settings->get( 'terms_order', 'ASC' ),
            'terms_orderby'            => $this->settings->get( 'terms_orderby', 'betterdocs_order' ),
            'show_icon'                => true
        ];
    }

    /**
     * Summary of render
     *
     * @param mixed $atts
     * @param mixed $content
     * @return mixed
     */
    public function render( $atts, $content = null ) {
        add_filter( 'betterdocs_term_permalink', [$this, 'term_permalink'], 10, 3 );

        $this->attributes['taxonomy']                = 'knowledge_base';
        $this->attributes['nested_subcategory']      = false;
        $this->attributes['multiple_knowledge_base'] = true;
        $this->attributes['kb_slug']                 = '';

        parent::render( $atts, $content );

        remove_filter( 'betterdocs_term_permalink', [$this, 'term_permalink'], 10 );
    }

    public function view_params() {
        $_view_params = [
            'wrapper_attr' => [
                'class' => ['betterdocs-multiple-kb-wrapper']
            ],
            'show_icon' => $this->attributes['show_icon']
        ];

        return $this->merge( parent::view_params(), $_view_params );
    }
}
