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
			add_filter( 'post_type_link', array( __CLASS__, 'docs_show_permalinks'), 1, 3);
			add_filter('nav_menu_link_attributes', array(__CLASS__, 'doc_category_nav_menu_permalink'), 10, 3);

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
			add_action( 'betterdocs_cat_term_permalink', array( __CLASS__, 'cat_term_permalink') );

			add_action( 'betterdocs_doc_category_add_form_before', array( __CLASS__, 'doc_category_add_form' ) );
			add_action( 'betterdocs_doc_category_update_form_before', array( __CLASS__, 'doc_category_update_form' ) );

			add_filter( 'doc_category_row_actions', array( __CLASS__, 'disable_category_view'), 10, 2 );

			// add_action('admin_enqueue_scripts', array(__CLASS__, 'load_media'));
			add_action( 'knowledge_base_add_form_fields', array( __CLASS__, 'add_knowledge_base_meta' ), 10, 2 );
			add_action( 'created_knowledge_base', array( __CLASS__, 'save_knowledge_base_meta' ), 10, 2 );
			add_action( 'knowledge_base_edit_form_fields', array( __CLASS__, 'update_knowledge_base_meta' ), 10, 2 );
			add_action( 'edited_knowledge_base', array( __CLASS__, 'updated_knowledge_base_meta' ), 10, 2 );
			add_action( 'admin_footer', array(__CLASS__, 'kb_script'));

			add_action( 'init', array( __CLASS__, 'front_end_order_terms' ) );
			add_action( 'admin_head', array( __CLASS__, 'admin_order_terms' ) );
			add_action( 'wp_ajax_update_knowledge_base_order', array( __CLASS__, 'update_knowledge_base_order' ) );

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
		
		$kb_terms = get_terms( 'knowledge_base' );
		$doc_category_terms = get_terms( 'doc_category' );

		if ( $multiple_kb == true && !empty($kb_terms) && !empty($doc_category_terms) && $wp_query->query['knowledge_base'] != 'non-knowledgebase' ) {

			$term_count = self::count_category($wp_query->query['knowledge_base'], $term_slug);

		} else {

			$term_count = betterdocs_get_postcount( $count, $term_id );
			
		}

		return $term_count;
		
	}

	public static function count_category( $kb_slug, $cat_slug ) {
        
        $args = array(
            'post_type'   => 'docs',
            'post_status' => 'publish',
        );

		$taxes = array( 'knowledge_base', 'doc_category' );
		$tax_map = array();

        foreach ( $taxes as $tax ) {
            $terms = get_terms( $tax );
        
            foreach ( $terms as $term )
                $tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
        }

        $args['tax_query'] = array(
            'relation' => 'AND'
		);
		
		if ( array_key_exists( 'knowledge_base', $tax_map ) && !empty( $tax_map['knowledge_base'][$kb_slug] ) ) {
			
			$args['tax_query'][] = array(
                'taxonomy' => 'knowledge_base',
                'field' => 'term_taxonomy_id',
                'terms' => array( $tax_map['knowledge_base'][$kb_slug] ),
                'operator' => 'IN',
                'include_children'  => false,
			);
			
		}
		
		if ( array_key_exists( 'doc_category', $tax_map ) && !empty( $tax_map['doc_category'][$cat_slug] ) ) {
			
			$args['tax_query'][] = array(
                'taxonomy' => 'doc_category',
                'field' => 'term_taxonomy_id',
                'operator' => 'IN',
                'terms' => array( $tax_map['doc_category'][$cat_slug] ),
                'include_children'  => false,
			);
			
		}

        $query = new WP_Query( $args );

        $count = $query->found_posts;

        return $count;
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
		$archive = '';
		$kb_term = $wp_query->query_vars['knowledge_base'];

		if ($kb_term != 'non-knowledgebase') {
			$get_kb_term = get_term_by('slug', $kb_term, 'knowledge_base');
			$kb_term_id = $get_kb_term->term_id;
			$archive .= betterdocs_get_term_parents_list($kb_term_id, 'knowledge_base', $delimiter);
			$archive .= '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
		}

		$cat_term = $wp_query->query_vars['doc_category'];
		$get_cat_term = get_term_by('slug', $cat_term, 'doc_category');
		$cat_term_id = $get_cat_term->term_id;
		$archive .= betterdocs_get_term_parents_list($cat_term_id, 'doc_category', $delimiter);

		return $html = $archive;
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
			$tax_map = array();

			foreach ( $taxes as $tax ) {
				$terms = get_terms( $tax );
			
				foreach ( $terms as $term )
					$tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
			}

			$tax_query = array(
				'relation' => 'AND',
				// array(
				// 	'taxonomy' => 'knowledge_base',
				// 	'field' => 'term_taxonomy_id',
				// 	'terms' => array( $tax_map['knowledge_base'][self::kb_slug()] ),
				// 	'operator' => 'IN',
				// 	'include_children'  => false,
				// ),
				// array(
				// 	'taxonomy' => 'doc_category',
				// 	'field' => 'term_taxonomy_id',
				// 	'operator' => 'IN',
				// 	'terms' => array( $tax_map['doc_category'][$tax_slug] ),
				// 	'include_children'  => false,
				// ),
			);

			if ( array_key_exists( 'knowledge_base', $tax_map ) && !empty( $tax_map['knowledge_base'][self::kb_slug()] ) ) {
			
				$tax_query['tax_query'][] = array(
					'taxonomy' => 'knowledge_base',
					'field' => 'term_taxonomy_id',
					'terms' => array( $tax_map['knowledge_base'][self::kb_slug()] ),
					'operator' => 'IN',
					'include_children'  => false,
				);
				
			}

			if ( array_key_exists( 'doc_category', $tax_map ) && !empty( $tax_map['doc_category'][$tax_slug] ) ) {
				
				$tax_query['tax_query'][] = array(
					'taxonomy' => 'doc_category',
					'field' => 'term_taxonomy_id',
					'operator' => 'IN',
					'terms' => array( $tax_map['doc_category'][$tax_slug] ),
					'include_children'  => false,
				);
				
			}

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
	
	public static function breadcrumb_term_permalink($term_permalink)
	{
		$kb_slug = self::kb_slug();

		if ($kb_slug) {
			$term_permalink = str_replace('%knowledge_base%', $kb_slug, $term_permalink);
		} else {
			$term_permalink = str_replace('%knowledge_base%', 'non-knowledgebase', $term_permalink);
		}

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
	
	public static function doc_category_nav_menu_permalink($atts, $item, $args)
	{
		if ($item->type == 'taxonomy' && $item->object == 'doc_category') {
			$atts['href'] = self::doc_category_permalink($atts['href'], $item->object_id);
		}
		return $atts;
	}

	public static function doc_category_permalink($termlink, $term_id)
	{
        $knowledgebase = 'knowledge_base';
        $knowledgebase_tag = '%' . $knowledgebase . '%';
        $kb_arr = get_term_meta($term_id, 'doc_category_knowledge_base', true);
		
		if (empty($kb_arr[0])) {
			$knowledge_base = 'non-knowledgebase';
		} else {
			$knowledge_base = $kb_arr[0];
		}
		
        $termlink = str_replace($knowledgebase_tag, $knowledge_base, $termlink);
        
        return $termlink;
	}
	
	public static function cat_term_permalink( $term_permalink ) {
    
        $q_object = get_queried_object();
        
        $kb_slug = '';

        if( $q_object instanceof WP_Term ) {

            $kb_slug = $q_object->slug;

        }

        $term_permalink = str_replace( '%knowledge_base%', $kb_slug, $term_permalink );
    
        return $term_permalink;
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
		
		$knowledge_base = get_term_meta( $term->term_id, 'doc_category_knowledge_base', true );
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
	
	/**
     * Add a form field in the new category page
     *
     * @since 1.3.1
    */
    public static function add_knowledge_base_meta( $taxonomy ) {
		
		?>
		
        <div class="form-field term-group">

            <label for="knowledge-base-image-id"><?php esc_html_e('KB Icon', 'betterdocs'); ?></label>
            <input type="hidden" id="knowledge-base-image-id" name="term_meta[image-id]" class="custom_media_url" value="">
			
			<div id="knowledge-base-image-wrapper">
                <?php echo '<img src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">' ?>
			</div>
			
            <p>
                <input type="button" class="button button-secondary betterdocs_tax_media_button"
                    id="betterdocs_tax_media_button" name="betterdocs_tax_media_button"
					value="<?php esc_html_e('Add Image', 'betterdocs'); ?>" />
					
                <input type="button" class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove"
                    name="doc_tax_media_remove"
                    value="<?php esc_html_e('Remove Image', 'betterdocs'); ?>" />
			</p>
			
		</div>
		
    <?php
	}

	/**
     * Save the form field
     *
     * @since 1.3.1
    */
    public static function save_knowledge_base_meta( $term_id ) {

        if ( isset( $_POST['term_meta'] ) ) {

            $term_meta = get_option("knowledge_base_$term_id");
			$cat_keys = array_keys($_POST['term_meta']);
			
            foreach ($cat_keys as $key) {

                if ( isset( $_POST['term_meta'][$key] ) ) {

                    add_term_meta($term_id, "knowledge_base_$key", $_POST['term_meta'][$key]);
					$term_meta[$key] = $_POST['term_meta'][$key];
					
				}
				
			}
			
		}
		
	}

	/**
     * Edit the form field
     *
     * @since 1.3.1
    */
    public static function update_knowledge_base_meta($term, $taxonomy) { ?>
        <?php
        $kb_icon_id = get_term_meta($term->term_id, 'knowledge_base_image-id', true);

        do_action( 'betterdocs_knowledge_base_update_form_before', $term );

        ?>

        <tr class="form-field term-group-wrap batterdocs-cat-media-upload">
			
			<th scope="row">
                <label for="knowledge-base-image-id"><?php esc_html_e('KB Icon', 'betterdocs'); ?></label>
			</th>
			
            <td>
                <input type="hidden" id="knowledge-base-image-id" name="term_meta[image-id]" value="<?php echo $kb_icon_id; ?>">
                <div id="knowledge-base-image-wrapper">
                    <?php
                        if ( $kb_icon_id ) {
                            echo wp_get_attachment_image( $kb_icon_id, 'thumbnail' );
                        } else {
                            echo '<img src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
                        }
                    ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary betterdocs_tax_media_button"
                        id="betterdocs_tax_media_button" name="betterdocs_tax_media_button"
                        value="<?php esc_html_e('Add Image', 'betterdocs'); ?>" />
                    <input type="button" class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove"
                        name="doc_tax_media_remove"
                        value="<?php esc_html_e('Remove Image', 'betterdocs'); ?>" />
                </p>
			</td>
			
		</tr>
		
        <?php
	}
	
	/*
     * Update the form field value
     *
     * @since 1.3.1
    */
    public static function updated_knowledge_base_meta( $term_id ) {

        if ( isset($_POST['term_meta']) ) {

			$cat_keys = array_keys($_POST['term_meta']);
			
            foreach ($cat_keys as $key) {

                if (isset($_POST['term_meta'][$key])) {

					update_term_meta($term_id, "knowledge_base_$key", $_POST['term_meta'][$key]);
					
				}
				
			}
			
		}
		
    }
	
	
	/*
     * Add script
     *
     * @since 1.3.1
    */
    public static function kb_script() {
        
		global $current_screen;

        if ( $current_screen->id == 'edit-knowledge_base' ) {
        
        ?>
        <script>
        jQuery(document).ready(function($) {

            function betterdocs_kb_media_upload(button_class) {
                var _custom_media = true,
                    _betterdocs_send_attachment = wp.media.editor.send.attachment;
                $('body').on('click', button_class, function(e) {
                    var button_id = '#' + $(this).attr('id');
                    var send_attachment_bkp = wp.media.editor.send.attachment;
                    var button = $(button_id);
                    _custom_media = true;
                    wp.media.editor.send.attachment = function(props, attachment) {
                        if (_custom_media) {
                            $('#knowledge-base-image-id').val(attachment.id);
                            $('#knowledge-base-image-wrapper').html(
                                '<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
                            );
                            $('#knowledge-base-image-wrapper .custom_media_image').attr('src', attachment
                                .url).css('display', 'block');
                        } else {
                            return _betterdocs_send_attachment.apply(button_id, [props, attachment]);
                        }
                    }
                    wp.media.editor.open(button);
                    return false;
                });
            }

            betterdocs_kb_media_upload('.betterdocs_tax_media_button.button');
            
            $('body').on('click', '.doc_tax_media_remove', function() {
                $('#knowledge-base-image-id').val('');
                $('#knowledge-base-image-wrapper').html(
                    '<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
                );
            });

            $(document).ajaxComplete(function(event, xhr, settings) {
                var queryStringArr = settings.data.split('&');
                if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                    var xml = xhr.responseXML;
                    $response = $(xml).find('term_id').text();
                    if ($response != "") {
                        // Clear the thumb image
                        $('#knowledge-base-image-wrapper').html('');
                    }
                }
            });
        });
        </script>
	
	<?php }
	}

	/**
	 * 
     * Default the taxonomy's terms' order if it's not set.
     *
     * @param string $tax_slug The taxonomy's slug.
     */
    public static function default_term_order($tax_slug) {

        $terms = get_terms($tax_slug, array('hide_empty' => false));
        $order = self::get_max_taxonomy_order($tax_slug);
		
		foreach ( $terms as $term ) {

            if ( !get_term_meta($term->term_id, 'kb_order', true ) ) {

                update_term_meta($term->term_id, 'kb_order', $order);
				$order++;
				
			}
			
        }
    }

    /**
     * Order the terms on the admin side.
     */
    public static function admin_order_terms() {
		
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : '';
		
        if ( in_array( $screen->id, array( 'toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings') ) ) {
			
			self::default_term_order('knowledge_base');

        }

        if ( !isset( $_GET['orderby'] ) && !empty( $screen ) && !empty( $screen->base ) && $screen->base === 'edit-tags' && $screen->taxonomy === 'knowledge_base' ) {
			
			self::default_term_order( $screen->taxonomy );

			add_filter( 'terms_clauses', array( __CLASS__, 'set_tax_order' ), 10, 3 );
			
        }
    }

    /**
	 * 
     * Get the maximum kb_order for this taxonomy.
	 * This will be applied to terms that don't have a tax position.
	 * 
     */

    private static function get_max_taxonomy_order($tax_slug) {
		
		global $wpdb;
        $max_term_order = $wpdb->get_col(

            $wpdb->prepare(

                "SELECT MAX( CAST( tm.meta_value AS UNSIGNED ) )
				FROM $wpdb->terms t
				JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = '%s'
				JOIN $wpdb->termmeta tm ON tm.term_id = t.term_id WHERE tm.meta_key = 'kb_order'",
				$tax_slug
				
			)
			
        );
        $max_term_order = is_array($max_term_order) ? current($max_term_order) : 0;
		return (int) $max_term_order === 0 || empty($max_term_order) ? 1 : (int) $max_term_order + 1;
		
    }

    /**
     * Re-Order the taxonomies based on the kb_order value.
     *
     * @param array $pieces     Array of SQL query clauses.
     * @param array $taxonomies Array of taxonomy names.
     * @param array $args       Array of term query args.
     */
    public static function set_tax_order( $pieces, $taxonomies, $args ) {

        foreach ($taxonomies as $taxonomy) {
			
			global $wpdb;
            if ($taxonomy === 'knowledge_base') {
				
				$join_statement = " LEFT JOIN $wpdb->termmeta AS kb_term_meta ON t.term_id = kb_term_meta.term_id AND kb_term_meta.meta_key = 'kb_order'";

                if (!self::does_substring_exist($pieces['join'], $join_statement)) {
                    $pieces['join'] .= $join_statement;
                }
                $pieces['orderby'] = 'ORDER BY CAST( kb_term_meta.meta_value AS UNSIGNED )';
			}
			
        }
        return $pieces;
	}

    /**
     * Order the taxonomies on the front end.
     */
    public static function front_end_order_terms() {

        if ( !is_admin() ) {

            add_filter( 'terms_clauses', array( __CLASS__, 'set_tax_order'), 10, 3 );
		
		}
		
    }

    /**
     * Check if a substring exists inside a string.
     *
     * @param string $string    The main string (haystack) we're searching in.
     * @param string $substring The substring we're searching for.
     *
     * @return bool True if substring exists, else false.
     */
    protected static function does_substring_exist($string, $substring) {

		return strstr($string, $substring) !== false;
		
	}
	
	/**
	 * 
	 * AJAX Handler to update terms' tax position.
	 * 
	 */
	static function update_knowledge_base_order() {

		if ( ! check_ajax_referer( 'knowledge_base_order_nonce', 'knowledge_base_order_nonce', false ) ) {
			wp_send_json_error();
		}

		$kb_ordering_data = filter_var_array( wp_unslash( $_POST['kb_ordering_data'] ), FILTER_SANITIZE_NUMBER_INT );
		$kb_index       = filter_var( wp_unslash( $_POST['kb_index'] ), FILTER_SANITIZE_NUMBER_INT ) ;
		
		foreach ( $kb_ordering_data as $order_data ) {

			if ( $kb_index > 0 ) {

				$current_position = get_term_meta( $order_data['term_id'], 'kb_order', true );
				
				if ( (int) $current_position < (int) $kb_index ) {
					continue;
				}
			}

			update_term_meta( $order_data['term_id'], 'kb_order', ( (int) $order_data['order'] + (int) $kb_index ) );
		
		}

		wp_send_json_success();
	}

}

BetterDocs_Multiple_Kb::init();

