<?php

namespace DynamicOOOS\Mpdf\Conversion;

use DynamicOOOS\Mpdf\Utils\UtfString;
class DecToHebrew
{
    public function convert($in, $reverse = \false)
    {
        // reverse is used when called from Lists, as these do not pass through bidi-algorithm
        $i = (int) $in;
        // I initially be the counter value
        $s = '';
        // S initially be the empty string
        // and glyph list initially be the list of additive tuples.
        $additive_nums = [400, 300, 200, 100, 90, 80, 70, 60, 50, 40, 30, 20, 19, 18, 17, 16, 15, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1];
        $additive_glyphs = [0x5ea, 0x5e9, 0x5e8, 0x5e7, 0x5e6, 0x5e4, 0x5e2, 0x5e1, 0x5e0, 0x5de, 0x5dc, 0x5db, [0x5d9, 0x5d8], [0x5d9, 0x5d7], [0x5d9, 0x5d6], [0x5d8, 0x5d6], [0x5d8, 0x5d5], 0x5d9, 0x5d8, 0x5d7, 0x5d6, 0x5d5, 0x5d4, 0x5d3, 0x5d2, 0x5d1, 0x5d0];
        // NB This system manually specifies the values for 19-15 to force the correct display of 15 and 16, which are commonly
        // rewritten to avoid a close resemblance to the Tetragrammaton.
        // This function only works up to 1,000
        if ($i > 999) {
            return $in;
        }
        // return as initial numeric string
        // If I is initially 0, and there is an additive tuple with a weight of 0, append that tuple's counter glyph to S and return S.
        if ($i === 0) {
            return '0';
        }
        // Otherwise, while I is greater than 0 and there are elements left in the glyph list:
        $additiveNumsCount = \count($additive_nums);
        for ($t = 0; $t < $additiveNumsCount; $t++) {
            // Pop the first additive tuple from the glyph list. This is the current tuple.
            $ct = $additive_nums[$t];
            // Append the current tuple's counter glyph to S x floor( I / current tuple's weight ) times (this may be 0).
            $n = \floor($i / $ct);
            for ($j = 0; $j < $n; $j++) {
                if (\is_array($additive_glyphs[$t])) {
                    foreach ($additive_glyphs[$t] as $ag) {
                        if ($reverse) {
                            $s = UtfString::code2utf($ag) . $s;
                        } else {
                            $s .= UtfString::code2utf($ag);
                        }
                    }
                } else {
                    if ($reverse) {
                        $s = UtfString::code2utf($additive_glyphs[$t]) . $s;
                    } else {
                        $s .= UtfString::code2utf($additive_glyphs[$t]);
                    }
                }
                $i -= $ct * $n;
            }
            if ($i === 0.0 || $i === 0) {
                return $s;
            }
        }
        return $in;
        // return as initial string
    }
}
