<?php

namespace DynamicOOOS\DeepCopy\Matcher\Doctrine;

use DynamicOOOS\DeepCopy\Matcher\Matcher;
use DynamicOOOS\Doctrine\Persistence\Proxy;
/**
 * @final
 */
class DoctrineProxyMatcher implements Matcher
{
    /**
     * Matches a Doctrine Proxy class.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof Proxy;
    }
}
