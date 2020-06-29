<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_dividers extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    /**
     * Returns all dividers
     * @return array
     */
    function getAll() {
        $fileHelper = WYSIJA::get('file', 'helper');
        $dirHandle = $fileHelper->exists('dividers');

        if($dirHandle['result'] === FALSE) {
            return array();
        } else {
            $dividers = array();
            $files = scandir($dirHandle['file']);
            foreach($files as $filename) {
                // don't add meta files
                if(in_array($filename, array('.', '..', '.DS_Store', 'Thumbs.db')) === FALSE) {
                    // get dimensions of image
                    $dimensions = @getimagesize($dirHandle['file'].DS.$filename);
                    if($dimensions !== FALSE) {
                        $width = (int)$dimensions[0];
                        $height = (int)$dimensions[1];
                    } else {
                        $width = 564;
                        $height = 1;
                    }

                    // only add divider if height is superior to 0
                    if($height > 0) {
                        $ratio = round(($width / $height) * 1000) / 1000;
                        $width = min($width, 564);
                        $height = (int)($width / $ratio);

                        $dividers[] = array(
                            'src' => $fileHelper->url($filename, 'dividers'),
                            'width' => $width,
                            'height' => $height
                        );
                    }
                }
            }
            return $dividers;
        }
    }

    /**
    * Get default divider
    * @return array
    */
    function getDefault() {
        $fileHelper = WYSIJA::get('file', 'helper');
        return array(
            'src' => $fileHelper->url('solid.jpg', 'dividers'),
            'width' => 564,
            'height' => 1
        );
    }
}