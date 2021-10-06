<?php

/**
 * Description of A2W_ReviewBackendController
 *
 * @author MA_GROUP
 * 
 * @autoload: a2w_admin_init
 * 
 * @ajax: true
 */
if (!class_exists('A2W_ReviewBackendController')) {

    class A2W_ReviewBackendController {

        private $upd_rvws_task_id = "a2w_product_update_reviews_manual";
        
        function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'assets'));

            add_action('wp_ajax_a2w_arvi_remove_reviews', array($this, 'ajax_remove_reviews'));
            
            add_action('wp_ajax_a2w_arvi_remove_product_reviews', array($this, 'ajax_remove_product_reviews'));
             
            add_action('wp_ajax_a2w_arvi_get_comment_photos', array($this, 'ajax_get_comment_photos'));
            add_action('wp_ajax_a2w_arvi_save_comment_photos', array($this, 'ajax_save_comment_photos'));

            add_filter('a2w_ajax_product_info', array($this, 'product_info'), 4, 10);
            add_filter('comment_row_actions', array($this, 'row_actions'), 10, 2);

            if (is_admin()) {
                // add bulk action to priducts list
                add_filter('a2w_wcpl_bulk_actions_init', array($this, 'bulk_actions_init'));
            }
        }

        public function assets() {

            $current_screen = get_current_screen();

            if ($current_screen->id === "product" || $current_screen->id === "edit-comments") {

                wp_enqueue_style('a2w-review-comment-widget-style', A2W()->plugin_url() . '/assets/css/review/comment_widget.css', array(), A2W()->version);
                wp_enqueue_script('a2w-review-comment-widget-script', A2W()->plugin_url() . '/assets/js/review/comment_widget.js', array(), A2W()->version, true);
                
                $lang_data = array(
                    'current_page' => $current_screen->id,
                    'i18n_please_wait' => 'Please wait...',
                    'i18n_done' => 'Done!',
                    'i18n_error_occur' => 'Server error occurred!'
                );
                
                if ( $current_screen->id === "product" && isset($_GET['post'])) $lang_data['product_id'] = $_GET['post'];

                wp_localize_script('a2w-review-comment-widget-script', 'WPDATA', $lang_data);
            }
        }

        public function row_actions($actions, $comment) {
            if (A2W_Review::get_comment_photos($comment->comment_ID)) {
                $actions = array_merge($actions, array('a2w_comment_edit_photo_link' => sprintf('<a id="a2w-%1$d" href="#">%2$s</a>', $comment->comment_ID, 'Edit Photos')));
            }
            return $actions;
        }

        function bulk_actions_init($bulk_actions_array) {
            if (a2w_get_setting('load_review')) {
                $bulk_actions_array[0][] = $this->upd_rvws_task_id;
                $bulk_actions_array[1][$this->upd_rvws_task_id] = 'Update reviews';
            }

            return $bulk_actions_array;
        }

        public function product_info($content, $post_id, $external_id) {
            $time_value = get_post_meta($post_id, '_a2w_reviews_last_update', true);
            $time_value = $time_value ? date("Y-m-d H:i:s", $time_value) : 'not loaded';

            $content[] = "Reviews update: <span class='a2w_value'>" . $time_value . "</span>";

            return $content;
        }

        public function ajax_remove_reviews() {

            a2w_init_error_handler();
            $result = A2W_ResultBuilder::buildOk();

            try {
                $comments = A2W_Review::get_all_review_ids();
                A2W_Review::remove_reviews_by_ids($comments);

                restore_error_handler();
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            }
            echo json_encode($result);
            wp_die();
        }
        
        public function ajax_remove_product_reviews(){
            
            
            $result = A2W_ResultBuilder::buildOk();
            
            $post_id = isset($_POST['id']) ? $_POST['id'] : false;
            
            if (!$post_id) {
                echo json_encode(A2W_ResultBuilder::buildError("Product related with this ID not found"));
                wp_die();
            }
            
            a2w_init_error_handler();
            try {
           
                $comments = A2W_Review::get_product_review_ids($post_id);
                A2W_Review::remove_reviews_by_ids($comments);
                restore_error_handler();
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            }
            
            echo json_encode($result);
            wp_die();    
        }

        public function ajax_get_comment_photos() {

            $result = array("state" => "ok", "message" => "");
  
            $comment_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $photos = A2W_Review::get_comment_photos($comment_id);
            if ($photos) {
                $result['photos'] = $photos;
            } else {
                $result['state'] = 'error';
                $result['message'] = 'No photos available';
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_save_comment_photos() {
         
            $result = array("state" => "ok", "message" => "");
   
            $comment_id = isset($_POST['id']) ? $_POST['id'] : false;
            $photos = isset($_POST['photos']) ? $_POST['photos'] : false;

            if (is_numeric($comment_id) && is_array($photos)) {
                $photos = $this->normalizePhotoArray($photos);
                A2W_Review::save_comment_photos($comment_id, $photos);
            } else {
                $result['state'] = 'error';
                $result['message'] = 'Can`t save this data. Wrong data format. Try to reload the page and repeat this operation again.';
            }

            echo json_encode($result);
            

            wp_die();
        }
       
        private function normalizePhotoArray($photo_array){
            $result = array();
            foreach ($photo_array as $photo){
                $result[] =  $photo['photo_id'];   
            }  
            return $result;  
        }

    }

}
