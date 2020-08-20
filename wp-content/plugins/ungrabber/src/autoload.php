<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

/** Register Merkulove Custom Autoloader. */
/** @noinspection PhpUnhandledExceptionInspection */
spl_autoload_register( function ( $class ) {

	$namespace = 'Merkulove\\';

	/** Bail if the class is not in our namespace. */
	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	/** Build the filename. */
	$file = realpath( __DIR__ );
	$file = $file . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';

	/** If the file exists for the class name, load it. */
	if ( file_exists( $file ) ) {
		/** @noinspection PhpIncludeInspection */
		include( $file );
	}

} );