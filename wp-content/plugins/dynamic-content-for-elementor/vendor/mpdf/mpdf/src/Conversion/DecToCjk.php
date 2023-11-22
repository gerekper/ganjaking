<?php

namespace DynamicOOOS\Mpdf\Conversion;

use DynamicOOOS\Mpdf\Utils\UtfString;
class DecToCjk
{
    public function convert($num)
    {
        $nstr = (string) $num;
        $rnum = '';
        $glyphs = [0x3007, 0x4e00, 0x4e8c, 0x4e09, 0x56db, 0x4e94, 0x516d, 0x4e03, 0x516b, 0x4e5d];
        $len = \strlen($nstr);
        for ($i = 0; $i < $len; $i++) {
            $rnum .= UtfString::code2utf($glyphs[(int) $nstr[$i]]);
        }
        return $rnum;
    }
}
