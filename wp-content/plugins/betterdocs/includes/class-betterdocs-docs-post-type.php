<?php
/**
 * Register docs post type and taxonomies.
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 */

use function YoastSEO_Vendor\GuzzleHttp\json_decode;

/**
 * Register docs post type and taxonomies class.
 *
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class BetterDocs_Docs_Post_Type {
    public static $post_type = 'docs';
    public static $menu_position = 5;
    public static $category = 'doc_category';
    public static $tag = 'doc_tag';
    public static $docs_archive;
    public static $docs_slug;
    public static $cat_slug;

    /**
     *
     * Initialize the class and start calling our hooks and filters
     *
     * @since    1.0.0
     *
     */
    public static function init() {
        self::$docs_archive = self::get_docs_archive();
        self::$docs_slug = self::get_docs_slug();
        self::$cat_slug = self::docs_category_slug();
        add_action('init', array(__CLASS__, 'register_post'));
        add_filter('rest_api_allowed_post_types', array(__CLASS__, 'rest_api_allowed_post_types'));
        add_action('admin_head', array(__CLASS__, 'admin_order_terms'));
        $alphabetically_order_term = BetterDocs_DB::get_settings('alphabetically_order_term');
        if ( $alphabetically_order_term != 1 ) {
            add_action('init', array(__CLASS__, 'front_end_order_terms'));
        }
        // doc category taxonomy media upload hooks
        add_action('doc_category_add_form_fields', array(__CLASS__, 'add_doc_category_meta'), 10, 2);
        add_action('doc_category_edit_form_fields', array(__CLASS__, 'update_doc_category_meta'), 10, 2);
        add_action('created_doc_category', array(__CLASS__, 'save_doc_category_meta'), 10, 2);
        add_action('edited_doc_category', array(__CLASS__, 'updated_doc_category_meta'), 10, 2);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'load_media'));
        add_action('admin_footer', array(__CLASS__, 'add_script'));
    }

    public static function get_docs_slug() {

        $builtin_doc_page = BetterDocs_DB::get_settings('builtin_doc_page');
        $docs_slug = BetterDocs_DB::get_settings('docs_slug');
        $docs_page = BetterDocs_DB::get_settings('docs_page');
        
        // $disable_root_slug = BetterDocs_DB::get_settings('disable_root_slug');
        // if ( $disable_root_slug == 1 ) { 
        //     $docs_post_slug = '';
        // }
        
        if ( $builtin_doc_page == 1 && $docs_slug ) {

            $docs_post_slug = $docs_slug;

        } elseif ( $builtin_doc_page != 1 && $docs_page ) {

            $post_info = get_post( $docs_page );
            $docs_post_slug = $post_info->post_name;

        } else {

            $docs_post_slug = 'docs';

        }

        return $docs_post_slug;
    }
    
    public static function get_docs_archive() {

        $builtin_doc_page = BetterDocs_DB::get_settings('builtin_doc_page');
        $docs_slug = BetterDocs_DB::get_settings('docs_slug');
        $docs_page = BetterDocs_DB::get_settings('docs_page');

        if ( $builtin_doc_page == 1 && $docs_slug ) {

            $docs_post_slug = $docs_slug;

        } elseif ( $builtin_doc_page != 1 && $docs_page ) {

            $post_info = get_post( $docs_page );
            $docs_post_slug = $post_info->post_name;
            
        } else {

            $docs_post_slug = 'docs';
            
        }

        return $docs_post_slug;
    }

    public static function docs_category_slug() {

        $category_slug = BetterDocs_DB::get_settings('category_slug');
        if( empty ( $category_slug ) ) {
            $category_slug = 'docs-category';  
        } 
        return $category_slug;
    } 

    /**
     *
     * Register post type and taxonomies
     *
     * @since    1.0.0
     *
     */
    public static function register_post() {

        $singular_name = BetterDocs_DB::get_settings('breadcrumb_doc_title');
        

        /**
         * Register category taxonomy
         */
        $category_labels = array(
            'name'             => esc_html__('Docs Categories', 'betterdocs'),
            'singular_name'    => esc_html__('Docs Category', 'betterdocs'),
            'all_items'        => esc_html__('Docs Categories', 'betterdocs'),
            'parent_item'      => esc_html__('Parent Docs Category', 'betterdocs'),
            'parent_item_colon'=> esc_html__('Parent Docs Category:', 'betterdocs'),
            'edit_item'        => esc_html__('Edit Category', 'betterdocs'),
            'update_item'      => esc_html__('Update Category', 'betterdocs'),
            'add_new_item'     => esc_html__('Add New Docs Category', 'betterdocs'),
            'new_item_name'    => esc_html__('New Docs Name', 'betterdocs'),
            'menu_name'        => esc_html__('Categories', 'betterdocs')
        );

        $category_args = array(
            'hierarchical'      => true,
            'public'            => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'             => true,
            'show_in_rest'          => true,
            'has_archive' => true,
        );

        $category_args['rewrite'] = apply_filters( 'betterdocs_category_rewrite', array( 'slug' => self::$cat_slug, 'with_front' => false ));
        
        register_taxonomy( self::$category, array( self::$post_type ), $category_args );


        /**
         * Register post type
         */
        $labels = array(
            'name'               => ($singular_name) ? $singular_name : 'Docs',
            'singular_name'      => ($singular_name) ? $singular_name : 'Docs',
            'menu_name'          => esc_html__('BetterDocs', 'betterdocs'),
            'name_admin_bar'     => esc_html__('Docs', 'betterdocs'),
            'add_new'            => esc_html__('Add New', 'betterdocs'),
            'add_new_item'       => esc_html__('Add New Docs', 'betterdocs'),
            'new_item'           => esc_html__('New Docs', 'betterdocs'),
            'edit_item'          => esc_html__('Edit Docs', 'betterdocs'),
            'view_item'          => esc_html__('View Docs', 'betterdocs'),
            'all_items'          => esc_html__('All Docs', 'betterdocs'),
            'search_items'       => esc_html__('Search Docs', 'betterdocs'),
            'parent_item_colorn' => null,
            'not_found'          => esc_html__('No docs found', 'betterdocs'),
            'not_found_in_trash' => esc_html__('No docs found in trash', 'betterdocs')
        );

        $betterdocs_articles_caps = apply_filters( 'betterdocs_articles_caps', 'edit_posts', 'article_roles' );

        $args = array(
            'labels'               => $labels,
            'description'          => esc_html__('Add new doc from here', 'betterdocs'),
            'public'               => true,
            'public_queryable'     => true,
            'exclude_from_search'  => false,
            'show_ui'              => true,
            'show_in_menu'         => false,
            'show_in_admin_bar'    => $betterdocs_articles_caps,
            'query_var'            => true,
            'capability_type'      => 'post',
            'hierarchical'         => true,
            'menu_position'        => self::$menu_position,
            'show_in_rest'         => true,
            'menu_icon'            => BETTERDOCS_ADMIN_URL . '/assets/img/betterdocs-icon-white.svg', 100,
            'supports'             => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions', 'custom-fields', 'comments')
        );

        $builtin_doc_page = BetterDocs_DB::get_settings('builtin_doc_page');

        if ( $builtin_doc_page == 'off' ) {

            $args['has_archive'] = false;

        } else {

            $args['has_archive'] = self::$docs_archive;

        }
        

        $args['rewrite'] = apply_filters('betterdocs_docs_rewrite', array( 'slug' => self::$docs_archive, 'with_front' => false ));

        register_post_type(self::$post_type, $args);

        flush_rewrite_rules();

        /**
         * Register tag taxonomy
         */
        $tags_labels = array(
            'name'                       => esc_html__('Docs Tags', 'betterdocs'),
            'singular_name'              => esc_html__('Tag', 'betterdocs'),
            'search_items'               => esc_html__('Search Tags', 'betterdocs'),
            'popular_items'              => esc_html__('Popular Tags', 'betterdocs'),
            'all_items'                  => esc_html__('All Tags', 'betterdocs'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => esc_html__('Edit Tag', 'betterdocs'),
            'update_item'                => esc_html__('Update Tag', 'betterdocs'),
            'add_new_item'               => esc_html__('Add New Tag', 'betterdocs'),
            'new_item_name'              => esc_html__('New Tag Name', 'betterdocs'),
            'separate_items_with_commas' => esc_html__('Separate tags with commas', 'betterdocs'),
            'add_or_remove_items'        => esc_html__('Add or remove tags', 'betterdocs'),
            'choose_from_most_used'      => esc_html__('Choose from the most used tags', 'betterdocs'),
            'menu_name'                  => esc_html__('Tags', 'betterdocs'),
        );

        $tag_args = array(
            'hierarchical'          => true,
            'labels'                => $tags_labels,
            'show_ui'               => true,
            'update_count_callback' => '_update_post_term_count',
            'show_admin_column'     => true,
            'query_var'             => true,
            'show_in_rest'          => true
        );

        $tag_slug = BetterDocs_DB::get_settings('tag_slug');

        if ($tag_slug) {
            $tag_args['rewrite'] = array('slug' => $tag_slug, 'with_front' => false);
        } else {
            $tag_args['rewrite'] = array('slug' => 'docs-tag', 'with_front' => false);
        }

        register_taxonomy(self::$tag, array(self::$post_type), $tag_args);

    }

    /**
     * Added post type to allowed for rest api
     *
     * @param  array $post_types Get the docs post types.
     * @return array
     *
     * @since    1.0.0
     *
     */
    public static function rest_api_allowed_post_types($post_types) {
        $post_types[] = self::$post_type;

        return $post_types;
    }

    /**
     * load media for taxonomy category image
     *
     * @since    1.0.0
     */
    public static function load_media() {
        wp_enqueue_media();
    }

    /**
     * Default the taxonomy's terms' order if it's not set.
     *
     * @param string $tax_slug The taxonomy's slug.
     */
    public static function default_term_order($tax_slug) {
        $terms = get_terms($tax_slug, array('hide_empty' => false));
        $order = self::get_max_taxonomy_order($tax_slug);
        foreach ($terms as $term) {
            if (!get_term_meta($term->term_id, 'doc_category_order', true)) {
                update_term_meta($term->term_id, 'doc_category_order', $order);
                $order++;
            }
        }
    }

    /**
     * Order the terms on the admin side.
     */
    public static function admin_order_terms() {
        $screen = function_exists('get_current_screen') ? get_current_screen() : '';
        if (in_array($screen->id, array('toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings'))) {
            self::default_term_order('doc_category');
        }

        if (!isset($_GET['orderby']) && !empty($screen) && !empty($screen->base) && $screen->base === 'edit-tags' && $screen->taxonomy === 'doc_category') {
            self::default_term_order($screen->taxonomy);
            add_filter('terms_clauses', array(__CLASS__, 'set_tax_order'), 10, 3);
        }
    }

    /**
     * Get the maximum doc_category_order for this taxonomy. This will be applied to terms that don't have a tax position.
     */
    private static function get_max_taxonomy_order($tax_slug) {
        global $wpdb;
        $max_term_order = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT MAX( CAST( tm.meta_value AS UNSIGNED ) )
				FROM $wpdb->terms t
				JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = '%s'
				JOIN $wpdb->termmeta tm ON tm.term_id = t.term_id WHERE tm.meta_key = 'doc_category_order'",
                $tax_slug
            )
        );
        $max_term_order = is_array($max_term_order) ? current($max_term_order) : 0;
        return (int) $max_term_order === 0 || empty($max_term_order) ? 1 : (int) $max_term_order + 1;
    }

    /**
     * Re-Order the taxonomies based on the doc_category_order value.
     *
     * @param array $pieces     Array of SQL query clauses.
     * @param array $taxonomies Array of taxonomy names.
     * @param array $args       Array of term query args.
     */
    public static function set_tax_order($pieces, $taxonomies, $args) {
        foreach ($taxonomies as $taxonomy) {
            global $wpdb;
            if ($taxonomy === 'doc_category') {
                $join_statement = " LEFT JOIN $wpdb->termmeta AS term_meta ON t.term_id = term_meta.term_id AND term_meta.meta_key = 'doc_category_order'";

                if (!self::does_substring_exist($pieces['join'], $join_statement)) {
                    $pieces['join'] .= $join_statement;
                }
                $pieces['orderby'] = 'ORDER BY CAST( term_meta.meta_value AS UNSIGNED )';
            }
        }
        return $pieces;
    }

    /**
     * Order the taxonomies on the front end.
     */
    public static function front_end_order_terms() {
        if (!is_admin()) {
            add_filter('terms_clauses', array(__CLASS__, 'set_tax_order'), 10, 3);
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
     * Default the taxonomy's terms' order if it's not set.
     *
     * @param string $tax_slug The taxonomy's slug.
     */
    public static function get_manage_docs() {
        $terms = get_terms('knowledge_base', array('hide_empty' => false));
        if($terms) {
            echo '<select name="term_meta[knowledge_base]" id="knowledge_base">';
            echo '<option> ' . esc_html__('Select an option') . ' </option>';
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            echo '</select>';
        }
    }

    /**
     * Add a form field in the new category page
     *
     * @since 1.0.0
    */
    public static function add_doc_category_meta($taxonomy) { ?>
        <?php 
        do_action( 'betterdocs_doc_category_add_form_before' );
        ?>
        <div class="form-field term-group">
            <label for="doc-category-order"><?php esc_html_e('Order', 'betterdocs'); ?></label>
            <input type="number" id="doc-category-order" style="width:100px" name="term_meta[order]" value="">
        </div>
        <div class="form-field term-group">
            <label for="doc-category-image-id"><?php esc_html_e('Category Icon', 'betterdocs'); ?></label>
            <input type="hidden" id="doc-category-image-id" name="term_meta[image-id]" class="custom_media_url" value="">
            <div id="doc-category-image-wrapper">
                <?php echo '<img src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">'; ?>
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
     * @since 1.0.0
    */
    public static function save_doc_category_meta($term_id) {
        if (isset($_POST['term_meta'])) {
            $term_meta = get_option("doc_category_$term_id");
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key) {
                if (isset($_POST['term_meta'][$key])) {
                    add_term_meta($term_id, "doc_category_$key", $_POST['term_meta'][$key]);
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
        }
        if ( isset($_POST['doc_category_kb']) ) {
            $doc_category_kb = $_POST['doc_category_kb'];
            update_term_meta($term_id, "doc_category_knowledge_base", $doc_category_kb);
        }
    }

    /**
     * Edit the form field
     *
     * @since 1.0.0
    */
    public static function update_doc_category_meta($term, $taxonomy) { ?>
        <?php
        $term_meta = get_option("doc_category_$term->term_id");
        $cat_order = get_term_meta($term->term_id, 'doc_category_order', true);
        $cat_icon_id = get_term_meta($term->term_id, 'doc_category_image-id', true);

        do_action( 'betterdocs_doc_category_update_form_before', $term );

        ?>
            
        <tr class="form-field term-group-wrap">
            <th scope="row">
            <label for="doc-category-id"><?php esc_html_e('Category Id', 'betterdocs'); ?></label>
            </th>
            <td>
            <input type="text" id="doc-category-id" style="width:100px" name="" value="<?php echo $_GET["tag_ID"] ?>" readonly>
            </td>
        </tr>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="doc-category-order"><?php esc_html_e('Order', 'betterdocs'); ?></label>
            </th>
            <td>
                <input type="number" id="doc-category-order" style="width:100px" name="term_meta[order]"
                    value="<?php echo $cat_order ? $cat_order : ''; ?>">
            </td>
        </tr>
        <tr class="form-field term-group-wrap batterdocs-cat-media-upload">
            <th scope="row">
                <label for="doc-category-image-id"><?php esc_html_e('Image', 'betterdocs'); ?></label>
            </th>
            <td>
                <input type="hidden" id="doc-category-image-id" name="term_meta[image-id]" value="<?php echo $cat_icon_id; ?>">
                <div id="doc-category-image-wrapper">
                    <?php
                        if ( $cat_icon_id ) {
                            echo wp_get_attachment_image( $cat_icon_id, 'thumbnail' );
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
     * @since 1.0.0
    */
    public static function updated_doc_category_meta( $term_id ) {
        if ( isset($_POST['term_meta']) ) {
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key) {
                if (isset($_POST['term_meta'][$key])) {
                    update_term_meta($term_id, "doc_category_$key", $_POST['term_meta'][$key]);
                }
            }
        }
        if ( isset($_POST['doc_category_kb']) ) {
            $doc_category_kb = $_POST['doc_category_kb'];
            update_term_meta($term_id, "doc_category_knowledge_base", $doc_category_kb);
        }
    }

    /*
     * Add script
     *
     * @since 1.0.0
    */
    public static function add_script() {
        global $current_screen;
        if($current_screen->id == 'edit-doc_category'){    
        ?>
        <script>
        jQuery(document).ready(function($) {
            function betterdocs_media_upload(button_class) {
                var _custom_media = true,
                    _betterdocs_send_attachment = wp.media.editor.send.attachment;
                $('body').on('click', button_class, function(e) {
                    var button_id = '#' + $(this).attr('id');
                    var send_attachment_bkp = wp.media.editor.send.attachment;
                    var button = $(button_id);
                    _custom_media = true;
                    wp.media.editor.send.attachment = function(props, attachment) {
                        if (_custom_media) {
                            $('#doc-category-image-id').val(attachment.id);
                            $('#doc-category-image-wrapper').html(
                                '<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
                            );
                            $('#doc-category-image-wrapper .custom_media_image').attr('src', attachment
                                .url).css('display', 'block');
                        } else {
                            return _betterdocs_send_attachment.apply(button_id, [props, attachment]);
                        }
                    }
                    wp.media.editor.open(button);
                    return false;
                });
            }
            betterdocs_media_upload('.betterdocs_tax_media_button.button');
            $('body').on('click', '.doc_tax_media_remove', function() {
                $('#doc-category-image-id').val('');
                $('#doc-category-image-wrapper').html(
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
                        $('#doc-category-image-wrapper').html('');
                    }
                }
            });
        });
        </script>
<?php }
}
}

BetterDocs_Docs_Post_Type::init();
