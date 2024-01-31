<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocsPro\Traits\MKB;

class MultipleKBList extends ListView {
    use MKB;

    protected $is_pro = true;

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_multiple_kb_list';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'terms'                    => '',
            'disable_customizer_style' => 'false',
            'title_tag'                => 'h2',
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
        $this->attributes['kb_slug']                 = '';
        $this->attributes['hide_empty']              = true;
        $this->attributes['parent']                  = 0;
        $this->attributes['multiple_knowledge_base'] = true;

        parent::render( $atts, $content );

        remove_filter( 'betterdocs_term_permalink', [$this, 'term_permalink'], 10 );
    }

    public function view_params() {
        $_view_params = [
            'wrapper_attr'     => [
                'class' => ['betterdocs-multiple-kb-list-wrapper']
            ],
            'show_icon' => $this->attributes['show_icon']
        ];

        return $this->merge( parent::view_params(), $_view_params );
    }
}
