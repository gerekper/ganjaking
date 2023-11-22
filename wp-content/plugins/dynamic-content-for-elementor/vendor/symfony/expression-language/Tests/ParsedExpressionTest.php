<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DynamicOOOS\Symfony\Component\ExpressionLanguage\Tests;

use DynamicOOOS\PHPUnit\Framework\TestCase;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\Node\ConstantNode;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\ParsedExpression;
class ParsedExpressionTest extends TestCase
{
    public function testSerialization()
    {
        $expression = new ParsedExpression('25', new ConstantNode('25'));
        $serializedExpression = \serialize($expression);
        $unserializedExpression = \unserialize($serializedExpression);
        $this->assertEquals($expression, $unserializedExpression);
    }
}
