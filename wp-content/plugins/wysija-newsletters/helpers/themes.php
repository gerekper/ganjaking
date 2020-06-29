<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_themes extends WYSIJA_object{
    var $extensions = array('png', 'jpg', 'jpeg', 'gif');

    function __construct(){
        parent::__construct();
    }

    /**
     * try three different methods for http request,
     * @param type $url
     * @return type
     */
    function getInstalled(){
        $helperF=WYSIJA::get('file','helper');
        $filenameres=$helperF->exists('themes');

        if(!$filenameres['result']) {
            return array();
        }

        $installedThemes = array();
        $files = scandir($filenameres['file']);
        foreach($files as $filename){
            if(!in_array($filename, array('.','..','.DS_Store','Thumbs.db','__MACOSX')) && is_dir($filenameres['file'].DS.$filename) && file_exists($filenameres['file'].DS.$filename.DS.'style.css')){
                $installedThemes[]=$filename;
            }
        }

        return $installedThemes;
    }

    /**
    * Get theme information (name, thumbnail, screenshot)
    * @param string $theme
    * @return array
    */
    function getInformation($theme) {
        // allowed file extensions

        $fileHelper = WYSIJA::get('file', 'helper');

        // scan for thumbnail
        $thumbnail = NULL;
        for($i = 0; $i < count($this->extensions); $i++) {
            // check file presence
            $result = $fileHelper->exists('themes'.DS.$theme.DS.'thumbnail.'.$this->extensions[$i]);
            if($result['result'] !== FALSE){
                $thumbnail = $fileHelper->url('thumbnail.'.$this->extensions[$i], 'themes'.DS.$theme);
            }
        }

        // scan for screenshot
        $screenshot = NULL;
        $width = $height = 0;
        for($i = 0; $i < count($this->extensions); $i++) {
            // check file presence
            $result = $fileHelper->exists('themes'.DS.$theme.DS.'screenshot.'.$this->extensions[$i]);
            if($result['result'] !== FALSE){
                $screenshot = $fileHelper->url('screenshot.'.$this->extensions[$i], 'themes'.DS.$theme);
                $dimensions = @getimagesize($result['file']);
                if($dimensions !== FALSE) {
                    list($width, $height) = $dimensions;
                }
            }
        }

        return array(
            'name' => $theme,
            'thumbnail' => $thumbnail,
            'screenshot' => $screenshot,
            'width' => $width,
            'height' => $height
        );
    }

    function getStylesheet($theme)
    {
        $fileHelper = WYSIJA::get('file', 'helper');

        $result = $fileHelper->exists('themes'.DS.$theme.DS.'style.css');
        if($result['result'] === FALSE) {
            return NULL;
        } else {
            $stylesheet = file_get_contents($result['file']);
            // clear all line breaks, tabs
            $stylesheet = preg_replace('/[\n|\t|\'|\"]/', '', $stylesheet);
            // remove extra spaces
            $stylesheet = preg_replace('/[\s]+/', ' ', $stylesheet);
            return $stylesheet;
        }
    }

    function getData($theme)
    {
        // allowed file extensions
        $this->extensions = array('png', 'jpg', 'jpeg', 'gif');

        $fileHelper = WYSIJA::get('file', 'helper');

        // scan for header
        $header = NULL;
        for($i = 0; $i < count($this->extensions); $i++) {
            // check file presence
            $result = $fileHelper->exists('themes'.DS.$theme.DS.'images'.DS.'header.'.$this->extensions[$i]);
            if($result['result'] !== FALSE) {
                $dimensions = @getimagesize($result['file']);
                if($dimensions !== FALSE and count($dimensions) >= 2) {
                    // if image width is inferior to the max width, adjust it
                    list($width, $height) = $dimensions;
                    $ratio = round(($width / $height) * 1000) / 1000;
                    $width = 600;
                    $height = (int)($width / $ratio);
                    // format data
                    $header = array(
                        'alignment' => 'center',
                        'type' => 'header',
                        'text' => null,
                        'image' => array(
                            'src' => $fileHelper->url('header.'.$this->extensions[$i], 'themes'.DS.$theme.DS.'images'),
                            'width' => $width,
                            'height' => $height,
                            'url' => null,
                            'alt' => __("Header", WYSIJA),
                            'alignment' => 'center'
                        )
                    );
                }
            }
        }

        // scan for footer
        $footer = NULL;
        for($i = 0; $i < count($this->extensions); $i++) {
            // check file presence
            $result = $fileHelper->exists('themes'.DS.$theme.DS.'images'.DS.'footer.'.$this->extensions[$i]);
            if($result['result'] !== FALSE) {
                $dimensions = @getimagesize($result['file']);
                if($dimensions !== FALSE and count($dimensions) >= 2) {
                    // if image width is inferior to the max width, adjust it
                    list($width, $height) = $dimensions;
                    $ratio = round(($width / $height) * 1000) / 1000;
                    $width = 600;
                    $height = (int)($width / $ratio);
                    // format data
                    $footer = array(
                        'alignment' => 'center',
                        'type' => 'footer',
                        'text' => null,
                        'image' => array(
                            'src' => $fileHelper->url('footer.'.$this->extensions[$i], 'themes'.DS.$theme.DS.'images'),
                            'width' => $width,
                            'height' => $height,
                            'url' => null,
                            'alt' => __('Footer', WYSIJA),
                            'alignment' => 'center'
                        )
                    );
                }
            }
        }

        // scan for divider
        $divider = NULL;
        for($i = 0; $i < count($this->extensions); $i++) {
            // check file presence
            $result = $fileHelper->exists('themes'.DS.$theme.DS.'images'.DS.'divider.'.$this->extensions[$i]);
            if($result['result'] !== FALSE) {
                $dimensions = @getimagesize($result['file']);
                if($dimensions !== FALSE and count($dimensions) >= 2) {
                    // if image width is inferior to the max width, adjust it
                    list($width, $height) = $dimensions;
                    $ratio = round(($width / $height) * 1000) / 1000;
                    $width = 564;
                    $height = (int)($width / $ratio);
                    // format data
                    $divider = array(
                        'type' => 'divider',
                        'src' => $fileHelper->url('divider.'.$this->extensions[$i], 'themes'.DS.$theme.DS.'images'),
                        'width' => $width,
                        'height' => $height
                    );
                }
            }
        }

        return array(
            'header' => $header,
            'footer' => $footer,
            'divider' => $divider
        );
    }

    function getDivider($theme = 'default') {
        $divider = NULL;

        if($theme === 'default') {
            $dividersHelper = WYSIJA::get('dividers', 'helper');
            $divider = $dividersHelper->getDefault();
        } else {
            // scan for divider
            $fileHelper = WYSIJA::get('file', 'helper');
            for($i = 0; $i < count($this->extensions); $i++) {
                // check file presence
                $result = $fileHelper->exists('themes'.DS.$theme.DS.'images'.DS.'divider.'.$this->extensions[$i]);
                if($result['result'] !== FALSE) {
                    $dimensions = @getimagesize($result['file']);
                    if($dimensions !== FALSE and count($dimensions) >= 2) {
                        // if image width is inferior to the max width, adjust it
                        list($width, $height) = $dimensions;
                        $ratio = round(($width / $height) * 1000) / 1000;
                        $width = 564;
                        $height = (int)($width / $ratio);
                        // format data
                        $divider = array(
                            'src' => $fileHelper->url('divider.'.$this->extensions[$i], 'themes'.DS.$theme.DS.'images'),
                            'width' => $width,
                            'height' => $height
                        );
                    }
                }
            }
        }

        return $divider;
    }

    /**
     * create a temporary if needed
     * @param type $ZipfileResult
     * @param type $theme_key
     * @return type
     */
    function installTheme($ZipfileResult,$manual=false){
        $helperF=WYSIJA::get('file','helper');
        if(!@file_exists($ZipfileResult)){
            /* 1- make the dir where the file is supposed to be received */

            $dirtemp=$helperF->makeDir();
            $dirtemp=str_replace("/",DS,$dirtemp);
            /* 2- create a temp file */
            $tempzipfile=$dirtemp.basename($_REQUEST['theme_key']).'.zip';

            $fp = fopen($tempzipfile, 'w');
            fwrite($fp, $ZipfileResult);
            fclose($fp);
        }else $tempzipfile=$ZipfileResult;

        //  chmod($tempzipfile, 0777);
        /* 3- unzip file*/
        $dirtheme=$helperF->makeDir('themes');

        if(!$dirtheme){
            $upload_dir = wp_upload_dir();
            $this->error(sprintf(__('The folder "%1$s" is not writable, please change the access rights to this folder so that Mailpoet can setup itself properly.',WYSIJA),$upload_dir['basedir'])."<a target='_blank' href='http://codex.wordpress.org/Changing_File_Permissions'>".__('Read documentation',WYSIJA)."</a>");
            return false;
        }

        //$timecreated=time();
        $timecreated = substr( md5(rand()), 0, 20);
        $dirthemetemp=$helperF->makeDir('temp'.DS.'temp_'.$timecreated,0755);

        $zipclass=WYSIJA::get('zip','helper');
        if(!$zipclass->unzip_wp($tempzipfile,$dirthemetemp)) {
            $this->error("Error while decompressing archive.");
            $helperF->rrmdir($dirthemetemp);
            return false;
        }

        /*check that there is just one folder*/
        $files = scandir($dirthemetemp);
        foreach($files as $filename){
            if(!in_array($filename, array('.','..','.DS_Store','Thumbs.db')) && !is_dir($dirthemetemp.DS.$filename)){
                //there is another file in there while there should be only a folder
                $this->error('In your zip there should be one folder only, with the content of your theme within.');
                $helperF->rrmdir($dirthemetemp);
                return false;
            }else{
                if(!in_array($filename, array('.','..','.DS_Store','Thumbs.db')))    $theme_key=$filename;
            }
        }

        // making sure this theme only has allowed files in its folders and subfolders
        $dir_iterator = new RecursiveDirectoryIterator($dirthemetemp);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif', 'css', 'txt');

        foreach ($iterator as $file) {

            if( !$file->isDir() && !in_array( pathinfo( $file->getBasename(), PATHINFO_EXTENSION ), $allowed_extensions)) {
                $this->error(sprintf('Your theme is not valid. It can only contain files that have the following extensions: "%s"', join('", "', $allowed_extensions)));
                $helperF->rrmdir($dirthemetemp);
                return false;
            }
        }

         if(!$theme_key){
            $this->error('There was an error while unzipping the file :'.  esc_html($tempzipfile).' to the folder: '.esc_html($dirthemetemp));
            $helperF->rrmdir($dirthemetemp);
            return false;
        }

        unlink($tempzipfile);

        //make sure we don't overwrite existing folder
        if($manual && !isset($_REQUEST['overwriteexistingtheme']) && file_exists($dirtheme.DS.$theme_key)){
            $this->error(sprintf(__('A theme called %1$s exists already. To overwrite it, tick the corresponding checkbox before uploading.',WYSIJA),'<strong>'.$theme_key.'</strong>'),1);
            $helperF->rrmdir($dirthemetemp);
            return false;
        }

        /*array of files needing to be in the package*/
        $result=true;

            $listoffilestocheck=array($theme_key,'style.css');

            foreach($listoffilestocheck as $keyindex=> $fileexist){
                if($keyindex==0)    $testfile=$listoffilestocheck[0];
                else    $testfile=$listoffilestocheck[0].DS.$fileexist;
                if($manual){
                    if(!file_exists($dirthemetemp.DS.$testfile)){
                        //this is not a theme file let's remove it
                        if($keyindex==0)    $this->error('Missing directory :'.  esc_html($testfile));
                        else    $this->error('Missing file :'.$dirthemetemp.DS.esc_html($testfile));

                        $result=false;

                    }
                }
            }


        /* 2- move folder to uploads/wysija/themes/ */
        if($result){
             //once it's all good we move the theme to the right folder
            $helperF->rcopy($dirthemetemp.DS.$listoffilestocheck[0],$dirtheme.DS.$listoffilestocheck[0]);

            $this->notice(sprintf(__('The theme %1$s has been installed on your site.',WYSIJA),'<strong>'.  esc_html($theme_key).'</strong>'));
        }else{
            $this->error(__("We could not install your theme. It appears it's not in the valid format.",WYSIJA),1);
        }
         //remove the temporary gfolder
        $helperF->rrmdir($dirthemetemp);

        return $result;
    }


    function delete($themekey){

        $helperF=WYSIJA::get('file','helper');
        $dirtheme=$helperF->makeDir('themes'.DS.$themekey);

        $helperF->rrmdir($dirtheme);

        if(!file_exists($dirtheme)){
            $this->notice(sprintf(__('Theme %1$s has been deleted.',WYSIJA),'<strong>'.$themekey.'</strong>'));
            return true;
        }else{
            $this->error(sprintf(__('Theme %1$s could not be deleted.',WYSIJA),'<strong>'.$themekey.'</strong>'));
            return false;
        }
    }
}