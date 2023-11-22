<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace DynamicOOOS\setasign\Fpdi;

use DynamicOOOS\setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use DynamicOOOS\setasign\Fpdi\PdfParser\PdfParserException;
use DynamicOOOS\setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use DynamicOOOS\setasign\Fpdi\PdfParser\Type\PdfNull;
/**
 * Class Fpdi
 *
 * This class let you import pages of existing PDF documents into a reusable structure for FPDF.
 */
class Fpdi extends FpdfTpl
{
    use FpdiTrait;
    use FpdfTrait;
    /**
     * FPDI version
     *
     * @string
     */
    const VERSION = '2.4.1';
}
