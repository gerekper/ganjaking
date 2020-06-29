<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_zip extends WYSIJA_object{

    function __construct(){
      parent::__construct();
    }

    /**
     * reeusing wordpress method
     * @param type $temp_file_addr
     * @param type $to
     * @return type 
     */
    function unzip($temp_file_addr, $to){
        $filesystem = WP_Filesystem();
        $dounzip = unzip_file($temp_file_addr, $to);

        if ( is_wp_error($dounzip) ) {

            //DEBUG
            $error = $dounzip->get_error_code();
            $data = $dounzip->get_error_data($error);
            $this->error($dounzip->get_error_message());

            return false;

        }
        return true;
    }
    /*
     * adapted from wp
     */
    function unzip_wp($file, $to){
        $filesystem = WP_Filesystem();
	// Unzip can use a lot of memory, but not this much hopefully
	@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );

	$to = str_replace("/",DS,$to);

	if (class_exists('ZipArchive')) return $this->_unzip_file_ziparchive($file, $to);
	// Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
	return $this->_unzip_file_pclzip($file, $to);
    }
    
    
    /*
     * adapted from wp
     */
    function _unzip_file_ziparchive($file, $to) {
        /*careful WordPress global*/
        global $wp_filesystem;

        $z = new ZipArchive();

        // PHP4-compat - php4 classes can't contain constants
        $zopen = $z->open($file, 4); // -- ZIPARCHIVE::CHECKCONS = 4
        
        if ($zopen !== true){
            $this->error("Archive is not of a correct format!");
            return false;
        }

        $z->extractTo($to); 

        $z->close();

        return true;
    }

    /*
     * adapted from wp
     */
    function _unzip_file_pclzip($file, $to) {
            global $wp_filesystem;

            // See #15789 - PclZip uses string functions on binary data, If it's overloaded with Multibyte safe functions the results are incorrect.
            if ( ini_get('mbstring.func_overload') && function_exists('mb_internal_encoding') ) {
                    $previous_encoding = mb_internal_encoding();
                    mb_internal_encoding('ISO-8859-1');
            }

            if(file_exists(ABSPATH . 'wp-admin/includes/class-pclzip.php')) require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

            $archive = new PclZip($file);

            $archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);

            if ( isset($previous_encoding) )
                    mb_internal_encoding($previous_encoding);

            // Is the archive valid?
            if ( !is_array($archive_files) ){
                $this->error("Archive is not of a correct format!");
                return false;
            }
                   

            if ( 0 == count($archive_files) ){
                $this->error("Archive is empty!");
                return false;
            }

            // Extract the files from the zip
            foreach ( $archive_files as $file ) {
                $filedest=str_replace("/",DS,$to . $file['filename']);
                if ( $file['folder']){
                    $to=str_replace("/",DS,$to);
                    if(file_exists($to))  chmod($to,0777);

                    //$folderTest=str_replace(array("/"),array(DS),$to . $file['filename']);
                    if(is_dir($to) ){
                        //$this->error($to.' is dir with chmod '.substr(sprintf('%o', fileperms($to)), -4));
                        if(!mkdir($filedest,0777)){
                            $this->error('cannot created folder : '.$filedest);
                            $to=dirname($to).DS;
                            $filedest=str_replace("/",DS,$to . $file['filename']);
                            if(!mkdir($filedest,0777)) {
                                $this->error('Still cannot created folder : '.$filedest);
                                return false;
                            }
                        }
                    }
                    
                    if(file_exists($filedest))  chmod($filedest,0777);
                    continue;
                }

                if ( '__MACOSX/' === substr($file['filename'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
                        continue;
                
                if ( ! $wp_filesystem->put_contents( $filedest, $file['content'], 0644) ){
                    //try another method
                    if ( ! ($fp = @fopen($filedest, 'w')) )
                            return false;
                    @fwrite($fp, $file['content']);
                    @fclose($fp);
                    
                    if(!file_exists($filedest)){
                        $this->error('Could not copy file : '. $filedest);
                        return false;
                    }
                }            
            }

            return true;
    }
    
}

