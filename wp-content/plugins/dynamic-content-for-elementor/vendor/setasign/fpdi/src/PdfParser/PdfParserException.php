<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace DynamicOOOS\setasign\Fpdi\PdfParser;

use DynamicOOOS\setasign\Fpdi\FpdiException;
/**
 * Exception for the pdf parser class
 */
class PdfParserException extends FpdiException
{
    /**
     * @var int
     */
    const NOT_IMPLEMENTED = 0x1;
    /**
     * @var int
     */
    const IMPLEMENTED_IN_FPDI_PDF_PARSER = 0x2;
    /**
     * @var int
     */
    const INVALID_DATA_TYPE = 0x3;
    /**
     * @var int
     */
    const FILE_HEADER_NOT_FOUND = 0x4;
    /**
     * @var int
     */
    const PDF_VERSION_NOT_FOUND = 0x5;
    /**
     * @var int
     */
    const INVALID_DATA_SIZE = 0x6;
}
