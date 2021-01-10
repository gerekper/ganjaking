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
        static $map = ['structure' => 'WPMailSMTP\\Vendor\\Aws\\Api\\StructureShape', 'map' => 'WPMailSMTP\\Vendor\\Aws\\Api\\MapShape', 'list' => 'WPMailSMTP\\Vendor\\Aws\\Api\\ListShape', 'timestamp' => 'WPMailSMTP\\Vendor\\Aws\\Api\\TimestampShape', 'integer' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'double' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'float' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'long' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'string' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'byte' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'character' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'blob' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape', 'boolean' => 'WPMailSMTP\\Vendor\\Aws\\Api\\Shape'];
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
}
