<?php
namespace memberpress\downloads\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models,
    memberpress\downloads\helpers as helpers;

class Files extends lib\BaseCptCtrl {
  public function load_hooks() {
    add_action('save_post', array($this, 'save_post_data'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('wp_ajax_mpdl_file_upload', array($this, 'process_file_upload'));
    add_action('manage_mpdl-file_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    add_action('before_delete_post', array($this, 'remove_files'));
    add_action('in_admin_header', array($this, 'admin_header'), 0);
    add_filter('post_type_link', array($this, 'modify_permalink'), 10, 2);
    add_filter('post_row_actions', array($this, 'add_row_actions'), 10, 2);
    add_filter('manage_mpdl-file_posts_columns', array($this, 'alter_columns'));
    add_filter('manage_edit-mpdl-file_sortable_columns', array($this, 'alter_sortable_columns'));
    add_filter('posts_clauses', array($this, 'custom_column_orderby'), 10, 2);

    $this->ctaxes = array('file-categories', 'file-tags');
  }

  public static function save_post_data($post_id) {
    # Verify nonce
    if(!wp_verify_nonce(isset($_POST[models\File::$nonce_str]) ? $_POST[models\File::$nonce_str] : '', models\File::$nonce_str . wp_salt())) {
      return $post_id;
    }
    # Skip ajax
    if(defined('DOING_AJAX')) {
      return;
    }

    $file = new models\File($post_id);

    // Cleanup replaced files
    if(!empty($file->filename) && $file->filename !== $_POST['mpdl-file-name']) {
      $file->destroy_files();
    }

    $file->filename        = $_POST['mpdl-file-name'];
    $file->filesize        = $_POST['mpdl-file-size'];
    $file->filetype        = $_POST['mpdl-file-type'];
    $file->validate();
    $file->store_meta();

    if(!$file->permalink_is_available()) {
      // We need to add a query param to the redirect to show the warning
      add_filter('redirect_post_location', function($location) {
        return add_query_arg('mpdl-warn', 'mpdl-permalink-warning', $location);
      });
    }
  }

  public static function admin_enqueue_scripts() {
    global $current_screen;

    if($current_screen->post_type === models\File::$cpt) {
      $locals = array(
        'nonce' => wp_create_nonce(models\File::$nonce_str),
        'post_id' => get_the_ID(),
      );
      wp_dequeue_script('autosave'); //Disable auto-saving
      wp_enqueue_script('mpdl-files-js', base\JS_URL . '/admin_files.js', array('jquery'), base\VERSION);
      wp_localize_script('mpdl-files-js', 'MpdlFile', $locals);
      wp_register_script('jquery-iframe-transport', base\JS_URL . '/vendor/jquery_file_upload/jquery.iframe-transport.min.js', array('jquery'), base\VERSION);
      wp_register_script('jquery-ui-widget', base\JS_URL . '/vendor/jquery_file_upload/jquery.ui.widget.min.js', array('jquery'), base\VERSION);
      wp_enqueue_script('jquery-fileupload', base\JS_URL . '/vendor/jquery_file_upload/jquery.fileupload.min.js', array('jquery', 'jquery-ui-widget', 'jquery-iframe-transport'), base\VERSION);
      wp_enqueue_script('jquery-form-validator', base\JS_URL . '/vendor/jquery.form-validator.min.js', array('jquery'), '2.3.79');
      wp_register_script('tooltipster-js', base\JS_URL . '/vendor/tooltipster.bundle.min.js', array('jquery'), base\VERSION);
      wp_enqueue_script('clipboard-js', base\JS_URL . '/vendor/clipboard.min.js', array('tooltipster-js'), base\VERSION);
      wp_enqueue_script('mpdl-table-control-js', base\JS_URL . '/table-control.js', array(), base\VERSION);
      wp_enqueue_style('mpcs-files', base\CSS_URL . '/admin_files.css', array(), base\VERSION);
      wp_register_style('tooltipster-borderless-theme', base\CSS_URL . '/vendor/tooltipster-sideTip-borderless.min.css', array(), base\VERSION);
      wp_enqueue_style('tooltipster-css', base\CSS_URL . '/vendor/tooltipster.bundle.min.css', array('tooltipster-borderless-theme'), base\VERSION);
    }
  }

  /**
  * Process the file uploaded via ajax
  * @see add_action('wp_ajax_mpdl_file_upload')
  * @return void Sends JSON repsonse string then wp_dies
  */
  public static function process_file_upload() {
    check_ajax_referer(models\File::$nonce_str, 'file_nonce');
    require_once(base\LIB_PATH . '/vendor/UploadHandler.php');
    $original_filename = $_FILES['mpdl-file-upload']['name'];
    $file_parts = pathinfo($original_filename);
    $clean_filename = sanitize_title($file_parts['filename']);
    // Prepend the filename with a MD5 the of site and post id
    $_FILES['mpdl-file-upload']['name'] = hash("crc32b", get_current_blog_id() . $_POST['post_id']) . '_' . $clean_filename . '.' . $file_parts['extension'];
    $upload_options = array(
      'param_name'     => 'mpdl-file-upload',
      'upload_dir'     => models\File::upload_dir(),
      'max_file_size'  => wp_max_upload_size(),
      'print_response' => false,
      'upload_url'     => models\File::upload_url(),
      'image_versions' => array(
        'thumbnail' => array(
          'max_width'  => 150,
          'max_height' => 150,
        )
      ),
    );
    $upload_handler = new \UploadHandler($upload_options);
    $upload_response = $upload_handler->get_response();

    if(isset($upload_response['mpdl-file-upload'][0]->error)) {
      wp_send_json_error(array('message' => sprintf(models\File::translate_error_message($upload_response['mpdl-file-upload'][0]->error), $original_filename)), 500);
    }
    else {
      $uploaded_file = $upload_response['mpdl-file-upload'][0];
      $response = array(
        'message'  => 'success',
        'filename' => $_FILES['mpdl-file-upload']['name'],
        'type'     => $uploaded_file->type,
        'hsize'    => helpers\Files::human_filesize($uploaded_file->size),
        'size'     => $uploaded_file->size,
      );
      if(isset($uploaded_file->thumbnailUrl)) {
        $response = \array_merge($response, array('thumb' => $uploaded_file->thumbnailUrl));
      }
      else {
        $response = \array_merge($response, array('thumb' => helpers\Files::file_thumb($uploaded_file->type)));
      }
      wp_send_json_success($response);
    }
  }

  /**
  * Content for custom columns
  * @see add_action('manage_mpdl-file_posts_custom_column')
  * @param string $column Current Column
  * @param int $post_id Current Post ID
  * @return void
  */
  public static function custom_column_content($column, $post_id) {
    if($column === 'downloads') {
      $file = new models\File($post_id);
      printf('<a href="%s">%s</a>', admin_url('admin.php?page=mpdl_stats&file_name='.$file->post_title), models\FileStat::download_count($file));
    }
  }

  public static function remove_files($post_id) {
    global $post_type;
    if($post_type === models\File::$cpt) {
      $file = new models\File($post_id);

      if(isset($file->ID) && $file->ID > 0) {
        $file->destroy_files();
      }
    }
  }

  /**
  * Handle custom column sorting
  * @see add_filter('posts_clauses')
  * @param array $clauses Query clauses
  * @param WP_Query Current query
  * @return array $clauses
  */
  public static function custom_column_orderby($clauses, $query) {
    global $wpdb;
    $db = new lib\Db;
    $orderby = $query->get('orderby');

    if($query->is_main_query() && $orderby === 'download_count') {
      $order = $query->get('order');
      if(!in_array($order, array('ASC', 'DESC'))) {
        $order = 'ASC';
      }
      $clauses['join'] .= "LEFT JOIN {$db->file_downloads} mpdl_fd ON mpdl_fd.file_id = {$wpdb->posts}.ID";
      $clauses['orderby'] = "mpdl_fd.download_count {$order}";
    }

    return $clauses;
  }

  /**
  * Permalink to file download
  * @see add_filter('post_type_link')
  * @param string $url Current permalink
  * @param WP_Post $post Current post
  * @return string Modified permalink to file download
  */
  public static function modify_permalink($url, $post) {
    if($post->post_type === models\File::$cpt) {
      $file = new models\File($post->ID);
      if(!empty($file->filename)) {
        return $file->permalink($url);
      }
    }

    return $url;
  }

  /**
  * Append row actions to list page
  * @see add_filter('post_row_actions')
  * @param array $actions Current row actions
  * @param WP_Post current Post
  * @return array filtered row actions
  */
  public static function add_row_actions($actions, $post) {
    if($post->post_type === models\File::$cpt) {
      $permalink = get_the_permalink($post);
      $shortcode = "[mpdl-file-link file_id={$post->ID}]";
      $actions[] = helpers\Files::post_row_action(__('Copy URL', 'memberpress-downloads'), array('mpdl-copy-url', 'mpdl-clipboard-link'), array('data-clipboard-text' => $permalink));
      $actions[] = helpers\Files::post_row_action(__('Copy Shortcode', 'memberpress-downloads'), array('mpdl-copy-shortcode', 'mpdl-clipboard-link'), array('data-clipboard-text' => $shortcode));
    }

    return $actions;
  }

  /**
  * Add custom columns
  * @see add_filter('manage_mpdl-file_posts_columns')
  * @param array $columns
  * @return array $columns
  */
  public static function alter_columns($columns) {
    $columns['downloads'] = __('Downloads', 'memberpress-downloads');

    return $columns;
  }

  /**
  * Add custom sortable columns
  * @see add_filter('manage_edit-mpdl-file_sortable_columns')
  * @param array $columns
  * @return array $columns
  */
  public static function alter_sortable_columns($columns) {
    $columns['downloads'] = 'download_count';

    return $columns;
  }

  public function register_post_type() {
    $this->cpt = (object)array(
      'slug' => models\File::$cpt,
      'config' => array(
        'labels' => array(
          'name' => __('Files', 'memberpress-downloads'),
          'singular_name' => __('File', 'memberpress-downloads'),
          'add_new_item' => __('Add New File', 'memberpress-downloads'),
          'edit_item' => __('Edit File', 'memberpress-downloads'),
          'new_item' => __('New File', 'memberpress-downloads'),
          'view_item' => __('View File', 'memberpress-downloads'),
          'search_items' => __('Search Files', 'memberpress-downloads'),
          'not_found' => __('No Files found', 'memberpress-downloads'),
          'not_found_in_trash' => __('No Files found in Trash', 'memberpress-downloads'),
          'parent_item_colon' => __('Parent File:', 'memberpress-downloads')
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_rest' => false,
        'show_in_menu' => base\PLUGIN_NAME,
        'has_archive' => true,
        'capability_type' => 'page',
        'hierarchical' => false,
        'register_meta_box_cb' => function () {
          $this->add_meta_boxes();
        },
        'rewrite' => array('slug' => models\File::$permalink_slug, 'with_font' => false),
        'supports' => array('title'),
      )
    );

    if(!empty($this->ctaxes)) {
      $this->cpt->config['taxonomies'] = $this->ctaxes;
    }

    register_post_type(models\File::$cpt, $this->cpt->config);
  }

  public function add_meta_boxes() {
    add_meta_box(models\File::$cpt . '-stats', __("Stats", 'memberpress-downloads'), array($this, 'stats_meta_box'), models\File::$cpt, "side", "high");
    add_meta_box(models\File::$cpt . '-meta', __("File Options", 'memberpress-downloads'), array($this, 'options_meta_box'), models\File::$cpt, "normal", "high");
  }

  public function options_meta_box($post) {
    $file = new models\File($post->ID);
    $max_upload_size = wp_max_upload_size();
    require_once(base\VIEWS_PATH . '/admin/files/options_meta_box.php');
  }

  public function stats_meta_box($post) {
    $file = new models\File($post->ID);
    require_once(base\VIEWS_PATH . '/admin/files/stats/meta_box.php');
  }

  public function admin_header() {
    global $current_screen;

    if($current_screen->post_type === models\File::$cpt || $current_screen->id === 'mp-downloads_page_mpdl_stats') {
      ?>
      <div id="mpdl-admin-header"><img class="mpdl-logo" src="https://memberpress.com/wp-content/themes/mp-bb-child/assets/images/memberpress-logo-color.svg" /></div>
      <?php
    }
  }
}
