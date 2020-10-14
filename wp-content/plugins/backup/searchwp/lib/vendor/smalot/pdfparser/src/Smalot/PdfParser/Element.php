<?php

/**
 * @file
 *          This file is part of the PdfParser library.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 * @date    2017-01-03
 * @license LGPLv3
 * @url     <https://github.com/smalot/pdfparser>
 *
 *  PdfParser is a pdf library written in PHP, extraction oriented.
 *  Copyright (C) 2017 - Sébastien MALOT <sebastien@malot.fr>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program.
 *  If not, see <http://www.pdfparser.org/sites/default/LICENSE.txt>.
 *
 */
namespace SearchWP\Dependencies\Smalot\PdfParser;

use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementArray;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementBoolean;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementDate;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementHexa;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementName;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementNull;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementNumeric;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementString;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementStruct;
use SearchWP\Dependencies\Smalot\PdfParser\Element\ElementXRef;
/**
 * Class Element
 *
 * @package Smalot\PdfParser
 */
class Element
{
    /**
     * @var Document
     */
    protected $document = null;
    /**
     * @var mixed
     */
    protected $value = null;
    /**
     * @param mixed    $value
     * @param Document $document
     */
    public function __construct($value, \SearchWP\Dependencies\Smalot\PdfParser\Document $document = null)
    {
        $this->value = $value;
        $this->document = $document;
    }
    /**
     *
     */
    public function init()
    {
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function equals($value)
    {
        return $value == $this->value;
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value)
    {
        if (\is_array($this->value)) {
            /** @var Element $val */
            foreach ($this->value as $val) {
                if ($val->equals($value)) {
                    return \true;
                }
            }
            return \false;
        } else {
            return $this->equals($value);
        }
    }
    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->value;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
    /**
     * @param string   $content
     * @param Document $document
     * @param int      $position
     *
     * @return array
     * @throws \Exception
     */
    public static function parse($content, \SearchWP\Dependencies\Smalot\PdfParser\Document $document = null, &$position = 0)
    {
        $args = \func_get_args();
        $only_values = isset($args[3]) ? $args[3] : \false;
        $content = \trim($content);
        $values = array();
        do {
            $old_position = $position;
            if (!$only_values) {
                if (!\preg_match('/^\\s*(?P<name>\\/[A-Z0-9\\._]+)(?P<value>.*)/si', \substr($content, $position), $match)) {
                    break;
                } else {
                    $name = \ltrim($match['name'], '/');
                    $value = $match['value'];
                    $position = \strpos($content, $value, $position + \strlen($match['name']));
                }
            } else {
                $name = \count($values);
                $value = \substr($content, $position);
            }
            if ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementName::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementXRef::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementNumeric::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementStruct::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementBoolean::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementNull::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementDate::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementString::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementHexa::parse($value, $document, $position)) {
                $values[$name] = $element;
            } elseif ($element = \SearchWP\Dependencies\Smalot\PdfParser\Element\ElementArray::parse($value, $document, $position)) {
                $values[$name] = $element;
            } else {
                $position = $old_position;
                break;
            }
        } while ($position < \strlen($content));
        return $values;
    }
}
