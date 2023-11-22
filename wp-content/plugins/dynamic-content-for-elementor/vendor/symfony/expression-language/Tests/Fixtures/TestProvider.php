<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DynamicOOOS\Symfony\Component\ExpressionLanguage\Tests\Fixtures;

use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionPhpFunction;
class TestProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [new ExpressionFunction('identity', function ($input) {
            return $input;
        }, function (array $values, $input) {
            return $input;
        }), ExpressionFunction::fromPhp('strtoupper'), ExpressionFunction::fromPhp('\\strtolower'), ExpressionFunction::fromPhp('DynamicOOOS\\Symfony\\Component\\ExpressionLanguage\\Tests\\Fixtures\\fn_namespaced', 'fn_namespaced')];
    }
}
function fn_namespaced()
{
    return \true;
}
