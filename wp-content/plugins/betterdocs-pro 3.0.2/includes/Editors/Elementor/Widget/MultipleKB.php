<?php

namespace WPDeveloper\BetterDocsPro\Editors\Elementor\Widget;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use ElementorPro\Base\Base_Widget_Trait;
use WPDeveloper\BetterDocsPro\Traits\MKB;
use WPDeveloper\BetterDocs\Editors\Elementor\Traits\TemplateQuery;
use WPDeveloper\BetterDocs\Editors\Elementor\Widget\Basic\CategoryBox;

class MultipleKB extends CategoryBox {
    use MKB;
    use Base_Widget_Trait;
    use TemplateQuery;

    public function get_name() {
        return 'betterdocs-multiple-kb';
    }

    public function get_title() {
        return __( 'BetterDocs Multiple KB', 'betterdocs-pro' );
    }

    public function get_icon() {
        return 'betterdocs-icon-category-box';
    }

    public function get_categories() {
        return ['betterdocs-elements', 'docs-archive'];
    }

    public function get_keywords() {
        return [
            'knowledgebase',
            'knowledge Base',
            'documentation',
            'doc',
            'kb',
            'betterdocs-pro',
            'docs',
            'category-box'
        ];
    }

    public function get_custom_help_url() {
        return 'https://betterdocs.co/docs/multiple-knowledge-bases-elementor/';
    }

    /**
     * Query  Controls!
     * @source includes/elementor-helper.php
     */
    public function betterdocs_do_action(){
        do_action('betterdocs/elementor/widgets/query', $this, 'knowledge_base');
    }

    public function view_params() {
        $settings = &$this->attributes;

        $terms_query = [
            'taxonomy' => 'knowledge_base',
            'order'    => $settings['order'],
            'orderby'  => $settings['orderby'],
            'offset'   => $settings['offset'],
            'number'   => $settings['box_per_page']
        ];

        if ( 'betterdocs_order' === $settings['orderby'] ) {
            $terms_query['meta_key'] = 'kb_order';
            $terms_query['orderby']  = 'meta_value_num';
            $terms_query['order']    = 'ASC';
        }

        if ( $settings['include'] ) {
            $terms_query['include'] = array_diff( $settings['include'], (array) $settings['exclude'] );
        }

        if ( $settings['exclude'] ) {
            $terms_query['exclude'] = $settings['exclude'];
        }

        $_parent_params = parent::view_params();
        $_parent_params[ 'term_icon_meta_key' ] = 'knowledge_base_image-id';
        unset( $_parent_params[ 'terms_query_args' ] );

        $_view_params['terms_query_args'] = $this->betterdocs( 'query' )->terms_query( $terms_query  );
        return $this->merge( $_parent_params, $_view_params );
    }

    public function render_callback(){
        $multiple_kb_status = betterdocs()->editor->get( 'elementor' )->multiple_kb_status();

        if ( $multiple_kb_status != true ) {
            betterdocs()->views->get( 'admin/notices/enable-kb' );
            return;
        }

        parent::render_callback();
    }
}
