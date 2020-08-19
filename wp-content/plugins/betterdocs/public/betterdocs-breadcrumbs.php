<?php 
/**
 * BetterDocs Breadcrumbs
 * Since 1.0.0
 */
function betterdocs_get_term_parents_list( $term_id, $taxonomy, $separator, $args = array() ) {

    $list = '';
    $term = get_term( $term_id, $taxonomy );
 
    if ( is_wp_error( $term ) ) {
        return $term;
    }
 
    if ( ! $term ) {
        return $list;
    }
 
    $term_id = $term->term_id;
 
    $defaults = array(
        'format'    => 'name',
        'separator' => $separator,
        'inclusive' => true,
    );
 
    $args = wp_parse_args( $args, $defaults );
 
    foreach ( array( 'inclusive' ) as $bool ) {
        $args[ $bool ] = wp_validate_boolean( $args[ $bool ] );
    }
 
    $parents = get_ancestors( $term_id, $taxonomy, 'doc_category' );
    
    if ( $args['inclusive'] ) {
        array_unshift( $parents, $term_id );
    }

    foreach ( array_reverse( $parents ) as $key => $term_id ) {
        $parent = get_term( $term_id, $taxonomy );
        $name   = ( 'slug' === $args['format'] ) ? $parent->slug : $parent->name;
 
        $term_permalink = get_term_link( $parent->term_id, $taxonomy );
        $term_permalink = apply_filters( 'betterdocs_breadcrumb_term_permalink', $term_permalink );

        $list .= '<li class="betterdocs-breadcrumb-item"><a href="' . $term_permalink . '">' . $name . '</a></li>';
        
        if ( next( $parents ) == true ) {
            $list .= '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter">' . $separator . '</li>';
        }
        
    }
 
    return $list;
}

function betterdocs_breadcrumbs() {

	$enable_breadcrumb_category = BetterDocs_DB::get_settings('enable_breadcrumb_category');
	$enable_breadcrumb_title = BetterDocs_DB::get_settings('enable_breadcrumb_title');
	$builtin_doc_page = BetterDocs_DB::get_settings('builtin_doc_page');
    $docs_page = BetterDocs_DB::get_settings('docs_page');
    $taxanomy = BetterDocs_Helper::get_tax();
	// Settings
    $delimiter	 = '<div class="icon-container"><svg class="breadcrumb-delimiter-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" class="svg-inline--fa fa-angle-right fa-w-8" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><div>';
    $home_title	 = esc_html__('Home', 'betterdocs');

    // Get Docs page
    
    $post_type = get_post_type();

    if ( $post_type != 'post' && $builtin_doc_page == 1 ) {
        $post_type_object = get_post_type_object($post_type);
        $post_type_archive = get_post_type_archive_link($post_type);
        $docs_page = '<li class="betterdocs-breadcrumb-item item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
    } elseif ( $docs_page ) {
        $docs_page_url = get_page_link($docs_page);
        $docs_page_title = get_the_title($docs_page);
        $docs_page = '<li class="betterdocs-breadcrumb-item item-cat item-custom-docs-page"><a class="bread-cat bread-custom-docs-page" href="' . $docs_page_url . '" title="' . $docs_page_title . '">' . $docs_page_title . '</a></li>';
    }

    // Get the query & post information
    global $post;

    // Do not display on the homepage
    if ( !is_front_page() ) {
		echo '<nav id="betterdocs-breadcrumb" class="betterdocs-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">';
        // Build the breadcrums
        echo '<ul class="betterdocs-breadcrumb-list">';

        // Home page
        echo '<li class="betterdocs-breadcrumb-item item-home"><a class="bread-link bread-home" href="' . esc_url(get_home_url()) . '" title="' . $home_title . '">' . $home_title . '</a></li>';
        
        if ( $taxanomy == 'doc_category' || is_tax('doc_tag') ) {
            
            // docs page  
            if ( $builtin_doc_page == 1 || $docs_page ) {          
                echo '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
                echo $docs_page;
            }
            
            // category
            if ( $enable_breadcrumb_category == 1) {
                
                echo '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
                $query_obj = get_queried_object();
                $term_id   = $query_obj->term_id;
                $archive_html = betterdocs_get_term_parents_list( $term_id, 'doc_category', $delimiter );
                
                echo apply_filters( 'betterdocs_breadcrumb_archive_html', $archive_html, $delimiter );
            }

        } else if ( is_single() ) {

            // docs page
            if ( $builtin_doc_page == 1 || $docs_page ) { 
                echo '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
                echo $docs_page;
            }

            // category

            if ( $enable_breadcrumb_category == 1 ) {

                $single_html = '';

                $cat_terms = wp_get_post_terms( $post->ID, 'doc_category' );
                
                echo apply_filters( 'betterdocs_breadcrumb_before_single_cat_html', $single_html, $delimiter );

                if ( $cat_terms ) {
                    echo '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
                    echo betterdocs_get_term_parents_list( $cat_terms[0]->term_id, 'doc_category', $delimiter );
                }
    
            }

			// Check if the post is in a category
			if($enable_breadcrumb_title == 1){
                echo '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
				echo '<li class="betterdocs-breadcrumb-item item-current item-' . $post->ID . ' current"><span>' . get_the_title() . '</span></li>';
            }

        }

        echo '</ul>';
		echo '</nav>';
    }
	
} // end betterdocs_breadcrumbs()