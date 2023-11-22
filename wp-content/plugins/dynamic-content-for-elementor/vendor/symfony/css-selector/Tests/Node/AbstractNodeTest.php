<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DynamicOOOS\Symfony\Component\CssSelector\Tests\Node;

use DynamicOOOS\PHPUnit\Framework\TestCase;
use DynamicOOOS\Symfony\Component\CssSelector\Node\NodeInterface;
abstract class AbstractNodeTest extends TestCase
{
    /** @dataProvider getToStringConversionTestData */
    public function testToStringConversion(NodeInterface $node, $representation)
    {
        $this->assertEquals($representation, (string) $node);
    }
    /** @dataProvider getSpecificityValueTestData */
    public function testSpecificityValue(NodeInterface $node, $value)
    {
        $this->assertEquals($value, $node->getSpecificity()->getValue());
    }
    public abstract function getToStringConversionTestData();
    public abstract function getSpecificityValueTestData();
}
