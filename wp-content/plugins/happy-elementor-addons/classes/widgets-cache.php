<?php
namespace Happy_Addons\Elementor;

use Elementor\Core\DocumentTypes\Post;

defined( 'ABSPATH' ) || die();

class Widgets_Cache {

    const OPTION_KEY = 'happyaddons_elements_cache';

    const META_KEY = '_happyaddons_elements_cache';

    protected $post_id = 0;

    protected $elementor_data = null;

    protected $is_built_with_elementor = false;

    protected $is_published = false;

    public function __construct( $post_id = 0, $data = null ) {
        if( ! $post_id ) {
            return;
        }
        $post = get_post($post_id);
        $postID = !empty($post) && isset($post->ID) ? $post->ID : 0;
        if ( ! $postID || ! Cache_Manager::is_built_with_elementor( $postID ) || ! Cache_Manager::is_published( $postID ) ) {
            return;
        }

        if ( ! is_null( $data ) ) {
            $this->elementor_data = $data;
        }

        $this->post_id = $post_id;
        $this->is_published = true;
        $this->is_built_with_elementor = true;
    }

    public function get_post_id() {
        return $this->post_id;
    }

    protected function get_global_widget_type( $template_id ) {
        $template_data = ha_elementor()->templates_manager->get_template_data( [
            'source' => 'local',
            'template_id' => $template_id,
        ] );

        if ( is_wp_error( $template_data ) ) {
            return '';
        }

        if ( empty( $template_data['content'] ) ) {
            return '';
        }

        $original_widget_type = ha_elementor()->widgets_manager->get_widget_types( $template_data['content'][0]['widgetType'] );
        return $original_widget_type ? $template_data['content'][0]['widgetType'] : '';
    }

    public function get_widget_type( $element ) {
        if ( empty( $element['widgetType'] ) ) {
            $type = $element['elType'];
        } else {
            $type = $element['widgetType'];
        }

        if ( $type === 'global' && ! empty( $element['templateID'] ) ) {
            $type = $this->get_global_widget_type( $element['templateID'] );
        }
        return $type;
    }

    public function get_cache_data() {
        $cache = get_post_meta( $this->get_post_id(), self::META_KEY, true );
        if ( empty( $cache ) || ! is_array( $cache ) ) {
            $cache = $this->save();
        }
        return $cache;
    }

    public function get() {
        $cache = $this->get_cache_data();
        return array_map( function( $widget_key ) {
            return str_replace( 'ha-', '', $widget_key );
        }, array_keys( $cache ) );
    }

    public function has() {
        $cache = $this->get();
        return ! empty( $cache );
    }

    public function delete() {
        delete_post_meta( $this->get_post_id(), self::META_KEY );
    }

    public function get_post_type() {
        return get_post_type( $this->get_post_id() );
    }

    public function get_elementor_data() {
        if ( ! $this->is_built_with_elementor || ! $this->is_published ) {
            return [];
        }

        if ( is_null( $this->elementor_data ) ) {
            $document = ha_elementor()->documents->get( $this->get_post_id() );
            $data = $document ? $document->get_elements_data() : [];
        } else {
            $data = $this->elementor_data;
        }

        return $data;
    }

    public function save() {
        $data = $this->get_elementor_data();

        if ( empty( $data ) ) {
            return [];
        }

        $cache = [];
        ha_elementor()->db->iterate_data( $data, function ( $element ) use ( &$cache ) {
            $type = $this->get_widget_type( $element );

            if ( strpos( $type, 'ha-' ) !== false ) {
                if ( ! isset( $cache[ $type ] ) ) {
                    $cache[ $type ] = 0;
                }
                $cache[ $type ] ++;
            }

            return $element;
        } );

        // Handle global cache here
        $doc_type = $this->get_post_type();
        $prev_cache = get_post_meta( $this->get_post_id(), self::META_KEY, true );
        $global_cache = get_option( self::OPTION_KEY, [] );

        if ( is_array( $prev_cache ) ) {
            foreach ( $prev_cache as $type => $count ) {
                if ( isset( $global_cache[ $doc_type ][ $type ] ) ) {
                    $global_cache[ $doc_type ][ $type ] -= $prev_cache[ $type ];
                    if ( 0 === $global_cache[ $doc_type ][ $type ] ) {
                        unset( $global_cache[ $doc_type ][ $type ] );
                    }
                }
            }
        }

        foreach ( $cache as $type => $count ) {
            if ( ! isset( $global_cache[ $doc_type ] ) ) {
                $global_cache[ $doc_type ] = [];
            }

            if ( ! isset( $global_cache[ $doc_type ][ $type ] ) ) {
                $global_cache[ $doc_type ][ $type ] = 0;
            }

            $global_cache[ $doc_type ][ $type ] += $cache[ $type ];
        }

        // Save cache
        update_option( self::OPTION_KEY, $global_cache );
        update_post_meta( $this->get_post_id(), self::META_KEY, $cache );
        return $cache;
    }
}
