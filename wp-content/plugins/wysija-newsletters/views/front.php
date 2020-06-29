<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_view_front extends WYSIJA_view{
	var $controller='';
	function __construct(){

	}
        /**
         * deprecated, but kept for conflict with plugin Magic action box
         * until it's fixed.
         * @param type $print
         */
        function addScripts($print=true){
        }
}