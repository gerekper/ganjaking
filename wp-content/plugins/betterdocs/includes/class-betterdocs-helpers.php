<?php
/**
 * This class will provide all kind of helper methods.
 */
class BetterDocs_Helper {
    /**
     * This function is responsible for the data sanitization
     *
     * @param array $field
     * @param string|array $value
     * @return string|array
     */
    public static function sanitize_field( $field, $value ) {
        if ( isset( $field['sanitize'] ) && ! empty( $field['sanitize'] ) ) {
            if ( function_exists( $field['sanitize'] ) ) {
                $value = call_user_func( $field['sanitize'], $value );
            }
            return $value;
        }

        if( is_array( $field ) && isset( $field['type'] ) ) {
            switch ( $field['type'] ) {
                case 'text':
                    $value = sanitize_text_field( $value );
                    break;
                case 'textarea':
                    $value = sanitize_textarea_field( $value );
                    break;
                case 'email':
                    $value = sanitize_email( $value );
                    break;
                default:
                    return $value;
                    break;
            }
        } else {
            $value = sanitize_text_field( $value );
        }

        return $value;
    }
    /**
     * This function is responsible for making an array sort by their key
     * @param array $data
     * @param string $using
     * @param string $way
     * @return array
     */
    public static function sorter( $data, $using = 'time_date',  $way = 'DESC' ){
        if( ! is_array( $data ) ) {
            return $data;
        }
        $new_array = [];
        if( $using === 'key' ) {
            if( $way !== 'ASC' ) {
                krsort( $data );
            } else {
                ksort( $data );
            }
        } else {
            foreach( $data as $key => $value ) {
                if( ! is_array( $value ) ) continue;
                foreach( $value as $inner_key => $single ) {
                    if( $inner_key == $using ) {
                        $value[ 'tempid' ] = $key;
                        $single = self::numeric_key_gen( $new_array, $single );
                        $new_array[ $single ] = $value;
                    }
                }
            }

            if( $way !== 'ASC' ) {
                krsort( $new_array );
            } else {
                ksort( $new_array );
            }

            if( ! empty( $new_array ) ) {
                foreach( $new_array as $array ) {
                    $index = $array['tempid'];
                    unset( $array['tempid'] );
                    $new_data[ $index ] = $array;
                }
                $data = $new_data;
            }
        }

        return $data;
    }
    /**
     * This function is responsible for generate unique numeric key for a given array.
     *
     * @param array $data
     * @param integer $index
     * @return integer
     */
    private static function numeric_key_gen( $data, $index = 0 ){
        if( isset( $data[ $index ] ) ) {
            $index+=1;
            return self::numeric_key_gen( $data, $index );
        }
        return $index;
    }
    /**
     * Sorting Data 
     * by their type
     *
     * @param array $value
     * @param string $key
     * @return void
     */
    public static function sortBy( &$value, $key = 'comments' ) {
        switch( $key ){
            case 'comments' : 
                return self::sorter( $value, 'key', 'DESC' );
                break;
            default: 
                return self::sorter( $value, 'timestamp', 'DESC' );
                break;
        }
    }
    /**
     * Human Readable Time Diff
     *
     * @param boolean $time
     * @return void
     */
    public static function get_timeago_html( $time = false ) {
        if ( ! $time ) {
            return;
		}
		
        $offset = get_option('gmt_offset'); // Time offset in hours
        $local_time = $time + ($offset * 60 * 60 ); // added offset in seconds
        $time = human_time_diff( $local_time, current_time('timestamp') );
        ob_start();
        ?>
            <small><?php echo esc_html__( 'About', 'betterdocs' ) . ' ' . esc_html( $time ) . ' ' . esc_html__( 'ago', 'betterdocs' ) ?></small>
        <?php
        $time_ago = ob_get_clean();
        return $time_ago;
    }
    /**
     * Get all post types
     *
     * @param array $exclude
     * @return void
     */
    public static function post_types( $exclude = array() ) {
		$post_types = get_post_types(array(
			'public'	=> true,
			'show_ui'	=> true
        ), 'objects');
        
        unset( $post_types['attachment'] );
        
        if ( count( $exclude ) ) {
            foreach ( $exclude as $type ) {
                if ( isset( $post_types[$type] ) ) {
                    unset( $post_types[$type] );
                }
            }
        }

		return apply_filters('betterdocs_post_types', $post_types );
    }
    /**
     * Get all taxonomies
     *
     * @param string $post_type
     * @param array $exclude
     * @return void
     */
	public static function taxonomies( $post_type = '', $exclude = array() ) {
        if ( empty( $post_type ) ) {
            $taxonomies = get_taxonomies(
				array(
					'public'       => true,
					'_builtin'     => false
				),
				'objects'
			);
        } else {
		    $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        }
        
        $data = array();
        if( is_array( $taxonomies ) ) {
            foreach ( $taxonomies as $tax_slug => $tax ) {
                if( ! $tax->public || ! $tax->show_ui ) {
                    continue;
                }
                if( in_array( $tax_slug, $exclude ) ) {
                    continue;
                }
                $data[$tax_slug] = $tax;
            }
        }
		return apply_filters('betterdocs_loop_taxonomies', $data, $taxonomies, $post_type );
    }

    public static function list_svg() {
        $html = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.86em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1536 1792"><path d="M1468 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28H96q-40 0-68-28t-28-68V96q0-40 28-68T96 0h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528V640H992q-40 0-68-28t-28-68V128H128v1536h1280zM384 800q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"/></svg>';
        return $html;
    }

    public static function arrow_right_svg() {
        $html = '<svg class="toggle-arrow arrow-right" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.48em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 608 1280"><g transform="translate(608 0) scale(-1 1)"><path d="M595 288q0 13-10 23L192 704l393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10L23 727q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></g></svg>';
        return $html;
    }
    
    public static function arrow_down_svg() {
        $html = '<svg class="toggle-arrow arrow-down" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.8em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 1280"><path d="M1011 480q0 13-10 23L535 969q-10 10-23 10t-23-10L23 503q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393l393-393q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>';
        return $html;
    }

    public static function get_tax( $tax = '' ) {

        global $wp_query;

		if ( is_tax( 'knowledge_base' ) ) {
            $get_tax = $wp_query->tax_query->queried_terms;
			if (array_key_exists( "doc_category", $get_tax )) {
				$tax = 'doc_category';
			} else {
				$tax = 'knowledge_base';
			}
		} elseif ( is_tax( 'doc_category' ) ) {
            $tax = 'doc_category';
        }

        return $tax;
    }

    public static function list_query_arg( $post_type, $multiple_kb, $tax_slug, $posts_per_grid, $alphabetically_order_post ) {

        $args = array(
            'post_type'   => $post_type,
            'post_status' => 'publish',
        );

        $tax_query = array(
            array(
                'taxonomy' => 'doc_category',
                'field'    => 'slug',
                'terms'    => $tax_slug,
                'operator'          => 'AND',
                'include_children'  => false
            ),
        );

        $args['tax_query'] = apply_filters( 'betterdocs_list_tax_query_arg', $tax_query, $multiple_kb, $tax_slug );
        
        
        if ( $posts_per_grid ) {
            $args['posts_per_page'] = $posts_per_grid;
        }
        
        if($alphabetically_order_post == 1) {
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
        }
        
        return $args;
    }
    
    public static function count_kb() {
        $result = array();
        $kbs = get_terms( array(
            'taxonomy' => 'knowledge_base',
            'hide_empty' => false,
            'fields' => 'id=>slug' 
        ) );
        
        $cats = get_terms( array(
            'taxonomy' => 'doc_category',
            'hide_empty' => false,
            'fields' => 'id=>slug' 
        ) );
        
        
        foreach ($kbs as $kb) {
        
            foreach ($cats as $cat) {
            
                $args = array(
                    'post_type' => 'docs',
                    'post_status'=>'publish',
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'knowledge_base',
                            'field'    => 'slug',
                            'terms'    => array( $kb ),
                        ),
                        array(
                            'taxonomy' => 'doc_category',
                            'field'    => 'slug',
                            'terms'    => array( $cat ),
                        ),
                    ),
                );
                $query = new WP_Query( $args );

                $result[$kb][$cat] = $query->post_count;
                
            }
        }

        return $result;

    }
    
    public static function count_category( $kb_slug, $cat_slug ) {
        
        $args = array(
            'post_type'   => 'docs',
            'post_status' => 'publish',
        );

        $taxes = array( 'knowledge_base', 'doc_category' );
        foreach ( $taxes as $tax ) {
            $terms = get_terms( $tax );
        
            foreach ( $terms as $term )
                $tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
        }

        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'knowledge_base',
                'field' => 'term_taxonomy_id',
                'terms' => array( $tax_map['knowledge_base'][$kb_slug] ),
                'operator' => 'IN',
                'include_children'  => false,
            ),
            array(
                'taxonomy' => 'doc_category',
                'field' => 'term_taxonomy_id',
                'operator' => 'IN',
                'terms' => array( $tax_map['doc_category'][$cat_slug] ),
                'include_children'  => false,
            ),
        );
    
        $query = new WP_Query( $args );

        $count = $query->found_posts;

        return $count;
    }
    
    public static function taxonomy_object( $multiple_kb, $terms ) {
    
        $terms_object = array(
            'hide_empty' => true,
            'taxonomy' => 'doc_category',
            'orderby' => 'name',
            'parent' => 0
        );
        
        $meta_query = '';
        $terms_object['meta_query'] = apply_filters( 'betterdocs_taxonomy_object_meta_query', $meta_query, $multiple_kb );
    
        if ( $terms ) {
            unset($terms_object['parent']);
        }

        if ( $terms ) {
            $terms_object['include'] = explode(',', $terms );
            $terms_object['orderby'] = 'include';
        }
    
        $taxonomy_objects = get_terms( $terms_object );
    
        return $taxonomy_objects;
    }
    
    public static function child_taxonomy_terms( $term_id, $multiple_kb ) {
    
        $terms_object = array(
            'child_of' => $term_id,
            'orderby' => 'name'
        );

        $meta_query = '';
        $terms_object['meta_query'] = apply_filters( 'betterdocs_child_taxonomy_meta_query', $meta_query, $multiple_kb );
    
        $taxonomy_objects = get_terms( 'doc_category', $terms_object);
    
        return $taxonomy_objects;
    }
    
    public static function term_permalink( $texanomy, $term_slug ) {
    
        $q_object = get_queried_object();

        $term_permalink = get_term_link( $term_slug, $texanomy );
        $kb_slug = '';
        if( $q_object instanceof WP_Term ) {
            $kb_slug = $q_object->slug;
        }
        $term_permalink = str_replace( '%knowledge_base%', $kb_slug, $term_permalink );
    
        return $term_permalink;
    }

}
