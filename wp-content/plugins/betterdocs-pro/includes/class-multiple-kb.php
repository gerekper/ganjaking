<?php 
/**
 * Multiple knowledge base functions.
 *
 *
 *
 * @since      2.0.0
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.net>
 */

class BetterDocs_Multiple_Kb {
    
    public static $enable;

    public static function init() {
        
        self::$enable = self::get_multiple_kb();
		add_action( 'betterdocs_multi_kb_settings', array( __CLASS__, 'kb_settings') );
        if ( self::$enable == 1 ) {
			
			add_action('init', array( __CLASS__, 'register_knowledge_base' ) );
			add_filter( 'betterdocs_docs_rewrite', array( __CLASS__, 'docs_rewrite' ) );
			add_filter( 'betterdocs_category_rewrite', array( __CLASS__, 'doc_category_rewrite' ) );
			add_filter('post_type_link', array( __CLASS__, 'docs_show_permalinks'), 1, 3);
			// add_filter('term_link', array( __CLASS__, 'doc_category_permalink'), 1, 3);

			add_action( 'betterdocs_cat_template_multikb', array( __CLASS__, 'get_multiple_kb') );
			add_action( 'betterdocs_postcount', array( __CLASS__, 'postcount'), 10, 5 );
			add_action( 'betterdocs_category_list_shortcode', array( __CLASS__, 'category_list_shortcode'), 10, 1 );
			add_action( 'betterdocs_category_box_shortcode', array( __CLASS__, 'category_box_shortcode'), 10, 1 );
			add_action( 'betterdocs_sidebar_category_shortcode', array( __CLASS__, 'sidebar_category_shortcode'), 10, 1 );
			add_action( 'betterdocs_list_tax_query_arg', array( __CLASS__, 'list_tax_query'), 10, 3 );
			add_action( 'betterdocs_taxonomy_object_meta_query', array( __CLASS__, 'taxonomy_object_meta_query'), 10, 2 );
			add_action( 'betterdocs_child_taxonomy_meta_query', array( __CLASS__, 'child_taxonomy_meta_query'), 10, 2 );
			add_action( 'betterdocs_kb_uncategorized_tax_query', array( __CLASS__, 'kb_uncategorized_tax_query'), 10, 2 );
			add_action( 'betterdocs_breadcrumb_archive_html', array( __CLASS__, 'breadcrumb_archive'), 10, 2 );
			add_action( 'betterdocs_breadcrumb_before_single_cat_html', array( __CLASS__, 'breadcrumb_single'), 10, 2 );
			add_action( 'betterdocs_breadcrumb_term_permalink', array( __CLASS__, 'breadcrumb_term_permalink') );

			add_action( 'betterdocs_doc_category_add_form_before', array( __CLASS__, 'doc_category_add_form' ) );
			add_action( 'betterdocs_doc_category_update_form_before', array( __CLASS__, 'doc_category_update_form' ) );

			add_filter( 'doc_category_row_actions', array( __CLASS__, 'disable_category_view'), 10, 2 );

        }
    }

	public static function kb_settings() {
		$settings = array(
			'type'        => 'checkbox',
			'label'       => __('Enable Multiple Knowledge Base' , 'betterdocs'),
			'default'     => '',
			'priority'    => 10,
		);
		return $settings;
	}

    public static function get_multiple_kb() {

        $multiple_kb = BetterDocs_DB::get_settings('multiple_kb');
        return $multiple_kb;
    }
	
	public static function postcount( $term_count, $multiple_kb, $term_id, $term_slug, $count ) {
		
		global $wp_query;

		if ( $multiple_kb == true && $wp_query->query['knowledge_base'] != 'non-knowledgebase' ) {
			$term_count = BetterDocs_Helper::count_category($wp_query->query['knowledge_base'], $term_slug);
		} else {
			$term_count = betterdocs_get_postcount( $count, $term_id );
		}

		return $term_count;
		
	}

	public static function category_list_shortcode( $shortcode ) {

		if ( is_tax( 'knowledge_base' ) ) {

			$shortcode = do_shortcode( '[betterdocs_category_grid multiple_knowledge_base="true"]' );

		} else {

			$shortcode = do_shortcode( '[betterdocs_category_grid]' );

		}

		return $shortcode;
		
	}
	
	public static function category_box_shortcode( $shortcode ) {

		if ( is_tax( 'knowledge_base' ) ) {

			$shortcode = do_shortcode( '[betterdocs_category_box multiple_knowledge_base="true"]' );

		} else {

			$shortcode = do_shortcode( '[betterdocs_category_box]' );

		}

		return $shortcode;
		
	}
	
	public static function sidebar_category_shortcode( $shortcode ) {

		$shortcode = do_shortcode( '[betterdocs_category_grid sidebar_list="true" posts_per_grid="-1" multiple_knowledge_base=true]' );

		return $shortcode;
		
	}
	
	public static function breadcrumb_archive( $html, $delimiter ) {

		global $wp_query;

		$kb_term = $wp_query->query_vars['knowledge_base'];
		$get_kb_term = get_term_by('slug', $kb_term, 'knowledge_base');
		$kb_term_id = $get_kb_term->term_id;

		$cat_term = $wp_query->query_vars['doc_category'];
		$get_cat_term = get_term_by('slug', $cat_term, 'doc_category');
		$cat_term_id = $get_cat_term->term_id;

		$html = betterdocs_get_term_parents_list( $kb_term_id, 'knowledge_base', $delimiter ) .
		'<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>'
		. betterdocs_get_term_parents_list( $cat_term_id, 'doc_category', $delimiter );

		return $html;
		
	}
	
	public static function breadcrumb_single( $html, $delimiter ) {

		global $post;

		$kb_terms = wp_get_post_terms( $post->ID, 'knowledge_base' );

		if ( $kb_terms ) {
			$html = '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>'
			. betterdocs_get_term_parents_list( $kb_terms[0]->term_id, 'knowledge_base', $delimiter );
		}

		return $html;
		
	}

	public static function register_knowledge_base() {

		/**
		 * Register knowledge base taxonomy
		 */
		$manage_labels = array(
			'name'                       => esc_html__('Knowledge Base', 'betterdocs'),
			'singular_name'              => esc_html__('Knowledge Base', 'betterdocs'),
			'search_items'               => esc_html__('Search Knowledge Base', 'betterdocs'),
			'all_items'                  => esc_html__('All Knowledge Base', 'betterdocs'),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => esc_html__('Edit Knowledge Base', 'betterdocs'),
			'update_item'                => esc_html__('Update Knowledge Base', 'betterdocs'),
			'not_found'                  => esc_html__('No Knowledge Base found.', 'betterdocs' ),
			'add_new_item'               => esc_html__('Add New Knowledge Base', 'betterdocs'),
			'new_item_name'              => esc_html__('New Knowledge Base Name', 'betterdocs'),
			'add_or_remove_items'        => esc_html__('Add or reomve Knowledge Base', 'betterdocs'),
			'menu_name'                  => esc_html__('Knowledge Base', 'betterdocs'),
		);

		$manage_args = array(
			'hierarchical'          => true,
			'labels'                => $manage_labels,
			'show_ui'               => true,
			'update_count_callback' => '_update_post_term_count',
			'show_admin_column'     => true,
			'query_var'             => true,
			'show_in_rest'          => true,
			'has_archive'           => true,
			'rewrite'               => array( 'slug' => BetterDocs_Docs_Post_Type::$docs_archive, 'with_front' => false ),
		);

		register_taxonomy('knowledge_base', array(BetterDocs_Docs_Post_Type::$post_type), $manage_args);

	}

	public static function docs_rewrite( $rewrite ) {

        $rewrite = array( 'slug' => BetterDocs_Docs_Post_Type::$docs_slug . '/%knowledge_base%/%doc_category%', 'with_front' => false );
        
		return $rewrite;

	}

	public static function doc_category_rewrite( $rewrite ) {

        $rewrite = array('slug' => BetterDocs_Docs_Post_Type::$docs_slug . '/%knowledge_base%', 'with_front' => false);
        
		return $rewrite;

	}
	
	public static function list_tax_query( $tax_query, $multiple_kb, $tax_slug ) {
		
		global $wp_query;

		if ( $multiple_kb == true && $wp_query->query['knowledge_base'] != 'non-knowledgebase' ) {
			$taxes = array( 'knowledge_base', 'doc_category' );

			foreach ( $taxes as $tax ) {
				$terms = get_terms( $tax );
			
				foreach ( $terms as $term )
					$tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
			}

			$tax_query = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'knowledge_base',
					'field' => 'term_taxonomy_id',
					'terms' => array( $tax_map['knowledge_base'][self::kb_slug()] ),
					'operator' => 'IN',
					'include_children'  => false,
				),
				array(
					'taxonomy' => 'doc_category',
					'field' => 'term_taxonomy_id',
					'operator' => 'IN',
					'terms' => array( $tax_map['doc_category'][$tax_slug] ),
					'include_children'  => false,
				),
			);

			return $tax_query;
		}

	}

	public static function taxonomy_object_meta_query( $meta_query, $multiple_kb ) {
		
		if ( $multiple_kb == true ) {

			$meta_query = array(
				array(
					'relation' => 'OR', 
					array(
						'key'       => 'doc_category_knowledge_base',
						'value'     => self::kb_slug(),
						'compare'   => 'LIKE'
					),
				),
			);

			return $meta_query;
		}

	}
	
	public static function child_taxonomy_meta_query( $meta_query, $multiple_kb ) {

		if ( $multiple_kb == true ) {

			$meta_query = array( 
				array(
					'key'       => 'doc_category_knowledge_base',
					'value'     => self::kb_slug(),
					'compare'   => '='
				)
			);

			return $meta_query;
		}

	}

	public static function kb_slug() {
        
        $kb_slug = '';
        $object = get_queried_object();

        if ( is_singular( 'docs' ) ) {

			$kbterms = get_the_terms( get_the_ID() , 'knowledge_base' );

			if ( $kbterms ) {
				$kb_slug = $kbterms[0]->slug;
			}
            
        } elseif ( is_tax( 'doc_category' ) ) {

            $kb_slug = self::doc_category_kb_slug( $object->term_id );          
   
        } elseif ( is_tax( 'knowledge_base' ) ) {

            $kb_slug = $object->slug;

        }

        return $kb_slug;
    }

    public static function doc_category_kb_slug( $term_id ) {
        
        $kb_slug = get_term_meta( $term_id, 'doc_category_knowledge_base', true);

        if ( is_array ( $kb_slug ) ) {

            global $wp;
            $current_url = home_url( add_query_arg( array(), $wp->request ) );
            $url_parse = wp_parse_url( $current_url );
            $url_path = $url_parse['path'];
            $path_arr = explode("/",$url_path);
            $reverse_path_arr = array_reverse($path_arr);
            $get_kb_slug_path = $reverse_path_arr[1];

            if ( in_array( $get_kb_slug_path, $kb_slug) ) {
                $kb_slug = $get_kb_slug_path;
            } else {
                $kb_slug = $kb_slug[0];
            }
            
        }

        return $kb_slug;
    }
	
	public static function breadcrumb_term_permalink( $term_permalink ) {
        
        $kb_slug = self::kb_slug();
        $term_permalink = str_replace( '%knowledge_base%', $kb_slug, $term_permalink );

        return $term_permalink;
    }
	
	public static function kb_uncategorized_tax_query( $tax_query, $wp_query ) {

		$tax_query = array(
			array(
				'taxonomy' => 'knowledge_base',
				'field'    => 'slug',
				'terms'    => $wp_query->query['knowledge_base'],
				'operator'          => 'AND',
				'include_children'  => false
			),
		);

		return $tax_query;

	}

	public static function docs_show_permalinks($url, $post = null, $leavename = false) {
        
        if ($post->post_type != 'docs') {
            return $url;
        }

        $doc_category = 'doc_category';
        $cat_tag = '%' . $doc_category . '%';
        $cat_terms = wp_get_object_terms( $post->ID, $doc_category );

        $knowledgebase = 'knowledge_base';
        $knowledgebase_tag = '%' . $knowledgebase . '%';
        $knowledgebase_terms = wp_get_object_terms( $post->ID, $knowledgebase );
        
            
        if (is_array($knowledgebase_terms) && sizeof($knowledgebase_terms) > 0) {
            $knowledgebase_terms = $knowledgebase_terms[0]->slug;
        } else {
            $knowledgebase_terms = 'non-knowledgebase';
        }
        // replace taxonomy tag with the term slug: /docs/%knowledge_base%/category/articlename
        $url = str_replace($knowledgebase_tag, $knowledgebase_terms, $url);


        if (is_array($cat_terms) && sizeof($cat_terms) > 0) {
            $doccat_terms = $cat_terms[0]->slug;
        } else {
            $doccat_terms = 'uncategorized';
        }

        return str_replace($cat_tag, $doccat_terms, $url);
    }
	
	public static function disable_category_view( $actions,$tag ) {
        
        unset($actions['view']);
		  
		return $actions;
		  
    }

    public static function doc_category_permalink( $termlink, $term, $taxonomy ) {
            
        //If this term is not a basepress product return the $termlink unchanged
        if ( 'doc_category' != $term->taxonomy ) {
            return $termlink;
        }

        $knowledgebase = 'knowledge_base';
        $knowledgebase_tag = '%' . $knowledgebase . '%';
        
        $knowledge_base = get_term_meta($term->term_id, 'doc_category_knowledge_base', true);
        if( empty( $knowledge_base ) ) {
            $knowledge_base = 'non-knowledgebase';
        }
        $termlink = str_replace( $knowledgebase_tag, $knowledge_base , $termlink );
        
        return $termlink;
    }
	
	public static function doc_category_add_form() {

		$manage_docs_terms = get_terms('knowledge_base', array('hide_empty' => false));
		if($manage_docs_terms) { ?>
			<div class="form-field term-group">
				<label><?php _e('Knowledge Base', 'betterdocs') ?></label>
				<select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
					<option value="" selected><?php _e('No Knowledge Base', 'betterdocs') ?></option>
					<?php 
					foreach ($manage_docs_terms as $term) {
						echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
					}
					?>
				</select>
			</div>
		<?php 
		}

	}
	
	public static function doc_category_update_form($term) {
		
		$knowledge_base = get_term_meta($term->term_id, 'doc_category_knowledge_base', true);
		$manage_docs_terms = get_terms('knowledge_base', array('hide_empty' => false));
		if( $manage_docs_terms ) { ?>
			<tr class="form-field term-group-wrap">
				<th scope="row">
				<label><?php _e('Knowledge Base', 'betterdocs') ?></label>
				</th>
				<td>
					<select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
						<option value=""><?php _e('No Knowledge Base', 'betterdocs') ?></option>
						<?php 
						foreach ($manage_docs_terms as $term) {
							$selected = ( is_array( $knowledge_base ) && in_array( $term->slug, $knowledge_base ) ) ? ' selected' : '';
							echo '<option value="' . $term->slug . '"' . $selected . '>' . $term->name . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
		<?php 
		}
    }
}

BetterDocs_Multiple_Kb::init();

