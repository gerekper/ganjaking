<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocsPro\Traits\MKB;
use WPDeveloper\BetterDocsPro\Shortcodes\CategoryBoxTwo;

class MultipleKBTwo extends CategoryBoxTwo {
    use MKB;

    protected $is_pro = true;
    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_multiple_kb_2';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'column'                   => $this->settings->get( 'column_number' ),
            'terms'                    => '',
            'disable_customizer_style' => false,
            'title_tag'                => 'h2',
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
        $this->attributes['terms_order']             = 'ASC';
        $this->attributes['terms_orderby']           = $this->settings->get( 'alphabetically_order_term' ) ? 'name' : 'slug';
        $this->attributes['multiple_knowledge_base'] = true;
        $this->attributes['kb_slug']                 = '';
        $this->attributes['show_description']        = false;

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
