<?php
if(!class_exists('NinjaDB\ModelTrait')) {
	include 'src/ModelTrait.php';
}
if(!class_exists('NinjaDB\BaseModel')) {
	include 'src/BaseModel.php';
}

// make available ninjaDB as global scope
if(!function_exists('ninjaDB')) {
	function ninjaDB($table = false) {
		return new NinjaDB\BaseModel($table);
	}
}
