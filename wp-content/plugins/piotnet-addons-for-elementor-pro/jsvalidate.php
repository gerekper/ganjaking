<?php

$errors = 0;
$errors_files = '\n';

$dh = opendir(__DIR__ . '/assets/js/');

while($file = readdir($dh)) {
	if ( substr_count( $file, ".js" ) == 1 ) {
		$contents = file_get_contents(__DIR__ . '/assets/js/' . $file);
		if ( substr_count( $contents, "[") !=  substr_count( $contents, "]") ) {
			$errors_files .= $file . '\n';
			$errors++;
		}
	}
}

$dh = opendir(__DIR__ . '/controls/');

while($file = readdir($dh)) {
	if ( substr_count( $file, ".php" ) == 1 ) {
		$contents = file_get_contents(__DIR__ . '/controls/' . $file);
		if ( substr_count( $contents, "[") !=  substr_count( $contents, "]") ) {
			$errors_files .= $file . '\n';
			$errors++;
		}
	}
}

$dh = opendir(__DIR__ . '/inc/');

while($file = readdir($dh)) {
	if ( substr_count( $file, ".php" ) == 1 ) {
		$contents = file_get_contents(__DIR__ . '/inc/' . $file);
		if ( substr_count( $contents, "[") !=  substr_count( $contents, "]") ) {
			$errors_files .= $file . '\n';
			$errors++;
		}
	}
}

$dh = opendir(__DIR__ . '/widgets/');

while($file = readdir($dh)) {
	if ( substr_count( $file, ".php" ) == 1 ) {
		$contents = file_get_contents(__DIR__ . '/widgets/' . $file);
		if ( substr_count( $contents, "[") !=  substr_count( $contents, "]") ) {
			$errors_files .= $file . '\n';
			$errors++;
		}
	}
}

define( 'PAFE_VALIDATE', '' );

// if ($errors != 0) {
// 	echo '<script>alert("BOT: Lỗi rồi đại ca ơi !' . $errors_files . '")</script>';
// }

?>