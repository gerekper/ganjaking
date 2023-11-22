<?php

namespace DynamicOOOS;

// warning: this file is encoded in UTF-8!
class HTML5_Data
{
    // at some point this should be moved to a .ser file. Another
    // possible optimization is to give UTF-8 bytes, not Unicode
    // codepoints
    // XXX: Not quite sure why it's named this; this is
    // actually the numeric entity dereference table.
    protected static $realCodepointTable = array(
        0x0 => 0xfffd,
        // REPLACEMENT CHARACTER
        0xd => 0xa,
        // LINE FEED (LF)
        0x80 => 0x20ac,
        // EURO SIGN ('€')
        0x81 => 0x81,
        // <control>
        0x82 => 0x201a,
        // SINGLE LOW-9 QUOTATION MARK ('‚')
        0x83 => 0x192,
        // LATIN SMALL LETTER F WITH HOOK ('ƒ')
        0x84 => 0x201e,
        // DOUBLE LOW-9 QUOTATION MARK ('„')
        0x85 => 0x2026,
        // HORIZONTAL ELLIPSIS ('…')
        0x86 => 0x2020,
        // DAGGER ('†')
        0x87 => 0x2021,
        // DOUBLE DAGGER ('‡')
        0x88 => 0x2c6,
        // MODIFIER LETTER CIRCUMFLEX ACCENT ('ˆ')
        0x89 => 0x2030,
        // PER MILLE SIGN ('‰')
        0x8a => 0x160,
        // LATIN CAPITAL LETTER S WITH CARON ('Š')
        0x8b => 0x2039,
        // SINGLE LEFT-POINTING ANGLE QUOTATION MARK ('‹')
        0x8c => 0x152,
        // LATIN CAPITAL LIGATURE OE ('Œ')
        0x8d => 0x8d,
        // <control>
        0x8e => 0x17d,
        // LATIN CAPITAL LETTER Z WITH CARON ('Ž')
        0x8f => 0x8f,
        // <control>
        0x90 => 0x90,
        // <control>
        0x91 => 0x2018,
        // LEFT SINGLE QUOTATION MARK ('‘')
        0x92 => 0x2019,
        // RIGHT SINGLE QUOTATION MARK ('’')
        0x93 => 0x201c,
        // LEFT DOUBLE QUOTATION MARK ('“')
        0x94 => 0x201d,
        // RIGHT DOUBLE QUOTATION MARK ('”')
        0x95 => 0x2022,
        // BULLET ('•')
        0x96 => 0x2013,
        // EN DASH ('–')
        0x97 => 0x2014,
        // EM DASH ('—')
        0x98 => 0x2dc,
        // SMALL TILDE ('˜')
        0x99 => 0x2122,
        // TRADE MARK SIGN ('™')
        0x9a => 0x161,
        // LATIN SMALL LETTER S WITH CARON ('š')
        0x9b => 0x203a,
        // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK ('›')
        0x9c => 0x153,
        // LATIN SMALL LIGATURE OE ('œ')
        0x9d => 0x9d,
        // <control>
        0x9e => 0x17e,
        // LATIN SMALL LETTER Z WITH CARON ('ž')
        0x9f => 0x178,
    );
    protected static $namedCharacterReferences;
    protected static $namedCharacterReferenceMaxLength;
    /**
     * Returns the "real" Unicode codepoint of a malformed character
     * reference.
     */
    public static function getRealCodepoint($ref)
    {
        if (!isset(self::$realCodepointTable[$ref])) {
            return \false;
        } else {
            return self::$realCodepointTable[$ref];
        }
    }
    public static function getNamedCharacterReferences()
    {
        if (!self::$namedCharacterReferences) {
            self::$namedCharacterReferences = \unserialize(\file_get_contents(\dirname(__FILE__) . '/named-character-references.ser'));
        }
        return self::$namedCharacterReferences;
    }
    /**
     * Converts a Unicode codepoint to sequence of UTF-8 bytes.
     * @note Shamelessly stolen from HTML Purifier, which is also
     *       shamelessly stolen from Feyd (which is in public domain).
     */
    public static function utf8chr($code)
    {
        /* We don't care: we live dangerously
           * if($code > 0x10FFFF or $code < 0x0 or
            ($code >= 0xD800 and $code <= 0xDFFF) ) {
              // bits are set outside the "valid" range as defined
              // by UNICODE 4.1.0
              return "\xEF\xBF\xBD";
            }*/
        $y = $z = $w = 0;
        if ($code < 0x80) {
            // regular ASCII character
            $x = $code;
        } else {
            // set up bits for UTF-8
            $x = $code & 0x3f | 0x80;
            if ($code < 0x800) {
                $y = ($code & 0x7ff) >> 6 | 0xc0;
            } else {
                $y = ($code & 0xfc0) >> 6 | 0x80;
                if ($code < 0x10000) {
                    $z = $code >> 12 & 0xf | 0xe0;
                } else {
                    $z = $code >> 12 & 0x3f | 0x80;
                    $w = $code >> 18 & 0x7 | 0xf0;
                }
            }
        }
        // set up the actual character
        $ret = '';
        if ($w) {
            $ret .= \chr($w);
        }
        if ($z) {
            $ret .= \chr($z);
        }
        if ($y) {
            $ret .= \chr($y);
        }
        $ret .= \chr($x);
        return $ret;
    }
}
// warning: this file is encoded in UTF-8!
\class_alias('DynamicOOOS\\HTML5_Data', 'HTML5_Data', \false);
