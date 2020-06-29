<?php

	/* Convert all args to data */
	function userpro_args_to_data( $args ) {
	$res = null;
		foreach($args as $k => $v) {
			if ( $v == '' || empty($v) ) {
				$res .= " data-$k='0'";
			} else {
				$v = htmlentities($v, ENT_QUOTES, "UTF-8");
				$res .= " data-$k='$v'";
			}
		}
		
		echo $res;
	}
