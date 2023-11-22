<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace DynamicOOOS\setasign\Fpdi\PdfReader;

use DynamicOOOS\setasign\Fpdi\FpdiException;
/**
 * Exception for the pdf reader class
 */
class PdfReaderException extends FpdiException
{
    /**
     * @var int
     */
    const KIDS_EMPTY = 0x101;
    /**
     * @var int
     */
    const UNEXPECTED_DATA_TYPE = 0x102;
    /**
     * @var int
     */
    const MISSING_DATA = 0x103;
}
