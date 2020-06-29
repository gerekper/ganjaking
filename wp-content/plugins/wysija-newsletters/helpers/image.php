<?php
defined('WYSIJA') or die('Restricted access');
require_once(dirname(__FILE__).DS.'file.php');
class WYSIJA_help_image extends WYSIJA_help_file{

  function __construct(){
    parent::__construct();
  }

  /**
     * get a list of images from a directory
     * @param type $template
     * @return type
     */
    public function get_list_directory($template =' default'){
        $foldersTocheck = 'themes'.DS.$template.DS.'img'.DS.'public';
        $url = 'themes/'.$template.'/img/public';
        $images_types_allowed = array('jpg','png','jpeg');
        $listed = array();

        $path = $this->getUploadDir($foldersTocheck);

        /* get a list of files from this folder and clear them */
        if(file_exists($path)){
           $files = scandir($path);
            $i=1;
            foreach($files as $file_name){
                if(!in_array($file_name, array('.','..','.DS_Store','Thumbs.db'))){
                    if(preg_match('/.*\.('.implode($images_types_allowed,'|').')/',$file_name,$match)){
                        $image_template = array(
                           'path'=> $path.$file_name,
                           'width'=> 0,
                           'height'=> 0,
                           'url'=> $this->url($file_name,$url),
                           'thumb_url'=>$this->url($file_name,$url),
                           'identifier'=>'tmpl-'.$template.$i,
                           );

                        $listed['tmpl-'.$template.$i] = $this->valid_image($image_template);
                        $i++;
                    }
                }
            }

            return $listed;
        }

    }

    /**
     * make sure the image is valid, has a src and has an height and width
     * @param type $post_image
     * @return null
     */
    public function valid_image($post_image){
        if(!isset($post_image['src']) && isset($post_image['url'])) $post_image['src'] = $post_image['url'];
        if(isset($post_image['src'])) {
            // check that height & width have been set, if not try to calculate
            if(empty($post_image['height']) || empty($post_image['width']) || (empty($post_image['height']) && empty($post_image['width']))) {

                try {
                    $image_info = getimagesize($post_image['src']);

                    if($image_info !== false) {
                        $post_image['width'] = $image_info[0];
                        $post_image['height'] = $image_info[1];

                    }else{
                        // if allow_url_fopen is off we need to convert the url image into a local file
                        $image_src = dirname(dirname(dirname(WYSIJA_UPLOADS_DIR))).wp_make_link_relative($post_image['src']);
                        $image_info = getimagesize($image_src);
                        if($image_info !== false) {
                            $post_image['width'] = $image_info[0];
                            $post_image['height'] = $image_info[1];
                        }

                    }
                } catch(Exception $e) {
                    return null;
                }
            }
            return $post_image;
        } else {
            return null;
        }
    }

}

