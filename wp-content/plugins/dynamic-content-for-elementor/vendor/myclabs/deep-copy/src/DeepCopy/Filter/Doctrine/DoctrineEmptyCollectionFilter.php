<?php

namespace DynamicOOOS\DeepCopy\Filter\Doctrine;

use DynamicOOOS\DeepCopy\Filter\Filter;
use DynamicOOOS\DeepCopy\Reflection\ReflectionHelper;
use DynamicOOOS\Doctrine\Common\Collections\ArrayCollection;
/**
 * @final
 */
class DoctrineEmptyCollectionFilter implements Filter
{
    /**
     * Sets the object property to an empty doctrine collection.
     *
     * @param object   $object
     * @param string   $property
     * @param callable $objectCopier
     */
    public function apply($object, $property, $objectCopier)
    {
        $reflectionProperty = ReflectionHelper::getProperty($object, $property);
        $reflectionProperty->setAccessible(\true);
        $reflectionProperty->setValue($object, new ArrayCollection());
    }
}
