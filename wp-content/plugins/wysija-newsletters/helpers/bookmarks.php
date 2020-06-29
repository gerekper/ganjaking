<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_bookmarks extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    /**
     * Get all bookmarks based on size
     * @param string $size
     * @return array
     */
    function getAll($size = 'medium', $theme = 'default') {
        $theme = basename($theme);
        $fileHelper = WYSIJA::get('file', 'helper');
        $dirHandle = $fileHelper->exists('bookmarks'.DS.$size);

        if($dirHandle['result'] === FALSE) {
            return array();
        } else {
            $bookmarks = array();

            // if size is medium and the current theme is not default, load theme's bookmarks
            if($size === 'medium' and $theme !== 'default') {
                $themeIcons = $this->getAllByTheme($theme, 'url');
                if(!empty($themeIcons)) {
                    $bookmarks['00'] = $themeIcons;
                }
            }

            $sourceDir = $dirHandle['file'];
            $iconsets = scandir($sourceDir);
            foreach($iconsets as $iconset) {
                // loop through each iconset
                if(in_array($iconset, array('.', '..', '.DS_Store', 'Thumbs.db')) === FALSE and is_dir($sourceDir.DS.$iconset)) {

                    // get all icons from current iconset
                    $icons = scandir($sourceDir.DS.$iconset);
                    foreach($icons as $icon) {
                        if(in_array($icon, array('.', '..', '.DS_Store', 'Thumbs.db')) === FALSE and strrpos($icon, '.txt') === FALSE) {
                            $info = pathinfo($sourceDir.DS.$iconset.DS.$icon);
                            $bookmarks[$iconset][basename($icon, '.'.$info['extension'])] = $fileHelper->url($icon, 'bookmarks'.DS.$size.DS.$iconset);
                        }
                    }
                }
            }
            return $bookmarks;
        }
    }

    /**
     * Get all bookmarks based on size for a given iconset
     * @param string $size
     * @param string $iconset
     * @return array
     */
    function getAllByIconset($size = 'medium', $iconset)
    {
        $iconset = basename($iconset);
        $fileHelper = WYSIJA::get('file', 'helper');
        $dirHandle = $fileHelper->exists('bookmarks'.DS.$size.DS.$iconset);

        if($dirHandle['result'] === FALSE) {
            return array();
        } else {
            $bookmarks = array();
            $sourceDir = $dirHandle['file'];
            $icons = scandir($sourceDir);
            foreach($icons as $icon) {
                if(in_array($icon, array('.', '..', '.DS_Store', 'Thumbs.db')) === FALSE and strrpos($icon, '.txt') === FALSE) {
                    $info = pathinfo($sourceDir.DS.$icon);
                    $dimensions = @getimagesize($sourceDir.DS.$icon);
                    $bookmarks[basename($icon, '.'.$info['extension'])] = array(
                        'src' => $fileHelper->url($icon, 'bookmarks/'.$size.'/'.$iconset),
                        'width' => $dimensions[0],
                        'height' => $dimensions[1]
                    );
                }
            }
            return $bookmarks;
        }
    }

    function getAllByTheme($theme, $type = 'all')
    {
        $theme = basename($theme);
        $fileHelper = WYSIJA::get('file', 'helper');
        $dirHandle = $fileHelper->exists('themes'.DS.$theme.DS.'bookmarks');

        if($dirHandle['result'] === FALSE) {
            return array();
        } else {
            $bookmarks = array();
            $sourceDir = $dirHandle['file'];
            $icons = scandir($sourceDir);
            foreach($icons as $icon) {
                if(in_array($icon, array('.', '..', '.DS_Store', 'Thumbs.db')) === FALSE and strrpos($icon, '.txt') === FALSE) {

                    if($type === 'all') {
                        $info = pathinfo($sourceDir.DS.$icon);
                        $dimensions = @getimagesize($sourceDir.DS.$icon);
                        $bookmarks[basename($icon, '.'.$info['extension'])] = array(
                            'src' => $fileHelper->url($icon, 'themes/'.$theme.'/bookmarks'),
                            'width' => $dimensions[0],
                            'height' => $dimensions[1]
                        );
                    } else if($type === 'url') {
                        $info = pathinfo($sourceDir.DS.$icon);
                        $bookmarks[basename($icon, '.'.$info['extension'])] = $fileHelper->url($icon, 'themes/'.$theme.'/bookmarks');
                    }
                }
            }
            return $bookmarks;
        }
    }
}