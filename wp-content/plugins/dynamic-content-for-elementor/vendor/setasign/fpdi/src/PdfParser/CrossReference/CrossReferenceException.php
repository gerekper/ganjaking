<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace DynamicOOOS\setasign\Fpdi\PdfParser\CrossReference;

use DynamicOOOS\setasign\Fpdi\PdfParser\PdfParserException;
/**
 * Exception used by the CrossReference and Reader classes.
 */
class CrossReferenceException extends PdfParserException
{
    /**
     * @var int
     */
    const INVALID_DATA = 0x101;
    /**
     * @var int
     */
    const XREF_MISSING = 0x102;
    /**
     * @var int
     */
    const ENTRIES_TOO_LARGE = 0x103;
    /**
     * @var int
     */
    const ENTRIES_TOO_SHORT = 0x104;
    /**
     * @var int
     */
    const NO_ENTRIES = 0x105;
    /**
     * @var int
     */
    const NO_TRAILER_FOUND = 0x106;
    /**
     * @var int
     */
    const NO_STARTXREF_FOUND = 0x107;
    /**
     * @var int
     */
    const NO_XREF_FOUND = 0x108;
    /**
     * @var int
     */
    const UNEXPECTED_END = 0x109;
    /**
     * @var int
     */
    const OBJECT_NOT_FOUND = 0x10a;
    /**
     * @var int
     */
    const COMPRESSED_XREF = 0x10b;
    /**
     * @var int
     */
    const ENCRYPTED = 0x10c;
}
