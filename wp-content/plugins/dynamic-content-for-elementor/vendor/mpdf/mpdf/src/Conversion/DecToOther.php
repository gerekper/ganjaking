<?php

namespace DynamicOOOS\Mpdf\Conversion;

use DynamicOOOS\Mpdf\Mpdf;
use DynamicOOOS\Mpdf\Utils\UtfString;
class DecToOther
{
    /**
     * @var \Mpdf\Mpdf
     */
    private $mpdf;
    public function __construct(Mpdf $mpdf)
    {
        $this->mpdf = $mpdf;
    }
    public function convert($num, $cp, $check = \true)
    {
        // From printlistbuffer: font is set, so check if character is available
        // From docPageNum: font is not set, so no check
        $nstr = (string) $num;
        $rnum = '';
        $len = \strlen($nstr);
        for ($i = 0; $i < $len; $i++) {
            if (!$check || $this->mpdf->_charDefined($this->mpdf->CurrentFont['cw'], $cp + (int) $nstr[$i])) {
                $rnum .= UtfString::code2utf($cp + (int) $nstr[$i]);
            } else {
                $rnum .= $nstr[$i];
            }
        }
        return $rnum;
    }
    /**
     * @param string $script
     * @return int
     */
    public function getCodePage($script)
    {
        $codePages = ['arabic-indic' => 0x660, 'persian' => 0x6f0, 'urdu' => 0x6f0, 'bengali' => 0x9e6, 'devanagari' => 0x966, 'gujarati' => 0xae6, 'gurmukhi' => 0xa66, 'kannada' => 0xce6, 'malayalam' => 0xd66, 'oriya' => 0xb66, 'telugu' => 0xc66, 'tamil' => 0xbe6, 'thai' => 0xe50, 'khmer' => 0x17e0, 'cambodian' => 0x17e0, 'lao' => 0xed0, 'myanmar' => 0x1040];
        return isset($codePages[$script]) ? $codePages[$script] : 0;
    }
}
