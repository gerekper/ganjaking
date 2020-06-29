<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_file extends WYSIJA_object{

    function __construct(){
        parent::__construct();
    }

    /**
     * Get the full path of a file
     * @param type $csvfilename
     * @param type $folder
     * @return boolean
     */
    function exists($fileFolder=false){
        $upload_base_dir = $this->getUploadBaseDir();

        $filename=str_replace('/',DS,$upload_base_dir).DS.'wysija'.DS.$fileFolder;
        if(!file_exists($filename)){
            return array('result'=>false,'file'=>$filename);
        }

        return array('result'=>true,'file'=>$filename);
    }

    /**
     * Get the full path of a file
     * @param type $csvfilename
     * @param type $folder
     * @return boolean
     */
    function get($csvfilename,$folder='temp'){
        $upload_base_dir = $this->getUploadBaseDir();

        $filename=$upload_base_dir.DS.'wysija'.DS.$folder.DS.$csvfilename;
        if(!file_exists($filename)){
            $filename=$upload_base_dir.DS.$csvfilename;
            if(!file_exists($filename)) $filename=false;
        }

        return $filename;
    }

    // Description: create a directory recursively if possible
    // Parameters: (Name of the directory, permissions)
    // Returns: Directory path. False if impossible to create folder.
    function makeDir($folder='temp',$mode=0755){
        $upload_base_dir = $this->getUploadBaseDir();

        if(strpos(str_replace('/',DS,$folder),str_replace('/',DS,$upload_base_dir))!==false){
            $dirname=$folder;
        }else{
            $dirname=$upload_base_dir.DS.'wysija'.DS.$folder.DS;
        }
        if(!file_exists($dirname)){
            if(!mkdir($dirname, $mode,true)){
                $this->error('Cannot create folder '.$dirname.' try to create the folder manually');
                return false;
            }
            chmod($dirname,$mode);
        }
        return $dirname;
    }


    function getUploadDir($folder=false){
        $upload_base_dir = $this->getUploadBaseDir();

        $dirname=$upload_base_dir.DS.'wysija'.DS;
        if($folder) $dirname.=$folder.DS;
        if(file_exists($dirname))    return $dirname;
        return false;
    }

    function getUploadBaseDir(){
        $upload_dir = wp_upload_dir();

        if(!isset($upload_dir['basedir'])){
            if(isset($upload_dir['error'])) $this->wp_error('<b>WordPress error</b> : '.$upload_dir['error'],1);
            return false;
        }

        //having .. in a path is not safe as it can lead to some parent path where we don't have control
        if(strpos($upload_dir['basedir'], '..')!==false){
            $pathsections=$pathsectionsc=explode(DS, $upload_dir['basedir']);

            while($key = array_search('..', $pathsections)){
                unset($pathsections[$key]);
                unset($pathsections[$key-1]);
                $newpatharray=array();
                foreach($pathsections as $ky=>$vy){
                    $newpatharray[]=$vy;
                }
                $pathsections=$newpatharray;
            }
            $cleanBaseDir=implode(DS, $pathsections);

            if(file_exists($cleanBaseDir)){
                $upload_dir['basedir']=$cleanBaseDir;
            }
        }


        return $upload_dir['basedir'];
    }

    /**
     * make a temporary file
     * @param type $content
     * @param type $key
     * @param type $format
     * @return type
     */
    function temp($content,$key='temp',$format='.tmp'){
        $tempDir=$this->makeDir();

        if(!$tempDir)   return false;

        $time_created = substr( md5(rand()), 0, 20);
        $file_name = $key.'-'.$time_created.$format;

        $handle=fopen($tempDir.$file_name, 'w');
        fwrite($handle, $content);
        fclose($handle);

        return array('path'=>$tempDir.$file_name,'name'=>$file_name, 'url'=>$this->url($file_name,'temp'));
    }

    /**
     * Get the url of a wysija file based on the filename and the wysija folder
     * @param type $filename
     * @param type $folder
     * @return string
     */
    function url($filename,$folder='temp'){
        $upload_dir = wp_upload_dir();

        if(file_exists($upload_dir['basedir'].DS.'wysija')){
            $url=$upload_dir['baseurl'].'/wysija/'.$folder.'/'.$filename;
        }else{
            $url=$upload_dir['baseurl'].'/'.$filename;
        }

        return str_replace(DS,'/',$url);
    }

    /*
     *
     */
    function clear(){
        $folders_to_clear = array("import","temp");
        $filename_removal = array("import-","export-", 'export_userids-');
        $deleted=array();
        foreach($folders_to_clear as $folder){
            $path=$this->getUploadDir($folder);
            /* get a list of files from this folder and clear them */
            if(!$path) continue;
            $files = scandir($path);
            foreach($files as $filename){
                if(!in_array($filename, array('.','..',".DS_Store","Thumbs.db"))){
                    if(preg_match('/('.implode($filename_removal,'|').')[a-f0-9]*\.(csv|txt)/',$filename,$match)){
                       $deleted[]=$path.$filename;
                    }
                }
            }
        }
        foreach($deleted as $filename){
            if(file_exists($filename)){
                $filename=str_replace('/',DS,$filename);
                unlink($filename);
            }
        }

    }

    function rrmdir($dir) {
      if(strpos($dir, '..')!==false){
          $this->error('Path is not safe, cannot contain ..');
          return false;
      }
      if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file){
            if ($file != "." && $file != "..") $this->rrmdir("$dir".DS."$file");
        }

        if(!rmdir($dir)){
            chmod($dir, 0777);
            rmdir($dir);
        }


      }
      else if (file_exists($dir)) {
          $dir=str_replace('/',DS,$dir);
          unlink($dir);
      }
    }

    function rcopy($src, $dst) {
      if(strpos($src, '..')!==false || strpos($dst, '..')!==false){
          $this->error('src : '.$src);
          $this->error('dst : '.$dst);
          $this->error('Path is not safe, cannot contain ..');
          return false;
      }else{
          if (file_exists($dst)) $this->rrmdir($dst);
      }

      if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file){
            if ($file != "." && $file != "..") $this->rcopy("$src/$file", "$dst/$file");
        }

      }
      else if (file_exists($src)) {
          copy(str_replace('/',DS,$src), str_replace('/',DS,$dst));
      }
    }
}

