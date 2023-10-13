<?php

/* * class
 * Description of A2W_WooCommerceProductEditController
 *
 * @author Ali2Woo Team
 * 
 * @autoload: a2w_admin_init
 * 
 * @ajax: true
 */
if (!class_exists('A2W_WooCommerceProductEditController')) {

    class A2W_WooCommerceProductEditController extends A2W_AbstractController {

        public function __construct() {
            parent::__construct();

            add_action('current_screen', array($this, 'current_screen'));
            add_action('edit_form_advanced', array($this, 'edit_form_advanced'));
            add_action('a2w_after_import', array($this, 'edit_form_advanced'));
            add_action('wp_ajax_a2w_get_image_by_id', array($this, 'ajax_get_image_by_id'));
            add_action('wp_ajax_a2w_save_image', array($this, 'ajax_save_image'));
            add_action('wp_ajax_a2w_upload_sticker', array($this, 'ajax_upload_sticker'));
            add_action('wp_ajax_a2w_edit_image_url', array($this, 'ajax_edit_image_url'));

            add_filter('get_sample_permalink_html', array($this, 'get_sample_permalink_html'), 10, 2);
        }

        public function get_sample_permalink_html($return, $id ){
            $return .= '<button type="button" data-id="' . $id . '" class="sync-ali-product button button-small hide-if-no-js">' . __("AliExpress Sync", 'ali2woo') . '</button>';
   
            return $return;
        }

        function current_screen($current_screen) {
            if ($current_screen->in_admin() && ($current_screen->id == 'product' || $current_screen->id == 'ali2woo_page_a2w_import')) {
                wp_enqueue_script('a2w-admin-script', A2W()->plugin_url() . '/assets/js/admin_script.js', array('jquery'),  A2W()->version);
              
                $lang_data = array(
                    'process_loading_d_of_d_erros_d' => _x('Process loading %d of %d. Errors: %d.', 'Status', 'ali2woo'),
                    'load_button_text' => _x('Load %d images', 'Status', 'ali2woo'),
                    'all_images_loaded_text' => _x('All images loaded', 'Status', 'ali2woo'),
                );
                wp_localize_script('a2w-admin-script', 'a2w_external_images_data', array('lang' => $lang_data));

                $lang_data = array(
                    'sync_successfully' => _x('Synchronized successfully.', 'Status', 'ali2woo'),
                    'sync_failed' => _x('Sync failed.', 'Status', 'ali2woo'),
                );
                wp_localize_script('a2w-admin-script', 'a2w_sync_data', array('lang' => $lang_data));


                wp_enqueue_style('a2w-admin-style', A2W()->plugin_url() . '/assets/css/admin_style.css', array(), A2W()->version);

                wp_enqueue_style('a2w-wc-spectrum-style', A2W()->plugin_url() . '/assets/js/spectrum/spectrum.css', array(), A2W()->version);
                wp_enqueue_script('a2w-wc-spectrum-script', A2W()->plugin_url() . '/assets/js/spectrum/spectrum.js', array(), A2W()->version);

                wp_enqueue_script('tui-image-editor-fabric', A2W()->plugin_url() . '/assets/js/image-editor/fabric.js', array('jquery'), A2W()->version);
                wp_enqueue_script('tui-code-snippet', A2W()->plugin_url() . '/assets/js/image-editor/tui-code-snippet.min.js', array('jquery'), A2W()->version);
                wp_enqueue_script('tui-image-editor-FileSaver', A2W()->plugin_url() . '/assets/js/image-editor/FileSaver.min.js', array('jquery'), A2W()->version);
                wp_enqueue_script('tui-image-editor', A2W()->plugin_url() . '/assets/js/image-editor/tui-image-editor.js', array('jquery'), A2W()->version);

                wp_enqueue_script('a2w-wc-pe-script', A2W()->plugin_url() . '/assets/js/wc_pe_script.js', array(), A2W()->version);
                wp_enqueue_style('a2w-wc-pe-style', A2W()->plugin_url() . '/assets/css/wc_pe_style.css', array(), A2W()->version);
            }
        }

        function edit_form_advanced($post) {
            $current_screen = get_current_screen();
            if ($current_screen && $current_screen->in_admin() && ($current_screen->id == 'product' || $current_screen->id == 'ali2woo_page_a2w_import')) {
                $srickers = a2w_get_setting('image_editor_srickers',array());
                
                foreach($srickers as $key=>$sricker){
                    if(substr($sricker, 0, strlen("http")) !== "http"){
                        $srickers[$key] = A2W()->plugin_url().$sricker;
                    }
                }
                
                $this->model_put('srickers', $srickers);
                $this->include_view('product_edit_photo.php');
            }
        }
        
        function ajax_get_image_by_id(){
            if (empty($_POST['attachment_id'])) {
                $result = A2W_ResultBuilder::buildError("waiting for attachment_id...");
            } else {
                $image_url = wp_get_attachment_url($_POST['attachment_id']);
                if(!$image_url){
                    $result = A2W_ResultBuilder::buildError("waiting for attachment_id...");
                }else{
                    $result = A2W_ResultBuilder::buildOk(array('image_url' => $image_url));
                }
            }
            echo json_encode($result);
            wp_die();
        }

        function ajax_save_image() {
            $attachment_model = new A2W_Attachment();
            $product_import_model = new A2W_ProductImport();

            $result = A2W_ResultBuilder::buildOk();
            if (empty($_POST['view']) || !in_array($_POST['view'], array('product', 'import'))) {
                $result = A2W_ResultBuilder::buildError("waiting view...");
            } else if ($_POST['view'] == 'import' && (empty($_POST['product_id']) || !($product = $product_import_model->get_product($_POST['product_id'])))) {
                $result = A2W_ResultBuilder::buildError("waiting product_id...");
            } else if (empty($_POST['attachment_id'])) {
                $result = A2W_ResultBuilder::buildError("waiting for attachment_id...");
            } else if (empty($_POST['data'])) {
                $result = A2W_ResultBuilder::buildError("Need data!");
            } else {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $view = $_POST['view'];

                if ($view == 'product') {
                    $attachment_id = intval($_POST['attachment_id']);
                    $attachment_parent_id = wp_get_post_parent_id($attachment_id);

                    $new_attachment_id = $attachment_model->create_attachment_from_data($attachment_parent_id, $_POST['data'], array('inner_post_id' => $attachment_parent_id));
                    if (is_wp_error($new_attachment_id)) {
                        $result = A2W_ResultBuilder::buildError($new_attachment_id->get_error_message($new_attachment_id->get_error_code()));
                    } else {
                        $external_image_url = get_post_meta($attachment_id, '_a2w_external_image_url', true);
                        if ($external_image_url) {
                            update_post_meta($new_attachment_id, '_a2w_external_image_url', $external_image_url);
                        }

                        global $wpdb;
                        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value=%d WHERE meta_key='_thumbnail_id' and meta_value=%d", $new_attachment_id, $attachment_id));
                        $rows = $wpdb->get_results("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_product_image_gallery' && meta_value like '%" . intval($attachment_id) . "%'", ARRAY_A);
                        foreach ($rows as $row) {
                            $ids = array_map("intval", explode(",", $row['meta_value']));
                            if (in_array($attachment_id, $ids)) {
                                array_splice($ids, array_search($attachment_id, $ids), 1, $new_attachment_id);
                                $wpdb->query($wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value=%s WHERE meta_id=%d", implode(",", $ids), $row['meta_id']));
                            }
                        }
                        A2W_Utils::delete_attachment($attachment_id, true);
                        
                        $src = wp_get_attachment_image_src($new_attachment_id,array(100,100));
                        
                        $result = A2W_ResultBuilder::buildOk(array('attachment_id'=>$new_attachment_id, 'attachment_url' => wp_get_attachment_url($new_attachment_id), 'croped_attachment_url' => $src?$src[0]:''));
                    }
                } else if ($view == 'import') {
                    $attachment_id = $_POST['attachment_id'];

                    $new_attachment_id = $attachment_model->create_attachment_from_data(0, $_POST['data']);
                    if (is_wp_error($new_attachment_id)) {
                        $result = A2W_ResultBuilder::buildError($new_attachment_id->get_error_message($new_attachment_id->get_error_code()));
                    } else {
                        if (!isset($product['tmp_edit_images'][$attachment_id])) {
                            $product['tmp_edit_images'][$attachment_id] = array();
                        }

                        if (isset($product['tmp_edit_images'][$attachment_id]['attachment_id'])) {
                            A2W_Utils::delete_attachment($product['tmp_edit_images'][$attachment_id]['attachment_id'], true);
                        }

                        $tmp_all_images = A2W_Utils::get_all_images_from_product($product);

                        $product['tmp_edit_images'][$attachment_id]['attachment_id'] = $new_attachment_id;
                        $product['tmp_edit_images'][$attachment_id]['attachment_url'] = wp_get_attachment_url($new_attachment_id);
                        $product['tmp_edit_images'][$attachment_id]['external_image_url'] = $tmp_all_images[$attachment_id]['image'];

                        $product_import_model->save_product($_POST['product_id'], $product);

                        $result = A2W_ResultBuilder::buildOk(array('image_id'=>$attachment_id, 'attachment_id'=>$new_attachment_id, 'attachment_url' => $product['tmp_edit_images'][$attachment_id]['attachment_url']));
                    }
                }
            }

            echo json_encode($result);
            wp_die();
        }

        function ajax_upload_sticker() {
            $result = A2W_ResultBuilder::buildOk();

            if ($_FILES) {
                foreach ($_FILES as $file => $array) {
                    if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                        $result = A2W_ResultBuilder::buildError("upload_sticker_error : " . $_FILES[$file]['error']);
                    }else{
                        $movefile = wp_handle_upload($array, array('test_form' => false));
                        if ($movefile && !isset($movefile['error'])) {
                            $srickers = a2w_get_setting('image_editor_srickers',array());
                            if(!in_array($movefile['url'], $srickers)){
                                $srickers = array_merge(array($movefile['url']), $srickers);
                                a2w_set_setting('image_editor_srickers', $srickers);
                            }
                            $result = A2W_ResultBuilder::buildOk(array('sticker_url' => $movefile['url']));
                        } else {
                            $result = A2W_ResultBuilder::buildError("upload_sticker_error: " . $movefile['error']);
                        }
                    }
                }
            }

            echo json_encode($result);
            wp_die();
        }

        function ajax_edit_image_url() {
            $result = A2W_ResultBuilder::buildOk();
            if (empty($_POST['url'])) {
                $result = A2W_ResultBuilder::buildError("waiting url...");
            }else{
                $url = base64_encode($_POST['url']);
                $result = A2W_ResultBuilder::buildOk(array('url'=>A2W()->plugin_url() . '/includes/cdn.php?url=' . $url.'&_sign='.a2w_sign_request(array('url'=>$url))));
            }
            echo json_encode($result);
            wp_die();
        }
    }

}

