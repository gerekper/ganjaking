<?php
if (!defined('ABSPATH')) {
    exit;
}
class ThePlus_Import {

    public $message = "";
    public $attachments = false;
    function __construct() {

    }

    public function import_content($file){
            ob_start();
            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
            require_once($class_wp_importer);			
            require_once(THEPLUS_PLUGIN_PATH.'/vc_elements/import/class.wordpress-importer.php');
            $ptplus_import = new WP_Import();
            set_time_limit(0);
            $path = get_template_directory() . '/includes/import/files/' . $file;

            $ptplus_import->fetch_attachments = $this->attachments;
            $returned_value = $ptplus_import->import($file);
            if(is_wp_error($returned_value)){
                $this->message = __("An Error Occurred During Import", "pt_theplus");
            }
            else {
                $this->message = __("Content imported successfully", "pt_theplus");
            }
            ob_get_clean();
    }

}
global $my_ThePlus_Import;
$my_ThePlus_Import = new ThePlus_Import();



if(!function_exists('ptplus_dataImport'))
{
    function ptplus_dataImport()
    {
        global $my_ThePlus_Import;

        if ($_POST['import_attachments'] == 1)
            $my_ThePlus_Import->attachments = true;
        else
            $my_ThePlus_Import->attachments = false;

       $folder = THEPLUS_PLUGIN_URL."vc_elements/import/demo-page/";

        $my_ThePlus_Import->import_content($folder.$_POST['xml']);

        die();
    }

    add_action('wp_ajax_ptplus_dataImport', 'ptplus_dataImport');
}

if(!function_exists('ptplus_posts_dataImport'))
{
    function ptplus_posts_dataImport()
    {
        global $my_ThePlus_Import;

        if ($_POST['import_attachments'] == 1)
            $my_ThePlus_Import->attachments = true;
        else
            $my_ThePlus_Import->attachments = false;

       $folder = THEPLUS_PLUGIN_URL."vc_elements/import/demo-posts/";

        $my_ThePlus_Import->import_content($folder.$_POST['xml']);

        die();
    }

    add_action('wp_ajax_ptplus_posts_dataImport', 'ptplus_posts_dataImport');
}