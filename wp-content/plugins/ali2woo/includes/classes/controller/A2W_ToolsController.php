<?php

/**
 * Description of A2W_ToolsController
 *
 * @author andrey
 * 
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_ToolsController')) {

    class A2W_ToolsController extends A2W_AbstractAdminPage {

        private $importClazz = "A2W_ImportShopmaster";

        public function __construct() {
            parent::__construct(__('Tools', 'ali2woo'), __('Tools', 'ali2woo'), 'import', 'a2w_tools', 1000);  

            $this->add_script('a2w-admin-tools-js', '/assets/js/admin_tools.js');
            $this->add_data_script('a2w-admin-tools-js', 'a2w_admin_tools_data', array(
                'please_wait_data_loads' => _x('Please wait, data loads..', 'Status', 'ali2woo'),
                'process_update_d_of_d' => _x('Process update %d of %d.', 'Status', 'ali2woo'),
                'process_update_d_of_d_erros_d' => _x('Process update %d of %d. Errors: %d.', 'Status', 'ali2woo'),
                'complete_result_updated_d_erros_d' => _x('Complete! Result updated: %d; errors: %d.', 'Status', 'ali2woo'),
            ));

            if (a2w_check_defined('A2W_SHOPMASTER_IMPORT_CLASS')) {
                $this->importClazz = A2W_SHOPMASTER_IMPORT_CLASS;
            }
        }

        public function render($params = array()) {

            if(!isset($_POST['reset']) && isset($_FILES['filecsv'])){
                $dir  = wp_upload_dir();
                $uploadfile = $dir['subdir'] .'/'. basename($_FILES['filecsv']['name']);

                $upload_state = A2W_ResultBuilder::buildOk();
                $product_ids = array();

            
                if (!move_uploaded_file($_FILES['filecsv']['tmp_name'], $dir['basedir'].$uploadfile)) {
                    $upload_state = A2W_ResultBuilder::buildError('upload file error');
                }
    
                if($upload_state['state']!="error"){
                    $import = new $this->importClazz($dir['basedir'].$uploadfile);
                    if(!$import->validate()){
                        $upload_state = A2W_ResultBuilder::buildError('invalid csv file');
                    }
                    $product_ids = $import->fetch_product_ids();
                }

                $this->model_put("upload_state", $upload_state);
                $this->model_put("product_ids", $product_ids);
                $this->model_put("file", $uploadfile);
            }

            $this->include_view('tools.php');
        }
        
    }
}
