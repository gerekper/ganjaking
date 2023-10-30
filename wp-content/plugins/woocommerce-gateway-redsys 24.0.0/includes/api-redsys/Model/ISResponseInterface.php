<?php
	if(!interface_exists('ISResponseInterface')){
		interface ISResponseInterface{
			public function setResult($code);
			public function getResult();
		}
	}