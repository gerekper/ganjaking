<?php

namespace WPMailSMTP\Vendor\Aws\Api;

/**
 * Base class representing a modeled shape.
 */
class Shape extends \WPMailSMTP\Vendor\Aws\Api\AbstractModel
{
    /**
     * Get a concrete shape for the given definition.
     *
     * @param array    $definition
     * @param ShapeMap $shapeMap
     *
     * @return mixed
     * @throws \RuntimeException if the type is invalid
     */
    public static function create(array $definition, \WPMailSMTP\Vendor\Aws\Api\ShapeMap $shapeMap)
    {
        static $map = ['structure' => \WPMailSMTP\Vendor\Aws\Api\StructureShape::class, 'map' => \WPMailSMTP\Vendor\Aws\Api\MapShape::class, 'list' => \WPMailSMTP\Vendor\Aws\Api\ListShape::class, 'timestamp' => \WPMailSMTP\Vendor\Aws\Api\TimestampShape::class, 'integer' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'double' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'float' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'long' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'string' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'byte' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'character' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'blob' => \WPMailSMTP\Vendor\Aws\Api\Shape::class, 'boolean' => \WPMailSMTP\Vendor\Aws\Api\Shape::class];
        if (isset($definition['shape'])) {
            return $shapeMap->resolve($definition);
        }
        if (!isset($map[$definition['type']])) {
            throw new \RuntimeException('Invalid type: ' . \print_r($definition, \true));
        }
        $type = $map[$definition['type']];
        return new $type($definition, $shapeMap);
    }
    /**
     * Get the type of the shape
     *
     * @return string
     */
    public function getType()
    {
        return $this->definition['type'];
    }
    /**
     * Get the name of the shape
     *
     * @return string
     */
    public function getName()
    {
        return $this->definition['name'];
    }
    /**
     * Get a context param definition.
     */
    public function getContextParam()
    {
        return $this->contextParam;
    }
}
