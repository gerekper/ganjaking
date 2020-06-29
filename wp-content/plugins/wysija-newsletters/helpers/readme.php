<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_readme extends WYSIJA_object{
    var $changelog=array();

    function __construct(){
        parent::__construct();
    }

    function scan($file=false){
        if(!$file) $file = WYSIJA_DIR.'readme.txt';
        $handle = fopen($file, 'r');
        $content = fread ($handle, filesize ($file));
        fclose($handle);

        // get the changelog content from the readme
        $exploded = explode('== Changelog ==', $content);
        $exploded_versions = explode("\n=", $this->markdown_to_html_links($exploded[1]) );
        foreach($exploded_versions as $key=> $version){
            if(!trim($version)) unset($exploded_versions[$key]);
        }

        foreach($exploded_versions as $key => $version){
            $version_number = '';
            foreach(explode("\n", $version) as $key => $commented_line){
                if($key==0){
                    //extract version number
                    $expldoed_version_number = explode(' - ',$commented_line);
                    $version_number = trim($expldoed_version_number[0]);
                }else{
                    //strip the stars
                    if(!isset($this->changelog[$version_number])) $this->changelog[$version_number] = array();
                    if(trim($commented_line))    $this->changelog[$version_number][] = str_replace('* ', '', $commented_line);
                }
            }
        }

    }

    /**
     * preg_replace that will parse a txt and look for links starting with http, https, ftp, file
     * @param string $content
     * @return string
     */
    function markdown_to_html_links($content){
        $pattern = "/\[(.*?)\]\((.*?)\)/i";
        $replace = "<a href=\"$2\" target=\"_blank\" >$1</a>";
        return preg_replace($pattern, $replace, $content);
    }
}